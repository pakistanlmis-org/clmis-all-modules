<?php
//Including Configuration file
include("../includes/classes/Configuration.inc.php");
//Login
Login();

//echo '<pre>';print_r($_SESSION);exit;
//Including db file
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
//Including FusionCharts file
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");


$todays_date = date('d');
if ($todays_date >= 10)
    $open_month = date('m') - 2;
else
    $open_month = date('m') - 3;

$a_date = date('Y') . '-' . ($open_month + 1) . '-1';
$open_month_d = date("t", strtotime($a_date));
 
//Getting user_level
$level = $_SESSION['user_level'];
//Getting user_province1
$province = $_SESSION['user_province1'];
//Getting user_district
$district = $_SESSION['user_district'];
$stakeholder = $_SESSION['user_stakeholder1'];

$selProv = (!empty($_REQUEST['province'])) ? $_REQUEST['province'] : $province;
$fromDate = (!empty($_REQUEST['from_date'])) ? $_REQUEST['from_date'] : date("Y-m") . "-01";
$selDist = (!empty($_REQUEST['dist'])) ? $_REQUEST['dist'] : $district;
//$toDate = $fromDate;
//echo '<pre>';print_r($_SESSION);exit;

$wh_comm = '';
if (!empty($stakeholder))
    $wh_comm .= " and stakeholder_id  = '" . $stakeholder . "'";
if (!empty($selProv))
    $wh_comm .= " and location_id     = '" . $selProv . "'";
?>
<style>
    .my_dash_cols{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }
    .my_dashlets{
        /*padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;*/
    }
</style>
</head>
<?php
$queryprov = "SELECT
                                                                                                tbl_locations.PkLocID AS prov_id,
                                                                                                tbl_locations.LocName AS prov_title
                                                                                            FROM
                                                                                                tbl_locations
                                                                                            WHERE
                                                                                                LocLvl = 2
                                                                                                AND parentid IS NOT NULL
                                                                                                AND tbl_locations.LocType = 2";
//query result
$rsprov = mysql_query($queryprov) or die();
$prov_name = 'Punjab';
while ($rowprov = mysql_fetch_array($rsprov)) {
    if ($selProv == $rowprov['prov_id']) {
        $sel = "selected='selected'";
        $prov_name = $rowprov['prov_title'];
    } else {
        $sel = "";
    }
}
$queryDist = "SELECT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 3
                                                                        AND tbl_locations.parentid = '" . $selProv . "'
                                                                        ORDER BY
                                                                                tbl_locations.LocName ASC";
//query result
$rsDist = mysql_query($queryDist) or die();
//fetch result
$dist_name = "Attock";
while ($rowDist = mysql_fetch_array($rsDist)) {
    if ($district == $rowDist['PkLocID']) {
        $sel = "selected='selected'";
        $dist_name = $rowDist['LocName'];
    } else {
        $sel = "";
    }
}
$queryStk = "SELECT
stakeholder.stkid,
stakeholder.stkname
FROM
stakeholder
WHERE
stakeholder.stkid = $stakeholder ";
//query result
//print_r($stakeholder);exit;
$rsStk = mysql_query($queryStk) or die();
//fetch result
$stk_name = "PWD";
while ($rowStk = mysql_fetch_array($rsStk)) {
    if ($stakeholder == $rowStk['stkid']) {
        
        $stk_name = $rowStk['stkname'];
    } else {
        $sel = "";
    }
}
//echo '<pre>';print_r($stk_name);
?>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">

        <div class="page-content-wrapper1">
            <div class="page-content">

                <div class="container-fluid">

                    <div class="row">

                        <div class="col-md-12 my_dash_cols">

                            <div class="col-md-12 ">
                                <div class="dashlet_graph" id="dashboard_ss_c2" href='so_graph.php'></div>
                            </div>

                        </div>
                    </div>
                    <input type="hidden" name="dist_name" id="dist_name" value="<?php echo $dist_name ?>">
                    <input type="hidden" name="prov_name" id="prov_name" value="<?php echo $prov_name ?>">
<input type="hidden" name="stk_name" id="stk_name" value="<?php echo $stk_name ?>">

                </div>

            </div>
        </div>
    </div>
