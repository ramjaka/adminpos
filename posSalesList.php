<?php
require_once 'controller/Session.php';

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

$page = 'pos-sales-list';
$page_access = array('1', '3');

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

$sql = "SELECT COUNT(*) AS total_rows FROM sales WHERE YEAR(sales_created_at) = YEAR(NOW()) AND sales_status = 'active'";

$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $sales_counter = $row["total_rows"];
}

// graph year
if (isset($_GET['year']) && !empty($_GET['year'])) {
    $year = $_GET['year'];
} else {
    $year = date("Y");
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
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:;">POS</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Sales List</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Sales List <span class="badge bg-soft-dark text-dark ms-2"><?php echo $sales_counter ?></span></h1>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <!-- Card -->
            <div class="card mb-3 mb-lg-5">
                <!-- Header -->
                <div class="card-header card-header-content-between">
                    <div>
                        <h6 class="card-subtitle mb-0">This year graph</h6>

                        <div class="dropdown">
                            <span class="badge bg-soft-info text-info">
                                <span class="legend-indicator bg-info"></span>Income
                            </span>
                            <span class="badge bg-soft-danger text-danger">
                                <span class="legend-indicator bg-danger"></span>Expense
                            </span>
                        </div>
                    </div>

                    <!-- Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Year
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="max-height: 200px; overflow-y: auto;">
                            <a class="dropdown-item" href="posSalesList.php?year=2024">2024</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2025">2025</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2026">2026</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2027">2027</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2028">2028</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2029">2029</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2030">2030</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2031">2031</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2032">2032</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2033">2033</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2034">2034</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2035">2035</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2036">2036</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2037">2037</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2038">2038</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2039">2039</a>
                            <a class="dropdown-item" href="posSalesList.php?year=2040">2040</a>
                        </div>
                    </div>
                    <!-- End Dropdown -->
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <!-- Bar Chart -->
                    <div class="chartjs-custom" style="height: 18rem;">
                        <?php
                        $sql = "SELECT 
                                months.month AS bulan,
                                COALESCE(SUM(mutation.debt), 0) AS total_debt,
                                COALESCE(SUM(mutation.credit), 0) AS total_credit
                            FROM
                                (SELECT 1 AS month
                                UNION SELECT 2
                                UNION SELECT 3
                                UNION SELECT 4
                                UNION SELECT 5
                                UNION SELECT 6
                                UNION SELECT 7
                                UNION SELECT 8
                                UNION SELECT 9
                                UNION SELECT 10
                                UNION SELECT 11
                                UNION SELECT 12) AS months
                            LEFT JOIN
                                mutation ON MONTH(mutation.mutation_created_at) = months.month AND YEAR(mutation.mutation_created_at) = $year
                            GROUP BY
                                months.month";

                        $result = $mysqli->query($sql);

                        if ($result->num_rows > 0) {
                            $total_debt_array = [];
                            $total_credit_array = [];
                            while ($row = $result->fetch_assoc()) {
                                $total_debt_array[] = $row['total_debt'];
                                $total_credit_array[] = $row['total_credit'];
                            }
                        } else {
                            // Jika tidak ada hasil dari query, inisialisasi array dengan nilai 0
                            $total_debt_array = array_fill(0, 12, 0);
                            $total_credit_array = array_fill(0, 12, 0);
                        }
                        ?>

                        <canvas id="project" class="js-chart" data-hs-chartjs-options='{
                        "type": "line",
                        "data": {
                            "labels": ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                            "datasets": [{
                                "data": [<?php echo implode(",", $total_debt_array); ?>],
                                "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                                "borderColor": "#00c9db",
                                "borderWidth": 2,
                                "pointRadius": 0,
                                "pointBorderColor": "#fff",
                                "pointBackgroundColor": "#00c9db",
                                "pointHoverRadius": 0,
                                "hoverBorderColor": "#fff",
                                "hoverBackgroundColor": "#00c9db",
                                "tension": 0.4
                            },
                            {
                                "data": [<?php echo implode(",", $total_credit_array); ?>],
                                "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                                "borderColor": "#ED4C78",
                                "borderWidth": 2,
                                "pointRadius": 0,
                                "pointBorderColor": "#fff",
                                "pointBackgroundColor": "#ED4C78",
                                "pointHoverRadius": 0,
                                "hoverBorderColor": "#fff",
                                "hoverBackgroundColor": "#ED4C78",
                                "tension": 0.4
                            }]
                        },
                        "options": {
                            "gradientPosition": {"y1": 200},
                            "scales": {
                                "y": {
                                    "grid": {
                                        "color": "#e7eaf3",
                                        "drawBorder": false,
                                        "zeroLineColor": "#e7eaf3"
                                    },
                                    "ticks": {
                                        "min": 0,
                                        "max": 100,
                                        "stepSize": 20,
                                        "color": "#97a4af",                                
                                        "font": {
                                            "family": "Open Sans, sans-serif"
                                        },
                                        "padding": 10,
                                        "postfix": "k"
                                    }
                                },
                                "x": {
                                    "grid": {
                                        "display": false,
                                        "drawBorder": false
                                    },
                                    "ticks": {
                                        "color": "#97a4af",
                                        "font": {
                                            "family": "Open Sans, sans-serif"
                                        },
                                        "padding": 5
                                    }
                                }
                            },
                            "plugins": {
                                "tooltip": {
                                    "prefix": "Rp",
                                    "postfix": "",
                                    "hasIndicator": true,
                                    "mode": "index",
                                    "intersect": false,
                                    "lineMode": true,
                                    "lineWithLineColor": "rgba(19, 33, 68, 0.075)"
                                }
                            },
                            "hover": {
                                "mode": "nearest",
                                "intersect": true
                            }
                        }
                    }'></canvas>

                    </div>
                    <!-- End Bar Chart -->
                </div>
                <!-- End Body -->
            </div>
            <!-- End Card -->

            <div class="mb-4 justify-content-end">
                <label>Transaction Date</label><br>
                <form action="posSalesList.php" method="POST">
                    <input id="js-daterangepicker-predefined" name="date_value" class="btn btn-white" readonly>
                    <span class="js-daterangepicker-predefined-preview"></span>
                    <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                        <button type="submit" name="btn-set-date" class="btn btn-primary">Search</button>
                        <button type="submit" name="btn-set-showall" class="btn btn-success">Show All</button>
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
                                <input id="datatableSearch2" type="search" class="form-control" placeholder="Search sales" aria-label="Search users">
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
                    <table id="datatable2" class="table table-lg table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
                        "columnDefs": [{
                            "targets": [0],
                            "orderable": false
                            }],
                        "order": [],
                        "info": {
                            "totalQty": "#datatableWithPaginationInfoTotalQty2"
                        },
                        "search": "#datatableSearch2",
                        "entries": "#datatableEntries2",
                        "pageLength": 15,
                        "isResponsive": false,
                        "isShowPaging": false,
                        "pagination": "datatablePagination2"
                        }'>
                        <thead class="thead-light">
                            <tr>
                                <th class="table-column-pe-0">Order</th>
                                <th>Date</th>
                                <th>Bank Account</th>
                                <th>Payment method</th>
                                <th>Payment status</th>
                                <th>promotion</th>
                                <th>subtotal</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $sql = "SELECT 
                                    s.*, 
                                    r.receivable_status, 
                                    b.bank_account_name, 
                                    b.bank_account_number, 
                                    b.bank_account_holder
                                    FROM 
                                        sales s
                                    LEFT JOIN 
                                        receivable r ON s.receivable_id = r.receivable_id
                                    JOIN 
                                        bank_account b ON s.bank_account_number = b.bank_account_number";

                            if ($date_from && $date_select) {
                                $sql .= " WHERE DATE(s.sales_date) BETWEEN '$date_from' AND '$date_select'";
                            }

                            $sql .= " ORDER BY s.sales_created_at DESC";

                            $result = $mysqli->query($sql);

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
                                        <td><?php echo $row['payment_method'] ?></td>
                                        <td>
                                            <span class="badge
                                                    <?php if ($row['receivable_status'] === '1') {
                                                        echo 'bg-soft-warning text-info';
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
                                        <td>
                                            <?php echo $row['promotion_name'] ?><br>
                                            <?php
                                            if (strpos($row['promotion_value'], '%') !== false) {
                                                $promotion = $row['promotion_value'];
                                            } elseif (strpos($row['promotion_value'], '%') !== true) {
                                                $promotion = " Rp " . number_format(floatval($row['promotion_value']), 0, ',', '.');
                                            }
                                            ?>
                                            <span class="fw-bold"><?php echo $promotion ?></span>
                                        </td>
                                        <td><?php echo "Rp " . number_format(floatval($row['sales_subtotal']), 0, ',', '.'); ?></td>
                                        <td><?php echo "Rp " . number_format($row['sales_total'], 0, ',', '.'); ?></td>
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
    <script src="./assets/vendor/hs-nav-scroller/dist/hs-nav-scroller.min.js"></script>

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>
    <script src="./assets/js/hs.theme-appearance-charts.js"></script>

    <!-- JS Plugins Init. -->
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            HSCore.components.HSDatatables.init($('#datatable2'), {
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

    <!-- End Style Switcher JS -->

</body>

</html>