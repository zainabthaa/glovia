<?php
require_once 'includes/security.php';

// Sign-up handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    // Validate input, store it raw, and escape it only when it is displayed
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        echo '<script>
            alert("Please enter a valid email address.");
            window.location.href = "signup.php";
        </script>';
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    require_once 'includes/connect.php';
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO users (email, password, is_verified) VALUES (?, ?, 0)");
        $stmt->bind_param("ss", $email, $hashed_password);
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $username = explode('@', $email)[0];

            start_authenticated_session(); // new session ID for the new account

            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $username;

            // Output is JSON-encoded with JSON_HEX_TAG, so it is safe inside <script> (reviewed)
            // nosemgrep: echoed-request
            echo '<script>
                localStorage.setItem("signup", "true");
                localStorage.setItem("username", ' . json_encode($username, JSON_HEX_TAG) . ');
                window.location.href = "main.php";
            </script>';
            exit();
        } else {
            echo '<script>
                alert("Error during sign-up. Please try again.");
                window.location.href = "signup.php";
            </script>';
        }
    } else {
        echo '<script>
            alert("This email is already registered. Please try another one.");
            window.location.href = "signup.php";
        </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Glovia</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/pages.css" />

    <link rel="icon" href="assets/images/round.png" type="image/x-icon">
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

    <section class="login-section"> <!-- Sign up -->
        <div class="login-container">
            <div class="login-image">
                <img src="assets/images/login.jpg" alt="Login Visual">
                <div class="image-text">It’s time to invest in your SKIN.</div>
            </div>

            <div class="login-form">
                <div class="text-overlay p-3">
                    <h3 class="display-6 fw-bold text-center">Sign Up</h3>
                </div>

                <form method="POST">
<?= csrf_field() ?>
                    <input type="email" class="form-control" name="email" placeholder="Email" required><br>
                    <input type="password" class="form-control" name="password" placeholder="Password" required><br>
                    <div class="d-flex justify-content-center ">
                        <button type="submit" class="button" style="width: 80%; max-width: 200px" name="signup">Create Account</button>
                    </div>
                </form>

                <br>
                <div class="extra-links text-center">
                    <span>Already have an account? <a href="login.php">Log In!</a></span>
                </div>
            </div>
    </section>

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
                                <li><a href="mixskin.php" style="text-decoration: none; color: #666;">Mix With Glovia</a>
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

    <script src="js/main.js"></script>
</body>

</html>