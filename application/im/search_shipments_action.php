<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * search_shipments_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
$strSql = "SELECT
            itminfo_tab.itm_id,
            itminfo_tab.itm_name,
            itminfo_tab.itm_type,
            shipments.*
            FROM
            itminfo_tab
            INNER JOIN shipments ON shipments.item_id = itminfo_tab.itm_id
            WHERE
            shipments.pk_id  = ".$_REQUEST['shipment_id'] ;
//query result
$rsSql = mysql_query($strSql) or die("Error GetProduct data");
$prev_data = array();
if (mysql_num_rows($rsSql) > 0) {
    $prev_data = mysql_fetch_assoc($rsSql);
}

if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'Edit')
{
    $sql = "UPDATE shipments SET ";
    $sql .= " shipment_date = '".$_REQUEST['receive_date']."', ";
    $sql .= " reference_number = '".$_REQUEST['refrence_number']."', ";
    $sql .= " stk_id = '".$_REQUEST['receive_from']."', ";
    $sql .= " shipment_quantity = '".$_REQUEST['qty']."', ";
    $sql .= " `status` = '".$_REQUEST['status']."' ";
    $sql .= "WHERE pk_id=" . $_REQUEST['shipment_id'];
   // echo $sql;exit;
    mysql_query($sql);
    
    $subject = 'Shipment Details Updated in cLMIS';
    $message = '
            <html>
            <head>
              <title>Shipment Details Were Updated </title>
            </head>
            <body>
              <h4>Following Shipment Details Were Updated:</h4>
              
              <p>This is to notify you that shipment of reference number : '.$prev_data['reference_number'].' was updated. </p>
              ';
    
    
    if($prev_data['shipment_date'] != $_REQUEST['receive_date'] )
        $message .=' <p>Shipment Arrival Date : '.$prev_data['shipment_date'].' was updated to : '.$_REQUEST['receive_date'].' </p>';
    
    if($prev_data['reference_number'] != $_REQUEST['refrence_number'] )
        $message .=' <p>Reference Number : '.$prev_data['reference_number'].' was updated to : '.$_REQUEST['refrence_number'].' </p>';
    
    if($prev_data['stk_id'] != $_REQUEST['receive_from'] )
        $message .=' <p>Funding Source : '.$objwarehouse->GetWHByWHId($prev_data['stk_id']).' was updated to : '.$objwarehouse->GetWHByWHId($_REQUEST['receive_from']).' </p>';
    
    if($prev_data['shipment_quantity'] != $_REQUEST['qty'] )
        $message .=' <p>Quantity of shipment : '.$prev_data['shipment_quantity'].' was updated to : '.$_REQUEST['qty'].' </p>';
    
    if($prev_data['status'] != $_REQUEST['status'] )
        $message .=' <p>Status of shipment : '.$prev_data['status'].' was updated to : '.$_REQUEST['status'].' </p>';
    
    $message .=' 
              <p>Thanks</p>
            </body>
            </html>
            ';
}
elseif(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'cancel')
{
    $sql = "UPDATE shipments SET status = 'Cancelled'  WHERE pk_id=" . $_REQUEST['shipment_id'];
    mysql_query($sql);
    
    $subject = 'Shipment Cancelled in cLMIS';
    $message = '
            <html>
            <head>
              <title>Shipment Cancelled</title>
            </head>
            <body>
              <h4>The following shipment was just cancelled:</h4>
              <p>This is to notify you that shipment of reference number : '.$prev_data['reference_number'].' having : </p>
              <p>'.$prev_data['shipment_quantity'].' '.$prev_data['itm_type'].' of '.$prev_data['itm_name'].' was cancelled. </p>
              <p>Thanks</p>
            </body>
            </html>
            ';
}

$to      = 'muhahmed@ghsc-psm.org,jakram@ghsc-psm.org';
$headers=array();
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=iso-8859-1';
$headers[] = 'From: No Reply <feedback@ghsc-psm.org>';

mail($to, $subject, $message, implode("\r\n", $headers));
    
    
header("location:search-shipments.php");
exit;
?>