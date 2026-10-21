<?php
require_once 'includes/security.php';

$showThankYou = false;

if (isset($_SESSION['show_thank_you'])) {
    $showThankYou = true;
    unset($_SESSION['show_thank_you']);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once 'includes/connect.php'; // provides $conn

    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>
            alert('All fields are required.');
            window.location.href = 'contact.php';
        </script>";
        exit;
    }

    $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        $_SESSION['show_thank_you'] = true;
        header("Location: contact.php");
        exit;
    } else {
        $_SESSION['form_error'] = "Unable to send message.";
        header("Location: contact.php");
        exit;
    }


    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - Glovia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/contact.css?v=5" />
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/popup.css" />
    <link rel="stylesheet" href="css/pages.css" />

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
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

    <section class="hero-section fade-section">
        <div class="container text-center fade-section">
            <div class="p-3">
                <h3 class="display-6 fw-bold text-center">Welcome to the Help Center</h3>
                <p class="lead text-center">Search for answers or contact us directly</p>
            </div>

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
            <div class="search-container fade-section">
                <input type="text" id="helpSearch" class="search-input" placeholder="Search for a topic (e.g., privacy, complaints)">
                <button class="buttonn" onclick="handleSearch()">Search</button>
            </div>
        </div>
    </section>

    <article class="fade-section"> <!-- FAQ -->
        <div class="container mt-5">
            <div class="row m-3">
                <div class="col-md-6 p-3 boxed">
                    <div class="text-overlay p-3">
                        <h3 class="display-6 fw-bold">Frequently Asked Questions</h3>
                        <p class="lead">We understand that choosing the perfect parts can be complex, so we gathered the
                            most common questions to help guide you.</p>
                    </div>
                </div>

                <div class="col-md-6 boxed">
                    <div class="faq-item">
                        <div class="faq-question">Do I pay more by using your links?</div>
                        <div class="faq-answer">Not at all! You pay the same price as everyone else. In some cases, you
                            may even get a discount.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">How do you choose which products to recommend?</div>
                        <div class="faq-answer">We only recommend products we've researched or tested ourselves, or that
                            are trusted by experts in the field.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">Can I trust your product recommendations?</div>
                        <div class="faq-answer">Absolutely. We prioritize honesty and value over profit. Our goal is to
                            help you make informed decisions.</div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">Will I be redirected to another website?</div>
                        <div class="faq-answer">Yes, clicking an affiliate link takes you to the merchant’s website
                            where the purchase is made.</div>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <!-- Contact Form Section -->
    <section class="container my-5 text-center fade-section" id="contact">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-overlay p-3">
                    <h3 class="display-6 fw-bold text-center">Still have questions?</h3>
                    <p class="lead text-center">Fill out the form below, and our team will get back to you.</p>
                </div>

                <form id="contactForm" class="fade-up" action="contact.php" method="POST">
<?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Name:</label>
                        <input type="text" class="form-control custom-input" id="name" name="name" placeholder="Enter your name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" class="form-control custom-input" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message:</label>
                        <textarea class="form-control custom-input" id="message" name="message" rows="4" placeholder="Enter your message" required></textarea>
                    </div>
                    <button type="submit" class="button">Send</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Thank You Modal -->
    <div id="thankYouPopup" class="popup-overlay" style="display: none;">
        <div class="popup-box">
            <h2>Thank You!</h2>
            <p>Your message has been submitted. We'll get back to you shortly!</p>
            <button onclick="closeThankYouPopup()">Close</button>
        </div>
    </div>

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
                                <li><a href="about.php" style="text-decoration: none; color: #666;">Behind Glovia</a></li>
                                <li><a href="buildRoutine.php" style="text-decoration: none; color: #666;">Discover Your Skin</a></li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <br>
                            <h5 style="font-weight: 600; color: #333;">Contact & Support</h5>
                            <ul class="list-unstyled" style="color: #666;">
                                <li><a href="contact.php" style="text-decoration: none; color: #666;">Contact Us</a></li>
                                <li><a href="terms.php" style="text-decoration: none; color: #666;">Terms & Conditions</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </article>

    <!-- JavaScript -->
    <script src="js/main.js"></script>

    <script>
        function handleSearch() {
            const input = document.getElementById("helpSearch").value.toLowerCase();
            if (input.includes("privacy")) {
                window.location.href = "terms.php#introduction";
            } else if (input.includes("complaint") || input.includes("issue")) {
                window.location.href = "contact.php#contact";
            } else if (input.includes("introduction")) {
                window.location.href = "terms.php";
            } else if (input.includes("responsibilities")) {
                window.location.href = "terms.php#introduction";
            } else if (input.includes("limitation")) {
                window.location.href = "terms.php#responsibilities";
            } else if (input.includes("disclaimer")) {
                window.location.href = "terms.php#privacy";
            } else {
                alert("Sorry, we couldn't find a match for your question.");
            }
        }

        // FAQ toggle
        document.querySelectorAll('.faq-question').forEach((question) => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                answer.classList.toggle('show');
            });
        });

        // Thank You Modal on Form Submit
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!name || !email || !message) {
                e.preventDefault(); // only prevent if something is missing
                alert('Please fill out all fields.');
            }
        });
    </script>

    <script>
        function closeThankYouPopup() {
            document.getElementById('thankYouPopup').style.display = 'none';
        }

        <?php if ($showThankYou ?? false): ?>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('thankYouPopup').style.display = 'flex';
            });
        <?php endif; ?>
    </script>


</body>

</html>