<?php
require_once 'controller/Session.php';
$page = 'users-overview';
$page_access = array('1', '17');

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
                                <li class="breadcrumb-item active" aria-current="page">Overview</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Overview</h1>
                    </div>
                    <!-- End Col -->
                    <div class="col-sm-auto">
                        <a class="btn btn-primary" href="userAddUser.php">
                            <i class="bi-person-plus-fill me-1"></i> Add user
                        </a>
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
                                <input id="datatableSearch" type="search" class="form-control" placeholder="Search users" aria-label="Search users">
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
                                <i class="bi-filter me-1"></i> Filter
                                <!-- <span class="badge bg-soft-dark text-dark rounded-circle ms-1">2</span> -->
                            </button>

                            <div class="dropdown-menu dropdown-menu-sm-end dropdown-card card-dropdown-filter-centered" aria-labelledby="usersFilterDropdown" style="min-width: 22rem;">
                                <!-- Card -->
                                <div class="card">
                                    <div class="card-header card-header-content-between">
                                        <h5 class="card-header-title">Filter users</h5>

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
                                                    <small class="text-cap text-body">Position</small>

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
                                                        <!-- End Select -->
                                                    </div>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col-sm mb-4">
                                                    <small class="text-cap text-body">Status</small>

                                                    <!-- Select -->
                                                    <div class="tom-select-custom">
                                                        <select class="js-select js-datatable-filter form-select form-select-sm" data-target-column-index="3" data-hs-tom-select-options='{
                                                        "placeholder": "Any status",
                                                        "searchInDropdown": false,
                                                        "hideSearch": true,
                                                        "dropdownWidth": "10rem"
                                                        }'>
                                                            <option value=" ">Any status</option>
                                                            <option value="Active" data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-success"></span>Active</span>'>
                                                                Active</option>
                                                            <option value="Hold" data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-warning"></span>Hold</span>'>
                                                                Hold</option>
                                                            <option value="Suspended" data-option-template='<span class="d-flex align-items-center"><span class="legend-indicator bg-danger"></span>Suspended</span>'>
                                                                Suspended</option>
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
                              "targets": [],
                              "orderable": false
                            }],
                           "order": [],
                           "info": {
                             "totalQty": "#datatableWithPaginationInfoTotalQty"
                           },
                           "search": "#datatableSearch",
                           "entries": "#datatableEntries",
                           "pageLength": 10,
                           "isResponsive": false,
                           "isShowPaging": false,
                           "pagination": "datatablePagination"
                         }'>
                        <thead class="thead-light">
                            <tr>
                                <th class="table-column-pe-0">Name</th>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Access</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $sql = "SELECT * FROM `user`";

                            $result = $mysqli->query($sql);

                            if ($result->num_rows > 0) { ?>
                                <?php while ($row = $result->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td class="table-column-pe-0">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-soft-primary avatar-circle me-2">
                                                    <span class="avatar-initials"><?php echo substr($row['first_name'], 0, 1) ?></span>
                                                </div> <?php echo $row['first_name'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block mb-0"><?php echo $row['username'] ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block fs-5">
                                                    <input name="member_phone" type="text" class="js-input-mask border-0" name="user_phone" id="phoneLabel" placeholder="+(xx) xxx-xxx-xxx-xxx" data-hs-mask-options='{
                                                    "mask": "+(00) 000-000-000-000"
                                                    }' value="<?php echo $row['phone'] ?>" readonly>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block fs-5">
                                                    <?php
                                                    $access_value = $row['access'];
                                                    $access_values_array = explode(',', $access_value);
                                                    foreach ($access_values_array as $value) {
                                                        if ($value == 1) {
                                                            echo "All Access";
                                                        } elseif ($value == 2) {
                                                            echo "<strong>POS</strong> Cashier";
                                                        } elseif ($value == 3) {
                                                            echo "<strong>POS</strong> Sales List";
                                                        } elseif ($value == 4) {
                                                            echo "<strong>POS</strong> Promotion";
                                                        } elseif ($value == 5) {
                                                            echo "<strong>POS</strong> CRM";
                                                        } elseif ($value == 6) {
                                                            echo "<strong>WMS</strong> Purchase";
                                                        } elseif ($value == 7) {
                                                            echo "<strong>WMS</strong> Purchasing List";
                                                        } elseif ($value == 8) {
                                                            echo "<strong>WMS</strong> Stock";
                                                        } elseif ($value == 9) {
                                                            echo "<strong>WMS</strong> Spending";
                                                        } elseif ($value == 10) {
                                                            echo "<strong>WMS</strong> Supplier";
                                                        } elseif ($value == 11) {
                                                            echo "<strong>WMS</strong> Products";
                                                        } elseif ($value == 12) {
                                                            echo "<strong>Bank</strong> Account";
                                                        } elseif ($value == 13) {
                                                            echo "<strong>Bank</strong> Debts";
                                                        } elseif ($value == 14) {
                                                            echo "<strong>Bank</strong> Receivables";
                                                        } elseif ($value == 15) {
                                                            echo "<strong>Bank</strong> Mutation";
                                                        } elseif ($value == 16) {
                                                            echo "<strong>Bank</strong> Accounting";
                                                        } elseif ($value == 17) {
                                                            echo "<strong>USER</strong> Overview";
                                                        } elseif ($value == 18) {
                                                            echo "<strong>USER</strong> Add User";
                                                        }
                                                        if ($value !== end($access_values_array)) {
                                                            echo ", ";
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="legend-indicator
                                                    <?php if ($row['user_status'] === 'active') {
                                                        echo 'bg-success';
                                                    } elseif ($row['user_status'] === 'hold') {
                                                        echo 'bg-warning';
                                                    } else {
                                                        echo 'bg-danger';
                                                    } ?>">
                                                </span>
                                                <?php echo $row['user_status']; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-white btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditUserProfile<?php echo $row['user_id']; ?>">
                                                <i class="bi bi-person me-1"></i> Profile
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Edit user -->
                                    <div class="modal fade" id="modalEditUserProfile<?php echo $row['user_id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editUserModalLabel">Edit user</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <!-- Body -->
                                                <div class="modal-body">
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

                                                    <!-- Tab Content -->
                                                    <div class="tab-content" id="editUserModalTabContent">
                                                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
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
                                                                    <input type="text" class="form-control " name="username" value="<?php echo $row['username']; ?>" disabled>
                                                                    <span id="usernameError" class="text-danger form-error"></span>
                                                                </div>
                                                            </div>
                                                            <!-- End Form -->

                                                            <!-- Form -->
                                                            <div class="js-add-field row mb-4" data-hs-add-field-options='{
                                                                        "template": "#addPhoneFieldTemplate",
                                                                        "container": "#addPhoneFieldContainer",
                                                                        "defaultCreated": 0
                                                                    }'>
                                                                <label for="phoneLabel" class="col-sm-3 col-form-label form-label">Phone <span class="form-label-secondary">(Optional)</span></label>

                                                                <div class="col-sm-9">
                                                                    <div class="input-group input-group-sm-vertical">
                                                                        <input type="text" class="js-input-mask form-control" name="user_phone" id="phoneLabel" placeholder="+(xx) xxx-xxx-xxx-xxx" data-hs-mask-options='{
                                                                                "mask": "+(00) 000-000-000-000"
                                                                            }' value="<?php echo $row['phone']; ?>" disabled>
                                                                    </div>
                                                                    <span id="userPhoneError" class="text-danger form-error"></span>
                                                                </div>
                                                            </div>

                                                            <!-- End Form -->

                                                            <!-- Form -->
                                                            <form action="controller/UserEditUser.php" method="POST">
                                                                <div class="row mb-4 <?php echo $row['username'] === 'admindefault' ? 'd-none' : ''; ?>"">
                                                                    <label for=" levelLabel" class="col-sm-3 col-form-label form-label">Access</label>

                                                                    <div class="col-sm-9">
                                                                        <!-- Select -->
                                                                        <div class="tom-select-custom">
                                                                            <select name="access[]" class="js-select form-select" autocomplete="off" multiple data-hs-tom-select-options='{
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
                                                                        </div>
                                                                        <!-- End Select -->
                                                                    </div>
                                                                </div>

                                                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                                                <input type="hidden" name="username_creator" value="<?php echo $username ?>">

                                                                <div class="d-flex justify-content-end">
                                                                    <div class="d-flex gap-3">
                                                                        <button type="button" class="btn btn-white" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                                                        <button type="submit" name="btn-edit-access" class="btn btn-primary">Save changes</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <!-- End Form -->

                                                            <p class="my-5 <?php echo $row['username'] === 'admindefault' ? 'd-none' : ''; ?>"">Change Password</p>

                                                            <!-- Form -->
                                                            <form action=" controller/AdminChangePassword.php" method="POST">
                                                                <input type="hidden" name="user_id" value="<?php echo $row['user_id'] ?>">
                                                                <!-- Form -->
                                                            <div class="row mb-4 <?php echo $row['username'] === 'admindefault' ? 'd-none' : ''; ?>"">
                                                                    <label for=" editUserModalCurrentPasswordLabel" class="col-sm-3 col-form-label form-label">Current password</label>

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
                                                            <div class="row mb-4 <?php echo $row['username'] === 'admindefault' ? 'd-none' : ''; ?>"">
                                                                    <label for=" editUserModalNewPassword" class="col-sm-3 col-form-label form-label">New
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
                                                            <div class="row mb-4 <?php echo $row['username'] === 'admindefault' ? 'd-none' : ''; ?>"">
                                                                    <label for=" editUserModalConfirmNewPasswordLabel" class="col-sm-3 col-form-label form-label">Confirm new password</label>

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

                                                            <input type="hidden" name="username" value="<?php echo $username ?>">

                                                            <div class="d-flex justify-content-end <?php echo $row['username'] === 'admindefault' ? 'd-none' : ''; ?>"">
                                                                    <div class=" d-flex gap-3">
                                                                <button type="button" class="btn btn-white" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                                                                <button name="btn-password-user" type="submit" class="btn btn-primary">Save changes</button>
                                                            </div>
                                                        </div>
                                                        </form>

                                                        <p class="my-5 <?php echo $row['username'] === 'admindefault' ? 'd-none' : ''; ?>"">Change Status</p>

                                                            <!-- Form -->
                                                            <form action=" controller/UserEditUser.php" method="POST">
                                                            <input type="hidden" name="user_id" value="<?php echo $row['user_id'] ?>">
                                                            <!-- Form -->
                                                        <div class="row mb-4 <?php echo $row['username'] === 'admindefault' ? 'd-none' : ''; ?>">
                                                            <label for=" editUserModalCurrentPasswordLabel" class="col-sm-3 col-form-label form-label">User status</label>

                                                            <div class="col-sm-9">
                                                                <!-- Input Group -->
                                                                <div class="d-flex justify-content-end">
                                                                    <button type="submit" name="active-button" class="btn btn-success ms-2 <?php echo $row['user_status'] === 'active' ? 'd-none' : ''; ?>"><i class="bi bi-play"></i> Active</button>

                                                                    <button type="submit" name="hold-button" class="btn btn-warning text-white ms-2 <?php echo $row['user_status'] === 'hold' ? 'd-none' : ''; ?>"><i class="bi bi-pause-circle"></i> Hold</button>

                                                                    <button type="submit" name="suspend-button" class="btn btn-danger ms-2 <?php echo $row['user_status'] === 'suspend' ? 'd-none' : ''; ?>"><i class="bi bi-stop-circle"></i> Suspend</button>
                                                                </div>
                                                                <!-- End Input Group -->
                                                            </div>
                                                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                                            <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                                                            <!-- End Form -->
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <!-- End Tab Content -->
                                                </div>
                                                <!-- End Body -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Edit user -->
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
    <script src="./assets/vendor/hs-toggle-password/dist/js/hs-toggle-password.js"></script>

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

    <script>
        (function() {
            // INITIALIZATION OF TOGGLE PASSWORD
            // =======================================================
            new HSTogglePassword('.js-toggle-password')
        })();
    </script>

    <!-- End Style Switcher JS -->
</body>

</html>