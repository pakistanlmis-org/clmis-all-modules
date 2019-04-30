<?php
/**
 * data_entry_hf_satelite_action
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
    //get wh_id
    $wh_id = $_POST['wh_id'];
    //delete query
    $delQry = "DELETE FROM tbl_hf_satellite_data WHERE warehouse_id = " . $wh_id . " AND reporting_date='" . $_POST['RptDate'] . "' ";
    mysql_query($delQry) or die(mysql_error());
    //delete query
    $delQry = "DELETE FROM tbl_hf_satellite_mother_care WHERE warehouse_id = " . $wh_id . " AND reporting_date='" . $_POST['RptDate'] . "' ";
    mysql_query($delQry) or die(mysql_error());
    //delete query
    $delQry = "DELETE FROM tbl_satellite_camps WHERE warehouse_id = " . $wh_id . " AND reporting_date='" . $_POST['RptDate'] . "' ";
    mysql_query($delQry) or die(mysql_error());
    // if isNewRpt == 0
    if ($_POST['isNewRpt'] == 0) {
        $addDate = $_POST['add_date'];
    } else {
        $addDate = date('Y-m-d H:i:s');
    }
    $lastUpdate = date('Y-m-d H:i:s');
    // Client IP
    $clientIp = getClientIp();

    $count = 0;
    //get data from postedArray
    foreach ($postedArray as $itemid) {
        //staticCamp
        $staticCamp = $_POST['staticCamp' . $itemid];
        //itemCategory
        $itemCategory = $_POST['flitm_category'][$count++];
//FLDIsuueUP
        $FLDIsuueUP = isset($_POST['FLDIsuueUP' . $itemid]) ? $_POST['FLDIsuueUP' . $itemid] : 0;
        //FLDnew
        $FLDnew = isset($_POST['FLDnew' . $itemid]) ? $_POST['FLDnew' . $itemid] : 0;
        //FLDold
        $FLDold = isset($_POST['FLDold' . $itemid]) ? $_POST['FLDold' . $itemid] : 0;
        //report_year 
        $report_year = $_POST['yy'];
        //report_month
        $report_month = $_POST['mm'];

        // Check if data already exists
        $checkData = "SELECT
						COUNT(tbl_hf_satellite_data.pk_id) AS num
					FROM
						tbl_hf_satellite_data
					WHERE
						tbl_hf_satellite_data.warehouse_id = " . $_POST['wh_id'] . "
					AND tbl_hf_satellite_data.reporting_date = " . $_POST['RptDate'] . "
					AND tbl_hf_satellite_data.item_id = '" . $val . "'";
        //query result
        $data = mysql_fetch_array(mysql_query($checkData));
        if ($data['num'] == 0) {
            //Add query
            //inserts
            //item_id,
            //warehouse_id,
            //issue_balance,
            //new,
            //old,
            //reporting_date,
            //created_date,
            //last_update,
            //ip_address,
            //created_by
            $queryadddata = "INSERT INTO tbl_hf_satellite_data(
								item_id,
								warehouse_id,
								issue_balance,
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
							" . $FLDIsuueUP . ",
							" . $FLDnew . ",
							" . $FLDold . ",
							'" . $_POST['RptDate'] . "',
							'" . $addDate . "',
							'" . $lastUpdate . "',
							'" . $clientIp . "',
							'" . $_SESSION['user_id'] . "'
						)";
//query result
            $rsadddata = mysql_query($queryadddata) or die(mysql_error());
            $hf_data_id = mysql_insert_id();
            $hf_type_id = $_POST['hf_type_id'];
            $counter = 1;
            foreach ($hf_type_id as $hf_type) {
                // insertion in table hf data reference by
                if ($itemid == '31' || $itemid == '32') {
                    $hf_type_id = $_POST['hf_type_id'];
                    $reffered = $_POST['reffered' . $itemid . $hf_type];

                    if ($counter == 1) {
                        //query_reff 
                        //inserts
                        //hf_data_id,
                        //hf_type_id,
                        //ref_surgeries,
                        //static,
                        //camp
                        $query_reff = "INSERT INTO tbl_hf_satellite_data_reffered_by(
														tbl_hf_satellite_data_reffered_by.hf_data_id,
														tbl_hf_satellite_data_reffered_by.hf_type_id,
														tbl_hf_satellite_data_reffered_by.ref_surgeries,
														tbl_hf_satellite_data_reffered_by.static,
														tbl_hf_satellite_data_reffered_by.camp)
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
                        //tbl_hf_satellite_data_reffered_by.hf_data_id,
                        //hf_type_id,
                        //ref_surgeries,
                        //static,
                        //camp
                        $query_reff = "INSERT INTO tbl_hf_satellite_data_reffered_by(
														tbl_hf_satellite_data_reffered_by.hf_data_id,
														tbl_hf_satellite_data_reffered_by.hf_type_id,
														tbl_hf_satellite_data_reffered_by.ref_surgeries,
														tbl_hf_satellite_data_reffered_by.static,
														tbl_hf_satellite_data_reffered_by.camp)
													Values(							
														'" . $hf_data_id . "',
														'" . $hf_type . "',
														'" . $reffered . "',    
														'',
														''
									)";
                    }
                    //query result
                    $res_reff = mysql_query($query_reff) or die(mysql_error());
                }
                $counter++;
            }
        }
    }

    //inserts
    //warehouse_id
    //pre_natal_new
    //pre_natal_old
    //post_natal_old
    //ailment_children
    //ailment_adults
    //general_ailment
    //reporting_date
    //
    $qry = "INSERT INTO tbl_hf_satellite_mother_care
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
    mysql_query($qry);

    $response['resp'] = 'ok';
    //encode in json
    echo json_encode($response);
    exit;
}