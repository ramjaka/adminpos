<!-- Required Meta Tags Always Come First -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Title -->
<title>
    <?php
    if ($page === 'index') {
        echo 'Welcome ' . "$first_name $last_name";
    } elseif ($page === 'dashboard') {
        echo 'Dashboard';
    } elseif ($page === 'users-overview') {
        echo 'User Overview';
    } elseif ($page === 'user-add-user') {
        echo 'Creatify - Add user';
    } elseif ($page === 'users-presences') {
        echo 'User presences';
    } elseif ($page === 'success-add-user') {
        echo 'User Successfully Added!';
    } elseif ($page === 'profile-account') {
        echo 'Profile account';
    } elseif ($page === 'settings') {
        echo 'Settings';
    } else {
        echo 'Creatify | POS & WMS';
    }
    ?>
</title>

<!-- Favicon -->
<link rel="shortcut icon" href="./favicon.ico">

<!-- Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<!-- CSS Implementing Plugins -->
<link rel="stylesheet" href="./assets/vendor/bootstrap-icons/font/bootstrap-icons.css">

<link rel="stylesheet" href="./assets/vendor/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="./assets/vendor/tom-select/dist/css/tom-select.bootstrap5.css">

<link rel="stylesheet" href="./assets/vendor/flatpickr/dist/flatpickr.min.css">

<!-- CSS Front Template -->
<link rel="preload" href="./assets/css/theme.min.css" data-hs-appearance="default" as="style">
<link rel="preload" href="./assets/css/theme-dark.min.css" data-hs-appearance="dark" as="style">

<!-- CSS Implementing Plugins -->
<link rel="stylesheet" href="./assets/vendor/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="./assets/vendor/tom-select/dist/css/tom-select.bootstrap5.css">
<link rel="stylesheet" href="./assets/vendor/quill/dist/quill.snow.css">

<!-- fontawesome -->
<link rel="stylesheet" data-purpose="Layout StyleSheet" title="Web Awesome" href="/css/app-wa-462d1fe84b879d730fe2180b0e0354e0.css?vsn=d">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/sharp-thin.css">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/sharp-solid.css">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/sharp-regular.css">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/sharp-light.css">

<!-- date range picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<style data-hs-appearance-onload-styles>
    * {
        transition: unset !important;
    }

    body {
        opacity: 0;
    }
</style>

<style>
    /* width */
    ::-webkit-scrollbar {
        width: 0px;
    }

    /* Hide the element on small screens */
    @media (max-width: 767.98px) {
        #time {
            display: none;
        }
    }
</style>

