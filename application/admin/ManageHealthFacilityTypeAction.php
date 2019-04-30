<?php

/**
 * Manage Health Facility Type Action
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required files
include("../includes/classes/AllClasses.php");


//Initiailizing variables
$nstkId = 0;
$stkid = 0;
$HealthFacilityTypeName = "";
$rank = 0;

if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    //getting hdnToDo
    $nstkId = $_REQUEST['hdnstkId'];
}

if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    $strDo = $_REQUEST['hdnToDo'];
}

if (isset($_REQUEST['HealthFacilityTypeName']) && !empty($_REQUEST['HealthFacilityTypeName'])) {
    //getting Health Facility Type Name
    $HealthFacilityTypeName = $_REQUEST['HealthFacilityTypeName'];
}
if (isset($_REQUEST['select']) && !empty($_REQUEST['select'])) {
    // 1 stakeholder
    $stkid = $_REQUEST['select'];
} else {
    $stkid = 0;
}
if (isset($_REQUEST['HealthFacilityRank']) && !empty($_REQUEST['HealthFacilityRank'])) {
    //getting Health Facility Rank
    $rank = $_REQUEST['HealthFacilityRank'];
}

//Filling value in HealthFacilityType objects variables

$HealthFacilityType->m_HealthFacilityTypeDescription = $HealthFacilityTypeName;
$HealthFacilityType->m_npkId = $nstkId;
$HealthFacilityType->m_StakeholderTypeID = $stkid;
$HealthFacilityType->m_HealthFacilityRank = $rank;

/**
 * 
 * Edit Health Facility Type
 * 
 */
if ($strDo == "Edit") {
    $HealthFacilityType->EditHealthFacilityType();

    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}

/**
 * 
 * Add Health Facility Type
 * 
 */
if ($strDo == "Add") {

    $HealthFacilityType->AddHealthFacilityType();

    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

//Redirecting to ManageHealthFacilityType
header("location:ManageHealthFacilityType.php");
exit;
?>