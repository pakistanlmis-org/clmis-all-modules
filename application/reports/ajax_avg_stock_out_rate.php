<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$months_list = explode(',', $_REQUEST['months']);

$stk = $_REQUEST['stk'];
$from_date = strval($_REQUEST['from_date']);


$to_date = strval($_REQUEST['to_date']);
$prov = $_REQUEST['prov_id'];
$itm_arr_request = $_REQUEST['itm_arr_request'];
$where_clause = "";

if (!empty($prov))
    $where_clause .= " AND tbl_warehouse.prov_id in (" . $prov . ")  ";
if (!empty($stk))
    $where_clause .= " AND tbl_warehouse.stkid in (" . $stk . ")  ";

?>

     
     
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->
     
    
                 
                 <?php
                 
                                    //get total number of facilities in province
                                    $qry_1 = "  
                                        SELECT
                                        tbl_warehouse.prov_id,
                                        
                                        stakeholder_item.stk_item as itm,
                                                COUNT(
                                                        DISTINCT tbl_warehouse.wh_id
                                                ) AS totalWH,
tbl_warehouse.dist_id,
tbl_locations.LocName AS district
                                        FROM
                                                tbl_warehouse
                                        INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                        INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
                                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                        WHERE
                                        stakeholder.lvl = 7
                                        AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                                        AND NOT (tbl_warehouse.prov_id = 3 and tbl_warehouse.stkid = 9)
                                        AND NOT (tbl_warehouse.prov_id = 3 and tbl_warehouse.stkid = 2)
                                        $where_clause
                                        GROUP BY
                                        stakeholder_item.stk_item,
tbl_warehouse.dist_id
                                            ";
//                                        echo $qry_1;exit;
                                            $res_1 = mysql_query($qry_1);
                                            $total_sdps= array();
                                            while($row_1 = mysql_fetch_array($res_1))
                                            {
                                                $total_sdps[$row_1['dist_id']][$row_1['itm']]=$row_1['totalWH'];
                                                
//                                                if(!isset($total_sdps['all'][$row_1['itm']])) $total_sdps['all'][$row_1['itm']]=0;
//                                                $total_sdps['all'][$row_1['itm']]+=$row_1['totalWH'];
                                            }
                                          
                                     
                                     //counting the disabled facilities 
                                     $disabled_qry = "
                                                SELECT
                                                        
                                                    COUNT(DISTINCT warehouse_status_history.warehouse_id) as cnt,
                                                    tbl_warehouse.prov_id,
                                                    stakeholder_item.stk_item,
                                                    warehouse_status_history.reporting_month,
tbl_warehouse.dist_id
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
                                                        
tbl_warehouse.dist_id,
                                                        stakeholder_item.stk_item,warehouse_status_history.reporting_month
                                        ";
