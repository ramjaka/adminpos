<?php
include "../model/config.php";

if (isset($_POST["btn-sales-success"])) {
    $user_id = $_POST["user_id"];
    $bank_account_number = $_POST["bank_account_number"];
    $member_id = $_POST["member_id"];
    $promotion_id = $_POST["promotion_id"];
    $username_creator = $_POST["username_creator"];
    $total = $_POST["total"];
    $amount = $_POST["amount"];
    $product_sku = $_POST["product_sku"];
    $product_qty = $_POST["product_qty"];
    $product_name = $_POST["product_name"];
    $product_category = $_POST["product_category"];
    $product_description = $_POST["product_description"];
    $selling_price = $_POST["selling_price"];
    $maturity = $_POST["maturity"];
    $promotion_value = $_POST["promotion"];
    $sales_date = $_POST["sales_date"];
    $payment_method = $_POST["payment_method"];

    $current_time = date("H:i:s");
    $sales_date = $sales_date . ' ' . $current_time;
    $maturity = $maturity . ' ' . $current_time;

    // insert to sales detail table
    $sql = "SELECT * FROM `sales_cart` WHERE sales_cart_id = '$user_id' ORDER BY created_at";
    $result = $mysqli->query($sql);

    $sales_details = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sales_details[] = array(
                'product_name' => $row['product_name'],
                'product_sku' => $row['product_sku'],
                'product_qty' => $row['product_qty'],
                'selling_price' => $row['selling_price'],
                'purchase_price' => $row['purchase_price'],
                'COGS' => $row['COGS'],
                'description' => $row['description']
            );
        }
    } else {
        $response = "No item to sales";
        header("Location: ../posCashier.php?errorMessage=" . urlencode($response));
        exit;
    }

    // price logic
    $selling_price = preg_replace("/[^0-9]/", "", $_POST["selling_price"]);
    $total = preg_replace("/[^0-9]/", "", $_POST["total"]);

    // get bank name
    $bank_get_query = "SELECT * FROM `bank_account` WHERE bank_account_number = '$bank_account_number'";
    $result = mysqli_query($mysqli, $bank_get_query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $bank_account_name = $row['bank_account_name'];
    }

    // get member name
    if ($member_id > 0) {
        $member_get_query = "SELECT * FROM `member` WHERE member_id = '$member_id'";
        $result = mysqli_query($mysqli, $member_get_query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $member_first_name = $row['member_first_name'];
            $member_last_name = $row['member_last_name'];
            $member_name = $row['member_first_name'] . ' ' . $row['member_last_name'];
        }
    } else {
        $member_name = '';
    }

    // get promotion name
    if ($promotion_id > 0) {
        $member_get_query = "SELECT * FROM `promotion` WHERE promotion_id = '$promotion_id'";
        $result = mysqli_query($mysqli, $member_get_query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $promotion_name = $row['promotion_name'];
        }
    } else {
        $promotion_name = '';
    }

    // date required
    if ($sales_date < 0) {
        $response = "Transaction date required";
        header("Location: ../posCashier.php?errorMessage=" . urlencode($response));
        exit;
    }

    $last_balance_query = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '$bank_account_number' ORDER BY mutation_created_at DESC LIMIT 1";
    $last_balance_result = mysqli_query($mysqli, $last_balance_query);
    $last_balance_row = mysqli_fetch_assoc($last_balance_result);

    // if ($last_balance_row["last_balance"] < $total & $bank_account_number != '123receivables') {
    //     $response = "Bank $bank_account_name balance is insufficient";
    //     header("Location: ../posCashier.php?errorMessage=" . urlencode($response));
    //     exit;
    // }

    // accumulation last balance logic
    if ($last_balance_row) {
        $last_balance = $last_balance_row['last_balance'] + $total;
    } else {
        $last_balance = $total;
    }

    // transaction id logic
    $transaction_id_date_suffix = date('dmY');
    $count_query = "SELECT COUNT(*) AS total FROM `sales` WHERE sales_created_at >= CURDATE()";
    $count_result = mysqli_query($mysqli, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $count = $count_row['total'];
    $count_formatted = sprintf("%03d", $count + 1);
    $transaction_id = 'S' . $transaction_id_date_suffix . $count_formatted;

    // receivables input
    if ($bank_account_number === '123receivables') {
        if ($maturity < 0) {
            $response = "Maturity date is required if selecting receivables";
            header("Location: ../posCashier.php?errorMessage=" . urlencode($response));
            exit;
        }

        // insert to receivable table
        $receivable_id = $transaction_id;

        $query_receivable = "INSERT INTO `receivable` (receivable_id, receivable_total, receivable_due_date, receivable_status, status, receivable_updated_by, receivable_created_by, receivable_updated_at, receivable_created_at) VALUES ('$receivable_id', '$total', '$maturity', '1', 'active', '', '$username_creator', '', NOW())";

        // accumulation mutation logic for receivable
        $last_balance_query = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '$bank_account_number' ORDER BY mutation_created_at DESC LIMIT 1";
        $last_balance_result = mysqli_query($mysqli, $last_balance_query);
        $last_balance_row = mysqli_fetch_assoc($last_balance_result);

        if ($last_balance_row) {
            $last_balance = $last_balance_row['last_balance'] + $total;
        } else {
            $last_balance = $total;
        }

        $result = mysqli_query($mysqli, $query_receivable);
    } else {
        $receivable_id = '';
    }

    // insert to sales table
    $query = "INSERT INTO `sales` (sales_transaction_id, receivable_id, bank_account_number, member_id, bank_account_name, member_name, promotion_name, promotion_value, sales_date, sales_due_date, sales_subtotal, sales_total, sales_status, payment_method, sales_updated_by, sales_created_by, sales_updated_at, sales_created_at)
    
    VALUES ('$transaction_id', '$receivable_id', '$bank_account_number', '$member_id', '$bank_account_name', '$member_name', '$promotion_name', '$promotion_value', '$sales_date', '$maturity', '$amount', '$total', 'active', '$payment_method', '', '$username_creator', '', '$sales_date')";
    $result = mysqli_query($mysqli, $query);

    foreach ($sales_details as $detail) {
        $product_name = $detail['product_name'];
        $product_sku = $detail['product_sku'];
        $product_qty = $detail['product_qty'];
        $selling_price = $detail['selling_price'];
        $purchase_price = $detail['purchase_price'];
        $COGS = $detail['COGS'];
        $description = $detail['description'];

        // item amount logic
        $item_amount = $detail['product_qty'] * $detail['selling_price'];
        $item_amount_purchase = $detail['product_qty'] * $detail['purchase_price'];

        $query = "INSERT INTO `sales_detail` (sales_transaction_id, product_sku, product_name, product_qty, purchase_price, selling_price, COGS, item_amount, sales_detail_status, sales_date, sales_detail_created_by, sales_detail_created_at)
        VALUES ('$transaction_id', '$product_sku', '$product_name', '$product_qty', '$item_amount_purchase', '$selling_price', '$COGS', '$item_amount', 'active', '$sales_date', '$username_creator', '$sales_date')";

        $result = mysqli_query($mysqli, $query);

        // ------ Update stock ------ //

        $stock_query = "UPDATE `stock` SET stock_qty = stock_qty - '$product_qty', stock_updated_by = '$username_creator', stock_updated_at = NOW() WHERE product_sku = '$product_sku'";

        $result_stock = mysqli_query($mysqli, $stock_query);
    }

    // ------ Delete cart ------ //
    $delete_query = "DELETE FROM `sales_cart` WHERE sales_Cart_id = '$user_id'";
    $delete_result = mysqli_query($mysqli, $delete_query);

    // ------ Input mutation ------ //
    $query_mutation = "INSERT INTO `mutation` (transaction_id, receivable_id, bank_account_number, bank_account_name, debt, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
    VALUES ('$transaction_id', '$receivable_id', '$bank_account_number', '$bank_account_name', '$total', '$last_balance', 'Sales with invoice #$transaction_id', '', '$username_creator', '', '$sales_date')";

    $result = mysqli_query($mysqli, $query_mutation);

    // $response = "Transaction $transaction_id successfully";
    // header("Location: ../posCashier.php?successMessage=" . urlencode($response));
    header("Location: ../printSales.php?sales_transaction_id=$transaction_id");
    exit;
}
