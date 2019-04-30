<?php
//echo '<pre>';print_r($_REQUEST);exit;
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

/*
$todays_date =  date('d');
if($todays_date >=10) $open_month =  date('m')-2;
else $open_month =  date('m')-3;

$a_date = date('Y').'-'.($open_month+1).'-1';
$open_month_d= date("t", strtotime($a_date));

$selProv = (!empty($_REQUEST['province']))?$_REQUEST['province']:'1';
$fromDate = (!empty($_REQUEST['from_date']))?$_REQUEST['from_date']:date("Y-m")."-01";
$selDist  = (!empty($_REQUEST['dist']))?$_REQUEST['dist']:'';
$stakeholder  = (!empty($_REQUEST['stk']))?$_REQUEST['stk']:'';

//$toDate = $fromDate;
//echo '<pre>';print_r($_SESSION);exit;

$wh_comm='';
if(!empty($stakeholder)) $wh_comm  .= " and stakeholder_id  = '".$stakeholder."' ";
if(!empty($selProv)) $wh_comm  .= " and location_id     = '".$selProv."' ";
*/

// filter values ----
if (date('d') > 10) {
    //set date
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    //set date
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
//selected month
$sel_month = date('m', strtotime($date));
//selected year
$sel_year = date('Y', strtotime($date));
//selected stakeholder
$sel_stk = $sel_prov = $sel_dist = $sel_wh = $stkName = $provName = $distName = $whName = $where = $where1 = $where2 = $lvl = $whid = '';
//colspan
$colspan = $header = $header1 = $header2 = $lvl = $width = $colAlign = $colType = $xmlstore = '';
//if submitted
if (isset($_POST['submit'])) {
    //check report month
    if (!empty($_REQUEST['report_month'])) {
        //set selected month
        $sel_month = $_REQUEST['report_month'];
        //set where
        $where[] = "tbl_wh_data.report_month = " . $_POST['report_month'] . " ";
        //set where1
        $where1[] = "MONTH (tbl_hf_data.reporting_date) = " . $_POST['report_month'] . " ";
    }
    //check report year
    if (!empty($_REQUEST['report_year'])) {
        //set selected year
        $sel_year = $_REQUEST['report_year'];
        //set where
        $where[] = "tbl_wh_data.report_year = " . $_POST['report_year'] . " ";
        //set where1
        $where1[] = "YEAR (tbl_hf_data.reporting_date) = " . $_POST['report_year'] . " ";
    }
    //check selected stakeholder
    if (!empty($_REQUEST['stk_sel'])) {
        //set selected stakeholder
        $sel_stk = $_REQUEST['stk_sel'];
        //set where
        $where[] = "tbl_warehouse.stkid = " . $sel_stk . " ";
        //set where1
        $where1[] = "tbl_warehouse.stkid = " . $sel_stk . " ";
        //set where2
        $where2 .= " AND tbl_hf_type_rank.stakeholder_id = " . $sel_stk . " ";
    }
    //check province
    if (!empty($_REQUEST['province'])) {
        //set selected province
        $sel_prov = $_REQUEST['province'];
        //set where
        $where[] = "tbl_warehouse.prov_id = " . $sel_prov . " ";
        //set where1
        $where1[] = "tbl_warehouse.prov_id = " . $sel_prov . " ";
        //set where2
        $where2 .= " AND tbl_hf_type_rank.province_id = " . $sel_prov . " ";
    }
    //check district
    if (!empty($_REQUEST['district'])) {
        //get selected district
        $sel_dist = $_REQUEST['district'];
        //set where
        $where[] = "tbl_warehouse.dist_id = " . $_POST['district'] . " ";
        //set where1
        $where1[] = "tbl_warehouse.dist_id = " . $_POST['district'] . " ";
    }
    //check warehouse
    if (!empty($_REQUEST['warehouse'])) {
        //set selected warehouse
        $sel_wh = $_REQUEST['warehouse'];
        //set where
        $where[] = "tbl_warehouse.wh_id = " . $_POST['warehouse'] . " ";
        //set where1
        $where1[] = "tbl_warehouse.wh_id = " . $_POST['warehouse'] . " ";
    }
    //set where
    $where = implode(' AND ', $where);
    //set where1
    $where1 = implode(' AND ', $where1);

    //include("xml_explorer.php");

    // Get Store name
    $getDist = mysql_fetch_array(mysql_query("SELECT
                                                    tbl_warehouse.wh_name
                                            FROM
                                                    tbl_warehouse
                                            WHERE
                                                    tbl_warehouse.wh_id = '" . $sel_wh . "' "));
    $whName = $getDist['wh_name'];
    $whName = empty($whName) ? 'All' : $whName;

    // Get Stakeholder name
    $getStk = mysql_fetch_array(mysql_query("SELECT
                                                    stakeholder.stkname
                                            FROM
                                                    stakeholder
                                            WHERE
                                                    stakeholder.stkid = '" . $sel_stk . "'"));
    $stkName = $getStk['stkname'];
    $stkName = empty($stkName) ? 'All' : $stkName;

    // Get District name
    $getDist = mysql_fetch_array(mysql_query("SELECT
                                                        tbl_locations.LocName
                                                FROM
                                                        tbl_locations
                                                WHERE
                                                        tbl_locations.PkLocID = '" . $sel_dist . "'"));
    $distName = $getDist['LocName'];
    $distName = empty($distName) ? 'All' : $distName;

    // Get Province name
    $getProv = mysql_fetch_array(mysql_query("SELECT
                                                        tbl_locations.LocName
                                                FROM
                                                        tbl_locations
                                                WHERE
                                                        tbl_locations.PkLocID = '" . $_POST['province'] . "'"));
    $provName = $getProv['LocName'];
    $provName = empty($provName) ? 'All' : $provName;

}
// end filter values

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
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
		//Including top file
        include PUBLIC_PATH . "html/top.php";
		//Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                
                <div class="container-fluid">
                    
                        <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-title row-br-b-wp">M&E Dashboard</h3>
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Filter by</h3>
                                </div>
                                <div class="widget-body">
                                    <table width="99%">
                                        <tr>
                                            <td><form action="" method="post">
                                                    <table>
                                                        <tr>
                                                            <td class="col-md-2"><label class="control-label">Ending Month</label>
                                                                <SELECT NAME="report_month" id="report_month" class="form-control input-sm" TABINDEX="3">
                                                                    <?php
                                                                    for ($i = 1; $i <= 12; $i++) {
                                                                        if ($sel_month == $i) {
                                                                            $sel = "selected='selected'";
                                                                        } elseif ($i == 1) {
                                                                            $sel = "selected='selected'";
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        ?>
                                                                        <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </SELECT></td>
                                                            <td class="col-md-2"><label class="control-label">Year</label>
                                                                <select name="report_year" id="report_year" class="form-control input-sm" tabindex="2">
                                                                    <?php
                                                                    //end year
                                                                    $EndYear = 2010;
                                                                    //start year
                                                                    $StartYear = date('Y');
                                                                    for ($i = $StartYear; $i >= $EndYear; $i--) {
                                                                        if ($i == $sel_year) {
                                                                            $chk4 = "Selected = 'Selected'";
                                                                        } else {
                                                                            $chk4 = "";
                                                                        }
                                                                        echo"<OPTION VALUE='$i' $chk4>$i</OPTION>";
                                                                    }
                                                                    ?>
                                                                </select></td>
                                                            <td class="col-md-2"><label class="control-label">Stakeholder</label>
                                                                <select name="stk_sel" id="stk_sel" class="form-control input-sm">
                                                                    <?php
                                                                    //select query
                                                                    //gets
                                                                    //stakeholder id
                                                                    //stakeholder name
                                                                    $querystk = "SELECT DISTINCT
                                                                                        stakeholder.stkid,
                                                                                        stakeholder.stkname
                                                                                FROM
                                                                                        tbl_warehouse
                                                                                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                                                INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                                                                                WHERE
                                                                                        stakeholder.stk_type_id IN (0, 1)
                                                                                ORDER BY
                                                                                        stakeholder.stkorder ASC";
                                                                    //query result
                                                                    $rsstk = mysql_query($querystk) or die();
                                                                    //fetch result
                                                                    while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                        ?>
                                                                        <option value="<?php echo $rowstk['stkid']; ?>" <?php echo ($sel_stk == $rowstk['stkid']) ? 'selected=selected' : '' ?>><?php echo $rowstk['stkname']; ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select></td>
                                                            <td class="col-md-2"><?php
                                                                //select query
                                                                //gets
                                                                //pk location id
                                                                //location name
                                                                $qry = "SELECT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 2
                                                                        AND tbl_locations.ParentID IS NOT NULL
                                                                        AND tbl_locations.LocType = 2";
                                                                //query result
                                                                $qryRes = mysql_query($qry);
                                                                ?>
                                                                <label class="control-label">Province/Region</label>
                                                                <select name="province" id="province" class="form-control input-sm" required="required">
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    //fetch result
                                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                                        ?>
                                                                        <option value="<?php echo $row['PkLocID']; ?>" <?php echo ($sel_prov == $row['PkLocID']) ? 'selected=selected' : '' ?>><?php echo $row['LocName']; ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select></td>
                                                            <td id="districts" class="col-md-2"><label class="control-label">District</label>
                                                                <select name="district" id="district" class="form-control input-sm">
                                                                    <option value="">Select</option>
                                                                </select></td>
                                                            <td id="stores" class="col-md-2"><label class="control-label">Store/Facility</label>
                                                                <select name="warehouse" id="warehouse" class="form-control input-sm">
                                                                    <option value="">Select</option>
                                                                </select></td>
                                                            <td id="pr_td" class="col-md-2"><label class="control-label">Products</label>
                                                                <select name="products" id="products" class="form-control input-sm">
                                                                    <option <?=((!empty($_REQUEST['products']) && $_REQUEST['products']=='all')?' selected ':'')?> value="all">All Products</option>
                                                                    <option <?=((!empty($_REQUEST['products']) && $_REQUEST['products']=='without_condom')?' selected ':'')?>  value="without_condom">Without Condom</option>
                                                                </select></td>
                                                            <td class="col-md-2">
                                                                
                                                                <input type="hidden" name="district_id" id="district_id" value="<?=((isset($_REQUEST['district'])?$_REQUEST['district']:''))?>">
                                                                <input type="hidden" name="warehouse_id" id="warehouse_id" value="<?=((isset($_REQUEST['warehouse'])?$_REQUEST['warehouse']:''))?>">
                                                                <input type="submit" value="Go" name="submit" class="btn btn-primary input-sm" style="margin-top:28px;" />
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </form></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="page-title row-br-b-wp center"> <?php echo $whName;?> - M&E Dashboard </h3>

                            </div>
                        </div>
                    
                        
                        <div class="row">
                                
                                
                                <div class="col-md-12 my_dash_cols">
                                    <div class="col-md-12 ">
                                        <div class="dashlet_graph" id="dashlet_b1" href='dashboard_mne_b1.php'></div>
                                    </div>
                                    
                                    
                                    
                                    <div class="col-md-12 ">
                                        <div class="dashlet_graph" id="dashlet_b2" href='dashboard_mne_b2.php'></div>
                                    </div>
                                    
                                    
                                </div>
                        </div>
                        
                </div>
            </div>
        </div>
    </div>

    <?php 
    //Including footer file
    include PUBLIC_PATH . "/html/footer.php"; ?>

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
    <?php /* ?><script language="Javascript" src="<?php echo PUBLIC_URL;?>js/maps/cyp_dashlet.js"></script>
      <script language="Javascript" src="<?php echo PUBLIC_URL;?>js/maps/dashlet_Interval.js"></script><?php */ ?>
    <script type="text/javascript">
        
		
                $(function() {
			if(!$('#accordion').hasClass('page-sidebar-menu-closed'))
                        {
                            $(".sidebar-toggler").trigger("click");
                        }
		})
                
                
		$(function() {
			loadDashlets();

                        if(!$('#accordion').hasClass('page-sidebar-menu-closed'))
                        {
                            $(".sidebar-toggler").trigger("click");
                        }
                       
                        
		})
		function loadDashlets(stkId='1')
		{
			$('.dashlet_graph').each(function(i, obj) {
				
				var url = $(this).attr('href');
				var id = $(this).attr('id');
				
                                var dataStr='';
                                dataStr += 'province=' + $('#province').val();
                                //dataStr += '&prov_name=' + $('#prov_name').val();
                                dataStr += '&from_date=' + $('#report_year').val()+'-'+ $('#report_month').val()+'-01';
                                //dataStr += '&to_date=' + $('#to_date').val();
                                dataStr += '&dist=' + $('#district_id').val();
                                //dataStr += '&dist_name='    + $('#dist_name').val();
                                dataStr += '&stk='          + $('#stk_sel').val();
                                dataStr += '&products='     + $('#products').val();
                                dataStr += '&warehouse='    + $('#warehouse_id').val();

                                $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

                                $.ajax({
                                        type: "POST",
                                        url: '<?php echo APP_URL; ?>dashboard/' + url,
                                        data: dataStr,
                                        dataType: 'html',
                                        success: function(data) {
                                                $("#" + id).html(data);
                                        }
                                });
				
			});
                        
                        
		}
                
    </script>
    
    <script>
        $(function() {
            showDistricts('<?php echo $sel_prov; ?>', '<?php echo $sel_stk; ?>');
            showStores('<?php echo $sel_dist; ?>');

            $('#province, #stk_sel').change(function(e) {
                $('#district').html('<option value="">All</option>');
                $('#warehouse').html('<option value="">Select</option>');
                showDistricts($('#province').val(), $('#stk_sel').val());
            });
            $('#stk_sel').change(function(e) {
                $('#warehouse').html('<option value="">All</option>');
            });

            $(document).on('change', '#province, #stk_sel, #district', function() {
                showStores($('#district option:selected').val());
            })
        })
        function showDistricts(prov, stk) {
            if (stk != '' && prov != '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'my_report_ajax.php',
                    data: {provId: prov, stkId: stk, distId: '<?php echo $sel_dist; ?>', showAll: 1},
                    success: function(data) {
                        $("#districts").html(data);
                    }
                });
            }
        }
        function showStores(dist) {
            var stk = $('#stk_sel').val();
            if (stk != '' && dist != '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'my_report_ajax.php',
                    data: {distId: dist, stkId: stk, whId: '<?php echo $sel_wh; ?>'},
                    success: function(data) {
                        $("#stores").html(data);
                    }
                });
            }
        }
    </script>
    
</body>
<!-- END BODY -->
</html>