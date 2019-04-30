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

$qry_1 = "  
                                        SELECT
                                        tbl_warehouse.dist_id,
                                        stakeholder_item.stk_item as itm,
                                                COUNT(
                                                        DISTINCT tbl_warehouse.wh_id
                                                ) AS totalWH
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
                                        tbl_warehouse.prov_id,
                                        stakeholder_item.stk_item
                                            ";
//echo $qry_1;exit;
$res_1 = mysql_query($qry_1);
$total_sdps = array();
while ($row_1 = mysql_fetch_array($res_1)) {
    $total_sdps[$row_1['dist_id']][$row_1['itm']] = $row_1['totalWH'];

    if (!isset($total_sdps['all'][$row_1['itm']]))
        $total_sdps['all'][$row_1['itm']] = 0;
    $total_sdps['all'][$row_1['itm']] += $row_1['totalWH'];
}


$qry = "SELECT
                                                    tbl_warehouse.prov_id,
tbl_warehouse.stkid,
tbl_hf_data.item_id,
itminfo_tab.itm_name,
tbl_hf_data.pk_id,
tbl_hf_data.closing_balance,
tbl_hf_data.avg_consumption,
tbl_hf_data.reporting_date,
tbl_warehouse.dist_id,
tbl_warehouse.wh_name,
district.LocName AS district
                                                        FROM
                                                                tbl_warehouse
                                                        INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                                        INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                                        INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                                                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_locations AS district ON tbl_warehouse.dist_id = district.PkLocID                                                         
WHERE
                                                                stakeholder.lvl = 7
                                                        AND tbl_warehouse.prov_id IN ($prov)
                                                            AND tbl_warehouse.stkid IN ($stk)

                                                        AND tbl_hf_data.reporting_date BETWEEN '" . $from_date . "' and '" . $to_date . "'

                                                        AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                                                        
                                                        AND itminfo_tab.itm_category = 1
                                                        AND itminfo_tab.itm_id IN($itm_arr_request)
                                                     

                                            ";
//    echo $qry;exit;
$qryRes = mysql_unbuffered_query($qry);
$xc = 1;
$unk_arr = $so_arr2 = $so_arr = $us_arr = $sat_arr = $os_arr = $dist_name = $dist_itm = array();


while ($row = mysql_fetch_assoc($qryRes)) {
    //echo '<br/>'.$xc;

    $mos = '';
    if (!empty($row['avg_consumption']) && $row['avg_consumption'] > 0)
        $mos = $row['closing_balance'] / $row['avg_consumption'];

    $xc++;
    //$itm_arr[$row['item_id']] = $row['itm_name'];
    //$rep_year = date('Y',$row['reporting_date']);
    $rep_month = date('Y-m-01', strtotime($row['reporting_date']));


    if (empty($so_arr[$row['dist_id']][$row['item_id']][$rep_month]))
        $so_arr[$row['dist_id']][$row['item_id']][$rep_month] = 0;
    if (empty($so_arr2[$row['item_id']][$rep_month]))
        $so_arr2[$row['item_id']][$rep_month] = 0;
    $dist_name[$row['dist_id']][$row['item_id']] = $row['district'];
    $dist_itm[$row['dist_id']] = $row['district'];

    //if( $row['closing_balance'] <= '0' )
    if ($mos <= '0.5') {
        $so_arr[$row['dist_id']][$row['item_id']][$rep_month] += 1;

        $so_arr2[$row['item_id']][$rep_month] += 1;
    }
}
$w_clause = "";
if (!empty($stk))
    $w_clause .= " stakeholder_item.stkid in (" . $stk . ")  ";

if (!empty($itm_arr_request))
    $w_clause .= " AND itminfo_tab.itm_id in (" . $itm_arr_request . ")  ";

