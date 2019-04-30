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
$chart_id = 'a6';

$from_date='2017-01-01';
$to_date='2017-12-01';
$f_date= $from_date;
$t_date= $to_date;
$where_clause ="";

//if(!empty($province))   $where_clause .= " AND tbl_warehouse.prov_id in (".$province.")  ";
//if(!empty($stk))        $where_clause .= " AND tbl_warehouse.stkid in (".$stk.")  ";
//if(!empty($province))   $where_clause .= " AND tbl_warehouse.prov_id  = 1 ";
//if(!empty($stk))        $where_clause .= " AND tbl_warehouse.stkid = 1  ";

//$where_clause .= " AND tbl_warehouse.prov_id  = 1 ";
//$where_clause .= " AND tbl_warehouse.stkid = 1  ";


$time1  = strtotime($from_date); 
$time2  = strtotime($to_date); 
$my     = date('mY', $time2); 
$months_list = array(date('Y-m-01', $time1)); 
if($f_date != $t_date){
    while($time1 < $time2) { 
       $time1 = strtotime(date('Y-m-d', $time1).' +1 month'); 
       if(date('mY', $time1) != $my && ($time1 < $time2)) 
          $months_list[] = date('Y-m-01', $time1); 
    } 
    $months_list[] = date('Y-m-01', $time2); 
}
$number_of_months = count($months_list);
//echo '<pre>';print_r($months_list);exit;
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
    $qry_pre_calc = "  
        SELECT
            pre_calculated_data.prov_id,
            pre_calculated_data.`key`,
            pre_calculated_data.`value`,
            pre_calculated_data.key_type
        FROM
            pre_calculated_data
        WHERE
            pre_calculated_data.type = 'sdp_reporting_rate_province_wise'
            
    ";
    //echo $qry_pre_calc;
    $res_pre = mysql_query($qry_pre_calc);
    $pre_calculated_data =  array();
    while($row_pre = mysql_fetch_array($res_pre))
    {
        $pre_calculated_data[$row_pre['prov_id']][$row_pre['key']]=$row_pre['value'];
    }
    
        
    if( empty($pre_calculated_data))    
    { //start of calculations    
    echo 'empty';exit;
        //get total number of facilities in province
        $qry_1 = "  
            SELECT
                tbl_warehouse.prov_id,
                COUNT( DISTINCT tbl_warehouse.wh_id ) AS totalWH
            FROM
                    tbl_warehouse
            INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
            WHERE
            stakeholder.lvl = 7
            AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
            $where_clause
            GROUP BY
            tbl_warehouse.prov_id
                ";
            //echo $qry_1;exit;
                $res_1 = mysql_query($qry_1);
                $total_sdps= array();
                while($row_1 = mysql_fetch_array($res_1))
                {
                    $total_sdps[$row_1['prov_id']]=$row_1['totalWH'];

                    if(!isset($total_sdps['all'])) $total_sdps['all']=0;
                    $total_sdps['all']+=$row_1['totalWH'];
                }


         //counting the disabled facilities 
         $disabled_qry = "
                    SELECT

                        COUNT(DISTINCT warehouse_status_history.warehouse_id) as cnt,
                        tbl_warehouse.prov_id,
                        warehouse_status_history.reporting_month
                    FROM
                            warehouse_status_history
                    INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                    INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
                    INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    WHERE
                            warehouse_status_history.reporting_month BETWEEN '".$from_date."' and '".$to_date."'
                    AND warehouse_status_history.`status` = 0
                    AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                    AND stakeholder.lvl=7
                    $where_clause
                    GROUP BY
                            tbl_warehouse.prov_id,
                            warehouse_status_history.reporting_month
            ";
         //echo $disabled_qry;exit;
        $res_d = mysql_query($disabled_qry);
        $disabled_count= array();
        while($row_d = mysql_fetch_array($res_d))
        {
            $disabled_count[$row_d['prov_id']][$row_d['reporting_month']]=$row_d['cnt'];
            if(empty($disabled_count['all'][$row_d['reporting_month']])) $disabled_count['all'][$row_d['reporting_month']]=0;
            $disabled_count['all'][$row_d['reporting_month']] +=$row_d['cnt'];
        }   

        //making list of items , to display list incase no data entry is found
        //echo '<pre>';print_r($total_sdps);print_r($disabled_count);exit;       
        $w_clause="";
        if(!empty($stk))             
            $w_clause .= " AND stakeholder_item.stkid in (".$stk.")  ";    

        if(!empty($itm))             
            $w_clause .= " AND itminfo_tab.itm_id in (".$itm.")  ";    

        $qry_1 = "  SELECT
                        itminfo_tab.itmrec_id,
                        itminfo_tab.itm_name,
                        itminfo_tab.itm_id
                    FROM
                        itminfo_tab
                        INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
                    WHERE
                        itminfo_tab.itm_id in (1,2,9,3,5,7,8,13)
                        $w_clause

                    ORDER BY
                        itminfo_tab.frmindex ASC
                ";
            //echo $qry_1;exit;
                $res_1 = mysql_query($qry_1);
                $itm_arr= array();
                while($row_1 = mysql_fetch_array($res_1))
                {
                    $itm_arr[$row_1['itm_id']]=$row_1['itm_name'];
                }


                //query for getting reported facilities
                $q_reporting  = "SELECT
                                        tbl_warehouse.stkid,
                                        COUNT(
                                                DISTINCT tbl_warehouse.wh_id
                                        ) AS reportedWH,

                                        tbl_warehouse.prov_id,
                                        tbl_locations.LocName,tbl_hf_data.reporting_date
                                FROM
                                        tbl_warehouse
                                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
                                WHERE
                                     stakeholder.lvl = 7
                                     AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                                AND tbl_hf_data.reporting_date BETWEEN '".$from_date."' and '".$to_date."'
                                
                                $where_clause
                                GROUP BY
                                        tbl_warehouse.prov_id,
                                        tbl_hf_data.reporting_date ";
                //echo $q_reporting;exit;
                $res_reporting = mysql_query($q_reporting);
                $reporting_wh_arr  = $prov_arr = array();
                $total_reporting_wh = 0;
                //$prov_arr['all']='Aggregated';
                while($row=mysql_fetch_assoc($res_reporting))
                {
                    $prov_arr[$row['prov_id']] = $row['LocName'];

                    if(empty($reporting_wh_arr['all'][$row['reporting_date']])) $reporting_wh_arr['all'][$row['reporting_date']]=0;
                    if(empty($reporting_wh_arr[$row['prov_id']][$row['reporting_date']])) $reporting_wh_arr[$row['prov_id']][$row['reporting_date']]=0;
                    
                    $reporting_wh_arr[$row['prov_id']][$row['reporting_date']]+=$row['reportedWH'];
                    $reporting_wh_arr['all'][$row['reporting_date']]+=$row['reportedWH'];
                    
                    $total_reporting_wh +=$row['reportedWH'];
                }

                $rep_rate= array();
                foreach($prov_arr as $prov_id => $prov_data){
                    
                    foreach($months_list as $k => $v){
                        $master_total = $total_sdps[$prov_id];
                        $disabled_fac = (isset($disabled_count[$prov_id][$v])?$disabled_count[$prov_id][$v]:0);
                        $to_be_reported = $master_total - $disabled_fac;

                        $val = (isset($prov_data[$v])?$prov_data[$v]:0);

                        if($to_be_reported>0 && isset($reporting_wh_arr[$prov_id][$v]))
                            $r_r = ($reporting_wh_arr[$prov_id][$v]*100)/$to_be_reported;
                        else
                            $r_r=0;

                        $rep_rate[$prov_id][$v] = $r_r;
                    }
                    
                }
     // end of calculations    
    } 
    else{      
        $rep_rate = $pre_calculated_data;
    }
//echo '<pre>RR:';print_r($rep_rate);
//echo 'Total:';print_r($total_sdps);
//echo 'Disabled:';print_r($disabled_count);
//echo 'Reported:';print_r($reporting_wh_arr);exit;  

    //xml for chart
    $xmlstore = '<chart caption="Trendlines - Province Wise Avg Reporting Rate "  subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Day" yaxisname="Consumption" showvalues="0" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
 
    $xmlstore .= ' <categories>';
    foreach($months_list as $k => $month)
    {
        $xmlstore .= ' <category label="'.date('Y-M',strtotime($month)).'" />';
    }
    $xmlstore .= ' </categories>';
    
    foreach($rep_rate as $prov_id => $r_rate)
    {
        $xmlstore .= ' <dataset seriesname="'.$prov_id.'">';
        foreach($months_list as $k => $month)
        {   
            $val=(!empty($r_rate[$month])? $r_rate[$month]:'0');
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