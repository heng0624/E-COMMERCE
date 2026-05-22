<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Table</title>
    <link rel="stylesheet" href="css/table.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'header_admin.php' ?>

    <?php
    // Restrict access to only "staff" and "manager"
    auth(["staff", "manager"], $_SESSION['ADMINID'] ?? null);
    $searchKeyword = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : "";
    // allow user search the name, price, stock, and category
    if (!empty($searchKeyword)) {
        $stm = $_db->prepare("
                SELECT p.*, c.CategoryName 
                FROM products p
                LEFT JOIN categories c ON p.CategoryID = c.CategoryID
                WHERE p.ProductName LIKE ? 
                OR c.CategoryName LIKE ? 
                OR p.Price LIKE ? 
                OR p.Stock LIKE ?
            ");
        $stm->execute(["%$searchKeyword%", "%$searchKeyword%", "%$searchKeyword%", "%$searchKeyword%"]);
    } else {
        $stm = $_db->prepare("
                SELECT p.*, c.CategoryName 
                FROM products p
                LEFT JOIN categories c ON p.CategoryID = c.CategoryID
            ");
        $stm->execute();
    }

    $products = $stm->fetchAll(PDO::FETCH_ASSOC);
    $count = 1;

    // Check if the form is submitted to delete selected products
    if (isset($_POST['delete_selected']) && !empty($_POST['deleteIds'])) {
        // Sanitize and prepare the list of IDs to be deleted
        $productIds = $_POST['deleteIds'];

        // Prepare the query to delete selected products
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $stm = $_db->prepare("DELETE FROM products WHERE ProductID IN ($placeholders)");
        $stm->execute($productIds);

        // Redirect after deletion
        redirect("admin_product.php");
    }
    ?>

    <div class="staff-container">
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'deleted'): ?>
                <div class="alert success" id="successMsg">Product successfully deleted.</div>
            <?php elseif ($_GET['msg'] === 'added'): ?>
                <div class="alert success" id="successMsg">Product successfully added.</div>
            <?php elseif ($_GET['msg'] === 'updated'): ?>
                <div class="alert success" id="successMsg">Product successfully updated.</div>
            <?php elseif ($_GET['msg'] === 'error'): ?>
                <div class="alert error" id="errorMsg">Something went wrong. Please try again.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="header-container">
            <h2>Product TABLE (<?= count($products) ?>) </h2>
            <div>
                <a href="admin_add_product.php"><img src="image/add.png" alt="add" /></a>
            </div>
            <form method="GET" id="searchForm">
                <div class="search-box">
                    <input type="text" class="search_keyword" name="search_keyword" id="searchInput"
                        value="<?= htmlspecialchars($searchKeyword) ?>" placeholder="Search product ...">
                    <button type="submit" id="searchButton">
                        <img src="image/search.png" alt="search" />
                    </button>
                </div>
            </form>
        </div>


        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $index => $product): ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= $product['ProductID'] ?></td>
                        <td><?= $product['ProductName'] ?></td>
                        <td><?= $product['Price'] ?></td>
                        <td><?= $product['Stock'] ?></td>
                        <td>
                            <?php
                            // Get category name using product category ID
                            $categoryData = getData($product['CategoryID'], "categories", "CategoryID");
                            foreach ($categoryData as $category) {
                                $name = $category["CategoryName"];
                            }
                            echo htmlspecialchars($name);
                            ?>
                        </td>
                        <td>
                            <a href="admin_edit_product.php?productId=<?= $product['ProductID'] ?>"><img src="image/edit.png" alt="edit" /></a>
                            <a href="admin_delete_product.php?productId=<?= $product['ProductID'] ?>"
                                onclick="return confirm('Are you sure you want to delete this product?');">
                                <img src="image/delete.png" alt="delete" /></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="js/app.js"></script>
</body>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const msg = document.getElementById('successMsg');
        if (msg) {
            setTimeout(() => {
                msg.style.display = 'none';
            }, 2000); // 2 seconds
        }
    });
</script>

</html>