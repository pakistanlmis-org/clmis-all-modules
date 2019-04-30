<?php 
function dev_prov($stakeholder,$province,$rep_date,$item){
    $default_funding_source = '6891';
$stk = implode(',', $stakeholder);
    $where = '';
$wher_fund='';
$itm = implode(',', $item);
if(count($stakeholder)==1)
{
    $wher_fund="funding_stk_prov.stakeholder_id = $stk AND ";
}
else{
    $wher_fund=" funding_stk_prov.stakeholder_id IN ($stk) AND";
}
//print_r($wher_fund);exit;
$qry_fund="SELECT
GROUP_CONCAT(funding_stk_prov.funding_source_id) as funding_source,
funding_stk_prov.stakeholder_id,
funding_stk_prov.province_id
FROM
funding_stk_prov
WHERE
$wher_fund
funding_stk_prov.province_id = $province
";
//echo $qry_fund;exit;
$res_fund = mysql_query($qry_fund);
 
$row_fund=mysql_fetch_assoc($res_fund);
if($row_fund){
$funding_source = $row_fund['funding_source'];
}
 
$itm = implode(',', $item);
$where_itm='';
//print_r($where_itm);
if (count($item) > 1) {
    $where_itm .= " itminfo_tab.itm_id IN(";

    foreach ($item as $item) {
        $where_itm.="$item".",";
//        echo $stk;
    }
    
  
$where_itm = rtrim($where_itm, ',') . ')';
//echo $where;exit;
   } 
    else if(count($item) == 1)
    {
         $where_itm .= " itminfo_tab.itm_id =$item[0]";
//        echo $where;exit;

    }

if (count($stakeholder) > 1) {
    $where .= " AND summary_province.stakeholder_id  IN(";

    foreach ($stakeholder as $stk) {
        $where.="$stk".",";
//        echo $stk;
    }
    
  
$where = rtrim($where, ',') . ')';
//echo $where;exit;
   } 
    else if(count($stakeholder) == 1)
    {
         $where .= " AND summary_province.stakeholder_id =$stakeholder[0]";
//        echo $where;exit;

    }
$mos_chart_array=array();
$product = $item;
if(empty($product) || $product == 'null') $product = '1,2,9,3,5,7,8,13';

//Previously: the to date becomes the last day of that month
//$to_date = date("Y-m-t",strtotime($rep_date));
//Now
$to_date = $rep_date;
//$rep_date3 = date('Y-m-01', strtotime($rep_date));

$total_cyp=0;
$mos_arr = array();
$mos_arr['SO']['from']  = '0';
$mos_arr['SO']['to']    = '4.99';
$mos_arr['SO']['color'] = '#ff370f';
$mos_arr['SO']['fullname'] = 'Stock Out';
$mos_arr['SO']['shortname'] = 'SO';

$mos_arr['US']['from']  = '5';
$mos_arr['US']['to']    = '6.99';
$mos_arr['US']['color'] = '#0000ff';
$mos_arr['US']['fullname'] = 'Under Stock';
$mos_arr['US']['shortname'] = 'US';

$mos_arr['SAT']['from']  = '7';
$mos_arr['SAT']['to']    = '14.99';
$mos_arr['SAT']['color'] = '#008000';
$mos_arr['SAT']['fullname'] = 'Satisfactory';
$mos_arr['SAT']['shortname'] = 'SAT';

$mos_arr['OS']['from']  = '15';
$mos_arr['OS']['to']    = '9999999';
$mos_arr['OS']['color'] = '#6bceff';
$mos_arr['OS']['fullname'] = 'Over Stock';
$mos_arr['OS']['shortname'] = 'OS';

 

$qry_c= "SELECT
                    GROUP_CONCAT(funding_stk_prov.stakeholder_id) as stk
                FROM
                    funding_stk_prov
                INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                WHERE
                    funding_stk_prov.province_id = $province 
                 ";     
if(!empty($funding_source))
               $qry_c .=" AND  funding_stk_prov.funding_source_id IN ( $funding_source) ";
else
               $qry_c .=" AND  funding_stk_prov.funding_source_id = $default_funding_source ";
    
//echo $qry_c;exit;
$res = mysql_query($qry_c);
$comments_arr =array();
$row=mysql_fetch_assoc($res);
//print_r($row);exit;
$stakeholder    = (!empty($row['stk']))?$row['stk']:'1';

$ex_s = explode(',',$stakeholder);
$is_pwd = false;
if(in_array('1', $ex_s)){
    $is_pwd = true;
    //$is_pwd = false;
}
 
$ex = explode('-', $rep_date);

$t1=strtotime($rep_date);
$current_month = date('Y-m');
$previous_month = date('Y-m',strtotime(date('Y-m').' -1 month')); 

$months_list2 = $months_list3 = array();
if (date('Y-m', strtotime($rep_date)) == date('Y-m')) {
    $a = strtotime($rep_date);
    $months_list2 = date('Y-m-d', strtotime('-1 month', $a));
    $months_list3 = date('Y-m-01', strtotime('-1 month', $a));
} else {
    $months_list2 = $rep_date;
    $months_list3 =  date('Y-m-01', strtotime($rep_date));
}
 
$q_data = array();
$qry_l='';
$qry_1 = "  SELECT
                itminfo_tab.itm_id,
                itminfo_tab.itm_name,
                itminfo_tab.itm_id,
                itminfo_tab.itm_type
            FROM
                itminfo_tab
            WHERE
                $where_itm
            ORDER BY
                itminfo_tab.frmindex ASC
        ";
//print_r($qry_l);
$res_1 = mysql_query($qry_1);
$itm_arr = $itm_arr2 = $itm_name_id = array();
while ($row_1 = mysql_fetch_array($res_1)) {
    $itm_arr[$row_1['itm_id']] = $row_1['itm_name'];
    $itm_arr2[$row_1['itm_id']] = $row_1['itm_id'];
    $itm_name_id[$row_1['itm_name']] = $row_1['itm_id'];
    $q_data[$row_1['itm_id']]['unit'] = $row_1['itm_type'];
}
//print_r($itm_name_id);exit;
$qry_2 = "SELECT
                tbl_locations.LocName,
                tbl_locations.PkLocID,
                year(summary_province.reporting_date) as yr,
                LPAD(month(summary_province.reporting_date), 2, '0')  as mon,
                sum(summary_province.avg_consumption) as avg_consumption,
                itminfo_tab.itm_id,
                
                Sum(summary_province.consumption) AS consumption,
                provincial_cyp_factors.cyp_factor
            FROM
                summary_province
                INNER JOIN tbl_locations ON summary_province.province_id = tbl_locations.PkLocID
                INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
                INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
                LEFT JOIN provincial_cyp_factors ON summary_province.province_id = provincial_cyp_factors.province_id AND itminfo_tab.itm_id = provincial_cyp_factors.item_id AND summary_province.stakeholder_id = provincial_cyp_factors.stakeholder_id

            WHERE
                    summary_province.reporting_date = '".$rep_date."'
                    
                    AND stakeholder.stk_type_id = 0 
                    AND tbl_locations.ParentID IS NOT NULL
                    AND summary_province.province_id = $province
                   $where     
                    
           GROUP BY
                summary_province.province_id,
                summary_province.item_id,
                year(summary_province.reporting_date),
                month(summary_province.reporting_date),
                summary_province.stakeholder_id
            ORDER BY
                summary_province.province_id,
                summary_province.item_id,
                year(summary_province.reporting_date),
                month(summary_province.reporting_date)
        ";
//echo $qry_2;exit;
$res_2 = mysql_query($qry_2);
 $total_cons_arr = $prov_name_id = array();

$total_cons = 0;
while ($row_2 = mysql_fetch_assoc($res_2)) {
    
    if ($row_2['PkLocID'] == $province) {
        
        @$q_data[$row_2['itm_id']]['consumtion']    += $row_2['consumption'];
        @$q_data[$row_2['itm_id']]['cyp']           += $row_2['consumption']*$row_2['cyp_factor'];
 
    }
}
//echo '<pre>';print_r($q_data);exit;

$last_date2 = date("Y-m-t", strtotime($to_date));
//soh from stock batch table, which is provincial share
$qry_3 = "SELECT
			itminfo_tab.itm_name,
			itminfo_tab.qty_carton,
			SUM(tbl_stock_detail.Qty)  AS vials,
			tbl_itemunits.UnitType,
                        itminfo_tab.itm_id
		FROM
			stock_batch
		INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
		INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
		INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
		WHERE
			
                        DATE_FORMAT(
                                tbl_stock_master.TranDate,
                                '%Y-%m-%d'
                        ) <= '" . $rep_date . "'
                    AND (
                            tbl_stock_master.WHIDFrom = 123
                            OR tbl_stock_master.WHIDTo = 123
                    )";

     if(!empty($funding_source))
               $qry_3 .=" AND stock_batch.funding_source IN ( $funding_source) ";
else
               $qry_3 .=" AND  stock_batch.funding_source = $default_funding_source ";   
    
    $qry_3 .= " GROUP BY
			itminfo_tab.itm_id
		ORDER BY
			itminfo_tab.frmindex
        ";
//echo $qry_3;exit;
$res_3 = mysql_query($qry_3);


while ($row_3 = mysql_fetch_assoc($res_3)) {
    $q_data[$row_3['itm_id']]['soh'] = $row_3['vials'];
    $q_data[$row_3['itm_id']]['unit'] = $row_3['UnitType'];
}
//echo '<pre>';print_r($q_data);

$and = '';

if (!empty($province)) {
    $and .= " AND national_stock.prov_id = $province  ";
}
if (!empty($last_date)) {
    $and .= " AND national_stock.tr_date < '$rep_date'  ";
}

$pipeline_arr = array();
$provincial_soh = array();
 $closing_bal_named_array =array();
//Start calculation USAID Stock -----
if(empty($funding_source) || $funding_source=='' || $is_pwd)
{

    $last_date = date("Y-m-t", strtotime($rep_date));
    
   
//    include('calculate_usaid_stock.php');
    include(APP_PATH . "dashboard/calculate_usaid_stock.php");
    $closing_bal_named_array = calculate_usaid_stock($rep_date,$province);

//end of is PWD
}
foreach($closing_bal_named_array as $p_name=>$p_arr){
foreach($p_arr as $itm_name=>$val){
    @$closing_bal[$itm_name_id[$itm_name]] = $val;
}
} 


 $w3 = '';
if(!empty($funding_source)) $w3 = " AND provincial_stock.funding_source_id IN ($funding_source) ";
//fetching the temp provincial stock at provincal store

$qry = "
    SELECT
        provincial_stock.province_id,
        provincial_stock.item_id,
        Sum(provincial_stock.quantity) AS soh_provincial_store
    FROM
        provincial_stock
    WHERE
        provincial_stock.province_id = $province
        AND provincial_stock.trans_date <= '$rep_date'
        $w3
    GROUP BY
        provincial_stock.province_id,
        provincial_stock.item_id
    ";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$prov_store_soh = array();
$min_expiry = array();

while ($row = mysql_fetch_assoc($qryRes)) {
     $prov_store_soh[$row['item_id']][$rep_date] = $row['soh_provincial_store'];
}
//echo '<pre>OPENING';print_r($prov_store_soh);exit;
    $qry_min = "SELECT
                        itminfo_tab.itm_id,
                        min(stock_batch.batch_expiry) as min_batch_expiry
                     FROM
                             stock_batch
                     INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                     INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                     INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                     INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                     INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                     WHERE

                             DATE_FORMAT(
                                     tbl_stock_master.TranDate,
                                     '%Y-%m-%d'
                             ) <= '" . $rep_date . "'
                         AND (
                                 tbl_stock_master.WHIDFrom = 123
                                 OR tbl_stock_master.WHIDTo = 123
                         )";
                if(empty($funding_source) || $funding_source== '' || $is_pwd)
                {
                $qry_min .= "AND stock_batch.funding_source in 
                         ( SELECT
                                         funding_stk_prov.funding_source_id
                                         FROM
                                         funding_stk_prov
                                         WHERE
                                         funding_stk_prov.province_id = $province
                          )";
                }
                else
                {
                    $qry_min .= "AND stock_batch.funding_source IN( $funding_source) ";
                }
                $qry_min .="AND tbl_stock_master.temp = 0
                    AND stock_batch.Qty > 0
                    GROUP BY
                            itminfo_tab.itm_id
                    ORDER BY
                            stock_batch.batch_expiry
             ";
    //echo $qry_min;exit;
    $res_min = mysql_query($qry_min);
    while ($row = mysql_fetch_assoc($res_min)) {
        $min_expiry[$row['itm_id']] = $row['min_batch_expiry'];
    }           
    $qry_5 = "SELECT
                             itminfo_tab.itm_name,
                             itminfo_tab.qty_carton,
                             SUM(tbl_stock_detail.Qty)  AS vials,
                             tbl_itemunits.UnitType,
                             itminfo_tab.itm_id,
                             stock_batch.funding_source,
                             tbl_warehouse.wh_name as funding_source_name,
                             stock_batch.batch_expiry
                     FROM
                             stock_batch
                     INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                     INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                     INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                     INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                     INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                     WHERE

                             DATE_FORMAT(
                                     tbl_stock_master.TranDate,
                                     '%Y-%m-%d'
                             ) <= '" . $rep_date . "'
                         AND (
                                 tbl_stock_master.WHIDFrom = 123
                                 OR tbl_stock_master.WHIDTo = 123
                         )";
                if(empty($funding_source) || $funding_source== ''  )
                {
                $qry_5 .= "AND stock_batch.funding_source in 
                         ( SELECT
                                         funding_stk_prov.funding_source_id
                                         FROM
                                         funding_stk_prov
                                         WHERE
                                         funding_stk_prov.province_id = $province
                          )";
                }
                else
                {
                    $qry_5 .= "AND stock_batch.funding_source IN( $funding_source )";
                }
                $qry_5 .="AND tbl_stock_master.temp = 0
                     GROUP BY
                             itminfo_tab.itm_id,
                             stock_batch.funding_source
                     ORDER BY
                             itminfo_tab.frmindex
             ";
//    echo $qry_5;exit;
    $res_5 = mysql_query($qry_5);

    $provincial_soh_stk_wise= $pro_soh= array();
    
    while ($row_5 = mysql_fetch_assoc($res_5)) {
   
        if (empty($provincial_soh[$row_5['itm_id']][$rep_date]['soh'])){
            $provincial_soh[$row_5['itm_id']][$rep_date]['soh'] = 0;
//            $pro_soh[$row['itm_id']]=0;
        }
        $provincial_soh[$row_5['itm_id']][$rep_date]['soh'] += $row_5['vials'];
         @$pro_soh[$row_5['itm_id']]+= $row_5['vials'];
         
        if(empty ($provincial_soh_stk_wise[$row_5['itm_id']][$rep_date][$row_5['funding_source_name']])) $provincial_soh_stk_wise[$row_5['itm_id']][$rep_date][$row_5['funding_source_name']]=0;
        $provincial_soh_stk_wise[$row_5['itm_id']][$rep_date][$row_5['funding_source_name']] += $row_5['vials'];
       
    }  

    $qry_6 = "
               
            SELECT
                itminfo_tab.itm_id,
                (shipments.shipment_quantity) as shipment_quantity,
                sum(tbl_stock_detail.Qty) as received_qty,
                shipments.reference_number,
                itminfo_tab.itm_name,
                tbl_warehouse.wh_name,
                tbl_locations.LocName,
                shipments.shipment_date,
                shipments.`status`
            FROM
                    shipments
            INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
            INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
            INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
            LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = shipments.pk_id
            LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
            WHERE
                shipments.shipment_date > '$rep_date' 
                AND shipments.status NOT IN ('Cancelled','Received')

            ";
            if(empty($funding_source) || $funding_source== '' || $is_pwd)
            {
            $qry_6 .= "AND shipments.stk_id in 
                     ( SELECT
                                     funding_stk_prov.funding_source_id
                                     FROM
                                     funding_stk_prov
                                     WHERE
                                     funding_stk_prov.province_id = $province
                      )";
            }
            else
            {
                $qry_6 .= "AND shipments.stk_id IN( $funding_source )";
            }
            $qry_6 .="                            
            GROUP BY
                shipments.pk_id,
                itminfo_tab.itm_id
                
                    ";
//echo $qry_6;exit;

    $res_6 = mysql_query($qry_6);
    $pipeline_detail_arr = array();
    while ($row_6 = mysql_fetch_assoc($res_6)) {
        $pipeline_detail_arr[$row_6['itm_id']][] = $row_6;
        $s_q = isset($row_6['shipment_quantity'])?$row_6['shipment_quantity']:'0';
        $r_q = isset($row_6['received_qty'])?$row_6['received_qty']:'0';
        $remaining_q = $s_q - $r_q;
        if(empty($pipeline_arr[$row_6['itm_id']][$rep_date]))$pipeline_arr[$row_6['itm_id']][$rep_date]=0;
        $pipeline_arr[$row_6['itm_id']][$rep_date] += $remaining_q;
    }
 
    $clr = '#26C281';
?>
<!--height:750px;overflow: auto;-->
<div class="" style="display:none;" >
 
            <?php
            $row_count = 1;
  
            foreach ($itm_arr as $id => $name) {
                
                
        $qry_ayc = "select AVG(avg_consumption) as ayc,itm_id FROM
                    (
                        SELECT
                            tbl_locations.LocName,
                            tbl_locations.PkLocID,
                            year(summary_province.reporting_date) as yr,
                            LPAD(month(summary_province.reporting_date), 2, '0')  as mon,
                            sum(summary_province.avg_consumption) as avg_consumption,
                            itminfo_tab.itm_id
                        FROM
                            summary_province
                            INNER JOIN tbl_locations ON summary_province.province_id = tbl_locations.PkLocID
                            INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
                            INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
                        WHERE
                                summary_province.reporting_date <= '".$rep_date."'

                                AND stakeholder.stk_type_id = 0 
                                AND tbl_locations.ParentID IS NOT NULL
                                AND summary_province.province_id = $province
                              $where    
                                AND 	itminfo_tab.itm_id = $id

                       GROUP BY
                            summary_province.province_id,
                            summary_province.item_id,
                            year(summary_province.reporting_date),
                            month(summary_province.reporting_date)
                        ORDER BY
                                summary_province.reporting_date desc
                                limit 12

                        ) as A
                ";
//        echo $qry_ayc;exit;
        $res_ayc = mysql_query($qry_ayc);
        $row_ayc = mysql_fetch_assoc($res_ayc);
        $this_prod_ayc = $row_ayc['ayc'];

        $this_prod_cyp=0;
        if(!empty($q_data[$id]['cyp']))
            $this_prod_cyp = $q_data[$id]['cyp'];
                
               if ($row_count % 2 == 0)
                    $row_clr = '#e2e2e2';
                else
                    $row_clr = '';

                $rowspan=((isset($pipeline_arr[$id][$rep_date]) && $pipeline_arr[$id][$rep_date]>0) ? '3' : '');
 
                if ($rep_date == date('m')) {
                    $rep_date = date('m') - 1;
                }
                $stock_val = 0;
                $national_q= ((isset($closing_bal[$id]) && $closing_bal[$id] > 0) ? $closing_bal[$id] : 0);
                $provincial =  (isset($provincial_soh[$id][$rep_date]['soh']) ? $provincial_soh[$id][$rep_date]['soh'] : 0);
                $prov_store = (isset($prov_store_soh[$id][$rep_date]) ? $prov_store_soh[$id][$rep_date] : 0);
                @$stock_val =  $national_q+$provincial+$prov_store ;
 
                $this_amc = $this_prod_ayc;
                 
                
                if (empty($this_prod_ayc) || $this_prod_ayc == 0)
                    $mos = 0;
                else
                    $mos = (isset($stock_val) ? $stock_val : '0') / $this_prod_ayc;

                $data_content = "";
                $t_mos = (!empty($this_prod_ayc) && $this_prod_ayc > 0) ? number_format(($provincial/$this_prod_ayc),2) : 0;
                $data_content .= '<b>Provincial Stock at CW&S : '.number_format($provincial).'('.$t_mos.')</b>';
                if(!empty($provincial_soh_stk_wise[$id][$rep_date]))
                {
                    foreach($provincial_soh_stk_wise[$id][$rep_date] as $funding_src => $val2)
                    {
                        $t_mos = (!empty($this_prod_ayc) && $this_prod_ayc > 0) ? number_format(($val2/$this_prod_ayc),2) : 0;
                        $data_content .= ' <br/> - '.$funding_src.': '.number_format($val2) .'('.$t_mos.')';
                    }
                }
                
                if(!empty($prov_store))
                {
                    $data_content .= ' <br/>';
                    $data_content .= '';
                    if($province == 1)
                    {
                        $t_mos = (!empty($this_prod_ayc) && $this_prod_ayc > 0) ? number_format(($prov_store/$this_prod_ayc),2) : 0;
                        $data_content .= ' <br/><b>Provincial stock at MSD: '.number_format($prov_store).'('.$t_mos.')</b>';
                    }
                }
                
               if(empty($funding_source) || $funding_source== '' || $is_pwd)
                {
                        $t_mos = (!empty($this_prod_ayc) && $this_prod_ayc > 0) ? number_format(($national_q/$this_prod_ayc),2) : 0;
                    $data_content .= ' <br/><b>USAID Supported Stock: '.number_format($national_q).'('.$t_mos.')</b>';
                }
                    $mos_chart_array[$id]=$mos;
                
                $min_delivery_time = 6;             //no of months it takes to deliver
                $min_stock_months = 12 ;            //minimum mos to be maintained
                $sufficiency_upper_level = 18;      //upper slab of sufficiency level
                $minimum_ordering_slab = 6;         //minimum stock to be ordered , suggested to be equal to min delivery time
                $reorder_after_months = round($mos3) - ($min_delivery_time + $min_stock_months);
                $reorder_after_months = max($reorder_after_months,0);
                $reorder_date = date('Y-m-d',strtotime("+".$reorder_after_months." months".date("Y-m-d")));
                
                if(round($mos3) > $sufficiency_upper_level)
                    $months_qty_to_order = $minimum_ordering_slab;
                elseif(round($mos3) > $min_delivery_time)
                    $months_qty_to_order = $sufficiency_upper_level - (round($mos3) - $min_delivery_time);
                else
                    $months_qty_to_order = $sufficiency_upper_level;
                $reorder_qty  = $months_qty_to_order * $this_prod_ayc;
                
                
                $row_cls = $date_txt = "";
                if($reorder_after_months <= 0) $row_cls = " ";
            
                
                $row_count++;
            }
            ?> 
 
     
</div>
<?php // print_r($pro_soh);exit;?>
<script>
    $('[data-toggle="popover"]').popover();
</script>
    <?php
    $mos_soh_array=array();
    $mos_so_array[0]=$mos_chart_array;
    $mos_so_array[1]=$pro_soh;
    return $mos_so_array;
}