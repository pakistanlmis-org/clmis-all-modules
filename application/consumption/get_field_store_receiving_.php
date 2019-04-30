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

if($fsrstk1 == 9 || $fsrstk1==6){
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
        $issuance_flag = 'TRUE';
    }
}