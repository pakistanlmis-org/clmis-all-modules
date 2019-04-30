<?php

ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");

include APP_PATH."includes/classes/functions.php";
//include(PUBLIC_PATH . "html/header.php");
require(PUBLIC_PATH."FusionCharts/Code/PHP/includes/FusionCharts.php");

$subCaption='';
$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$stakeholder = $_REQUEST['stk_id'];
$stk_name = $_REQUEST['stk_name'];
$prod_id = $_REQUEST['prod_id'];
$prod_name = $_REQUEST['prod_name'];

$dist = $_REQUEST['dist_id'];
$dist_name = $_REQUEST['dist_name'];

//Chart caption
$caption = "Stock Availability Status in Reporting Districts";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b3';

?>
<?php

include PUBLIC_PATH . "html/top_im.php";
?>
<link href="<?php echo PUBLIC_URL;?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;display:none;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
       
        
        //query for getting reported facilities
        $q_reporting  = "SELECT
                                    tbl_warehouse.stkid,
                                    stk2.stkname,
                                    COUNT(
                                            DISTINCT tbl_warehouse.wh_id
                                    ) AS reportedWH
                            FROM
                                    tbl_warehouse
                            INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                            INNER JOIN stakeholder as stk2 ON tbl_warehouse.stkid = stk2.stkid
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
                            AND tbl_warehouse.dist_id = ".$dist."
                                
                            AND stakeholder.lvl = 7
                            AND tbl_hf_data.reporting_date = '".$from_date."'
                            AND tbl_hf_data.item_id = $prod_id";
        
        if(!empty($stakeholder))
            $q_reporting  .= " AND tbl_warehouse.stkid in($stakeholder) ";
            
            $q_reporting  .= " GROUP BY
                                    tbl_warehouse.stkid";
//        echo $q_reporting;exit;
        $res_reporting = mysql_query($q_reporting);
        $reporting_wh_arr = $stk_arr = array();
        $total_reporting_wh = 0;
        while($row=mysql_fetch_assoc($res_reporting))
        {
            $stk_name = $row['stkname'];
            $reporting_wh_arr[$row['stkid']]=$row['reportedWH'];
            $stk_arr[$row['stkid']]=$row['stkname'];
            $total_reporting_wh +=$row['reportedWH'];
        }
        
        
        
        //Query for shipment main dashboard
        $qry = "SELECT
                tbl_warehouse.stkid,
                tbl_hf_data.item_id ,
                itminfo_tab.itm_name,
                count(tbl_hf_data.pk_id) as stock_outs
                    FROM
                            tbl_warehouse
                    INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                    INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                    WHERE
                            stakeholder.lvl = 7
                    AND tbl_warehouse.prov_id = ".$province."
                    AND tbl_warehouse.dist_id = ".$dist."
                        
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
                    AND  IFNULL(ROUND( (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 2 ),0) > 0
                    AND itminfo_tab.itm_category = 1
                    AND itminfo_tab.itm_id = $prod_id";
        
        if(!empty($stakeholder))
            $qry  .= " AND tbl_warehouse.stkid in($stakeholder) ";
            
            $qry  .= "
                GROUP BY
                tbl_warehouse.stkid,
                tbl_hf_data.item_id 
                ORDER BY 
                tbl_warehouse.stkid,
                tbl_hf_data.item_id 
        ";
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c=1;
    $so_arr2  = $so_arr3 = $so_arr=$itm_arr = array();
   
    while($row = mysql_fetch_assoc($qryRes))
    {
        $itm_arr[$row['item_id']] = $row['itm_name'];

        if(empty($so_arr[$row['item_id']])) $so_arr[$row['item_id']]=0;
        
        $so_arr[$row['item_id']] += $row['stock_outs'];
        $so_arr2[$row['item_id']][$row['stkid']] = $row['stock_outs'];
        
    }    
    //echo '<pre>'.$total_reporting_wh;print_r($itm_arr);print_r($so_arr);print_r($reporting_wh_arr);exit;    

    //xml for chart
    $xmlstore = '<chart caption="'.$caption.' - '.$prod_name.' - '.$prov_name.'- '.$dist_name.'" yaxismaxvalue="100"  subcaption="" xaxisname="Stakeholders" exportEnabled="1"  yaxisname="Percentage" numberprefix="" theme="fint">';
 
    $xmlstore .= ' <dataset>';
    foreach($stk_arr as $stk_id => $stk_name)
    {
        $val = (isset($so_arr2[$prod_id][$stk_id])?$so_arr2[$prod_id][$stk_id]:0);
        if(!empty($reporting_wh_arr[$stk_id]) && $reporting_wh_arr[$stk_id] > 0)
            $perc = ((!empty($val)?$val:0)* 100)/$reporting_wh_arr[$stk_id];
        else
            $perc = 0;
        
        
        $xmlstore .= '     <set label="'.$stk_name.'" value="'.(number_format($perc  , 1)).'" link="JavaScript:showDrillDown_lvl2('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$prod_id.',\''.$itm_arr[$prod_id].'\',\''.$stk_id.'\',\''.$stk_name.'\',\''.$dist.'\',\''.$dist_name.'\');"  tooltext="'.(number_format($perc  , 1)).' {br} Click bar to drill down"  />';
        //$xmlstore .= '     <set label="'.$stk_name.',Av:'.$val.',Total:'.$reporting_wh_arr[$stk_id].'" value="'.(number_format($perc  , 1)).'" link="JavaScript:showDrillDown_lvl2('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$prod_id.',\''.$itm_arr[$prod_id].'\',\''.$stk_id.'\',\''.$stk_name.'\',\''.$dist.'\',\''.$dist_name.'\');"  tooltext="'.(number_format($perc  , 1)).' {br} Click bar to drill down"  />';
    }
    $xmlstore .= '  </dataset>';

$xmlstore .= ' </chart>';
    //end chart
   //echo $xmlstore;exit;
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/Column2D.swf", "", $xmlstore, $chart_id, '100%', 300, false, false);
  ?>
	</div>
</div>
    
    <div class="widget widget-tabs">    
        <div class="widget-body" id="drilldown_div_2">
            
        </div>
    </div>
    <script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    
    <script>
         
     function showDrillDown_lvl2(prov,prov_name,from_date,prod_id,prod_name,stk,stk_name,dist_id,dist_name) {
       
        //window.open("drilldown_stk_1.php?province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name ,"", "width=800,height=700");
        
        
        var url = 'drilldown_ss_dist_b2_2.php';
        
        var dataStr='';
        dataStr += "province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name+"&prod_id="+prod_id+"&prod_name="+prod_name+"&dist_id="+dist_id+"&dist_name="+dist_name;

        $('#drilldown_div_2').html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

        $.ajax({
            type: "POST",
            url: '<?php echo APP_URL; ?>dashboard/' + url,
            data: dataStr,
            dataType: 'html',
            success: function(data) {
                    $("#drilldown_div_2").html(data);
            }
        });
    
    }
    </script>

