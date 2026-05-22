<?php
include 'base.php';
session_start();
auth(["manager"], $_SESSION['ADMINID'] ?? null);


if (is_get()) {
    $staffId = req('staffId');
    $customerId=req('customerID');

    $staffExists = is_exists($staffId, "users", "userId");
    $customerExists = is_exists($customerId, "users", "userId");



    // Check if the product exists
    if ($staffExists) {
        // Now delete the products
        $stm = $_db->prepare("DELETE FROM users WHERE userId = ?");
        $stm->execute([$staffId]);

        redirect("admin_staff.php?msg=deleted");
    } else if( $customerExists) {      
        $stm = $_db->prepare("DELETE FROM users WHERE userId = ?");
        $stm->execute([$customerId]);

        redirect("admin_customer.php?msg=deleted");  
        
    }
}
