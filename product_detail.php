<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Product Detail</title>
    <link rel="stylesheet" href="css/product_detail.css" />
</head>

<body>
<?php include "header.php"; ?>

    <?php

    $userId = $_SESSION["ID"] ?? null;



    if (isset($_GET['id'])) {
        $productId = $_GET['id'];
        $stm = $_db->prepare('SELECT * FROM products WHERE ProductID = ?');
        $stm->execute([$productId]);
        $product = $stm->fetch(PDO::FETCH_ASSOC);
    }

    if (!$product) {
        echo "<div class='not-found'>Product not found!</div>";
        exit;
    }
    ?>

    <div class="product-detail-container">
        <div class="image-section">
            <img src="<?= $product['Photo'] ?>" alt="<?= $product['ProductName'] ?>" />
        </div>

        <div class="info-section">
            <h1><?= $product['ProductName'] ?></h1>
            <form method="post" action="add_to_cart.php">
                <p class="price">Price (each): RM <span id="unitPrice"><?= number_format($product['Price'], 2) ?></span></p>
                <p class="stock">Stock: <?= $product['Stock'] ?></p>
                <p class="desc"><?= $product['Description'] ?></p>

                <input type="hidden" name="product_id" value="<?= $product['ProductID'] ?>">
                <label for="quantity">Qty:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= $product['Stock'] ?>">

                <p class="price">Total: RM <span id="totalPrice"><?= number_format($product['Price'], 2) ?></span></p>
                <?php if ($userId): ?>
                    <button type="submit" class="btn-add" <?= $product['Stock'] == 0 ? 'disabled' : '' ?>>Add to Cart</button>
                    <button type="button" class="btn-purchases" onclick="goToCart()">Purchase</button>
                <?php else: ?>
                    <button type="button" class="btn-add" onclick="window.location.href='login.php?msg=NoLogin'">Add to Cart</button>
                    <button type="button" class="btn-purchases" onclick="window.location.href='login.php?msg=NoLogin'">Purchase</button>
                <?php endif; ?>

            </form>
        </div>
    </div>

    <script>
        const unitPrice = <?= $product['Price'] ?>;
        const quantityInput = document.getElementById('quantity');
        const totalPriceEl = document.getElementById('totalPrice');

        quantityInput.addEventListener('input', () => {
            let qty = parseInt(quantityInput.value) || 1;
            if (qty < 1) qty = 1;
            if (qty > <?= $product['Stock'] ?>) qty = <?= $product['Stock'] ?>;
            const total = unitPrice * qty;
            totalPriceEl.textContent = total.toFixed(2);
        });

        function goToCart() {
            const quantity = document.getElementById('quantity').value;
            const productId = <?= json_encode($product['ProductID']) ?>;
            window.location.href = `cart.php?product_id=${productId}&quantity=${quantity}`;
        }
    </script>
    </script>
</body>

</html>