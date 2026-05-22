<?php
session_start();
include "header.php";

$userID = $_SESSION['ID'] ?? null;
$order_id = $_POST['order_id'] ?? null;
$card_number = $_POST['card_number'] ?? null;
$expiry_date = $_POST['expiry_date'] ?? null;
$cvv = $_POST['cvv'] ?? null;
$amount = $_POST['total_amount'] ?? null;



// ✅ General check
if (!$userID || !$order_id || !$card_number || !$expiry_date || !$cvv) {
    $_err['general'] = "All fields are required.";
}

// ✅ Card number validation
if (empty($card_number)) {
    $_err["card_number"] = "Card number is required!";
} elseif (!preg_match("/^\d+$/", $card_number)) {
    $_err["card_number"] = "Card number must contain digits only!";
} elseif (strlen($card_number) < 13) {
    $_err["card_number"] = "Card number must be at least 13 digits!";
} elseif (strlen($card_number) > 19) {
    $_err["card_number"] = "Card number must be at most 19 digits!";
}

// ✅ Expiry date validation (MM/YY or MM/YYYY)
if (empty($expiry_date)) {
    $_err["expiry_date"] = "Expiry date is required!";
} elseif (!preg_match("/^(0[1-9]|1[0-2])\/?([0-9]{2}|[0-9]{4})$/", $expiry_date)) {
    $_err["expiry_date"] = "Invalid expiry date format. Use MM/YY or MM/YYYY.";
} else {
    // Convert to a future date check
    $parts = explode('/', $expiry_date);
    $month = (int) $parts[0];
    $year = (int) (strlen($parts[1]) == 2 ? '20' . $parts[1] : $parts[1]);

    $expTime = strtotime("$year-$month-01 +1 month -1 day");
    $now = strtotime("today");
    if ($expTime < $now) {
        $_err["expiry_date"] = "The card has expired.";
    }
}

// ✅ CVV validation
if (empty($cvv)) {
    $_err["cvv"] = "CVV is required!";
} elseif (!preg_match("/^\d{3,4}$/", $cvv)) {
    $_err["cvv"] = "CVV must be 3 or 4 digits!";
}

// ✅ Redirect with error if any
if (!empty($_err)) {
    $_SESSION['payment_errors'] = $_err;
    $_SESSION['old_input'] = $_POST;
    redirect("payments.php?order_id=" . urlencode($order_id));
    exit;
}

// ✅ Fetch order to ensure it exists
$stmt = $_db->prepare("SELECT * FROM orders WHERE orderID = ? AND userID = ?");
$stmt->execute([$order_id, $userID]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['payment_errors']['general'] = "Order not found.";
    header("Location: payment.php?order_id=" . urlencode($order_id));
    exit;
}

// ✅ Simulate payment success
$payment_successful = true;

// ✅ Mask card number
$last_four = substr($card_number, -4);
$payment_id = generateID("payments", "paymentID", null);

// ✅ Insert into payments table
$status = $payment_successful ? 'success' : 'failed';

$stmt = $_db->prepare("INSERT INTO payments (paymentID, orderID, userID, total_amount,card_number, expiry_date, cvv, status, payment_date)
                       VALUES (?, ?, ?,?, ?, ?, ?, ?, NOW())");
$stmt->execute([$payment_id, $order_id, $userID,$amount, $last_four, $expiry_date, $cvv, $status]);

if ($payment_successful) {
    $stmt = $_db->prepare("UPDATE orders SET order_status = 'paid' WHERE orderID = ?");
    $stmt->execute([$order_id]);
    redirect("home.php?msg=added");
} else {
    $_SESSION['payment_errors']['general'] = "❌ Payment failed.";
    redirect("payments.php?order_id=" . urlencode($order_id));
    exit;
}
?>
