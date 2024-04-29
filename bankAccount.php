<?php
require_once 'controller/Session.php';
$page = 'bank-account';
$page_access = array('1', '12');

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

    <!-- End Navbar Vertical -->

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
                                <li class="breadcrumb-item active" aria-current="page">Account</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Account</h1>
                    </div>

                    <div class="col-auto">
                        <a class="btn btn-success" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addBalanceModal">
                            <i class="bi bi-cash-coin me-1"></i> Add Balance
                        </a>
                    </div>
                    <!-- End Col -->

                    <div class="col-auto">
                        <a class="btn btn-primary" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                            <i class="bi bi-credit-card-2-front me-1"></i> Add Account
                        </a>
                    </div>
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

            <!-- Stats -->
            <div class="row">
                <?php
                $sql = "SELECT 
                b.bank_account_number, 
                b.bank_account_name, 
                b.bank_account_holder, 
                b.bank_account_status, 
                b.bank_account_type, 
                (
                    SELECT 
                        m.last_balance 
                    FROM 
                        `mutation` m 
                    WHERE 
                        m.bank_account_number = b.bank_account_number 
                    ORDER BY 
                        m.mutation_created_at DESC 
                    LIMIT 1
                ) AS last_balance 
            FROM 
                `bank_account` b 
            WHERE
                b.bank_account_number NOT IN ('123receivableS', '123debt')
            ORDER BY 
                b.bank_account_created_at";

                $result = $mysqli->query($sql);

                if ($result->num_rows > 0) { ?>
                    <?php while ($row = $result->fetch_assoc()) {
                    ?>
                        <a href="bankAccountDetails.php?bank_account_number=<?php echo $row['bank_account_number']; ?>" class="col-sm-6 col-lg-4 mb-3">
                            <!-- Card -->
                            <div class="card card-hover-shadow h-100">
                                <div class="card-header bg-soft-secondary">
                                </div>
                                <div class="card-body">
                                    <h6 class="card-subtitle"><?php echo $row['bank_account_name'] ?></h6>

                                    <div class="row align-items-center gx-2 mb-1">
                                        <div class="col-12 my-2">
                                            <h2 class="card-title text-inherit <?php echo ($row['bank_account_type'] === 'cash') ? 'text-success' : (($row['bank_account_type'] === 'debt') ? 'text-danger' : (($row['bank_account_type'] === 'receivables') ? 'text-info' : '')); ?>">
                                                <?php echo "Rp " . number_format($row['last_balance'], 0, ',', '.'); ?>
                                            </h2>
                                        </div>
                                    </div>
                                    <!-- End Row -->
                                    <span class="text-body fs-5"><?php echo $row['bank_account_number'] ?></span>
                                    <br>
                                    <span class="text-body fs-5 text-capitalize"><?php echo $row['bank_account_holder'] ?></span>
                                    <br>
                                    <span class="text-muted">
                                        <span class="legend-indicator
                                        <?php if ($row['bank_account_status'] === 'active') {
                                            echo 'bg-success';
                                        } elseif ($row['bank_account_status'] === 'hold') {
                                            echo 'bg-warning';
                                        } else {
                                            echo 'bg-danger';
                                        } ?>">
                                        </span><?php echo $row['bank_account_status']; ?>
                                    </span>
                                </div>
                            </div>
                            <!-- End Card -->
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
            <!-- End Stats -->
        </div>
        <!-- End Content -->

        <!-- Footer -->
        <?php include_once('components/footer.php'); ?>
        <!-- End Footer -->
    </main>
    <!-- ========== END MAIN CONTENT ========== -->

    <!-- add balance Modal -->
    <div class="modal fade" id="addBalanceModal" tabindex="-1" aria-labelledby="addBalanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" <h4 class="modal-title" id="addBalanceModalLabel">Add balance</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <!-- form -->
                    <form action="controller/BankAddBalance.php" method="POST">
                        <input name="username_creator" type="hidden" value="<?php echo $username ?>">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-4">
                                    <label for="bankLabel" class="form-label">Bank Account</label>
                                    <!-- Select -->
                                    <div class="tom-select-custom">
                                        <select name="bank_account_number" class="js-select form-select text-uppercase" autocomplete="off" data-hs-tom-select-options='{
                                                "placeholder": "Select discount..."
                                            }'>
                                            <?php
                                            $sql = "SELECT * FROM `bank_account` ORDER BY bank_account_created_at DESC";
                                            $result = $mysqli->query($sql);

                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    if ($row['bank_account_number'] != '123debt' & $row['bank_account_number'] != '123receivables') {
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
                            <div class="col-sm-12">
                                <div class="mb-4">
                                    <label class="form-label" for="firstName">Balance (Rp)</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">Rp</span>
                                        <input id="balance" name="bank_account_balance" type="text" class="form-control" aria-label="Amount (to the nearest dollar)" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-white me-2" data-bs-dismiss="modal">Close</button>
                            <button name="btn-save-account" type="submit" class="btn btn-primary">Save account</button>
                        </div>
                    </form>
                    <!-- end form -->
                </div>
                <!-- End Body -->
            </div>
        </div>
    </div>
    <!-- End add balance Modal -->

    <!-- Create a new account Modal -->
    <div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addAccountModalLabel">Create account</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <!-- form -->
                    <form action="controller/BankAddAccount.php" method="POST">
                        <input name="username_creator" type="hidden" value="<?php echo $username ?>">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-4">
                                    <label class="form-label" for="firstName">Account name</label>
                                    <input name="bank_account_name" type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label class="form-label" for="lastName">Account number</label>
                                    <input name="bank_account_number" type="number" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label class="form-label" for="lastName">Account holder</label>
                                    <input name="bank_account_holder" type="text" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-white me-2" data-bs-dismiss="modal">Close</button>
                            <button name="btn-save-account" type="submit" class="btn btn-primary">Save account</button>
                        </div>
                    </form>
                    <!-- end form -->
                </div>
                <!-- End Body -->
            </div>
        </div>
    </div>
    <!-- End Create a new account Modal -->
    <!-- ========== END SECONDARY CONTENTS ========== -->

    <!-- JS Global Compulsory  -->
    <script src="./assets/vendor/jquery/dist/jquery.min.js"></script>
    <script src="./assets/vendor/jquery-migrate/dist/jquery-migrate.min.js"></script>
    <script src="./assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS Implementing Plugins -->
    <script src="./assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside.min.js"></script>
    <script src="./assets/vendor/hs-form-search/dist/hs-form-search.min.js"></script>

    <script src="./assets/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="./assets/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="./assets/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="./assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="./assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="./assets/vendor/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="./assets/vendor/clipboard/dist/clipboard.min.js"></script>
    <script src="./assets/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="./assets/vendor/datatables.net.extensions/select/select.min.js"></script>

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

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>
    <script src="./assets/js/hs.theme-appearance-charts.js"></script>

    <!-- JS Plugins Init. -->
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF DATERANGEPICKER
            // =======================================================
            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);
        });


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

        document.querySelectorAll('.js-datatable-filter').forEach(function(item) {
            item.addEventListener('change', function(e) {
                const elVal = e.target.value,
                    targetColumnIndex = e.target.getAttribute('data-target-column-index'),
                    targetTable = e.target.getAttribute('data-target-table');

                HSCore.components.HSDatatables.getItem(targetTable).column(targetColumnIndex).search(elVal !== 'null' ? elVal : '').draw()
            })
        })
    </script>

    <!-- JS Plugins Init. -->
    <script>
        (function() {
            localStorage.removeItem('hs_theme')

            window.onload = function() {


                // INITIALIZATION OF NAVBAR VERTICAL ASIDE
                // =======================================================
                new HSSideNav('.js-navbar-vertical-aside').init()


                // INITIALIZATION OF FORM SEARCH
                // =======================================================
                const HSFormSearchInstance = new HSFormSearch('.js-form-search')

                if (HSFormSearchInstance.collection.length) {
                    HSFormSearchInstance.getItem(1).on('close', function(el) {
                        el.classList.remove('top-0')
                    })

                    document.querySelector('.js-form-search-mobile-toggle').addEventListener('click', e => {
                        let dataOptions = JSON.parse(e.currentTarget.getAttribute('data-hs-form-search-options')),
                            $menu = document.querySelector(dataOptions.dropMenuElement)

                        $menu.classList.add('top-0')
                        $menu.style.left = 0
                    })
                }


                // INITIALIZATION OF BOOTSTRAP DROPDOWN
                // =======================================================
                HSBsDropdown.init()


                // INITIALIZATION OF CHARTJS
                // =======================================================
                HSCore.components.HSChartJS.init('.js-chart')


                // INITIALIZATION OF CHARTJS
                // =======================================================
                HSCore.components.HSChartJS.init('#updatingBarChart')
                const updatingBarChart = HSCore.components.HSChartJS.getItem('updatingBarChart')

                // Call when tab is clicked
                document.querySelectorAll('[data-bs-toggle="chart-bar"]').forEach(item => {
                    item.addEventListener('click', e => {
                        let keyDataset = e.currentTarget.getAttribute('data-datasets')

                        const styles = HSCore.components.HSChartJS.getTheme('updatingBarChart', HSThemeAppearance.getAppearance())

                        if (keyDataset === 'lastWeek') {
                            updatingBarChart.data.labels = ["Apr 22", "Apr 23", "Apr 24", "Apr 25", "Apr 26", "Apr 27", "Apr 28", "Apr 29", "Apr 30", "Apr 31"];
                            updatingBarChart.data.datasets = [{
                                    "data": [120, 250, 300, 200, 300, 290, 350, 100, 125, 320],
                                    "backgroundColor": styles.data.datasets[0].backgroundColor,
                                    "hoverBackgroundColor": styles.data.datasets[0].hoverBackgroundColor,
                                    "borderColor": styles.data.datasets[0].borderColor,
                                    "maxBarThickness": 10
                                },
                                {
                                    "data": [250, 130, 322, 144, 129, 300, 260, 120, 260, 245, 110],
                                    "backgroundColor": styles.data.datasets[1].backgroundColor,
                                    "borderColor": styles.data.datasets[1].borderColor,
                                    "maxBarThickness": 10
                                }
                            ];
                            updatingBarChart.update();
                        } else {
                            updatingBarChart.data.labels = ["May 1", "May 2", "May 3", "May 4", "May 5", "May 6", "May 7", "May 8", "May 9", "May 10"];
                            updatingBarChart.data.datasets = [{
                                    "data": [200, 300, 290, 350, 150, 350, 300, 100, 125, 220],
                                    "backgroundColor": styles.data.datasets[0].backgroundColor,
                                    "hoverBackgroundColor": styles.data.datasets[0].hoverBackgroundColor,
                                    "borderColor": styles.data.datasets[0].borderColor,
                                    "maxBarThickness": 10
                                },
                                {
                                    "data": [150, 230, 382, 204, 169, 290, 300, 100, 300, 225, 120],
                                    "backgroundColor": styles.data.datasets[1].backgroundColor,
                                    "borderColor": styles.data.datasets[1].borderColor,
                                    "maxBarThickness": 10
                                }
                            ]
                            updatingBarChart.update();
                        }
                    })
                })


                // INITIALIZATION OF CHARTJS
                // =======================================================
                HSCore.components.HSChartJS.init('.js-chart-datalabels', {
                    plugins: [ChartDataLabels],
                    options: {
                        plugins: {
                            datalabels: {
                                anchor: function(context) {
                                    var value = context.dataset.data[context.dataIndex];
                                    return value.r < 20 ? 'end' : 'center';
                                },
                                align: function(context) {
                                    var value = context.dataset.data[context.dataIndex];
                                    return value.r < 20 ? 'end' : 'center';
                                },
                                color: function(context) {
                                    var value = context.dataset.data[context.dataIndex];
                                    return value.r < 20 ? context.dataset.backgroundColor : context.dataset.color;
                                },
                                font: function(context) {
                                    var value = context.dataset.data[context.dataIndex],
                                        fontSize = 25;

                                    if (value.r > 50) {
                                        fontSize = 35;
                                    }

                                    if (value.r > 70) {
                                        fontSize = 55;
                                    }

                                    return {
                                        weight: 'lighter',
                                        size: fontSize
                                    };
                                },
                                formatter: function(value) {
                                    return value.r
                                },
                                offset: 2,
                                padding: 0
                            }
                        },
                    }
                })

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
        (function() {
            // INITIALIZATION OF SELECT
            // =======================================================
            HSCore.components.HSTomSelect.init('.js-select')
        })();
    </script>

    <!-- End Style Switcher JS -->
</body>

</html>