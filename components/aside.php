<aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered bg-white  ">
    <div class="navbar-vertical-container">
        <div class="navbar-vertical-footer-offset">
            <!-- Logo -->

            <a class="navbar-brand" href="./index.php" aria-label="Front">
                <img class="navbar-brand-logo" src="./assets/img/logo/logo.png" alt="Logo" data-hs-theme-appearance="default">
                <img class="navbar-brand-logo" src="./assets/img/logo/logo-white.png" alt="Logo" data-hs-theme-appearance="dark">
                <img class="navbar-brand-logo-mini" src="./assets/img/logo/logo-icon.png" alt="Logo" data-hs-theme-appearance="default">
                <img class="navbar-brand-logo-mini" src="./assets/img/logo/logo-icon-white.png" alt="Logo" data-hs-theme-appearance="dark">
            </a>

            <!-- End Logo -->

            <!-- Navbar Vertical Toggle -->
            <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
                <i class="bi-arrow-bar-left navbar-toggler-short-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Collapse"></i>
                <i class="bi-arrow-bar-right navbar-toggler-full-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Expand"></i>
            </button>

            <!-- End Navbar Vertical Toggle -->

            <!-- Content -->
            <div class="navbar-vertical-content">
                <div id="navbarVerticalMenu" class="nav nav-pills nav-vertical card-navbar-nav">
                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'dashboard') ? 'active' : '' ?>" href="./dashboard.php" data-placement="left">
                            <i class="bi-house-door nav-icon"></i>
                            <span class="nav-link-title">Dashboard</span>
                        </a>
                    </div>

                    <span class="dropdown-header mt-4">POS</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'pos-cashier') ? 'active' : '' ?>" href="./posCashier.php" data-placement="left">
                            <i class="fa-light fa-cash-register nav-icon mt-1"></i>
                            <span class="nav-link-title">Cashier</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'pos-sales-list') ? 'active' : '' ?>" href="./posSalesList.php" data-placement="left">
                            <i class="bi bi-list-task nav-icon"></i>
                            <span class="nav-link-title">Sales List</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'pos-promotion') ? 'active' : '' ?>" href="./posPromotion.php" data-placement="left">
                            <i class="bi bi-percent nav-icon"></i>
                            <span class="nav-link-title">Promotion</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'pos-crm' || $page === 'customer-profile') ? 'active' : '' ?>" href="./posCRM.php" data-placement="left">
                            <i class="fa-regular fa-users-rays nav-icon mt-1"></i>
                            <span class="nav-link-title">CRM</span>
                        </a>
                    </div>

                    <span class="dropdown-header mt-4">WMS</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'wms-purchase') ? 'active' : '' ?>" href="./wmsPurchase.php" data-placement="left">
                            <i class="bi bi-bag nav-icon"></i>
                            <span class="nav-link-title">Purchase</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'wms-purchasing-list') ? 'active' : '' ?>" href="./wmsPurchasingList.php" data-placement="left">
                            <i class="bi bi-receipt nav-icon"></i>
                            <span class="nav-link-title">Purchasing List</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'wms-stock' || $page === 'stock-details') ? 'active' : '' ?>" href="./wmsStock.php" data-placement="left">
                            <i class="bi bi-boxes nav-icon"></i>
                            <span class="nav-link-title">Stock</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'wms-spending') ? 'active' : '' ?>" href="./wmsSpending.php" data-placement="left">
                            <i class="bi bi-wallet2 nav-icon"></i>
                            <span class="nav-link-title">Spending</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'wms-supplier' || $page === 'supplier-details') ? 'active' : '' ?>" href="./wmsSupplier.php" data-placement="left">
                            <i class="bi bi-building nav-icon"></i>
                            <span class="nav-link-title">Supplier</span>
                        </a>
                    </div>

                    <!-- Collapse -->
                    <div class="nav-item">
                        <a class="nav-link dropdown-toggle <?php echo ($page === 'wms-add-product' || $page === 'wms-product-list') ? 'active' : '' ?>" href="#navbarVerticalMenuPagesEcommerceProductsMenu" role="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalMenuPagesEcommerceProductsMenu" aria-expanded="<?php echo ($page === 'wms-add-product' || $page === 'wms-product-list') ? 'true' : 'false' ?>" aria-controls="navbarVerticalMenuPagesEcommerceProductsMenu">
                            <i class="bi bi-box2-heart nav-icon"></i>
                            <span class="nav-link-title">Products</span>
                        </a>

                        <div id="navbarVerticalMenuPagesEcommerceProductsMenu" class="nav-collapse collapse <?php echo ($page === 'wms-add-product' || $page === 'wms-product-list') ? 'show' : '' ?>" data-bs-parent="#navbarVerticalMenuPagesMenuEcommerce">
                            <a class="nav-link <?php echo ($page === 'wms-add-product') ? 'active' : '' ?>" href="./wmsAddProduct.php">Add Product</a>
                            <a class="nav-link <?php echo ($page === 'wms-product-list') ? 'active' : '' ?>" href="./wmsProductsList.php">Product List</a>
                        </div>
                    </div>
                    <!-- End Collapse -->

                    <span class="dropdown-header mt-4">Bank</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'bank-account') ? 'active' : '' ?>" href="./bankAccount.php" data-placement="left">
                            <i class="bi bi-credit-card-2-front nav-icon"></i>
                            <span class="nav-link-title">Account</span>
                        </a>
                    </div>

                    <!-- <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'bank-debt') ? 'active' : '' ?>" href="./bankDebt.php" data-placement="left">
                            <i class="fa-light fa-money-check-dollar-pen nav-icon mt-1"></i>
                            <span class="nav-link-title">Debts</span>
                        </a>
                    </div> -->

                    <!-- <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'bank-receivables') ? 'active' : '' ?>" href="./bankReceivables.php" data-placement="left">
                            <i class="bi bi-cash-coin nav-icon"></i>
                            <span class="nav-link-title">Receivables</span>
                        </a>
                    </div> -->

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'bank-mutation') ? 'active' : '' ?>" href="./bankMutation.php" data-placement="left">
                            <i class="bi bi-bank nav-icon"></i>
                            <span class="nav-link-title">Mutation</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'bank-accounting') ? 'active' : '' ?>" href="./bankAccounting.php" data-placement="left">
                            <i class="fa-regular fa-calculator-simple nav-icon mt-1"></i>
                            <span class="nav-link-title">Accounting</span>
                        </a>
                    </div>

                    <span class="dropdown-header mt-4">User</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'users-overview') ? 'active' : '' ?>" href="./userOverview.php" data-placement="left">
                            <i class="bi bi-people nav-icon"></i>
                            <span class="nav-link-title">Overview</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link <?php echo ($page === 'user-add-user') ? 'active' : '' ?>" href="./userAddUser.php" data-placement="left">
                            <i class="bi bi-person-plus nav-icon"></i>
                            <span class="nav-link-title">Add User</span>
                        </a>
                    </div>
                </div>

            </div>
            <!-- End Content -->

            <!-- Footer -->
            <div class="navbar-vertical-footer">
                <ul class="navbar-vertical-footer-list">
                    <li class="navbar-vertical-footer-list-item">
                        <!-- Style Switcher -->
                        <div class="dropdown dropup">
                            <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle" id="selectThemeDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-dropdown-animation>

                            </button>

                            <div class="dropdown-menu navbar-dropdown-menu navbar-dropdown-menu-borderless" aria-labelledby="selectThemeDropdown">
                                <a class="dropdown-item" href="#" data-icon="bi-moon-stars" data-value="auto">
                                    <i class="bi-moon-stars me-2"></i>
                                    <span class="text-truncate" title="Auto (system default)">Auto (system default)</span>
                                </a>
                                <a class="dropdown-item" href="#" data-icon="bi-brightness-high" data-value="default">
                                    <i class="bi-brightness-high me-2"></i>
                                    <span class="text-truncate" title="Default (light mode)">Default (light mode)</span>
                                </a>
                                <a class="dropdown-item active" href="#" data-icon="bi-moon" data-value="dark">
                                    <i class="bi-moon me-2"></i>
                                    <span class="text-truncate" title="Dark">Dark</span>
                                </a>
                            </div>
                        </div>

                        <!-- End Style Switcher -->
                    </li>

                    <li class="navbar-vertical-footer-list-item">
                        <!-- Other Links -->
                        <div class="dropdown dropup">
                            <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle" id="otherLinksDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-dropdown-animation>
                                <i class="bi-info-circle"></i>
                            </button>

                            <div class="dropdown-menu navbar-dropdown-menu-borderless" aria-labelledby="otherLinksDropdown">
                                <span class="dropdown-header">Help</span>
                                <a class="dropdown-item" href="#">
                                    <i class="bi-journals dropdown-item-icon"></i>
                                    <span class="text-truncate" title="Resources &amp; tutorials">Resources &amp; tutorials</span>
                                </a>
                            </div>
                        </div>
                        <!-- End Other Links -->
                    </li>
                </ul>
            </div>
            <!-- End Footer -->
        </div>
    </div>
</aside>