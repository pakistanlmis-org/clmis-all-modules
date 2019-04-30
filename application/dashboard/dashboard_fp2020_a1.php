<?php
//echo '<pre>';print_r($_REQUEST);exit;
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
$prov_name = $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
$default_funding_source = '6891';
$funding_source = (!empty($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : '');
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
//echo '<pre>';print_r($_REQUEST);
$where_funding = '';
$where_summary_prov = '';
$where_funding_qry_f = '';
$product = $_REQUEST['product'];
if (empty($product) || $product == 'null')
    $product = '1,2,9,3,5,7,8,13';
if (!empty($province)) {
    $where_funding = " funding_stk_prov.province_id = $province AND";
    $where_summary_prov = " AND summary_province.province_id = $province";
    $where_funding_qry_f = " WHERE funding_stk_prov.province_id = $province";
}
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

function get_mos_color($mos_val, $mos_arr) {
    $color1 = '';
    foreach ($mos_arr as $k => $mos_data) {
        //print_r($mos_data);
        if ($mos_val <= $mos_data['to']) {
            $color1 = $mos_data['color'];
            break;
        }
    }
    return $color1;
}

$qry_c = "SELECT
                    GROUP_CONCAT(funding_stk_prov.stakeholder_id) as stk
                FROM
                    funding_stk_prov
                INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                WHERE
                    $where_funding
                 ";
if (!empty($funding_source))
    $qry_c .= "   funding_stk_prov.funding_source_id = $funding_source ";
else
    $qry_c .= "   funding_stk_prov.funding_source_id = $default_funding_source ";

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
//$funding_qry_f=str_replace('AND','',$where_funding) ;
//echo($funding_qry_f);exit;
$qry_f = "SELECT
            funding_stk_prov.funding_source_id
            FROM
            funding_stk_prov
           $where_funding_qry_f";


//            echo $qry_f;
$res_f = mysql_query($qry_f);
$funding_stks = array();
while ($row_f = mysql_fetch_assoc($res_f)) {
    $funding_stks[$row_f['funding_source_id']] = $row_f['funding_source_id'];
}

//print_r($funding_stks);exit;


$stk_name = $_REQUEST['stk_name'];

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
                itminfo_tab.itm_id in (" . $product . ")
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
                    $where_summary_prov
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

    if (!empty($province)) {
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
    } else {
        @$q_data[$row_2['itm_id']]['consumtion'] += $row_2['consumption'];
        @$q_data[$row_2['itm_id']]['cyp'] += $row_2['consumption'] * $row_2['cyp_factor'];
    }
}
//echo '<pre>';
//print_r($q_data);
//exit;

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
			(tbl_stock_master.WHIDFrom = 123 AND tbl_stock_master.TranTypeID = 2)
			OR (tbl_stock_master.WHIDTo = 123 AND tbl_stock_master.TranTypeID = 1)
			OR (tbl_stock_master.WHIDFrom = 123 AND tbl_stock_master.WHIDTo = 123 AND tbl_stock_master.TranTypeID > 2)
		) ";

$qry_3 .= " AND stock_batch.funding_source =  " . ((!empty($funding_source)) ? $funding_source : $default_funding_source) . " ";
$qry_3 .= " GROUP BY
			itminfo_tab.itm_id
		ORDER BY
			itminfo_tab.frmindex
        ";
//echo $qry_3;
//exit;
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
if (empty($funding_source) || $funding_source == '' || $is_pwd) {

    $last_date = date("Y-m-t", strtotime($from_date));

    //include('calculate_usaid_stock_old_formula.php');
    //$closing_bal = calculate_usaid_stock_old_formula($from_date,$province);

    include('calculate_usaid_stock.php');
    $closing_bal_named_array = calculate_usaid_stock($from_date, $province);

//end of is PWD
}
foreach ($closing_bal_named_array as $p_name => $p_arr) {
    foreach ($p_arr as $itm_name => $val) {
        @$closing_bal[$itm_name_id[$itm_name]] += $val;
    }
}
//echo '<pre>';
//print_r($closing_bal);
//echo '<pre>OPENING';print_r($closing_bal_named_array);print_r($closing_bal);exit;
//END calculation USAID Stock -----

