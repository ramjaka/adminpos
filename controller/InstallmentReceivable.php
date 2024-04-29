<?php
include "../model/config.php";

// add installment
if (isset($_POST["btn-add-installment"])) {
    $username_creator = $_POST["username_creator"];
    $receivable_id = $_POST["receivable_id"];
    $bank_account_number = $_POST["bank_account_number"];
    $installment_amount = $_POST["installment_amount"];
    $confirmation_delete = $_POST["confirmation_delete"];
    $sales_total = $_POST["sales_total"];

    if ($confirmation_delete != $receivable_id) {
        $response = "Confirmation transaction id name does not match";
        header("Location: ../installmentReceivable.php?receivable_id=$receivable_id&errorMessage=" . urlencode($response));
    } else {

        // installment amount required
        if ($installment_amount < 0) {
            $response = "Required to fill in the installment amount";
            header("Location: ../installmentReceivable.php?receivable_id=$receivable_id&errorMessage=" . urlencode($response));
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

        $last_balance_query_receivable = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '123receivables' ORDER BY mutation_created_at DESC LIMIT 1";
        $last_balance_result_receivable = mysqli_query($mysqli, $last_balance_query_receivable);
        $last_balance_row_receivable = mysqli_fetch_assoc($last_balance_result_receivable);


        // balance logic
        if ($last_balance_row["last_balance"] < $installment_amount) {
            $response = "Bank $bank_account_name balance is insufficient";
            header("Location: ../installmentReceivable.php?receivable_id=$receivable_id&errorMessage=" . urlencode($response));
            exit;
        }

        // last balance logic
        if ($last_balance_row) {
            $last_balance = $last_balance_row['last_balance'] + $installment_amount;
        } else {
            $last_balance = $installment_amount;
        }

        if ($last_balance_row_receivable) {
            $last_balance_receivable = $last_balance_row_receivable['last_balance'] - $installment_amount;
        } else {
            $last_balance_receivable = $installment_amount;
        }

        // receivable total checker
        $receivable_total_check = "SELECT receivable_total FROM `receivable` WHERE receivable_id = '$receivable_id'";
        $receivable_total_result = mysqli_query($mysqli, $receivable_total_check);
        $receivable_total_row = mysqli_fetch_row($receivable_total_result);
        $receivable_total = $receivable_total_row[0];

        $query_total_installment = "SELECT SUM(installment_amount) AS total_installment FROM receivable_installment WHERE receivable_id = '$receivable_id'";
        $result_total_installment = $mysqli->query($query_total_installment);
        if ($result_total_installment) {
            $row = $result_total_installment->fetch_assoc();
            $total_installment = $row['total_installment'];
        }

        $accumulation_total_receivable = strval($sales_total - $total_installment);

        // receivable installment checker
        if ($receivable_total < $installment_amount || $accumulation_total_receivable < $installment_amount) {
            $response = "Installment payments should not be more than the amount of the receivable";
            header("Location: ../installmentReceivable.php?receivable_id=$receivable_id&errorMessage=" . urlencode($response));
            exit;
        }

        if ($receivable_total === $installment_amount || $accumulation_total_receivable === $installment_amount) {
            $query_receivable = "UPDATE `receivable` SET receivable_status = '2', receivable_updated_by = '$username_creator', fulfillment_date = NOW(), receivable_updated_at = NOW()  WHERE receivable_id = '$receivable_id'";
            $result = mysqli_query($mysqli, $query_receivable);
        }

        $query = "INSERT INTO `receivable_installment` (receivable_id, bank_account_number, bank_account_name, installment_amount, receivable_installment_updated_by, receivable_installment_created_by, receivable_installment_updated_at, receivable_installment_created_at)
        VALUES ('$receivable_id', '$bank_account_number', '$bank_account_name', '$installment_amount', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query);

        // transaction id logic
        $transaction_id_date_suffix = date('dmY');
        $count_query = "SELECT COUNT(*) AS total FROM `mutation` WHERE mutation_created_at >= CURDATE()";
        $count_result = mysqli_query($mysqli, $count_query);
        $count_row = mysqli_fetch_assoc($count_result);
        $count = $count_row['total'];
        $count_formatted = sprintf("%03d", $count + 1);
        $transaction_id_credit = 'I' . $transaction_id_date_suffix . $count_formatted;

        $query_mutation = "INSERT INTO `mutation` (transaction_id, bank_account_number, bank_account_name, debt, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
        VALUES ('$transaction_id_credit', '$bank_account_number', '$bank_account_name', '$installment_amount', '$last_balance', 'Payable with invoice #$receivable_id', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query_mutation);

        // transaction id logic
        $transaction_id_date_suffix = date('dmY');
        $count_query = "SELECT COUNT(*) AS total FROM `mutation` WHERE mutation_created_at >= CURDATE()";
        $count_result = mysqli_query($mysqli, $count_query);
        $count_row = mysqli_fetch_assoc($count_result);
        $count = $count_row['total'];
        $count_formatted = sprintf("%03d", $count + 1);
        $transaction_id_receivable = 'I' . $transaction_id_date_suffix . $count_formatted;

        $query_mutation = "INSERT INTO `mutation` (transaction_id, bank_account_number, bank_account_name, credit, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
        VALUES ('$transaction_id_receivable', '123receivables', 'RECEIVABLE', '$installment_amount', '$last_balance_receivable', 'Payable with invoice #$receivable_id', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query_mutation);

        
        $response = "Installment payment $receivable_id added successfully";
        header("Location: ../installmentReceivable.php?receivable_id=$receivable_id&successMessage=" . urlencode($response));
    }
}