$qry_1 = "  SELECT
                                                    itminfo_tab.itmrec_id,
                                                    itminfo_tab.itm_name,
                                                    itminfo_tab.itm_id
                                                FROM
                                                    itminfo_tab
                                                    INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
                                                WHERE
                                                     
                                                    $w_clause
                                                        
                                                ORDER BY
                                                    itminfo_tab.frmindex ASC
                                            ";
//echo $qry_1;exit;
$res_1 = mysql_query($qry_1);
$itm_arr = array();
while ($row_1 = mysql_fetch_array($res_1)) {
    $itm_arr[$row_1['itm_id']] = $row_1['itm_name'];
}



$q_reporting = "SELECT
                                                                    tbl_warehouse.stkid,
                                                                    COUNT(
                                                                            DISTINCT tbl_warehouse.wh_id
                                                                    ) AS reportedWH,
                                                                    
                                                                    tbl_warehouse.dist_id,
                                                                    tbl_hf_data.item_id,
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
                                                            AND tbl_hf_data.reporting_date BETWEEN '" . $from_date . "' and '" . $to_date . "'
                                                            $where_clause
                                                                AND tbl_hf_data.item_id IN ($itm_arr_request)
                                                            GROUP BY
                                                                    tbl_warehouse.dist_id,
                                                                    tbl_hf_data.item_id,tbl_hf_data.reporting_date";
//    echo $q_reporting;exit;
$res_reporting = mysql_query($q_reporting);
$reporting_wh_arr = $dist_arr = array();
$total_reporting_wh = 0;
//$dist_arr['all'] = 'Aggregated';
while ($row = mysql_fetch_assoc($res_reporting)) {
    $dist_arr[$row['dist_id']] = $row['LocName'];


    if (empty($reporting_wh_arr[$row['dist_id']][$row['item_id']][$row['reporting_date']]))
        $reporting_wh_arr[$row['dist_id']][$row['item_id']][$row['reporting_date']] = 0;
    if (empty($reporting_wh_arr2[$row['item_id']]))
        $reporting_wh_arr2[$row['item_id']] = 0;

    $reporting_wh_arr[$row['dist_id']][$row['item_id']][$row['reporting_date']] += $row['reportedWH'];

    $reporting_wh_arr2[$row['item_id']] += $row['reportedWH'];
    $total_reporting_wh += $row['reportedWH'];
}
//echo '<pre>';
//print_r($reporting_wh_arr);
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
                        foreach ($months_list as $k => $v) {
                            echo '<th colspan="1" class="months_td" style="text-align:center !important;">' . date('M Y', strtotime($v)) . '</th>';
                        }
                        ?>
                        <th colspan="4" style="text-align:center !important;">Average</th>

                    </tr>
                    <tr>
                        <th rowspan="">#</th>
                        <th rowspan="" align="center">Product</th>
                         
                        <?php
                        foreach ($months_list as $k => $v) {
                            //echo '<td align="center" class="so_n">SO</td>';
                            echo '<td align="center" class="so_r">SO Rate</td>';
                            //echo '<td align="center" class="reported_n">Reported</td>';
                            //echo '<td align="center" class="total_n">Total</td>';
                            //echo '<td align="center" class="reporting_r">RR</td>';
                        }
                        ?>
<!--                                        <td align="center">Total SO</td>-->
                        <td align="center">SO Rate</td>
<!--                                        <td align="center">Total reported</td>
                        <td align="center">Total SDPs</td>-->

                    </tr>
                    <?php
