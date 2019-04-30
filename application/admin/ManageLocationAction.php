<?php

/**
 * Manage Location ction
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required file
include("../includes/classes/AllClasses.php");

//Getting loc_level
$loc_level = $_REQUEST['loc_level'];
//Getting provinces
$prov_id = $_REQUEST['provinces'];
//Getting dist_id
$dist_id = isset($_REQUEST['dist_id']) ? $_REQUEST['dist_id'] : '';
//Getting loc_type
$loc_type = $_REQUEST['loc_type'];
//Getting loc_name
$loc_name = $_REQUEST['loc_name'];

if ($loc_level == 3) {
    $parent_id = $prov_id;
} else {
    $parent_id = $dist_id;
}
$objloc->PkLocID = (isset($_REQUEST['hdnstkId'])) ? $_REQUEST['hdnstkId'] : '';
$objloc->LocLvl = $loc_level;
$objloc->LocType = $loc_type;
$objloc->LocName = $loc_name;
$objloc->ParentID = $parent_id;

$strDo = $_REQUEST['hdnToDo'];

/**
 * Edit locations
 */
if ($strDo == "Edit") {
    $objloc->Editlocations();
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
/**
 * Add locations
 */
if ($strDo == "Add") {
    $objloc->Addlocations();
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

//Redirecting to ManageLocations
header("location:ManageLocations.php");
exit;
