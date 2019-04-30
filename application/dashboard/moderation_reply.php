<?php
error_reporting(0); 
require_once("../includes/classes/Configuration.inc.php");
require_once("../includes/classes/db.php");
include_once "include_functions_email.php";

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

//    echo '<pre>';print_r($mod);exit;


    $userSql = "SELECT
                      sysuser_tab.user_level,
                      sysuser_tab.usrlogin_id,
                      sysuser_tab.sysusr_ph,
                      sysuser_tab.sysusr_cell,
                      sysuser_tab.sysusr_email,
                      sysuser_tab.sysusr_name,
                      stakeholder.stkname
                  FROM
                  sysuser_tab
                  INNER JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
              WHERE
                  sysuser_tab.UserID = '".$mod['sent_by']."' ";
  $userResult = mysql_query($userSql) or die("Error " . $userSql);
  $rowUser = mysql_fetch_assoc($userResult);
//    efcho '<pre>';print_r($rowUser);exit;
  
  
      $userSql = "SELECT
                        sysuser_tab.user_level,
                        sysuser_tab.usrlogin_id,
                        sysuser_tab.sysusr_ph,
                        sysuser_tab.sysusr_cell,
                        sysuser_tab.sysusr_email,
                        sysuser_tab.sysusr_name,
                        stakeholder.stkname
                    FROM
                    sysuser_tab
                    INNER JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
                WHERE
                    sysuser_tab.UserID = '".$mod['sent_to']."' ";
    $userResult = mysql_query($userSql) or die("Error " . $userSql);
    $rowUser_sent_to = mysql_fetch_assoc($userResult);

    $sysusr_name    = $rowUser['sysusr_name'];
    $sysusr_email   = $rowUser['sysusr_email'];
    $cell   = $rowUser['sysusr_ph'];
//echo '<pre>';
//print_r($mod);
//exit;
if(isset($_REQUEST['submit'])){
//    print_r($_REQUEST);
//    exit;
    

    if($mod['type']=='SMS'){
        $to      = $sysusr_email;
        $cc      = $mod['cc'];
        $subject = 'Moderators Reply :'.$mod['subject'];

        $msg     = 'Your SMS was not approved by LMIS Moderator.<br/><br/><br/>';
        $msg     .= '<b>Moderator replies :</b><br/>'.$_REQUEST['reply_msg'];
        $msg     .= '<br/><br/><br/><br/><br/>';
        $msg     .= '**********************************************<br/>';
        $msg    .= '<b>This reply was sent to you in Response to the following SMS:</b><br/><br/> '.$mod['body'];
        $msg     .= '<br/><br/>';
        $page_name = $mod['interface'];
        $sent_by_user = $_SESSION['user_id'];
        $sent_to_user = $mod['sent_by'];
        $wh_id        = $mod['warehouse_id'];

        func_shoot_email($to,$subject,$msg,$cc,$page_name,$wh_id,TRUE,$sent_by_user,$sent_to_user);
    }
    else
    {
        //replying back to user
        $to      = $sysusr_email;
        $cc      = $mod['cc'];
        $subject = 'Moderators Reply :'.$mod['subject'];

        $msg     = 'Your EMAIL message was not approved by LMIS Moderator.<br/><br/><br/>';
        $msg     .= '<b>Moderator replies :</b><br/>'.$_REQUEST['reply_msg'];
        $msg     .= '<br/><br/><br/><br/><br/>';
        $msg     .= '**********************************************<br/>';
        $msg    .= '<b>This reply was sent to you in Response to the following EMAIL:</b><br/><br/> '.$mod['body'];
        $msg     .= '<br/><br/>';
        $page_name = $mod['interface'];
        $sent_by_user = $_SESSION['user_id'];
        $sent_to_user = $mod['sent_by'];
        $wh_id        = $mod['warehouse_id'];

    //    echo $to;
    //    exit;
        func_shoot_email($to,$subject,$msg,$cc,$page_name,$wh_id,TRUE,$sent_by_user,$sent_to_user);
    }
    
    
    $log_q = "UPDATE moderation SET `status` = 'replied_back', remarks= '<b>Reply:<br/></b>".addslashes($_REQUEST['reply_msg'])."' WHERE pk_id = '".$_REQUEST['moderation_id']."' ";
    mysql_query($log_q);
    
    
    redirect('moderation_list.php');
    exit;
}
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
    
    
//    func_shoot_email($to,$subject,$msg,$cc,$page_name,$wh_id,TRUE,$sent_by_user,$sent_to_user);
//    
//    $log_q = "UPDATE moderation SET `status` = 'sent' WHERE pk_id = '".$_REQUEST['moderation_id']."' ";
//    //echo $log_q;exit;
//    mysql_query($log_q);
?>
<div class="row well well-dark">
    <div class="portlet   dark center">
        <form id="frm" action="">
            <table class="table table-bordered table-hover">
                <tr>
                    <td colspan="2" class="info">
                        <h4>Moderation response back to the <?=$mod['type']?>. </h4>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    
                       <b><?=$sysusr_name?></b> sent <?=$mod['type']?> to <b><?=$rowUser_sent_to['sysusr_name']?></b>
                    </td>
                </tr>
                <tr>
                    <td>
                    Message:
                    </td>
                    <td>
                       <?= nl2br($mod['body'])?>
                    </td>
                </tr>
                <tr>
                    <td>
                    Reply back to <?=$sysusr_name?>:
                    </td>
                    <td>
                        <input type="hidden" name="reply_back_to_user"  value="<?=$sent_by_user?>">
                        <input type="hidden" name="moderation_id"  value="<?=$_REQUEST['moderation_id']?>">
                        <textarea rows="10" cols="50" name="reply_msg" maxlength="<?=(($mod['type']=='SMS')?'':'')?>"></textarea>
                    </td>
                </tr>
                <tr><td>
                     
                    </td>
                    <td align="center">
                    <button type="submit" name="submit" class="btn green" value="submit">Send Reply</button>
                    </td>
                </tr>
                 
                </h4> 
            </table>
        </form>
    </div>
</div>
    
<?php
}
else{
    ?>
    <div class="row well well-dark">
    <div class="portlet   dark center">
        <h4>This sms/email has <span style="color:red">already been processed.</span> You can not take any action.
        <br/><hr>You may close this window now.
     </h4> 
    </div>
</div>
    
    <?php
}
?>
    
    </body></html>