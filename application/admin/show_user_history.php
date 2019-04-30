<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$caption = "User Status History";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
$chart_id = 'incoming_pipeline';

$id=$_REQUEST['id'];
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
                            <th>Status</th>
                            <th>Comments</th>
                            <th>Updated at</th>
                            <th>Updated by</th>
                        </tr>
                </thead>
                <tbody  style="font-size: 10px">
                    <?php
                    
                    $qry = " SELECT
                                    user_status_history.pk_id,
                                    user_status_history.user_id,
                                    user_status_history.`status`,
                                    user_status_history.comments,
                                    user_status_history.created_by,
                                    user_status_history.created_at,
                                    sysuser_tab.sysusr_name
                                FROM
                                    user_status_history
                                INNER JOIN sysuser_tab ON user_status_history.created_by = sysuser_tab.UserID
                                WHERE
                                    user_status_history.user_id = $id
                                ORDER BY
                                    user_status_history.pk_id DESC
                                 ";
                $qryRes = mysql_query($qry);
    
                    $c=1;
                    while($row = mysql_fetch_assoc($qryRes))
                    {
                           
                        echo '<tr>
                                <td>'.$c++.'</td>
                                <td>'.$row['status'].'</td>
                                <td>'.$row['comments'].'</td>
                                <td>'.$row['created_at'].'</td>
                                <td>'.$row['sysusr_name'].'</td>
                                
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