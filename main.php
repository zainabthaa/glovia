<?php
require_once 'includes/security.php';
require_once 'includes/connect.php'; // Ensure this connects to your database

// Fetch reviews from the database, including 'created_at' for sorting
$result = $conn->query("SELECT name, product_name, rating, review, created_at FROM reviews ORDER BY created_at DESC LIMIT 5"); // Adjust the LIMIT if needed
?>


<!DOCTYPE html>
<html>

<head>
    <title>Main Page - Glovia</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/pages.css" />
    <link rel="stylesheet" href="css/popup.css">
    <link rel="icon" href="assets/images/round.png" type="image/png">

    <meta charset="UTF-8"> <!-- specifies the character encoding for the document.-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- used for responsive web design -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .buttonn {
            background: transparent;
            width: 20%;
            max-width: 200px;
            min-width: 120px;
            border-radius: 50px;
            padding: 15px;
            cursor: pointer;
            border: 2px solid white;
            color: white;
            transition: all 0.3s ease-in-out;
            font-size: 1rem;
            text-decoration: none;
            text-align: center;
        }

        .buttonn:hover {
            background-color: white;
            color: black;
        }

        @media (max-width: 768px) {
            .buttonn {
                width: 100%;
                max-width: 250px;
                margin: 0 auto;
                display: block;

            }
        }

        .carousel-caption {
            bottom: auto !important;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Show it by default */
        .caption-overlay-box {
            display: block;
            background-color: transparent;
            /* on large screens, no black box */
        }

        /* Add black box only on small screens */
        @media (max-width: 768px) {
            .caption-overlay-box {
                background-color: rgba(0, 0, 0, 0.2);
                padding: 20px;
                border-radius: 10px;
            }
        }
    </style>

</head>

<body>
    <article id="article-head"> <!-- Header -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light" style="padding-top: 1rem; padding-bottom: 1rem;">

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
                            <a class="nav-link active" href="main.php">Home</a>
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

    <article> <!-- Carousel -->
        <div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active"
                    aria-label="Slide 1" aria-current="true"></button>
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="carousel-bg" style="background-image: url('assets/images/poses.jpg');"></div>
                    <div class="container">
                        <div class="carousel-caption text-start" style="color: white;">
                            <div class="caption-overlay-box">
                                <h1 style="color: white;">Tailored Care. Real Results</h1>
                                <p style="color: white;">
                                    Struggling with excess oil, clogged pores, and breakouts? Take the guesswork out of skincare — answer a few simple questions, and we’ll match you with personalized recommendations tailored to your unique skin needs.
                                </p>
                                <br>
                                <p><a class="buttonn" href="buildRoutine.php">Discover Your Skin</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="carousel-bg" style="background-image: url('assets/images/cute.jpg');"></div>
                    <div class="container">
                        <div class="carousel-caption text-start" style="color: white;">
                            <div class="caption-overlay-box">
                                <h1 style="color: white;">Combination Skin? Balance the Best of Both Worlds!</h1>
                                <p style="color: white;">Struggling with an oily T-zone but dry cheeks? You’re not alone. Discover skincare
                                    that targets both concerns—hydrating where needed and controlling excess shine for a
                                    healthy, even complexion.</p>
                                <br>
                                <p><a class="buttonn" href="mixskin.php">Balance Your Routine</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="carousel-bg" style="background-image: url('assets/images/pink.jpg');"></div>
                    <div class="container">
                        <div class="carousel-caption text-start" style="color: white;">
                            <div class="caption-overlay-box">
                                <h1 style="color: white;">Oily Skin? Say Goodbye to Shine!</h1>
                                <p style="color: white;">
                                    Struggling with excess oil, clogged pores, and breakouts? Our revolutionary formula
                                    controls shine,
                                    hydrates without greasiness, and leaves your skin fresh and balanced all day.
                                </p>
                                <br>
                                <p><a class="buttonn" href="oily.php">Control The Shine</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        </div>
    </article>

    <article> <!-- Popular products -->
        <section class="fade-section">
            <div class="container py-5">
                <div class="text-overlay p-3">
                    <h3 class="display-6 fw-bold text-center">Glovia's Best Picks</h3>
                </div>
                <div class="row g-4 align-items-stretch">
                    <?php
                    $productResult = $conn->query("SELECT id, name, skin_type, price, rating, num_reviews, image_path, product_url, description FROM products WHERE picks = 1");

                    // Check if there are any products and display them
                    while ($product = $productResult->fetch_assoc()) {
                    ?>
                        <!-- Product -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="product-card h-100 d-flex flex-column">
                                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
                                <div class="rating"><?php echo str_repeat("★", $product['rating']) . str_repeat("☆", 5 - $product['rating']); ?> (<?php echo htmlspecialchars($product['num_reviews']); ?>)</div>
                                <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p><?php echo htmlspecialchars($product['skin_type']); ?></p>
                                <p class="product-price">SAR<?php echo htmlspecialchars($product['price']); ?></p>
                                <div class="mt-auto">
                                    <a href="<?php echo htmlspecialchars($product['product_url']); ?>" class="view-button">View</a>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </section>
    </article>

    <article class="fade-section"> <!-- Photo with txt-->
        <section class="container my-3">
            <div class="row align-items-center">
                <!-- Left Side: Image -->
                <div class="col-md-6 text-center">
                    <img src="assets/images/group.jpeg" alt="Glovia Skincare Picks" class="img-fluid w-100 rounded">
                </div>
                <!-- Right Side: Text -->
                <div class="col-md-6 text-center position-relative">
                    <div class="text-overlay p-3">
                        <h3 class="display-6 fw-bold">Your Guide to Glowing Skin</h3>
                        <p class="lead">
                            At Glovia, we handpick the best skincare products from trusted brands — so you don’t have
                            to. Whether you’re into serums, sunscreens, or clean beauty, we’ve got the glow-getters
                            you’ll love.
                        </p>
                        <br>
                    </div>
                </div>
            </div>
        </section>
    </article>

    <article class="fade-section"> <!-- Commen Concerns -->
        <div class="container py-2">
            <div class="text-overlay p-3">
                <h3 class="display-6 fw-bold text-center">Common Concerns</h3>
                <p class="lead text-center">Not sure where to begin? Here are some common skin concerns..</p>
            </div>
            <div class="row g-4">
                <!-- First Concern -->
                <div class="col-md-3">
                    <div class="grid-tile">
                        <a href="redness.php" class="event-image">
                            <img src="assets/images/redness.webp" alt="Signs of redness">
                        </a>
                        <div class="subtitle">
                            <a href="redness.php">Redness →</a>
                        </div>
                    </div>
                </div>

                <!-- Second Concern -->
                <div class="col-md-3">
                    <div class="grid-tile">
                        <a href="dryness.php" class="event-image">
                            <img src="assets/images/dryness.webp" alt="Signs of Dryness">
                        </a>
                        <div class="subtitle">
                            <a href="dryness.php">Dryness →</a>
                        </div>
                    </div>
                </div>

                <!-- Third Concern -->
                <div class="col-md-3">
                    <div class="grid-tile">
                        <a href="scarring.php" class="event-image">
                            <img src="assets/images/scaring.webp" alt="Signs of Scarring">
                        </a>
                        <div class="subtitle">
                            <a href="scarring.php">Scarring →</a>
                        </div>
                    </div>
                </div>

                <!-- Forth Concern -->
                <div class="col-md-3">
                    <div class="grid-tile">
                        <a href="aging.php" class="event-image">
                            <img src="assets/images/aging.jpeg" alt="Signs of Aging">
                        </a>
                        <div class="subtitle">
                            <a href="aging.php">Aging →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </article>

    <article class="fade-section"> <!-- Customer reviews -->
        <section class="container my-5 text-center">
            <div class="text-overlay p-3">
                <h3 class="display-6 fw-bold text-center">What Our Clients Say</h3>
            </div>
            <p>Our clients love their results, and we are proud to share their experiences.</p>

            <div id="reviewCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    // Check if there are reviews and display them in the carousel
                    $isFirstItem = true;
                    while ($row = $result->fetch_assoc()) {
                        // Check if it's the first item to add 'active' class
                        $activeClass = $isFirstItem ? ' active' : '';
                        $isFirstItem = false;
                    ?>
                        <div class="carousel-item<?php echo $activeClass; ?>">
                            <div class="d-flex justify-content-center">
                                <div class="review-card">
                                    <p>Product Reviewer: <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>

                                    <p><?php echo htmlspecialchars($row['review']); ?></p>
                                    <p>Product Name: <strong><?php echo htmlspecialchars($row['product_name']); ?></strong><br>

                                    <div class="d-flex align-items-center">
                                        <div>
                                            <span class="stars">
                                                <?php
                                                // Display rating as stars
                                                echo str_repeat("★", $row['rating']) . str_repeat("☆", 5 - $row['rating']);
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#reviewCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#reviewCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>

            <br>
            <p>Join our community and share your experience with us!</p>
            <br>
            <div class="text-center">
                <a href="review.php" class="button text-center">Leave a Review</a>
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

    <!-- POP-UP MESSAGE -->
    <div id="welcome-popup" class="popup-overlay">
        <div class="popup-box">
            <h2>Welcome to Glovia!</h2>
            <p id="welcome-message"></p>
            <button onclick="closePopup()">Close</button>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="js/main.js"></script>

</body>

</html>