<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/home.css">
</head>

<body>
    <?php include "header.php"; ?>

    <?php
    $searchKeyword = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : "";
    if (!empty($searchKeyword)) {
        $stm = $_db->prepare("
        SELECT p.*, c.CategoryName 
        FROM products p
        LEFT JOIN categories c ON p.CategoryID = c.CategoryID
        WHERE p.ProductName LIKE ? 
        OR c.CategoryName LIKE ? 
        OR p.Price LIKE ? 
        OR p.Stock LIKE ?
    ");
        $stm->execute(["%$searchKeyword%", "%$searchKeyword%", "%$searchKeyword%", "%$searchKeyword%"]);
    } else {
        $stm = $_db->prepare("
        SELECT p.*, c.CategoryName 
        FROM products p
        LEFT JOIN categories c ON p.CategoryID = c.CategoryID
    ");
        $stm->execute();
    }
    $products = $stm->fetchAll(PDO::FETCH_ASSOC);


    ?>
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'added'): ?>
            <div class="alert success" id="successMsg">Your order is paid successful</div>
        <?php elseif ($_GET['msg'] === 'login'): ?>
            <div class="alert success" id="successMsg">You log in the system successfully</div>
        <?php elseif ($_GET['msg'] === 'register'): ?>
            <div class="alert success" id="successMsg"> You register the acoount successfully</div>
        <?php endif; ?>
    <?php endif; ?>
    <!-- Search -->
    <div class="search-bar">
        <form action="home.php" method="GET">
            <input type="text" name="search_keyword" placeholder="Search for products...">
            <button type="submit">Search</button>
        </form>
    </div>

 <!-- Hero Section (Slider) -->
<section class="hero-slider">
    <div class="slider-container">
        <div class="slide fade">
            <img src="image/slider1.jpg" alt="Promo 1">
        </div>
        <div class="slide fade">
            <img src="image/slider2.avif" alt="Promo 2">
        </div>
        <div class="slide fade">
            <img src="image/slider3.webp" alt="Promo 3">
        </div>
        <div class="slide fade">
            <img src="image/slider4.jpg" alt="Promo 4">
        </div>
    </div>
</section>



    <section class="category-section">
        <h2>Category</h2>
        <div class="cat-grid">
            <?php
            $stm = $_db->prepare("SELECT * FROM categories");
            $stm->execute();
            $cats = $stm->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cats as $cat):
            ?>
                <div class="cat-card">
                    <a href="product.php?category=<?= $cat['CategoryID'] ?>">
                        <img src="<?= !empty($cat['photo']) ? $cat['photo'] : 'image/logo.png' ?>" alt="cat">
                        <h4><?= htmlspecialchars($cat["CategoryName"]) ?></h4>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- Top Selling Products Section -->
    <section class="top-selling">

        <div class="product-group-border">
            <h2>Top Selling Products</h2>
            <?php
            // Get top-selling products (example: top 5 by total_sold)
            $stm = $_db->prepare("
                    SELECT p.*, c.CategoryName, 
                    IFNULL((SELECT SUM(oi.Quantity) FROM order_items oi WHERE oi.ProductID = p.ProductID), 0) as total_sold
                    FROM products p
                    LEFT JOIN categories c ON p.CategoryID = c.CategoryID
                    ORDER BY total_sold DESC
                    LIMIT 4
    
                ");

            $stm->execute();
            $topProducts = $stm->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="product-grid">
                <?php foreach ($topProducts as $product): ?>
                    <div class="product-card">
                        <a href="product_detail.php?id=<?= $product['ProductID'] ?>">
                            <img src="<?= !empty($product['Photo']) ? $product['Photo'] : 'image/logo.png' ?>" alt="Product">
                            <h4><?= htmlspecialchars($product["ProductName"]) ?></h4>
                            <p class="price">RM <?= htmlspecialchars($product["Price"]) ?></p>
                            <p class="sold"><?= $product['total_sold'] ?> sold</p>
                        </a>
                    </div>
                <?php endforeach; ?>
                <div class="see-more">
                    <a href="product.php?filter[]=top_sales">See More &rarr;</a>
                </div>
            </div>

        </div>
    </section>


</body>
<script src="js/app.js"></script>

</html>