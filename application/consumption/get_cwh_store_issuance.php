<?php

$cwh_issuance_flag = 'FALSE';
$cwh_issuance = 0;

if (!isset($fsrlevel)) {

    $rscwh = mysql_query("SELECT
	stakeholder.lvl,
        tbl_warehouse.stkid
FROM
	tbl_warehouse
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
	tbl_warehouse.wh_id = $whID
LIMIT 1");
    $rcwh = mysql_fetch_array($rscwh);
    $fsrlevel = $rcwh['lvl'];
    $fsrstk1 = $rs1['stkid'];
}

$yearcheck = $yy;
$monthcheck = $mm;

if($fsrstk1 == 9 || $fsrstk1==6){
    //Do Nothing
} else if ($fsrlevel == 3 && $yearcheck >= 2018) {
    
    $trtrtype = 2;
    /*if($yearcheck >= 2018 && $monthcheck >= 4){
        $trtrtype = 1;
    }*/

    $qryCwhR = "SELECT
	IFNULL(ABS(SUM(tbl_stock_detail.Qty)),0) Rcv
FROM
	tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
WHERE
	tbl_stock_master.WHIDFrom = 123
AND tbl_stock_master.WHIDTo = $wh_id
AND tbl_stock_master.TranTypeID = $trtrtype
AND tbl_stock_master.temp = 0
AND stock_batch.item_id = '" . $rsRow1['itm_id'] . "'
AND DATE_FORMAT(
	tbl_stock_master.TranDate,
	'%Y-%m-01'
) = '$RptDate'";
    $rsCwhR = mysql_query($qryCwhR);
    $rCwhR = mysql_fetch_array($rsCwhR);
    
    if ($rCwhR['Rcv'] > 0) {
        $cwh_issuance = $rCwhR['Rcv'];
        $cwh_issuance_flag = 'TRUE';
    }
}