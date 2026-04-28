<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

header('Content-Type: application/json');
echo json_encode($_SESSION['cart']);
