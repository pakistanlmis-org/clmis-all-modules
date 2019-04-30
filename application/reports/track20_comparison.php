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

$na = false;

if (isset($_POST['submit'])) {
    $date_from = date("Y-m-01", strtotime($_POST['date_from']));
    $date_to = date("Y-m-01", strtotime($_POST['date_to']));
    $prov_sel = $_POST['prov_sel'];
    $stk_sel = $_POST['stk_sel'];
    $sdp_cat = $_POST['sdp_cat'];
    $report_by = $_POST['report_by'];
} else if (isset($_GET['by'])) {
    $by = $_GET['by'];
    $report_by = $_GET['report_by'];
    $date_to = $_GET['d'];
    if ($by == 'quarterly') {        
        $date_from = date("Y-m-01", strtotime("$date_to -2 months"));        
    }
    if ($by == 'annually') {
        $date_from = date("Y-m-01", strtotime("$date_to -11 months"));
    }

    $prov_sel = $_GET['prov_sel'];
    $stk_sel = 'all';
    $sdp_cat = $_GET['sdp_cat'];
    $_POST['submit'] = 1;
    $_POST['date_from'] = date("F Y", strtotime($date_from));
    $_POST['date_to'] = date("F Y", strtotime($date_to));
}

if (isset($_POST['submit'])) {
    $sel_wr = '';
    if (isset($prov_sel) && !empty($prov_sel) && $prov_sel != 'all') {
        $sel_wr[] = " alerts_stockout_table.prov_id =" . $prov_sel;
    }
    if (isset($stk_sel) && !empty($stk_sel) && $stk_sel != 'all') {
        if ($stk_sel == 2) {
            $sel_wr[] = " alerts_stockout_table.stkid IN (2,7,73)";
        } else {
            $sel_wr[] = " alerts_stockout_table.stkid =" . $stk_sel;
        }
    }

    if (is_array($sel_wr)) {
        $sel_location_wr = " AND " . implode(" AND ", $sel_wr);
    }

    $catcount = 3;
    $catcountchar = "three";
    if ($sdp_cat == 2) {
        $sdp_cat = "2,3";
        $catcount = 5;
        $catcountchar = "five";
    }

    $operator = ">";
    $operator2 = ">=";
    $operatorvalue = "availability";
    if ($report_by == 2) {
        $operator = "<=";
        $operator2 = ">";
        $operatorvalue = "out";
    }

    $stakeholders = array(
        1 => array('PWD'),
        2 => array('DOH (LHW)', 'DOH (Static HF)', 'DOH (MNCH)'),
        9 => array('PPHI')
    );
    //$Balochistan = $Sindh = array();
    if ($stk_sel == 'all') {
        $Punjab = array(
            'PWD',
            'DOH (LHW)',
            'DOH (Static HF)',
            'DOH (MNCH)'
        );
        $Sindh = array(
            'PWD',
            'PPHI',
            'DOH (LHW)',
            'DOH (Static HF)',
            'DOH (MNCH)'
        );
        $KhyberPakhtunkhwa = array(
            'PWD',
            'DOH (LHW)',
            'DOH (Static HF)',
            'DOH (MNCH)'
        );
        $Balochistan = array(
            'PWD',
            'DOH (LHW)',
            'DOH (Static HF)',
            'DOH (MNCH)'
        );
    } else {
        $Punjab = $Sindh = $KhyberPakhtunkhwa = $Balochistan = $stakeholders[$stk_sel];
    }


    $qry_rr = "SELECT
	alerts_stockout_table.Province,
	alerts_stockout_table.Stakeholder,
	alerts_stockout_table.reporting_date,
	COUNT(
		DISTINCT alerts_stockout_table.wh_id
	) AS reportedstores
FROM
	alerts_stockout_table
INNER JOIN tbl_warehouse ON alerts_stockout_table.wh_id = tbl_warehouse.wh_id
WHERE
	alerts_stockout_table.reporting_date BETWEEN '$date_from' AND '$date_to'
AND alerts_stockout_table.item_id IN (1, 5, 7, 8, 9, 13)
AND tbl_warehouse.hf_cat_id IN ($sdp_cat)
    $sel_location_wr
GROUP BY
	alerts_stockout_table.prov_id,
	alerts_stockout_table.stkid,
	alerts_stockout_table.reporting_date";

    $res_rr = $connc->query($qry_rr);

    while ($row_rr = $res_rr->fetch_object()) {
        $datearray[$row_rr->reporting_date] = $row_rr->reporting_date;
        $reporteddata[$row_rr->Province][$row_rr->Stakeholder][$row_rr->reporting_date] = $row_rr->reportedstores;
    }

    $qry_rr2 = "SELECT
	A.Province,
	A.prov_id,
	A.stkid,
	A.Stakeholder,
	A.reporting_date,
	COUNT(DISTINCT IF(A.itemsa>=$catcount,A.wh_id,0)) availability,
	COUNT(DISTINCT IF(A.itemsa<$catcount,A.wh_id,0)) stockout

FROM
	(
		SELECT DISTINCT
			Sum(

				IF (
					alerts_stockout_table.SOH > 0,
					1,
					0
				)
			) AS itemsa,
			GROUP_CONCAT(
				DISTINCT
				IF (
					alerts_stockout_table.SOH > 0,
					alerts_stockout_table.itm_name,
					''
				)
			) AS items,
			Sum(

				IF (
					alerts_stockout_table.SOH <= 0,
					1,
					0
				)
			) AS itemso,
			GROUP_CONCAT(
				DISTINCT
				IF (
					alerts_stockout_table.SOH <= 0,
					alerts_stockout_table.itm_name,
					''
				)
			) AS itemsso,
			alerts_stockout_table.Stakeholder,
			alerts_stockout_table.stkid,
			alerts_stockout_table.Province,
			alerts_stockout_table.prov_id,
			alerts_stockout_table.District,
			alerts_stockout_table.wh_name,
			alerts_stockout_table.reporting_date,
			alerts_stockout_table.wh_id
		FROM
			alerts_stockout_table
		INNER JOIN tbl_warehouse ON alerts_stockout_table.wh_id = tbl_warehouse.wh_id
		WHERE
			tbl_warehouse.hf_cat_id IN ($sdp_cat)
                AND alerts_stockout_table.reporting_date BETWEEN '$date_from' AND '$date_to'
                    $sel_location_wr
		GROUP BY
			alerts_stockout_table.wh_id,
			alerts_stockout_table.reporting_date
	) A
GROUP BY
	A.Province,
	A.Stakeholder,
	A.reporting_date
ORDER BY
	A.prov_id,
	A.stkid";

//    $qry_rr2 = "SELECT
//	A.Province,
//        A.prov_id,
//        A.stkid,
//	A.Stakeholder,
//	A.reporting_date,
//	COUNT(DISTINCT A.wh_id) storecount
//FROM
//	(
//		SELECT
//			alerts_stockout_table.Province,
//                        alerts_stockout_table.prov_id,
//                        alerts_stockout_table.stkid,
//			alerts_stockout_table.Stakeholder,
//			alerts_stockout_table.reporting_date,
//			COUNT(
//				DISTINCT alerts_stockout_table.item_id
//			) itemso,
//			alerts_stockout_table.wh_id
//		FROM
//			alerts_stockout_table
//		INNER JOIN tbl_warehouse ON alerts_stockout_table.wh_id = tbl_warehouse.wh_id
//		WHERE
//			alerts_stockout_table.item_id IN (1, 5, 7, 8, 9, 13)
//		AND alerts_stockout_table.reporting_date BETWEEN '$date_from' AND '$date_to'
//		AND alerts_stockout_table.SOH $operator 0
//		AND tbl_warehouse.hf_cat_id IN ($sdp_cat)
//                    $sel_location_wr
//		GROUP BY
//			alerts_stockout_table.wh_id,
//			alerts_stockout_table.reporting_date
//	) A
//WHERE
//	A.itemso $operator2 $catcount
//GROUP BY
//	A.Province,
//	A.Stakeholder,
//	A.reporting_date "
//            . "ORDER BY A.prov_id, A.stkid";
//echo $qry_rr2;

    $res_rr2 = $connc->query($qry_rr2);

    while ($row_rr2 = $res_rr2->fetch_object()) {
        $provarray[$row_rr2->Province] = $row_rr2->prov_id;
        $provarray[$row_rr2->Stakeholder] = $row_rr2->stkid;
        if ($report_by == 1) {
            $data[$row_rr2->Province][$row_rr2->Stakeholder][$row_rr2->reporting_date] = ($row_rr2->availability) - 1;
        } else {
            $data[$row_rr2->Province][$row_rr2->Stakeholder][$row_rr2->reporting_date] = ($row_rr2->stockout) - 1;
        }
    }

    //echo "<pre>";
    //print_r($data);
} else {
    $_POST['date_from'] = date("F Y", strtotime("-1 YEAR - 2 MONTHS"));
    $_POST['date_to'] = date("F Y", strtotime("-3 MONTHS"));
}
?>
<!-- END HEAD -->

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
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Track20 Comparison Report</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" name="track20" id="track20" action="">
                                    <table width="100%">
                                        <tr>
                                            <td class="col-md-2">
                                                <label class="control-label">From date</label>
                                                <input name="date_from" id="date_from" class="form-control input-sm" value="<?php echo $_POST['date_from']; ?>" required="required" />
                                            </td>
                                            <td class="col-md-2">
                                                <label class="control-label">To date</label>
                                                <input name="date_to" id="date_to" class="form-control input-sm" value="<?php echo $_POST['date_to']; ?>" required="required" />
                                            </td>
                                            <td class="col-md-2"><label class="control-label">Province</label>
                                                <select name="prov_sel" id="prov_sel" class="form-control input-sm">


                                                    <option value="all">All</option>
                                                    <?php
                                                    //select query
                                                    //gets
                                                    //province id
                                                    //province title
                                                    $filter = (!empty($stk_sel) && $stk_sel != 'all') ? " AND tbl_warehouse.stkid = $stk_sel" : "";
                                                    $queryprov = "SELECT DISTINCT
																tbl_locations.PkLocID,
																tbl_locations.LocName
															FROM
																tbl_locations
															INNER JOIN tbl_warehouse ON tbl_locations.PkLocID = tbl_warehouse.prov_id
															INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
															WHERE
																tbl_locations.ParentID IS NOT NULL
															AND tbl_locations.LocLvl = 2
															AND tbl_warehouse.is_active = 1
															$filter";
                                                    //query result
                                                    $rsprov = mysql_query($queryprov) or die();
                                                    while ($rowprov = mysql_fetch_array($rsprov)) {

                                                        if ($prov_sel == $rowprov['PkLocID']) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $rowprov['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowprov['LocName']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td class="col-md-2"><label class="control-label">Stakeholder</label><select name="stk_sel" id="stk_sel" class="form-control input-sm">

                                                    <option value="all">All</option>
                                                    <?php
                                                    //select query
                                                    //gets
                                                    //stakeholder id
                                                    //stakeholder name
                                                    $stkarray = array(
                                                        1 => 'PWD',
                                                        2 => 'DOH',
                                                        9 => 'PPHI'
                                                    );
                                                    //fetch result
                                                    foreach ($stkarray as $key => $value) {
                                                        //check seleted stakeholder
                                                        if ($stk_sel == $key) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }

                                                        $stkName = $rowstk['stkname'];
                                                        ?>
                                                        <option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $value; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select></td>
                                            <td id="sdp_catCol" class="col-md-2"><label class="control-label">SDP Category</label>
                                                <select name="sdp_cat" id="sdp_cat" class="form-control input-sm">
                                                    <option value='1' <?php if ($sdp_cat == 1) { ?>selected=""<?php } ?>>Primary</option>
                                                    <option value='2' <?php if ($sdp_cat == 2) { ?>selected=""<?php } ?>>Secondary/Tertiary</option>
                                                </select></td>
                                            <td id="rpt_by" class="col-md-2"><label class="control-label">Report By</label>
                                                <select name="report_by" id="report_by" class="form-control input-sm">
                                                    <option value='1' <?php if ($report_by == 1) { ?>selected=""<?php } ?>>Stock availability</option>
                                                    <option value='2' <?php if ($report_by == 2) { ?>selected=""<?php } ?>>Stock out</option>
                                                </select></td>
                                        </tr>
                                        <tr><td>&nbsp;</td></tr>
                                        <tr><td colspan="5" class="col-md-10"></td>
                                            <td class="col-md-2"><input type="submit" name="submit" value="Submit" class="form-control input-sm btn btn-primary"/></td></tr>
                                    </table>
                                </form>
                            </div>
                        </div>
                        <?php if (isset($_POST['submit'])) { ?>
                            <div class="widget" data-toggle="collapse-widget"> 

                                <!-- Widget heading -->
                                <div class="widget-head">
                                    <h4 class="heading">Stock <?php echo $operatorvalue; ?> rate for <?php
                                        if ($sdp_cat == 1) {
                                            echo "Primary SDPs";
                                        } if ($sdp_cat == 2) {
                                            echo "Secondary/Tertiary SDPs";
                                        }
                                        ?> from <?php echo $_POST['date_from']; ?> to <?php echo $_POST['date_to']; ?></h4>
                                </div>
                                <!-- // Widget heading END -->
                                <div class="widget-body">
                                    <!-- Table --> 
                                    <!-- Table -->
                                    <table class="table table-bordered table-condensed">
                                        <!-- Table heading -->
                                        <thead>
                                            <tr>
                                                <th>Province</th>
                                                <th>Stakeholder</th>
                                                <?php foreach ($datearray as $datee) { ?>
                                                    <th><?php echo date("M Y", strtotime($datee)); ?></th>
                                                <?php } ?>
                                                <th>Grand Total</th>
                                            </tr>
                                        </thead>
                                        <!-- // Table heading END -->
                                        <!-- Table body -->
                                        <tbody>
                                            <?php
                                            foreach ($reporteddata as $province => $stkarray) {
                                                $province1 = str_replace(" ", "", $province);
                                                ?>
                                                <tr>
                                                    <td rowspan="<?php echo count($$province1); ?>"><?php echo $province; ?></td>
                                                    <?php foreach ($$province1 as $stakeholder) { ?>
                                                        <td><?php echo $stakeholder; ?></td>                                            
                                                        <?php
                                                        foreach ($datearray as $datee) {
                                                            $total[$province][$datee]['reported'] += $data[$province][$stakeholder][$datee];
                                                            $total[$province][$datee]['total'] += $reporteddata[$province][$stakeholder][$datee];
                                                            $grandtotal[$datee]['reported'] += $data[$province][$stakeholder][$datee];
                                                            $grandtotal[$datee]['total'] += $reporteddata[$province][$stakeholder][$datee];
                                                            $horizontaltotal[$province][$stakeholder]['reported'] += $data[$province][$stakeholder][$datee];
                                                            $horizontaltotal[$province][$stakeholder]['total'] += $reporteddata[$province][$stakeholder][$datee];
                                                            $grandtotal['reported'] += $data[$province][$stakeholder][$datee];
                                                            $grandtotal['total'] += $reporteddata[$province][$stakeholder][$datee];
                                                            ?>
                                                            <td title="<?php echo (!empty($data[$province][$stakeholder][$datee]) ? $data[$province][$stakeholder][$datee] : '0') . "/" . $reporteddata[$province][$stakeholder][$datee]; ?>"><?php if (!empty($reporteddata[$province][$stakeholder][$datee])) { ?><a href="<?php echo SITE_URL; ?>application/reports/track20.php?report_by=<?php echo $report_by; ?>&sdp_cat=<?php echo $sdp_cat; ?>&d=<?php echo $datee; ?>&prov_sel=<?php echo $provarray[$province]; ?>&stk_sel=<?php echo $provarray[$stakeholder]; ?>" target="_blank"><?php echo (!empty($data[$province][$stakeholder][$datee]) ? round($data[$province][$stakeholder][$datee] / $reporteddata[$province][$stakeholder][$datee] * 100) : '0'); ?>%<?php } else {
                                                $na = true;
                                                                echo "N/A*";
                                            } ?></a></td>
                                                        <?php } ?>
                                                        <td><?php echo round($horizontaltotal[$province][$stakeholder]['reported'] / $horizontaltotal[$province][$stakeholder]['total'] * 100); ?>%</td>
                                                    </tr><tr>
        <?php } ?>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="right" style="background-color: #dce0e2">Total</th>
                                                    <?php
                                                    foreach ($datearray as $datee) {
                                                        $horizontaltotal['reported'] += $total[$province][$datee]['reported'];
                                                        $horizontaltotal['total'] += $total[$province][$datee]['total'];
                                                        ?>
                                                        <th style="background-color: #dce0e2" title="<?php echo $total[$province][$datee]['reported'] . "/" . $total[$province][$datee]['total']; ?>"><a href="<?php echo SITE_URL; ?>application/reports/track20.php?report_by=<?php echo $report_by; ?>&sdp_cat=<?php echo $sdp_cat; ?>&d=<?php echo $datee; ?>&prov_sel=<?php echo $provarray[$province]; ?>&stk_sel=all" target="_blank"><?php echo (!empty($total[$province][$datee]['reported']) ? round($total[$province][$datee]['reported'] / $total[$province][$datee]['total'] * 100) : '0'); ?>%</a></th>
                                                <?php } ?>
                                                    <th style="background-color: #dce0e2"><?php echo (!empty($horizontaltotal['reported']) ? round($horizontaltotal['reported'] / $horizontaltotal['total'] * 100) : '0'); ?>%</th>
                                                </tr>
    <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2" class="right" style="background-color: #bcd5e6">Grand Total</th>
                                                <?php foreach ($datearray as $datee) {
                                                    ?>
                                                    <th style="background-color: #bcd5e6" title="<?php echo $grandtotal[$datee]['reported'] . "/" . $grandtotal[$datee]['total']; ?>"><a href="<?php echo SITE_URL; ?>application/reports/track20.php?report_by=<?php echo $report_by; ?>&sdp_cat=<?php echo $sdp_cat; ?>&d=<?php echo $datee; ?>&prov_sel=all&stk_sel=all" target="_blank"><?php echo (!empty($grandtotal[$datee]['reported']) ? round($grandtotal[$datee]['reported'] / $grandtotal[$datee]['total'] * 100) : '0'); ?>%</a></th>
    <?php } ?>
                                                <th style="background-color: #bcd5e6"><?php echo (!empty($grandtotal['reported']) ? round($grandtotal['reported'] / $grandtotal['total'] * 100) : '0'); ?>%</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <?php if($na) { ?>
                                    <p><b>N/A* (Not Available)</b>: These facilities are not included and yet to be added per decision by the relevant stakeholders.</p>
                                    <?php } ?>                                    
<!--<p>Report based on SDPs having <b title="<?php echo $catcount; ?>"><?php echo $catcountchar; ?></b> modern FP methods with non-zero stocks.</p>-->
                                </div>
                            </div>
<?php } ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php include PUBLIC_PATH . "/html/footer.php"; ?>
<?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>

    <script>
        var startDateTextBox = $('#date_from');
        var endDateTextBox = $('#date_to');

        startDateTextBox.datepicker({
            minDate: "-5Y",
            maxDate: "-2M",
            dateFormat: 'MM yy',
            changeMonth: true,
            changeYear: true,
            onClose: function (dateText, inst) {
                //$(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            },
            onSelect: function (selectedDateTime, inst) {
                endDateTextBox.datepicker('option', 'minDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
        });
        endDateTextBox.datepicker({
            maxDate: "-1M",
            dateFormat: 'MM yy',
            changeMonth: true,
            changeYear: true,
            onClose: function (dateText, inst) {
                //$(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
        });

        function functionCall(ip)
        {
            window.open('ip_info.php?ip=' + ip, '_blank', 'scrollbars=1,width=650,height=600');
        }

        function showData(p)
        {
            window.open('wh_info.php?whId=' + p, '_blank', 'scrollbars=1,width=900,height=500');
        }
        function showHistory(p)
        {
            window.open('wh_data_history_list.php?whId=' + p, '_blank', 'scrollbars=1,width=900,height=600');
        }
    </script>
</body>
<!-- END BODY -->
</html>