<?php

//Including AllClasses file
include("../includes/classes/AllClasses.php");
//Initializing variables
//status
$status = "Approved";
//approved
$approved = '';
//Getting user_id
$userid = $_SESSION['user_id'];
//Checking clr6_id
if (isset($_REQUEST['clr6_id']) && !empty($_REQUEST['clr6_id'])) {
    //Getting clr6_id
    $clr6_id = $_REQUEST['clr6_id'];
}
//Checking rq_no
if (isset($_REQUEST['rq_no']) && !empty($_REQUEST['rq_no'])) {
    //Getting rq_no
    $rq = $_REQUEST['rq_no'];
}
//Checking warehouse
if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
    //Getting warehouse
    $issue_to = $_REQUEST['warehouse'];
} else {
    header("location:" . SITE_URL . "plmis_src/operations/issue.php?warehouse=1");
    exit;
}
//Checking product_status
if (isset($_REQUEST['product_status']) && !empty($_REQUEST['product_status'])) {
    //Getting product_status
    $product_status = $_REQUEST['product_status'];
}
//Checking warehouse
if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
    //Getting warehouse
    $warehouse = $_REQUEST['warehouse'];
}
//Checking itmrec
if (isset($_REQUEST['itmrec']) && !empty($_REQUEST['itmrec'])) {
    //Getting itmrec
    $itemrec = $_REQUEST['itmrec'];
}
//Checking qty_approved
if (isset($_REQUEST['qty_approved']) && !empty($_REQUEST['qty_approved'])) {
    //Getting qty_approved
    $qty = $_REQUEST['qty_approved'];
}
//Checking approve
if (isset($_REQUEST['approve']) && !empty($_REQUEST['approve'])) {
    //Getting approve
    $approveItm = $_REQUEST['approve'];
}
//Checking remarks
if (isset($_REQUEST['remarks_central']) && !empty($_REQUEST['remarks_central'])) {
    //Getting qty_available
    $remarks_central = $_REQUEST['remarks_central'];
}
//Checking qty_available
if (isset($_REQUEST['qty_available']) && !empty($_REQUEST['qty_available'])) {
    //Getting qty_available
    $qtyAvailable = $_REQUEST['qty_available'];
}

//Checking $clr6_id
if (!empty($clr6_id)) {
    
    /*echo '<pre>';
    print_r($_REQUEST);
    print_r($_SESSION);
    exit;*/

    
    
    //Update clr details
    $qry = "UPDATE clr_details SET approve_qty = 0 WHERE pk_master_id=" . $clr6_id . " AND approval_status != 'Issued' ";
    mysql_query($qry);
    
    
    if( !empty($_SESSION['user_level']) && $_SESSION['user_level']=='1')
    {
        //only Central Level approvals.
        
        $national_store_id = $_SESSION['user_warehouse'];
        
        $status = 'Approved';
        foreach ($itemrec as $key => $value) {
            $approvedQty = (float)str_replace(',', '', $qty[$key]);
            $availableQty = (float)str_replace(',', '', $qtyAvailable[$key]);
            $remarks = $remarks_central[$key];
            $itm_id = $itemrec[$key];
            //checking product_status
            if ($product_status[$key] != 'Issued') {
                
                    $approved = true;
                    $status = 'Approved';
                    
                    $approveClr6Detail = "update clr_details set available_qty='" . $availableQty . "',approve_qty='" . $approvedQty . "',qty_req_central='" . $approvedQty . "', remarks_central='".$remarks."', approval_status='" . $status . "',approved_by=" . $userid . ", approve_date='" . date('Y-m-d H:i:s') . "' where pk_master_id=" . $clr6_id . " AND itm_id='" . $itm_id . "'";
                    $resUpdateClrDetail = mysql_query($approveClr6Detail) or die(mysql_error());
                 
                //echo $approveClr6Detail;
                
            }
        }
        //exit;
        
    }

   
    //Query for check Detail Status
    $checkDetailStatus = "SELECT
                                    clr_details.pk_id
                            FROM
                                    clr_details
                            WHERE
                                    clr_details.pk_master_id = $clr6_id
                            AND clr_details.approval_status = 'Issued' ";
    if (mysql_num_rows(mysql_query($checkDetailStatus)) == 0) {
        
        //$qry = "UPDATE clr_master      SET approval_status = '$status',requested_by='$userid',requisition_to = '$national_store_id' WHERE pk_id = $clr6_id ";
        $qry = "UPDATE clr_master     SET approval_status = '$status' WHERE pk_id = $clr6_id ";
        mysql_query($qry);
        
         $q_log = "INSERT INTO clr_master_log  SET master_id= '$clr6_id',wh_id = '".$_REQUEST['warehouse']."',approval_status = '$status',requested_by='$userid',log_timestamp = now(),user_id='$userid',approval_level='central' " ;
        
        mysql_query($q_log);
    }
}
//Redirecting to im/requisitions

header("location: " . APP_URL . "im/requisitions.php");
exit;
?>