//                                     echo $disabled_qry;exit;
                                    $res_d = mysql_query($disabled_qry);
                                    $disabled_count= array();
                                    while($row_d = mysql_fetch_array($res_d))
                                    {
                                        $disabled_count[$row_d['dist_id']][$row_d['stk_item']][$row_d['reporting_month']]=$row_d['cnt'];
//                                        if(empty($disabled_count['all'][$row_d['stk_item']][$row_d['reporting_month']])) $disabled_count['all'][$row_d['stk_item']][$row_d['reporting_month']]=0;
//                                        $disabled_count['all'][$row_d['stk_item']][$row_d['reporting_month']] +=$row_d['cnt'];
                                    }   
                                            
                                    //making list of items , to display list incase no data entry is found
                                    //echo '<pre>';print_r($total_sdps);print_r($disabled_count);exit;       
                                    $w_clause="";
                                    if(!empty($stk))             
                                        $w_clause .= " AND stakeholder_item.stkid in (".$stk.")  ";    
                                    
                                    if(!empty($itm_arr_request))             
                                        $w_clause .= " AND itminfo_tab.itm_id in (".$itm_arr_request.")  ";    
                                    
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
                                                                    tbl_hf_data.item_id,
                                                                     tbl_warehouse.dist_id,
                                                                    tbl_locations.LocName,
                                                                    tbl_hf_data.reporting_date
                                                            FROM
                                                                    tbl_warehouse
                                                            INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                                            INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                                            WHERE
                                                                 stakeholder.lvl = 7
                                                                 AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                                                                AND NOT (tbl_warehouse.prov_id = 3 and tbl_warehouse.stkid = 9)
                                                                AND NOT (tbl_warehouse.prov_id = 3 and tbl_warehouse.stkid = 2)
                                                                AND tbl_hf_data.reporting_date BETWEEN '".$from_date."' and '".$to_date."'
                                                            $where_clause
                                                            GROUP BY
                                                                    tbl_warehouse.dist_id,
                                                                    tbl_hf_data.item_id,tbl_hf_data.reporting_date";
                                            //echo $q_reporting;exit;
                                            $res_reporting = mysql_query($q_reporting);
                                            $reporting_wh_arr  = $prov_arr = array();
                                            $total_reporting_wh = 0;
                                            $prov_arr=array();
                                            while($row=mysql_fetch_assoc($res_reporting))
                                            {
                                                $prov_arr[$row['dist_id']] = $row['LocName'];
                                                
//                                                if(empty($reporting_wh_arr['all'][$row['item_id']][$row['reporting_date']])) $reporting_wh_arr['all'][$row['item_id']][$row['reporting_date']]=0;
                                                if(empty($reporting_wh_arr[$row['dist_id']][$row['item_id']][$row['reporting_date']])) $reporting_wh_arr[$row['dist_id']][$row['item_id']][$row['reporting_date']]=0;
                                                if(empty($reporting_wh_arr2[$row['item_id']])) $reporting_wh_arr2[$row['item_id']]=0;
                                                
                                                $reporting_wh_arr[$row['dist_id']][$row['item_id']][$row['reporting_date']]+=$row['reportedWH'];
//                                                $reporting_wh_arr['all'][$row['item_id']][$row['reporting_date']]+=$row['reportedWH'];
                                                $reporting_wh_arr2[$row['item_id']] +=$row['reportedWH'];
                                                $total_reporting_wh +=$row['reportedWH'];
                                            }

//echo '<pre>';print_r($reporting_wh_arr);print_r($reporting_wh_arr2);exit;    

                                            //Query for mos
                                            $qry = "SELECT
                                                    tbl_warehouse.prov_id,
                                                    tbl_warehouse.stkid,
                                                    tbl_hf_data.item_id ,
                                                    itminfo_tab.itm_name,
                                                    tbl_hf_data.pk_id,
                                                    tbl_hf_data.closing_balance,
                                                    tbl_hf_data.avg_consumption,
                                                    tbl_hf_data.reporting_date,
                                                    tbl_warehouse.dist_id
                                                        FROM
                                                                tbl_warehouse
                                                        INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                                        INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                                        INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                                                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                                        WHERE
                                                                stakeholder.lvl = 7
                                                        $where_clause

                                                        AND tbl_hf_data.reporting_date BETWEEN '".$from_date."' and '".$to_date."'

                                                        AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                                                        AND NOT (tbl_warehouse.prov_id = 3 and tbl_warehouse.stkid = 9)
                                                        AND NOT (tbl_warehouse.prov_id = 3 and tbl_warehouse.stkid = 2)
                                                        
                                                        AND itminfo_tab.itm_category = 1
                                                        AND itminfo_tab.itm_id IN($itm_arr_request)
                                                    

                                            ";
