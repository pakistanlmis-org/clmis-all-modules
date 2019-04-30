<?php

/**
 * warehousereport
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required files
include("../../application/includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include('auth.php');

//Getting for data
$ID = $_REQUEST['ID'];
$YYYY = $_REQUEST['YYYY'];
$MM = $_REQUEST['MM'];

//for warehouse report
$query = "SELECT
itminfo_tab.itm_id,
tbl_wh_data.wh_obl_a  as obl_a,
tbl_wh_data.wh_obl_c  as obl_c,
tbl_wh_data.wh_received  as received,
tbl_wh_data.wh_issue_up  as issue_up,
tbl_wh_data.wh_adja  as adja,
tbl_wh_data.wh_adjb  as adjb,
tbl_wh_data.wh_cbl_a as cbl_a,
tbl_wh_data.wh_cbl_c as cbl_c,
tbl_wh_data.w_id as Uploaded
FROM
tbl_wh_data Inner Join itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id";
if (!empty($ID) && !empty($YYYY) && !empty($MM)) {
    $query = $query . " WHERE tbl_wh_data.wh_id ='$ID' and tbl_wh_data.report_year='$YYYY' and tbl_wh_data.report_month='$MM'";

    $rs = mysql_query($query) or die(print mysql_error());
    $rows = array();
    while ($r = mysql_fetch_assoc($rs)) {
        $rows[] = $r;
    }
    //Encode in json
    print json_encode($rows);
} else {
    print "-1";
}

//example: http://localhost/lmis/ws/warehousereport.php?ID=3743&YYYY=2011&MM=5
?>