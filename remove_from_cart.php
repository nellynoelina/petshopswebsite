<?php
session_start();
header('Content-Type: application/json');

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$product_id = $data['product_id'] ?? null;

if ($product_id === null) {
    echo json_encode(["message" => "Invalid product ID"]);
    exit;
}

// Remove product from cart
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['cart'][$index]);
            // Re-index the array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            echo json_encode(["message" => "Item removed from cart"]);
            exit;
        }
    }
}

echo json_encode(["message" => "Item not found"]);
