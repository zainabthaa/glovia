<?php
require_once 'includes/security.php';
require_once 'includes/connect.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (login_is_blocked($conn, $email)) {
        $error_message = "Too many failed login attempts. Please wait " . LOGIN_WINDOW_MINUTES . " minutes and try again.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, banned, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $hash = $user['password'] ?? LOGIN_DUMMY_HASH;
        $password_ok = password_verify($password, $hash) && $user !== null;

        if (!$password_ok) {
            record_failed_login($conn, $email);
            $error_message = "Invalid email or password.";
        } elseif ($user['banned']) {
            $error_message = "This account has been banned.";
        } elseif (!$user['is_admin']) {
            $error_message = "Access denied. Not an admin.";
        } else {
            clear_failed_logins($conn, $email);
            start_authenticated_session(); // new session ID after login (prevents session fixation)

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $email;
            $_SESSION['username'] = explode('@', $email)[0];
            $_SESSION['is_admin'] = 1;

            header("Location: admin_panel.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login as an Admin - Glovia</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="icon" href="assets/images/round.png" type="image/png">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <article id="article-head"> <!-- Header -->
        <nav class="navbar navbar-expand-md navbar-light bg-light" style="padding-top: 1rem; padding-bottom: 1rem;">

            <div class="container-fluid">

                <!-- Left - Brand -->
                <a class="navbar-brand" href="main.php">glovia</a>

                <!-- Navbar Toggler -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Center + Right - Navbar Content -->
                <div class="collapse navbar-collapse justify-content-between" id="navbarNav">

                    <!-- Centered Nav Links -->
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="main.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">Behind Glovia</a>
                        </li>

                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin_panel.php">Admin</a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item align-items-center ms-2" style="margin-top: 4px;">
                            <a class="button text-center px-3 py-2" href="buildRoutine.php">Discover Your Skin</a>
                        </li>

                        
                    </ul>

                    <!-- Right-aligned user/account buttons -->
                    <div class="d-flex align-items-center" style="margin-left: auto;">
                        <?php if (isset($_SESSION['username'])): ?>
                            <a href="liked.php" class="initial-circle me-2" title="View Liked Products" style="text-decoration: none;">
                                <?php echo htmlspecialchars(strtoupper($_SESSION['username'][0])); ?>
                            </a>
                            <a href="logout.php" class="btn" style="margin-right: 15px;">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class="button text-center px-2 py-1" style="margin-right: 15px;">Account</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </article>

    <article> <!-- Login Section -->
        <section class="login-section">
            <div class="login-container">
                <div class="login-image">
                    <img src="assets/images/login.jpg" alt="Login Visual">
                    <div class="image-text">Welcome back, Admin. Let’s keep Glovia glowing.</div>
                </div>

                <div class="login-form">
                    <div class="text-overlay p-3">
                        <h3 class="display-6 fw-bold text-center">Admin Panel</h3>
                    </div>

                    <form method="POST">
<?= csrf_field() ?>
                        <input type="email" class="form-control mb-4" name="email" placeholder="Email" required>
                        <input type="password" class="form-control mb-3" name="password" placeholder="Password" required>
                        <div class='d-flex justify-content-center'>
                            <button class="button" type="submit" style="width: 80%; max-width: 200px" name="login">log In</button>
                        </div>
                    </form>
                    <br>

                    <!-- Display error message if any -->
                    <div class="text-center">
                        <?php if (!empty($error_message)) {
                            echo "<p style='color: red;'>" . htmlspecialchars($error_message) . "</p>";
                        } ?>
                    </div>

                </div>
            </div>
        </section>
    </article>

    <article> <!-- Footer -->
        <div class="container-fluid">
            <footer class="py-3" style="background: #f8f9fa; border-top: 2px solid #ddd;">
                <div class="container">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <img src="assets/images/round.png" alt="Company Logo" style="width: 120px; border-radius: 50%;">
                            <p class="mt-2" style="color: #555;">© 2025 Glovia</p>
                        </div>
                        <div class="col-md-4">
                            <br>
                            <h5 style="font-weight: 600; color: #333;">Explore</h5>
                            <ul class="list-unstyled">
                                <li><a href="main.php" style="text-decoration: none; color: #666;">Home</a></li>
                                <li><a href="about.php" style="text-decoration: none; color: #666;">Behind Glovia</a>
                                </li>
                                <li><a href="buildRoutine.php" style="text-decoration: none; color: #666;">Discover Your Skin</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <br>
                            <h5 style="font-weight: 600; color: #333;">Contact & Support</h5>
                            <ul class="list-unstyled" style="color: #666;">
                                <li><a href="contact.php" style="text-decoration: none; color: #666;">Contact Us</a>
                                </li>
                                <li><a href="terms.php" style="text-decoration: none; color: #666;">Terms &
                                        Conditions</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </article>

    <!-- JavaScript -->
    <script src="js/main.js"></script>

</body>

</html>