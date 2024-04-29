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
if (isset($_GET['transaction_id']) && !empty($_GET['transaction_id'])) {
    $transaction_id = $_GET['transaction_id'];
}

$query = "SELECT 
            m.*,
            b.bank_account_name, 
            b.bank_account_number
            FROM 
            mutation m
            JOIN 
                bank_account b ON m.bank_account_number = b.bank_account_number
            WHERE
                m.transaction_id = '$transaction_id';
            ";
$result = mysqli_query($mysqli, $query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $transaction_id = $row['transaction_id'];
    $mutation_created_at = $row['mutation_created_at'];
    $debt_id = $row['debt_id'];
    $debt = $row['debt'];
    $bank_account_name = $row['bank_account_name'];
    $bank_account_number = $row['bank_account_number'];
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
        <div class="col-lg-8 mx-auto border border-1 rounded-3">
            <!-- Header -->
            <div class="modal-top-cover bg-dark text-center">
                <figure class="position-absolute end-0 bottom-0 start-0">
                    <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1920 100.1">
                        <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z" />
                    </svg>
                </figure>
            </div>
            <!-- End Header -->

            <div class="modal-top-cover-icon">
                <span class="icon icon-lg icon-light icon-circle icon-centered shadow-sm">
                    <i class="bi-receipt fs-2"></i>
                </span>
            </div>

            <!-- Body -->
            <div class="modal-body px-5">
                <div class="text-center mb-5">
                    <h3 class="mb-1">Invoice from Add Balance</h3>
                    <span class="d-block">Invoice #<?php echo $transaction_id ?></span>
                </div>

                <span class="stamp is-paid">paid</span>

                <div class="row mb-6">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <small class="text-cap text-secondary mb-0">Amount paid:</small>
                        <span class="text-dark"><?php echo "Rp " . number_format($debt, 0, ',', '.'); ?></span>
                    </div>
                    <!-- End Col -->

                    <div class="col-md-4 mb-3 mb-md-0">
                        <small class="text-cap text-secondary mb-0">Date paid:</small>
                        <span class="text-dark"><?php echo date('d/m/Y', strtotime($mutation_created_at)) ?></span>
                    </div>
                    <!-- End Col -->

                    <div class="col-md-4">
                        <small class="text-cap text-secondary mb-0">Payment method:</small>
                        <div class="d-flex align-items-center">
                            <span class="text-dark"><?php echo $bank_account_name ?></span>
                        </div>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->

                <small class="text-cap mb-2">Summary</small>

                <ul class="list-group mb-4">
                    <?php
                    $sql = "SELECT * FROM `mutation` WHERE transaction_id = '$transaction_id' ORDER BY mutation_created_at";

                    $result = $mysqli->query($sql);

                    if ($result->num_rows > 0) { ?>
                        <?php while ($row = $result->fetch_assoc()) {
                        ?>
                            <li class="list-group-item text-dark">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><?php echo $row['transaction_description'] ?></span>
                                    <span><?php echo "Rp " . number_format($row['debt'], 0, ',', '.') ?></span>
                                </div>
                            </li>
                        <?php } ?>
                    <?php } ?>
                    <li class="list-group-item list-group-item-light text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Amount paid</strong>
                            <strong><?php echo "Rp " . number_format($debt, 0, ',', '.') ?></strong>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- End Body -->
        </div>
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