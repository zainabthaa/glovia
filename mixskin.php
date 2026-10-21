<?php
require_once 'includes/security.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Combination Skin Concerns - Glovia</title>
    <link rel="stylesheet" href="css/pages.css" />
    <link rel="stylesheet" href="css/cards.css" />
    <link rel="stylesheet" href="css/popup.css" />

    <link rel="icon" href="assets/images/round.png" type="image/png">


    <meta charset="UTF-8"> <!-- specifies the character encoding for the document.-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- used for responsive web design -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
                    <div class="d-flex align-items-center" style="margin-left: auto;">
                        <?php if (isset($_SESSION['username'])): ?>
                            <a href="liked.php" class="initial-circle me-2" title="View Liked Products" style="text-decoration: none;">
                                <?php echo htmlspecialchars(strtoupper($_SESSION['username'][0])); ?>
                            </a>
                            <a href="logout.php" class="btn" style="margin-right: 15px;">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class="button text-center px-2 py-1" style="margin-right: 30px;">Account</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </article>

    <article>
        <header class="about-hero" style="background-image: url('assets/images/mixHeader.webp');">
            <div class="overlay">
                <h1>Balance the Blend and Glow</h1>
                <p class="mt-3">
                    Combination skin can be tricky—but with the right products, harmony is possible. Glovia helps you manage oiliness while keeping dry areas soothed and hydrated. </p>
            </div>
        </header>
    </article>

    <article> <!-- Intro -->
        <section class="redness-intro-section py-5">
            <div class="container text-center">
                <h2 class="section-heading mb-3">What is Combination Skin?</h2>
                <p class="lead mb-4">
                    Combination skin includes both oily and dry/normal zones, often with an oily T-zone (forehead, nose, chin) and drier cheeks. The key is finding a skincare routine that treats both concerns without overcorrecting.
                </p>
            </div>
        </section>
    </article>


    <article> <!-- Info Cards -->
        <div class="carousel-container" id="carousel">
            <!-- Card 1 -->
            <div class="redness-card blur">
                <img src="assets/images/tzone.webp" alt="T-Zone" />
                <div class="text-section">
                    <h3>Excess Oil Production</h3>
                    <p>
                        Combination skin often features an oily T-zone (forehead, nose, and chin) while the cheeks remain normal or dry.
                        This dual nature requires products that balance oil without over-drying.
                    </p>
                    <div class="treatment-tip">
                        <strong>Tip:</strong> Use a gentle cleanser and a lightweight, oil-free moisturizer for the T-zone.
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="redness-card active">
                <img src="assets/images/pores.webp" alt="Large Pores" />
                <div class="text-section">
                    <h3>Acne and Breakouts</h3>
                    <p>
                        While the T-zone may be oily, the cheeks often feel dry or tight. This imbalance can make product selection tricky.
                        Hydrating products focused on drier areas can help even out skin texture.
                    </p>
                    <div class="treatment-tip">
                        <strong>Tip:</strong> Apply richer creams or serums to the cheeks and a lighter formula to the oily areas.
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="redness-card blur">
                <img src="assets/images/pH.jpg" alt="pH Imbalance" />
                <div class="text-section">
                    <h3>Maintaining pH Balance</h3>
                    <p>
                        Combination skin is prone to imbalance—oily zones may become oilier while dry zones get dehydrated.
                        Maintaining the skin's natural pH helps support its barrier and regulate oil production.
                    </p>
                    <div class="treatment-tip">
                        <strong>Tip:</strong> Use pH-balanced cleansers (around 5.5) and avoid harsh toners that strip the skin.
                    </div>
                </div>
            </div>

        </div>


        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.getElementById('carousel');
                const cards = container.querySelectorAll('.redness-card');

                function updateCards() {
                    const containerCenter = container.scrollLeft + container.offsetWidth / 2;

                    let closestCard = null;
                    let closestDistance = Infinity;

                    cards.forEach(card => {
                        const cardCenter = card.offsetLeft + card.offsetWidth / 2;
                        const distance = Math.abs(containerCenter - cardCenter);

                        if (distance < closestDistance) {
                            closestDistance = distance;
                            closestCard = card;
                        }
                    });

                    cards.forEach(card => {
                        if (card === closestCard) {
                            card.classList.remove('blur');
                            card.classList.add('active');
                        } else {
                            card.classList.add('blur');
                            card.classList.remove('active');
                        }
                    });
                }

                function centerActiveCard() {
                    const activeCard = container.querySelector('.redness-card.active');
                    if (activeCard) {
                        const scrollPos = activeCard.offsetLeft - (container.offsetWidth / 2) + (activeCard.offsetWidth / 2);
                        container.scrollTo({
                            left: scrollPos,
                            behavior: 'smooth'
                        });
                    }
                }

                // On load: update cards and center the active one
                // On load: start on the card marked "active" in the HTML (the middle one), centred
                const startCard = container.querySelector('.redness-card.active');
                if (startCard) {
                    container.scrollTo({ left: startCard.offsetLeft - (container.offsetWidth / 2) + (startCard.offsetWidth / 2), behavior: 'instant' });
                }
                updateCards();

                // On scroll: update active/blur states
                container.addEventListener('scroll', () => {
                    updateCards();
                });

                // On window resize, re-center active card
                window.addEventListener('resize', () => {
                    updateCards();
                    centerActiveCard();
                });

                // NEW: Add click listeners on cards to make clicked card active and center it
                cards.forEach(card => {
                    card.style.cursor = 'pointer'; // show pointer cursor
                    card.addEventListener('click', () => {
                        cards.forEach(c => {
                            c.classList.add('blur');
                            c.classList.remove('active');
                        });
                        card.classList.remove('blur');
                        card.classList.add('active');

                        // Center the clicked card
                        const scrollPos = card.offsetLeft - (container.offsetWidth / 2) + (card.offsetWidth / 2);
                        container.scrollTo({
                            left: scrollPos,
                            behavior: 'smooth'
                        });
                    });
                });
            });
        </script>
    </article>

    <article class="fade-section"> <!-- Recommended Products using Database -->
        <br>
        <div class="text-overlay p-3">
            <h3 class="display-6 fw-bold text-center">Recommended Products</h3>
        </div>

        <div class="container py-2">
            <div class="row gy-4">
                <?php
                require_once 'includes/connect.php';
                $stmt = $conn->prepare("SELECT * FROM products WHERE skin_type = 'Combo' ORDER BY id DESC");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):

                        $liked = false;
                        if (isset($_SESSION['user_id'])) {
                            $check = $conn->prepare("SELECT 1 FROM likes WHERE user_id = ? AND product_id = ?");
                            $check->bind_param("ii", $_SESSION['user_id'], $row['id']);
                            $check->execute();
                            $liked = $check->get_result()->num_rows > 0;
                        }
                ?>
                        <div class="col-12 col-md-6">
                            <div class="product-card">
                                <div class="left-side">
                                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Product Image" class="product-image">
                                    <br><br>
                                    <h5 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                    <p class="product-price">SAR<?php echo htmlspecialchars($row['price']); ?></p>
                                </div>
                                <div class="right-side d-flex flex-column justify-content-between" style="height: 100%;">
                                    <div class="product-description-wrapper mb-2">
                                        <div class="product-description short-description">
                                            <?php echo htmlspecialchars($row['description']); ?>
                                        </div>
                                        <button class="read-more-btn btn btn-link p-0 mt-1">Read More</button>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mt-auto action-buttons">
                                        <!-- Heart Button -->
                                        <button type="button" class="btn-heart" data-product-id="<?= $row['id']; ?>" aria-label="Like"
                                            style="background: none; border: none; padding: 0; cursor: pointer; height: 40px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                                fill="<?= $liked ? 'red' : 'none'; ?>"
                                                stroke="<?= $liked ? 'red' : 'currentColor'; ?>"
                                                stroke-width="1.5" class="bi bi-heart" viewBox="0 0 16 16">
                                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357
                3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01z" />
                                            </svg>
                                        </button>

                                        <!-- View Button -->
                                        <a href="<?= htmlspecialchars($row['product_url']); ?>" target="_blank">
                                            <button class="button-right">View</button>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                <?php
                    endwhile;
                else:
                    echo "<p class='text-center'>No Redness products found.</p>";
                endif;
                $stmt->close();
                ?>
            </div>
        </div>

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
        </script>
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

    <!-- Login Required Popup -->
    <div class="popup-overlay" id="loginPopup">
        <div class="popup-box">
            <h2>Login Required</h2>
            <p>You must be logged in to like this product.</p>
            <a href="login.php" style="text-decoration: none;">
                <button type="button">Go to Login</button>
            </a>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.read-more-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const wrapper = btn.closest('.product-description-wrapper');
                    const desc = wrapper.querySelector('.product-description');
                    desc.classList.toggle('short-description');
                    btn.textContent = desc.classList.contains('short-description') ? 'Read More' : 'Read Less';
                });
            });
        });
    </script>

</body>

</html>