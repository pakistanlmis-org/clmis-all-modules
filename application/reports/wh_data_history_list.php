<?php
/**
 * wh_data_history_list
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
    $month=$_REQUEST['month'];
}
//Checking year
if (isset($_REQUEST['year']) && $_REQUEST['year'] != "") {
    //Getting year    
    $year=$_REQUEST['year'];
}
//Checking whid
if (isset($_REQUEST['whId']) && $_REQUEST['whId'] != "") {
    //Getting whid
    $wh_id=$_REQUEST['whId'];
}

$query_xmlw = "SELECT  
                    tbl_warehouse.wh_name,
                    tbl_wh_update_history.wh_id,
                    tbl_wh_update_history.reporting_date,
                    tbl_wh_update_history.update_on,
                    tbl_wh_update_history.updated_by,
                    tbl_wh_update_history.ip_address
                FROM
                    tbl_wh_update_history
                        INNER JOIN tbl_warehouse ON tbl_wh_update_history.wh_id = tbl_warehouse.wh_id
                WHERE
                    tbl_wh_update_history.wh_id = $wh_id
                    AND MONTH (tbl_wh_update_history.reporting_date) = $month
                    AND YEAR (tbl_wh_update_history.reporting_date) = $year
                   
                ORDER BY
                    update_on desc";
//Query result
//echo $query_xmlw;
$result_xmlw = mysql_query($query_xmlw);
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 0;
$numOfRows = mysql_num_rows($result_xmlw);
//$_SESSION['numOfRows'] = $numOfRows;
if ($numOfRows > 0) {
    while ($row_xmlw = mysql_fetch_array($result_xmlw)) {

        $xmlstore .="<row id=\"$counter\">";
        //itm_name
        $xmlstore .="<cell>" . ++$counter . "</cell>";
        $xmlstore .="<cell>" . $row_xmlw['wh_name'] . "</cell>";
        //wh_name
        $xmlstore .="<cell>" . date('M Y',strtotime($row_xmlw['reporting_date'])) . "</cell>";
        //wh_obl_a
        $xmlstore .="<cell>" . date('Y-m-d h:i:s A',strtotime($row_xmlw['update_on'])) . "</cell>";
        //wh_received
        $xmlstore .="<cell>" . $row_xmlw['ip_address'] . "</cell>";
        $xmlstore .="</row>";
        //$counter++;
    }
}
//End xml
$xmlstore .="</rows>";

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
                mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold; font-family:Helvetica'>Update History</div>,#cspan,#cspan,#cspan,#cspan");
                mygrid.attachHeader("<span title='Sr No'>Sr No</span>,<span title='Store/Facility name'>Store/Facility</span>,<span title='Data Entry Month'>Data Entry Month</span>,<span title='Update Time'>Update Time</span>,<span title='IP Address'>IP Address</span>");
                mygrid.attachHeader("#rspan,#rspan,#rspan,#rspan,#rspan");
                mygrid.setInitWidths("50,150,150,190,150");
                mygrid.setColAlign("left,left,right,right,right");
                mygrid.setColSorting("str,str");
                mygrid.setColTypes("ro,ro,ro,ro,ro");
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