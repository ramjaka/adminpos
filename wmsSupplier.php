<?php
require_once 'controller/Session.php';
$page = 'wms-supplier';
$page_access = array('1', '10');

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
                                <li class="breadcrumb-item active" aria-current="page">Supplier</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Supplier</h1>
                    </div>
                    <!-- End Col -->

                    <div class="col-sm-auto">
                        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal"><i class="bi bi-building me-1"></i> Add Supplier</a>
                    </div>
                    <!-- End Col -->
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
                                <input id="datatableSearch" type="search" class="form-control" placeholder="Search supplier" aria-label="Search users">
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

                    <div class="d-grid d-sm-flex gap-2">

                        <!-- Modal -->
                        <div id="addSupplierModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addSupplierModalTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addSupplierModalTitle">Create supplier</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- form -->
                                        <form action="controller/WmsAddSupplier.php" method="POST">
                                            <input name="username_creator" type="hidden" value="<?php echo $username ?>">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="mb-4">
                                                        <label class="form-label" for="supplierName">Supplier Name</label>
                                                        <input name="supplier_name" type="text" id="supplierName" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-4">
                                                        <label class="form-label" for="phoneNumber">Phone number / WA</label>
                                                        <input name="supplier_phone" type="text" class="js-input-mask form-control" id="phoneLabel" placeholder="+(xx) xxx-xxx-xxx-xxx" data-hs-mask-options='{
                                                            "mask": "+(00) 000-000-000-000"
                                                        }' required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-4">
                                                        <label class="form-label" for="supplierName">Supplier Email</label>
                                                        <input name="supplier_email" type="email" id="supplierName" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="mb-4">
                                                        <label class="form-label" for="addressLabel">Supplier Address</label>
                                                        <textarea name="supplier_address" id="addressLabel" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-white me-2" data-bs-dismiss="modal">Close</button>
                                                <button name="btn-save-supplier" type="submit" class="btn btn-primary">Save supplier</button>
                                            </div>
                                        </form>
                                        <!-- end form -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal -->
                    </div>
                </div>
                <!-- End Header -->

                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="datatableDatepickerFilter" class="js-datatable table table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
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
                    "pageLength": 12,
                    "isResponsive": false,
                    "isShowPaging": false,
                    "pagination": "datatablePagination"
                    }'>
                        <thead class="thead-light">
                            <tr>
                                <th class="table-column-pe-0">Supplier name</th>
                                <th>Supplier Phone</th>
                                <th>Supplier Email</th>
                                <th>Supplier Address</th>
                                <th>Supplier Status</th>
                                <th>Supplier date created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $sql = "SELECT * FROM `supplier` ORDER BY supplier_created_at DESC";

                            $result = $mysqli->query($sql);

                            if ($result->num_rows > 0) { ?>
                                <?php while ($row = $result->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td class="table-column-pe-0">
                                            <a href="wmsSupplierDetails.php?supplier_id=<?php echo $row['supplier_id']; ?>" class="text-muted">
                                                <?php echo $row['supplier_name'] ?>
                                            </a>
                                        </td>
                                        <td>
                                            <input type="text" class="js-input-mask border-0" id="phoneLabel" placeholder="+(xx) xxx-xxx-xxx-xxx" data-hs-mask-options='{
                                            "mask": "+(00) 000-000-000-000"
                                            }' value="<?php echo $row['supplier_phone'] ?>" readonly>
                                        </td>
                                        <td><?php echo $row['supplier_email'] ?></td>
                                        <td>
                                            <?php echo $row['supplier_address'] ?>
                                        </td>
                                        <td>
                                            <span class="legend-indicator
                                                <?php if ($row['supplier_status'] === 'active') {
                                                    echo 'bg-success';
                                                } elseif ($row['supplier_status'] === 'hold') {
                                                    echo 'bg-warning';
                                                } else {
                                                    echo 'bg-danger';
                                                } ?>">
                                            </span>
                                            <?php echo $row['supplier_status']; ?>
                                        </td>
                                        <td>
                                            <?php echo $row['supplier_created_at'] ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="wmsSupplierDetails.php?supplier_id=<?php echo $row['supplier_id']; ?>" class="btn btn-white btn-sm">
                                                    <i class="bi bi-building"></i> Profile
                                                </a>
                                            </div>
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
</body>

</html>