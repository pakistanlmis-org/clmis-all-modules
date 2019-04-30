<?php

/**
 * add_action_manufacturer
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
$strDo = "Add";
$nstkId = 0;
$autorun = false;

if (!empty($_REQUEST['new_manufacturer'])) {
    //Getting new_manufacturer
    $new_manufacturer = $_REQUEST['new_manufacturer'];
    $hdnstkId = $_REQUEST['hdnstkId'];
    //Getting itm_id
    $item_pack_size_id = $_REQUEST['itm_id'];
    $brand_name = (!empty($_REQUEST['brand_name'])) ? mysql_real_escape_string($_REQUEST['brand_name']) : '';

    //check manufacturer
    $checkManufacturer = mysql_query("select stkid,stkname from stakeholder where stkname='" . $new_manufacturer . "' AND stk_type_id = 3") or die(mysql_error());
    $manufacturer = mysql_num_rows($checkManufacturer);
    $stkRow = mysql_fetch_assoc($checkManufacturer);
    //if not exist for any product
    if ($manufacturer < 1) {
        // Get Stakeholder Item
        $getStkOrder = "SELECT
							MAX(stakeholder.stkorder) + 1 AS stkorder
						FROM
							stakeholder
						WHERE
							stakeholder.stk_type_id = 3";
        //Query result
        $getStkOrderRes = mysql_fetch_array(mysql_query($getStkOrder));
        $stkOrder = $getStkOrderRes['stkorder'];
        //Assigning data to objstk
        //stkname
        $objstk->m_stkname = $new_manufacturer;
        //stkorder
        $objstk->m_stkorder = $stkOrder;
        //ParentID
        $objstk->ParentID = '1';
        //stk_type_id
        $objstk->m_stk_type_id = '3';
        //level
        $objstk->m_lvl = '1';
        //Add Stakeholder
        $stkid = $objstk->AddStakeholder();
    } else {
        $stkid = $stkRow['stkid'];
    }

    //stkid
    $objstakeholderitem->m_stkid = $stkid;
    //stk_item
    $objstakeholderitem->m_stk_item = $item_pack_size_id;
    //brand_name
    $objstakeholderitem->brand_name = (!empty($_REQUEST['brand_name'])) ? mysql_real_escape_string($_REQUEST['brand_name']) : '';
    //quantity_per_pack
    $objstakeholderitem->quantity_per_pack = (!empty($_REQUEST['quantity_per_pack'])) ? mysql_real_escape_string($_REQUEST['quantity_per_pack']) : '';
    //carton_per_pallet
    $objstakeholderitem->carton_per_pallet = (!empty($_REQUEST['carton_per_pallet'])) ? mysql_real_escape_string($_REQUEST['carton_per_pallet']) : '';
    //gtin
    $objstakeholderitem->gtin = (!empty($_REQUEST['gtin'])) ? mysql_real_escape_string($_REQUEST['gtin']) : '';
    //gross_capacity
    $objstakeholderitem->gross_capacity = $_REQUEST['pack_length'] * $_REQUEST['pack_width'] * $_REQUEST['pack_height'];
    //net_capacity
    $objstakeholderitem->net_capacity = (!empty($_REQUEST['net_capacity'])) ? mysql_real_escape_string($_REQUEST['net_capacity']) : '';
    //
    $objstakeholderitem->pack_length = (!empty($_REQUEST['pack_length'])) ? mysql_real_escape_string($_REQUEST['pack_length']) : '';
    //pack_length
    $objstakeholderitem->pack_width = (!empty($_REQUEST['pack_width'])) ? mysql_real_escape_string($_REQUEST['pack_width']) : '';
    //pack_height
    $objstakeholderitem->pack_height = (!empty($_REQUEST['pack_height'])) ? mysql_real_escape_string($_REQUEST['pack_height']) : '';
    //Add stakeholder item1

    $getStkItem = "select * from stakeholder_item where stk_item=" . $item_pack_size_id . " AND stkid=" . $stkid . " AND brand_name = '" . $brand_name . "' ";
    $resStkItem = mysql_query($getStkItem) or die(mysql_error());
    $numStkItem = mysql_num_rows($resStkItem);
    if ($numStkItem == 0) {
        $stkItemId = $objstakeholderitem->Addstakeholderitem1();
    } else if (!empty($hdnstkId)){
        $objstakeholderitem->m_npkId = $hdnstkId;
        $stkItemId = $objstakeholderitem->UpdateStakeholderItem();

        $_SESSION['err']['text'] = 'Data has been successfully updated.';

        $_SESSION['err']['type'] = 'success';
    }
}

redirect("ManageManufacturers.php");
exit;
?>