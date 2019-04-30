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
include("../../includes/classes/AllClasses.php");

//Initializing variables
//Getting hdnstkId
if (isset($_REQUEST['Sources']) && !empty($_REQUEST['Sources'])) {
    $nSources = $_REQUEST['Sources'];
} else {
    $_SESSION['err']['text'] = 'Please select data source name';
    $_SESSION['err']['type'] = 'error';
    header("location:index.php");
    exit;
}
//Getting hdnToDo
if (isset($_REQUEST['Provinces']) && !empty($_REQUEST['Provinces'])) {
    $nProvinces = $_REQUEST['Provinces'];
} else {
    $_SESSION['err']['text'] = 'Please select province';
    $_SESSION['err']['type'] = 'error';
    header("location:index.php");
    exit;
}
//Getting ItemGroupName
if (isset($_REQUEST['Stakeholders']) && !empty($_REQUEST['Stakeholders'])) {
    $nStakeholders = $_REQUEST['Stakeholders'];
} else {
    $_SESSION['err']['text'] = 'Please select stakeholder';
    $_SESSION['err']['type'] = 'error';
    header("location:index.php");
    exit;
}
//Getting hdnToDo
if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    $strDo = $_REQUEST['hdnToDo'];
}

//Getting hdnToDo
if (isset($_REQUEST['pk_id']) && !empty($_REQUEST['pk_id'])) {
    $pk_id = $_REQUEST['pk_id'];
}

$lvl = $_REQUEST['lvl'];
/**
 * AddItemGroup
 */
if ($strDo == "Add") {
    mysql_query("INSERT INTO stock_sources(stock_source_id,
stakeholder_id,
province_id,
created_date,
created_by,
modified_by, lvl) VALUES ('" . $nSources . "','" . $nStakeholders . "','" . $nProvinces . "','" . date("Y-m-d h:i:s") . "',1,1,$lvl)");

    //setting messages
    $_SESSION['err']['text'] = 'Data has been added successfully.';
    $_SESSION['err']['type'] = 'success';
}

if ($strDo == "Edit") {
       mysql_query("UPDATE stock_sources SET stock_source_id = '" . $nSources . "',
stakeholder_id = '" . $nStakeholders . "',
province_id= '" . $nProvinces . "', lvl = $lvl WHERE pk_id = $pk_id");

    $_SESSION['err']['text'] = 'Data has been updated successfully.';
    $_SESSION['err']['type'] = 'success';
}

//Redirecting to the ManageItemsGroups
header("location:index.php");
exit;
?>