$where_prov_stock = '';
if (!empty($province)) {
    $where_prov_stock = "provincial_stock.province_id = $province AND";
}
$w3 = '';
if (!empty($funding_source))
    $w3 = " AND provincial_stock.funding_source_id  = '$funding_source' ";
//fetching the temp provincial stock at provincal store

$qry = "
    SELECT
        provincial_stock.province_id,
        provincial_stock.item_id,
        Sum(provincial_stock.quantity) AS soh_provincial_store
    FROM
        provincial_stock
    WHERE
        $where_prov_stock provincial_stock.trans_date <= '$from_date'
        $w3
    GROUP BY
        provincial_stock.province_id,
        provincial_stock.item_id
    ";
//echo $qry;
//exit;
$qryRes = mysql_query($qry);
$prov_store_soh = array();
$min_expiry = array();

while ($row = mysql_fetch_assoc($qryRes)) {
    $prov_store_soh[$row['item_id']][$from_date] = $row['soh_provincial_store'];
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
                             ) <= '" . $from_date . "'
                         AND (
                                 tbl_stock_master.WHIDFrom = 123
                                 OR tbl_stock_master.WHIDTo = 123
                         )";
if (empty($funding_source) || $funding_source == '' || $is_pwd) {
    //if (!empty($funding_stks))
        $qry_min .= "AND stock_batch.funding_source in  (" . implode(',', $funding_stks) . ") ";
}
else {
    $qry_min .= "AND stock_batch.funding_source = $funding_source ";
}
$qry_min .= "AND tbl_stock_master.temp = 0
                    AND stock_batch.Qty > 0
                    GROUP BY
                            itminfo_tab.itm_id
                    ORDER BY
                            stock_batch.batch_expiry
             ";
//    echo $qry_min;
//    exit;
$res_min = mysql_query($qry_min);
while ($row = mysql_fetch_assoc($res_min)) {
    $min_expiry[$row['itm_id']] = $row['min_batch_expiry'];
}
//echo '<pre>';print_r($min_expiry);exit;
//now fetching the provincial share...
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
                             ) <= '" . $from_date . "'
                        AND (
			(tbl_stock_master.WHIDFrom = 123 AND tbl_stock_master.TranTypeID = 2)
			OR (tbl_stock_master.WHIDTo = 123 AND tbl_stock_master.TranTypeID = 1)
			OR (tbl_stock_master.WHIDFrom = 123 AND tbl_stock_master.WHIDTo = 123 AND tbl_stock_master.TranTypeID > 2)
		)  ";
if (empty($funding_source) || $funding_source == '') {
    //if (!empty($funding_stks))
        $qry_5 .= "AND stock_batch.funding_source in  (" . implode(',', $funding_stks) . ") ";
}
else {
    $qry_5 .= "AND stock_batch.funding_source = $funding_source ";
}
$qry_5 .= "AND tbl_stock_master.temp = 0
                     GROUP BY
                             itminfo_tab.itm_id,
                             stock_batch.funding_source
                     ORDER BY
                             itminfo_tab.frmindex
             ";
//    echo $qry_5;
//    exit;
$res_5 = mysql_query($qry_5);

