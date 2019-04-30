<?php

//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption = '';
$debug = FALSE;
ini_set('max_execution_time', 0);
$prov_name = $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-01", strtotime($_REQUEST['from_date']));
$yr_mon = date('Y-m', strtotime($from_date));

$products = $_REQUEST['product'];

$where_funding = '';
$where_locations = '';
$where_summary = '';
$where_wh = '';
if (!empty($province)) {
    $where_funding = " funding_stk_prov.province_id = $province AND ";
    $where_locations = " AND Province.PkLocID = $province";
    $where_summary = "WHERE
                            summary_district.province_id = " . $province . " ";
    $where_wh = " AND tbl_warehouse.prov_id = " . $province;
}
$funding_source = (!empty($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : '');
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
//Chart caption
$caption = "* Average Stock Availability Rate by Stakeholders at District Level of $prov_name";
//Chart heading sub Caption
//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b1';

if (!empty($_REQUEST['stakeholder'])) {
    $qry_c = "SELECT
                        GROUP_CONCAT(funding_stk_prov.stakeholder_id) as stk
                    FROM
                        funding_stk_prov
                    INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                    WHERE
                        $where_funding funding_stk_prov.funding_source_id = $funding_source ";

    //echo $qry_c;exit;
    $res1 = mysql_query($qry_c);
    $row = mysql_fetch_assoc($res1);
    $stakeholders = $row['stk'];
} else {
    $stakeholders = "1,2,7,73";
}
?>
<?php

$output = '<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart(\'' . $chart_id . '\', \'' . $downloadFileName . '\')" style="float:right;"><img class="export_excel" src="' . PUBLIC_URL . 'images/excel-16.png" alt="Export" /></a>
	';

//making list of items , to display list incase no data entry is found
$qry_1 = "  SELECT
                        stakeholder.stkid,
                        stakeholder.stkname,
                        stakeholder.lvl,
                        stakeholder.MainStakeholder
                    FROM
                        stakeholder
                    INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkid
                    INNER JOIN wh_user ON wh_user.wh_id = tbl_warehouse.wh_id
                    WHERE
                        stakeholder.stk_type_id = 0 AND
                        stakeholder.lvl = 1
                       $where_wh AND stakeholder.stkid IN (" . $stakeholders . ") 
                        
                ";
//                echo $qry_1;exit;
$res_1 = mysql_query($qry_1);
$stk_arr = array();
while ($row_1 = mysql_fetch_array($res_1)) {
    $stk_arr[$row_1['stkid']] = $row_1['stkname'];
}
//query for total districts of the stakeholder
$qry_dist = " SELECT
                        	stakeholder.stkid,
                        count( DISTINCT summary_district.district_id ) AS reported_districts
                      FROM
                                summary_district
                        INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
                        INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
                        INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
                        INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
                      WHERE
                        summary_district.reporting_date = '" . $from_date . "' $where_locations
                        AND itminfo_tab.itm_category = 1 
                        
                        GROUP BY
                                stakeholder.stkid

";
$res_dist = mysql_query($qry_dist);
$reported_districts = array();
while ($row = mysql_fetch_assoc($res_dist)) {
    $reported_districts[$row['stkid']] = $row['reported_districts'];
}


//Query for shipment main dashboard
$qry = "
            
                SELECT
                  stakeholder.stkid,
                  stakeholder.stkname,
                  summary_district.item_id,
                  count(distinct summary_district.district_id) as stock_available_districts,
                  itminfo_tab.itm_name
                FROM
                          summary_district
                  INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
                  INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
                  INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
                  INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
                WHERE
                  summary_district.reporting_date = '" . $from_date . "' $where_locations
                 AND (summary_district.soh_district_store/avg_consumption) > 0
                  AND itminfo_tab.itm_category = 1
                  AND stakeholder.stk_type_id = 0
                  AND stakeholder.lvl=1";
if (empty($products) || $products == 'null')
    $qry .= " AND itminfo_tab.itm_id NOT IN(4,6,10,33) ";
else
    $qry .= " AND itminfo_tab.itm_id IN(" . $products . ") ";

if (!empty($_REQUEST['stakeholder']))
    $qry .= " AND stakeholder.stkid in (" . $stakeholders . ") ";
$qry .= " GROUP BY
                stakeholder.stkid,
                summary_district.item_id
            
 ";
//    echo $qry;exit;

$qryRes = mysql_query($qry);
$c = 1;
$detail_display = 'Number of district stores having stock available in Reported District Stores shown stakeholder / product wise.<br/>';
$r_arr = $so_arr = $so_perc1 = $so_number = $temp_itm_arr = $comments_arr = array();

//    $arr_item[1]['itm_count'] = 6;
//    $arr_item[2]['itm_count'] = 3;
//    $arr_item[73]['itm_count'] = 6;
//    $arr_item[7]['itm_count'] = 6;

$old_stk = '';
while ($row = mysql_fetch_assoc($qryRes)) {
    $stk_arr[$row['stkid']] = $row['stkname'];
    @$so_arr[$row['stkid']] += $row['stock_available_districts'];

    @$arr_item[$row['stkid']]['itm_count'] += 1;

    if ($row['stkid'] != $old_stk)
        $detail_display .= '<br/>';

    $detail_display .= '<span>' . $row['stkname'] . '-' . $row['itm_name'] . ':</span><span class="pull-right">' . $row['stock_available_districts'] . ' / ' . $reported_districts[$row['stkid']] . '</span> <br/>';
    $old_stk = $row['stkid'];
}

foreach ($stk_arr as $stk_id => $stk_name) {

    $val = @$so_arr[$stk_id];
    if (@$reported_districts[$stk_id] == 0 || empty(@$reported_districts[$stk_id])) {
        $perc1 = 0;
    } else {
        $perc1 = ((!empty($val) ? $val : 0) * 100) / $reported_districts[$stk_id];
    }
    //$itm_count = (!empty($arr_item[$row['stkid']]['itm_count'])?$arr_item[$row['stkid']]['itm_count']:6);
    $itm_count = (!empty($arr_item[$stk_id]['itm_count']) ? $arr_item[$stk_id]['itm_count'] : 6);
    $perc = $perc1 / $itm_count;

    //echo '</br>perc1'.$perc1.',val:'.$val.',total:'.$total_districts.',perc='.$perc.' , itm c:'.$row['itm_count'];

    $r_arr[$stk_id] = $perc;
    //$comments_arr[$row['stkid']] = $val.' District Stores Per Item have stock available in a total of '.$total_districts.' Districts  '.$itm_count.' items.';
    $so_perc1[$stk_id] = $perc1;
    $so_number[$stk_id] = $val;
    $temp_itm_arr[$stk_id] = $itm_count;
}
//    echo '<pre>$arr_item';print_r($arr_item);
//    echo '<pre>$stk_arr';print_r($stk_arr);
//    echo '<pre>$so_arr';print_r($so_arr);
//    echo '<pre>$arr_item';print_r($arr_item);
//    echo '<pre>$r_arr';print_r($r_arr);
//    echo '<pre>$so_perc1';print_r($so_perc1);
//    echo '<pre>$so_number';print_r($so_number);
//    echo '<pre>$temp_itm_arr';print_r($temp_itm_arr);
//    exit;       

if (empty($_SESSION['reporting_wh_arr'][$province][$yr_mon])) {
    //query for getting reported facilities
    $q_reporting = "select stk_id,stkname,count(wh_id) as total, sum(is_reported) as reported
                    from 
                    ( SELECT DISTINCT
                                tbl_warehouse.wh_id,
                                tbl_warehouse.stkid,
                                stk2.stkid AS stk_id,
                                stk2.stkname,
                                (
                                                SELECT
                                                        count(DISTINCT tbl_wh_data.wh_id)
                                                FROM
                                                        tbl_wh_data
                                                WHERE
                                                        tbl_wh_data.wh_id = tbl_warehouse.wh_id
                                                AND tbl_wh_data.RptDate = '$from_date'

                                        ) AS is_reported,
                                tbl_warehouse.wh_name
                                FROM
                            	tbl_warehouse
                                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                INNER JOIN stakeholder AS stk2 ON tbl_warehouse.stkid = stk2.stkid
                                WHERE
                                        tbl_warehouse.wh_id NOT IN (
                                                SELECT
                                                        warehouse_status_history.warehouse_id
                                                FROM
                                                        warehouse_status_history
                                                INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                                                WHERE
                                                        warehouse_status_history.reporting_month = '$from_date'
                                                AND warehouse_status_history.`status` = 0
                                        )
                                AND stakeholder.lvl = 3 $where_wh) A
                    group by
                        stkid";
//            echo $q_reporting;exit;
    $res_reporting = mysql_query($q_reporting);
    $reporting_wh_arr = array();
    while ($row = mysql_fetch_assoc($res_reporting)) {
        $reporting_wh_arr[$row['stk_id']]['total'] = $row['total'];
        $reporting_wh_arr[$row['stk_id']]['reported'] = $row['reported'];
    }

    $_SESSION['reporting_wh_arr'][$province][$yr_mon] = $reporting_wh_arr;
}
$reporting_wh_arr = $_SESSION['reporting_wh_arr'][$province][$yr_mon];
//echo '<pre>';print_r($reporting_wh_arr);exit;    
//xml for chart
$xmlstore = '<chart caption="' . $caption . '" yaxismaxvalue="100" subcaption="Stakeholder Wise" xaxisname="Stakeholders" yaxisname="Percentage" numberprefix="" exportEnabled="1" palettecolors="#26C281,#FF0000,#53A5E0" theme="fint">';

$xmlstore .= ' <categories>';
foreach ($stk_arr as $id => $name) {
    $val = (isset($r_arr[$id]) ? $r_arr[$id] : '0');
    $xmlstore .= '     <category label="' . $name . '"/>';
}
$xmlstore .= ' </categories>';

$province_param = 'all';
$province_param=(!empty($province)?$province:'\'all\'');
$province_name_param = 'All';
$province_name_param=(!empty($province)?$prov_name:'All');
        
        
$perc_count = $perc_total = 0;
$xmlstore .= ' <dataset  seriesname="Stock Availability Rate"  showvalues="1">';
foreach ($stk_arr as $id => $name) {
    $val = (isset($r_arr[$id]) ? $r_arr[$id] : '0');
    //$xmlstore .= '     <set value="'.(number_format(!empty($val)?$val:0,1)).'" link="JavaScript:showDrillDown_b1('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$id.',\''.$name.'\');" />';
    $xmlstore .= '     <set label="' . $name . '" value="' . (number_format(!empty($val) ? $val : 0, 1)) . '" link="JavaScript:showDrillDown_b1(' . $province_param . ',\'' . $province_name_param . '\',\'' . $from_date . '\',' . $id . ',\'' . $name . '\',\'' . $products . '\');"  tooltext="' . $name . ':' . (number_format(!empty($val) ? $val : 0, 1)) . ' {br} Click bar to drill down"  />';

    $perc_count ++;
    $perc_total += $val;
}
$xmlstore .= '  </dataset>';

if ($debug) {
    //DEBUG    
    $xmlstore .= ' <dataset  seriesname="Perc1"  showvalues="1">';
    foreach ($stk_arr as $id => $name) {
        $val = (isset($so_perc1[$id]) ? $so_perc1[$id] : '0');
        $xmlstore .= '     <set label="' . $name . '" value="' . (number_format(!empty($val) ? $val : 0, 1)) . '" link="JavaScript:showDrillDown_b1(' . $province_param . ',\'' . $province_name_param . '\',\'' . $from_date . '\',' . $id . ',\'' . $name . '\');"  tooltext="' . $name . ':' . (number_format(!empty($val) ? $val : 0, 1)) . ' {br} Click bar to drill down"  />';
        $perc_count ++;
        $perc_total += $val;
    }
    $xmlstore .= '  </dataset>';
    $xmlstore .= ' <dataset  seriesname="Stock Available Dists"  showvalues="1">';
    foreach ($stk_arr as $id => $name) {
        $val = (isset($so_number[$id]) ? $so_number[$id] : '0');
        $xmlstore .= '     <set label="' . $name . '" value="' . (number_format(!empty($val) ? $val : 0, 1)) . '" link="JavaScript:showDrillDown_b1(' . $province_param . ',\'' . $province_name_param . '\',\'' . $from_date . '\',' . $id . ',\'' . $name . '\');"  tooltext="' . $name . ':' . (number_format(!empty($val) ? $val : 0, 1)) . ' {br} Click bar to drill down"  />';
        $perc_count ++;
        $perc_total += $val;
    }
    $xmlstore .= '  </dataset>';
    $xmlstore .= ' <dataset  seriesname="Total Dists"  showvalues="1">';
    foreach ($stk_arr as $id => $name) {
        $val = (isset($reported_districts[$id]) ? $reported_districts[$id] : '0');
        $xmlstore .= '     <set label="' . $name . '" value="' . (number_format(!empty($val) ? $val : 0, 1)) . '" link="JavaScript:showDrillDown_b1(' . $province_param . ',\'' . $province_name_param . '\',\'' . $from_date . '\',' . $id . ',\'' . $name . '\');"  tooltext="' . $name . ':' . (number_format(!empty($val) ? $val : 0, 1)) . ' {br} Click bar to drill down"  />';
        $perc_count ++;
        $perc_total += $val;
    }
    $xmlstore .= '  </dataset>';
    $xmlstore .= ' <dataset  seriesname="itm count"  showvalues="1">';
    foreach ($stk_arr as $id => $name) {
        $val = (isset($temp_itm_arr[$id]) ? $temp_itm_arr[$id] : '0');
        $xmlstore .= '     <set label="' . $name . '" value="' . (number_format(!empty($val) ? $val : 0, 1)) . '" link="JavaScript:showDrillDown_b1(' . $province_param . ',\'' . $province_name_param . '\',\'' . $from_date . '\',' . $id . ',\'' . $name . '\');"  tooltext="' . $name . ':' . (number_format(!empty($val) ? $val : 0, 1)) . ' {br} Click bar to drill down"  />';
        $perc_count ++;
        $perc_total += $val;
    }
    $xmlstore .= '  </dataset>';

    // DEBUG ends ...
}

$xmlstore .= ' <dataset  seriesname="Reporting Rate (District Level)"  renderas="line" showvalues="1">';
foreach ($stk_arr as $id => $name) {
    $rep = (isset($reporting_wh_arr[$id]['reported']) ? $reporting_wh_arr[$id]['reported'] : '0');
    $tot = (isset($reporting_wh_arr[$id]['total']) ? $reporting_wh_arr[$id]['total'] : '0');
    if (isset($tot) && $tot > 0)
        $val = $rep * 100 / $tot;
    else
        $val = 0;

    $xmlstore .= '     <set label="' . $name . '" value="' . (number_format(!empty($val) ? $val : 0, 1)) . '" link="JavaScript:showDrillDown_b1(' . $province_param . ',\'' . $province_name_param . '\',\'' . $from_date . '\',' . $id . ',\'' . $name . '\',\'' . $products . '\');"   />';
}
$xmlstore .= '  </dataset>';


//$_SESSION['dashboard_vals']['general_summary_district']=number_format(($perc_total/$perc_count),2);
$xmlstore .= ' </chart>';
//echo $xmlstore;exit;
//end chart
//Render chart
FC_SetRenderer('javascript');
$output .= renderChart(PUBLIC_URL . "FusionCharts/Charts/StackedColumn2DLine.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
//$output .=  renderChart(PUBLIC_URL."FusionCharts/Charts/MSColumnLine3D.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
//$output .=  renderChart(PUBLIC_URL."FusionCharts/Charts/ScrollCombi2D.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);



$output .= '</div>
</div>';
$json['display'] = $output;
$json['general_summary_district'] = number_format(($perc_total / $perc_count));
$json['details'] = $detail_display;
echo json_encode($json);
?>
	