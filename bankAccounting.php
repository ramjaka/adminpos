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
                <div class="row align-items-center">
                    <div class="col-sm mb-2 mb-sm-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-no-gutter">
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:;">Bank</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Accounting</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Accounting</h1>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <h2 class="h4 mb-3">Ledger</h2>

            <!-- Folders -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 mb-3">
                <div class="col mb-3 mb-lg-4">
                    <!-- Card -->
                    <div class="card card-sm card-hover-shadow h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-gem fs-2 text-body me-2"></i>

                                <h5 class="text-truncate ms-2 mb-0">Assets</h5>
                            </div>
                        </div>
                        <a class="stretched-link" href="ledgerAssets.php"></a>
                    </div>
                    <!-- End Card -->
                </div>
                <!-- End Col -->

                <div class="col mb-3 mb-lg-4">
                    <!-- Card -->
                    <div class="card card-sm card-hover-shadow h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-wallet2 fs-2 text-body me-2"></i>

                                <h5 class="text-truncate ms-2 mb-0">Liability</h5>
                            </div>
                        </div>
                        <a class="stretched-link" href="ledgerLiability.php"></a>
                    </div>
                    <!-- End Card -->
                </div>
                <!-- End Col -->

                <div class="col mb-3 mb-lg-4">
                    <!-- Card -->
                    <div class="card card-sm card-hover-shadow h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-piggy-bank fs-2 text-body me-2"></i>

                                <h5 class="text-truncate ms-2 mb-0">Equity</h5>
                            </div>
                        </div>
                        <a class="stretched-link" href="ledgerEquity.php"></a>
                    </div>
                    <!-- End Card -->
                </div>
                <!-- End Col -->

                <div class="col mb-3 mb-lg-4">
                    <!-- Card -->
                    <div class="card card-sm card-hover-shadow h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-currency-dollar fs-2 text-body me-2"></i>

                                <h5 class="text-truncate ms-2 mb-0">Revenue</h5>
                            </div>
                        </div>
                        <a class="stretched-link" href="ledgerRevenue.php"></a>
                    </div>
                    <!-- End Card -->
                </div>
                <!-- End Col -->

                <div class="col mb-3 mb-lg-4">
                    <!-- Card -->
                    <div class="card card-sm card-hover-shadow h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash-coin fs-2 text-body me-2"></i>

                                <h5 class="text-truncate ms-2 mb-0">Expense</h5>
                            </div>
                        </div>
                        <a class="stretched-link" href="ledgerExpense.php"></a>
                    </div>
                    <!-- End Card -->
                </div>
                <!-- End Col -->
            </div>
            <!-- End Folders -->

            <h2 class="h4 mb-3">Trial Balance</h2>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 mb-3">
                <div class="col mb-3 mb-lg-4">
                    <!-- Card -->
                    <div class="card card-sm card-hover-shadow h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="fa-light fa-scale-balanced fs-2 text-body me-2 my-1"></i>

                                <h5 class="text-truncate ms-2 mb-0">Filter & Print</h5>
                            </div>
                        </div>
                        <a class="stretched-link" href="trialBalance.php" target="_blank"></a>
                    </div>
                    <!-- End Card -->
                </div>
                <!-- End Col -->
            </div>

            <h2 class="h4 mb-3">Income Statement</h2>
            
            <div class="mb-4 justify-content-end">
                <label>Select Date</label><br>
                <form action="" method="POST">
                    <input id="js-daterangepicker-predefined" name="date_value" class="btn btn-white" readonly>
                    <span class="js-daterangepicker-predefined-preview"></span>
                    <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <button type="submit" name="btn-set-date" class="btn btn-primary">Search</button>
                        <!-- <button type="submit" name="btn-set-showall" class="btn btn-success">Show All</button> -->
                    </div>
                </form>
            </div>

            <!-- Card -->
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
                                <input id="datatableSearch" type="search" class="form-control" placeholder="Search income statement" aria-label="Search users">
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>

                    <div class="d-grid d-sm-flex justify-content-md-end align-items-sm-center gap-2">
                        <!-- Datatable Info -->
                        <div id="datatableCounterInfo" style="display: none;">
                            <div class="d-flex align-items-center">
                                <span class="fs-5 me-3">
                                    <span id="datatableCounter">0</span>
                                    Selected
                                </span>
                                <a class="btn btn-outline-danger btn-sm" href="javascript:;">
                                    <i class="bi-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                        <!-- End Datatable Info -->

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
                                <i class="bi-filter me-1"></i> Filter <span class="badge bg-soft-dark text-dark rounded-circle ms-1">2</span>
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
                                            <div class="mb-4">
                                                <small class="text-cap text-body">Role</small>

                                                <div class="row">
                                                    <div class="col">
                                                        <!-- Check -->
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="" id="usersFilterCheckAll" checked>
                                                            <label class="form-check-label" for="usersFilterCheckAll">
                                                                All
                                                            </label>
                                                        </div>
                                                        <!-- End Check -->
                                                    </div>
                                                    <!-- End Col -->

                                                    <div class="col">
                                                        <!-- Check -->
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="" id="usersFilterCheckEmployee">
                                                            <label class="form-check-label" for="usersFilterCheckEmployee">
                                                                Employee
                                                            </label>
                                                        </div>
                                                        <!-- End Check -->
                                                    </div>
                                                    <!-- End Col -->
                                                </div>
                                                <!-- End Row -->
                                            </div>

                                            <div class="row">
                                                <div class="col-sm mb-4">
                                                    <small class="text-cap text-body">Position</small>

                                                    <!-- Select -->
                                                    <div class="tom-select-custom">
                                                        <select class="js-select js-datatable-filter form-select form-select-sm" data-target-column-index="2" data-hs-tom-select-options='{
                                                        "placeholder": "Any",
                                                        "searchInDropdown": false,
                                                        "hideSearch": true,
                                                        "dropdownWidth": "10rem"
                                                        }'>
                                                            <option value="">Any</option>
                                                            <option value="Accountant">Accountant</option>
                                                            <option value="Co-founder">Co-founder</option>
                                                            <option value="Designer">Designer</option>
                                                            <option value="Developer">Developer</option>
                                                            <option value="Director">Director</option>
                                                        </select>
                                                        <!-- End Select -->
                                                    </div>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col-sm mb-4">
                                                    <small class="text-cap text-body">Status</small>

                                                    <!-- Select -->
                                                    <div class="tom-select-custom">
                                                        <select class="js-select js-datatable-filter form-select form-select-sm" data-target-column-index="4" data-hs-tom-select-options='{
                                                        "placeholder": "Any status",
                                                        "searchInDropdown": false,
                                                        "hideSearch": true,
                                                        "dropdownWidth": "10rem"
                                                        }'>
                                                            <option value="">Any status</option>
                                                            <option value="Completed" data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-success"></span>Completed</span>'>Completed</option>
                                                            <option value="In progress" data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-warning"></span>In progress</span>'>In progress</option>
                                                            <option value="To do" data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-danger"></span>To do</span>'>To do</option>
                                                        </select>
                                                    </div>
                                                    <!-- End Select -->
                                                </div>
                                                <!-- End Col -->

                                                <div class="col-12 mb-4">
                                                    <small class="text-cap text-body">Members</small>

                                                    <!-- Select -->
                                                    <div class="tom-select-custom">
                                                        <select class="js-select form-select" autocomplete="off" multiple data-hs-tom-select-options='{
                                      "singleMultiple": true,
                                      "hideSelected": false,
                                      "placeholder": "Select member"
                                    }'>
                                                            <option label="empty"></option>
                                                            <option value="AH" selected data-option-template='<span class="d-flex align-items-center"><img class="avatar avatar-xss avatar-circle me-2" src="./assets/img/160x160/img10.jpg" alt="Image Description" /><span class="text-truncate">Amanda Harvey</span></span>'>Amanda Harvey</option>
                                                            <option value="DH" selected data-option-template='<span class="d-flex align-items-center"><img class="avatar avatar-xss avatar-circle me-2" src="./assets/img/160x160/img3.jpg" alt="Image Description" /><span class="text-truncate">David Harrison</span></span>'>David Harrison</option>
                                                            <option value="SK" data-option-template='<span class="d-flex align-items-center"><img class="avatar avatar-xss avatar-circle me-2" src="./assets/img/160x160/img4.jpg" alt="Image Description" /><span class="text-truncate">Sam Kart</span></span>'>Sam Kart</option>
                                                            <option value="FH" data-option-template='<span class="d-flex align-items-center"><img class="avatar avatar-xss avatar-circle me-2" src="./assets/img/160x160/img5.jpg" alt="Image Description" /><span class="text-truncate">Finch Hoot</span></span>'>Finch Hoot</option>
                                                            <option value="CQ" selected data-option-template='<span class="d-flex align-items-center"><img class="avatar avatar-xss avatar-circle me-2" src="./assets/img/160x160/img6.jpg" alt="Image Description" /><span class="text-truncate">Costa Quinn</span></span>'>Costa Quinn</option>
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
                    <table id="datatable" class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table table-striped table-hover" data-hs-datatables-options='{
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
                        <thead class="thead-light">
                            <tr>
                                <th class="table-column-pe-0">Type</th>
                                <th>Transaction Id</th>
                                <th>Bank Account</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Transaction Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $sql = "SELECT
                            'Income' AS type,
                            m.transaction_id,
                            m.bank_account_number,
                            m.bank_account_name,
                            m.debt AS amount,
                            m.transaction_description,
                            m.mutation_created_at
                        FROM
                            mutation m
                        WHERE
                            m.debt <> ''";

                            if ($date_from && $date_select) {
                                $sql .= " AND DATE(m.mutation_created_at) BETWEEN '$date_from' AND '$date_select'";
                            }

                            $sql .= " UNION ALL
                
                        SELECT
                            'Expense' AS type,
                            m.transaction_id,
                            m.bank_account_number,
                            m.bank_account_name,
                            m.credit AS amount,
                            m.transaction_description,
                            m.mutation_created_at
                        FROM
                            mutation m
                        WHERE
                            m.credit <> ''";

                            if ($date_from && $date_select) {
                                $sql .= " AND DATE(m.mutation_created_at) BETWEEN '$date_from' AND '$date_select'";
                            }

                            $sql .= " UNION ALL
                
                        SELECT
                            'Total Income' AS type,
                            '' AS transaction_id,
                            '' AS bank_account_number,
                            '' AS bank_account_name,
                            SUM(CAST(m.debt AS DECIMAL(10,2))) AS amount,
                            '' AS transaction_description,
                            NULL AS mutation_created_at
                        FROM
                            mutation m
                        WHERE
                            m.debt <> ''";

                            if ($date_from && $date_select) {
                                $sql .= " AND DATE(m.mutation_created_at) BETWEEN '$date_from' AND '$date_select'";
                            }

                            $sql .= " UNION ALL
                
                        SELECT
                            'Total Expense' AS type,
                            '' AS transaction_id,
                            '' AS bank_account_number,
                            '' AS bank_account_name,
                            SUM(CAST(m.credit AS DECIMAL(10,2))) AS amount,
                            '' AS transaction_description,
                            NULL AS mutation_created_at
                        FROM
                            mutation m
                        WHERE
                            m.credit <> ''";

                            if ($date_from && $date_select) {
                                $sql .= " AND DATE(m.mutation_created_at) BETWEEN '$date_from' AND '$date_select'";
                            }

                            $sql .= " UNION ALL
                
                        SELECT
                            'Profit/Loss' AS type,
                            '' AS transaction_id,
                            '' AS bank_account_number,
                            '' AS bank_account_name,
                            ((SELECT COALESCE(SUM(CAST(debt AS DECIMAL(10,2))), 0) FROM mutation WHERE DATE(mutation_created_at) BETWEEN '$date_from' AND '$date_select') - (SELECT COALESCE(SUM(CAST(credit AS DECIMAL(10,2))), 0) FROM mutation WHERE DATE(mutation_created_at) BETWEEN '$date_from' AND '$date_select')) AS amount,
                            '' AS transaction_description,
                            NULL AS mutation_created_at
                            
                            ORDER BY mutation_created_at DESC";

                            $result = $mysqli->query($sql);

                            if ($result->num_rows > 0) { ?>
                                <?php while ($row = $result->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td class="table-column-pe-0">
                                            <?php echo $row['type'] ?>
                                        </td>
                                        <td>
                                            <?php echo $row['transaction_id'] ?>
                                        </td>
                                        <td>
                                            <?php echo $row['bank_account_name'] ?><br>
                                            <?php echo $row['bank_account_number'] ?>
                                        </td>
                                        <td>
                                            <?php echo $row['transaction_description'] ?>
                                        </td>
                                        <td>
                                            <?php echo "Rp " . number_format(floatval($row['amount']), 0, ',', '.'); ?>
                                        </td>
                                        <td>
                                            <?php if ($row['mutation_created_at'] === NULL) { ?>

                                            <?php } else { ?>
                                                <?php echo date('d-m-Y', strtotime($row['mutation_created_at'])); ?><br><?php echo date('H:i:s', strtotime($row['mutation_created_at'])); ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
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
            <!-- End Card -->

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

</html>