//                                        echo $qry;exit;
                                        $qryRes = mysql_unbuffered_query($qry);
                                        $xc=1;
                                        $unk_arr= $so_arr2 = $so_arr = $us_arr = $sat_arr = $os_arr = array();
                                        $so_arr=array();

                                        while($row = mysql_fetch_assoc($qryRes))
                                        {
                                            //echo '<br/>'.$xc;
                                            $xc++;
                                            //$itm_arr[$row['item_id']] = $row['itm_name'];
                                            //$rep_year = date('Y',$row['reporting_date']);
                                            $rep_month= date('Y-m-01',strtotime($row['reporting_date']));

//                                            if(empty($so_arr['all'][$row['item_id']][$rep_month])) $so_arr['all'][$row['item_id']][$rep_month]=0;
                                            if(empty($so_arr[$row['dist_id']][$row['item_id']][$rep_month])) $so_arr[$row['dist_id']][$row['item_id']][$rep_month]=0;
                                            if(empty($so_arr2[$row['item_id']][$rep_month])) $so_arr2[$row['item_id']][$rep_month]=0;
                                            if( $row['closing_balance'] <= '0' )
                                            {
                                                $so_arr[$row['dist_id']][$row['item_id']][$rep_month] += 1;
//                                                $so_arr['all'][$row['item_id']][$rep_month] += 1;
                                                $so_arr2[$row['item_id']][$rep_month] += 1;
                                            }
                                                                                        
                                        }    
