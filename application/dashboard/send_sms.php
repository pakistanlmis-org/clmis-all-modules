<?php
ini_set('max_execution_time', 60);
//phpinfo();
include("../includes/classes/AllClasses.php");
require("../includes/classes/clsLogin.php");


$created_by = $modified_by = $_SESSION['user_id'];
$user_id = $_REQUEST['user_id'];
$to_list_sms = array();
$cc ='';

$userSql = "SELECT
                sysuser_tab.user_level,
                sysuser_tab.usrlogin_id,
                sysuser_tab.sysusr_ph,
                sysuser_tab.sysusr_cell,
                sysuser_tab.sysusr_email,
                sysuser_tab.sysusr_name,
                stakeholder.stkname,
                prov.LocName as prov_name,
                tbl_warehouse.wh_name,
                tbl_warehouse.wh_id,
                district.LocName as dist_name,
                tbl_warehouse.dist_id
            FROM
                sysuser_tab
            INNER JOIN stakeholder ON sysuser_tab.stkid = stakeholder.stkid
            INNER JOIN tbl_locations AS prov ON sysuser_tab.province = prov.PkLocID
            INNER JOIN tbl_warehouse ON sysuser_tab.whrec_id = tbl_warehouse.wh_id
            INNER JOIN tbl_locations AS district ON tbl_warehouse.dist_id = district.PkLocID
            WHERE
                sysuser_tab.UserID = $user_id";
//echo $userSql;exit;
$userResult = mysql_query($userSql) or die("Error " . $userSql);
$rowUser = mysql_fetch_assoc($userResult);
//echo '<pre>';print_r($rowUser);exit;

$sysusr_name    = $rowUser['sysusr_name'];
$sysusr_email   = $rowUser['sysusr_email'];
$stkname        = $rowUser['stkname'];
$prov_name      = $rowUser['prov_name'];
$wh_name        = $rowUser['wh_name'];
$dist_name      = $rowUser['dist_name'];
$cell_no        = $sysusr_cell = $rowUser['sysusr_ph'];


if(!empty($_REQUEST['for_warehouse']) && $_REQUEST['for_warehouse'] != 'all') {
    $wh_id = $_REQUEST['for_warehouse'];
    $log_s  = "SELECT tbl_warehouse.wh_id,tbl_warehouse.wh_name FROM tbl_warehouse   WHERE wh_id = '".$wh_id."' ";
    $rs     = mysql_query($log_s);
    $wh_info    = mysql_fetch_assoc($rs);
    $wh_name = $wh_info['wh_name'];
}
elseif(empty($_REQUEST['for_warehouse'])){
    
    $wh_id = $rowUser['wh_id'];
    $wh_name=$rowUser['wh_name'];
}
else{
    $wh_id = '';
    $wh_name = 'SDPs of District '.$dist_name;
    $district_id = $rowUser['dist_id'];
}


$cc = '923331519963';
$to_list_sms[] = $cell_no;

//echo '<pre>';print_r($to_list_sms);exit;

$created_by = $modified_by = $_SESSION['user_id'];

include(PUBLIC_PATH . "/html/header.php");

