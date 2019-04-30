<?php
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$months= array();
$caption = "Trends";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'a1';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        $qry = "SELECT
                data_mismatches_log.comparison_type,
                data_mismatches_log.`month`,
                data_mismatches_log.province,
                GROUP_CONCAT(data_mismatches_log.mismatches_count,'/',data_mismatches_log.out_of_possibilites_checked,'@',data_mismatches_log.checked_at_time) 
                as log
                FROM
                data_mismatches_log
                WHERE
                data_mismatches_log.stakeholder ='' AND
                data_mismatches_log.district = ''
                GROUP BY
                data_mismatches_log.comparison_type,
                data_mismatches_log.`month`,
                data_mismatches_log.province


 
        ";
//    echo $qry;exit;
    $qryRes = mysql_query($qry);
    $disp_arr =$timestamps= $temp = array();
    while($row = mysql_fetch_assoc($qryRes))
    {
         $mykey = $row['comparison_type'].' , '.$row['province'].' , '.$row['month'];
        
        $logs=array();
        $logs = explode(',',$row['log']);
        foreach($logs as $k => $this_log){
            //$temp[$mykey]=
            $a = explode('/',$this_log);
            $b = explode('@',$a[1]);
            
            $this_count = $a[0];
            $this_time= $b[1];
            if($this_time <= '2018-04-10 11:00:00')
            $timestamps[]=$this_time;
            
            $disp_arr[$mykey][$this_time]=$this_count;
        }
        
    }    
//    echo '<pre>';print_r($disp_arr);print_r($timestamps);exit; 
    //echo '<pre>';print_r($months);exit;    

    //xml for chart
    $xmlstore = '<chart caption="Trendlines - National Consumption "  subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Day" yaxisname="Consumption" showvalues="0" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
 
    $xmlstore .= ' <categories>';
    foreach($timestamps as $k => $timestamp)
    {
        $xmlstore .= ' <category label="'.$timestamp.'" />';
    }
    $xmlstore .= ' </categories>';
    
    foreach($disp_arr as $key => $key_data)
    {
        $xmlstore .= ' <dataset seriesname="'.$key.'">';
        foreach($key_data as $timestamp => $count)
        {   
            $val=(!empty($count)?$count:'0');
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