<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Denied</title>
    <link rel="stylesheet" href="css/access_denied.css"/>
</head>
<body>
<?php
session_start();
$role = $_SESSION['role'] ?? null;

?>
    <div class="box">
    <h1>🚫 Access Denied</h1>
        <?php if ($role === 'manager' || $role === 'staff'):?>
        <p>You do not have permission to view this page because only customer can access.</p>
        <a href='admin_login.php?msg=login'>← Return to  admin login</a>
        <?php else:?>
            <p>You do not have permission to view this page becuase only staff and manager can access.</p>
            <a href='login.php?msg=login'>← Return to  user login</a>
        <?php endif;?>
    </div>
</body>
</html>
