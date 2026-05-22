<?php
session_start();
require 'base.php';

$userID = $_SESSION['ID'] ?? null;
if (!$userID || !isset($_POST['cart_id'])) {
    header("Location: login.php");
    exit;
}
$addressId=req("address_id")?? null;
$cart_id = $_POST['cart_id'] ?? null;

//fetch address
$stmt = $_db->prepare("SELECT* FROM address_book WHERE addressID = ? AND userID = ? ");
$stmt->execute([$addressId,$userID]);
$address = $stmt->fetch(PDO::FETCH_ASSOC);
// Fetch items
$stmt = $_db->prepare("SELECT ci.productID, ci.quantity, p.price FROM cart_items ci
                      JOIN products p ON ci.productID = p.productID
                      WHERE ci.cartId = ?");
$stmt->execute([$cart_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
    echo "Cart is empty!";
    exit;
}

// Calculate total
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}
$shipping = 25.00;
$sst = $shipping * 0.2172;
$grandTotal = $total + $shipping + $sst;

$orderId=generateID("orders","orderId",null);
// Insert order
$stmt = $_db->prepare("INSERT INTO orders (orderId,userId, total_amount, order_status, created_at,shipping_address) VALUES (?,?, ?, ?, NOW(),?)");
$stmt->execute([$orderId,$userID, $grandTotal, 'Pending',$address["shippingAddress"]]);

// Insert order items
foreach ($items as $item) {
    $id=generateID("order_items","order_item_id",null);
    $stmt = $_db->prepare("INSERT INTO order_items (order_item_id,orderId, productID, quantity, price) VALUES (?,?, ?, ?, ?)");
    $stmt->execute([$id,$orderId ,$item['productID'], $item['quantity'], $item['price']]);
}

// Clear cart
$_db->prepare("DELETE FROM cart_items WHERE cartID = ?")->execute([$cart_id]);

// Redirect
redirect("payments.php?order_id=$orderId");

exit;
