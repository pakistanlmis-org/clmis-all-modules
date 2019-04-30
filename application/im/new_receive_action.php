<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * new_receive_action
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

$strDo = "Add";
$nstkId = 0;
//remarks
$remarks = '';
//initialize 
$prod_date = $unit_price = '';
// Make data parmanent when user click on save button
//check stock id
if (isset($_REQUEST['stockid']) && !empty($_REQUEST['stockid'])) {
    ?>
    <!--<script language="javascript">
        var ref_no, rec_no, rec_date, unit_pric, vvm_type, rec_from;
        ref_no = $('#receive_ref').val();
        rec_no = $('#receive_no').val();
        rec_date = $('#receive_date').val();
        unit_pric = $('#unit_price').val();
        vvm_type = $('#vvmtype').val();
        rec_from = $('#receive_from').val();
        window.open('vaccine_placement_details.php?rec_no=' + rec_no + '&ref_no=' + ref_no + '&rec_date=' + rec_date + '&unit_pric=' + unit_pric + '&vvm_type=' + vvm_type + '&rec_from=' + rec_from, '_blank', 'width=842,height=595');
    </script>-->
    <?php
    //get stock id
	$stockid = $_REQUEST['stockid'];
        //updste stock master
    $objStockMaster->updateTemp($stockid);
    //update stock detail
    $objStockDetail->updateTemp($stockid);

    //Save Data in WH data table
   	//$objWhData->addReport($stockid, 1);
   	$objWhData->addReport_for_dist_wh($stockid, 1);
	$_SESSION['success'] = 1;
        
     
    //sending email start ----------------------------
   
    $proc_by_arr = array();
    $shipment_stk_arr = array();
    if($_SERVER['SERVER_ADDR'] == '::1' || $_SERVER['SERVER_ADDR'] == 'localhost'  || $_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == 'beta.lmis.gov.pk') 
    {
        //$subject = 'Stock Received in beta.lmis.gov.pk/clmisapp';
        $shipment_stk_arr[] = "'all'";
        $proc_by_arr[] = '10';
    }
    else
    {
        //$subject = 'Stock Received in cLMIS - c.lmis.gov.pk';
        $stk_qry = "SELECT
                   funding_stk_prov.stakeholder_id,funding_stk_prov.province_id,funding_stk_prov.funding_source_id
               FROM
                   funding_stk_prov
               WHERE
                   funding_stk_prov.funding_source_id = $shipment_detail->stk_id AND
                   funding_stk_prov.province_id = $shipment_detail->procured_by ";
        $res_stk= mysql_query($stk_qry);
        while($row = mysql_fetch_assoc($res_stk))
        {
            $shipment_stk_arr[] = "'".$row['stakeholder_id']."'";
        }
        $shipment_stk_arr[] = "'all'";
        $proc_by_arr[] = $shipment_detail->procured_by;
        $proc_by_arr[] = '10';
    }
    
    
    $to_list = $cc_list = array();
    $emails_qry = "SELECT
                        email_persons_list.pk_id,
                        email_persons_list.person_name,
                        email_persons_list.email_address,
                        email_persons_list.cell_number,
                        email_persons_list.office_name,
                        email_persons_list.stkid,
                        email_persons_list.prov_id
                    FROM
                        email_persons_list
                        INNER JOIN email_bridge ON email_persons_list.pk_id = email_bridge.person_id
                    WHERE
                        email_bridge.action_id = 2 AND
                        email_persons_list.stkid IN (".implode(',',$shipment_stk_arr).") AND
                        email_persons_list.prov_id IN (".implode(',',$proc_by_arr).")";
    //echo $emails_qry;exit;
    $res_email= mysql_query($emails_qry);
    while($row = mysql_fetch_assoc($res_email))
    {
        $office_name = $row['office_name'];
        if($office_name == 'Government'){
            $to_list[] = $row['email_address'];
            $to_list_sms[] = $row['cell_number'];
        } else {
            $cc_list[] = $row['email_address'];
            $cc_list_sms[] = $row['cell_number'];
        }
    }
    $to = implode(',', $to_list);
    $cc = implode(',', $cc_list);
    //echo '<pre>';print_r($to_list);print_r($to);exit;
    
  
    $rcvFrom = ' Stock ';
    ob_start();
    include('receive_voucher_email.php');
    $message=ob_get_contents(); 
    ob_end_clean();
   
    $subject = "Stock Receive Alert :$whName[0] For:".$rec_from;
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
    $headers .= "From: feedback@lmis.gov.pk" . "\r\n" .
    "Reply-To: feedback@lmis.gov.pk" . "\r\n" .
            "Cc: $cc" . "\r\n" .
    "X-Mailer: PHP/" . phpversion();
    mail($to, $subject, $message,  $headers);
    //end of mail -------------------    
      
    mysql_query("INSERT INTO alerts_log (
alerts_log.`to`,
alerts_log.cc,
alerts_log.`subject`,
alerts_log.body,
alerts_log.response,alerts_log.type,alerts_log.interface ) VALUES ('$to','$cc','$subject','$message','$response','Email','IssuecLMIS')");


//$url = "http://cbs.zong.com.pk/reachcwsv2/corporatesms.svc?wsdl";
//$client = new SoapClient($url, array("trace" => 1, "exception" => 0));
//$username = '923125154792';
//$password = '38917466';
//
//if (count($to_list_sms) > 0) {
//    foreach ($to_list_sms as $to) {
//        $resultQuick = $client->QuickSMS(
//                array('obj_QuickSMS' =>
//                    array('loginId' => $username,
//                        'loginPassword' => $password,
//                        'Destination' => $to,
//                        'Mask' => 'LMIS Alert',
//                        'Message' => $message,
//                        'UniCode' => 0,
//                        'ShortCodePrefered' => 'n'
//                    )
//                )
//        );
//
//        $response = $resultQuick->QuickSMSResult;
//
//        mysql_query("INSERT INTO alerts_log (
//alerts_log.`to`,
//alerts_log.`subject`,
//alerts_log.body,
//alerts_log.response, alerts_log.type, alerts_log.interface) VALUES ('$cc','LMIS Alert','$message','$response','SMS','IssuecLMIS')");
//    }
//}
//
//if (count($cc_list_sms) > 0) {
//    foreach ($cc_list_sms as $cc) {
//        $resultQuick = $client->QuickSMS(
//                array('obj_QuickSMS' =>
//                    array('loginId' => $username,
//                        'loginPassword' => $password,
//                        'Destination' => $cc,
//                        'Mask' => 'LMIS Alert',
//                        'Message' => $message,
//                        'UniCode' => 0,
//                        'ShortCodePrefered' => 'n'
//                    )
//                )
//        );
//
//        $response = $resultQuick->QuickSMSResult;
//
//        mysql_query("INSERT INTO alerts_log (
//alerts_log.`to`,
//alerts_log.`subject`,
//alerts_log.body,
//alerts_log.response, alerts_log.type, alerts_log.interface) VALUES ('$cc','LMIS Alert','$message','$response','SMS','IssuecLMIS')");
//    }
//}
        
        
    redirect("new_receive.php");
    exit;
}
// End save button
//check transaction number
if (isset($_REQUEST['trans_no']) && !empty($_REQUEST['trans_no'])) {
    //get transaction number
    $trans_no = $_REQUEST['trans_no'];
}
//check stock id
if (isset($_REQUEST['stock_id']) && !empty($_REQUEST['stock_id'])) {
    //get stock id 
    $stock_id = $_REQUEST['stock_id'];
}
//check receive date
if (isset($_REQUEST['receive_date']) && !empty($_REQUEST['receive_date'])) {
    //get receive date
    $receive_date = $_REQUEST['receive_date'];
}
//check receive ref
if (isset($_REQUEST['receive_ref']) && !empty($_REQUEST['receive_ref'])) {
    //get receive ref
    $receive_ref = $_REQUEST['receive_ref'];
}
//check receive from
if (isset($_REQUEST['receive_from']) && !empty($_REQUEST['receive_from'])) {
    //get receive from
    $receive_from = $_REQUEST['receive_from'];
}
//check product
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
    //get product
    $product = $_REQUEST['product'];
}
//check manufacturer
if (isset($_REQUEST['manufacturer']) && !empty($_REQUEST['manufacturer'])) {
    //get manufacturer
    $manufacturer = $_REQUEST['manufacturer'];
	
}
//check batch
if (isset($_REQUEST['batch']) && !empty($_REQUEST['batch'])) {
    //get batch
    $batch = $_REQUEST['batch'];
}
//check expiry date
if (isset($_REQUEST['expiry_date']) && !empty($_REQUEST['expiry_date'])) {
    //get expiry date
    $expiry_date = $_REQUEST['expiry_date'];
} else {
    $expiry_date = date('d/m/Y');
}

