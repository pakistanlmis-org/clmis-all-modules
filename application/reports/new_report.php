<?php

//Including files

include(APP_PATH . "includes/classes/db.php");
//$prov_name = $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['date_from']));
$default_funding_source = '6891';
$funding_source = '';
$funding_source = (!empty($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : '');
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
//echo $from_date;
//echo '<pre>';print_r($_REQUEST);exit;
$product = $_REQUEST['product'];
if (empty($product))
    $product = '1,2,9,3,5,7,8,13';

//Previously: the to date becomes the last day of that month
//$to_date = date("Y-m-t",strtotime($from_date));
//Now
$to_date = $from_date;
$from_date3 = date('Y-m-01', strtotime($from_date));

$total_cyp = 0;
$mos_arr = array();
$mos_arr['SO']['from'] = '0';
$mos_arr['SO']['to'] = '4.99';
$mos_arr['SO']['color'] = '#ff370f';
$mos_arr['SO']['fullname'] = 'Stock Out';
$mos_arr['SO']['shortname'] = 'SO';

$mos_arr['US']['from'] = '5';
$mos_arr['US']['to'] = '6.99';
$mos_arr['US']['color'] = '#0000ff';
$mos_arr['US']['fullname'] = 'Under Stock';
$mos_arr['US']['shortname'] = 'US';

$mos_arr['SAT']['from'] = '7';
$mos_arr['SAT']['to'] = '14.99';
$mos_arr['SAT']['color'] = '#008000';
$mos_arr['SAT']['fullname'] = 'Satisfactory';
$mos_arr['SAT']['shortname'] = 'SAT';

$mos_arr['OS']['from'] = '15';
$mos_arr['OS']['to'] = '9999999';
$mos_arr['OS']['color'] = '#6bceff';
$mos_arr['OS']['fullname'] = 'Over Stock';
$mos_arr['OS']['shortname'] = 'OS';



$qry_c = "SELECT
                    GROUP_CONCAT(funding_stk_prov.stakeholder_id) as stk
                FROM
                    funding_stk_prov
                INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                WHERE
                    funding_stk_prov.province_id = $province
                 ";
if (!empty($funding_source))
    $qry_c .= " AND  funding_stk_prov.funding_source_id = $funding_source ";
else
    $qry_c .= " AND  funding_stk_prov.funding_source_id = $default_funding_source ";

//echo $qry_c;exit;
$res = mysql_query($qry_c);
$comments_arr = array();
$row = mysql_fetch_assoc($res);
//print_r($row);exit;
$stakeholder = (!empty($row['stk'])) ? $row['stk'] : '1';
$ex_s = explode(',', $stakeholder);
$is_pwd = false;
if (in_array('1', $ex_s)) {
    $is_pwd = true;
    //$is_pwd = false;
}
$querys = "SELECT DISTINCT   tbl_warehouse.wh_id,
      tbl_warehouse.wh_name FROM funding_stk_prov INNER JOIN tbl_warehouse ON
      funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
       WHERE
    funding_stk_prov.province_id = $province";
//query result
$rsprov = mysql_query($querys) or die();
$stk_name = '';
while ($rowp = mysql_fetch_array($rsprov)) {
    if ($funding_source == $rowp['wh_id']) {
        $sel = "selected='selected'";
        $stk_name = $rowp['wh_name'];
    } else {
        $sel = "";
    }
}


//echo $stakeholder;exit;
//list of last 6 months from the date ...
$ex = explode('-', $from_date);

$t1 = strtotime($from_date);
$current_month = date('Y-m');
$previous_month = date('Y-m', strtotime(date('Y-m') . ' -1 month'));

$months_list2 = $months_list3 = array();
if (date('Y-m', strtotime($from_date)) == date('Y-m')) {
    $a = strtotime($from_date);
    $months_list2 = date('Y-m-d', strtotime('-1 month', $a));
    $months_list3 = date('Y-m-01', strtotime('-1 month', $a));
} else {
    $months_list2 = $from_date;
    $months_list3 = date('Y-m-01', strtotime($from_date));
}

//echo '<pre>';print_r($from_date);print_r($months_list2);print_r($months_list3);exit;
$q_data = array();
$qry_1 = "  SELECT
                itminfo_tab.itm_id,
                itminfo_tab.itm_name,
                itminfo_tab.itm_id,
                itminfo_tab.itm_type
            FROM
                itminfo_tab
            WHERE
                itminfo_tab.itm_id in (" . implode($product, ',') . ")
            ORDER BY
                itminfo_tab.frmindex ASC
        ";
$res_1 = mysql_query($qry_1);
$itm_arr = $itm_arr2 = $itm_name_id = array();
while ($row_1 = mysql_fetch_array($res_1)) {
    $itm_arr[$row_1['itm_id']] = $row_1['itm_name'];
    $itm_arr2[$row_1['itm_id']] = $row_1['itm_id'];
    $itm_name_id[$row_1['itm_name']] = $row_1['itm_id'];
    $q_data[$row_1['itm_id']]['unit'] = $row_1['itm_type'];
}

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
                    summary_province.reporting_date = '" . $months_list3 . "'

                    AND stakeholder.stk_type_id = 0
                    AND tbl_locations.ParentID IS NOT NULL
                    AND summary_province.province_id = $province
                   " . (!empty($funding_source) ? " AND summary_province.stakeholder_id in ($stakeholder) " : "") . "

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

        @$q_data[$row_2['itm_id']]['consumtion'] += $row_2['consumption'];
        @$q_data[$row_2['itm_id']]['cyp'] += $row_2['consumption'] * $row_2['cyp_factor'];
//        if (date('Y-m', strtotime($from_date)) == date('Y-m')) {
//
//            $q_data[$row_2['itm_id']][$from_date3]['amc'] = $row_2['avg_consumption'];
//        } else {
//            $q_data[$row_2['itm_id']][$row_2['yr'] . '-' . $row_2['mon'] . '-01']['amc'] = $row_2['avg_consumption'];
//        }
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
                        ) <= '" . $last_date2 . "'
                    AND (
                            tbl_stock_master.WHIDFrom = 123
                            OR tbl_stock_master.WHIDTo = 123
                    )";