$provincial_soh_stk_wise = array();
while ($row_5 = mysql_fetch_assoc($res_5)) {

    //minimum expiry
//        if(empty($min_expiry[$row_5['itm_id']]))
//            $min_expiry[$row_5['itm_id']]=$row_5['batch_expiry'];
//        if(!empty($min_expiry[$row_5['itm_id']]) && $min_expiry[$row_5['itm_id']]>$row_5['batch_expiry'])
//            $min_expiry[$row_5['itm_id']]=$row_5['batch_expiry'];
    //this is the prov stock
    if (empty($provincial_soh[$row_5['itm_id']][$from_date]['soh']))
        $provincial_soh[$row_5['itm_id']][$from_date]['soh'] = 0;

    $provincial_soh[$row_5['itm_id']][$from_date]['soh'] += $row_5['vials'];

    if (empty($provincial_soh_stk_wise[$row_5['itm_id']][$from_date][$row_5['funding_source_name']]))
        $provincial_soh_stk_wise[$row_5['itm_id']][$from_date][$row_5['funding_source_name']] = 0;
    $provincial_soh_stk_wise[$row_5['itm_id']][$from_date][$row_5['funding_source_name']] += $row_5['vials'];


    // if(!empty($row_5['vials']) && !empty($q_data[$row_5['itm_id']]['share_of_stock']))
    //    $q_data[$row_5['itm_id']]['share_of_stock']+=$row_5['vials'];
    // if(!empty($row_5['vials']))
    // $q_data[$row_5['itm_id']]['stock_of_funding_sources'][$row_5['funding_source_name']] = $row_5['vials'];
}
//echo '<pre>';print_r($itm_arr);print_r($min_expiry);exit;
//getting shipments data 
//    print_r($provincial_soh_stk_wise);
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
                shipments.shipment_date > '$from_date' 
                AND shipments.status NOT IN ('Cancelled','Received')

            ";
if (empty($funding_source) || $funding_source == '' || $is_pwd) {
    //if (!empty($funding_stks))
        $qry_6 .= "AND shipments.stk_id  in  (" . implode(',', $funding_stks) . ") ";
}
else {
    $qry_6 .= "AND shipments.stk_id = $funding_source ";
}
$qry_6 .= "                            
            GROUP BY
                shipments.pk_id,
                itminfo_tab.itm_id
                
                    ";
//echo $qry_6;
//exit;

$res_6 = mysql_query($qry_6);
$pipeline_detail_arr = array();
while ($row_6 = mysql_fetch_assoc($res_6)) {
    $pipeline_detail_arr[$row_6['itm_id']][] = $row_6;
    $s_q = isset($row_6['shipment_quantity']) ? $row_6['shipment_quantity'] : '0';
    $r_q = isset($row_6['received_qty']) ? $row_6['received_qty'] : '0';
    $remaining_q = $s_q - $r_q;
    if (empty($pipeline_arr[$row_6['itm_id']][$from_date]))
        $pipeline_arr[$row_6['itm_id']][$from_date] = 0;
    $pipeline_arr[$row_6['itm_id']][$from_date] += $remaining_q;
}

//echo '<pre>closing:';print_r($closing_bal);echo ',prov';print_r($provincial_soh);echo ',q ';print_r($q_data);
//echo '<pre>';print_r($pipeline_arr);print_r($pipeline_detail_arr);

$clr = '#26C281';
?>
<!--height:750px;overflow: auto;-->
<div class="" style="">
    <table width="100%" border="1" class="table table-condensed " bordercolor="grey" style="border:1px solid grey!important;">
        <tr style="background-color: <?= $clr ?>;color:#ffffff;">
            <th rowspan="2" style="text-align:left;vertical-align:middle;color:#fffff;" width="23%">Products</th>
            <th rowspan="2"  style="text-align:center;vertical-align:middle;" width="5%">Unit</th>
            <th rowspan="2" style="text-align:left;vertical-align:middle;color: #ffffff">Status</th>
<?php
echo '<th colspan="2"  style="text-align:center;vertical-align:middle;">' . date('d-M-Y', strtotime($from_date)) . '</th>';
?>
        </tr>

        <tr style="background-color: <?= $clr ?>;color:#ffffff;">

<?php
echo '      <th style="text-align:center;vertical-align:middle;">Quantity</th>
                            <th style="text-align:center;vertical-align:middle;" title="Months of stock">MOS</th>';
?>
        </tr>
            <?php
            $row_count = 1;
