<?php
require 'base.php'; // Include database connection file if needed

session_start();

$id = isset($_SESSION['ADMINID'] ) ?   $_SESSION['ADMINID']  : null;


$message =  isset($_SESSION['message']) ? $_SESSION['message'] : null;


$stm = $_db->prepare("SELECT * FROM users WHERE userId = ?");
$stm->execute([$id]);
$result = $stm->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="css/header_admin.css">

</head>

<body>
    <header>
        <div class="logo_box">
            <img src="image/logo.png" alt="Logo" />
        </div>

        <nav class="nav-bar">
            <a href="admin_home.php">Home</a>
            <a href="admin_staff.php">Staff</a>
            <a href="admin_customer.php">Customer</a>
            <a href="admin_product.php">Product</a>
            <a href="admin_category.php">Category</a>
            <a href="admin_order.php">Order</a>
            <a href="report.php">Report</a>
        </nav>
        <?php if (!empty($id) || !empty($message)): ?>
            <div class="userinfo_box">
                <p><?= $result['userName']?? "" ?></p>
                <div class="dropdown">
                <img class="user_img" src="<?= $result['profile_image']?? ""?>" alt="user" />

                    <div class="dropdown-options">
                        <a href="admin_profile.php">My Profile</a>
                        <a href="admin_logout.php">Logout</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
           <?php redirect("admin_login.php")?>
        <?php endif; ?>
    </header>
</body>

</html>