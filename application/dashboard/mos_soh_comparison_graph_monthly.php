<?php
//echo '<pre>';print_r($_REQUEST);exit;    
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$products  = $_REQUEST['product'];
$province  = $_REQUEST['province'];
$from_date  = $_REQUEST['from_date'];
$to_date    = $_REQUEST['to_date'];
$from_date  = date('Y-m-01',strtotime($from_date));
$to_date    = date('Y-m-01',strtotime($to_date));
@$stakeholder  = $_REQUEST['stakeholder'];
@$district  = $_REQUEST['district'];
 

$months = $months2 = array();


$caption = "Trends";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'a';
    for($i=01;$i<=12;$i++){
        $months[sprintf("%02d", $i)]=sprintf("%02d", $i);
    }
    
    
    $qry = "SELECT
mosscale_tab.row_id,
mosscale_tab.itmrec_id,
mosscale_tab.shortterm,
mosscale_tab.longterm,
mosscale_tab.sclstart,
mosscale_tab.sclsend,
mosscale_tab.extra,
mosscale_tab.colorcode,
mosscale_tab.stkid,
mosscale_tab.lvl_id
FROM
mosscale_tab
INNER JOIN itminfo_tab ON mosscale_tab.itmrec_id = itminfo_tab.itmrec_id
WHERE
mosscale_tab.stkid = $stakeholder AND
itminfo_tab.itm_id = $products AND
mosscale_tab.lvl_id = 4
ORDER BY
mosscale_tab.sclstart ASC
            ";  
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $mos_scale=array();
    while($row = mysql_fetch_assoc($qryRes))
    {
        $mos_scale[]          = $row;
        
        if($row['shortterm'] == 'US')
            $min = $row['sclstart'];
        
        if($row['shortterm'] == 'SAT')
            $max = $row['sclsend'];
    }    
?>
<div class="portlet">    
    <div class="portlet-body">
        
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        
         $qry = "SELECT
                            summary_district.item_id,
                            summary_district.stakeholder_id,
                            summary_district.reporting_date,
                            summary_district.consumption,
                            itminfo_tab.itm_name,
                            summary_district.avg_consumption,
                            (summary_district.soh_district_lvl - summary_district.soh_district_store) as soh,
                            
                            summary_district.district_id as p_id,
                            tbl_locations.LocName as p_name
                        FROM
                            summary_district
                            INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
                            INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
                        WHERE
                            itminfo_tab.itm_id = $products AND
                            summary_district.stakeholder_id = $stakeholder  AND
                            summary_district.province_id = '".$province."' AND
                            summary_district.district_id = '".$district."' AND
                            summary_district.reporting_date >= '".$from_date."' AND
                            summary_district.reporting_date <= '".$to_date."'
                        ORDER BY
                            summary_district.reporting_date ASC

            ";  
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $prod_name='';
    $labels=array();
    while($row = mysql_fetch_assoc($qryRes))
    {
        $labels[$row['reporting_date']] = $row['reporting_date'];
        $prod_name=$row['itm_name'];
        $disp_arr[$row['reporting_date']]['consumption']          = $row['consumption'];
        $disp_arr[$row['reporting_date']]['avg_consumption']      = $row['avg_consumption'];
        $disp_arr[$row['reporting_date']]['soh']     = $row['soh'];
    }    
//    echo '<pre>';print_r($disp_arr);exit; 
//    echo '<pre>';print_r($months);exit;    
    
    $xmlstore = '<chart caption=" " allowSelection="0" PYAxisName="SOH" SYAxisName="Months (MOS)"   axisOnLeft="0"  subcaption="" captionfontsize="14" placeValuesInside="0" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Months" yaxisname="Consumption" showvalues="1" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
    $xmlstore .= ' <categories>';
    foreach($labels as $k => $label)
    {
        $xmlstore .= ' <category label="'.date('M-Y',strtotime($label)).'" />';
    }
    //$xmlstore .= '  <vline lineposition="0" color="#6baa01" labelhalign="center" labelposition="0" label="National holiday" dashed="1" />';
    $xmlstore .= ' </categories>';

//    $xmlstore .= "<axis title='Power' titlePos='left' tickWidth='10' divlineisdashed='1' numberSuffix='W'> ";
    $xmlstore .= ' <dataset renderas="line"  parentYAxis="P"    seriesname="SOH">';
    foreach($labels as $k => $label)
    {   
        $val=(!empty($disp_arr[$k]['soh'])?$disp_arr[$k]['soh']:'0');
        $xmlstore .= '    <set  value="'.$val.'"  />';
    }
    $xmlstore .= '  </dataset>';
//    $xmlstore .= '  </axis>';
    
//    $xmlstore .= '  <axis title="abc">';
    $xmlstore .= " <dataset renderas='line' parentYAxis='S'  seriesname='MOS' showValues='1' >";
    
   foreach($labels as $k => $label)
    {   

        $val=(!empty($disp_arr[$k]['avg_consumption'] && !empty($disp_arr[$k]['soh']))? round($disp_arr[$k]['soh']/$disp_arr[$k]['avg_consumption'],1):'0');
        $xmlstore .= '    <set  value="'.$val.'"  />';
    }
   $xmlstore .= "</dataset>";
//    $xmlstore .= '  </axis>';
    
    $xmlstore .= '  <trendlines>

		<line startvalue="'.$min.'"    parentYAxis="S" displayValue="Min" color="DE4545" valueOnRight="1"/>
		<line startvalue="'.$max.'"    parentYAxis="S" displayValue="Max" color="DE4545" valueOnRight="1"/>
		
    </trendlines>';
    //<line startvalue="1"  endValue="2" parentYAxis="S" displayValue="Critical" color="008ee4" isTrendZone="1" showOnTop="0" alpha="35" valueOnRight="1"/>
    //<line startvalue="3"  endValue="5"  parentYAxis="S" displayValue="Warning" color="33bdda" isTrendZone="1" showOnTop="0" alpha="35" valueOnRight="1"/>

    
    
    
    $xmlstore .= ' </chart>';
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSCombiDY2D.swf", "", $xmlstore, $chart_id, '100%', 300, false, false);
    ?>
    
	</div>
</div>

<div class="portlet">    
    <div class="portlet-body">
        <table class="table table-bordered table-condensed  table-striped">
            <tr>
                <th>Sr No</th>
                <th>Location</th>
                <th style="text-align: center;">SOH</th>
                <th style="text-align: center;">AMC</th>
                <th style="text-align: center;">MOS</th>
            </tr>
            <?php
            $c=1;
            foreach($labels as $k => $label){
                echo '<tr>
                        <td>'.$c++.'</td>
                        <td>'.date('M-Y',strtotime($label)).'</td>
                        <td align="right">'.number_format((!empty($disp_arr[$k]['soh'])?$disp_arr[$k]['soh']:'0')).'</td>
                        <td align="right">'.number_format((!empty($disp_arr[$k]['avg_consumption'])?$disp_arr[$k]['avg_consumption']:'0')).'</td>
                        <td align="right">'.number_format((!empty($disp_arr[$k]['avg_consumption'] && !empty($disp_arr[$k]['soh']))?($disp_arr[$k]['soh']/$disp_arr[$k]['avg_consumption']):'0'),2).'</td>
                    </tr>';
            }
            
            ?>
            
        </table>
    </div>
</div>
