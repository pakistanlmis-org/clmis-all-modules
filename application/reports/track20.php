<?php
/**
 * non_report
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$report_id = "TRACK20";
//action page 
$actionpage = "track20.php";

$parameters = "TSP";

$report_title = "Track20 Report";
//print_r($_REQUEST);

if (isset($_GET['d']) && !empty($_GET['d'])) {
    list($yy, $mm, $dd) = explode("-", $_GET['d']);
    $_REQUEST['month_sel'] = $mm;
    $_REQUEST['year_sel'] = $yy;
    //get selected month 
    $sel_month = $_REQUEST['month_sel'];
//get selected year
    $sel_year = $_REQUEST['year_sel'];
} else {
    //get selected month 
    $sel_month = $_REQUEST['month_sel'];
//get selected year
    $sel_year = $_REQUEST['year_sel'];
}

//get selected stakeholder
$sel_stk = (!empty($_REQUEST['stk_sel']) ? $_REQUEST['stk_sel'] : 'all');
$sel_report_by = $_REQUEST['report_by'];
$operator = ">";
$operator2 = ">=";
$operatorvalue = "availability";


$stk_where = $prov_where = $dist_where = '';

if ($sel_stk != 'all') {
    $stk_where = " AND alerts_stockout_table.stkid = $sel_stk";
}
//get selected province
$sel_prov = $_REQUEST['prov_sel'];
if ($sel_prov != 'all') {
    $prov_where = " AND alerts_stockout_table.prov_id = $sel_prov";
}
//get selected district
$sel_district = $_REQUEST['district'];
if (!empty($sel_district)) {
    $dist_where = " AND alerts_stockout_table.dist_id = $sel_district";
}
//get selected report type
$sel_report_type = $_REQUEST['sdp_cat'];
$productcount = 3;
if ($sel_report_type == 2) {
    $sel_report_type = "2,3";
    $productcount = 5;
}

$sel_report_type_array = array(
    '1' => 'Primary SDP',
    '2' => 'Secondary SDP',
    '3' => 'Tertiary SDP'
);
//get selected level type
$sel_items = $_REQUEST['prod_sel'];

//$productcount_where = '';
//if (is_array($sel_items)) {
//    $productcount = count($sel_items);
//    $productcount_where = " HAVING itemso = $productcount";
//    foreach ($sel_items as $item) {
//        $product_list2[] = (int) str_replace("IT-", "", $item);
//    }
//    $product_list = implode(",", $product_list2);
//}
//echo "<pre>";
//print_r($_REQUEST);
//exit;
//get report type
//warehouse filter
$wh_filter = '';
//check selected province
//select query
//gets
//provinceId,
//province,
//districtId,
//district,
//stakeholder Main,
//stakeholder Office,
//warehouse id,
//warehouse name,
//warehouse rank,
//add_date,
//last_update,
//ip address
$qry = "SELECT DISTINCT
	Sum(

		IF (
			alerts_stockout_table.SOH > 0,
			1,
			0
		)
	) AS itemsa,
	GROUP_CONCAT(
		DISTINCT
		IF (
			alerts_stockout_table.SOH > 0,
			alerts_stockout_table.itm_name,
			''
		)
	) AS items,
	Sum(

		IF (
			alerts_stockout_table.SOH <= 0,
			1,
			0
		)
	) AS itemso,
	GROUP_CONCAT(
		DISTINCT
		IF (
			alerts_stockout_table.SOH <= 0,
			alerts_stockout_table.itm_name,
			''
		)
	) AS itemsso,
	alerts_stockout_table.Stakeholder,
	alerts_stockout_table.Province,
	alerts_stockout_table.District,
	alerts_stockout_table.wh_name,
	alerts_stockout_table.reporting_date,
	alerts_stockout_table.wh_id
FROM
	alerts_stockout_table
INNER JOIN tbl_warehouse ON alerts_stockout_table.wh_id = tbl_warehouse.wh_id
WHERE
	alerts_stockout_table.reporting_date = '$sel_year-$sel_month-01'
AND tbl_warehouse.hf_cat_id IN ($sel_report_type)
$stk_where
$prov_where
$dist_where
GROUP BY
	alerts_stockout_table.wh_id";
//$qry = "SELECT
//	alerts_stockout_table.Stakeholder,
//	alerts_stockout_table.Province,
//	alerts_stockout_table.District,
//	alerts_stockout_table.wh_name,
//	COUNT(
//		DISTINCT alerts_stockout_table.item_id
//	) AS itemso,
//	GROUP_CONCAT(
//		DISTINCT alerts_stockout_table.itm_name
//	) AS items,
//	alerts_stockout_table.reporting_date
//FROM
//	alerts_stockout_table
//INNER JOIN tbl_warehouse ON alerts_stockout_table.wh_id = tbl_warehouse.wh_id
//WHERE
//    alerts_stockout_table.reporting_date = '$sel_year-$sel_month-01'
//AND alerts_stockout_table.SOH $operator 0
//AND tbl_warehouse.hf_cat_id IN ($sel_report_type)
//$stk_where
//$prov_where
//$dist_where
//GROUP BY
//	alerts_stockout_table.wh_id
//        HAVING itemso $operator2 $productcount
//ORDER BY
//	alerts_stockout_table.stkid,
//	alerts_stockout_table.prov_id,
//	alerts_stockout_table.dist_id,
//	alerts_stockout_table.wh_name";
//echo $qry; //exit;
//query result
$qryRes = mysql_query($qry);

$qry22 = "SELECT
	COUNT(
		DISTINCT alerts_stockout_table.wh_id
	) total
FROM
	alerts_stockout_table
INNER JOIN tbl_warehouse ON alerts_stockout_table.wh_id = tbl_warehouse.wh_id
WHERE
	alerts_stockout_table.reporting_date = '$sel_year-$sel_month-01' "
        . "AND tbl_warehouse.hf_cat_id IN ($sel_report_type)"
        . "$stk_where
$prov_where
$dist_where";
//echo $qry22;

$qryRes22 = mysql_query($qry22);
while ($row22 = mysql_fetch_assoc($qryRes22)) {
    $totalcnt = $row22['total'];
}
//count
$count = 1;
// Create XML for the Grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";

while ($row = mysql_fetch_assoc($qryRes)) {
    if ($sel_report_by == 1 && $row['itemsa'] >= $productcount) {
        $xmlstore .= "<row>";
        $xmlstore .= "<cell>" . $count++ . "</cell>";
//province
        $xmlstore .= "<cell><![CDATA[" . $row['Stakeholder'] . "]]></cell>";
//district
        $xmlstore .= "<cell><![CDATA[" . $row['Province'] . "]]></cell>";
//stakeholder Main
        $xmlstore .= "<cell><![CDATA[" . $row['District'] . "]]></cell>";
//stakeholder Office
        $xmlstore .= "<cell><![CDATA[" . $row['wh_name'] . "]]></cell>";
        //$xmlstore .= "<cell><![CDATA[" . $sel_report_type_array[$sel_report_type] . "]]></cell>";
        //$xmlstore .= "<cell><![CDATA[" . date('F', mktime(0, 0, 0, $sel_month)) . " " . $sel_year . "]]></cell>";
//last_update
        $xmlstore .= "<cell><![CDATA[" . $row['items'] . "]]></cell>";

        $xmlstore .= "</row>";

        $stakeholderName = $row['Stakeholder'];
        $provinceName = $row['Province'];
    }

    if ($sel_report_by == 2 && $row['itemsa'] < $productcount) {
        $xmlstore .= "<row>";
        $xmlstore .= "<cell>" . $count++ . "</cell>";
//province
        $xmlstore .= "<cell><![CDATA[" . $row['Stakeholder'] . "]]></cell>";
//district
        $xmlstore .= "<cell><![CDATA[" . $row['Province'] . "]]></cell>";
//stakeholder Main
        $xmlstore .= "<cell><![CDATA[" . $row['District'] . "]]></cell>";
//stakeholder Office
        $xmlstore .= "<cell><![CDATA[" . $row['wh_name'] . "]]></cell>";
        //$xmlstore .= "<cell><![CDATA[" . $sel_report_type_array[$sel_report_type] . "]]></cell>";
        //$xmlstore .= "<cell><![CDATA[" . date('F', mktime(0, 0, 0, $sel_month)) . " " . $sel_year . "]]></cell>";
//last_update
        $xmlstore .= "<cell><![CDATA[" . $row['itemsso'] . "]]></cell>";

        $xmlstore .= "</row>";

        $stakeholderName = $row['Stakeholder'];
        $provinceName = $row['Province'];
        $operatorvalue = "out";
    }
}

$xmlstore .= "</rows>";
//echo "Total".$totalcnt."<br>count:".$count;

$stockststussdp = --$count;
//if ($sel_report_by == 2) {
//    $operatorvalue = "out";
//    $stockststussdp = $totalcnt - $stockststussdp;
//}
//echo $stockststussdp;

if ($sel_stk == 'all') {
    $stakeholderName = 'All';
}

if ($sel_prov == 'all') {
    $provinceName = 'All';
}
//echo $stockststussdp;
?>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <table width="100%">
                            <tr>
                                <td><?php
        //include reportheader
        include(APP_PATH . "includes/report/reportheader.php");
        ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h4>Total Reported SDPs: <?php echo $totalcnt; ?></h4>
                    </div>
                    <div class="col-md-4">
                        <h4>Stock <?php echo $operatorvalue; ?> in SDPs: <?php echo ($stockststussdp); ?></h4>
                    </div>
                    <div class="col-md-4">
                        <h4>Stock <?php echo $operatorvalue; ?> rate: <?php echo ROUND($stockststussdp / $totalcnt * 100, 2); ?>%</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if ($count > 1) {
                            ?>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="right" style="padding-right:5px;">
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" />
                                    </td>
                                </tr>
                                <tr>
                                    <td><div id="mygrid_container" style="width:100%; height:470px;"></div></td>
                                </tr>
                            </table>
                            <?php
                        } else {
                            echo "No record found.";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script>

        $(function () {
            $('#districts_td_id').hide();

            $('#sector').change(function (e) {
                var val = $('#sector').val();
                getStakeholder(val, '');
            });
            getStakeholder('<?php echo $rptType; ?>', '<?php echo $sel_stk; ?>');
        })
        function getStakeholder(val, stk)
        {
            $.ajax({
                url: 'ajax_stk.php',
                data: {type: val, stk: stk},
                type: 'POST',
                success: function (data) {
                    $('#stk_sel').html(data)
                }
            })
        }
        $(function () {
            showDistricts('<?php echo $sel_district; ?>');
            $('#prov_sel').change(function (e) {
                showDistricts('');
            });
        })
        function showDistricts(dId)
        {
            var provinceId = $('#prov_sel').val();
            $.ajax({
                url: 'ajax_calls.php',
                type: 'POST',
                data: {provinceId: provinceId, validate: 'no', allOpt: 'yes', dId: dId, stkId: $('#stk_sel').val()},
                success: function (data) {
                    $('#districtsCol').html(data);
                }
            });
        }
    </script> 
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "$report_title Stakeholder = $stakeholderName And Province/Region = $provinceName (" . date('F', mktime(0, 0, 0, $sel_month)) . ' ' . $sel_year . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("S. No., Stakeholder, Province/Region, District, Warehouse/Store Name, Stock <?php echo $operatorvalue; ?> in");
            mygrid.attachHeader(",,,#select_filter,,#text_filter");
            mygrid.setInitWidths("50,150,150,150,*,250");
            mygrid.setColAlign("center,left,left,left,left,left");
            mygrid.setColSorting("int,str,str,str,str,str");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
            mygrid.enableMultiline(true);
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }

        function functionCall(ip)
        {
            window.open('ip_info.php?ip=' + ip, '_blank', 'scrollbars=1,width=650,height=600');
        }

        function showData(p)
        {
            window.open('wh_info.php?whId=' + p, '_blank', 'scrollbars=1,width=900,height=500');
        }
        function showHistory(p)
        {
            window.open('wh_data_history_list.php?whId=' + p, '_blank', 'scrollbars=1,width=900,height=600');
        }
    </script>
</body>
<!-- END BODY -->
</html>