//    echo '<pre>'.$total_reporting_wh;print_r($itm_arr);echo 'SO Array:';print_r($so_arr);echo'wh_rep';print_r($reporting_wh_arr2);  
                ?>
                
                     
                    
                    
                 
                 <div class="row">
                    <div class="col-md-12">
                        <div class="widget1 widget-tabs">
                            <div class="widget-body" style="overflow:auto">
                                <table id="AvgStockOut" name="tbl" class="table table-bordered table-condensed" border="">    
                                    <tr>
                                        <th rowspan=""></th>
                                        <th rowspan=""></th>
                                        <?php
                                        foreach($months_list as $k => $v){
                                            echo '<th colspan="5" class="months_td" style="text-align:center !important;">'.date('M Y',strtotime($v)).'</th>';
                                        }
                                        ?>
                                        <th colspan="4" style="text-align:center !important;">Totals</th>

                                    </tr>
                                    <tr>
                                        <th rowspan="">#</th>
                                        <th rowspan="" align="center">Product</th>
                                        <?php
                                        foreach($months_list as $k => $v){
                                            echo '<td align="center" class="so_n">SO</td>';
                                            echo '<td align="center" class="so_r">SO Rate</td>';
                                            echo '<td align="center" class="reported_n">Reported</td>';
                                            echo '<td align="center" class="total_n">Total</td>';
                                            echo '<td align="center" class="reporting_r">RR</td>';
                                        }
                                        ?>
                                        <td align="center">Total SO</td>
                                        <td align="center">SO Rate</td>
                                        <td align="center">Total reported</td>
                                        <td align="center">Total SDPs</td>

                                    </tr>
                            <?php
                            $rep_rate = $so_rate = array();
                                foreach($so_arr as $prov_id => $prov_data)
                                {
                                    $month_total_so = $month_total_sdp = $month_total_reported = array();
                                    
                                    
                                        $pro_class = " prov_row ";
                                        $pro_style = " display:none ";
                                        $pro_color = "#9cd39c";
                                        $st = "" ;
                                     
                                    
                                    echo '<tr class="'.$pro_class.'"  >';
                                    echo '<td colspan="99" bgcolor="'.$pro_color.'" '.$st.'>'.$prov_arr[$prov_id].'</td>';
                                    echo '</tr>';
                                    
                                    $c=1;
                                    foreach($itm_arr as $itm_id => $itm_name)
                                    {
                                        $prod_total_so = $prod_total_sdp = $prod_total_reported = 0;
                                        
                                        echo '<tr  class="'.$pro_class.'"  >';
                                        echo '<td>'.$c++.'</td>';
                                        echo '<td>'.$itm_name.'</td>';
                                        
                                        foreach($months_list as $k => $v){
                                            $master_total = $total_sdps[$prov_id][$itm_id];
                                            $disabled_fac = (isset($disabled_count[$prov_id][$itm_id][$v])?$disabled_count[$prov_id][$itm_id][$v]:0);
                                            $to_be_reported = $master_total - $disabled_fac;
                                            
                                            $val = (isset($prov_data[$itm_id][$v])?$prov_data[$itm_id][$v]:0);
                                            
                                            if($reporting_wh_arr[$prov_id][$itm_id][$v]>0 && isset($val))
                                                $so_r=  ($val*100)/$reporting_wh_arr[$prov_id][$itm_id][$v];
                                            else
                                                $so_r = 0;
                                            
                                            if($to_be_reported>0 && isset($reporting_wh_arr[$prov_id][$itm_id][$v]))
                                                $r_r = ($reporting_wh_arr[$prov_id][$itm_id][$v]*100)/$to_be_reported;
                                            else
                                                $r_r=0;
                                            $so_rate[$prov_id][$itm_id][$v] = $so_r;
                                            $rep_rate[$prov_id][$itm_id][$v] = $r_r;
                                            echo '<td align="right" class="danger so_n">'.number_format($val).'</td>';
                                            echo '<td align="right" class="so_r">'.number_format($so_r,2).'</td>';
                                            echo '<td align="right" class="reported_n">'.number_format($reporting_wh_arr[$prov_id][$itm_id][$v]).'</td>';
                                            echo '<td align="right" class="total_n" title="All:'.$master_total.',Disabled:'.$disabled_fac.'">'.number_format($to_be_reported).'</td>';
                                            echo '<td align="right" class="reporting_r">'.number_format($r_r,2).'</td>';
                                            
                                            $prod_total_so          += $val;
                                            $prod_total_sdp         += $to_be_reported;
                                            $prod_total_reported    += (!empty($reporting_wh_arr[$prov_id][$itm_id][$v])?$reporting_wh_arr[$prov_id][$itm_id][$v]:0);
                                            
                                            @$month_total_so[$v]         += $val;
                                            @$month_total_sdp[$v]        += $to_be_reported;
                                            @$month_total_reported[$v]   += $reporting_wh_arr[$prov_id][$itm_id][$v];
                                        }
                                        echo '<td align="right" class="info">'.number_format($prod_total_so).'</td>';
                                        echo '<td align="right" class="info">'.number_format($prod_total_so*100/$prod_total_reported,2).'</td>';
                                        echo '<td align="right" class="info">'.number_format($prod_total_reported).'</td>';
                                        echo '<td align="right" class="info">'.number_format($prod_total_sdp).'</td>';
                                        
                                        echo '</tr>';
                                    }
                                    
                                        echo '<tr class="'.$pro_class.' warning"  >';
//                                        echo '<td></td>';
//                                        echo '<td><b>TOTAL</b></td>';
                                        
//                                        foreach($months_list as $k => $v){
//                                            
//                                            echo '<td align="right" class="danger so_n">'.number_format($month_total_so[$v]).'</td>';
//                                            echo '<td align="right" class="so_r">'.number_format(($month_total_so[$v]*100)/$month_total_reported[$v],2).'</td>';
//                                            echo '<td align="right" class="reported_n">'.number_format($month_total_reported[$v]).'</td>';
//                                            echo '<td align="right" class="total_n">'.number_format($month_total_sdp[$v]).'</td>';
//                                            echo '<td align="right" class="reporting_r">'.number_format(($month_total_reported[$v]*100)/$month_total_sdp[$v],2).'</td>';
//                                            
//                                        }
//                                        echo '<td align="right" class="">'.number_format(array_sum($month_total_so)).'</td>';
//                                        echo '<td align="right" class="">'.number_format(array_sum($month_total_so)*100/array_sum($month_total_reported),2).'</td>';
//                                        
//                                        echo '<td align="right" class="">'.number_format(array_sum($month_total_reported)).'</td>';
//                                        echo '<td align="right" class="">'.number_format(array_sum($month_total_sdp)).'</td>';
//                                        
                                         
                                }
                                ?>
                                </table>
                          </div>
                       
                   
                
                  
                
                 
                
                
                
                
            </div>
        </div>
    </div>

  
<!-- END BODY -->
</html>