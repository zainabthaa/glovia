<?php
require_once 'includes/check_admin.php'; // loads security.php and checks admin rights
require_once 'includes/connect.php';


if (!isset($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit();
}



// Save an uploaded product image. Only real images (jpg, png, webp, gif, avif) are accepted.
// Returns the saved path, or null if there was no valid upload.
function save_product_image($file)
{
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true) || @getimagesize($file['tmp_name']) === false) {
        return null;
    }
    $base = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $path = 'assets/products/' . $base . '.' . $ext;
    return move_uploaded_file($file['tmp_name'], $path) ? $path : null;
}


// Ban user
if (isset($_POST['ban_user'])) {
    $email = $_POST['ban_user'];
    $stmt = $conn->prepare("UPDATE users SET banned = 1 WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_panel.php");
    exit();
}

// Unban user
if (isset($_POST['unban_user'])) {
    $email = $_POST['unban_user'];
    $stmt = $conn->prepare("UPDATE users SET banned = 0 WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_panel.php");
    exit();
}

// Make user admin
if (isset($_POST['make_admin'])) {
    $email = $_POST['make_admin'];
    $stmt = $conn->prepare("UPDATE users SET is_admin = 1 WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_panel.php");
    exit();
}

// Remove admin rights
if (isset($_POST['remove_admin'])) {
    $email = $_POST['remove_admin'];
    $stmt = $conn->prepare("UPDATE users SET is_admin = 0 WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_panel.php");
    exit();
}

// Delete review
if (isset($_POST['delete_review'])) {
    $review_id = intval($_POST['delete_review']);
    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->bind_param("i", $review_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_panel.php");
    exit();
}

// Add new product
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $skin_type = $_POST['skin_type'];
    $price = $_POST['price'];
    $rating = $_POST['rating'] ?? 0;
    $num_reviews = $_POST['num_reviews'] ?? 0;
    $description = $_POST['description'] ?? '';
    $product_url = $_POST['product_url'] ?? '';
    $picks = isset($_POST['picks']) ? 1 : 0;  // Handle the "Picks" checkbox

    // Image handling
    $image_path = save_product_image($_FILES['image'] ?? null) ?? '';

    $stmt = $conn->prepare("INSERT INTO products (name, skin_type, price, rating, num_reviews, image_path, product_url, description, picks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiisssi", $name, $skin_type, $price, $rating, $num_reviews, $image_path, $product_url, $description, $picks); // Add picks to the query
    $stmt->execute();
    $stmt->close();
    header("Location: admin_panel.php");
    exit();
}

// Delete product
if (isset($_POST['delete_product'])) {
    $id = intval($_POST['delete_product']);
    // Remove this product's likes first (the likes table references products)
    $stmt = $conn->prepare("DELETE FROM likes WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_panel.php");
    exit();
}


