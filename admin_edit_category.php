<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/admin_form.css" />
    <title>View & Edit Category</title>
    <link rel="stylesheet" href="css/admin_category.css" />
</head>

<body>
<?php include 'header_admin.php' ?>

    <?php
    // Restrict access to only "staff" and "manager"
    auth(["staff", "manager"], $_SESSION['ADMINID'] ?? NULL);

    
     if (is_get()) {
        $id = req('categoryId');

        $stm = $_db->prepare('SELECT * FROM categories WHERE CategoryID = ?');
        $stm->execute([$id]);
        $catgeoryData = $stm->fetch(PDO::FETCH_ASSOC);

    }

    
     if (is_post()) {
        $id = req("CatId"); // <- ADD THIS LINE
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
 
        if ($id != null && !$_err) {
            $stm = $_db->prepare('UPDATE categories SET CategoryName = ? , photo = ? WHERE CategoryID = ?');
            $updated = $stm->execute([$name,$imagePath, $id]);
            redirect('admin_category.php?msg=updated');

        }else{
            echo("record not found or input error！");
        }
    }
    ?>
    <div class="container">
        <a href="admin_category.php" class="go-back-btn">← Back to table</a>
        <h1>View & Edit Category</h1>
        <form method="post" action="admin_edit_category.php" enctype="multipart/form-data">
            <div class="form-row">
            <div class="staff-avatar-wrapper"><?= err('photo') ?>
                <img id="uploadedImage" src="<?= $catgeoryData["photo"] ?>" alt="" class="product-image" />
                <input type="file" id="avatar" name="photo" accept="image/png, image/jpeg, image/gif" onchange="displaySelectedImage(event)" /><br>
            </div>

                <label>Category Name &nbsp&nbsp<?= err('CatName') ?></label>
                <input type="text" name="CatName" value="<?= $catgeoryData["CategoryName"]?>">

                <label>Category ID</label>
                <input type="text" name="CatId" readonly value="<?= $id ?>">

                <input type="submit" name="edit" value="EDIT" class="custom-button">
        </form>
    </div>
</body>

</html>