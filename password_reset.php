<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/password_reset.css">
</head>

<body>
    <?php
    require "base.php";

    //validation : method (post)
    if (is_post()) {
        //input
        $newPassword = $_POST['new_password'];
        $email = $_POST['email'];

        //validation:email
        if (empty($email)) {
            $_err['email'] = "Email is required!";
        } else if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $_err['email']  = "Invalid email format!";
        } else if (!is_exists($email, "users", "email")) {
            $_err['email']  = "Account Not exist!";
        }

        //validation:password
        if (strlen($newPassword) < 8) {
            $_err['new_password'] = "New password must be at least 8 characters!";
        }

        // if not error , update the password
        if (!$_err) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stm = $_db->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stm->execute([$hashedPassword, $email]);
            redirect("login.php?msg=reset");
        }
    }
    ?>
       <div class="reset-container">
        <h2>Reset Your Password</h2>
        <form method="POST" action="password_reset.php">
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= $_POST['email'] ?? '' ?>" >
            <?php if (!empty($_err['email'])): ?>
                <div class="error"><?= $_err['email'] ?></div>
            <?php endif; ?>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" >
            <?php if (!empty($_err['new_password'])): ?>
                <div class="error"><?= $_err['new_password'] ?></div>
            <?php endif; ?>

            <button type="submit">Reset Password</button>
        </form>

        <a class="back-link" href="login.php">← Back to Login</a>
    </div>

</body>

</html>