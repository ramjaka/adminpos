<?php
require_once 'controller/Session.php';
$page = 'wms-purchase-details';
$page_access = array('1', '7');

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

// purchase trasanction id
if (isset($_GET['purchase_transaction_id']) && !empty($_GET['purchase_transaction_id'])) {
    $purchase_transaction_id = $_GET['purchase_transaction_id'];
}

// total installment
$query_total_installment = "SELECT SUM(installment_amount) AS total_installment FROM debt_installment WHERE debt_id = '$purchase_transaction_id'";
$result_total_installment = $mysqli->query($query_total_installment);

if ($result_total_installment) {
    $row = $result_total_installment->fetch_assoc();
    $total_installment = $row['total_installment'];
}

$query = "SELECT 
            p.purchase_transaction_id,
            p.purchase_date,
            p.purchase_total, 
            p.supplier_id, 
            p.supplier_name, 
            p.purchase_status, 
            s.supplier_phone,
            s.supplier_email,
            s.supplier_address,
            d.debt_id, 
            d.debt_status, 
            d.debt_due_date, 
            d.fulfillment_date, 
            b.bank_account_name, 
            b.bank_account_number, 
            pd.product_sku, 
            pd.product_name,
            pd.product_qty,
            pd.purchase_price, 
            pd.selling_price,
            pd.item_amount,
            pd.description
            FROM 
            purchase p
            LEFT JOIN 
                debt d ON p.debt_id = d.debt_id
            JOIN 
                bank_account b ON p.bank_account_number = b.bank_account_number
            JOIN 
                purchase_detail pd ON p.purchase_transaction_id = pd.purchase_transaction_id
            JOIN
                supplier s ON p.supplier_id = s.supplier_id
            WHERE
                p.purchase_transaction_id = '$purchase_transaction_id';
            ";
$result = mysqli_query($mysqli, $query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $purchase_transaction_id = $row['purchase_transaction_id'];
    $debt_id = $row['debt_id'];
    $debt_status = $row['debt_status'];
    $purchase_total = $row['purchase_total'];
    $purchase_date = $row['purchase_date'];
    $debt_due_date = $row['debt_due_date'];
    $purchase_status = $row['purchase_status'];
    $fulfillment_date = $row['fulfillment_date'];
    $supplier_id = $row['supplier_id'];
    $supplier_name = $row['supplier_name'];
    $supplier_phone = $row['supplier_phone'];
    $supplier_email = $row['supplier_email'];
    $supplier_address = $row['supplier_address'];
    $bank_account_name = $row['bank_account_name'];
    $bank_account_number = $row['bank_account_number'];
}

$accumulation_total_debt = $purchase_total - $total_installment;

// order counter
$order_counter = "SELECT COUNT(*) AS total_rows FROM purchase WHERE supplier_id = '$supplier_id';";
$order_counter_result = mysqli_query($mysqli, $order_counter);

