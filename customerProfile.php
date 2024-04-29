<?php
require_once 'controller/Session.php';
$page = 'customer-profile';
$page_access = array('1', '5');

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

// member id
if (isset($_GET['member_id']) && !empty($_GET['member_id'])) {
    $member_id = $_GET['member_id'];
}

$query = "SELECT * FROM `member` WHERE member_id = '$member_id'";
$result = mysqli_query($mysqli, $query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $member_first_name = $row['member_first_name'];
    $member_last_name = $row['member_last_name'];
    $member_phone = $row['member_phone'];
    $member_email = $row['member_email'];
    $member_address = $row['member_address'];
    $member_status = $row['member_status'];
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
                <div class="row align-items-end">
                    <div class="col-sm mb-2 mb-sm-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-no-gutter">
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:;">CRM</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Customer Profile</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title"><?php echo $member_first_name . ' ' . $member_last_name ?></h1>
                        <span class="legend-indicator
                            <?php if ($row['member_status'] === 'active') {
                                echo 'bg-success';
                            } elseif ($row['member_status'] === 'hold') {
                                echo 'bg-warning';
                            } else {
                                echo 'bg-danger';
                            } ?>">
                        </span><?php echo $row['member_status']; ?>
                    </div>

                    <div class="col-sm-auto">
                        <div class="d-none d-lg-block">
                            <form action="controller/PosEditMember.php" method="POST">
                                <input type="hidden" name="member_id" value="<?php echo $member_id ?>">
                                <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                                <input type="hidden" name="member_first_name" value="<?php echo $member_first_name ?>">
                                <input type="hidden" name="member_last_name" value="<?php echo $member_last_name ?>">
                                <?php if ($member_status === 'hold') { ?>
                                    <button name="btn-active" type="submit" class="btn btn-success"><i class="bi bi-eye"></i> Active</button>
                                <?php } ?>
                                <?php if ($member_status === 'active') { ?>
                                    <button name="btn-hold" type="submit" class="btn btn-warning text-white"><i class="bi bi-stop-circle"></i> Hold</button>
                                <?php } ?>
                            </form>
                        </div>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
                <a href="posCRM.php" class="btn btn-white mt-2"><i class="bi bi-chevron-left"></i> Back</a>
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

            <div class="row">
                <div class="col-lg-12">
                    <!-- Card -->
                    <div class="card mb-3 mb-lg-5">
                        <form action="controller/PosEditMember.php" method="POST">
                            <input type="hidden" name="member_id" value="<?php echo $member_id ?>">
                            <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                            <input type="hidden" name="member_first_name" value="<?php echo $member_first_name ?>">
                            <input type="hidden" name="member_last_name" value="<?php echo $member_last_name ?>">
                            <input type="hidden" name="member_email_old" value="<?php echo $member_email ?>">
                            <input type="hidden" name="member_phone_old" value="<?php echo $member_phone ?>">
                            <!-- Body -->
                            <div class="card-body">

                                <!-- form -->
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-4">
                                            <label class="form-label" for="firstName">First name</label>
                                            <input name="member_first_name" type="text" class="form-control" value="<?php echo $member_first_name ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-4">
                                            <label class="form-label" for="lastName">Last name</label>
                                            <input name="member_last_name" type="text" class="form-control" value="<?php echo $member_last_name ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-4">
                                            <label class="form-label" for="phoneNumber">Phone number / WA</label>

                                            <input name="member_phone" type="text" class="js-input-mask form-control <?php echo isset($_GET['MemberPhoneValidation']) ? 'is-invalid' : ''; ?>" placeholder="+(xx) xxx-xxx-xxx-xxx" data-hs-mask-options='{
                                                "mask": "+(00) 000-000-000-000"
                                            }' value="<?php echo $member_phone ?>" required>

                                            <?php if (isset($_GET['MemberPhoneValidation'])) { ?>
                                                <span class="invalid-feedback"><?php echo urldecode($_GET['MemberPhoneValidation']); ?></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-4">
                                            <label class="form-label" for="email">Email</label>
                                            <input name="member_email" type="email" class="form-control <?php echo isset($_GET['memberEmailValidation']) ? 'is-invalid' : ''; ?>" value="<?php echo $member_email ?>" required>
                                            <?php if (isset($_GET['memberEmailValidation'])) { ?>
                                                <span class="invalid-feedback"><?php echo urldecode($_GET['memberEmailValidation']); ?></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-4">
                                            <label class="form-label" for="addressLabel">Address</label>
                                            <textarea name="member_address" class="form-control" rows="4" required><?php echo $member_address ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- end form -->
                            </div>
                            <!-- Body -->

                            <!-- Footer -->
                            <div class="card-footer">
                                <div class="d-flex justify-content-end gap-3">
                                    <button name="btn-edit-member" type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                            <!-- End Footer -->
                        </form>
                    </div>
                    <!-- End Card -->

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

                    <!-- Card -->
                    <div class="card mb-3 mb-lg-5">
                        <!-- Header -->
                        <div class="card-header card-header-content-sm-between">
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

                                    <!-- <input id="datatableSearch" type="text" class="js-daterangepicker form-control daterangepicker-custom-input" placeholder="Select dates" data-hs-daterangepicker-options='{
                            "autoUpdateInput": true,
                            "locale": {
                                "cancelLabel": "Clear"
                            }
                            }'> -->
                                </form>
                            </div>

                            <!-- Nav Scroller -->
                            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                                <span class="hs-nav-scroller-arrow-prev" style="display: none;">
                                    <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                                        <i class="bi-chevron-left"></i>
                                    </a>
                                </span>

                                <span class="hs-nav-scroller-arrow-next" style="display: none;">
                                    <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                                        <i class="bi-chevron-right"></i>
                                    </a>
                                </span>
                            </div>
                            <!-- End Nav Scroller -->
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
                                        bank_account b ON s.bank_account_number = b.bank_account_number
                                    WHERE member_id = '$member_id'";

                                    if ($date_from && $date_select) {
                                        $sql .= " AND STR_TO_DATE(s.sales_date, '%d/%m/%Y') BETWEEN '$date_from' AND '$date_select'";
                                    }

                                    $sql .= " ORDER BY s.sales_created_at DESC";

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
                                                <option value="12">12</option>
                                                <option value="14" selected>14</option>
                                                <option value="16">16</option>
                                                <option value="18">18</option>
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
            </div>
            <!-- End Row -->

            <div class="d-lg-none">
                <button type="button" class="btn btn-danger">Delete customer</button>
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

    <script src="./assets/vendor/hs-nav-scroller/dist/hs-nav-scroller.min.js"></script>
    <script src="./assets/vendor/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="./assets/vendor/quill/dist/quill.min.js"></script>
    <script src="./assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
    <script src="./assets/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="./assets/vendor/datatables.net.extensions/select/select.min.js"></script>
    <script src="./assets/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="./assets/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="./assets/vendor/leaflet/dist/leaflet.js"></script>
    <script src="./assets/vendor/imask/dist/imask.min.js"></script>

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>
    <script src="./assets/js/hs.theme-appearance-charts.js"></script>

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


                // INITIALIZATION OF CHARTJS
                // =======================================================
                HSCore.components.HSChartJS.init('.js-chart')


                // INITIALIZATION OF QUILLJS EDITOR
                // =======================================================
                HSCore.components.HSQuill.init('.js-quill')
                HSCore.components.HSQuill.init('.js-quill-step')


                // INITIALIZATION OF LEAFLET
                // =======================================================
                const leaflet = HSCore.components.HSLeaflet.init(document.getElementById('map'))

                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
                    id: 'mapbox/light-v9'
                }).addTo(leaflet)
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
            // INITIALIZATION OF INPUT MASK
            // =======================================================
            HSCore.components.HSMask.init('.js-input-mask')
        })();
    </script>

    <!-- End Style Switcher JS -->
</body>

</html>