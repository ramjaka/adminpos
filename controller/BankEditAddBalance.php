<?php
include "../model/config.php";

// delete sales
if (isset($_POST["btn-delete-sales"])) {
    $username_creator = $_POST["username_creator"];
    $transaction_id = $_POST["transaction_id"];
    $bank_account_number = $_POST["bank_account_number"];
    $confirmation_delete = $_POST["confirmation_delete"];
    $debt = $_POST["debt"];

    if ($confirmation_delete != $transaction_id) {
        $response = "Confirmation invoice id name does not match";
        header("Location: ../addBalanceDetails.php?transaction_id=$transaction_id&errorMessage=" . urlencode($response));
    } else {
        // get bank name
        $bank_get_query = "SELECT * FROM `bank_account` WHERE bank_account_number = '$bank_account_number'";
        $result = mysqli_query($mysqli, $bank_get_query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $bank_account_name = $row['bank_account_name'];
        }

        // balance checker logic
        $last_balance_query = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '$bank_account_number' ORDER BY mutation_created_at DESC LIMIT 1";
        $last_balance_result = mysqli_query($mysqli, $last_balance_query);
        $last_balance_row = mysqli_fetch_assoc($last_balance_result);

        if ($last_balance_row) {
            $last_balance = $last_balance_row['last_balance'] - $debt;
        } else {
            $last_balance = $debt;
        }

        // transaction id mutation logic
        $transaction_id_mutation = str_replace('A', 'C', $transaction_id);

        $query_mutation = "INSERT INTO `mutation` (transaction_id, bank_account_number, bank_account_name, credit, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
        VALUES ('$transaction_id_mutation', '$bank_account_number', '$bank_account_name', '$debt', '$last_balance', 'Cancellation of transaction #$transaction_id', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query_mutation);

        var_dump($query_mutation);
        exit;

        
        $response = "Add Balance #$transaction successfully deleted";
        header("Location: ../bankMutataion.php?successMessage=" . urlencode($response));
    }
}