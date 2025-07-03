<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data['product_id'] ?? null;

if (!$product_id) {
    echo json_encode(["message" => "No product ID provided"]);
    exit();
}

// Fetch product from database
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if ($product) {
    $found = false;
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    // Check if already in cart
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $product['quantity'] = 1;
        $_SESSION['cart'][] = $product;
    }
    echo json_encode(["message" => "Item added to cart"]);
} else {
    echo json_encode(["message" => "Product not found"]);
}
