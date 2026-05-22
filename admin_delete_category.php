<?php
include 'base.php';
session_start();

auth(["staff", "manager"], $_SESSION['ADMINID'] ?? NULL);

if (is_get()) {
    $id = req('categoryId');
    $exists = is_exists($id, "categories", "categoryID");
       

    // Check if the product exists
    if ($exists) {
        
        // Now delete the category
        $stm = $_db->prepare("DELETE FROM categories WHERE categoryID = ?");
        $stm->execute([$id]);

        redirect("admin_category.php?msg=deleted");
    }
}
