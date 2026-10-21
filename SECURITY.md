# Security Review & Hardening – Glovia

This document records a security review of Glovia, a PHP/MySQL web application I originally built as a university project. I treated it as a real application. I reviewed the code manually and ran a static analysis scan (SAST), then fixed the findings and wrote automated tests that run the attacks against the fixed version.

**Scope:** all PHP pages, the AJAX endpoint, the admin panel, session handling and the database layer.
**Method:** manual code review, mapped to the OWASP Top 10 (2021), plus Semgrep static analysis before and after the fixes.
**Result:** 13 issues fixed. 0 Semgrep findings remain, and the attack tests in [`tests/security_tests.sh`](tests/security_tests.sh) all pass (18 of 18).

---

## Summary of findings

| # | Finding | OWASP 2021 | Severity | Status |
|---|---------|-----------|----------|--------|
| 1 | Cross-Site Request Forgery on every form and on all admin actions | A01 Broken Access Control | **High** | Fixed |
| 2 | Hard-coded Google OAuth client secret in source code | A07 Identification & Auth Failures / A05 | **High** | Fixed (removed from code) |
| 3 | Unrestricted file upload in admin panel (could upload `.php`) | A04 Insecure Design | **High** | Fixed |
| 4 | No brute-force protection on login | A07 Identification & Auth Failures | Medium | Fixed |
| 5 | Session fixation (session ID not renewed at login) | A07 Identification & Auth Failures | Medium | Fixed |
| 6 | Open redirect / script injection via `?redirect=` on login | A03 Injection | Medium | Fixed |
| 7 | Cross-site scripting sinks (unencoded values echoed into HTML/JS) | A03 Injection | Medium | Fixed |
| 8 | User enumeration through login error messages and response time | A07 Identification & Auth Failures | Low | Fixed |
| 9 | Missing security headers (CSP, clickjacking, MIME sniffing) | A05 Security Misconfiguration | Low | Fixed |
| 10 | Session cookie without `HttpOnly` / `SameSite` | A05 Security Misconfiguration | Low | Fixed |
| 11 | PHP errors displayed to visitors (information leakage) | A05 Security Misconfiguration | Low | Fixed |
| 12 | Database credentials hard-coded in tracked source file | A05 Security Misconfiguration | Low | Fixed |
| 13 | Personal data (real users' emails, password hashes, messages) in the database dump | A02 / Privacy | Medium | Fixed (replaced with demo data) |

Already in good shape before the review: **all SQL queries use prepared statements** (no SQL injection found), **passwords are hashed with bcrypt** (`password_hash`), and product/review content is escaped with `htmlspecialchars` on output.

---

## Details

### 1. Cross-Site Request Forgery (CSRF) – High

**Problem.** No form had a CSRF token. Worse, admin actions were plain GET links:

```html
<a href="admin_panel.php?make_admin=someone@example.com">Make Admin</a>
```

If a logged-in admin opened a malicious page, the browser would send that request with the admin's cookie. That page could contain just `<img src="https://site/admin_panel.php?make_admin=attacker@evil.com">`. The attacker would be made an admin silently. The same applied to banning users and deleting products and reviews.

**Fix.**
- All state-changing admin actions are now **POST forms**. GET requests no longer change anything.
- A per-session random token (`random_bytes(32)`) is added to every form (`csrf_field()`). The AJAX "like" request sends the token in an `X-CSRF-Token` header.
- Verification is **enforced centrally** in `includes/security.php`. Every POST request is checked with a constant-time `hash_equals()` before any page code runs, so a new form can't accidentally skip it.
- The `SameSite=Lax` cookie flag (finding 10) adds a second layer of defense.

```php
// includes/security.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();   // 403 unless the token matches the one stored in the session
}
```

**Verified:** a forged POST without a token returns `403`, the old GET link no longer has any effect, and the real buttons still work.

### 2. Hard-coded OAuth client secret – High

`signup.php` contained a Google OAuth client ID **and client secret**. Anyone with access to the code could have used the secret to impersonate the application. The Google sign-in code that used it was unreachable (no button called it), so it was removed entirely, along with the 141 MB `vendor/` dependency. Because the secret had been stored in project files, it also has to be revoked in Google Cloud Console. Deleting it from the code isn't enough.

*Lesson:* secrets belong in configuration that is never committed.

### 3. Unrestricted file upload – High

The admin "add product" form saved any uploaded file into a web-accessible folder under its original name. Uploading `shell.php` would have given remote code execution.

**Fix:** `save_product_image()` only accepts `jpg, jpeg, png, webp, gif, avif`. It checks that the file really is an image with `getimagesize()` and strips unsafe characters from the file name.

### 4. Brute-force protection – Medium

The login form allowed unlimited guesses. A new `login_attempts` table records failed logins. After **5 failures for one email** or **20 from one IP address** within **15 minutes**, logins are refused, even with the correct password. A successful login clears the counter. The lockout applies to both the user and admin login pages.

### 5. Session fixation – Medium

The session ID stayed the same before and after login. If an attacker could set a victim's session ID in advance, they would share the victim's logged-in session.

**Fix:**
- `session_regenerate_id(true)` runs on every login and sign-up, and the old ID is destroyed.
- `session.use_strict_mode` makes PHP refuse session IDs it didn't create.
- `session.use_only_cookies` stops session IDs being accepted from URLs.

**Verified:** a planted ID is rejected, the ID changes at login, and the old ID is no longer logged in.

### 6. Open redirect / injection in the login redirect – Medium

```php
// before
$redirect_url = $_GET['redirect'];
echo "<script>window.location.href = '$redirect_url';</script>";
```

`login.php?redirect=https://evil.com` sent users to a phishing site after a real login. A value containing `';alert(document.cookie);//` would have run attacker JavaScript.

**Fix:** only local page names that match `^[A-Za-z0-9_-]+\.php$` are allowed, and the value is written into JavaScript with `json_encode(..., JSON_HEX_TAG)`.

### 7. Cross-site scripting sinks – Medium

Several values from the request or session were echoed without encoding: the username initial in the navbar on every page, the username written into `<script>` blocks, and error messages. Sign-up also HTML-encoded the email *before storing it*, which mixes up data and presentation.

**Fix:** input is validated (`FILTER_VALIDATE_EMAIL`) and stored raw. Output is encoded for its context: `htmlspecialchars` in HTML, and `json_encode` with `JSON_HEX_TAG` inside JavaScript.

### 8. User enumeration – Low

The login said *"No account found with this email"* or *"Invalid password"*, which lets an attacker check whether someone has an account. Both cases now return **"Invalid email or password."**

When an email doesn't exist, the code still runs `password_verify()` against a dummy hash. Without that, the response would be measurably faster and give the same information away through timing. Both cases now took about 51 ms on average in testing.

### 9. Security headers – Low

`includes/security.php` now sends these headers on every page:

| Header | Purpose |
|---|---|
| `Content-Security-Policy` | Only allows scripts, styles and fonts from this site and the specific CDNs it uses. Also sets `object-src 'none'`, `base-uri 'self'`, `form-action 'self'` and `frame-ancestors 'none'` |
| `X-Frame-Options: DENY` | Prevents clickjacking in older browsers |
| `X-Content-Type-Options: nosniff` | Stops browsers from treating uploaded files as scripts |
| `Referrer-Policy: strict-origin-when-cross-origin` | Doesn't leak full URLs to other sites |
| `Permissions-Policy` | Disables the camera, microphone and location, which the site doesn't need |

**Known limitation:** the pages contain a lot of inline JavaScript and CSS, so the CSP still allows `'unsafe-inline'`. The next step is to move inline scripts into `js/` files and switch to nonces. That would make the CSP a much stronger defense against XSS.

### 10. Session cookie flags – Low

The session cookie is now `HttpOnly`, so JavaScript can't read it even if XSS occurs, and `SameSite=Lax`. It is also `Secure` automatically when the site is served over HTTPS.

### 11. Error display – Low

Two pages forced `display_errors = 1`, which could show file paths and SQL details to visitors. Errors are now logged instead of displayed. A single `APP_DEBUG` switch in `includes/config.php` turns display on for local debugging only.

### 12. Hard-coded database credentials – Low

The database settings moved from `connect.php` into `includes/config.php`, which is excluded by `.gitignore`. A `config.example.php` template is committed instead.

### 13. Personal data in the database dump – Medium

The original export contained real people's email addresses, password hashes and private contact messages. The published `database/database.sql` contains only product data, first-name reviews and two demo accounts. `.gitignore` now also blocks any other `.sql` export in `database/`, so a raw dump can't be committed by accident.

---

## Static analysis (SAST) results

I used [Semgrep](https://semgrep.dev) with the open-source `semgrep-rules` PHP security and secrets rulesets (268 rules).

| Version | Findings |
|---|---|
| Original project | 4: `detected-google-oauth-url` (hard-coded OAuth credentials), 3 × `echoed-request` (unencoded request data echoed) |
| After cleanup, before hardening | 3 × `echoed-request` |
| **After hardening** | **0** |

Two lines are marked `nosemgrep` after manual review. Each has a comment explaining why:
- The dummy bcrypt hash used for timing protection. It is a hash of a random throw-away value, not a credential.
- `echo` statements whose values are already encoded with `json_encode(JSON_HEX_TAG)`. Semgrep can't see that the encoding makes them safe.

SAST doesn't detect missing CSRF tokens, session fixation or missing rate limiting, which is why those were found by manual review and are covered by the attack tests instead.

## Attack tests

[`tests/security_tests.sh`](tests/security_tests.sh) runs the attacks above against a running copy of the site and reports pass or fail for each:

```bash
BASE_URL=http://localhost/glovia ./tests/security_tests.sh
```

```
1. Security headers          6 passed
2. CSRF protection           6 passed
3. User enumeration          1 passed
4. Session fixation          3 passed
5. Brute-force protection    1 passed
6. Information leakage       1 passed
Result: 18 passed, 0 failed
```

## Remaining risks and next steps

- **CSP still allows `'unsafe-inline'`.** Next: move inline scripts to files and use nonces.
- **Logout is a GET link.** A forced logout is low-impact, but it could be changed to a POST form.
- **Admin rights are cached in the session.** If an admin is demoted or banned, they keep access until they log out. Next: re-check `is_admin` and `banned` from the database on each admin request.
- **No password strength policy or email verification.**
- **Account lockout can be abused** to lock someone else out. Possible improvements are progressive delays or a CAPTCHA instead of a hard lock.
- **HTTPS** must be enabled on any real deployment, for the `Secure` cookie flag and HSTS.
