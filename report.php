<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Top 5 Sold Products Report</title>
     <link rel="stylesheet" href="css/report.css"/>
</head>
<body>

<?php
include 'header_admin.php'; 
auth(["staff", "manager"], $_SESSION['ADMINID']);

$filterType = $_GET['filter_type'] ?? null;
$filterDate = $_GET['filter_date'] ?? null;

$whereClause = '';
$params = [];

if ($filterType && $filterDate) {
    if ($filterType === 'daily') {
        $whereClause = "WHERE DATE(o.created_at) = :filter_date";
        $params[':filter_date'] = $filterDate;
    } elseif ($filterType === 'monthly') {
        $whereClause = "WHERE MONTH(o.created_at) = MONTH(:filter_date) AND YEAR(o.created_at) = YEAR(:filter_date)";
        $params[':filter_date'] = $filterDate;
    }
}

try {
    $sql = "SELECT 
                p.productID,
                p.productName,
                p.photo,
                SUM(oi.quantity) AS total_sold,
                SUM(oi.quantity * oi.price) AS total_income
            FROM order_items oi
            JOIN products p ON oi.productID = p.productID
            JOIN orders o ON oi.orderID = o.orderID
            $whereClause
            GROUP BY p.productID, p.productName, p.photo
            ORDER BY total_sold DESC
            LIMIT 5";

    $stmt = $_db->prepare($sql);
    $stmt->execute($params);
    $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p class='center'>Error generating report: " . $e->getMessage() . "</p>";
    exit;
}
?>

<h2>Top 5 Sold Products Report</h2>

<form method="GET">
    <label>Filter by:</label>
    <select name="filter_type" required>
        <option value="">--Select--</option>
        <option value="daily" <?= $filterType === 'daily' ? 'selected' : '' ?>>Daily</option>
        <option value="monthly" <?= $filterType === 'monthly' ? 'selected' : '' ?>>Monthly</option>
    </select>
    <input type="date" name="filter_date" value="<?= htmlspecialchars($filterDate ?? '') ?>" required>
    <button type="submit">Generate</button>
</form>

<?php if (!empty($topProducts)): ?>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Product ID</th>
                <th>Name</th>
                <th>Image</th>
                <th>Units Sold</th>
                <th>Total Income (RM)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topProducts as $index => $product): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($product['productID']) ?></td>
                <td><?= htmlspecialchars($product['productName']) ?></td>
                <td><img src="<?= htmlspecialchars($product['photo']) ?>" alt="<?= htmlspecialchars($product['productName']) ?>"></td>
                <td><?= $product['total_sold'] ?></td>
                <td><?= number_format($product['total_income'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="center">No sales data found for the selected filter.</p>
<?php endif; ?>

</body>
</html>
