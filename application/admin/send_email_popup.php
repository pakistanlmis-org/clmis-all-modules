<?php
/**
 * changePassUser
 * @package default
 *
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 *
 * @version    2.2
 *
 */
//Including files
include("../includes/classes/AllClasses.php");
require("../includes/classes/clsLogin.php");

$created_by = $modified_by = $_SESSION['user_id'];
$user_id = $_REQUEST['user_id'];

$userSql = "select * from sysuser_tab where UserID = $user_id";
$userResult = mysql_query($userSql) or die("Error " . $userSql);
$rowUser = mysql_fetch_assoc($userResult);
$sysusr_name = $rowUser['sysusr_name'];
$sysusr_email = $rowUser['sysusr_email'];
$sysusr_deg = $rowUser['sysusr_deg'];
$sysusr_dept = $rowUser['sysusr_dept'];
$sysusr_cell = $rowUser['sysusr_ph'];

include(PUBLIC_PATH . "/html/header.php");

if (isset($_REQUEST['submit']) && !empty($_REQUEST['submit'])) {
//sending email
   //echo '<pre>';print_r($_REQUEST);exit;
   $to      = $sysusr_email;
   $subject = addslashes($_REQUEST['subject']);
   $m1 = addslashes($_REQUEST['message']);
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
    $headers .= "From: feedback@lmis.gov.pk" . "\r\n" .
    "Reply-To: feedback@lmis.gov.pk" . "\r\n" .
    "X-Mailer: PHP/" . phpversion();
    mail($to, $subject, $message,  $headers);
    
    
    
    ?>
    <script>
        jQuery(document).ready(function($) {
                $('#heading_title').html('Email Sent');
                $('#heading_title').addClass('note note-success');
                $('#last_row').html('<a id="close_btn" name="close_btn" value="submit"  class="btn btn-default red">Close</a>');
                
                $('#r1').hide();
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
    <div class="page-container1">

        <?php
//Including files
        //include $_SESSION['menu'];
        //include PUBLIC_PATH . "html/top_im.php";
        $user_d_id = $_SESSION['user_id'];
        $current_date = date("Y-m-d");

        $getLocsSql = "SELECT
                survey.pk_id,
                survey.`name`,
                survey.email,
                survey.department,
                survey.office,
                survey.cell_number,
                survey.q1_data_difficulty,
                survey.q2_report,
                survey.`comment`,
                survey.q1_y_n,
                survey.q2_y_n,
                survey.user_id,
                survey.created_by,
                survey.created_date,
                survey.modified_by,
                survey.modified_date
                FROM
                survey
                where survey.user_id = '$user_d_id'
                and DATE_FORMAT(survey.created_date,'%Y-%m-%d') = '$current_date'";
//Query result
        $res = mysql_query($getLocsSql) or die(mysql_error());
        $row = mysql_fetch_assoc($res);
        ?>
        <div class="page-content-wrapper1 " style="padding:20px">
            <div class="page-content1 center">
                <div class="row center">
                    <div class="col-md-12 col-sm-12  col-xs-12 ">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 id="heading_title" class="heading">Write e-mail to : <?=$sysusr_name?></h3>
                            </div>
                            <div class="widget-body">
                                <form action="" method="post" id="survey" name="survey">
                                    <div class="row">
                                        <div class="col-md-3  col-sm-3  col-xs-3 ">
                                            <div class="form-group">
                                                <label class="control-label" for="comment">
                                                    To
                                                </label>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-6  col-sm-6  col-xs-6 ">
                                            <div class="form-group">
                                                 <label class="control-label" for="comment"><?=$sysusr_email?></label> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="r1">
                                        <div class="col-md-3 col-sm-3  col-xs-3">
                                            <div class="form-group">
                                                <label class="control-label" for="comment">
                                                    Subject
                                                    <span class="control-label" for="comment" style="font-size:10px;">
                                                        (<span id="max_sub">150</span> chars left)
                                                </span>
                                                </label>
                                                
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6  col-xs-6">
                                            <div class="form-group">
                                                <input required="" class="form-control" id="subject" name="subject"  maxlength="150" /> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row"  id="r2">
                                        <div class="col-md-3 col-sm-3  col-xs-3">
                                            <div class="form-group">
                                                <label class="control-label" for="comment">
                                                    Message
                                                </label>
                                                <label class="control-label" for="comment" style="font-size:10px;">
                                                    ((<span id="max_msg">1000</span> chars left)
                                                </label>
                                                
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6  col-xs-6">
                                            <div class="form-group">
                                               
                                                <textarea  maxlength="1000" required="" class="form-control" id="message" name="message" cols="80" rows="10    "></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="last_row">
                                        <div class="col-md-6 col-sm-6  col-xs-6">
                                            <div class="form-group">
                                                <button type ="submit" id="submit" name="submit" value="submit"  class="btn btn-primary">Send</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            var b = 1000-a;
            $('#max_msg').html(b);
        });
    });
</script>
            </body>
            <!-- END BODY -->
            </html>
