<?php
//echo '<pre>';print_r($_REQUEST);exit;
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$debug = false;

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-01", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$funding_source = (!empty($_REQUEST['stakeholder'])?$_REQUEST['stakeholder']:'');

$products = $_REQUEST['product'];
$yr_mon = date('Y-m',strtotime($from_date));  
//Chart caption
$caption = '* Average Stock Availability Rate at SDP Level for '.date('M-Y',strtotime($from_date));
//Chart heading sub Caption
$where_funding='';
$where_locations='';
$where_summary='';
$where_wh='';
$where_hf='';
if(!empty($province))
{
    $where_funding=" funding_stk_prov.province_id = $province AND ";
    $where_locations=" AND Province.PkLocID = $province";
   $where_summary= " WHERE summary_district.province_id = ".$province ." ";
    $where_wh= " AND tbl_warehouse.prov_id = ".$province;
    $where_hf=" AND tbl_hf_type_rank.province_id = ".$province;
}
if(!empty($_REQUEST['stakeholder'])){
    $qry_c= "SELECT
                        GROUP_CONCAT(funding_stk_prov.stakeholder_id) as stk
                    FROM
                        funding_stk_prov
                    INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                    WHERE
                        $where_funding funding_stk_prov.funding_source_id = $funding_source ";

//    echo $qry_c;exit;
    $res1 = mysql_query($qry_c);
    $row = mysql_fetch_assoc($res1);
    $stakeholders = $row['stk'];
}
 else {
    $stakeholders= "1,2,7,73";    
}

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b2_1';
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
            AND stakeholder.stkid IN (".$stakeholders.") $where_wh
    ";
    //echo $qry_1;exit;
    $res_1 = mysql_query($qry_1);
    $stk_arr = array();
    while($row_1 = mysql_fetch_array($res_1))
    {
        //$stk_arr[$row_1['stkid']]=$row_1['stkname'];
    }

