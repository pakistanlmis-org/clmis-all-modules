<?php

function calculate_usaid_stock($date,$province,$sector = 'all'){
    $ret_arr = array();
    $stk_name = '';
    
    $qry = "SELECT
                national_stock_control.pk_id,
                national_stock_control.stkid,
                national_stock_control.provid,
                national_stock_control.checked,
                national_stock_control.last_modified_at,
                national_stock_control.date_from
            FROM
                national_stock_control
            WHERE
                date_from < '".$date."' 
            ORDER BY
                national_stock_control.date_from
    ";
//    echo $qry;exit;
    $qryRes = mysql_query($qry);
    $rules  = $rules_slabs  = $rules_slabs2  = $rules_slabs3  = array();
    while ($row = mysql_fetch_assoc($qryRes)) {
    //   if($row['provid'] != '10')
       $rules[$row['provid']][$row['stkid']] = 1; 
       $rules_slabs[$row['date_from']][$row['provid']][$row['stkid']] = 1; 
       $rules_slabs3[] = $row['date_from']; 
    }
    $rules_slabs2 = array_unique($rules_slabs3); 
    sort($rules_slabs2, SORT_NUMERIC);
    $where_warehosue="";
if(!empty($province))
{
    $where_warehosue="and tbl_warehouse.prov_id='$province'";
}
    $genesis = '2016-10-01';
    //$d[] = $genesis;
    $key_date = $key_date_nxt = 0;
    $apply_key = $apply_date = '0';
    $date_1 = $genesis;
    $date_2 = $date;
    $i=0;
    $apply_rule_arr = array();
    while($d<$date_2){
        $s = $i * 1;
        $d = date('Y-m-d',strtotime($genesis.'+'.$s.' days'));
        //echo '<br/> '.$i.' , d:  '.$d.' , k:'.$key_date.', nxt:'.$key_date_nxt.' = '.$rules_slabs2[$key_date];
        if(!empty($rules_slabs2[$key_date_nxt]) && $d >= $rules_slabs2[$key_date_nxt]){
            //echo ' -------- d:'.$d.' key:'.$key_date_nxt.' date:'.$rules_slabs2[$key_date_nxt];
            $apply_key = $key_date_nxt;
            $apply_date = $rules_slabs2[$key_date_nxt];
            $key_date_nxt++;
            if($key_date!=0)
            {
                $key_date = $key_date_nxt;
            }
        }
        //echo ' ,Final:'.$apply_key.' , '.$apply_date.'>>';
        $apply_rule_arr[$d]=$apply_key;
        $i++;
        //exit;
    }
//    echo '<pre>';print_r($apply_rule_arr);
//    echo '<pre>';print_r($rules_slabs2);print_r($rules_slabs);exit;
    
    //End of rules setting
    //Start fetching data 
    
    
    $and = '';

                if (!empty($date))
                    $and .= " AND DATE_FORMAT(national_stock.tr_date,'%Y-%m-%d') <= '$date'  ";
                
                if (!empty($province))
                    $and .= " AND national_stock.prov_id = $province  ";


                $qry = "SELECT
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
                        Sum(national_stock.quantity) as opening,
                        national_stock.prov_id,
                        national_stock.item_id
                        FROM
                        national_stock
                        INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                        INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                        INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                        WHERE
                        
                        national_stock.ref = 'ob'
                        $and
                        GROUP BY
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name
                        ORDER BY
                        national_stock.prov_id,
                        national_stock.stk_id,
                        national_stock.item_id
                ";
                //query result
                $qryRes = mysql_query($qry);
                $opening_bal = $issue_arr = $stk_arr = $closing_bal = $closing_bal_all = $prov_arr = $prod_arr = array();
                while ($row = mysql_fetch_assoc($qryRes)) {
                    //print_r($row);
                    $opening_bal[$row['LocName']][$stk_name][$row['itm_name']] = $row['opening'];
                    $closing_bal[$row['LocName']][$row['itm_name']] = $row['opening'];
                    
                    $prov_arr[$row['prov_id']] = $row['LocName'];
                    $products[$row['itm_name']] = $row['item_id'];
                    
                    $prod_arr[$row['LocName']][$row['item_id']] = $row['itm_name'];
                }

                $and_sector = '';
                if($sector=='public') $and_sector = " AND stakeholder.stk_type_id = 0 ";

                $qry = "(
                            SELECT
                                tbl_locations.LocName,
                                stakeholder.stkname,
                                itminfo_tab.itm_name,
                                Sum(national_stock.quantity) as qty,
                                national_stock.prov_id,
                                national_stock.item_id
                            FROM
                                national_stock
                                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                            WHERE
                                national_stock.ref = 'issue'
                                AND stakeholder.stk_type_id = 0
                                $and
                            GROUP BY
                                tbl_locations.LocName,
                                stakeholder.stkname,
                                itminfo_tab.itm_name
                            ORDER BY
                                national_stock.prov_id,
                                national_stock.stk_id,
                                national_stock.item_id
                    )";
                
               
                    $qry .= " UNION
                    (
                            SELECT
                                tbl_locations.LocName,
                                'Private Stk / NGOs' as stkname,
                                itminfo_tab.itm_name,
                                Sum(national_stock.quantity) as qty,
                                national_stock.prov_id,
                                national_stock.item_id
                            FROM
                                national_stock
                                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                            WHERE
                                national_stock.ref = 'issue'
                                $and
                                AND stakeholder.stk_type_id <> 0
                            GROUP BY
                                    tbl_locations.LocName,
                                    itminfo_tab.itm_name
                            ORDER BY
                                    national_stock.prov_id,
                                    national_stock.item_id
                    )";
                
                   $qry .= "  UNION
                    (
                            SELECT
                                tbl_locations.LocName,
                                'Issuance' as stkname,
                                itminfo_tab.itm_name,
                                Sum(national_stock.quantity) as qty,
                                national_stock.prov_id,
                                national_stock.item_id
                            FROM
                                national_stock
                                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                            WHERE
                                national_stock.ref in ('ob','issue')
                                $and_sector
                                $and
                                
                            GROUP BY
                                    tbl_locations.LocName,
                                    itminfo_tab.itm_name
                            ORDER BY
                                    national_stock.prov_id,
                                    national_stock.item_id
                    )
                    UNION
                    (
                            SELECT
                                tbl_locations.LocName,
                                'Loans/Relocations' as stkname,
                                itminfo_tab.itm_name,
                                Sum(national_stock.quantity) as qty,
                                national_stock.prov_id,
                                national_stock.item_id
                            FROM
                                national_stock
                                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                            WHERE
                                national_stock.ref in ('loan','relocate')
                                $and
                                
                            GROUP BY
                                    tbl_locations.LocName,
                                    itminfo_tab.itm_name
                            ORDER BY
                                    national_stock.prov_id,
                                    national_stock.item_id
                    )
                ";
                //query result
