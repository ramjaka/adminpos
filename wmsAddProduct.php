<?php
require_once 'controller/Session.php';
$page = 'wms-add-product';
$page_access = array('1', '11');

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
                <div class="row align-items-center">
                    <div class="col-sm mb-2 mb-sm-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-no-gutter">
                                <li class="breadcrumb-item"><a class="breadcrumb-link" href="./ecommerce-products.html">WMS</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Product</li>
                            </ol>
                        </nav>

                        <h1 class="page-header-title">Add Product</h1>
                    </div>
                    <!-- End Col -->
                </div>
                <!-- End Row -->
            </div>
            <!-- End Page Header -->

            <form class="row" action="controller/WmsAddProduct.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="username_creator" value="<?php echo $username ?>">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <!-- Card -->
                    <div class="card mb-3 mb-lg-5">
                        <!-- Header -->
                        <div class="card-header">
                            <h4 class="card-header-title">Product information</h4>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Form -->
                            <div class="mb-4">
                                <label for="productNameLabel" class="form-label">Product name *</label>

                                <input type="text" class="form-control <?php echo isset($_GET['productNameValidation']) ? 'is-invalid' : ''; ?>" name="product_name">
                                <?php if (isset($_GET['productNameValidation'])) { ?>
                                    <span class="invalid-feedback"><?php echo urldecode($_GET['productNameValidation']); ?></span>
                                <?php } ?>
                            </div>
                            <!-- End Form -->

                            <div class="row">
                                <div class="col-sm-6">
                                    <!-- Form -->
                                    <div class="mb-4">
                                        <label for="SKULabel" class="form-label">SKU *</label>

                                        <input type="text" class="form-control <?php echo isset($_GET['productSKUValidation']) ? 'is-invalid' : ''; ?>" name="product_sku" placeholder="eg. 348121032">
                                        <?php if (isset($_GET['productSKUValidation'])) { ?>
                                            <span class="invalid-feedback"><?php echo urldecode($_GET['productSKUValidation']); ?></span>
                                        <?php } ?>
                                    </div>
                                    <!-- End Form -->
                                </div>
                                <!-- End Col -->

                                <div class="col-sm-6">
                                    <!-- Form -->
                                    <div class="mb-4">
                                        <label for="weightLabel" class="form-label">Weight *</label>

                                        <div class="input-group">
                                            <input type="text" class="form-control <?php echo isset($_GET['productWeightValidation']) ? 'is-invalid' : ''; ?>" name="product_weight">
                                            <span class="input-group-text">kg</span>
                                            <?php if (isset($_GET['productWeightValidation'])) { ?>
                                                <span class="invalid-feedback"><?php echo urldecode($_GET['productWeightValidation']); ?></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <!-- End Form -->
                                </div>
                                <!-- End Col -->

                                <div class="col-sm-12">
                                    <!-- Form -->
                                    <div class="mb-4">
                                        <label for="weightLabel" class="form-label">Barcode</label>

                                        <input type="text" class="form-control <?php echo isset($_GET['productBarcodeValidation']) ? 'is-invalid' : ''; ?>" name="product_barcode">
                                        <?php if (isset($_GET['productBarcodeValidation'])) { ?>
                                            <span class="invalid-feedback"><?php echo urldecode($_GET['productBarcodeValidation']); ?></span>
                                        <?php } ?>
                                    </div>
                                    <!-- End Form -->
                                </div>
                                <!-- End Col -->
                            </div>
                            <!-- End Row -->

                            <div class="d-flex justify-content-between">
                                <label for="reviewLabelModalEg" class="form-label">Description <span class="form-label-secondary">(Optional)</span></label>

                                <span id="maxLengthCountCharacters" class="text-muted"></span>
                            </div>
                            <textarea class="js-count-characters form-control" id="reviewLabelModalEg" placeholder="Textarea field" rows="4" maxlength="100" data-hs-count-characters-options='{
                            "output": "#maxLengthCountCharacters"
                            }' name="product_description"></textarea>
                        </div>
                        <!-- Body -->
                    </div>
                    <!-- End Card -->

                    <!-- Card -->
                    <div class="card mb-3 mb-lg-5">
                        <!-- Header -->
                        <div class="card-header card-header-content-between">
                            <h4 class="card-header-title">Media</h4>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Gallery -->
                            <div id="fancyboxGallery" class="js-fancybox row justify-content-sm- gx-3">

                                <div class="col-6 col-sm-4 col-md-3 mb-3 mb-lg-5">
                                    <!-- Card -->
                                    <div class="card card-sm">
                                        <img id="avatarImg1" class="card-img-top" src="assets/img/others/gallery-upload-icon.png" alt="Image Description">

                                        <div class="card-body <?php echo isset($_GET['productMedia1Validation']) ? 'is-invalid' : ''; ?>">
                                            <div class="row col-divider text-center">
                                                <div class="col">
                                                    <div class="form-attachment-btn1" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload">
                                                        <i class="bi bi-upload"></i>
                                                        <input name="product_media_1" type="file" class="js-file-attach form-attachment-btn1-label" id="avatarUploader" data-hs-file-attach-options='{
                                                            "textTarget": "#avatarImg1",
                                                            "mode": "image",
                                                            "targetAttr": "src",
                                                            "resetTarget": ".js-file-attach-reset-img1",
                                                            "resetImg": "assets/img/others/gallery-upload-icon.png",
                                                            "allowTypes": [".png", ".jpeg", ".jpg"]
                                                        }'>
                                                    </div>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col">
                                                    <a class="js-file-attach-reset-img1 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                        <i class="bi-trash"></i>
                                                    </a>
                                                </div>
                                                <!-- End Col -->
                                            </div>
                                            <!-- End Row -->
                                        </div>
                                        <!-- End Col -->
                                        <?php if (isset($_GET['productMedia1Validation'])) { ?>
                                            <span class="invalid-feedback bg-soft-danger rounded-bottom p-2">
                                                <?php echo urldecode($_GET['productMedia1Validation']); ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                    <!-- End Card -->
                                </div>
                                <!-- End Col -->

                                <div class="col-6 col-sm-4 col-md-3 mb-3 mb-lg-5">
                                    <!-- Card -->
                                    <div class="card card-sm">
                                        <img id="avatarImg2" class="card-img-top" src="assets/img/others/gallery-upload-icon.png" alt="Image Description">

                                        <div class="card-body">
                                            <div class="row col-divider text-center">
                                                <div class="col">
                                                    <div class="form-attachment-btn2" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload">
                                                        <i class="bi bi-upload"></i>
                                                        <input name="product_media_2" type="file" class="js-file-attach form-attachment-btn2-label" id="avatarUploader" data-hs-file-attach-options='{
                                                            "textTarget": "#avatarImg2",
                                                            "mode": "image",
                                                            "targetAttr": "src",
                                                            "resetTarget": ".js-file-attach-reset-img2",
                                                            "resetImg": "assets/img/others/gallery-upload-icon.png",
                                                            "allowTypes": [".png", ".jpeg", ".jpg"]
                                                        }'>
                                                    </div>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col">
                                                    <a class="js-file-attach-reset-img2 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                        <i class="bi-trash"></i>
                                                    </a>
                                                </div>
                                                <!-- End Col -->
                                            </div>
                                            <!-- End Row -->
                                        </div>
                                        <!-- End Col -->
                                    </div>
                                    <!-- End Card -->
                                </div>
                                <!-- End Col -->

                                <div class="col-6 col-sm-4 col-md-3 mb-3 mb-lg-5">
                                    <!-- Card -->
                                    <div class="card card-sm">
                                        <img id="avatarImg3" class="card-img-top" src="assets/img/others/gallery-upload-icon.png" alt="Image Description">

                                        <div class="card-body">
                                            <div class="row col-divider text-center">
                                                <div class="col">
                                                    <div class="form-attachment-btn3" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload">
                                                        <i class="bi bi-upload"></i>
                                                        <input name="product_media_3" type="file" class="js-file-attach form-attachment-btn3-label" id="avatarUploader" data-hs-file-attach-options='{
                                                            "textTarget": "#avatarImg3",
                                                            "mode": "image",
                                                            "targetAttr": "src",
                                                            "resetTarget": ".js-file-attach-reset-img3",
                                                            "resetImg": "assets/img/others/gallery-upload-icon.png",
                                                            "allowTypes": [".png", ".jpeg", ".jpg"]
                                                        }'>
                                                    </div>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col">
                                                    <a class="js-file-attach-reset-img3 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                        <i class="bi-trash"></i>
                                                    </a>
                                                </div>
                                                <!-- End Col -->
                                            </div>
                                            <!-- End Row -->
                                        </div>
                                        <!-- End Col -->
                                    </div>
                                    <!-- End Card -->
                                </div>
                                <!-- End Col -->

                                <div class="col-6 col-sm-4 col-md-3 mb-3 mb-lg-5">
                                    <!-- Card -->
                                    <div class="card card-sm">
                                        <img id="avatarImg4" class="card-img-top" src="assets/img/others/gallery-upload-icon.png" alt="Image Description">

                                        <div class="card-body">
                                            <div class="row col-divider text-center">
                                                <div class="col">
                                                    <div class="form-attachment-btn4" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload">
                                                        <i class="bi bi-upload"></i>
                                                        <input name="product_media_4" type="file" class="js-file-attach form-attachment-btn4-label" id="avatarUploader" data-hs-file-attach-options='{
                                                            "textTarget": "#avatarImg4",
                                                            "mode": "image",
                                                            "targetAttr": "src",
                                                            "resetTarget": ".js-file-attach-reset-img4",
                                                            "resetImg": "assets/img/others/gallery-upload-icon.png",
                                                            "allowTypes": [".png", ".jpeg", ".jpg"]
                                                        }'>
                                                    </div>
                                                </div>
                                                <!-- End Col -->

                                                <div class="col">
                                                    <a class="js-file-attach-reset-img4 text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                        <i class="bi-trash"></i>
                                                    </a>
                                                </div>
                                                <!-- End Col -->
                                            </div>
                                            <!-- End Row -->
                                        </div>
                                        <!-- End Col -->
                                    </div>
                                    <!-- End Card -->
                                </div>
                                <!-- End Col -->
                                <span>Maximum file size is 5MB & supported file formats are only jpg & png</span>
                            </div>
                            <!-- End Gallery -->
                        </div>
                        <!-- Body -->
                    </div>
                    <!-- End Card -->
                </div>
                <!-- End Col -->

                <div class="col-lg-4">
                    <!-- Card -->
                    <div class="card">
                        <!-- Header -->
                        <div class="card-header">
                            <h4 class="card-header-title">Organization</h4>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Form -->
                            <div class="mb-4">
                                <label for="categoryLabel" class="form-label">Category *</label>

                                <!-- Select -->
                                <div class="tom-select-custom">
                                    <select class="js-select form-select <?php echo isset($_GET['productCategoryValidation']) ? 'is-invalid' : ''; ?>" autocomplete="off" data-hs-tom-select-options='{
                                        "placeholder": "Select a category..."
                                    }' name="product_category">
                                        <option value="">Select gear...</option>
                                        <optgroup label="Engine and Engine Components">
                                            <option value="air filter">Air Filter</option>
                                            <option value="engine oil">Engine Oil</option>
                                            <option value="alternator">Alternator</option>
                                            <option value="starter">Starter</option>
                                            <option value="clutch">Clutch</option>
                                            <!-- Add more engine components here -->
                                        </optgroup>
                                        <optgroup label="Fuel System">
                                            <option value="fuel pump">Fuel Pump</option>
                                            <option value="injector">Injector</option>
                                            <option value="carburetor">Carburetor</option>
                                            <option value="fuel tank">Fuel Tank</option>
                                            <!-- Add more fuel system components here -->
                                        </optgroup>
                                        <optgroup label="Engine Control System">
                                            <option value="engine sensors">Engine Sensors</option>
                                            <option value="engine control module">Engine Control Module (ECM)</option>
                                            <option value="throttle body">Throttle Body</option>
                                            <!-- Add more engine control system components here -->
                                        </optgroup>
                                        <optgroup label="Transmission System">
                                            <option value="automatic transmission">Automatic Transmission</option>
                                            <option value="manual transmission">Manual Transmission</option>
                                            <option value="clutch">Clutch</option>
                                            <option value="transmission gears">Transmission Gears</option>
                                            <option value="driveshaft">Driveshaft</option>
                                            <!-- Add more transmission system components here -->
                                        </optgroup>
                                        <optgroup label="Brake System">
                                            <option value="brake pads">Brake Pads</option>
                                            <option value="brake discs">Brake Discs</option>
                                            <option value="brake pump">Brake Pump</option>
                                            <option value="brake cylinder">Brake Cylinder</option>
                                            <!-- Add more brake system components here -->
                                        </optgroup>
                                        <optgroup label="Suspension System">
                                            <option value="shock absorbers">Shock Absorbers</option>
                                            <option value="springs">Springs</option>
                                            <option value="suspension bushings">Suspension Bushings</option>
                                            <option value="stabilizer link">Stabilizer Link</option>
                                            <!-- Add more suspension system components here -->
                                        </optgroup>
                                        <optgroup label="Braking System">
                                            <option value="brake pads">Brake Pads</option>
                                            <option value="master cylinder">Master Cylinder</option>
                                            <option value="wheel cylinder">Wheel Cylinder</option>
                                            <!-- Add more braking system components here -->
                                        </optgroup>
                                        <optgroup label="Electrical System">
                                            <option value="headlights">Headlights</option>
                                            <option value="battery">Battery</option>
                                            <option value="alternator">Alternator</option>
                                            <option value="electrical wires">Electrical Wires</option>
                                            <option value="switches">Switches</option>
                                            <!-- Add more electrical system components here -->
                                        </optgroup>
                                        <optgroup label="Cooling System">
                                            <option value="radiator">Radiator</option>
                                            <option value="radiator fan">Radiator Fan</option>
                                            <option value="thermostat">Thermostat</option>
                                            <option value="coolant hoses">Coolant Hoses</option>
                                            <!-- Add more cooling system components here -->
                                        </optgroup>
                                        <optgroup label="Ignition System">
                                            <option value="spark plugs">Spark Plugs</option>
                                            <option value="ignition coil">Ignition Coil</option>
                                            <option value="spark plug wires">Spark Plug Wires</option>
                                            <!-- Add more ignition system components here -->
                                        </optgroup>
                                        <optgroup label="Exhaust System">
                                            <option value="tires">Tires</option>
                                            <option value="rims">Rims</option>
                                            <option value="bearings">Bearings</option>
                                            <option value="exhaust tip">Exhaust Tip</option>
                                            <!-- Add more exhaust system components here -->
                                        </optgroup>
                                        <optgroup label="Exterior Accessories">
                                            <option value="grill">Grill</option>
                                            <option value="mirror">Mirror</option>
                                            <option value="bumper">Bumper</option>
                                            <!-- Add more exterior accessories here -->
                                        </optgroup>
                                        <optgroup label="Interior Accessories">
                                            <option value="seats">Seats</option>
                                            <option value="interior panel">Interior Panel</option>
                                            <option value="carpet">Carpet</option>
                                            <!-- Add more interior accessories here -->
                                        </optgroup>
                                        <optgroup label="Other Spare Parts">
                                            <option value="vehicle service tools">Vehicle Service Tools</option>
                                            <option value="lubricants">Lubricants and Other Fluids</option>
                                            <option value="other supporting equipment">Other Supporting Equipment</option>
                                            <!-- Add more other spare parts here -->
                                        </optgroup>
                                    </select>
                                    <?php if (isset($_GET['productCategoryValidation'])) { ?>
                                        <span class="invalid-feedback"><?php echo urldecode($_GET['productCategoryValidation']); ?></span>
                                    <?php } ?>
                                </div>
                                <!-- End Select -->
                            </div>
                            <!-- Form -->
                            <div class="mb-2">
                                <label for="categoryLabel" class="form-label">Color *</label>

                                <!-- Select -->
                                <div class="tom-select-custom">
                                    <select class="js-select form-select <?php echo isset($_GET['productColorValidation']) ? 'is-invalid' : ''; ?>" autocomplete="off" name="product_color" data-hs-tom-select-options='{
                                        "placeholder": "Select color..."
                                    }'>
                                        <option label="empty"></option>
                                        <option value="black">Black</option>
                                        <option value="blue">Blue</option>
                                        <option value="brown">Brown</option>
                                        <option value="gray">Gray</option>
                                        <option value="green">Green</option>
                                        <option value="maroon">Maroon</option>
                                        <option value="navy">Navy</option>
                                        <option value="olive">Olive</option>
                                        <option value="orange">Orange</option>
                                        <option value="pink">Pink</option>
                                        <option value="purple">Purple</option>
                                        <option value="red">Red</option>
                                        <option value="teal">Teal</option>
                                        <option value="white">White</option>
                                        <option value="yellow">Yellow</option>
                                    </select>
                                    <?php if (isset($_GET['productColorValidation'])) { ?>
                                        <span class="invalid-feedback"><?php echo urldecode($_GET['productColorValidation']); ?></span>
                                    <?php } ?>
                                </div>
                                <!-- End Select -->
                            </div>
                            <!-- Form -->
                        </div>
                        <!-- Body -->
                    </div>
                    <!-- End Card -->
                </div>
                <!-- End Col -->

                <div class="position-fixed start-50 bottom-0 translate-middle-x w-100 zi-99 mb-3" style="max-width: 40rem;">
                    <!-- Card -->
                    <div class="card card-sm bg-dark border-dark mx-2">
                        <div class="card-body">
                            <div class="row justify-content-center justify-content-sm-between">
                                <div class="col text-white" style="margin-top: 10px;">
                                    <span><i class="bi bi-info-circle me-1"></i> Press the save button to save the product master</span>
                                </div>
                                <!-- End Col -->

                                <div class="col-auto">
                                    <div class="d-flex gap-3">
                                        <button type="submit" name="btn-add-product" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                                <!-- End Col -->
                            </div>
                            <!-- End Row -->
                        </div>
                    </div>
                    <!-- End Card -->
                </div>
            </form>
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

    <script src="./assets/vendor/hs-quantity-counter/dist/hs-quantity-counter.min.js"></script>
    <script src="./assets/vendor/hs-add-field/dist/hs-add-field.min.js"></script>
    <script src="./assets/vendor/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="./assets/vendor/quill/dist/quill.min.js"></script>
    <script src="./assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
    <script src="./assets/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="./assets/vendor/datatables.net.extensions/select/select.min.js"></script>
    <script src="./assets/vendor/imask/dist/imask.min.js"></script>
    <script src="./assets/vendor/hs-file-attach/dist/hs-file-attach.min.js"></script>
    <script src="./assets/vendor/hs-count-characters/dist/js/hs-count-characters.js"></script>

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>

    <!-- JS Plugins Init. -->
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            HSCore.components.HSDatatables.init($('#datatable'), {
                select: {
                    style: 'multi',
                    selector: 'td:first-child input[type="checkbox"]',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                }
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


                // INITIALIZATION OF ADD FIELD
                // =======================================================
                new HSAddField('.js-add-field', {
                    addedField: field => {
                        HSCore.components.HSTomSelect.init(field.querySelector('.js-select-dynamic'))
                    }
                })


                // INITIALIZATION OF  QUANTITY COUNTER
                // =======================================================
                new HSQuantityCounter('.js-quantity-counter')


                // INITIALIZATION OF DROPZONE
                // =======================================================
                HSCore.components.HSDropzone.init('.js-dropzone')


                // INITIALIZATION OF QUILLJS EDITOR
                // =======================================================
                HSCore.components.HSQuill.init('.js-quill')
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
        // Function to format input value as rupiah
        function formatRupiah(value) {
            // Check if the value is not empty
            if (value) {
                // Remove non-digit characters
                value = value.toString().replace(/[^0-9]/g, '');
                // Add thousands separator
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                // Add rupiah symbol
                value = value;
            }
            return value;
        }

        // Function to update input value on keyup event
        function updateValue() {
            var input = document.getElementById('productPriceLabel');
            var value = input.value;
            input.value = formatRupiah(value);
        }

        // Attach event listener to input element
        var inputElement = document.getElementById('productPriceLabel');
        inputElement.addEventListener('keyup', updateValue);
    </script>

    <script>
        (function() {
            // INITIALIZATION OF FILE ATTACH
            // =======================================================
            new HSFileAttach('.js-file-attach')
        })();
    </script>

    <script>
        (function() {
            // INITIALIZATION OF COUNT CHARACTERS
            // =======================================================
            new HSCountCharacters('.js-count-characters')
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener to all input fields
            document.querySelectorAll('input').forEach(function(input) {
                input.addEventListener('keypress', function(event) {
                    // Check if Enter key is pressed
                    if (event.which == 10 || event.which == 13) {
                        event.preventDefault(); // Prevent form submission
                    }
                });
            });
        });
    </script>

    <!-- End Style Switcher JS -->
</body>

</html>