if ($order_counter_result->num_rows > 0) {
    $row_order_counter = $order_counter_result->fetch_assoc();
    $order_counter = $row_order_counter['total_rows'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once('components/head.php'); ?>

    <style>
        .stamp {
            transform: rotate(12deg);
            color: #555;
            font-size: 3rem;
            font-weight: 700;
            border: 0.25rem solid #555;
            display: inline-block;
            padding: 0.25rem 1rem;
            text-transform: uppercase;
            border-radius: 1rem;
            font-family: 'Courier';
            -webkit-mask-image: url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/8399/grunge.png');
            -webkit-mask-size: 944px 604px;
            mix-blend-mode: multiply;
            position: absolute;
            left: 100px;
        }

        .is-paid {
            color: #0A9928;
            border: 0.5rem solid #0A9928;
            -webkit-mask-position: 13rem 6rem;
            transform: rotate(-14deg);
            border-radius: 0;
            opacity: 0.8;
            z-index: 100;
        }

        .is-canceled {
            color: #D23;
            border: 0.5rem double #D23;
            transform: rotate(3deg);
            -webkit-mask-position: 2rem 3rem;
            font-size: 2rem;
            z-index: 100;
        }

        .is-debt {
            color: #ddd422;
            border: 0.5rem double #ddd422;
            transform: rotate(3deg);
            -webkit-mask-position: 2rem 3rem;
            font-size: 2rem;
            z-index: 100;
        }
    </style>
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
            <div class="page-header d-print-none">
                <div class="row align-items-end">
                    <div class="col-sm mb-2 mb-sm-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-no-gutter">
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="">WMS</a></li>
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="">Purchase Details</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Purchase Details</li>
                            </ol>
                        </nav>

                        <div class="d-sm-flex align-items-sm-center">
                            <h1 class="page-header-title">Order #<?php echo $purchase_transaction_id ?></h1>
                            <span class="badge ms-sm-3
                                    <?php if ($row['debt_status'] === '1') {
                                        echo 'bg-soft-warning text-warning';
                                    } else {
                                        echo 'bg-soft-success text-success';
                                    } ?>
                                ">
                                <span class="legend-indicator
                                    <?php if ($row['debt_status'] === '1') {
                                        echo 'bg-warning';
                                    } else {
                                        echo 'bg-success';
                                    } ?>
                                "></span>
                                <?php if ($row['debt_status'] === '1') {
                                    echo 'Debt';
                                } else {
                                    echo 'Paid';
                                } ?>
                            </span>
                        </div>

                        <div class="gap-2 mt-2">
                            <i class="bi bi-calendar4-week"></i> Transaction date : <?php echo $purchase_date ?>
                        </div>
                        <div class="gap-2 mt-2 <?php echo ($debt_id > 1) ? '' : 'd-none' ?>">
                            <i class="bi bi-calendar-minus"></i> Due date : <?php echo $debt_due_date ?>
                        </div>
                        <div class="gap-2 mt-2 <?php echo ($debt_id > 1) ? '' : 'd-none' ?>">
                            <i class="bi bi-calendar-check"></i> Fulfilled date : <?php echo date("d/m/Y", strtotime($fulfillment_date)) ?>
                        </div>
                    </div>
                    <!-- End Col -->

                    <?php if ($purchase_status === 'active') { ?>
                        <div class="col-sm-auto">
                            <a class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletePurchaseModal"><i class="bi bi-trash me-1"></i> Delete Purchase</a>
                        </div>
                    <?php } ?>

                    <!-- Modal delete purchase -->
                    <div id="deletePurchaseModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalCenterTitle">Delete purchase?
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>Are you sure want to delete "<strong><?php echo $purchase_transaction_id ?></strong>"?<br>You can't undo this action.
                                    </p>
                                    <div class="alert alert-soft-danger" role="alert">
                                        <h3 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Warning</h3>
                                        <p>When you delete this purchase, we permanently delete your purchase.</p>
                                    </div>

                                    <form action="controller/WmsEditPurchase.php" method="POST">
                                        <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                                        <input type="hidden" name="purchase_transaction_id" value="<?php echo $purchase_transaction_id ?>">
                                        <input type="hidden" name="debt_id" value="<?php echo $debt_id ?>">
                                        <input type="hidden" name="purchase_total" value="<?php echo $purchase_total ?>">
                                        <input type="hidden" name="bank_account_number" value="<?php echo $bank_account_number ?>">
                                        <!-- Content -->
                                        <?php
                                        $sql = "SELECT product_qty, product_sku FROM `purchase_detail` WHERE purchase_transaction_id = '$purchase_transaction_id'";

                                        $result = $mysqli->query($sql);

                                        if ($result->num_rows > 0) { ?>
                                            <?php while ($row = $result->fetch_assoc()) {
                                            ?>
                                                <input type="hidden" name="product_sku[]" value="<?php echo $row['product_sku'] ?>">
                                                <input type="hidden" name="product_qty[]" value="<?php echo $row['product_qty'] ?>">
                                            <?php } ?>
                                        <?php } ?>

                                        <div class="mb-4 fw-bold">
                                            <label class="form-label" for="spendingName">Please type in your transaction id to confirm.</label>
                                            <input name="confirmation_delete" type="text" class="form-control border-danger" required>
                                        </div>

                                        <button name="btn-delete-purchase" type="submit" class="btn btn-danger w-100">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->
                </div>
                <!-- End Row -->
                <a href="javascript:window.close()" class="btn btn-white mt-2"><i class="bi bi-chevron-left"></i> Back</a>
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
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <!-- Invoice Card -->
                    <h1 class="page-header-title mb-3">Purchase Invoice</h1>
                    <div class="card card-lg">
                        <!-- Body -->
                        <div class="card-body">
                            <div class="row justify-content-md-between">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <!-- Logo -->
                                    <img src="assets/img/logo/logo.png" width="200px" alt="" class="my-5">
                                    <!-- End Logo -->
                                </div>
                                <!-- End Col -->

                                <div class="col-md-5 text-md-end">
                                    <h2>Invoice #<?php echo $purchase_transaction_id ?></h2>

                                    <span><?php echo $supplier_name ?></span>
                                </div>
                                <!-- End Col -->
                            </div>
                            <!-- End Row -->

                            <hr class="my-5">

                            <?php if ($purchase_status === 'canceled') { ?>
                                <span class="stamp is-canceled">Canceled</span>
                            <?php } elseif ($debt_id == '' || $debt_status === '2') { ?>
                                <span class="stamp is-paid">paid</span>
                            <?php } else { ?>
                                <span class="stamp is-debt">debt</span>
                            <?php } ?>

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
                                                <span class="text-end"><?php echo $purchase_date ?></span>
                                                <!-- End Flatpickr -->
                                            </dd>
                                        </dl>

                                        <dl class="row align-items-sm-center mb-3 <?php echo ($debt_id > 1) ? '' : 'd-none' ?>">
                                            <dt class="col-md text-sm-end mb-2 mb-sm-0">Due date:</dt>
                                            <dd class="col-md-auto mb-0">
                                                <span class="text-end"><?php echo $debt_due_date ?></span>
                                                <!-- End Flatpickr -->
                                            </dd>
                                        </dl>

                                        <dl class="row align-items-sm-center">
                                            <dt class="col-md text-sm-end mb-2 mb-sm-0">Payment Method:</dt>
                                            <dd class="col-md-auto mb-0">
                                                <span class="text-end"><?php echo $bank_account_name ?></span>
                                            </dd>
                                        </dl>
                                    </div>
                                    <!-- End Form -->
                                </div>
                                <!-- End Col -->
                            </div>
                            <!-- End Row -->

                            <div class="js-add-field" data-hs-add-field-options='{
                                        "template": "#addInvoiceItemTemplate",
                                        "container": "#addInvoiceItemContainer",
                                        "defaultCreated": 0
                                        }'>
                                <!-- Title -->
                                <div class="bg-light border-bottom p-2 mb-3">
                                    <div class="row">
                                        <div class="col-sm-4">
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
                                $sql = "SELECT * FROM `purchase_detail` WHERE purchase_transaction_id = '$purchase_transaction_id' ORDER BY purchase_detail_created_at";

                                $result = $mysqli->query($sql);

                                if ($result->num_rows > 0) { ?>
                                    <?php while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <div class="row">
                                            <div class="col-md-4">
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
                                                <span><?php echo "Rp " . number_format($row['item_amount'], 0, ',', '.'); ?></span>
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
                                        <dd class="col-md-6"><?php echo "Rp " . number_format($purchase_total, 0, ',', '.'); ?></dd>
                                        <dt class="col-md-6">Total:</dt>
                                        <dd class="col-md-6"><?php echo "Rp " . number_format($purchase_total, 0, ',', '.'); ?></dd>
                                    </dl>
                                    <!-- End Row -->
                                </div>
                                <!-- End Col -->
                            </div>
                            <!-- End Row -->

                            <!-- <p class="fs-6 mb-0">&copy; Front. 2020 Htmlstream.</p> -->
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Invoice Card -->
                    <!-- Footer -->
                    <div class="d-flex justify-content-end d-print-none gap-3 mt-4">
                        <a class="btn btn-primary" href="printPurchase.php?purchase_transaction_id=<?php echo $purchase_transaction_id ?>">
                            <i class="bi-printer me-1"></i> Print Invocie
                        </a>
                    </div>
                    <!-- End Footer -->

                    <!-- Dispatch Note -->
                    <h1 class="page-header-title mt-4 mb-3">Dispatch Note</h1>
                    <div class="card card-lg">
                        <div class="card-body">
                            <div class="row justify-content-md-between">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <img src="assets/img/logo/logo.png" width="200px" alt="" class="my-5">
                                </div>

                                <div class="col-md-5 text-md-end mt-5">
                                    <h2>Surat Jalan</h2>

                                    <span><?php echo $supplier_name ?></span>
                                </div>
                            </div>

                            <hr class="my-5">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <div>
                                            <label for="invoiceAddressToLabel" class="form-label">No Document:</label>
                                            #<?php echo $purchase_transaction_id ?>
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
                                            <?php echo $purchase_date ?>
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
                                $sql = "SELECT * FROM `purchase_detail` WHERE purchase_transaction_id = '$purchase_transaction_id'";

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
                    </div>

                    <!-- Footer -->
                    <div class="d-flex justify-content-end d-print-none gap-3 mt-4">
                        <a class="btn btn-primary" href="printDispatchNote.php?purchase_transaction_id=<?php echo $purchase_transaction_id ?>">
                            <i class="bi-printer me-1"></i> Print Dispatch Note
                        </a>
                    </div>
                    <!-- End Footer -->

                    <?php if ($debt_id > 0) { ?>
                        <h1 class="page-header-title mt-4 mb-3">Installment Purchase</h1>
                        <div class="card">
                            <div class="row">
                                <div class="col-lg-4">
                                    <!-- Body -->
                                    <div class="card card-centered bg-light h-100 rounded-0 rounded-start shadow-none">
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
                        </div>
                    <?php } ?>
                </div>

                <div class="col-lg-4">
                    <h1 class="page-header-title mb-3 text-white">Supplier Information</h1>
                    <!-- Card -->
                    <div class="card sticky-top" style="top: 90px; margin-bottom: 15px; z-index: 10;">
                        <!-- Header -->
                        <div class="card-header">
                            <h4 class="card-header-title">Supplier</h4>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="card-body">
                            <!-- List Group -->
                            <ul class="list-group list-group-flush list-group-no-gutters">
                                <li class="list-group-item">
                                    <a class="d-flex align-items-center" href="customerProfile.php">
                                        <div class="icon icon-soft-secondary icon-circle">
                                            <i class="bi-building"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <span class="text-body text-inherit"><?php echo $supplier_name ?></span>
                                        </div>
                                        <div class="flex-grow-1 text-end">
                                            <i class="bi-chevron-right text-body"></i>
                                        </div>
                                    </a>
                                </li>

                                <li class="list-group-item">
                                    <a class="d-flex align-items-center" href="customerProfile.php">
                                        <div class="icon icon-soft-info icon-circle">
                                            <i class="bi-basket"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <span class="text-body text-inherit">
                                                <?php echo $order_counter ?> orders
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 text-end">
                                            <i class="bi-chevron-right text-body"></i>
                                        </div>
                                    </a>
                                </li>

                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>Contact info</h5>
                                    </div>

                                    <ul class="list-unstyled list-py-2 text-body">
                                        <li><i class="bi-at me-2"></i><?php echo $supplier_email ?></li>
                                        <li><i class="bi-phone me-2"></i>
                                            <input type="text" class="js-input-mask border-0" id="phoneLabel" placeholder="+(xx) xxx-xxx-xxx-xxx" data-hs-mask-options='{
                                            "mask": "+(00) 000-000-000-000"
                                            }' value="<?php echo $supplier_phone ?>" readonly>
                                        </li>
                                    </ul>
                                </li>

                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>Supplier address</h5>
                                    </div>

                                    <span class="d-block text-body">
                                        <?php echo $supplier_address ?>
                                    </span>
                                </li>
                            </ul>
                            <!-- End List Group -->
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->
                </div>
            </div>
            <!-- End Row -->
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
    <script src="./assets/vendor/imask/dist/imask.min.js"></script>

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