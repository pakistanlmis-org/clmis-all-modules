<?php

/**
 * diststkreport
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
$report_id = "SDISTRICTREPORT";
//report title
$report_title = "District Report for ";
//action page
$actionpage = "";
//parameters 
$parameters = "SPIT";
//showexport 
$showexport = "no";
//back page setting
//backparameters 
$backparameters = "TSI";
//backpage 
$backpage = "provincialreport.php";
//province Filter 
$provFilter = $provFilter1 = '';

//forward page setting
//forward parameters 
$forwardparameters = "TSIP";
//forward page
$forwardpage = "fieldstkreport.php";
//parameter width 
$parameter_width = "90%";
//level stakeholder type
$lvl_stktype = 0;
//check selected month
if (isset($_GET['month_sel']) && !isset($_POST['go'])) {
    //get selected month
    $sel_month = $_GET['month_sel'];
    //get selected year
    $sel_year = $_GET['year_sel'];
    //get selected item
    $sel_item = $_GET['item_sel'];
    //get selected province
    $sel_prov = $_GET['prov_sel'];
    //report type
    $rptType = $_GET['sector'];
    //get selected stakeholder
    $sel_stk = $_GET['stkid'];
    //check selected province 
    if ($sel_prov != 'all') {
        //province filter
        $provFilter = " AND tbl_locations.ParentID = $sel_prov";
        //province filter1
        $provFilter1 = " AND summary_district.province_id = $sel_prov";
    }
    //check selected stakeholder
    if (!empty($sel_stk) && $sel_stk != 'all') {
        //filter
        $filter = " AND summary_district.stakeholder_id = '" . $sel_stk . "'";
        //stakeholder Filter 
        $stkFilter = " AND tbl_warehouse.stkid = '" . $sel_stk . "'";
    } else if ($rptType == 'public' && $sel_stk == 'all') {
        //filter
        $filter = " AND stakeholder.stk_type_id = 0";
        //stakeholder Filter 
        $stkFilter = " AND stakeholder.stk_type_id = 0";
        //level stakeholder type 
        $lvl_stktype = 0;
    } else if ($rptType == 'private' && $sel_stk == 'all') {
        //filter
        $filter = " AND stakeholder.stk_type_id = 1";
        //stakeholder Filter 
        $stkFilter = " AND stakeholder.stk_type_id = 1";
        //level stakeholder type 
        $lvl_stktype = 1;
    }
} elseif (isset($_POST['go'])) {
    //check selected month
    if (isset($_POST['month_sel']) && !empty($_POST['month_sel'])) {
        //set selected month
        $sel_month = $_POST['month_sel'];
    }
    //check selected year
    if (isset($_POST['year_sel']) && !empty($_POST['year_sel'])) {
        //get selected year
        $sel_year = $_POST['year_sel'];
    }
    //check selected product
    if (isset($_POST['prod_sel']) && !empty($_POST['prod_sel'])) {
        //get selected product
        $sel_item = $_POST['prod_sel'];
    }
    //check selected province
    if (isset($_POST['prov_sel']) && !empty($_POST['prov_sel'])) {
        //get selected province
        $sel_prov = $_POST['prov_sel'];
    }
    //check selected province
    if ($sel_prov != 'all') {
        //set province filter 
        $provFilter = " AND tbl_locations.ParentID = $sel_prov";
        //set province filter1
        $provFilter1 = " AND summary_district.province_id = $sel_prov";
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
    if (!empty($_POST['stk_sel']) && $_POST['stk_sel'] != 'all') {
        //set selected stakeholder
        $sel_stk = $_POST['stk_sel'];
        //set filter
        $filter = " AND summary_district.stakeholder_id = '" . $_POST['stk_sel'] . "'";
        //set stakeholder filter
        $stkFilter = " AND tbl_warehouse.stkid = '" . $sel_stk . "'";
    } else if ($_POST['sector'] == 'public' && $_POST['stk_sel'] == 'all') {
        //set selected stakeholder
        $sel_stk = 'all';
        //set filter
        $filter = " AND stakeholder.stk_type_id = 0";
        //stakeholder filter
        $stkFilter = " AND stakeholder.stk_type_id = 0";
        //level stakeholder type
        $lvl_stktype = 0;
    } else if ($_POST['sector'] == 'private' && $_POST['stk_sel'] == 'all') {
        //set selected stakeholder
        $sel_stk = 'all';
        //set filter
        $filter = " AND stakeholder.stk_type_id = 1";
        //stakeholder filter
        $stkFilter = " AND stakeholder.stk_type_id = 1";
        //level stakeholder type
        $lvl_stktype = 1;
    } else {
        $sel_stk = 'all';
    }
} else {
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
    //selected item
    $sel_item = "IT-001";
    //selected province
    $sel_prov = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //selected province
    $sel_prov = ($sel_prov != 10) ? $sel_prov : 'all';
    //check selected province
    if ($sel_prov != 'all') {
        //province filter
        $provFilter = " AND tbl_locations.ParentID = $sel_prov";
        //province filter1
        $provFilter1 = " AND summary_district.province_id = $sel_prov";
    }

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
    $in_stk = $sel_stk;
    $filter = " AND summary_district.stakeholder_id = '" . $sel_stk . "'";
    $stkFilter = " AND tbl_warehouse.stkid = '" . $sel_stk . "'";
}

$sector = $rptType;
$_SESSION['PROSTKHOLDER'] = $sel_stk;
$reportingDate = $sel_year . '-' . $sel_month . '-01';

$qry = "SELECT
			*
		FROM
			(
				SELECT DISTINCT
					tbl_locations.PkLocID,
					tbl_locations.LocName
				FROM
					tbl_warehouse
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
				INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
				WHERE
					1 = 1
				$provFilter
				$stkFilter
			) A
		LEFT JOIN (
			SELECT
			summary_district.district_id,
			SUM(summary_district.consumption) AS consumption,
			SUM(summary_district.avg_consumption) AS avg_consumption,
			SUM(summary_district.soh_district_lvl) AS SOH,
			(SUM(summary_district.soh_district_lvl) / SUM(summary_district.avg_consumption)) AS MOS
		FROM
			summary_district
		INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
		INNER JOIN tbl_locations ON summary_district.province_id = tbl_locations.PkLocID
		INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
		WHERE
			summary_district.item_id = '$sel_item'
		AND summary_district.reporting_date = '$reportingDate'
		$provFilter1
		$filter
		GROUP BY
			summary_district.district_id
		) B ON A.PkLocID = B.district_id
		ORDER BY 
			A.LocName";
//query result
$qryRes = mysql_query($qry);
$num = mysql_num_rows(mysql_query($qry));
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$i = 1;
while ($row = mysql_fetch_array($qryRes)) {
    $tempVar = "";
    $tempVar .= "\"$sel_month\",";
    $tempVar .= "\"$sel_year\",";
    $tempVar .= "\"$sel_item\",";
    $tempVar .= "\"$sel_stk\",";
    $tempVar .= "\"$row[PkLocID]\"";
    $mos = (!is_null($row['MOS'])) ? number_format($row['MOS'], 1) : 'UNK';

    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $row['PkLocID'] . "</cell>";
    $xmlstore .= "<cell>" . $i++ . "</cell>";
    $xmlstore .= "<cell><![CDATA[" . $row['LocName'] . "]]></cell>";
    $xmlstore .= "<cell>" . ((!is_null($row['consumption'])) ? number_format($row['consumption']) : 'UNK') . "</cell>";
    $xmlstore .= "<cell>" . ((!is_null($row['avg_consumption'])) ? number_format($row['avg_consumption']) : 'UNK') . "</cell>";
    $xmlstore .= "<cell>" . ((!is_null($row['SOH'])) ? number_format($row['SOH']) : 'UNK') . "</cell>";
    $xmlstore .= "<cell>" . ($mos) . "</cell>";

    $rs_mos = mysql_query("SELECT getMosColor('$mos', '" . $sel_item . "', '" . $sel_stk . "', 3)");
    $bgcolor = mysql_result($rs_mos, 0, 0);

    $xmlstore .= "<cell><![CDATA[<div style=\"width:10px; height:12px; background-color:$bgcolor;\"></div>]]></cell>";
    $xmlstore .= "</row>";
    $consumption += $row['consumption'];
    $avg_consumption += $row['avg_consumption'];
    $SOH +=  $row['SOH'];
    $total_mos += $mos;
}
$xmlstore .= "<row>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell  style=\"text-align:right;font-weight:bold;\">Total</cell>";
$xmlstore .= "<cell>" . number_format($consumption) . "</cell>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell>" . number_format($SOH) . "</cell>";
$xmlstore .= "<cell></cell>";



$xmlstore .= "<cell></cell>";
$xmlstore .= "</row>";
$xmlstore .= "</rows>";

////////////// GET Product Name
$proNameQryRes = mysql_fetch_array(mysql_query("SELECT itm_name FROM `itminfo_tab` WHERE itmrec_id = '" . $sel_item . "' "));
$prodName = "\'$proNameQryRes[itm_name]\'";
if ($sel_stk == 'all') {
    $stakeholderName = "\'All\'";
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
<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
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
                        <!-------- -------->

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
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(0, false);
                                                mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                mygrid.setColumnHidden(0, true);
                                             " title="Export to Excel" />
                                    </td>
                                </tr>
                                <tr>
                                    <td><div id="mygrid_container" style="width:100%; height:320px;"></div></td>
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
            mygrid.setHeader(",<div style='text-align:center;'><?php echo "District Report for Sector = '" . ucwords($rptType) . "' Stakeholder(s) = $stakeholderName Province/Region = $provinceName  And Product = $prodName (" . date('F', mktime(0, 0, 0, $sel_month)) . ' ' . $sel_year . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("District Id, Sr. No., District, Consumption, AMC, Stock On Hand, <div text-align:center;>Month of Stock</div>,#cspan");
            mygrid.attachFooter(",<div style='font-size: 10px;'><?php echo $lastUpdateText; ?><br> Stock on Hand = District Stock on hand + Field Stock on hand</div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("50,60,*,160,160,160,60,40");
            mygrid.setColAlign("left,center,left,right,right,right,center,center");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setColumnHidden(0, true);
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
        function getStakeholder(val, stk)
        {
            $.ajax({
                url: 'ajax_stk.php',
                data: {type: val, stk: stk},
                type: 'POST',
                success: function(data) {
                    $('#stk_sel').html(data);
                    showProducts('<?php echo $sel_item; ?>');
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
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>