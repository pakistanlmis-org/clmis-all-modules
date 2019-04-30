<?php

//echo '<pre>';print_r($_REQUEST);exit;
/**
 * new_issue_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//AllClasses
include("../includes/classes/AllClasses.php");

$strDo = "Add";
$nstkId = 0;
$autorun = false;

// Make data parmanent when user click on save button
if (isset($_REQUEST['stockid']) && !empty($_REQUEST['stockid'])) {
    //stock id
    $stockid = $_REQUEST['stockid'];
    //update stock master temp
    $objStockMaster->updateTemp($stockid);
    //update stock detail temp
    $objStockDetail->updateTemp($stockid);

    //Save Data in WH data table
    $objWhData->addReport($stockid, 2);
    //get Wh Level By Stock ID
    $result = $objStockMaster->getWhLevelByStockID($stockid);
//    echo '<pre>';print_r($_REQUEST);print_r($result);exit;
    if ($result['level'] >= 4) {
        $objWhData->addReport($stockid, 10, '', $result['uc_wh_id']);
    }
    
    if ($result['level'] == 7) {
        $objStockMaster->autoReceiveData($stockid);
    }
    //sending email start ----------------------------
    $proc_by_arr = array();
    $shipment_stk_arr = array();
    if ($_SERVER['SERVER_ADDR'] == '::1' || $_SERVER['SERVER_ADDR'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == 'beta.lmis.gov.pk') {
        //$subject = 'Notification: Stock Issued in beta.lmis.gov.pk/clmisapp';
        $shipment_stk_arr[] = "'all'";
        $proc_by_arr[] = '10';
    } else {
        //$subject = 'Notification: Stock Issued in cLMIS - c.lmis.gov.pk';
        $stk_qry = "SELECT DISTINCT
stock_batch.funding_source,
tbl_warehouse.prov_id,
tbl_warehouse.dist_id,
tbl_warehouse.stkid
FROM
	tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
WHERE
	tbl_stock_master.PkStockID = $stockid";
        $res_stk = mysql_query($stk_qry);
        while ($row = mysql_fetch_assoc($res_stk)) {
            $shipment_stk_arr[] = "'" . $row['stkid'] . "'";
            $province = $row['prov_id'];
            $district_id = $row['dist_id'];
        }
        $shipment_stk_arr[] = "'all'";
        $proc_by_arr[] = $province;
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
                        email_persons_list.stkid IN (" . implode(',', $shipment_stk_arr) . ") AND
                        email_persons_list.prov_id IN (" . implode(',', $proc_by_arr) . ")";
    //echo $emails_qry;exit;
    $res_email = mysql_query($emails_qry);
    while ($row = mysql_fetch_assoc($res_email)) {
        $office_name = $row['office_name'];
        if ($office_name == 'Government') {
            $to_list[] = $row['email_address'];
            $to_list_sms[] = $row['cell_number'];
        } else {
            $cc_list[] = $row['email_address'];
            $cc_list_sms[] = $row['cell_number'];
        }
    }

    $emails_dist = "SELECT
                alerts.pk_id,
                alerts.person_name,
                alerts.email_address,
                alerts.cell_number,
                alerts.`level`,
                alerts.stkid,
                alerts.prov_id
        FROM
                alerts
        WHERE
                alerts.dist_id = $district_id";
    //echo $emails_qry;exit;
    $res_email_dist = mysql_query($emails_dist);
    $count_dist = mysql_num_rows($res_email_dist);
    if ($count_dist > 0) {
        while ($row_dist = mysql_fetch_assoc($res_email_dist)) {
            $to_list[] = $row_dist['email_address'];
            $to_list_sms[] = $row_dist['cell_number'];
        }
    }

    $to = implode(',', $to_list);
    $cc = implode(',', $cc_list);
    //echo '<pre>';print_r($to_list);print_r($to);exit;


    $stockId = $stockid;
    ob_start();
    include('issue_voucher_email.php');
    $message = ob_get_contents();
    ob_end_clean();

//    require_once '/../includes/classes/clsEmail.php';    
//    $objEmail = new clsEmail();
//    $options = array(
//        ''
//    );
//    $objEmail->send($options);
//    
//    require_once '/../includes/classes/clsSMS.php';
//    $objSms = new clsSMS();
//    $options = array(
//        ''
//    );
//    $objSms->send($options);

    $subject = 'Stock Issue Alert : ' . $issue_to . ' From: ' . $whName[0];

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";

    $headers .= "From: feedback@lmis.gov.pk" . "\r\n" .
            "Reply-To: feedback@lmis.gov.pk" . "\r\n" .
            "Cc: $cc" . "\r\n" .
            "X-Mailer: PHP/" . phpversion();
    mail($to, $subject, $message, $headers);
    //end of mail -------------------

    mysql_query("INSERT INTO alerts_log (
alerts_log.`to`,
alerts_log.cc,
alerts_log.`subject`,
alerts_log.body,
alerts_log.response,alerts_log.type,alerts_log.interface) VALUES ('$to','$cc','$subject','$message','$response','Email','IssuecLMIS')");


//    $url = "http://cbs.zong.com.pk/reachcwsv2/corporatesms.svc?wsdl";
//    $client = new SoapClient($url, array("trace" => 1, "exception" => 0));
//    $username = '923125154792';
//    $password = '38917466';
//
//    if (count($to_list_sms) > 0) {
//        foreach ($to_list_sms as $to) {
//            $resultQuick = $client->QuickSMS(
//                    array('obj_QuickSMS' =>
//                        array('loginId' => $username,
//                            'loginPassword' => $password,
//                            'Destination' => $to,
//                            'Mask' => 'LMIS Alert',
//                            'Message' => $message,
//                            'UniCode' => 0,
//                            'ShortCodePrefered' => 'n'
//                        )
//                    )
//            );
//
//            $response = $resultQuick->QuickSMSResult;
//
//            mysql_query("INSERT INTO alerts_log (
//alerts_log.`to`,
//alerts_log.`subject`,
//alerts_log.body,
//alerts_log.response, alerts_log.type, alerts_log.interface) VALUES ('$cc','LMIS Alert','$message','$response','SMS','IssuecLMIS')");
//        }
//    }
//
//    if (count($cc_list_sms) > 0) {
//        foreach ($cc_list_sms as $cc) {
//            $resultQuick = $client->QuickSMS(
//                    array('obj_QuickSMS' =>
//                        array('loginId' => $username,
//                            'loginPassword' => $password,
//                            'Destination' => $cc,
//                            'Mask' => 'LMIS Alert',
//                            'Message' => $message,
//                            'UniCode' => 0,
//                            'ShortCodePrefered' => 'n'
//                        )
//                    )
//            );
//
//            $response = $resultQuick->QuickSMSResult;
//
//            mysql_query("INSERT INTO alerts_log (
//alerts_log.`to`,
//alerts_log.`subject`,
//alerts_log.body,
//alerts_log.response, alerts_log.type, alerts_log.interface) VALUES ('$cc','LMIS Alert','$message','$response','SMS','IssuecLMIS')");
//        }
//    }

    $_SESSION['success'] = 1;
    header("location:new_issue.php");
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
//check issue date
if (isset($_REQUEST['issue_date']) && !empty($_REQUEST['issue_date'])) {
    //get issue date
    $issue_date = $_REQUEST['issue_date'];
}
//check issue ref
if (isset($_REQUEST['issue_ref']) && !empty($_REQUEST['issue_ref'])) {
    //get issue ref
    $issue_ref = $_REQUEST['issue_ref'];
}
//check warehouse
if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
    //get warehouse
    $issue_to = $_REQUEST['warehouse'];
} else {
    header("Location:new_issue.php?warehouse=1");
    exit;
}
//check product
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
    //get product
    $product = $_REQUEST['product'];
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
}
//check qty
if (isset($_REQUEST['qty']) && !empty($_REQUEST['qty'])) {

    //get qty
    $qty = str_replace(",", "", $_REQUEST['qty']);
}
//check available_qty
if (isset($_REQUEST['available_qty']) && !empty($_REQUEST['available_qty'])) {
    //get available_qty
    $ava_qty = str_replace(",", "", $_REQUEST['available_qty']);
}
if ((int) $qty > (int) $ava_qty || (int) $qty == (int) $ava_qty) {
    $qty = $ava_qty;
    $autorun = true;
}
//check unit
if (isset($_REQUEST['unit']) && !empty($_REQUEST['unit'])) {
    //get unit
    $unit = $_REQUEST['unit'];
}
//get comments
if (isset($_REQUEST['comments']) && !empty($_REQUEST['comments'])) {
    //check comments
    $comments = $_REQUEST['comments'];
}
//issue by
if (isset($_REQUEST['issued_by']) && !empty($_REQUEST['issued_by'])) {
    //issued by
    $issued_by = $_REQUEST['issued_by'];
}

$objStockBatch->funding_source = $receive_from;

if (empty($trans_no)) {
    $objStockMaster->TranDate = dateToDbFormat($issue_date);
    $objStockMaster->TranTypeID = 2;
    $objStockMaster->issued_by = $issued_by;
    $objStockMaster->TranRef = $issue_ref;
    $objStockMaster->WHIDFrom = $_SESSION['user_warehouse'];
    $objStockMaster->WHIDTo = $issue_to;
    $objStockMaster->CreatedBy = $_SESSION['user_id'];
    $objStockMaster->CreatedOn = date("Y-m-d");
    $objStockMaster->ReceivedRemarks = $comments;

    
    $date_breakdown = explode('/',$issue_date);
    $year1 = $date_breakdown[2];
    $mon1 = $date_breakdown[1];
    $date1 = $date_breakdown[0];
    $issue_date_formatted = $year1.'-'.$mon1.'-'.$date1;
    
    //changing the from and to date , from fiscal year to only month
    $current_year = $year1;
    $current_month = $mon1;
    $from_date = date('Y-m-01',strtotime($issue_date_formatted));
    $to_date = date('Y-m-t',strtotime($issue_date_formatted));

    $last_id = $objStockMaster->getLastID($from_date, $to_date, 2);

    if ($last_id == NULL) {
        $last_id = 0;
    }
    
    
    $trans_no = "I".date('ym',strtotime($issue_date_formatted)). str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
    //echo $from_date.','.$to_date.','.$last_id;exit;
    $objStockMaster->TranNo = $trans_no;
    $objStockMaster->temp = 1;
    $objStockMaster->trNo = ($last_id + 1);
    $objStockMaster->LinkedTr = 0;
    //echo $objStockMaster->trNo;exit;
    $fkStockID = $objStockMaster->save();
} else {
    $fkStockID = $stock_id;
    $objStockMaster->TranDate = dateToDbFormat($issue_date);
    $objStockMaster->TranRef = $issue_ref;
    $objStockMaster->issued_by = $issued_by;
    $objStockMaster->ReceivedRemarks = $comments;
    $objStockMaster->updateMasterIssueDate($stock_id);
}

if ($strDo == "Add") {
    $objStockDetail->fkStockID = $fkStockID;
    $objStockDetail->BatchID = $batch;
    if(empty($unit)) $unit=0;
    $objStockDetail->fkUnitID = $unit;
    $objStockDetail->Qty = "-" . $qty;
    $objStockDetail->temp = 1;
    $objStockDetail->IsReceived = 0;
    $objStockDetail->adjustmentType = 2;
    $result = $objStockDetail->save();

    // Adjust Batch Quantity
    $objStockBatch->adjustQtyByWh($batch, $_SESSION['user_warehouse']);

    if ($autorun == true) {
        $objStockBatch->autoRunningLEFOBatch($product, $_SESSION['user_warehouse']);
        $objStockBatch->changeStatus($batch, 'Finished');
    }
}

$_SESSION['stock_id'] = $fkStockID;
header("location:new_issue.php");
exit;
?>