<header id="header" class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-container navbar-bordered bg-white">
    <div class="navbar-nav-wrap">
        <!-- Logo -->
        <a class="navbar-brand" href="./index.html" aria-label="Front">
            <img class="navbar-brand-logo" src="./assets/img/logo/logo.png" alt="Logo" data-hs-theme-appearance="default">
            <img class="navbar-brand-logo" src="./assets/img/logo/logo-white.png" alt="Logo" data-hs-theme-appearance="dark">
            <img class="navbar-brand-logo-mini" src="./assets/img/logo/logo-icon.png" alt="Logo" data-hs-theme-appearance="default">
            <img class="navbar-brand-logo-mini" src="./assets/img/logo/logo-icon-white.png" alt="Logo" data-hs-theme-appearance="dark">
        </a>
        <!-- End Logo -->

        <div class="navbar-nav-wrap-content-start">
            <!-- Navbar Vertical Toggle -->
            <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
                <i class="bi-arrow-bar-left navbar-toggler-short-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Collapse"></i>
                <i class="bi-arrow-bar-right navbar-toggler-full-align" data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>' data-bs-toggle="tooltip" data-bs-placement="right" title="Expand"></i>
            </button>

            <!-- End Navbar Vertical Toggle -->

            <!-- Search Form -->
            <div class="ms-2">
                <div id="time"></div>
            </div>
        </div>

        <div class="navbar-nav-wrap-content-end">
            <!-- Navbar -->
            <ul class="navbar-nav">

                <li class="nav-item">
                    <!-- Account -->
                    <div class="dropdown">
                        <a class="navbar-dropdown-account-wrapper" href="javascript:;" id="accountNavbarDropdown" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" data-bs-dropdown-animation>
                            <div class="avatar avatar-soft-primary avatar-sm avatar-circle">
                                <span class="avatar-initials"><?php echo substr($first_name, 0, 1) ?></span>
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end navbar-dropdown-menu navbar-dropdown-menu-borderless navbar-dropdown-account" aria-labelledby="accountNavbarDropdown" style="width: 16rem;">
                            <div class="dropdown-item-text">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-soft-primary avatar-sm avatar-circle">
                                        <span class="avatar-initials"><?php echo substr($first_name, 0, 1) ?></span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="mb-0"><?php echo $first_name . ' ' . $last_name ?></h5>
                                        <p class="card-text text-body"><?php echo $username ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="settings.php">Settings</a>

                            <div class="dropdown-divider"></div>

                            <a class="dropdown-item" href="controller/Signout.php">Sign out</a>
                        </div>
                    </div>
                    <!-- End Account -->
                </li>
            </ul>
            <!-- End Navbar -->
        </div>
    </div>
</header>

<!-- time -->
<script>
    function displayTime() {
        const now = new Date();
        const options = {
            weekday: 'short',
            month: 'short',
            day: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZoneName: 'short'
        };

        const formattedDate = now.toLocaleString('en-US', options);
        document.getElementById('time').innerText = formattedDate;
    }

    // Call the function to display time immediately
    displayTime();

    // Update time every second
    setInterval(displayTime, 1000);
</script>
<!-- end time -->