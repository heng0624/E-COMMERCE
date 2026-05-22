<?php
session_start();
include "base.php"; // or your database connection file

if (!isset($_SESSION['ID'])) {
    header("Location: login.php");
    exit;
}
 $addressID =req('addressID');
if (is_post() && isset( $addressID)) {
    $userID = $_SESSION['ID'];
   

    // Verify ownership (so a user can only delete their own address)
    $stmt = $_db->prepare("DELETE FROM address_book WHERE addressID = ? AND userID = ?");
    $stmt->execute([$addressID, $userID]);

    // Redirect back with success message (optional)
    redirect("addressBook.php?msg=deleted");
    exit;
} else {
    redirect("addressBook.php");
    exit;
}
?>
