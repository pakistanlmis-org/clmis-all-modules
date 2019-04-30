<?php
/**
 * stock_availability
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
$report_id = "SNASUMSTOCKLOC";
//report title
$report_title = "Stock Availability Report for ";
//action page
$actionpage = "stock_availability.php";
//parameters 
$parameters = "TS01P01I";
//selected product
$sel_prod = $sel_stk = $stk_id = $prov_id = $stkFilter = $provFilter = $prov_id = '';

if (isset($_GET['tp']) && !isset($_POST['go'])) {
    //check report_month
    if (isset($_GET['report_month']) && !empty($_GET['report_month'])) {
        //get report_month
        $sel_month = $_GET['report_month'];
    }
    //check report_year
    if (isset($_GET['report_year']) && !empty($_GET['report_year'])) {
        //get report_year
        $sel_year = $_GET['report_year'];
    }
//check item_id
    if (isset($_GET['item_id']) && !empty($_GET['item_id'])) {
        //get item_id
        $sel_item = $_GET['item_id'];
        //proFilter
        $proFilter = " AND summary_district.item_id = '" . $sel_item . "'";
    }
    //check stk id
    if (isset($_GET['stk_id']) && !empty($_GET['stk_id'])) {
        if ($_GET['stk_id'] != 'all') {
            //get stk id
            $sel_stk = $_GET['stk_id'];
            $stkFilter = " AND stakeholder.stkid = " . $sel_stk;
        } else {
            $qStrStk = " ";
            $sel_stk = $_GET['stk_id'];
        }
    }
    //check prov id
    if (isset($_GET['prov_id']) && !empty($_GET['prov_id'])) {
        if ($_GET['prov_id'] != 'all') {
            //get prov Id
            $sel_prov = $_GET['prov_id'];
            //provFilter 
            $provFilter = " AND summary_district.province_id = " . $sel_prov;
        } else {
            $sel_prov = $_GET['prov_id'];
            $qStrProv = "";
        }
    }
} else if (isset($_POST['go'])) {
    //check month_sel
    if (isset($_POST['month_sel']) && !empty($_POST['month_sel'])) {
        //get month_sel
        $sel_month = $_POST['month_sel'];
    }
//check year_sel
    if (isset($_POST['year_sel']) && !empty($_POST['year_sel'])) {
        //get year_sel
        $sel_year = $_POST['year_sel'];
    }
//check prod_sel
    if (isset($_POST['prod_sel']) && !empty($_POST['prod_sel']) && $_POST['prod_sel'] != 'all') {
        //get prod_sel
        $sel_item = $_POST['prod_sel'];
        //proFilter 
        $proFilter = " AND summary_district.item_id = '" . $sel_item . "'";
    }
//check stl_sel
    if (isset($_POST['stk_sel']) && !empty($_POST['stk_sel']) && $_POST['stk_sel'] != 'all') {
        //get stl_sel
        $sel_stk = $_POST['stk_sel'];
        //stkFilter 
        $stkFilter = " AND stakeholder.stkid = " . $sel_stk;
    } else {
        $qStrStk = " ";
        //get selected stk
        $sel_stk = isset($_GET['stk_id']) ? $_GET['stk_id'] : '';
    }
//check prov_sel
    if (isset($_POST['prov_sel']) && !empty($_POST['prov_sel']) && $_POST['prov_sel'] != 'all') {
        //get selected prov
        $sel_prov = $_POST['prov_sel'];
        //prov filter
        $provFilter = " AND summary_district.province_id = " . $sel_prov;
    } else {
        //selected prov
        $sel_prov = isset($_GET['prov_id']) ? $_GET['prov_id'] : '';
        $qStrProv = "";
    }
} else {
    if (date('d') > 10) {
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    //selected month
    $sel_month = date('m', strtotime($date));
    //selected year
    $sel_year = date('Y', strtotime($date));
//selected prov
    $sel_prov = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //selected stk
    $sel_stk = $_SESSION['user_stakeholder1'];
    //stk filter
    $stkFilter = " AND stakeholder.stkid = $sel_stk ";
    //selected item
    $sel_item = 'IT-001';
    //prov filter
    $proFilter = " AND summary_district.item_id = '$sel_item'";
    $provFilter = ($sel_prov != 10) ? " AND summary_district.province_id = $sel_prov " : '';
    //selected prov
    $sel_prov = ($sel_prov != 10) ? $sel_prov : 'all';
}

if ($sel_stk == 'all') {
    $in_stk = 0;
}
if ($sel_prov == 'all') {
    $in_prov = 0;
}
$in_month = $sel_month;
$in_year = $sel_year;
$in_item = $sel_prod;

// Central Warehouses
ob_flush();
$total = 0;
$cwhtotal = 0;
$ppiutotal = 0;
$disttotal = 0;
//reporting date


$qry_c= "SELECT
                    GROUP_CONCAT(distinct funding_stk_prov.funding_source_id) as sources
                FROM
                    funding_stk_prov
                INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                WHERE
                    1=1
                 ";     
if(!empty($sel_stk))
               $qry_c .=" AND  funding_stk_prov.stakeholder_id = $sel_stk ";
if(!empty($sel_prov) && $sel_prov!='all')
               $qry_c .=" AND  funding_stk_prov.province_id = $sel_prov  ";
 
    
//echo $qry_c;exit;
$res = mysql_query($qry_c);
$comments_arr =array();
$row=mysql_fetch_assoc($res);
//print_r($row);exit;

$funding_sources=$row['sources'];

$reportingDate = $sel_year . '-' . $sel_month . '-01';

$qry1 = "SELECT
            tbl_locations.LocName,
            funding_stk_prov.funding_source_id,
            Sum(summary_province.avg_consumption) AS avg_consumption,
            itminfo_tab.itm_id,
            tbl_warehouse.wh_name
        FROM
            summary_province
            INNER JOIN tbl_locations ON summary_province.province_id = tbl_locations.PkLocID
            INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
            INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
            INNER JOIN funding_stk_prov ON summary_province.province_id = funding_stk_prov.province_id AND stakeholder.stkid = funding_stk_prov.stakeholder_id
            INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
        WHERE
            summary_province.reporting_date = '".$reportingDate."' AND
            stakeholder.stk_type_id = 0 AND
            tbl_locations.ParentID IS NOT NULL ";
$qry1 .= " AND itminfo_tab.itmrec_id = '$sel_item' ";
if(!empty($sel_prov) && $sel_prov != 'ALL' && $sel_prov != 'all')
    $qry1 .= " AND summary_province.province_id = $sel_prov  ";
if(!empty($funding_sources))
    $qry1 .= " AND funding_stk_prov.funding_source_id in ($funding_sources)
        GROUP BY
            funding_stk_prov.funding_source_id
";
//query result
//echo $qry1;
$qryRes1 = mysql_query($qry1);
$amc_arr = array();
while ($row = mysql_fetch_assoc($qryRes1)) {
   $amc_arr[$row['funding_source_id']]  = $row['avg_consumption'];
}


$qry = "    SELECT
                    itminfo_tab.itm_name,
                    itminfo_tab.qty_carton,
                    Sum(tbl_stock_detail.Qty) AS soh,
                    tbl_itemunits.UnitType,
                    itminfo_tab.itm_id,
                    stock_batch.funding_source,
                    tbl_warehouse.wh_name AS funding_source_name
            FROM
                    stock_batch
            INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
            INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
            INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
            INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
            INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
            WHERE
                    DATE_FORMAT(
                            tbl_stock_master.TranDate,
                            '%Y-%m-%d'
                    ) <= '".date('Y-m-t',strtotime($reportingDate))."'
                    AND (
                            tbl_stock_master.WHIDFrom = 123
                            OR tbl_stock_master.WHIDTo = 123
                    )
                    AND stock_batch.funding_source in ($funding_sources)
                    AND tbl_stock_master.temp = 0
                    AND itminfo_tab.itmrec_id = '$sel_item'
            GROUP BY
                    stock_batch.funding_source
            
";
//query result
//echo $qry;exit;
$qryRes = mysql_query($qry);
$numCentral = mysql_num_rows(mysql_query($qry));
$xmlCentral = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlCentral .= "<rows>";
$i = 1;
//fetch result
while ($row = mysql_fetch_array($qryRes)) {
    $xmlCentral .= "<row>";
    $xmlCentral .= "<cell>" . $i++ . "</cell>";
    $xmlCentral .= "<cell><![CDATA[" . $row['funding_source_name'] . "]]></cell>";
    //$xmlCentral .= "<cell><![CDATA[" . $row['stkname'] . "]]></cell>";
    $xmlCentral .= "<cell>" . ((!is_null($amc_arr[$row['funding_source']])) ? number_format($amc_arr[$row['funding_source']]) : 'UNK') . "</cell>";
    $xmlCentral .= "<cell>" . ((!is_null($row['soh'])) ? number_format($row['soh']) : 'UNK') . "</cell>";

    $mos = 0 ;
    if(isset($amc_arr[$row['funding_source']]) && $amc_arr[$row['funding_source']] > 0) $mos = $row['soh'] / ($amc_arr[$row['funding_source']]);
    
    $rs_mos = mysql_query("SELECT getMosColor('" . $mos . "', '" . $sel_item . "', '" . $sel_stk . "', 1)");
    $bgcolor = mysql_result($rs_mos, 0, 0);
    $xmlCentral .= "<cell><![CDATA[" . ((!is_null($mos)) ? number_format($mos, 1) : 'UNK') . "<label style=\"width:10px; height:12px; background-color:$bgcolor;vertical-align: text-top; margin-left:5px;\"></label>]]></cell>";

    $xmlCentral .= "</row>";
}
$xmlCentral .= "</rows>";
 
$qry = "SELECT	
			tbl_locations.PkLocID AS distId,
			tbl_locations.LocName AS distName,
			Province.PkLocID AS provId,
			Province.LocName AS provName,
			stakeholder.stkname,
			summary_district.avg_consumption,
			summary_district.soh_district_store AS SOH_district,
			(summary_district.soh_district_store / summary_district.avg_consumption) AS MOS_district,
			(summary_district.soh_district_lvl - summary_district.soh_district_store) AS SOH_field,
			((summary_district.soh_district_lvl - summary_district.soh_district_store) / summary_district.avg_consumption ) AS MOS_field,
			summary_district.soh_district_lvl AS SOH_total,
			(summary_district.soh_district_lvl / summary_district.avg_consumption) AS MOS_total
		FROM
			summary_district
		INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
		INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
		INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
		WHERE
			summary_district.reporting_date = '$reportingDate'
		$proFilter
		$stkFilter
		$provFilter
                Group by 
                    summary_district.district_id,
                    stakeholder.stkid
		ORDER BY
			provId ASC,
			distName ASC,
			stakeholder.stkorder ASC";
//query result
//echo $qry;
$qryRes = mysql_query($qry);
$numDistrict = mysql_num_rows(mysql_query($qry));
$xmlDistrict = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlDistrict .= "<rows>";
$i = 1;
//fetch results
while ($row = mysql_fetch_array($qryRes)) {
    $xmlDistrict .= "<row>";
    $xmlDistrict .= "<cell>" . $row['distId'] . "</cell>";
    $xmlDistrict .= "<cell>" . $i++ . "</cell>";
    $xmlDistrict .= "<cell><![CDATA[" . $row['provName'] . "]]></cell>";
    $xmlDistrict .= "<cell><![CDATA[" . $row['distName'] . "]]></cell>";
    $xmlDistrict .= "<cell><![CDATA[" . $row['stkname'] . "]]></cell>";
    $xmlDistrict .= "<cell>" . ((!is_null($row['avg_consumption'])) ? number_format($row['avg_consumption']) : 'UNK') . "</cell>";
    $xmlDistrict .= "<cell>" . ((!is_null($row['SOH_district'])) ? number_format($row['SOH_district']) : 'UNK') . "</cell>";

//query result
    $rs_mos = mysql_query("SELECT getMosColor('" . ((!is_null($row['MOS_district'])) ? number_format($row['MOS_district'], 1) : 'UNK'). "', '" . $sel_item . "', '" . $sel_stk . "', 3)");
    $bgcolor = mysql_result($rs_mos, 0, 0);
    $xmlDistrict .= "<cell><![CDATA[" . ((!is_null($row['MOS_district'])) ? number_format($row['MOS_district'], 1) : 'UNK') . "<label style=\"width:10px; height:12px; background-color:$bgcolor;vertical-align: text-top; margin-left:5px;\"></label>]]></cell>";

    $xmlDistrict .= "<cell>" . ((!is_null($row['SOH_field'])) ? number_format($row['SOH_field']) : 'UNK') . "</cell>";

    $rs_mos = mysql_query("SELECT getMosColor('" . ((!is_null($row['MOS_field'])) ? number_format($row['MOS_field'], 1) : 'UNK') . "', '" . $sel_item . "', '" . $sel_stk . "', 4)");
    $bgcolor = mysql_result($rs_mos, 0, 0);
    $xmlDistrict .= "<cell><![CDATA[" . ((!is_null($row['MOS_field'])) ? number_format($row['MOS_field'], 1) : 'UNK') . "<label style=\"width:10px; height:12px; background-color:$bgcolor;vertical-align: text-top; margin-left:5px;\"></label>]]></cell>";

    $xmlDistrict .= "<cell>" . ((!is_null($row['SOH_total'])) ? number_format($row['SOH_total']) : 'UNK') . "</cell>";
    $xmlDistrict .= "<cell>" . ((!is_null($row['MOS_total'])) ? number_format($row['MOS_total'], 1) : 'UNK') . "</cell>";
    $xmlDistrict .= "</row>";
}
$xmlDistrict .= "</rows>";


////////////// GET Product Name
$proNameQryRes = mysql_fetch_array(mysql_query("SELECT itm_name FROM `itminfo_tab` WHERE itmrec_id = '" . $sel_item . "' "));
$prodName = "\'$proNameQryRes[itm_name]\'";
//////////// GET Province/Region Name
//check sel_prov
if ($sel_prov == 'all' || $sel_prov == "") {
    $provinceName = "\'All\'";
} else {
    $qry = "SELECT tbl_locations.LocName as prov_title
			FROM tbl_locations where tbl_locations.PkLocID = '" . $sel_prov . "' ";
    $provinceQryRes = mysql_fetch_array(mysql_query($qry));
    $provinceName = "\'$provinceQryRes[prov_title]\'";
}
////////////// GET Stakeholders
//check sel_stk
if ($sel_stk == 'all' || $sel_stk == "") {
    $stakeholderName = "\'All\'";
} else {
    //stakeNameQryRes
    $stakeNameQryRes = mysql_fetch_array(mysql_query("SELECT stkname FROM stakeholder WHERE stkid = '" . $sel_stk . "' "));
    //stakeholderName
    $stakeholderName = "\'$stakeNameQryRes[stkname]\'";
}
?>
<style>
    .objbox {
        overflow-x: hidden !important;
    }
</style>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
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
                                <td><?php include(APP_PATH . "includes/report/reportheader.php"); ?></td>
                            </tr>
                        </table>
                        <?php if ($numCentral > 0) { ?>
                            <table width="100%">
                                <tr>
                                    <td align="right" style="padding-right:5px;">
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="gridCentral.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="gridCentral.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="hdrTable"><div id="central_container" style="width:100%; height:200px; background-color:white;overflow:hidden"></div></td>
                                </tr>
                            </table>
                            <br>
                        <?php } else { ?>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="hdrTable"><div id="central_container" style="width:100%; height:26px; background-color:white;overflow:hidden"></div></td>
                                </tr>
                                <tr>
                                    <td align="center"><strong>No record found.</strong></td>
                                </tr>
                            </table>
                            <br>
                        <?php } if ($numDistrict > 0) { ?>
                            <table width="100%">
                                <tr>
                                    <td align="right" style="padding-right:5px;">
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="gridDistrict.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="gridDistrict.setColumnHidden(0, false);
                                                gridDistrict.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                gridDistrict.setColumnHidden(0, true);" title="Export to Excel" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="hdrTable"><div id="district_container" style="width:100%; height:350px; background-color:white;overflow:hidden"></div></td>
                                </tr>
                            </table>
                            <br>
                        <?php } else { ?>
                            <table width="100%" cellpadding="0" cellspacing="0" style="display:none;">
                                <tr>
                                    <td class="hdrTable"><div id="district_container" style="width:100%; height:26px; background-color:white;overflow:hidden"></div></td>
                                </tr>
                                <tr>
                                    <td align="center"><strong>No record found.</strong></td>
                                </tr>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
//include report_includes
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
        var mygrid;
        function doInitGrid() {
            gridCentral = new dhtmlXGridObject('central_container');
            gridCentral.selMultiRows = true;
            gridCentral.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            gridCentral.setHeader("<div style='text-align:center;'><?php echo "Central Warehouse Report for Stakeholder = $stakeholderName And Product = $prodName (" . date('F', mktime(0, 0, 0, $sel_month)) . ' ' . $sel_year . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan");
            gridCentral.attachHeader("Sr. No., Warehouse, AMC, Stock on Hand, Month of Stock");
            gridCentral.attachFooter("<div style='font-size: 10px;'><?php echo $lastUpdateText; ?></div>,#cspan,#cspan,#cspan,#cspan");
            gridCentral.setInitWidths("60,*,100,100,100");
            gridCentral.setColAlign("center,left,right,right,right");
            gridCentral.setColSorting("int,str,str");
            gridCentral.setColTypes("ro,ro,ro,ro,ro");
            gridCentral.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            gridCentral.setSkin("light");
            gridCentral.init();
            gridCentral.clearAll();
            gridCentral.loadXMLString('<?php echo $xmlCentral; ?>');

            /*gridProvince = new dhtmlXGridObject('province_container');
             gridProvince.selMultiRows = true;
             gridProvince.setImagePath("../operations/dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
             gridProvince.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold; font-family:Helvetica'><?php echo "Provincial Report for Stakeholder = $stakeholderName Province/Region = $provinceName  And Product = $prodName (" . date('F', mktime(0, 0, 0, $sel_month)) . ' ' . $sel_year . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan");
             gridProvince.attachHeader("<span title='Provincial Office'>Provincial Warehouse</span>,<span title='Stakeholder Name'>Stakeholder</span>,<span title='Average Monthly Consumption'>AMC</span>,<span title='Total'>Total</span>,<span title='Month of Scale'>MOS</span>");
             gridProvince.setInitWidths("*,250,100,100,100");
             gridProvince.setColAlign("left,left,right,right,right");
             gridProvince.setColSorting("str,str");
             gridProvince.setColTypes("ro,ro,ro,ro,ro");
             gridProvince.enableRowsHover(true,'onMouseOver');   // `onMouseOver` is the css class name.
             gridProvince.setSkin("light");
             gridProvince.init();
             gridProvince.clearAll();
             gridProvince.loadXMLString('<?php //echo $xmlProvince;                 ?>');*/

            gridDistrict = new dhtmlXGridObject('district_container');
            gridDistrict.selMultiRows = true;
            gridDistrict.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            gridDistrict.setHeader(",<div style='text-align:center;'><?php echo "Districts Report for Stakeholder = $stakeholderName Province/Region = $provinceName  And Product = $prodName (" . date('F', mktime(0, 0, 0, $sel_month)) . ' ' . $sel_year . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            gridDistrict.attachHeader("District Id, Sr. No., Province/Region, District, Stakeholder, AMC, SOH Store, MOS Store, SOH Field, MOS Field, SOH Total, MOS Total");
            gridDistrict.attachFooter(",<div style='font-size: 10px;'><?php echo $lastUpdateText; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            gridDistrict.setInitWidths("60,50,*,150,100,80,80,80,80,80,80,80");
            gridDistrict.setColAlign("left,center,left,left,left,right,right,right,right,right,right,right");
            gridDistrict.setColSorting("int,str,str,str");
            gridDistrict.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
            gridDistrict.setColumnHidden(0, true);
            gridDistrict.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            gridDistrict.setSkin("light");
            gridDistrict.init();
            gridDistrict.clearAll();
            gridDistrict.loadXMLString('<?php echo $xmlDistrict; ?>');
        }
    </script>
</body>
</html>