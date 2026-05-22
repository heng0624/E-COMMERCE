<?php
session_start();
require "base.php";
if (is_post()) {
    $method_id = $_POST['method_id'];
    $userID = $_SESSION['ID'];

    $stmt = $_db->prepare("DELETE FROM payment_methods WHERE method_id = ? AND userID = ?");
    $stmt->execute([$method_id, $userID]);

    header("Location: bank&Ewallet.php");
    exit();
}
?>
