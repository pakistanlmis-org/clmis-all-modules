<?php

/**
 * LastReport
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
    //Query for LastReport
    $query = "SELECT 
( SELECT max(report_year) 
FROM tbl_wh_data 
WHERE tbl_wh_data.wh_id ='$ID') as ryear,
(SELECT max(report_month) as rmonth 
FROM tbl_wh_data 
WHERE tbl_wh_data.wh_id ='$ID' 
and report_year=
( (SELECT max(report_year) as rmonth 
FROM tbl_wh_data 
WHERE tbl_wh_data.wh_id ='$ID') )) as rmonth";
//Query result
    $rs = mysql_query($query) or die(print mysql_error());
    //for query result
    $rows = array();
    while ($r = mysql_fetch_assoc($rs)) {
        //Checking rmonth
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

//example: http://localhost/lmis/ws/LastReport.php?ID=3743
?>