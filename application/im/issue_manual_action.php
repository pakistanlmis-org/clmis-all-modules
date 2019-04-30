<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * clr6_new_issue_action
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
$autorun = false;
$vvmstage = '';

//check issue date
if (isset($_REQUEST['issue_date']) && !empty($_REQUEST['issue_date'])) {
//get issue date	
    $issue_date = $_REQUEST['issue_date'];
}
//check clr6 id
if (isset($_REQUEST['clr6_id']) && !empty($_REQUEST['clr6_id'])) {
//get clr6 id	
    $clr6_id = $_REQUEST['clr6_id'];
}
//check issue reference
if (isset($_REQUEST['issue_ref']) && !empty($_REQUEST['issue_ref'])) {
//get issue reference	
    $issue_ref = $_REQUEST['issue_ref'];
}
//check issued by
if (isset($_REQUEST['issued_by']) && !empty($_REQUEST['issued_by'])) {
//get issued by	
    $issued_by = $_REQUEST['issued_by'];
}
//check warehouse
if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
//get warehouse	
    $issue_to = $_REQUEST['warehouse'];
} else {
    header("location:" . APP_URL . "im/issue.php?warehouse=1");

    exit;
}
//check product
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
//get product	
    $product = $_REQUEST['product'];
}
//check item rec
if (isset($_REQUEST['itmrec']) && !empty($_REQUEST['itmrec'])) {
//get item rec	
    $itemrec = $_REQUEST['itmrec'];
}
//check batch
$batch=array();
if (isset($_REQUEST['batch']) && !empty($_REQUEST['batch'])) {
    $batch = $_REQUEST['batch'];
}
//check qty issued
if (isset($_REQUEST['qty_issued']) && !empty($_REQUEST['qty_issued'])) {
//get qty	
    $qty = $_REQUEST['qty_issued'];
}
//qty
$qty = array_filter($qty);
//batch
$batch = array_filter($batch);
foreach ($qty as $key => $value) {
    $batch = explode("|", $key);
    $batchId = $batch[1];
}
//set Transaction date
$objStockMaster->TranDate = dateToDbFormat($issue_date);
//set Transaction type id
$objStockMaster->TranTypeID = 2;
//set Transaction ref
$objStockMaster->TranRef = $issue_ref;
//set issued by
$objStockMaster->issued_by = (!empty($issued_by)?$issued_by:'0');
//set from warehouse
$objStockMaster->WHIDFrom = $_SESSION['user_warehouse'];
//set to warehouse
$objStockMaster->WHIDTo = $issue_to;
//set created by
$objStockMaster->CreatedBy = $_SESSION['user_id'];
//set created on
$objStockMaster->CreatedOn = date("Y-m-d");
//current year
$current_year = date("Y");
//current month
$current_month = date("m");
//echo '<pre>';print_r($objStockMaster);exit;
if ($current_month < 7) {
    $from_date = ($current_year - 1) . "-06-30";
    $to_date = $current_year . "-07-30";
} else {
    $from_date = $current_year . "-06-30";
    $to_date = ($current_year + 1) . "-07-30";
}
//last id
$last_id = $objStockMaster->getLastID($from_date, $to_date, 2);

if ($last_id == NULL) {
    $last_id = 0;
}
//transaction number
$trans_no = "I" . date('ym') . str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
//set transaction number
$objStockMaster->TranNo = $trans_no;
$objStockMaster->trNo = ($last_id + 1);
$objStockMaster->LinkedTr = 0;
$objStockMaster->temp = 0;
//save stock master
$fkStockID = $objStockMaster->save();
//add
if ($strDo == "Add") {
    foreach ($qty as $key => $value) {
        //value
        $value = str_replace(',', '', $value);
        //batch
        $batch = explode("|", $key);
        //batch id
        $batchId = $batch[1];
        //item rec
        $itemrec[$batch[0]];
        //value array
        $valueArr[$batch[0]] = $value;
        //set fk stock id
        $objStockDetail->fkStockID = $fkStockID;
        //set batch id
        $objStockDetail->BatchID = $batchId;
        //set fk unit id
        $objStockDetail->fkUnitID = (!empty($unit)?$unit:'0');
        //set qty
        $objStockDetail->Qty = "-" . $value;
        //set is received
        $objStockDetail->IsReceived = 0;
        //set adjustment type
        $objStockDetail->adjustmentType = 2;
        //set vvm stage
        $objStockDetail->vvm_stage = (!empty($vvmstage)?$vvmstage:'0');
        $objStockDetail->temp = 0;
        //set comment
        $objStockDetail->comments = $comments;

        $result = $objStockDetail->save();

        // Adjust Batch Quantity
        $objStockBatch->adjustQtyByWh($batchId, $_SESSION['user_warehouse']);

        if ($autorun == true) {
            $objStockBatch->autoRunningLEFOBatch($batch[0], $_SESSION['user_warehouse']);
            $objStockBatch->changeStatus($batchId, 'Finished');
        }
        foreach ($valueArr as $id => $value) {
            $sumArray[$id]+=$value;
        }
        //update clr6
        $updateCLR6 = mysql_query("UPDATE clr_details SET approval_status='Issued', stock_master_id=" . $fkStockID . " WHERE itm_id='" . $itemrec[$batch[0]] . "' AND pk_master_id=" . $clr6_id) or die(mysql_error());
    }
}

