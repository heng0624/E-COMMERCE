<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/admin_form.css" />
    <title>Add New Category</title>
    <link rel="stylesheet" href="css/admin_category.css" />
</head>

<body>
<?php include 'header_admin.php' ?>

    <?php
    // Restrict access to only "staff" and "manager"
    auth(["staff", "manager"], $_SESSION['ADMINID'] ?? NULL);
    
    $id = generateID("categories","CategoryID","");
    if (is_post()) {
        $name = req("CatName");
        $f=$_FILES["photo"];

        // Validate: name
        if ($name == '') {
            $_err['CatName'] = 'Required';
        } else if (strlen($name) > 100) {
            $_err['CatName'] = 'Maximum 100 characters';
        }

         // Retain old image if no new file is uploaded

         if ($f['error'] == 0) {
             $uploadDir = 'image/';
             $imageName = basename($f['name']);
             $newImagePath = $uploadDir . $imageName;
 
             $fileExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
             $fileMime = mime_content_type($f['tmp_name']);
             $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
             $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
 
             if (in_array($fileExt, $allowedExtensions) && in_array($fileMime, $allowedMimeTypes) && $f['size'] <= 1 * 1024 * 1024) {
                 move_uploaded_file($f['tmp_name'], $newImagePath);
                 $imagePath = $newImagePath; // Only update if upload is successful
             } else {
                 $_err['photo'] = 'Invalid file or exceeds size limit!';
             }
         }

        if (!$_err) {
            $stm = $_db->prepare('INSERT INTO categories (CategoryID,CategoryName,photo) VALUES (?, ?,?)');
            $stm->execute([$id,$name,$imagePath]);

            redirect('admin_category.php?msg=added');
        }
        $category = $stm->fetchAll(PDO::FETCH_ASSOC);
    }


    ?>
    <div class="container">
        <a href="admin_category.php" class="go-back-btn">← Back to table</a>
        <h1>Add New Category</h1>

        <form action="admin_add_category.php" method="post" enctype="multipart/form-data">
            <div class="form-row">
                 <!-- Avatar Upload -->
            <div class="staff-avatar-wrapper"><?= err('photo') ?>
                <img id="uploadedImage" src="<?= $productData["photo"]  ?>" alt="" class="product-image" />
                <input type="file" id="avatar" name="photo" accept="image/png, image/jpeg, image/gif" 
                onchange="displaySelectedImage(event)" /><br>
            </div>
                <label>Category Name &nbsp&nbsp<?= err('CatName') ?><label><br>
                <input type="text" name="CatName" value=""><br>

                <input type="submit" name="Add" value="Add" class="custom-button">
        </form>
    </div>
</body>
<script src="js/app.js"></script>


</html>