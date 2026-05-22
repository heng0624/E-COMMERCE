<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/admin_form.css" />
    <title>Add New Product</title>
</head>

<body>
<?php include 'header_admin.php' ?>

    <?php
    // Restrict access to only "staff" and "manager"
    auth(["staff", "manager"], $_SESSION['ADMINID'] ?? NULL);
    
    $categoryData = getData("", "categories","");
    $proId    = generateID("products", "ProductID",null);

   
    if (is_post()) {    
        $name  = req('ProName');
        $price = req('price');
        $stock = req('stock');
        $des    = req('des');
        $category = req('category');
        $f = $_FILES['photo'];

        //get ID from category table
        $catID=getData($category, "categories","CategoryID");
        foreach($catID as $ID){
            $id=$ID["CategoryID"];
        }
        // Validate: name
        if ($name == '') {
            $_err['ProName'] = 'Required!';
        } else if (strlen($name) > 100) {
            $_err['ProName'] = 'Maximum 100 characters';
        }

        // Validate: price
        if ($price == '') {
            $_err['price'] = 'Required!';
        } else if (!is_money($price)) { // TODO
            $_err['price'] = 'Must be money format';
        } else if ($price < 0.01 || $price > 999.99) { // TODO
            $_err['price'] = 'Must between 0.01 - 99.99';
        }

        // Validate: photo (file)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Allowed image formats
        $allowedMimeTypes  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']; // Allowed MIME types

        if ($f['error'] == 4) { // Error code 4 means "No file uploaded"
            $_err['photo'] = 'Required!';
        } else if ($f['error'] == 0) {
            $uploadDir = 'image/';
            $imageName = basename($f['name']);
            $imagePath = $uploadDir . $imageName;
        
            // ✅ Extract file extension & MIME type
            $fileExt  = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $fileMime = mime_content_type($f['tmp_name']);
        
            // ❌ Reject if not an image format
            if (!in_array($fileExt, $allowedExtensions) || !in_array($fileMime, $allowedMimeTypes)) {
                $_err['photo'] = 'Invalid file type! Only JPG, JPEG, PNG, GIF, and WEBP are allowed.';
            }
            // ❌ Reject if file is too large
            else if ($f['size'] > 1 * 1024 * 1024) {
                $_err['photo'] = 'Image size must not exceed 1MB.';
            }
        }
        



       //validation :stock
        if ($stock == '') {
            $_err['stock'] = 'Required!';
        } else if (!ctype_digit($stock)) {
            $_err['stock'] = 'the stock must be integer';
        }

        //validation :category
        if ($category == '') {
            $_err['category'] = 'Required!';
        }


        if (!$_err) {
            // $uploadDir = 'uploads/';

            // $imagePath = save_photo($f, $uploadDir, 500, 500); // Crop & resize

                $stm = $_db->prepare('
                INSERT INTO products (ProductID,ProductName,Description,Price,Stock,CategoryID,Photo)
                VALUES (?, ?, ?, ?,?,?,?)');
                $stm->execute([$proId,$name,$des,$price,$stock, $id,$imagePath]);
            
              redirect("admin_product.php?msg=added");
            
        }
    }
    
    ?>
    <div class="container">
        <a href="admin_product.php" class="go-back-btn">← Back to table</a>
        <h1>Add New Product</h1>

        <form method="post" action="admin_add_product.php" enctype="multipart/form-data">

            <div class="form-row">
                <div>
                    <label>Product Name &nbsp&nbsp<?= err('ProName') ?></label>
                    <input type="text" name="ProName" value="">
                </div>
                <div>
                    <label>Category &nbsp&nbsp<?= err('category') ?></label>
                    <select name="category">
                    <option value="">Select Category</option>
                    <?php foreach ($categoryData as $category): ?>
                        <option value="<?= $category['CategoryID'] ?>">
                            <?= htmlspecialchars($category['CategoryName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label>Stock &nbsp&nbsp<?= err('stock') ?></label>
                    <input type="number" name="stock" value="">
                </div>
                <div>
                    <label>Price(RM) &nbsp&nbsp<?= err('price') ?></label>
                    <input type="number" name="price" value="">
                </div>
            </div>
            <label>Description &nbsp&nbsp<?= err('des') ?></label>
            <textarea id="description" name="des" rows="8" cols="100"></textarea></br>
            <!-- Avatar Upload -->
            <div class="staff-avatar-wrapper">
                <img id="uploadedImage" src="image/doraemon-avatar.png" />
                <input type="file" id="avatar" name="photo" accept="image/png, image/jpeg, image/gif" onchange="displaySelectedImage(event)" /><br>
            </div><?= err('photo') ?>

            <input type="submit" name="add" value="Add" class="custom-button">
        </form>
    </div>
    <script src="js/app.js"></script>
</body>

</html>