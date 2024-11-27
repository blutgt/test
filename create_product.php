<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access!");
}

// Database connection
$conn = new mysqli("localhost", "root", "", "e_commerce");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for creating a new product
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['create_product'])) {
    $user_id = $_SESSION['user_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = $_POST['price'];
    $delivery_type = $_POST['delivery_type'];
    $delivery_details = $conn->real_escape_string($_POST['delivery_details']);
    
    // Initialize file_path as NULL
    $file_path = null;
    
    // Check if file upload is required (File delivery)
    if ($delivery_type == 'file_delivery' && isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        // Check if the uploaded file is valid (size and type)
        $file_name = $_FILES['file_upload']['name'];
        $file_tmp_name = $_FILES['file_upload']['tmp_name'];
        $file_size = $_FILES['file_upload']['size'];
        $file_error = $_FILES['file_upload']['error'];

        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf', 'docx'];

        // Validate file type and size
        if (in_array($file_ext, $allowed_ext) && $file_size <= 10 * 1024 * 1024) {  // Max file size: 10MB
            $file_new_name = uniqid('', true) . '.' . $file_ext;
            $file_upload_path = 'uploads/' . $file_new_name;

            if (move_uploaded_file($file_tmp_name, $file_upload_path)) {
                $file_path = $file_upload_path;
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file type or file is too large.";
        }
    }

    // Prepare and execute the insert query
    $stmt = $conn->prepare("INSERT INTO products (user_id, name, description, price, image_path, delivery_type, delivery_details, file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // You can set $image_path to NULL since no image is being uploaded in this case
    $image_path = null;
    
    // Bind parameters: bind_param expects types for each parameter (e.g., 'i', 's', 'd', 's', etc.)
    $stmt->bind_param("issdssss", $user_id, $name, $description, $price, $image_path, $delivery_type, $delivery_details, $file_path);

    if ($stmt->execute()) {
        // Redirect to the same page after successful product creation to show the updated list
        header("Location: create_product.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle product deletion (No product ID in URL, use POST request for security)
if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    // Prepare and execute the delete query
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        // Redirect after successful deletion
        header("Location: create_product.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch user's products
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM products WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product - BluShop</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2c2c2c;
            color: white;
            padding: 20px;
        }

        header {
            background-color: #0070ba;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .form-container, .product-container {
            background-color: #444;
            border-radius: 5px;
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-container input, .form-container textarea, .form-container select, .form-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #0070ba;
        }

        .form-container button {
            background-color: #0070ba;
            color: white;
        }

        .form-container button:hover {
            background-color: #005b8d;
        }

        .form-container label {
            font-weight: bold;
        }

        .product-container table {
            width: 100%;
            margin-top: 20px;
        }

        .product-container th, .product-container td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #0070ba;
        }

        .product-container button {
            background-color: #0070ba;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .product-container button:hover {
            background-color: #005b8d;
        }

        .go-home-button {
            background-color: #0070ba;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-align: center;
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
        }

        .go-home-button:hover {
            background-color: #005b8d;
        }
    </style>
</head>
<body>

<header>
    <h1>Create a Product</h1>
</header>

<div class="form-container">
    <form action="create_product.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter product name" required autocomplete="off">

        <label for="description">Product Description:</label>
        <textarea id="description" name="description" rows="4" placeholder="Enter product description" required autocomplete="off"></textarea>

        <label for="price">Product Price ($):</label>
        <input type="number" id="price" name="price" step="0.01" placeholder="Enter product price" required autocomplete="off">

        <label for="delivery_type">Delivery Type:</label>
        <select id="delivery_type" name="delivery_type" onchange="toggleFileUpload(this)">
            <option value="message_delivery">Message Delivery</option>
            <option value="file_delivery">File Delivery</option>
        </select>

        <label for="delivery_details">Delivery Details:</label>
        <textarea id="delivery_details" name="delivery_details" rows="4" placeholder="Enter delivery details (if any)" autocomplete="off"></textarea>

        <!-- File Upload (only for file delivery) -->
        <div id="file-upload-container" style="display: none;">
            <label for="file_upload">Upload File:</label>
            <input type="file" id="file_upload" name="file_upload" accept=".jpg,.jpeg,.png,.pdf,.docx">
        </div>

        <button type="submit" name="create_product">Create Product</button>
    </form>
</div>


<div class="product-container">
    <h2>Your Products</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <form action="create_product.php" method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
</div>
<!-- Go Home button -->
<a href="homepage.php" class="go-home-button">Go to Home</a>

<script>
// Toggle file upload input based on delivery type selection
function toggleFileUpload(selectElement) {
    const fileUploadContainer = document.getElementById('file-upload-container');
    if (selectElement.value === 'file_delivery') {
        fileUploadContainer.style.display = 'block';
    } else {
        fileUploadContainer.style.display = 'none';
    }
}
</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
