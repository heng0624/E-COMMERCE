<?php
$_db = new PDO('mysql:dbname=art_drawing', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Is unique?
function is_unique($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Is GET request?
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Redirect to URL
function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}
// Global error array
$_err = [];

// Generate <span class='err'>
function err($key)
{
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span style='color:red';>$_err[$key]</span>";
    } else {
        echo '<span></span>';
    }
}

$_genders = [
    'F' => 'Female',
    'M' => 'Male',
];

// retrieve data from database
function getData($value = null, $table, $field = null)
{
    global $_db;
    if ($value != null && $field != null) {
        $stm = $_db->prepare("SELECT * FROM $table WHERE $field = ?");
        $stm->execute([$value]);
    } else {
        $stm = $_db->prepare("SELECT * FROM $table");
        $stm->execute();
    }

    return $stm->fetchAll(PDO::FETCH_ASSOC); // Fix: Fetch all rows instead of one
}


function generateID($table, $field, $role = null) {
    global $_db;
    
    // Define prefixes for valid tables and roles
    $prefixes = [
        "users" => [
            "customer" => "CUST", // For customers
            "staff" => "STFF" ,    // For staff
        ],
        "products" => "PRO",
        "categories" => "CAT",
        "orders" => "ORD",
        "order_items"=>"ORD_ITEM",
        "carts"=> "CART",
        "cart_items"=>"CART_ITEM",
        "address_book"=>"ADDR",
        "payments"=>"PAY",
        "payment_methods"=>"PAY_METHOD"
    ];

    // Validate table name
    if (!isset($prefixes[$table])) {
        return false; // Invalid table
    }

    // If the table is 'user' and no role is provided, set a default role or allow it to be NULL
    if ($table == "users" && $role === "customer") {
        $role = 'customer';  // Default to 'customer' if no role is provided
    }else if($table == "users" && $role === "staff"){
        $role = 'staff';  
    }

    // If table is 'user', we need to check for a valid role prefix
    if ($table == "users" && !isset($prefixes["users"][$role])) {
        return false; // Invalid role for user table
    }

    // Set the correct prefix based on role
    $prefix = ($table == "users") ? $prefixes["users"][$role] : $prefixes[$table];

    // Get the latest ID from the table
    $query = "SELECT $field FROM `$table` WHERE $field LIKE ? ORDER BY $field DESC LIMIT 1";
    $stm = $_db->prepare($query);
    $likePattern = $prefix . "_%"; // Use the prefix with a wildcard
    $stm->execute([$likePattern]);

    $row = $stm->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Extract the number from the last ID and increment it
        preg_match('/(\d+)$/', $row[$field], $matches);
        $newNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
    } else {
        // If no record found, start from 1
        $newNumber = 1;
    }

    // Generate and return the new ID
     return $prefix . "_" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

}

// Obtain uploaded file --> cast to object
function get_file($key)
{
    $f = $_FILES[$key] ?? null;

    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder, $width = 500, $height = 500)
{
    $photo = uniqid() . '.jpg';

    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f['tmp_name']) // ✅ fix here
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return "$folder/$photo";
}


function auth($allowed_roles, $id) {
    global $_db;

    if (!$id) {
        redirect("login.php");
        exit();
    }

    $stm = $_db->prepare('SELECT * FROM users WHERE userId = ?');
    $stm->execute([$id]);
    $user = $stm->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        redirect("access_denied.php");
        exit();
    }

    if (!in_array($user['role'], $allowed_roles)) {
        redirect("access_denied.php");
        exit();
    }
}



// Is money?
function is_money($value)
{
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}
