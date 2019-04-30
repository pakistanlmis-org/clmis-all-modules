<?php
/**
 * provincial_warehose_report
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
$report_id = "PROVINCIALWAREHOUSE";
//report title 
$report_title = "Provincial Yearly Report for ";
//actionpage 
$actionpage = "provincial_warehouse_report.php";
//parameters 
$parameters = "TS01IP";
//parameter width 
$parameter_width = "95%";

//forward page setting
//forward parameters 
$forwardparameters = "";
//forward page 
$forwardpage = "";
//set province Filter 
$provFilter = '';
//if submitted
if (isset($_POST['go'])) {
    //check selected year
    if (isset($_POST['year_sel']) && !empty($_POST['year_sel'])) {
        //get selected year
        $sel_year = $_POST['year_sel'];
    }
    if (isset($_POST['month_sel']) && !empty($_POST['month_sel'])) {
        //set selected year 
        $sel_mon = $_POST['month_sel'];
        $sel_month = $sel_mon;
    }

    //check selected stakeholder
    if (isset($_POST['stk_sel']) && !empty($_POST['stk_sel'])) {
        //get selected stakeholder
        $sel_stk = $_POST['stk_sel'];
    }
    //check report Indicators
    if (isset($_POST['repIndicators']) && !empty($_POST['repIndicators'])) {
        //get selected report Indicators
        $sel_indicator = $_POST['repIndicators'];
    }
    //check selected province
    if (isset($_POST['prov_sel']) && !empty($_POST['prov_sel'])) {
        //get selected province
        $sel_prov = $_POST['prov_sel'];
    }
    //checl sector
    if ($_POST['sector'] == 'All') {
        //set report type
        $rptType = 'All';
    } else {
        //set report type
        $rptType = $_POST['sector'];
    }
    //check selected stakeholder
    if (!empty($sel_stk) && $sel_stk != 'all') {
        //set stakeholder
        $stkFilter = " AND tbl_warehouse.stkid = '" . $sel_stk . "' ";
    } else if ($rptType == 'public' && $sel_stk == 'all') {
        //set stakeholder
        $stkFilter = " AND stakeholder.stk_type_id = 0";
    } else if ($rptType == 'private' && $sel_stk == 'all') {
        //set stakeholder
        $stkFilter = " AND stakeholder.stk_type_id = 1";
    }
    //check selected province
    if ($sel_prov != 'all') {
        //set province filter
        $provFilter = " AND tbl_locations.PkLocID = $sel_prov";
    }

    // Check indicators
    if ($sel_indicator == 1) {
        //Consumption
        $ind = "\'Consumption\'";
        //set level filter
        $lvlFilter = " AND stakeholder.lvl = 4";
        //set column name
        $colName = 'SUM(tbl_wh_data.wh_issue_up) AS total';
    } else if ($sel_indicator == 2) {
        //Stock on Hand
        $ind = "\'Stock on Hand\'";
        //set level filter
        $lvlFilter = " AND stakeholder.lvl >= 2";
        //set column name
        $colName = 'SUM(tbl_wh_data.wh_cbl_a) AS total';
    } else if ($sel_indicator == 3) {
        //CYP
        $ind = "\'CYP\'";
        //set level filter
        $lvlFilter = " AND stakeholder.lvl = 4";
        //set column name
        //$colName = 'SUM(tbl_wh_data.wh_issue_up) * itminfo_tab.extra AS total';
        $colName = 'SUM(tbl_wh_data.wh_issue_up) * provincial_cyp_factors.cyp_factor AS total';
    } else if ($sel_indicator == 4) {
        //Received(District)
        $ind = "\'Received(District)\'";
        //set level filter
        $lvlFilter = " AND stakeholder.lvl = 3";
        //set column name
        $colName = 'SUM(tbl_wh_data.wh_received) AS total';
    } else if ($sel_indicator == 5) {
        //Received(Field)
        $ind = "\'Received(Field)\'";
        //set level filter
        $lvlFilter = " AND stakeholder.lvl = 4";
        //set column name
        $colName = 'SUM(tbl_wh_data.wh_received) AS total';
    }

    $endDate = $sel_year . '-' . $sel_mon . '-01';
    $startDate = date('Y-m-01', strtotime("-11 months", strtotime($endDate)));
    //echo $startDate.' to '.$endDate;exit;
    $endDate1 = date('Y-m-01', strtotime("+1 months", strtotime($endDate)));

    $qry = "SELECT
				itminfo_tab.itmrec_id,
				itminfo_tab.itm_name,
				itminfo_tab.extra as old,
                                provincial_cyp_factors.cyp_factor as extra,
				tbl_wh_data.RptDate,
				$colName
			FROM
				tbl_warehouse
			INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
			INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
                        LEFT JOIN provincial_cyp_factors ON tbl_warehouse.prov_id = provincial_cyp_factors.province_id  AND tbl_warehouse.stkid = provincial_cyp_factors.stakeholder_id AND itminfo_tab.itm_id = provincial_cyp_factors.item_id

			WHERE
				tbl_wh_data.RptDate BETWEEN '$startDate' AND '$endDate'
				AND itminfo_tab.itm_category = 1
				$stkFilter
				$provFilter
				$lvlFilter
			GROUP BY
				tbl_wh_data.RptDate,
				tbl_wh_data.item_id
			ORDER BY
				itminfo_tab.frmindex ASC";

    if ($sel_indicator == 6) {
        //Received(Field)
        $ind = "\'MOS\'";

        if($sel_stk != 'all'){
            $wherestk = "AND summary_province.stakeholder_id = $sel_stk";
        }
        if($sel_prov != 'all'){
            $wherepro = "AND summary_province.province_id = $sel_prov";
        }
        $qry = "SELECT
                        itminfo_tab.itmrec_id,
                        itminfo_tab.itm_name,
                        summary_province.reporting_date AS RptDate,
                        ROUND(
                                SUM(summary_province.soh_province_lvl) / SUM(summary_province.avg_consumption),
                                2
                        ) total
                FROM
                        summary_province
                INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
                WHERE
                        summary_province.reporting_date BETWEEN '$startDate' AND '$endDate'
                AND itminfo_tab.itm_category = 1
                $wherestk
                    $wherepro
                        GROUP BY
	summary_province.reporting_date,
	itminfo_tab.itmrec_id
                ";
    }
} else {
    //check date
    if (date('d') > 10) {
        //set date
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        //set date
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    $sel_year = date('Y', strtotime($date));
    //check user_stakeholder_type
    if ($_SESSION['user_stakeholder_type'] == 0) {
        //report type
        $rptType = 'public';
        //level stakeholder type
        $lvl_stktype = 0;
    } else if ($_SESSION['user_stakeholder_type'] == 1) {
        //report type
        $rptType = 'private';
        //level stakeholder type
        $lvl_stktype = 1;
    }
    //set selected stakeholder
    $sel_stk = $_SESSION['user_stakeholder1'];
    //set selected province
    $sel_prov = $sel_prov = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //set province filter
    $provFilter = ($sel_prov != 10) ? (" AND tbl_locations.PkLocID = '$sel_prov' ") : '';
    //set selected province
    $sel_prov = ($sel_prov != 10) ? $sel_prov : 'all';

    $sel_item = "IT-001";
    $Stkid = "";
    $sel_indicator = 1;

    $endDate = $sel_year . '-' . $sel_mon . '-01';
    $startDate = date('Y-m-01', strtotime("-11 months", strtotime($endDate)));
    //echo $startDate.' to '.$endDate;exit;
    $endDate1 = date('Y-m-01', strtotime("+1 months", strtotime($endDate)));


    $qry = "SELECT
				itminfo_tab.itmrec_id,
				itminfo_tab.itm_name,
				itminfo_tab.extra as old,
                                provincial_cyp_factors.cyp_factor as extra,
				tbl_wh_data.RptDate,
				SUM(tbl_wh_data.wh_issue_up) AS total
			FROM
				tbl_warehouse
			INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
			INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
                        LEFT JOIN provincial_cyp_factors ON tbl_warehouse.prov_id = provincial_cyp_factors.province_id  AND tbl_warehouse.stkid = provincial_cyp_factors.stakeholder_id AND itminfo_tab.itm_id = provincial_cyp_factors.item_id

			WHERE
				stakeholder.lvl = 4
			$provFilter
			AND tbl_warehouse.stkid = '$sel_stk'
			AND tbl_wh_data.RptDate BETWEEN '$startDate' AND '$endDate'
			AND itminfo_tab.itm_category = 1
			GROUP BY
				tbl_wh_data.RptDate,
				tbl_wh_data.item_id
			ORDER BY
				itminfo_tab.frmindex ASC";

    $ind = "\'Consumption\'";
}

if ($sel_stk == 0) {
    $in_type = 'N';
    $in_stk = 0;
} else {
    $in_type = 'S';
    $in_id = $sel_stk;
    $in_stk = $sel_stk;
}
$in_year = $sel_year;


// Execute uery
//echo $qry;exit;
if (!isset($_POST['go']))
    $qry = '';
$qryRes = mysql_query($qry);
$num = mysql_num_rows(mysql_query($qry));
$data = array();
while ($row = mysql_fetch_array($qryRes)) {
    $data[$row['itmrec_id']][$row['RptDate']] = $row['total'];
    $itemsArr[$row['itmrec_id']]['name'] = $row['itm_name'];
    $itemsArr[$row['itmrec_id']]['CYPFactror'] = $row['extra'];
}

$months_list = '';
// Create XML
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";

$begin = new DateTime($startDate);
$end = new DateTime($endDate1);
$diff = $begin->diff($end);
$totalMonths = (($diff->format('%y') * 12) + $diff->format('%m'));
$interval = DateInterval::createFromDateString('1 month');
$period = new DatePeriod($begin, $interval, $end);
$i = 1;
$loop = 1;
foreach ($data as $itemId => $prodData) {
    $row_total = 0;
    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $i++ . "</cell>";
    $xmlstore .= "<cell><![CDATA[" . $itemsArr[$itemId]['name'] . "]]></cell>";
    foreach ($period as $date) {
        @$total = number_format($prodData[$date->format("Y-m-d")]);
        @$total2 = $prodData[$date->format("Y-m-d")];
        if ($loop == 1)
            $months_list .= "," . $date->format("M");

        $param = urlencode($sel_indicator . '|' . $sel_year . '|' . $date->format("m") . '|' . $sel_stk . '|' . $sel_prov . '|' . $itemId . '|' . $itemsArr[$itemId]['name'] . '|' . $rptType . '|' . $itemsArr[$itemId]['CYPFactror']);
        if ($total != 0 && $sel_indicator!=6) {
            $xmlstore .= "<cell style=\"text-align:right\"><![CDATA[<a href=javascript:showDetail(\"$param\")>$total</a>]]>^_self</cell>";
        } else {
            $xmlstore .= "<cell>$total</cell>";
        }
        $row_total += @$total2;
    }
    $loop++;
    $xmlstore .= "<cell><![CDATA[" . ($sel_indicator!=6? number_format($row_total) : '') . "]]></cell>";
    $xmlstore .= "</row>";
}

$xmlstore .= "</rows>";
//echo $months_list;exit;
////////  Stakeholders for Grid Header
if ($sel_stk == 'all') {
    $stakeholderName = "\'All $sector\'";
} else {
    $stakeNameQryRes = mysql_fetch_array(mysql_query("SELECT stkname FROM stakeholder WHERE stkid = '" . $sel_stk . "' "));
    $stakeholderName = "\'$stakeNameQryRes[stkname]\'";
}

if ($sel_prov == 'all') {
    $provinceName = "\'All\'";
} else {
    $provinceQryRes = mysql_fetch_array(mysql_query("SELECT LocName as prov_title FROM tbl_locations WHERE PkLocID = '" . $sel_prov . "' "));
    $provinceName = "\'$provinceQryRes[prov_title]\'";
}
?>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <table width="100%">
                            <tr>
                                <td><?php include(APP_PATH . "includes/report/reportheader.php"); ?></td>
                            </tr>
                            <?php
                            if ($num > 0) {
                                ?>
                                <tr>
                                    <td align="right" style="padding-right:5px;"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/> <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></td>
                                </tr>
                                <tr>
                                    <td><div id="mygrid_container" style="width:100%; height:320px; background-color:white;"></div></td>
                                </tr>
                                <?php
                            } else {
                                echo "<tr><td>No record found</td></tr>";
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script>
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
            $('#sector').change(function (e) {
                var val = $('#sector').val();
                getStakeholder(val, '');
            });
            getStakeholder('<?php echo $rptType; ?>', '<?php echo $sel_stk; ?>');
        })
    </script>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            //mygrid.setHeader("Province,Consumption,AMC,On Hand,MOS,#cspan");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "Provincial Yearly Report for Stakeholder(s) = $stakeholderName Province = $provinceName And Indicator = $ind (" . date('M Y', strtotime($startDate)) . " to " . date('M Y', strtotime($endDate)) . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No.,Product <?= $months_list ?> ,Total");
            mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("60,*,65,65,65,65,65,65,65,65,65,65,65,65,65");
            mygrid.setColAlign("center,left,right,right,right,right,right,right,right,right,right,right,right,right,right");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
        function showDetail(param)
        {
            window.open('detail_view.php?param=' + param, '_blank', 'scrollbars=1,width=600,height=500');
        }

    </script>
</body>
<!-- END BODY -->
</html>