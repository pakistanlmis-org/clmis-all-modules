<?php
//echo '<pre>';print_r($_REQUEST);
//exit;    
include("../includes/classes/AllClasses.php");

$status = $_REQUEST['act'];
$userid=$_SESSION['user_id'];
$clr6_id=$_REQUEST['id'];
$response = array();

 $approve_master = "SELECT
                    clr_master.pk_id,
                    clr_master.requisition_num,
                    clr_master.approval_status
                    FROM
                    clr_master
                    WHERE
                    clr_master.pk_id = " . $clr6_id . "  ";
            //echo $approve_master;

    $res  =  mysql_query($approve_master);
    $row = mysql_fetch_assoc($res);
    $previous_status = $row ['approval_status'];

if(!empty($clr6_id)){
    foreach($_REQUEST as $key => $val){
        $a = array();
        $a = explode('_',$key);
        if($previous_status != 'Prov_Approved' )
        {
            if($a[0]=='itm'){
                $itm_id = $a[1];
                $approveClr6Detail = "update clr_details set qty_req_prov='" . $val . "', "
                . "approved_by=" . $userid . ", "
                . "approve_date='" . date('Y-m-d H:i:s') . "' "
                . "where pk_master_id=" . $clr6_id . " AND itm_id='" . $itm_id . "'";
                //echo $approveClr6Detail;
                mysql_query($approveClr6Detail) or die(mysql_error());
            }
        }
    }
    $approve_master = "update clr_master set  "
            . "approval_status='" . $status . "'  "
            . "where pk_id=" . $clr6_id . "  ";
            //echo $approve_master;

    mysql_query($approve_master) or die(mysql_error());
    
    $response['updated'] ='yes';
    $s = explode('_',$status);
    $response['status'] = $s[1];
}
else
{
    
    $response['updated'] ='no';
    $s = explode('_',$status);
    $response['status'] = $s[1];
}
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