if (isset($_REQUEST['submit']) && !empty($_REQUEST['submit'])) {
        // '<pre>';print_r($_REQUEST);exit;
        $message = $_REQUEST['message'];
        $message .= '\r\n('.$wh_name.')';
        //$message .= '\r\n[system generated SMS]';
        //echo $message;exit;
        $url = "http://cbs.zong.com.pk/reachcwsv2/corporatesms.svc?wsdl";
        $client = new SoapClient($url, array("trace" => 1, "exception" => 0));
        
        $username = '923125154792';
        $password = '38917466';

        if ( count($to_list_sms) > 0) {
            foreach ($to_list_sms as $to) {
                //$to      = '923331519963';

                $subject = $_REQUEST['subject'];
                $msg     = $_REQUEST['message'];
                $page_name = "fp2020DashboardcLMIS";
                $sent_by_user = $created_by;
                $sent_to_user = $user_id;

                include_once "include_functions_sms.php";
                func_send_sms_for_moderation($to,$subject,$msg,$cc,$page_name,$wh_id,$sent_by_user,$sent_to_user,$district_id);
                //exit; 
            }
        }
    ?>
    <script>
        jQuery(document).ready(function($) {
                $('#msg_row').html('<i class="fa fa-check" style="background-color:green"></i> SMS has been sent to the moderator. It will be delivered to <b><?=$sysusr_name?></b> shortly after moderation.');
                $('#msg_row').addClass('note note-success');
                $('#last_row').html('<a id="close_btn" name="close_btn" value=""  class="btn btn-default red">Close this window</a>');
                
                $('#message').attr("readonly","readonly");
                $('#r2').hide();
                
                 $('#close_btn').click(function(){
                    window.close();
                });
        })
    
    </script>
    <?php
}
?>    

</head>
<!-- END HEAD -->

<!-- BEGIN body -->
<body class="page-header-fixed page-quick-sidebar-over-content1" >
    <!-- BEGIN HEADER -->
    <form action="" method="post" id="survey" name="survey">
    <div class="portlet box purple-plum">
        <div class="portlet-title">
                <div class="caption">
                        <i class="fa fa-envelope"></i> Send SMS
                </div>
        </div>
        <div class="portlet-body">
            <div class="margin-top-10 margin-bottom-10 clearfix">
                <div class="hide">to_list<?php print_r($to_list_sms); ?></div>
                <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <td rowspan="2" width="30%">
                                 <h4 id="heading_title" class="heading">Send SMS to : </h4>
                        </td>
                        <td>
                               <?=$sysusr_name?>
                        </td>
                    </tr>
                    <tr> 
                        <td>
                                <?=$stkname.' - '.$dist_name.' - '.$prov_name?>
                        </td>
                    </tr>
                    <?php
                    if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == 'beta.lmis.gov.pk')
                    {
                    
                    ?>
                    <tr> 
                        <td>
                                <label class="control-label" for="comment">
                                    Contact Info 
                                </label>
                        </td>
                        <td>
                               <?=$sysusr_cell.' , '.$sysusr_email?>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr> 
                        <td>
                                <label class="control-label" for="comment">
                                    Regarding 
                                </label>
                        </td>
                        <td>
                               <b><?=$wh_name?></b>
                        </td>
                    </tr>
                    <tr id="">
                        <td>
                               <label class="control-label" for="comment">
                                    Message
                                </label>
                                <label class="control-label" for="comment" style="font-size:10px;">
                                    (<span id="max_msg">150</span> chars left)
                                </label>
                        </td>
                        <td>
                             
                             <textarea  maxlength="150" required="" class="form-control" id="message" name="message" cols="80" rows="8"><?=(isset($_REQUEST['submit'])?$_REQUEST['message']:'')?></textarea>
                              
                        </td>
                    </tr>
                
                </tbody>
                </table>
            </div>
        </div>
					</div>
        
        <div id="msg_row" class="">
        </div>
        <div id="last_row">
             <?php
             if(isset($_REQUEST['submit']))
             {
                 echo '';
             }
             else
             {
                echo '<div id="s_btn"><button type ="submit" id="submit" name="submit" value="submit"  class=" btn btn-primary ">Send</button></div>';
  
             }
             ?>
        </div>
        </form>
    <div class="page-container1">

        <?php
        $user_d_id = $_SESSION['user_id'];
        $current_date = date("Y-m-d");

        ?>
       
           
<script src="../../public/assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        
        
            
        
        $( "#subject" ).on('input', function() {
            var a = $(this).val().length;
            var b = 150-a;
            $('#max_sub').html(b);
        });
        $( "#message" ).on('input', function() {
            var a = $(this).val().length;
            var b = 150-a;
            $('#max_msg').html(b);
        });
    });
</script>
            </body>
            <!-- END BODY -->
            </html>
