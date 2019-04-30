<?php
/**
 * wh_info
 * @package reports
 * 
 * @author     Ajmal Hussain 
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses
include("../includes/classes/AllClasses.php");
//Including header
include(PUBLIC_PATH . "html/header.php");
//Initializing variables
$where = "";
$where1 = "";
//Checking month
if (isset($_REQUEST['month']) && $_REQUEST['month'] != "") {
    //Getting month
    $where .=" tbl_wh_data.report_month = " . $_REQUEST['month'] . " ";
    $where1 .="MONTH (tbl_hf_data.reporting_date) = " . $_REQUEST['month'] . " ";
}
//Checking year
if (isset($_REQUEST['year']) && $_REQUEST['year'] != "") {
    //Getting year
    $where .=" AND tbl_wh_data.report_year = " . $_REQUEST['year'] . " ";
    $where1 .=" AND YEAR (tbl_hf_data.reporting_date) = " . $_REQUEST['year'] . " ";
}
//Checking whid
if (isset($_REQUEST['whId']) && $_REQUEST['whId'] != "") {
    //Getting whid
    $where .=" AND tbl_wh_data.wh_id = " . $_REQUEST['whId'] . " ";
    $where1 .=" AND tbl_hf_data.warehouse_id = " . $_REQUEST['whId'] . " ";
}
//This query gets
//tbl_wh_data.w_id,
//wh_name,
//report_month,
//report_year,
//itm_name,
//frmindex,
//wh_obl_a,
//wh_received,
//wh_issue_up,
//wh_adja,
//wh_adjb,
//wh_cbl_a
$query_xmlw = "SELECT * FROM ((SELECT
					tbl_wh_data.w_id,
					CONCAT(tbl_warehouse.wh_name, ' ', IFNULL(tbl_warehouse.wh_type_id, '')) AS wh_name,
					tbl_wh_data.report_month,
					tbl_wh_data.report_year,
					itminfo_tab.itm_name,
					itminfo_tab.frmindex,
					tbl_wh_data.wh_obl_a,
					tbl_wh_data.wh_received,
					tbl_wh_data.wh_issue_up,
					tbl_wh_data.wh_adja,
					tbl_wh_data.wh_adjb,
					tbl_wh_data.wh_cbl_a
				FROM
					tbl_wh_data
				INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
				INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
				WHERE
					$where)
			UNION (
					SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						MONTH (tbl_hf_data.reporting_date) AS report_month,
						YEAR (tbl_hf_data.reporting_date) AS report_year,
						itminfo_tab.itm_name,
						itminfo_tab.frmindex,
						tbl_hf_data.opening_balance AS wh_obl_a,
						tbl_hf_data.received_balance AS wh_received,
						tbl_hf_data.issue_balance AS wh_issue_up,
						tbl_hf_data.adjustment_positive AS wh_adja,
						tbl_hf_data.adjustment_negative AS wh_adjb,
						tbl_hf_data.closing_balance AS wh_cbl_a
					FROM
						tbl_warehouse
					INNER JOIN tbl_hf_data ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
					INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
					WHERE 
						$where1
					AND itminfo_tab.itm_category = 1
				)) A
				ORDER BY A.frmindex ASC";
//Query result
$result_xmlw = mysql_query($query_xmlw);
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 0;
$numOfRows = mysql_num_rows($result_xmlw);
$_SESSION['numOfRows'] = $numOfRows;
if ($numOfRows > 0) {
    while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
        if ($row_xmlw['report_month'] == '1') {
            $month = 'January';
        } else if ($row_xmlw['report_month'] == '2') {
            $month = 'February';
        } else if ($row_xmlw['report_month'] == '3') {
            $month = 'March';
        } else if ($row_xmlw['report_month'] == '4') {
            $month = 'April';
        } else if ($row_xmlw['report_month'] == '5') {
            $month = 'May';
        } else if ($row_xmlw['report_month'] == '6') {
            $month = 'June';
        } else if ($row_xmlw['report_month'] == '7') {
            $month = 'July';
        } else if ($row_xmlw['report_month'] == '8') {
            $month = 'August';
        } else if ($row_xmlw['report_month'] == '9') {
            $month = 'September';
        } else if ($row_xmlw['report_month'] == '10') {
            $month = 'October';
        } else if ($row_xmlw['report_month'] == '11') {
            $month = 'November';
        } else if ($row_xmlw['report_month'] == '12') {
            $month = 'December';
        }

        $temp = "\"$row_xmlw[w_id]\"";
        $xmlstore .="<row id=\"$counter\">";
        //itm_name
        $xmlstore .="<cell>" . $row_xmlw['itm_name'] . "</cell>";
        //wh_name
        $xmlstore .="<cell>" . $row_xmlw['wh_name'] . "</cell>";
        //wh_obl_a
        $xmlstore .="<cell>" . number_format($row_xmlw['wh_obl_a']) . "</cell>";
        //wh_received
        $xmlstore .="<cell>" . number_format($row_xmlw['wh_received']) . "</cell>";
        //wh_issue_up
        $xmlstore .="<cell>" . number_format($row_xmlw['wh_issue_up']) . "</cell>";
        //wh_adja
        $xmlstore .="<cell>" . number_format($row_xmlw['wh_adja']) . "</cell>";
        //wh_adjb
        $xmlstore .="<cell>" . number_format($row_xmlw['wh_adjb']) . "</cell>";
        //wh_cbl_a
        $xmlstore .="<cell>" . number_format($row_xmlw['wh_cbl_a']) . "</cell>";

        $xmlstore .="</row>";
        $counter++;
    }
}
//End xml
$xmlstore .="</rows>";


// Get Warehouse Information
//This query gets
//wh_name
//District
//Province
//stkname
$whInfo = mysql_fetch_array(mysql_query("SELECT
	tbl_warehouse.wh_name,
	District.LocName AS District,
	Province.LocName AS Province,
	stakeholder.stkname
FROM
	tbl_warehouse
INNER JOIN tbl_locations AS District ON tbl_warehouse.dist_id = District.PkLocID
INNER JOIN tbl_locations AS Province ON District.ParentID = Province.PkLocID
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
	tbl_warehouse.wh_id = " . $_REQUEST['whId'] . " "));
$title = date('F', mktime(0, 0, 0, $_REQUEST['month'])) . ' ' . $_REQUEST['year'] . " Report For Warehouse: '" . $whInfo['wh_name'] . "' Stakeholder: '" . $whInfo['stkname'] . "' District: '" . $whInfo['District'] . "' And Province: '" . $whInfo['Province'] . "'";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
        <script>
            var mygrid;
            function doInitGrid() {
                mygrid = new dhtmlXGridObject('mygrid_container');
                mygrid.selMultiRows = true;
                mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
                mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold; font-family:Helvetica'><?php echo $title; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
                mygrid.attachHeader("<span title='Product name'>Product</span>,<span title='Store/Facility name'>Store/Facility</span>,<span title='Opening Balance Calculated'>Opening Balance</span>,<span title='Balance received'>Received</span>,<span title='Balance issued'>Issued</span>,Adjustments,#cspan,<span title='Closing balance'>Closing Balance</span>");
                mygrid.attachHeader("#rspan,#rspan,#rspan,#rspan,#rspan,<div style='text-align:center;'>(+)</div>,<div style='text-align:center;'>(-)</div>,#rspan");
                mygrid.setInitWidths("150,*,110,110,110,60,60,110");
                mygrid.setColAlign("left,left,right,right,right,right,right,right");
                mygrid.setColSorting("str,str");
                mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");
                mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
                mygrid.setSkin("light");
                mygrid.init();
                //mygrid.loadXML("xml/non_report.xml");
                mygrid.clearAll();
                mygrid.loadXMLString('<?php echo $xmlstore; ?>');
            }
        </script>
    </head>
    <body onLoad="doInitGrid();">
        <table width="99%" align="center">
            <tr>
                <td>
                    <div id="mygrid_container" style="width:100%; height:380px;"></div>
                </td>
            </tr>
        </table>
    </body>
</html>