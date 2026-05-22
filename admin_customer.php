<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Table</title>
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
        $stm = $_db->prepare("SELECT * FROM users WHERE (userName LIKE ? 
        OR email LIKE ? OR phone LIKE ? OR userId LIKE ?) AND role='customer'");
        $searchTerm = "%$searchKeyword%"; // Add wildcard for partial search
        $stm->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stm = $_db->prepare("SELECT * FROM users WHERE role='customer' ");
        $stm->execute();
    }

    $customerData = $stm->fetchAll(PDO::FETCH_ASSOC);

    $count = 1;

    ?>
    <div class="staff-container">
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'deleted'): ?>
                <div class="alert success" id="successMsg">customers successfully deleted.</div>
            <?php elseif ($_GET['msg'] === 'added'): ?>
                <div class="alert success" id="successMsg">customers successfully added.</div>
            <?php elseif ($_GET['msg'] === 'updated'): ?>
                <div class="alert success" id="successMsg">customers successfully updated.</div>
            <?php elseif ($_GET['msg'] === 'error'): ?>
                <div class="alert error" id="errorMsg">Something went wrong. Please try again.</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="header-container">
            <h2>Customer TABLE (<?= count($customerData) ?>) </h2>
            <form method="GET" id="searchForm">
                <div class="search-box">
                    <input type="text" class="search_keyword" name="search_keyword" id="searchInput" placeholder="Search customers..."></input>
                    <button id="searchButton" name="search" value="search">
                        <img src="image/search.png" alt="search" />
                    </button>
                </div>
            </form>
        </div>

        <table>
            <thead>

                <tr>
                    <th>#</th>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>

            </thead>
            <tbody>
                <?php foreach ($customerData as $index => $customer): ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= $customer["userId"] ?></td>
                        <td><?= $customer["userName"] ?></td>
                        <td><?= $customer["phone"] ?></td>
                        <td><?= $customer["email"] ?></td>

                        <td>
                            <a href="admin_edit_customer.php?customerID=<?= $customer["userId"] ?>"><img src="image/edit.png" alt="edit" /></a>
                            <a href="admin_delete_user.php?customerID=<?= $customer["userId"] ?>"
                                onclick="return confirm('Are you sure you want to delete this customers member?');">
                                <img src="image/delete.png" alt="delete" />
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
<script src="js/app.js"></script>
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