<?php
require "base.php";
session_start();
// Assume user_id is from the session
$user_id = $_SESSION['ID'] ?? null;

// Check for existing cart
$stm = $_db->prepare("SELECT cartId FROM carts WHERE userId = ?");
$stm->execute([$user_id]);
$cart = $stm->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    
    $cartID=generateID("carts","cartId",null);
    // Create a new cart
    $create = $_db->prepare("INSERT INTO carts (cartId,userId) VALUES (?,?)");
    $create->execute([$cartID,$user_id]);
    $cart_id = $cartID; // <-- IMPORTANT

} else {
    $cart_id = $cart['cartId']  ?? null;
}

$product_id = req('product_id')?? null;
$quantity = req('quantity') ?? 1;

// Check if product already in cart
$check = $_db->prepare("SELECT * FROM cart_items WHERE cartId = ? AND productID = ?");
$check->execute([ $cart_id, $product_id]);
$exists = $check->fetch(PDO::FETCH_ASSOC);


if ($exists) {
    // Update quantity
    $update = $_db->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE cartId = ? AND productID = ?");
    $update->execute([$quantity, $cart_id, $product_id]);
    redirect("cart.php");
} else {

    $ID=generateID("cart_items","cart_item_id",null);
    // Insert new
    $insert = $_db->prepare("INSERT INTO cart_items (cart_item_id,cartId, productID, quantity) VALUES (?, ?, ?, ?)");
    $insert->execute([$ID, $cart_id, $product_id, $quantity]);

      redirect("cart.php");
}



?>