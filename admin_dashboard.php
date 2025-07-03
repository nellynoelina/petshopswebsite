<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}//to make sure only admin access

include("db.php"); // Make sure this connects to your 'petshop' database

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit();
}

// Handle Add Product
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    mysqli_query($conn, "INSERT INTO products (name, description, price, image) VALUES ('$name', '$desc', '$price', '$image')");
    header("Location: admin_dashboard.php");
    exit();
}

// Handle Edit Product
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    mysqli_query($conn, "UPDATE products SET name='$name', description='$desc', price='$price', image='$image' WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch Products
$products = mysqli_query($conn, "SELECT * FROM products");

// Fetch Messages
$messages = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");

// Add after the main dashboard content
require 'db.php';
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - PetShop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if (isset($_SESSION['username'])): ?>
      <div class="user-info">
        <img src="images/user-icon.png" alt="User" class="user-icon">
        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
      </div>
    <?php endif; ?>
    <a href="logout.php" class="logout-link">Logout</a>
    <a href="index.html" class="logout-link main-page-link"> Main Page</a>
    <div class="dashboard-container">
        <h1>Welcome Admin: <?php echo $_SESSION['username']; ?></h1>

        <div class="add-product-section">
            <h2>Add New Product</h2>
            <form method="POST" class="add-product-form">
                <input type="text" name="name" placeholder="Product Name" required>
                <input type="text" name="description" placeholder="Description" required>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <input type="text" name="image" placeholder="Image filename (e.g., cat food bowl.png)" required>
                <button type="submit" name="add">Add Product</button>
            </form>
            <small>Enter only the filename. Images should be in the 'images' folder.</small>
        </div>

        <div class="products-table-section">
            <h2>All Products</h2>
            <table>
                <tr>
                    <th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Image</th><th>Actions</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($products)) { ?>
                <tr>
                    <form method="POST">
                        <td><?php echo $row['id']; ?><input type="hidden" name="id" value="<?php echo $row['id']; ?>"></td>
                        <td><input type="text" name="name" value="<?php echo $row['name']; ?>"></td>
                        <td><input type="text" name="description" value="<?php echo $row['description']; ?>"></td>
                        <td><input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>"></td>
                        <td><input type="text" name="image" value="<?php echo $row['image']; ?>"></td>
                        <td>
                            <button type="submit" name="update">Update</button>
                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')" style="color:#f44336; font-weight:600; text-decoration:none;">Delete</a>
                        </td>
                    </form>
                </tr>
                <?php } ?>
            </table>
        </div>

        <div class="orders-section">
            <h2 class="orders-title">Recent Orders</h2>
            <table class="orders-table">
                <thead>
                    <tr class="orders-table-header">
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Products</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = $orders->fetch_assoc()): ?>
                        <tr class="orders-table-row">
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo $order['created_at']; ?></td>
                            <td><?php echo htmlspecialchars($order['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($order['contact']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($order['address'])); ?></td>
                            <td style="font-size:13px;">
                                <?php 
                                    $items = json_decode($order['order_data'], true);
                                    if ($items) {
                                        echo '<ul style="padding-left:18px; margin:0;">';
                                        foreach ($items as $item) {
                                            echo '<li>' . htmlspecialchars($item['name']) . ' (x' . intval($item['quantity']) . ') - $' . number_format((float)$item['price'], 2) . '</li>';
                                        }
                                        echo '</ul>';
                                    } else {
                                        echo 'No products';
                                    }
                                ?>
                            </td>
                            <td><b>$<?php echo number_format((float)$order['total'], 2); ?></b></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="messages-section">
            <h2 class="messages-title">Contact Messages</h2>
            <table class="messages-table">
                <thead>
                    <tr class="messages-table-header">
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Subject</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($msg = $messages->fetch_assoc()): ?>
                    <tr class="messages-table-row">
                        <td><?php echo $msg['created_at']; ?></td>
                        <td><?php echo htmlspecialchars($msg['name']); ?></td>
                        <td><?php echo htmlspecialchars($msg['email']); ?></td>
                        <td><?php echo htmlspecialchars($msg['phone']); ?></td>
                        <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
