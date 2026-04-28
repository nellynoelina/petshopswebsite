<?php
require 'db.php';

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

header('Content-Type: application/json');
echo json_encode($products);
?>
