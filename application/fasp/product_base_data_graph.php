<?php
//echo '<pre>';print_r($_REQUEST);exit;    
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

@$products  = $_REQUEST['products'];
@$province  = $_REQUEST['province'];
$years_arr  = explode(',',$_REQUEST['years']);
$years_arr  = array_unique($years_arr);
$years_arr  = array_filter($years_arr);
$count_of_years = count($years_arr);
$years      = implode(',',$years_arr);

// echo '<pre>';print_r($years_arr); 
$where_y = " AND ( ";
$y2 = $fiscal_months =$fiscal_mapping= array();
foreach ($years_arr as $k=> $this_y){
    $y2[] = " ( reporting_date BETWEEN '".$this_y."-07-01' AND '".($this_y+1)."-06-30' )  ";
    
    for($i=07;$i<=12;$i++){
            $fiscal_months[$this_y][sprintf("%02d", $i)]=$this_y."-".sprintf("%02d", $i)."-01";
            $fiscal_mapping[$this_y."-".sprintf("%02d", $i)."-01"] = $this_y;
        }
    for($i=01;$i<=06;$i++){
            $fiscal_months[$this_y][sprintf("%02d", $i)]=($this_y+1)."-".sprintf("%02d", $i)."-01";
            $fiscal_mapping[($this_y+1)."-".sprintf("%02d", $i)."-01"] = $this_y;
        }
}
$y3 = implode(' OR ',$y2);
$where_y .= "  ".$y3;
$where_y .= " ) ";
//echo $where_y;
//echo '<pre>';print_r($fiscal_mapping);exit; 


$months = $months2 = array();


$caption = "Trends";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = $_REQUEST['dashlet'];
    for($i=07;$i<=12;$i++){
        $months[sprintf("%02d", $i)]=sprintf("%02d", $i);
    }
    for($i=01;$i<=06;$i++){
        $months[sprintf("%02d", $i)]=sprintf("%02d", $i);
    }
    
//echo '<pre>';print_r($months);exit;   
?>
<div class="portlet">    
    <div class="portlet-body">
        <ul class="nav nav-tabs nav-justified">
                <li class="active btn default1">
                        <a href="#tab_comparison" data-toggle="tab">
                        Yearly Comparison </a>
                </li>
                <li class=" btn default1">
                        <a href="#tab_line" data-toggle="tab">
                        Trendline </a>
                </li>
        </ul>
        
        
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        if($_REQUEST['level']==3){
            $qry = "SELECT
                        summary_district.item_id,
                        summary_district.stakeholder_id,
                        summary_district.reporting_date,
                        summary_district.consumption,
                        summary_district.province_id,
                        summary_district.reporting_rate,
                        itminfo_tab.itm_name,
                        date_format(reporting_date,'%Y') as year,
                        date_format(reporting_date,'%m') as month,
                        summary_district.total_health_facilities
                    FROM
                        summary_district
                        INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
                    WHERE
                        itminfo_tab.itm_id = '".$_REQUEST['prod_id']."' AND
                        summary_district.stakeholder_id in ('".$_REQUEST['stakeholders']."')  AND
                        summary_district.district_id = '".$_REQUEST['dist_id']."' 
                        ".$where_y."    
                    ORDER BY
                        summary_district.reporting_date ASC
        ";
        }
        else
        {
                $qry = "SELECT
                            summary_province.item_id,
                            summary_province.stakeholder_id,
                            summary_province.reporting_date,
                            summary_province.consumption,
                            summary_province.province_id,
                            summary_province.reporting_rate,
                            itminfo_tab.itm_name,
                            date_format(reporting_date,'%Y') as year,
                            date_format(reporting_date,'%m') as month,
                            summary_province.total_health_facilities
                        FROM
                            summary_province
                            INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
                        WHERE
                            itminfo_tab.itm_id = '".$_REQUEST['prod_id']."' AND
                            summary_province.stakeholder_id in ('".$_REQUEST['stakeholders']."')  AND
                            summary_province.province_id = '".$_REQUEST['prov_id']."' 
                            ".$where_y."    
                        ORDER BY
                            summary_province.reporting_date ASC
            ";
        }
//    echo $qry;exit;
    $qryRes = mysql_query($qry);
    $prod_name='';
    $all_months_data = $yearly_non_zero_months =array();
    while($row = mysql_fetch_assoc($qryRes))
    {
        $prod_name=$row['itm_name'];
        $this_fiscal_year = $fiscal_mapping[$row['reporting_date']];
        //$disp_arr[$row['year']][$row['month']]['consumption']   = $row['consumption'];
        $disp_arr[$this_fiscal_year][$row['month']]['consumption']   = $row['consumption'];
        $all_months_data[$row['reporting_date']]   = $row['consumption'];
        @$disp_arr[$this_fiscal_year]['sum_reporting_rate']           += $row['reporting_rate'];
        $disp_arr[$this_fiscal_year]['total_health_facilities']      = $row['total_health_facilities'];
        $months2[$row['reporting_date']]=date('Y-M',strtotime($row['reporting_date']));
        
        if(!empty($row['consumption']) && $row['consumption'] > 0)
            @$yearly_non_zero_months[$this_fiscal_year]+=1;
        
    }    