<?php
//Including footer file
//include PUBLIC_PATH . "/html/footer.php"; 
?>

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
<?php /* ?><script language="Javascript" src="<?php echo PUBLIC_URL;?>js/maps/cyp_dashlet.js"></script>
  <script language="Javascript" src="<?php echo PUBLIC_URL;?>js/maps/dashlet_Interval.js"></script><?php */ ?>
    <script type="text/javascript">

        $(function () {
            if (!$('#accordion').hasClass('page-sidebar-menu-closed'))
            {
                $(".sidebar-toggler").trigger("click");
            }
        });

        $(function () {
            loadDashlets();

            if (!$('#accordion').hasClass('page-sidebar-menu-closed'))
            {
                $(".sidebar-toggler").trigger("click");
            }

            $(document).on("click", ".pipeline_anchor", function () {
                $('#pipeline_modal').modal('show');

                var url = 'dashboard_ss_dist_pipeline.php';
                var id = 'pipeline_modal_graph';

                var dataStr = '';
                dataStr += 'province=' + $('#province').val();
                dataStr += '&prov_name=' + $('#prov_name').val();

                dataStr += '&from_date=' + $(this).data('date');
                dataStr += '&to_date=' + $(this).data('date');
                dataStr += '&itm_id=' + $(this).data('id');


                $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

                $.ajax({
                    type: "POST",
                    url: '<?php echo APP_URL; ?>dashboard/' + url,
                    data: dataStr,
                    dataType: 'html',
                    success: function (data) {
                        $("#" + id).html(data);
                    }
                });



            });

        });

        function showDrillDown_b1(prov, prov_name, from_date, stk, stk_name, dist_id, dist_name) {

            window.open("drilldown_ss_dist_b1_1.php?province=" + prov + "&prov_name=" + prov_name + "&from_date=" + from_date + "&stakeholder=" + stk + "&stk_name=" + stk_name + "&dist_id=" + dist_id + "&dist_name=" + dist_name, "", "width=800,height=600");
        }

        function showDrillDown_b2(prov, prov_name, from_date, prod_id, prod_name, stk_id, stk_name, dist_id, dist_name) {

            window.open("drilldown_ss_dist_b2_1.php?province=" + prov + "&prov_name=" + prov_name + "&from_date=" + from_date + "&prod_id=" + prod_id + "&prod_name=" + prod_name + "&stk_id=" + stk_id + "&stk_name=" + stk_name + "&dist_id=" + dist_id + "&dist_name=" + dist_name, "", "width=800,height=600");
        }

        function loadDashlets(stkId = '1')
        {
            $('.dashlet_graph').each(function (i, obj) {

                var url = $(this).attr('href');
                var id = $(this).attr('id');

                var dataStr = '';
                dataStr += 'province=' + $('#province').val();
                dataStr += '&prov_name=' + $('#prov_name').val();
                dataStr += '&from_date=' + $('#from_date').val();
                dataStr += '&to_date=' + $('#to_date').val();
                dataStr += '&dist=' + $('#dist').val();
                dataStr += '&dist_name=' + $('#dist_name').val();
                  
                //dataStr += '&lvl=' + $('#ofc_level').val();
                //dataStr += '&proFilter=' + $('#product_filter').val();
                //dataStr += '&stkId=' + stkId;
                //dataStr += '&sector=0';

                $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

                $.ajax({
                    type: "POST",
                    url: '<?php echo APP_URL; ?>popups/' + url,
                    data: dataStr,
                    dataType: 'html',
                    success: function (data) {
                        $("#" + id).html(data);
                    }
                });

            });


        }


        function showProvinces() {
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                data: {lvl: 2},
                success: function (data) {
                    $("#provinceArea").html(data);
                    showDistricts();
                }
            });
        }

        function showDistricts() {
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                data: {lvl: 3, prov_id: $('#prov_id').val()},
                success: function (data) {
                    $("#districtArea").html(data);
                }
            });
        }
        function showProvinces1(provId) {
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                data: {lvl: 2, provId: provId},
                success: function (data) {
                    $("#provinceArea").html(data);
                    showDistricts1('<?php echo $district; ?>');
                }
            });
        }

        function showDistricts1(distId) {
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                data: {lvl: 3, prov_id: $('#prov_id').val(), distId: distId},
                success: function (data) {
                    $("#districtArea").html(data);
                    loadDashlets();
                }
            });
        }
        function showData(param) {
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                data: {stockStatus: param},
                success: function (data) {
                    $("#modalData").html(data);
                }
            });
            $('#modalId').trigger('click');
        }
        function loadGraph(stkId, type, tabId)
        {
            var dataStr;
            dataStr = 'month=' + $('#month').val();
            dataStr += '&year=' + $('#year').val();
            dataStr += '&lvl=' + $('#ofc_level').val();
            dataStr += '&proFilter=' + $('#product_filter').val();
            if ($('#ofc_level').val() == 2)
            {
                dataStr += '&prov_id=' + $('#prov_id').val();
            }
            if ($('#ofc_level').val() == 3)
            {
                dataStr += '&dist_id=' + $('#dist_id').val();
            }
            dataStr += '&sector=0';
            dataStr = dataStr + '&stkId=' + stkId + '&type=' + type;
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/stock_status_ajax.php',
                data: dataStr,
                success: function (data) {
                    $("#stock-" + tabId).html(data);
                }
            });
        }

        $(function () {
            $('#from_date, #to_date').datepicker({
                dateFormat: "yy-mm",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,

<?php
if ($_SERVER['REMOTE_ADDR'] == 'localhost' || $_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == 'beta.lmis.gov.pk') {
    echo "maxDate: '' ";
} else {
    echo 'minDate: new Date( 2017, 2, 1 ),
                                maxDate: new Date( 2017, ' . $open_month . ' , ' . $open_month_d . ')';
}
?>

            });
        })

    </script>


</body>
<!-- END BODY -->
</html>