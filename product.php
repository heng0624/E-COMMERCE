<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Listing</title>
  <link rel="stylesheet" href="css/product.css">
</head>

<body>
  <?php include "header.php"; ?>

  <?php
  // --- Read filters
  $filters        = $_GET['filter'] ?? [];
  $categories     = $_GET['categories'] ?? [];
  $price_order    = $_GET['price_order'] ?? '';
  $categoryId     = $_GET['category'] ?? '';
  $searchKeyword  = $_GET['search_keyword'] ?? '';
  $page           = max(1, intval($_GET['page'] ?? 1));
  $limit          = 10;
  $offset         = ($page - 1) * $limit;

  // Fetch categories
  $stm = $_db->prepare('SELECT * FROM categories');
  $stm->execute();
  $categoryData = $stm->fetchAll(PDO::FETCH_ASSOC);

  // Build WHERE
  $where   = ' WHERE 1=1';
  $params  = [];
  $useTopSales = in_array('top_sales', $filters);

  // Base query
  $baseQuery = "FROM products p
LEFT JOIN (
  SELECT ProductID, SUM(quantity) AS total_sold
  FROM order_items
  GROUP BY ProductID
) s ON p.ProductID = s.ProductID";


  // Filters
  if (in_array('latest', $filters)) {
    $where .= " AND p.created_at > NOW() - INTERVAL 30 DAY";
  }

  if (!empty($categories)) {
    $categoryPlaceholders = [];
    foreach ($categories as $index => $cat) {
      $key = ":category_$index";
      $categoryPlaceholders[] = $key;
      $params[$key] = $cat;
    }
    $where .= " AND p.CategoryID IN (" . implode(',', $categoryPlaceholders) . ")";
  }

  if ($categoryId !== '') {
    $params[":single_category"] = $categoryId;
    $where .= " AND p.CategoryID = :single_category";
  }

  if ($searchKeyword !== '') {
    $params[":kw1"] = "%$searchKeyword%";
    $params[":kw2"] = "%$searchKeyword%";
    $params[":kw3"] = "%$searchKeyword%";
    $where .= " AND (
      p.ProductName LIKE :kw1 OR
      p.Price       LIKE :kw2 OR
      p.Description LIKE :kw3
  )";
  }
  $min_price = $_GET['min_price'] ?? '';
  $max_price = $_GET['max_price'] ?? '';

  if ($min_price !== '') {
    $where .= " AND p.Price >= :min_price";
    $params[":min_price"] = $min_price;
  }

  if ($max_price !== '') {
    $where .= " AND p.Price <= :max_price";
    $params[":max_price"] = $max_price;
  }


  // Count
  $countSql = "SELECT COUNT(*) " . $baseQuery . $where;
  $countStmt = $_db->prepare($countSql);
  $countStmt->execute($params);
  $totalRows = $countStmt->fetchColumn();
  $totalPages = ceil($totalRows / $limit);

  // Main SELECT
  $selectFields = "SELECT p.*, COALESCE(s.total_sold, 0) AS total_sold ";

  $sql = $selectFields . $baseQuery . $where;

  // Sorting
  if ($useTopSales) {
    $sql .= " ORDER BY total_sold DESC";
  } elseif ($price_order === 'low_to_high') {
    $sql .= " ORDER BY p.Price ASC";
  } elseif ($price_order === 'high_to_low') {
    $sql .= " ORDER BY p.Price DESC";
  }

  $sql .= " LIMIT :limit OFFSET :offset";
  $params[":limit"]  = $limit;
  $params[":offset"] = $offset;

  $stmt = $_db->prepare($sql);
  foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
  }
  $stmt->execute();
  $productData = $stmt->fetchAll(PDO::FETCH_ASSOC);

  function buildQuery(array $overrides = [])
  {
    return http_build_query(array_merge($_GET, $overrides));
  }
  ?>

  <!-- Search Form -->
  <form class="search-bar" action="product.php" method="GET">
    <input type="text" class="search-keyword" name="search_keyword" placeholder="Search by product"
      value="<?= htmlspecialchars($searchKeyword) ?>">
    <button type="submit">Search</button>
  </form>

  <form method="GET" action="" id="filterForm">
    <div class="container">
      <aside class="sidebar">
        <h3>Categories</h3>
        <?php foreach ($categoryData as $cat): ?>
          <div class="filter-group">
            <label>
              <input type="checkbox" name="categories[]" value="<?= $cat['CategoryID'] ?>"
                <?= in_array($cat['CategoryID'], $categories) ? 'checked' : '' ?>>
              <?= $cat["CategoryName"] ?>
            </label>
          </div>
        <?php endforeach; ?>

        <div class="filter-group">
          <label>Min Price: <input type="number" name="min_price" step="0.01" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>"></label>
          <label>Max Price: <input type="number" name="max_price" step="0.01" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"></label>
        </div>

      </aside>

      <div class="main-section">
        <div class="price-filter-top">
          <div class="filter-group">
            <h4>Filter:</h4>
            <select name="price_order">
              <option value="">Price</option>
              <option value="low_to_high" <?= $price_order == 'low_to_high' ? 'selected' : '' ?>>Low to High</option>
              <option value="high_to_low" <?= $price_order == 'high_to_low' ? 'selected' : '' ?>>High to Low</option>
            </select>
            <button type="submit" name="filter[]" value="top_sales" <?= in_array('top_sales', $filters) ? 'class="active"' : '' ?>>Top Sales</button>
            <button type="submit" name="filter[]" value="latest" <?= in_array('latest', $filters) ? 'class="active"' : '' ?>>Latest</button>
          </div>
        </div>

        <!-- Product Grid -->
        <main class="product-grid">
          <?php foreach ($productData as $product): ?>
            <div class="product-card">
              <a href="product_detail.php?id=<?= $product['ProductID'] ?>">
                <img src="<?= !empty($product['Photo']) ? $product['Photo'] : 'image/logo.png' ?>" alt="Product">
                <h4><?= htmlspecialchars($product["ProductName"]) ?></h4>
                <p class="price">RM<?= htmlspecialchars($product["Price"]) ?></p>
                <p class="sold">sold: <?= $product['total_sold'] ?? 0 ?></p>
              </a>
            </div>
          <?php endforeach; ?>
        </main>

        <!-- Pagination -->
        <nav class="pagination">
          <?php if ($page > 1): ?>
            <a href="?<?= buildQuery(['page' => $page - 1]) ?>">&laquo; Prev</a>
          <?php endif; ?>
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= buildQuery(['page' => $i]) ?>" class="<?= $i == $page ? 'active-page' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if ($page < $totalPages): ?>
            <a href="?<?= buildQuery(['page' => $page + 1]) ?>">Next &raquo;</a>
          <?php endif; ?>
        </nav>
      </div>
    </div>
  </form>
  <script src="js/app.js"></script>
</body>

</html>