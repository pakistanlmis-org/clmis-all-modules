<?php
/**
 * placement_location_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("includes/AllClasses.php");

$strDo = "Add";
$nstkId = 0;
$remarks = '';
//check area
if (isset($_REQUEST['area']) && !empty($_REQUEST['area'])) {
    //get area
    $area = $_REQUEST['area'];
}
//check level
if (isset($_REQUEST['level']) && !empty($_REQUEST['level'])) {
    //get level
    $level = $_REQUEST['level'];
}
//get user_warehouse
$wh_id = $_SESSION['user_warehouse'];

$getLocationStatus=mysql_query("select * from placement_config where area=".$area." AND level=".$level."") or die("Err Ger Location Status");

header("location:placement_locations.php");
exit;
?>