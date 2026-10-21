<?php
require_once 'includes/security.php';
require_once 'includes/connect.php';

$recommendation = '';
$recommended_products = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simple scoring system based on answers:

    $answers = $_POST['answers'] ?? [];

    // Initialize counters
    // Categories to track
    $scores = [
        'Dryness' => 0,
        'Redness' => 0,
        'Oily' => 0,
        'Combo' => 0,
        'Normal' => 0,
        'Scarring' => 0
    ];

    // Map answer values to categories
    foreach ($answers as $answer) {
        if (isset($scores[$answer])) {
            $scores[$answer]++;
        }
    }

    // Get category with highest score
    arsort($scores);
    $top_category = key($scores);

    $recommendation = $top_category;

    // Fetch products from DB based on skin_type
    $stmt = $conn->prepare("SELECT * FROM products WHERE skin_type = ? ORDER BY id DESC");
    $stmt->bind_param("s", $top_category);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $recommended_products[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Find Your Skin Type - Glovia</title>
    <link rel="stylesheet" href="css/pages.css" />
    <link rel="stylesheet" href="css/cards.css" />
    <link rel="stylesheet" href="css/popup.css" />
    <link rel="stylesheet" href="css/questions.css" />
    <link rel="stylesheet" href="css/contact.css" />

    <link rel="icon" href="assets/images/round.png" type="image/png" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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

    <section class="hero-section fade-section" style="position: relative; height: 60vh; background-image: url('assets/images/poses.jpg'); background-size: cover; background-position: center;">
        <!-- Text for large screens (no box) -->
        <div id="textLarge" style="color: white; text-align: center; padding: 2rem;">
            <h3 class="display-6 fw-bold text-center">Discover Your Skin Type</h3>
            <p class="lead">Take this quick quiz to find out what skincare products suit you best!</p>
        </div>

        <!-- Boxed text for small screens only -->
        <div id="overlayBox" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 800px; width: 80%; background-color: rgba(0, 0, 0, 0.2); padding: 1.5rem 2rem; border-radius: 1rem; text-align: center; color: white;">
            <h3 class="display-6 fw-bold text-center">Discover Your Skin Type</h3>
            <p class="lead">Take this quick quiz to find out what skincare products suit you best!</p>
        </div>

        <style>
            /* Hide large text on small screens */
            @media (max-width: 768px) {
                #textLarge {
                    display: none;
                }

                #overlayBox {
                    display: block !important;
                }
            }
        </style>
    </section>

    <main class="container my-5">

        <?php if (!$recommendation): ?>
            <!-- Quiz Form -->
            <form method="POST" action="">
