<?php
header('Content-Type: application/json');
include 'db.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('sssss', $name, $email, $phone, $subject, $message);
$success = $stmt->execute();

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
} 