//    echo '<pre>';print_r($all_months_data);
//    echo '<pre>';print_r($months2); 
//    echo '<pre>';print_r($disp_arr); 
//    echo '<pre>';print_r($fiscal_months);exit;    
    
    //xml for chart
    $xmlstore = '<chart caption="Yearly Trend Comparison - Month Wise Consumption of '.$prod_name.' "  subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Months" yaxisname="Consumption" showvalues="1" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
    $xmlstore2 = '<chart caption="Trendlines - Month Wise Consumption of '.$prod_name.' "  subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Months" yaxisname="Consumption" showvalues="1" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
 
    $xmlstore .= ' <categories>';
    foreach($months as $k => $month)
    {
        $xmlstore .= ' <category label="'.date("F", strtotime('00-'.$month.'-01')).'" />';
    }
    $xmlstore .= ' </categories>';

    $xmlstore2 .= ' <categories>';
    //$xmlstore2 .= ' <category label="a" />';
    foreach($months2 as $k => $month)
    {
        $xmlstore2 .= ' <category label="'.$month.'" />';
    }
    $xmlstore2 .= ' </categories>';
    
    foreach($disp_arr as $year => $itm_data)
    {
        $yy2 = substr(($year+1),2);
        $xmlstore .= ' <dataset seriesname="'.($year.'-'.$yy2).'">';
        foreach($months as $k => $month)
        {   
            $m = $year.'-'.$k.'-01';
            $val=(!empty($itm_data[$k]['consumption'])?$itm_data[$k]['consumption']:'0');
            $xmlstore .= '    <set  value="'.$val.'"  />';
        }
        $xmlstore .= '  </dataset>';
    }
    
    $xmlstore2 .= ' <dataset seriesname="Consumption">';
    foreach($months2 as $k => $month)
    {
        
        $a = explode('-',$k);
        $y = $a[0];
        $m = $a[1];
        
        $this_fiscal_year = $fiscal_mapping[$k];
        $val =(!empty($disp_arr[$this_fiscal_year][$m]['consumption'])?$disp_arr[$this_fiscal_year][$m]['consumption']:'0');
        $xmlstore2 .= '    <set  value="'.$val.'"  />';
        
        
    }    $xmlstore2 .= '  </dataset>';



    
    
    $xmlstore .= ' </chart>';
    $xmlstore2 .= ' </chart>';
    FC_SetRenderer('javascript');
    
    ?>
    
    <div class="tab-content">
            <div class="tab-pane active" id="tab_comparison">
                <?php
                    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSline.swf", "", $xmlstore, $chart_id, '100%', 300, false, false);
                ?>
            </div>    
            <div class="tab-pane" id="tab_line">
                <?php
                    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSline.swf", "", $xmlstore2, 'Second_chart', '100%', 300, false, false);
                ?>
            </div>
        </div>
	</div>
</div>

<form id="frm1" action="product_base_save.php">
<table class="table table-bordered table-condensed">
       <tr class="success">
        <td>Base Year</td>
        <td colspan="12" align="center">Months</td>
        <td colspan="3"> </td>
        
    </tr>
    <tr class="success">
        <td>Base Year</td>
        <?php
        foreach($months as $k => $month)
        {   
            echo '
                    <td >'.date("F", strtotime('00-'.$k.'-01')).'</td>';
        }
        ?>
        
        <td class="info">No of SDPs</td>
        <td class="info">Avg Reporting Rate</td>
        <td class="info">AMC</td>
    </tr>
    <?php
    $total_amc_yearly = 0;
    foreach($disp_arr as $year => $itm_data)
    {
        
            echo '
                <tr>
                    <td class="success">'.$year.'-'.substr(($year+1),2).'</td>
                    ';
        $year_total = 0;
        foreach($months as $k => $month)
        {   
            
            $this_v = (!empty($itm_data[$k]['consumption'])?$itm_data[$k]['consumption']:'0');
            echo '<td align="center">'.number_format($this_v).'</td>
                ';
            @$year_total+=$itm_data[$k]['consumption'];
        }
        
        $this_amc_1 = $year_total/$yearly_non_zero_months[$year];
        echo '
                <td align="right" class="info">'.number_format($itm_data['total_health_facilities']).'</td>
                <td align="right" class="info">'.number_format($itm_data['sum_reporting_rate']/$yearly_non_zero_months[$year],1).' %</td>
                <td align="right" class="info">'.number_format($this_amc_1).'</td>';
            echo '
                </tr>';
            $total_amc_yearly+=($this_amc_1);
    }
    $final_amc = (($total_amc_yearly/$count_of_years));
    echo '
                <tr>
                    <td class="bold" colspan="15">Overall Average Consumption</td>
                    <td class="bold" >'.number_format($final_amc).'</td>
                        <input type="hidden" name="amc" value="'.$final_amc.'">
                        <input type="hidden" name="prod_id" value="'.$_REQUEST['prod_id'].'">
                        <input type="hidden" name="master_id" value="'.$_REQUEST['master_id'].'">
                        <input type="hidden" name="base_years" value="'.$years.'">
                </tr>';
    ?>

</table>
<button type="submit" class="btn green-jungle pull-right"> Save AMC for <?=$prod_name?></button>
</form>
