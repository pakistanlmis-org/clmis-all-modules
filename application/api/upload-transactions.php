<?php

/**
 * upload-transactions
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required files
include("../../application/includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include('auth.php');

if (isset($_GET['qty']) && isset($_GET['placement_loc_id']) && isset($_GET['batch_id']) && isset($_GET['detail_id']) && isset($_GET['created_date']) && isset($_GET['user_id']) && isset($_GET['placement_loc_type_id'])) {

    $qty = $_GET['qty'];
    $placement_loc_id = $_GET['placement_loc_id'];
    $detail_id = $_GET['detail_id'];
    $batch_id = $_GET['batch_id'];
    $created_date = $_GET['created_date'];
    $user_id = $_GET['user_id'];
    $loc_type_id = $_GET['placement_loc_type_id'];

    $query = "INSERT INTO placements SET quantity='" . $qty . "',placement_location_id='" . $placement_loc_id . "',stock_batch_id='" . $batch_id . "',stock_detail_id='" . $detail_id . "',placement_transaction_type_id='" . $loc_type_id . "',created_by='" . $user_id . "',created_date='" . $created_date . "'";

    $rs = mysql_query($query) or die(print mysql_error());
    if ($rs) {
        print json_encode("success");
    }
} else {
    print json_encode('failure');
}
// example: http://localhost/application/api/locations.php?ID=4
?>