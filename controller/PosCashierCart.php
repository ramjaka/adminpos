<?php
include "../model/config.php";

if (isset($_POST["btn-add-cart"])) {
    $user_id = $_POST["user_id"];
    $product_sku = $_POST["product_sku"];
    $product_qty = $_POST["product_qty"];

    // get product data
    $product_data = "SELECT p.*, s.selling_price, s.purchase_price, s.COGS 
    FROM product p
    JOIN stock s ON p.product_sku = s.product_sku
    WHERE p.product_sku = '$product_sku'";

    $result = mysqli_query($mysqli, $product_data);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $product_media_1 = $row['product_media_1'];
        $product_name = $row['product_name'];
        $product_category = $row['product_category'];
        $product_description = $row['product_description'];
        $selling_price = $row['selling_price'];
        $purchase_price = $row['purchase_price'];
        $COGS = $row['COGS'];
    }

    // qty required
    if ($product_qty <= 0) {
        $response = "Quantity cannot be 0";
        header("Location: ../posCashier.php?errorMessage=" . urlencode($response));
        exit;
    }

    // accumulation amount
    $amount = $selling_price * $product_qty;
    $COGS = $COGS * $product_qty;

    // check product sku
    $existing_product_query = "SELECT * FROM sales_cart WHERE product_sku = '$product_sku'";
    $existing_product_result = mysqli_query($mysqli, $existing_product_query);

    if (mysqli_num_rows($existing_product_result) > 0) {
        $existing_product_row = mysqli_fetch_assoc($existing_product_result);
        $new_product_qty = $existing_product_row['product_qty'] + $product_qty;
        $new_amount = $new_product_qty * $selling_price;
        $new_COGS = $COGS * $product_qty;

        $update_query = "UPDATE sales_cart SET product_qty = '$new_product_qty', selling_price = '$selling_price', purchase_price = '$purchase_price', COGS = '$new_COGS', amount = '$new_amount' WHERE product_sku = '$product_sku'";

        $update_result = mysqli_query($mysqli, $update_query);
        
        $response = "Your product item $product_sku has been updated";
        header("Location: ../posCashier.php?successMessage=" . urlencode($response));
    }

    // insert to sales cart
    $query = "INSERT INTO `sales_cart` (sales_cart_id, product_sku, product_media_1, product_name, product_category, product_qty, selling_price, purchase_price, COGS, amount, description, created_at)
    VALUES ('$user_id', '$product_sku', '$product_media_1', '$product_name', '$product_category', '$product_qty', '$selling_price', '$purchase_price', '$COGS', '$amount', '$product_description', NOW())";

    $result = mysqli_query($mysqli, $query);
    $response = "Product item $product_sku successfully added to cart";
    header("Location: ../posCashier.php?successMessage=" . urlencode($response));

    var_dump($amount);
    exit;
}

// delete cart
if (isset($_POST["btn-delete-cart"])) {
    $product_sku = $_POST["product_sku"];

    $query = "DELETE FROM sales_cart WHERE product_sku = '$product_sku'";

    $result = mysqli_query($mysqli, $query);
    $response = "Product item $product_sku successfully deleted";
    header("Location: ../posCashier.php?successMessage=" . urlencode($response));
}

// promotion
if (isset($_POST["btn-add-promotion"])) {
    $user_id = $_POST["user_id"];
    $promotion_id = $_POST["promotion_id"];

    // get promotion name
    $get_promotion_name = "SELECT * FROM `promotion` WHERE promotion_id = '$promotion_id'";

    if ($promotion_id < 0) {
        $query = "UPDATE `sales_cart` SET promotion_id = '', promotion = '' WHERE sales_cart_id = '$user_id'";

        $result = mysqli_query($mysqli, $query);
        
        $response = "Promotion $promotion_name successfully addess";
        header("Location: ../posCashier.php?successMessage=" . urlencode($response));
        exit;
    }

    $result = mysqli_query($mysqli, $get_promotion_name);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $promotion_name = $row['promotion_name'];
        $promotion_value_rupiah = $row['promotion_value_rupiah'];
        $promotion_value_percentage = $row['promotion_value_percentage'];
    }

    if ($promotion_value_rupiah > 0) {
        $promotion = $promotion_value_rupiah;
    } else {
        $promotion = $promotion_value_percentage . '%';
    }

    $query = "UPDATE `sales_cart` SET promotion_id = '$promotion_id', promotion = '$promotion' WHERE sales_cart_id = '$user_id'";

    $result = mysqli_query($mysqli, $query);
    $response = "Promotion $promotion_name successfully addess";
    header("Location: ../posCashier.php?successMessage=" . urlencode($response));
}
