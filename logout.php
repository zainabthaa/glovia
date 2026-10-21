<?php
require_once 'includes/security.php';

// Clear all session data
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();
?>

<script>
    // Clear all client-side storage
    localStorage.removeItem("username");
    localStorage.removeItem("admin");
    
    // Redirect to home page
    window.location.href = "main.php";
</script>