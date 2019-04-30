<?php

/**
 * private_sector_report
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
$report_id = "PRIVATESECTOR";
//if submitted
if (isset($_POST['go'])) {
    //check selected year
    if (isset($_POST['year_sel']) && !empty($_POST['year_sel'])) {
        //set selected year 
        $sel_year = $_POST['year_sel'];
    }
    if (isset($_POST['ending_month']) && !empty($_POST['ending_month'])) {
        //set selected year 
        $sel_mon = $_POST['ending_month'];
    }
    //check selected stakeholder
    if (isset($_POST['stk_sel']) && !empty($_POST['stk_sel'])) {
        //set selected stakeholder
        $sel_stk = $_POST['stk_sel'];
    }
    //check Indicators
    if (isset($_POST['repIndicators']) && !empty($_POST['repIndicators'])) {
        //set Indicators
        $sel_indicator = $_POST['repIndicators'];
    }
//check selected stakeholder
    if ($sel_stk != 'all') {
        //set stakeholder filter
        $stkFilter = " AND tbl_warehouse.stkid = $sel_stk";
    } else {
        //set stakeholder filter
        $stkFilter = " AND stakeholder.stk_type_id = 1";
    }

    // Check indicator
    if ($sel_indicator == 1) {
        //Consumption
        $ind = "\'Consumption\'";
        //column name
        $colName = 'SUM(tbl_wh_data.wh_issue_up) AS total';
        //level filter
        $lvlFilter = " AND stakeholder.lvl = 4";
    } else if ($sel_indicator == 2) {
        //Stock on Hand
        $ind = "\'Stock on Hand\'";
        //column name
        $colName = 'SUM(tbl_wh_data.wh_cbl_a) AS total';
        //level filter
        $lvlFilter = " AND stakeholder.lvl >= 2";
    } else if ($sel_indicator == 3) {
        //Received
        $ind = "\'Received\'";
        //column name
        $colName = 'SUM(tbl_wh_data.wh_received) AS total';
        //level filter
        $lvlFilter = " AND stakeholder.lvl = 4";
    }
    
    
    $endDate = $sel_year . '-'.$sel_mon.'-01';
    $startDate = date('Y-m-01',strtotime("-11 months",strtotime($endDate)));
    //echo $startDate.' to '.$endDate;exit;
    $endDate1 = date('Y-m-01',strtotime("+1 months",strtotime($endDate)));


    $qry = "SELECT
				itminfo_tab.itm_name,
				tbl_wh_data.RptDate,
				$colName
			FROM
				tbl_warehouse
			INNER JOIN tbl_wh_data ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
			INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
			INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
			WHERE
				tbl_wh_data.RptDate BETWEEN '$startDate' AND '$endDate'
			$stkFilter
			$lvlFilter
			AND itminfo_tab.itm_category = 1
			GROUP BY
				tbl_wh_data.RptDate,
				tbl_wh_data.item_id
			ORDER BY
				itminfo_tab.frmindex ASC";
} else {
    //check date
    if (date('d') > 10) {
        //set date
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        //set date
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    //set selected year 
    $sel_year = date('Y', strtotime($date));
    $sel_mon = date('m', strtotime($date));
    //set selected item
    $sel_item = "IT-001";
    //set stakeholder
    $Stkid = "";
    //set selected stakeholder
    $sel_stk = ($_SESSION['user_stakeholder_type'] == 1) ? $_SESSION['user_stakeholder1'] : 'all';
    //set stakeholder filter
    $stkFilter = ($sel_stk != 'all') ? " AND tbl_warehouse.stkid = $sel_stk" : ' AND stakeholder.stk_type_id = 1';
    //set start date
        
    $endDate = $sel_year . '-'.$sel_mon.'-01';
    $startDate = date('Y-m-01',strtotime("-11 months",strtotime($endDate)));
    //echo $startDate.' to '.$endDate;exit;
    $endDate1 = date('Y-m-01',strtotime("+1 months",strtotime($endDate)));
//select query
    //gets
    //item name
    //report date
    //total
    $qry = "SELECT
				itminfo_tab.itm_name,
				tbl_wh_data.RptDate,
				SUM(tbl_wh_data.wh_issue_up) AS total
			FROM
				tbl_warehouse
			INNER JOIN tbl_wh_data ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
			INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
			INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
			WHERE
				stakeholder.lvl = 4
			AND tbl_wh_data.RptDate BETWEEN '$startDate' AND '$endDate'
			AND itminfo_tab.itm_category = 1
			$stkFilter
			GROUP BY
				tbl_wh_data.RptDate,
				tbl_wh_data.item_id
			ORDER BY
				itminfo_tab.frmindex ASC";
//set indicator
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
//echo $qry;exit;
// Execute query
$qryRes = mysql_query($qry);
//num of record
$num = mysql_num_rows(mysql_query($qry));
//data array
$data = array();
//fetch result
while ($row = mysql_fetch_array($qryRes)) {
    //set data array
    $data[$row['itm_name']][$row['RptDate']] = $row['total'];
}

$months_list = '';
// Create XML
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
//set begin 
$begin = new DateTime($startDate);
//set end
$end = new DateTime($endDate1);
//set diff
$diff = $begin->diff($end);
//set total months
$totalMonths = (($diff->format('%y') * 12) + $diff->format('%m'));
//set interval
$interval = DateInterval::createFromDateString('1 month');
//set period
$period = new DatePeriod($begin, $interval, $end);
//set i
$i = 1;
$loop = 1;
foreach ($data as $itemName => $prodData) {

    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $i++ . "</cell>";
    $xmlstore .= "<cell><![CDATA[" . $itemName . "]]></cell>";
    foreach ($period as $date) {
        $xmlstore .= "<cell>" . (isset($prodData[$date->format("Y-m-d")]) ? number_format($prodData[$date->format("Y-m-d")]) : 0) . "</cell>";
        if($loop==1)$months_list .= ",".$date->format("M");
    }
    $loop++;
    $xmlstore .= "</row>";
}

$xmlstore .="</rows>";

//echo $months_list;exit;
////////  Stakeholders for Grid Header
if ($sel_stk == 'all') {
    $stakeholderName = "\'All\'";
} else {
    //select 
    //query
    //gets
    //stakeholder name
    $stakeNameQryRes = mysql_fetch_array(mysql_query("SELECT stkname FROM stakeholder WHERE stkid = '" . $sel_stk . "' "));
    //stakeholder name
    $stakeholderName = "\'$stakeNameQryRes[stkname]\'";
}
?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp"> <?php echo "Private Sector Yearly Report for "; ?> <span class="green-clr-txt"><?php echo $sel_year; ?></span> </h3>
                        <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="" id="searchfrm" name="searchfrm">
                                    <table cellspacing="3" cellpadding="3" border="0">
                                        <tbody>
                                            <tr>
                                                <td class="col-md-2"><label class="control-label">Ending Month</label>
                                                    <select class="form-control input-sm" id="ending_month" name="ending_month">
                                                        <?php
                                                        for ($j = 1; $j <=12; $j++) {
                                                            if ($sel_mon == $j) {
                                                                $sel = "selected='selected'";
                                                            } else if ($j == date("m")) {
                                                                $sel = "selected='selected'";
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            ?>
                                                            <option value="<?php echo sprintf("%02d", $j); ?>" <?php echo $sel; ?> ><?php echo date("M",strtotime('2018-'.$j.'-01')); ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td class="col-md-2"><label class="control-label">Year</label>
                                                    <select class="form-control input-sm" id="year_sel" name="year_sel">
                                                        <?php
                                                        for ($j = date('Y'); $j >= 2010; $j--) {
                                                            if ($sel_year == $j) {
                                                                $sel = "selected='selected'";
                                                            } else if ($j == date("Y")) {
                                                                $sel = "selected='selected'";
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            ?>
                                                            <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </td>
                                                <!-- Stakeholder -->
                                                <td class="col-md-2"><label class="control-label">Stakeholder</label>
                                                    <?php
                                                    //select query
   													//gets
                                                    /// Get private Sectors Stakeholders
                                                    $pvtQry = mysql_query("SELECT DISTINCT
																				stakeholder.stkid,
																				stakeholder.stkname
																			FROM
																				tbl_warehouse
																			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
																			INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
																			WHERE
																				tbl_warehouse.is_active = 1
																			AND stakeholder.stk_type_id = 1
																			ORDER BY
																				stakeholder.stkorder ASC");
                                                    ?>
                                                    <select class="form-control input-sm" id="stk_sel" name="stk_sel">
                                                        <option value="all">All</option>
                                                        <?php while ($pvtRow = mysql_fetch_array($pvtQry)) { ?>
                                                            <option value="<?php echo $pvtRow['stkid']; ?>" <?php
                                                            if ($pvtRow['stkid'] == $sel_stk) {
                                                                echo "selected=selected";
                                                            }
                                                            ?>><?php echo $pvtRow['stkname']; ?></option>
														<?php } ?>
                                                    </select></td>
                                                <td class="col-md-2"><label class="control-label">Indicator</label>
                                                    <select id="repIndicators" name="repIndicators" class="form-control input-sm">
                                                        <option value="1" <?php echo ($sel_indicator == 1) ? "selected=selected" : '';?>>Consumption</option>
                                                        <option value="2" <?php echo ($sel_indicator == 2) ? "selected=selected" : '';?>>Stock on Hand</option>
                                                        <option value="3" <?php echo ($sel_indicator == 3) ? "selected=selected" : '';?>>Received</option>
                                                    </select></td>
                                                <td class="col-md-2"><input type="submit" class="btn btn-primary input-sm" value="GO" id="go" name="go" style="margin-left: 15px; margin-top:28px;"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
<?php
if ($num > 0) {
    ?>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="right" style="padding-right:5px;"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/> <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></td>
                                </tr>
                                <tr>
                                    <td><div id="mygrid_container" style="width:100%; height:390px;"></div></td>
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
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "Private Sector Yearly Report for Stakeholder(s) = $stakeholderName And Indicator = $ind (" . date('M Y',strtotime($startDate)) ." to ".  date('M Y',strtotime($endDate)) . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No., Product <?=$months_list?>");
            mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("60,*,60,60,60,60,60,60,60,60,60,60,60,60");
            mygrid.setColAlign("center,left,right,right,right,right,right,right,right,right,right,right,right,right");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script> 
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>