$qry_3 .= " AND stock_batch.funding_source =  " . ((!empty($funding_source)) ? $funding_source : $default_funding_source) . " ";
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
    $and .= " AND national_stock.tr_date < '$from_date'  ";
}

$pipeline_arr = array();
$provincial_soh = array();

//Start calculation USAID Stock -----
//if (empty($funding_source) || $funding_source == '' || $is_pwd) {
//
//    $last_date = date("Y-m-t", strtotime($from_date));
//
//    //include('calculate_usaid_stock_old_formula.php');
//    //$closing_bal = calculate_usaid_stock_old_formula($from_date,$province);
//
//    include('calculate_usaid_stock.php');
//    $closing_bal_named_array = calculate_usaid_stock($from_date, $province);
//
////end of is PWD
//}
//foreach ($closing_bal_named_array as $p_name => $p_arr) {
//    foreach ($p_arr as $itm_name => $val) {
//        @$closing_bal[$itm_name_id[$itm_name]] = $val;
//    }
//}
//echo '<pre>OPENING';print_r($closing_bal_named_array);print_r($closing_bal);exit;
//END calculation USAID Stock -----


$w3 = '';
if (!empty($funding_source))
    $w3 = " AND provincial_stock.funding_source_id  = '$funding_source' ";
//fetching the temp provincial stock at provincal store

$prov_store_soh = array();
//$qry = "
//    SELECT
//        provincial_stock.province_id,
//        provincial_stock.item_id,
//        Sum(provincial_stock.quantity) AS soh_provincial_store
//    FROM
//        provincial_stock
//    WHERE
//        provincial_stock.province_id = $province
//        AND provincial_stock.trans_date <= '$from_date'
//        $w3
//    GROUP BY
//        provincial_stock.province_id,
//        provincial_stock.item_id
//    ";
////echo $qry;exit;
//$qryRes = mysql_query($qry);
//$min_expiry = array();
//
//while ($row = mysql_fetch_assoc($qryRes)) {
//    $prov_store_soh[$row['item_id']][$from_date] = $row['soh_provincial_store'];
//}
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
                             ) <= '" . $from_date . "'
                         AND (
                                 tbl_stock_master.WHIDFrom = 123
                                 OR tbl_stock_master.WHIDTo = 123
                         )";