//                    echo "<pre>";
//                    print_r($reporting_wh_arr);
                    $rep_rate = $so_rate = $so_rate_national = $so_rate_provincial = $so_rate_yearly = $so_rate_yearly_product_wise = array();
                    foreach ($so_arr as $dist_id => $dist_data) {
                        $month_total_so = $month_total_sdp = $month_total_reported = array();
                        
                       echo' <tr align="left" class="so_r" style="background-color:pink;"><td colspan="'.(sizeof($months_list)+3).'">'.$dist_itm[$dist_id].'</td></tr>';
                        
                        $c = 1;
                        foreach ($itm_arr as $itm_id => $itm_name) {
                            $prod_total_so = $prod_total_sdp = $prod_total_reported = 0;

                            echo '<tr  class="">';
                            echo '<td>' . $c++ . '</td>';
                            echo '<td>' . $itm_name . '</td>';
                            
                            foreach ($months_list as $k => $v) {
                                @$master_total = @$total_sdps[$dist_id][$itm_id];
                                $disabled_fac = (isset($disabled_count[$dist_id][$itm_id][$v]) ? $disabled_count[$dist_id][$itm_id][$v] : 0);
                                $to_be_reported = $master_total - $disabled_fac;

                                $val = (isset($dist_data[$itm_id][$v]) ? $dist_data[$itm_id][$v] : 0);

                                if (@$reporting_wh_arr[$dist_id][$itm_id][$v] > 0 && isset($val))
                                    $so_r = ($val * 100) / @$reporting_wh_arr[$dist_id][$itm_id][$v];
                                else
                                    $so_r = 0;

                                if ($to_be_reported > 0 && isset($reporting_wh_arr[$dist_id][$itm_id][$v]))
                                    $r_r = ($reporting_wh_arr[$dist_id][$itm_id][$v] * 100) / $to_be_reported;
                                else
                                    $r_r = 0;
                                $so_rate[$dist_id][$itm_id][$v] = $so_r;
                                $rep_rate[$dist_id][$itm_id][$v] = $r_r;
                                //echo '<td align="right" class="danger so_n">'.number_format($val).'</td>';
//                                 echo '<td align="right" class="so_r">' . $dist_id . '</td>';
                                echo '<td align="right" class="so_r">' . number_format($so_r, 2) . '</td>';
                                //echo '<td align="right" class="reported_n">'.number_format($reporting_wh_arr[$dist_id][$itm_id][$v]).'</td>';
                                //echo '<td align="right" class="total_n" title="All:'.$master_total.',Disabled:'.$disabled_fac.'">'.number_format($to_be_reported).'</td>';
                                //echo '<td align="right" class="reporting_r">'.number_format($r_r,2).'</td>';

                                $prod_total_so += $val;
                                $prod_total_sdp += $to_be_reported;
                                $prod_total_reported += (!empty($reporting_wh_arr[$dist_id][$itm_id][$v]) ? $reporting_wh_arr[$dist_id][$itm_id][$v] : 0);

                                @$month_total_so[$v] += $val;
                                @$month_total_sdp[$v] += $to_be_reported;
                                @$month_total_reported[$v] += $reporting_wh_arr[$dist_id][$itm_id][$v];

                                $b = explode('-', $v);
                                $year = $b[0];
                                if ($dist_id == 'all')
                                    @$so_rate_yearly_product_wise[$year][$itm_id] += $so_r;
                            }
                            //echo '<td align="right" class="info">'.number_format($prod_total_so).'</td>';
                            $avg = $prod_total_so * 100 / $prod_total_reported;
                            echo '<td align="right" class="info">' . number_format($avg, 2) . '</td>';
                            //echo '<td align="right" class="info">'.number_format($prod_total_reported).'</td>';
                            //echo '<td align="right" class="info">'.number_format($prod_total_sdp).'</td>';

                            echo '</tr>';
                        }

//                        echo '<tr class="' . $pro_class . ' warning" style="' . $pro_style . '">';
//                        echo '<tr>'.$dist_name[$dist_id][$itm_id].'</tr>';
                        //echo '<td align="right" class="">'.number_format(array_sum($month_total_so)).'</td>';
//                        echo '<td align="right" class="">' . number_format(array_sum($month_total_so) * 100 / array_sum($month_total_reported), 2) . '</td>';
                        //echo '<td align="right" class="">'.number_format(array_sum($month_total_reported)).'</td>';
                        //echo '<td align="right" class="">'.number_format(array_sum($month_total_sdp)).'</td>';
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>