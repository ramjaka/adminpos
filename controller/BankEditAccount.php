<?php
include "../model/config.php";

if (isset($_POST["btn-edit-account"])) {
    $username_creator = $_POST["username_creator"];
    $bank_account_number_old = $_POST["bank_account_number_old"];
    $bank_account_number = $_POST["bank_account_number"];
    $bank_account_name = $_POST["bank_account_name"];
    $bank_account_balance = $_POST["bank_account_balance"];
    $bank_account_number = $_POST["bank_account_number"];
    $bank_account_holder = $_POST["bank_account_holder"];

    // balance logic
    $bank_account_balance = preg_replace("/[^0-9]/", "", $_POST["bank_account_balance"]);

    // Bank account number validation
    $new_bank_account_number = mysqli_real_escape_string($mysqli, $bank_account_number);
    $bank_account_number_check_query = "SELECT * FROM `bank_account` WHERE bank_account_number = '$new_bank_account_number' AND bank_account_number != '$bank_account_number_old'";
    $bank_account_number_check_result = mysqli_query($mysqli, $bank_account_number_check_query);
    if (mysqli_num_rows($bank_account_number_check_result) > 0) {
        $errors['accountNumberValidation'] = "Bank account number has already been taken.";
    }

    if (empty($errors)) {
        $query = "UPDATE `bank_account` SET bank_account_name = '$bank_account_name', bank_account_holder = '$bank_account_holder', bank_account_updated_by = '$username_creator', bank_account_updated_at = NOW() WHERE bank_account_number = '$bank_account_number'";

        $result = mysqli_query($mysqli, $query);
        
        $response = "bank_account $bank_account_name successfully updated";
        header("Location: ../bankAccountDetails.php?bank_account_number=$bank_account_number&successMessage=" . urlencode($response));
    } else {
        $queryString = http_build_query($errors);
        header("Location: ../bankAccountDetails.php?bank_account_number=$bank_account_number&$queryString");
        exit;
    }
}

// active
if (isset($_POST["btn-active"])) {
    $bank_account_number = $_POST["bank_account_number"];
    $username_creator = $_POST["username_creator"];

    $query = "UPDATE `bank_account` SET bank_account_status = 'active', bank_account_updated_by = '$username_creator', bank_account_updated_at = NOW() WHERE bank_account_number = '$bank_account_number'";

    $result = mysqli_query($mysqli, $query);

    $response = "Bank account $bank_account_number successfully active";
    header("Location: ../bankAccountDetails.php?bank_account_number=$bank_account_number&successMessage=" . urlencode($response));
}

// hold
if (isset($_POST["btn-hold"])) {
    $bank_account_number = $_POST["bank_account_number"];
    $username_creator = $_POST["username_creator"];

    $query = "UPDATE `bank_account` SET bank_account_status = 'hold', bank_account_updated_by = '$username_creator', bank_account_updated_at = NOW() WHERE bank_account_number = '$bank_account_number'";

    $result = mysqli_query($mysqli, $query);

    $response = "bank_account $bank_account_number successfully hold";
    header("Location: ../bankAccountDetails.php?bank_account_number=$bank_account_number&successMessage=" . urlencode($response));
}