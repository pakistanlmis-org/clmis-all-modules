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


$prod_id = $_REQUEST['prod_id'];
$prod_name = $_REQUEST['prod_name'];

//Chart caption
$caption = "Stock Sufficiency Status in Reporting Districts";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b2';
?>
<?php

include PUBLIC_PATH . "html/top_im.php";
?>
<script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;display:none;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        //query for getting reported facilities
        $q_reporting  = "SELECT
                                   tbl_warehouse.stkid,
                                    COUNT(
                                            DISTINCT tbl_warehouse.wh_id
                                    ) AS reportedWH
                            FROM
                                    tbl_warehouse
                            INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
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
                            AND tbl_warehouse.prov_id = ".$province."
                            
                            AND tbl_hf_data.reporting_date = '".$from_date."'
                            GROUP BY
                                    tbl_warehouse.stkid";
        $res_reporting = mysql_query($q_reporting);
        $reporting_wh_arr = array();
        $total_reporting_wh = 0;
        while($row=mysql_fetch_assoc($res_reporting))
        {
            $reporting_wh_arr[$row['stkid']]=$row['reportedWH'];
            $total_reporting_wh +=$row['reportedWH'];
        }
        
        
        
        //Query for shipment main dashboard
        $qry = "SELECT
                stakeholder.stkid,
                stakeholder.stkname,
                tbl_hf_data.item_id ,
                itminfo_tab.itm_name,
                count(tbl_hf_data.pk_id) as stock_outs
                    FROM
                            tbl_warehouse
                    INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkid
                    INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                    WHERE
                        
                    tbl_warehouse.prov_id = ".$province."
                    AND tbl_hf_type_rank.province_id = ".$province."

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
                GROUP BY
                stakeholder.stkid,
                tbl_hf_data.item_id 
                ORDER BY 
                stakeholder.stkid,
                tbl_hf_data.item_id 
        ";
    
    $qryRes = mysql_query($qry);
    $c=1;
    $dist_arr  = $so_arr = array();
   
    while($row = mysql_fetch_assoc($qryRes))
    {
        $dist_arr[$row['stkid']] = $row['stkname'];

        $so_arr[$row['stkid']] = $row['stock_outs'];
        
    }    
    //echo '<pre>'.$total_reporting_wh;print_r($itm_arr);print_r($so_arr);print_r($reporting_wh_arr);exit;    

    //xml for chart
    $xmlstore = '<chart caption="Stock Sufficiency Rate at SDPs - '.$prov_name.' - '.$prod_name.'" yaxismaxvalue="100"  subcaption="" xaxisname="Products" yaxisname="Percentage" numberprefix="" theme="fint">';
    $xmlstore .= ' <categories>';
    
    foreach($dist_arr as $itm_id => $name)
    {
        $xmlstore .= '<category label="'.$name.'" />';
    }
    $xmlstore .= '</categories>';
   
    $xmlstore .= ' <dataset seriesname="Stock Sufficiency Rate at SDPs">';
    foreach($so_arr as $dist_id => $val)
    {
        if(!empty($reporting_wh_arr[$dist_id]) && $reporting_wh_arr[$dist_id] > 0)
            $perc = ((!empty($val)?$val:0)* 100)/$reporting_wh_arr[$dist_id];
        else
            $perc = 0;
        
        $reverse_perc=100-$perc;
        $xmlstore .= '     <set value="'.(number_format($perc  , 2)).'"  link="JavaScript:showDrillDown_lvl2('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$dist_id.',\''.$dist_arr[$dist_id].'\',\''.$prod_id.'\',\''.$prod_name.'\');" />';
    }
    $xmlstore .= '  </dataset>';

$xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSCombi2D.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    ?>
	</div>
</div>
     <div class="widget widget-tabs" style='display:none;'>    
        <div class="widget-body" id="drilldown_div_2">
            
        </div>
    </div>
    
     <div class="widget widget-tabs" style='display:none;'>    
        <div class="widget-body " id="drilldown_div_3">
            
        </div>
    </div>
<script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    
<script>
         
     function showDrillDown_lvl2(prov,prov_name,from_date,stk,stk_name,prod_id,prod_name) {
       
        var div_id = "drilldown_div_2";
        var url = 'drilldown_internal_b2_2.php';
        
        var dataStr='';
        dataStr += "province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name+"&prod_id="+prod_id+"&prod_name="+prod_name;

        $('#'+div_id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

        $.ajax({
            type: "POST",
            url: '<?php echo APP_URL; ?>dashboard/' + url,
            data: dataStr,
            dataType: 'html',
            success: function(data) {
                    $("#"+div_id).html(data);
            },
            beforeSend:function(){
                    
                    $("#"+div_id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");
                    $("#"+div_id).parent().show();
            }
        });
    
    }
    </script>