<?php

/**
 * Manage Item Action
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
$strDo = "Add";
$nstkId = 0;
$itm_name = "";
$itm_type = "";
$itm_category = "";
$qty_carton = 0;
$field_color = "";
$itm_des = "";
$itm_status = "";
$frmindex = 0;
$extra = "";
$stkname = "";
$stkid = 0;
$stkorder = 0;

//Getting hdnstkId
if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    //Getting hdnstkId
    $nstkId = $_REQUEST['hdnstkId'];
}
//Getting hdnToDo
if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    $strDo = $_REQUEST['hdnToDo'];
}
//Getting txtStkName1
if (isset($_REQUEST['txtStkName1']) && !empty($_REQUEST['txtStkName1'])) {
    $itm_name = $_REQUEST['txtStkName1'];
}
//Getting txtStkName2
if (isset($_REQUEST['txtStkName2']) && !empty($_REQUEST['txtStkName2'])) {
    $itm_type = $_REQUEST['txtStkName2'];
}
//Getting txtStkName4
if (isset($_REQUEST['txtStkName4']) && !empty($_REQUEST['txtStkName4'])) {
    $itm_category = $_REQUEST['txtStkName4'];
}
//Getting txtStkName5
if (isset($_REQUEST['txtStkName5']) && !empty($_REQUEST['txtStkName5'])) {
    $field_color = $_REQUEST['txtStkName5'];
}
//Getting txtStkName6
if (isset($_REQUEST['txtStkName6']) && !empty($_REQUEST['txtStkName6'])) {
    $itm_status = $_REQUEST['txtStkName6'];
}
//Getting txtStkName7
if (isset($_REQUEST['txtStkName7']) && !empty($_REQUEST['txtStkName7'])) {
    $itm_des = $_REQUEST['txtStkName7'];
}
//Getting txtStkName8
if (isset($_REQUEST['txtStkName8']) && !empty($_REQUEST['txtStkName8'])) {
    $frmindex = $_REQUEST['txtStkName8'];
}
//Getting generic_name
if (isset($_REQUEST['generic_name']) && !empty($_REQUEST['generic_name'])) {
    $generic_name = $_REQUEST['generic_name'];
}
//Getting stkid
if (isset($_REQUEST['stkid']) && !empty($_REQUEST['stkid'])) {
    $stkid = $_REQUEST['stkid'];
}

//product group id here
//
//Getting select2
if (isset($_REQUEST['select2']) && !empty($_REQUEST['select2'])) {
    $productgroupid = $_REQUEST['select2'];
}

//Filling value in Manage Item objects variables

list($unit, $type) = explode('-', $itm_type);
$objManageItem->m_npkId = $nstkId;
$objManageItem->m_itm_name = $itm_name;
$objManageItem->m_generic_name = $generic_name;
$objManageItem->m_itm_type = $type;
$objManageItem->m_itm_unit = $unit;
$objManageItem->m_itm_category = $itm_category;

$objManageItem->m_qty_carton = $qty_carton;
$objManageItem->m_itm_des = $itm_des;
$objManageItem->m_itm_status = $itm_status;

$objManageItem->m_frmindex = $frmindex;
$objManageItem->m_extra = $extra;

/**
 * 
 * EditManageItem
 * 
 */
if ($strDo == "Edit") {
    $objManageItem->EditManageItem();

    //editing value from stakeholder table
    $objstakeholderitem->m_stk_item = $nstkId;
    $objstakeholderitem->m_stkid = $stkid;
    $objstakeholderitem->Editstkholderitem();

    //editing values from product group table
    $ItemOfGroup->m_ItemID = $nstkId;
    $ItemOfGroup->m_GroupID = $productgroupid;
    $ItemOfGroup->EditItemGroup();

    //setting messages
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
/**
 * 
 * AddManageItem
 * 
 */
if ($strDo == "Add") {

    $itemid = $objManageItem->AddManageItem();


    //calling method to add values in stakeholder item
    $objstakeholderitem->m_stk_item = $itemid;

    $objstakeholderitem->m_stkid = $stkid;
    //Add stakeholder item
    $objstakeholderitem->Addstakeholderitem();

    //calling method to add values in item of groups
    $ItemOfGroup->m_ItemID = $itemid;

    $ItemOfGroup->m_GroupID = $productgroupid;
    $ItemOfGroup->AddItemOfGroup1();

    //setting messages
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

header("location:ManageItems.php");
exit;
?>