<?php
require_once 'controller/Session.php';
$page = 'wms-purchase';
$page_access = array('1', '6');

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

$query = "SELECT SUM(amount) AS amount FROM purchase_cart;";

$result = mysqli_query($mysqli, $query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $amount = $row['amount'];
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
                                <li class="breadcrumb-item active" aria-current="page">Purchase</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Purchase</h1>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <div class="alert alert-soft-danger d-none col-10 mx-auto" role="alert" id="dynamicAlertError">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                    <div class="flex-grow-1 ms-2" id="alertMessageError">
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-center">

                <div class="col-lg-10">
                    <div id="checkoutStepFormContent">
                        <!-- Card -->
                        <div id="checkoutStepDelivery" class="active">
                            <div class="card mb-3 mb-lg-5">
                                <!-- Header -->
                                <div class="card-header">
                                    <h4 class="card-header-title">Purchase</h4>
                                </div>
                                <!-- End Header -->

                                <!-- Body -->
                                <form action="controller/WmsPurchaseCart.php" method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
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
                                                                    $sql = "SELECT * FROM `product` WHERE product_status = 'active' ORDER BY product_created_at DESC";
                                                                    $result = $mysqli->query($sql);

                                                                    if ($result->num_rows > 0) {
                                                                        while ($row = $result->fetch_assoc()) {
                                                                    ?>
                                                                            <option value="<?php echo $row['product_sku'] ?>" data-name="<?php echo htmlspecialchars($row['product_name']) ?>" data-description="<?php echo htmlspecialchars($row['product_description']) ?>" data-category="<?php echo htmlspecialchars($row['product_category']) ?>" data-color="<?php echo htmlspecialchars($row['product_color']) ?>" data-option-template='<div class="d-flex align-items-start">
                                                                                    <div class="flex-grow-1"><span class="d-block fw-semibold"><?php echo $row['product_name'] ?></span><span class="tom-select-custom-hide small">Color : <?php echo $row['product_color'] ?> <br>Category : <?php echo $row['product_category'] ?> <br> SKU : <?php echo $row['product_sku'] ?></span></div>
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

                                                    <input type="hidden" id="descriptionInput" name="product_description" value="">
                                                    <input type="hidden" id="categoryInput" name="product_category" value="">
                                                    <input type="hidden" id="colorInput" name="product_color" value="">
                                                    <input type="hidden" id="nameInput" name="product_name" value="">

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

                                                    <div class="col-sm-6">
                                                        <!-- Form -->
                                                        <div class="mb-4">
                                                            <label for="purchaseSalesLabel" class="form-label">Purchase Price</label>
                                                            <div class="input-group mb-4">
                                                                <span class="input-group-text">Rp</span>
                                                                <input type="text" class="form-control balance" name="purchase_price" required>
                                                            </div>
                                                        </div>
                                                        <!-- End Form -->
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <!-- Form -->
                                                        <div class="mb-4">
                                                            <label for="sellingSalesLabel" class="form-label">Selling Price</label>
                                                            <div class="input-group mb-4">
                                                                <span class="input-group-text">Rp</span>
                                                                <input type="text" class="form-control balance" name="selling_price" required>
                                                            </div>
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
                                            <button name="btn-add-invoice" type="submit" class="btn btn-primary">
                                                Add to Invoice
                                            </button>
                                        </div>
                                        <!-- End Custom Checkbox -->
                                    </div>
                                    <!-- Body -->
                                </form>
                            </div>
                            <!-- End Card -->
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

                        <!-- Invoice Card -->
                        <h1 class="page-header-title mt-4 mb-3">Purchase Invoice</h1>
                        <div class="card card-lg">
                            <!-- Body -->
                            <div class="card-body">
                                <form action="controller/WmsPurchase.php" method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                                    <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                                    <input type="hidden" name="amount" value="<?php echo $amount ?>">
                                    <div class="row justify-content-md-between">
                                        <div class="col-md-4 mb-3 mb-md-0">
                                            <!-- Logo -->
                                            <img src="assets/img/logo/logo.png" width="200px" alt="" style="margin-top: 30px;">
                                            <!-- End Logo -->
                                        </div>
                                        <!-- End Col -->

                                        <div class="col-md-5 text-md-end">
                                            <h2>Invoice #</h2>

                                            <!-- Form -->
                                            <div class="d-md justify-content-md-end mb-2 mb-md-6">
                                                <div class="tom-select-custom">
                                                    <select name="supplier_id" class="js-select form-select" autocomplete="off" data-hs-tom-select-options='{
                                                                    "placeholder": "Select supplier..."
                                                                }'>
                                                        <?php
                                                        $sql = "SELECT * FROM `supplier` WHERE supplier_status = 'active' ORDER BY supplier_created_at DESC";
                                                        $result = $mysqli->query($sql);

                                                        if ($result->num_rows > 0) {
                                                            while ($row = $result->fetch_assoc()) {
                                                        ?>
                                                                <option value="<?php echo $row['supplier_id'] ?>">
                                                                    <?php echo $row['supplier_name'] ?>
                                                                </option>
                                                        <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- End Form -->
                                        </div>
                                        <!-- End Col -->
                                    </div>
                                    <!-- End Row -->

                                    <hr class="my-5">

                                    <div class="row mb-3">
                                        <div class="col-md-5">
                                            <!-- Form -->
                                            <div class="mb-4">
                                                <label for="invoiceAddressToLabel" class="form-label">Bill to:</label><br>
                                                PT SPARE PART SURABAYA
                                            </div>
                                            <!-- End Form -->
                                        </div>
                                        <!-- End Col -->

                                        <div class="col-md-7 align-self-md-end">
                                            <!-- Form -->
                                            <div class="mb-4">
                                                <dl class="row align-items-sm-center mb-3">
                                                    <dt class="col-md text-sm-end mb-2 mb-sm-0">Invoice date:</dt>
                                                    <dd class="col-md-auto mb-0">
                                                        <!-- Flatpickr -->
                                                        <div id="invoiceDateFlatpickr" class="js-flatpickr flatpickr-custom" data-hs-flatpickr-options='{
                                                        "appendTo": "#invoiceDateFlatpickr",
                                                        "dateFormat": "Y-m-d",
                                                        "wrap": true
                                                        }'>
                                                            <input id="tanggalInput" type="text" class="flatpickr-custom-form-control form-control" placeholder="Select dates" data-input name="purchase_date" value="">

                                                            <script>
                                                                var today = new Date();
                                                                var day = today.getDate();
                                                                var month = today.getMonth() + 1;
                                                                var year = today.getFullYear();
                                                                var formattedDate = year + '/' + month + '/' + day;

                                                                document.getElementById('tanggalInput').value = formattedDate;
                                                            </script>
                                                        </div>
                                                        <!-- End Flatpickr -->
                                                    </dd>
                                                </dl>

                                                <dl class="row maturity-input mb-3">
                                                    <dt class="col-md text-sm-end mt-2 mb-2 mb-sm-0">Due date:</dt>
                                                    <dd class="col-md mb-0">
                                                        <!-- Select -->
                                                        <div class="tom-select-custom">
                                                            <!-- Flatpickr -->
                                                            <div id="invoiceDateFlatpickr" class="js-flatpickr flatpickr-custom mb-2" data-hs-flatpickr-options='{
                                                            "appendTo": "#invoiceDateFlatpickr",
                                                            "dateFormat": "Y-m-d",
                                                            "wrap": true
                                                            }'>
                                                                <input type="text" name="maturity" class="flatpickr-custom-form-control form-control maturity-input border-warning" placeholder="Select dates" data-input>
                                                            </div>
                                                            <span class="text-warning fw-normal">
                                                                <i class="bi bi-exclamation-octagon"></i> Required maturity input if choosing debt repayment
                                                            </span>
                                                            <!-- End Flatpickr -->
                                                        </div>
                                                        <!-- End Select -->
                                                    </dd>
                                                </dl>

                                                <dl class="row align-items-sm-center">
                                                    <dt class="col-md text-sm-end mb-2 mb-sm-0">Payment Method:</dt>
                                                    <dd class="col-md mb-0">
                                                        <!-- Select -->
                                                        <div class="tom-select-custom">
                                                            <select name="bank_account_number" class="js-select form-select" autocomplete="off" data-hs-tom-select-options='{
                                                                    "placeholder": "Select payment..."
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
                                                        <!-- End Select -->
                                                    </dd>
                                                </dl>
                                            </div>

                                            <!-- purchase detail -->
                                            <?php
                                            $sql = "SELECT * FROM `purchase_cart` WHERE purchase_cart_id = '$user_id' ORDER BY created_at";

                                            $result = $mysqli->query($sql);

                                            if ($result->num_rows > 0) { ?>
                                                <?php while ($row = $result->fetch_assoc()) {
                                                ?>
                                                    <input type="hidden" value="<?php echo $row['product_name'] ?>" name="product_name">
                                                    <input type="hidden" value="<?php echo $row['product_sku'] ?>" name="product_sku">
                                                    <input type="hidden" value="<?php echo $row['product_qty'] ?>" name="product_qty">
                                                    <input type="hidden" value="<?php echo $row['purchase_price'] ?>" name="purchase_price">
                                                    <input type="hidden" value="<?php echo $row['selling_price'] ?>" name="selling_price">
                                                    <input type="hidden" value="<?php echo $row['description'] ?>" name="description">
                                                <?php } ?>
                                            <?php } ?>

                                            <div class="position-fixed start-50 bottom-0 translate-middle-x w-100 zi-99 mb-3" style="max-width: 40rem;">
                                                <!-- Card -->
                                                <div class="card card-sm bg-dark border-dark mx-2">
                                                    <div class="card-body">
                                                        <div class="row justify-content-center justify-content-sm-between">
                                                            <div class="col text-white" style="margin-top: 10px;">
                                                                <span><i class="bi bi-info-circle me-1"></i> Press the purchase button to create purchase data</span>
                                                            </div>
                                                            <!-- End Col -->

                                                            <div class="col-auto">
                                                                <div class="d-flex gap-3">
                                                                    <button name="btn-add-purchase" type="submit" class="btn btn-primary">Purchase</button>
                                                                </div>
                                                            </div>
                                                            <!-- End Col -->
                                                        </div>
                                                        <!-- End Row -->
                                                    </div>
                                                </div>
                                                <!-- End Card -->
                                            </div>
                                            <!-- End Form -->
                                        </div>
                                        <!-- End Col -->
                                    </div>
                                    <!-- End Row -->
                                </form>

                                <div class="js-add-field" data-hs-add-field-options='{
                                        "template": "#addInvoiceItemTemplate",
                                        "container": "#addInvoiceItemContainer",
                                        "defaultCreated": 0
                                        }'>
                                    <!-- Title -->
                                    <div class="bg-light border-bottom p-2 mb-3">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="card-title text-cap">Item</h6>
                                            </div>
                                            <!-- End Col -->

                                            <div class="col-sm-2">
                                                <h6 class="card-title text-cap">SKU</h6>
                                            </div>
                                            <!-- End Col -->

                                            <div class="col-sm-2 d-none d-sm-inline-block">
                                                <h6 class="card-title text-cap">Quantity</h6>
                                            </div>
                                            <!-- End Col -->

                                            <div class="col-sm-2 d-none d-sm-inline-block">
                                                <h6 class="card-title text-cap">Purchase Price</h6>
                                            </div>
                                            <!-- End Col -->

                                            <div class="col-sm-2 d-none d-sm-inline-block">
                                                <h6 class="card-title text-cap">Amount</h6>
                                            </div>
                                            <!-- End Col -->
                                        </div>
                                        <!-- End Row -->
                                    </div>
                                    <!-- End Title -->

                                    <!-- Content -->
                                    <?php
                                    $sql = "SELECT * FROM `purchase_cart` WHERE purchase_cart_id = '$user_id' ORDER BY created_at";

                                    $result = $mysqli->query($sql);

                                    if ($result->num_rows > 0) { ?>
                                        <?php while ($row = $result->fetch_assoc()) {
                                        ?>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <span class="fw-bold"><?php echo $row['product_name'] ?></span><br>
                                                    <span><?php echo $row['description'] ?></span>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col-md-2">
                                                    <span><?php echo $row['product_sku'] ?></span>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col-12 col-sm-auto col-md-2">
                                                    <span><?php echo $row['product_qty'] ?></span>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col-12 col-sm col-md-2">
                                                    <!-- Input Group -->
                                                    <div class="mb-3">
                                                        <span><?php echo "Rp " . number_format($row['purchase_price'], 0, ',', '.'); ?></span>
                                                    </div>
                                                    <!-- End Input Group -->
                                                </div>
                                                <!-- End Col -->

                                                <div class="col col-md-2">
                                                    <span><?php echo "Rp " . number_format($row['amount'], 0, ',', '.'); ?></span>
                                                </div>

                                                <div class="col col-md-1">
                                                    <form action="controller/WmsPurchaseCart.php" method="POST">
                                                        <input type="hidden" name="product_sku" value="<?php echo $row['product_sku'] ?>">
                                                        <button name="btn-delete-cart" class="btn btn-xs btn-danger"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                </div>
                                                <!-- End Col -->
                                            </div>
                                            <!-- End Content -->
                                            <hr class="my-3">
                                        <?php } ?>
                                    <?php } ?>
                                </div>

                                <div class="row justify-content-md-end mt-5">
                                    <div class="col-md-auto">
                                        <dl class="row text-md-end">
                                            <dt class="col-md-6">Subtotal:</dt>
                                            <dd class="col-md-6"><?php echo "Rp " . number_format($amount, 0, ',', '.'); ?></dd>
                                            <dt class="col-md-6">Total:</dt>
                                            <dd class="col-md-6"><?php echo "Rp " . number_format($amount, 0, ',', '.'); ?></dd>
                                        </dl>
                                        <!-- End Row -->
                                    </div>
                                    <!-- End Col -->
                                </div>
                                <!-- End Row -->

                                <!-- <p class="fs-6 mb-0">&copy; Front. 2020 Htmlstream.</p> -->
                            </div>
                            <!-- End Body -->
                            <!-- End Content -->
                        </div>
                        <!-- End Invoice Card -->
                    </div>

                    <!-- Dispatch Note -->
                    <!-- <h1 class="page-header-title mt-4 mb-3">Dispatch Note</h1>
                    <div class="card card-lg">
                        <div class="card-body">
                            <div class="row justify-content-md-between">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <img src="assets/img/logo/logo.png" width="200px" alt="" class="my-5">
                                </div>

                                <div class="col-md-5 text-md-end mt-5">
                                    <h2>Surat Jalan</h2>

                                    <span>PT SPARE PART</span>
                                </div>
                            </div>

                            <hr class="my-5">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div>
                                            <label for="invoiceAddressToLabel" class="form-label">No Document:</label>
                                            #0982131
                                        </div>
                                        <div>
                                            <label for="invoiceAddressToLabel" class="form-label">Recipient:</label>
                                            Budi Hartono
                                        </div>
                                        <div>
                                            <label for="invoiceAddressToLabel" class="form-label">Recipient Address:</label>
                                            Jl Soekarno 10
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 align-self-md-end">
                                    <div class="mb-4">
                                        <div class="text-end">
                                            <label for="invoiceAddressToLabel" class="form-label">Date:</label>
                                            03/03/2024
                                        </div>
                                        <div class="text-end">
                                            <label for="invoiceAddressToLabel" class="form-label">Telp. Recipient:</label>
                                            08123456789
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="js-add-field" data-hs-add-field-options='{
                                        "template": "#addInvoiceItemTemplate",
                                        "container": "#addInvoiceItemContainer",
                                        "defaultCreated": 0
                                        }'>
                                <div class="bg-light border-bottom p-2 mb-3">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <h6 class="card-title text-cap">Item</h6>
                                        </div>

                                        <div class="col-sm-2 d-none d-sm-inline-block">
                                            <h6 class="card-title text-cap">Quantity</h6>
                                        </div>

                                        <div class="col-sm-2 d-none d-sm-inline-block">
                                            <h6 class="card-title text-cap">Unit</h6>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $sql = "SELECT * FROM `purchase_cart` WHERE purchase_cart_id = '$user_id'";

                                $result = $mysqli->query($sql);

                                if ($result->num_rows > 0) { ?>
                                    <?php while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <div class="row mb-1">
                                            <div class="col-md-8">
                                                <span class="fw-bold"><?php echo $row['product_name'] ?></span><br>
                                                <span><?php echo $row['description'] ?></span>
                                            </div>

                                            <div class="col-12 col-sm col-md-2">
                                                <div class="mb-3">
                                                    <span><?php echo $row['product_qty'] ?></span>
                                                </div>
                                            </div>

                                            <div class="col col-md-2">
                                                <span>Piece</span>
                                            </div>
                                        </div>
                                        <hr class="my-3">
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

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
        // Mendapatkan semua elemen dengan kelas 'balance'
        var rupiahs = document.querySelectorAll('.balance');

        // Melakukan iterasi melalui setiap elemen
        rupiahs.forEach(function(rupiah) {
            // Menambahkan event listener 'keyup' ke setiap elemen
            rupiah.addEventListener('keyup', function(e) {
                // Memanggil fungsi formatRupiah untuk mengonversi nilai
                this.value = formatRupiah(this.value, 'Rp. ');
            });
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

    <script type="text/javascript">
        var selectElement = document.querySelector('.js-select');

        var descriptionInput = document.getElementById('descriptionInput');
        var categoryInput = document.getElementById('categoryInput');
        var colorInput = document.getElementById('colorInput');

        selectElement.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];

            var productName = selectedOption.getAttribute('data-name');
            var productDescription = selectedOption.getAttribute('data-description');
            var productCategory = selectedOption.getAttribute('data-category');
            var productColor = selectedOption.getAttribute('data-color');

            nameInput.value = productName;
            descriptionInput.value = productDescription;
            categoryInput.value = productCategory;
            colorInput.value = productColor;
        });
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