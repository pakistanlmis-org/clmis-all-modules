<?php
/**
 * data_entry_action1
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses
include("includes/AllClasses.php");
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
//Set Time zone
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
    //Checking itmrec_id
    if (isset($_POST['itmrec_id']) && !empty($_POST['itmrec_id']) && is_array($_POST['itmrec_id'])) {
        //Getting itmrec_id
        $postedArray = $_POST['itmrec_id'];
    } else {
        //flitmrec_id
        $postedArray = $_POST['flitmrec_id'];
    }
    //***********************************************
    // Validation: Start
    //***********************************************
    $error = '';
    $response = array();
    //Loop
    foreach ($postedArray as $val) {
        $itemid = explode('-', $val);
        $ob = "0" . $_POST['FLDOBLA' . $itemid[1]];
        $rcv = "0" . $_POST['FLDRecv' . $itemid[1]];
        $issue = "0" . $_POST['FLDIsuueUP' . $itemid[1]];
        $cb = "0" . $_POST['FLDCBLA' . $itemid[1]];
        $adjPos = "0" . $_POST['FLDReturnTo' . $itemid[1]];
        $adjNeg = "0" . $_POST['FLDUnusable' . $itemid[1]];
        $calCB = $ob + $rcv - $issue + $adjPos - $adjNeg;
        if ($calCB != $cb) {
            //Error
            $error .= 'Incorrect closing Balance for ' . $_POST['flitmname' . $itemid[1]] . '<br>';
        }
    }
    //***********************************************
    // Validation: End
    //***********************************************
    if (strlen($error) > 0) {
        $response['resp'] = 'err';
        $response['msg'] = $error;
        //Encode in json
        echo json_encode($response);
        exit;
    } else {
        //Delete query
        $delQry = "DELETE FROM tbl_wh_data WHERE `wh_id`=" . $_POST['wh_id'] . " AND RptDate='" . $_POST['RptDate'] . "' AND tbl_wh_data.item_id IN ('IT-008', 'IT-013') ";
        mysql_query($delQry) or die(mysql_error());
        //If new report
        if ($_POST['isNewRpt'] == 0) {
            //Getting add_date
            $addDate = $_POST['add_date'];
        } else {
            $addDate = date('Y-m-d H:i:s');
        }
        $lastUpdate = date('Y-m-d H:i:s');
        // Client IP
        $clientIp = getClientIp();
        //Loop
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
            //Getting wh_id
            $wh_id = $_POST['wh_id'];
            $itemid = $val;
            //Getting year
            $report_year = $_POST['yy'];
            //Getting month
            $report_month = $_POST['mm'];
            //***********************************************
            // Check if data already exists
            //***********************************************
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
            //If data not exists then insert
            if ($data['num'] == 0) {
                $queryadddata = "INSERT INTO tbl_wh_data(report_month,report_year,item_id,wh_id,wh_obl_a,wh_obl_c,
				wh_received,wh_issue_up,wh_cbl_a,wh_cbl_c,wh_adja,wh_adjb,RptDate,add_date,last_update,ip_address,created_by)  Values(		
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
                if (!empty($DEQry['RptDate'])) {
                    //End date
                    $endDate = '2015-02-01';
                    //Start date
                    $endDate = date('Y-m-d', strtotime("+1 month", strtotime($endDate)));
                    $begin = new DateTime($startDate);
                    $end = new DateTime($endDate);
                    $diff = $begin->diff($end);
                    $totalMonths = (($diff->format('%y') * 12) + $diff->format('%m'));
                    $interval = DateInterval::createFromDateString('1 month');
                    //Period
                    $period = new DatePeriod($begin, $interval, $end);
                    foreach ($period as $date) {
                        $date = $date->format("Y-m-d");
                        $qry = "SELECT REPUpdateCarryForward('$date', '$itemid', $wh_id) FROM DUAL";
                        mysql_query($qry);
                    }
                }
                /* ----------------- End ----------------- */
                //*************************************************************
            }
        }
        //*************************************************************
        // Track history
        //*************************************************************
        $sql = "INSERT INTO tbl_wh_update_history
			SET
				wh_id = '$wh_id',
				reporting_date = '" . $_POST['RptDate'] . "',
				update_on = NOW(),
				updated_by = '" . $_SESSION['user_warehouse'] . "',
				ip_address = '" . $clientIp . "' ";
        mysql_query($sql);
        //*************************************************************
        // Delete draft data
        //*************************************************************
        $delQry = "DELETE FROM tbl_wh_data_draft WHERE `wh_id`=" . $_POST['wh_id'] . " AND report_month='" . $_POST['mm'] . "' AND report_year='" . $_POST['yy'] . "' ";
        mysql_query($delQry) or die(mysql_error());

        $response['resp'] = 'ok';
        //Encode in json
        echo json_encode($response);
        exit;
    }
}