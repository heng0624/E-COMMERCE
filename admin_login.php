<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
    $email=req('email');
    $password=req('password');

     //validation : email
    if (empty($email) ){
        $_err['email'] = "Email is required!";
    } elseif(!is_exists($email,"users","email")){
        $_err['email']  = "email not matach!";

    }

    
    
     //validation : passsword
    if (empty($password)) {
        $_err['password']  = "Password is required!";
    } else{
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
         $_SESSION['ADMINID'] = $result['userId']; 
         $_SESSION['role']=$result['role'];
          // Redirect based on role
            redirect('admin_home.php');
    }
}
?>

<div class="form-container">
    <h2>Admin Login</h2>
    <form action="admin_login.php" method="post"> 
        <input type="email" name="email" placeholder="Enter your email" value="">
        <?= err('email') ?> <!-- Email error message -->
        
        <input type="password" name="password" placeholder="Enter your password" value="" >
        <?= err('password') ?> <!-- Password error message -->
       

        <button type="submit">Login</button>
</form>
</div>

</body>
</html>
