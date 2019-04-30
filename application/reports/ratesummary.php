<?php
/**
 * ratesummary
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//and array
$and = array('');
//check sector
if (isset($sector) && $sector != 'all') {
    //check sector
    if ($sector == 'Public' || $sector == 'public') {
        //set level stakeholder type
        $lvl_stktype = 0;
    } else {
        //set level stakeholder type
        $lvl_stktype = 1;
    }
//set and array	
    $and[] = "stakeholder.stk_type_id = $lvl_stktype";
}
//check selected province
if (isset($sel_prov) && !empty($sel_prov) && $sel_prov != 'all') {
//set and array
    $and[] = "tbl_warehouse.prov_id = $sel_prov";
}
//check selected stakeholder
if (isset($sel_stk) && $sel_stk != 'all') {
    //set and array
    $and[] = "tbl_warehouse.stkid = $sel_stk";
} else {
    //set selected stakeholder
    $sel_stk = 'all';
}
//check selected item
if (isset($sel_item) && !empty($sel_item)) {
    //set and1
    $and1 = " AND tbl_wh_data.item_id = '$sel_item' ";
}
//implode and
$and = implode(' AND ', $and);
//select query
//Get Reporting Rate (All Warehouses)
//district Reporting Rate 
//field Reporting Rate 
$reportedQry = "SELECT
						(C.districtReportedWH / D.districtTotalWH) * 100 AS districtRR,
						(C.FieldReportedWH / D.FieldTotalWH) * 100 AS fieldRR
					FROM
						(
							SELECT
								SUM(A.districtReportedWH) AS districtReportedWH,
								SUM(A.FieldReportedWH) AS FieldReportedWH
							FROM
								(
									SELECT DISTINCT
										tbl_warehouse.wh_id,
										IF (stakeholder.lvl = 3, 1, 0) AS districtReportedWH,
										IF (stakeholder.lvl = 4, 1, 0) AS FieldReportedWH,
										tbl_warehouse.stkid
									FROM
										tbl_warehouse
									INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
									INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
									INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
									WHERE
										tbl_wh_data.report_month = $sel_month
									AND tbl_wh_data.report_year = $sel_year
									AND stakeholder.lvl IN (3, 4)
									$and
								) A
						) C
					JOIN (
						SELECT
							SUM(A.districtTotalWH) AS districtTotalWH,
							SUM(A.FieldTotalWH) AS FieldTotalWH
						FROM
							(
								SELECT DISTINCT
									tbl_warehouse.wh_id,
									IF (stakeholder.lvl = 3, 1, 0) AS districtTotalWH,
									IF (stakeholder.lvl = 4, 1, 0) AS FieldTotalWH,
									tbl_warehouse.stkid
								FROM
									tbl_warehouse
								INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
								INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
								WHERE
									stakeholder.lvl IN (3, 4)
								$and
							) A
					) D";
//query result
//echo $reportedQry;exit;
$reportedQryRes = mysql_fetch_array(mysql_query($reportedQry));
//district Reporting Rate 
$districtRR = !empty($reportedQryRes['districtRR']) ? (round($reportedQryRes['districtRR'], 2) . '%') : 'NA';
//field Reporting Rate 
$fieldRR = !empty($reportedQryRes['fieldRR']) ? (round($reportedQryRes['fieldRR'], 2) . '%') : 'NA';

//select query
// Get 
// Availability Rate (All Warehouses)
//district
//and field
$availabilityQry = "SELECT
						(C.districtReportedWH / D.districtTotalWH) * 100 AS districtRR,
						(C.FieldReportedWH / D.FieldTotalWH) * 100 AS fieldRR
					FROM
						(
							SELECT
								SUM(A.districtReportedWH) AS districtReportedWH,
								SUM(A.FieldReportedWH) AS FieldReportedWH
							FROM
								(
									SELECT DISTINCT
										tbl_warehouse.wh_id,
										IF (stakeholder.lvl = 3, 1, 0) AS districtReportedWH,
										IF (stakeholder.lvl = 4, 1, 0) AS FieldReportedWH,
										tbl_warehouse.stkid
									FROM
										tbl_warehouse
									INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
									INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
									INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
									WHERE
										tbl_wh_data.report_month = $sel_month
									AND tbl_wh_data.report_year = $sel_year
									AND stakeholder.lvl IN (3, 4)
									AND tbl_wh_data.wh_cbl_a > 0
									$and
									$and1
								) A
						) C
					JOIN (
						SELECT
							SUM(A.districtTotalWH) AS districtTotalWH,
							SUM(A.FieldTotalWH) AS FieldTotalWH
						FROM
							(
								SELECT DISTINCT
									tbl_warehouse.wh_id,
									IF (stakeholder.lvl = 3, 1, 0) AS districtTotalWH,
									IF (stakeholder.lvl = 4, 1, 0) AS FieldTotalWH,
									tbl_warehouse.stkid
								FROM
									tbl_warehouse
								INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
								INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
								WHERE
									stakeholder.lvl IN (3, 4)
								$and
							) A
					) D";
//query result
$availabilityQryRes = mysql_fetch_array(mysql_query($availabilityQry));
//district Availability Rate 
$districtAvailabilityRate = !empty($availabilityQryRes['districtRR']) ? (round($availabilityQryRes['districtRR'], 2) . '%') : 'NA';
//field Availability Rate 
$fieldAvailabilityRate = !empty($availabilityQryRes['fieldRR']) ? (round($availabilityQryRes['fieldRR'], 2) . '%') : 'NA';
?>

<div class="col-md-12">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="dashboard-stat_custom">
            <div class="visual" style="width: auto;">
                <i class="fa fa-report-icon"></i>
            </div>
            <div class="details" style="float: left !important; left:0px !important;">
                <?php //reporting ?>
                <div class="dashboard-title-1"> Reporting </div>
                <?php //rate ?>
                <div class="dashboard-title-2"> Rate <a href="non_report.php?lvl_type=3&month_sel=<?php echo $sel_month; ?>&year_sel=<?php echo $sel_year; ?>&item_id=<?php echo $sel_item; ?>&stk_sel=<?php echo $sel_stk; ?>&sector=<?php echo $sector; ?>&prov_sel=<?php echo $sel_prov; ?>&rptType=reported"><img src="<?php echo PUBLIC_URL; ?>images/book02.gif" title="Detail" width="25" border="0" height="15"></a></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="dashboard-stat_custom pull-right">
            <div class="visual" style="width: auto;">
                <i class="fa fa-report-icon"></i>
            </div>
            <div class="details" style="float: right !important;">
                <?php //Availability ?>
                <div class="dashboard-title-1"> Availability </div>
                <?php //rate ?>
                <div class="dashboard-title-2"> Rate <a href="stock_availability.php?tp=f&report_month=<?php echo $sel_month; ?>&report_year=<?php echo $sel_year; ?>&item_id=<?php echo $sel_item; ?>&stk_sel=<?php echo $sel_stk; ?>&sector=<?php echo $sector; ?>&prov_id=<?php echo $sel_prov; ?>"><img src="<?php echo PUBLIC_URL; ?>images/book02.gif" title="Detail" width="25" border="0" height="15"></a></div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12 row-br-tb">
    <div class="pull-left">
        <ul class="nav navbar-nav report-value-tab-left">
            <li>
                <div class="report-value-orange">
                    <?php //field reporting Rate  ?> 
                    <?php echo $fieldRR; ?> <a href="../reports/non_report.php?lvl_type=4&month_sel=<?php echo $sel_month; ?>&year_sel=<?php echo $sel_year; ?>&item_id=<?php echo $sel_item; ?>&stk_sel=<?php echo $sel_stk; ?>&sector=<?php echo $sector; ?>&prov_sel=<?php echo $sel_prov; ?>&rptType=reported"><img src="<?php echo PUBLIC_URL; ?>images/book02.gif" title="Detail" width="20" border="0" height="10"></a>
                </div>
                Field
            </li>
            <li>
                <div class="report-value-green">
                    <?php //district Reporting Rate  ?> 
                    <?php echo $districtRR; ?><a href="../reports/non_report.php?lvl_type=3&month_sel=<?php echo $sel_month; ?>&year_sel=<?php echo $sel_year; ?>&item_id=<?php echo $sel_item; ?>&stk_sel=<?php echo $sel_stk; ?>&sector=<?php echo $sector; ?>&prov_sel=<?php echo $sel_prov; ?>&rptType=reported"><img src="<?php echo PUBLIC_URL; ?>images/book02.gif" title="Detail" width="20" border="0" height="10"></a>
                </div>
                <?php // Store / Facility ?>
                District Store
            </li>
        </ul>
    </div>
    <div class="pull-right">
        <ul class="nav navbar-nav report-value-tab-right">
            <li>
                <div class="report-value-orange">
                    <?php //field Availability Rate  ?> 
                    <?php echo $fieldAvailabilityRate; ?><a href="../reports/stock_availability.php?lvl_type=4&report_month=<?php echo $sel_month; ?>&report_year=<?php echo $sel_year; ?>&item_id=<?php echo $sel_item; ?>&stk_sel=<?php echo $sel_stk; ?>&sector=<?php echo $sector; ?>&prov_id=<?php echo $sel_prov; ?>"><img src="<?php echo PUBLIC_URL; ?>images/book02.gif" title="Detail" width="20" border="0" height="10"></a>
                </div>
                Field
            </li>
            <li>
                <div class="report-value-green">
                    <?php //district Availability Rate  ?>                    
                    <?php echo $districtAvailabilityRate; ?><a href="../reports/stock_availability.php?lvl_type=3&report_month=<?php echo $sel_month; ?>&report_year=<?php echo $sel_year; ?>&item_id=<?php echo $sel_item; ?>&stk_sel=<?php echo $sel_stk; ?>&sector=<?php echo $sector; ?>&prov_id=<?php echo $sel_prov; ?>"><img src="<?php echo PUBLIC_URL; ?>images/book02.gif" title="Detail" width="20" border="0" height="10"></a>
                </div>
                District Store
            </li>
        </ul>
    </div>
</div>

