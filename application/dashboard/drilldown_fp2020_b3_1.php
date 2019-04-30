<?php
//echo '<pre>';print_r($_REQUEST);exit;
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';
include PUBLIC_PATH . "html/top_im.php";
?>
<link href="<?php echo PUBLIC_URL;?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
<?php
//System settings 

$userSql = "SELECT
            system_settings.pk_id,
            system_settings.setting_name,
            system_settings.setting_value
            FROM
            system_settings
";
//echo $userSql;exit;
$userResult = mysql_query($userSql) or die("Error " . $userSql);
$system_settings = array();
while($row = mysql_fetch_assoc($userResult)){
    $system_settings[$row['setting_name']] = $row['setting_value'];
}
//echo '<pre>';print_r($system_settings);exit;
//End systems settings 


$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$stakeholder = $_REQUEST['stk_id'];
//$stakeholder=1;
$stk_name = $_REQUEST['stk_name'];
$prod_id = $_REQUEST['prod_id'];
@$prod_name = $_REQUEST['prod_name'];

$dist_id = $_REQUEST['dist_id'];
$dist_name = $_REQUEST['dist_name'];

$email = $cell = '';

//
//    $qry_contact = "SELECT
//                        (sysuser_tab.sysusr_ph) as cell,
//                        (sysuser_tab.sysusr_email) as email,
//                        sysuser_tab.sysusr_name,
//                        sysuser_tab.usrlogin_id,
//                        sysuser_tab.UserID
//                    FROM
//                    sysuser_tab
//                    INNER JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
//                    INNER JOIN tbl_warehouse tw ON wh_user.wh_id = tw.wh_id
//                    INNER JOIN stakeholder ON tw.stkofficeid = stakeholder.stkid
//                    WHERE
//                        tw.dist_id = $dist_id AND
//                        stakeholder.lvl = 3 AND
//                        tw.stkid = $stakeholder
//                        AND sysuser_tab.user_level = 3
//                        
//                        AND sysuser_tab.sysusr_type <> 23
//                    limit 1
//                    ";
//    //echo $qry_contact;exit;
//    $qryRes_contact = mysql_query($qry_contact);
//    $row_contact = mysql_fetch_assoc($qryRes_contact);
//    $email = $row_contact['email'];
//    $cell = $row_contact['cell'];
//    $user_id = $row_contact['UserID'];
//    //echo '<pre>';print_r($_REQUEST);print_r($row_contact);exit;


   $qry = "
            SELECT
                	tbl_warehouse.wh_id,
                        tbl_warehouse.wh_name,
                        tbl_locations.LocName as dist_name,
                        itminfo_tab.itmrec_id,
                        tbl_hf_data.closing_balance,
                        ROUND( (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 2 ) as mos,
                        stakeholder.stkname,
                        stakeholder.stkid
                    FROM
                            tbl_warehouse
                    INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkid
                    INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                    WHERE
                         
                     tbl_warehouse.prov_id = ".$province." ";
            if(!empty($stakeholder)){
                 $qry .= " AND tbl_warehouse.stkid in ($stakeholder) ";
            }
            $qry .= " AND tbl_hf_type_rank.province_id = ".$province."

                    AND tbl_warehouse.wh_id NOT IN (
                            SELECT
                                    warehouse_status_history.warehouse_id
                            FROM
                                    warehouse_status_history
                            INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                            WHERE
                                    warehouse_status_history.reporting_month = '".$from_date."'
                            AND warehouse_status_history.`status` = 0

                    )
                    AND tbl_hf_data.reporting_date = '".$from_date."'

                    AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                    
                    AND itminfo_tab.itm_category = 1
                    AND tbl_hf_data.item_id = '".$prod_id."'
                    AND tbl_warehouse.dist_id = $dist_id
                ORDER BY 
                    stkid,
                    tbl_warehouse.wh_name 
            
 ";
//    echo $qry;exit;
    $qryRes = mysql_query($qry);
    
    $legend='';
//    $qry2 = "SELECT
//                mosscale_tab.shortterm,
//                mosscale_tab.longterm,
//                mosscale_tab.sclstart,
//                mosscale_tab.sclsend,
//                mosscale_tab.colorcode,
//                mosscale_tab.stkid,
//                mosscale_tab.lvl_id,
//                itminfo_tab.itm_id
//            FROM
//                mosscale_tab
//            INNER JOIN itminfo_tab ON mosscale_tab.itmrec_id = itminfo_tab.itmrec_id
//            WHERE
//                mosscale_tab.lvl_id = 4 AND
//                mosscale_tab.stkid = $stakeholder AND
//                itminfo_tab.itm_id = '".$prod_id."'
//            ";
//    $qryRes2 = mysql_query($qry2);
//    
//    $legend = '<table class="table table-condensed table-bordered">';
//    $legend .= '<tr>';
//    $legend .=' <td id="show_all" style="display:none;" ><div class="btn btn-sm blue-hoki">Show All</div></td>';
//    $legend .= '<td>';
//    $legend .= '<span> MOS Legend :</span>';
//    $legend .= '</td>';
//    while($row2 = mysql_fetch_assoc($qryRes2))
//    {
//        $legend .= '<td><div    class="legend_block" data-clr="' . $row2['colorcode'] . '" style="display:inline-block;width:15px; height:15px; vertical-align: bottom; background-color:'.$row2['colorcode'].';margin-left:20px;"></div>';
//        $legend .= '<div        class="legend_block" data-clr="' . $row2['colorcode'] . '"  style="display:inline-block;margin-left:5px;">'.$row2['longterm'].'</div></td>';
//    }
//    $legend .= '</tr>';
//    $legend .='</table>';
    
    $c=1;
     $itm_arr   = $so_arr = array();
    
     echo '<div class="row">';
        echo '<div class="well well-dark ">';
     echo '<h3 align="center">MOS at SDP\'s of District :';
   
     echo '<b> '.$dist_name.' '.(!empty($stk_name)?' - '.$stk_name:'').'  - '.$prod_name.' </b>';
     echo 'for '.date('M-Y',strtotime($from_date)).'</h3>';
     echo '</div>'; 
     echo '</div>';
     
     echo ' <div class="row">';
     echo '     <div class="col-md-12 center">';
     echo $legend;
     echo '     </div>';
     echo ' </div>';
     
     echo '<div class="row">';
    echo '<div class="col-md-12"  style=" height: auto; overflow-y: scroll;">';
     echo '<table width="90%" class="table table-condensed table-striped custom_sortable ">';
     
     echo '<thead>';
     echo '<tr>';
     echo '<th>Sr no <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>District <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Stakeholder <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Warehouse <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th width="15%">Stock on hand - (SOH) <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th width="15%">Months of stock - (MOS) <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th></th>';
     echo '<th> </th>';
     //echo '<th> Contact</th>';
     echo '</tr>';
     echo '</thead>';
     echo '<tbody class="custom_sortable_body">';
     $c=1;
    while($row = mysql_fetch_assoc($qryRes))
    {
       $mos_1 = ((isset($row['mos']) && $row['mos']>0)?number_format($row['mos'],2):'0');
        
       $bgcolor='';
//        $q_mos = "SELECT getMosColor('".$mos_1."', '".$row['itmrec_id']."','".$stakeholder."',4)";
//        //echo $q_mos;exit;
//        $rs_mos = mysql_query($q_mos);
//        $bgcolor = mysql_result($rs_mos, 0, 0);
        
        echo '<tr class="t_rows" data-clr="'.$bgcolor.'">';
        echo '<td>'.$c++.'</td>';
        echo '<td>'.$row['dist_name'].'</td>';
        echo '<td>'.$row['stkname'].'</td>';
        echo '<td>'.$row['wh_name'].'</td>';
        //echo '<td>'.$row['stkname'].'</td>';
        echo '<td align="right">'.number_format($row['closing_balance']).'</td>';
        echo '<td align="right">'.$mos_1.'</td>';
        echo '<td><div style="width:10px; height:12px; background-color:'.$bgcolor.';" title=""></div></td>';
//        echo '<td>';
//        echo ' <a  class="pull-left " onclick="window.open(\'contact_history.php?wh_id='.$row['wh_id'].'\', \'_blank\', \'scrollbars=1,width=800,height=500\');"><i class="fa fa-history" style="color:black !important;padding-top:5px;font-size:25px;"></i> </a>';
//            
//        if(!empty($row['history_count']) && $row['history_count']>0)
//        {
//            echo ' <span class="badge badge-warning" style="position:relative;left: -5px;">'.$row['history_count'].'</span>';
//        }
//        echo '</td>';
        
//        echo '<td>';
//        
//        $deny_access =false;
//        if( $_SESSION['user_level'] > 2 )
//        {
//            $deny_access =true;
//        }
//        
        //$cell=$email='';
//        echo '<div class="btn-group btn-group-circle btn-group-sm btn-group-solid">';
//        
//        if(empty($email) || $system_settings['dashboard_email'] !='active' || $deny_access)
//            echo ' <button type="button" class="btn  grey-gallery "   ><i class="fa fa-envelope"></i> Email</button>';
//        else
//            echo ' <button type="button" class="btn green-jungle "  onclick="window.open(\'send_email.php?user_id='.$user_id.'&for_warehouse='.$row['wh_id'].'\', \'_blank\', \'scrollbars=1,width=600,height=500\');"><i class="fa fa-envelope"></i> Email</button>';
//        
//        if(empty($cell)|| $system_settings['dashboard_sms'] !='active' || $deny_access)
//            echo ' <button type="button" class="btn grey-gallery"   ><i class="fa fa-comment"></i> Message</button>';
//        else
//            echo ' <button type="button" class="btn green"  onclick="window.open(\'send_sms.php?user_id='.$user_id.'&for_warehouse='.$row['wh_id'].'\', \'_blank\', \'scrollbars=1,width=600,height=500\');"><i class="fa fa-comment"></i> Message</button>';
//        
//        
//        echo '</div>';
//        echo '</td>';
        echo '</tr>';
    }  
    echo '</tbody>';
    echo '</table>';
    echo ' </div>';
     echo '</div>';
    ?>

  
    <script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    <script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>