//include PUBLIC_PATH . "html/top_im.php";
 
  $output = '<div class="widget widget-tabs">    
        <div class="widget-body">
        <a href="javascript:exportChart(\''.$chart_id.'\', \''.$downloadFileName.'\')" style="float:right;"><img class="export_excel" src="'.PUBLIC_URL.'images/excel-16.png" alt="Export" /></a>
	';
        $reporting_wh_arr = array();
        
       

        //get total number of facilities
        $q_reporting  = "SELECT
                            tbl_warehouse.stkid,
                    count(tbl_warehouse.wh_id) as total

                    FROM
                            tbl_warehouse 
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
                    AND tbl_warehouse.reporting_start_month <= '".$from_date."'
                   $where_wh
                    AND stakeholder.lvl = 7
                    GROUP BY
                            tbl_warehouse.stkid";
        //echo $q_reporting;exit;
        $res_reporting = mysql_query($q_reporting);
        $reporting_wh_arr  =  array();
        while($row=mysql_fetch_assoc($res_reporting))
        {
            $reporting_wh_arr[$row['stkid']]['total']=$row['total'];
        }  

        //query for getting reported facilities
        $q_reporting  = "SELECT
                                   tbl_warehouse.stkid,
                                    COUNT(
                                            DISTINCT tbl_warehouse.wh_id
                                    ) AS reportedWH
                            FROM
                                    tbl_warehouse

                            INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
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
                            $where_wh

                            AND tbl_hf_data.reporting_date = '".$from_date."'
                            GROUP BY
                                    tbl_warehouse.stkid";
        //echo $q_reporting;exit;
        $res_reporting = mysql_query($q_reporting);
        while($row=mysql_fetch_assoc($res_reporting))
        {
            $reporting_wh_arr[$row['stkid']]['reported']=$row['reportedWH'];
        }
           

        //Query for shipment main dashboard
        $qry = "SELECT
                stakeholder.stkid,
                stakeholder.stkname,
                tbl_hf_data.item_id ,
                itminfo_tab.itm_name,
                count(distinct tbl_hf_data.warehouse_id) as non_so,
                count(distinct tbl_hf_data.item_id) as item_count
                    FROM
                            tbl_warehouse
                    INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkid
                    INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
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
                     $where_hf
                        $where_wh
                     
                    AND tbl_hf_data.reporting_date = '".$from_date."'

                    AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                    AND ROUND( (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 2 ) > 0
                    AND itminfo_tab.itm_category = 1
                    ";
         if(empty($products) || $products == 'null') 
                $qry .= " AND itminfo_tab.itm_id NOT IN(4,6,10,33) ";
         else
                $qry .= " AND itminfo_tab.itm_id IN(".$products.") ";
        
         if(!empty($_REQUEST['stakeholder'])) $qry .= " AND stakeholder.stkid in (".$stakeholders.") ";
        $qry .= "GROUP BY
                    stakeholder.stkid,itminfo_tab.itm_id
                ORDER BY 
                    stakeholder.stkid,itminfo_tab.itm_id
        ";
//    echo $qry;
//    exit;
    $qryRes = mysql_query($qry);
    $c=1;
    $detail_display='Number of SDPs having stock available in Reported SDPs shown stakeholder / product wise.<br/>';
    $dist_arr  = $so_arr = $item_count_arr = array();
   $old_stk = '';
    while($row = mysql_fetch_assoc($qryRes))
    {
        $stk_arr[$row['stkid']]         = $row['stkname'];
        
        @$so_arr[$row['stkid']]          += $row['non_so'];
        @$item_count_arr[$row['stkid']]  += $row['item_count'];
        
        
        if($row['stkid']!=$old_stk) 
            $detail_display .= '<br/>';
        
        $detail_display .= '<span>'.$row['stkname'].'-'.$row['itm_name'].':</span><span class="pull-right">'.$row['non_so'].' / '.$reporting_wh_arr[$row['stkid']]['reported'].' </span><br/>';
        $old_stk = $row['stkid'];
    }
    
//    foreach($stk_arr as $stk_id => $stk_name)
//    {
//        $so_arr[$row['stkid']]          = $row['non_so'];
//        $item_count_arr[$row['stkid']]  = $row['item_count'];
//    }    
//    echo '<pre>$so_arr';print_r($so_arr);
//    echo '<pre>$stk_arr';print_r($stk_arr);
//    echo '<pre>$reporting_wh_arr';print_r($reporting_wh_arr);
//    exit;    

    
    $province_param = 'all';
    $province_param=(!empty($province)?$province:'\'all\'');
    $province_name_param = 'All';
    $province_name_param=(!empty($province)?$prov_name:'All');
        
    //xml for chart
    $xmlstore = '<chart caption="'.$caption.'" yaxismaxvalue="100" showToolTip="1"    subcaption="" xaxisname="Stakeholders" yaxisname="Percentage" exportEnabled="1"  numberprefix=""  palettecolors="#26C281,#FF0000,#57c5ed" theme="fint">';
   
    $xmlstore .= ' <categories>';
    foreach($stk_arr as $id => $name)
    {
         $xmlstore .= '     <category  toolText="d"  label="'.$name.'"/>';  
    }
    $xmlstore .= ' </categories>';
    
    $perc_count = $perc_total = 0 ;
    $xmlstore .= ' <dataset seriesname="Stock Availability Rate at SDPs" showvalues="1">';
    foreach($stk_arr as $stk_id => $stk_name)
    {
        $val=(isset($so_arr[$stk_id])?$so_arr[$stk_id]:'0');
        if(!empty($reporting_wh_arr[$stk_id]['reported']) && $reporting_wh_arr[$stk_id]['reported'] > 0)
            $perc = ((!empty($val)?$val:0)* 100)/($reporting_wh_arr[$stk_id]['reported'] * $item_count_arr[$stk_id]);
        else
            $perc = 0;
        
        $perc_count ++;
        $perc_total += $perc;
        
        
        $rep = (isset($reporting_wh_arr[$stk_id]['reported'])?$reporting_wh_arr[$stk_id]['reported']:'0');
        $tot = (isset($reporting_wh_arr[$stk_id]['total'])?$reporting_wh_arr[$stk_id]['total']:'0');
        if(isset($tot) && $tot>0) $r_rate = $rep * 100 / $tot;
        else $r_rate = 0;
        
        $xmlstore .= '     <set label="'.$stk_name.'" value="'.(number_format($perc  , 2)).'" toolText="'.(number_format($perc  , 2)).', Click for drill down"  link="JavaScript:showDrillDown_b2('.$province_param.',\''.$province_name_param.'\',\''.$from_date.'\','.$stk_id.',\''.$stk_arr[$stk_id].'\',\''.$products.'\',\''.$r_rate.'\');" />';
    }
    
    //$_SESSION['dashboard_vals']['general_summary_sdp']=number_format(($perc_total/$perc_count),2);
    $xmlstore .= '  </dataset>';

    
    $xmlstore .= ' <dataset  seriesname="Reporting Rate (SDP Level)"  renderas="line" showvalues="1">';
    foreach($stk_arr as $id => $name)
    {
        $rep = (isset($reporting_wh_arr[$id]['reported'])?$reporting_wh_arr[$id]['reported']:'0');
        $tot = (isset($reporting_wh_arr[$id]['total'])?$reporting_wh_arr[$id]['total']:'0');
        if(isset($tot) && $tot>0) $val = $rep * 100 / $tot;
        else $val = 0;
        
        $xmlstore .= '     <set label="'.$name.'"  value="'.(number_format(!empty($val)?$val:0,1)).'"  link="JavaScript:showDrillDown_b2('.$province_param.',\''.$province_name_param.'\',\''.$from_date.'\','.$id.',\''.$stk_arr[$id].'\',\''.$products.'\',\''.$r_rate.'\');"   />';
    }
    $xmlstore .= '  </dataset>';
    
    
    if($debug){
    //DEBUG    
    
        $xmlstore .= ' <dataset  seriesname="Reported SDP"   showvalues="1">';
        foreach($stk_arr as $id => $name)
        {
            $rep = (isset($reporting_wh_arr[$id]['reported'])?$reporting_wh_arr[$id]['reported']:'0');
            $xmlstore .= '     <set label="'.$name.'" value="'.((!empty($rep)?$rep:0)).'"   />';
        }
        $xmlstore .= '  </dataset>';
        $xmlstore .= ' <dataset  seriesname="total SDP"   showvalues="1">';
        foreach($stk_arr as $id => $name)
        {
            $tot = (isset($reporting_wh_arr[$id]['total'])?$reporting_wh_arr[$id]['total']:'0');
            $xmlstore .= '     <set label="'.$name.'" value="'.((!empty($tot)?$tot:0)).'"   />';
        }
        $xmlstore .= '  </dataset>';
        $xmlstore .= ' <dataset  seriesname="Available SDPs"   showvalues="1">';
        foreach($stk_arr as $stk_id => $name)
        {
            $val=(isset($so_arr[$stk_id])?$so_arr[$stk_id]:'0');
            $xmlstore .= '     <set label="'.$name.'" value="'.((!empty($val)?$val:0)).'"   />';
        }
        $xmlstore .= '  </dataset>';
        $xmlstore .= ' <dataset  seriesname="items"   showvalues="1">';
        foreach($stk_arr as $stk_id => $name)
        {
            $val=(isset($item_count_arr[$stk_id])?$item_count_arr[$stk_id]:'0');
            $xmlstore .= '     <set label="'.$name.'" value="'.((!empty($val)?$val:0)).'"   />';
        }
        $xmlstore .= '  </dataset>';
        
        
    //Debug END
    }
    
    
    $xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    if($debug)
        $output .=  renderChart(PUBLIC_URL."FusionCharts/Charts/ScrollCombi2D.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    else
        $output .=  renderChart(PUBLIC_URL."FusionCharts/Charts/StackedColumn2DLine.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
   
    $output .= '</div>
    </div>';
    $json['display']=$output;
    if(!empty($perc_count) &&  $perc_count > 0)
        $json['general_summary_sdp']=number_format(($perc_total/$perc_count));
    else
        $json['general_summary_sdp']=0;
    
    
    $json['details']=$detail_display;
    echo json_encode($json);
    ?>