<script>
    window.hs_config = {
        "autopath": "@@autopath",
        "deleteLine": "hs-builder:delete",
        "deleteLine:build": "hs-builder:build-delete",
        "deleteLine:dist": "hs-builder:dist-delete",
        "previewMode": false,
        "startPath": "/index.html",
        "vars": {
            "themeFont": "https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap",
            "version": "?v=1.0"
        },
        "layoutBuilder": {
            "extend": {
                "switcherSupport": true
            },
            "header": {
                "layoutMode": "default",
                "containerMode": "container-fluid"
            },
            "sidebarLayout": "default"
        },
        "themeAppearance": {
            "layoutSkin": "default",
            "sidebarSkin": "default",
            "styles": {
                "colors": {
                    "primary": "#377dff",
                    "transparent": "transparent",
                    "white": "#fff",
                    "dark": "132144",
                    "gray": {
                        "100": "#f9fafc",
                        "900": "#1e2022"
                    }
                },
                "font": "Inter"
            }
        },
        "languageDirection": {
            "lang": "en"
        },
        "skipFilesFromBundle": {
            "dist": ["assets/js/hs.theme-appearance.js", "assets/js/hs.theme-appearance-charts.js", "assets/js/demo.js"],
            "build": ["assets/css/theme.css", "assets/vendor/hs-navbar-vertical-aside/dist/hs-navbar-vertical-aside-mini-cache.js", "assets/js/demo.js", "assets/css/theme-dark.css", "assets/css/docs.css", "assets/vendor/icon-set/style.css", "assets/js/hs.theme-appearance.js", "assets/js/hs.theme-appearance-charts.js", "node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js", "assets/js/demo.js"]
        },
        "minifyCSSFiles": ["assets/css/theme.css", "assets/css/theme-dark.css"],
        "copyDependencies": {
            "dist": {
                "*assets/js/theme-custom.js": ""
            },
            "build": {
                "*assets/js/theme-custom.js": "",
                "node_modules/bootstrap-icons/font/*fonts/**": "assets/css"
            }
        },
        "buildFolder": "",
        "replacePathsToCDN": {},
        "directoryNames": {
            "src": "./src",
            "dist": "./dist",
            "build": "./build"
        },
        "fileNames": {
            "dist": {
                "js": "theme.min.js",
                "css": "theme.min.css"
            },
            "build": {
                "css": "theme.min.css",
                "js": "theme.min.js",
                "vendorCSS": "vendor.min.css",
                "vendorJS": "vendor.min.js"
            }
        },
        "fileTypes": "jpg|png|svg|mp4|webm|ogv|json"
    }
    window.hs_config.gulpRGBA = (p1) => {
        const options = p1.split(',')
        const hex = options[0].toString()
        const transparent = options[1].toString()

        var c;
        if (/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)) {
            c = hex.substring(1).split('');
            if (c.length == 3) {
                c = [c[0], c[0], c[1], c[1], c[2], c[2]];
            }
            c = '0x' + c.join('');
            return 'rgba(' + [(c >> 16) & 255, (c >> 8) & 255, c & 255].join(',') + ',' + transparent + ')';
        }
        throw new Error('Bad Hex');
    }
    window.hs_config.gulpDarken = (p1) => {
        const options = p1.split(',')

        let col = options[0].toString()
        let amt = -parseInt(options[1])
        var usePound = false

        if (col[0] == "#") {
            col = col.slice(1)
            usePound = true
        }
        var num = parseInt(col, 16)
        var r = (num >> 16) + amt
        if (r > 255) {
            r = 255
        } else if (r < 0) {
            r = 0
        }
        var b = ((num >> 8) & 0x00FF) + amt
        if (b > 255) {
            b = 255
        } else if (b < 0) {
            b = 0
        }
        var g = (num & 0x0000FF) + amt
        if (g > 255) {
            g = 255
        } else if (g < 0) {
            g = 0
        }
        return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16)
    }
    window.hs_config.gulpLighten = (p1) => {
        const options = p1.split(',')

        let col = options[0].toString()
        let amt = parseInt(options[1])
        var usePound = false

        if (col[0] == "#") {
            col = col.slice(1)
            usePound = true
        }
        var num = parseInt(col, 16)
        var r = (num >> 16) + amt
        if (r > 255) {
            r = 255
        } else if (r < 0) {
            r = 0
        }
        var b = ((num >> 8) & 0x00FF) + amt
        if (b > 255) {
            b = 255
        } else if (b < 0) {
            b = 0
        }
        var g = (num & 0x0000FF) + amt
        if (g > 255) {
            g = 255
        } else if (g < 0) {
            g = 0
        }
        return (usePound ? "#" : "") + (g | (b << 8) | (r << 16)).toString(16)
    }
</script>

<!-- date range pciker -->
<script>
    (function() {
        // INITIALIZATION OF FLATPICKR
        // =======================================================
        HSCore.components.HSDaterangepicker.init('.js-daterangepicker')
    })();
</script>

<script>
    $(document).on('ready', function() {
        var start = moment().subtract(29, 'days');
        var end = moment();

        function cb(start, end) {
            $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        HSCore.components.HSDaterangepicker.init('#js-daterangepicker-predefined', {
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb)

        cb(start, end)

        $('#js-daterangepicker-predefined').on('apply.daterangepicker', function(ev, picker) {
            $(this).find('.js-daterangepicker-predefined-preview').html(
                picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY')
            );
        });
    })
</script>

<!-- chart js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- midtrans -->
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-I5z8XScARk43D0Ke"></script>