<?php
include 'base.php';
session_start();

auth(["staff", "manager"], $_SESSION['ADMINID'] ?? NULL);

if (is_get()) {
    $id = req('orderId');
    $exists = is_exists($id, "orders", "orderId");
       

    // Check if the product exists
    if ($exists) {
        
        // Step 1: Delete related order_items
        $stmItems = $_db->prepare("DELETE FROM order_items WHERE orderId = ?");
        $stmItems->execute([$id]);
        // Now delete the category
        $stm = $_db->prepare("DELETE FROM orders WHERE orderId = ?");
        $stm->execute([$id]);

        redirect("admin_order.php?msg=deleted");
    }
}
