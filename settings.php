<?php
require_once 'controller/Session.php';
$page = 'settings';
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

    <?php include_once('components/header.php') ?>

    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->

    <!-- Navbar Aside -->
    <?php include_once('components/aside.php') ?>
    <!-- End Navbar Aside -->

    <main id="content" role="main" class="main">
        <!-- Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-end">
                    <div class="col-sm mb-2 mb-sm-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-no-gutter">
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:;">Users</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Settings</h1>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-lg-3">
                    <!-- Navbar -->
                    <div class="navbar-expand-lg navbar-vertical mb-3 mb-lg-5">
                        <!-- Navbar Toggle -->
                        <div class="d-grid">
                            <button type="button" class="navbar-toggler btn btn-white mb-3" data-bs-toggle="collapse" data-bs-target="#navbarVerticalNavMenu" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarVerticalNavMenu">
                                <span class="d-flex justify-content-between align-items-center">
                                    <span class="text-dark">Menu</span>

                                    <span class="navbar-toggler-default">
                                        <i class="bi-list"></i>
                                    </span>

                                    <span class="navbar-toggler-toggled">
                                        <i class="bi-x"></i>
                                    </span>
                                </span>
                            </button>
                        </div>
                        <!-- End Navbar Toggle -->

                        <!-- Navbar Collapse -->
                        <div id="navbarVerticalNavMenu" class="collapse navbar-collapse">
                            <ul id="navbarSettings" class="js-sticky-block js-scrollspy card card-navbar-nav nav nav-tabs nav-lg nav-vertical" data-hs-sticky-block-options='{
                     "parentSelector": "#navbarVerticalNavMenu",
                     "targetSelector": "#header",
                     "breakpoint": "lg",
                     "startPoint": "#navbarVerticalNavMenu",
                     "endPoint": "#stickyBlockEndPoint",
                     "stickyOffsetTop": 20
                   }'>
                                <li class="nav-item">
                                    <a class="nav-link active" href="#content">
                                        <i class="bi-person nav-icon"></i> Basic information
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#passwordSection">
                                        <i class="bi-key nav-icon"></i> Password
                                    </a>
                                </li>
                                <li class="nav-item <?php echo $username === 'admindefault' ? 'd-none' : ''; ?>">
                                    <a class="nav-link" href="#deleteAccountSection">
                                        <i class="bi-trash nav-icon"></i> Delete account
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- End Navbar Collapse -->
                    </div>
                    <!-- End Navbar -->
                </div>

                <?php
                $sql = "SELECT * FROM `user` WHERE user_id = '$user_id' ORDER BY user_status";

                $result = $mysqli->query($sql);

                if ($result->num_rows > 0) { ?>
                    <?php while ($row = $result->fetch_assoc()) {
                    ?>
                        <div class="col-lg-9">
                            <div class="d-grid gap-3 gap-lg-2">

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
                                <div class="card">
                                    <div class="card-header">
                                        <h2 class="card-title h4">Basic information</h2>
                                    </div>

                                    <!-- Body -->
                                    <div class="card-body">
                                        <!-- Form -->
                                        <form>
                                            <!-- Form -->
                                            <div class="row mb-4">
                                                <label for="firstNameLabel" class="col-sm-3 col-form-label form-label">
                                                    Full name
                                                </label>

                                                <div class="col-sm-9">
                                                    <div class="input-group input-group-sm-vertical">
                                                        <input type="text" class="form-control " name="first_name" id="firstNameLabel" value="<?php echo $row['first_name'] ?>" disabled>
                                                        <input type="text" class="form-control " name="last_name" id="lastNameLabel" value="<?php echo $row['last_name'] ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Form -->

                                            <!-- Form -->
                                            <div class="row mb-4">
                                                <label for="usernameLabel" class="col-sm-3 col-form-label form-label">Username</label>

                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control " name="username" id="usernameLabel" value="<?php echo $row['username'] ?>" disabled>
                                                </div>
                                            </div>
                                            <!-- End Form -->

                                            <!-- Form -->
                                            <div class="js-add-field row mb-4" data-hs-add-field-options='{
                                                "template": "#addPhoneFieldTemplate",
                                                "container": "#addPhoneFieldContainer",
                                                "defaultCreated": 0
                                            }'>
                                                <label for="phoneLabel" class="col-sm-3 col-form-label form-label">Phone</label>

                                                <div class="col-sm-9">
                                                    <div class="input-group input-group-sm-vertical">
                                                        <input type="text" class="js-input-mask form-control" name="phone" id="phoneLabel" placeholder="+(xx) xxx-xxx-xxx-xxx" data-hs-mask-options='{
                                                    "mask": "+(00) 000-000-000-000"
                                                }' value="<?php echo $row['phone'] ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- End Form -->
                                        </form>
                                        <!-- End Form -->
                                    </div>
                                    <!-- End Body -->
                                </div>
                                <!-- End Card -->

                                <!-- Card -->
                                <div id="passwordSection" class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Change your password</h4>
                                    </div>

                                    <!-- Body -->
                                    <div class="card-body">
                                        <!-- Form -->
                                        <form action="controller/SettingsChangePassword.php" method="POST">
                                            <input type="hidden" name="user_id" value="<?php echo $row['user_id'] ?>">
                                            <!-- Form -->
                                            <div class="row mb-4">
                                                <label for="editUserModalCurrentPasswordLabel" class="col-sm-3 col-form-label form-label">Current password</label>

                                                <div class="col-sm-9">
                                                    <!-- Input Group -->
                                                    <div class="input-group input-group-merge">
                                                        <input name="currentPassword" type="password" class="js-toggle-password form-control" tabindex="1" id="multiToggleCurrentPasswordLabel" placeholder="Enter current password" data-hs-toggle-password-options='{
                                                 "target": [".js-change-password-multi-1", ".js-change-password-multi-2"],
                                                 "defaultClass": "bi-eye-slash",
                                                 "showClass": "bi-eye",
                                                 "classChangeTarget": "#showMultiPassIcon1"
                                               }'>
                                                        <a class="js-change-password-multi-1 input-group-append input-group-text" href="javascript:;">
                                                            <i id="showMultiPassIcon1"></i>
                                                        </a>
                                                    </div>
                                                    <!-- End Input Group -->
                                                </div>
                                            </div>
                                            <!-- End Form -->

                                            <!-- Form -->
                                            <div class="row mb-4">
                                                <label for="editUserModalNewPassword" class="col-sm-3 col-form-label form-label">New
                                                    password</label>

                                                <div class="col-sm-9">
                                                    <div class="input-group input-group-merge">
                                                        <input name="newPassword" type="password" class="js-toggle-password form-control" tabindex="2" id="multiToggleNewPasswordLabel" placeholder="Enter new password" data-hs-toggle-password-options='{
                                                 "target": [".js-change-password-multi-1", ".js-change-password-multi-2"],
                                                 "defaultClass": "bi-eye-slash",
                                                 "showClass": "bi-eye",
                                                 "classChangeTarget": "#showMultiPassIcon2"
                                               }' minlength="6">
                                                        <a class="js-change-password-multi-2 input-group-append input-group-text" href="javascript:;">
                                                            <i id="showMultiPassIcon2"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Form -->

                                            <!-- Form -->
                                            <div class="row mb-4">
                                                <label for="editUserModalConfirmNewPasswordLabel" class="col-sm-3 col-form-label form-label">Confirm new password</label>

                                                <div class="col-sm-9">
                                                    <div class="input-group input-group-merge">
                                                        <input name="confirmPassword" type="password" class="js-toggle-password form-control" tabindex="3" id="multiToggleNewPasswordLabel" placeholder="Comfirm your new password" data-hs-toggle-password-options='{
                                                 "target": [".js-change-password-multi-1", ".js-change-password-multi-2"],
                                                 "defaultClass": "bi-eye-slash",
                                                 "showClass": "bi-eye",
                                                 "classChangeTarget": "#showMultiPassIcon3"
                                               }' minlength="6" required>
                                                        <a class="js-change-password-multi-2 input-group-append input-group-text" href="javascript:;">
                                                            <i id="showMultiPassIcon3"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Form -->

                                            <input type="hidden" name="username" value="<?php echo $row['username'] ?>">

                                            <div class="d-flex justify-content-end">
                                                <div class="d-flex gap-3">
                                                    <button name="btn-password-user" type="submit" class="btn btn-primary">Save
                                                        changes</button>
                                                </div>
                                            </div>
                                        </form>
                                        <!-- End Form -->
                                    </div>
                                    <!-- End Body -->
                                </div>
                                <!-- End Card -->

                                <!-- Card -->
                                <div id="deleteAccountSection" class="card <?php echo $username === 'admindefault' ? 'd-none' : ''; ?>">
                                    <div class="card-header">
                                        <h4 class="card-title">Delete your account</h4>
                                    </div>

                                    <!-- Body -->
                                    <div class="card-body">
                                        <p class="card-text">When you delete your account, you lose access to your account
                                            services, and we permanently delete your personal data.</p>
                                        <form action="controller/SettingsDeleteAccount.php" method="POST">
                                            <div class="d-flex justify-content-end gap-3">

                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>
                                            </div>

                                            <div id="deleteModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalCenterTitle">Delete account?
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <p>Are you sure want to delete "<strong><?php echo $username ?></strong>"<br>You can't undo this action.
                                                            </p>
                                                            <div class="alert alert-soft-danger" role="alert">
                                                                <h3 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Warning</h3>
                                                                <p>When you delete your account, you lose access to your
                                                                    account
                                                                    services, and we permanently delete your personal data.</p>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="user_id" value="<?php echo $row['user_id'] ?>">
                                                        <input type="hidden" name="username" value="<?php echo $row['username'] ?>">

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-white" data-bs-dismiss="modal">Close</button>
                                                            <button name="btn-suspend-user" type="submit" class="btn btn-danger">Delete</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- End Body -->
                                </div>
                                <!-- End Card -->
                            </div>

                            <!-- Sticky Block End Point -->
                            <div id="stickyBlockEndPoint"></div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Content -->

        <!-- Footer -->

        <div class="footer">
            <div class="row justify-content-between align-items-center">
                <div class="col">
                    <p class="fs-6 mb-0">&copy; Front. <span class="d-none d-sm-inline-block">2022 Htmlstream.</span>
                    </p>
                </div>
                <!-- End Col -->

                <div class="col-auto">
                    <div class="d-flex justify-content-end">
                        <!-- List Separator -->
                        <ul class="list-inline list-separator">
                            <li class="list-inline-item">
                                <a class="list-separator-link" href="#">FAQ</a>
                            </li>

                            <li class="list-inline-item">
                                <a class="list-separator-link" href="#">License</a>
                            </li>

                            <li class="list-inline-item">
                                <!-- Keyboard Shortcuts Toggle -->
                                <button class="btn btn-ghost-secondary btn btn-icon btn-ghost-secondary rounded-circle" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasKeyboardShortcuts" aria-controls="offcanvasKeyboardShortcuts">
                                    <i class="bi-command"></i>
                                </button>
                                <!-- End Keyboard Shortcuts Toggle -->
                            </li>
                        </ul>
                        <!-- End List Separator -->
                    </div>
                </div>
                <!-- End Col -->
            </div>
            <!-- End Row -->
        </div>

        <!-- End Footer -->
    </main>
    <!-- ========== END MAIN CONTENT ========== -->

    <!-- ========== SECONDARY CONTENTS ========== -->

    <!-- ========== END SECONDARY CONTENTS ========== -->

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

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>

    <!-- update data -->
    <script src="./controller/Settings.js"></script>

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
</body>

</html>