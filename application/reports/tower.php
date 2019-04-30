<?php
/**
 * non_report
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$report_id = "TOWER";
//action page 
$actionpage = "tower.php";
$rptId = "tower";
$parameters = "TSP";
$date_from = date('01/m/Y', strtotime("-6 months"));
$date_to = date('01/m/Y', strtotime("+5 months"));

$report_title = "Supply Chain Control Tower";
if (isset($_POST['go'])) {
    //from date
    $date_from = isset($_POST['date_from']) ? mysql_real_escape_string($_POST['date_from']) : '';
    $date_from1 = dateToDbFormat($date_from);
    //to date
    $date_to = isset($_POST['date_to']) ? mysql_real_escape_string($_POST['date_to']) : '';
    $date_to1 = dateToDbFormat($date_to);
    //selected province
    $sel_prov = mysql_real_escape_string($_POST['province']);

    $sel_wh = mysql_real_escape_string($_POST['warehouse']);
    $sel_dist = mysql_real_escape_string($_POST['district']);
    $sel_stk = mysql_real_escape_string($_POST['stk_sel']);
    $selItem = mysql_real_escape_string($_POST['itm_id']);
    //    $hf_id;
    //    if ($hf_id != 0) {
    //        $and_hf = "  tbl_warehouse.hf_type_id=$hf_id
    //                AND";
    //    } else if ($hf_id == 0) {
    //        $and_hf = '';
    //    }
}
?>
<!-- END HEAD -->
<link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
<style>
    .table-scroll {
        position:relative;
        margin:auto;
        overflow:hidden;
        border:1px solid #000;
    }
    .table-wrap {
        overflow:auto;
    }
    .table-scroll table {
        width:100%;
        margin:auto;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-scroll th, .table-scroll td {
        padding:5px 10px;
        border:1px solid #000;
        background:#fff;
        white-space:nowrap;
        vertical-align:top;
    }
    .table-scroll thead, .table-scroll tfoot {
        background:#f9f9f9;
    }
    .clone {
        position:absolute;
        top:0;
        left:0;
        pointer-events:none;
    }
    .clone th, .clone td {
        visibility:hidden
    }
    .clone td, .clone th {
        border-color:transparent
    }
    .clone tbody th {
        visibility:visible;
        color:red;
    }
    .clone .fixed-side {
        border:1px solid #000;
        background:#eee;
        visibility:visible;
    }
    .clone thead, .clone tfoot{background:transparent;}
</style>
</head><!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <h3 class="page-title row-br-b-wp"> <?php echo $report_title; ?></h3>
                <!--                <div class="row">
                                    <div class="col-md-12">
                                        <div class="widget" data-toggle="collapse-widget">
                                            <div class="widget-head">
                                                <h3 class="heading">Filter by</h3>
                                            </div>
                                            <div class="widget-body">
                                                //<?php include('sub_dist_form.php'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>  -->
                <div class="row">
                    <div class="col-md-12">
                        <form name="searchfrm" id="searchfrm" action="<?php $actionpage ?>" method="post">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Filter by</h3>
                                </div>
                                <div class="widget-body">
                                    <div class="row">                                    
                                        <div class="col-md-3">
                                            <label class="control-label">Stakeholder</label>
                                            <div class="form-group">
                                                <select name="stk_sel" id="stk_sel" class="input-medium select2me">
                                                    <?php
                                                    //select query
                                                    //gets
                                                    //stakeholder id
                                                    //stakeholder name
                                                    $querystk = "SELECT DISTINCT
																				stakeholder.stkid,
																				stakeholder.stkname
																			FROM
																				tbl_warehouse
																			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
																			INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
																			WHERE
																				stakeholder.stk_type_id IN (0, 1)
																			ORDER BY
																				stakeholder.stkorder ASC";
                                                    //query result
                                                    $rsstk = mysql_query($querystk) or die();
                                                    //fetch result
                                                    while ($rowstk = mysql_fetch_array($rsstk)) {
                                                        ?>
                                                        <option value="<?php echo $rowstk['stkid']; ?>" <?php echo ($sel_stk == $rowstk['stkid']) ? 'selected=selected' : '' ?>><?php echo $rowstk['stkname']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label">Province</label>
                                            <div class="form-group">
                                                <select name="province" id="province" class="input-medium select2me" data-placeholder="Select..." required="required">
                                                    <option value="">Select</option>
                                                    <?php
                                                    $qry = "SELECT
																			tbl_locations.PkLocID,
																			tbl_locations.LocName
																		FROM
																			tbl_locations
																		WHERE
																			tbl_locations.LocLvl = 2
																		AND tbl_locations.ParentID IS NOT NULL";
                                                    //query result
                                                    $qryRes = mysql_query($qry);
                                                    //fetch result
                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                        ?>
                                                        <option value="<?php echo $row['PkLocID']; ?>" <?php echo ($sel_prov == $row['PkLocID']) ? 'selected=selected' : '' ?>><?php echo $row['LocName']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-md-3" id="districts">
                                            <label class="control-label">District</label>
                                            <div class="form-group">
                                                <select name="district" id="district" class="input-medium  select2me" data-placeholder="Select..." >
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="stores">
                                            <label class="control-label">SDPs</label>
                                            <div class="form-group">
                                                <select name="warehouse" id="warehouse" class="input-medium select2me">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">Start Date</label>
                                                <div class="form-group">
                                                    <input type="text" name="date_from" id="date_from" readonly="readonly" class="form-control input-medium" value="<?php echo $date_from; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label">End Date</label>
                                                <div class="form-group">
                                                    <input type="text" name="date_to" id="date_to" readonly="readonly" class="form-control input-medium" value="<?php echo $date_to; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" >
                                                <label class="control-label">Product</label>
                                                <div class="form-group">
                                                    <select name="itm_id" id="itm_id" class="form-control input-medium" required>
                                                        <option value="">Select</option>
                                                        <?php
                                                        //Query for item
                                                        //gets 
                                                        //itm_name
                                                        //itm_id
                                                        $queryItem = "SELECT
                                        itminfo_tab.itm_name,
                                        itminfo_tab.itm_id
                                    FROM
                                        stakeholder_item
                                        INNER JOIN itminfo_tab ON stakeholder_item.stk_item = itminfo_tab.itm_id
                                    WHERE
                                        stakeholder_item.stkid = 1
                                        AND itminfo_tab.itm_category = 1
                                    ORDER BY
                                        itminfo_tab.frmindex ASC";
                                                        //Result
                                                        $rsprov = mysql_query($queryItem) or die();
                                                        while ($rowItem = mysql_fetch_array($rsprov)) {
                                                            if ($selItem == $rowItem['itm_id']) {
                                                                $sel = "selected='selected'";
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            ?>
                                                            <?php //Populate itm_id combo ?>
                                                            <option value="<?php echo $rowItem['itm_id']; ?>" <?php echo $sel; ?>><?php echo $rowItem['itm_name']; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label">&nbsp;</label>
                                            <input type="submit" name="go" id="go" value="GO" class="btn btn-primary input-sm" style="display:block" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php
                if (isset($_POST['go'])) {
                    ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="col-md-1 right" style="background-color: #00FF99">
                                &nbsp;
                            </div>
                            <div class="col-md-3 left">
                                Previous Reported Months
                            </div>
                            <div class="col-md-1 right" style="background-color: #6699FF">
                                &nbsp;
                            </div>
                            <div class="col-md-3 left">
                                Last Reported Month
                            </div>
                            <div class="col-md-1 right" style="background-color: #CC99FF">
                                &nbsp;
                            </div>
                            <div class="col-md-3 left">
                                Projected Months
                            </div>
                        </div>
                    </div>
                    <br>
                    <div id="table-scroll" class="table-scroll">
                        <div class="table-wrap">
                            <?php
                            $qry = "SELECT
	tbl_hf_data.reporting_date,
	tbl_hf_data.opening_balance,
	tbl_hf_data.received_balance,
	tbl_hf_data.issue_balance,
	tbl_hf_data.closing_balance,
	tbl_hf_data.adjustment_positive,
	tbl_hf_data.adjustment_negative,
	tbl_hf_data.avg_consumption AMC,
        itminfo_tab.extra,
	ROUND(
		tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption,
		2
	) MOS,
	ROUND(
		tbl_hf_data.issue_balance * itminfo_tab.extra,
		2
	) CYP
FROM
	tbl_hf_data
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
WHERE
	 tbl_hf_data.reporting_date BETWEEN '$date_from1'
AND '$date_to1'
AND tbl_hf_data.item_id = $selItem
AND tbl_warehouse.prov_id = $sel_prov
AND tbl_warehouse.stkid = $sel_stk
AND tbl_warehouse.dist_id = $sel_dist
AND tbl_hf_data.warehouse_id = $sel_wh
GROUP BY
	tbl_hf_data.reporting_date
ORDER BY
        tbl_hf_data.reporting_date ASC";
                            //echo $qry;
                            $qryRes = mysql_query($qry);
                            if (mysql_num_rows($qryRes) > 0) {
//                                    echo 'into if';
//                                    echo mysql_num_rows($qryRes);
                                ?>

                                <table id="myTable" cellspacing="0" align="center" class="table table-bordered table-condensed main-table">
                                    <?php
//                                    for ($i = 1; $i <= 6; $i++) {
//                                        $additional_months[$i] = date('Y-m', strtotime("$i month", strtotime($date_to)));
//                                    }
//                                    print_r($additional_months);
//                                    $ts1 = strtotime($date_from);
//                                    $ts2 = strtotime($date_to);
//
//                                    $year1 = date('Y', $ts1);
//                                    $year2 = date('Y', $ts2);
//
//                                    $month1 = date('m', $ts1);
//                                    $month2 = date('m', $ts2);
//
//                                    $months_diff = (($year2 - $year1) * 12) + ($month2 - $month1);
//                                    print_r($months_diff);



                                    while ($row = mysql_fetch_assoc($qryRes)) {
                                        $cyp_factor = $row['extra'];
                                        $months_color[$row['reporting_date']] = '#00FF99';
                                        $months_arr[$row['reporting_date']] = $row['reporting_date'];
                                        $positive_stock['s_r'][$row['reporting_date']] += $row['received_balance'];
                                        $positive_stock['o_b'][$row['reporting_date']] += $row['opening_balance'];
                                        $positive_stock['a_p'][$row['reporting_date']] += $row['adjustment_positive'];
                                        $negative_stock['issue'][$row['reporting_date']] += $row['issue_balance'];
                                        $negative_stock['a_n'][$row['reporting_date']] += $row['adjustment_negative'];
                                        $amc['AMC'][$row['reporting_date']] += $row['AMC'];
                                        $mos['MOS'][$row['reporting_date']] += $row['MOS'];
                                        $cyp['CYP'][$row['reporting_date']] += $row['CYP'];

                                        $opening_b['total'] += $row['opening_balance'];
                                        $stock_receive['total'] += $row['received_balance'];
                                        $stock_issue['total'] += $row['issue_balance'];
                                        $adj_pos['total'] += $row['adjustment_positive'];
                                        $adj_neg['total'] += $row['adjustment_negative'];
                                        $amc_total['total'] += $row['AMC'];
                                        $mos_total['total'] += $row['MOS'];
                                        $cyp_total['total'] += $row['CYP'];

                                        $total_stock[$row['reporting_date']] += ($row['opening_balance'] + $row['received_balance'] + $row['adjustment_positive']);
                                        $negative_total_stock[$row['reporting_date']] += ($row['issue_balance'] + $row['adjustment_negative']);
                                    }

                                    foreach ($months_arr as $date) {
                                        $curDate = strtotime($date);
                                        if ($curDate > $mostRecent) {
                                            $mostRecent = $curDate;
                                        }
                                    }

                                    $months_color[date("Y-m-01", $mostRecent)] = '#6699FF';

                                    //echo $date_from1;
                                    //echo date('Y-m-d', $mostRecent);

                                    $fromDate = new DateTime($date_from1);
                                    $toDate = new DateTime(date('Y-m-d', $mostRecent));

                                    //echo $toDate->diff($fromDate)->m+2;
                                    $months_diff = $toDate->diff($fromDate)->m + 1 + ($toDate->diff($fromDate)->y * 12);
                                    //echo $months_diff;
                                    //Add additional months
                                    $start = $month = $mostRecent;
                                    $end = strtotime($date_to1);
                                    while ($month < $end) {
                                        $month = strtotime("+1 month", $month);
                                        $additional_months[] = date('Y-m', $month);
                                    }

                                    //print_r($additional_months);

                                    foreach ($additional_months as $key => $value) {
                                        if ($months_diff > 3) {
                                            $months_diff = 3;
                                        }

                                        $value = $value . '-01';
                                        $months_color[$value] = '#CC99FF';

                                        $months_arr[$value] = $value;
                                        $positive_stock['s_r'][$value] = ($positive_stock['s_r'][date("Y-m-d", strtotime("-1 months", strtotime($value)))] + $positive_stock['s_r'][date("Y-m-d", strtotime("-2 months", strtotime($value)))] + $positive_stock['s_r'][date("Y-m-d", strtotime("-3 months", strtotime($value)))]) / $months_diff;
                                        $positive_stock['o_b'][$value] = ($positive_stock['o_b'][date("Y-m-d", strtotime("-1 months", strtotime($value)))] + $positive_stock['o_b'][date("Y-m-d", strtotime("-2 months", strtotime($value)))] + $positive_stock['o_b'][date("Y-m-d", strtotime("-3 months", strtotime($value)))]) / $months_diff;
                                        $positive_stock['a_p'][$value] = ($positive_stock['a_p'][date("Y-m-d", strtotime("-1 months", strtotime($value)))] + $positive_stock['a_p'][date("Y-m-d", strtotime("-2 months", strtotime($value)))] + $positive_stock['a_p'][date("Y-m-d", strtotime("-3 months", strtotime($value)))]) / $months_diff;
                                        $negative_stock['issue'][$value] = ($negative_stock['issue'][date("Y-m-d", strtotime("-1 months", strtotime($value)))] + $negative_stock['issue'][date("Y-m-d", strtotime("-2 months", strtotime($value)))] + $negative_stock['issue'][date("Y-m-d", strtotime("-3 months", strtotime($value)))]) / $months_diff;
                                        //$negative_stock['consumption'][$value] = ($negative_stock['consumption'][date("Y-m-d", strtotime("-1 months", strtotime($value)))] + $negative_stock['consumption'][date("Y-m-d", strtotime("-2 months", strtotime($value)))] + $negative_stock['consumption'][date("Y-m-d", strtotime("-3 months", strtotime($value)))]) / $months_diff;
                                        $negative_stock['a_n'][$value] = ($negative_stock['a_n'][date("Y-m-d", strtotime("-1 months", strtotime($value)))] + $negative_stock['a_n'][date("Y-m-d", strtotime("-2 months", strtotime($value)))] + $negative_stock['a_n'][date("Y-m-d", strtotime("-3 months", strtotime($value)))]) / $months_diff;
                                        
                                        $total_stock[$value] = $positive_stock['o_b'][$value] + $positive_stock['s_r'][$value] + $positive_stock['a_p'][$value];
                                        $negative_total_stock[$value] = $negative_stock['issue'][$value] + $negative_stock['a_n'][$value];
                                        
                                        $amc['AMC'][$value] = ($negative_stock['issue'][$value] + $negative_stock['issue'][date("Y-m-d", strtotime("-1 months", strtotime($value)))] + $negative_stock['issue'][date("Y-m-d", strtotime("-2 months", strtotime($value)))]) / $months_diff;
                                        $mos['MOS'][$value] = ROUND(($total_stock[$value]-$negative_total_stock[$value])/$amc['AMC'][$value], 2);
                                        $cyp['CYP'][$value] = ROUND($negative_stock['issue'][$value]*$cyp_factor, 2);
                                    }

                                    //print_r($positive_stock);
//                                    foreach ($additional_months as $key => $value) {
//                                        $value = $value . '-01';
//                                        $months_arr[$value] = $value;
//                                        $positive_stock['s_r'][$value] = $stock_receive['total'] / $months_diff;
//                                        $positive_stock['o_b'][$value] = $opening_b['total'] / $months_diff;
//                                        $positive_stock['a_p'][$value] = $adj_pos['total'] / $months_diff;
//                                        $negative_stock['issue'][$value] = $stock_issue['total'] / $months_diff;
//                                        $negative_stock['consumption'][$value] = $consumption['total'] / $months_diff;
//                                        $negative_stock['a_n'][$value] = $adj_neg['total'] / $months_diff;
//                                        $amc['AMC'][$value] = $amc_total['total'] / $months_diff;
//                                        $mos['MOS'][$value] = ROUND($mos_total['total'] / $months_diff,2);
//                                        $cyp['CYP'][$value] = ROUND($cyp_total['total'] / $months_diff,2);
//
//                                        $total_stock[$value] = ($opening_b['total'] / $months_diff ) + ($stock_receive['total'] / $months_diff ) + ($adj_pos['total'] / $months_diff );
//                                        $negative_total_stock[$value] = ($stock_issue['total'] / $months_diff) + ($consumption['total'] / $months_diff ) + ($adj_neg['total'] / $months_diff );
//                                    }
//                                    print_r($months_arr);
                                    ?>
                                    <thead>
                                        <tr>
                                            <th class="fixed-side">Type</th>
                                            <?php foreach ($months_arr as $key => $value) {
                                                ?>
                                                <th style="background-color: <?php echo $months_color[$value]; ?>"><?php echo date('M Y', strtotime($key)); ?></th>
                                            <?php }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="fixed-side">Opening Balance</th>
                                            <?php
                                            foreach ($months_arr as $key => $value) {
                                                ?>
                                                <td style="background-color: <?php echo $months_color[$value]; ?>"><?php echo number_format($positive_stock['o_b'][$key]) ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <th class="fixed-side">Stock Received</th>
                                            <?php
                                            foreach ($months_arr as $key => $value) {
                                                ?>
                                                <td style="background-color: <?php echo $months_color[$value]; ?>"><?php echo number_format($positive_stock['s_r'][$key]) ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <th class="fixed-side">Adjustment Positive</th>
                                            <?php
                                            foreach ($months_arr as $key => $value) {
                                                ?>
                                                <td style="background-color: <?php echo $months_color[$value]; ?>"><?php echo number_format($positive_stock['a_p'][$key]) ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <th style="background-color: #eddada;" class="fixed-side">Total Stock</th>
                                            <?php
                                            foreach ($months_arr as $key => $value) {
                                                ?>
                                                <td style="background-color: #eddada;"><b><?php echo number_format($total_stock[$key]) ?></b></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <th class="fixed-side">Issue/Consume</th>
                                            <?php
                                            foreach ($months_arr as $key => $value) {
                                                ?>
                                                <td style="background-color: <?php echo $months_color[$value]; ?>"><?php echo number_format($negative_stock['issue'][$key]) ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <th class="fixed-side">Adjustment Negative</th>
                                            <?php
                                            foreach ($months_arr as $key => $value) {
                                                ?>
                                                <td style="background-color: <?php echo $months_color[$value]; ?>"><?php echo number_format($negative_stock['a_n'][$key]) ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <th style="background-color: #eddada;" class="fixed-side">Available Stock</th>
                                            <?php
                                            foreach ($months_arr as $key => $value) {
                                                ?>
                                                <td style="background-color: #eddada;"><b><?php echo number_format($total_stock[$key] - $negative_total_stock[$key]) ?></b></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>                                        
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="fixed-side">AMC</th>
                                            <?php
                                            foreach ($months_arr as $key => $value) {
                                                ?>
                                                <td style="background-color: <?php echo $months_color[$value]; ?>"><?php echo number_format($amc['AMC'][$key]) ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <th class="fixed-side">MOS</th>
                                            <?php
                                            foreach ($months_arr as $key => $value) {
                                                ?>
                                                <td style="background-color: <?php echo $months_color[$value]; ?>"><?php echo ($mos['MOS'][$key]) ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <th class="fixed-side">CYP</th>
                                            <?php
                                            foreach ($months_arr as $key => $value) {
                                                ?>
                                                <td style="background-color: <?php echo $months_color[$value]; ?>"><?php echo ROUND($cyp['CYP'][$key]) ?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                    </tfoot>
                                </table>
                                <?php
                            } else {
                                echo "No data found";
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <?php
    //include PUBLIC_PATH . "/html/reports_includes.php";
    //include ('combos.php');
    ?>
    <script>
        $(function () {
            showDistricts('<?php echo $sel_prov; ?>', '<?php echo $sel_stk; ?>');
            showStores('<?php echo $sel_dist; ?>');

            $('#province, #stk_sel').change(function (e) {
                $('#district').html('<option value="">All</option>');
                $('#warehouse').html('<option value="">Select</option>');
                showDistricts($('#province').val(), $('#stk_sel').val());
            });
            $('#stk_sel').change(function (e) {
                $('#warehouse').html('<option value="">All</option>');
            });

            $(document).on('change', '#province, #stk_sel, #district', function () {
                showStores($('#district option:selected').val());
            })

            var startDateTextBox = $('#date_from');
            var endDateTextBox = $('#date_to');

            startDateTextBox.datepicker({
                minDate: "-5Y",
                maxDate: "-5M",
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                onClose: function (dateText, inst) {
                    if (endDateTextBox.val() != '') {
                        var testStartDate = startDateTextBox.datepicker('getDate');
                        var testEndDate = endDateTextBox.datepicker('getDate');
                        if (testStartDate > testEndDate)
                            endDateTextBox.datepicker('setDate', testStartDate);
                    } else {
                        endDateTextBox.val(dateText);
                    }
                },
                onSelect: function (selectedDateTime) {
                    endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
                }
            });
            endDateTextBox.datepicker({
                minDate: "+5M",
                maxDate: "+1Y",
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                onClose: function (dateText, inst) {
                    if (startDateTextBox.val() != '') {
                        var testStartDate = startDateTextBox.datepicker('getDate');
                        var testEndDate = endDateTextBox.datepicker('getDate');
                        if (testStartDate > testEndDate)
                            startDateTextBox.datepicker('setDate', testEndDate);
                    } else {
                        startDateTextBox.val(dateText);
                    }
                },
                onSelect: function (selectedDateTime) {
                    startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
                }
            });

        });

        function showDistricts(prov, stk) {
            if (stk != '' && prov != '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'my_report_ajax.php',
                    data: {provId: prov, stkId: stk, distId: '<?php echo $sel_dist; ?>', showAll: 1},
                    success: function (data) {
                        $("#districts").html(data);
                        $('#district').select2();
                        $('#district').removeClass('form-control').addClass('select2me');
                        $('#district').removeClass('input-sm').addClass('input-medium');
                    }
                });
            }
        }
        function showStores(dist) {
            var stk = $('#stk_sel').val();
            if (stk != '' && dist != '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'tower_report_ajax.php',
                    data: {distId: dist, stkId: stk, whId: '<?php echo $sel_wh; ?>'},
                    success: function (data) {
                        $("#stores").html(data);
                        $('#warehouse').select2();
                        $('#warehouse').removeClass('form-control').addClass('select2me');
                        $('#warehouse').removeClass('input-sm').addClass('input-medium');
                    }
                });
            }
        }
    </script>
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
</body>
<!-- END BODY -->
</html>