// Edit product - fetch product data
$edit_product = null;
if (isset($_GET['edit_product'])) {
    $id = intval($_GET['edit_product']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_product = $result->fetch_assoc();
    $stmt->close();
}

// Update product
if (isset($_POST['update_product'])) {
    $id = intval($_POST['product_id']);
    $name = $_POST['name'];
    $skin_type = $_POST['skin_type'];
    $price = $_POST['price'];
    $rating = $_POST['rating'] ?? 0;
    $num_reviews = $_POST['num_reviews'] ?? 0;
    $description = $_POST['description'] ?? '';
    $product_url = $_POST['product_url'] ?? '';
    $picks = isset($_POST['picks']) ? 1 : 0;

    // Handle image update if a new image was uploaded
    $image_path = save_product_image($_FILES['image'] ?? null) ?? $_POST['existing_image'];

    $stmt = $conn->prepare("UPDATE products SET name=?, skin_type=?, price=?, rating=?, num_reviews=?, image_path=?, product_url=?, description=?, picks=? WHERE id=?");
    $stmt->bind_param("ssdiisssii", $name, $skin_type, $price, $rating, $num_reviews, $image_path, $product_url, $description, $picks, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_panel.php");
    exit();
}

// Fetch all reviews
$reviews = $conn->query("SELECT id, name, rating, review, product_name, created_at FROM reviews ORDER BY created_at DESC");

// Fetch all users
$users = $conn->query("SELECT email, banned, is_admin FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Panel - Glovia</title>
    <link rel="stylesheet" href="css/styles.css" />
    <link rel="stylesheet" href="css/pages.css" />

    <link rel="icon" href="assets/images/round.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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
                            <a href="login.php" class="button text-center px-2 py-1" style="margin-right: 15px;">Account</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </article>

    <article class="px-3"> <!-- User Management -->
        <br><br>
        <h2>Admin Dashboard</h2>

        <h3 class="mt-4">User Management</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['banned'] ? 'Banned' : 'Active'; ?></td>
                        <td>
                            <?php if ($user['is_admin']): ?>
                                <form method="POST" action="admin_panel.php" class="d-inline"><?= csrf_field() ?><input type="hidden" name="remove_admin" value="<?php echo htmlspecialchars($user['email']); ?>"><button type="submit" class="btn btn-sm btn-secondary">Remove Admin</button></form>
                            <?php else: ?>
                                <?php if (!$user['banned']): ?>
                                    <form method="POST" action="admin_panel.php" class="d-inline"><?= csrf_field() ?><input type="hidden" name="ban_user" value="<?php echo htmlspecialchars($user['email']); ?>"><button type="submit" class="btn btn-sm btn-danger">Ban</button></form>
                                <?php else: ?>
                                    <form method="POST" action="admin_panel.php" class="d-inline"><?= csrf_field() ?><input type="hidden" name="unban_user" value="<?php echo htmlspecialchars($user['email']); ?>"><button type="submit" class="btn btn-sm btn-success">Unban</button></form>
                                <?php endif; ?>
                                <form method="POST" action="admin_panel.php" class="d-inline"><?= csrf_field() ?><input type="hidden" name="make_admin" value="<?php echo htmlspecialchars($user['email']); ?>"><button type="submit" class="btn btn-sm btn-warning">Make Admin</button></form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </article>

    <article class="px-3"> <!-- Review Management -->
        <h3 class="mt-5">Review Management</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Product</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($review['name']); ?></td>
                        <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                        <td><?php echo $review['rating']; ?></td>
                        <td><?php echo nl2br(htmlspecialchars($review['review'])); ?></td>
                        <td><?php echo $review['created_at']; ?></td>
                        <td><form method="POST" action="admin_panel.php" class="d-inline"><?= csrf_field() ?><input type="hidden" name="delete_review" value="<?php echo htmlspecialchars($review['id']); ?>"><button type="submit" class="btn btn-sm btn-danger">Delete</button></form></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </article>

    <article class="px-3"> <!-- Products -->
        <h3 class="mt-5">Product Management</h3>

        <!-- Add/Edit Product Form -->
        <form method="POST" action="admin_panel.php" enctype="multipart/form-data" class="mb-4">
<?= csrf_field() ?>
            <?php if ($edit_product): ?>
                <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_product['image_path']); ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Product Name"
                        value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="skin_type" class="form-control" placeholder="Skin Type"
                        value="<?php echo $edit_product ? htmlspecialchars($edit_product['skin_type']) : ''; ?>" required>
                </div>
                <div class="col-md-4">
                    <input type="number" name="price" step="0.01" class="form-control" placeholder="Price (SAR)"
                        value="<?php echo $edit_product ? htmlspecialchars($edit_product['price']) : ''; ?>" required>
                </div>
                <div class="col-md-4">
                    <select name="rating" class="form-select" required>
                        <option value="">Select rating</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>"
                                <?php if ($edit_product && $edit_product['rating'] == $i) echo 'selected'; ?>>
                                <?php echo str_repeat('★', $i) . str_repeat('☆', 5 - $i) . " ($i)"; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="product_url" class="form-control" placeholder="Product URL"
                        value="<?php echo $edit_product ? htmlspecialchars($edit_product['product_url']) : ''; ?>">
                </div>
                <div class="col-md-4">
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <?php if ($edit_product && $edit_product['image_path']): ?>
                        <small class="text-muted">Current image: <?php echo basename($edit_product['image_path']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <textarea name="description" class="form-control" rows="2" placeholder="Description"><?php
                                                                                                            echo $edit_product ? htmlspecialchars($edit_product['description']) : '';
                                                                                                            ?></textarea>
                </div>
                <div class="col-md-3">
                    <input type="checkbox" name="picks" value="1"
                        <?php if ($edit_product && $edit_product['picks']) echo 'checked'; ?>> Glovia's Best Picks
                </div>
                <div class="col-md-3">
                    <?php if ($edit_product): ?>
                        <button type="submit" name="update_product" class="btn btn-warning">Update Product</button>
                        <a href="admin_panel.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- Product Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Skin Type</th>
                    <th>Price</th>
                    <th>Rating</th>
                    <th>Picks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $products = $conn->query("SELECT * FROM products ORDER BY id DESC");
                while ($prod = $products->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prod['name']); ?></td>
                        <td><?php echo htmlspecialchars($prod['skin_type']); ?></td>
                        <td><?php echo htmlspecialchars($prod['price']); ?></td>
                        <td><?php echo $prod['rating']; ?></td>
                        <td><?php echo $prod['picks'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <a href="admin_panel.php?edit_product=<?php echo $prod['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            <form method="POST" action="admin_panel.php" class="d-inline"><?= csrf_field() ?><input type="hidden" name="delete_product" value="<?php echo htmlspecialchars($prod['id']); ?>"><button type="submit" class="btn btn-sm btn-danger">Delete</button></form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </article>

</body>

</html>