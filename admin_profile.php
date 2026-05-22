<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile </title>
    <link rel="stylesheet" href="css/admin_profile.css">

</head>

<body>
    <?php include "header_admin.php" ?>

    <?php
   
    // Restrict access to only "staff" and "manager"

    $staffID = $_SESSION['ADMINID']  ?? null;
    auth(["staff", "manager"], $staffID);

    $stm = $_db->prepare('SELECT * FROM users WHERE userId = ?');
    $stm->execute([$staffID]);
    $staffData = $stm->fetch(PDO::FETCH_ASSOC);

    //validation method post
    if (is_post()) {
        $id     = $staffData['userId'] ?? null;
        $email  = req('email')?? null;
        $name   = req('name')?? null;
        $gender = req('gender')?? null;
        $phone  = req('phone')?? null;
        $date   = req('date')?? null;
        $image  = $_FILES['image']?? null;
        $_err = [];
        
        // email validation
        if ($email == '') {
            $_err['email'] = 'Required';
        } else  if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $_err['email'] = 'Invalid email format!';
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
            if (empty($staffData['profile_image'])) { // If no image exists in the database
                $_err['image'] = 'Image is required!';
            } else {
                // Keep the old image if the user hasn't uploaded a new one
                $imagePath = $staffData['profile_image'];
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
        if ($staffData && !$_err) {
            $stm = $_db->prepare('UPDATE users
                                      SET userName = ?, phone=?, email=?, gender=?, birthday=?, profile_image=?
                                      WHERE userId = ?');
            $updated = $stm->execute([$name, $phone, $email, $gender, $date, $imagePath, $id]);
            redirect("admin_profile.php?msg=updated");
        }
    }

    ?>
    <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'updated'): ?>
                <div class="alert success" id="successMsg">Your profile is updated successfully</div>
        <?php endif;?>
        <?php endif; ?>

    <div class="container">
        <main class="profile-container">
            <h2>PROFILE</h2>
            <form class="profile-form" id="profileForm" action="admin_profile.php" method="POST" enctype="multipart/form-data">
                <div class="profile-image-section">
                    <img id="uploadedImage" src="<?= $staffData["profile_image"] ?>" alt="User Avatar" class="profile-img-large">
                    <input type="file" id="imageInput" name="image" accept="image/png, image/jpeg, image/gif" style="display: none;" onchange="displaySelectedImage(event)">
                    <a href="#" onclick="document.getElementById('imageInput').click(); return false;" class="select-image" name="image">Select Image</a> <?= err('image') ?>
                </div>

                <label>Name</label>
                <input type="text" placeholder="Enter your name" name="name" value="<?=$staffData['userName'] ?>"> <?= err('name') ?>

                <label>Email</label>
                <input type="email" placeholder="Enter your email" name="email" value="<?= $staffData['email'] ?>"> <?= err('email') ?>

                <label>Phone</label>
                <input type="tel" placeholder="Enter your phone number" name="phone" value="<?= $staffData['phone'] ?>"> <?= err('phone') ?>

                <label>Gender</label>
                <div class="gender-options">
                    <input type="radio" name="gender" id="male" value="M" <?= (isset($staffData['gender']) &&  $staffData['gender'] == 'M') ? 'checked' : '' ?>>
                    <label for="male">Male</label>
                    <input type="radio" name="gender" id="female" value="F" <?= (isset($staffData['gender']) &&  $staffData['gender'] == 'F') ? 'checked' : '' ?>>
                    <label for="female">Female</label>

                    <?= err('gender') ?>
                </div>

                <label>Date of Birth</label>
                <div class="dob">
                    <input type="date" name="date" id="date" value="<?=$staffData['birthday'] ?>" /> <?= err('date') ?>
                </div>

                <input type="submit" class="edit-btn" name="submit" value="Update Profile" />
            </form>
            <script src="js/app.js"></script>
        </main>


</html>