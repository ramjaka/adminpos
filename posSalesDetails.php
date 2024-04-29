<?php
require_once 'controller/Session.php';
$page = 'pos-sales-details';
$page_access = array('1', '3');

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

// sales trasanction id
if (isset($_GET['sales_transaction_id']) && !empty($_GET['sales_transaction_id'])) {
    $sales_transaction_id = $_GET['sales_transaction_id'];
}

// total installment
$query_total_installment = "SELECT SUM(installment_amount) AS total_installment FROM receivable_installment WHERE receivable_id = '$sales_transaction_id'";
$result_total_installment = $mysqli->query($query_total_installment);

if ($result_total_installment) {
    $row = $result_total_installment->fetch_assoc();
    $total_installment = $row['total_installment'];
}

$query = "SELECT 
            s.*, 
            r.receivable_id, 
            r.receivable_status, 
            r.receivable_due_date, 
            r.fulfillment_date, 
            m.member_email,
            m.member_phone,
            m.member_address,
            b.bank_account_name, 
            b.bank_account_number, 
            sd.product_sku, 
            sd.product_name,
            sd.product_qty,
            sd.selling_price
            FROM 
            sales s
            LEFT JOIN 
                receivable r ON s.receivable_id = r.receivable_id
            JOIN 
                bank_account b ON s.bank_account_number = b.bank_account_number
            JOIN 
                sales_detail sd ON s.sales_transaction_id = sd.sales_transaction_id
            LEFT JOIN
                member m ON s.member_id = m.member_id
            WHERE
                s.sales_transaction_id = '$sales_transaction_id';
            ";
$result = mysqli_query($mysqli, $query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $sales_transaction_id = $row['sales_transaction_id'];
    $receivable_id = $row['receivable_id'];
    $member_id = $row['member_id'];
    $payment_method = $row['payment_method'];
    $receivable_status = $row['receivable_status'];
    $sales_subtotal = $row['sales_subtotal'];
    $sales_total = $row['sales_total'];
    $sales_date = $row['sales_date'];
    $product_name = $row['product_name'];
    $member_name = $row['member_name'];
    $member_email = $row['member_email'];
    $member_phone = $row['member_phone'];
    $member_address = $row['member_address'];
    $receivable_due_date = $row['receivable_due_date'];
    $sales_status = $row['sales_status'];
    $fulfillment_date = $row['fulfillment_date'];
    $bank_account_name = $row['bank_account_name'];
    $bank_account_number = $row['bank_account_number'];
    $promotion_name = $row['promotion_name'];
    $promotion_value = $row['promotion_value'];
}

$accumulation_total_receivable = $sales_total - $total_installment;

