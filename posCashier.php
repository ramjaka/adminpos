<?php
require_once 'controller/Session.php';
$page = 'pos-cashier';
$page_access = array('1', '2');

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

// counter cart
$query = "SELECT SUM(product_qty) AS total_rows FROM `sales_cart` WHERE sales_cart_id = '$user_id'";
$result = $mysqli->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $counter_cart = $row['total_rows'];
}

// subtotal
$query = "SELECT SUM(amount) AS amount FROM `sales_cart` WHERE sales_cart_id = '$user_id';";
$result = mysqli_query($mysqli, $query);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $amount = intval($row['amount']);
}

// promotion
$query = "SELECT promotion_id, promotion FROM `sales_cart` WHERE sales_cart_id = '$user_id';";
$result = mysqli_query($mysqli, $query);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $promotion_id = $row['promotion_id'];
    $promotion = $row['promotion'];

    if (strpos($promotion, '%') !== false) {
        $promotion = str_replace('%', '', $promotion);
        $discount = ($amount * $promotion) / 100;
        $total = $amount - $discount;
        $promotion = $promotion . '%';
    } else {
        $promotion = intval($promotion);
        $total = $amount - $promotion;
        $promotion = "Rp " . number_format($promotion, 0, ',', '.');
    }
}

// snap token
if (isset($_GET['snap_token']) && !empty($_GET['snap_token'])) {
    $snap_token = $_GET['snap_token'];
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
                                <li class="breadcrumb-item active" aria-current="page">Cashier</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Cashier</h1>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <div class="alert alert-soft-danger d-none col-112 mx-auto" role="alert" id="dynamicAlertError">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                    <div class="flex-grow-1 ms-2" id="alertMessageError">
                    </div>
                </div>
            </div>

            <div class="alert alert-soft-success d-none col-12 mx-auto" role="alert" id="dynamicAlert">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        🎉
                    </div>
                    <div class="flex-grow-1 ms-2" id="alertMessage">
                    </div>
                </div>
            </div>

            <!-- Step Form -->
            <div class="js-step-form">
                <!-- Content Step Form -->
                <div class="row">

                    <div class="col-lg-7">
                        <div id="checkoutStepFormContent" class="sticky-top" style="top: 90px; margin-bottom: 15px; z-index: 10;">
                            <!-- Card -->
                            <div id="checkoutStepDelivery" class="active">
                                <form action="controller/PosCashierCart.php" method="POST" class="card mb-3 mb-lg-5">
                                    <input type="hidden" value="<?php echo $user_id ?>" name="user_id">
                                    <!-- Header -->
                                    <div class="card-header">
                                        <h4 class="card-header-title">Cashier</h4>
                                    </div>
                                    <!-- End Header -->

                                    <!-- Body -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <!-- Form -->
                                                        <div class="mb-4">
                                                            <label for="selectProductLabel" class="form-label">Product</label>
                                                            <!-- Select -->
                                                            <div class="tom-select-custom">
                                                                <select class="js-select form-select" name="product_sku" data-hs-tom-select-options='{
                                                                    "placeholder": "Select product",
                                                                    "hideSearch": false,
                                                                    "width": ""
                                                                    }' required>
                                                                    <option value=""></option>
                                                                    <?php
                                                                    $sql = "SELECT s.*
                                                                    FROM stock s
                                                                    INNER JOIN product p ON s.product_sku = p.product_sku
                                                                    ORDER BY s.stock_created_at DESC";
                                                                    $result = $mysqli->query($sql);

                                                                    if ($result->num_rows > 0) {
                                                                        while ($row = $result->fetch_assoc()) {
                                                                    ?>
                                                                            <option value="<?php echo $row['product_sku'] ?>" data-option-template='<div class="d-flex align-items-start">
                                                                                    <div class="flex-grow-1"><span class="d-block fw-semibold"><?php echo $row['product_name'] ?></span><span class="tom-select-custom-hide small">Color : <?php echo $row['product_color'] ?> <br>Category : <?php echo $row['product_category'] ?> <br> Stock : <?php echo $row['stock_qty'] ?></span></div>
                                                                                </div>'>
                                                                                <?php echo $row['product_name'] ?><?php echo $row['product_sku'] ?><?php echo $row['product_barcode'] ?>
                                                                            </option>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <!-- End Select -->
                                                        </div>
                                                        <!-- End Form -->
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <!-- Form -->
                                                        <div class="mb-4">
                                                            <label for="salesPrice" class="form-label">Qty</label>
                                                            <!-- Quantity -->
                                                            <div class="quantity-counter">
                                                                <div class="js-quantity-counter row align-items-center">
                                                                    <div class="col">
                                                                        <input id="counter" class="js-result form-control form-control-quantity-counter" type="number" value="1" name="product_qty">
                                                                    </div>
                                                                    <!-- End Col -->

                                                                    <div class="col-auto">
                                                                        <a class="js-minus btn btn-outline-secondary btn-xs btn-icon rounded-circle" href="javascript:;">
                                                                            <svg width="8" height="2" viewBox="0 0 8 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M0 1C0 0.723858 0.223858 0.5 0.5 0.5H7.5C7.77614 0.5 8 0.723858 8 1C8 1.27614 7.77614 1.5 7.5 1.5H0.5C0.223858 1.5 0 1.27614 0 1Z" fill="currentColor" />
                                                                            </svg>
                                                                        </a>
                                                                        <a class="js-plus btn btn-outline-secondary btn-xs btn-icon rounded-circle" href="javascript:;">
                                                                            <svg width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M4 0C4.27614 0 4.5 0.223858 4.5 0.5V3.5H7.5C7.77614 3.5 8 3.72386 8 4C8 4.27614 7.77614 4.5 7.5 4.5H4.5V7.5C4.5 7.77614 4.27614 8 4 8C3.72386 8 3.5 7.77614 3.5 7.5V4.5H0.5C0.223858 4.5 0 4.27614 0 4C0 3.72386 0.223858 3.5 0.5 3.5H3.5V0.5C3.5 0.223858 3.72386 0 4 0Z" fill="currentColor" />
                                                                            </svg>
                                                                        </a>
                                                                    </div>
                                                                    <!-- End Col -->
                                                                </div>
                                                                <!-- End Row -->
                                                            </div>
                                                            <!-- End Quantity -->
                                                        </div>
                                                        <!-- End Form -->
                                                    </div>
                                                </div>
                                                <!-- End Row -->
                                            </div>
                                        </div>
                                        <!-- End Row -->

                                        <!-- Custom Checkbox -->
                                        <div class="form-check p-0 d-flex justify-content-end">
                                            <button type="submit" name="btn-add-cart" class="btn btn-primary">
                                                Add to Chart
                                            </button>
                                        </div>
                                        <!-- End Custom Checkbox -->
                                    </div>
                                    <!-- Body -->
                                </form>
                                <!-- End Card -->
                            </div>
                        </div>
                    </div>
                    <!-- End Col -->
                    <div class="col-lg-5 lg-2 mb-5 mb-lg-0">
                        <div id="checkoutStepOrderSummary">
                            <!-- Card -->
                            <div class="card mb-3">
                                <!-- Header -->
                                <div class="card-header card-header-content-between">
                                    <h4 class="card-header-title"><?php echo $counter_cart ?> item</h4>

                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                        <i class="bi bi-person-plus me-1"></i> Create Member
                                    </button>

                                    <!-- Modal -->
                                    <div id="addMemberModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addMemberModalTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addMemberModalTitle">Add membership data</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- form -->
                                                    <form action="controller/PosAddMember.php" method="POST">
                                                        <input name="username_creator" type="hidden" value="<?php echo $username ?>">
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="mb-4">
                                                                    <label class="form-label" for="firstName">First name</label>
                                                                    <input name="member_first_name" type="text" class="form-control" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="mb-4">
                                                                    <label class="form-label" for="lastName">Last name</label>
                                                                    <input name="member_last_name" type="text" class="form-control" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="mb-4">
                                                                    <label class="form-label" for="phoneNumber">Phone number / WA</label>
                                                                    <input name="member_phone" type="text" class="js-input-mask form-control" id="phoneLabel" placeholder="+(xx) xxx-xxx-xxx-xxx" data-hs-mask-options='{
                                                            "mask": "+(00) 000-000-000-000"
                                                        }' required>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="mb-4">
                                                                    <label class="form-label" for="email">Email</label>
                                                                    <input name="member_email" type="email" class="form-control" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <div class="mb-4">
                                                                    <label class="form-label" for="addressLabel">Address</label>
                                                                    <textarea name="member_address" class="form-control" rows="4" required></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button" class="btn btn-white me-2" data-bs-dismiss="modal">Close</button>
                                                            <button name="btn-save-member" type="submit" class="btn btn-primary">Save member</button>
                                                        </div>
                                                    </form>
                                                    <!-- end form -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Modal -->
                                </div>
                                <!-- End Header -->

                                <!-- Body -->
                                <div class="card-body">
                                    <?php
                                    $sql = "SELECT * FROM `sales_cart` WHERE sales_cart_id = '$user_id' ORDER BY created_at DESC";

                                    $result = $mysqli->query($sql);

                                    if ($result->num_rows > 0) { ?>
                                        <?php while ($row = $result->fetch_assoc()) {
                                        ?>
                                            <!-- Media -->
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar avatar-lg">
                                                        <img class="avatar-img" src="./assets/img/product-img/<?php echo $row['product_media_1'] ?>" alt="Image Description">
                                                        <span class="avatar-status avatar-lg-status avatar-status-primary"><?php echo $row['product_qty'] ?></span>
                                                    </div>
                                                </div>

                                                <div class="flex-grow-1 ms-3">
                                                    <h4 class="mb-0"><?php echo "Rp " . number_format(floatval($row['amount']), 0, ',', '.'); ?></h4>
                                                    <span><?php echo $row['product_name'] ?></span>

                                                    <div class="text-body fs-6">
                                                        <span>Category:</span>
                                                        <span class="text-dark fw-semibold"><?php echo $row['product_category'] ?></span>
                                                    </div>
                                                    <div class="text-body fs-6">
                                                        <span>Description:</span>
                                                        <span class="text-dark fw-semibold"><?php echo $row['description'] ?></span>
                                                    </div>
                                                    <div class="form-check mt-4 d-flex justify-content-end">
                                                        <form action="controller/PosCashierCart.php" method="POST">
                                                            <input type="hidden" name="product_sku" value="<?php echo $row['product_sku'] ?>">
                                                            <button name="btn-delete-cart" type="submit" class="btn btn-danger">
                                                                <i class="bi bi-trash"></i> Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Media -->

                                            <hr class="my-4">
                                        <?php } ?>
                                    <?php } else { ?>
                                        <div class="text-center">
                                            <img class="mb-3" src="assets/svg/illustrations/oc-empty-cart.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="default">
                                            <img class="mb-3" src="assets/svg/illustrations-light/oc-empty-cart.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="dark"><br>
                                            <span>No items in cart</span><br><br>
                                        </div>
                                    <?php } ?>

                                    <form action="controller/PosCashierCart.php" method="POST">
                                        <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                                        <div class="tom-select-custom">
                                            <label for="discountLabel" class="form-label">Discount</label>
                                            <select id="select-promotion" name="promotion_id" class="js-select form-select" autocomplete="off" data-hs-tom-select-options='{
                                                    "placeholder": "Select discount..."
                                                }'>
                                                <option value=" ">No Discount</option>
                                                <?php
                                                $sql = "SELECT * FROM `promotion` ORDER BY promotion_created_at DESC";
                                                $result = $mysqli->query($sql);

                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                ?>
                                                        <option value="<?php echo $row['promotion_id'] ?>">
                                                            <?php echo $row['promotion_name'] ?> -
                                                            <?php if ($row['promotion_value_rupiah'] > 0) { ?>
                                                                <?php echo "Rp " . number_format(floatval($row['promotion_value_rupiah']), 0, ',', '.'); ?>
                                                            <?php } else { ?>
                                                                <?php echo $row['promotion_value_percentage'] . '%' ?>
                                                            <?php } ?>
                                                        </option>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-check mt-4 p-0">
                                            <button type="submit" name="btn-add-promotion" class="btn btn-primary w-100">
                                                Choose Promo
                                            </button>
                                        </div>
                                    </form>

                                    <!-- ==== insert sales ==== -->
                                    <form id="checkout-form" action="" method="POST">
                                        <input type="hidden" value="<?php echo $user_id ?>" name="user_id">
                                        <input type="hidden" value="<?php echo $username ?>" name="username_creator">
                                        <input type="hidden" value="<?php echo $total ?>" name="total">
                                        <input type="hidden" value="<?php echo $amount ?>" name="amount">
                                        <?php
                                        $sql = "SELECT * FROM `sales_cart` WHERE sales_cart_id = '$user_id' ORDER BY created_at";

                                        $result = $mysqli->query($sql);

                                        if ($result->num_rows > 0) { ?>
                                            <?php while ($row = $result->fetch_assoc()) {
                                            ?>
                                                <input type="hidden" value="<?php echo $row['product_sku'] ?>" name="product_sku">
                                                <input type="hidden" value="<?php echo $row['product_qty'] ?>" name="product_qty">
                                                <input type="hidden" value="<?php echo $row['product_name'] ?>" name="product_name">
                                                <input type="hidden" value="<?php echo $row['product_category'] ?>" name="product_category">
                                                <input type="hidden" value="<?php echo $row['description'] ?>" name="product_description">
                                                <input type="hidden" value="<?php echo $row['selling_price'] ?>" name="selling_price">
                                                <input type="hidden" value="<?php echo $row['purchase_price'] ?>" name="purchase_price">
                                                <input type="hidden" value="<?php echo $row['promotion_id'] ?>" name="promotion_id">
                                                <input type="hidden" value="<?php echo $row['promotion'] ?>" name="promotion">
                                            <?php } ?>
                                        <?php } ?>
                                        <!-- Select -->
                                        <div class="tom-select-custom mt-4">
                                            <label for="paymentLabel" class="form-label">Payment method</label>
                                            <select id="select-payment" name="payment_method" class="js-select form-select" autocomplete="off" data-hs-tom-select-options='{
                                                    "placeholder": "Select payment method..."
                                                }'>
                                                <option value="NO PAYMENT METHOD">No payment method</option>
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
                                        </div>
                                        <!-- End Select -->

                                        <div class="col-sm-12 mt-4">
                                            <!-- Form -->
                                            <div class="mb-4">
                                                <label for="paymentLabel" class="form-label">Cash in</label>
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
                                                                if ($row['bank_account_number'] != '123debt' && $row['bank_account_number'] != '123receivables') {
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
                                            <!-- End Form -->
                                        </div>

                                        <div class="col-sm-12 mt-4">
                                            <!-- Form -->
                                            <div class="mb-4">
                                                <label for="member" class="form-label">Member</label>
                                                <!-- Select -->
                                                <div class="tom-select-custom">
                                                    <select name="member_id" class="js-select form-select" autocomplete="off" data-hs-tom-select-options='{
                                                    "placeholder": "Select member..."
                                                }'>
                                                        <option value=" ">No Member</option>
                                                        <?php
                                                        $sql = "SELECT * FROM `member` ORDER BY member_created_at DESC";
                                                        $result = $mysqli->query($sql);

                                                        if ($result->num_rows > 0) {
                                                            while ($row = $result->fetch_assoc()) {
                                                        ?>
                                                                <option value="<?php echo $row['member_id'] ?>"><?php echo $row['member_first_name'] ?> <?php echo $row['member_last_name'] ?></option>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <!-- End Select -->
                                            </div>
                                            <!-- End Form -->
                                        </div>

                                        <div class="col-sm-12 mb-4 p-4 maturity-input bg-soft-warning rounded border border-warning">
                                            <label class="form-label" for="phoneNumber">Due date</label>
                                            <!-- Flatpickr -->
                                            <div id="invoiceDateFlatpickr" class="js-flatpickr flatpickr-custom mb-2" data-hs-flatpickr-options='{
                                                        "appendTo": "#invoiceDateFlatpickr",
                                                        "dateFormat": "Y-m-d",
                                                        "wrap": true
                                                        }'>
                                                <input id="maturity" type="text" name="maturity" class="flatpickr-custom-form-control form-control maturity-input border-warning" placeholder="Select dates" data-input>
                                            </div>
                                            <span class="text-warning">
                                                <i class="bi bi-exclamation-octagon"></i> Required maturity input if choosing debt repayment
                                            </span>
                                            <!-- End Flatpickr -->
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="mb-4">
                                                <label class="form-label" for="phoneNumber">Transaction Date</label>
                                                <!-- Flatpickr -->
                                                <input id="tanggalInput" name="sales_date" type="text" class="js-flatpickr form-control flatpickr-custom" placeholder="Select dates" data-hs-flatpickr-options='{
                                                    "dateFormat": "Y-m-d",
                                                    "enableTime": true
                                                }'>

                                                <script>
                                                    var today = new Date();
                                                    var day = today.getDate();
                                                    var month = today.getMonth() + 1;
                                                    var year = today.getFullYear();
                                                    var formattedDate = year + '/' + month + '/' + day;

                                                    document.getElementById('tanggalInput').value = formattedDate;
                                                </script>
                                                <!-- End Flatpickr -->
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <div class="row align-items-center mb-3">
                                            <span class="col-6">Subtotal:</span>
                                            <h4 class="col-6 text-end text-dark mb-0"><?php echo "Rp " . number_format($amount, 0, ',', '.'); ?></h4>
                                        </div>
                                        <!-- End Row -->

                                        <div class="row align-items-center">
                                            <span class="col-6">Discount:</span>
                                            <h4 class="col-6 text-end text-dark mb-0">
                                                <?php if (isset($promotion)) { ?>
                                                    <?php echo $promotion ?>
                                                <?php } else { ?>
                                                    -
                                                <?php } ?>
                                            </h4>
                                        </div>
                                        <!-- End Row -->

                                        <hr class="my-4">

                                        <div class="row align-items-center">
                                            <span class="col-6 text-dark fw-semibold">Total to pay:</span>
                                            <h3 class="col-6 text-end text-dark mb-0">
                                                <?php if (isset($total)) { ?>
                                                    <?php echo "Rp " . number_format($total, 0, ',', '.'); ?>
                                                <?php } else { ?>
                                                    -
                                                <?php } ?>
                                            </h3>
                                        </div>
                                        <!-- End Row -->

                                        <div class="form-check mt-4 p-0">
                                            <button type="submit" name="btn-add-sales" class="btn btn-primary w-100" onclick="setAction('midtrans/placeOrder.php')">Checkout</button>
                                            <button id="success-form" type="submit" name="btn-sales-success" class="btn btn-primary w-100 d-none" onclick="setAction('controller/PosCashier.php')">Success</button>
                                        </div>
                                        <!-- ==== end insert sales ==== -->
                                    </form>
                                    <!-- Body -->
                                </div>
                                <!-- End Card -->
                            </div>
                        </div>
                        <!-- End Col -->
                    </div>
                    <!-- End Step Form -->
                </div>
                <!-- End Step Form -->

                <script>
                    function setAction(action) {
                        document.getElementById('checkout-form').action = action;
                    }
                </script>


                <script type="text/javascript">
                    window.snap.pay('<?php echo $snap_token ?>', {
                        onSuccess: function(result) {
                            document.getElementById('success-form').click();
                        },
                        onPending: function(result) {
                            alert("wating your payment!");
                            console.log(result);
                        },
                        onError: function(result) {
                            alert("payment failed!");
                            console.log(result);
                        },
                        onClose: function() {
                            alert('you closed the popup without finishing the payment');
                        }
                    });
                </script>

                <!-- Modal -->
                <div id="exampleModalCenter" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <!-- <h5 class="modal-title" id="exampleModalCenterTitle">Modal title</h5> -->
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center">
                                    <img class="img-fluid mb-3" src="./assets/svg/illustrations/oc-hi-five.svg" alt="Image Description" data-hs-theme-appearance="default" style="max-width: 15rem;">
                                    <img class="img-fluid mb-3" src="./assets/svg/illustrations-light/oc-hi-five.svg" alt="Image Description" data-hs-theme-appearance="dark" style="max-width: 15rem;">

                                    <div class="mb-4">
                                        <h2>Your payment was successful!</h2>
                                        <p>Transaction #72712</p>
                                        <p>You can print the receipt.</p>
                                    </div>

                                    <a class="btn btn-primary" href="">
                                        <i class="bi bi-printer me-1"></i> Print
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->
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
    <script src="./assets/vendor/hs-quantity-counter/dist/hs-quantity-counter.min.js"></script>

    <!-- JS Implementing Plugins -->
    <script src="./assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside.min.js"></script>
    <script src="./assets/vendor/hs-form-search/dist/hs-form-search.min.js"></script>

    <script src="./assets/vendor/hs-step-form/dist/hs-step-form.min.js"></script>
    <script src="./assets/vendor/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="./assets/vendor/imask/dist/imask.min.js"></script>
    <script src="./assets/vendor/quill/dist/quill.min.js"></script>
    <script src="./assets/vendor/dropzone/dist/min/dropzone.min.js"></script>

    <script src="./assets/vendor/flatpickr/dist/flatpickr.min.js"></script>

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>

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


                // INITIALIZATION OF STEP FORM
                // =======================================================
                new HSStepForm('.js-step-form', {
                    finish: () => {
                        document.getElementById("checkoutStepFormProgress").style.display = 'none'
                        document.getElementById("checkoutStepFormContent").style.display = 'none'
                        document.getElementById("checkoutStepOrderSummary").style.display = 'none'
                        document.getElementById("checkoutStepSuccessMessage").style.display = 'block'
                        const formContainer = document.getElementById('formContainer')
                    },
                    onNextStep: function() {
                        scrollToTop()
                    },
                    onPrevStep: function() {
                        scrollToTop()
                    }
                })

                function scrollToTop(el = '.js-step-form') {
                    el = document.querySelector(el)
                    window.scrollTo({
                        top: (el.getBoundingClientRect().top + window.scrollY) - 30,
                        left: 0,
                        behavior: 'smooth'
                    })
                }
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
            // INITIALIZATION OF FLATPICKR
            // =======================================================
            HSCore.components.HSFlatpickr.init('.js-flatpickr')
        })();
    </script>

    <script>
        // Mendapatkan elemen input
        var input = document.getElementById("counter");

        input.addEventListener("change", function() {
            if (input.value === "0") {
                input.value = "1";
            }
        });

        input.addEventListener("input", function() {
            if (input.value === "0") {
                input.value = "1";
            }
        });
    </script>

    <script>
        (function() {
            // INITIALIZATION OF  QUANTITY COUNTER
            // =======================================================
            new HSQuantityCounter('.js-quantity-counter')
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
                if (paymentSelect.value === '123receivables') {
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
</body>

</html>