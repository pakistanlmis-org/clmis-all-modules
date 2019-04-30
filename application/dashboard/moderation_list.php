<?php
include("../includes/classes/Configuration.inc.php");
//Login
Login();
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");



$userSql = "
            SELECT
                    moderation.pk_id,
                    moderation.`to`,
                    moderation.cc,
                    moderation.`subject`,
                    moderation.body,
                    moderation.created_date,
                    moderation.type,
                    moderation.interface,
                    moderation.warehouse_id,
                    moderation.sent_by,
                    moderation.`status`,
                    moderation.sent_to,
                    moderation.remarks,
                    sent_by_user.usrlogin_id AS sent_by_user,
                    sent_to_user.usrlogin_id AS sent_to_user,
                    sent_by_user.sysusr_name,
                    sent_to_user.sysusr_name,
                    tbl_warehouse.wh_name,
                    sent_to_user.sysusr_email,
                    sent_to_user.sysusr_cell,
                    sent_to_user.sysusr_ph
                FROM
                moderation
                INNER JOIN sysuser_tab AS sent_by_user ON moderation.sent_by = sent_by_user.UserID
                INNER JOIN sysuser_tab AS sent_to_user ON moderation.sent_to = sent_to_user.UserID
                LEFT JOIN tbl_warehouse ON moderation.warehouse_id = tbl_warehouse.wh_id
            Order by 
            moderation.pk_id DESC
            ";
//echo $userSql;exit;
$userResult = mysql_query($userSql) or die("Error " . $userSql);

?>
    <style>
        .my_dash_cols{
            padding-left: 1px;
            padding-right: 0px;
            padding-top: 1px;
            padding-bottom: 0px;
        }
        .my_dashlets{
            /*padding-left: 1px;
            padding-right: 0px;
            padding-top: 1px;
            padding-bottom: 0px;*/
        }
        
    </style>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                
                <div class="container-fluid">
                    
                        <div class="row">
                            <div class="widget" data-toggle="">
                                <div class="widget-head">
                                    <h3 class="heading">Moderations List</h3>
                                </div>
                                <div class="widget-body collapse in">
                                    <table class="table table-bordered table-striped">
                                        <tbody>
                                            <tr>
                                                <td width="2%">#</td> 
                                                <td width="6%">Type</td> 
                                                <td width="12%">Sent By</td> 
                                                <td width="12%">Sent To</td> 
                                                <td width="8%">Sent On</td> 
                                                <td width="40%" colspan="2">Message</td> 
                                                <td>Status / Actions</td> 
                                            </tr>


                                                <?php
                                                
        $server = $_SERVER['SERVER_NAME'];
        if($_SERVER['SERVER_NAME'] =='beta.lmis.gov.pk') $server = $_SERVER['SERVER_NAME'].'/clmisapp';
        
                                                $c=1;
                                                while($row = mysql_fetch_assoc($userResult)){
                                                    $moderation_id = $row['pk_id'];
                                                     echo '<tr>';
                                                     echo '<td>'.$c++.'</td>';
                                                     if($row['type'] == 'SMS')
                                                        echo '<td><i class="fa fa-comment" style="color:green !important;"></i> '.$row['type'].'</td>';
                                                     else
                                                         echo '<td><i class="fa fa-envelope" style="color:purple !important;"></i> '.$row['type'].'</td>';

                                                     echo '<td>'.$row['sent_by_user'].'</td>';
                                                     //echo '<td>'.$row['sent_to_user'].'<br/><span style=" color:#999999;font-size:13px;">'.$row['sysusr_ph'].'  '.$row['sysusr_email'].'</span></td>';
                                                     echo '<td>'.$row['sent_to_user'].'<br/><span style=" color:#999999;font-size:13px;">'.$row['to'].'</span></td>';
                                                     echo '<td>'.date('d-M-Y h:m A',strtotime($row['created_date'])).'</td>';
                                                     
                                                     if(!empty($row['remarks'])){
                                                        echo '<td  colspan="">';
                                                        if(!empty($row['subject']))
                                                            echo '<b>Subject : '.$row['subject'].'</b><br/>';
                                                        echo ''.nl2br($row['body']).'</td>';
                                                        echo '<td  colspan="">'.nl2br($row['remarks']).'</td>';
                                                     }
                                                     else{
                                                        echo '<td  colspan="2">';
                                                        
                                                        if(!empty($row['subject']))
                                                            echo '<b>Subject : '.$row['subject'].'</b><br/>';
                                                        echo ''.nl2br($row['body']).'</td>';
                                                     }
                                                     
                                                     if($row['status'] == 'pending'){
                                                        //echo '<td style="color:orange">'.strtoupper($row['status']).'</td>';
                                                        echo '<td>';
                                                        echo'   <a class="btn btn-sm  btn-circle green-jungle" target=""  href="moderation_proceed.php?moderation_id='.$moderation_id.'&redirect=moderation_list" >Proceed</a> 
                                                                <a class="btn btn-sm  btn-circle yellow" target=""        href="moderation_junk.php?moderation_id='.$moderation_id.'&redirect=moderation_list" >Archive</a>
                                                                <a class="btn btn-sm  btn-circle red-sunglo" target=""    href="moderation_reply.php?moderation_id='.$moderation_id.'&redirect=moderation_list" >Reply Back</a>';
                                                        echo '</td>';
                                                     }
                                                     elseif($row['status'] == 'sent'){
                                                        echo '<td style="color:green"><i class="fa fa-check-square-o font-green"></i> '.strtoupper($row['status']).'</td>';
                                                        //echo '<td> </td>';
                                                     }
                                                     elseif($row['status'] == 'replied_back'){
                                                        echo '<td style="color:green"><i class="fa fa-reply font-green"></i> '.strtoupper('Replied Back').'</td>';
                                                        //echo '<td> </td>';
                                                     }
                                                     else{
                                                        echo '<td style="color:red"><i class="fa fa-times font-red"></i> '.strtoupper($row['status']).'</td>';
                                                        //echo '<td> </td>';
                                                     }
                                                     
                                                     echo '</tr>';
                                                }
                                                ?>


                                        </tbody>
                                        </table>
                                </div>
                            </div>
                        </div>
                    
                    
                </div>
                
                
               
            </div>
        </div>
    </div>

    <?php 
    //Including footer file
    include PUBLIC_PATH . "/html/footer.php"; ?>
    
</body>
<!-- END BODY -->
</html>