//                echo $qry;exit;
                $qryRes = mysql_query($qry);
                $all_products = $all_totals = array();
                while ($row = mysql_fetch_assoc($qryRes)) {
                    //print_r($row);
                    $issue_arr[$row['LocName']][$row['stkname']][$row['itm_name']] = $row['qty'];
                    if (empty($closing_bal[$row['LocName']][$row['itm_name']]))
                        $closing_bal[$row['LocName']][$row['itm_name']] = 0;

                    if ($row['stkname'] != 'Issuance')
                        $closing_bal[$row['LocName']][$row['itm_name']] += $row['qty'];
                    $stk_arr[$row['stkname']] = $row['stkname'];

                    $prov_arr[$row['prov_id']] = $row['LocName'];
                    $prod_arr[$row['LocName']][$row['item_id']] = $row['itm_name'];
                    
                    
                    $all_products[$row['item_id']] = $row['itm_name'];
                }

                
                
$qry = "SELECT
                tbl_warehouse.stkid,
                (CASE WHEN stk.stk_type_id <> 0 THEN 'Private Stk / NGOs'	ELSE	stk.stkname END	) AS stkname,
                    tbl_locations.PkLocID,
                    itminfo_tab.itm_id,
                    DATE_FORMAT(
                        tbl_stock_master.TranDate,
                        '%Y-%m-%d'
                    ) TranDate,
                    (tbl_stock_detail.Qty) Qty,
                        itminfo_tab.itm_name,
                        tbl_locations.LocName
                FROM
                    tbl_stock_master
                INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
                INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                LEFT JOIN tbl_warehouse AS fundingSource ON stock_batch.funding_source = fundingSource.wh_id
                INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                LEFT JOIN stakeholder AS stk_ofc ON tbl_warehouse.stkofficeid = stk_ofc.stkid
                LEFT JOIN stakeholder AS stk ON tbl_warehouse.stkid = stk.stkid
                LEFT JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
                WHERE
                    DATE_FORMAT(
                        tbl_stock_master.TranDate,
                        '%Y-%m-%d'
                    ) BETWEEN '$genesis' AND  '$date'
                AND  stock_batch.funding_source = 6891
                AND tbl_stock_master.TranTypeID = 2
                AND stock_batch.wh_id = 123
                AND tbl_stock_detail.temp = 0
                $where_warehosue
                ORDER BY
                    TranDate";
