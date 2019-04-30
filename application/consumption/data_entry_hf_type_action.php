<?php
/**
 * data_entry_hf_type+action
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("includes/AllClasses.php");
//get user_province
$province = $_SESSION['user_province'];
//get user_stakeholder
$mainStk = $_SESSION['user_stakeholder'];
//get dist_id
$districtId = $_SESSION['dist_id'];
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
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $ip;
}

//add
if ($_POST['ActionType'] == 'Add') {
    // If one of the previous 3 months then delete first then add new	
    if ($_POST['isNewRpt'] == 0) {
        //delete query
        $delQry = "DELETE FROM tbl_hf_type_data WHERE facility_type_id = " . $_POST['wh_id'] . " AND reporting_date='" . $_POST['RptDate'] . "' AND district_id = $districtId ";
        mysql_query($delQry) or die(mysql_error());
        //delete query
        $delQry = "DELETE FROM tbl_hf_type_mother_care WHERE district_id = " . $districtId . " AND facility_type_id = " . $_POST['wh_id'] . " AND reporting_date='" . $_POST['RptDate'] . "' ";
        mysql_query($delQry) or die(mysql_error());
        //get add date
        $addDate = $_POST['add_date'];
    } else {
        $addDate = date('Y-m-d H:i:s');
    }
    $lastUpdate = date('Y-m-d H:i:s');
    // Client IP
    $clientIp = getClientIp();
    //check itmrec_id
    if (isset($_POST['itmrec_id']) && !empty($_POST['itmrec_id']) && is_array($_POST['itmrec_id'])) {
        //get itmrec_id
        $postedArray = $_POST['itmrec_id'];
    } else {
        //get flitmrec_id
        $postedArray = $_POST['flitmrec_id'];
    }

    foreach ($postedArray as $val) {
        $itemid = explode('-', $val);
        $FLDOBLA = "0" . $_POST['FLDOBLA' . $itemid[1]];
        $FLDRecv = "0" . $_POST['FLDRecv' . $itemid[1]];
        $FLDIsuueUP = "0" . $_POST['FLDIsuueUP' . $itemid[1]];
        $FLDCBLA = "0" . $_POST['FLDCBLA' . $itemid[1]];
        $FLDReturnTo = "0" . $_POST['FLDReturnTo' . $itemid[1]];
        $FLDUnusable = "0" . $_POST['FLDUnusable' . $itemid[1]];
        $FLDnew = "0" . $_POST['FLDnew' . $itemid[1]];
        $FLDold = "0" . $_POST['FLDold' . $itemid[1]];

        $wh_id = $_POST['wh_id'];
        $itemid = $val;
        $report_year = $_POST['yy'];
        $report_month = $_POST['mm'];

        // Check if data already exists
        $checkData = "SELECT
						COUNT(tbl_hf_type_data.pk_id) AS num
					FROM
						tbl_hf_type_data
					WHERE
						tbl_hf_type_data.facility_type_id = " . $_POST['wh_id'] . "
					AND tbl_hf_type_data.reporting_date = " . $_POST['RptDate'] . "
					AND tbl_hf_type_data.item_id = '" . $val . "'
					AND district_id = $districtId";
        $data = mysql_fetch_array(mysql_query($checkData));
        if ($data['num'] == 0) {
            //query add data
            //inserts
            //item_id,
            //facility_type_id,
            //district_id,
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
            $queryadddata = "INSERT INTO tbl_hf_type_data(
								item_id,
								facility_type_id,
								district_id,
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
							'" . $val . "',
							" . $_POST['wh_id'] . ",
							" . $districtId . ",
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

            // If previous month is edited then it should carried to the last data entry month
            /* ----------------- Start ----------------- */
            $stDate = $report_year . '-' . $report_month . '-01';
            $startDate = date('Y-m-d', strtotime("+1 month", strtotime($stDate)));
            $DEQry = mysql_fetch_array(mysql_query("SELECT
						tbl_hf_type_data.reporting_date
					FROM
						tbl_hf_type_data
					WHERE
						tbl_hf_type_data.facility_type_id = $wh_id
					AND district_id = $districtId
					ORDER BY
						tbl_hf_type_data.reporting_date DESC
					LIMIT 1"));
            //check reporting_date
            if (!empty($DEQry['reporting_date'])) {
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
                foreach ($period as $date) {
                    $date = $date->format("Y-m-d");
                    $qry = "SELECT REPUpdateCarryForwardHFType('$date', '$itemid', $wh_id, $districtId) FROM DUAL";
                    mysql_query($qry);
                }
            }
            /* ----------------- End ----------------- */
        }
    }
    $qry = "INSERT INTO tbl_hf_type_mother_care
		SET
			district_id = $districtId,
			facility_type_id = $wh_id,
			pre_natal_new = '" . $_POST['pre_natal_new'] . "',
			pre_natal_old = '" . $_POST['pre_natal_old'] . "',
			post_natal_new = '" . $_POST['post_natal_new'] . "',
			post_natal_old = '" . $_POST['post_natal_old'] . "',
			ailment_children = '" . $_POST['ailment_child'] . "',
			ailment_adults = '" . $_POST['ailment_adult'] . "',
			reporting_date = '" . $_POST['RptDate'] . "' ";
    mysql_query($qry);
    //redirecting to data_entry_hf_type
    header("location:data_entry_hf_type.php?e=ok");
    exit;
}