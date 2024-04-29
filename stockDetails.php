<?php
require_once 'controller/Session.php';
$page = 'stock-details';
$page_access = array('1', '8');

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

// stock id
if (isset($_GET['product_sku']) && !empty($_GET['product_sku'])) {
    $product_sku = $_GET['product_sku'];
}

$query = "SELECT * FROM `stock` WHERE product_sku = '$product_sku'";
$result = mysqli_query($mysqli, $query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $selling_price = $row['selling_price'];
    $purchase_price = $row['purchase_price'];
}

// date picker
$date_from = date("Y-m-d");
$date_select = date("Y-m-d");
$transaction_type = 'purchase';

if (isset($_POST["btn-set-date"])) {
    $date_value = $_POST["date_value"];
    $transaction_type = $_POST["transaction_type"];

    $dates = explode(" - ", $date_value);

    $date_from = date("Y-m-d", strtotime(trim($dates[0])));
    $date_select = date("Y-m-d", strtotime(trim($dates[1])));
}

if (isset($_POST["btn-set-showall"])) {
    $transaction_type = $_POST["transaction_type"];
    $date_from = NULL;
    $date_select = NULL;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('components/head.php'); ?>
</head>

<body class="has-navbar-vertical-aside navbar-vertical-aside-show-xl   footer-offset">

    <script src="./assets/js/hs.theme-appearance.js"></script>

    <script src="./assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside-mini-cache.js"></script>

    <!-- ========== HEADER ========== -->

    <?php include_once('components/header.php'); ?>

    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->
    <!-- Navbar Vertical -->

    <?php include_once('components/aside.php'); ?>

    <main id="content" role="main" class="main">
        <!-- Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-sm mb-2 mb-sm-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-no-gutter">
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:;">WMS</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Stock Details</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Stock Details - <?php echo $product_sku ?></h1>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <div class="alert alert-soft-success d-none col-12 mx-auto" role="alert" id="dynamicAlert">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        🎉
                    </div>
                    <div class="flex-grow-1 ms-2" id="alertMessage">
                    </div>
                </div>
            </div>

            <div class="alert alert-soft-danger d-none col-12 mx-auto" role="alert" id="dynamicAlertError">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                    <div class="flex-grow-1 ms-2" id="alertMessageError">
                    </div>
                </div>
            </div>

            <!-- Step Form -->
            <form action="controller/wmsEditStock.php" method="POST" class="js-step-form">
                <!-- Content Step Form -->
                <div class="row">

                    <div class="col-lg-12">
                        <div id="checkoutStepFormContent">
                            <!-- Card -->
                            <div id="checkoutStepDelivery" class="active">
                                <div class="card mb-3 mb-lg-5">

                                    <!-- Body -->
                                    <div class="card-body">
                                        <div class="row">
                                            <input type="hidden" name="product_sku" value="<?php echo $product_sku ?>">
                                            <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                                            <div class="col-sm-12">
                                                <div class="row">

                                                    <div class="col-sm-6">
                                                        <!-- Form -->
                                                        <label for="sellingSalesLabel" class="form-label">Purchase Price</label>
                                                        <div class="mb-4">
                                                            <div class="input-group mb-4">
                                                                <span class="input-group-text">Rp</span>
                                                                <input type="text" class="js-input-mask form-control balance" name="purchase_price" value="<?php echo "" . number_format($row['purchase_price'], 0, ',', '.'); ?>" required>
                                                            </div>
                                                        </div>
                                                        <!-- End Form -->
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <!-- Form -->
                                                        <label for="sellingSalesLabel" class="form-label">Selling Price</label>
                                                        <div class="mb-4">
                                                            <div class="input-group mb-4">
                                                                <span class="input-group-text">Rp</span>
                                                                <input type="text" class="js-input-mask form-control balance" name="selling_price" value="<?php echo "" . number_format($row['selling_price'], 0, ',', '.'); ?>" required>
                                                            </div>
                                                        </div>
                                                        <!-- End Form -->
                                                    </div>
                                                </div>
                                                <!-- End Row -->
                                            </div>
                                        </div>
                                        <!-- End Row -->

                                        <!-- Custom Checkbox -->
                                        <div class="form-check p-0 d-flex justify-content-end">
                                            <button type="submit" name="btn-save-cogs" class="btn btn-primary">
                                                Save changes
                                            </button>
                                        </div>
                                        <!-- End Custom Checkbox -->
                                    </div>
                                    <!-- Body -->
                                </div>
                                <!-- End Card -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Step Form -->
            </form>
            <!-- End Step Form -->

            <div class="justify-content-end">
                <form action="" method="POST">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-lg-4 col-md-12 mb-4">
                                <label>Purchase / Sale</label><br>
                                <!-- Select -->
                                <div class="tom-select-custom">
                                    <select class="js-select form-select" name="transaction_type" autocomplete="off" data-hs-tom-select-options='{
                                        "placeholder": "<div>Purhcases / Sale</div>",
                                        "hideSearch": true,
                                        "width": "20rem"
                                        }'>
                                        <option value="purchase" <?php echo ($transaction_type === 'purchase') ? 'selected' : '' ?>>Purchase</option>
                                        <option value="sale" <?php echo ($transaction_type === 'sale') ? 'selected' : '' ?>>Sale</option>
                                    </select>
                                </div>
                                <!-- End Select -->
                            </div>
                            <div class="col-lg-8 col-md-12 mb-4">
                                <label>Transaction Date</label><br>
                                <input id="js-daterangepicker-predefined" name="date_value" class="btn btn-white" readonly>
                                <span class="js-daterangepicker-predefined-preview"></span>
                                <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <button type="submit" name="btn-set-date" class="btn btn-primary">Search</button>
                                    <button type="submit" name="btn-set-showall" class="btn btn-success">Show All</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card">
                <!-- Header -->
                <div class="card-header card-header-content-md-between">
                    <div class="mb-2 mb-md-0">
                        <form>
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend input-group-text">
                                    <i class="bi-search"></i>
                                </div>
                                <input id="datatableSearch" type="search" class="form-control" placeholder="Search transaction" aria-label="Search users">
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>

                    <div class="d-grid d-sm-flex justify-content-md-end align-items-sm-center gap-2">

                        <!-- Dropdown -->
                        <div class="dropdown">
                            <button type="button" class="btn btn-white btn-sm dropdown-toggle w-100" id="usersExportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi-download me-2"></i> Export
                            </button>

                            <div class="dropdown-menu dropdown-menu-sm-end" aria-labelledby="usersExportDropdown">
                                <span class="dropdown-header">Options</span>
                                <a id="export-copy" class="dropdown-item" href="javascript:;">
                                    <img class="avatar avatar-xss avatar-4x3 me-2" src="./assets/svg/illustrations/copy-icon.svg" alt="Image Description">
                                    Copy
                                </a>
                                <a id="export-print" class="dropdown-item" href="javascript:;">
                                    <img class="avatar avatar-xss avatar-4x3 me-2" src="./assets/svg/illustrations/print-icon.svg" alt="Image Description">
                                    Print
                                </a>
                                <div class="dropdown-divider"></div>
                                <span class="dropdown-header">Download options</span>
                                <a id="export-excel" class="dropdown-item" href="javascript:;">
                                    <img class="avatar avatar-xss avatar-4x3 me-2" src="./assets/svg/brands/excel-icon.svg" alt="Image Description">
                                    Excel
                                </a>
                                <a id="export-csv" class="dropdown-item" href="javascript:;">
                                    <img class="avatar avatar-xss avatar-4x3 me-2" src="./assets/svg/components/placeholder-csv-format.svg" alt="Image Description">
                                    .CSV
                                </a>
                                <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                    <img class="avatar avatar-xss avatar-4x3 me-2" src="./assets/svg/brands/pdf-icon.svg" alt="Image Description">
                                    PDF
                                </a>
                            </div>
                        </div>
                        <!-- End Dropdown -->

                        <!-- Dropdown -->
                        <div class="dropdown">
                            <button type="button" class="btn btn-white btn-sm w-100" id="usersFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi-filter me-1"></i> Filter
                            </button>

                            <div class="dropdown-menu dropdown-menu-sm-end dropdown-card card-dropdown-filter-centered" aria-labelledby="usersFilterDropdown" style="min-width: 22rem;">
                                <!-- Card -->
                                <div class="card">
                                    <div class="card-header card-header-content-between">
                                        <h5 class="card-header-title">Filter users</h5>

                                        <!-- Toggle Button -->
                                        <button type="button" class="btn btn-ghost-secondary btn-icon btn-sm ms-2">
                                            <i class="bi-x-lg"></i>
                                        </button>
                                        <!-- End Toggle Button -->
                                    </div>

                                    <div class="card-body">
                                        <form>
                                            <div class="row">
                                                <div class="col-sm mb-4">
                                                    <small class="text-cap text-body">Payment Method</small>

                                                    <!-- Select -->
                                                    <div class="tom-select-custom">
                                                        <select class="js-select js-datatable-filter form-select form-select-sm" data-target-column-index="2" data-hs-tom-select-options='{
                                                        "placeholder": "Any",
                                                        "searchInDropdown": false,
                                                        "hideSearch": true,
                                                        "dropdownWidth": "10rem"
                                                        }'>
                                                            <option value=" ">Any method</option>
                                                            <?php
                                                            $sql = "SELECT * FROM `bank_account` WHERE bank_account_status = 'active' ORDER BY bank_account_created_at DESC";
                                                            $result = $mysqli->query($sql);

                                                            if ($result->num_rows > 0) {
                                                                while ($row = $result->fetch_assoc()) {
                                                                    if ($row['bank_account_number'] != '123receivables') {
                                                            ?>
                                                                        <option value="<?php echo $row['bank_account_number'] ?>">
                                                                            <?php echo $row['bank_account_name'] ?>
                                                                        </option>
                                                            <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <!-- End Select -->
                                                    </div>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col-sm mb-4">
                                                    <small class="text-cap text-body">Payment Status</small>

                                                    <!-- Select -->
                                                    <div class="tom-select-custom">
                                                        <select class="js-select js-datatable-filter form-select form-select-sm" data-target-column-index="3" data-hs-tom-select-options='{
                                                        "placeholder": "Any status",
                                                        "searchInDropdown": false,
                                                        "hideSearch": true,
                                                        "dropdownWidth": "10rem"
                                                        }'>
                                                            <option value=" ">Any status</option>
                                                            <option value="paid" data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-success"></span>Paid</span>'>Paid</option>
                                                            <option value="debt" data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-warning"></span>Debt</span>'>Debt</option>
                                                        </select>
                                                    </div>
                                                    <!-- End Select -->
                                                </div>
                                                <!-- End Col -->
                                            </div>
                                            <!-- End Row -->

                                            <div class="d-grid">
                                                <a class="btn btn-primary" href="javascript:;">Apply</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- End Card -->
                            </div>
                        </div>
                        <!-- End Dropdown -->
                    </div>
                </div>
                <!-- End Header -->

                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="datatable" class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
                    "columnDefs": [{
                        "targets": [0],
                        "orderable": false
                        }],
                    "order": [],
                    "info": {
                        "totalQty": "#datatableWithPaginationInfoTotalQty"
                    },
                    "search": "#datatableSearch",
                    "entries": "#datatableEntries",
                    "pageLength": 15,
                    "isResponsive": false,
                    "isShowPaging": false,
                    "pagination": "datatablePagination"
                    }'>
                        <?php if ($transaction_type === 'purchase') { ?>
                            <thead class="thead-light">
                                <tr>
                                    <th class="table-column-pe-0">Order</th>
                                    <th>Date</th>
                                    <th>Payment method</th>
                                    <th>Payment status</th>
                                    <th>Stock Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $sql = "SELECT 
                            p.purchase_transaction_id, 
                            p.purchase_date, 
                            p.purchase_total, 
                            p.purchase_status, 
                            d.debt_status, 
                            b.bank_account_name, 
                            b.bank_account_number, 
                            b.bank_account_holder, 
                            pd.product_sku,
                            pd.product_qty,
                            p.purchase_created_at
                            FROM 
                                purchase p
                            LEFT JOIN 
                                debt d ON p.debt_id = d.debt_id
                            JOIN 
                                bank_account b ON p.bank_account_number = b.bank_account_number
                            JOIN
                                purchase_detail pd ON p.purchase_transaction_id = pd.purchase_transaction_id
                            WHERE
                                pd.product_sku = '$product_sku'                                                                                    
                            ";

                                if ($date_from && $date_select) {
                                    $sql .= " AND STR_TO_DATE(p.purchase_date, '%d/%m/%Y') BETWEEN '$date_from' AND '$date_select'";
                                }

                                $sql .= " ORDER BY p.purchase_created_at DESC";

                                $result = $mysqli->query($sql);

                                if ($result->num_rows > 0) { ?>
                                    <?php while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td class="table-column-pe-0">
                                                <a href="wmsPurchaseDetails.php?purchase_transaction_id=<?php echo $row['purchase_transaction_id']; ?>" target="_blank">#<?php echo $row['purchase_transaction_id'] ?></a><br>
                                                <?php if ($row['purchase_status'] === 'canceled') { ?>
                                                    <span class="badge bg-soft-danger text-danger">
                                                        <span class="legend-indicator bg-danger "></span> Canceled
                                                    </span>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $row['purchase_date'] ?></td>
                                            <td>
                                                <?php echo $row['bank_account_name'] ?><br>
                                                <?php echo $row['bank_account_number'] ?><br>
                                                <?php echo $row['bank_account_holder'] ?>
                                            </td>
                                            <td>
                                                <span class="badge
                                                    <?php if ($row['debt_status'] === '1') {
                                                        echo 'bg-soft-warning text-warning';
                                                    } else {
                                                        echo 'bg-soft-success text-success';
                                                    } ?>
                                                ">
                                                    <span class="legend-indicator
                                                    <?php if ($row['debt_status'] === '1') {
                                                        echo 'bg-warning';
                                                    } else {
                                                        echo 'bg-success';
                                                    } ?>
                                                "></span>
                                                    <?php if ($row['debt_status'] === '1') {
                                                        echo 'Debt';
                                                    } else {
                                                        echo 'Paid';
                                                    } ?>
                                                </span>
                                            </td>
                                            <td><?php echo $row['product_qty'] ?></td>
                                            <td><?php echo "Rp " . number_format($row['purchase_total'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                        <?php } ?>

                        <?php if ($transaction_type === 'sale') { ?>
                            <thead class="thead-light">
                                <tr>
                                    <th class="table-column-pe-0">Order</th>
                                    <th>Date</th>
                                    <th>Payment method</th>
                                    <th>Payment status</th>
                                    <th>Stock Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $sql = "SELECT 
                            s.sales_transaction_id, 
                            s.sales_date, 
                            s.sales_total, 
                            s.sales_status, 
                            r.receivable_status, 
                            b.bank_account_name, 
                            b.bank_account_number, 
                            b.bank_account_holder, 
                            sd.product_sku,
                            sd.product_qty,
                            s.sales_created_at
                            FROM 
                                sales s
                            LEFT JOIN 
                                receivable r ON s.receivable_id = r.receivable_id
                            JOIN 
                                bank_account b ON s.bank_account_number = b.bank_account_number
                            JOIN
                                sales_detail sd ON s.sales_transaction_id = sd.sales_transaction_id
                            WHERE
                                sd.product_sku = '$product_sku'                                                                                    
                            ";

                                if ($date_from && $date_select) {
                                    $sql .= " AND STR_TO_DATE(s.sales_date, '%d/%m/%Y') BETWEEN '$date_from' AND '$date_select'";
                                }

                                $sql .= " ORDER BY s.sales_date DESC";

                                $result = $mysqli->query($sql);

                                if ($result->num_rows > 0) { ?>
                                    <?php while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td class="table-column-pe-0">
                                                <a href="posSalesDetails.php?sales_transaction_id=<?php echo $row['sales_transaction_id']; ?>" target="_blank">#<?php echo $row['sales_transaction_id'] ?></a><br>
                                                <?php if ($row['sales_status'] === 'canceled') { ?>
                                                    <span class="badge bg-soft-danger text-danger">
                                                        <span class="legend-indicator bg-danger "></span> Canceled
                                                    </span>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $row['sales_date'] ?></td>
                                            <td>
                                                <?php echo $row['bank_account_name'] ?><br>
                                                <?php echo $row['bank_account_number'] ?><br>
                                                <?php echo $row['bank_account_holder'] ?>
                                            </td>
                                            <td>
                                                <span class="badge
                                                    <?php if ($row['receivable_status'] === '1') {
                                                        echo 'bg-soft-info text-info';
                                                    } else {
                                                        echo 'bg-soft-success text-success';
                                                    } ?>
                                                ">
                                                    <span class="legend-indicator
                                                    <?php if ($row['receivable_status'] === '1') {
                                                        echo 'bg-info';
                                                    } else {
                                                        echo 'bg-success';
                                                    } ?>
                                                "></span>
                                                    <?php if ($row['receivable_status'] === '1') {
                                                        echo 'Receivable';
                                                    } else {
                                                        echo 'Paid';
                                                    } ?>
                                                </span>
                                            </td>
                                            <td><?php echo $row['product_qty'] ?></td>
                                            <td><?php echo "Rp " . number_format($row['sales_total'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                        <?php } ?>
                    </table>
                </div>
                <!-- End Table -->

                <!-- Footer -->
                <div class="card-footer">
                    <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                        <div class="col-sm mb-2 mb-sm-0">
                            <div class="d-flex justify-content-center justify-content-sm-start align-items-center">
                                <span class="me-2">Showing:</span>

                                <!-- Select -->
                                <div class="tom-select-custom">
                                    <select id="datatableEntries" class="js-select form-select form-select-borderless w-auto" autocomplete="off" data-hs-tom-select-options='{
                            "searchInDropdown": false,
                            "hideSearch": true
                          }'>
                                        <option value="10">10</option>
                                        <option value="15" selected>15</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                                <!-- End Select -->

                                <span class="text-secondary me-2">of</span>

                                <!-- Pagination Quantity -->
                                <span id="datatableWithPaginationInfoTotalQty"></span>
                            </div>
                        </div>
                        <!-- End Col -->

                        <div class="col-sm-auto">
                            <div class="d-flex justify-content-center justify-content-sm-end">
                                <!-- Pagination -->
                                <nav id="datatablePagination" aria-label="Activity pagination"></nav>
                            </div>
                        </div>
                        <!-- End Col -->
                    </div>
                    <!-- End Row -->
                </div>
                <!-- End Footer -->
            </div>
        </div>
        <!-- End Content -->

        <!-- Footer -->

        <?php include_once('components/footer.php'); ?>

        <!-- End Footer -->
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
            <p class="mb-0">No transaction data to show</p>
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
                    ],
                    [
                        [35, 50, 15]
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

    <!-- alert -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function fadeOut(element, duration) {
                const interval = 50;
                const steps = duration / interval;
                let currentStep = 0;

                const fadeInterval = setInterval(function() {
                    currentStep++;
                    element.style.opacity = 1 - (currentStep / steps);

                    if (currentStep >= steps) {
                        clearInterval(fadeInterval);
                        element.classList.add('d-none');
                    }
                }, interval);
            }

            function showAlert(alertElementId, alertMessageId, message) {
                const alertElement = document.getElementById(alertElementId);
                const alertMessageElement = document.getElementById(alertMessageId);

                if (alertElement && alertMessageElement) {
                    alertMessageElement.innerText = message;
                    alertElement.classList.remove('d-none');

                    setTimeout(function() {
                        fadeOut(alertElement, 1000);
                    }, 4000);
                } else {
                    console.error('Alert elements not found.');
                }
            }

            const urlParams = new URLSearchParams(window.location.search);
            const successMessage = urlParams.get('successMessage');
            const errorMessage = urlParams.get('errorMessage');

            if (successMessage) {
                showAlert('dynamicAlert', 'alertMessage', successMessage);
            }

            if (errorMessage) {
                showAlert('dynamicAlertError', 'alertMessageError', errorMessage);
            }
        });
    </script>

    <!-- End Style Switcher JS -->
</body>

</html>