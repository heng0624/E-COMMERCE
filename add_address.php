<?php
session_start();
require "base.php"; // Include your DB connection here

// Check login
if (!isset($_SESSION['ID'])) {
    redirect("login.php");
    exit;
}

$userID = $_SESSION['ID'];

$userId = $_POST['userId'];
$addressId = $_POST['addressId'];
$name = $_POST['fullName'];
$phone = $_POST['phone'];
$city = $_POST['city'];
$state = $_POST['state'];
$postal = $_POST['postalCode'];
$country = $_POST['country'];
$address = $_POST['address'];
$makeDefault = isset($_POST['makeDefault']) ? 1 : 0;

if (!empty($addressId)) {
    // Update existing
    $stmt = $_db->prepare("UPDATE address_book SET recipientName=?, phoneNumber=?, city=?, state=?, postalCode=?, country=?, shippingAddress=?, isDefault=? WHERE addressID=? AND userID=?");
    $stmt->execute([$name, $phone, $city, $state, $postal, $country, $address, $makeDefault, $addressId, $userId]);
} else {
    // Insert new
    $addressId=generateID("address_book","addressID",null);
    $stmt = $_db->prepare("INSERT INTO address_book (addressID,userID, recipientName, phoneNumber, city, state, postalCode, country, shippingAddress, isDefault) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$addressId,$userId, $name, $phone, $city, $state, $postal, $country, $address, $makeDefault]);
}

redirect("addressBook.php?msg=added");
exit;
