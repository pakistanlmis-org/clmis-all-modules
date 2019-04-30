<?php

function calculate_usaid_stock_old_formula($from_date,$province){
    $qry = "
            SELECT
                tbl_locations.LocName,
                stakeholder.stkname,
                itminfo_tab.itm_name,
                itminfo_tab.itm_id,
                Sum(national_stock.quantity) as qty,
                tbl_locations.LocType
            FROM
                national_stock
                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
            WHERE
                national_stock.ref in ('ob', 'issue','loan','relocate') 
                 /*AND stakeholder.stk_type_id = 0*/
                AND national_stock.prov_id = $province 
                AND national_stock.tr_date <= '$from_date'
            GROUP BY
                tbl_locations.LocName,

                itminfo_tab.itm_name
            ORDER BY
                national_stock.prov_id,
                national_stock.stk_id,
                national_stock.item_id
        ";
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $closing_bal=array();
    while ($row = mysql_fetch_assoc($qryRes)) {
        
        if (empty($closing_bal[$row['itm_id']][$from_date]))
            $closing_bal[$row['itm_id']][$from_date] = 0;

        //this is federal share only , at every month
        $closing_bal[$row['itm_id']][$from_date] += $row['qty'];
        //$q_data[$row['itm_id']]['share_of_stock']=$closing_bal[$row['itm_id']][$v];
    }
    return $closing_bal;
}
?>