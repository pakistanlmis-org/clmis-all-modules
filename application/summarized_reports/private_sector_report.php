<?php

$_REQUEST['repIndicators']=$_REQUEST['type'];
//report id
$report_id = "PRIVATESECTOR";
//if submitted
if (isset($_REQUEST['submit'])) {
    //check selected year
    if (isset($_REQUEST['year_sel']) && !empty($_REQUEST['year_sel'])) {
        //set selected year 
        $sel_year = $_REQUEST['year_sel'];
    }
    //check selected stakeholder
    if (isset($_REQUEST['stk_sel']) && !empty($_REQUEST['stk_sel'])) {
        //set selected stakeholder
        $sel_stk = $_REQUEST['stk_sel'];
    }
    //check Indicators
    if (isset($_REQUEST['repIndicators']) && !empty($_REQUEST['repIndicators'])) {
        //set Indicators
        $sel_indicator = $_REQUEST['repIndicators'];
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
    //start date
    $startDate = $sel_year . '-01-01';
    //end date
    $endDate = $sel_year . '-12-01';
    //end date1
    $endDate1 = ($sel_year + 1) . '-01-01';
    //select query
    //gets
    //item name
    //report date
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
    //set selected item
    $sel_item = "IT-001";
    //set stakeholder
    $Stkid = "";
    //set selected stakeholder
    $sel_stk = ($_SESSION['user_stakeholder_type'] == 1) ? $_SESSION['user_stakeholder1'] : 'all';
    //set stakeholder filter
    $stkFilter = ($sel_stk != 'all') ? " AND tbl_warehouse.stkid = $sel_stk" : ' AND stakeholder.stk_type_id = 1';
    //set start date
    $startDate = $sel_year . '-01-01';
    //set end date
    $endDate = $sel_year . '-12-01';
    //set end date1
    $endDate1 = ($sel_year + 1) . '-01-01';
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
foreach ($data as $itemName => $prodData) {

    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $i++ . "</cell>";
    $xmlstore .= "<cell><![CDATA[" . $itemName . "]]></cell>";
    foreach ($period as $date) {
        $xmlstore .= "<cell>" . (isset($prodData[$date->format("Y-m-d")]) ? number_format($prodData[$date->format("Y-m-d")]) : 0) . "</cell>";
    }
    $xmlstore .= "</row>";
}

$xmlstore .="</rows>";

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

<div onLoad="doInitGrid()">
   
                
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

    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "Private Sector Yearly Report for Stakeholder(s) = $stakeholderName And Indicator = $ind (" . $sel_year . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No., Product, Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec");
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
</div>