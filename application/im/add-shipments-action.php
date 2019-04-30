<?php

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
//echo '<pre>';print_r($_REQUEST);
//exit;
//check receive date
if (isset($_REQUEST['receive_date']) && !empty($_REQUEST['receive_date'])) {
    //get receive date
    $receive_date = $_REQUEST['receive_date'];
}
//check refrence number
if (isset($_REQUEST['refrence_number']) && !empty($_REQUEST['refrence_number'])) {
    //get receive ref
    $refrence_number = $_REQUEST['refrence_number'];
}
//check receive from
if (isset($_REQUEST['receive_from']) && !empty($_REQUEST['receive_from'])) {
    //get receive from
    $receive_from = $_REQUEST['receive_from'];
}
//check receive from
if (isset($_REQUEST['procured_by']) && !empty($_REQUEST['procured_by'])) {
    //get receive from
    $procured_by = $_REQUEST['procured_by'];
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

//check qty
if (isset($_REQUEST['qty']) && !empty($_REQUEST['qty'])) {
    //get qty
    $qty = str_replace(',', '', $_REQUEST['qty']);
}
//check status
if (isset($_REQUEST['status']) && !empty($_REQUEST['status'])) {
    //get status
    $status = $_REQUEST['status'];
}

$objshipments->reference_number = $refrence_number;
$objshipments->item_id = $product;
$objshipments->manufacturer = $manufacturer;
$dataArr = explode(' ', $receive_date);
$time = date('H:i:s', strtotime($dataArr[1] . $dataArr[2]));
//transaction date

$objshipments->shipment_date = dateToDbFormat($dataArr[0]) . ' ' . $time;
$objshipments->shipment_quantity = $qty;
$objshipments->stk_id = $receive_from;
$objshipments->procured_by = $procured_by;
$objshipments->status = $status;
$objshipments->created_date = date("Y-m-d");
$objshipments->created_by = $_SESSION['user_id'];
$objshipments->modified_by = $_SESSION['user_id'];

$shipments = $objshipments->save();

$strSql = "SELECT
                itminfo_tab.itm_id,
                itminfo_tab.itm_name,
                itminfo_tab.itm_type
            FROM
                itminfo_tab
            WHERE
                itminfo_tab.itm_id  = " . $product;
//query result
$rsSql = mysql_query($strSql) or die("Error GetProduct data");
$prod_data = array();
if (mysql_num_rows($rsSql) > 0) {
    $prod_data = mysql_fetch_assoc($rsSql);
}


$res = $objstakeholderitem->get_manufacturer_by_id($manufacturer);
$manuf_data = mysql_fetch_assoc($res);

$objloc->PkLocID = $procured_by;
$loc_name = $objloc->get_location_name();

//sending email start ----------------------------


$proc_by_arr = array();
$shipment_stk_arr = array();
if ($_SERVER['SERVER_ADDR'] == '::1' || $_SERVER['SERVER_ADDR'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_NAME'] == 'beta.lmis.gov.pk') {
    $subject = 'New Shipment Added in beta.lmis.gov.pk/clmisapp';
    $shipment_stk_arr[] = "'all'";
    $proc_by_arr[] = '10';
} else {
    $subject = 'New Shipment Added in cLMIS - c.lmis.gov.pk';
    $stk_qry = "SELECT
                   funding_stk_prov.stakeholder_id,funding_stk_prov.province_id,funding_stk_prov.funding_source_id
               FROM
                   funding_stk_prov
               WHERE
                   funding_stk_prov.funding_source_id = $receive_from AND
                   funding_stk_prov.province_id = $procured_by ";
    $res_stk = mysql_query($stk_qry);
    while ($row = mysql_fetch_assoc($res_stk)) {
        $shipment_stk_arr[] = "'" . $row['stakeholder_id'] . "'";
    }
    $shipment_stk_arr[] = "'all'";
    $proc_by_arr[] = $procured_by;
    $proc_by_arr[] = '10';
}
//echo '<pre>';print_r($proc_by_arr);print_r($shipment_stk_arr);exit;

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
                        email_bridge.action_id = 1 AND
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
$to = implode(',', $to_list);
$cc = implode(',', $cc_list);
///echo '<pre>';print_r($to_list);print_r($to);exit;

$message = '
            <html>
            <body>
              <h4>New Shipment Added in ' . $_SERVER['SERVER_NAME'] . ':</h4>

              <p>This is to notify you that a new shipment of reference number : <b>' . $refrence_number . '</b> is added in cLMIS. </p>
              ';
$message .= ' <p>Item            : <b>' . $prod_data['itm_name'] . '</b> </p>';
$message .= ' <p>Manufacturer    : <b>' . $manuf_data['stkname'] . '|' . $manuf_data['brand_name'] . '</b> </p>';
$message .= ' <p>Shipment Date   : <b>' . $receive_date . '</b> </p>';
$message .= ' <p>Quantity        : <b>' . $qty . ' ' . $prod_data['itm_type'] . '</b> </p>';
$message .= ' <p>Funding Source  : <b>' . $objwarehouse->GetWHByWHId($receive_from) . '</b> </p>';
$message .= ' <p>Procured For    : <b>' . $loc_name . '</b> </p>';
$message .= ' <p>Status          : <b>' . $status . '</b> </p>';

if (!empty($_SESSION['user_name'])) {
    $message .= ' <p>Created By:<b> ' . $_SESSION['user_name'] . '</b></p>';
}

$message .= ' 
              <p>Thanks</p>
            </body>
            </html>
            ';

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
$headers .= "From: feedback@lmis.gov.pk" . "\r\n" .
        "Reply-To: feedback@lmis.gov.pk" . "\r\n" .
        "Cc: $cc" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();
mail($to, $subject, $message, $headers);

mysql_query("INSERT INTO alerts_log (
alerts_log.`to`,
alerts_log.cc,
alerts_log.`subject`,
alerts_log.body,
alerts_log.response,alerts_log.type,alerts_log.interface ) VALUES ('$to','$cc','$subject','$message','$response','Email','ShipmentscLMIS')");


//$url = "http://cbs.zong.com.pk/reachcwsv2/corporatesms.svc?wsdl";
//$client = new SoapClient($url, array("trace" => 1, "exception" => 0));
//$username = '923125154792';
//$password = '38917466';
//
//if (count($to_list_sms) > 0) {
//    foreach ($to_list_sms as $to) {
//        if (!empty($to)) {
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
//            $response = $resultQuick->QuickSMSResult;
//
//            mysql_query("INSERT INTO alerts_log (
//alerts_log.`to`,
//alerts_log.`subject`,
//alerts_log.body,
//alerts_log.response, alerts_log.type, alerts_log.interface) VALUES ('$cc','LMIS Alert','$message','$response','SMS','ShipmentscLMIS')");
//        }
//    }
//}
//
//if (count($cc_list_sms) > 0) {
//    foreach ($cc_list_sms as $cc) {
//        if (!empty($cc)) {
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
//alerts_log.response, alerts_log.type, alerts_log.interface) VALUES ('$cc','LMIS Alert','$message','$response','SMS','ShipmentscLMIS')");
//        }
//    }
//}

//end of email---------

$_SESSION["success"] = 1;
header("location:add-shipments.php");
exit;
?>