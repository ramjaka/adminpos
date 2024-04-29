<?php
include "../model/config.php";

if (isset($_POST["btn-save-account"])) {
    $username_creator = $_POST["username_creator"];
    $bank_account_number = $_POST["bank_account_number"];
    $bank_account_balance = $_POST["bank_account_balance"];

    // get bank name
    $bank_get_query = "SELECT bank_account_name FROM `bank_account` WHERE bank_account_number = '$bank_account_number'";
    $result = mysqli_query($mysqli, $bank_get_query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $bank_account_name = $row['bank_account_name'];
    }

    // balance logic
    $bank_account_balance = preg_replace("/[^0-9]/", "", $_POST["bank_account_balance"]);

    // transaction id logic
    $transaction_id_date_suffix = date('dmY');
    $count_query = "SELECT COUNT(*) AS total FROM `mutation` WHERE mutation_created_at >= CURDATE()";
    $count_result = mysqli_query($mysqli, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $count = $count_row['total'];
    $count_formatted = sprintf("%03d", $count + 1);
    $transaction_id = 'A' . $transaction_id_date_suffix . $count_formatted;

    // accumulation mutation logic
    $last_balance_query = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '$bank_account_number' ORDER BY mutation_created_at DESC LIMIT 1";
    $last_balance_result = mysqli_query($mysqli, $last_balance_query);
    $last_balance_row = mysqli_fetch_assoc($last_balance_result);

    if ($last_balance_row) {
        $last_balance = $last_balance_row['last_balance'] + $bank_account_balance;
    } else {
        $last_balance = $bank_account_balance;
    }

    $query = "INSERT INTO `mutation` (transaction_id, bank_account_number, bank_account_name, debt, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
        VALUES ('$transaction_id', '$bank_account_number', '$bank_account_name', '$bank_account_balance', '$last_balance', 'Add balance to $bank_account_name', '', '$username_creator', '', NOW())";

    $result = mysqli_query($mysqli, $query);
    $response = "Bank account $bank_account_name balance has been added";
    header("Location: ../bankAccount.php?successMessage=" . urlencode($response));
}