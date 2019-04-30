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
/*$months[] =$from_date;
for ($i = 1; $i <= 11; $i++) {
    $months[] = date("Y-m-01", strtotime( date( 'Y-m-01',strtotime($from_date) )." -$i months"));
}
krsort($months);
*/
$caption = "Trends";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'a2';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        $qry = "SELECT
                        summary_national.item_id,
                        summary_national.stakeholder_id,
                        summary_national.reporting_date,
                        summary_national.consumption,
                        summary_national.soh_national_lvl,
                        summary_national.soh_national_store,
                        summary_national.reporting_rate,
                        itminfo_tab.itm_name
                    FROM
                        summary_national
                        INNER JOIN itminfo_tab ON summary_national.item_id = itminfo_tab.itmrec_id
                    WHERE
                        summary_national.item_id = 'IT-001' AND
                        summary_national.stakeholder_id = 1
                    ORDER BY
                        summary_national.reporting_date ASC
 
        ";
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    while($row = mysql_fetch_assoc($qryRes))
    {
        $disp_arr[$row['item_id']][$row['reporting_date']]['consumption']           = $row['consumption'];
        $disp_arr[$row['item_id']][$row['reporting_date']]['soh_national_store']    = $row['soh_national_store'];
        $disp_arr[$row['item_id']][$row['reporting_date']]['soh_national_lvl']      = $row['soh_national_lvl'];
        $item_arr[$row['item_id']] = $row['itm_name'];
        $months[$row['reporting_date']]=date('Y-M',strtotime($row['reporting_date']));
        
    }    
    //echo '<pre>';print_r($disp_arr);exit; 
    //echo '<pre>';print_r($months);exit;    

    //xml for chart
    $xmlstore = '<chart caption="Trendlines - SOH National Store"  subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Day" yaxisname="Consumption" showvalues="0" palettecolors="#1aaf5d,#AF1AA5,#AF711A,#D93636,#0075c2" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
 
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
            $val=(!empty($itm_data[$k]['soh_national_store'])?$itm_data[$k]['soh_national_store']:'0');
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