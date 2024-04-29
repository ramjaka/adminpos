<?php
require_once 'controller/Session.php';
$page = 'bank-accounting';
$page_access = array('1', '16');

// authorization
$get_access = "SELECT access FROM `user` WHERE `user_id` = '$user_id'";
$result = $mysqli->query($get_access);
$access = array();
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $access_values = explode(',', $row['access']);

    $authorized = false;
    foreach ($page_access as $value) {
        if (in_array($value, $access_values)) {
            $authorized = true;
            break;
        }
    }

    if (!$authorized) {
        $response = "Sorry you don't have access to that page";
        header("Location: index.php?errorMessage=" . urlencode($response));
        exit;
    }
}

// date picker
$date_from = date("Y-m-d");
$date_select = date("Y-m-d");

if (isset($_POST["btn-set-date"])) {
    $date_value = $_POST["date_value"];

    $dates = explode(" - ", $date_value);

    $date_from = date("Y-m-d", strtotime(trim($dates[0])));
    $date_select = date("Y-m-d", strtotime(trim($dates[1])));
}

if (isset($_POST["btn-set-showall"])) {
    $date_from = NULL;
    $date_select = NULL;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('components/head.php'); ?>

    <style>
        @media print {
            .d-print-none {
                display: none !important;
            }
        }
    </style>
</head>

<body class="footer-offset">

    <script src="./assets/js/hs.theme-appearance.js"></script>

    <script src="./assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside-mini-cache.js"></script>

    <main id="content" role="main" class="main">
        <div class="container p-5">
            <a href="javascript:window.close()" class="btn btn-white mb-5 d-print-none"><i class="bi bi-chevron-left"></i> Back</a>
            <div class="mb-4 col-12 d-print-none">
                <div class="row">
                    <div class="col-6">
                        <label>Transaction Date</label><br>
                        <form action="" method="POST">
                            <input id="js-daterangepicker-predefined" name="date_value" class="btn btn-white" readonly>
                            <span class="js-daterangepicker-predefined-preview"></span>
                            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                <button type="submit" name="btn-set-date" class="btn btn-primary">Search</button>
                                <button type="submit" name="btn-set-showall" class="btn btn-success">Show All</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-6 d-flex justify-content-end">
                        <div class="d-print-none gap-3 mt-3">
                            <a id="printButton" class="btn btn-primary">
                                <i class="bi-printer me-1"></i> Print Trial Balance
                            </a>
                            <script>
                                var printButton = document.getElementById('printButton');
                                printButton.addEventListener('click', function() {
                                    window.print();
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>

            <table class="w-100">
                <tr>
                    <th class="border border-2 p-2">Account Type</th>
                    <th class="border border-2 p-2">Account Name</th>
                    <th class="border border-2 p-2">Debit</th>
                    <th class="border border-2 p-2">Credit</th>
                </tr>
                <?php
                // Query for bank account assets
                $sql_bank_accounts = "SELECT bank_account_number FROM bank_account";
                $result_bank_accounts = $mysqli->query($sql_bank_accounts);

                if ($result_bank_accounts->num_rows > 0) {
                    while ($row_bank_account = $result_bank_accounts->fetch_assoc()) {
                        $bank_account_number = $row_bank_account['bank_account_number'];

                        if ($bank_account_number !== '123debt') {
                            $sql = "SELECT m.*, b.bank_account_number, b.bank_account_name 
                        FROM `mutation` m 
                        JOIN `bank_account` b ON m.bank_account_number = b.bank_account_number
                        WHERE b.bank_account_number = '$bank_account_number'";

                            if ($date_from && $date_select) {
                                $sql .= " AND DATE(m.mutation_created_at) BETWEEN '$date_from' AND '$date_select'";
                            }

                            $sql .= " ORDER BY m.mutation_created_at ASC";

                            $result = $mysqli->query($sql);

                            if ($result->num_rows > 0) {
                                $last_balance = 0;
                                while ($row = $result->fetch_assoc()) {
                                    $last_balance = floatval($row['last_balance']);
                                    $bank_account_name = $row['bank_account_name'];
                                }
                ?>
                                <tr>
                                    <td class="border border-2 p-2">Assets</td>
                                    <td class="border border-2 p-2"><?php echo $bank_account_name; ?></td>
                                    <td class="border border-2 p-2">
                                        <input disabled class="account_cash bg-transparent border-0" type="text" value="<?php echo "Rp " . number_format($last_balance, 0, ',', '.'); ?>">
                                    </td>
                                    <td class="border border-2 p-2">-</td>
                                </tr>
                        <?php
                            }
                        }
                    }
                }

                // Query for inventory assets
                $product_query = "SELECT product_sku, product_name FROM product";
                $product_result = mysqli_query($mysqli, $product_query);

                while ($product_row = mysqli_fetch_assoc($product_result)) {
                    $product_sku = $product_row['product_sku'];
                    $product_name = $product_row['product_name'];

                    $query_where = "";
                    if ($date_from && $date_select) {
                        $query_where .= " WHERE DATE(transaction_date) BETWEEN '$date_from' AND '$date_select'";
                    }
                    $query = "SELECT 
                        transaction_date,
                        description,
                        purchase_amount,
                        sales_amount,
                        SUM(purchase_amount - sales_amount) OVER (ORDER BY transaction_date) AS balance
                    FROM (
                        SELECT 
                            purchase_detail_created_at AS transaction_date,
                            CONCAT('Pembelian - Rp ', purchase_price * product_qty) AS description,
                            item_amount AS purchase_amount,
                            0 AS sales_amount
                        FROM 
                            purchase_detail
                        WHERE 
                            product_sku = '$product_sku'
                        UNION ALL
                        SELECT 
                            sales_date AS transaction_date,
                            CONCAT('Penjualan - Rp ', purchase_price * product_qty) AS description,
                            0 AS purchase_amount,
                            purchase_price AS sales_amount
                        FROM 
                            sales_detail
                        WHERE 
                            product_sku = '$product_sku'
                    ) AS all_transactions
                    $query_where
                    ORDER BY 
                        transaction_date DESC
                    LIMIT 1";

                    $result = mysqli_query($mysqli, $query);

                    // Pastikan ada hasil dari query
                    if ($result && mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        ?>
                        <tbody>
                            <tr>
                                <td class="border border-2 p-2">Assets</td>
                                <td class="border border-2 p-2"><?php echo $product_sku ?> - <?php echo $product_name ?></td>
                                <td class="border border-2 p-2">
                                    <input disabled class="account_cash bg-transparent border-0" type="text" value="<?php echo "Rp " . number_format(floatval($row['balance']), 0, ',', '.'); ?>">
                                </td>
                                <td class="border border-2 p-2">-</td>
                            </tr>
                        </tbody>
                    <?php
                    }
                }

                // Query for spending assets
                $query = "SELECT * 
                FROM `spending` s
                JOIN `mutation` m ON s.`spending_transaction_id` = m.`transaction_id`
                WHERE s.`spending_description` LIKE '%Repaid Rent%'";

                if ($date_from && $date_select) {
                    $query .= " AND DATE(s.spending_date) BETWEEN '$date_from' AND '$date_select'";
                }

                $query .= " ORDER BY s.spending_date";

                $result = mysqli_query($mysqli, $query);

                $totalRows = mysqli_num_rows($result);
                $rowCount = 0;
                $totalSpending = 0;

                // Loop untuk menampilkan data dalam format yang diinginkan
                while ($row = mysqli_fetch_assoc($result)) {
                    $rowCount++;
                    $totalSpending += floatval($row['spending_total']); // Menjumlahkan total pengeluaran
                    ?>
                    <tr>
                        <td class="border border-2 p-2">Assets</td>
                        <td class="border border-2 p-2"><?php echo $row['spending_description'] ?></td>
                        <td class="border border-2 p-2">
                            <input disabled class="account_cash bg-transparent border-0" type="text" value="<?php echo "Rp " . number_format(floatval($row['spending_total']), 0, ',', '.'); ?>">
                        </td>
                        <td class="border border-2 p-2">-</td>
                    </tr>
                <?php
                }

                // Query for spending assets
                $query = "SELECT * 
                FROM `spending` s
                JOIN `mutation` m ON s.`spending_transaction_id` = m.`transaction_id`
                WHERE s.`spending_description` LIKE '%Office Needs%'";

                if ($date_from && $date_select) {
                    $query .= " AND DATE(s.spending_date) BETWEEN '$date_from' AND '$date_select'";
                }

                $query .= " ORDER BY s.spending_date";

                $result = mysqli_query($mysqli, $query);

                $totalRows = mysqli_num_rows($result);
                $rowCount = 0;
                $totalSpending = 0;

                // Loop untuk menampilkan data dalam format yang diinginkan
                while ($row = mysqli_fetch_assoc($result)) {
                    $rowCount++;
                    $totalSpending += floatval($row['spending_total']); // Menjumlahkan total pengeluaran
                ?>
                    <tr>
                        <td class="border border-2 p-2">Assets</td>
                        <td class="border border-2 p-2"><?php echo $row['spending_description'] ?></td>
                        <td class="border border-2 p-2">
                            <input disabled class="account_cash bg-transparent border-0" type="text" value="<?php echo "Rp " . number_format(floatval($row['spending_total']), 0, ',', '.'); ?>">
                        </td>
                        <td class="border border-2 p-2">-</td>
                    </tr>
                    <?php
                }

                // Query for bank accounts debt
                $sql_bank_accounts = "SELECT bank_account_number FROM bank_account";
                $result_bank_accounts = $mysqli->query($sql_bank_accounts);

                if ($result_bank_accounts->num_rows > 0) {
                    while ($row_bank_account = $result_bank_accounts->fetch_assoc()) {
                        $bank_account_number = $row_bank_account['bank_account_number'];

                        if ($bank_account_number === '123debt') {
                            $sql = "SELECT m.*, b.bank_account_number, b.bank_account_name 
                        FROM `mutation` m 
                        JOIN `bank_account` b ON m.bank_account_number = b.bank_account_number
                        WHERE b.bank_account_number = '$bank_account_number'";

                            if ($date_from && $date_select) {
                                $sql .= " AND DATE(m.mutation_created_at) BETWEEN '$date_from' AND '$date_select'";
                            }

                            $sql .= " ORDER BY m.mutation_created_at ASC";

                            $result = $mysqli->query($sql);

                            if ($result->num_rows > 0) {
                                $last_balance = 0;
                                while ($row = $result->fetch_assoc()) {
                                    $last_balance = floatval($row['last_balance']);
                                    $bank_account_name = $row['bank_account_name'];
                                }
                    ?>
                                <tr>
                                    <td class="border border-2 p-2">Liability</td>
                                    <td class="border border-2 p-2"><?php echo $bank_account_name; ?></td>
                                    <td class="border border-2 p-2">-</td>
                                    <td class="border border-2 p-2">
                                        <input disabled class="account_credit bg-transparent border-0" type="text" value="<?php echo "Rp " . number_format($last_balance, 0, ',', '.'); ?>">
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                    }
                }

                // Query for equity
                $sql = "SELECT 
                    *
                FROM 
                    `mutation`
                WHERE 
                    `transaction_description` LIKE '%Add balance%'                                                                      
                ";

                if ($date_from && $date_select) {
                    $sql .= " AND DATE(mutation_created_at) BETWEEN '$date_from' AND '$date_select'";
                }

                $sql .= " ORDER BY mutation_created_at ASC";

                $result = $mysqli->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $rows[] = $row;
                    }

                    // Loop untuk setiap transaksi
                    foreach ($rows as $key => $row) {
                        $totalDebt = 0;
                        $totalCredit = 0;
                        $totalRows = count($rows);
                        $rowCount = $key + 1;

                        // Hitung total debt dan credit hingga transaksi saat ini
                        for ($i = 0; $i <= $key; $i++) {
                            $totalDebt += (!empty($rows[$i]['debt'])) ? floatval($rows[$i]['debt']) : 0;
                            $totalCredit += (!empty($rows[$i]['credit'])) ? floatval($rows[$i]['credit']) : 0;
                        }

                        // Hitung saldo aktual
                        $balance = $totalDebt - $totalCredit;
                        ?>

                        <tr>
                            <td class="border border-2 p-2">Equity</td>
                            <td class="border border-2 p-2"><?php echo $row['transaction_description'] ?></td>
                            <td class="border border-2 p-2">-</td>
                            <td class="border border-2 p-2">
                                <input disabled class="account_credit bg-transparent border-0" type="text" value="<?php echo (!empty($row['debt'])) ? "Rp " . number_format(floatval($row['debt']), 0, ',', '.') : '-'; ?>">
                            </td>
                        </tr>
                    <?php
                    }
                }

                // Query for prive
                $sql = "SELECT 
                    *
                FROM 
                    `mutation`
                WHERE 
                    `transaction_description` LIKE '%Prive%'                                                                      
                ";

                if ($date_from && $date_select) {
                    $sql .= " AND DATE(mutation_created_at) BETWEEN '$date_from' AND '$date_select'";
                }

                $sql .= " ORDER BY mutation_created_at ASC";

                $result = $mysqli->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $transactions[] = $row;
                    }

                    foreach ($transactions as $key => $transaction) {
                        $totalDebt = 0;
                        $totalCredit = 0;
                        $totalRows = count($transactions);
                        $rowCount = $key + 1;

                        for ($i = 0; $i <= $key; $i++) {
                            $totalDebt += (!empty($transactions[$i]['debt'])) ? floatval($transactions[$i]['debt']) : 0;
                            $totalCredit += (!empty($transactions[$i]['credit'])) ? floatval($transactions[$i]['credit']) : 0;
                        }

                        $balance = $totalCredit - $totalDebt;
                    ?>
                        <tr>
                            <td class="border border-2 p-2">Equity</td>
                            <td class="border border-2 p-2"><?php echo $transaction['transaction_description'] ?></td>
                            <td class="border border-2 p-2">
                                <input disabled class="account_cash bg-transparent border-0" type="text" value=" <?php echo (!empty($transaction['credit'])) ? "Rp " . number_format(floatval($transaction['credit']), 0, ',', '.') : '-'; ?>">
                            </td>
                            <td class="border border-2 p-2">-</td>
                        </tr>
                    <?php
                    }
                }

                // Query for revenue
                $sql = "SELECT * FROM `sales`";

                if ($date_from && $date_select) {
                    $sql .= " WHERE DATE(sales_date) BETWEEN '$date_from' AND '$date_select'";
                }

                $sql .= " ORDER BY sales_date ASC";

                $result = $mysqli->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $sales[] = $row;
                    }

                    // Loop untuk setiap transaksi
                    foreach ($sales as $key => $sale) {
                        $totalDebt = 0;
                        $totalCredit = 0;
                        $totalRows = count($sales);
                        $rowCount = $key + 1;

                        for ($i = 0; $i <= $key; $i++) {
                            $totalDebt += (!empty($sales['sales_subtotal'])) ? floatval($sales['sales_subtotal']) : 0;
                            $totalCredit += (!empty($sales['credit'])) ? floatval($sales['credit']) : 0;
                        }

                        $balance = $totalDebt + $totalCredit;
                    ?>
                        <tr>
                            <td class="border border-2 p-2">Revenue</td>
                            <td class="border border-2 p-2">Sales with invoice #<?php echo $sale['sales_transaction_id'] ?></td>
                            <td class="border border-2 p-2">-</td>
                            <td class="border border-2 p-2">
                                <input disabled class="account_credit bg-transparent border-0" type="text" value="<?php echo (!empty($sale['sales_subtotal'])) ? "Rp " . number_format(floatval($sale['sales_subtotal']), 0, ',', '.') : '-'; ?>">
                            </td>
                        </tr>
                    <?php
                    }
                }

                // Query for expense
                $query = "SELECT * 
                FROM `spending` s
                JOIN `mutation` m ON s.`spending_transaction_id` = m.`transaction_id`
                WHERE s.`spending_description` LIKE '%Utility Expense%'";

                if ($date_from && $date_select) {
                    $query .= " AND DATE(s.spending_date) BETWEEN '$date_from' AND '$date_select'";
                }

                $query .= " ORDER BY s.spending_date";

                $result = mysqli_query($mysqli, $query);

                $totalRows = mysqli_num_rows($result);
                $rowCount = 0;
                $totalSpending = 0;

                // Loop untuk menampilkan data dalam format yang diinginkan
                while ($row = mysqli_fetch_assoc($result)) {
                    $rowCount++;
                    $totalSpending += floatval($row['spending_total']); // Menjumlahkan total pengeluaran
                    ?>
                    <tr>
                        <td class="border border-2 p-2">Expense</td>
                        <td class="border border-2 p-2"><?php echo $row['spending_description'] ?></td>
                        <td class="border border-2 p-2">
                            <input disabled class="account_cash bg-transparent border-0" type="text" value="<?php echo "Rp " . number_format(floatval($row['spending_total']), 0, ',', '.'); ?>">
                        </td>
                        <td class="border border-2 p-2">-</td>
                    </tr>
                <?php
                }

                // Query for expense
                $query = "SELECT * 
                FROM `spending` s
                JOIN `mutation` m ON s.`spending_transaction_id` = m.`transaction_id`
                WHERE s.`spending_description` LIKE '%Salary Expense%'";

                if ($date_from && $date_select) {
                    $query .= " AND DATE(s.spending_date) BETWEEN '$date_from' AND '$date_select'";
                }

                $query .= " ORDER BY s.spending_date";

                $result = mysqli_query($mysqli, $query);

                $totalRows = mysqli_num_rows($result);
                $rowCount = 0;
                $totalSpending = 0;

                // Loop untuk menampilkan data dalam format yang diinginkan
                while ($row = mysqli_fetch_assoc($result)) {
                    $rowCount++;
                    $totalSpending += floatval($row['spending_total']); // Menjumlahkan total pengeluaran
                ?>
                    <tr>
                        <td class="border border-2 p-2">Expense</td>
                        <td class="border border-2 p-2"><?php echo $row['spending_description'] ?></td>
                        <td class="border border-2 p-2">
                            <input disabled class="account_cash bg-transparent border-0" type="text" value="<?php echo "Rp " . number_format(floatval($row['spending_total']), 0, ',', '.'); ?>">
                        </td>
                        <td class="border border-2 p-2">-</td>
                    </tr>
                <?php
                }

                // Query for expense
                $query = "SELECT * 
                FROM `spending` s
                JOIN `mutation` m ON s.`spending_transaction_id` = m.`transaction_id`
                WHERE s.`spending_description` LIKE '%Other Expense%'";

                if ($date_from && $date_select) {
                    $query .= " AND DATE(s.spending_date) BETWEEN '$date_from' AND '$date_select'";
                }

                $query .= " ORDER BY s.spending_date";

                $result = mysqli_query($mysqli, $query);

                $totalRows = mysqli_num_rows($result);
                $rowCount = 0;
                $totalSpending = 0;

                // Loop untuk menampilkan data dalam format yang diinginkan
                while ($row = mysqli_fetch_assoc($result)) {
                    $rowCount++;
                    $totalSpending += floatval($row['spending_total']); // Menjumlahkan total pengeluaran
                ?>
                    <tr>
                        <td class="border border-2 p-2">Expense</td>
                        <td class="border border-2 p-2"><?php echo $row['spending_description'] ?></td>
                        <td class="border border-2 p-2">
                            <input disabled class="account_cash bg-transparent border-0" type="text" value="<?php echo "Rp " . number_format(floatval($row['spending_total']), 0, ',', '.'); ?>">
                        </td>
                        <td class="border border-2 p-2">-</td>
                    </tr>
                <?php
                }

                // Query for sales discount expense
                $query = "SELECT 
            sales_transaction_id, sales_date, (sales_subtotal - sales_total) AS sales_discount
            FROM 
                sales
            WHERE
                promotion_value IS NOT NULL AND promotion_value != 0";

                if ($date_from && $date_select) {
                    $query .= " AND DATE(sales_date) BETWEEN '$date_from' AND '$date_select'";
                }

                $query .= " ORDER BY sales_date ASC";

                $result = mysqli_query($mysqli, $query);

                $totalRows = mysqli_num_rows($result);
                $rowCount = 0;
                $totalDiscount = 0; // Menginisialisasi variabel total diskon

                while ($row = mysqli_fetch_assoc($result)) {
                    $rowCount++;
                    // Menambahkan nilai diskon dari setiap baris ke total diskon
                    $totalDiscount += floatval($row['sales_discount']);
                ?>
                    <tr>
                        <td class="border border-2 p-2">Expense</td>
                        <td class="border border-2 p-2">Promotion Sales with invoice #<?php echo $row['sales_transaction_id'] ?></td>
                        <td class="border border-2 p-2">
                            <input disabled class="account_cash bg-transparent border-0" type="text" value="<?php echo "Rp " . number_format(floatval($row['sales_discount']), 0, ',', '.'); ?>">
                        </td>
                        <td class="border border-2 p-2">-</td>
                    </tr>
                    <?php
                }

                // Query for COGS expense
                $product_query = "SELECT product_sku FROM product";
                $product_result = mysqli_query($mysqli, $product_query);

                while ($product_row = mysqli_fetch_assoc($product_result)) {
                    $product_sku = $product_row['product_sku'];
                    $balance = 0; // Inisialisasi saldo

                    $query = "SELECT 
                        transaction_date,
                        description,
                        purchase_amount,
                        sales_amount
                    FROM (
                        SELECT 
                            sales_date AS transaction_date,
                            CONCAT('Penjualan - Rp ', purchase_price * product_qty) AS description,
                            0 AS purchase_amount,
                            purchase_price AS sales_amount
                        FROM 
                            sales_detail
                        WHERE 
                            product_sku = '$product_sku'
                    ) AS sales_transactions";

                    if ($date_from && $date_select) {
                        $query .= " WHERE DATE(transaction_date) BETWEEN '$date_from' AND '$date_select'";
                    }

                    $query .= " ORDER BY transaction_date DESC";

                    $result = mysqli_query($mysqli, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        $totalRows = mysqli_num_rows($result);
                        $rowCount = 0;

                        while ($row = mysqli_fetch_assoc($result)) {
                            $rowCount++;
                            // Update saldo
                            $balance += $row['purchase_amount'] + $row['sales_amount'];
                            // Jika ini adalah baris terakhir, tampilkan saldo
                            if ($rowCount == $totalRows) {
                    ?>
                                <tr>
                                    <td class="border border-2 p-2">COGS</td>
                                    <td class="border border-2 p-2"><?php echo $product_sku ?> - <?php echo $product_name ?></td>
                                    <td class="border border-2 p-2">
                                        <input disabled class="account_cash bg-transparent border-0" type="text" value="<?php echo "Rp " . number_format($balance, 0, ',', '.'); ?>">
                                    </td>
                                    <td class="border border-2 p-2">-</td>
                                </tr>
                <?php
                            }
                        }
                    }
                }
                ?>

                <tr>
                    <td class="border border-2 p-2 fw-bold" colspan="2">Total</td>
                    <td class="border border-2 p-2 fw-bold" id="total_display"></td>
                    <td class="border border-2 p-2 fw-bold" id="total_credit_display"></td>
                </tr>
            </table>
        </div>
    </main>
    <!-- ========== END MAIN CONTENT ========== -->

    <!-- JS Global Compulsory  -->
    <script src="./assets/vendor/jquery-migrate/dist/jquery-migrate.min.js"></script>
    <script src="./assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS Implementing Plugins -->
    <script src="./assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside.min.js"></script>
    <script src="./assets/vendor/hs-form-search/dist/hs-form-search.min.js"></script>

    <script src="./assets/vendor/hs-toggle-password/dist/js/hs-toggle-password.js"></script>
    <script src="./assets/vendor/hs-file-attach/dist/hs-file-attach.min.js"></script>
    <script src="./assets/vendor/hs-step-form/dist/hs-step-form.min.js"></script>
    <script src="./assets/vendor/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="./assets/vendor/quill/dist/quill.min.js"></script>
    <script src="./assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
    <script src="./assets/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="./assets/vendor/datatables.net.extensions/select/select.min.js"></script>
    <script src="./assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="./assets/vendor/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="./assets/vendor/jszip/dist/jszip.min.js"></script>
    <script src="./assets/vendor/pdfmake/build/pdfmake.min.js"></script>
    <script src="./assets/vendor/pdfmake/build/vfs_fonts.js"></script>
    <script src="./assets/vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="./assets/vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="./assets/vendor/datatables.net-buttons/js/buttons.colVis.min.js"></script>
    <script src="./assets/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="./assets/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="./assets/vendor/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="./assets/vendor/hs-nav-scroller/dist/hs-nav-scroller.min.js"></script>
    <script src="./assets/vendor/daterangepicker/moment.min.js"></script>
    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>
    <script src="./assets/js/hs.theme-appearance-charts.js"></script>

    <!-- JS Plugins Init. -->
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            HSCore.components.HSDatatables.init($('#datatable'), {
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'd-none'
                    },
                    {
                        extend: 'excel',
                        className: 'd-none'
                    },
                    {
                        extend: 'csv',
                        className: 'd-none'
                    },
                    {
                        extend: 'pdf',
                        className: 'd-none'
                    },
                    {
                        extend: 'print',
                        className: 'd-none'
                    },
                ],
                select: {
                    style: 'multi',
                    selector: 'td:first-child input[type="checkbox"]',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: `<div class="text-center p-4">
              <img class="mb-3" src="./assets/svg/illustrations/oc-error.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="default">
              <img class="mb-3" src="./assets/svg/illustrations-light/oc-error.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="dark">
            <p class="mb-0">No data to show</p>
            </div>`
                }
            });

            const datatable = HSCore.components.HSDatatables.getItem(0)

            $('#export-copy').click(function() {
                datatable.button('.buttons-copy').trigger()
            });

            $('#export-excel').click(function() {
                datatable.button('.buttons-excel').trigger()
            });

            $('#export-csv').click(function() {
                datatable.button('.buttons-csv').trigger()
            });

            $('#export-pdf').click(function() {
                datatable.button('.buttons-pdf').trigger()
            });

            $('#export-print').click(function() {
                datatable.button('.buttons-print').trigger()
            });

            $('.js-datatable-filter').on('change', function() {
                var $this = $(this),
                    elVal = $this.val(),
                    targetColumnIndex = $this.data('target-column-index');

                datatable.column(targetColumnIndex).search(elVal).draw();
            });
        });
    </script>

    <!-- JS Plugins Init. -->
    <script>
        (function() {
            window.onload = function() {


                // INITIALIZATION OF NAVBAR VERTICAL ASIDE
                // =======================================================
                new HSSideNav('.js-navbar-vertical-aside').init()


                // INITIALIZATION OF FORM SEARCH
                // =======================================================
                new HSFormSearch('.js-form-search')


                // INITIALIZATION OF BOOTSTRAP DROPDOWN
                // =======================================================
                HSBsDropdown.init()


                // INITIALIZATION OF SELECT
                // =======================================================
                HSCore.components.HSTomSelect.init('.js-select')


                // INITIALIZATION OF FILE ATTACHMENT
                // =======================================================
                new HSFileAttach('.js-file-attach')


                // INITIALIZATION OF QUILLJS EDITOR
                // =======================================================
                HSCore.components.HSQuill.init('.js-quill')


                // INITIALIZATION OF DROPZONE
                // =======================================================
                HSCore.components.HSDropzone.init('.js-dropzone')


                // INITIALIZATION OF STEP FORM
                // =======================================================
                new HSStepForm('.js-step-form', {
                    finish: () => {
                        document.getElementById("createProjectStepFormProgress").style.display = 'none'
                        document.getElementById("createProjectStepFormContent").style.display = 'none'
                        document.getElementById("createProjectStepDetails").style.display = 'none'
                        document.getElementById("createProjectStepTerms").style.display = 'none'
                        document.getElementById("createProjectStepMembers").style.display = 'none'
                        document.getElementById("createProjectStepSuccessMessage").style.display = 'block'
                        const formContainer = document.getElementById('formContainer')
                    }
                })
            }
        })()
    </script>

    <!-- Style Switcher JS -->

    <script>
        (function() {
            // STYLE SWITCHER
            // =======================================================
            const $dropdownBtn = document.getElementById('selectThemeDropdown') // Dropdowon trigger
            const $variants = document.querySelectorAll(`[aria-labelledby="selectThemeDropdown"] [data-icon]`) // All items of the dropdown

            // Function to set active style in the dorpdown menu and set icon for dropdown trigger
            const setActiveStyle = function() {
                $variants.forEach($item => {
                    if ($item.getAttribute('data-value') === HSThemeAppearance.getOriginalAppearance()) {
                        $dropdownBtn.innerHTML = `<i class="${$item.getAttribute('data-icon')}" />`
                        return $item.classList.add('active')
                    }

                    $item.classList.remove('active')
                })
            }

            // Add a click event to all items of the dropdown to set the style
            $variants.forEach(function($item) {
                $item.addEventListener('click', function() {
                    HSThemeAppearance.setAppearance($item.getAttribute('data-value'))
                })
            })

            // Call the setActiveStyle on load page
            setActiveStyle()

            // Add event listener on change style to call the setActiveStyle function
            window.addEventListener('on-hs-appearance-change', function() {
                setActiveStyle()
            })
        })()
    </script>

    <script>
        (function() {
            window.onload = function() {


                // INITIALIZATION OF NAVBAR VERTICAL ASIDE
                // =======================================================
                new HSSideNav('.js-navbar-vertical-aside').init()


                // INITIALIZATION OF FORM SEARCH
                // =======================================================
                new HSFormSearch('.js-form-search')


                // INITIALIZATION OF BOOTSTRAP DROPDOWN
                // =======================================================
                HSBsDropdown.init()


                // INITIALIZATION OF NAV SCROLLER
                // =======================================================
                new HsNavScroller('.js-nav-scroller')


                // INITIALIZATION OF SELECT
                // =======================================================
                HSCore.components.HSTomSelect.init('.js-select')


                // INITIALIZATION OF CIRCLES
                // =======================================================
                const colors = () => {
                    return [HSThemeAppearance.getAppearance() === 'dark' ? '#34383b' : '#e7eaf3', '#377dff']
                }

                setTimeout(() => {
                    document.querySelectorAll('.js-circle').forEach(item => {
                        HSCore.components.HSCircles.init(item, {
                            colors: colors()
                        })
                    })

                    window.addEventListener('on-hs-appearance-change', () => {
                        HSCore.components.HSCircles.getItems().forEach(circle => {
                            circle.updateColors(colors())
                        })
                    })
                })


                // INITIALIZATION OF CLIPBOARD
                // =======================================================
                HSCore.components.HSClipboard.init('.js-clipboard')


                // INITIALIZATION OF CHARTJS
                // =======================================================
                HSCore.components.HSChartJS.init('.js-chart')


                // INITIALIZATION OF MATRIX CHARTJS WITH CHARTJS MATRIX PLUGIN
                // =======================================================
                function generateHoursData() {
                    var data = [];
                    var dt = moment().subtract(365, 'days').startOf('day');
                    var end = moment().startOf('day');
                    while (dt <= end) {
                        data.push({
                            x: dt.format('YYYY-MM-DD'),
                            y: dt.format('e'),
                            d: dt.format('YYYY-MM-DD'),
                            v: Math.random() * 24
                        });
                        dt = dt.add(1, 'day');
                    }
                    return data;
                }

                HSCore.components.HSChartMatrixJS.init(document.querySelector('.js-chart-matrix'), {
                    data: {
                        datasets: [{
                            label: 'Commits',
                            data: generateHoursData(),
                            width(c) {
                                const a = c.chart.chartArea || {};
                                return (a.right - a.left) / 70;
                            },
                            height(c) {
                                const a = c.chart.chartArea || {};
                                return (a.bottom - a.top) / 7;
                            }
                        }]
                    },
                    options: {
                        aspectRatio: 5,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    title: function() {
                                        return '';
                                    },
                                    label: function(item) {
                                        var v = item.dataset.data[item.datasetIndex]

                                        if (v.v.toFixed() > 0) {
                                            return '<span class="fw-semibold">' + v.v.toFixed() + 'hours</span> on ' + v.d;
                                        } else {
                                            return '<span class="fw-semibold">No time</span> on ' + v.d;
                                        }
                                    }
                                }
                            },
                        },
                        scales: {
                            y: {
                                type: 'time',
                                offset: true,
                                time: {
                                    unit: 'day',
                                    round: 'day',
                                    isoWeekday: 1,
                                    parser: 'i',
                                    displayFormats: {
                                        day: 'iiiiii'
                                    }
                                },
                                reverse: true,
                                ticks: {
                                    font: {
                                        size: 12,
                                    },
                                    maxTicksLimit: 5,
                                    color: "rgba(22, 52, 90, 0.5)",
                                    maxRotation: 0,
                                    autoSkip: true
                                },
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                    tickLength: 0
                                }
                            },
                            x: {
                                type: 'time',
                                position: 'bottom',
                                offset: true,
                                time: {
                                    unit: 'week',
                                    round: 'week',
                                    isoWeekday: 1,
                                    displayFormats: {
                                        week: 'MMM dd'
                                    }
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                    },
                                    maxTicksLimit: 5,
                                    color: "rgba(22, 52, 90, 0.5)",
                                    maxRotation: 0,
                                    autoSkip: true
                                },
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                    tickLength: 0,
                                }
                            }
                        }
                    }
                })


                HSCore.components.HSChartJS.init('#updatingDoughnutChart')
                const updatingDoughnutChart = HSCore.components.HSChartJS.getItem('updatingDoughnutChart')

                // Datasets for chart, can be loaded from AJAX request
                const updatingDoughnutChartDatasets = [
                    [
                        [45, 25, 30]
                    ]
                ]

                // Set datasets for chart when page is loaded
                const setDataChart = function() {
                    updatingDoughnutChart.data.datasets.forEach(function(dataset, key) {
                        dataset.data = updatingDoughnutChartDatasets[0][key];
                    })

                    updatingDoughnutChart.update()
                }

                setDataChart()

                window.addEventListener('on-hs-appearance-change', e => {
                    setDataChart()
                })

                // Call when tab is clicked
                document.querySelectorAll('[data-bs-toggle="chart-doughnut"]').forEach(item => {
                    item.addEventListener('click', e => {
                        let keyDataset = e.currentTarget.getAttribute('data-datasets')

                        // Update datasets for chart
                        updatingDoughnutChart.data.datasets.forEach(function(dataset, key) {
                            dataset.data = updatingDoughnutChartDatasets[keyDataset][key]
                        })
                        updatingDoughnutChart.update()
                    })
                })


                // INITIALIZATION OF FLATPICKR
                // =======================================================
                HSCore.components.HSFlatpickr.init('.js-flatpickr')
            }
        })()
    </script>

    <script>
        var inputs = document.querySelectorAll(".account_cash");

        var total = 0;

        inputs.forEach(function(input) {
            var value = input.value.replace("Rp ", "").replace(/\./g, "").replace(",", ".");

            total += parseFloat(value) || 0;
        });

        var totalDisplay = document.getElementById("total_display");

        totalDisplay.textContent = "Rp " + total.toLocaleString('id-ID', {
            minimumFractionDigits: 2
        });
    </script>

    <script>
        var inputs = document.querySelectorAll(".account_credit");

        var totalCredit = 0;

        inputs.forEach(function(input) {
            var value = input.value.replace("Rp ", "").replace(/\./g, "").replace(",", ".");

            totalCredit += parseFloat(value) || 0;
        });

        var totalCreditDisplay = document.getElementById("total_credit_display");

        totalCreditDisplay.textContent = "Rp " + totalCredit.toLocaleString('id-ID', {
            minimumFractionDigits: 2
        });
    </script>

    <!-- End Style Switcher JS -->
</body>