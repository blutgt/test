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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BluShop - E-Commerce Homepage</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1f1f1f;
            color: white;
        }

        header {
            background-color: #0070ba;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        header h1 {
            margin: 0;
            text-transform: uppercase;
            font-size: 2.5rem;
        }

        button {
            padding: 10px 20px;
            background-color: #0070ba;
            color: white;
            border: 2px solid #005b8d;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #005b8d;
            border-color: #004a70;
            box-shadow: 0 0 10px rgba(0, 122, 255, 0.7);
        }

        /* Product Display */
        .product-list {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            margin: 20px;
        }

        .product-card {
            background-color: #2d2d2d;
            border: 1px solid #555;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .product-card:hover {
            box-shadow: 0 0 15px rgba(0, 122, 255, 0.7);
            transform: translateY(-5px);
        }

        .product-card h3 {
            margin-top: 0;
            font-size: 1.5rem;
            color: #0070ba;
        }

        .product-card p {
            color: #ccc;
        }

        .product-card .price {
            font-weight: bold;
            color: #0070ba;
            font-size: 1.2rem;
        }

        .create-product-btn {
            margin-top: 20px;
            text-align: center;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4); /* Semi-transparent black */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #333;
            margin: 5% auto;
            padding: 30px;
            border: 2px solid #0070ba;
            width: 40%;
            color: white;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 15px rgba(0, 122, 255, 0.7);
        }

        .modal-header {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #0070ba;
        }

        .modal button {
            background-color: #0070ba;
            color: white;
            padding: 12px 30px;
            font-size: 1.2rem;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }

        .modal button:hover {
            background-color: #005b8d;
            box-shadow: 0 0 10px rgba(0, 122, 255, 0.7);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .product-card {
                width: 90%;
            }

            .modal-content {
                width: 80%;
            }
        }
    </style>
    <script>
        function openPaymentModal() {
            document.getElementById('paymentModal').style.display = 'block';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }
    </script>
</head>
<body>

<header>
    <h1>BluShop - Your Marketplace</h1>
</header>

<main>
    <div style="text-align: center; padding: 20px;">
        <?php if ($is_logged_in): ?>
            <h2>Welcome, <?php echo $username; ?>!</h2>
            <form method="POST" action="">
                <button type="submit" name="logout_btn">Logout</button>
            </form>

            <!-- Button to Create Product -->
            <div class="create-product-btn">
                <a href="create_product.php">
                    <button>Create New Product</button>
                </a>
            </div>
        <?php else: ?>
            <p>Please log in to start selling or <a href="login.php" style="color: #0070ba;">Log in</a> | <a href="register.php" style="color: #0070ba;">Sign up</a></p>
        <?php endif; ?>
    </div>

    <!-- Product Display Section -->
    <div class="product-list">
        <?php while ($row = mysqli_fetch_assoc($product_result)): ?>
            <div class="product-card">
                <h3><?php echo $row['name']; ?></h3>
                <p><?php echo $row['description']; ?></p>
                <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                <button onclick="openPaymentModal()">Buy Now</button>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">How Would You Like to Pay?</div>
            <p>Please select your payment method below:</p>
            <button>Pay with PayPal</button>
            <br><br>
            <button onclick="closePaymentModal()">Close</button>
        </div>
    </div>

</main>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>