<?php
include "../model/config.php";

// delete purchase
if (isset($_POST["btn-delete-purchase"])) {
    $username_creator = $_POST["username_creator"];
    $debt_id = $_POST["debt_id"];
    $purchase_transaction_id = $_POST["purchase_transaction_id"];
    $bank_account_number = $_POST["bank_account_number"];
    $confirmation_delete = $_POST["confirmation_delete"];
    $purchase_total = $_POST["purchase_total"];
    $product_qty = $_POST["product_qty"];
    $product_sku = $_POST["product_sku"];

    if ($confirmation_delete != $purchase_transaction_id) {
        $response = "Confirmation purchase id name does not match";
        header("Location: ../wmsPurchaseDetails.php?purchase_transaction_id=$purchase_transaction_id&errorMessage=" . urlencode($response));
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

        if ($debt_id > 0) {
            // accumulation last balance logic
            if ($last_balance_row) {
                $last_balance = $last_balance_row['last_balance'] - $purchase_total;
            } else {
                $last_balance = $purchase_total;
            }
        } elseif ($debt_id < 0) {
            if ($last_balance_row) {
                $last_balance = $last_balance_row['last_balance'] + $purchase_total;
            } else {
                $last_balance = $purchase_total;
            }
        }

        $query_debt = "UPDATE `debt` SET status = 'canceled', debt_updated_by = '$username_creator', debt_updated_at = NOW() WHERE debt_id = '$debt_id'";
        $result = mysqli_query($mysqli, $query_debt);

        // stock logic
        foreach ($product_sku as $key => $sku) {
            $qty = $product_qty[$key];

            $check_stock_query = "SELECT stock_qty FROM `stock` WHERE product_sku = '$sku'";
            $check_stock_result = mysqli_query($mysqli, $check_stock_query);
            $row = mysqli_fetch_assoc($check_stock_result);
            $current_stock = $row['stock_qty'];

            if ($current_stock >= $qty) {
                $query_stock = "UPDATE `stock` SET stock_qty = stock_qty - $qty, stock_updated_by = '$username_creator', stock_updated_at = NOW() WHERE product_sku = '$sku'";
                $result = mysqli_query($mysqli, $query_stock);
            }
        }

        // transaction id mutation logic
        $purchase_transaction_id_mutation = str_replace('P', 'C', $purchase_transaction_id);

        $query_mutation = "INSERT INTO `mutation` (transaction_id, debt_id, bank_account_number, bank_account_name, debt, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
        VALUES ('$purchase_transaction_id_mutation', '$debt_id', '$bank_account_number', '$bank_account_name', '$purchase_total', '$last_balance', 'Cancellation of transaction #$purchase_transaction_id', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query_mutation);

        $query_purchase_detail = "UPDATE `purchase_detail` SET purchase_detail_status = 'canceled', purchase_detail_updated_by = '$username_creator', purchase_detail_updated_at = NOW()  WHERE purchase_transaction_id = '$purchase_transaction_id'";
        $result = mysqli_query($mysqli, $query_purchase_detail);

        $query_purchase = "UPDATE `purchase` SET purchase_status = 'canceled', purchase_updated_by = '$username_creator', purchase_updated_at = NOW() WHERE purchase_transaction_id = '$purchase_transaction_id'";
        $result = mysqli_query($mysqli, $query_purchase);

        
        $response = "Purchase #$purchase_transaction_id successfully deleted";
        header("Location: ../wmsPurchasingList.php?successMessage=" . urlencode($response));
    }
}