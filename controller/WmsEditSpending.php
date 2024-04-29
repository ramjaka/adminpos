<?php
include "../model/config.php";

// delete non maturity
if (isset($_POST["btn-delete-paid"])) {
    $username_creator = $_POST["username_creator"];
    $debt_id = $_POST["debt_id"];
    $spending_transaction_id = $_POST["spending_transaction_id"];
    $spending_name = $_POST["spending_name"];
    $spending_total = $_POST["spending_total"];
    $bank_account_number = $_POST["bank_account_number"];
    $confirmation_delete = $_POST["confirmation_delete"];

    if ($confirmation_delete != $spending_name) {
        $response = "Confirmation spending name does not match";
        header("Location: ../wmsSpending.php?errorMessage=" . urlencode($response));
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
            $last_balance = $last_balance_row['last_balance'] + $spending_total;
        } else {
            $last_balance = $spending_total;
        }

        $query_debt = "UPDATE `debt` SET status = 'canceled', debt_updated_by = '$username_creator', debt_updated_at = NOW() WHERE debt_id = '$debt_id'";
        $result = mysqli_query($mysqli, $query_debt);

        // transaction id mutation logic
        $spending_transaction_id_mutation = str_replace('E', 'C', $spending_transaction_id);

        $query_mutation = "INSERT INTO `mutation` (transaction_id, debt_id, bank_account_number, bank_account_name, debt, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
        VALUES ('$spending_transaction_id_mutation', '$debt_id', '$bank_account_number', '$bank_account_name', '$spending_total', '$last_balance', 'Cancellation of transaction #$spending_transaction_id', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query_mutation);

        $query_spending = "UPDATE `spending` SET spending_status = 'canceled', spending_updated_by = '$username_creator', spending_updated_at = NOW() WHERE spending_transaction_id = '$spending_transaction_id'";
        $result = mysqli_query($mysqli, $query_spending);

        
        $response = "Spending $spending_name successfully deleted";
        header("Location: ../wmsSpending.php?successMessage=" . urlencode($response));
    }
}

// delete maturity
if (isset($_POST["btn-delete-debt"])) {
    $username_creator = $_POST["username_creator"];
    $debt_id = $_POST["debt_id"];
    $spending_transaction_id = $_POST["spending_transaction_id"];
    $spending_name = $_POST["spending_name"];
    $spending_total = $_POST["spending_total"];
    $bank_account_number = $_POST["bank_account_number"];
    $confirmation_delete = $_POST["confirmation_delete"];

    if ($confirmation_delete != $spending_name) {
        $response = "Confirmation spending name does not match";
        header("Location: ../wmsSpending.php?errorMessage=" . urlencode($response));
    } else {
        // balance checker logic
        $last_balance_query = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '$bank_account_number' ORDER BY mutation_created_at DESC LIMIT 1";
        $last_balance_result = mysqli_query($mysqli, $last_balance_query);
        $last_balance_row = mysqli_fetch_assoc($last_balance_result);

        // accumulation last balance logic
        if ($last_balance_row) {
            $last_balance = $last_balance_row['last_balance'] - $spending_total;
        } else {
            $last_balance = $spending_total;
        }

        $query_debt = "UPDATE `debt` SET status = 'canceled', debt_updated_by = '$username_creator', debt_updated_at = NOW() WHERE debt_id = '$debt_id'";
        $result = mysqli_query($mysqli, $query_debt);

        // transaction id mutation logic
        $spending_transaction_id_mutation = str_replace('E', 'C', $spending_transaction_id);

        $query_mutation = "INSERT INTO `mutation` (transaction_id, debt_id, bank_account_number, bank_account_name, debt, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
        VALUES ('$spending_transaction_id_mutation', '$debt_id', '$bank_account_number', '$bank_account_name', '$spending_total', '$last_balance', 'Cancellation of spending #$spending_transaction_id', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query_mutation);

        $query_spending = "UPDATE `spending` SET spending_status = 'canceled', spending_updated_by = '$username_creator', spending_updated_at = NOW() WHERE spending_transaction_id = '$spending_transaction_id'";
        $result = mysqli_query($mysqli, $query_spending);

        
        $response = "Spending $spending_name successfully deleted";
        header("Location: ../wmsSpending.php?successMessage=" . urlencode($response));
    }
}
