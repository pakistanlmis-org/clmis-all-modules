<?php
error_reporting(0); 
require_once("../includes/classes/Configuration.inc.php");
require_once("../includes/classes/db.php");

include(PUBLIC_PATH . "/html/header.php");
		//Including top file
//        include PUBLIC_PATH . "html/top.php";
//		//Including top_im file
//        include PUBLIC_PATH . "html/top_im.php";
//this file should be open, dont need authentication here.
//echo '<pre>';print_r($_SERVER);exit;
?>
<html>
    <body>
    <div class="header navbar"> 
        <div class="header-inner"> 
            <a class="navbar-brand" href="http://c.lmis.gov.pk:80/" style="margin-top: -10px;"> <img src="http://c.lmis.gov.pk:80/public/assets/admin/layout/img/landing-images/contraceptive-logo.png" height="55" width="323" alt="vaccine LMIS"> </a> 
        </div>
    </div>
        
    
<?php
$log_s = "SELECT * FROM moderation   WHERE pk_id = '".$_REQUEST['moderation_id']."' ";
$rs = mysql_query($log_s);
$mod = mysql_fetch_assoc($rs);
//echo '<pre>';
//print_r($mod);
//exit;
if($mod['status']=='pending'){
    //send the msg now
    $to      = $mod['to'];
    $cc      = $mod['cc'];
    $subject = $mod['subject'];
    $msg     = $mod['body'];
    $page_name = $mod['interface'];
    $sent_by_user = $mod['sent_by'];
    $sent_to_user = $mod['sent_to'];
    $wh_id        = $mod['warehouse_id'];
    
//    echo $mod['type'];exit;
    
    if($mod['type'] == 'SMS'){
        include_once "include_functions_sms.php";
        func_shoot_sms($to,$subject,$msg,$cc,$page_name,$wh_id,TRUE,$sent_by_user,$sent_to_user);
    }
    else{
        
        include_once "include_functions_email.php";
        func_shoot_email($to,$subject,$msg,$cc,$page_name,$wh_id,TRUE,$sent_by_user,$sent_to_user);
    }
    
    $log_q = "UPDATE moderation SET `status` = 'sent' WHERE pk_id = '".$_REQUEST['moderation_id']."' ";
    //echo $log_q;exit;
    mysql_query($log_q);
    
    if(!empty($_REQUEST['redirect'])){
        redirect($_REQUEST['redirect'].'.php');
        exit;
    }
?>
<div class="row well well-dark">
    <div class="portlet   dark center">
    <h4>Processing complete. Email/SMS will be delivered shortly. 
     <br/><hr>You may close this window now.
    </h4> 
    </div>
</div>
    
<?php
}
else{
    ?>
    <div class="row well well-dark">
    <div class="portlet   dark center">
    <h4>This sms/email has  <span style="color:red">already been processed.</span> You can not take any action.
        <br/><hr>You may close this window now.
     </h4> 
    </div>
</div>
    
    <?php
}
?>
    
    </body></html>