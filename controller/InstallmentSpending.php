<?php
include "../model/config.php";

// add installment
if (isset($_POST["btn-add-installment"])) {
    $username_creator = $_POST["username_creator"];
    $debt_id = $_POST["debt_id"];
    $bank_account_number = $_POST["bank_account_number"];
    $installment_amount = $_POST["installment_amount"];
    $confirmation_delete = $_POST["confirmation_delete"];
    $spending_total = $_POST["spending_total"];

    if ($confirmation_delete != $debt_id) {
        $response = "Confirmation transaction id name does not match";
        header("Location: ../installmentSpending.php?debt_id=$debt_id&errorMessage=" . urlencode($response));
    } else {

        // installment amount required
        if ($installment_amount < 0) {
            $response = "Required to fill in the installment amount";
            header("Location: ../installmentSpending.php?debt_id=$debt_id&errorMessage=" . urlencode($response));
            exit;
        }

        // get bank name
        $bank_get_query = "SELECT * FROM `bank_account` WHERE bank_account_number = '$bank_account_number'";
        $result = mysqli_query($mysqli, $bank_get_query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $bank_account_name = $row['bank_account_name'];
        }

        // price logic
        $installment_amount = preg_replace("/[^0-9]/", "", $_POST["installment_amount"]);

        // balance checker logic
        $last_balance_query = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '$bank_account_number' ORDER BY mutation_created_at DESC LIMIT 1";
        $last_balance_result = mysqli_query($mysqli, $last_balance_query);
        $last_balance_row = mysqli_fetch_assoc($last_balance_result);

        $last_balance_query_debt = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '123debt' ORDER BY mutation_created_at DESC LIMIT 1";
        $last_balance_result_debt = mysqli_query($mysqli, $last_balance_query_debt);
        $last_balance_row_debt = mysqli_fetch_assoc($last_balance_result_debt);


        // balance logic
        if ($last_balance_row["last_balance"] < $installment_amount) {
            $response = "Bank $bank_account_name balance is insufficient";
            header("Location: ../installmentSpending.php?debt_id=$debt_id&errorMessage=" . urlencode($response));
            exit;
        }

        // last balance logic
        if ($last_balance_row) {
            $last_balance = $last_balance_row['last_balance'] - $installment_amount;
        } else {
            $last_balance = $installment_amount;
        }

        if ($last_balance_row_debt) {
            $last_balance_debt = $last_balance_row_debt['last_balance'] - $installment_amount;
        } else {
            $last_balance_debt = $installment_amount;
        }

        // debt total checker
        $debt_total_check = "SELECT debt_total FROM `debt` WHERE debt_id = '$debt_id'";
        $debt_total_result = mysqli_query($mysqli, $debt_total_check);
        $debt_total_row = mysqli_fetch_row($debt_total_result);
        $debt_total = $debt_total_row[0];

        $query_total_installment = "SELECT SUM(installment_amount) AS total_installment FROM debt_installment WHERE debt_id = '$debt_id'";
        $result_total_installment = $mysqli->query($query_total_installment);
        if ($result_total_installment) {
            $row = $result_total_installment->fetch_assoc();
            $total_installment = $row['total_installment'];
        }

        $accumulation_total_debt = strval($spending_total - $total_installment);

        // debt installment checker
        if ($debt_total < $installment_amount || $accumulation_total_debt < $installment_amount) {
            $response = "Installment payments should not be more than the amount of the debt";
            header("Location: ../installmentSpending.php?debt_id=$debt_id&errorMessage=" . urlencode($response));
            exit;
        }

        if ($debt_total === $installment_amount || $accumulation_total_debt === $installment_amount) {
            $query_debt = "UPDATE `debt` SET debt_status = '2', debt_updated_by = '$username_creator', fulfillment_date = NOW(), debt_updated_at = NOW()  WHERE debt_id = '$debt_id'";
            $result = mysqli_query($mysqli, $query_debt);
        }

        $query = "INSERT INTO `debt_installment` (debt_id, bank_account_number, bank_account_name, installment_amount, debt_installment_updated_by, debt_installment_created_by, debt_installment_updated_at, debt_installment_created_at)
        VALUES ('$debt_id', '$bank_account_number', '$bank_account_name', '$installment_amount', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query);

        // transaction id logic
        $transaction_id_date_suffix = date('dmY');
        $count_query = "SELECT COUNT(*) AS total FROM `mutation` WHERE mutation_created_at >= CURDATE()";
        $count_result = mysqli_query($mysqli, $count_query);
        $count_row = mysqli_fetch_assoc($count_result);
        $count = $count_row['total'];
        $count_formatted = sprintf("%03d", $count + 1);
        $transaction_id_credit = 'I' . $transaction_id_date_suffix . $count_formatted;

        $query_mutation = "INSERT INTO `mutation` (transaction_id, bank_account_number, bank_account_name, credit, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
        VALUES ('$transaction_id_credit', '$bank_account_number', '$bank_account_name', '$installment_amount', '$last_balance', 'Payable with invoice #$debt_id', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query_mutation);

        // transaction id logic
        $transaction_id_date_suffix = date('dmY');
        $count_query = "SELECT COUNT(*) AS total FROM `mutation` WHERE mutation_created_at >= CURDATE()";
        $count_result = mysqli_query($mysqli, $count_query);
        $count_row = mysqli_fetch_assoc($count_result);
        $count = $count_row['total'];
        $count_formatted = sprintf("%03d", $count + 1);
        $transaction_id_debt = 'I' . $transaction_id_date_suffix . $count_formatted;

        $query_mutation = "INSERT INTO `mutation` (transaction_id, bank_account_number, bank_account_name, debt, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
        VALUES ('$transaction_id_debt', '123debt', 'DEBT', '$installment_amount', '$last_balance_debt', 'Payable with invoice #$debt_id', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query_mutation);

        
        $response = "Installment payment $debt_id added successfully";
        header("Location: ../installmentSpending.php?debt_id=$debt_id&successMessage=" . urlencode($response));
    }
}
