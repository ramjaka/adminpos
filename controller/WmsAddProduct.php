<?php
include "../model/config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_name = $_POST["product_name"];
    $product_sku = $_POST["product_sku"];
    $product_weight = $_POST["product_weight"];
    $product_barcode = $_POST["product_barcode"];
    $product_description = $_POST["product_description"];
    $product_category = $_POST["product_category"];
    $product_color = $_POST["product_color"];
    $username_creator = $_POST["username_creator"];

    // Product name validation
    if (empty($product_name)) {
        $errors['productNameValidation'] = "Product name is required.";
    } else {
        $name_check_query = "SELECT * FROM `product` WHERE product_name = '$product_name'";
        $name_check_result = mysqli_query($mysqli, $name_check_query);
        if (mysqli_num_rows($name_check_result) > 0) {
            $errors['productNameValidation'] = "Product name has already been taken.";
        }
    }

    // Product SKU validation
    if (empty($product_sku)) {
        $errors['productSKUValidation'] = "Product SKU is required.";
    } else {
        $sku_check_query = "SELECT * FROM `product` WHERE product_sku = '$product_sku'";
        $sku_check_result = mysqli_query($mysqli, $sku_check_query);
        if (mysqli_num_rows($sku_check_result) > 0) {
            $errors['productSKUValidation'] = "Product SKU has already been taken.";
        }
    }

    // Product weight validation
    if (empty($product_weight)) {
        $errors['productWeightValidation'] = "Product weight is required.";
    } elseif (!preg_match('/^\d+(\.\d+)?$/', $product_weight)) {
        $errors['productWeightValidation'] = "Product weight must be a number with optional decimal point.";
    }

    // Product barcode validation
    $sku_check_query = "SELECT * FROM `product` WHERE product_barcode = '$product_barcode'";
    $sku_check_result = mysqli_query($mysqli, $sku_check_query);
    if (mysqli_num_rows($sku_check_result) > 0) {
        $errors['productBarcodeValidation'] = "Product barcode has already been taken.";
    }

    // Product category validation
    if (empty($product_category)) {
        $errors['productCategoryValidation'] = "Product category is required.";
    }

    // Product color validation
    if (empty($product_color)) {
        $errors['productColorValidation'] = "Product color is required.";
    }

    $product_media_1 = "";
    $product_media_2 = "";
    $product_media_3 = "";
    $product_media_4 = "";

    for ($i = 1; $i <= 4; $i++) {
        $input_name = "product_media_$i";

        // Check if file is uploaded successfully
        if ($_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
            // Get file name
            $file_name = $_FILES[$input_name]['name'];
            // Generate unique file name based on product_sku and input number
            $file_unique_name = $product_sku . "_" . $i;
            // Define target file name with jpg extension
            $target_file_name = $file_unique_name . ".jpg";

            // Move uploaded file to target directory
            if (move_uploaded_file($_FILES[$input_name]['tmp_name'], "../assets/img/product-img/" . $target_file_name)) {
                // File uploaded successfully
                ${"product_media_$i"} = $target_file_name; // Assigning value to dynamic variable
            } else {
                // File failed to upload, handle error as needed
                $errors["productMedia{$i}Validation"] = "Failed to upload product media $i.";
            }
        } elseif ($i == 1) {
            // No file uploaded or error occurred for media 1, handle error as needed
            $errors["productMedia1Validation"] = "Product media 1 is required.";
        }
    }

    if (empty($errors)) {

        // Insert data into the user
        $query = "INSERT INTO `product` (product_sku, product_name, product_weight, product_barcode, product_color, product_category, product_description, product_media_1, product_media_2, product_media_3, product_media_4, product_status, product_updated_by, product_created_by, product_updated_at, product_created_at)
        VALUES ('$product_sku', '$product_name', '$product_weight', '$product_barcode', '$product_color', '$product_category', '$product_description', '$product_media_1', '$product_media_2', '$product_media_3', '$product_media_4', 'active', '', '$username_creator', '', NOW())";

        $result = mysqli_query($mysqli, $query);
        
        $response = "Product $product_sku successfully added";
        header("Location: ../wmsProductsList.php?successMessage=" . urlencode($response));
    } else {
        $queryString = http_build_query($errors);
        header("Location: ../wmsAddProduct.php?$queryString");
        exit;
    }
}