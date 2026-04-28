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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - PetShop</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div id="user-info"></div>
  <div class="checkout-container">
    <a href="cart.html" class="back-btn back-btn-abs"> Back to Cart</a>
    <h1>Checkout</h1>
    <div class="order-summary" id="order-summary">
      Loading order summary...
    </div>
    <form class="checkout-form" id="checkout-form">
      <label for="fullname">Full Name</label>
      <input type="text" id="fullname" name="fullname" required placeholder="Enter your full name">

      <label for="address">Shipping Address</label>
      <textarea id="address" name="address" required placeholder="Enter your shipping address"></textarea>

      <label for="contact">Contact (Phone or Email)</label>
      <input type="text" id="contact" name="contact" required placeholder="Enter your contact info">

      <button type="submit" class="checkout-btn">Pay & Place Order</button>
    </form>
    <div class="success-message" id="success-message">
      ✅ Thank you! Your order has been placed successfully.<br>
      We will ship your products soon.<br>
      Payment will be made at the arrival.
    </div>
  </div>
  <script>
    // Load order summary from cart
    fetch('get_cart.php')
      .then(res => res.json())
      .then(cartItems => {
        let subtotal = 0;
        cartItems.forEach(item => {
          subtotal += item.price * item.quantity;
        });
        const tax = subtotal * 0.08;
        const shipping = subtotal > 0 ? 5.00 : 0;
        const total = subtotal + tax + shipping;
        document.getElementById('order-summary').innerHTML = `
          <strong>Order Summary:</strong><br>
          Subtotal: $${subtotal.toFixed(2)}<br>
          Shipping: $${shipping.toFixed(2)}<br>
          Tax: $${tax.toFixed(2)}<br>
          <strong>Total: $${total.toFixed(2)}</strong>
        `;
      });

    // Handle form submission
    document.getElementById('checkout-form').onsubmit = function(e) {
      e.preventDefault();
      // Gather form data
      const fullname = document.getElementById('fullname').value;
      const address = document.getElementById('address').value;
      const contact = document.getElementById('contact').value;
      // Get cart from get_cart.php
      fetch('get_cart.php')
        .then(res => res.json())
        .then(cartItems => {
          // Send order to place_order.php
          fetch('place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              fullname,
              address,
              contact,
              cart: cartItems
            })
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              document.getElementById('checkout-form').style.display = 'none';
              document.getElementById('success-message').style.display = 'block';
            } else {
              alert('Order failed: ' + (data.message || 'Unknown error'));
            }
          });
        });
    };

    // Show user info if logged in
    fetch('get_user.php')
      .then(res => res.json())
      .then(data => {
        if (data.logged_in) {
          document.getElementById('user-info').innerHTML = `
            <div class="user-info">
              <img src="images/user-icon.png" alt="User" class="user-icon">
              <span>${data.username}</span>
            </div>
          `;
        }
      });
  </script>
</body>
</html>
