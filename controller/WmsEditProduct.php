<?php
include "../model/config.php";

if (isset($_POST["btn-save-product"])) {
    $product_sku_old = $_POST["product_sku_old"];
    $product_name_old = $_POST["product_name_old"];
    $product_barcode_old = $_POST["product_barcode_old"];
    $product_media_1_old = $_POST["product_media_1_old"];
    $product_media_2_old = $_POST["product_media_2_old"];
    $product_media_3_old = $_POST["product_media_3_old"];
    $product_media_4_old = $_POST["product_media_4_old"];
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
        $new_product_name = mysqli_real_escape_string($mysqli, $product_name);
        $product_name_check_query = "SELECT * FROM `product` WHERE product_name = '$new_product_name' AND product_name != '$product_name_old'";
        $product_name_check_result = mysqli_query($mysqli, $product_name_check_query);
        if (mysqli_num_rows($product_name_check_result) > 0) {
            $errors['productNameValidation'] = "Product name has already been taken.";
        }
    }

    // Product weight validation
    if (empty($product_weight)) {
        $errors['productWeightValidation'] = "Product weight is required.";
    } elseif (!preg_match('/^\d+(\.\d+)?$/', $product_weight)) {
        $errors['productWeightValidation'] = "Product weight must be a number with optional decimal point.";
    }

    // Product barcode validation
    $new_barcode = mysqli_real_escape_string($mysqli, $product_barcode);
    $barcode_check_query = "SELECT * FROM `product` WHERE product_barcode = '$new_barcode' AND product_barcode != '$product_barcode_old'";
    $barcode_check_result = mysqli_query($mysqli, $barcode_check_query);
    if (mysqli_num_rows($barcode_check_result) > 0) {
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

    $product_media_1_old = $_POST["product_media_1_old"];
    $product_media_2_old = $_POST["product_media_2_old"];
    $product_media_3_old = $_POST["product_media_3_old"];
    $product_media_4_old = $_POST["product_media_4_old"];

    for ($i = 1; $i <= 4; $i++) {
        $input_name = "product_media_$i";

        if ($_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
            // Get file name
            $file_name = $_FILES[$input_name]['name'];
            $file_unique_name = $product_sku . "_" . $i;
            $target_file_name = $file_unique_name . ".jpg";
            $target_file_path = "../assets/img/product-img/" . $target_file_name;

            if (!file_exists($target_file_path)) {
                if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_file_path)) {
                    ${"product_media_$i"} = $target_file_name;
                } else {
                    $errors["productMedia{$i}Validation"] = "Failed to upload product media $i.";
                }
            } else {
                ${"product_media_$i"} = $target_file_name;
            }
        } elseif (!empty($_POST["product_media_{$i}_old"])) {
            ${"product_media_$i"} = $_POST["product_media_{$i}_old"];
        } elseif ($i == 1) {
            $errors["productMedia1Validation"] = "Product media 1 is required.";
        }
    }

    if (empty($errors)) {

        // Insert data into the user
        $query = "UPDATE `product` SET product_name = '$product_name', product_weight = '$product_weight', product_barcode = '$product_barcode', product_color = '$product_color', product_category = '$product_category', product_description = '$product_description', product_media_1 = '$product_media_1', product_media_2 = '$product_media_2', product_media_3 = '$product_media_3', product_media_4 = '$product_media_4', product_updated_by = '$username_creator', product_updated_at = NOW() WHERE product_sku = '$product_sku'";

        // var_dump($query);
        // exit;

        $result = mysqli_query($mysqli, $query);
        
        $response = "Product $product_sku successfully updated";
        header("Location: ../wmsEditProduct.php?product_sku=$product_sku_old&successMessage=" . urlencode($response));
    } else {
        $queryString = http_build_query($errors);
        header("Location: ../wmsEditProduct.php?product_sku=$product_sku_old&$queryString");
        exit;
    }
}

