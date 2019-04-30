<?php

/**
 * heartbeat
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */


include("../../application/includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include('auth.php');

/**
 * Get Product Name
 * 
 * @return type
 */
function getProdName() {
    $query = "SELECT itm_name FROM itminfo_tab LIMIT 1";
    $rs = mysql_query($query) or die(mysql_error());
    return $rs;
}

$result = getProdName();
$row = mysql_fetch_array($result);
if (strlen($row[0]) > 0) {
    echo 1;
} else {
    echo 0;
}
?>