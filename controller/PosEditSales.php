<?php
include "../model/config.php";

// delete sales
if (isset($_POST["btn-delete-sales"])) {
    $username_creator = $_POST["username_creator"];
    $receivable_id = $_POST["receivable_id"];
    $sales_transaction_id = $_POST["sales_transaction_id"];
    $bank_account_number = $_POST["bank_account_number"];
    $confirmation_delete = $_POST["confirmation_delete"];
    $sales_total = $_POST["sales_total"];
    $product_qty = $_POST["product_qty"];
    $product_sku = $_POST["product_sku"];

    if ($confirmation_delete != $sales_transaction_id) {
        $response = "Confirmation sales id name does not match";
        header("Location: ../posSalesDetails.php?sales_transaction_id=$sales_transaction_id&errorMessage=" . urlencode($response));
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

        if ($receivable_id > 0) {
            // accumulation last balance logic
            if ($last_balance_row) {
                $last_balance = $last_balance_row['last_balance'] - $sales_total;
            } else {
                $last_balance = $sales_total;
            }
        } elseif ($receivable_id < 0) {
            if ($last_balance_row) {
                $last_balance = $last_balance_row['last_balance'] - $sales_total;
            } else {
                $last_balance = $sales_total;
            }
        }

        $query_receivable = "UPDATE `receivable` SET status = 'canceled', receivable_updated_by = '$username_creator', receivable_updated_at = NOW() WHERE receivable_id = '$receivable_id'";
        $result = mysqli_query($mysqli, $query_receivable);

        // stock logic
        foreach ($product_sku as $key => $sku) {
            $qty = $product_qty[$key];

            $check_stock_query = "SELECT stock_qty FROM `stock` WHERE product_sku = '$sku'";
            $check_stock_result = mysqli_query($mysqli, $check_stock_query);
            $row = mysqli_fetch_assoc($check_stock_result);
            $current_stock = $row['stock_qty'];

            if ($current_stock >= $qty) {
                $query_stock = "UPDATE `stock` SET stock_qty = stock_qty + $qty, stock_updated_by = '$username_creator', stock_updated_at = NOW() WHERE product_sku = '$sku'";
                $result = mysqli_query($mysqli, $query_stock);
            }
        }

        // transaction id mutation logic
        $sales_transaction_id_mutation = str_replace('S', 'C', $sales_transaction_id);

        $query_mutation = "INSERT INTO `mutation` (transaction_id, receivable_id, bank_account_number, bank_account_name, credit, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
        VALUES ('$sales_transaction_id_mutation', '$receivable_id', '$bank_account_number', '$bank_account_name', '$sales_total', '$last_balance', 'Cancellation of transaction #$sales_transaction_id', '', '$username_creator', '', NOW())";
        $result = mysqli_query($mysqli, $query_mutation);

        $query_sales_detail = "UPDATE `sales_detail` SET sales_detail_status = 'canceled', sales_detail_updated_by = '$username_creator', sales_detail_updated_at = NOW()  WHERE sales_transaction_id = '$sales_transaction_id'";
        $result = mysqli_query($mysqli, $query_sales_detail);

        $query_sales = "UPDATE `sales` SET sales_status = 'canceled', sales_updated_by = '$username_creator', sales_updated_at = NOW() WHERE sales_transaction_id = '$sales_transaction_id'";
        $result = mysqli_query($mysqli, $query_sales);

        
        $response = "Sales #$sales_transaction_id successfully deleted";
        header("Location: ../posSalesList.php?successMessage=" . urlencode($response));
    }
}