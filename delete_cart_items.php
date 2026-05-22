<?php
include "base.php";
session_start();

$userID = $_SESSION['ID'] ?? null;

$delete=req('delete_selected');
if ( is_post() && isset($delete)) {
    $deleteIds = $_POST['deleteIds'] ?? [];
     echo(1);
    if (!empty($deleteIds)) {
        // Get the user's cart ID
        $cartStmt = $_db->prepare("SELECT cartId FROM carts WHERE userID = ?");
        $cartStmt->execute([$userID]);
        $cart = $cartStmt->fetch(PDO::FETCH_ASSOC);

        if ($cart) {
            $cartID = $cart['cartId'];

            // Build placeholders for secure query
            $placeholders = [];
            $params = [];
            foreach ($deleteIds as $i => $id) {
                $placeholders[] = ":id$i";
                $params[":id$i"] = $id;
            }

            $inClause = implode(',', $placeholders);
            $sql = "DELETE FROM cart_items WHERE cartID = :cartID AND productID IN ($inClause)";
            $params[":cartID"] = $cartID;

            $stmt = $_db->prepare($sql);
            $stmt->execute($params);
        }
    }
    redirect("cart.php?msg=deleted");
    exit;
}