// delete media 1
if (isset($_POST["btn-delete-media1"])) {
    $product_sku_old = $_POST["product_sku"];
    $product_sku = $_POST["product_sku"];
    $username_creator = $_POST["username_creator"];
    $product_media_1 = $_POST["product_media_1"];
    $file_path = "../assets/img/product-img/" . $product_media_1;

    if (file_exists($file_path)) {
        unlink($file_path);
    }

    $query = "UPDATE `product` SET product_media_1 = '', product_updated_by = '$username_creator', product_updated_at = NOW() WHERE product_sku = '$product_sku'";

    $result = mysqli_query($mysqli, $query);

    
    $response = "Product media 1 successfully deleted";
    header("Location: ../wmsEditProduct.php?product_sku=$product_sku_old&successMessage=" . urlencode($response));
}

// delete media 2
if (isset($_POST["btn-delete-media2"])) {
    $product_sku_old = $_POST["product_sku"];
    $product_sku = $_POST["product_sku"];
    $username_creator = $_POST["username_creator"];
    $product_media_2 = $_POST["product_media_2"];
    $file_path = "../assets/img/product-img/" . $product_media_2;

    if (file_exists($file_path)) {
        unlink($file_path);
    }

    $query = "UPDATE `product` SET product_media_2 = '', product_updated_by = '$username_creator', product_updated_at = NOW() WHERE product_sku = '$product_sku'";

    $result = mysqli_query($mysqli, $query);

    
    $response = "Product media 1 successfully deleted";
    header("Location: ../wmsEditProduct.php?product_sku=$product_sku_old&successMessage=" . urlencode($response));
}

// delete media 3
if (isset($_POST["btn-delete-media3"])) {
    $product_sku_old = $_POST["product_sku"];
    $product_sku = $_POST["product_sku"];
    $username_creator = $_POST["username_creator"];
    $product_media_3 = $_POST["product_media_3"];
    $file_path = "../assets/img/product-img/" . $product_media_3;

    if (file_exists($file_path)) {
        unlink($file_path);
    }

    $query = "UPDATE `product` SET product_media_3 = '', product_updated_by = '$username_creator', product_updated_at = NOW() WHERE product_sku = '$product_sku'";

    $result = mysqli_query($mysqli, $query);

    
    $response = "Product media 1 successfully deleted";
    header("Location: ../wmsEditProduct.php?product_sku=$product_sku_old&successMessage=" . urlencode($response));
}

// delete media 4
if (isset($_POST["btn-delete-media4"])) {
    $product_sku_old = $_POST["product_sku_old"];
    $product_sku = $_POST["product_sku"];
    $username_creator = $_POST["username_creator"];
    $product_media_4 = $_POST["product_media_4"];
    $file_path = "../assets/img/product-img/" . $product_media_4;

    if (file_exists($file_path)) {
        unlink($file_path);
    }

    $query = "UPDATE `product` SET product_media_4 = '', product_updated_by = '$username_creator', product_updated_at = NOW() WHERE product_sku = '$product_sku'";

    $result = mysqli_query($mysqli, $query);

    
    $response = "Product media 1 successfully deleted";
    header("Location: ../wmsEditProduct.php?product_sku=$product_sku_old&successMessage=" . urlencode($response));
}

// active
if (isset($_POST["btn-active"])) {
    $product_sku = $_POST["product_sku"];
    $username_creator = $_POST["username_creator"];

    $query = "UPDATE `product` SET product_status = 'active', product_updated_by = '$username_creator', product_updated_at = NOW() WHERE product_sku = '$product_sku'";

    $result = mysqli_query($mysqli, $query);

    
    $response = "Product $product_sku successfully active";
    header("Location: ../wmsEditProduct.php?product_sku=$product_sku&successMessage=" . urlencode($response));
}

// hold
if (isset($_POST["btn-hold"])) {
    $product_sku = $_POST["product_sku"];
    $username_creator = $_POST["username_creator"];

    $query = "UPDATE `product` SET product_status = 'hold', product_updated_by = '$username_creator', product_updated_at = NOW() WHERE product_sku = '$product_sku'";

    $result = mysqli_query($mysqli, $query);

    
    $response = "Product $product_sku successfully hold";
    header("Location: ../wmsEditProduct.php?product_sku=$product_sku&successMessage=" . urlencode($response));
}