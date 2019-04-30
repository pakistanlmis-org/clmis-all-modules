<?php

/**
 * Manage Product Category Action
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

$nstkId = 0;
$CategoryGroupName = "";

//Getting hdnstkId
if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    $nstkId = $_REQUEST['hdnstkId'];
}
//Getting hdnToDo
if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    $strDo = $_REQUEST['hdnToDo'];
}
//Getting productcategory
if (isset($_REQUEST['productcategory']) && !empty($_REQUEST['productcategory'])) {
    $productcategory = $_REQUEST['productcategory'];
}

//Filling value in itemcategory objects variables

$objitemcategory->m_ItemCategoryName = $productcategory;
$objitemcategory->m_npkId = $nstkId;

/**
 * Edit Item Category 
 */
if ($strDo == "Edit") {
    $objitemcategory->EditItemCategory();

    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
/**
 * Add Item Category
 */
if ($strDo == "Add") {

    $objitemcategory->AddItemCategory();

    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

/**
 * Delete Item Category
 */
if ($strDo == "Delete") {
    $objitemcategory->DeleteItemCategory();
}

//Redirecting to ManageProductCategory
header("location:ManageProductCategory.php");
exit;
?>