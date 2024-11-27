<?php
session_start(); // Start session to check if the user is logged in

// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "logindetails");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user is verified
$sql = "SELECT * FROM logindetails WHERE id = '$user_id' AND verified = 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('You do not have access to sell. Please contact support.');</script>";
    exit();
}

// User is verified, allow access to the page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product</title>
</head>
<body>
    <h1>Create a New Product</h1>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="product_name" required><br>

        <label>Description:</label>
        <textarea name="description" required></textarea><br>

        <label>Price:</label>
        <input type="number" name="price" required><br>

        <label>Upload Image:</label>
        <input type="file" name="image" required><br>

        <label>Delivery Method:</label>
        <select name="delivery_method" required>
            <option value="email">Email</option>
            <option value="file">File</option>
            <option value="account">Account</option>
        </select><br>

        <label>Serial Numbers (if applicable):</label>
        <textarea name="serial_numbers"></textarea><br>

        <button type="submit">Submit Product</button>
    </form>
</body>
</html>