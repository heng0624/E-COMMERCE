<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/admin_form.css" />
    <title>View & Edit Order</title>
</head>

<body>
<?php include 'header_admin.php' ?>

<?php

auth(["staff", "manager"], $_SESSION['ADMINID'] ?? NULL);

if (is_get()) {
    $orderId = req("orderId");

    $stm = $_db->prepare('SELECT 
        o.orderId,
        u.userName,
        o.total_amount,
        o.order_status,
        o.created_at,
        o.shipping_address,
        oi.productId,
        p.productName,
        oi.quantity,
        oi.price
    FROM orders o
    JOIN users u ON o.userId = u.userId
    JOIN order_items oi ON o.orderId = oi.orderId
    JOIN products p ON oi.productId = p.productId
    WHERE o.orderId = ?');

    $stm->execute([$orderId]);
    $ordersData = $stm->fetchAll(PDO::FETCH_ASSOC);

    if (!$ordersData) {
        echo "<script>alert('Orders not found');window.location.href='admin_order.php';</script>";
        exit();
    }

    $order = $ordersData[0];
}

if (is_post()) {
    $ordersId = req("id");
    $dateTime = req("dateTime");
    $status = req("status");
    $address = req("address");
    $items = $_POST['items'] ?? [];
    $totalAmount = 0;

    // Validation
    if ($dateTime == '') {
        $_err['dateTime'] = 'Required';
    }

    if ($address == '') {
        $_err["address"] = "Required!";
    } else if (strlen($address) < 5) {
        $_err["address"] = "Address is too short.";
    }

    if ($status == '') {
        $_err['status'] = 'Required';
    }

    if (!$_err) {
        // Recalculate total amount
        foreach ($items as $item) {
            $quantity = (int)$item['quantity'];
            $price = (float)$item['price'];
            $totalAmount += $quantity * $price;
        }

        $stm = $_db->prepare('UPDATE orders
            SET total_amount = ?, order_status = ?, shipping_address = ?, created_at = ?
            WHERE orderId = ?');
        $stm->execute([$totalAmount, $status, $address, $dateTime, $ordersId]);

        foreach ($items as $item) {
            $productId = $item['productId'];
            $quantity = (int)$item['quantity'];
            $price = (float)$item['price'];

            $stmItem = $_db->prepare("UPDATE order_items
                SET quantity = ?, price = ?
                WHERE orderId = ? AND productId = ?");
            $stmItem->execute([$quantity, $price, $ordersId, $productId]);
        }

        redirect("admin_order.php?msg=updated");
    }

    // If errors, refetch order data
    $stm = $_db->prepare('SELECT 
        o.orderId,
        u.userName,
        o.total_amount,
        o.order_status,
        o.created_at,
        o.shipping_address,
        oi.productId,
        p.productName,
        oi.quantity,
        oi.price
    FROM orders o
    JOIN users u ON o.userId = u.userId
    JOIN order_items oi ON o.orderId = oi.orderId
    JOIN products p ON oi.productId = p.productId
    WHERE o.orderId = ?');

    $stm->execute([$ordersId]);
    $ordersData = $stm->fetchAll(PDO::FETCH_ASSOC);
    $order = $ordersData[0];
}
?>

<div class="container">
    <a href="admin_order.php" class="go-back-btn">← Back to table</a>
    <h1>View & Edit Order</h1>

    <form action="admin_edit_order.php" method="post">
        <div class="form-row">
            <div>
                <label>Order ID </label>
                <input type="text" name="id" value="<?= $order["orderId"] ?>" readonly>
            </div>

            <div>
                <label>Customer Name</label>
                <input type="text" name="userName" value="<?= $order["userName"] ?>" readonly>
            </div>

            <div>
                <label>Date & Time <?= err('dateTime') ?></label>
                <input type="datetime-local" name="dateTime" value="<?= date('Y-m-d\TH:i', strtotime($order["created_at"])) ?>" />
            </div>
        </div>

        <div class="form-row">
            <div>
                <label>Total Amount</label>
                <input type="number" name="totalAmount" value="<?= $order["total_amount"] ?>" readonly>
            </div>

            <div>
                <label>Order Status <?= err('status') ?></label>
                <select name="status">
                    <option value="">SELECT THE STATUS</option>
                    <?php
                    $statuses = ["Pending", "Paid", "Shipped", "Delivered", "Completed"];
                    foreach ($statuses as $s) {
                        $selected = ($order['order_status'] == $s) ? 'selected' : '';
                        echo "<option value='$s' $selected>$s</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <?php if (!empty($ordersData) && is_array($ordersData)): ?>
            <h3>Ordered Items</h3>
            <?php foreach ($ordersData as $index => $item): ?>
                <div class="form-row">
                    <div>
                        <label>Product Name</label>
                        <input type="text" name="items[<?= $index ?>][name]" value="<?= htmlspecialchars($item['productName']) ?>" readonly />
                    </div>
                    <div>
                        <label>Quantity</label>
                        <input type="number" name="items[<?= $index ?>][quantity]" value="<?= $item['quantity'] ?>" min="1">
                    </div>
                    <div>
                        <label>Price (RM)</label>
                        <input type="number" step="0.01" name="items[<?= $index ?>][price]" value="<?= $item['price'] ?>" min="0">
                    </div>
                    <input type="hidden" name="items[<?= $index ?>][productId]" value="<?= $item['productId'] ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <label>Delivery Address <?= err('address') ?></label>
        <textarea id="address" name="address" rows="8" cols="100"><?= htmlspecialchars($order["shipping_address"]) ?></textarea><br>

        <input type="submit" name="edit" value="EDIT" class="custom-button">
    </form>
</div>
</body>

</html>
