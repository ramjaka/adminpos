<?php
require_once 'controller/Session.php';
$page = 'wms-stock';
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
                                <li class="breadcrumb-item active" aria-current="page">Purchasing List</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Purchasing List <span class="badge bg-soft-dark text-dark ms-2">106,905</span></h1>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-md-6 mb-3 mb-lg-5">
                    <!-- Card -->
                    <div class="card">
                        <!-- Header -->
                        <div class="card-header card-header-content-between">
                            <h4 class="card-header-title" style="margin-top: 12px; margin-bottom: 12px;">Threshold Stock</h4>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Pie Chart -->
                            <div class="chartjs-custom mb-3 mb-sm-5" style="height: 14rem;">
                                <canvas id="updatingDoughnutChart" data-hs-chartjs-options='{
                                    "type": "doughnut",
                                    "data": {
                                        "labels": ["QTY", "QTY", "QTY"],
                                        "datasets": [{
                                        "backgroundColor": ["#ED4C78", "#F5CA99", "#377dff"],
                                        "borderWidth": 5,
                                        "hoverBorderColor": "#fff"
                                        }]
                                    },
                                    "options": {
                                        "cutoutPercentage": 80,
                                        "plugins": {
                                        "tooltip": {
                                            "postfix": " pcs",
                                            "hasIndicator": true,
                                            "mode": "index",
                                            "intersect": false
                                        }
                                        },
                                        "hover": {
                                        "mode": "nearest",
                                        "intersect": true
                                        }
                                    }
                                    }'>
                                </canvas>
                            </div>
                            <!-- End Pie Chart -->

                            <div class="row justify-content-center">
                                <div class="col-auto mb-3 mb-sm-0">
                                    <h4 class="card-title my-4"></h4>
                                    <span class="legend-indicator bg-danger"></span>
                                    < 10 </div>
                                        <!-- End Col -->

                                        <div class="col-auto mb-3 mb-sm-0">
                                            <h4 class="card-title my-4"></h4>
                                            <span class="legend-indicator bg-warning"></span>
                                            < 50</div>
                                                <!-- End Col -->

                                                <div class="col-auto">
                                                    <h4 class="card-title my-4"></h4>
                                                    <span class="legend-indicator bg-primary"></span>
                                                    < 100 </div>
                                                        <!-- End Col -->
                                                </div>
                                                <!-- End Row -->
                                        </div>
                                        <!-- End Body -->
                                </div>
                                <!-- End Card -->
                            </div>
                            <!-- End Col -->

                            <div class="col-md-6 mb-3 mb-lg-5">
                                <!-- Card -->
                                <div class="card h-100">
                                    <!-- Header -->
                                    <div class="card-header card-header-content-between">
                                        <h4 class="card-header-title" style="margin-top: 12px; margin-bottom: 12px;">Products Threshold Stock</h4>
                                    </div>
                                    <!-- End Header -->

                                    <!-- Body -->
                                    <div class="card-body card-body-height">
                                        <!-- List Group -->
                                        <ul class="list-group list-group-flush list-group-start-bordered">
                                            <?php
                                            $sql = "SELECT s.*, p.product_media_1
                                        FROM stock s
                                        INNER JOIN product p ON s.product_sku = p.product_sku
                                        WHERE s.stock_created_at = (
                                            SELECT MAX(stock_created_at)
                                            FROM stock
                                            WHERE product_sku = s.product_sku
                                        )
                                        AND s.stock_qty < 5
                                        ORDER BY s.stock_created_at DESC";

                                            $result = $mysqli->query($sql);

                                            if ($result->num_rows > 0) { ?>
                                                <?php while ($row = $result->fetch_assoc()) {
                                                ?>
                                                    <!-- Item -->
                                                    <li class="list-group-item">
                                                        <div class="list-group-item-action">
                                                            <div class="row">
                                                                <div class="col-sm mb-2 mb-sm-0">
                                                                    SKU : <?php echo $row['product_sku'] ?>
                                                                    <h2 class="fw-normal my-1"><?php echo $row['product_name'] ?></h2>
                                                                    <h5 class="text-inherit mb-0">
                                                                        Category : <?php echo $row['product_category'] ?>
                                                                    </h5>
                                                                    <span class="text-body small"><?php echo $row['stock_qty'] ?> pcs</span>
                                                                </div>

                                                                <div class="col-sm-auto">
                                                                    <img src="assets/img/product-img/<?php echo $row['product_media_1'] ?>" class="rounded-2" width="60px" alt="Image Description">
                                                                </div>
                                                            </div>
                                                            <!-- End Row -->
                                                        </div>
                                                    </li>
                                                    <!-- End Item -->
                                                <?php } ?>
                                            <?php } ?>
                                        </ul>
                                        <!-- End List Group -->
                                    </div>
                                    <!-- End Body -->
                                </div>
                                <!-- End Card -->
                            </div>
                            <!-- End Col -->
                        </div>
                    </div>
                    <!-- End Row -->

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
                                        <input id="datatableSearch" type="search" class="form-control" placeholder="Search stock" aria-label="Search stock">
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
                                        <th class="table-column-pe-0">Product</th>
                                        <th>SKU</th>
                                        <th>Purchase Price</th>
                                        <th>Selling Price</th>
                                        <th>COGS</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $sql = "SELECT s.*, p.product_media_1
                                    FROM stock s
                                    INNER JOIN product p ON s.product_sku = p.product_sku
                                    ORDER BY s.stock_created_at DESC";

                                    $result = $mysqli->query($sql);

                                    if ($result->num_rows > 0) { ?>
                                        <?php while ($row = $result->fetch_assoc()) {
                                        ?>
                                            <tr>
                                                <td class="table-column-pe-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <img class="avatar avatar-lg" src="./assets/img/product-img/<?php echo $row['product_media_1'] ?>" alt="Image Description">
                                                        </div>
                                                        <a href="stockDetails.php?product_sku=<?php echo $row['product_sku'] ?>" class="flex-grow-1 ms-3">
                                                            <h5 class="text-inherit mb-0"><?php echo $row['product_name'] ?></h5>
                                                            <span class="text-inherit mb-0 text-muted">Color : <?php echo $row['product_color']; ?></span>
                                                        </a>
                                                    </div>
                                                </td>
                                                <td><?php echo $row['product_sku'] ?></td>
                                                <td><?php echo "Rp " . number_format($row['purchase_price'], 0, ',', '.'); ?></td>
                                                <td><?php echo "Rp " . number_format($row['selling_price'], 0, ',', '.'); ?></td>
                                                <td><?php echo "Rp " . number_format($row['COGS'], 0, ',', '.'); ?></td>
                                                <td><?php echo $row['stock_qty'] ?></td>
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
            </div>
        </div>
        <!-- End Content -->

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
                        <?php
                        $sql = "
                                SELECT
                                    10 AS stock,
                                    COUNT(CASE WHEN stock_qty < 10 THEN 1 END) AS total_product
                                FROM
                                    stock
                                UNION ALL
                                SELECT
                                    50 AS stock,
                                    COUNT(CASE WHEN stock_qty < 50 THEN 1 END) AS total_product
                                FROM
                                    stock
                                UNION ALL
                                SELECT
                                    100 AS stock,
                                    COUNT(CASE WHEN stock_qty < 100 THEN 1 END) AS total_product
                                FROM
                                    stock
                            ";

                        $result = $mysqli->query($sql);

                        $data = [];
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $data[] = $row['total_product'];
                            }
                        }

                        // Echo hasilnya di sini
                        echo "[";
                        echo implode(",", $data);
                        echo "]";
                        ?>

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