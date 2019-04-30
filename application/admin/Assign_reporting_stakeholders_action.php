<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * Manage Sub Admin Action
 * @package Admin
 * 
 * @author     Muhammad Waqas Azeem 
 * @email <waqas@deliver-pk.org>
 * 
 * @version    2.2
 * 
 */
//Including file
include("../includes/classes/AllClasses.php");
//Initializing variables
//strDo
$strDo = "Add";

//wh_id
$wh_id = array('');


if (isset($_REQUEST['stkholders']) && !empty($_REQUEST['stkholders'])) {
    //Getting stkholders
    $stkholders = $_REQUEST['stkholders'];
}
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //Getting Do
    $strDo = $_REQUEST['Do'];
}

if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    //Getting Id
    $nuserId = $_REQUEST['Id'];
}
if (isset($_REQUEST['prov']) && !empty($_REQUEST['prov'])) {
    //Getting Id
    $prov = $_REQUEST['prov'];
}

if (isset($nuserId)) {
    $strSql = "DELETE FROM integrated_stakeholders WHERE main_stk_id = " . $nuserId ." AND province_id = ".$prov;
    $rsSql = mysql_query($strSql) or die("Error delete");
}   
    
if (isset($stkholders)) {
    
    foreach ($stkholders as $stk) {
        $strSql = "INSERT INTO  integrated_stakeholders (province_id ,main_stk_id, sub_stk_id) VALUES (".$prov." , " . $nuserId . ", " . $stk . ")";
        $rsSql = mysql_query($strSql) or die("Error insert");
    }
}
//Redirecting to ManageSubAdmin
header("location:Assign_reporting_stakeholders.php");
exit;
?>