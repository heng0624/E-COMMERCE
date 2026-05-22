<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Table</title>
    <link rel="stylesheet" href="css/table.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'header_admin.php' ?>

    <?php
    // Restrict access to only "staff" and "manager"
    auth(["staff", "manager"], $_SESSION['ADMINID'] ?? NULL);

    $searchKeyword = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : "";

    $sql = "SELECT 
                o.orderId,
                u.userName,
                p.productName,
                oi.quantity,
                oi.price,
                o.total_amount,
                o.order_status
            FROM orders o
            JOIN users u ON o.userId = u.userId
            JOIN order_items oi ON o.orderId = oi.orderId
            JOIN products p ON oi.productId = p.productId";

    $params = [];

    if (!empty($searchKeyword)) {
        $sql .= " WHERE o.orderId LIKE ? OR u.userName LIKE ? OR p.productName LIKE ? OR o.order_status LIKE ?";
        $searchParam = "%$searchKeyword%";
        $params = [$searchParam, $searchParam, $searchParam,$searchParam];
    }

    $sql .= " ORDER BY o.orderId DESC";

    $stm = $_db->prepare($sql);
    $stm->execute($params);
    $orders = $stm->fetchAll(PDO::FETCH_ASSOC);

    $count = 1;


    ?>
    <div class="staff-container">
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'deleted'): ?>
                <div class="alert success" id="successMsg">order successfully deleted.</div>
            <?php elseif ($_GET['msg'] === 'added'): ?>
                <div class="alert success" id="successMsg">order successfully added.</div>
            <?php elseif ($_GET['msg'] === 'updated'): ?>
                <div class="alert success" id="successMsg">order successfully updated.</div>
            <?php elseif ($_GET['msg'] === 'error'): ?>
                <div class="alert error" id="errorMsg">Something went wrong. Please try again.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="header-container">
            <h2>Order TABLE (<?= count($orders) ?>)</h2>
            <form class="search-box" method="GET">
            <div class="search-box">
                <input type="text" class="search_keyword" name="search_keyword" id="searchInput" placeholder="Search orders..."value="<?= htmlspecialchars($searchKeyword) ?>">
                <button id="searchButton" >
                    <img src="image/search.png" alt="search" />
                </button>
            </div>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <!-- todo order listing admin -->
                    <?php foreach ($orders as $order): ?>
                        <td><?= $count++ ?></td>
                        <td><?= htmlspecialchars($order['orderId']) ?></td>
                        <td><?= htmlspecialchars($order['userName']) ?></td>
                        <td><?= htmlspecialchars($order['productName']) ?></td>
                        <td><?= htmlspecialchars($order['quantity']) ?></td>
                        <td><?= htmlspecialchars($order['price']) ?></td>
                        <td><?= htmlspecialchars($order['order_status']) ?></td>
                        <td>
                            <?php if ($order['order_status'] === 'Completed'): ?>
                                <span class="completed-icon">&#10004;</span>
                            <?php else: ?>
                                <a href="admin_edit_order.php?orderId=<?= htmlspecialchars($order['orderId']) ?>">
                                    <img src="image/edit.png" alt="edit" />
                                </a>

                                <a href="admin_delete_order.php?orderId=<?= htmlspecialchars($order['orderId']) ?>"
                                    onclick="return confirm('Are you sure you want to delete this order?');">
                                    <img src="image/delete.png" alt="delete"/>
                                </a>
                            <?php endif; ?>
                        </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="js/app.js"></script>
</body>

</html>