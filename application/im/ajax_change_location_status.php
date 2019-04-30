<?php
/**
 * ajax_change_location_status
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including Configuration file
include("../includes/classes/Configuration.inc.php");
//Including db file
include(APP_PATH."includes/classes/db.php");
//change Status
function changeStatus($loc_id, $status) {
	$strSql = "UPDATE placement_config SET status='".$status."' WHERE pk_id=" . $loc_id;
	$rsSql = mysql_query($strSql) or die("Error changeStatus");
	if (mysql_affected_rows()) {
		return true;
	} else {
		return false;
	}
}
//Checking loc_id
if (isset($_POST['loc_id']) && !empty($_POST['loc_id'])) {
    //Getting loc_id
    $loc_id = $_POST['loc_id'];
    $status = $_POST['status'];

    if ($status == 1) {
        $status = 0;
        $button = 'Active';
    } else {
        $status = 1;
        $button = 'Inactive';
    }
    $result = changeStatus($loc_id, $status);
        $array = array(
            'status' => $status,
            'button' => $button
        );
        //Encode in json
    echo json_encode($array);
}