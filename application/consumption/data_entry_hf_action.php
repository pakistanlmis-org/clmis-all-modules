<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * data_entry_hf_action
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//get user_province
$province = $_SESSION['user_province'];
//get user_stakeholder
$mainStk = $_SESSION['user_stakeholder'];
//check user id
if (!isset($_SESSION['user_id'])) {
    $location = SITE_URL . 'index.php';
    ?>

    <script type="text/javascript">
        window.location = "<?php echo $location; ?>";
    </script>
    <?php
}
//time zone
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

//add
if ($_POST['ActionType'] == 'Add') {
    //check itmrec_id
    if (isset($_POST['itmrec_id']) && !empty($_POST['itmrec_id']) && is_array($_POST['itmrec_id'])) {
        //get itmrec_id
        $postedArray = $_POST['itmrec_id'];
    } else {
        //get flitmrec_id
        $postedArray = $_POST['flitmrec_id'];
    }

    // Validation: Start
    $error = '';
    $response = array();
    //loop
    foreach ($postedArray as $itemid) {
        //ob
        $ob = isset($_POST['FLDOBLA' . $itemid]) ? $_POST['FLDOBLA' . $itemid] : 0;
        //rcv
        $rcv = isset($_POST['FLDRecv' . $itemid]) ? $_POST['FLDRecv' . $itemid] : 0;
        //issue
        $issue = isset($_POST['FLDIsuueUP' . $itemid]) ? $_POST['FLDIsuueUP' . $itemid] : 0;
        //
        $cb = isset($_POST['FLDCBLA' . $itemid]) ? $_POST['FLDCBLA' . $itemid] : 0;
        //adjPos
        $adjPos = isset($_POST['FLDReturnTo' . $itemid]) ? $_POST['FLDReturnTo' . $itemid] : 0;
        //adjNeg
        $adjNeg = isset($_POST['FLDUnusable' . $itemid]) ? $_POST['FLDUnusable' . $itemid] : 0;
        //calCB
        $calCB = $ob + $rcv - $issue + $adjPos - $adjNeg;
        //check calCB 
        if ($calCB != $cb) {
            //error msg
            $error .= 'Incorrect closing Balance for ' . $_POST['flitmname' . $itemid] . '<br>';
        }
    }
    // Validation: End
    if (strlen($error) > 0) {
        //set msg
        $response['resp'] = 'err';
        $response['msg'] = $error;
        //encode in json
        echo json_encode($response);
        exit;
    } else {
        //delete query
        $delQry = "DELETE FROM tbl_hf_data WHERE tbl_hf_data.warehouse_id = " . $_POST['wh_id'] . " AND tbl_hf_data.reporting_date = '" . $_POST['RptDate'] . "'";
        mysql_query($delQry) or die(mysql_error());

        if ($_POST['isNewRpt'] == 0) {
            $addDate = $_POST['add_date'];
        } else {
            $addDate = date('Y-m-d H:i:s');
        }
        $lastUpdate = date('Y-m-d H:i:s');

        // Client IP
        $clientIp = getClientIp();

        $count = 0;
        foreach ($postedArray as $itemid) {
            $itemCategory = $_POST['flitm_category'][$count++];

            $FLDOBLA = isset($_POST['FLDOBLA' . $itemid]) ? $_POST['FLDOBLA' . $itemid] : 0;
            $FLDRecv = isset($_POST['FLDRecv' . $itemid]) ? $_POST['FLDRecv' . $itemid] : 0;
            $src_stock = isset($_POST['FLDSrcs' . $itemid]) ? $_POST['FLDSrcs' . $itemid] : array();
            $FLDIsuueUP = isset($_POST['FLDIsuueUP' . $itemid]) ? $_POST['FLDIsuueUP' . $itemid] : 0;
            $FLDCBLA = isset($_POST['FLDCBLA' . $itemid]) ? $_POST['FLDCBLA' . $itemid] : 0;
            $FLDReturnTo = isset($_POST['FLDReturnTo' . $itemid]) ? $_POST['FLDReturnTo' . $itemid] : 0;
            $FLDUnusable = isset($_POST['FLDUnusable' . $itemid]) ? $_POST['FLDUnusable' . $itemid] : 0;

            $wh_id = $_POST['wh_id'];
            $report_year = $_POST['yy'];
            $report_month = $_POST['mm'];

            // Check if data already exists
            $checkData = "SELECT
							COUNT(tbl_hf_data.pk_id) AS num
						FROM
							tbl_hf_data
						WHERE
							tbl_hf_data.warehouse_id = " . $_POST['wh_id'] . "
						AND tbl_hf_data.reporting_date = " . $_POST['RptDate'] . "
						AND tbl_hf_data.item_id = '" . $itemid . "'";
            $data = mysql_fetch_array(mysql_query($checkData));
            if ($data['num'] == 0) {
                //query 
                //inserts
                //item_id,
                //warehouse_id,
                //opening_balance,
                //received_balance,
                //issue_balance,
                //closing_balance,
                //adjustment_positive,
                //adjustment_negative,
                //reporting_date,
                //created_date,
                //last_update,
                //ip_address,
                //created_by
                $queryadddata = "INSERT INTO tbl_hf_data(
									item_id,
									warehouse_id,
									opening_balance,
									received_balance,
									issue_balance,
									closing_balance,
									adjustment_positive,
									adjustment_negative,
									reporting_date,
									created_date,
									last_update,
									ip_address,
									created_by)
							Values(							
								'" . $itemid . "',
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

                $insert_id = mysql_insert_id();
                if (count($src_stock) > 0) {
                    foreach ($src_stock as $key => $val) {
                        $querysrc = "INSERT INTO stock_sources_data(stock_sources_id,hf_data_id,received,created_date)
							Values(							
								" . $key . ",
								" . $insert_id . ",
								" . $val . ",
								'" . date("Y-m-d h:i:s") . "'
							)";

                        mysql_query($querysrc);
                    }
                }


                // If previous month is edited then it should carried to the last data entry month
                /* ----------------- Start ----------------- */
                //start date
                $stDate = $report_year . '-' . $report_month . '-01';
                $startDate = date('Y-m-d', strtotime("+1 month", strtotime($stDate)));
                //query
                //gets
                //reporting_date
                $DEQry = mysql_fetch_array(mysql_query("SELECT
							tbl_hf_data.reporting_date
						FROM
							tbl_hf_data
						WHERE
							tbl_hf_data.warehouse_id = $wh_id
						ORDER BY
							tbl_hf_data.reporting_date DESC
						LIMIT 1"));
                //check reporting_date
                if (!empty($DEQry['reporting_date']) && $itemCategory != 2) {
                    //end date
                    $endDate = $DEQry['reporting_date'];
                    $endDate = date('Y-m-d', strtotime("+1 month", strtotime($endDate)));
                    //begin
                    $begin = new DateTime($startDate);
                    //end
                    $end = new DateTime($endDate);
                    $diff = $begin->diff($end);
                    //total month
                    $totalMonths = (($diff->format('%y') * 12) + $diff->format('%m'));
                    //interval
                    $interval = DateInterval::createFromDateString('1 month');
                    //period
                    $period = new DatePeriod($begin, $interval, $end);
                    //loop
                    foreach ($period as $date) {
                        if ($date->format("Y") > 2010) {
                            $date = $date->format("Y-m-d");
                            //query
                            $qry = "SELECT REPUpdateCarryForwardHF('$date', '$itemid', $wh_id) FROM DUAL";
                            mysql_query($qry);
                        }
                    }
                }
                /* ----------------- End ----------------- */
            }
        }

        // Update AMC		
        $qry = "SELECT
					tbl_hf_data.pk_id,
					tbl_hf_data.warehouse_id,
					tbl_hf_data.item_id,
					tbl_hf_data.reporting_date
				FROM
					tbl_hf_data
				WHERE
					tbl_hf_data.is_amc_calculated = 0
				AND tbl_hf_data.warehouse_id = $wh_id
				AND tbl_hf_data.reporting_date = '" . $_POST['RptDate'] . "' ";
        $qryRes = mysql_query($qry);
        while ($row = mysql_fetch_array($qryRes)) {
            $pk_id = $row['pk_id'];
            $warehouse_id = $row['warehouse_id'];
            $itm_id = $row['item_id'];
            $reporting_date = $row['reporting_date'];

            $updateQry = "UPDATE tbl_hf_data
							SET tbl_hf_data.avg_consumption = REPgetHFAMC (
								'$reporting_date',
								$itm_id,
								$warehouse_id
							),
							tbl_hf_data.is_amc_calculated = 1 
						WHERE
							tbl_hf_data.pk_id = $pk_id ";
            mysql_query($updateQry);
        }

        // Track history
        $sql = "INSERT INTO tbl_wh_update_history
			SET
				wh_id = '$wh_id',
				reporting_date = '" . $_POST['RptDate'] . "',
				update_on = NOW(),
				updated_by = '" . $_SESSION['user_warehouse'] . "',
				ip_address = '" . $clientIp . "' ";
        mysql_query($sql);
        //check redir_url
        if (!empty($_POST['redir_url'])) {
            $url = $_POST['redir_url'];
        } else {
            $url = 'data_entry_hf.php';
        }
        $response['resp'] = 'ok';
        //encode in json
        echo json_encode($response);
        exit;
    }
}