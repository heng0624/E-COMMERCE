<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Table</title>
    <link rel="stylesheet" href="css/table.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php include 'header_admin.php' ?>

    <?php
    // Restrict access to only "staff" and "manager"
    auth(["staff", "manager"], $_SESSION['ADMINID'] ?? NULL);
    
    $searchKeyword = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : "";

    // Prepare search query
    if (!empty($searchKeyword)) {
        $stm = $_db->prepare("SELECT * FROM categories WHERE CategoryName LIKE ?");
        $stm->execute(["%$searchKeyword%"]);
    } else {
        $stm = $_db->prepare("SELECT * FROM categories");
        $stm->execute();
    }

    $categories = $stm->fetchAll(PDO::FETCH_ASSOC);

    $count = 1;

    ?>

    <div class="staff-container">
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'deleted'): ?>
                <div class="alert success" id="successMsg">category successfully deleted.</div>
            <?php elseif ($_GET['msg'] === 'added'): ?>
                <div class="alert success" id="successMsg">category successfully added.</div>
            <?php elseif ($_GET['msg'] === 'updated'): ?>
                <div class="alert success" id="successMsg">category successfully updated.</div>
            <?php elseif ($_GET['msg'] === 'error'): ?>
                <div class="alert error" id="errorMsg">Something went wrong. Please try again.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="header">
            <div class="header-container">
                <h2>Category TABLE (<?= count($categories) ?>)</h2>
                <div>
                    <a href="admin_add_category.php"><img src="image/add.png" alt="add" /></a>
                </div>
                <form method="GET" id="searchForm">
                    <div class="search-box">
                        <input type="text" class="search_keyword" name="search_keyword" id="searchInput"
                            value="<?= htmlspecialchars($searchKeyword) ?>" placeholder="Search category ...">
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
                        <th>Category ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $index => $category): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= $category['CategoryID'] ?></td>
                            <td><?= $category['CategoryName'] ?></td>
                            <td>
                                <a href="admin_edit_category.php?categoryId=<?= $category['CategoryID'] ?>"><img src="image/edit.png" alt="edit" /></a>
                                <a href="admin_delete_category.php?categoryId=<?= $category['CategoryID'] ?>"
                                    onclick="return confirm('Are you sure you want to delete this category member?');"><img src="image/delete.png" alt="delete" />
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <script src="js/app.js"></script>
</body>

</html>