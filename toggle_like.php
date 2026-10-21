<?php
require_once 'includes/security.php';
require_once 'includes/connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Not authorized
    echo json_encode(["status" => "error", "message" => "Please log in to like products."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);

// Check if already liked
$stmt = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Unlike
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    echo json_encode(["status" => "unliked"]);
} else {
    // Like
    $stmt = $conn->prepare("INSERT INTO likes (user_id, product_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    echo json_encode(["status" => "liked"]);
}
?>
