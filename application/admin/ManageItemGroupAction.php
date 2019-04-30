<?php

/**
 * Manage Item Group Action
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
$ItemGroupName = "";

//Getting hdnstkId
if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    $nstkId = $_REQUEST['hdnstkId'];
}
//Getting hdnToDo
if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    $strDo = $_REQUEST['hdnToDo'];
}
//Getting ItemGroupName
if (isset($_REQUEST['ItemGroupName']) && !empty($_REQUEST['ItemGroupName'])) {
    $ItemGroupName = $_REQUEST['ItemGroupName'];
}

//Filling value in ItemGroup objects variables
$ItemGroup->m_ItemGroupName = $ItemGroupName;
$ItemGroup->m_npkId = $nstkId;

/**
 * EditItemGroup
 */
if ($strDo == "Edit") {
    $ItemGroup->EditItemGroup();

    //setting messages
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}

/**
 * AddItemGroup
 */
if ($strDo == "Add") {
    $ItemGroup->AddItemGroup();

    //setting messages
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

/**
 * DeleteItemGroup
 */
if ($strDo == "Delete") {
    $ItemGroup->DeleteItemGroup();
}

//Redirecting to the ManageItemsGroups
header("location:ManageItemsGroups.php");
exit;
?>