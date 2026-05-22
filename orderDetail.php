<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Detail</title>
    <link rel="stylesheet" href="css/orderDetail.css">
</head>

<body>
    <?php include 'header.php';


    $userID = $_SESSION['ID'] ?? null;
    $orderID = $_GET['orderId'] ?? null;
    auth(["customer"],  $userID );

    if (!$userID) {
        header("Location: login.php");
        exit;
    }


    $stmt = $_db->prepare("SELECT * FROM orders WHERE orderID = ? AND userID = ?");
    $stmt->execute([$orderID, $userID]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<div class='container'><p>Order not found or unauthorized.</p></div>";
        exit;
    }

    $itemsStmt = $_db->prepare("
    SELECT oi.*, p.productName, p.photo
    FROM order_items oi
    JOIN products p ON oi.productID = p.productID
    WHERE oi.orderID = ?
");
    $itemsStmt->execute([$orderID]);
    $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="container">
        <h1>Order Details</h1>

        <div class="order-info">
            <p><strong>Order ID:</strong> <?= $orderID ?></p>
            <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
            <p><strong>Status:</strong> <?= ucfirst($order['order_status']) ?></p>
        </div>

        <table class="order-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td><img src="<?= $item['photo'] ?>" alt="<?= $item['productName'] ?>"></td>
                        <td><?= $item['productName'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>RM <?= number_format($item['price'], 2) ?></td>
                        <td>RM <?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-summary">
            <p><strong>Total Amount: </strong>RM <?= number_format($order['total_amount'], 2) ?></p>
        </div>
        <div class="back-button">
            <a href="orderHistory.php" class="btn-back">← Back to My Orders</a>
        </div>

    </div>
</body>

</html>