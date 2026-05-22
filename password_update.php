<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <title>Update Password</title>
    <link rel="stylesheet" href="css/password_updated.css">

</head>

<body>
    <?php
    include 'header.php';
    $userID = $_SESSION["ID"] ?? null;
    auth(["customer"],  $userID);

    if (!$userID) {
        header("Location: login.php");
        exit;
    }
    //retrieve data from database
    // $customerData = getData($_SESSION['ID'], 'customer', 'Cust_ID');
    $stm = $_db->prepare('SELECT * FROM users WHERE userId = ?');
    $stm->execute([$_SESSION['ID']]);
    $customerData = $stm->fetch(PDO::FETCH_ASSOC);

    if (is_post()) {
        //input
        $oldPassword = $_POST['old_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        echo $oldPassword ,$confirmPassword;

        // Check if the input field is empty first
        if (empty($oldPassword)) {
            $_err['old_password'] = "❌ Required!";
        } elseif (!password_verify($oldPassword, $customerData['password'])) {
            $_err['old_password'] = "❌ Old password does not match!";
        }

        // Validate New Password Length
        if (strlen($newPassword) < 8) {
            $_err['new_password'] = "❌ New password must be at least 8 characters!";
        }
        if (empty($newPassword)) {
            $_err['new_password'] = "required";
        }

        // Validate Confirm Password
        if ($newPassword !== $confirmPassword) {
            $_err['confirm_password'] = "❌ Passwords do not match!";
        }

        // If no errors, update password
        if (!$_err) {
            $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $_db->prepare("UPDATE users SET password=? WHERE userId=?");
            $update->execute([$hashed_password, $userID]); // ✅ Fixed Cust_ID reference
             
            redirect("password_update.php?msg=updated");
        }
    }
    ?>
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'updated'): ?>
            <div class="alert success" id="successMsg">password successfully updated.</div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="container">
        <h3>Change Password</h3>
        <form action="password_update.php" method="post">
            <label for="old_password">Old Password</label>
            <input type="password" id="old_password" name="old_password" >
            <?php if (!empty($_err['old_password'])): ?>
                <div class="error"><?= $_err['old_password'] ?></div>
            <?php endif; ?>

            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" >
            <?php if (!empty($_err['new_password'])): ?>
                <div class="error"><?= $_err['new_password'] ?></div>
            <?php endif; ?>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password">
            <?php if (!empty($_err['confirm_password'])): ?>
                <div class="error"><?= $_err['confirm_password'] ?></div>
            <?php endif; ?>

            <input type="submit" name="change_password" value="Change Password">
        </form>
    </div>
</body>
<script src="js/app.js"></script>
</html>