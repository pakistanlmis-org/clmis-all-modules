<?php

/**
 * Manage Stakeholder Type Action
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
$StakeholderTypeName = "";

if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    //getting hdnstkId
    $nstkId = $_REQUEST['hdnstkId'];
}

if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    //getting hdnToDo
    $strDo = $_REQUEST['hdnToDo'];
}

if (isset($_REQUEST['StakeholderTypeName']) && !empty($_REQUEST['StakeholderTypeName'])) {
    //Stakeholder Type Name
    $StakeholderTypeName = $_REQUEST['StakeholderTypeName'];
}

//Filling value in stakeholder objects variables

$StakeholderType->m_StakeholderTypeDescription = $StakeholderTypeName;
$StakeholderType->m_npkId = $nstkId;

/**
 * 
 * Edit Stakeholder Type
 * 
 */
if ($strDo == "Edit") {
    $StakeholderType->EditStakeholderType();

    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}

/**
 * 
 * Add Stakeholder Type
 * 
 */
if ($strDo == "Add") {

    $StakeholderType->AddStakeholderType();

    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

/**
 * 
 * Delete Stakeholder Type
 * 
 */
if ($strDo == "Delete") {
    $StakeholderType->DeleteStakeholderType();
}

header("location:ManageStakeholderType.php");
exit;
?>