<?php
/**
 * quarterly-rate
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
$report_id = "QTRREPORT";
$date1 = '';
$date2 = '';
$date3 = '';
//quarter 
$quarter = isset($_POST['ending_month']) ? $_POST['ending_month'] : '1';
//year
$year = isset($_POST['year_sel']) ? $_POST['year_sel'] : date('Y');
$i = 1;
//check quarter 
if ($quarter == '1') {
    //set month
    $month = 1;
//set date1
    $date1 = "$year-01-01";
//set date2
    $date2 = "$year-02-01";
//set date3 
    $date3 = "$year-03-01";
} else if ($quarter == '2') {
//set month	
    $month = 4;
    //set date1
    $date1 = "$year-04-01";
//set date2
    $date2 = "$year-05-01";
//set date3
    $date3 = "$year-06-01";
}
//check quarter
else if ($quarter == '3') {
    //set month
    $month = 7;
    //set date1
    $date1 = "$year-07-01";
    //set date2
    $date2 = "$year-08-01";
    //set date3 
    $date3 = "$year-09-01";
} else if ($quarter == '4') {
    //set month
    $month = 10;
    //set date1
    $date1 = "$year-10-01";
    //set date2
    $date2 = "$year-11-01";
    //set date3
    $date3 = "$year-12-01";
}
//select query
//gets
//A.prov_id,
//Location Name,
//PWD pct_1,
//LHW pct_1,
//DOH pct_1,
//PWD pct_2,
//LHW pct_2,
//DOH pct_2,
//PWD pct_3,
//LHW pct_3,
//DOH pct_3
$query = "SELECT
			A.prov_id,
			tbl_locations.LocName,
			IFNULL(ROUND((SUM(PWDRpt_1)/PWD) * 100, 2),0) AS PWDpct_1,
			IFNULL(ROUND((SUM(LHWRpt_1)/LHW) * 100, 2),0) AS LHWpct_1,
			IFNULL(ROUND((SUM(DOHRpt_1)/DOH) * 100, 2),0) AS DOHpct_1,
			IFNULL(ROUND((SUM(PWDRpt_2)/PWD) * 100, 2),0) AS PWDpct_2,
			IFNULL(ROUND((SUM(LHWRpt_2)/LHW) * 100, 2),0) AS LHWpct_2,
			IFNULL(ROUND((SUM(DOHRpt_2)/DOH) * 100, 2),0) AS DOHpct_2,
			IFNULL(ROUND((SUM(PWDRpt_3)/PWD) * 100, 2),0) AS PWDpct_3,
			IFNULL(ROUND((SUM(LHWRpt_3)/LHW) * 100, 2),0) AS LHWpct_3,
			IFNULL(ROUND((SUM(DOHRpt_3)/DOH) * 100, 2),0) AS DOHpct_3
		FROM (
			SELECT
				tbl_locations.PkLocID AS prov_id,
				SUM(IF(tbl_warehouse.stkid = 1, 1, 0)) AS PWD,
				SUM(IF(tbl_warehouse.stkid = 2, 1, 0)) AS LHW,
				SUM(IF(tbl_warehouse.stkid = 7, 1, 0)) AS DOH
			FROM
			stakeholder
			INNER JOIN tbl_warehouse ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
			WHERE
				stakeholder.lvl IN(3, 4)
			AND tbl_locations.ParentID IS NOT NULL
			GROUP BY
				tbl_warehouse.prov_id
			) A
			LEFT JOIN
			(SELECT
				tbl_warehouse.prov_id,
				SUM(IF(tbl_warehouse.stkid = 1 AND tbl_wh_data.RptDate = '$date1', 1, 0)) AS PWDRpt_1,
				SUM(IF(tbl_warehouse.stkid = 2 AND tbl_wh_data.RptDate = '$date1', 1, 0)) AS LHWRpt_1,
				SUM(IF(tbl_warehouse.stkid = 7 AND tbl_wh_data.RptDate = '$date1', 1, 0)) AS DOHRpt_1,
				SUM(IF(tbl_warehouse.stkid = 1 AND tbl_wh_data.RptDate = '$date2', 1, 0)) AS PWDRpt_2,
				SUM(IF(tbl_warehouse.stkid = 2 AND tbl_wh_data.RptDate = '$date2', 1, 0)) AS LHWRpt_2,
				SUM(IF(tbl_warehouse.stkid = 7 AND tbl_wh_data.RptDate = '$date2', 1, 0)) AS DOHRpt_2,
				SUM(IF(tbl_warehouse.stkid = 1 AND tbl_wh_data.RptDate = '$date3', 1, 0)) AS PWDRpt_3,
				SUM(IF(tbl_warehouse.stkid = 2 AND tbl_wh_data.RptDate = '$date3', 1, 0)) AS LHWRpt_3,
				SUM(IF(tbl_warehouse.stkid = 7 AND tbl_wh_data.RptDate = '$date3', 1, 0)) AS DOHRpt_3
			FROM
				stakeholder
			INNER JOIN tbl_warehouse ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
			WHERE
				stakeholder.lvl IN(3, 4) AND
			tbl_wh_data.RptDate BETWEEN '$date1' AND '$date3' AND tbl_wh_data.item_id = 'IT-001'
			GROUP BY
				tbl_warehouse.prov_id
			UNION 
			SELECT
				tbl_warehouse.prov_id,
				SUM(IF(tbl_warehouse.stkid = 1 AND tbl_hf_data.reporting_date = '$date1', 1, 0)) AS PWDRpt_1,
				SUM(IF(tbl_warehouse.stkid = 2 AND tbl_hf_data.reporting_date = '$date1', 1, 0)) AS LHWRpt_1,
				SUM(IF(tbl_warehouse.stkid = 7 AND tbl_hf_data.reporting_date = '$date1', 1, 0)) AS DOHRpt_1,
				SUM(IF(tbl_warehouse.stkid = 1 AND tbl_hf_data.reporting_date = '$date2', 1, 0)) AS PWDRpt_2,
				SUM(IF(tbl_warehouse.stkid = 2 AND tbl_hf_data.reporting_date = '$date2', 1, 0)) AS LHWRpt_2,
				SUM(IF(tbl_warehouse.stkid = 7 AND tbl_hf_data.reporting_date = '$date2', 1, 0)) AS DOHRpt_2,
				SUM(IF(tbl_warehouse.stkid = 1 AND tbl_hf_data.reporting_date = '$date3', 1, 0)) AS PWDRpt_3,
				SUM(IF(tbl_warehouse.stkid = 2 AND tbl_hf_data.reporting_date = '$date3', 1, 0)) AS LHWRpt_3,
				SUM(IF(tbl_warehouse.stkid = 7 AND tbl_hf_data.reporting_date = '$date3', 1, 0)) AS DOHRpt_3
			FROM
				stakeholder
			INNER JOIN tbl_warehouse ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
			WHERE
				stakeholder.lvl IN(3, 4) AND 
			tbl_hf_data.reporting_date BETWEEN '$date1' AND '$date3' AND tbl_hf_data.item_id = '1'
			GROUP BY
				tbl_warehouse.prov_id
			) B
			ON A.prov_id = B.prov_id
			JOIN tbl_locations ON tbl_locations.PkLocID = A.prov_id
			WHERE
				tbl_locations.ParentID IS NOT NULL
			GROUP BY 
				A.prov_id";
//query result
$rs_query = mysql_query($query);
//num of result
$num = mysql_num_rows(mysql_query($query));
//check num
if ($num > 0) {
    //xml
    $xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xmlstore .="<rows>";
    $counter = 1;
    //fetch array
    while ($rsPro = mysql_fetch_array($rs_query)) {

        $xmlstore .="<row>";
        //increment counter
        $xmlstore .="<cell>" . $counter++ . "</cell>";
        //location name
        $xmlstore .="<cell>$rsPro[LocName]</cell>";
        //PWDpct_1
        $xmlstore .="<cell style=\"text-align:right;\">" . (($rsPro['PWDpct_1'] > 0) ? $rsPro['PWDpct_1'] : 0) . "</cell>";
        //LHWpct_1
        $xmlstore .="<cell style=\"text-align:right;\">" . (($rsPro['LHWpct_1'] > 0) ? $rsPro['LHWpct_1'] : 0) . "</cell>";
        //DOHpct_1
        $xmlstore .="<cell style=\"text-align:right;\">" . (($rsPro['DOHpct_1'] > 0) ? $rsPro['DOHpct_1'] : 0) . "</cell>";
        //PWDpct_2
        $xmlstore .="<cell style=\"text-align:right;\">" . (($rsPro['PWDpct_2'] > 0) ? $rsPro['PWDpct_2'] : 0) . "</cell>";
        //LHWpct_2
        $xmlstore .="<cell style=\"text-align:right;\">" . (($rsPro['LHWpct_2'] > 0) ? $rsPro['LHWpct_2'] : 0) . "</cell>";
        //DOHpct_2
        $xmlstore .="<cell style=\"text-align:right;\">" . (($rsPro['DOHpct_2'] > 0) ? $rsPro['DOHpct_2'] : 0) . "</cell>";
        //PWDpct_3
        $xmlstore .="<cell style=\"text-align:right;\">" . (($rsPro['PWDpct_3'] > 0) ? $rsPro['PWDpct_3'] : 0) . "</cell>";
        //LHWpct_3
        $xmlstore .="<cell style=\"text-align:right;\">" . (($rsPro['LHWpct_3'] > 0) ? $rsPro['LHWpct_3'] : 0) . "</cell>";
        //DOHpct_3
        $xmlstore .="<cell style=\"text-align:right;\">" . (($rsPro['DOHpct_3'] > 0) ? $rsPro['DOHpct_3'] : 0) . "</cell>";
        //xml row
        $xmlstore .="</row>";
    }

    $xmlstore .="</rows>";
}
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

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp"> <?php echo "Reporting Rate for"; ?> <span class="green-clr-txt"><?php echo " Quarter-" . $quarter . " of " . $year; ?></span> </h3>
                        <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post">
                                    <table width="50%">
                                        <tr>
                                            <td class="col-md-2"><label class="sb1NormalFont">Quarter</label>
                                                <select name="ending_month" id="ending_month" class="form-control input-sm">
                                                    <option value="1" name="quarter1" <?php
                                                    if ($quarter == "1") {
                                                        echo "selected='selected'";
                                                    }
                                                    ?>>First Quarter</option>
                                                    <option value="2" name="quarter2" <?php
                                                    if ($quarter == "2") {
                                                        echo "selected='selected'";
                                                    }
                                                    ?>>Second Quarter</option>
                                                    <option value="3" name="quarter3" <?php
                                                            if ($quarter == "3") {
                                                                echo "selected='selected'";
                                                            }
                                                            ?>>Third Quarter</option>
                                                    <option value="4" name="quarter4"<?php
                                                    if ($quarter == "4") {
                                                        echo "selected='selected'";
                                                    }
                                                    ?>>Fourth Quarter</option>
                                                </select></td>
                                            <td class="col-md-2"><label class="sb1NormalFont">Year</label>
                                                <select name="year_sel" id="year_sel" class="form-control input-sm">
                                                    <?php
                                                    for ($j = date('Y'); $j >= 2010; $j--) {
                                                        if ($year == $j) {
                                                            $sel = "selected='selected'";
                                                        } else if ($j == 1) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
    <?php
}
?>
                                                </select></td>
                                            <td class="col-md-2" style="margin-left:20px; padding-top: 20px;" valign="middle"><input type="submit" name="submit" id="go" value="GO" class="btn btn-primary input-sm" /></td>
                                        </tr>
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
                            <table width="100%" cellpadding="0" cellspacing="0" id="myTable">
                                <tr>
                                    <td align="right" style="padding-right:5px;"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/> <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></td>
                                </tr>
                                <tr>
                                    <td><div id="mygrid_container" style="width:100%; height:330px;"></div></td>
                                </tr>
                            </table>
    <?php
} else {
    echo "No record found";
}
?>
                    </div>
                </div>
            </div>
        </div>
        <!-- END FOOTER -->
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
//include reports_includes
include PUBLIC_PATH . "/html/reports_includes.php";
?>
        <script>
            var mygrid;
            function doInitGrid() {
                mygrid = new dhtmlXGridObject('mygrid_container');
                mygrid.selMultiRows = true;
                mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
                mygrid.setHeader("<div style='text-align:center;'><?php echo "Provincial - Quarterly Reporting Rate " . ' (Quarter-' . $quarter . ' ' . $year . ")"; ?> </div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
                mygrid.attachHeader("<div title='Serial Number'>Sr. No.</div>,<div title='Province Name'>Province</div>,<div title='January' style='text-align:center;'><?php echo date('F', mktime(0, 0, 0, $month)) ?></div>,#cspan,#cspan,<div title='February' style='text-align:center;'><?php echo date('F', mktime(0, 0, 0, $month + 1)) ?></div>,#cspan,#cspan,<div title='March' style='text-align:center;'><?php echo date('F', mktime(0, 0, 0, $month + 2)) ?></div>,#cspan,#cspan");
                mygrid.setColAlign("center,left,center,center,center,center,center,center,center,center,center");
                mygrid.attachHeader("#rspan,#rspan,<div style='text-align:center;' title='Public Welfare Health'>PWD</div>,<div style='text-align:center;' title='Lady Health Worker'>LHW</div>,<div style='text-align:center;' title='Department of Health'>DOH</div>,<div style='text-align:center;'>PWD</div>,<div style='text-align:center;'>LHW</div>,<div style='text-align:center;'>DOH</div>,<div style='text-align:center;'>PWD</div>,<div style='text-align:center;'>LHW</div>,<div style='text-align:center;'>DOH</div>");
                mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
                mygrid.setInitWidths("60,200,*,*,*,*,*,*,*,*,*");
                mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
                mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
                mygrid.setSkin("light");
                mygrid.init();
                mygrid.clearAll();
                mygrid.loadXMLString('<?php echo $xmlstore; ?>');
            }
        </script>
</body>
<!-- END BODY -->
</html>