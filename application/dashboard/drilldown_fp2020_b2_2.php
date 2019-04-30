<?php
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));

$province_param = 'all';
$province_param = (($province!='all')?$province:'\'all\'');
$province_name_param = 'All';
$province_name_param = (($province!='all')?$prov_name:'All');
    
$stakeholder = $_REQUEST['stakeholder'];
$stk_name = $_REQUEST['stk_name'];


$prod_id = $_REQUEST['prod_id'];
$prod_name = $_REQUEST['prod_name'];

//Chart caption
$caption = 'Stock Availability Rate at SDPs - '.$prov_name.' - '.$stk_name.' - '.$prod_name.' for '.date('M-Y',strtotime($from_date));
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'drill2';

$qry_1 = "  SELECT
                tbl_locations.PkLocID,
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.LocLvl = 3
            
        ";
    if($province!='all') $qry_1 .= " AND tbl_locations.ParentID = $province";
    $qry_1 .= " ORDER BY tbl_locations.LocName ASC";
        $res_1 = mysql_query($qry_1);
        $dist_arr = array();
        
        while($row_1 = mysql_fetch_array($res_1))
        {
            $dist_arr[$row_1['PkLocID']]=$row_1['LocName'];
            $temp_dist = $row_1['PkLocID'];
        }
        
        
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
?>

<div class=" ">    
    <a href="../reports/stock_optimization.php?stakeholder=<?=$stakeholder?>&prov_sel=<?=$province?>&dist_id=<?=$temp_dist?>&product=<?=$prod_id?>&submit=Submit&date=<?=$from_date?>" target="_blank" class="btn btn-xs green right pull-right">Analysis for Stock Optimization</a>
    <div class=" ">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;display:none;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        
        
    $qry_contact = "SELECT tw.dist_id,
                        (sysuser_tab.sysusr_ph) as cell,
                        (sysuser_tab.sysusr_email) as email,
                        sysuser_tab.sysusr_name,
                        sysuser_tab.usrlogin_id,
                        sysuser_tab.UserID
                    FROM
                    sysuser_tab
                    INNER JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
                    INNER JOIN tbl_warehouse tw ON wh_user.wh_id = tw.wh_id
                    INNER JOIN stakeholder ON tw.stkofficeid = stakeholder.stkid
                    WHERE
                         
                        stakeholder.lvl = 3 AND
                        tw.stkid = $stakeholder
                        AND sysuser_tab.user_level = 3
                        AND sysuser_tab.sysusr_type <> 23 
                    ";
        if($province!='all') $qry_contact .= " AND tw.prov_id = $province ";
            //echo $qry_contact;exit;
            $qryRes_contact = mysql_query($qry_contact);
            $district_contacts = array();
            while($row=mysql_fetch_assoc($qryRes_contact))
                {
                    $district_contacts[$row['dist_id']]=$row;
                }
        //echo '<pre>';print_r($district_contacts);exit;
        //query for getting reported facilities
        $q_reporting  = "SELECT
                                   tbl_warehouse.dist_id,
                                    COUNT(
                                            DISTINCT tbl_warehouse.wh_id
                                    ) AS total
                            FROM
                                    tbl_warehouse
                            INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                            WHERE
                                    tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
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
                            
                            AND  tbl_warehouse.stkid = $stakeholder
                            ";
         if($province!='all') $q_reporting .= " AND tbl_warehouse.prov_id = ".$province." ";
         $q_reporting .= " GROUP BY  tbl_warehouse.dist_id ";
        //echo $q_reporting;
        $res_reporting = mysql_query($q_reporting);
        $tot_sdp = array();
        while($row=mysql_fetch_assoc($res_reporting))
        {
            $tot_sdp[$row['dist_id']]=$row['total'];
        }
        
        $q_reporting  = "SELECT
                                   tbl_warehouse.dist_id,
                                    COUNT(
                                            DISTINCT tbl_warehouse.wh_id
                                    ) AS reportedWH
                            FROM
                                    tbl_warehouse
                            INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                            WHERE
                                    tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
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
                            
                            AND  tbl_warehouse.stkid = $stakeholder
                            AND tbl_hf_data.reporting_date = '".$from_date."'

                            ";
         if($province!='all') 
             $q_reporting .= " AND tbl_warehouse.prov_id = ".$province." ";
         $q_reporting .= " GROUP BY tbl_warehouse.dist_id ";
        //echo $q_reporting;
        $res_reporting = mysql_query($q_reporting);
        $reporting_wh_arr  =  array();
        $total_reporting_wh = 0;
        while($row=mysql_fetch_assoc($res_reporting))
        {
            $reporting_wh_arr[$row['dist_id']]=$row['reportedWH'];
            $total_reporting_wh +=$row['reportedWH'];
        }
        
        
        
        //Query for shipment main dashboard
        $qry = "SELECT
                tbl_warehouse.dist_id,
                tbl_locations.LocName,
                tbl_hf_data.item_id ,
                itminfo_tab.itm_name,
                count(tbl_hf_data.pk_id) as nmbr,