if (empty($funding_source) || $funding_source == '' || $is_pwd) {
    $qry_min .= "AND stock_batch.funding_source in
                         ( SELECT
                                         funding_stk_prov.funding_source_id
                                         FROM
                                         funding_stk_prov
                                         WHERE
                                         funding_stk_prov.province_id = $province
                          )";
} else {
    $qry_min .= "AND stock_batch.funding_source = $funding_source ";
}
$qry_min .= "AND tbl_stock_master.temp = 0
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
//echo '<pre>';print_r($min_expiry);exit;
//now fetching the provincial share...
$qry_5 = "SELECT
                             itminfo_tab.itm_name,
                            stock_batch.manufacturer,
                            stakeholder.stkname as manuf,
                            itminfo_tab.qty_carton,
                            Sum(tbl_stock_detail.Qty) AS vials,
                            tbl_itemunits.UnitType,
                            itminfo_tab.itm_id
                                                 FROM
                            stock_batch
                            INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                            INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                            INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                            INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                            INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                            INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                                                 WHERE

                                                         DATE_FORMAT(
                                                                 tbl_stock_master.TranDate,
                                                                 '%Y-%m-%d'
                                                         ) <= '" . $from_date . "'
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
                                $qry_5 .= "AND stock_batch.funding_source = $funding_source ";
                            }
                            $qry_5 .= "AND tbl_stock_master.temp = 0
                                AND
                            itminfo_tab.itm_id IN (" . implode($product, ',') . ")
                                                 GROUP BY
                            itminfo_tab.itm_id,
                            stock_batch.manufacturer
                            ORDER BY
                            itminfo_tab.frmindex ASC,

                            stock_batch.manufacturer
             ";
//echo $qry_5;
//exit;
$res_5 = mysql_query($qry_5);

$provincial_soh_stk_wise = array();
while ($row_5 = mysql_fetch_assoc($res_5)) {

    if (empty($provincial_soh[$row_5['itm_id']][$from_date]['soh']))
        $provincial_soh[$row_5['manufacturer']] = 0;

    $provincial_soh[$row_5['manufacturer']] = $row_5['vials'];
}
    $qry_manu = "SELECT
itminfo_tab.itm_id as id,
itminfo_tab.itm_name,
stakeholder.stkname,
stakeholder_item.brand_name,
stakeholder_item.carton_volume,
stakeholder_item.carton_per_pallet,
stakeholder_item.quantity_per_pack,
stakeholder_item.stk_id
FROM
itminfo_tab
INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
stakeholder.stk_type_id = 3 AND
itminfo_tab.itm_id IN (" . implode($product, ',') . ")
ORDER BY
itminfo_tab.frmindex ASC
";
    $res_manu = mysql_query($qry_manu);
    $manu_name = $itm_stk = $carton_vol = $carton_pallets = array();
    while ($row_manu = mysql_fetch_assoc($res_manu)) {

        $manu_name[$row_manu['stk_id']] = $row_manu['itm_name'] . '-' . $row_manu['stkname'] . ' ' . $row_manu['brand_name'];
        $itm_stk[$row_manu['stk_id']] = $row_manu['id'];
        $carton_vol[$row_manu['stk_id']][$row_manu['id']] = $row_manu['carton_volume'];
        $carton_pallets[$row_manu['stk_id']][$row_manu['id']] = $row_manu['carton_per_pallet'];
    }
