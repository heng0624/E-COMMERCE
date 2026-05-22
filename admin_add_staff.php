<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/admin_form.css" />
    <title>Create Staff</title>
</head>

<body>
<?php include 'header_admin.php' ?>

    <?php
    
    // Restrict access to only "staff" and "manager"
    auth(["manager"], $_SESSION['ADMINID'] ?? NULL);
    
    $id = generateID("users", "userId", "staff");
    if (is_post()) {
        $email = req('email');
        $name = req('name');
        $password = req('password');
        $f = isset($_FILES['photo']) ? $_FILES['photo'] : null;  // Ensure $f is defined
        $gender = req('gender');
        $phone  = req('phone');  // Fixed typo here
        $date   = req('date');
        $privilege = req('privilege');
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

        // Validate gender
        if ($gender == '') {
            $_err['gender'] = 'Required';
        } else if (!array_key_exists($gender, $_genders)) {
            $_err['gender'] = 'Invalid value';
        }

        // Validate privilege
        if ($privilege == '') {
            $_err['privilege'] = 'Required';
        }

        // Phone validation
        if ($phone == '') {
            $_err['phone'] = 'Required';
        } else if (!preg_match('/^[0-9]{10,12}$/', $phone)) {
            $_err['phone'] = 'Invalid phone number.';
        }

        // Date validation
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

        // Image validation
        $imagePath = ''; // Initialize imagePath variable

        if ($f && $f['error'] == 0) {
            $uploadDir = 'image/';
            $imageName = basename($f['name']);
            $newImagePath = $uploadDir . $imageName;

            $fileExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $fileMime = mime_content_type($f['tmp_name']);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (in_array($fileExt, $allowedExtensions) && in_array($fileMime, $allowedMimeTypes) && $f['size'] <= 1 * 1024 * 1024) {
                move_uploaded_file($f['tmp_name'], $newImagePath);
                $imagePath = $newImagePath; // Update imagePath if the upload is successful
            } else {
                $_err['photo'] = 'Invalid file or exceeds size limit!';
            }
        }

        // If no errors, proceed with registration
        if (!$_err) {

            // Generate unique Cust_ID
            $id = generateID("users", "userID", "staff");

            // Insert into database
            $stm = $_db->prepare('INSERT INTO users (userID, userName, phone, email, gender, birthday, password, profile_image, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stm->execute([$id, $name, $phone, $email, $gender, $date, password_hash($password, PASSWORD_BCRYPT), $imagePath, $privilege]);
            redirect('admin_staff.php?msg=added');
        }
    }

    ?>
    <div class="container">
        <a href="admin_staff.php" class="go-back-btn">← Back to table</a>
        <h1>Create Staff</h1>
        <form action="admin_add_staff.php" method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div>
                    <label>Name  &nbsp&nbsp<?= err('name') ?></label>
                    <input type="text" name="name" value="">
                </div>
                <div>
                    <label>Privilege &nbsp&nbsp<?= err('privilege') ?></label>
                    <select name="privilege">
                        <option value="Manager" selected>Manager</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label>Email &nbsp&nbsp<?= err('email') ?></label>
                    <input type="email" name="email" value="">
                </div>
                <div>
                    <label>Password &nbsp&nbsp<?= err('password') ?></label>
                    <input type="password" name="password" value="">
                </div>
            </div>

            <label>Phone &nbsp&nbsp<?= err('phone') ?></label>
            <input type="tel" name="phone" value="">

            <label>Gender &nbsp&nbsp<?= err('gender') ?></label>
            <div class="gender-group">
                <input type="radio" name="gender" value="M"> Male
                <input type="radio" name="gender" value="F"> Female
            </div>

            <label>Date of birth &nbsp&nbsp<?= err('date') ?></label>
            <input type="date" name="date" id="date" value=""/>

            <div class="staff-avatar-wrapper">
                <img id="uploadedImage" src="" /><?= err('photo') ?>
                <input type="file" id="avatar" name="photo" accept="image/png, image/jpeg, image/gif" onchange="displaySelectedImage(event)" /><br>
            </div>

            <input type="submit" name="create" value="CREATE" class="custom-button">
        </form>
    </div>
    <script src="js/app.js"></script>

</body>

</html>