<?= csrf_field() ?>
                <div class="questions-wrapper">

                    <!-- Question 1 -->
                    <div class="mb-4 question-block">
                        <label class="form-label fw-semibold">1. How does your skin usually feel by midday?</label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q1]" id="q1a1" value="Oily" required>
                                <label class="form-check-label" for="q1a1">Shiny and greasy</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q1]" id="q1a2" value="Dry">
                                <label class="form-check-label" for="q1a2">Tight and dry</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q1]" id="q1a3" value="Combination">
                                <label class="form-check-label" for="q1a3">Oily in some areas, dry in others</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q1]" id="q1a4" value="Redness">
                                <label class="form-check-label" for="q1a4">Red or irritated easily</label>
                            </div>
                        </div>
                    </div>

                    <!-- Question 2 -->
                    <div class="mb-4 question-block hidden">
                        <label class="form-label fw-semibold">2. How often do you experience breakouts?</label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q2]" id="q2a1" value="Oily" required>
                                <label class="form-check-label" for="q2a1">Frequently, especially on forehead and chin</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q2]" id="q2a2" value="Dry">
                                <label class="form-check-label" for="q2a2">Rarely</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q2]" id="q2a3" value="Combination">
                                <label class="form-check-label" for="q2a3">Sometimes in oily areas only</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q2]" id="q2a4" value="Redness">
                                <label class="form-check-label" for="q2a4">Breakouts accompanied by redness or irritation</label>
                            </div>
                        </div>
                    </div>

                    <!-- Question 3 -->
                    <div class="mb-4 question-block hidden">
                        <label class="form-label fw-semibold">3. How does your skin react to new skincare products?</label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q3]" id="q3a1" value="Redness" required>
                                <label class="form-check-label" for="q3a1">I get redness, itching, or irritation easily</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q3]" id="q3a2" value="Dry">
                                <label class="form-check-label" for="q3a2">Usually fine, but sometimes dry patches appear</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q3]" id="q3a3" value="Oily">
                                <label class="form-check-label" for="q3a3">No issues, but skin gets oily</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q3]" id="q3a4" value="Combination">
                                <label class="form-check-label" for="q3a4">Some areas react more than others</label>
                            </div>
                        </div>
                    </div>

                    <!-- Question 4 -->
                    <div class="mb-4 question-block hidden">
                        <label class="form-label fw-semibold">4. How does your skin feel after cleansing?</label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q4]" id="q4a1" value="Dryness" required>
                                <label class="form-check-label" for="q4a1">Tight and flaky</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q4]" id="q4a2" value="Oily">
                                <label class="form-check-label" for="q4a2">Still greasy or shiny</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q4]" id="q4a3" value="Combo">
                                <label class="form-check-label" for="q4a3">Tight in some areas, oily in others</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q4]" id="q4a4" value="Normal">
                                <label class="form-check-label" for="q4a4">Feels fresh and normal</label>
                            </div>
                        </div>
                    </div>

                    <!-- Question 5 -->
                    <div class="mb-4 question-block hidden">
                        <label class="form-label fw-semibold">5. Do you have any visible marks or post-acne dark spots?</label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q5]" id="q5a1" value="Scarring" required>
                                <label class="form-check-label" for="q5a1">Yes, I have noticeable acne scars</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q5]" id="q5a2" value="Oily">
                                <label class="form-check-label" for="q5a2">I still get acne sometimes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q5]" id="q5a3" value="Redness">
                                <label class="form-check-label" for="q5a3">I get red marks, but no scars</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q5]" id="q5a4" value="Normal">
                                <label class="form-check-label" for="q5a4">No major issues</label>
                            </div>
                        </div>
                    </div>

                    <!-- Question 6 -->
                    <div class="mb-4 question-block hidden">
                        <label class="form-label fw-semibold">6. What’s your skincare goal?</label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q6]" id="q6a1" value="Dryness" required>
                                <label class="form-check-label" for="q6a1">Hydrate dry skin</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q6]" id="q6a2" value="Redness">
                                <label class="form-check-label" for="q6a2">Calm irritation or redness</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q6]" id="q6a3" value="Scarring">
                                <label class="form-check-label" for="q6a3">Fade acne marks or scars</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="answers[q6]" id="q6a4" value="Normal">
                                <label class="form-check-label" for="q6a4">General care for healthy skin</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="button">See Recommendations</button>
                </div>

            </form>

        <?php else: ?>
            <!-- Show Result and Recommended Products -->
            <section class="text-center mb-5">
                <h2>Your skin type appears to be: <span class="text-primary"><?= htmlspecialchars($recommendation) ?></span></h2>
                <p class="lead">Based on your answers, we recommend these products for your skin.</p>
            </section>

            <article class="fade-section"> <!-- Recommended Products using Database -->
                <br>
                <div class="text-overlay p-3">
                    <h3 class="display-6 fw-bold text-center">Recommended Products</h3>
                </div>

                <div class="container py-2">
                    <div class="row gy-4">
                        <?php
                        // Use $recommendation to fetch products dynamically
                        require_once 'includes/connect.php';
                        $stmt = $conn->prepare("SELECT * FROM products WHERE skin_type = ? ORDER BY id DESC");
                        $stmt->bind_param("s", $recommendation);
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
                                            <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Product Image" class="product-image">
                                            <br><br>
                                            <h5 class="product-title"><?= htmlspecialchars($row['name']) ?></h5>
                                            <p class="product-price">SAR<?= htmlspecialchars($row['price']) ?></p>
                                        </div>
                                        <div class="right-side d-flex flex-column justify-content-between" style="height: 100%;">
                                            <div class="product-description-wrapper mb-2">
                                                <div class="product-description short-description">
                                                    <?= htmlspecialchars($row['description']) ?>
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
                            echo "<p class='text-center'>Sorry, no products found for your skin type yet.</p>";
                        endif;
                        $stmt->close();
                        ?>
                    </div>
                </div>

                <script>
                    const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

                    document.querySelectorAll('.btn-heart').forEach(button => {
                        button.addEventListener('click', () => {
                            if (!isLoggedIn) {
                                // You can implement your login popup here
                                alert('Please log in to like products.');
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
                </script>
            </article>

            <div class="text-center mt-5">
                <a href="" class="button">Take Quiz Again</a>
            </div>

        <?php endif; ?>
    </main>

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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const questionBlocks = document.querySelectorAll('.question-block');
            const wrapper = document.querySelector('.questions-wrapper');
            let currentIndex = 0;

            function showQuestion(index) {
                questionBlocks.forEach((block, i) => {
                    if (i === index) {
                        block.classList.remove('hidden');
                        wrapper.style.height = block.offsetHeight + 'px'; // set wrapper height
                    } else {
                        block.classList.add('hidden');
                    }
                });
            }

            showQuestion(currentIndex);

            questionBlocks.forEach((block, index) => {
                const inputs = block.querySelectorAll('input[type="radio"]');
                inputs.forEach(input => {
                    input.addEventListener('change', () => {
                        if (index < questionBlocks.length - 1) {
                            currentIndex = index + 1;
                            showQuestion(currentIndex);
                        }
                    });
                });
            });
        });
    </script>

    <script src="js/main.js"></script>

</body>

</html>