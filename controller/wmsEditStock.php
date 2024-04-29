<?php
include "../model/config.php";

// input to stock table
if (isset($_POST["btn-save-cogs"])) {
    $username_creator = $_POST["username_creator"];
    $product_sku = $_POST["product_sku"];
    $purchase_price = $_POST["purchase_price"];
    $selling_price = $_POST["selling_price"];

    // price logic
    $purchase_price = preg_replace("/[^0-9]/", "", $_POST["purchase_price"]);
    $selling_price = preg_replace("/[^0-9]/", "", $_POST["selling_price"]);

    if ($purchase_price > $selling_price) {
        $response = "The selling price cannot be lower than the buying price";
        header("Location: ../stockDetails.php?product_sku=$product_sku&errorMessage=" . urlencode($response));
        exit;
    }

    $query = "UPDATE `stock` SET purchase_price = '$purchase_price', selling_price = '$selling_price', COGS = '$selling_price' - '$purchase_price', stock_updated_by = '$username_creator', stock_updated_at = NOW() WHERE product_sku = '$product_sku'";

    $result = mysqli_query($mysqli, $query);
    
    $response = "Stock COGS successfully updated";
    header("Location: ../stockDetails.php?product_sku=$product_sku&successMessage=" . urlencode($response));
}