//new fields like msd
if (isset($_REQUEST['physical_inspection'])) $physical_inspection = $_REQUEST['physical_inspection'];
else $physical_inspection = '';

if (isset($_REQUEST['dtl'])) $dtl = $_REQUEST['dtl'];
else $dtl = '';

if (isset($_REQUEST['distribution_plan'])) $distribution_plan = $_REQUEST['distribution_plan'];
else $distribution_plan = '';




//check qty
if (isset($_REQUEST['qty']) && !empty($_REQUEST['qty'])) {
    //get qty
    $qty = str_replace(',', '', $_REQUEST['qty']);
}
//check unit
if (isset($_REQUEST['unit']) && !empty($_REQUEST['unit'])) {
    //get unit
    $unit = $_REQUEST['unit'];
}
 else {
    $unit=0;
}
//check remarks
if (isset($_REQUEST['remarks']) && !empty($_REQUEST['remarks'])) {
    //get remarks
    $remarks = $_REQUEST['remarks'];
}
//set funding source
$objStockBatch->funding_source = $receive_from;

if (empty($trans_no) && $receive_from>0) {
	$dataArr = explode(' ', $receive_date);
	$time = date('H:i:s', strtotime($dataArr[1].$dataArr[2]));
        //transaction date
    $objStockMaster->TranDate = dateToDbFormat($dataArr[0]).' '.$time;
    //transaction type id
    $objStockMaster->TranTypeID = 1;
    //transaction ref
    $objStockMaster->TranRef = $receive_ref;
    //from warehouse
    $objStockMaster->WHIDFrom = $receive_from;
    //to warehouse
    $objStockMaster->WHIDTo = $_SESSION['user_warehouse'];
    //created by
    $objStockMaster->CreatedBy = $_SESSION['user_id'];
    //created on
    $objStockMaster->CreatedOn = date("Y-m-d");
    //Received Remarks 
    $objStockMaster->ReceivedRemarks = $remarks;
    //current year
    $current_year = date("Y");
    //current month
    $current_month = date("m");
    if ($current_month < 7) {
        //from date
        $from_date = ($current_year - 1) . "-06-30";
        //to date
        $to_date = $current_year . "-07-30";
    } else {
        //from date
        $from_date = $current_year . "-06-30";
        //to date
        $to_date = ($current_year + 1) . "-07-30";
    }
    //get last id
    $last_id = $objStockMaster->getLastID($from_date, $to_date, 1);
    if ($last_id == NULL) {
        $last_id = 0;
    }
    $trans_no = "R" .  date('ym').str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
    $objStockMaster->TranNo = $trans_no;
    $objStockBatch->batch_no = $batch;
    $objStockBatch->batch_expiry = dateToDbFormat($expiry_date);
    $objStockBatch->item_id = $product;
    
    $objStockBatch->phy_inspection = $physical_inspection;
    $objStockBatch->dtl = $dtl ;
    $objStockBatch->dist_plan = $distribution_plan;
    
    $objStockBatch->Qty = $qty;
    $objStockBatch->status = "Stacked";
    $objStockBatch->production_date = dateToDbFormat($prod_date);
    $objStockBatch->unit_price = $unit_price;
    $objStockBatch->wh_id = $_SESSION['user_warehouse'];
	$objStockBatch->manufacturer = $manufacturer;
    $batch_id = $objStockBatch->save();

    $objStockMaster->BatchID = $batch_id;
    $objStockMaster->temp = 1;
    $objStockMaster->trNo = ($last_id + 1);
    $objStockMaster->LinkedTr = 0;
    $objStockMaster->issued_by = $_SESSION['user_id'];
    $fkStockID = $objStockMaster->save();

} else {
    $fkStockID = $stock_id;
    $objStockBatch->batch_no = $batch;
    $objStockBatch->batch_expiry = dateToDbFormat($expiry_date);
    $objStockBatch->item_id = $product;
    
    $objStockBatch->phy_inspection = $physical_inspection;
    $objStockBatch->dtl = $dtl ;
    $objStockBatch->dist_plan = $distribution_plan;
    
    $objStockBatch->Qty = $qty;
    $objStockBatch->status = "Stacked";
    $objStockBatch->production_date = dateToDbFormat($prod_date);
    $objStockBatch->unit_price = $unit_price;
    $objStockBatch->wh_id = $_SESSION['user_warehouse'];
    $objStockBatch->manufacturer = $manufacturer;
    $batch_id = $objStockBatch->save();
}

if ($strDo == "Add") {
    $objStockDetail->fkStockID = $fkStockID;
    $objStockDetail->BatchID = $batch_id;
    $objStockDetail->fkUnitID = $unit;
    $objStockDetail->IsReceived = 0;
    $objStockDetail->adjustmentType = 1;
    $objStockDetail->Qty = "+" . $qty;
    $objStockDetail->temp = 1;
    $objStockDetail->save();

    // Adjust Batch Quantity
    $objStockBatch->adjustQtyByWh($batch_id, $_SESSION['user_warehouse']);
    
    // Auto Running Batches
    $objStockBatch->autoRunningLEFOBatch($product, $_SESSION['user_warehouse']);
}

header("location:new_receive.php");
exit;
?>