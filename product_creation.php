<?php
session_start();

// CSRF Token Setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "product_management";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // CSRF validation
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];

    // Input validation
    if (empty($name) || strlen($name) > 255 || strlen($description) > 1000 || !filter_var($price, FILTER_VALIDATE_FLOAT) || $price <= 0) {
        die("Invalid input");
    }

    // Insert using prepared statement
    $stmt = $conn->prepare("INSERT INTO products (name, description, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $description, $price);
    if ($stmt->execute()) {
        echo "Product created successfully!";
    } else {
        error_log($stmt->error);
        echo "An error occurred";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Product Management</title>
</head>
<body>
    <h1>Create a Product</h1>
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label>Product Name: <input type="text" name="name" maxlength="255" required></label><br>
        <label>Description: <textarea name="description" maxlength="1000"></textarea></label><br>
        <label>Price: <input type="number" name="price" step="0.01" required></label><br>
        <button type="submit">Create Product</button>
    </form>
</body>
</html>