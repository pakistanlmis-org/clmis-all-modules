<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * Manage Warehouse Action
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including file
include("../includes/classes/AllClasses.php");

//Initializing variables
//nwharehouseId
$nwharehouseId = 0;
//npkId
$npkId = 0;
//stkOfficeId
$stkOfficeId = "";
//dist_id
$dist_id = 0;
//prov_id
$prov_id = 0;
//stkid
$stkid = 0;
//wh_type_id
$wh_type_id = "";
//stkname
$stkname = "";
//wh_desc
$wh_desc = "";
//wh_name
$wh_name = "";

if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    //Getting hdnstkId
    $nstkId = $_REQUEST['hdnstkId'];
}
if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    //Getting hdnToDo
    $strDo = $_REQUEST['hdnToDo'];
}
if (isset($_REQUEST['Stakeholders']) && !empty($_REQUEST['Stakeholders'])) {
    //Stakeholders
    $stkid = $_REQUEST['Stakeholders'];
}
if (isset($_REQUEST['StakeholdersOffices']) && !empty($_REQUEST['StakeholdersOffices'])) {
    //Stakeholders Offices
    $stkofficeid = $_REQUEST['StakeholdersOffices'];
}
if (isset($_REQUEST['Provinces']) && !empty($_REQUEST['Provinces'])) {
    //Provinces
    $prov_id = $_REQUEST['Provinces'];
}
if (isset($_REQUEST['districts']) && !empty($_REQUEST['districts'])) {
    //districts
    $dist_id = $_REQUEST['districts'];
}
if (isset($_REQUEST['wh_name']) && !empty($_REQUEST['wh_name'])) {
    //warehouse name
    $wh_name = $_REQUEST['wh_name'];
}
if (isset($_REQUEST['month']) && !empty($_REQUEST['month'])) {
    //month
    $wh_month = $_REQUEST['month'];
}
if (isset($_REQUEST['year']) && !empty($_REQUEST['year'])) {
    //year
    $wh_year = $_REQUEST['year'];
}
if (isset($_REQUEST['wh_type']) && !empty($_REQUEST['wh_type'])) {
    //Warehouse type
    $hf_type_id = $_REQUEST['wh_type'];
}
if (isset($_REQUEST['reporting_start_month']) && !empty($_REQUEST['reporting_start_month'])) {
    //reporting start month
    $reporting_start_month = $_REQUEST['reporting_start_month'];
}
if (isset($_REQUEST['im_date']) && !empty($_REQUEST['im_date'])) {
    //reporting start month
    $im_date = $_REQUEST['im_date'];
}
if (isset($_REQUEST['editable_data_entry_months']) && !empty($_REQUEST['editable_data_entry_months'])) {
    //editable data entry months
    $editable_data_entry_months = $_REQUEST['editable_data_entry_months'];
}
if (isset($_REQUEST['is_lock_data_entry'])) {
    $is_lock_data_entry = $_REQUEST['is_lock_data_entry'];
}
if (isset($_REQUEST['wh_rank'])) {
    //warehose rank
    $wh_rank = $_REQUEST['wh_rank'];
}
if (isset($_REQUEST['hf_code'])) {
    $hf_code = $_REQUEST['hf_code'];
}
if (isset($_REQUEST['is_active'])) {
    $is_active = $_REQUEST['is_active'];
}
if (!empty($_REQUEST['is_allowed_im'])) {
    $is_allowed_im = $_REQUEST['is_allowed_im'];
}
else
    $is_allowed_im=0;

//nstkId
$objwarehouse->m_npkId = $nstkId;
//stkid
$objwarehouse->m_stkid = $stkid;
//stkofficeid
$objwarehouse->m_stkofficeid = $stkofficeid;
//prov_id
$objwarehouse->m_prov_id = $prov_id;
//dist_id
$objwarehouse->m_dist_id = $dist_id;
//wh_name
$objwarehouse->m_wh_name = $wh_name;
//hf_type_id
$objwarehouse->hf_type_id = $hf_type_id;
//reporting_start_month
$objwarehouse->reporting_start_month = $reporting_start_month . '-01';
$objwarehouse->im_date = $im_date . '-01';
//editable_data_entry_months
$objwarehouse->editable_data_entry_months = $editable_data_entry_months;
//is_lock_data_entry
$objwarehouse->is_lock_data_entry = $is_lock_data_entry;
//wh_rank
$objwarehouse->wh_rank = $wh_rank;
//hf_code
$objwarehouse->hf_code = $hf_code;
//is_active
$objwarehouse->is_active = $is_active;
//Enable Inventory Management
$objwarehouse->is_allowed_im = $is_allowed_im;
$objwarehouse->user_id = $_SESSION['user_id'];
//echo '<pre>';print_r($objwarehouse);
if (!empty($wh_rank) && $wh_rank != $_POST['wh_rank_old']) {
    $qry = "UPDATE tbl_warehouse
		SET wh_rank = wh_rank + 1
		WHERE
			dist_id = $dist_id
		AND stkid = stkid
		AND wh_rank >= $wh_rank
		AND wh_rank IS NOT NULL";
    mysql_query($qry);
}

/**
 * 
 * Edit warehouse
 * 
 */
if ($strDo == "Edit") {
    $objwarehouse->Editwarehouse();
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}

/**
 * 
 * Add warehouse
 * 
 */
if ($strDo == "Add") {
    $id = $objwarehouse->Addwarehouse();
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}
header("location:ManageWarehouse.php");
exit;
