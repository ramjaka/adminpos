<?php
include "../model/config.php";

if (isset($_POST["btn-save-supplier"])) {
    $username_creator = $_POST["username_creator"];
    $supplier_name = $_POST["supplier_name"];
    $supplier_phone = $_POST["supplier_phone"];
    $supplier_email = $_POST["supplier_email"];
    $supplier_address = $_POST["supplier_address"];

    // user_phone logic
    $supplier_phone = preg_replace("/[^0-9]/", "", $_POST["supplier_phone"]);

    $supplier_check_query = "SELECT * FROM `supplier` WHERE supplier_name = '$supplier_name'";
    $supplier_check_result = mysqli_query($mysqli, $supplier_check_query);
    if (mysqli_num_rows($supplier_check_result) > 0) {
        $response = "Supplier name $supplier_name already exists";
        header("Location: ../wmsSupplier.php?errorMessage=" . urlencode($response));
        exit;
    }

    $query = "INSERT INTO `supplier` (supplier_name, supplier_phone, supplier_email, supplier_address, supplier_status, supplier_updated_by, supplier_created_by, supplier_updated_at, supplier_created_at)
        VALUES ('$supplier_name', '$supplier_phone', '$supplier_email', '$supplier_address', 'active', '', '$username_creator', '', NOW())";

    $result = mysqli_query($mysqli, $query);
    
    $response = "Supplier $supplier_name successfully added";
    header("Location: ../wmsSupplier.php?successMessage=" . urlencode($response));
}
