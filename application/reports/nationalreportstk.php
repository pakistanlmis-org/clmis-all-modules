<?php
/**
 * nationalreportstk
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
include(APP_PATH . "includes/report/FunctionLib.php"); //Include Global Function File
//include header
include(PUBLIC_PATH . "html/header.php");
// report id
$report_id = "SNASUMSTK";
//report title
$report_title = "National Summary Report by Stakeholder for ";
//action page
$actionpage = "nationalreportSTK.php";
//param
$parameters = "TI";
//param width
$parameter_width = "60%";
//back page setting
$backparameters = "T";
//back page
$backpage = "nationalreport.php";
//forward page setting
$forwardparameters = "";
//forward page
$forwardpage = $forwardurl = '';
//sel stk
$sel_stk = '';
//sel prov
$sel_prov = '';


//'user may have run '
if (isset($_POST['go'])) {
    //ckeck month_sel
    if (isset($_POST['month_sel']) && !empty($_POST['month_sel'])) {
        //get month_sel
        $sel_month = $_POST['month_sel'];
    }
    //ckeck year_sel
    if (isset($_POST['year_sel']) && !empty($_POST['year_sel'])) {
        //get year_sel
        $sel_year = $_POST['year_sel'];
    }
    //ckeck prod_sel
    if (isset($_POST['prod_sel']) && !empty($_POST['prod_sel'])) {
        //get prod_sel
        $sel_item = $_POST['prod_sel'];
    }
} else {
    if (date('d') > 10) {
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    $sel_month = date('m', strtotime($date));
    $sel_year = date('Y', strtotime($date));

    $sel_item = "IT-001";
}

$reportingDate = $sel_year . '-' . $sel_month . '-01';
//query 
//gets
//A.stkid,
//stkname,
//consumption,
//avg_consumption
$qry = "SELECT
			A.stkid,
			A.stkname,
			B.consumption,
			B.avg_consumption
		FROM
			(
				SELECT DISTINCT
					stakeholder.stkid,
					stakeholder.stkname
				FROM
					tbl_warehouse
				INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
				INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
				WHERE
					stakeholder.stk_type_id IN (0, 1)
				AND tbl_warehouse.is_active = 1
				ORDER BY
					stakeholder.stk_type_id ASC,
					stakeholder.stkorder ASC
			) A
		LEFT JOIN (
			SELECT
				SUM(summary_national.consumption) AS consumption,
				SUM(summary_national.avg_consumption) AS avg_consumption,
				summary_national.stakeholder_id
			FROM
				summary_national
			INNER JOIN itminfo_tab ON summary_national.item_id = itminfo_tab.itmrec_id
			RIGHT JOIN stakeholder ON summary_national.stakeholder_id = stakeholder.stkid
			WHERE
				summary_national.reporting_date = '$reportingDate'
			AND itminfo_tab.itmrec_id = '$sel_item'
			GROUP BY
				summary_national.stakeholder_id
		) B ON A.stkid = B.stakeholder_id";
$qryRes = mysql_query($qry);
$num = mysql_num_rows(mysql_query($qry));
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$i = 1;
while ($row = mysql_fetch_array($qryRes)) {
    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $i++ . "</cell>";
    $xmlstore .= "<cell>" . $row['stkname'] . "</cell>";
    $xmlstore .= "<cell>" . ((!is_null($row['consumption'])) ? number_format($row['consumption']) : 'UNK') . "</cell>";
    $xmlstore .= "<cell>" . ((!is_null($row['avg_consumption'])) ? number_format($row['avg_consumption']) : 'UNK') . "</cell>";
    $xmlstore .= "</row>";
    $consumption += $row['consumption'];
    $avg_consumption += $row['avg_consumption'];
   
}
$xmlstore .= "<row>";

$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell  style=\"text-align:right;font-weight:bold;\">Total</cell>";
$xmlstore .= "<cell>" . number_format($consumption) . "</cell>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "</row>";
$xmlstore .= "</rows>";

////////////// GET Product Name
$proNameQryRes = mysql_fetch_array(mysql_query("SELECT itm_name FROM `itminfo_tab` WHERE itmrec_id = '" . $sel_item . "' "));
$prodName = "\'$proNameQryRes[itm_name]\'";
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->

    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        <!-- BEGIN SIDEBAR -->
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>

        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <div class="page-content">

                <!-- BEGIN PAGE HEADER-->
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
                                    <td align="right" style="padding-right:5px;">
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="mygrid_container" style="width:100%; height:320px;"></div>
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
    <!-- END FOOTER -->
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "National Summary Report by Stakeholder for $prodName (" . date('F', mktime(0, 0, 0, $sel_month)) . ' ' . $sel_year . ")"; ?></div>,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No.,Stakeholder, Consumption, AMC");
            mygrid.attachFooter("<div style='font-size: 10px;'><?php echo $lastUpdateText; ?></div>,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("60,*,160,160");
            mygrid.setColAlign("center,left,right,right");
            mygrid.setColTypes("ro,ro,ro,ro");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            $('body').append('<textarea id="xml_string" style="display:none;"><?php echo $xmlstore; ?></textarea>');
            mygrid.loadXMLString(document.getElementById('xml_string').value);
        }
    </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>