<?php
include "../model/config.php";

if (isset($_POST["btn-save-account"])) {
    $username_creator = $_POST["username_creator"];
    $bank_account_name = $_POST["bank_account_name"];
    $bank_account_number = $_POST["bank_account_number"];
    $bank_account_holder = $_POST["bank_account_holder"];

    $account_number_check_query = "SELECT * FROM `bank_account` WHERE bank_account_number = '$bank_account_number'";
    $account_number_check_result = mysqli_query($mysqli, $account_number_check_query);
    if (mysqli_num_rows($account_number_check_result) > 0) {
        $response = "Account number $account_number already exists";
        header("Location: ../bankAccount.php?errorMessage=" . urlencode($response));
        exit;
    }

    // balance logic
    $bank_account_balance = preg_replace("/[^0-9]/", "", $_POST["bank_account_balance"]);

    // bank id logic
    $bank_account_id_prefix = strtoupper(substr($bank_account_name, 0, 1));
    $bank_account_id_date_suffix = date('dmY');
    $count_query = "SELECT COUNT(*) AS total FROM `bank_account` WHERE bank_account_created_at >= CURDATE()";
    $count_result = mysqli_query($mysqli, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $count = $count_row['total'];
    $count_formatted = sprintf("%03d", $count + 1);
    $bank_account_id = $bank_account_id_prefix . $bank_account_id_date_suffix . $count_formatted . '_BANK';

    $query = "INSERT INTO `bank_account` (bank_account_id, bank_account_name,  bank_account_number, bank_account_holder, bank_account_type, bank_account_status, bank_account_updated_by, bank_account_created_by, bank_account_updated_at, bank_account_created_at)
        VALUES ('$bank_account_id', '$bank_account_name', '$bank_account_number', '$bank_account_holder', 'saving', 'active', '', '$username_creator', '', NOW())";

    $result = mysqli_query($mysqli, $query);
    $response = "Bank account $bank_account_name successfully added";
    header("Location: ../bankAccount.php?successMessage=" . urlencode($response));
}