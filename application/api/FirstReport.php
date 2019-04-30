<?php

/**
 * FirstReport
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
//Getting ID
$ID = $_REQUEST['ID'];
if (!empty($ID)) {
    //Query for FirstReport
    $query = "SELECT 
( SELECT min(report_year) 
FROM tbl_wh_data 
WHERE tbl_wh_data.wh_id ='$ID') as ryear,
(SELECT min(report_month) as rmonth 
FROM tbl_wh_data 
WHERE tbl_wh_data.wh_id ='$ID' 
and report_year=
( (SELECT min(report_year) as rmonth 
FROM tbl_wh_data 
WHERE tbl_wh_data.wh_id ='$ID') )) as rmonth";
//Query result
    $rs = mysql_query($query) or die(print mysql_error());
    //for query result
    $rows = array();
    while ($r = mysql_fetch_assoc($rs)) {
        if (empty($r['rmonth'])) {
            $m = "1";
        } else {
            $m = $r['rmonth'];
        }


        if (empty($r['ryear'])) {
            $y = "1991";
        } else {
            $y = $r['ryear'];
        }

        print $m . "/1/" . $y . " 12:00:00 AM";
    }
} else {
    print "1/1/1990";
}
//just for
//example: http://localhost/lmis/ws/warehousereport.php?ID=3743&YYYY=2011&MM=5
?>