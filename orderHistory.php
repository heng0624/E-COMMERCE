<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
    <link rel="stylesheet" href="css/purchase.css">
</head>
<?php include "header.php" ?>

<body>
    <?php
    $userID = $_SESSION["ID"] ?? null;
    auth(["customer"], $userID);

    if (!$userID) {
        header("Location: login.php");
        exit;
    }

    // Fetch user data
    $stmt = $_db->prepare("SELECT * FROM users WHERE userId = ?");
    $stmt->execute([$userID]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filter order status from query string
    $statusFilter = $_GET['status'] ?? 'all';

    // Fetch orders by status
    if ($statusFilter === 'all') {
        $stmt = $_db->prepare("SELECT * FROM orders WHERE userId = ? ORDER BY created_at DESC");
        $stmt->execute([$userID]);
    } else {
        $stmt = $_db->prepare("SELECT * FROM orders WHERE userId = ? AND LOWER(order_status) = ? ORDER BY created_at DESC");
        $stmt->execute([$userID, strtolower($statusFilter)]);
    }

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch order items for each order
    $order_items = [];
    foreach ($orders as $order) {
        $stmt = $_db->prepare("SELECT oi.*, p.productName AS product_name, p.photo AS product_image 
                               FROM order_items oi
                               JOIN products p ON oi.productID = p.productID
                               WHERE oi.orderId = ?");
        $stmt->execute([$order['orderId']]);
        $order_items[$order['orderId']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>

    <div class="container">
        <aside class="sidebar">
            <?php foreach ($users as $user): ?>
                <div class="profile">
                    <img src="<?= $user["profile_image"] ?>" alt="User Image" class="profile-img">
                    <p class="username"><?= $user["userName"] ?></p>
                </div>
                <nav>
                    <ul>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="addressBook.php">Address Book</a></li>
                        <li><a href="orderHistory.php">Orders</a></li>
                        <li class="logout"><a href="logout.php">Log Out</a></li>
                    </ul>
                </nav>
            <?php endforeach; ?>
        </aside>

        <main class="content">
            <h1>PURCHASE</h1>

            <!-- FILTER BUTTONS -->
            <div class="filters">
                <?php
                $statuses = ['all' => 'All', 'pending' => 'Pending', 'paid' => 'Paid', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'completed' => 'Completed'];
                foreach ($statuses as $key => $label):
                    $active = ($statusFilter === $key) ? 'active' : '';
                    echo "<a href='?status=$key'><button class='$active'>$label</button></a>";
                endforeach;
                ?>
            </div>

            <!-- ORDER LIST -->
            <div class="orders">
                <?php if (empty($orders)): ?>
                    <p>No orders found.</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order">
                            <a href="orderDetail.php?orderId=<?= $order['orderId'] ?>">
                                <div class="order-header">
                                    <span>Order No: <?= htmlspecialchars($order['orderId']) ?></span>
                                    <span>Updated On: <?= date('d-m-Y', strtotime($order['created_at'])) ?></span>
                                     <span> Order status: <?= htmlspecialchars($order['order_status']) ?></span>
                                    </span>
                                </div>

                                <div class="order-items">
                                    <?php foreach ($order_items[$order['orderId']] as $item): ?>
                                        <div class="item">
                                            <img src="<?= htmlspecialchars($item['product_image']) ?>" alt="Product Image">
                                            <p><?= htmlspecialchars($item['product_name']) ?></p>
                                            <span>RM <?= number_format($item['price'], 2) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="order-total">
                                    Order Total: RM <?= number_format($order['total_amount'], 2) ?>
                                </div>

                                <?php if (strtolower($order['order_status']) === 'delivery'): ?>
                                    <button class="complete-order">Complete Order</button>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
