<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$caption = "User Login History";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
$chart_id = 'incoming_pipeline';

$id=$_REQUEST['user_id'];
?>

<div class="page-content" style="">
<div class="container" >
<div class="widget widget-tabs" style="">    
    <div class="widget-body" >
        
        <div class="text-center"><?=$caption?></div>
        <div  style="">
            <table class="table table-striped table-hover table-condensed">
                    <thead style="font-size: 10px">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Login Time</th>
                            <th>IP Address</th>
                        </tr>
                </thead>
                <tbody  style="font-size: 10px">
                    <?php
                    
                    $qry = " SELECT
                                tbl_user_login_log.pk_id,
                                tbl_user_login_log.user_id,
                                tbl_user_login_log.ip_address,
                                tbl_user_login_log.login_time,
                                sysuser_tab.usrlogin_id
                                FROM
                                tbl_user_login_log
                                INNER JOIN sysuser_tab ON sysuser_tab.UserID = tbl_user_login_log.user_id
                                WHERE
                                tbl_user_login_log.user_id = $id
                                ORDER BY pk_id DESC

                                 ";
                $qryRes = mysql_query($qry);
    
                    $c=1;
                    while($row = mysql_fetch_assoc($qryRes))
                    {
                           
                        echo '<tr>
                                <td>'.$c++.'</td>
                                <td>'.$row['usrlogin_id'].'</td>
                                <td>'.date('Y-M-d (D) h:i:s A', strtotime($row['login_time'])).'</td>
                                
                                <td>'.$row['ip_address'].'</td>
                                
                            </tr>';

                    }
                    ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>