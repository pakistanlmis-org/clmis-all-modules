<?php
include("../includes/classes/AllClasses.php");
require("../includes/classes/clsLogin.php");

$wh_id = $_REQUEST['wh_id'];


$userSql = "SELECT
            tbl_warehouse.wh_id,
            tbl_warehouse.wh_name
            FROM
            tbl_warehouse
            WHERE
            tbl_warehouse.wh_id = $wh_id";
$userResult = mysql_query($userSql) or die("Error " . $userSql);
$row_warehouse = mysql_fetch_assoc($userResult);


$userSql = "select * from 
            (
                (
                    SELECT
                            
                            alerts_log.warehouse_id,
                            alerts_log.`to`,
                            alerts_log.`subject`,
                            alerts_log.body,
                            alerts_log.created_date,
                            alerts_log.response,
                            alerts_log.type,
                            alerts_log.sent_by,
                            sysuser_tab.sysusr_name,
                            'sent' as status
                    FROM
                            alerts_log
                        LEFT JOIN sysuser_tab ON alerts_log.sent_by = sysuser_tab.UserID
                    WHERE
                        alerts_log.interface = 'fp2020DashboardcLMIS'
                        AND  alerts_log.warehouse_id = $wh_id
                    ORDER BY
                        alerts_log.created_date DESC
                    )
                UNION
                (
                    SELECT
                    moderation.warehouse_id,
                    moderation.`to`,
                    moderation.`subject`,
                    moderation.body,
                    moderation.created_date,
                    '' AS response,
                    moderation.type,
                    moderation.sent_by,
                    sysuser_tab.sysusr_name,
                    moderation.`status`
                    FROM
                    moderation
                    
                    INNER JOIN sysuser_tab ON moderation.sent_by = sysuser_tab.UserID
                    where
                        moderation.`status` <> 'sent'
                        AND  moderation.warehouse_id = $wh_id
                )
            )
            as A
            ORDER BY
            A.created_date DESC
            ";
$userResult = mysql_query($userSql) or die("Error " . $userSql);


include(PUBLIC_PATH . "/html/header.php");

?>    

</head>
<!-- END HEAD -->

<!-- BEGIN body -->
<body class="page-header-fixed page-quick-sidebar-over-content1" >
    <!-- BEGIN HEADER -->
    <form action="" method="post" id="survey" name="survey">
        <div id="msg_row" class="">
        </div>
        <div id="btn_div" class="">
        </div>
    <div class="portlet box yellow-gold">
        <div class="portlet-title">
                <div class="caption">
                        <i class="fa fa-history"> Message / Email History - <?=isset($row_warehouse['wh_name'])?$row_warehouse['wh_name']:''?></i> 
                </div>
        </div>
        <div class="portlet-body">
            <div class="margin-top-10 margin-bottom-10 clearfix">
                <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <td>#</td> 
                        <td>Type</td> 
                        <td>Sent By</td> 
                        <td>Sent On</td> 
                        <td>Message</td> 
                        <td>Status</td> 
                    </tr>
                    
                   
                        <?php
                        $c=1;
                        while($row = mysql_fetch_assoc($userResult)){
                             echo '<tr>';
                             echo '<td>'.$c++.'</td>';
                             if($row['type'] == 'SMS')
                                echo '<td><i class="fa fa-comment" style="color:green !important;"></i> '.$row['type'].'</td>';
                             else
                                 echo '<td><i class="fa fa-envelope" style="color:purple !important;"></i> '.$row['type'].'</td>';
                             
                             echo '<td>'.$row['sysusr_name'].'</td>';
                             echo '<td>'.date('d-M-Y h:m A',strtotime($row['created_date'])).'</td>';
                             echo '<td>'.nl2br($row['body']).'</td>';
                             if($row['status'] == 'pending'){
                                echo '<td style="color:red">'.$row['status'].'</td>';
                             }
                             elseif($row['status'] == 'sent'){
                                echo '<td style="color:green">'.$row['status'].'</td>';
                             }
                             else{
                                echo '<td style="color:red">'.$row['status'].'</td>';
                             }
                             echo '</tr>';
                        }
                        ?>
                    
                
                </tbody>
                </table>
            </div>
        </div>
					</div>
        
        <div id="last_row">
        <button type ="submit" id="submit" name="submit" value="submit"  class=" btn btn-primary <?=((empty($sysusr_email) || isset($_REQUEST['submit']))?' hide ':'')?>">Send</button>
        </div>
    </form>
   

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
