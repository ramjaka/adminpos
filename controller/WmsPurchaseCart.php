<?php
include "../model/config.php";

if (isset($_POST["btn-add-invoice"])) {
    $user_id = $_POST["user_id"];
    $product_sku = $_POST["product_sku"];
    $product_qty = $_POST["product_qty"];
    $purchase_price = $_POST["purchase_price"];
    $selling_price = $_POST["selling_price"];
    $product_description = $_POST["product_description"];
    $product_category = $_POST["product_category"];
    $product_color = $_POST["product_color"];
    $product_name = $_POST["product_name"];

    // price logic
    $purchase_price = preg_replace("/[^0-9]/", "", $_POST["purchase_price"]);
    $selling_price = preg_replace("/[^0-9]/", "", $_POST["selling_price"]);

    if ($product_qty <= 0) {
        $response = "Quantity cannot be 0";
        header("Location: ../wmsPurchase.php?errorMessage=" . urlencode($response));
        exit;
    } elseif ($purchase_price > $selling_price) {
        $response = "The selling price cannot be lower than the buying price";
        header("Location: ../wmsPurchase.php?errorMessage=" . urlencode($response));
        exit;
    }

    // accumulation amount
    $amount = $purchase_price * $product_qty;

    // check product sku
    $existing_product_query = "SELECT * FROM purchase_cart WHERE product_sku = '$product_sku'";
    $existing_product_result = mysqli_query($mysqli, $existing_product_query);

    if (mysqli_num_rows($existing_product_result) > 0) {
        $existing_product_row = mysqli_fetch_assoc($existing_product_result);
        $new_product_qty = $existing_product_row['product_qty'] + $product_qty;
        $new_amount = $new_product_qty * $purchase_price;

        $update_query = "UPDATE purchase_cart SET product_qty = '$new_product_qty', purchase_price = '$purchase_price', selling_price = '$selling_price', amount = '$new_amount' WHERE product_sku = '$product_sku'";

        $update_result = mysqli_query($mysqli, $update_query);
        
        $response = "Your product item $product_sku has been updated";
        header("Location: ../wmsPurchase.php?successMessage=" . urlencode($response));
    }

    $query = "INSERT INTO `purchase_cart` (purchase_cart_id, product_sku, product_name, product_qty, purchase_price, selling_price, amount, description, created_at)
    VALUES ('$user_id', '$product_sku', '$product_name', '$product_qty', '$purchase_price', '$selling_price', '$amount', '$product_sku - $product_color', NOW())";

    $result = mysqli_query($mysqli, $query);
    
    $response = "Product item $product_sku successfully added to invoice";
    header("Location: ../wmsPurchase.php?successMessage=" . urlencode($response));
}

// delete cart
if (isset($_POST["btn-delete-cart"])) {
    $product_sku = $_POST["product_sku"];

    $query = "DELETE FROM purchase_cart WHERE product_sku = '$product_sku'";

    $result = mysqli_query($mysqli, $query);
    
    $response = "Product item $product_sku successfully deleted";
    header("Location: ../wmsPurchase.php?successMessage=" . urlencode($response));
}