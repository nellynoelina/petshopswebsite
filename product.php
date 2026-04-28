<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html?message=login_required');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Products - PetShop</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php if (isset($_SESSION['username'])): ?>
  <div class="user-info">
    <img src="images/user-icon.png" alt="User" class="user-icon">
    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
  </div>
<?php endif; ?>
  <div class="top-links" style="text-align:right; margin: 10px 8px 0 0;">
    <a href="index.html" class="plain-link">Back to Home</a>
    <a href="cart.html" class="plain-link">View Cart</a>
  </div>
  <div class="products-title">Our Products</div>
  <div class="products-container" id="products-container"></div>
  <script>
    // Load products dynamically
    function loadProducts() {
      fetch("get-products.php")
        .then(response => response.json())
        .then(products => {
          const container = document.getElementById("products-container");
          container.innerHTML = '';
          products.forEach(product => {
            const div = document.createElement("div");
            div.className = "product-card";
            div.innerHTML = `
              <img src="images/${product.image}" alt="${product.name}">
              <h3>${product.name}</h3>
              <div style="color:#666; font-size:13px; min-height:32px; margin-bottom:4px;">${product.description ? product.description : ''}</div>
              <p>$${product.price}</p>
              <button onclick="addToCart(${product.id})">Add to Cart</button>
            `;
            container.appendChild(div);
          });
        });
    }
    function addToCart(id) {
      fetch("add_to_cart.php", {
        method: "POST",
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ product_id: id })
      })
      .then(res => res.json())
      .then(data => alert(data.message));
    }
    function goToIndex() {
      window.location.href = "index.html";
    }
    function viewCart() {
      window.location.href = "cart.html";
    }
    // Load products on page load
    loadProducts();
  </script>
</body>
</html> 