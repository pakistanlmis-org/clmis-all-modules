<?php
include("../includes/classes/AllClasses.php");
include("include_functions_common.php");
   //echo '<pre>';print_r($_REQUEST);exit;

function func_shoot_email($to,$subject,$msg,$cc="",$page_name,$wh_id='',$insert_log=TRUE,$sent_by_user,$sent_to_user,$from="feedback@lmis.gov.pk",$reply_to="feedback@lmis.gov.pk"){
        $bcc      = 'muhahmed@ghsc-psm.org';
        $subject = addslashes($subject);
        $m1 = addslashes($msg);
        $message = '
                 <html>
                 <head>
                   <title>'.$subject.'</title>
                 </head>
                 <body>'.nl2br($m1).'</body>
                 </html>
                 ';
         $headers = "MIME-Version: 1.0" . "\r\n";
         $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
         $headers .= "From: ".$from. "\r\n" .
         "Reply-To: " .$reply_to. "\r\n" .
         "Bcc: ".$bcc."\r\n" .
         "X-Mailer: PHP/" . phpversion();
         mail($to, $subject, $message,  $headers);

         if($insert_log)
         {
            $log_q = "INSERT INTO alerts_log (
                         alerts_log.`to`,
                         alerts_log.`cc`,
                         alerts_log.`warehouse_id`,
                         alerts_log.`subject`,
                         alerts_log.`sent_by`,
                         alerts_log.body,
                         alerts_log.response, alerts_log.type, alerts_log.interface) 
                         VALUES ('$to','$cc','$wh_id','$subject','$sent_by_user','$message','','Email','$page_name')";
               //echo $log_q;exit;
             mysql_query($log_q);
        }
}

function func_send_email_for_moderation($to,$subject,$msg,$cc="",$page_name,$wh_id='',$sent_by_user,$sent_to_user,$district_id){
        $sent_by_info = get_user_basic_info($sent_by_user);
        $sent_to_info = get_user_basic_info($sent_to_user);
        
        if(!empty($wh_id)){
            $wh_info = get_wh_basic_info($wh_id);
            $wh_name = $wh_info['wh_name'];
        }
        else
        {
            
            $log_s = "SELECT tbl_locations.PkLocID,tbl_locations.LocName FROM tbl_locations   WHERE PkLocID = '".$district_id."' ";
            $rs = mysql_query($log_s);
            $dist_info = mysql_fetch_assoc($rs);
            
            $wh_name = 'SDPs of District '.$dist_info['wh_name'];
        }
        
        
        $actions = '';
        //$moderator  = 'muhahmed@ghsc-psm.org';
        $moderator  = 'jakram@ghsc-psm.org';
        $bcc        = 'muhahmed@ghsc-psm.org';
        $from       = 'no-reply@lmis.gov.pk';
        $reply_to   = 'no-reply@lmis.gov.pk';
        $mail_subject = 'Email for Moderation From '.$sent_by_info['sysusr_name'].' ,To '.$sent_to_info['sysusr_name'];
        $msg_body_orig = addslashes($msg);
        
        
         $log_q = "INSERT INTO moderation (
                     `to`,
                     `cc`,
                     `warehouse_id`,
                     `subject`,
                     `sent_by`,
                     `sent_to`,
                     body,
                     type, interface,status) 
                     VALUES ('$to','$cc','$wh_id','$subject','$sent_by_user','$sent_to_user','','Email','$page_name','pending')";
//        echo $log_q;
         mysql_query($log_q);
         $moderation_id = mysql_insert_id();
         
         if(empty($moderation_id) || $moderation_id == 0){
             echo 'Could not process further, please contact Admin.Error : Could not create moderation ID';
             exit;
         }
         
        $server = $_SERVER['SERVER_NAME'];
        if($_SERVER['SERVER_NAME'] =='beta.lmis.gov.pk') $server = $_SERVER['SERVER_NAME'].'/clmisapp';
        
        $actions=' <a href="'.$server.'/application/dashboard/moderation_proceed.php?moderation_id='.$moderation_id.'" style="color:green">Proceed (Message is OK)</a><br/> 
                    <a href="'.$server.'/application/dashboard/moderation_junk.php?moderation_id='.$moderation_id.'" style="color:red">Archive (Message is JUNK)</a><br/> 
                    <a href="'.$server.'/application/dashboard/moderation_reply.php?moderation_id='.$moderation_id.'" style="color:red">Reply Back (Needs some changes)</a><br/> ';
        
        $message = '
                 <html>
                 <head>
                   <title>'.$subject.'</title>
                 </head>
                 <body>Following Email was generated from LMIS System . It is presented before you for moderation.
                    <br/>
                        Details are as follows :
                    <br/>
                    <table border = "1">
                        <tr><td width="25%">Sent By: </td><td width="75%">'.$sent_by_info['sysusr_name'].' </td></tr> 
                        <tr><td>Sent To </td><td>'.$sent_to_info['sysusr_name'] .' </td></tr> 
                        <tr><td>Regarding Warehouse : </td><td>'.$wh_name .'  </td></tr> 
                        <tr><td>Subject : </td><td>'.$subject .'  </td></tr> 
                        <tr><td>Email Message :</td><td><p style="color:blue">'.nl2br($msg_body_orig).'</p></td></tr> 
                        <tr>
                            <td>Please proceed with any one of these options :</td>
                            <td>
                               '.$actions.'
                            </td>
                        </tr>
                    </table>

                    </body>
                 </html>
                 ';
         $headers = "MIME-Version: 1.0" . "\r\n";
         $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
         $headers .= "From: ".$from. "\r\n" .
         "Reply-To: " .$reply_to. "\r\n" .
         "Bcc: ".$bcc."\r\n" .
         "X-Mailer: PHP/" . phpversion();
         mail($moderator, $mail_subject, $message,  $headers);

        $log_q = "UPDATE moderation SET `body` = '".$msg_body_orig."' WHERE pk_id = '".$moderation_id."' ";
        mysql_query($log_q);
       
}

