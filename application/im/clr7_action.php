<?php
//echo '<pre>';print_r($_REQUEST);exit;
//Including AllClasses file
include("../includes/classes/AllClasses.php");

if (!empty($_REQUEST['id'])) {
    //Getting clr6_id
    $clr_master_id = $_REQUEST['id'];
}
$product = $_REQUEST['product'];
if(!empty($product))
{
    foreach($product as $itm_id)
    {
         $qry = "UPDATE clr_details SET 
                received_by_consignee   = '".floatval(str_replace(',','',$_REQUEST['received_by_consignee'][$itm_id]))."',
                var_req_n_disp          = '".floatval(str_replace(',','',$_REQUEST['var_req_n_disp'][$itm_id]))."',
                var_disp_n_rec          = '".floatval(str_replace(',','',$_REQUEST['var_disp_n_rec'][$itm_id]))."',
                remarks_clr7            = '".$_REQUEST['remarks_clr7'][$itm_id]."'
                WHERE pk_master_id=" . $clr_master_id . " AND itm_id ='".$itm_id."'  ";
        mysql_query($qry);
    }
}
header("location: " . APP_URL . "im/clr7_view.php?id=".$clr_master_id."&wh_id=".$_REQUEST['wh_id']);
exit;
?>