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
$caption = "Health Facilities Compliance Rate";
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
$chart_id = 'b3';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        
        //query for getting reported facilities
        $q_reporting  = "select stk_id,stkname,count(wh_id) as total, sum(is_reported) as reported
                from 
                ( 
                SELECT
                DISTINCT tbl_warehouse.wh_id,
                        tbl_warehouse.stkid,
                        stk2.stkid as stk_id,
                        stk2.stkname,
                (select count(distinct tbl_hf_data.pk_id )
                        from tbl_hf_data 
                        where tbl_hf_data.warehouse_id=tbl_warehouse.wh_id 
                        AND tbl_hf_data.reporting_date =  '".$from_date."'
                        AND tbl_hf_data.item_id = 1) as is_reported

                FROM
                        tbl_warehouse
                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		INNER JOIN stakeholder as stk2 ON tbl_warehouse.stkid = stk2.stkid
                WHERE

                 tbl_warehouse.wh_id NOT IN (
                        SELECT
                                warehouse_status_history.warehouse_id
                        FROM
                                warehouse_status_history
                        INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                        WHERE
                                warehouse_status_history.reporting_month = '".$from_date."'
                        AND warehouse_status_history.`status` = 0
                )
                AND tbl_warehouse.prov_id = $province
                AND tbl_warehouse.dist_id = $dist
                    
                AND stakeholder.lvl = 7";
        if(!empty($stakeholder))
        $q_reporting  .= "
                AND tbl_warehouse.stkid in ($stakeholder) ";
        $q_reporting  .= "
                ) A
                group by
                    stkid";
        //echo $q_reporting;exit;
        $res_reporting = mysql_query($q_reporting);
        $reporting_wh_arr =$stk_arr =  array();
        $total_reporting_wh = 0;
        while($row=mysql_fetch_assoc($res_reporting))
        {
            $reporting_wh_arr[$row['stk_id']][$row['total']]=$row['reported'];
            $stk_arr[$row['stk_id']]=$row['stkname'];
        }
        //echo '<pre>';print_r($reporting_wh_arr);exit;
    //xml for chart
    $xmlstore = '<chart caption="'.$caption.' - '.$prov_name.'- '.$dist_name.'" yaxismaxvalue="100"  subcaption="" xaxisname="Compliance Rate" exportEnabled="1"  yaxisname="Percentage"  palettecolors="#26C281"  numberprefix="" theme="fint">';
 
    //$xmlstore .= ' <dataset>';
    foreach($reporting_wh_arr as $stkid => $stk_data)
    {
        foreach($stk_data as $total => $reported)
        {
            $stk_name = $stk_arr[$stkid];
            $perc = ((!empty($reported)?$reported:0)* 100)/$total;
            $xmlstore .= '     <set label="'.$stk_arr[$stkid].'" value="'.(number_format($perc  , 1)).'"   link="JavaScript:showDrillDown_dist_b3('.$province.',\''.$prov_name.'\',\''.$from_date.'\',\''.$dist.'\',\''.$dist_name.'\',\''.$stkid.'\',\''.$stk_name.'\');"  tooltext="'.$stk_name.':'.(number_format(!empty($perc)?$perc:0,1)).' {br} Click bar to drill down"   />';
        }
    }
    //$xmlstore .= '  </dataset>';

$xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/Column2D.swf", "", $xmlstore, $chart_id, '100%', 280, false, false);
    ?>
	</div>
</div>