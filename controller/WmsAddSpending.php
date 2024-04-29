<?php
include "../model/config.php";

if (isset($_POST["btn-save-spending"])) {
    $username_creator = $_POST["username_creator"];
    $bank_account_number = $_POST["bank_account_number"];
    $spending_name = $_POST["spending_name"];
    $maturity = $_POST["maturity"];
    $spending_date = $_POST["spending_date"];
    $spending_description = $_POST["spending_description"];
    $spending_total = $_POST["spending_total"];

    $current_time = date("H:i:s");
    $spending_date = $spending_date . ' ' . $current_time;
    $maturity = $maturity . ' ' . $current_time;

    // Spending total logic
    $spending_total = preg_replace("/[^0-9]/", "", $_POST["spending_total"]);

    // get bank name
    $bank_get_query = "SELECT * FROM `bank_account` WHERE bank_account_number = '$bank_account_number'";
    $result = mysqli_query($mysqli, $bank_get_query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $bank_account_name = $row['bank_account_name'];
    }

    // transaction id logic
    $transaction_id_date_suffix = date('dmY');
    $count_query = "SELECT COUNT(*) AS total FROM `spending` WHERE spending_created_at >= CURDATE()";
    $count_result = mysqli_query($mysqli, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $count = $count_row['total'];
    $count_formatted = sprintf("%03d", $count + 1);
    $spending_transaction_id = 'E' . $transaction_id_date_suffix . $count_formatted;

    // balance checker logic
    $last_balance_query = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '$bank_account_number' ORDER BY mutation_created_at DESC LIMIT 1";
    $last_balance_result = mysqli_query($mysqli, $last_balance_query);
    $last_balance_row = mysqli_fetch_assoc($last_balance_result);

    // balance logic
    if ($last_balance_row["last_balance"] < $spending_total & $bank_account_number != '123debt') {
        $response = "Bank $bank_account_name balance is insufficient";
        header("Location: ../wmsSpending.php?errorMessage=" . urlencode($response));
        exit;
    }

    // accumulation last balance logic
    if ($last_balance_row) {
        $last_balance = $last_balance_row['last_balance'] - $spending_total;
    } else {
        $last_balance = $spending_total;
    }

    // ------ Input debt ------ //

    // input debt
    if ($bank_account_number === '123debt') {

        if ($maturity < 0) {
            $response = "Required to enter due date if selecting debt";
            header("Location: ../wmsSpending.php?errorMessage=" . urlencode($response));
            exit;
        }

        $debt_id = $spending_transaction_id;

        $query_debt = "INSERT INTO `debt` (debt_id, debt_total, debt_due_date, debt_status, status, debt_updated_by, debt_created_by, debt_updated_at, debt_created_at) VALUES ('$debt_id', '$spending_total', '$maturity', '1', 'active', '', '$username_creator', '', NOW())";

        // accumulation mutation logic for debt
        $last_balance_query = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '$bank_account_number' ORDER BY mutation_created_at DESC LIMIT 1";
        $last_balance_result = mysqli_query($mysqli, $last_balance_query);
        $last_balance_row = mysqli_fetch_assoc($last_balance_result);

        if ($last_balance_row) {
            $last_balance = $last_balance_row['last_balance'] + $spending_total;
        } else {
            $last_balance = $spending_total;
        }

        $result = mysqli_query($mysqli, $query_debt);
    } else {
        $debt_id = '';
    }

    // ------ Input spending ------ //

    // input spending
    $query_spending = "INSERT INTO `spending` (spending_transaction_id, debt_id, bank_account_number, bank_account_name, spending_name, spending_date, spending_description, spending_total, spending_status, spending_updated_by, spending_created_by, spending_updated_at, spending_created_at)

    VALUES ('$spending_transaction_id', '$debt_id', '$bank_account_number', '$bank_account_name', '$spending_name', '$spending_date', '$spending_description - $spending_name', '$spending_total', 'active', '', '$username_creator', '', NOW())";

    $result = mysqli_query($mysqli, $query_spending);

    // ------ Input mutation ------ //
    // input mutation
    $query_mutation = "INSERT INTO `mutation` (transaction_id, debt_id, bank_account_number, bank_account_name, credit, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
    VALUES ('$spending_transaction_id', '$debt_id', '$bank_account_number', '$bank_account_name', '$spending_total', '$last_balance', '$spending_description - $spending_name', '', '$username_creator', '', NOW())";

    $result = mysqli_query($mysqli, $query_mutation);

    
    $response = "Spending $spending_name successfully added";
    header("Location: ../wmsSpending.php?successMessage=" . urlencode($response));
}
