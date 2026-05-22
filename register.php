<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
<?php
session_start();
require_once "base.php";

$email = $name = $password = "";

if (is_post()) {
    $email = req('email') ?? null;
    $name = req('name') ?? null;
    $password = req('password') ?? null;
    $image=$_FILES['image'] ?? null;
    $_err = [];
  
    // Email validation
    if (empty($email)) {
        $_err['email'] = "Email is required!";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $_err['email']  = "Invalid email format!";
    } elseif (!is_unique($email, 'users', 'email')) {
        $_err['email'] = 'Duplicated email!';
    }

    // Password validation
    if (empty($password)) {
        $_err['password'] = "Password is required!";
    } elseif (strlen($password) < 8) {
        $_err['password'] = "Password must be at least 8 characters!";
    }

    // Name validation
    if (empty($name)) {
        $_err['name'] = "Name is required!";
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        $_err['name'] = "Only letters and white space allowed!";
    }

    // image validation
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Allowed image formats
    $allowedMimeTypes  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']; // Allowed MIME types
      if ($image['error'] == 4) { // Error code 4 means "No file uploaded"
            $_err['image'] = 'Required!';
      }else  if ($image['error'] == 0) {
        $uploadDir = 'image/';
        $imageName = basename($image['name']);
        $imagePath = $uploadDir . $imageName;

        // ✅ Extract file extension & MIME type
        $fileExt  = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $fileMime = mime_content_type($image['tmp_name']);

        // ❌ Reject if not an image format
        if (!in_array($fileExt, $allowedExtensions) || !in_array($fileMime, $allowedMimeTypes)) {
            $_err['image'] = 'Invalid file type! Only JPG, JPEG, PNG, GIF, and WEBP are allowed.';
        }
        // ❌ Reject if file is too large
        else if ($image['size'] > 1 * 1024 * 1024) {
            $_err['image'] = 'Image size must not exceed 1MB.';
        }
    } else {
        // Keep old image if no new file is uploaded
        $imagePath = $customerData['profile_image'];
    }

    // If no errors, proceed with registration
    if (!$_err) {
        // Generate unique Cust_ID
        $id = generateID("users", "userId","customer");
        // Insert into database
        $stm = $_db->prepare('INSERT INTO users (userId, userName, email, password,profile_image,role) VALUES (?, ?, ?, ?,?,?)');
        $stm->execute([$id, $name, $email, password_hash($password, PASSWORD_BCRYPT),$imagePath,"customer"]);

        $_SESSION['ID'] = $id;

        redirect('home.php?msg=register');
    }
}
?>
    <div class="form-container">
        <h2>Sign Up</h2>
        <form action="register.php" method="post"  enctype="multipart/form-data">
            <div class="profile-image-section">
                <img id="uploadedImage" src="<?= $customerData["profile_image"] ?>" alt="" class="profile-img-large">
                <input type="file" id="imageInput" name="image" accept="image/png, image/jpeg, image/gif" style="display: none;" onchange="displaySelectedImage(event)">
                <a href="#" onclick="document.getElementById('imageInput').click(); return false;" class="select-image" name="image">Select Image</a> <?= err('image') ?>
            </div>
            <input type="text" name="name" placeholder="Enter your name" value="<?php echo htmlspecialchars($name) ?>">
            <?= err('name') ?>

            <input type="email" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email) ?>">
            <?= err('email') ?>

            <input type="password" name="password" placeholder="Create a password">
            <?= err('password') ?>

            <button type="submit" name="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
    <script src="js/app.js"></script>
</body>

</html>