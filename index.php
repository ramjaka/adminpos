<?php
require_once 'controller/Session.php';
$page = 'index';
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

    <?php include_once('components/header.php') ?>

    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT ========== -->

    <!-- Navbar Aside -->
    <?php include_once('components/aside.php') ?>
    <!-- End Navbar Aside -->

    <main id="content" role="main" class="main">
        <!-- Content -->
        <div class="content container-fluid">
            <div class="alert alert-soft-danger d-none" role="alert" id="dynamicAlertError">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="bi bi-exclamation-octagon"></i>
                    </div>
                    <div class="flex-grow-1 ms-2" id="alertMessageError">
                    </div>
                </div>
            </div>
            <div class="row justify-content-sm-center text-center py-10">
                <div class="col-sm-7 col-md-5">
                    <img class="img-fluid mb-5" src="./assets/svg/illustrations/oc-collaboration.svg" alt="Image Description" data-hs-theme-appearance="default">
                    <img class="img-fluid mb-5" src="./assets/svg/illustrations-light/oc-collaboration.svg" alt="Image Description" data-hs-theme-appearance="dark">

                    <h1>Hello <?php echo $first_name ?>, nice to see you!</h1>
                    <p id="quoteDisplay"></p>

                    <a class="btn btn-primary" href="dashboard.php">Let's start</a>
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

    <script src="./assets/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="./assets/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="./assets/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="./assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="./assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="./assets/vendor/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="./assets/vendor/clipboard/dist/clipboard.min.js"></script>
    <script src="./assets/vendor/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="./assets/vendor/datatables.net.extensions/select/select.min.js"></script>

    <!-- JS Front -->
    <script src="./assets/js/theme.min.js"></script>
    <script src="./assets/js/hs.theme-appearance-charts.js"></script>

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

    <script>
        // Array of quotes
        const quotes = [
            "Start where you are. Use what you have. Do what you can. - Arthur Ashe",
            "Your work is going to fill a large part of your life, and the only way to be truly satisfied is to do what you believe is great work. - Steve Jobs",
            "Believe you can and you're halfway there. - Theodore Roosevelt",
            "Don't watch the clock; do what it does. Keep going. - Sam Levenson",
            "Success is not final, failure is not fatal: It is the courage to continue that counts. - Winston S. Churchill",
            "The only way to do great work is to love what you do. - Steve Jobs",
            "It always seems impossible until it's done. - Nelson Mandela",
            "The only limit to our realization of tomorrow will be our doubts of today. - Franklin D. Roosevelt",
            "The future belongs to those who believe in the beauty of their dreams. - Eleanor Roosevelt",
            "Success usually comes to those who are too busy to be looking for it. - Henry David Thoreau",
            "Do not wait to strike till the iron is hot, but make it hot by striking. - William Butler Yeats",
            "The only place where success comes before work is in the dictionary. - Vidal Sassoon",
            "Your talent determines what you can do. Your motivation determines how much you are willing to do. Your attitude determines how well you do it. - Lou Holtz",
            "The only thing standing between you and your goal is the story you keep telling yourself as to why you can't achieve it. - Jordan Belfort",
            "Don't be afraid to give up the good to go for the great. - John D. Rockefeller",
            "What lies behind us and what lies before us are tiny matters compared to what lies within us. - Ralph Waldo Emerson",
            "Success is stumbling from failure to failure with no loss of enthusiasm. - Winston S. Churchill",
            "I find that the harder I work, the more luck I seem to have. - Thomas Jefferson",
            "Opportunities don't happen. You create them. - Chris Grosser",
            "Don't be pushed around by the fears in your mind. Be led by the dreams in your heart. - Roy T. Bennett"
        ];

        // Function to get a random quote
        function getRandomQuote() {
            const randomIndex = Math.floor(Math.random() * quotes.length);
            return quotes[randomIndex];
        }

        // Display a random quote on page load
        document.getElementById('quoteDisplay').innerText = getRandomQuote();
    </script>

    <!-- JS Plugins Init. -->
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF DATERANGEPICKER
            // =======================================================
            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
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
            }, cb);

            cb(start, end);
        });


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
            },
            language: {
                zeroRecords: `<div class="text-center p-4">
              <img class="mb-3" src="./assets/svg/illustrations/oc-error.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="default">
              <img class="mb-3" src="./assets/svg/illustrations-light/oc-error.svg" alt="Image Description" style="width: 10rem;" data-hs-theme-appearance="dark">
            <p class="mb-0">No data to show</p>
            </div>`
            }
        });

        const datatable = HSCore.components.HSDatatables.getItem(0)

        document.querySelectorAll('.js-datatable-filter').forEach(function(item) {
            item.addEventListener('change', function(e) {
                const elVal = e.target.value,
                    targetColumnIndex = e.target.getAttribute('data-target-column-index'),
                    targetTable = e.target.getAttribute('data-target-table');

                HSCore.components.HSDatatables.getItem(targetTable).column(targetColumnIndex).search(elVal !== 'null' ? elVal : '').draw()
            })
        })
    </script>

    <!-- JS Plugins Init. -->
    <script>
        (function() {
            localStorage.removeItem('hs_theme')

            window.onload = function() {


                // INITIALIZATION OF NAVBAR VERTICAL ASIDE
                // =======================================================
                new HSSideNav('.js-navbar-vertical-aside').init()


                // INITIALIZATION OF FORM SEARCH
                // =======================================================
                const HSFormSearchInstance = new HSFormSearch('.js-form-search')

                if (HSFormSearchInstance.collection.length) {
                    HSFormSearchInstance.getItem(1).on('close', function(el) {
                        el.classList.remove('top-0')
                    })

                    document.querySelector('.js-form-search-mobile-toggle').addEventListener('click', e => {
                        let dataOptions = JSON.parse(e.currentTarget.getAttribute('data-hs-form-search-options')),
                            $menu = document.querySelector(dataOptions.dropMenuElement)

                        $menu.classList.add('top-0')
                        $menu.style.left = 0
                    })
                }


                // INITIALIZATION OF BOOTSTRAP DROPDOWN
                // =======================================================
                HSBsDropdown.init()


                // INITIALIZATION OF CHARTJS
                // =======================================================
                HSCore.components.HSChartJS.init('.js-chart')


                // INITIALIZATION OF CHARTJS
                // =======================================================
                HSCore.components.HSChartJS.init('#updatingBarChart')
                const updatingBarChart = HSCore.components.HSChartJS.getItem('updatingBarChart')

                // Call when tab is clicked
                document.querySelectorAll('[data-bs-toggle="chart-bar"]').forEach(item => {
                    item.addEventListener('click', e => {
                        let keyDataset = e.currentTarget.getAttribute('data-datasets')

                        const styles = HSCore.components.HSChartJS.getTheme('updatingBarChart', HSThemeAppearance.getAppearance())

                        if (keyDataset === 'lastWeek') {
                            updatingBarChart.data.labels = ["Apr 22", "Apr 23", "Apr 24", "Apr 25", "Apr 26", "Apr 27", "Apr 28", "Apr 29", "Apr 30", "Apr 31"];
                            updatingBarChart.data.datasets = [{
                                    "data": [120, 250, 300, 200, 300, 290, 350, 100, 125, 320],
                                    "backgroundColor": styles.data.datasets[0].backgroundColor,
                                    "hoverBackgroundColor": styles.data.datasets[0].hoverBackgroundColor,
                                    "borderColor": styles.data.datasets[0].borderColor,
                                    "maxBarThickness": 10
                                },
                                {
                                    "data": [250, 130, 322, 144, 129, 300, 260, 120, 260, 245, 110],
                                    "backgroundColor": styles.data.datasets[1].backgroundColor,
                                    "borderColor": styles.data.datasets[1].borderColor,
                                    "maxBarThickness": 10
                                }
                            ];
                            updatingBarChart.update();
                        } else {
                            updatingBarChart.data.labels = ["May 1", "May 2", "May 3", "May 4", "May 5", "May 6", "May 7", "May 8", "May 9", "May 10"];
                            updatingBarChart.data.datasets = [{
                                    "data": [200, 300, 290, 350, 150, 350, 300, 100, 125, 220],
                                    "backgroundColor": styles.data.datasets[0].backgroundColor,
                                    "hoverBackgroundColor": styles.data.datasets[0].hoverBackgroundColor,
                                    "borderColor": styles.data.datasets[0].borderColor,
                                    "maxBarThickness": 10
                                },
                                {
                                    "data": [150, 230, 382, 204, 169, 290, 300, 100, 300, 225, 120],
                                    "backgroundColor": styles.data.datasets[1].backgroundColor,
                                    "borderColor": styles.data.datasets[1].borderColor,
                                    "maxBarThickness": 10
                                }
                            ]
                            updatingBarChart.update();
                        }
                    })
                })


                // INITIALIZATION OF CHARTJS
                // =======================================================
                HSCore.components.HSChartJS.init('.js-chart-datalabels', {
                    plugins: [ChartDataLabels],
                    options: {
                        plugins: {
                            datalabels: {
                                anchor: function(context) {
                                    var value = context.dataset.data[context.dataIndex];
                                    return value.r < 20 ? 'end' : 'center';
                                },
                                align: function(context) {
                                    var value = context.dataset.data[context.dataIndex];
                                    return value.r < 20 ? 'end' : 'center';
                                },
                                color: function(context) {
                                    var value = context.dataset.data[context.dataIndex];
                                    return value.r < 20 ? context.dataset.backgroundColor : context.dataset.color;
                                },
                                font: function(context) {
                                    var value = context.dataset.data[context.dataIndex],
                                        fontSize = 25;

                                    if (value.r > 50) {
                                        fontSize = 35;
                                    }

                                    if (value.r > 70) {
                                        fontSize = 55;
                                    }

                                    return {
                                        weight: 'lighter',
                                        size: fontSize
                                    };
                                },
                                formatter: function(value) {
                                    return value.r
                                },
                                offset: 2,
                                padding: 0
                            }
                        },
                    }
                })

                // INITIALIZATION OF SELECT
                // =======================================================
                HSCore.components.HSTomSelect.init('.js-select')


                // INITIALIZATION OF CLIPBOARD
                // =======================================================
                HSCore.components.HSClipboard.init('.js-clipboard')
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