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
//nstkId
$nstkId = 0;
//stkid
$stkid = 0;

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

$sysusr_type = '24';

//Filling value in stakeholder objects variables

if (isset($stkholders)) {
    $objuserstk->m_nuserId = $nuserId;
    //Delete stakeholder
    $objuserstk->delete();
    foreach ($stkholders as $stk) {
        $objuserstk->m_nuserId = $nuserId;
        $objuserstk->m_nstkId = $stk;
        $objuserstk->insert();
    }
}
//Redirecting to ManageSubAdmin
header("location:ManageReqAccess.php");
exit;
?>