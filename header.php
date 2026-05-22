<?php
require 'base.php'; // Include database connection file if needed

session_start();

$id = isset($_SESSION['ID']) ? $_SESSION['ID'] : null;

$message = isset($_SESSION['message']) ? $_SESSION['message'] : null;

$stm = $_db->prepare("SELECT * FROM users WHERE userID = ?");
$stm->execute([$id]);
$result = $stm->fetch(PDO::FETCH_ASSOC);

// Get the cart from carts table
$cartItemCount = 0;

if (!empty($id)) {
    // Get the user's active cart
    $stmt = $_db->prepare("SELECT cartId FROM carts WHERE userId = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$id]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart) {
        $cartId = $cart['cartId'];

        // Get total quantity of items in this cart
        $stmt2 = $_db->prepare("SELECT SUM(quantity) AS total FROM cart_items WHERE cartId = ?");
        $stmt2->execute([$cartId]);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        $cartItemCount = $row['total'] ?? 0;
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="css/header.css">
</head>

<body>
    <div class="header-container">
        <div class="logo-box">
            <a href="home.php">
                <img src="image/logo.png" alt="Art" />
            </a>
        </div>
        <nav class="nav-links">
            <a href="home.php">Home</a>
            <a href="aboutus.php">About Us</a>
            <a href="product.php">Product</a>
        </nav>

        <div class="header-controls">
            <a href="cart.php" class="shopping-cart">
                <img src="image/shopping-cart.png" alt="cart" width="25" height="25" />
                <?php if ($cartItemCount > 0): ?>
                    <span class="cart-count"><?= $cartItemCount ?></span>
                <?php endif; ?>
            </a>
            <div class="user-info">
                <?php if (!empty($id) || !empty($message)): ?>
                    <div class="dropdown">
                        <img class="user-img" src="<?= $result['profile_image'] ?>" alt="User" width="30" height="30">
                        <a href="profile.php"></a>
                        <div class="dropdown-options">
                            <a href="profile.php">My Profile</a>
                            <a href="orderHistory.php">My Orders</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php">Login</a> | <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