$objWhData->addReport($fkStockID, 2);
//get Wh Level By Stock ID
$result = $objStockMaster->getWhLevelByStockID($fkStockID);
if ($result['level'] == 3) {
    $result['uc_wh_id'];
    $objWhData->addReport($fkStockID, 10, '', $result['uc_wh_id']);
}
//add Report
$objWhData->addReport($fkStockID, 2);
//get Wh Level By Stock ID
$result = $objStockMaster->getWhLevelByStockID($fkStockID);
if ($result['level'] == 3) {
    $objWhData->addReport($fkStockID, 10, '', $result['uc_wh_id']);
}
if ($clr6_id) {
    $qry = "SELECT
				clr_details.stock_master_id,
				clr_details.approval_status
			FROM
				clr_details
			WHERE
				clr_details.pk_master_id = $clr6_id
			AND clr_details.approval_status <> 'Denied'
			AND clr_details.stock_master_id IS NULL
			AND clr_details.qty_req_dist_lvl1 > 0";
    $num = mysql_num_rows(mysql_query($qry));
    if ($num == 0) {
        $clr_master_status = 'Hard_Copy_Issued';
    } else {
        $clr_master_status = 'Hard_Copy';
    }
}
$updateCLR6 = mysql_query("UPDATE clr_master SET approval_status='" . $clr_master_status . "' where pk_id=" . $clr6_id) or die(mysql_error());




//sending email start ----------------------------
   
    $proc_by_arr = array();
    $shipment_stk_arr = array();
    if($_SERVER['SERVER_ADDR'] == '::1' || $_SERVER['SERVER_ADDR'] == 'localhost'  || $_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == 'beta.lmis.gov.pk') 
    {
        //$subject = 'Notification: Stock Issued Against Requisition in beta.lmis.gov.pk/clmisapp';
        $shipment_stk_arr[] = "'all'";
        $proc_by_arr[] = '10';
    }
    else
    {
        //$subject = 'Notification: Stock Issued Against Requisition in cLMIS - c.lmis.gov.pk';
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
                        email_bridge.action_id = 3 AND
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
  
   $stockId=$fkStockID;
    ob_start();
    include('issue_voucher_email.php');
    $message=ob_get_contents(); 
    ob_end_clean();
   
    $subject = 'Stock Issue Alert : '.$issue_to.' From: '.$whName[0];
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
    
    $headers .= "From: feedback@lmis.gov.pk" . "\r\n" .
    //$headers .= "From: No Reply <no-reply@ghsc-psm.org>";
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


$url = "http://cbs.zong.com.pk/reachcwsv2/corporatesms.svc?wsdl";
$client = new SoapClient($url, array("trace" => 1, "exception" => 0));
$username = '923125154792';
$password = '38917466';

if (count($to_list_sms) > 0) {
    foreach ($to_list_sms as $to) {
        $resultQuick = $client->QuickSMS(
                array('obj_QuickSMS' =>
                    array('loginId' => $username,
                        'loginPassword' => $password,
                        'Destination' => $to,
                        'Mask' => 'LMIS Alert',
                        'Message' => $message,
                        'UniCode' => 0,
                        'ShortCodePrefered' => 'n'
                    )
                )
        );

        $response = $resultQuick->QuickSMSResult;

        mysql_query("INSERT INTO alerts_log (
alerts_log.`to`,
alerts_log.`subject`,
alerts_log.body,
alerts_log.response, alerts_log.type, alerts_log.interface) VALUES ('$cc','LMIS Alert','$message','$response','SMS','IssuecLMIS')");
    }
}

if (count($cc_list_sms) > 0) {
    foreach ($cc_list_sms as $cc) {
        $resultQuick = $client->QuickSMS(
                array('obj_QuickSMS' =>
                    array('loginId' => $username,
                        'loginPassword' => $password,
                        'Destination' => $cc,
                        'Mask' => 'LMIS Alert',
                        'Message' => $message,
                        'UniCode' => 0,
                        'ShortCodePrefered' => 'n'
                    )
                )
        );

        $response = $resultQuick->QuickSMSResult;

        mysql_query("INSERT INTO alerts_log (
alerts_log.`to`,
alerts_log.`subject`,
alerts_log.body,
alerts_log.response, alerts_log.type, alerts_log.interface) VALUES ('$cc','LMIS Alert','$message','$response','SMS','IssuecLMIS')");
    }
}

if(!empty($_REQUEST['ref_page']))
{
    if($_REQUEST['ref_page']=='distribution_plan_issue')
        $url = 'distribution_plan_issue.php?plan_id='.$_REQUEST['plan_id'].'&plan_num='.$_REQUEST['plan_num'];
    else
        $url = $_REQUEST['ref_page'].'.php';
    header("location: " . APP_URL . "im/".$url);
} 
else
{
    header("location:" . APP_URL . "im/requisitions.php");
}
exit;
?>