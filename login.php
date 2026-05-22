<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/form.css">
</head>

<body>
    <?php

    require_once "base.php";
    session_start();
    $email = "";

    // validation : post
    if (is_post()) {

        // input
        $email = req('email');
        $password = req('password');

        //validation : email
        if (empty($email)) {
            $_err['email'] = "Email is required!";
        } elseif (!is_exists($email, "users", "email")) {
            $_err['email']  = "email not matach!";
        }



        //validation : passsword
        if (empty($password)) {
            $_err['password']  = "Password is required!";
        } else {
            //to check the account exist or not
            $stm = $_db->prepare('SELECT * FROM users WHERE email = ?');
            $stm->execute([$email]);
            $result = $stm->fetch(PDO::FETCH_ASSOC);

            if (!$result) {  // If no user found, set an error
                $_err['email'] = "No account found with this email!";
            } elseif (!password_verify($password, $result["password"])) {
                $_err['password'] = "Password does not match!";
            }
        }


        // if not error , go to home page 
        if (!$_err) {
            $_SESSION['message'] = "login successful";
            $_SESSION['ID'] = $result['userId'];
            $_SESSION['role'] = $result['role'];
                redirect('home.php?msg=login');

        }
    }
    ?>

    <div class="form-container">
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'NoLogin'): ?>
                <div class="alert success" id="successMsg">you have to login as customer first</div>
            <?php elseif ($_GET['msg'] === 'reset'): ?>
                <div class="alert success" id="successMsg">Reset password successfully.</div>
            <?php endif; ?>
        <?php endif; ?>
        <h2>Login</h2>
        <form action="login.php" method="post">
            <input type="email" name="email" placeholder="Enter your email" value="">
            <?= err('email') ?> <!-- Email error message -->

            <input type="password" name="password" placeholder="Enter your password" value="">
            <?= err('password') ?> <!-- Password error message -->

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register</a></p>
        <p><a href="password_reset.php">Forgot Password ?</a></p>
    </div>

</body>
<script src="js/app.js"></script>

</html>