tbl_locations.ParentID as prov_id
                    FROM
                            tbl_warehouse
                    INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkid
                    INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                    WHERE
                         tbl_warehouse.stkid = $stakeholder
                    

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
                    AND ROUND( (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 2 ) > 0
                    AND itminfo_tab.itm_category = 1
                    AND tbl_hf_data.item_id = '".$prod_id."'
                
        ";
    if($province!='all') 
        $qry .= " AND tbl_warehouse.prov_id = ".$province." AND tbl_hf_type_rank.province_id = ".$province." ";
    $qry .= " GROUP BY
                tbl_warehouse.dist_id,
                tbl_hf_data.item_id 
                ORDER BY 
                tbl_warehouse.dist_id,
                tbl_hf_data.item_id ";
//    echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c=1;
     $so_arr = $parents_arr = array();
   
    while($row = mysql_fetch_assoc($qryRes))
    {
        $dist_arr[$row['dist_id']]      = $row['LocName'];
        $parents_arr[$row['dist_id']]   = $row['prov_id'];

        $so_arr[$row['dist_id']] = $row['nmbr'];
    }    
    //echo '<pre>'.$total_reporting_wh;print_r($so_arr);print_r($reporting_wh_arr);exit;    

    //xml for chart
    $xmlstore = '<chart caption="'.$caption.'" yaxismaxvalue="100"  exportEnabled="1" subcaption="" xaxisname="Districts" yaxisname="Percentage" palettecolors="#26C281,#FF0000,#57c5ed" numberprefix="" theme="fint">';
   
    $xmlstore .= ' <categories>';
    foreach($dist_arr as $dist_id => $name)
    {
         $xmlstore .= '     <category label="'.$name.'"/>';  
    }
    $xmlstore .= ' </categories>';
    
    $xmlstore .= ' <dataset seriesname="Stock Availability Rate at SDPs" showvalues="1">';
    foreach($dist_arr as $dist_id => $name)
    {
        $val = (isset($so_arr[$dist_id])?$so_arr[$dist_id]:0);
        if(!empty($reporting_wh_arr[$dist_id]) && $reporting_wh_arr[$dist_id] > 0)
            $perc = ((!empty($val)?$val:0)* 100)/$reporting_wh_arr[$dist_id];
        else
            $perc = 0;
        
        
        $dist_for_table[$dist_id]['sa_rate'] = $perc; 
        
            
        $xmlstore .= '     <set label="'.$name.'"  value="'.(number_format($perc  , 2)).'"  link="JavaScript:showDrillDown_lvl3('.(!empty($parents_arr[$dist_id])?$parents_arr[$dist_id]:'\'all\'').',\''.$province_name_param.'\',\''.$from_date.'\','.$stakeholder.',\''.$stk_name.'\',\''.$prod_id.'\',\''.$prod_name.'\',\''.$dist_id.'\',\''.$dist_arr[$dist_id].'\');"  />';
    }
    $xmlstore .= '  </dataset>';

    $xmlstore .= ' <dataset  seriesname="Reporting Rate (SDP Level)"  renderas="line" showvalues="1">';
    foreach($dist_arr as $dist_id => $name)
    {
        $rep = (isset($reporting_wh_arr[$dist_id])?$reporting_wh_arr[$dist_id]:'0');
        $tot = (isset($tot_sdp[$dist_id])?$tot_sdp[$dist_id]:'0');
        if(isset($tot) && $tot>0) $r_rate = $rep * 100 / $tot;
        else $r_rate = 0;
        
        $xmlstore .= '     <set label="'.$name.'" value="'.(number_format($r_rate,1)).'"   />';
        
        $dist_for_table[$dist_id]['r_rate'] = $r_rate; 
    }
    $xmlstore .= '  </dataset>';
$xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/StackedColumn2DLine.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    //print_r($dist_for_table);
    ?>
	</div>
</div>


    <div class="row">
       <div class="well well-dark ">
        <h3 align="center">Districts Contact List <b><span class="btn yellow-gold"><?= $stk_name.'  - '.$prod_name?></span></b></h3>
        </div>
    </div>
     
<div class="row">    
    <div class=" col-md-11">
     <div class=" "  style=" height: 430px; overflow-y: scroll;">
        <table class=" table table-bordered table-condensed">
        <?php
        $c=1;
        
            echo '<tr>';
            echo '<td>#</td>';
            echo '<td>District</td>';
            echo '<td>Reporting Rate</td>';
            echo '<td>Stock Availability Rate</td>';
            echo '<td>Action</td>';
            echo '</tr>';
            foreach($dist_for_table as $dist_id => $v)
            {
                echo '<tr>';
                echo '<td>'.$c++.'</td>';
                echo '<td>'.$dist_arr[$dist_id].'</td>';
                echo '<td align="right" class="'.(($v['r_rate']<=0)?' red ':'').'">'.number_format($v['r_rate'],2).' %</td>';
                echo '<td align="right"  class="'.(($v['sa_rate']<=0)?' red ':'').'">'.number_format($v['sa_rate'],2).' %</td>';
                echo '<td align=" ">';
                //echo '<a class="btn btn-sm blue-madison show_sdp" onclick="JavaScript:showDrillDown_lvl3('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$stakeholder.',\''.$stk_name.'\',\''.$prod_id.'\',\''.$prod_name.'\',\''.$dist_id.'\',\''.$dist_arr[$dist_id].'\');"">Show SDPs</a>';
                //echo '</td>';
                //echo '<td>';
        
                @$user_id   = $district_contacts[$dist_id]['UserID'];
                @$email     = $district_contacts[$dist_id]['email'];
                @$cell      = $district_contacts[$dist_id]['cell'];
                $row['wh_id'] = '';
                
                $deny_access =false;
                if( $_SESSION['user_level'] > 2 )
                {
                    $deny_access =true;
                }

                //$cell=$email='';
                echo '<div class="btn-group btn-group-circle btn-group-sm btn-group-solid">';

                if(empty($email) || $system_settings['dashboard_email'] !='active' || $deny_access)
                    echo ' <button type="button" class="btn  grey-gallery "   ><i class="fa fa-envelope"></i> Email</button>';
                else
                    echo ' <button type="button" class="btn green-jungle "  onclick="window.open(\'send_email.php?user_id='.$user_id.'&for_warehouse='.((!empty($row['wh_id'])?$row['wh_id']:'all')).'\', \'_blank\', \'scrollbars=1,width=600,height=500\');"><i class="fa fa-envelope"></i> Email</button>';

                if(empty($cell)|| $system_settings['dashboard_sms'] !='active' || $deny_access)
                    echo ' <button type="button" class="btn grey-gallery"   ><i class="fa fa-comment"></i> Message</button>';
                else
                    echo ' <button type="button" class="btn green"  onclick="window.open(\'send_sms.php?user_id='.$user_id.'&for_warehouse='.((!empty($row['wh_id'])?$row['wh_id']:'all')).'\', \'_blank\', \'scrollbars=1,width=600,height=500\');"><i class="fa fa-comment"></i> Message</button>';


                echo '</div>';
                echo '</td>';
                
                echo '</tr>';
            }
        ?>
        </table>
    </div>
    </div>
</div>

<script>
         
     function showDrillDown_lvl3(prov,prov_name,from_date,stk,stk_name,prod_id,prod_name,dist_id,dist_name) {
        var div_id = "drilldown_div_3";
        var url = 'drilldown_fp2020_b2_3.php';
        
        var dataStr='';
        dataStr += "province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name+"&prod_id="+prod_id+"&prod_name="+prod_name+"&dist_id="+dist_id+"&dist_name="+dist_name;

        $('#'+div_id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

        $.ajax({
            type: "POST",
            url: '<?php echo APP_URL; ?>dashboard/' + url,
            data: dataStr,
            dataType: 'html',
            success: function(data) {
                    $("#"+div_id).html(data);
                    $("#"+div_id).focus();
                    $("html, body").animate({ scrollTop: $(document).height() }, 1000);
            },
            beforeSend:function(){
                    $("#"+div_id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");
                    $("#"+div_id).parent().show();
            }
        });
    
    }
    </script>