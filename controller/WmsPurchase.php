<?php
include "../model/config.php";

// input to purchase table
if (isset($_POST["btn-add-purchase"])) {
    $user_id = $_POST["user_id"];
    $username_creator = $_POST["username_creator"];
    $supplier_id = $_POST["supplier_id"];
    $purchase_date = $_POST["purchase_date"];
    $maturity = $_POST["maturity"];
    $bank_account_number = $_POST["bank_account_number"];
    $amount = $_POST["amount"];

    $current_time = date("H:i:s");
    $purchase_date = $purchase_date . ' ' . $current_time;
    $maturity = $maturity . ' ' . $current_time;

    // purchase detail
    $sql = "SELECT * FROM `purchase_cart` WHERE purchase_cart_id = '$user_id' ORDER BY created_at";
    $result = $mysqli->query($sql);

    $purchase_details = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $purchase_details[] = array(
                'product_name' => $row['product_name'],
                'product_sku' => $row['product_sku'],
                'product_qty' => $row['product_qty'],
                'purchase_price' => $row['purchase_price'],
                'selling_price' => $row['selling_price'],
                'description' => $row['description']
            );
        }
    } else {
        $response = "No item to sales";
        header("Location: ../wmsPurchase.php?errorMessage=" . urlencode($response));
        exit;
    }

    // price logic
    $purchase_price = preg_replace("/[^0-9]/", "", $_POST["purchase_price"]);
    $selling_price = preg_replace("/[^0-9]/", "", $_POST["selling_price"]);

    // get bank name
    $bank_get_query = "SELECT * FROM `bank_account` WHERE bank_account_number = '$bank_account_number'";
    $result = mysqli_query($mysqli, $bank_get_query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $bank_account_name = $row['bank_account_name'];
    }

    // get supplier name
    $supplier_get_query = "SELECT * FROM `supplier` WHERE supplier_id = '$supplier_id'";
    $result = mysqli_query($mysqli, $supplier_get_query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $supplier_name = $row['supplier_name'];
    }

    // transaction id logic
    $transaction_id_date_suffix = date('dmY');
    $count_query = "SELECT COUNT(*) AS total FROM `purchase` WHERE purchase_created_at >= CURDATE()";
    $count_result = mysqli_query($mysqli, $count_query);
    $count_row = mysqli_fetch_assoc($count_result);
    $count = $count_row['total'];
    $count_formatted = sprintf("%03d", $count + 1);
    $transaction_id = 'P' . $transaction_id_date_suffix . $count_formatted;

    // balance checker logic
    $last_balance_query = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '$bank_account_number' ORDER BY mutation_created_at DESC LIMIT 1";
    $last_balance_result = mysqli_query($mysqli, $last_balance_query);
    $last_balance_row = mysqli_fetch_assoc($last_balance_result);

    // balance logic
    if ($last_balance_row["last_balance"] < $amount & $bank_account_number != '123debt') {
        $response = "Bank $bank_account_name balance is insufficient";
        header("Location: ../wmsPurchase.php?errorMessage=" . urlencode($response));
        exit;
    }

    // accumulation last balance logic
    if ($last_balance_row) {
        $last_balance = $last_balance_row['last_balance'] - $amount;
    } else {
        $last_balance = $amount;
    }

    // ------ Input debt ------ //

    // input debt
    if ($bank_account_number === '123debt') {

        if ($maturity < 0) {
            $response = "Required to enter due date if selecting debt";
            header("Location: ../wmsPurchase.php?errorMessage=" . urlencode($response));
            exit;
        }

        $debt_id = $transaction_id;

        $query_debt = "INSERT INTO `debt` (debt_id, debt_total, debt_due_date, debt_status, status, debt_updated_by, debt_created_by, debt_updated_at, debt_created_at) VALUES ('$debt_id', '$amount', '$maturity', '1', 'active', '', '$username_creator', '', NOW())";

        // accumulation mutation logic for debt
        $last_balance_query = "SELECT last_balance FROM `mutation` WHERE bank_account_number = '$bank_account_number' ORDER BY mutation_created_at DESC LIMIT 1";
        $last_balance_result = mysqli_query($mysqli, $last_balance_query);
        $last_balance_row = mysqli_fetch_assoc($last_balance_result);

        if ($last_balance_row) {
            $last_balance = $last_balance_row['last_balance'] + $amount;
        } else {
            $last_balance = $amount;
        }

        $result = mysqli_query($mysqli, $query_debt);
    } else {
        $debt_id = '';
    }

    // ------ Input purchase ------ //

    $query = "INSERT INTO `purchase` (purchase_transaction_id, debt_id, bank_account_number, supplier_id, bank_account_name, supplier_name, purchase_date, purchase_due_date, purchase_total, purchase_status, purchase_updated_by, purchase_created_by, purchase_updated_at, purchase_created_at)
    VALUES ('$transaction_id', '$debt_id', '$bank_account_number', '$supplier_id', '$bank_account_name', '$supplier_name', '$purchase_date', '$maturity', '$amount', 'active', '', '$username_creator', '', '$purchase_date')";

    $result = mysqli_query($mysqli, $query);

    // ------ Input purchase detail ------ //

    foreach ($purchase_details as $detail) {
        $product_name = $detail['product_name'];
        $product_sku = $detail['product_sku'];
        $product_qty = $detail['product_qty'];
        $purchase_price = $detail['purchase_price'];
        $selling_price = $detail['selling_price'];
        $description = $detail['description'];

        // item amount logic
        $item_amount = $detail['product_qty'] * $detail['purchase_price'];

        // COGS
        $COGS = $detail['selling_price'] - $detail['purchase_price'];

        $query = "INSERT INTO `purchase_detail` (purchase_transaction_id, product_sku, product_name, product_qty, purchase_price, selling_price, item_amount, description, purchase_date, purchase_detail_status, purchase_detail_created_at)
        VALUES ('$transaction_id', '$product_sku', '$product_name', '$product_qty', '$purchase_price', '$selling_price', '$item_amount', '$description', '$purchase_date', 'active', '$purchase_date')";

        $result = mysqli_query($mysqli, $query);

        // ------ Input stock ------ //
        $product_info_query = "SELECT * FROM product WHERE product_sku = '$product_sku'";
        $product_info_result = mysqli_query($mysqli, $product_info_query);

        if ($product_info_result && mysqli_num_rows($product_info_result) > 0) {
            $product_info = mysqli_fetch_assoc($product_info_result);

            if ($product_info) {
                $product_weight = $product_info['product_weight'];
                $product_barcode = $product_info['product_barcode'];
                $product_color = $product_info['product_color'];
                $product_category = $product_info['product_category'];
                $product_description = $product_info['product_description'];

                // check stock
                $existing_product_query = "SELECT * FROM stock WHERE product_sku = '$product_sku'";
                $existing_product_result = mysqli_query($mysqli, $existing_product_query);

                if (mysqli_num_rows($existing_product_result) > 0) {
                    $existing_product_row = mysqli_fetch_assoc($existing_product_result);
                    $new_stock_qty = $existing_product_row['stock_qty'] + $product_qty;

                    $update_query = "UPDATE `stock` SET stock_qty = '$new_stock_qty', purchase_price = '$purchase_price', selling_price = '$selling_price', COGS = '$COGS', stock_updated_by = '$username_creator', stock_updated_at = NOW() WHERE product_sku = '$product_sku'";

                    $update_result = mysqli_query($mysqli, $update_query);
                    
                    $response = "Your product item $product_sku stock has been added";
                    header("Location: ../wmsPurchasingList.php?successMessage=" . urlencode($response));
                } else {
                    // Insert stock
                    $stock_insert_query = "INSERT INTO stock (product_sku, product_name, product_weight, product_barcode, product_color, product_category, product_description, purchase_price, selling_price, COGS, stock_qty, stock_updated_by, stock_created_by, stock_updated_at, stock_created_at)
                    VALUES ('$product_sku', '$product_name', '$product_weight', '$product_barcode', '$product_color', '$product_category', '$product_description', '$purchase_price', '$selling_price', '$COGS', '$product_qty', '', '$username_creator', '', NOW())";

                    $stock_insert_result = mysqli_query($mysqli, $stock_insert_query);
                }
            }
        }
    }

    // ------ Delete cart ------ //

    $delete_query = "DELETE FROM `purchase_cart` WHERE purchase_cart_id = '$user_id'";
    $delete_result = mysqli_query($mysqli, $delete_query);

    // ------ Input mutation ------ //

    $query_mutation = "INSERT INTO `mutation` (transaction_id, debt_id, bank_account_number, bank_account_name, credit, last_balance, transaction_description, mutation_updated_by, mutation_created_by, mutation_updated_at, mutation_created_at)
    VALUES ('$transaction_id', '$debt_id', '$bank_account_number', '$bank_account_name', '$amount', '$last_balance', 'Purchase with invoice #$transaction_id - $supplier_name', '', '$username_creator', '', '$purchase_date')";

    $result = mysqli_query($mysqli, $query_mutation);

    
    $response = "Purchase $transaction_id successfully added";
    header("Location: ../wmsPurchase.php?successMessage=" . urlencode($response));
}
