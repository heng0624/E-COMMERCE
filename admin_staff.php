<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Table</title>
    <link rel="stylesheet" href="css/table.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</head>

<body>
<?php include 'header_admin.php' ?>

    <?php
    // Restrict access to only "staff" and "manager"
    auth(["staff", "manager"], $_SESSION["ADMINID"] ?? null);

    $searchKeyword = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : "";

    // Prepare search query
    if (!empty($searchKeyword)) {
        $stm = $_db->prepare("SELECT * FROM users WHERE (userName LIKE ? 
    OR email LIKE ? OR phone LIKE ? OR userId LIKE ?) AND (role='staff' OR role='manager')");
        $searchTerm = "%$searchKeyword%"; // Add wildcard for partial search
        $stm->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stm = $_db->prepare("SELECT * FROM users WHERE (role='staff' OR role='manager') ");
        $stm->execute();
    }

    $staffData = $stm->fetchAll(PDO::FETCH_ASSOC);

    $count = 1;

    ?>
    <div class="staff-container">
        <div class="content">
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'deleted'): ?>
                    <div class="alert success" id="successMsg">staff successfully deleted.</div>
                <?php elseif ($_GET['msg'] === 'added'): ?>
                    <div class="alert success" id="successMsg">staff successfully added.</div>
                <?php elseif ($_GET['msg'] === 'updated'): ?>
                    <div class="alert success" id="successMsg">staff successfully updated.</div>
                <?php elseif ($_GET['msg'] === 'error'): ?>
                    <div class="alert error" id="errorMsg">Something went wrong. Please try again.</div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="header-container">
                <h2>STAFF TABLE (<?= count($staffData) ?>) </h2>
                <div>                  
                    <a href="admin_add_staff.php"><img src="image/add.png" alt="add" /></a>
                </div>

                <form method="GET" id="searchForm">
                    <div class="search-box">
                        <input type="text" class="search_keyword" name="search_keyword" id="searchInput" placeholder="Search staff..."></input>
                        <button id="searchButton" name="search" value="search">
                            <img src="image/search.png" alt="search" />
                        </button>
                    </div>
                </form>
                
            </div>
            
            <!-- Wrap the table and buttons with a form for deletion -->
            <form method="POST" action="admin_staff.php" id="deleteForm">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Staff ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Privilege</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($staffData as $index => $staff): ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td><?= $staff["userId"] ?></td>
                                <td><?= $staff["userName"] ?></td>
                                <td><?= $staff["email"] ?></td>
                                <td><?= $staff["role"] ?></td>
                                <td>
                                    <a href="admin_edit_staff.php?staffId=<?= $staff["userId"] ?>"><img src="image/edit.png" alt="edit" /></a>
                                    <a href="admin_delete_user.php?staffId=<?= $staff["userId"] ?>"
                                        onclick="return confirm('Are you sure you want to delete this staff member?');">
                                        <img src="image/delete.png" alt="delete" />
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>

</body>
<script src="js/app.js"></script>


</html>