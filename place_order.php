<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$fullname = $data['fullname'] ?? '';
$address = $data['address'] ?? '';
$contact = $data['contact'] ?? '';
$cart = $data['cart'] ?? [];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$fullname || !$address || !$contact || empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields or cart is empty']);
    exit();
}

$order_data = json_encode($cart);
$total = 0;
foreach ($cart as $item) {
    $total += floatval($item['price']) * intval($item['quantity']);
}

$stmt = $conn->prepare("INSERT INTO orders (user_id, fullname, contact, address, order_data, total) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssd", $user_id, $fullname, $contact, $address, $order_data, $total);
$success = $stmt->execute();

if ($success) {
    // Clear the cart
    $_SESSION['cart'] = [];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}