// print_r($itm_stk);exit;
    $clr = '#26C281';
    ?>
    <!--height:750px;overflow: auto;-->
    <div class="" style="">
        <table width="100%" border="1" class="table table-condensed " bordercolor="grey" style="border:1px solid grey!important;">
            
            <tr style="background-color: <?= $clr ?>;color:#ffffff;">
                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Manufacturers</th>
                <th colspan="4" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Opening Balance
                </th>
                <th colspan="4" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Stock Received</th>
                <th colspan="4" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Stock Issue</th>
                <th colspan="4" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Pipeline Stock</th>
                <th colspan="5" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Closing Balance</th>
            </tr>
            <tr>
                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Opening Balance</th>
                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Cartons</th>
                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Pallets</th>
                <th style="text-align:center;vertical-align:middle;">Total volume CBM</th>

                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Stock Received</th>
                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Cartons</th>
                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Pallets</th>
                <th style="text-align:center;vertical-align:middle;">Total volume CM</th>

                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Stock Issue</th>
                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Cartons</th>
                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Pallets</th>
                <th style="text-align:center;vertical-align:middle;">Total volume CBM</th>

                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Pipeline Stock</th>
                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Cartons</th>
                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">Pallets</th>
                <th style="text-align:center;vertical-align:middle;">Total volume CBM</th>

                <th rowspan="2" style="text-align:center;vertical-align:middle;color:#fffff;" width="23%">AMC</th>
                <th style="text-align:center;vertical-align:middle;">Quantity</th>
                <th style="text-align:center;vertical-align:middle;">Cartons Volume</th>
                <th style="text-align:center;vertical-align:middle;">Pallets</th>
                <th style="text-align:center;vertical-align:middle;" title="Months of stock">MOS</th>
            </tr>

            <tr style="background-color: <?= $clr ?>;color:#ffffff;">
            </tr>
            <?php
            $row_count = 1;
            foreach ($manu_name as $stk_id => $brand_name) {
                foreach ($itm_stk as $stid => $itm_id) {
                    if ($stid == $stk_id) {
                        foreach ($itm_arr as $id => $name) {
                            if ($itm_id == $id) {

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
                                summary_province.reporting_date <= '" . $months_list3 . "'

                                AND stakeholder.stk_type_id = 0
                                AND tbl_locations.ParentID IS NOT NULL
                                AND summary_province.province_id = $province
                               " . (!empty($funding_source) ? " AND summary_province.stakeholder_id in ($stakeholder) " : "") . "
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
//        echo $qry_2;exit;
                                $res_ayc = mysql_query($qry_ayc);
                                $row_ayc = mysql_fetch_assoc($res_ayc);
                                $this_prod_ayc = $row_ayc['ayc'];

                                $this_prod_cyp = 0;
                                if (!empty($q_data[$id]['cyp']))
                                    $this_prod_cyp = $q_data[$id]['cyp'];

                                if ($row_count % 2 == 0)
                                    $row_clr = '#e2e2e2';
                                else
                                    $row_clr = '';

                                $rowspan = 1;

                                echo '<tr style="background-color:' . $row_clr . '">';
                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';
//                                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">' . $brand_name . '</td>';

                                echo ' <td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" "></td>';
                                echo number_format($this_prod_ayc, 2);


                                if ($from_date == date('m')) {
                                    $from_date = date('m') - 1;
                                }
                                $stock_val = 0;
                                //$national_q = ((isset($closing_bal[$id]) && $closing_bal[$id] > 0) ? $closing_bal[$id] : 0);
                                $provincial = (isset($provincial_soh[$id][$from_date]['soh']) ? $provincial_soh[$id][$from_date]['soh'] : 0);
                                //$prov_store = (isset($prov_store_soh[$id][$from_date]) ? $prov_store_soh[$id][$from_date] : 0);
                                //@$stock_val = $national_q + $provincial + $prov_store;
                                @$stock_val = $provincial;
                                $this_amc = $this_prod_ayc;
                                echo '</td> ';

                                if (empty($this_prod_ayc) || $this_prod_ayc == 0)
                                    $mos = 0;
                                else
                                    $mos = (isset($provincial_soh[$stid]) ? $provincial_soh[$stid] : '0') / $this_prod_ayc;

                                echo '  <td style="text-align:right">CB: ' . (number_format(@$provincial_soh[$stid])) . '</td>
                                    <td style="text-align:right"><div class="pull-right">Ctn: ' . (number_format(@$provincial_soh[$stid] / $carton_vol[$stid][$itm_id])) . '</div>';

                                echo '</td>';
                                echo '  <td style="text-align:right">Plt: ' . (number_format(@$provincial_soh[$stid] / $carton_pallets[$stid][$itm_id])) . '</td>
                                    <td style="text-align:right">MOS: <div class="pull-right">' . (($mos > 0) ? number_format($mos, 2) : '0') . '</div>';

                                echo '</td>';
                                echo '</tr> ';

                                $row_count++;
                            }
                        }
                    }
                }
            }
 
            ?>
    </table>



</div>
<script>
    $('[data-toggle="popover"]').popover();
</script>