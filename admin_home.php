<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_home.css">

    <!-- Google Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <?php include 'header_admin.php' ?>

    <?php
    // Restrict access to only "staff" and "manager"
    auth(["staff", "manager"], $_SESSION['ADMINID']?? NULL);
    //orders
    $stm = $_db->prepare("SELECT SUM(total_amount) as total_sales FROM orders");
    $stm->execute();
    $total_sales = $stm->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

    //orders items
    $stm = $_db->prepare("SELECT SUM(Quantity) as total_products_sold FROM order_items");
    $stm->execute();
    $total_products_sold = $stm->fetch(PDO::FETCH_ASSOC)['total_products_sold'] ?? 0;



    // Count total staff
    $stm = $_db->prepare("SELECT COUNT(*) as total_staff FROM users WHERE role = 'staff'");
    $stm->execute();
    $staff_count = $stm->fetch(PDO::FETCH_ASSOC)['total_staff'];

    // Count total customers
    $stm = $_db->prepare("SELECT COUNT(*) as total_customers FROM users WHERE role = 'customer'");
    $stm->execute();
    $customer_count = $stm->fetch(PDO::FETCH_ASSOC)['total_customers'];

    // Count total managers
    $stm = $_db->prepare("SELECT COUNT(*) as total_managers FROM users WHERE role = 'manager'");
    $stm->execute();
    $manager_count = $stm->fetch(PDO::FETCH_ASSOC)['total_managers'];
    //
    $stm = $_db->prepare("SELECT COUNT(*) as total_orders FROM orders");
    $stm->execute();
    $orders_count = $stm->fetch(PDO::FETCH_ASSOC)['total_orders'];


    ?>


    <h2>Welcome, Admin</h2>
    <div class="dashboard">
        <div class="card">
            <i class="fas fa-dollar-sign"></i>
            <h3>Total Sales</h3>
            <p>$<?= number_format($total_sales, 2) ?></p>
        </div>
        <div class="card">
            <i class="fas fa-shopping-cart"></i>
            <h3>Orders</h3>
            <p><?= $orders_count ?></p>
        </div>
        <div class="card">
            <i class="fas fa-users"></i>
            <h3>Customers</h3>
            <p><?= $customer_count ?></p>
        </div>
        <div class="card">
            <i class="fas fa-paint-brush"></i>
            <h3>Art Pieces Sold</h3>
            <p><?= $total_products_sold ?></p>
        </div>
        <div class="card">
            <i class="fas fa-user-tie"></i>
            <h3>Total Staff</h3>
            <p><?= $staff_count ?></p> <!-- Replace with dynamic PHP data -->
        </div>
        <div class="card">
            <i class="fas fa-user-tie"></i>
            <h3>Manager</h3>
            <p><?= $manager_count ?></p>
        </div>
    </div>

</body>

</html>