<?php

/**
 * Manage Itemso fGroups Action
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

//Initializing variables
$nstkId = 0;
$stkname = "";
// stakeholder group id
$stkgroupid = 0;
$strNewGroupName = "";
$stktype = 0;
//province id
$prov_id = 0;

//Geting hdnstkId
if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    $nstkId = $_REQUEST['hdnstkId'];
}
//Getting hdnToDo
if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    $strDo = $_REQUEST['hdnToDo'];
}

if (isset($_REQUEST['ItemID']) && !empty($_REQUEST['ItemID'])) {
    $ItemID = $_REQUEST['ItemID'];
    $ItemOfGroup->m_GroupID = $nstkId;
    //Delete Item Of Group
    $ItemOfGroup->DeleteItemOfGroup();

    foreach ($ItemID as $arec) {
        $ItemOfGroup->m_ItemID = $arec;
        $ItemOfGroup->m_GroupID = $nstkId;
        //Add Item Of Group
        $ItemOfGroup->AddItemOfGroup();
    }

    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
//Redirecting to ManageItemsofGroups
header("location:ManageItemsofGroups.php");
exit;
?>