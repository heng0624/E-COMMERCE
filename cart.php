<?php include 'header.php'; ?>

<?php
$userID = $_SESSION['ID'] ?? null;
auth(["customer"],  $userID);


$productId = null;
$qty = null;

if (is_get()) {
    $productId = req("product_id");
    $qty = req("quantity");
}

// Get or create cart
$cartStmt = $_db->prepare("SELECT cartId FROM carts WHERE userID = ?");
$cartStmt->execute([$userID]);
$cart = $cartStmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    $id = generateID("carts", "cartId", null);
    $createCart = $_db->prepare("INSERT INTO carts (cartId,userID) VALUES (?,?)");
    $createCart->execute([$id, $userID]);
    $cartID = $_db->lastInsertId();
} else {
    $cartID = $cart['cartId'];
}

// Add product to cart if provided
if ($productId && $qty) {
    $checkStmt = $_db->prepare("SELECT * FROM cart_items WHERE cartID = ? AND productID = ?");
    $checkStmt->execute([$cartID, $productId]);
    $existingItem = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingItem) {
        $newQty = $existingItem['quantity'] + $qty;
        $updateStmt = $_db->prepare("UPDATE cart_items SET quantity = ? WHERE cartID = ? AND productID = ?");
        $updateStmt->execute([$newQty, $cartID, $productId]);
    } else {
        $insertStmt = $_db->prepare("INSERT INTO cart_items (cartID, productID, quantity) VALUES (?, ?, ?)");
        $insertStmt->execute([$cartID, $productId, $qty]);
    }

    redirect("cart.php");
    exit;
}

// Get all items in cart
$stmt = $_db->prepare("
    SELECT ci.*, p.ProductName, p.Photo, p.Price 
    FROM cart_items ci
    JOIN products p ON ci.productID = p.ProductID
    WHERE ci.cartID = ?
");
$stmt->execute([$cartID]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['quantity'] * $item['Price'];
}
$shippingFee = 25.00;
$grandTotal = $total + $shippingFee;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/cart.css">
</head>

<body>
    <main>
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'deleted'): ?>
                <div class="alert success" id="successMsg">cart items successfully removed.</div>
            <?php elseif ($_GET['msg'] === 'added'): ?>
                <div class="alert success" id="successMsg">cart items successfully added.</div>
            <?php elseif ($_GET['msg'] === 'updated'): ?>
                <div class="alert success" id="successMsg">cart items successfully updated.</div>
            <?php elseif ($_GET['msg'] === 'error'): ?>
                <div class="alert error" id="errorMsg">Something went wrong. Please try again.</div>
            <?php endif; ?>
        <?php endif; ?>
        <h1>My Shopping Cart</h1>

        <form method="POST" action="delete_cart_items.php" onsubmit="return confirm('Are you sure you want to delete selected items?');">
            <div class="cart">
                <?php if (empty($cartItems)): ?>
                    <p>Your cart is empty.</p>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <input type="checkbox" class="slaveCheckbox" name="deleteIds[]" value="<?= $item['productID'] ?>" onchange="uncheckMasterCheckBox()">
                            <img src="<?= $item['Photo'] ?>" alt="<?= $item['ProductName'] ?>">
                            <div class="details">
                                <h2><?= $item['ProductName'] ?></h2>
                                <p class="original-price">RM <?= number_format($item['Price'], 2) ?></p>
                            </div>
                            <div class="quantity">
                                <input type="number" value="<?= $item['quantity'] ?>" class="quantity-input" disabled>
                            </div>
                            <p class="total-price">RM <?= number_format($item['Price'] * $item['quantity'], 2) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($cartItems)): ?>
                <div class="cart-summary">
                    <label><input type="checkbox" id="masterCheckBox" onchange="toggleCheckboxes()"> Select All</label>
                    <button type="submit" name="delete_selected" class="remove">Remove</button>
                    <p>Shipping Fee: RM <?= number_format($shippingFee, 2) ?></p>
                    <p>Total (<?= count($cartItems) ?> items): <span id="total-price">RM <?= number_format($grandTotal, 2) ?></span></p>
                </div>

                <div class="checkout-container">
                    <a href="checkout.php" class="checkout-button"><button class="checkout" type="button">CHECK OUT</button></a>
                    <a href="product.php" class="checkout-button"><button class="checkout" type="button">Back</button></a>

                </div>
            <?php endif; ?>
        </form>

    </main>
    <script src="js/app.js"></script>
</body>

</html>