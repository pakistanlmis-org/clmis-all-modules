<?php
//////////// GET FILE NAME FROM THE URL
$arr = explode("?", basename($_SERVER['REQUEST_URI']));
//base name 
$basename = $arr[0];
//file Path
$filePath = "plmis_src/reports/" . $basename;
//report_id
$report_id = "SPROVINCEREPORT";
//report_title
$report_title = "Province/Region Report for ";
//actionpage
$actionpage = "provincialreport.php";
//parameters
$parameters = "TS01I";
//parameter_width
$parameter_width = "100%";
//sel_stk
$sel_stk = '1';
//sel_prov
$sel_prov = $forwardpage = $forwardurl = '';

//back page setting
//backparameters
$backparameters = "TI";
//backpage
$backpage = "nationalreportSTK.php";

//forward page setting
//forwardparameters
$forwardparameters = "";
//forwardpage
$forwardpage = "";
$filter=$filter1='';
//if submitted

if (isset($_REQUEST['go'])) {
//check month_sel
    if (isset($_REQUEST['month_sel']) && !empty($_REQUEST['month_sel'])) {
        //get month_sel
        $sel_month = $_REQUEST['month_sel'];
    }
//check year_sel
    if (isset($_REQUEST['year_sel']) && !empty($_REQUEST['year_sel'])) {
        //get year_sel
        $sel_year = $_REQUEST['year_sel'];
    }
//check prod_sel
    if (isset($_REQUEST['prod_sel']) && !empty($_REQUEST['prod_sel'])) {
        //prod_sel
        $sel_item = $_REQUEST['prod_sel'];
    }
//sector
    if ($_REQUEST['sector'] == 'All') {
        //report type
        $rptType = 'All';
    } else {
        //report type
        $rptType = $_REQUEST['sector'];
    }
//stk_sel
    if (!empty($_REQUEST['stk_sel']) && $_REQUEST['stk_sel'] != 'all') {
        //sel_stk
        $sel_stk = $_REQUEST['stk_sel'];
        //filter
        $filter = " AND summary_province.stakeholder_id = '" . $_REQUEST['stk_sel'] . "'";
        $filter1 = " AND tbl_warehouse.stkid = $sel_stk";
    } else if ($_REQUEST['sector'] == 'public' && $_REQUEST['stk_sel'] == 'all') {
        //sel_stk
        $sel_stk = 'all';
        //filter
        $filter = " AND stakeholder.stk_type_id = 0";
    } else if ($_REQUEST['sector'] == 'private' && $_REQUEST['stk_sel'] == 'all') {
        //sel_stk
        $sel_stk = 'all';
        //filter
        $filter = " AND stakeholder.stk_type_id = 1";
    } else {
        //sel_stk
        $sel_stk = 'all';
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
    //selected month
    $sel_month = date('m', strtotime($date));
    //selected year
    $sel_year = date('Y', strtotime($date));
    //selected item
    $sel_item = "IT-001";

    if ($_SESSION['user_stakeholder_type'] == 0) {
        //report type
        $rptType = 'public';
        //level stk type 
        $lvl_stktype = 0;
    } else if ($_SESSION['user_stakeholder_type'] == 1) {
        //report type
        $rptType = 'private';
        //level stk type 
        $lvl_stktype = 1;
    }
    
    $sel_stk = $_SESSION['user_stakeholder1'];
    //filter
    if (!empty($sel_stk)) {
        $filter = " AND summary_province.stakeholder_id = " . $sel_stk;
        $filter1 = " AND tbl_warehouse.stkid = $sel_stk";
    }
}
//sector
$sector = $rptType;
//reporting Date
$reportingDate = $sel_year . '-' . $sel_month . '-01';
//select query
//gets
// pk location id
//location name
 $qry = "SELECT
			*
		FROM
			(
				SELECT DISTINCT
					tbl_locations.PkLocID,
					tbl_locations.LocName
				FROM
					tbl_locations
				INNER JOIN tbl_warehouse ON tbl_locations.PkLocID = tbl_warehouse.prov_id
				INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
				WHERE
					tbl_locations.ParentID IS NOT NULL
				AND tbl_locations.LocLvl = 2
				AND tbl_warehouse.is_active = 1
				$filter1
			) A
		LEFT JOIN (
			SELECT
				summary_province.province_id,
				SUM(summary_province.consumption) AS consumption,
				SUM(summary_province.avg_consumption) AS avg_consumption,
				SUM(summary_province.soh_province_lvl) AS SOH,
				(SUM(summary_province.soh_province_lvl) / SUM(summary_province.avg_consumption)) AS MOS
			FROM
				summary_province
			INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
			INNER JOIN tbl_locations ON summary_province.province_id = tbl_locations.PkLocID
			INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
			WHERE
				summary_province.item_id = '$sel_item'
			AND summary_province.reporting_date = '$reportingDate'
			$filter
			AND tbl_locations.ParentID IS NOT NULL
			GROUP BY
				summary_province.province_id
		) B ON A.PkLocID = B.province_id
	ORDER BY
			A.PkLocID";
//results
$qryRes = mysql_query($qry);
//num of results
$num = mysql_num_rows(mysql_query($qry));
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$i = 1;
$consumption=$avg_consumption=$SOH=$total_mos=0;
//fetch result
while ($row = mysql_fetch_array($qryRes)) {
    $tempVar = "";
    $tempVar .= "\"$sel_month\",";
    $tempVar .= "\"$sel_year\",";
    $tempVar .= "\"$sel_item\",";
    $tempVar .= "\"$rptType\",";
    $tempVar .= "\"$sel_stk\",";
    $tempVar .= "\"$row[PkLocID]\"";
    $mos = (!is_null($row['MOS'])) ? number_format($row['MOS'], 1) : 'UNK';

    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $i++ . "</cell>";
    $xmlstore .= "<cell><![CDATA[<a href=javascript:functionCall($tempVar)>" . $row['LocName'] . "</a>]]>^_self</cell>";
    $xmlstore .= "<cell>" . ((!is_null($row['consumption'])) ? number_format($row['consumption']) : 'UNK') . "</cell>";
    $xmlstore .= "<cell>" . ((!is_null($row['avg_consumption'])) ? number_format($row['avg_consumption']) : 'UNK') . "</cell>";
    $xmlstore .= "<cell>" . ((!is_null($row['SOH'])) ? number_format($row['SOH']) : 'UNK') . "</cell>";
    $xmlstore .= "<cell>" . $mos . "</cell>";

    $rs_mos = mysql_query("SELECT getMosColor('$mos', '" . $sel_item . "', '" . $sel_stk . "', 2)");
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
////////////// GET Stakeholders

if ($sel_stk == 'all') {
    $stakeholderName = "\'All\'";
} else {
    $stakeNameQryRes = mysql_fetch_array(mysql_query("SELECT stkname FROM stakeholder WHERE stkid = '" . $sel_stk . "' "));
    $stakeholderName = "\'$stakeNameQryRes[stkname]\'";
}
?>

<div onLoad="doInitGrid()">
                <div class="row">
                    <div class="col-md-12">                
                        <table width="100%">
                            
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
                                    <td><div id="mygrid_container" style="width:100%; height:300px;"></div></td>
                                </tr>
                                <?php
                            } else {
                                echo "<tr><td>No record found</td></tr>";
                            }
                            ?>
                        </table>
                  
        </div>
    </div>
    <!-- END FOOTER -->

    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            //mygrid.setHeader("Province,Consumption,AMC,On Hand,MOS,#cspan");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "Province/Region Report for Sector = '" . ucwords($rptType) . "' Stakeholder(s) = $stakeholderName And Product = $prodName (" . date('F', mktime(0, 0, 0, $sel_month)) . ' ' . $sel_year . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No.,Province/Region, Consumption, AMC, Stock On Hand, <div style='text-align:center;'>Month of Stock</dive>,#cspan");
            mygrid.attachFooter("<div style='font-size: 10px;'><?php echo $lastUpdateText; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("60,*,160,160,160,60,40");
            mygrid.setColAlign("center,left,right,right,right,center,center");
            //mygrid.setColSorting("str,int");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
            //mygrid.enableLightMouseNavigation(true);
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
        
    </script>
    <script>
        function functionCall(month, year, prod, sector, stkID, province) {
            window.location = "summary_report_main.php?report_type=d_rep&month_sel=" + month + "&year_sel=" + year + "&prov_sel=" + province + "&sector=" + sector + "&stkid=" + stkID + "&prod_sel=" + prod;
        }
    </script> 
    <!-- END JAVASCRIPTS -->
</div>