// order counter
$order_counter = "SELECT COUNT(*) AS total_rows FROM sales WHERE member_id = '$member_id';";
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

        .is-receivable {
            color: #0a85cc;
            border: 0.5rem double #0a85cc;
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
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="">POS</a></li>
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="">Sales List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Order details</li>
                            </ol>
                        </nav>

                        <div class="d-sm-flex align-items-sm-center">
                            <h1 class="page-header-title">Order #<?php echo $sales_transaction_id ?></h1>
                            <span class="badge ms-sm-3
                                    <?php if ($row['receivable_status'] === '1') {
                                        echo 'bg-soft-info text-info';
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
                        </div>

                        <div class="gap-2 mt-2">
                            <i class="bi bi-calendar4-week"></i> Transaction date : <?php echo $sales_date ?>
                        </div>
                        <div class="gap-2 mt-2 <?php echo ($receivable_id > 1) ? '' : 'd-none' ?>">
                            <i class="bi bi-calendar-minus"></i> Due date : <?php echo $receivable_due_date ?>
                        </div>
                        <div class="gap-2 mt-2 <?php echo ($receivable_id > 1) ? '' : 'd-none' ?>">
                            <i class="bi bi-calendar-check"></i> Fulfilled date : <?php echo $fulfillment_date ?>
                        </div>
                    </div>
                    <!-- End Col -->

                    <?php if ($sales_status === 'active') { ?>
                        <div class="col-sm-auto">
                            <a class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteSalesModal"><i class="bi bi-trash me-1"></i> Delete Sales</a>
                        </div>
                    <?php } ?>

                    <!-- Modal delete sales -->
                    <div id="deleteSalesModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalCenterTitle">Delete sales?
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p>Are you sure want to delete "<strong><?php echo $sales_transaction_id ?></strong>"?<br>You can't undo this action.
                                    </p>
                                    <div class="alert alert-soft-danger" role="alert">
                                        <h3 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Warning</h3>
                                        <p>When you delete this sales, we permanently delete your sales.</p>
                                    </div>

                                    <form action="controller/PosEditSales.php" method="POST">
                                        <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                                        <input type="hidden" name="sales_transaction_id" value="<?php echo $sales_transaction_id ?>">
                                        <input type="hidden" name="receivable_id" value="<?php echo $receivable_id ?>">
                                        <input type="hidden" name="sales_total" value="<?php echo $sales_total ?>">
                                        <input type="hidden" name="bank_account_number" value="<?php echo $bank_account_number ?>">
                                        <!-- Content -->
                                        <?php
                                        $sql = "SELECT product_qty, product_sku FROM `sales_detail` WHERE sales_transaction_id = '$sales_transaction_id'";

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

                                        <button name="btn-delete-sales" type="submit" class="btn btn-danger w-100">Delete</button>
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

            <div class="row">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <!-- Card -->
                    <h1 class="page-header-title mb-3">Sales Invoice</h1>
                    <div class="col-lg-12 mx-auto border border-1 rounded-3">
                        <!-- Header -->
                        <!-- <div class="modal-top-cover bg-dark text-center">
                            <figure class="position-absolute end-0 bottom-0 start-0">
                                <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1920 100.1">
                                    <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z" />
                                </svg>
                            </figure>
                        </div> -->
                        <!-- End Header -->

                        <!-- <div class="modal-top-cover-icon">
                            <span class="icon icon-lg icon-light icon-circle icon-centered shadow-sm">
                                <i class="bi-receipt fs-2"></i>
                            </span>
                        </div> -->

                        <!-- Body -->
                        <div class="modal-body px-5 mt-5">
                            <div class="text-center mb-5">
                                <h3 class="mb-1">RESTO ABC</h3>
                                <span class="d-block">Invoice #<?php echo $sales_transaction_id ?></span>
                            </div>

                            <?php if ($sales_status === 'canceled') { ?>
                                <span class="stamp is-canceled">Canceled</span>
                            <?php } elseif ($receivable_id == '' || $receivable_status === '2') { ?>
                                <span class="stamp is-paid">paid</span>
                            <?php } else { ?>
                                <span class="stamp is-receivable">receivable</span>
                            <?php } ?>

                            <div class="row mb-6">
                                <!-- End Col -->
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <small class="text-cap text-secondary mb-0">Date paid:</small>
                                    <span class="text-dark"><?php echo $sales_date ?></span>
                                </div>
                                <!-- End Col -->

                                <div class="col-md-6">
                                    <small class="text-cap text-secondary mb-0">Payment method:</small>
                                    <div class="d-flex align-items-center">
                                        <span class="text-dark"><?php echo $payment_method ?></span>
                                    </div>
                                </div>
                                <!-- End Col -->
                            </div>
                            <!-- End Row -->

                            <small class="text-cap mb-2">Summary</small>

                            <ul class="list-group mb-4">
                                <?php
                                $sql = "SELECT * FROM `sales_detail` WHERE sales_transaction_id = '$sales_transaction_id' ORDER BY sales_detail_created_at";

                                $result = $mysqli->query($sql);

                                if ($result->num_rows > 0) { ?>
                                    <?php while ($row = $result->fetch_assoc()) {
                                    ?>
                                        <li class="list-group-item text-dark">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><?php echo $row['product_name'] ?></span>
                                                <span><?php echo "Rp " . number_format($row['selling_price'], 0, ',', '.') ?></span>
                                            </div>
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                                <li class="list-group-item list-group-item-light text-dark">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Subtotal</strong>
                                        <strong><?php echo "Rp " . number_format($sales_subtotal, 0, ',', '.') ?></strong>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-light text-dark">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Discount</strong>
                                        <?php
                                        if (strpos($promotion_value, '%') !== false) {
                                            $promotion = $promotion_name . ' - ' . $promotion_value;
                                        } elseif (strpos($promotion_value, '%') !== true) {
                                            $promotion = $promotion_name . ' - ' . " Rp " . number_format(floatval($promotion_value), 0, ',', '.');
                                        }
                                        ?>
                                        <span class="fw-bold"><?php echo $promotion ?></span>
                                    </div>
                                </li>
                                <li class="list-group-item list-group-item-light text-dark">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Amount paid</strong>
                                        <strong><?php echo "Rp " . number_format($sales_total, 0, ',', '.') ?></strong>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->

                    <div class="d-flex justify-content-end d-print-none gap-3 mt-4">
                        <a class="btn btn-primary" href="printSales.php?sales_transaction_id=<?php echo $sales_transaction_id ?>">
                            <i class="bi-printer me-1"></i> Print Sales
                        </a>
                    </div>

                    <?php if ($receivable_id > 0) { ?>
                        <h1 class="page-header-title mt-4 mb-3">Installment sales</h1>
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
                                                    <?php echo "Rp " . number_format($accumulation_total_receivable, 0, ',', '.') ?>
                                                </span>
                                            </span>

                                            <span class="d-block">
                                                &mdash; Total debt
                                                <span class="badge bg-soft-dark text-dark rounded-pill ms-1"><?php echo $receivable_due_date ?></span>
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
                                            $sql = "SELECT * FROM `receivable_installment` WHERE receivable_id = '$receivable_id' ORDER BY receivable_installment_created_at DESC";

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
                                                                <span class="fw-semibold text-dark"><?php echo date('d/m/Y H:i:s', strtotime($row['receivable_installment_created_at'])); ?></span>
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
                    <h1 class="page-header-title mb-3 text-white">Member Information</h1>
                    <!-- Card -->
                    <div class="card sticky-top" style="top: 90px; margin-bottom: 15px; z-index: 10;">
                        <!-- Header -->
                        <div class="card-header">
                            <h4 class="card-header-title">Customer</h4>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="card-body">
                            <!-- List Group -->
                            <ul class="list-group list-group-flush list-group-no-gutters">
                                <?php if ($member_name > 0) { ?>
                                    <li class="list-group-item">
                                        <a class="d-flex align-items-center" href="customerProfile.php">
                                            <div class="avatar avatar-soft-primary avatar-circle me-2">
                                                <span class="avatar-initials"><?php echo substr($member_name, 0, 1) ?></span>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <span class="text-body text-inherit"><?php echo $member_name ?></span>
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
                                                <span class="text-body text-inherit"><?php echo $order_counter ?> orders</span>
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
                                            <li><i class="bi-at me-2"></i><?php echo $member_email ?></li>
                                            <li><i class="bi-phone me-2"></i>
                                                <input type="text" class="js-input-mask border-0" id="phoneLabel" placeholder="+(xx) xxx-xxx-xxx-xxx" data-hs-mask-options='{
                                            "mask": "+(00) 000-000-000-000"
                                            }' value="<?php echo $member_phone ?>" readonly>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5>Customer address</h5>
                                        </div>

                                        <span class="d-block text-body">
                                            <?php echo $member_address ?>
                                        </span>
                                    </li>
                                <?php } else { ?>
                                    <img class="img-fluid mx-auto mb-3" src="assets/svg/illustrations/oc-error.svg" alt="Image Description" data-hs-theme-appearance="default" style="max-width: 15rem;">
                                    <img class="img-fluid mx-auto mb-3" src="assets/svg/illustrations-light/oc-error.svg" alt="Image Description" data-hs-theme-appearance="dark" style="max-width: 15rem;">
                                    <span class="text-center">No customer data in this sales</span>
                                <?php } ?>
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
    <script src="./assets/vendor/imask/dist/imask.min.js"></script>

    <!-- JS Implementing Plugins -->
    <script src="./assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside.min.js"></script>
    <script src="./assets/vendor/hs-form-search/dist/hs-form-search.min.js"></script>

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>

    <script>
        (function() {
            // INITIALIZATION OF INPUT MASK
            // =======================================================
            HSCore.components.HSMask.init('.js-input-mask')
        })();
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