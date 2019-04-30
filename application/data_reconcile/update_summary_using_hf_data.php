<?php

include("../includes/classes/Configuration.inc.php");
include("../includes/classes/AllClasses.php");
// Get all districts
$qry = "SELECT
	tbl_hf_data.warehouse_id,
	tbl_hf_data.item_id,
	tbl_hf_data.reporting_date
FROM
tbl_hf_data
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
WHERE
tbl_hf_data.reporting_date = '2018-02-01' AND
tbl_warehouse.prov_id = 1 AND
tbl_warehouse.stkid = 1
";
echo $qry;exit;
$qryRes = mysql_query($qry);

$summary = '';
$a=array();
while ($row = mysql_fetch_array($qryRes)) {
$a=$row;
    $warehouse_id = $row['warehouse_id'];
    $item_id = $row['item_id'];
    $reporting_date = $row['reporting_date'];

    $summary .= "
	
	CALL REPUpdateHFTypeFromHF('".$warehouse_id."', '".$item_id."', '".$reporting_date."');
    CALL REPUpdateHFData('".$warehouse_id."', '".$item_id."', '".$reporting_date."');
    CALL REPUpdateDistrictStock('".$warehouse_id."', '".$item_id."', '".$reporting_date."');";
}
//print_r($a);
//echo $summary;exit;
mysql_query($summary);
//mail("ahussain@ghsc-psm.org", 'Summary tables has been updated for last one hour health facility data', 'Summary tables are updated!');
