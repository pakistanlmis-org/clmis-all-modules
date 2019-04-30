<?php

/**
 * logins-WH
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


$InLgID = $_REQUEST['ID'];

//Checking credentials
if (!empty($InLgID)) {
    $EnLgPW = base64_encode($InLgPW);
    $query = "SELECT
				wh_user.wh_id
				FROM
				wh_user
				where sysusrrec_id='$InLgID'";
    $rs = mysql_query($query) or die(print mysql_error());
    $rows = array();
    while ($r = mysql_fetch_assoc($rs)) {
        $rows[] = $r;
    }
    print json_encode($rows);
} else {
    print "-1";
}

// http://localhost/lmisn/ws/loginsWH.php?ID=164
?>

