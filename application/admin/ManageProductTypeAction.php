<?php

/**
 * Manage Product Type Action
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../includes/classes/AllClasses.php");

//Inializing variables
$nstkId = 0;
$ItemGroupName = "";

//Getting form data
if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    $m_npkId = $_REQUEST['hdnstkId'];
}

if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    $strDo = $_REQUEST['hdnToDo'];
}

if (isset($_REQUEST['producttype']) && !empty($_REQUEST['producttype'])) {
    //Product type
    $m_unit_type = $_REQUEST['producttype'];
}

//Filling value in ItemUnits objects variables

$objItemUnits->m_unit_type = $m_unit_type;
$objItemUnits->m_npkId = $m_npkId;

/**
 * Edit Item Unit
 */
if ($strDo == "Edit") {
    $objItemUnits->EditItemUnit();

    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
/**
 * Add Item Unit
 */
if ($strDo == "Add") {

    $objItemUnits->AddItemUnit();

    $_SESSION['err']['text'] = 'Data has been successfully addded.';
    $_SESSION['err']['type'] = 'success';
}

/**
 * Delete Item Unit
 */
if ($strDo == "Delete") {
    $objItemUnits->DeleteItemUnit();
}

header("location:ManageProductType.php");
exit;
?>