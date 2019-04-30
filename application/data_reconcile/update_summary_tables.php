<?php
include("../includes/classes/Configuration.inc.php");
include("../includes/classes/db.php");

// Get all districts
$qry = "SELECT
			tbl_wh_data.wh_id,
			tbl_wh_data.item_id,
			tbl_wh_data.report_month,
			tbl_wh_data.report_year,
			tbl_warehouse.stkid,
			tbl_warehouse.dist_id
		FROM
			tbl_wh_data
		INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
		WHERE
			tbl_wh_data.is_calculated = 0
                        and report_year = 2018 and report_month= 1  AND
tbl_warehouse.dist_id = 11
		GROUP BY
			tbl_wh_data.report_month,
			tbl_wh_data.report_year,
			tbl_wh_data.item_id,
			tbl_warehouse.dist_id,
			tbl_warehouse.stkid";
$qryRes = mysql_query($qry);

while ($row = mysql_fetch_array($qryRes)) {
    $whId = $row['wh_id'];
    $itmId = $row['item_id'];
    $month = $row['report_month'];
    $year = $row['report_year'];
    $stkid = $row['stkid'];
    $dist_id = $row['dist_id'];

    $addSummary = "CALL REPUpdateSummaryDistrict($whId, '$itmId', $month, $year);";
    
    $r=mysql_query($addSummary);
    echo $addSummary . '<br>';
//    $a=mysql_fetch_array($r);
//    echo '<pre>';
//    print_r($a);
    
//    $updateQry = "UPDATE
//				tbl_wh_data, tbl_warehouse
//				SET tbl_wh_data.is_calculated = 1
//				WHERE
//				tbl_warehouse.wh_id = tbl_wh_data.wh_id
//				AND tbl_wh_data.item_id = '$itmId'
//				AND tbl_wh_data.report_month = $month
//				AND tbl_wh_data.report_year = $year
//				AND tbl_warehouse.stkid = $stkid
//				AND tbl_warehouse.dist_id = $dist_id";
//    mysql_query($updateQry);
}
//mysql_query("INSERT INTO cron_log_time SET last_run = NOW()");
