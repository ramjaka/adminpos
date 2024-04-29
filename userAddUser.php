<?php
require_once 'controller/Session.php';
$page = 'user-add-user';
$page_access = array('1', '18');

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
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- advanced select -->
    <link rel="stylesheet" href="./assets/vendor/tom-select/dist/css/tom-select.bootstrap5.css">
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
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="javascript:;">User</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Add User</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Add User</h1>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <!-- <div class="d-flex justify-content-center">
                <div class="alert alert-soft-danger d-none w-50" role="alert" id="dynamicAlertError">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-octagon"></i>
                        </div>
                        <div class="flex-grow-1 ms-2" id="alertMessageError">
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Step Form -->
            <form method="POST" id="addUserForm" action="controller/UserAddUser.php">
                <div class="row justify-content-lg-center">
                    <div class="col-lg-8">
                        <!-- Card -->
                        <div class="card card-lg active">
                            <!-- Body -->
                            <div class="card-body">

                                <!-- Form -->
                                <div class="row mb-4">
                                    <label for="firstNameLabel" class="col-sm-3 col-form-label form-label">
                                        Full name
                                    </label>

                                    <div class="col-sm-9">
                                        <div class="input-group input-group-sm-vertical">
                                            <input name="first_name" type="text" class="form-control <?php echo isset($_GET['firstNameValidation']) ? 'is-invalid' : ''; ?>" name="first_name" id="firstNameLabel" placeholder="John" aria-label="John">
                                            <input name="last_name" type="text" class="form-control " name="last_name" id="lastNameLabel" placeholder="Doe" aria-label="Doe">
                                            <?php if (isset($_GET['firstNameValidation'])) { ?>
                                                <span class="invalid-feedback"><?php echo urldecode($_GET['firstNameValidation']); ?></span>
                                            <?php } ?>
                                        </div>
                                        <span id="firstNameError" class="text-danger form-error"></span>
                                        <span id="lastNameError" class="text-danger form-error"></span>
                                    </div>
                                </div>
                                <!-- End Form -->

                                <!-- Form -->
                                <div class="row mb-4">
                                    <label for="usernameLabel" class="col-sm-3 col-form-label form-label">Username</label>

                                    <div class="col-sm-9">
                                        <input name="username" type="text" class="form-control <?php echo isset($_GET['usernameValidation']) ? 'is-invalid' : ''; ?>" name="username" id="usernameLabel" placeholder="">

                                        <?php if (isset($_GET['usernameValidation'])) { ?>
                                            <span class="invalid-feedback"><?php echo urldecode($_GET['usernameValidation']); ?></span>
                                        <?php } ?>
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
                                            <input name="phone" type="text" class="js-input-mask form-control <?php echo isset($_GET['phoneValidation']) ? 'is-invalid' : ''; ?>" name="user_phone" id="phoneLabel" placeholder="+(xx) xxx-xxx-xxx-xxx" data-hs-mask-options='{
                                                "mask": "+(00) 000-000-000-000"
                                            }'>
                                            <?php if (isset($_GET['phoneValidation'])) { ?>
                                                <span class="invalid-feedback"><?php echo urldecode($_GET['phoneValidation']); ?></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- End Form -->

                                <!-- Form -->
                                <div class="row mb-4">
                                    <label for="levelLabel" class="col-sm-3 col-form-label form-label">Access</label>

                                    <div class="col-sm-9">
                                        <!-- Select -->
                                        <div class="tom-select-custom">
                                            <select name="access[]" class="js-select form-select <?php echo isset($_GET['accessValidation']) ? 'is-invalid' : ''; ?>" autocomplete="off" multiple data-hs-tom-select-options='{
                                                "singleMultiple": true,
                                                "hideSelected": false,
                                                "placeholder": ""
                                            }'>
                                                <option value="">Select a access...</option>
                                                <option value="1">All Access</option>
                                                <optgroup label="POS">
                                                    <option value="2">Cashier</option>
                                                    <option value="3">Sales List</option>
                                                    <option value="4">Promotion</option>
                                                    <option value="5">CRM</option>
                                                </optgroup>
                                                <optgroup label="WMS">
                                                    <option value="6">Purchase</option>
                                                    <option value="7">Purchasing List</option>
                                                    <option value="8">Stock</option>
                                                    <option value="9">Spending</option>
                                                    <option value="10">Supplier</option>
                                                    <option value="11">Products</option>
                                                </optgroup>
                                                <optgroup label="BANK">
                                                    <option value="12">Account</option>
                                                    <option value="13">Debts</option>
                                                    <option value="14">Receivables</option>
                                                    <option value="15">Mutation</option>
                                                    <option value="16">Accounting</option>
                                                </optgroup>
                                                <optgroup label="USER">
                                                    <option value="17">Overview</option>
                                                    <option value="18">Add User</option>
                                                </optgroup>
                                            </select>
                                            <?php if (isset($_GET['accessValidation'])) { ?>
                                                <span class="invalid-feedback"><?php echo urldecode($_GET['accessValidation']); ?></span>
                                            <?php } ?>
                                        </div>
                                        <!-- End Select -->
                                    </div>
                                </div>
                                <!-- End Form -->

                                <!-- Form -->
                                <div class="row">
                                    <label class="col-sm-3 col-form-label form-label" for="signupSimpleLoginPassword">Password</label>

                                    <div class="col-sm-9">
                                        <div id="signupSimpleLoginPassword1" class="input-group input-group-merge " data-hs-validation-validate-class>
                                            <input name="password" type="password" class="js-toggle-password form-control form-control-lg" name="user_password" id="signupSimpleLoginPassword" placeholder="6+ characters required" data-hs-toggle-password-options='{
                                       "target": "#changePassTarget",
                                       "defaultClass": "bi-eye-slash",
                                       "showClass": "bi-eye",
                                       "classChangeTarget": "#changePassIcon"
                                     }'>
                                            <a id="changePassTarget" class="input-group-append input-group-text" href="javascript:;">
                                                <i id="changePassIcon" class="bi-eye"></i>
                                            </a>
                                        </div>
                                        <span id="userPasswordError" class="text-danger form-error"></span>
                                    </div>
                                </div>
                                <!-- End Form -->
                            </div>
                            <!-- End Body -->

                            <input type="hidden" name="username_creator" value="<?php echo $username ?>">

                            <!-- Footer -->
                            <div class="card-footer d-flex justify-content-end align-items-center">
                                <button name="btn-create-user" type="submit" class="btn btn-primary">
                                    Add user
                                </button>
                            </div>
                            <!-- End Footer -->
                        </div>
                        <!-- End Card -->
                    </div>
                </div>
            </form>
            <!-- End Step Form -->
        </div>
        <!-- End Content -->

        <!-- Footer -->

        <?php include_once('components/footer.php') ?>

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
    <script src="./assets/vendor/imask/dist/imask.min.js"></script>
    <script src="./assets/vendor/hs-toggle-password/dist/js/hs-toggle-password.js"></script>

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>

    <!-- insert data -->
    <script src="./controller/UsersAddUser.js"></script>

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

    <!-- Advanced Select -->
    <script src="./assets/vendor/tom-select/dist/js/tom-select.complete.min.js"></script>

    <script>
        (function() {
            // INITIALIZATION OF SELECT
            // =======================================================
            HSCore.components.HSTomSelect.init('.js-select')
        })();
    </script>

    <script>
        (function() {
            // INITIALIZATION OF INPUT MASK
            // =======================================================
            HSCore.components.HSMask.init('.js-input-mask')
        })();
    </script>

    <script>
        (function() {
            // INITIALIZATION OF TOGGLE PASSWORD
            // =======================================================
            new HSTogglePassword('.js-toggle-password')
        })();
    </script>
</body>

</html>