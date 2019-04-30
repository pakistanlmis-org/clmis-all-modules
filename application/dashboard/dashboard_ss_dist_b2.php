<?php
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-01", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));

$dist_name= $_REQUEST['dist_name'];
$dist = $_REQUEST['dist'];

//Chart caption
$caption = "Stock Availability Rate at SDPs";
//Chart heading sub Caption




$product = (!empty($_REQUEST['product'])?$_REQUEST['product']:'1,2,9,3,5,7,8,13');

$funding_source = (!empty($_REQUEST['stakeholder'])?$_REQUEST['stakeholder']:'');
if(!empty($funding_source))
{
        $qry_c= "SELECT
                    GROUP_CONCAT(funding_stk_prov.stakeholder_id) as stk
                FROM
                    funding_stk_prov
                INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                WHERE
                    funding_stk_prov.province_id = $province 
                    AND  funding_stk_prov.funding_source_id = $funding_source 
                 ";   
            
    $res = mysql_query($qry_c);
    $comments_arr =array();
    $row=mysql_fetch_assoc($res);
}
$stakeholder    = (!empty($row['stk']))?$row['stk']:'';
//print_r($stakeholder);exit;



//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b2';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        //making list of items , to display list incase no data entry is found
        $qry_1 = "  SELECT
                        itminfo_tab.itmrec_id,
                        itminfo_tab.itm_name,
                        itminfo_tab.itm_id
                    FROM
                        itminfo_tab
                    WHERE
                        itminfo_tab.itm_id in ($product)
                    ORDER BY
                        itminfo_tab.frmindex ASC
                ";
        
        
                $res_1 = mysql_query($qry_1);
                $itm_arr=$itm_arr2 = array();
                while($row_1 = mysql_fetch_array($res_1))
                {
                    $itm_arr[$row_1['itm_id']]=$row_1['itm_name'];
                }
       
        
        //query for getting reported facilities
        $q_reporting  = "SELECT
                                    tbl_hf_data.item_id,
                                    COUNT(
                                            DISTINCT tbl_warehouse.wh_id
                                    ) AS reportedWH
                            FROM
                                    tbl_warehouse
                            INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
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
                            AND tbl_warehouse.dist_id = ".$dist."";
        
        if(!empty($stakeholder))
            $q_reporting .= " AND tbl_warehouse.stkid in($stakeholder) ";
        
        $q_reporting .= " 
                            AND stakeholder.lvl = 7
                            AND tbl_hf_data.reporting_date = '".$from_date."'
                            GROUP BY
                                 tbl_hf_data.item_id   ";
        //echo $q_reporting;exit;
        $res_reporting = mysql_query($q_reporting);
        $reporting_wh_arr = array();
        $total_reporting_wh = 0;
        $stk_name='';
        while($row=mysql_fetch_assoc($res_reporting))
        {
            //$stk_name = $row['stkname'];
            $reporting_wh_arr[$row['item_id']]=$row['reportedWH'];
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
            ";
        
        if(!empty($stakeholder))
            $qry .= " AND tbl_warehouse.stkid in($stakeholder) ";
        
        $qry .= "    
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
                    AND itminfo_tab.itm_id in ($product)
                    AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                    AND  IFNULL(ROUND( (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 2 ),0) > 0
                    AND itminfo_tab.itm_category = 1
                    AND itminfo_tab.itm_id NOT IN(4,6,10,33)
                GROUP BY
                tbl_warehouse.stkid,
                tbl_hf_data.item_id 
                ORDER BY 
                tbl_warehouse.stkid,
                tbl_hf_data.item_id 
        ";
//    echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c=1;
    $so_arr2  = $so_arr3 = $so_arr = array();
   
    while($row = mysql_fetch_assoc($qryRes))
    {
        $itm_arr[$row['item_id']] = $row['itm_name'];

        if(empty($so_arr[$row['item_id']])) $so_arr[$row['item_id']]=0;
        
        $so_arr[$row['item_id']] += $row['stock_outs'];
        $so_arr2[$row['item_id']][$row['stkid']] = $row['stock_outs'];
        
    }    
    //echo '<pre>'.$total_reporting_wh;print_r($so_arr);exit;    

    //xml for chart
    $xmlstore = '<chart caption="'.$caption.' - '.$prov_name.'- '.$dist_name.'" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage"  palettecolors="#26C281"  numberprefix="" theme="fint">';
 
    //$xmlstore .= ' <dataset>';
    foreach($itm_arr as $itm_id => $itm_name)
    {
        $val = (isset($so_arr[$itm_id])?$so_arr[$itm_id]:0);
        if(!empty($reporting_wh_arr[$itm_id]) && $reporting_wh_arr[$itm_id] > 0)
            $perc = ((!empty($val)?$val:0)* 100)/$reporting_wh_arr[$itm_id];
        else
            $perc = 0;
        
        
        //$xmlstore .= '     <set label="'.$itm_name.',Av:'.$val.',Total:'.$reporting_wh_arr[$itm_id].'" value="'.(number_format($perc  , 1)).'" link="JavaScript:showDrillDown_b2('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$itm_id.',\''.$itm_arr[$itm_id].'\',\''.$_SESSION['user_stakeholder1'].'\',\''.$stk_name.'\',\''.$dist.'\',\''.$dist_name.'\');"  tooltext="'.$itm_arr[$itm_id].':'.(number_format($perc  , 1)).' {br} Click bar to drill down"  />';
        $xmlstore .= '     <set label="'.$itm_name.'" value="'.(number_format($perc  , 1)).'" link="JavaScript:showDrillDown_dist_b2('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$itm_id.',\''.$itm_arr[$itm_id].'\',\''.$stakeholder.'\',\''.$stk_name.'\',\''.$dist.'\',\''.$dist_name.'\');"  tooltext="'.$itm_arr[$itm_id].':'.(number_format($perc  , 1)).' {br} Click bar to drill down"  />';
    }
    //$xmlstore .= '  </dataset>';

$xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/Column2D.swf", "", $xmlstore, $chart_id, '100%', 300, false, false);
    ?>
	</div>
</div>