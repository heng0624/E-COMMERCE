<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/admin_form.css" />
    <title>View & Edit Product</title>
</head>

<body>
<?php include 'header_admin.php' ?>

    <?php     
    
     // Restrict access to only "staff" and "manager"
     auth(["staff", "manager"], $_SESSION['ADMINID'] ?? NULL);

    $categoryData = getData("", "categories", "");

    if (is_get()) {
        $id = req('productId');

        $stm = $_db->prepare('SELECT * FROM products WHERE ProductID = ?');
        $stm->execute([$id]);
        $productData = $stm->fetch(PDO::FETCH_ASSOC);

        if (!$productData) {
            redirect('admin_home.php');
            exit;
        }

        // ✅ Fetch category details
        $cat = getData($productData['CategoryID'], "categories", "CategoryID");
    }

    if (is_post()) {
        $id = req('productId');
        $name = req('ProName');
        $price = req('price');
        $stock = req('stock');
        $des = req('des');
        $category = req('category');
        $f = $_FILES['photo'];

        // ✅ Get category ID correctly
        $catID = getData($category, "categories", "CategoryID");
        foreach ($catID as $ID) {
            $categoryID = $ID["CategoryID"]; // Store in a separate variable
        }
        $stm = $_db->prepare('SELECT * FROM products WHERE ProductID = ?');
        $stm->execute([$id]);
        $productData = $stm->fetch(PDO::FETCH_ASSOC);
        // Validate: Name
        if ($name == '') {
            $_err['ProName'] = 'Required!';
        } else if (strlen($name) > 100) {
            $_err['ProName'] = 'Maximum 100 characters';
        }

        // Validate: Price
        if ($price == '') {
            $_err['price'] = 'Required!';
        } else if (!is_money($price)) {
            $_err['price'] = 'Must be money format';
        } else if ($price < 0.01 || $price > 999.99) {
            $_err['price'] = 'Must be between 0.01 - 999.99';
        }

        // Retain old image if no new file is uploaded
        $imagePath = $productData['Photo'];

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
        // Validate: Stock
        if ($stock == '') {
            $_err['stock'] = 'Required!';
        } else if (!ctype_digit($stock)) {
            $_err['stock'] = 'Stock must be an integer!';
        }

        // Validate: Category
        if ($category == '') {
            $_err['category'] = 'Required!';
        }

        if (!$_err && $id != null) {
            // $uploadDir = 'uploads/';

            // $imagePath = save_photo($f, $uploadDir, 500, 500); // Crop & resize
            $stm = $_db->prepare('UPDATE products
                                  SET ProductName = ?, Description = ?, Price = ?, Stock = ?, CategoryID = ?, Photo = ?
                                  WHERE ProductID = ?');
            $stm->execute([$name, $des, $price, $stock, $categoryID, $imagePath, $id]);

            redirect("admin_product.php?msg=updated");
        }
    }
    ?>
    <div class="container">
        <a href="admin_product.php" class="go-back-btn">← Back to table</a>
        <h1>View & Edit Product</h1>

        <form action="admin_edit_product.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="productId" value="<?= $productData["ProductID"] ?>">
            <div class="form-row">
                <div>
                    <label>Product ID </label>
                    <input type="text" name="ID" value="<?= $productData["ProductID"] ?>" readonly>
                </div>
                <div>
                    <label>Product Name &nbsp&nbsp<?= err('ProName') ?></label>
                    <input type="text" name="ProName" value="<?= $productData["ProductName"] ?>">
                </div>
                <div>
                    <label>Category &nbsp&nbsp<?= err('category') ?></label>
                    <select name="category">
                        <?php foreach ($cat as $categories): ?>
                            <option value="<?= $categories['CategoryID'] ?>" <?= ($categories['CategoryID'] == $productData['CategoryID']) ? 'selected' : '' ?>
                                ?><?= htmlspecialchars($categories['CategoryName']) ?></option>
                            <?php foreach ($categoryData as $category): ?>
                                <option value="<?= $category['CategoryID'] ?>">
                                    <?= htmlspecialchars($category['CategoryName']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label>Stock &nbsp&nbsp<?= err('stock') ?></label>
                    <input type="number" name="stock" value="<?= $productData["Stock"] ?>">
                </div>
                <div>
                    <label>Price(RM) &nbsp&nbsp<?= err('price') ?></label>
                    <input type="number" name="price" value="<?= $productData["Price"] ?>">
                </div>
            </div>


            <label>Description &nbsp&nbsp<?= err('des') ?></label>
            <textarea id="description" name="des" rows="8" cols="100"><?= htmlspecialchars($productData['Description']) ?></textarea></br>
            <!-- Avatar Upload -->
            <div class="staff-avatar-wrapper"><?= err('photo') ?>
                <img id="uploadedImage" src="<?= $productData["Photo"] ?>" alt="Product Avatar" class="product-image" />
                <input type="file" id="avatar" name="photo" accept="image/png, image/jpeg, image/gif" onchange="displaySelectedImage(event)" /><br>
            </div>
            <input type="submit" name="edit" value="EDIT" class="custom-button">
        </form>

    </div>

</body>
<script src="js/app.js"></script>

</html>