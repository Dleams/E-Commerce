<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mkrn") or die("Connection Failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['veg_id'])) {
    $_SESSION['cart'][$_POST['veg_id']] = ($_SESSION['cart'][$_POST['veg_id']] ?? 0) + max(1, (int)$_POST['quantity']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
   /* Typography */
body {
  font-family: 'Poppins', sans-serif; /* Change font to Poppins */
  margin: 0;
  padding: 20px; /* Increase padding */
  background: #f4f4f4; /* Change background color */
}

h1,
h2 {
  text-align: center;
  margin-bottom: 30px; /* Increase margin */
  color: #333; /* Change color */
  font-weight: 600; /* Change font weight */
}

/* Products Styling */
.products {
  display: flex;
  flex-wrap: wrap;
  gap: 30px; /* Increase gap */
  justify-content: center;
}

.product {
  background: #fff; /* Change background color */
  border-radius: 15px; /* Increase radius */
  width: 300px; /* Increase width */
  padding: 25px; /* Increase padding */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Change box shadow */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product:hover {
  transform: translateY(-8px); /* Increase translation */
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Change box shadow */
}

.product img {
  width: 100%;
  height: auto;
  border-radius: 12px; /* Increase radius */
  margin-bottom: 15px; /* Increase margin */
}

.product h5 {
  font-size: 24px; /* Increase font size */
  margin-bottom: 10px; /* Decrease margin */
  color: #333; /* Change color */
}

.product p {
  margin-bottom: 15px; /* Increase margin */
  font-weight: 500; /* Change font weight */
  color: #ff5722; /* Change color */
}

/* Inputs & Buttons */
.product input[type="number"] {
  width: 100%;
  padding: 12px; /* Increase padding */
  margin-bottom: 15px; /* Increase margin */
  border: 1px solid #ddd;
  border-radius: 8px; /* Increase radius */
  box-sizing: border-box;
}

.product button {
  width: 100%;
  padding: 16px; /* Increase padding */
  background: #ff5722;
  color: white;
  border: none;
  border-radius: 10px; /* Increase radius */
  cursor: pointer;
  font-weight: bold;
  font-size: 18px; /* Increase font size */
  transition: background 0.3s ease;
}

.product button:hover {
  background: #e64a19;
}

/* Shopping Cart */
.cart {
  margin-top: 50px; /* Increase margin */
  background: #fff; /* Change background color */
  padding: 30px; /* Increase padding */
  border-radius: 15px; /* Increase radius */
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Change box shadow */
}

.cart ul {
  list-style: none;
  padding: 0;
}

.cart li {
  padding: 16px; /* Increase padding */
  border-bottom: 1px solid #ddd;
  font-weight: 500;
}

.cart h4 {
  margin-top: 25px; /* Increase margin */
  font-weight: bold;
}

.cart button {
  margin-top: 20px; /* Increase margin */
  padding: 16px; /* Increase padding */
  background: #007bff;
  color: white;
  border: none;
  border-radius: 10px; /* Increase radius */
  cursor: pointer;
  font-size: 18px; /* Increase font size */
  font-weight: bold;
  transition: background 0.3s ease;
}

.cart button:hover {
  background: #0056b3;
}

/* Shopping Cart Icon */
.cart-icon {
  position: fixed;
  top: 20px; /* Increase top position */
  right: 20px; /* Increase right position */
  background: #ff5722;
  color: white;
  padding: 14px; /* Increase padding */
  border-radius: 50%;
  font-size: 22px; /* Increase font size */
  font-weight: bold;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}
    </style>
</head>

<body>
    <div class="cart-icon">🛒 <?php echo array_sum($_SESSION['cart'] ?? []); ?></div>
    
    <h1>Vegetables</h1>
    <div class="products">
        <?php
        $result = $conn->query("SELECT * FROM products");
        while ($row = $result->fetch_assoc()) {
            echo "<div class='product'>
                    <img src='images/{$row['image']}' alt='{$row['product_name']}'>
                    <h5>{$row['product_name']}</h5>
                    <p>₱{$row['price']}</p>
                    <form method='POST'>
                        <input type='hidden' name='veg_id' value='{$row['id']}'>
                        <input type='number' name='quantity' value='1' min='1'>
                        <button type='submit'>Add to Cart</button>
                    </form>
                  </div>";
        }
        ?>
    </div>

    <div class="cart">
        <h2>Shopping Cart</h2>
        <?php
        $total_price = 0;
        if (!empty($_SESSION['cart'])) {
            echo "<ul>";
            foreach ($_SESSION['cart'] as $id => $qty) {
                if ($row = $conn->query("SELECT product_name, price FROM products WHERE id = $id")->fetch_assoc()) {
                    $total_price += $row['price'] * $qty;
                    echo "<li>{$row['product_name']} - Qty: $qty - ₱" . number_format($row['price'] * $qty, 2) . "</li>";
                }
            }
            echo "</ul><h4>Total: ₱" . number_format($total_price, 2) . "</h4>";
        } else {
            echo "<p>Your cart is empty.</p>";
        }
        ?>
        <form action="payout.php">
            <button type="submit">Checkout</button>
        </form>
    </div>
</body>

</html>
