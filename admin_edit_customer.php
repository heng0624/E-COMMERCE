<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/admin_form.css" />
    <title>View & Edit Customer</title>
</head>

<body>
<?php include 'header_admin.php' ?>

    <?php  
    // Restrict access to only "staff" and "manager"
    auth(["staff", "manager"], $_SESSION['ADMINID'] ?? NULL);

    if (is_get()) {
        $id = req('customerID');

        $stm = $_db->prepare('SELECT * FROM users WHERE userId = ?');
        $stm->execute([$id]);
        $usersData = $stm->fetch(PDO::FETCH_ASSOC);
    }
    //validation method post
    if (is_post() == 'POST') {
        $ids     = req('id');
        $email  = req('email');
        $name   = req('name');
        $gender = req('gender');
        $phone  = req('phone');
        $date   = req('date');
        $password = req("password");
        $image  = $_FILES['image'];

        $stm = $_db->prepare('SELECT * FROM users WHERE userId = ?');
        $stm->execute([$ids]);
        $usersData = $stm->fetch(PDO::FETCH_ASSOC);
        // email validation
        if ($email == '') {
            $_err['email'] = 'Required';
        } else  if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $_err['email'] = 'Invalid email format!';
        }

        // password validation
        if ($password != '') {
            if (strlen($password) < 8) {
                $_err['password'] = 'Password must be at least 8 characters long.';
            }
        }


        // name validation
        if ($name == '') {
            $_err['name'] = 'Required';
        } else if (strlen($name) > 100) {
            $_err['name'] = 'Maximum length 100';
        }

        // Validate gender
        if ($gender == '') {
            $_err['gender'] = 'Required';
        } else if (!array_key_exists($gender, $_genders)) {
            $_err['gender'] = 'Invalid value';
        }

        // phone validation
        if ($phone == '') {
            $_err['phone'] = 'Required';
        } else if (!preg_match('/^[0-9]{10,12}$/', $phone)) {
            $_err['phone'] = 'Invalid phone number.';
        }

        // date validation
        if ($date == '') {
            $_err['date'] = 'Required';
        } else {
            $dob_timestamp = strtotime($date);
            $today_timestamp = time();
            $min_age = 18 * 365 * 24 * 60 * 60; // 18 years in seconds

            if ($dob_timestamp > $today_timestamp) {
                $_err['date'] = "Invalid birthdate.";
            }
        }



        // image validation
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Allowed image formats
        $allowedMimeTypes  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']; // Allowed MIME types


        // Image validation
        if ($image['error'] == 4) { // No file uploaded
            if ($usersData['profile_image'] == null) { // If no image exists in the database
                $_err['image'] = 'Image is required!';
            } else {
                // Keep the old image if the user hasn't uploaded a new one
                $imagePath = $userData['profile_image'];
            }
        } elseif ($image['error'] == 0) {
            // File uploaded successfully, process the image
            $uploadDir = 'image/';
            $imageName = basename($image['name']);
            $imagePath = $uploadDir . $imageName;

            // Extract file extension & MIME type
            $fileExt  = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $fileMime = mime_content_type($image['tmp_name']);

            // Check for valid file type
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $allowedMimeTypes  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!in_array($fileExt, $allowedExtensions) || !in_array($fileMime, $allowedMimeTypes)) {
                $_err['image'] = 'Invalid file type! Only JPG, JPEG, PNG, GIF, and WEBP are allowed.';
            } elseif ($image['size'] > 1 * 1024 * 1024) { // Check for file size
                $_err['image'] = 'Image size must not exceed 1MB.';
            } else {
                // Move the uploaded file to the server directory
                move_uploaded_file($image['tmp_name'], $imagePath);
            }
        }
        // if record exists and not error , update the customer record
        if (!$_err) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stm = $_db->prepare('UPDATE users
                                      SET userName = ?, phone=?, email=?, password=?,gender=?, birthday=?, profile_image=?
                                      WHERE userId = ?');
            $updated = $stm->execute([$name, $phone, $email, $hashedPassword, $gender, $date, $imagePath, $ids]);
            redirect("admin_customer.php?msg=updated");
        }
    }



    ?>
    <div class="container">
        <a href="admin_customer.php" class="go-back-btn">← Back to table</a>
        <h1>View & Edit Customer</h1>
        <form method="post" action="admin_edit_customer.php" enctype="multipart/form-data">
            <div class="form-row">
                <div>
                    <label>Name: &nbsp&nbsp<?= err('name') ?></label>
                    <input type="text" name="name" value="<?= $usersData["userName"] ?>">
                </div>
                <div>
                    <label>Customer ID: &nbsp&nbsp<?= err('id') ?></label>
                    <input type="text" name="id" value="<?= $usersData["userId"] ?>" readonly>
                </div>
            </div>

            <!-- Email & Password -->
            <div class="form-row">
                <div>
                    <label>Email: &nbsp&nbsp<?= err('email') ?></label>
                    <input type="email" name="email" value="<?= $usersData["email"] ?>">
                </div>
                <div>
                    <label>Password: &nbsp&nbsp<?= err('password') ?></label>
                    <input type="password" name="password" value="<?= $usersData["password"] ?>">
                </div>
            </div>

            <!-- Phone -->
            <label>Phone: &nbsp&nbsp<?= err('phone') ?></label>
            <input type="tel" name="phone" value="<?= $usersData["phone"] ?>">

            <!-- Gender -->
            <label>Gender: &nbsp&nbsp<?= err('gender') ?></label>
            <div class="gender-group">
                <input type="radio" name="gender" value="M" <?= (isset($usersData['gender']) &&  $usersData['gender'] == 'M') ? 'checked' : '' ?>> Male
                <input type="radio" name="gender" value="F" <?= (isset($usersData['gender']) &&  $usersData['gender'] == 'F') ? 'checked' : '' ?>> Female
            </div>

            <!-- Date of Birth -->
            <label>Date of birth: &nbsp&nbsp<?= err('date') ?></label>
            <input type="date" name="date" id="date" value="<?= $usersData["birthday"] ?>" /><br />


            <!-- Avatar Upload -->
            <div class="staff-avatar-wrapper">
                <?= err('image') ?>
                <img id="uploadedImage" src="<?= $usersData["profile_image"] ?>" />
                <input type="file" id="avatar" name="image" accept="image/png, image/jpeg, image/gif" onchange="displaySelectedImage(event)" /><br>
            </div>


            <!-- Submit Button -->
            <input type="submit" name="edit" value="EDIT" class="custom-button">
        </form>
    </div>


</body>
<script src="js/app.js"></script>

</html>