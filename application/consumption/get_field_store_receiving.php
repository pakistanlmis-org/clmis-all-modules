<?php

$issuance_flag = 'FALSE';
$district_issuance = 0;

if (!isset($fsrlevel)) {

    $qry1 = "SELECT
	tbl_warehouse.dist_id,
	tbl_warehouse.stkid,
	stakeholder.lvl
FROM
	tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
	tbl_warehouse.wh_id = $wh_id
LIMIT 1";
    $rs1 = mysql_query($qry1);
    $rs1 = mysql_fetch_array($rs1);
    $fsrlevel = $rs1['lvl'];
    $fsrdist1 = $rs1['dist_id'];
    $fsrstk1 = $rs1['stkid'];
}

$yearcheck = $yy;

if ($fsrstk1 == 9 || $fsrstk1 == 6) {
    //Do Nothing
} else if ($fsrlevel == 3 && $yearcheck >= 2018) {
    if (!isset($whID)) {
        $qry2 = "SELECT
		wh_id
	FROM
		tbl_warehouse
	INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
	WHERE
		tbl_warehouse.dist_id = $fsrdist1
	AND stakeholder.lvl = 4
	AND tbl_warehouse.stkid = $fsrstk1
	LIMIT 1";
        $rs2 = mysql_query($qry2);
        $rs2 = mysql_fetch_array($rs2);
        $whID = $rs2['wh_id'];
    }

    $qryIR = "SELECT
	IFNULL(SUM(tbl_wh_data.wh_received),0) Issue
FROM
	tbl_wh_data
INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
WHERE
	tbl_warehouse.wh_id = $whID
AND tbl_wh_data.item_id = '$item_char'
AND tbl_wh_data.RptDate = '$RptDate'";
    $rsIR = mysql_query($qryIR);
    $rsIR = mysql_fetch_array($rsIR);
    if ($rsIR['Issue'] > 0) {
        $district_issuance = $rsIR['Issue'];

        $qryIR2 = "SELECT
	SUM(stock_sources_data.received) total
FROM
	stock_sources_data
INNER JOIN tbl_hf_data ON stock_sources_data.hf_data_id = tbl_hf_data.pk_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
	tbl_warehouse.dist_id = $fsrdist1
AND tbl_warehouse.stkid = $fsrstk1
AND stakeholder.lvl = 7
AND stock_sources_data.stock_sources_id <> 216
AND tbl_hf_data.item_id = $item_int
AND tbl_hf_data.reporting_date = '$RptDate'";
        $rsIR2 = mysql_query($qryIR2);
        $rsIR22 = mysql_fetch_array($rsIR2);
        if ($rsIR22['total'] > 0) {
            $district_issuance = $district_issuance - $rsIR22['total'];
        }
        $issuance_flag = 'TRUE';
    }
}