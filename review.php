<?php
require_once 'includes/security.php';
require_once 'includes/connect.php'; // Ensure this connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username'])) {
    $name = $_SESSION['username']; // Auto-fill name from session
    $product_name = $_POST['product_name'] ?? ''; // Get the product name
    $rating = intval($_POST['rating']);
    $review = $_POST['review'];

    $stmt = $conn->prepare("INSERT INTO reviews (name, product_name, rating, review) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $product_name, $rating, $review);

    if ($stmt->execute()) {
        echo "<script>localStorage.setItem('reviewSubmitted', true); window.location = 'review.php';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Reviews - Glovia</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/popup.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/pages.css" />
    <link rel="stylesheet" href="css/contact.css" />


    <link rel="icon" href="assets/images/round.png" type="image/png">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .buttonn {
            background: transparent;
            width: 20%;
            max-width: 200px;
            /* Prevents button from being too large */
            min-width: 120px;
            /* Prevents button from being too small */
            border-radius: 50px;
            /* Rounded edges */
            padding: 15px;
            cursor: pointer;
            border: 2px solid white;
            color: white;
            transition: all 0.3s ease-in-out;
            font-size: 1rem;
            /* Adjust text size */
            text-decoration: none;
            /* Remove underline from the button */

        }

        .buttonn:hover {
            background-color: white;
            color: black;
        }
    </style>

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
                    <div class="d-flex align-items-center" style="margin-left: auto; padding-left: 50px;">
                        <?php if (isset($_SESSION['username'])): ?>
                            <a href="liked.php" class="initial-circle me-2" title="View Liked Products" style="text-decoration: none;">
                                <?php echo htmlspecialchars(strtoupper($_SESSION['username'][0])); ?>
                            </a>
                            <a href="logout.php" class="btn">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class="button text-center px-2 py-1" style="margin-right: 15px;">Account</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </article>

    <section class="hero-section fade-section" style="height: 60vh; background-image: url('assets/images/reviews.jpg'); background-position: center top 20%; background-size: cover; background-repeat: no-repeat; position: relative;">

    <!-- Original content for large screens -->
    <div class="container text-center fade-section" id="reviewTextLarge">
        <div class="p-3">
            <h3 class="display-6 fw-bold text-center">We Love to Hear from You!</h3>
        </div>

        <?php if (isset($_SESSION['username'])): ?>
            <p class="lead">Hello, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! We'd love to hear your thoughts.</p>
        <?php else: ?>
            <p class="lead">Please log in to leave a review. Your feedback helps others find the right products and helps us improve Glovia for everyone!</p>
            <br>
            <a href="login.php?redirect=<?php echo urlencode('review.php'); ?>" class="buttonn">Login</a>
        <?php endif; ?>
    </div>

    <!-- Overlay box for small screens -->
    <div id="overlayBox2" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 800px; width: 80%; background-color: rgba(0, 0, 0, 0.5); padding: 1.5rem 2rem; border-radius: 1rem; text-align: center; color: white;">
        <h3 class="display-6 fw-bold text-center">We Love to Hear from You!</h3>
        <?php if (isset($_SESSION['username'])): ?>
            <p class="lead">Hello, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! We'd love to hear your thoughts.</p>
        <?php else: ?>
            <p class="lead">Please log in to leave a review. Your feedback helps others find the right products and helps us improve Glovia for everyone!</p>
            <br>
            <a href="login.php?redirect=<?php echo urlencode('review.php'); ?>" class="buttonn">Login</a>
        <?php endif; ?>
    </div>

    <style>
        @media (max-width: 768px) {
            #reviewTextLarge {
                display: none;
            }

            #overlayBox2 {
                display: block !important;
            }
        }
    </style>
</section>



    <article class="container my-5">
        <section class="fade-section">
            <?php if (isset($_SESSION['username'])): ?>
                <!-- Review Submission Form -->
                <div class="card p-4 mb-5">
                    <div class="text-overlay p-3">
                        <h3 class="display-6 fw-bold text-center">Leave a Review</h3>
                    </div>
                    <form action="review.php" method="post">
<?= csrf_field() ?>
                        <div class="mb-3">
                            <input type="text" name="product_name" class="form-control" placeholder="Product Name" required>
                        </div>
                        <div class="mb-3">
                            <label>Rating:</label>
                            <select name="rating" class="form-select" required>
                                <option value="">Select rating</option>
                                <option value="5">★★★★★ (5)</option>
                                <option value="4">★★★★☆ (4)</option>
                                <option value="3">★★★☆☆ (3)</option>
                                <option value="2">★★☆☆☆ (2)</option>
                                <option value="1">★☆☆☆☆ (1)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <textarea name="review" class="form-control" rows="4" placeholder="Write your review..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="button" style="width: 200px;">Submit Review</button>
                        </div>

                    </form>

                </div>
            <?php endif; ?>
        </section>
    </article>

    <article>
        <!-- Review Display Section -->
        <?php
        require_once 'includes/connect.php';
        $result = $conn->query("SELECT name, product_name, rating, review, created_at FROM reviews ORDER BY created_at DESC");
        ?>

        <section class="fade-section">
            <div class="mb-5 p-4">
                <div class="text-overlay p-3">
                    <h3 class="display-6 fw-bold text-center">Hear from other Reviewers!</h3>
                </div>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card my-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($row['name']); ?>
                                <small class="text-muted"><?php echo htmlspecialchars($row['product_name']); ?></small>
                            </h5>
                            <p class="stars">
                                <?php
                                echo str_repeat("★", $row['rating']);
                                echo str_repeat("☆", 5 - $row['rating']);
                                ?>
                            </p>

                            <p class="card-text"><?php echo nl2br(htmlspecialchars($row['review'])); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
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

    <!-- Review Submitted Popup -->
    <div id="review-popup" class="popup-overlay">
        <div class="popup-box">
            <h2>Thank You, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>!</h2>
            <p>Your review has been submitted successfully. We appreciate your feedback.</p>
            <button onclick="document.getElementById('review-popup').style.display='none'">Close</button>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="js/main.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (localStorage.getItem("reviewSubmitted")) {
                document.getElementById("review-popup").style.display = "flex";
                localStorage.removeItem("reviewSubmitted");
            }
        });
    </script>

</body>

</html>