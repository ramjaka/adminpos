<?php
include "../model/config.php";

if (isset($_POST["btn-edit-supplier"])) {
    $username_creator = $_POST["username_creator"];
    $supplier_id = $_POST["supplier_id"];
    $supplier_name = $_POST["supplier_name"];
    $supplier_name_old = $_POST["supplier_name_old"];
    $supplier_address = $_POST["supplier_address"];
    $supplier_email = $_POST["supplier_email"];
    $supplier_email = $_POST["supplier_email"];

    // user_phone logic
    $supplier_phone = preg_replace("/[^0-9]/", "", $_POST["supplier_phone"]);

    // Supplier name validation
    $new_supplier_name = mysqli_real_escape_string($mysqli, $supplier_name);
    $supplier_name_check_query = "SELECT * FROM `supplier` WHERE supplier_name = '$new_supplier_name' AND supplier_name != '$supplier_name_old'";
    $supplier_name_check_result = mysqli_query($mysqli, $supplier_name_check_query);
    if (mysqli_num_rows($supplier_name_check_result) > 0) {
        $errors['supplierNameValidation'] = "Supplier name has already been taken.";
    }

    if (empty($errors)) {

        $query = "UPDATE `supplier` SET supplier_name = '$supplier_name', supplier_address = '$supplier_address', supplier_phone = '$supplier_phone', supplier_email = '$supplier_email', supplier_updated_by = '$username_creator', supplier_updated_at = NOW() WHERE supplier_id = '$supplier_id' ";

        $result = mysqli_query($mysqli, $query);
        
        $response = "Supplier $supplier_name successfully updated";
        header("Location: ../wmsSupplierDetails.php?supplier_id=$supplier_id&successMessage=" . urlencode($response));
    } else {
        $queryString = http_build_query($errors);
        header("Location: ../wmsSupplierDetails.php?supplier_id=$supplier_id&$queryString");
        exit;
    }
}

// active
if (isset($_POST["btn-active"])) {
    $supplier_id = $_POST["supplier_id"];
    $supplier_name = $_POST["supplier_name"];
    $username_creator = $_POST["username_creator"];

    $query = "UPDATE `supplier` SET supplier_status = 'active', supplier_updated_by = '$username_creator', supplier_updated_at = NOW() WHERE supplier_id = '$supplier_id'";

    $result = mysqli_query($mysqli, $query);

    
    $response = "supplier $supplier_name successfully active";
    header("Location: ../wmsSupplierDetails.php?supplier_id=$supplier_id&successMessage=" . urlencode($response));
}

// hold
if (isset($_POST["btn-hold"])) {
    $supplier_id = $_POST["supplier_id"];
    $supplier_name = $_POST["supplier_name"];
    $username_creator = $_POST["username_creator"];

    $query = "UPDATE `supplier` SET supplier_status = 'hold', supplier_updated_by = '$username_creator', supplier_updated_at = NOW() WHERE supplier_id = '$supplier_id'";

    $result = mysqli_query($mysqli, $query);

    
    $response = "supplier $supplier_name successfully hold";
    header("Location: ../wmsSupplierDetails.php?supplier_id=$supplier_id&successMessage=" . urlencode($response));
}
