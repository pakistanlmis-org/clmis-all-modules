<?php

/**
 * placement_locations_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");

$strDo = "Add";
$nstkId = 0;
$remarks = '';
//check id
if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    //get id
    $update = $_REQUEST['id'];
}
//check area
if (isset($_REQUEST['area']) && !empty($_REQUEST['area'])) {
    //get area
    $area = $_REQUEST['area'];
}
//check row
if (isset($_REQUEST['row']) && !empty($_REQUEST['row'])) {
    //get row
    $row = $_REQUEST['row'];
}
//check rack
if (isset($_REQUEST['rack']) && !empty($_REQUEST['rack'])) {
    //get rack
    $rack = $_REQUEST['rack'];
}
//check rack_type
if (isset($_REQUEST['rack_type']) && !empty($_REQUEST['rack_type'])) {
    //get rack_type
    $rack_type = $_REQUEST['rack_type'];
}
//check pallet
if (isset($_REQUEST['pallet']) && !empty($_REQUEST['pallet'])) {
    //get pallet
    $pallet = $_REQUEST['pallet'];
}
//check level
if (isset($_REQUEST['level']) && !empty($_REQUEST['level'])) {
    //get level
    $level = $_REQUEST['level'];
}
//get user_warehouse
$wh_id = $_SESSION['user_warehouse'];
//get List Master
$getListMaster = mysql_query("select pk_id,list_master_name from list_master") or die("ERR list master");
while ($resListMaster = mysql_fetch_assoc($getListMaster)) {
    $getDetail = mysql_query("select pk_id,list_value from list_detail where list_master_id=" . $resListMaster['pk_id']) or die(mysql_error());
    while ($resListDetail = mysql_fetch_assoc($getDetail)) {
        if ($resListDetail['pk_id'] == $area) {
            $locNameArr[] = $resListDetail['list_value'];
        }
        if ($resListDetail['pk_id'] == $row) {
            $locNameArr[] = $resListDetail['list_value'];
        }
        if ($resListDetail['pk_id'] == $rack) {
            $locNameArr[] = $resListDetail['list_value'];
        }
        if ($resListDetail['pk_id'] == $pallet) {
            $locNameArr[] = $resListDetail['list_value'];
        }
        if ($resListDetail['pk_id'] == $level) {
            $locNameArr[] = $resListDetail['list_value'];
        }
    }
}
$locName = implode('', $locNameArr);
if ($update) {
    //add Placement Location
    $addPlacementLocation = mysql_query("update placement_config set location_name='" . $locName . "',warehouse_id=" . $wh_id . ",rack_information_id=" . $rack_type . ",area=" . $area . ",row=" . $row . ",rack=" . $rack . ",pallet=" . $pallet . ",level=" . $level . " where pk_id=" . $update);
} else {
    // Check location name if already exists
    $qry = "SELECT
				placement_config.location_name
			FROM
				placement_config
			WHERE
				placement_config.location_name = '" . $locName . "'
			AND placement_config.warehouse_id=" . $wh_id . "";
    $num = (mysql_num_rows(mysql_query($qry)));
    if ($num == 0) {
        //add Placement Location
        $addPlacementLocation = mysql_query("INSERT INTO placement_config set location_name='" . $locName . "',warehouse_id=" . $wh_id . ",rack_information_id=" . $rack_type . ",area=" . $area . ",row=" . $row . ",rack=" . $rack . ",pallet=" . $pallet . ",level=" . $level . "");
        $_SESSION['success'] = 2;
    } else {
        $_SESSION['success'] = 1;
    }
}
//redirect to placement_locations
header("location: placement_locations.php");
exit;
?>