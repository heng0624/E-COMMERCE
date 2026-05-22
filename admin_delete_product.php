<?php
include 'base.php';
session_start();

auth(["manager","staff"], $_SESSION['ADMINID'] ?? null);

if (is_get()) {
    $id = req('productId');
    $exists = is_exists($id, "products", "productID");


    // Check if the product exists
    if ($exists) {
        // Now delete the products
        $stm = $_db->prepare("DELETE FROM products WHERE ProductID = ?");
        $stm->execute([$id]);

        // redirect("admin_product.php?msg=deleted");
    } else {        
        
    }
}