//            echo "<pre>";
//            print_r($q_data);
//            print_r($itm_arr);
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
                                summary_province.reporting_date <= '" . $months_list3 . "'

                                AND stakeholder.stk_type_id = 0 
                                AND tbl_locations.ParentID IS NOT NULL
                                $where_summary_prov
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
//        echo $qry_ayc;
//        exit;
//                echo '<pre>';
//                print_r($q_data[$id]['cyp']);
                $res_ayc = mysql_query($qry_ayc);
                $row_ayc = mysql_fetch_assoc($res_ayc);
                $this_prod_ayc = $row_ayc['ayc'];

                $this_prod_cyp = 0;
                if (!empty($q_data[$id]['cyp']))
                    $this_prod_cyp = $q_data[$id]['cyp'];

                //$this_prod_amc=((isset($q_data[$id][$from_date3]['amc']) ? $q_data[$id][$from_date3]['amc'] : '0'));
                if ($row_count % 2 == 0)
                    $row_clr = '#e2e2e2';
                else
                    $row_clr = '';

                $rowspan = ((isset($pipeline_arr[$id][$from_date]) && $pipeline_arr[$id][$from_date] > 0) ? '3' : '');

                echo '<tr style="background-color:' . $row_clr . '">
                    <td rowspan="' . $rowspan . '" style="vertical-align:middle;" title=" ">';
                echo '<a style="color:black;" data-toggle="popover" title="Details" data-trigger="hover" data-placement="right" data-html="true" data-content="<b>AMC</b> : Avg Monthly Consumption calculated on last 12 non zero months = ' . number_format($this_prod_ayc, 2) . ' <br/><br/><br/> ' . (!empty($this_prod_cyp) ? ' <b>CYP</b> : Couple Year Protection = ' . number_format($this_prod_cyp) . '' : '') . '">';
                echo $name;
                echo '</br>';
                //echo '(AMC: ' . number_format($this_prod_amc,2)  . ')</br>';
                echo '(<span style="">AMC</span>**: ' . number_format($this_prod_ayc, 2) . ')';
                if ($this_prod_cyp > 0) {
                    echo '<br/>(<span style="">CYP</span>:' . number_format($this_prod_cyp) . ')';
                }
                $total_cyp += $this_prod_cyp;

//                if(date('Y-m',strtotime($from_date)) == $current_month || date('Y-m',strtotime($from_date)) == $previous_month){
//                    echo '<div class="center"><a data-prod="'.$id.'" style="font-size:10px" class=" hide when_to_order btn green-jungle btn-xs pull-left"><i class="fa fa-angle-down"></i> When to Re-Order ?</a></div>';
//                }

                if ($from_date == date('m')) {
                    $from_date = date('m') - 1;
                }
                $stock_val = 0;
                //$national_q = ((isset($closing_bal[$id]) && $closing_bal[$id] > 0) ? $closing_bal[$id] : 0);
                $national_q = ((isset($closing_bal[$id]) ) ? $closing_bal[$id] : 0);
                $provincial = (isset($provincial_soh[$id][$from_date]['soh']) ? $provincial_soh[$id][$from_date]['soh'] : 0);
                $prov_store = (isset($prov_store_soh[$id][$from_date]) ? $prov_store_soh[$id][$from_date] : 0);
