<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BluShop - E-Commerce Homepage</title>
    <style>
        /* Your existing CSS styles here */
    </style>
    <script>
        function openPaymentModal() {
            document.getElementById('paymentModal').style.display = 'block';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        function loadUserInfo() {
            fetch('home.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('user-info').innerHTML = data;
                });
        }

        window.onload = loadUserInfo;
    </script>
</head>
<body>

<header>
    <h1>BluShop - Your Marketplace</h1>
</header>

<main>
    <div id="user-info" style="text-align: center; padding: 20px;">
        <!-- User info will be injected here by PHP via AJAX -->
    </div>

    <!-- Product Display Section -->
    <div class="product-list" id="product-list">
        <!-- Product list will be injected here by PHP or AJAX -->
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