#!/usr/bin/env bash
# Glovia security regression tests.
# Usage: BASE_URL=http://localhost/glovia ./tests/security_tests.sh
# Needs: curl, and the demo accounts from database/database.sql.
# Note: test 5 makes 5 failed logins for demo@glovia.com, so that account is locked for 15 minutes afterwards.

B="${BASE_URL:-http://localhost/glovia}"
J=$(mktemp); PASS=0; FAIL=0
ok()   { echo "  PASS  $1"; PASS=$((PASS+1)); }
bad()  { echo "  FAIL  $1"; FAIL=$((FAIL+1)); }
check(){ if [ "$2" = "$3" ]; then ok "$1"; else bad "$1 (expected '$3', got '$2')"; fi; }
token(){ curl -s -b "$J" -c "$J" "$B/$1" | grep -o 'name="csrf_token" value="[a-f0-9]*"' | head -1 | sed 's/.*value="//;s/"//'; }
status(){ curl -s -o /tmp/glovia_out.html -w '%{http_code}' -b "$J" -c "$J" "$@"; }
msg()  { grep -o "color: red;'>[^<]*" /tmp/glovia_out.html | cut -d'>' -f2; }
fresh(){ rm -f "$J"; touch "$J"; }

echo "1. Security headers"
H=$(curl -s -D - -o /dev/null "$B/main.php")
for h in "X-Frame-Options: DENY" "X-Content-Type-Options: nosniff" "Content-Security-Policy:" "Referrer-Policy:"; do
  echo "$H" | grep -qi "$h" && ok "$h" || bad "$h missing"; done
echo "$H" | grep -i "set-cookie" | grep -qi "httponly" && ok "session cookie is HttpOnly" || bad "cookie not HttpOnly"
echo "$H" | grep -i "set-cookie" | grep -qi "samesite=lax" && ok "session cookie is SameSite=Lax" || bad "cookie not SameSite"

echo "2. CSRF protection"
fresh; status "$B/main.php" >/dev/null
check "contact form POST without token is rejected" "$(status -X POST -d 'name=A&email=a@a.com&message=x' "$B/contact.php")" 403
check "login POST with wrong token is rejected" "$(status -X POST -d 'email=x@x.com&password=x&login=1&csrf_token=bad' "$B/login.php")" 403
check "like (AJAX) without token header is rejected" "$(status -X POST -d 'product_id=1' "$B/toggle_like.php")" 403
fresh; T=$(token admin_login.php)
status -X POST -d "csrf_token=$T&email=admin@glovia.com&password=admin1234&login=1" "$B/admin_login.php" >/dev/null
check "admin: forged POST make_admin without token is rejected" "$(status -X POST -d 'make_admin=demo@glovia.com' "$B/admin_panel.php")" 403
status "$B/admin_panel.php?make_admin=demo%40glovia.com" >/dev/null
grep -q 'name="make_admin" value="demo@glovia.com"' /tmp/glovia_out.html && ok "admin: old GET link attack no longer changes anything" || bad "GET make_admin had an effect"
AT=$(token admin_panel.php)
check "admin: real button (POST + token) still works" "$(status -X POST -d "csrf_token=$AT&ban_user=demo@glovia.com" "$B/admin_panel.php")" 302
status -X POST -d "csrf_token=$AT&unban_user=demo@glovia.com" "$B/admin_panel.php" >/dev/null

echo "3. User enumeration"
fresh; T=$(token login.php); status -X POST -d "csrf_token=$T&email=nobody@example.com&password=x&login=1" "$B/login.php" >/dev/null; A=$(msg)
T=$(token login.php); status -X POST -d "csrf_token=$T&email=admin@glovia.com&password=wrong&login=1" "$B/login.php" >/dev/null; Bm=$(msg)
check "unknown email and wrong password give the same message" "$A" "$Bm"

echo "4. Session fixation"
fresh; curl -s -o /dev/null -b "PHPSESSID=attackerchosen123" -c "$J" "$B/login.php"
grep -q attackerchosen123 "$J" && bad "server accepted a planted session ID" || ok "planted session ID is rejected (strict mode)"
fresh; curl -s -o /dev/null -c "$J" "$B/login.php"; BEFORE=$(grep PHPSESSID "$J" | awk '{print $7}')
T=$(token login.php); status -X POST -d "csrf_token=$T&email=demo@glovia.com&password=demo1234&login=1" "$B/login.php" >/dev/null
AFTER=$(grep PHPSESSID "$J" | awk '{print $7}')
[ -n "$BEFORE" ] && [ "$BEFORE" != "$AFTER" ] && ok "session ID changes after login" || bad "session ID did not change"
check "old session ID is no longer logged in" "$(curl -s -o /dev/null -w '%{http_code}' -b "PHPSESSID=$BEFORE" "$B/liked.php")" 302

echo "5. Brute-force protection"
fresh
for i in 1 2 3 4 5; do T=$(token login.php); status -X POST -d "csrf_token=$T&email=demo@glovia.com&password=guess$i&login=1" "$B/login.php" >/dev/null; done
T=$(token login.php); status -X POST -d "csrf_token=$T&email=demo@glovia.com&password=demo1234&login=1" "$B/login.php" >/dev/null
msg | grep -q "Too many failed login attempts" && ok "account locked after 5 failures (even with the right password)" || bad "no lockout after 5 failures"

echo "6. Information leakage"
curl -s "$B/includes/config.php" | grep -q "DB_" && bad "config.php source is exposed" || ok "config.php does not reveal its contents"

echo; echo "Result: $PASS passed, $FAIL failed"; rm -f "$J"; [ "$FAIL" -eq 0 ]
