<?php
require_once 'includes/security.php';
require_once 'includes/connect.php'; // Make sure $conn is your mysqli connection

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Step 1: Fetch liked product IDs
$stmt = $conn->prepare('SELECT product_id FROM likes WHERE user_id = ?');
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$product_ids = [];
while ($row = $result->fetch_assoc()) {
    $product_ids[] = $row['product_id'];
}

$products = [];

if (!empty($product_ids)) {
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed for product select: " . $conn->error);
    }

    $types = str_repeat('i', count($product_ids));
    $refs = [];
    foreach ($product_ids as $key => $value) {
        $refs[$key] = &$product_ids[$key];
    }
    array_unshift($refs, $types);
    call_user_func_array([$stmt, 'bind_param'], $refs);

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Liked Products</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/pages.css" />
    <link rel="stylesheet" href="css/popup.css">
    <link rel="stylesheet" href="css/contact.css" />


    <link rel="icon" href="assets/images/round.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .short-description {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
        }

        .read-more-btn {
            font-size: 0.9rem;
            color: #007bff;
            background: none;
            border: none;
            cursor: pointer;
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

    <section class="intro-section"> <!-- Intro Section-->
        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div class="text-section">
                <h3 class="display-6 fw-bold text-center">Your Liked Products</h3>
                <p class="lead text-center">Your personal collection of skin-loving products.</p>

            </div>
            <div class="image-section">
                <img src="assets/images/dry_5.webp" alt="Skin Care Image" class="img-fluid"> <!-- Added img-fluid for responsiveness -->
            </div>
        </div>
    </section>


    <article>
        <br>
        <div class="text-overlay p-3">
            <p class="lead text-center">Browse the products you’ve liked and explore more tailored to your skin.</p>
        </div>

        <?php if (!empty($products)): ?>
            <div class="container py-2">
                <div class="row gy-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-12 col-md-6">
                            <div class="product-card">
                                <div class="left-side">
                                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" class="product-image">
                                    <br><br>
                                    <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="product-price">SAR <?php echo htmlspecialchars($product['price']); ?></p>
                                </div>
                                <div class="right-side">
                                    <div class="product-description-wrapper mb-2">
                                        <div class="product-description short-description">
                                            <?php echo htmlspecialchars($product['description']); ?>
                                        </div>
                                        <button class="read-more-btn btn btn-link p-0 mt-1">Read More</button>
                                    </div>


                                    <!-- Heart Button -->
                                    <button type="button" class="btn-heart" data-product-id="<?= $product['id']; ?>" aria-label="Like" style="background:none; border:none; padding:0; cursor:pointer; height:50px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                            fill="red"
                                            stroke="red"
                                            stroke-width="1.5" class="bi bi-heart" viewBox="0 0 16 16">
                                            <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01z" />
                                        </svg>


                                    </button>

                                    <a href="<?php echo htmlspecialchars($product['product_url']); ?>" target="_blank">
                                        <button class="button-right">View</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center mt-4">You have not liked any products yet.</p>
        <?php endif; ?>



        <script>
            const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

            function closePopup() {
                document.getElementById('loginPopup').style.display = 'none';
            }

            document.querySelectorAll('.btn-heart').forEach(button => {
                button.addEventListener('click', () => {
                    if (!isLoggedIn) {
                        document.getElementById('loginPopup').style.display = 'flex';
                        return;
                    }

                    const productId = button.getAttribute('data-product-id');
                    fetch('toggle_like.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-CSRF-Token': <?= json_encode(csrf_token()) ?>,
                            },
                            body: `product_id=${productId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            const svg = button.querySelector('svg');
                            if (data.status === 'liked') {
                                svg.setAttribute('fill', 'red');
                                svg.setAttribute('stroke', 'red');
                            } else if (data.status === 'unliked') {
                                svg.setAttribute('fill', 'none');
                                svg.setAttribute('stroke', 'currentColor');
                            }
                        })
                        .catch(err => console.error('Error:', err));
                });
            });

            function showLoginPopup() {
                document.getElementById('loginPopup').style.display = 'flex';
            }

            function closePopup() {
                document.getElementById('loginPopup').style.display = 'none';
            }


            document.querySelectorAll('.read-more-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const description = button.previousElementSibling;
                    description.classList.toggle('short-description');
                    button.textContent = description.classList.contains('short-description') ? 'Read More' : 'Show Less';
                });
            });
        </script>

        <br>
        <br>

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

    <script src="js/main.js"></script>

</body>

</html>