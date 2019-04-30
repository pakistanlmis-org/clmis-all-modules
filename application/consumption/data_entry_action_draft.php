<?php

/**
 * data_entry_action_draft
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including files
include("../includes/classes/AllClasses.php");
//Setting Time Zone
date_default_timezone_set("Asia/Karachi");

/**
 * Get Client Ip
 * 
 * @return type
 */
function getClientIp() {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $ip;
}

if ($_POST['ActionType'] == 'Add') {
    //Query for deleting 
    $delQry = "DELETE FROM tbl_wh_data_draft WHERE `wh_id`=" . $_POST['wh_id'] . " AND report_month='" . $_POST['mm'] . "' AND report_year='" . $_POST['yy'] . "' ";
    mysql_query($delQry) or die(mysql_error());
    //Checking isNewRpt
    if ($_POST['isNewRpt'] == 0) {
        //Getting add_date
        $addDate = $_POST['add_date'];
    } else {
        $addDate = date('Y-m-d H:i:s');
    }

    $lastUpdate = date('Y-m-d H:i:s');
    // Client IP
    $clientIp = getClientIp();
    //Checking itmrec_id
    if (isset($_POST['itmrec_id']) && !empty($_POST['itmrec_id']) && is_array($_POST['itmrec_id'])) {
        //Getting itmrec_id
        $postedArray = $_POST['itmrec_id'];
    } else {
        //Getting flitmrec_id
        $postedArray = $_POST['flitmrec_id'];
    }
    //Exploding data from postedArray
    foreach ($postedArray as $val) {
        $itemid = explode('-', $val);
        $FLDOBLA = "0" . $_POST['FLDOBLA' . $itemid[1]];
        $FLDOBLC = "0" . $_POST['FLDOBLC' . $itemid[1]];
        $FLDRecv = "0" . $_POST['FLDRecv' . $itemid[1]];
        $FLDIsuueUP = "0" . $_POST['FLDIsuueUP' . $itemid[1]];
        $FLDCBLA = "0" . $_POST['FLDCBLA' . $itemid[1]];
        $FLDCBLC = "0" . $_POST['FLDCBLC' . $itemid[1]];
        $FLDReturnTo = "0" . $_POST['FLDReturnTo' . $itemid[1]];
        $FLDUnusable = "0" . $_POST['FLDUnusable' . $itemid[1]];
        //Query for inserting
        $queryadddata = "INSERT INTO tbl_wh_data_draft(report_month,report_year,item_id,wh_id,wh_obl_a,wh_obl_c,
		wh_received,wh_issue_up,wh_cbl_a,wh_cbl_c,wh_adja,wh_adjb,RptDate,add_date,last_update,ip_address)  Values(		
		" . $_POST['mm'] . ",
		" . $_POST['yy'] . ",							
		'" . $val . "',
		" . $_POST['wh_id'] . ",
		" . $FLDOBLA . ",
		" . $FLDOBLC . ",
		" . $FLDRecv . ",
		" . $FLDIsuueUP . ",
		" . $FLDCBLA . ",
		" . $FLDCBLC . ",
		" . $FLDReturnTo . ",
		" . $FLDUnusable . ",
		'" . $_POST['RptDate'] . "',
		'" . $addDate . "',
		'" . $lastUpdate . "',
		'" . $clientIp . "'
		)";

        $rsadddata = mysql_query($queryadddata) or die(mysql_error());
    }
}
?>