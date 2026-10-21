<?php
/**
 * Security bootstrap: included at the very top of every page, before any output.
 *
 *  1. Error handling     - errors are logged, never shown to visitors (unless APP_DEBUG)
 *  2. Security headers   - CSP, clickjacking protection, MIME sniffing protection, ...
 *  3. Hardened sessions  - HttpOnly + SameSite cookies, strict mode, ID regeneration on login
 *  4. CSRF protection    - every POST request must carry a valid per-session token
 *  5. Login throttling   - blocks brute-force attempts per account and per IP address
 */

require_once __DIR__ . '/config.php';

/* ---------------------------------------------------------------
 * 1. Error handling (prevents information leakage)
 * ------------------------------------------------------------- */
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

/* ---------------------------------------------------------------
 * 2. Security headers
 * ------------------------------------------------------------- */
header('X-Frame-Options: DENY');                                  // no clickjacking via iframes
header('X-Content-Type-Options: nosniff');                        // no MIME-type sniffing
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header(
    "Content-Security-Policy: "
    . "default-src 'self'; "
    . "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://kit.fontawesome.com; "
    . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://ka-f.fontawesome.com; "
    . "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://ka-f.fontawesome.com; "
    . "img-src 'self' data: https:; "
    . "connect-src 'self' https://ka-f.fontawesome.com; "
    . "object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'"
);

/* ---------------------------------------------------------------
 * 3. Hardened session
 * ------------------------------------------------------------- */
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    ini_set('session.use_strict_mode', '1');   // reject session IDs the server did not create
    ini_set('session.use_only_cookies', '1');  // never accept session IDs from the URL
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,                // cookie only sent over HTTPS when the site uses HTTPS
        'httponly' => true,                    // JavaScript cannot read the session cookie
        'samesite' => 'Lax',                   // cookie not sent on cross-site POST requests
    ]);
    session_start();
}

/**
 * Call right after a successful login/sign-up.
 * Issues a brand-new session ID so a pre-planted ID is useless (prevents session fixation).
 */
function start_authenticated_session(): void
{
    session_regenerate_id(true);
    unset($_SESSION['csrf_token']); // new session, new CSRF token
}

/* ---------------------------------------------------------------
 * 4. CSRF protection
 * ------------------------------------------------------------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Hidden input to place inside every POST form. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

/** Rejects the request unless it carries the correct token (form field or X-CSRF-Token header). */
function csrf_verify(): void
{
    $sent = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($sent) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $sent)) {
        http_response_code(403);
        $isAjax = isset($_SERVER['HTTP_X_CSRF_TOKEN']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'json');
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid or missing CSRF token.']);
        } else {
            echo 'Your session expired or the request was invalid. Please go back, refresh the page and try again.';
        }
        exit();
    }
}

// Enforced centrally: every POST request on the site must pass the CSRF check.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
}

/* ---------------------------------------------------------------
 * 5. Login throttling (brute-force protection)
 * ------------------------------------------------------------- */
const LOGIN_MAX_PER_ACCOUNT = 5;   // failed attempts allowed per email ...
const LOGIN_MAX_PER_IP      = 20;  // ... and per IP address ...
const LOGIN_WINDOW_MINUTES  = 15;  // ... within this many minutes

// bcrypt hash of a random throw-away value (not a real password or secret).
// When an email does not exist we still run password_verify() against this hash,
// so a login attempt takes the same time whether or not the account exists.
const LOGIN_DUMMY_HASH = '$2y$10$UmnhZTFqrs2h3PeBBb1HuuB/9SYnCzBVMn3/Ky5tsx/4oZbrlskzi'; // nosemgrep: detected-bcrypt-hash

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function login_is_blocked(mysqli $conn, string $email): bool
{
    $ip = client_ip();
    $stmt = $conn->prepare(
        "SELECT
            SUM(email = ?) AS by_email,
            SUM(ip = ?)    AS by_ip
         FROM login_attempts
         WHERE attempted_at > (NOW() - INTERVAL ? MINUTE)"
    );
    $window = LOGIN_WINDOW_MINUTES;
    $stmt->bind_param('ssi', $email, $ip, $window);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (int)$row['by_email'] >= LOGIN_MAX_PER_ACCOUNT || (int)$row['by_ip'] >= LOGIN_MAX_PER_IP;
}

function record_failed_login(mysqli $conn, string $email): void
{
    $ip = client_ip();
    $stmt = $conn->prepare("INSERT INTO login_attempts (email, ip) VALUES (?, ?)");
    $stmt->bind_param('ss', $email, $ip);
    $stmt->execute();
    $stmt->close();
}

function clear_failed_logins(mysqli $conn, string $email): void
{
    $stmt = $conn->prepare("DELETE FROM login_attempts WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->close();
}