//echo $qry;exit;
$result = mysql_query($qry);
$issuance_data = $issuance_data_ruled = array();
while ($row = mysql_fetch_assoc($result)) {
//    if($row['itm_name'] != 'Jadelle') continue;
//    if($row['LocName'] != 'Sindh') continue;
//    if($row['stkname'] != 'Private Stk / NGOs') continue;
//    echo $row['Qty'].':';

    $rules = $rules_slabs[$rules_slabs2[$apply_rule_arr[$row['TranDate']]]];
    
    if(empty($issuance_data_ruled['National'][$row['stkname']][$row['itm_name']])) $issuance_data_ruled['National'][$row['stkname']][$row['itm_name']] = 0;
    if(!empty($rules) && $row['stkname']=='Private Stk / NGOs'){
        if(isset($rules[$row['PkLocID']][$row['stkid']]) && $rules[$row['PkLocID']][$row['stkid']]==1  ){
            if($row['PkLocID'] == '10'){
                 $issuance_data_ruled['National'][$row['stkname']][$row['itm_name']] += abs($row['Qty']);
            }
            else{
                @$issuance_data_ruled[$row['LocName']][$row['stkname']][$row['itm_name']] += abs($row['Qty']);
            }
        }
        else{
           $issuance_data_ruled['National'][$row['stkname']][$row['itm_name']] += abs($row['Qty']);
        }
    }
    else{
        @$issuance_data_ruled[$row['LocName']][$row['stkname']][$row['itm_name']]+=abs($row['Qty']);
    }
    
    @$issuance_data[$row['LocName']][$row['stkname']][$row['itm_name']] += abs($row['Qty']);
}


    foreach ($prov_arr as $province_id => $prov) {
        if (!empty($opening_bal[$prov])) {
            $prov_data = $opening_bal[$prov];
        } else {
            $prov_data = array();
            foreach ($prod_arr[$prov] as $prv => $prd) {
                $prov_data[$stk_name][$prd] = 0;
            }
        }
        foreach ($prov_data as $stk => $prod_data) {
            
            foreach ($prod_data as $prod => $qty) {
                $pr_issuance = 0;
                $pr_cb = 0;
                foreach ($stk_arr as $k => $stk) {
                    if ($stk == 'Issuance') {
                        
                        //Opening - Issued to all stks
                        $pr_cb = (($qty - $pr_issuance));
                        //echo '<br/>O-I ::Prv:'.$prov.' ,PRod:'.$prod.',O-I:'.($qty - $pr_issuance);
                    } 
                    elseif ( $stk =='Loans/Relocations') {
                        //Loans and Reallocations
                        $pr_cb+=(!empty($issue_arr[$prov][$stk][$prod]) ?(($issue_arr[$prov][$stk][$prod])) : '0');
                        //echo '<br/>Loans:: Prv:'.$prov.' ,PRod:'.$prod.',Loans:'.(!empty($issue_arr[$prov][$stk][$prod]) ?(($issue_arr[$prov][$stk][$prod])) : '0');
                    } 
                    elseif ( $stk =='Private Stk / NGOs') {
                        $new_r_qty = (!empty($issuance_data_ruled[$prov][$stk][$prod]) ? (($issuance_data_ruled[$prov][$stk][$prod])) : '0');
                        $pr_issuance += $new_r_qty;
                        //echo '<br/>NGO::Prv:'.$prov.' ,PRod:'.$prod.',O-I:'.($new_r_qty);
                    } 
                    else
                    {
                        $new_r_qty = (!empty($issuance_data_ruled[$prov][$stk][$prod]) ? (($issuance_data_ruled[$prov][$stk][$prod])) : '0');
                        $pr_issuance += $new_r_qty;
                    }
                }

                $cb = 0;
                if(!empty($closing_bal[$prov][$prod])) $cb = $closing_bal[$prov][$prod];
                //echo 'Prv:'.$prov.' ,PRod:'.$prod.',CB:'.$pr_cb;
                $closing_bal_all[$prov][$prod] = $pr_cb;
                
            }
        }
    }   
//echo '<pre>';
//echo 'OPENING';print_r($opening_bal);
////echo 'cb ';print_r($closing_bal);
////echo 'loans:';print_r($issue_arr);
//echo 'cb all';print_r($closing_bal_all);
////echo 'issuance rules:';print_r($issuance_data_ruled);
//exit;
    $ret_arr = $closing_bal_all;
    
    return $ret_arr;
}
?>