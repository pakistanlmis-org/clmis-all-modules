<?php
//echo '<pre>';print_r($_REQUEST);exit;
ini_set('max_execution_time', 0);
//$_POST=$_REQUEST;
/**
 * data_entry_hf_pwd_action
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
//check user_id
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
 * getClientIp
 * 
 * @return type
 */
function getClientIp() {
    // $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $ip = $_SERVER['REMOTE_ADDR'];
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
        if (!in_array($itemid, array(31, 32))) {
            //ob
            $ob = isset($_POST['FLDOBLA' . $itemid]) ? $_POST['FLDOBLA' . $itemid] : 0;
            //rcv
            $rcv = isset($_POST['FLDRecv' . $itemid]) ? $_POST['FLDRecv' . $itemid] : 0;
            //issue
            $issue = isset($_POST['FLDIsuueUP' . $itemid]) ? $_POST['FLDIsuueUP' . $itemid] : 0;
            //cb 
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
    }
    // Validation: End
    if (strlen($error) > 0) {
        $response['resp'] = 'err';
        $response['msg'] = $error;
        //encode in json
        echo json_encode($response);
        exit;
    } else {
        //delete query
        $delQry = "DELETE FROM tbl_hf_data WHERE warehouse_id = " . $_POST['wh_id'] . " AND reporting_date='" . $_POST['RptDate'] . "' ";
        //execute query
        mysql_query($delQry) or die(mysql_error());
        //delete query
        $delQry = "DELETE FROM tbl_hf_mother_care WHERE warehouse_id = " . $_POST['wh_id'] . " AND reporting_date='" . $_POST['RptDate'] . "' ";
        //execute query
        mysql_query($delQry) or die(mysql_error());

        if ($province == 3) {
            //delete query
            $delQry = "DELETE FROM tbl_hf_non_program_count WHERE warehouse_id = " . $_POST['wh_id'] . " AND reporting_date='" . $_POST['RptDate'] . "' ";
            //query result
            mysql_query($delQry) or die(mysql_error());
        }
        //check if new report
        if ($_POST['isNewRpt'] == 0) {
            $addDate = $_POST['add_date'];
        } else {
            $addDate = date('Y-m-d H:i:s');
        }
        $lastUpdate = date('Y-m-d H:i:s');
        // Client IP
        $clientIp = getClientIp();

        $count = 0;
        //loop
        foreach ($postedArray as $itemid) {
            $staticCamp = isset($_POST['staticCamp' . $itemid]) ? $_POST['staticCamp' . $itemid] : '';
            $itemCategory = $_POST['flitm_category'][$count++];

            $FLDOBLA = isset($_POST['FLDOBLA' . $itemid]) ? $_POST['FLDOBLA' . $itemid] : 0;
            $FLDRecv = isset($_POST['FLDRecv' . $itemid]) ? $_POST['FLDRecv' . $itemid] : 0;
            $FLDIsuueUP = isset($_POST['FLDIsuueUP' . $itemid]) ? $_POST['FLDIsuueUP' . $itemid] : 0;
            $FLDCBLA = isset($_POST['FLDCBLA' . $itemid]) ? $_POST['FLDCBLA' . $itemid] : 0;
            $FLDReturnTo = isset($_POST['FLDReturnTo' . $itemid]) ? $_POST['FLDReturnTo' . $itemid] : 0;
            $FLDUnusable = isset($_POST['FLDUnusable' . $itemid]) ? $_POST['FLDUnusable' . $itemid] : 0;
            $FLDnew = isset($_POST['FLDnew' . $itemid]) ? $_POST['FLDnew' . $itemid] : 0;
            $FLDold = isset($_POST['FLDold' . $itemid]) ? $_POST['FLDold' . $itemid] : 0;

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
                //insert query
                //inserts
                //item_id,
                //warehouse_id,
                //opening_balance,
                //received_balance,
                //issue_balance,
                //closing_balance,
                //adjustment_positive,
                //adjustment_negative,
                //new,
                //old,
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
									new,
									old,
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
								" . $FLDnew . ",
								" . $FLDold . ",
								'" . $_POST['RptDate'] . "',
								'" . $addDate . "',
								'" . $lastUpdate . "',
								'" . $clientIp . "',
								'" . $_SESSION['user_id'] . "'
							)";

                $rsadddata = mysql_query($queryadddata) or die(mysql_error());
                $hf_data_id = mysql_insert_id();
                $hf_type_id = $_POST['hf_type_id'];
                $counter = 1;
                //loop
                if (!empty($hf_type_id) && is_array($hf_type_id)) {
                    //print_r($hf_type_id);

                    foreach ($hf_type_id as $hf_type) {
                        // insertion in table hf data reference by
                        if ($itemid == '31' || $itemid == '32') {
                            $hf_type_id = $_POST['hf_type_id'];
                            $reffered = $_POST['reffered' . $itemid . $hf_type];

                            if ($counter == 1) {
                                //query_reff 
                                //inserts
                                //tbl_hf_data_reffered_by.hf_data_id,
                                //tbl_hf_data_reffered_by.hf_type_id,
                                //tbl_hf_data_reffered_by.ref_surgeries,
                                //tbl_hf_data_reffered_by.static,
                                //tbl_hf_data_reffered_by.camp
                                $query_reff = "INSERT INTO tbl_hf_data_reffered_by(
																tbl_hf_data_reffered_by.hf_data_id,
																tbl_hf_data_reffered_by.hf_type_id,
																tbl_hf_data_reffered_by.ref_surgeries,
																tbl_hf_data_reffered_by.static,
																tbl_hf_data_reffered_by.camp)
															Values(							
																'" . $hf_data_id . "',
																'" . $hf_type . "',
																'" . $reffered . "',    
																'" . $staticCamp[0] . "',
																'" . $staticCamp[1] . "'
																)";
                            } else {
                                //query_reff
                                //inserts
                                //tbl_hf_data_reffered_by.hf_data_id,
                                //tbl_hf_data_reffered_by.hf_type_id,
                                //tbl_hf_data_reffered_by.ref_surgeries,
                                //tbl_hf_data_reffered_by.static,
                                //tbl_hf_data_reffered_by.camp
                                $query_reff = "INSERT INTO tbl_hf_data_reffered_by(
																tbl_hf_data_reffered_by.hf_data_id,
																tbl_hf_data_reffered_by.hf_type_id,
																tbl_hf_data_reffered_by.ref_surgeries,
																tbl_hf_data_reffered_by.static,
																tbl_hf_data_reffered_by.camp)
															Values(							
																'" . $hf_data_id . "',
																'" . $hf_type . "',
																'" . $reffered . "',    
																'',
																''
											)";
                            }
                            $res_reff = mysql_query($query_reff) or die(mysql_error());
                        }
                        $counter++;
                    }
                } else {
                    if ($itemid == '31' || $itemid == '32') {
                        $hf_type_id = $_POST['hf_type_id'];
                        //if ($hf_type_id == 2 || $hf_type_id == 3) {
                            $reffered = $_POST['FLDIsuueUP' . $itemid];
                            $query_reff = "INSERT INTO tbl_hf_data_reffered_by(
                                            tbl_hf_data_reffered_by.hf_data_id,
                                            tbl_hf_data_reffered_by.hf_type_id,
                                            tbl_hf_data_reffered_by.ref_surgeries,
                                            tbl_hf_data_reffered_by.static,
                                            tbl_hf_data_reffered_by.camp)
                                    Values(							
                                            '" . $hf_data_id . "',
                                            '" . $hf_type_id . "',
                                            '" . $reffered . "',    
                                            '0',
                                            '0'
                                            )";
                            $res_reff = mysql_query($query_reff) or die(mysql_error());
                        }
                    //}
                }

                // If previous month is edited then it should carried to the last data entry month
                /* ----------------- Start ----------------- */
                //start date
                $stDate = $report_year . '-' . $report_month . '-01';
                $startDate = date('Y-m-d', strtotime("+1 month", strtotime($stDate)));
                //query
                //gets
                //report date
                $DEQry = mysql_fetch_array(mysql_query("SELECT
							tbl_hf_data.reporting_date
						FROM
							tbl_hf_data
						WHERE
							tbl_hf_data.warehouse_id = $wh_id
						ORDER BY
							tbl_hf_data.reporting_date DESC
						LIMIT 1"));
                //check reporting date
                if (!empty($DEQry['reporting_date']) && $itemCategory != 2) {
                    //end date
                    $endDate = $DEQry['reporting_date'];
                    $endDate = date('Y-m-d', strtotime("+1 month", strtotime($endDate)));
                    //begin
                    $begin = new DateTime($startDate);
                    //end
                    $end = new DateTime($endDate);
                    //diff
                    $diff = $begin->diff($end);
                    //total month
                    $totalMonths = (($diff->format('%y') * 12) + $diff->format('%m'));
                    //interval
                    $interval = DateInterval::createFromDateString('1 month');
                    //period
                    $period = new DatePeriod($begin, $interval, $end);
                    //loop
                    foreach ($period as $date) {
                        $date = $date->format("Y-m-d");
                        //query
                        $qry = "SELECT REPUpdateCarryForwardHF('$date', '$itemid', $wh_id) FROM DUAL";
                        //result
                        mysql_query($qry);
                    }
                }
                /* ----------------- End ----------------- */
            }
        }

        // Track history
        $sql = "INSERT INTO tbl_wh_update_history
			SET
				wh_id = '$wh_id',
				reporting_date = '" . $_POST['RptDate'] . "',
				update_on = NOW(),
				updated_by = '" . $_SESSION['user_warehouse'] . "',
				ip_address = '" . $clientIp . "' ";
        //execute query
        mysql_query($sql);

        $qry = "INSERT INTO tbl_hf_mother_care
			SET
				warehouse_id = $wh_id,
				pre_natal_new = '" . (isset($_POST['pre_natal_new']) ? $_POST['pre_natal_new'] : 0) . "',
				pre_natal_old = '" . (isset($_POST['pre_natal_old']) ? $_POST['pre_natal_old'] : 0) . "',
				post_natal_new = '" . (isset($_POST['post_natal_new']) ? $_POST['post_natal_new'] : 0) . "',
				post_natal_old = '" . (isset($_POST['post_natal_old']) ? $_POST['post_natal_old'] : 0) . "',
				ailment_children = '" . (isset($_POST['ailment_child']) ? $_POST['ailment_child'] : 0) . "',
				ailment_adults = '" . (isset($_POST['ailment_adult']) ? $_POST['ailment_adult'] : 0) . "',
				general_ailment = '" . (isset($_POST['general_ailment']) ? $_POST['general_ailment'] : 0) . "', 
				reporting_date = '" . (isset($_POST['RptDate']) ? $_POST['RptDate'] : 0) . "' ";
        //execute query
        mysql_query($qry);

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

        if ($province == 3 && isset($_POST['total_hf'])) {
            $qry = "INSERT INTO tbl_hf_non_program_count
				SET
					warehouse_id = $wh_id,
					total_facilities =   '" . $_POST['total_hf'] . "',
					reporting_date = '" . $_POST['RptDate'] . "' ";
            //execute query
            mysql_query($qry);
        }
        $response['resp'] = 'ok';
        //encode in json
        echo json_encode($response);
        exit;
    }
}