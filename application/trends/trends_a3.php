<?php
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$products = $_REQUEST['products'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$months= array();
$caption = "Trends";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'a3';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        $qry = "SELECT
                        tbl_warehouse.prov_id,
                        tbl_warehouse.stkid,
                        tbl_hf_data.item_id,
                        itminfo_tab.itm_name,
                        tbl_hf_data.pk_id,
                        tbl_hf_data.closing_balance,
                        tbl_hf_data.avg_consumption,
                        tbl_hf_data.reporting_date
                FROM
                        tbl_warehouse
                INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                WHERE
                        stakeholder.lvl = 7
                        AND tbl_warehouse.prov_id =1
                        AND tbl_warehouse.stkid =1
                        AND itminfo_tab.itm_category = 1
                        AND itminfo_tab.itm_id  IN (1,5,7,9) 
                        
                ORDER BY
                        tbl_hf_data.reporting_date ASC
 
        ";
    //echo $qry;exit;
    $qryRes = mysql_unbuffered_query($qry);
    while($row = mysql_fetch_assoc($qryRes))
    {
        if(empty($disp_arr[$row['item_id']][$row['reporting_date']]['stock_outs'])) $disp_arr[$row['item_id']][$row['reporting_date']]['stock_outs']=0;
        if( $row['closing_balance'] <= '0' )
        {
            $disp_arr[$row['item_id']][$row['reporting_date']]['stock_outs'] += 1;
        }
        
        $item_arr[$row['item_id']] = $row['itm_name'];
        $months[$row['reporting_date']]=date('Y-M',strtotime($row['reporting_date']));
        
    }    
    //echo '<pre>';print_r($disp_arr);exit; 
    //echo '<pre>';print_r($months);exit;    

    //xml for chart
    $xmlstore = '<chart caption="Trendlines - No of SDPs Stock Out Over Time"  subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Day" yaxisname="Consumption" showvalues="1" palettecolors="#AF1AA5,#AF711A,#D93636,#0075c2,#1aaf5d" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
 
    $xmlstore .= ' <categories>';
    foreach($months as $k => $month)
    {
        $xmlstore .= ' <category label="'.date('M-Y',strtotime($month)).'" />';
    }
    $xmlstore .= ' </categories>';
    
    foreach($disp_arr as $itm_id => $itm_data)
    {
        $xmlstore .= ' <dataset seriesname="'.$item_arr[$itm_id].'">';
        foreach($months as $k => $month)
        {   
            $val=(!empty($itm_data[$k]['stock_outs'])?$itm_data[$k]['stock_outs']:'0');
            $xmlstore .= '    <set  value="'.$val.'"  />';
        }
        $xmlstore .= '  </dataset>';
        
    }
    $xmlstore .= ' </chart>';
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSSpline.swf", "", $xmlstore, $chart_id, '100%', 300, false, false);
    ?>
	</div>
</div>