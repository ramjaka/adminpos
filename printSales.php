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

        @media print {
            .row {
                display: flex !important;
                flex-wrap: wrap !important;
            }

            .col-md-4 {
                flex: 0 0 33.33333% !important;
                max-width: 33.33333% !important;
            }
        }
    </style>
</head>

<body class="has-navbar-vertical-aside navbar-vertical-aside-show-xl   footer-offset">

    <script src="./assets/js/hs.theme-appearance.js"></script>

    <script src="./assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside-mini-cache.js"></script>

    <!-- ========== MAIN CONTENT ========== -->

    <!-- Content -->
    <div class="content container-fluid">
        <div class="row">
            <div class="col-lg-4 mb-3 mb-lg-0">
                <!-- Card -->
                <div class="col-lg-12 mx-auto rounded-3">

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

                        <div class="row mb-6">

                            <div class="col-md-4 mb-3 mb-md-0">
                                <small class="text-cap text-secondary mb-0">Date paid:</small>
                                <span class="text-dark"><?php echo $sales_date ?></span>
                            </div>
                            <!-- End Col -->

                            <div class="col-md-4">
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
            </div>
        </div>
        <!-- End Row -->
    </div>
    <!-- End Content -->
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

    <script>
        function printPage() {
            window.print();
        }

        window.onload = printPage;
        window.addEventListener('afterprint', function(event) {
            window.history.back();
        });
        window.onbeforeunload = function() {
            window.removeEventListener('afterprint', function(event) {
                window.history.back();
            });
        };
    </script>

    <!-- End Style Switcher JS -->
</body>

</html>