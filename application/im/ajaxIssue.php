<?php
/**
 * ajaxIssue
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
//Checking Id
if(isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])){
    //Getting Id
    $detail_id = $_REQUEST['Id'];	
	$type = $_REQUEST['type'];
	
	if($type == 'qty'){
		$uQty = str_replace(",","",$_REQUEST['data']);
		$objStockDetail->editIssue($detail_id, $uQty);
	}
}
if( (isset($_REQUEST['provId']) && !empty($_REQUEST['provId'])) || (isset($_REQUEST['stkId']) && !empty($_REQUEST['stkId'])) ){
	$and = '1=1 ';
	if (!empty($_REQUEST['provId']))
	{
		$and .= " AND tbl_warehouse.prov_id = ".$_REQUEST['provId']." ";
	}if (!empty($_REQUEST['stkId']))
	{
		$and .= " AND tbl_warehouse.stkid = ".$_REQUEST['stkId']."";
	}
        //Gets
        //wh_id
        //wh_name
        //
	$qry = "SELECT DISTINCT
				tbl_warehouse.wh_id,
				CONCAT(tbl_warehouse.wh_name,	'(', stakeholder.stkname, ')') AS wh_name
			FROM
				tbl_warehouse
			INNER JOIN tbl_stock_master ON tbl_warehouse.wh_id = tbl_stock_master.WHIDTo
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
				$and
			ORDER BY
				tbl_warehouse.wh_name ASC";
        //Query result
	$qryRes = mysql_query($qry);
	echo '<option value="">Select</option>';
	while ($row = mysql_fetch_array($qryRes))
	{
		echo "<option value=\"$row[wh_id]\">$row[wh_name]</option>";
	}
}
?>