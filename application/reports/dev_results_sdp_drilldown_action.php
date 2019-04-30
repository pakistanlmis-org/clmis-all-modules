<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/Configuration.inc.php");
Login();
include(APP_PATH . "includes/classes/db.php");
include APP_PATH . "includes/classes/functions.php";

if(!empty($_REQUEST['wh_id'])){
    
    $qry = "DELETE from stock_out_reasons where wh_id = '".$_REQUEST['wh_id']."' AND itm_id = '".$_REQUEST['item_id']."' AND month = '".$_REQUEST['month']."' ";
    mysql_query($qry) ;
}
//echo '<pre>';print_r($_REQUEST);exit;
foreach($_REQUEST as $field_name => $field_val)
{
    $temp = array();
    $temp = explode('_',$field_name);
    if($temp[0] == 'comments' && !empty($field_val))
    {
        $wh_id  = $temp[1];
        $itm_id = $temp[2];
        $month  = $temp[3];
        
       $query="SELECT * from `stock_out_reasons` "
        
        . " WHERE "
        . "`wh_id` = '$wh_id' AND `itm_id` = '$itm_id' AND `month`='$month'";
       //echo $query;exit;
       $rs = mysql_query($query) ; 
       $rows = mysql_num_rows($rs);
       $vals = implode(',',$field_val);
       if($rows > 0 )
       {
            $query=" UPDATE `stock_out_reasons` SET ";
            $query.= " `reason`='$vals', `last_modified_by`= '".$_SESSION['user_id']."' ";
            $query.= " WHERE `wh_id` = '$wh_id' AND  `itm_id` = '$itm_id' AND `month`='$month'   ";
       }
       else
       {
            $query=" INSERT INTO `stock_out_reasons` SET ";
            $query.= " `wh_id` = '$wh_id' , `itm_id` = '$itm_id' , `month`='$month' , `reason`='$vals', `last_modified_by`= '".$_SESSION['user_id']."' ";
       }
       
       //echo $query;exit;
       mysql_query($query) ;
    }
    
    
    if($temp[0] == 'actions' && !empty($field_val))
    {
        $wh_id  = $temp[1];
        $itm_id = $temp[2];
        $month  = $temp[3];
        
       $query="SELECT * from `stock_out_reasons` "
        
        . " WHERE "
        . "`wh_id` = '$wh_id' AND `itm_id` = '$itm_id' AND `month`='$month'";
       //echo $query;exit;
       $rs = mysql_query($query) ; 
       $rows = mysql_num_rows($rs);
       $vals = implode(',',$field_val);
       if($rows > 0 )
       {
            $query=" UPDATE `stock_out_reasons` SET ";
            $query.= " `action_suggested`='$vals', `last_modified_by`= '".$_SESSION['user_id']."' ";
            $query.= " WHERE `wh_id` = '$wh_id' AND  `itm_id` = '$itm_id' AND `month`='$month'   ";
       }
       else
       {
            $query=" INSERT INTO `stock_out_reasons` SET ";
            $query.= " `wh_id` = '$wh_id' , `itm_id` = '$itm_id' , `month`='$month' , `action_suggested`='$vals', `last_modified_by`= '".$_SESSION['user_id']."' ";
       }
       
       //echo $query;exit;
       mysql_query($query) ;
    }
}
echo 'Reasons and actions Saved Successfully.';

?>