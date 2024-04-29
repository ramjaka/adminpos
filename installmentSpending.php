<?php
require_once 'controller/Session.php';
$page = 'isntallment';
$page_access = array('1', '13');

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

// debt id
if (isset($_GET['debt_id']) && !empty($_GET['debt_id'])) {
    $debt_id = $_GET['debt_id'];
}

// total installment
$query_total_installment = "SELECT SUM(installment_amount) AS total_installment FROM debt_installment WHERE debt_id = '$debt_id'";
$result_total_installment = $mysqli->query($query_total_installment);

if ($result_total_installment) {
    $row = $result_total_installment->fetch_assoc();
    $total_installment = $row['total_installment'];
}

$query = "SELECT 
s.*,
d.debt_id, 
d.debt_status, 
d.debt_due_date, 
d.fulfillment_date, 
b.bank_account_name, 
b.bank_account_number
FROM 
spending s
LEFT JOIN 
    debt d ON s.debt_id = d.debt_id
JOIN 
    bank_account b ON s.bank_account_number = b.bank_account_number
WHERE
    s.spending_transaction_id = '$debt_id';
";
$result = mysqli_query($mysqli, $query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $spending_transaction_id = $row['spending_transaction_id'];
    $debt_id = $row['debt_id'];
    $debt_status = $row['debt_status'];
    $spending_name = $row['spending_name'];
    $spending_total = $row['spending_total'];
    $spending_date = $row['spending_date'];
    $debt_due_date = $row['debt_due_date'];
    $spending_status = $row['spending_status'];
    $fulfillment_date = $row['fulfillment_date'];
    $bank_account_name = $row['bank_account_name'];
    $bank_account_number = $row['bank_account_number'];
}
$accumulation_total_debt = $spending_total - $total_installment;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('components/head.php'); ?>

    <link rel="stylesheet" href="./assets/vendor/tom-select/dist/css/tom-select.bootstrap5.css">
    <link rel="stylesheet" href="./assets/vendor/quill/dist/quill.snow.css">
    <link rel="stylesheet" href="./assets/vendor/leaflet/dist/leaflet.css">

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
                <form action="controller/InstallmentSpending.php" method="POST" class="row align-items-end">
                    <div class="col-sm mb-2 mb-sm-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-no-gutter">
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:;">WMS</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Supplier</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Installment</h1>
                        <?php if ($debt_id) { ?>
                            <span class="legend-indicator
                            <?php if ($row['debt_status'] === '1') {
                                echo 'bg-warning';
                            } elseif ($row['debt_status'] === '2') {
                                echo 'bg-success';
                            } else {
                                echo 'bg-danger';
                            } ?>">
                            </span><?php echo ($row['debt_status'] === '1') ? 'debt' : 'paid' ?>
                        <?php } ?>
                    </div>
                    <?php if ($debt_id && $debt_status === '1') { ?>
                        <div class="col-sm-auto">
                            <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#payableModal"><i class="bi bi-wallet2 me-1"></i> Payable</a>
                        </div>
                    <?php } ?>

                    <!-- Modal -->
                    <div id="payableModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="payableModalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="payableModalTitle">Payable</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- form -->
                                    <div>
                                        <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                                        <div class="row">
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
                                                                    if ($row['bank_account_number'] != '123receivables' && $row['bank_account_number'] != '123debt') {
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
                                            <div class="col-sm-6">
                                                <label class="form-label" for="phoneNumber">Total</label>
                                                <div class="input-group mb-4">
                                                    <span class="input-group-text">Rp</span>
                                                    <input id="balance" type="text" class="form-control" name="installment_amount">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-white me-2" data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deletePayableModal<?php echo $debt_id ?>"><i class="bi bi-wallet me-1"></i>Save payable</button>
                                        </div>
                                    </div>
                                    <!-- end form -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->

                    <!-- Modal installment -->
                    <div id="deletePayableModal<?php echo $debt_id ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalCenterTitle">Installment payments?
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>Are you sure want to payable "<strong><?php echo $debt_id ?></strong>"?<br>You can't undo this action.
                                    </p>
                                    <div class="alert alert-soft-danger" role="alert">
                                        <h3 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Warning</h3>
                                        <p>When you make a payable for this debt, you cannot cancel or delete your payable.</p>
                                    </div>

                                    <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                                    <input type="hidden" name="debt_id" value="<?php echo $debt_id ?>">
                                    <input type="hidden" name="spending_total" value="<?php echo $spending_total ?>">

                                    <div class="mb-4 fw-bold">
                                        <label class="form-label" for="spendingName">Please type in your transaction id to confirm.</label>
                                        <input name="confirmation_delete" type="text" class="form-control border-danger" required>
                                    </div>

                                    <button name="btn-add-installment" type="submit" class="btn btn-danger w-100">Save payable</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->
                    <!-- End Col -->
                </form>
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

            <!-- Card -->
            <div class="card overflow-hidden">
                <!-- Header -->
                <div class="card-header card-header-content-between">
                    <h4 class="card-header-title">Transaction receipt</h4>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <!-- Matrix Chart -->
                    <div class="chartjs-matrix-custom mb-3" style="min-width: 100%; width: 700px;">
                        <div class="col-lg-6 col-sm-12 mx-auto border border-1 rounded-3">
                            <!-- Header -->
                            <div class="modal-top-cover bg-dark text-center">
                                <figure class="position-absolute end-0 bottom-0 start-0">
                                    <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1920 100.1">
                                        <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z" />
                                    </svg>
                                </figure>
                            </div>
                            <!-- End Header -->

                            <div class="modal-top-cover-icon">
                                <span class="icon icon-lg icon-light icon-circle icon-centered shadow-sm">
                                    <i class="bi-receipt fs-2"></i>
                                </span>
                            </div>

                            <!-- Body -->
                            <div class="modal-body px-5">
                                <div class="text-center mb-5">
                                    <h3 class="mb-1">Invoice from Spending</h3>
                                    <span class="d-block">Invoice #<?php echo $spending_transaction_id ?></span>
                                </div>

                                <div class="row mb-6">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <small class="text-cap text-secondary mb-0">Amount paid:</small>
                                        <span class="text-dark"><?php echo "Rp " . number_format($spending_total, 0, ',', '.'); ?></span>
                                    </div>
                                    <!-- End Col -->

                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <small class="text-cap text-secondary mb-0">Date paid:</small>
                                        <span class="text-dark"><?php echo $spending_date ?></span>
                                    </div>
                                    <!-- End Col -->

                                    <div class="col-md-4">
                                        <small class="text-cap text-secondary mb-0">Payment method:</small>
                                        <div class="d-flex align-items-center">
                                            <span class="text-dark"><?php echo $bank_account_name ?></span>
                                        </div>
                                    </div>
                                    <!-- End Col -->
                                </div>
                                <!-- End Row -->

                                <small class="text-cap mb-2">Summary</small>

                                <ul class="list-group mb-4">
                                    <li class="list-group-item text-dark">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><?php echo $spending_name ?></span>
                                            <span><?php echo "Rp " . number_format($spending_total, 0, ',', '.') ?></span>
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-light text-dark">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>Amount paid</strong>
                                            <strong><?php echo "Rp " . number_format($spending_total, 0, ',', '.') ?></strong>
                                        </div>
                                    </li>
                                </ul>

                                <div class="d-flex justify-content-end gap-3 mb-5">
                                    <a class="btn btn-white btn-sm" href="#"><i class="bi-printer-fill me-1"></i> Print Details</a>
                                </div>
                            </div>
                            <!-- End Body -->
                        </div>
                    </div>
                    <!-- End Matrix Chart -->
                </div>
                <!-- End Body -->

                <hr class="my-0">

                <div class="row">
                    <div class="col-lg-4">
                        <!-- Body -->
                        <div class="card card-centered bg-light h-100 rounded-0 shadow-none">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <img class="avatar avatar-xxl avatar-4x3" src="assets/svg/illustrations/oc-money-profits.svg" alt="Image Description" data-hs-theme-appearance="default">
                                    <img class="avatar avatar-xxl avatar-4x3" src="assets/svg/illustrations-light/oc-money-profits.svg" alt="Image Description" data-hs-theme-appearance="dark">
                                </div>

                                <span class="display-4 d-block text-dark">
                                    <span>
                                        <?php echo "Rp " . number_format($accumulation_total_debt, 0, ',', '.') ?>
                                    </span>
                                </span>

                                <span class="d-block">
                                    &mdash; Total debt
                                    <span class="badge bg-soft-dark text-dark rounded-pill ms-1"><?php echo $debt_due_date ?></span>
                                </span>
                            </div>
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Col -->

                    <div class="col-lg-8">
                        <!-- Body -->
                        <div class="card-body card-body-height">
                            <ul class="list-group list-group-flush list-group-no-gutters">
                                <?php
                                $sql = "SELECT * FROM `debt_installment` WHERE debt_id = '$debt_id' ORDER BY debt_installment_created_at DESC";

                                $result = $mysqli->query($sql);

                                if ($result->num_rows > 0) { ?>
                                    <?php while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <!-- List Item -->
                                        <li class="list-group-item">
                                            <div class="row align-items-center">
                                                <div class="col-sm mb-3 mb-sm-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-grow-1 ms-2">
                                                            <h5 class="text-inherit"><?php echo $row['bank_account_name'] ?></h5>
                                                            <?php echo $row['bank_account_number'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col">
                                                    <span class="text-cap text-body small mb-0">Installment amount</span>
                                                    <span class="fw-semibold text-success"><?php echo "Rp " . number_format($row['installment_amount'], 0, ',', '.'); ?></span>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col">
                                                    <span class="text-cap text-body small mb-0">Transaction time</span>
                                                    <span class="fw-semibold text-dark"><?php echo date('d/m/Y H:i:s', strtotime($row['debt_installment_created_at'])); ?></span>
                                                </div>
                                                <!-- End Col -->
                                            </div>
                                        </li>
                                        <!-- End List Item -->
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Card -->
        </div>
        <!-- End Row -->

        <!-- Footer -->

        <?php include_once('components/footer.php'); ?>

        <!-- End Footer -->
    </main>
    <!-- ========== END MAIN CONTENT ========== -->

    <!-- JS Global Compulsory  -->
    <script src="./assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="./assets/vendor/jquery-migrate/dist/jquery-migrate.min.js"></script>
    <script src="./assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS Implementing Plugins -->
    <script src="./assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside.min.js"></script>
    <script src="./assets/vendor/hs-form-search/dist/hs-form-search.min.js"></script>

    <script src="./assets/vendor/hs-toggle-password/dist/js/hs-toggle-password.js"></script>
    <script src="./assets/vendor/hs-file-attach/dist/hs-file-attach.min.js"></script>
    <script src="./assets/vendor/hs-nav-scroller/dist/hs-nav-scroller.min.js"></script>
    <script src="./assets/vendor/hs-step-form/dist/hs-step-form.min.js"></script>
    <script src="./assets/vendor/hs-counter/dist/hs-counter.min.js"></script>
    <script src="./assets/vendor/appear/dist/appear.min.js"></script>
    <script src="./assets/vendor/imask/dist/imask.min.js"></script>
    <script src="./assets/vendor/tom-select/dist/js/tom-select.complete.min.js"></script>
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

    <script src="./assets/vendor/flatpickr/dist/flatpickr.min.js"></script>

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>

    <!-- update data -->
    <script src="./controller/UsersOverview.js"></script>

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

    <!-- JS Plugins Init. -->
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            HSCore.components.HSDatatables.init($('.js-datatable'), {
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
            })

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


                // INITIALIZATION OF SELECT
                // =======================================================
                HSCore.components.HSTomSelect.init('.js-select')


                // INITIALIZATION OF INPUT MASK
                // =======================================================
                HSCore.components.HSMask.init('.js-input-mask')


                // INITIALIZATION OF NAV SCROLLER
                // =======================================================
                new HsNavScroller('.js-nav-scroller')


                // INITIALIZATION OF COUNTER
                // =======================================================
                new HSCounter('.js-counter')


                // INITIALIZATION OF TOGGLE PASSWORD
                // =======================================================
                new HSTogglePassword('.js-toggle-password')


                // INITIALIZATION OF FILE ATTACHMENT
                // =======================================================
                new HSFileAttach('.js-file-attach')
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

    <!-- JS Implementing Plugins -->
    <script src="./assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="./assets/vendor/daterangepicker/daterangepicker.js"></script>

    <!-- JS Front -->

    <script>
        (function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            HSCore.components.HSDatatables.init('.js-datatable')
            const datatableDatepickerFilter = HSCore.components.HSDatatables.getItem('datatableDatepickerFilter')

            HSCore.components.HSDaterangepicker.init('.js-daterangepicker')
            const daterangepicker = HSCore.components.HSDaterangepicker.getItem(0)

            var startDate = null,
                endDate = null

            daterangepicker.on('apply.daterangepicker', function(ev, picker) {
                this.value = picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY')

                startDate = moment(picker.startDate.format('DD/MM/YYYY'))
                endDate = moment(picker.endDate.format('DD/MM/YYYY'))

                datatableDatepickerFilter.draw()
            })

            daterangepicker.on('cancel.daterangepicker', function(ev, picker) {
                this.value = ''

                startDate = null
                endDate = null

                datatableDatepickerFilter.draw()
            })

            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    if (!startDate || !endDate) return true
                    let compareDate = moment(moment(data[4]).format('DD/MM/YYYY'))
                    return compareDate.isBetween(startDate, endDate)
                }
            )
        })()
    </script>

    <!-- block alert -->
    <script>
        window.alert = function() {};

        window.addEventListener('beforeunload', function(event) {
            event.returnValue = '';
        });
    </script>

    <script>
        (function() {
            // INITIALIZATION OF FLATPICKR
            // =======================================================
            HSCore.components.HSFlatpickr.init('.js-flatpickr')
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
</body>

</html>