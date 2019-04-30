<?php

/**
 * central_warehouse_report
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
$report_id = "CENTRALWAREHOUSE";
//report title
$report_title = "Central/Provincial Warehouse Report for ";
//action page
$actionpage = "central_warehouse_report.php";
//parameters
$parameters = "TS01I";
//parameter width
$parameter_width = "100%";
//forward parameters
$forwardparameters = "";
//forward page
$forwardpage = "";
//warehouse type
$whType = $sel_indicator = $stkFilter = '';
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
    //check report indicators
    if (isset($_POST['repIndicators']) && !empty($_POST['repIndicators'])) {
        //get report indicators
        $sel_indicator = $_POST['repIndicators'];
    }
    //check warehouse type
    if (isset($_POST['wh_type']) && !empty($_POST['repIndicators'])) {
        //get  warehouse type
        $whType = $_POST['wh_type'];
    }
    //check selected stakeholder
    if ($sel_stk != 'all') {
        //set stakeholder filter
        $stkFilter = " AND tbl_warehouse.stkid = $sel_stk";
    }
    //check warehouse type
    if ($whType == 'all') {
        //set warehouse filter
        $whTypeFilter = " AND Office.lvl IN(1, 2)";
    } else {
        //set warehouse filter
        $whTypeFilter = " AND Office.lvl = $whType";
    }
    // Check Indicators
    if ($sel_indicator == 1) {
        $ind = "\'Issued\'";
        $colName = 'SUM(tbl_wh_data.wh_issue_up) AS total';
    } else if ($sel_indicator == 2) {
        $ind = "\'Stock on Hand\'";
        $colName = 'SUM(tbl_wh_data.wh_cbl_a) AS total';
    } else if ($sel_indicator == 3) {
        $ind = "\'Received\'";
        $colName = 'SUM(tbl_wh_data.wh_received) AS total';
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
			INNER JOIN stakeholder AS Office ON Office.stkid = tbl_warehouse.stkofficeid
			INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
			WHERE
				1 = 1
			AND tbl_wh_data.RptDate BETWEEN '$startDate' AND '$endDate'
			$stkFilter
			$whTypeFilter
			GROUP BY
				tbl_wh_data.RptDate,
				tbl_wh_data.item_id
			ORDER BY
				itminfo_tab.frmindex ASC";
} else {
    //check date
    if (date('d') > 10) {
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    //set selected year
    $sel_year = date('Y', strtotime($date));
    //set satkeholder id
    $Stkid = "";
    //set selected satkeholder
    $sel_stk = 'all';
    //start date
    
      
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
			INNER JOIN stakeholder AS Office ON Office.stkid = tbl_warehouse.stkofficeid
			INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
			WHERE
				Office.lvl < 3
			AND tbl_wh_data.RptDate BETWEEN '$startDate' AND '$endDate'
			AND itminfo_tab.itm_category = 1
			GROUP BY
				tbl_wh_data.RptDate,
				tbl_wh_data.item_id
			ORDER BY
				itminfo_tab.frmindex ASC";
    $ind = "\'Issued\'";
}
//check selected satkeholder
if ($sel_stk == 0) {
    $in_type = 'N';
    $in_stk = 0;
} else {
    $in_type = 'S';
    $in_id = $sel_stk;
    $in_stk = $sel_stk;
}
$in_year = $sel_year;

////////  Stakeholders for Grid Header
if ($sel_stk == 'all') {
    $stakeholderName = "\'All\'";
} else {
    $stakeNameQryRes = mysql_fetch_array(mysql_query("SELECT stkname FROM stakeholder WHERE stkid = '" . $sel_stk . "' "));
    if ($stakeNameQryRes['stkname'] == 'PWD') {
        $stakeholderName = "\'PPW/CWH\'";
    } else {
        $stakeholderName = "\'$stakeNameQryRes[stkname]\'";
    }
}
//get warehouse type
$_REQUEST['wh_type'] = $whType;
//warehouse type
$whType = ($whType == 1) ? 'Central' : 'Provincial';

// Execute query
$qryRes = mysql_query($qry);
$num = mysql_num_rows(mysql_query($qry));
$data = array();
//fetch result
while ($row = mysql_fetch_array($qryRes)) {
    $data[$row['itm_name']][$row['RptDate']] = $row['total'];
}
$months_list = '';
// Create XML
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
//begin
$begin = new DateTime($startDate);
//end
$end = new DateTime($endDate1);
//diff
$diff = $begin->diff($end);
//total month
$totalMonths = (($diff->format('%y') * 12) + $diff->format('%m'));
//interval
$interval = DateInterval::createFromDateString('1 month');
//peroid
$period = new DatePeriod($begin, $interval, $end);
$i = 1;
$loop = 1;
//get result
foreach ($data as $itemName => $prodData) {
    //xml
    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $i++ . "</cell>";
    $xmlstore .= "<cell><![CDATA[" . $itemName . "]]></cell>";
    foreach ($period as $date) {
        $xmlstore .= "<cell>" . ((isset($prodData[$date->format("Y-m-d")])) ? number_format($prodData[$date->format("Y-m-d")]) : 0) . "</cell>";
        if($loop==1)$months_list .= ",".$date->format("M");
    }
    $loop++;
    $xmlstore .= "</row>";
}

$xmlstore .="</rows>";
//echo $months_list;exit;
?>
<script type="text/javascript">
    function func() {
        var val = $('#stk_sel').val();

        if (val == 2) {
            $('#ppiuList').show("slow");
            $('#ppiuList1').show("slow");
        } else {
            $('#ppiuList').hide("slow");
            $('#ppiuList1').hide("slow");
        }
    }
</script>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php 
        //include top
        include PUBLIC_PATH . "html/top.php"; 
        //include top_im
        include PUBLIC_PATH . "html/top_im.php"; ?>
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
                                    <td><div id="mygrid_container" style="width:100%; height:390px; background-color:white;"></div></td>
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
    <?php 
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    //include reports_includes
    include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            //mygrid.setHeader("Province,Consumption,AMC,On Hand,MOS,#cspan");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "$whType Warehouse Report for Stakeholder(s) = $stakeholderName And Indicator = $ind (" . date('M Y',strtotime($startDate)) ." to ".  date('M Y',strtotime($endDate)) . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No., Product <?=$months_list?> ");
            mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("60,*,65,65,65,65,65,65,65,65,65,65,65,65");
            mygrid.setColAlign("center,left,right,right,right,right,right,right,right,right,right,right,right,right");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }

    </script>
</body>
</html>