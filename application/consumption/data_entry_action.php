<?php
/**
 * data_entry_action
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * @version    2.2
 * 
 */
//Including AllClasses
include("../includes/classes/AllClasses.php");
//Getting user_province
$province = $_SESSION['user_province'];
//Getting user_stakeholder
$mainStk = $_SESSION['user_stakeholder'];
//Checking user_id
if (!isset($_SESSION['user_id'])) {
    $location = SITE_URL . 'index.php';
    ?>

    <script type="text/javascript">
        window.location = "<?php echo $location; ?>";
    </script>
    <?php
}
//Setting Time zone
date_default_timezone_set("Asia/Karachi");
/**
 * getClientIp
 * 
 * @return type
 */
function getClientIp() {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $ip;
}
//Add
if ($_POST['ActionType'] == 'Add') {
    if (isset($_POST['itmrec_id']) && !empty($_POST['itmrec_id']) && is_array($_POST['itmrec_id'])) {
        //Getting itmrec_id
        $postedArray = $_POST['itmrec_id'];
    } else {
        //Getting flitmrec_id
        $postedArray = $_POST['flitmrec_id'];
    }
    //******************************************************
    // Validation: Start
    //******************************************************
    $error = '';
    $response = array();

    foreach ($postedArray as $val) {
        $itemid = explode('-', $val);
        $ob = isset($_POST['FLDOBLA' . $itemid[1]]) ? $_POST['FLDOBLA' . $itemid[1]] : 0;
        $rcv = isset($_POST['FLDRecv' . $itemid[1]]) ? $_POST['FLDRecv' . $itemid[1]] : 0;
        $issue = isset($_POST['FLDIsuueUP' . $itemid[1]]) ? $_POST['FLDIsuueUP' . $itemid[1]] : 0;
        $cb = isset($_POST['FLDCBLA' . $itemid[1]]) ? $_POST['FLDCBLA' . $itemid[1]] : 0;
        $adjPos = isset($_POST['FLDReturnTo' . $itemid[1]]) ? $_POST['FLDReturnTo' . $itemid[1]] : 0;
        $adjNeg = isset($_POST['FLDUnusable' . $itemid[1]]) ? $_POST['FLDUnusable' . $itemid[1]] : 0;
        $calCB = $ob + $rcv - $issue + $adjPos - $adjNeg;
        if ($calCB != $cb) {
            $error .= 'Incorrect closing Balance for ' . $_POST['flitmname' . $itemid[1]] . '<br>';
        }
    }
    //******************************************************
    // Validation: End
    //******************************************************
    if (strlen($error) > 0) {
        $response['resp'] = 'err';
        $response['msg'] = $error;
        //Encode in json
        echo json_encode($response);
        exit;
    } else {
        //Delete query
        $delQry = "DELETE FROM tbl_wh_data WHERE `wh_id`=" . $_POST['wh_id'] . " AND RptDate='" . $_POST['RptDate'] . "' ";
        mysql_query($delQry) or die(mysql_error());
        //If new report
        if ($_POST['isNewRpt'] == 0) {
            $addDate = $_POST['add_date'];
        } else {
            $addDate = date('Y-m-d H:i:s');
        }
        $lastUpdate = date('Y-m-d H:i:s');
        // Client IP
        $clientIp = getClientIp();

        foreach ($postedArray as $val) {
            //Explode
            $itemid = explode('-', $val);
            //Getting FLDOBLA
            $FLDOBLA = "0" . $_POST['FLDOBLA' . $itemid[1]];
            //Getting FLDOBLA
            $FLDOBLA = isset($_POST['FLDOBLA' . $itemid[1]]) ? $_POST['FLDOBLA' . $itemid[1]] : 0;
            //Getting FLDRecv
            $FLDRecv = isset($_POST['FLDRecv' . $itemid[1]]) ? $_POST['FLDRecv' . $itemid[1]] : 0;
            //Getting FLDIsuueUP
            $FLDIsuueUP = isset($_POST['FLDIsuueUP' . $itemid[1]]) ? $_POST['FLDIsuueUP' . $itemid[1]] : 0;
            //Getting FLDCBLA
            $FLDCBLA = isset($_POST['FLDCBLA' . $itemid[1]]) ? $_POST['FLDCBLA' . $itemid[1]] : 0;
            //Getting FLDReturnTo
            $FLDReturnTo = isset($_POST['FLDReturnTo' . $itemid[1]]) ? $_POST['FLDReturnTo' . $itemid[1]] : 0;
            //Getting FLDUnusable
            $FLDUnusable = isset($_POST['FLDUnusable' . $itemid[1]]) ? $_POST['FLDUnusable' . $itemid[1]] : 0;
            //Getting wh-id
            $wh_id = $_POST['wh_id'];
            $itemid = $val;
            //Getting year
            $report_year = $_POST['yy'];
            //Getting month
            $report_month = $_POST['mm'];
            //******************************************************
            // Check if data already exists
            //******************************************************
            $checkData = "SELECT
							COUNT(tbl_wh_data.w_id) AS num
						FROM
							tbl_wh_data
						WHERE
							tbl_wh_data.wh_id = " . $_POST['wh_id'] . "
						AND tbl_wh_data.report_month = " . $_POST['mm'] . "
						AND tbl_wh_data.report_year = " . $_POST['yy'] . "
						AND tbl_wh_data.item_id = '" . $val . "'";
            //Query result
            $data = mysql_fetch_array(mysql_query($checkData));
            if ($data['num'] == 0) {
                //Insert query
                //Inserts
                //report_month,
                //report_year,
                //item_id,
                //wh_id,
                //wh_obl_a,
                //wh_received,
                //wh_issue_up,
                //wh_cbl_a,
                //wh_adja,
                //wh_adjb,
                //RptDate,
                //add_date,
                //last_update,
                //ip_address,
                //created_by
                $queryadddata = "INSERT INTO tbl_wh_data(report_month,report_year,item_id,wh_id,wh_obl_a,
				wh_received,wh_issue_up,wh_cbl_a,wh_adja,wh_adjb,RptDate,add_date,last_update,ip_address,created_by)  Values(		
				" . $_POST['mm'] . ",
				" . $_POST['yy'] . ",							
				'" . $val . "',
				" . $_POST['wh_id'] . ",
				" . $FLDOBLA . ",
				" . $FLDRecv . ",
				" . $FLDIsuueUP . ",
				" . $FLDCBLA . ",
				" . $FLDReturnTo . ",
				" . $FLDUnusable . ",
				'" . $_POST['RptDate'] . "',
				'" . $addDate . "',
				'" . $lastUpdate . "',
				'" . $clientIp . "',
				'" . $_SESSION['user_id'] . "'
				)";

                $rsadddata = mysql_query($queryadddata) or die(mysql_error());
                //Insert query
                //Inserts
                //report_month,
                //report_year,
                //item_id,
                //wh_id,
                //wh_obl_a,
                //wh_received,
                //wh_issue_up,
                //wh_cbl_a,
                //wh_adja,
                //wh_adjb,
                //RptDate,
                //add_date,
                //ip_address,
                //created_by
                $queryadddata1 = "INSERT INTO tbl_wh_data_history(report_month,report_year,item_id,wh_id,wh_obl_a,
				wh_received,wh_issue_up,wh_cbl_a,wh_adja,wh_adjb,RptDate,add_date,ip_address,created_by) Values(		
				" . $_POST['mm'] . ",
				" . $_POST['yy'] . ",							
				'" . $val . "',
				" . $_POST['wh_id'] . ",
				" . $FLDOBLA . ",
				" . $FLDRecv . ",
				" . $FLDIsuueUP . ",
				" . $FLDCBLA . ",
				" . $FLDReturnTo . ",
				" . $FLDUnusable . ",
				'" . $_POST['RptDate'] . "',
				'" . $lastUpdate . "',
				'" . $clientIp . "',
				'" . $_SESSION['user_id'] . "'
				)";

                $rsadddata1 = mysql_query($queryadddata1) or die(mysql_error());
                //*******************************************************************************
                // If previous month is edited then it should carried to the last data entry month
                /* ----------------- Start ----------------- */
                $stDate = $report_year . '-' . $report_month . '-01';
                $startDate = date('Y-m-d', strtotime("+1 month", strtotime($stDate)));
                //Gets RptDate
                $DEQry = mysql_fetch_array(mysql_query("SELECT
							tbl_wh_data.RptDate
						FROM
							tbl_wh_data
						WHERE
							tbl_wh_data.wh_id = $wh_id
						ORDER BY
							tbl_wh_data.RptDate DESC
						LIMIT 1"));
                //Checking Report Date
                if (!empty($DEQry['RptDate'])) {
                    $endDate = $DEQry['RptDate'];
                    $endDate = date('Y-m-d', strtotime("+1 month", strtotime($endDate)));
                    $begin = new DateTime($startDate);
                    $end = new DateTime($endDate);
                    $diff = $begin->diff($end);
                    $totalMonths = (($diff->format('%y') * 12) + $diff->format('%m'));
                    $interval = DateInterval::createFromDateString('1 month');
                    $period = new DatePeriod($begin, $interval, $end);
                    foreach ($period as $date) {
                        $date = $date->format("Y-m-d");
                        $qry = "SELECT REPUpdateCarryForward('$date', '$itemid', $wh_id) FROM DUAL";
                        mysql_query($qry);
                    }
                }
                //******************************************************
                /* ----------------- End ----------------- */
                //******************************************************
            }
        }
        //********************************************************************
        // Track history
        //********************************************************************
        $sql = "INSERT INTO tbl_wh_update_history
			SET
				wh_id = '$wh_id',
				reporting_date = '" . $_POST['RptDate'] . "',
				update_on = NOW(),
				updated_by = '" . $_SESSION['user_warehouse'] . "',
				ip_address = '" . $clientIp . "' ";
        mysql_query($sql);

        // Delete draft data
        $delQry = "DELETE FROM tbl_wh_data_draft WHERE `wh_id`=" . $_POST['wh_id'] . " AND report_month='" . $_POST['mm'] . "' AND report_year='" . $_POST['yy'] . "' ";
        mysql_query($delQry) or die(mysql_error());

        $response['resp'] = 'ok';
        //Encode in json
        echo json_encode($response);
        exit;
    }
}