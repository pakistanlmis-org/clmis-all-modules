<?php

/**
 * Manage Product Status Action
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../includes/classes/AllClasses.php");

$nstkId = 0;
$statusGroupName = "";
//Getting hdnstkId
if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    $nstkId = $_REQUEST['hdnstkId'];
}
//Getting hdnToDo
if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    $strDo = $_REQUEST['hdnToDo'];
}
//Getting productstatus
if (isset($_REQUEST['productstatus']) && !empty($_REQUEST['productstatus'])) {
    $productstatus = $_REQUEST['productstatus'];
}

//Filling value in itemstatus objects variables

$objitemstatus->m_ItemStatusName = $productstatus;
$objitemstatus->m_npkId = $nstkId;

/**
 * Edit Item Status
 */
if ($strDo == "Edit") {
    $objitemstatus->EditItemStatus();

    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
/**
 * Add Item Status
 */
if ($strDo == "Add") {

    $objitemstatus->AddItemStatus();

    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

/**
 * Delete Item Status
 */
if ($strDo == "Delete") {
    $objitemstatus->DeleteItemStatus();
}

header("location:ManageProductStatus.php");
exit;
?>