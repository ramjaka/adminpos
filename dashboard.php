<?php
require_once 'controller/Session.php';
$page = 'dashboard';
$access_value = '1';

// authorization
$get_access = "SELECT access FROM `user` WHERE `user_id` = '$user_id'";
$result = $mysqli->query($get_access);
$access = array();
if ($result) {
  $row = mysqli_fetch_assoc($result);
  $access_values = explode(',', $row['access']);
  if (!in_array($access_value, $access_values)) {
    $response = "Sorry you don't have access to that page";
    header("Location: index.php?errorMessage=" . urlencode($response));
    exit;
  }
}

// graph year
if (isset($_GET['year']) && !empty($_GET['year'])) {
  $year = $_GET['year'];
} else {
  $year = date("Y");
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

  <!-- End Navbar Vertical -->

  <main id="content" role="main" class="main">
    <!-- Content -->
    <div class="content container-fluid">
      <!-- Page Header -->
      <div class="page-header">
        <div class="row align-items-center">
          <div class="col">
            <h1 class="page-header-title">Dashboard</h1>
          </div>
          <!-- End Col -->

          <!-- <div class="col-auto">
            <a href="dashboardReports.php" class="btn btn-primary" target="_blank">
              <i class="bi bi-journals me-1"></i> Reports
            </a>
          </div> -->
          <!-- End Col -->
        </div>
        <!-- End Row -->
      </div>
      <!-- End Page Header -->

      <!-- Stats -->

      <!-- End Stats -->

      <!-- Card -->
      <div class="card mb-3 mb-lg-5">
        <!-- Header -->
        <div class="card-header card-header-content-between">
          <div>
            <h6 class="card-subtitle mb-0">This year graph</h6>

            <div class="dropdown">
              <span class="badge bg-soft-info text-info">
                <span class="legend-indicator bg-info"></span>Income
              </span>
              <span class="badge bg-soft-danger text-danger">
                <span class="legend-indicator bg-danger"></span>Expense
              </span>
            </div>
          </div>

          <!-- Dropdown -->
          <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
              Year
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="max-height: 200px; overflow-y: auto;">
              <a class="dropdown-item" href="posSalesList.php?year=2024">2024</a>
              <a class="dropdown-item" href="posSalesList.php?year=2025">2025</a>
              <a class="dropdown-item" href="posSalesList.php?year=2026">2026</a>
              <a class="dropdown-item" href="posSalesList.php?year=2027">2027</a>
              <a class="dropdown-item" href="posSalesList.php?year=2028">2028</a>
              <a class="dropdown-item" href="posSalesList.php?year=2029">2029</a>
              <a class="dropdown-item" href="posSalesList.php?year=2030">2030</a>
              <a class="dropdown-item" href="posSalesList.php?year=2031">2031</a>
              <a class="dropdown-item" href="posSalesList.php?year=2032">2032</a>
              <a class="dropdown-item" href="posSalesList.php?year=2033">2033</a>
              <a class="dropdown-item" href="posSalesList.php?year=2034">2034</a>
              <a class="dropdown-item" href="posSalesList.php?year=2035">2035</a>
              <a class="dropdown-item" href="posSalesList.php?year=2036">2036</a>
              <a class="dropdown-item" href="posSalesList.php?year=2037">2037</a>
              <a class="dropdown-item" href="posSalesList.php?year=2038">2038</a>
              <a class="dropdown-item" href="posSalesList.php?year=2039">2039</a>
              <a class="dropdown-item" href="posSalesList.php?year=2040">2040</a>
            </div>
          </div>
          <!-- End Dropdown -->
        </div>
        <!-- End Header -->

        <div class="card-body">
          <!-- Bar Chart -->
          <div class="chartjs-custom" style="height: 18rem;">
            <?php
            $sql = "SELECT 
                                months.month AS bulan,
                                COALESCE(SUM(mutation.debt), 0) AS total_debt,
                                COALESCE(SUM(mutation.credit), 0) AS total_credit
                            FROM
                                (SELECT 1 AS month
                                UNION SELECT 2
                                UNION SELECT 3
                                UNION SELECT 4
                                UNION SELECT 5
                                UNION SELECT 6
                                UNION SELECT 7
                                UNION SELECT 8
                                UNION SELECT 9
                                UNION SELECT 10
                                UNION SELECT 11
                                UNION SELECT 12) AS months
                            LEFT JOIN
                                mutation ON MONTH(mutation.mutation_created_at) = months.month AND YEAR(mutation.mutation_created_at) = $year
                            GROUP BY
                                months.month";

            $result = $mysqli->query($sql);

            if ($result->num_rows > 0) {
              $total_debt_array = [];
              $total_credit_array = [];
              while ($row = $result->fetch_assoc()) {
                $total_debt_array[] = $row['total_debt'];
                $total_credit_array[] = $row['total_credit'];
              }
            } else {
              // Jika tidak ada hasil dari query, inisialisasi array dengan nilai 0
              $total_debt_array = array_fill(0, 12, 0);
              $total_credit_array = array_fill(0, 12, 0);
            }
            ?>

            <canvas id="project" class="js-chart" data-hs-chartjs-options='{
                        "type": "line",
                        "data": {
                            "labels": ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                            "datasets": [{
                                "data": [<?php echo implode(",", $total_debt_array); ?>],
                                "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                                "borderColor": "#00c9db",
                                "borderWidth": 2,
                                "pointRadius": 0,
                                "pointBorderColor": "#fff",
                                "pointBackgroundColor": "#00c9db",
                                "pointHoverRadius": 0,
                                "hoverBorderColor": "#fff",
                                "hoverBackgroundColor": "#00c9db",
                                "tension": 0.4
                            },
                            {
                                "data": [<?php echo implode(",", $total_credit_array); ?>],
                                "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                                "borderColor": "#ED4C78",
                                "borderWidth": 2,
                                "pointRadius": 0,
                                "pointBorderColor": "#fff",
                                "pointBackgroundColor": "#ED4C78",
                                "pointHoverRadius": 0,
                                "hoverBorderColor": "#fff",
                                "hoverBackgroundColor": "#ED4C78",
                                "tension": 0.4
                            }]
                        },
                        "options": {
                            "gradientPosition": {"y1": 200},
                            "scales": {
                                "y": {
                                    "grid": {
                                        "color": "#e7eaf3",
                                        "drawBorder": false,
                                        "zeroLineColor": "#e7eaf3"
                                    },
                                    "ticks": {
                                        "min": 0,
                                        "max": 100,
                                        "stepSize": 20,
                                        "color": "#97a4af",                                
                                        "font": {
                                            "family": "Open Sans, sans-serif"
                                        },
                                        "padding": 10,
                                        "postfix": "k"
                                    }
                                },
                                "x": {
                                    "grid": {
                                        "display": false,
                                        "drawBorder": false
                                    },
                                    "ticks": {
                                        "color": "#97a4af",
                                        "font": {
                                            "family": "Open Sans, sans-serif"
                                        },
                                        "padding": 5
                                    }
                                }
                            },
                            "plugins": {
                                "tooltip": {
                                    "prefix": "Rp",
                                    "postfix": "",
                                    "hasIndicator": true,
                                    "mode": "index",
                                    "intersect": false,
                                    "lineMode": true,
                                    "lineWithLineColor": "rgba(19, 33, 68, 0.075)"
                                }
                            },
                            "hover": {
                                "mode": "nearest",
                                "intersect": true
                            }
                        }
                    }'></canvas>

          </div>
          <!-- End Bar Chart -->
        </div>
      </div>
      <!-- End Card -->

      <div class="row">

        <div class="col-md-6 mb-3 mb-lg-5">
          <!-- Card -->
          <div class="card">
            <!-- Header -->
            <div class="card-header card-header-content-between">
              <h4 class="card-header-title" style="margin-top: 12px; margin-bottom: 12px;">Threshold Stock</h4>
            </div>
            <!-- End Header -->

            <!-- Body -->
            <div class="card-body">
              <!-- Pie Chart -->
              <div class="chartjs-custom mb-3 mb-sm-5" style="height: 14rem;">
                <canvas id="updatingDoughnutChart" data-hs-chartjs-options='{
                                    "type": "doughnut",
                                    "data": {
                                        "labels": ["QTY", "QTY", "QTY"],
                                        "datasets": [{
                                        "backgroundColor": ["#ED4C78", "#F5CA99", "#377dff"],
                                        "borderWidth": 5,
                                        "hoverBorderColor": "#fff"
                                        }]
                                    },
                                    "options": {
                                        "cutoutPercentage": 80,
                                        "plugins": {
                                        "tooltip": {
                                            "postfix": " pcs",
                                            "hasIndicator": true,
                                            "mode": "index",
                                            "intersect": false
                                        }
                                        },
                                        "hover": {
                                        "mode": "nearest",
                                        "intersect": true
                                        }
                                    }
                                    }'>
                </canvas>
              </div>
              <!-- End Pie Chart -->

              <div class="row justify-content-center">
                <div class="col-auto mb-3 mb-sm-0">
                  <h4 class="card-title my-4"></h4>
                  <span class="legend-indicator bg-danger"></span>
                  < 10 </div>
                    <!-- End Col -->

                    <div class="col-auto mb-3 mb-sm-0">
                      <h4 class="card-title my-4"></h4>
                      <span class="legend-indicator bg-warning"></span>
                      < 50</div>
                        <!-- End Col -->

                        <div class="col-auto">
                          <h4 class="card-title my-4"></h4>
                          <span class="legend-indicator bg-primary"></span>
                          < 100 </div>
                            <!-- End Col -->
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
              </div>
              <!-- End Col -->

              <div class="col-md-6 mb-3 mb-lg-5">
                <!-- Card -->
                <div class="card h-100">
                  <!-- Header -->
                  <div class="card-header card-header-content-between">
                    <h4 class="card-header-title" style="margin-top: 12px; margin-bottom: 12px;">Products Threshold Stock</h4>
                  </div>
                  <!-- End Header -->

                  <!-- Body -->
                  <div class="card-body card-body-height">
                    <!-- List Group -->
                    <ul class="list-group list-group-flush list-group-start-bordered">
                      <?php
                      $sql = "SELECT s.*, p.product_media_1
                                        FROM stock s
                                        INNER JOIN product p ON s.product_sku = p.product_sku
                                        WHERE s.stock_created_at = (
                                            SELECT MAX(stock_created_at)
                                            FROM stock
                                            WHERE product_sku = s.product_sku
                                        )
                                        AND s.stock_qty < 5
                                        ORDER BY s.stock_created_at DESC";

                      $result = $mysqli->query($sql);

                      if ($result->num_rows > 0) { ?>
                        <?php while ($row = $result->fetch_assoc()) {
                        ?>
                          <!-- Item -->
                          <li class="list-group-item">
                            <div class="list-group-item-action">
                              <div class="row">
                                <div class="col-sm mb-2 mb-sm-0">
                                  SKU : <?php echo $row['product_sku'] ?>
                                  <h2 class="fw-normal my-1"><?php echo $row['product_name'] ?></h2>
                                  <h5 class="text-inherit mb-0">
                                    Category : <?php echo $row['product_category'] ?>
                                  </h5>
                                  <span class="text-body small"><?php echo $row['stock_qty'] ?> pcs</span>
                                </div>

                                <div class="col-sm-auto">
                                  <img src="assets/img/product-img/<?php echo $row['product_media_1'] ?>" class="rounded-2" width="60px" alt="Image Description">
                                </div>
                              </div>
                              <!-- End Row -->
                            </div>
                          </li>
                          <!-- End Item -->
                        <?php } ?>
                      <?php } ?>
                    </ul>
                    <!-- End List Group -->
                  </div>
                  <!-- End Body -->
                </div>
                <!-- End Card -->
              </div>
              <!-- End Col -->
            </div>
          </div>
          <!-- End Row -->
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

    HSCore.components.HSChartJS.init('#updatingDoughnutChart')
    const updatingDoughnutChart = HSCore.components.HSChartJS.getItem('updatingDoughnutChart')

    // Datasets for chart, can be loaded from AJAX request
    const updatingDoughnutChartDatasets = [
      [
        [45, 25, 30]
      ]
    ]

    // Set datasets for chart when page is loaded
    const setDataChart = function() {
      updatingDoughnutChart.data.datasets.forEach(function(dataset, key) {
        dataset.data = updatingDoughnutChartDatasets[0][key];
      })

      updatingDoughnutChart.update()
    }

    setDataChart()

    window.addEventListener('on-hs-appearance-change', e => {
      setDataChart()
    })

    // Call when tab is clicked
    document.querySelectorAll('[data-bs-toggle="chart-doughnut"]').forEach(item => {
      item.addEventListener('click', e => {
        let keyDataset = e.currentTarget.getAttribute('data-datasets')

        // Update datasets for chart
        updatingDoughnutChart.data.datasets.forEach(function(dataset, key) {
          dataset.data = updatingDoughnutChartDatasets[keyDataset][key]
        })
        updatingDoughnutChart.update()
      })
    })


    // INITIALIZATION OF FLATPICKR
    // =======================================================
    HSCore.components.HSFlatpickr.init('.js-flatpickr')
  </script>

  <!-- End Style Switcher JS -->
</body>

</html>