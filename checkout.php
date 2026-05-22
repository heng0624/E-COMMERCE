<?php
session_start();
require 'base.php'; // DB connection

$userID = $_SESSION['ID'] ?? null;
auth(["customer"], $userID);

if (!$userID) {
    header("Location: login.php");
    exit;
}

// Get latest cart
$stmt = $_db->prepare("SELECT * FROM carts WHERE userId = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$userID]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cart) {
    echo "No active cart!";
    exit;
}
$cart_id = $cart['cartId'];

// Get cart items
$stmt = $_db->prepare("SELECT ci.productID, ci.quantity, p.productName, p.price 
                   FROM cart_items ci
                   JOIN products p ON ci.productID = p.productID
                   WHERE ci.cartId = ?");
$stmt->execute([$cart_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch addresses
$stmt = $_db->prepare("SELECT * FROM address_book WHERE userID = ?");
$stmt->execute([$userID]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 25.00;
$sst = $shipping * 0.2172;
$grandTotal = $subtotal + $shipping + $sst;

// Fetch default address
$addressStmt = $_db->prepare("SELECT * FROM address_book WHERE userID = ? AND isDefault = 1 LIMIT 1");
$addressStmt->execute([$userID]);
$address = $addressStmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/checkout.css">
</head>

<body>
    <div class="checkout-container">
        <h2>CHECK OUT</h2>

        <h3>DELIVERY ADDRESS</h3>

        <section class="delivery-address">
            <div id="selectedAddress">
                <?php if ($address): ?>
                    <div id="currentAddress">
                        <span><strong><?= htmlspecialchars($address['recipientName']) ?></strong></span>
                        <p><?= htmlspecialchars($address['phoneNumber']) ?></p>
                        <p>
                            <?= htmlspecialchars($address['shippingAddress']) ?>,
                            <?= htmlspecialchars($address['city']) ?>,
                            <?= htmlspecialchars($address['state']) ?>,
                            <?= htmlspecialchars($address['postalCode']) ?>,
                            <?= htmlspecialchars($address['country']) ?>
                        </p>
                        <button onclick="toggleForms()">Change</button>
                    </div>
                <?php else: ?>
                    <p>No delivery address found. <a href="addressBook.php">Add one now</a>.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="products-ordered">
            <h3>PRODUCTS ORDERED</h3>
            <?php foreach ($items as $item): ?>
                <?php
                $stmt = $_db->prepare("SELECT Photo FROM products WHERE productID= ?");
                $stmt->execute([$item["productID"]]);
                $productPhoto = $stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="product-item">
                    <img src="<?= $productPhoto['Photo']; ?>" alt="Product">
                    <div class="product-details">
                        <p><?= htmlspecialchars($item['productName']) ?></p>
                        <p>Unit Price: RM <?= number_format($item['price'], 2) ?></p>
                        <p>Amount: <?= $item['quantity'] ?></p>
                        <p>Item Subtotal: RM <?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="order-summary">
            <h3>ORDER SUMMARY</h3>
            <p>Merchandise Subtotal: RM <?= number_format($subtotal, 2) ?></p>
            <p>Shipping Total (excl. SST): RM <?= number_format($shipping, 2) ?></p>
            <p>Shipping Fee SST: RM <?= number_format($sst, 2) ?></p>
            <p><strong>Total Payment: RM <?= number_format($grandTotal, 2) ?></strong></p>

            <form method="post" action="place_order.php">
                <input type="hidden" name="cart_id" value="<?= $cart_id ?>">
                <input type="hidden" id="selectedAddressId" name="address_id" value="<?= $address['addressID'] ?? '' ?>">
                <button type="submit">PLACE ORDER</button>
            </form>
        </section>
    </div>

    <!-- Address Selection Modal -->
    <div id="addressModal" class="modal">
      <div class="modal-content">
        <h2>Choose Delivery Address</h2>
        <?php if (!empty($addresses)): ?>
            <?php foreach ($addresses as $addr): ?>
                <div class="address-option">
                    <input type="radio" name="addressSelect" value="<?= $addr['addressID'] ?>"
                        <?= (isset($address['addressID']) && $addr['addressID'] === $address['addressID']) ? 'checked' : '' ?>>
                    <label>
                        <strong><?= htmlspecialchars($addr['recipientName']) ?></strong><br>
                        <?= htmlspecialchars($addr['phoneNumber']) ?><br>
                        <?= htmlspecialchars($addr['shippingAddress']) ?>,
                        <?= htmlspecialchars($addr['city']) ?>,
                        <?= htmlspecialchars($addr['state']) ?>,
                        <?= htmlspecialchars($addr['postalCode']) ?>,
                        <?= htmlspecialchars($addr['country']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No addresses found. <a href="addressBook.php">Add one now</a>.</p>
        <?php endif; ?>
        <div class="modal-actions">
          <button onclick="saveSelectedAddress()">Save</button>
          <button onclick="toggleForm()">Cancel</button>
        </div>
      </div>
    </div>

    <script src="js/app.js"></script>

</body>
</html>
