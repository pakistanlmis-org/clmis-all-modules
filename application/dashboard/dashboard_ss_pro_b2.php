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

//Chart caption
$caption = "Stock Out Status in Reporting Districts";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b2';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        //query for getting all warehouses 
        /*$q_t_wh="SELECT
                            tbl_warehouse.stkid,
                            COUNT(
                                    DISTINCT tbl_warehouse.wh_id
                            ) AS totalWH
                    FROM
                            tbl_warehouse
                    INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
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
                    AND stakeholder.lvl = 7
                    GROUP BY
                            tbl_warehouse.stkid";
        $res_t_wh = mysql_query($q_t_wh);
        $total_wh_arr = array();
        while($row=mysql_fetch_assoc($res_t_wh))
        {
            $total_wh_arr[$row['stkid']]=$row['totalWH'];
        }*/
        
        //query for getting reported facilities
        $q_reporting  = "SELECT
                                    tbl_warehouse.stkid,
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
                            AND stakeholder.lvl = 7
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
                    AND (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption) = 0
                    AND itminfo_tab.itm_category = 1
                    AND itminfo_tab.itm_id NOT IN(4,6,8,13,33)
                GROUP BY
                tbl_warehouse.stkid,
                tbl_hf_data.item_id 
                ORDER BY 
                tbl_warehouse.stkid,
                tbl_hf_data.item_id 
        ";
    
    $qryRes = mysql_query($qry);
    $c=1;
    $so_arr2 = $itm_arr  = $so_arr3 = $so_arr = array();
   
    while($row = mysql_fetch_assoc($qryRes))
    {
        $itm_arr[$row['item_id']] = $row['itm_name'];

        if(empty($so_arr[$row['item_id']])) $so_arr[$row['item_id']]=0;
        
        $so_arr[$row['item_id']] += $row['stock_outs'];
        $so_arr2[$row['item_id']][$row['stkid']] = $row['stock_outs'];
        //$so_arr3[$row['item_id']] += $row['stock_outs'];
    }    
    //echo '<pre>'.$total_reporting_wh;print_r($itm_arr);print_r($so_arr2);print_r($so_arr3);print_r($so_arr);print_r($reporting_wh_arr);exit;    

    //xml for chart
    $xmlstore = '<chart caption="Stock Out Rate at SDPs - '.$prov_name.'" yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage" numberprefix="" theme="fint">';
    $xmlstore .= ' <categories>';
    
    foreach($itm_arr as $itm_id => $name)
    {
        $xmlstore .= '<category label="'.$name.'" />';
    }
    $xmlstore .= '</categories>';
   
    $xmlstore .= ' <dataset>';
    foreach($so_arr as $itm_id => $val)
    {
        if(!empty($total_reporting_wh) && $total_reporting_wh > 0)
            $perc = ((!empty($val)?$val:0)* 100)/$total_reporting_wh;
        else
            $perc = 0;
        
        $xmlstore .= '     <set value="'.(number_format($perc  , 1)).'" link="JavaScript:showDrillDown_b2('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$itm_id.',\''.$itm_arr[$itm_id].'\');"  tooltext="'.$itm_arr[$itm_id].':'.(number_format($perc  , 1)).' {br} Click for details"  />';
    }
    $xmlstore .= '  </dataset>';

$xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/Column2D.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    ?>
	</div>
</div>