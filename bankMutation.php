<?php
require_once 'controller/Session.php';
$page = 'bank-mutation';
$page_access = array('1', '15');

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
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:;">Bank</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Mutation</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Mutation</h1>
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header card-header-content-md-between">
                    <div class="mb-2 mb-md-0">
                        <form>
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-borderless">
                                <div class="input-group-prepend input-group-text">
                                    <i class="bi-search"></i>
                                </div>
                                <input id="datatableSearch" type="search" class="form-control" placeholder="Search mutation" aria-label="Search users">
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>
                    <!-- End Col -->

                    <div class="d-grid d-sm-flex align-items-sm-center gap-2">
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

                        <!-- Filter Collapse Trigger -->
                        <a class="btn btn-white dropdown-toggle" data-bs-toggle="collapse" href="#filterSearchCollapse" role="button" aria-expanded="false" aria-controls="filterSearchCollapse">
                            <i class="bi-funnel me-1"></i> Filters
                        </a>
                        <!-- End Filter Collapse Trigger -->
                    </div>
                </div>
                <!-- End Header -->

                <!-- Filter Search Collapse -->
                <div class="collapse" id="filterSearchCollapse">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <!-- Form -->
                                <div class="mb-4">
                                    <label for="teamsFilterLabel" class="form-label">Bank Account</label>

                                    <!-- Select -->
                                    <div class="tom-select-custom">
                                        <select class="js-select js-datatable-filter form-select form-select-sm" data-target-column-index="2" data-hs-tom-select-options='{
                                                        "placeholder": "Any",
                                                        "searchInDropdown": false,
                                                        "hideSearch": true,
                                                        "dropdownWidth": "10rem"
                                                        }'>
                                            <option value="debt">Any method</option>
                                            <?php
                                            $sql = "SELECT * FROM `bank_account` WHERE bank_account_status = 'active' ORDER BY bank_account_created_at DESC";
                                            $result = $mysqli->query($sql);

                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                            ?>
                                                    <option value="<?php echo $row['bank_account_number'] ?>">
                                                        <?php echo $row['bank_account_name'] ?>
                                                    </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <!-- End Select -->
                                    </div>
                                    <!-- End Select -->
                                </div>
                                <!-- End Form -->
                            </div>
                            <!-- End Col -->

                            <div class=" col-sm-12 col-md-6 col-lg-6">
                                <!-- Form -->
                                <div class="mb-4">

                                    <!-- Select -->
                                    <div class="tom-select-custom">
                                        <small class="text-cap text-body">Mutation Date</small>

                                        <!-- Select -->
                                        <form action="" method="POST">
                                            <input id="js-daterangepicker-predefined" name="date_value" class="btn btn-white w-100" readonly><br>
                                            <span class="js-daterangepicker-predefined-preview"></span>
                                            <div class="text-end mt-2">
                                                <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                                    <button type="submit" name="btn-set-date" class="btn btn-primary">Search</button>
                                                    <button type="submit" name="btn-set-showall" class="btn btn-success">Show All</button>
                                                </div>
                                            </div>
                                        </form>
                                        <!-- End Select -->
                                    </div>
                                    <!-- End Select -->
                                </div>
                                <!-- End Form -->
                            </div>
                            <!-- End Col -->
                        </div>
                        <!-- End Row -->
                    </div>
                </div>
                <!-- End Filter Search Collapse -->

                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="datatable" class="table table-lg table-borderedless table-thead-bordered table-nowrap table-align-middle card-table table-striped table-hover" data-hs-datatables-options='{
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
                                <th class="table-column-pe-0">Transaction date</th>
                                <th>transaction id</th>
                                <th>Bank Account</th>
                                <th>Payment Status</th>
                                <th>Debt</th>
                                <th>Credit</th>
                                <th>Last Balance</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $sql = "SELECT m.*, b.bank_account_name, b.bank_account_holder, d.debt_id, d.debt_due_date, d.debt_total, d.debt_status, r.receivable_id, r.receivable_due_date, r.receivable_total, r.receivable_status
                            FROM mutation m
                            JOIN bank_account b ON m.bank_account_number = b.bank_account_number
                            LEFT JOIN debt d ON m.debt_id = d.debt_id
                            LEFT JOIN receivable r ON m.receivable_id = r.receivable_id
                            ";

                            if ($date_from && $date_select) {
                                $sql .= " WHERE DATE(m.mutation_created_at) BETWEEN '$date_from' AND '$date_select'";
                            }

                            $sql .= " ORDER BY m.mutation_created_at DESC";

                            $result = $mysqli->query($sql);

                            if ($result->num_rows > 0) { ?>
                                <?php while ($row = $result->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td class="table-column-pe-0">
                                            <?php echo date('d-m-Y', strtotime($row['mutation_created_at'])); ?><br><?php echo date('H:i:s', strtotime($row['mutation_created_at'])); ?>
                                        </td>
                                        <td>
                                            <?php if (strpos($row['transaction_id'], 'A') !== false) { ?>
                                                <a href="addBalanceDetails.php?transaction_id=<?php echo $row['transaction_id'] ?>" target="_blank">
                                                    #<?php echo $row['transaction_id'] ?>
                                                </a>
                                            <?php } ?>
                                            <?php if (strpos($row['transaction_id'], 'S') !== false) { ?>
                                                <a href="posSalesDetails.php?sales_transaction_id=<?php echo $row['transaction_id'] ?>" target="_blank">
                                                    #<?php echo $row['transaction_id'] ?>
                                                </a>
                                            <?php } ?>
                                            <?php if (strpos($row['transaction_id'], 'P') !== false) { ?>
                                                <a href="wmsPurchaseDetails.php?purchase_transaction_id=<?php echo $row['transaction_id'] ?>" target="_blank">
                                                    #<?php echo $row['transaction_id'] ?>
                                                </a>
                                            <?php } ?>
                                            <?php if (strpos($row['transaction_id'], 'E') !== false) { ?>
                                                <a href="wmsSpendingDetails.php?spending_transaction_id=<?php echo $row['transaction_id'] ?>" target="_blank">
                                                    #<?php echo $row['transaction_id'] ?>
                                                </a>
                                            <?php } ?>
                                            <?php if (strpos($row['transaction_id'], 'C') !== false) { ?>
                                                #<?php echo $row['transaction_id'] ?>
                                            <?php } ?>
                                            <?php if (strpos($row['transaction_id'], 'I') !== false) { ?>
                                                #<?php echo $row['transaction_id'] ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php echo $row['bank_account_name'] ?><br>
                                            <?php echo $row['bank_account_number'] ?><br>
                                            <?php echo $row['bank_account_holder'] ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-success text-success <?php echo ($row['debt_status'] === NULL && $row['receivable_status'] === NULL || $row['debt_status'] === '2' || $row['receivable_status'] === '2') ? '' : 'd-none'; ?>">
                                                <span class="legend-indicator bg-success"></span>Paid
                                            </span>
                                            <div class="<?php echo ($row['debt_status'] === '1') ? '' : 'd-none'; ?>">
                                                <span class="badge bg-soft-warning text-warning mb-2">
                                                    <span class="legend-indicator bg-warning"></span>Debt
                                                </span><br>
                                                Maturity date : <span class="fw-bold"><?php echo $row['debt_due_date'] ?></span>
                                            </div>
                                            <div class="<?php echo ($row['receivable_status'] === '1') ? '' : 'd-none'; ?>">
                                                <span class="badge bg-soft-info text-info mb-2">
                                                    <span class="legend-indicator bg-info"></span>Receivable
                                                </span><br>
                                                Maturity date : <span class="fw-bold"><?php echo $row['receivable_due_date'] ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo "Rp " . number_format(floatval($row['debt']), 0, ',', '.'); ?>
                                        </td>
                                        <td>
                                            <?php echo "Rp " . number_format(floatval($row['credit']), 0, ',', '.'); ?>
                                        <td>
                                            <?php echo "Rp " . number_format($row['last_balance'], 0, ',', '.'); ?>
                                        </td>
                                        <td>
                                            <?php echo $row['transaction_description'] ?>
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
                                        <option value="4">4</option>
                                        <option value="6">6</option>
                                        <option value="8" selected>8</option>
                                        <option value="12">12</option>
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
    <script src="./assets/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="./assets/vendor/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="./assets/vendor/hs-nav-scroller/dist/hs-nav-scroller.min.js"></script>

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>

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
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            HSCore.components.HSDatatables.init($('#datatable'), {
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

            $('.js-datatable-filter').on('change', function() {
                var $this = $(this),
                    elVal = $this.val(),
                    targetColumnIndex = $this.data('target-column-index');

                if (elVal === 'null') elVal = ''

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


                // INITIALIZATION OF NAV SCROLLER
                // =======================================================
                new HsNavScroller('.js-nav-scroller')


                // INITIALIZATION OF SELECT
                // =======================================================
                HSCore.components.HSTomSelect.init('.js-select')


                // INITIALIZATION OF CLIPBOARD
                // =======================================================
                HSCore.components.HSClipboard.init('.js-clipboard')
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
            var originalAlert = window.alert;
            window.alert = function(message) {
                if (message.includes("DataTables warning")) {
                    console.log("DataTables warning:", message);
                } else {
                    originalAlert(message);
                }
            };
        })();
    </script>
</body>

</html>