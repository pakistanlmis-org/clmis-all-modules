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
$from_date  = date('Y-m-01',strtotime($from_date));
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
?>
<div class="portlet">    
    <div class="portlet-body">
        
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        
        if(!empty($district)){
            $qry = "SELECT
                        tbl_hf_data.warehouse_id,
                        tbl_hf_data.item_id,
                        tbl_hf_data.closing_balance as soh,
                        tbl_hf_data.avg_consumption,
                        tbl_hf_data.issue_balance as consumption,
                        tbl_hf_data.reporting_date,
                        tbl_warehouse.wh_id as p_id,
                        tbl_warehouse.wh_name as p_name,
itminfo_tab.itm_name
FROM
tbl_hf_data
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                    WHERE
                        tbl_hf_data.reporting_date = '".$from_date."' AND
                        tbl_warehouse.dist_id = $district AND
                        tbl_warehouse.stkid = $stakeholder AND
                        tbl_hf_data.item_id = $products
                    ORDER BY
                        tbl_warehouse.wh_name ASC
            ";
        }
        else
        {
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
                            reporting_date = '".$from_date."'
                        ORDER BY
                            tbl_locations.LocName ASC

            ";  
        }
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $prod_name='';
    $labels=array();
    while($row = mysql_fetch_assoc($qryRes))
    {
        $labels[$row['p_id']] = $row['p_name'];
        $prod_name=$row['itm_name'];
        $disp_arr[$row['p_id']]['consumption']          = $row['consumption'];
        $disp_arr[$row['p_id']]['avg_consumption']      = $row['avg_consumption'];
        $disp_arr[$row['p_id']]['soh']     = $row['soh'];
    }    
//    echo '<pre>';print_r($disp_arr);exit; 
//    echo '<pre>';print_r($months);exit;    
    
    $xmlstore = '<chart caption=" " allowSelection="0" PYAxisName="SOH" SYAxisName="Months (MOS)"   axisOnLeft="0"  subcaption="" captionfontsize="14" placeValuesInside="0" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Months" yaxisname="Consumption" showvalues="1" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
    $xmlstore .= ' <categories>';
    foreach($labels as $k => $label)
    {
        $xmlstore .= ' <category label="'.$label.'" />';
    }
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
                        <td>'.$label.'</td>
                        <td align="right">'.number_format((!empty($disp_arr[$k]['soh'])?$disp_arr[$k]['soh']:'0')).'</td>
                        <td align="right">'.number_format((!empty($disp_arr[$k]['avg_consumption'])?$disp_arr[$k]['avg_consumption']:'0')).'</td>
                        <td align="right">'.number_format((!empty($disp_arr[$k]['avg_consumption'] && !empty($disp_arr[$k]['soh']))?($disp_arr[$k]['soh']/$disp_arr[$k]['avg_consumption']):'0'),2).'</td>
                    </tr>';
            }
            
            ?>
            
        </table>
    </div>
</div>
