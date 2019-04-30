<?php
/**
 * Admin Home page
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");

$qry    = "SELECT
            count(distinct tbl_warehouse.dist_id) as disp_val
            FROM
            tbl_wh_data
            INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
            WHERE
            tbl_wh_data.RptDate = '2018-04-01'
";
$q_res  = mysql_query($qry);
$row = mysql_fetch_assoc($q_res);
$reported_districts = $row['disp_val'];
        
$qry    = "SELECT
                count(distinct stock_batch.batch_no) disp_val
                FROM
                stock_batch

";
$q_res  = mysql_query($qry);
$row = mysql_fetch_assoc($q_res);
$batches_handled = $row['disp_val'];

$qry    = "SELECT
            Count(tbl_stock_master.PkStockID) disp_val
            FROM
            tbl_stock_master
";
$q_res  = mysql_query($qry);
$row = mysql_fetch_assoc($q_res);
$txn_done = $row['disp_val'];

$qry    = "SELECT
                count(tbl_stock_master.TranNo)  disp_val
               FROM
               tbl_stock_master
               WHERE
               tbl_stock_master.TranTypeID = 2
";
$q_res  = mysql_query($qry);
$row = mysql_fetch_assoc($q_res);
$vouchers_issued = $row['disp_val'];


?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <h3 class="heading">
                    <?php
                    echo "<h1>Welcome: " . $_SESSION['user_name'] . " </h1>";
                    ?>
                </h3>
                <div class="widget">
                    <div class="widget-head">

                    </div>
                    <div class="widget-body">

                        
                        <div class="row">
                        <div class="col-md-3 center">
                            <div class="dashboard-stat red-pink" style="height:100px;background-color: #81ce4b;">
                                <div class="visual"><i class="fa fa-bar-chart-o"></i></div>
                                <div class="details">
                                    <div class="number" id="general_av_dist"><?=$reported_districts?></div>
                                    <div class="desc" style="font-size:13px;">Actively Reporting Districts<br/>All Pakistan</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 center">
                            <div class="dashboard-stat red-pink" style="height:100px;background-color: #81ce4b;">
                                <div class="visual"><i class="fa fa-bar-chart-o"></i></div>
                                <div class="details">
                                    <div class="number" id="general_av_dist"><?=$batches_handled?></div>
                                    <div class="desc" style="font-size:13px;">Total Batches Handled</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 center">
                            <div class="dashboard-stat red-pink" style="height:100px;background-color: #81ce4b;">
                                <div class="visual"><i class="fa fa-bar-chart-o"></i></div>
                                <div class="details">
                                    <div class="number" id="general_av_dist"><?=$txn_done?></div>
                                    <div class="desc" style="font-size:13px;">Total Transactions Done</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 center">
                            <div class="dashboard-stat red-pink" style="height:100px;background-color: #81ce4b;">
                                <div class="visual"><i class="fa fa-bar-chart-o"></i></div>
                                <div class="details">
                                    <div class="number" id="general_av_dist"><?=$vouchers_issued?></div>
                                    <div class="desc" style="font-size:13px;">Total Vouchers Issued</div>
                                </div>
                            </div>
                        </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 my_dash_cols">
                                <div class="col-md-12 my_dashlets">
                                    <div class="dashlet_graph" id="dashboard_stock_a1" href='landing_p_1.php'></div>
                                </div>
                            </div>
                            <div class="col-md-3 my_dash_cols">
                                <div class="col-md-12 my_dashlets"  >
                                    <div class="dashlet_graphx" id="dashboard_stock_a2" href='landing_p_1.php'></div>
                                </div>
                            </div>
                            <div class="col-md-3 my_dash_cols">
                                <div class="col-md-12 my_dashlets">
                                    <div class="dashlet_graphx" id="dashboard_stock_a3" href='landing_p_1.php'></div>
                                </div>
                            </div>
                            <div class="col-md-3 my_dash_cols">
                                <div class="col-md-12 my_dashlets">
                                    <div class="dashlet_graphx" id="dashboard_stock_a4" href='landing_p_1.php'></div>
                                </div>
                            </div>
                        </div>





                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <script>
    
    
        $(function () {
            loadDashlets();
        });
        function loadDashlets()
        {
            $('.dashlet_graph').each(function (i, obj) {

                var url = $(this).attr('href');
                var id = $(this).attr('id');
                var form_data = $('form').serialize();

                //dataStr = 'month=' + $('#month').val();
                //dataStr += '&year=' + $('#year').val();
                //dataStr += '&lvl=' + $('#ofc_level').val(); 
                console.log('id is ' + id);
                $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

                $.ajax({
                    type: "POST",
                    url: '<?php echo APP_URL; ?>dashboard/' + url,
                    data: form_data,
                    dataType: 'html',
                    success: function (data) {
                        $("#" + id).html(data);
                    }
                });

            });
        }    
    </script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
</body>
</html>