<?php

/**
 * clr6_approve_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
//Initializing variables
//status
$status = "Pending";
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
if (isset($_REQUEST['remarks_dist_lvl2']) && !empty($_REQUEST['remarks_dist_lvl2'])) {
    //Getting qty_available
    $remarks_dist_lvl2 = $_REQUEST['remarks_dist_lvl2'];
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
    
    
    if(!empty($_SESSION['user_role']) && $_SESSION['user_role']=='23' && !empty($_SESSION['user_level']) && $_SESSION['user_level']=='3')
    {
        //only District level2 approvals.
        
        
            
        /*$sel_q="SELECT
                sysuser_tab.usrlogin_id,
                sysuser_tab.stkid,
                sysuser_tab.province,
                sysuser_tab.user_level,
                sysuser_tab.sysusr_type,
                sysuser_tab.whrec_id
                FROM
                sysuser_tab
                WHERE
                sysuser_tab.province = '".$_SESSION['user_province1']."' AND
                sysuser_tab.stkid = '".$_SESSION['user_stakeholder1']."' AND
                sysuser_tab.user_level = 2 
                limit 1";    */
        $sel_q="
                    SELECT

                wh_user.wh_id as whrec_id

                FROM
                wh_user
                INNER JOIN sysuser_tab ON sysuser_tab.UserID = wh_user.sysusrrec_id
                WHERE
                sysuser_tab.province = '".$_SESSION['user_province1']."' AND
                sysuser_tab.stkid = '".$_SESSION['user_stakeholder1']."' AND
                sysuser_tab.user_level = 2
               
                limit 1";
        


        
        $rs_q = mysql_query($sel_q);
        if (mysql_num_rows($rs_q) > 0) {
            $row = mysql_fetch_array($rs_q);
             $provincial_store_id = $row['whrec_id'];
        }
        
        $status = 'Dist_Approved';
        foreach ($itemrec as $key => $value) {
            $approvedQty  = (float)str_replace(',', '', $qty[$key]);
            $availableQty = (float)str_replace(',', '', $qtyAvailable[$key]);
            $remarks = $remarks_dist_lvl2[$key];
            $itm_id = $itemrec[$key];
            //checking product_status
            if ($product_status[$key] != 'Issued') {
                
                $approved = true;
                
               $approveClr6Detail = "update clr_details set available_qty='" . $availableQty . "', qty_req_dist_lvl2='" . $approvedQty . "', remarks_dist_lvl2='".$remarks."',approval_status='" . $status . "',approved_by=" . $userid . ", approve_date='" . date('Y-m-d H:i:s') . "' where pk_master_id=" . $clr6_id . " AND itm_id='" . $itm_id . "'";
                $resUpdateClrDetail = mysql_query($approveClr6Detail) or die(mysql_error());
                
            }
        }
        
        
    }
    else if( !empty($_SESSION['user_level']) && $_SESSION['user_level']=='2')
    {
        //Provincial Level approvals.
        $status = ($approved === true) ? 'Prov_Approved' : 'Denied';
    }
    else if( !empty($_SESSION['user_level']) && $_SESSION['user_level']=='1')
    {
        //National Level approvals.
        $status = 'Approved';
        
    }


    /*foreach ($itemrec as $key => $value) {
        $approvedQty = str_replace(',', '', $qty[$key]);
        $availableQty = str_replace(',', '', $qtyAvailable[$key]);
        $itm_id = $itemrec[$key];
        //checking product_status
        if ($product_status[$key] != 'Issued') {
            if (($approveItm[$key] && $approvedQty > 0)) {
                $approved = true;
                $status = 'Approved';
                $approveClr6Detail = "update clr_details set available_qty=" . $availableQty . ",approve_qty=" . $approvedQty . ", approval_status='" . $status . "',approved_by=" . $userid . ", approve_date='" . date('Y-m-d H:i:s') . "' where pk_master_id=" . $clr6_id . " AND itm_id='" . $itm_id . "'";
                $resUpdateClrDetail = mysql_query($approveClr6Detail) or die(mysql_error());
            } else {
                $status = 'Denied';
                $approveClr6Detail = "update clr_details set available_qty=" . $availableQty . ",approval_status='" . $status . "',approved_by=" . $userid . ", approve_date='" . date('Y-m-d H:i:s') . "' where pk_master_id=" . $clr6_id . " AND itm_id='" . $itm_id . "'";
                $resUpdateClrDetail = mysql_query($approveClr6Detail) or die(mysql_error());
            }
        }
    }*/
   
    //Query for check Detail Status
    $checkDetailStatus = "SELECT
                                    clr_details.pk_id
                            FROM
                                    clr_details
                            WHERE
                                    clr_details.pk_master_id = $clr6_id
                            AND clr_details.approval_status = 'Issued' ";
    if (mysql_num_rows(mysql_query($checkDetailStatus)) == 0) {
        
        $qry = "UPDATE clr_master      SET approval_status = '$status',requested_by='$userid' WHERE pk_id = $clr6_id ";
        mysql_query($qry);
        
         $q_log = "INSERT INTO clr_master_log  SET master_id= '$clr6_id',wh_id = '".$_REQUEST['warehouse']."',approval_status = '$status',requested_by='$userid',log_timestamp = now(),user_id='$userid',approval_level='dist_lvl2' " ;
        
        mysql_query($q_log);
    }
}
//Redirecting to im/requisitions
if( !empty($_SESSION['user_level']) && ($_SESSION['user_level']=='3' || $_SESSION['user_level']=='2'))
    header("location: " . APP_URL . "im/requisition_approvals.php");
else
    header("location: " . APP_URL . "im/requisitions.php");
exit;
?>