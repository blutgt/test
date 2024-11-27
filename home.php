<?php
session_start();

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "e_commerce"); // Specify database

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

if ($is_logged_in) {
    $username = $_SESSION['username']; // Assuming username is stored in session
}

// Handle logout
if (isset($_POST['logout_btn'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Fetch products
$product_query = "SELECT p.name, p.description, p.price FROM products p";
$product_result = mysqli_query($conn, $product_query);

// Output user info and product list
if ($is_logged_in) {
    echo "<h2>Welcome, $username!</h2>";
    echo '<form method="POST" action=""><button type="submit" name="logout_btn">Logout</button></form>';
    echo '<div class="create-product-btn"><a href="create_product.php"><button>Create New Product</button></a></div>';
} else {
    echo '<p>Please log in to start selling or <a href="login.php" style="color: #0070ba;">Log in</a> | <a href="register.php" style="color: #0070ba;">Sign up</a></p>';
}

echo '<div class="product-list">';
while ($row = mysqli_fetch_assoc($product_result)) {
    echo '<div class="product-card">';
    echo '<h3>' . $row['name'] . '</h3>';
    echo '<p>' . $row['description'] . '</p>';
    echo '<p class="price">$' . number_format($row['price'], 2) . '</p>';
    echo '<button onclick="openPaymentModal()">Buy Now</button>';
    echo '</div>';
}
echo '</div>';

// Close the database connection
mysqli_close($conn);
?>