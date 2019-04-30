<?php
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$products= $_REQUEST['products'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$months= array();
$months[] =$from_date;
for ($i = 1; $i <= 11; $i++) {
    $months[] = date("Y-m-01", strtotime( date( 'Y-m-01',strtotime($from_date) )." -$i months"));
}
krsort($months);

//echo '<pre>';print_r($months);
$warehouse_id = $_REQUEST['warehouse'];
//$dist_name= $_REQUEST['dist_name'];
$dist = $_REQUEST['dist'];

//Chart caption
$caption = "Stock Availability Status in Reporting Districts";
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
        $where = '  AND itminfo_tab.itm_id NOT IN(4,6,10,33) ';
       if(!empty($products) && $products=='without_condom')
        $where = '  AND itminfo_tab.itm_id NOT IN(1,4,6,10,33) ';
        
        //Query for shipment main dashboard
        $qry = "SELECT
                tbl_warehouse.stkid,
                tbl_hf_data.item_id ,
                itminfo_tab.itm_name,	
                MONTH(tbl_hf_data.reporting_date) as mon,
                YEAR(tbl_hf_data.reporting_date) as yr,
                tbl_hf_data.reporting_date,
                (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption) as mos
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

                    AND tbl_hf_data.reporting_date BETWEEN '".$months[11]."' AND '".$months[0]."'

                    AND itminfo_tab.itm_category = 1
                    $where
                    AND tbl_hf_data.warehouse_id = '".$warehouse_id."'
                GROUP BY
                        YEAR(tbl_hf_data.reporting_date),
                        MONTH(tbl_hf_data.reporting_date),
                        tbl_hf_data.item_id
                ORDER BY

                        YEAR(tbl_hf_data.reporting_date),
                        MONTH(tbl_hf_data.reporting_date),
                        tbl_hf_data.item_id 
        ";
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c=1;
    $disp_arr = $item_arr = array();
   
    while($row = mysql_fetch_assoc($qryRes))
    {
        $disp_arr[$row['item_id']][$row['reporting_date']] = $row['mos'];
        $item_arr[$row['item_id']] = $row['itm_name'];
        
    }    
    //echo '<pre>';print_r($disp_arr);exit;    

    //xml for chart
    $xmlstore = '<chart caption="Months of stock (MOS) Trend"   subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Day" yaxisname="MOS" showvalues="0" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
 
    
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
            $mos=(!empty($itm_data[$month])?$itm_data[$month]:'0');
            $xmlstore .= '    <set  value="'.number_format($mos,2).'"  />';
        }
        $xmlstore .= '  </dataset>';
        
    }
    

$xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSSpline.swf", "", $xmlstore, $chart_id, '100%', 300, false, false);
    ?>
	</div>
</div>