//                echo '<pre>';
//                print_r($national_q);
//                echo 'ndc';
//                print_r($provincial_soh);
//                echo 'jkn';
//                print_r($prov_store);
                //@$stock_val = $national_q + $provincial + $prov_store;
                @$stock_val = $provincial + $prov_store;
                $this_amc = $this_prod_ayc;
                echo '</a>';
                echo '</td>
                    <td rowspan="' . $rowspan . '"  style="text-align:center;vertical-align:middle;">' . $q_data[$id]['unit'] . '</td>
                    
                    <td style="text-align:left;" title="Central Warehouse & Supplies">Stocks at <span style="color:blue;text-decoration: underline;">CW&S</span>
                    ' . ((!empty($min_expiry[$id]) && $provincial > 0) ? '<br/><span style="color:green" onclick="window.open(\'dashboard_fp2020_batches.php?province=' . $province . '&prov_name=' . $prov_name . '&prod_name=' . $name . '&from_date=' . $from_date . '&funding_source=' . $funding_source . '&itm_id=' . $id . '\', \'_blank\', \'scrollbars=1,width=900,height=500\')">Expiring on and after: ' . date('M-Y', strtotime($min_expiry[$id])) . '</span>' : '') . '
                    '.((!empty($min_expiry[$id]) && $provincial > 0)?'':'<br/>').'<br/><br/><br/>USAID Supported stock allocated by PPW
                    </td>
                   ';


                if (empty($this_prod_ayc) || $this_prod_ayc == 0)
                {
                    $mos = 0;$mos_usaid = 0;
                }
                else
                {
                    $mos = (isset($stock_val) ? $stock_val : '0') / $this_prod_ayc;
                    $mos_usaid = ((isset($national_q) && $national_q>0) ? $national_q : '0') / $this_prod_ayc;
                }

                $data_content = "";
                $t_mos = (!empty($this_prod_ayc) && $this_prod_ayc > 0) ? number_format(($provincial / $this_prod_ayc), 2) : 0;
                $data_content .= '<b>Provincial Stock at CW&S : ' . number_format($provincial) . '(' . $t_mos . ')</b>';
                if (!empty($provincial_soh_stk_wise[$id][$from_date])) {
                    foreach ($provincial_soh_stk_wise[$id][$from_date] as $funding_src => $val2) {
                        $t_mos = (!empty($this_prod_ayc) && $this_prod_ayc > 0) ? number_format(($val2 / $this_prod_ayc), 2) : 0;
                        $data_content .= ' <br/> - ' . $funding_src . ': ' . number_format($val2) . '(' . $t_mos . ')';
                    }
                }

                if (!empty($prov_store)) {
                    $data_content .= ' <br/>';
                    $data_content .= '';
                    if ($province == 1 || empty($province)) {
                        $t_mos = (!empty($this_prod_ayc) && $this_prod_ayc > 0) ? number_format(($prov_store / $this_prod_ayc), 2) : 0;
                        $data_content .= ' <br/><b>Provincial stock at MSD Punjab: ' . number_format($prov_store) . '(' . $t_mos . ')</b>';
                    }
                }

                if (empty($funding_source) || $funding_source == '' || $is_pwd) {
                    $t_mos = (!empty($this_prod_ayc) && $this_prod_ayc > 0) ? number_format(($national_q / $this_prod_ayc), 2) : 0;
                    $data_content .= ' <br/><b>USAID Supported Allocation: ' . number_format($national_q) . '(' . $t_mos . ')</b>';
                }
                
                $data_content .= '  <br/><br/><br/><b>Note:</b><br/> USAID supported stock allocated by PPW vide F. No. 12-1/2015/PPW/P&FC letters dated 10 Nov 2016 & 28 Dec 2017 and 17 Jan 2019 is displayed for your information and not depicting the actual stock.';
                //else $data_content='';
                echo '  <td style="text-align:right"> <a data-toggle="popover" title="Stock Breakdown (with MOS)" data-trigger="hover" data-placement="left" data-html="true" data-content="' . $data_content . '"><i style="color:black !important;" class="fa fa-table"></i></a>
                    ' . (number_format($stock_val)) . 
                    '<br/><br/><br/><br/>'. number_format($national_q).'</td>
                    <td style="text-align:right"><div class="pull-right">' . (($mos > 0) ? number_format($mos, 2) : '0') . '</div>';

                echo '<div style="width:10px; height:12px; background-color:' . get_mos_color($mos, $mos_arr) . ';border:1px solid green;" title="">';
                //echo '<br/><br/><br/><br/>'. number_format($mos_usaid,2).'';
                echo '</div>';
                
                echo '';
                echo '</td>';



                echo '</tr> ';




                $pipeline_val = 0;
                $pipeline_val = (isset($pipeline_arr[$id][$from_date]) ? $pipeline_arr[$id][$from_date] : '0');

                if (empty($pipeline_val) || empty($this_prod_ayc) || $this_prod_ayc == 0)
                    $mos2 = 0;
                else
                    $mos2 = (isset($pipeline_val) ? $pipeline_val : '0') / $this_prod_ayc;

                if (!empty($pipeline_val) && $pipeline_val > 0) {
                    echo '<tr style="background-color:' . $row_clr . '">
                        <td style="text-align:left">Pipeline</td>';
                    echo '  <td style="text-align:right">';
                    if (!empty($pipeline_val) && $pipeline_val > 0) {
                        $data_content2 = "";
                        $data_content2 .= '<b>Shipments in pipeline </b>';
                        foreach ($pipeline_detail_arr[$id] as $k => $pipe_data) {
                            $s_q = isset($pipe_data['shipment_quantity']) ? $pipe_data['shipment_quantity'] : '0';
                            $r_q = isset($pipe_data['received_qty']) ? $pipe_data['received_qty'] : '0';
                            $remaining_q = $s_q - $r_q;
                            $data_content2 .= '<br/>*' . $pipe_data['wh_name'] . ' ' . date('Y/M/d', strtotime($pipe_data['shipment_date'])) . ': ' . number_format($remaining_q) . '';
                        }
                        $data_content2 .= ' <br/>';
                        echo '<a data-toggle="popover" title="Pipeline Details" data-trigger="hover" data-placement="left" data-html="true" data-content="' . $data_content2 . '"><i style="color:black !important;" class="fa fa-table"></i></a>';
                    }
                    echo ' ' . ( isset($pipeline_val) ? number_format($pipeline_val) : '0');
                    echo '</td>
                            <td style="text-align:right"><div class="pull-right">' . (($mos2 > 0) ? number_format($mos2, 2) : '0') . '</div>';
                    echo '</td>';
                    echo '</tr> ';


                    echo '<tr style="background-color:' . $row_clr . '">
                        <td style="text-align:left"><b>Total</b></td>';

                    $tot_val = 0;
                    $tot_val += ((isset($closing_bal[$id][$from_date]) && $closing_bal[$id][$from_date] > 0) ? $closing_bal[$id][$from_date] : '0') + (isset($provincial_soh[$id][$from_date]['soh']) ? $provincial_soh[$id][$from_date]['soh'] : '0');

                    $tot_val += (isset($pipeline_arr[$id][$from_date]) ? $pipeline_arr[$id][$from_date] : '0');
                    $tot_val += (isset($prov_store_soh[$id][$from_date]) ? $prov_store_soh[$id][$from_date] : '0');

                    if (empty($tot_val) || empty($this_prod_ayc) || $this_prod_ayc == 0)
                        $mos3 = 0;
                    else
                        $mos3 = (isset($tot_val) ? $tot_val : '0') / $this_prod_ayc;


                    echo '<td style="text-align:right" class=" "><b>' . (number_format($tot_val)) . '</b></td>
                        <td style="text-align:right"><div class="pull-right"><b>' . (($mos3 > 0) ? number_format($mos3, 2) : '0') . '</b></div>';
                    echo '<div style="width:10px; height:12px; background-color:' . get_mos_color($mos3, $mos_arr) . ';border:1px solid green;" title=""></div>';
                    echo '</td>';

                    echo '</tr> ';
                }//end of if pipeline is not zero
                else {
                    $mos3 = $mos;
                }
                //forecast calculations START 

                $min_delivery_time = 6;             //no of months it takes to deliver
                $min_stock_months = 12;            //minimum mos to be maintained
                $sufficiency_upper_level = 18;      //upper slab of sufficiency level
                $minimum_ordering_slab = 6;         //minimum stock to be ordered , suggested to be equal to min delivery time
                $reorder_after_months = round($mos3) - ($min_delivery_time + $min_stock_months);
                $reorder_after_months = max($reorder_after_months, 0);
                $reorder_date = date('Y-m-d', strtotime("+" . $reorder_after_months . " months" . date("Y-m-d")));

                if (round($mos3) > $sufficiency_upper_level)
                    $months_qty_to_order = $minimum_ordering_slab;
                elseif (round($mos3) > $min_delivery_time)
                    $months_qty_to_order = $sufficiency_upper_level - (round($mos3) - $min_delivery_time);
                else
                    $months_qty_to_order = $sufficiency_upper_level;
                $reorder_qty = $months_qty_to_order * $this_prod_ayc;


                $row_cls = $date_txt = "";
                if ($reorder_after_months <= 0)
                    $row_cls = " ";

                if ($reorder_date == date('Y-m-d')) {
                    $date_txt = "  ASAP ";
                    $b_cls = 'badge badge-danger';
                    $b_style = '';
                } else {
                    if ($reorder_after_months >= 12) {
                        $yrs = $reorder_after_months / 12;
                        if ($yrs > 1)
                            $date_txt_2 = ' <b>' . round($yrs) . ' years</b> ';
                        else
                            $date_txt_2 = ' <b>' . round($yrs) . ' year</b> ';
                    }
                    else {
                        if ($reorder_after_months > 1)
                            $date_txt_2 = ' <b>' . $reorder_after_months . ' months</b> ';
                        else
                            $date_txt_2 = ' <b>' . $reorder_after_months . ' month</b> ';
                    }
                    $date_txt = " after " . $date_txt_2 . ' ';
                    $b_cls = ' badge ';
                    $b_style = 'background-color:' . (empty($row_clr) ? 'white' : $row_clr) . ' !important;color:black !important;';
                }

                if (date('Y-m', strtotime($from_date)) == $current_month || date('Y-m', strtotime($from_date)) == $previous_month) {
                    echo '<tr prod="' . $id . '" class="reorder ' . $row_cls . '" style="display:none1;background-color:' . $row_clr . '"> ';


                    $txt = '';
                    $txt .= 'Order <b>' . number_format($reorder_qty) . ' ' . $name . ' ' . $q_data[$id]['unit'] . '</b> ';
                    //$txt_part2 = ' Which will be sufficient for '.$months_qty_to_order.' months.';
                    $txt_part2 = ' to maintain the desired stock level';
                    echo '<td class="left grey " colspan="5"  >';
                    //echo '<span style="font-family:Comic Sans MS" class="pull-left">'.$txt.'</span>';
                    echo '<span class=" ' . $b_cls . ' pull-right1" style="' . $b_style . '"  >' . $txt . ' ' . $date_txt . ' ' . $txt_part2 . '</span></td> ';
                    echo '</tr> ';
                }

                //forecast calculations END 

                $row_count++;
            }
            ?>
    </table>

    <div class="row "  style=" ">
        <div class="col-md-12 "  style=" ">
            <div class="alert alert-info">Total CYP : <?= number_format($total_cyp) ?></div> 
        </div>
    </div>

    <div class="col-md-12 "  style=" ">

        <div class="col-md-12 left" style=" font-size: 10px;padding-left: 0;"><span> MOS Legend :</span>

        <?php
        foreach ($mos_arr as $k => $legend) {
            echo '
            <div style="display:inline-block;width:12px; height:12px; vertical-align: bottom; background-color:' . $legend['color'] . ';margin-left:20px;border:1px solid green;"></div>
            <div style="display:inline-block;margin-left:5px;">' . $legend['fullname'] . ' (' . $legend['from'] . ' - ' . $legend['to'] . ')</div>';
        }
        ?>

        </div>

    </div>
</div>
<script>
    $('[data-toggle="popover"]').popover();
</script>