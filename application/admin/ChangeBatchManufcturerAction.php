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

if (isset($_REQUEST['hdnbatchId']) && !empty($_REQUEST['hdnbatchId'])) {
    //getting hdnToDo
    $nbatchId = $_REQUEST['hdnbatchId'];
}

if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    $strDo = $_REQUEST['hdnToDo'];
}

if (isset($_REQUEST['Manufacturer']) && !empty($_REQUEST['Manufacturer'])) {
    //getting Health Facility Type Name
    $Manufacturer = $_REQUEST['Manufacturer'];
}

/**
 * 
 * Edit Health Facility Type
 * 
 */
if ($strDo == "Update") {
    $objStockBatch->UpdateBatchManufacturer($nbatchId,$Manufacturer);

    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}

//Redirecting to ManageHealthFacilityType
header("location:ManageBatches.php");
exit;
?>