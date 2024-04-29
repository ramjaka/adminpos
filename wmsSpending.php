<?php
require_once 'controller/Session.php';
$page = 'wms-spending';
$page_access = array('1', '9');

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
                <div class="row align-items-end">
                    <div class="col-sm mb-2 mb-sm-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-no-gutter">
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:;">WMS</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Spending</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Spending</h1>
                    </div>
                    <!-- End Col -->

                    <div class="col-sm-auto">
                        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal"><i class="bi bi-wallet2 me-1"></i> Add Spending</a>
                    </div>
                    <!-- End Col -->

                    <!-- Modal -->
                    <div id="addMemberModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addMemberModalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addMemberModalTitle">Create Spending</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- form -->
                                    <form action="controller/WmsAddSpending.php" method="POST">
                                        <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-4">
                                                    <label class="form-label" for="spendingName">Spending Name</label>
                                                    <input name="spending_name" type="text" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-4">
                                                    <label for="paymentLabel" class="form-label">Payment</label>
                                                    <!-- Select -->
                                                    <div class="tom-select-custom">
                                                        <select name="bank_account_number" class="js-select form-select text-uppercase" autocomplete="off" data-hs-tom-select-options='{
                                                                "placeholder": "Select bank account..."
                                                            }'>
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
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 mb-4 py-4 maturity-input bg-soft-warning rounded border border-warning">
                                                <label class="form-label" for="phoneNumber">Due Date</label>
                                                <!-- Flatpickr -->
                                                <div id="invoiceDateFlatpickr" class="js-flatpickr flatpickr-custom mb-2" data-hs-flatpickr-options='{
                                                        "appendTo": "#invoiceDateFlatpickr",
                                                        "dateFormat": "Y-m-d",
                                                        "wrap": true
                                                        }'>
                                                    <input type="text" name="maturity" class="flatpickr-custom-form-control form-control maturity-input border-warning" placeholder="Select dates" data-input>
                                                </div>
                                                <span class="text-warning">
                                                    <i class="bi bi-exclamation-octagon"></i> Required maturity input if choosing debt repayment
                                                </span>
                                                <!-- End Flatpickr -->
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="mb-4">
                                                    <label class="form-label" for="phoneNumber">Transaction Date</label>
                                                    <!-- Flatpickr -->
                                                    <div id="invoiceDateFlatpickr1" class="js-flatpickr flatpickr-custom" data-hs-flatpickr-options='{
                                                        "appendTo": "#invoiceDateFlatpickr1",
                                                        "dateFormat": "Y-m-d",
                                                        "wrap": true
                                                        }'>
                                                        <input id="tanggalInput" type="text" name="spending_date" class="flatpickr-custom-form-control form-control" placeholder="Select dates" data-input required>

                                                        <script>
                                                            var today = new Date();
                                                            var day = today.getDate();
                                                            var month = today.getMonth() + 1;
                                                            var year = today.getFullYear();
                                                            var formattedDate = year + '/' + month + '/' + day;

                                                            document.getElementById('tanggalInput').value = formattedDate;
                                                        </script>
                                                    </div>
                                                    <!-- End Flatpickr -->
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label" for="phoneNumber">Total</label>
                                                <div class="input-group mb-4">
                                                    <span class="input-group-text">Rp</span>
                                                    <input id="balance" type="text" class="form-control" name="spending_total" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="mb-4">
                                                    <label class="form-label" for="addressLabel">Description</label>
                                                    <div class="tom-select-custom">
                                                        <select name="spending_description" class="js-select form-select text-uppercase" autocomplete="off" data-hs-tom-select-options='{
                                                                "placeholder": "Select expense..."
                                                            }'>
                                                            <option value="Utility Expense">
                                                                Utility Expenses
                                                            </option>
                                                            <option value="Salary Expenses">
                                                                Salary Expenses
                                                            </option>
                                                            <option value="Office Needs">
                                                                Office Needs Expenses
                                                            </option>
                                                            <option value="Repaid Rent">
                                                                Repaid Rent Expenses
                                                            </option>
                                                            <option value="Prive">
                                                                Prive Expenses
                                                            </option>
                                                            <option value="Other Expenses">
                                                                Other Expenses
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-white me-2" data-bs-dismiss="modal">Close</button>
                                            <button name="btn-save-spending" type="submit" class="btn btn-primary">Save spending</button>
                                        </div>
                                    </form>
                                    <!-- end form -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <div class="alert alert-soft-success d-none" role="alert" id="dynamicAlert">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        🎉
                    </div>
                    <div class="flex-grow-1 ms-2" id="alertMessage">
                    </div>
                </div>
            </div>

            <div class="alert alert-soft-danger d-none" role="alert" id="dynamicAlertError">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                    <div class="flex-grow-1 ms-2" id="alertMessageError">
                    </div>
                </div>
            </div>

            <div class="mb-4 justify-content-end">
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
                                <input id="datatableSearch" type="search" class="form-control" placeholder="Search sales" aria-label="Search users">
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
                                <i class="bi-filter me-1"></i> Filter</span>
                            </button>

                            <div class="dropdown-menu dropdown-menu-sm-end dropdown-card card-dropdown-filter-centered" aria-labelledby="usersFilterDropdown" style="min-width: 22rem;">
                                <!-- Card -->
                                <div class="card">
                                    <div class="card-header card-header-content-between">
                                        <h5 class="card-header-title">Filter sales</h5>

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
                                                        <select class="js-select js-datatable-filter form-select form-select-sm" data-target-column-index="3" data-hs-tom-select-options='{
                                                        "placeholder": "Any",
                                                        "searchInDropdown": false,
                                                        "hideSearch": true,
                                                        "dropdownWidth": "10rem"
                                                        }'>
                                                            <option value=" ">Any payment</option>
                                                            <option value="CASH">CASH</option>
                                                            <option value="BANK TRANSFER">Bank Transfer</option>
                                                            <option value="DEBIT">Debit Card</option>
                                                            <option value="DANA">DANA</option>
                                                            <option value="GoPay">GoPay</option>
                                                            <option value="LinkAja">LinkAja</option>
                                                            <option value="OVO">OVO</option>
                                                            <option value="QRIS">QRIS</option>
                                                            <option value="ShopeePay">ShopeePay</option>
                                                        </select>
                                                        <!-- End Select -->
                                                    </div>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col-sm mb-4">
                                                    <small class="text-cap text-body">Payment Status</small>

                                                    <!-- Select -->
                                                    <div class="tom-select-custom">
                                                        <select class="js-select js-datatable-filter form-select form-select-sm" data-target-column-index="4" data-hs-tom-select-options='{
                                                        "placeholder": "Any status",
                                                        "searchInDropdown": false,
                                                        "hideSearch": true,
                                                        "dropdownWidth": "10rem"
                                                        }'>
                                                            <option value=" ">Any status</option>
                                                            <option value="paid" data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-success"></span>Paid</span>'>Paid</option>
                                                            <option value="receivable" data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-info"></span>Receivable</span>'>Receivable</option>
                                                        </select>
                                                    </div>
                                                    <!-- End Select -->
                                                </div>
                                                <!-- End Col -->

                                                <div class="col-12 mb-4">
                                                    <small class="text-cap text-body">Bank Account</small>

                                                    <!-- Select -->
                                                    <div class="tom-select-custom">
                                                        <select class="js-select js-datatable-filter form-select form-select-sm" data-target-column-index="2" data-hs-tom-select-options='{
                                                        "placeholder": "Any status",
                                                        "searchInDropdown": false,
                                                        "hideSearch": true,
                                                        "dropdownWidth": "10rem"
                                                        }'>
                                                            <option value=" ">Any bank account</option>
                                                            <?php
                                                            $sql = "SELECT * FROM `bank_account` WHERE bank_account_status = 'active' ORDER BY bank_account_created_at DESC";
                                                            $result = $mysqli->query($sql);

                                                            if ($result->num_rows > 0) {
                                                                while ($row = $result->fetch_assoc()) {
                                                                    if ($row['bank_account_number'] != '123debt') {
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
                        <thead class="thead-light">
                            <tr>
                                <th class="table-column-pe-0">Order</th>
                                <th>Spending Name</th>
                                <th>Payment Status</th>
                                <th>Payment Method</th>
                                <th>Description</th>
                                <th>Transaction date</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $sql = "SELECT * FROM `spending` s
                            INNER JOIN `bank_account` b ON s.bank_account_number = b.bank_account_number
                            LEFT JOIN `debt` d ON s.debt_id = d.debt_id WHERE s.spending_status = 'active' 
                            ";

                            if ($date_from && $date_select) {
                                $sql .= " AND DATE(s.spending_date) BETWEEN '$date_from' AND '$date_select'";
                            }

                            $sql .= " ORDER BY s.spending_created_at DESC";

                            $result = $mysqli->query($sql);

                            if ($result->num_rows > 0) { ?>
                                <?php while ($row = $result->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td class="table-column-pe-0">
                                            <a href="wmsSpendingDetails.php?spending_transaction_id=<?php echo $row['spending_transaction_id'] ?>">
                                                #<?php echo $row['spending_transaction_id'] ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php echo $row['spending_name'] ?>
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
                                        <td class="text-uppercase">
                                            <?php echo $row['bank_account_name'] ?>
                                        </td>
                                        <td>
                                            <?php echo $row['spending_description'] ?>
                                        </td>
                                        <td>
                                            <?php echo $row['spending_date'] ?>
                                        </td>
                                        <td>
                                            <?php echo "Rp " . number_format($row['spending_total'], 0, ',', '.'); ?>
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

    <script src="./assets/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="./assets/vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="./assets/vendor/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="./assets/vendor/datatables.net-buttons/js/buttons.print.min.js"></script>

    <script src="./assets/vendor/hs-step-form/dist/hs-step-form.min.js"></script>
    <script src="./assets/vendor/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="./assets/vendor/imask/dist/imask.min.js"></script>
    <script src="./assets/vendor/quill/dist/quill.min.js"></script>
    <script src="./assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
    <!-- JS Implementing Plugins -->
    <script src="./assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside.min.js"></script>
    <script src="./assets/vendor/hs-form-search/dist/hs-form-search.min.js"></script>
    <script src="./assets/vendor/hs-quantity-counter/dist/hs-quantity-counter.min.js"></script>

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>
    <script src="./assets/vendor/flatpickr/dist/flatpickr.min.js"></script>

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

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>

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


                // INITIALIZATION OF INPUT MASK
                // =======================================================
                HSCore.components.HSMask.init('.js-input-mask')


                // INITIALIZATION OF STEP FORM
                // =======================================================
                new HSStepForm('.js-step-form', {
                    finish: () => {
                        document.getElementById("checkoutStepFormProgress").style.display = 'none'
                        document.getElementById("checkoutStepFormContent").style.display = 'none'
                        document.getElementById("checkoutStepOrderSummary").style.display = 'none'
                        document.getElementById("checkoutStepSuccessMessage").style.display = 'block'
                        const formContainer = document.getElementById('formContainer')
                    },
                    onNextStep: function() {
                        scrollToTop()
                    },
                    onPrevStep: function() {
                        scrollToTop()
                    }
                })

                function scrollToTop(el = '.js-step-form') {
                    el = document.querySelector(el)
                    window.scrollTo({
                        top: (el.getBoundingClientRect().top + window.scrollY) - 30,
                        left: 0,
                        behavior: 'smooth'
                    })
                }
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

    <!-- End Style Switcher JS -->

    <script>
        (function() {
            // INITIALIZATION OF FLATPICKR
            // =======================================================
            HSCore.components.HSFlatpickr.init('.js-flatpickr')
        })();
    </script>

    <script>
        // Mendapatkan elemen input
        var input = document.getElementById("counter");

        input.addEventListener("change", function() {
            if (input.value === "0") {
                input.value = "1";
            }
        });

        input.addEventListener("input", function() {
            if (input.value === "0") {
                input.value = "1";
            }
        });
    </script>

    <script>
        (function() {
            // INITIALIZATION OF  QUANTITY COUNTER
            // =======================================================
            new HSQuantityCounter('.js-quantity-counter')
        })();
    </script>

    <!-- rupiah -->
    <script type="text/javascript">
        var rupiah = document.getElementById('balance');
        rupiah.addEventListener('keyup', function(e) {
            rupiah.value = formatRupiah(this.value, 'Rp. ');
        });

        /* Fungsi formatRupiah */
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var paymentSelect = document.querySelector('select[name="bank_account_number"]');
            var maturityInput = document.querySelector('.maturity-input');

            function handlePaymentChange() {
                if (paymentSelect.value === '123debt') {
                    maturityInput.disabled = false;
                    maturityInput.classList.remove('d-none');
                } else {
                    maturityInput.disabled = true;
                    maturityInput.classList.add('d-none');
                }
            }

            paymentSelect.addEventListener('change', handlePaymentChange);
            handlePaymentChange();
        });
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

</body>

</html>