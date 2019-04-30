<?php
/**
 * district_warehouse_report
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
$report_title = "District Yearly Report for ";
//set actionpage 
$actionpage = "provincial_warehouse_report.php";
//set parameters 
$parameters = "TS01IP";
//set parameter_width 
$parameter_width = "95%";

//forward page setting
//forward parameters 
$forwardparameters = "";
//forward page 
$forwardpage = "";
//selected item
$sel_item = 'IT-001';
//selected filter
$stkFilter = '';
//column name
$colName = 'SUM(tbl_wh_data.wh_issue_up) AS total';
//if submitted
if (isset($_POST['go'])) {
    //check selected year
    if (isset($_POST['year_sel']) && !empty($_POST['year_sel'])) {
        //set selected year
        $sel_year = $_POST['year_sel'];
    }
    //check selected stakeholder
    if (isset($_POST['stk_sel']) && !empty($_POST['stk_sel'])) {
        //get selected stakeholder
        $sel_stk = $_POST['stk_sel'];
    }
    //check repeort Indicators
    if (isset($_POST['repIndicators']) && !empty($_POST['repIndicators'])) {
        //get selected repeort Indicators
        $sel_indicator = $_POST['repIndicators'];
    }
    //check selected province
    if (isset($_POST['prov_sel']) && !empty($_POST['prov_sel'])) {
        //get selected province
        $sel_prov = $_POST['prov_sel'];
    }
    //check selected product
    if (isset($_POST['prod_sel']) && !empty($_POST['prod_sel'])) {
        //get selected product
        $sel_item = $_POST['prod_sel'];
    }
    //check sector
    if ($_POST['sector'] == 'All') {
        //set report type
        $rptType = 'All';
    } else {
        //set report type
        $rptType = $_POST['sector'];
    }
    //check selected stakeholder
    if (!empty($sel_stk) && $sel_stk != 'all') {
        //set stakeholder filter
        $stkFilter = " AND tbl_warehouse.stkid = '" . $sel_stk . "' ";
    } else if ($rptType == 'public' && $sel_stk == 'all') {
        //set stakeholder filter
        $stkFilter = " AND stakeholder.stk_type_id = 0";
    } else if ($rptType == 'private' && $sel_stk == 'all') {
        //set stakeholder filter
        $stkFilter = " AND stakeholder.stk_type_id = 1";
    }
    //province filter
    $provFilter = '';
    //check selected province
    if ($sel_prov != 'all') {
        //set province filter
        $provFilter = " AND tbl_warehouse.prov_id = $sel_prov";
    }

    // Check indicators
    if ($sel_indicator == 1) {
        //Consumption
        $ind = "\'Consumption\'";
        //level filter
        $lvlFilter = " AND stakeholder.lvl = 4";
        //column name
        $colName = 'SUM(tbl_wh_data.wh_issue_up) AS total';
    } else if ($sel_indicator == 2) {
        //Stock on Hand
        $ind = "\'Stock on Hand\'";
        //level filter
        $lvlFilter = " AND stakeholder.lvl >= 2";
        //column name
        $colName = 'SUM(tbl_wh_data.wh_cbl_a) AS total';
    } else if ($sel_indicator == 3) {
        //CYP
        $ind = "\'CYP\'";
        //level filter
        $lvlFilter = " AND stakeholder.lvl = 4";
        //column name
        $colName = 'SUM(tbl_wh_data.wh_issue_up) * itminfo_tab.extra AS total';
    } else if ($sel_indicator == 4) {
        //Received(District)
        $ind = "\'Received(District)\'";
        //level filter
        $lvlFilter = " AND stakeholder.lvl = 3";
        //column name
        $colName = 'SUM(tbl_wh_data.wh_received) AS total';
    } else if ($sel_indicator == 5) {
        //Received(Field)
        $ind = "\'Received(Field)\'";
        //level filter
        $lvlFilter = " AND stakeholder.lvl = 4";
        //column name
        $colName = 'SUM(tbl_wh_data.wh_received) AS total';
    }
    //start date
    $startDate = $sel_year . '-01-01';
    //end date
    $endDate = $sel_year . '-12-01';
    //end date1
    $endDate1 = ($sel_year + 1) . '-01-01';
} else {
    if (date('d') > 10) {
        //set date
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        //set date
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    //set selected year
    $sel_year = date('Y', strtotime($date));

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

    $sel_stk = $_SESSION['user_stakeholder1'];
    //level filter
    $stkFilter = " AND tbl_warehouse.stkid = '" . $sel_stk . "' ";
    //stakeholder filter
    $lvlFilter = " AND stakeholder.lvl = 4";
    //selected province    
    $sel_prov = $sel_prov = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //province filter
    $provFilter = ($sel_prov != 10) ? (" AND tbl_warehouse.prov_id = '$sel_prov' ") : '';
    //selected province
    $sel_prov = ($sel_prov != 10) ? $sel_prov : 'all';
    //selected item
    $sel_item = "IT-001";
    //stakeholder id
    $Stkid = "";
    //selected indicator 
    $sel_indicator = 1;
    //start date
    $startDate = $sel_year . '-01-01';
    //end date
    $endDate = $sel_year . '-12-01';
    //end date1
    $endDate1 = ($sel_year + 1) . '-01-01';


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
//select query
//gets
//report date
//district id
$qry = "SELECT
		*
		FROM
		(
			SELECT
			tbl_wh_data.RptDate,
			tbl_warehouse.dist_id,
		   $colName		                
			FROM
					tbl_warehouse
			INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
			INNER JOIN itminfo_tab ON itminfo_tab.itmrec_id = tbl_wh_data.item_id
			INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
					tbl_wh_data.RptDate BETWEEN '$startDate' AND '$endDate'
			$stkFilter				
			$lvlFilter
			$provFilter
			AND tbl_wh_data.item_id = '$sel_item'
		GROUP BY
		tbl_locations.PkLocID,
		tbl_wh_data.RptDate
		ORDER BY
			tbl_locations.LocName ASC,
			tbl_wh_data.RptDate ASC
		) A
		RIGHT JOIN (
			SELECT DISTINCT
				tbl_locations.PkLocID,
				tbl_locations.LocName
		FROM
			tbl_locations
			INNER JOIN tbl_warehouse ON tbl_locations.PkLocID = tbl_warehouse.dist_id
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
		WHERE
			tbl_locations.LocLvl = 3
			$stkFilter
			$provFilter
		) B ON A.dist_id = B.PkLocID Order By LocName ASC";
// Execute uery
$qryRes = mysql_query($qry);
$num = mysql_num_rows(mysql_query($qry));
$data = array();
while ($row = mysql_fetch_array($qryRes)) {
    $data[$row['LocName']][$row['RptDate']] = $row['total'];
    $itemsArr[$row['LocName']]['name'] = $row['LocName'];
    $itemsArr[$row['LocName']]['CYPFactror'] = $row['extra'];
}

// Create XML
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";

$begin = new DateTime($startDate);
$end = new DateTime($endDate1);
$diff = $begin->diff($end);
$totalMonths = (($diff->format('%y') * 12) + $diff->format('%m'));
$interval = DateInterval::createFromDateString('1 month');
$period = new DatePeriod($begin, $interval, $end);
$counter = 1;
foreach ($data as $itemId => $prodData) {

    $xmlstore .= "<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    $xmlstore .= "<cell><![CDATA[" . $itemsArr[$itemId]['name'] . "]]></cell>";
    foreach ($period as $date) {
        $total = number_format($prodData[$date->format("Y-m-d")]);
        $param = urlencode($sel_indicator . '|' . $sel_year . '|' . $date->format("m") . '|' . $sel_stk . '|' . $sel_prov . '|' . $itemId . '|' . $itemsArr[$itemId]['name'] . '|' . $rptType . '|' . $itemsArr[$itemId]['CYPFactror']);
        if ($total != 0) {
            $xmlstore .= "<cell style=\"text-align:right\"><![CDATA[$total]]>^_self</cell>";
        } else {
            $xmlstore .="<cell>$total</cell>";
        }
    }
    $xmlstore .= "</row>";
}

$xmlstore .="</rows>";


////////  Stakeholders for Grid Header
if ($sel_stk == 'all') {
    $stkName = "\'All $sector\'";
} else {
    $stakeNameQryRes = mysql_fetch_array(mysql_query("SELECT stkname FROM stakeholder WHERE stkid = '" . $sel_stk . "' "));
    $stkName = "\'$stakeNameQryRes[stkname]\'";
}

if ($sel_prov == 'all') {
    $provinceName = "\'All\'";
} else {
    $provinceQryRes = mysql_fetch_array(mysql_query("SELECT LocName as prov_title FROM tbl_locations WHERE PkLocID = '" . $sel_prov . "' "));
    $provinceName = "\'$provinceQryRes[prov_title]\'";
}

//select query
//gets
//item name
$productQryRes = mysql_fetch_array(mysql_query("SELECT
                                                itminfo_tab.itm_name
                                                FROM
                                                itminfo_tab
                                                WHERE
                                                itminfo_tab.itmrec_id = '" . $sel_item . "' "));
$productName = "\'$productQryRes[itm_name]\'";
?>
</head>
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
                            <?php
                            if ($num > 0) {
                                ?>
                                <tr>
                                    <td align="right" style="padding-right:5px;">
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" />                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="mygrid_container" style="width:100%; height:320px; background-color:white;"></div>
                                    </td>
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
    <script language="javascript">
        function frmvalidate() {
            if (document.getElementById('month_sel').value == '') {
                alert('Please Select Month');
                document.getElementById('month_sel').focus();
                return false;
            }
            if (document.getElementById('year_sel').value == '') {
                alert('Please Select Year');
                document.getElementById('year_sel').focus();
                return false;
            }
        }
        function functionCall(month, year, prod) {
            window.location = "nationalreportSTK.php?month_sel=" + month + "&year_sel=" + year + "&item_sel=" + prod;
        }
        function getStakeholder(val, stk)
        {
            $.ajax({
                url: 'ajax_stk.php',
                data: {type: val, stk: stk},
                type: 'POST',
                success: function(data) {
                    $('#stk_sel').html(data)
                }
            })
        }
    </script>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("../operations/dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            //mygrid.setHeader("Province,Consumption,AMC,On Hand,MOS,#cspan");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "District Yearly Report for Stakeholder(s) = $stkName Province = $provinceName Product= $productName And Indicator = $ind (" . $sel_year . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No.,District, Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec");
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
        function showDetail(param)
        {
            window.open('detail_view.php?param=' + param, '_blank', 'scrollbars=1,width=600,height=500');
        }

    </script>
    <script>
        function getStakeholder(val, stk)
        {
            $.ajax({
                url: 'ajax_stk.php',
                data: {type: val, stk: stk},
                type: 'POST',
                success: function(data) {
                    $('#stk_sel').html(data)
                }
            })
        }
        $(function() {
            $('#sector').change(function(e) {
                var val = $('#sector').val();
                getStakeholder(val, '');
            });
            getStakeholder('<?php echo $rptType; ?>', '<?php echo $sel_stk; ?>');
        })
    </script>
</body>
<!-- END BODY -->
</html>