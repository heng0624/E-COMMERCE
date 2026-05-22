<!DOCTYPE html>
<html>

<head>
    <title>Payment</title>
    <link href="css/payment.css" rel="stylesheet" />
</head>

<body>
    <?php
    include "header.php";
    $errors = $_SESSION['payment_errors'] ?? [];
    $old = $_SESSION['old_input'] ?? [];

    $userID = $_SESSION['ID'] ?? null;
    $order_id = $_GET['order_id'] ?? null;
    auth(["customer"],$userID);

    if (!$userID || !$order_id) {
        redirect("login.php");
        exit;
    }

    // Fetch order details
    $stmt = $_db->prepare("SELECT * FROM orders WHERE orderID = ? AND userID = ?");
    $stmt->execute([$order_id, $userID]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "Order not found.";
        exit;
    }



    ?>

    <div class="payment-container">
        <h2><i class="fas fa-credit-card"></i> Order Payment</h2>

        <div class="order-info">
            <p><strong>Order ID:</strong> <?= htmlspecialchars($order_id) ?></p>
            <p><strong>Total Amount:</strong> RM <?= number_format($order['total_amount'], 2) ?></p>
        </div>

        <form method="POST" action="process_payment.php">
                <label for="card_number">Card Number</label><?php if (isset($errors['card_number'])): ?><div class="error"><?= $errors['card_number'] ?></div><?php endif; ?>
                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" >

                <label for="expiry_date">Expiry Date (MM/YY)</label><?php if (isset($errors['expiry_date'])): ?><div class="error"><?= $errors['expiry_date'] ?></div><?php endif; ?>
                <input type="text" id="expiry_date" name="expiry_date" placeholder="12/25" >
            
            <label for="cvv">CVV</label> <?php if (isset($errors['cvv'])): ?><div class="error"><?= $errors['cvv'] ?></div><?php endif; ?>
            <input type="text" id="cvv" name="cvv" placeholder="123">

            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
            <input type="hidden" name="total_amount" value="<?= number_format($order['total_amount'], 2) ?>">


            <button type="submit"><i class="fas fa-lock"></i> Submit Payment</button>
        </form>

    </div>
</body>
<?php
unset($_SESSION['payment_errors']);
unset($_SESSION['old_input']);
?>
</html>