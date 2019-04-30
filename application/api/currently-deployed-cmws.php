#!/usr/local/bin/php -q
<?php

/**
 * currently-deployed-cmws
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including DBCon file
include_once("DBCon.php");
$url = "http://mnch.pk/cmw_db/getcmwlist.php";
//Checking ccurl
if (!function_exists('curl_init')){
        //Display message
	die('Sorry CURL is not installed.');
}
//Start curl
$ch = curl_init();
//CURLOPT_URL
curl_setopt($ch, CURLOPT_URL, $url);
//CURLOPT_HEADER
curl_setopt($ch, CURLOPT_HEADER, 0);
//CURLOPT_RETURNTRANSFER
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//CURLOPT_TIMEOUT
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//curl_exec
$output = curl_exec($ch);
//close curl
curl_close($ch);
//Decode from json
$deployedCMWs = json_decode($output);
//Loop
//Getting cmw
foreach($deployedCMWs as $cmw)
{
    //cmw list
	$cmwList[] = $cmw->cmwcode;
	$cmwsArr[$cmw->districtname][] = $cmw->cmwcode;
}
//**********************
// Set all to in-active
//**********************
$cmws = "UPDATE tbl_warehouse
			SET tbl_warehouse.is_active = 0
			WHERE
				tbl_warehouse.stkid = 73
			AND tbl_warehouse.stkofficeid = 111";
//execute query
mysql_query($cmws);

//**********************
// Update 
//**********************
$updateCMWs = "UPDATE tbl_warehouse
			SET tbl_warehouse.is_active = 1
			WHERE
				tbl_warehouse.stkid = 73
			AND tbl_warehouse.stkofficeid = 111
			AND tbl_warehouse.dhis_code IN (".implode(',', $cmwList).")";
//execute query
mysql_query($updateCMWs);

//**********************
// Update Total Warehouse Table
//**********************
$qry = "INSERT INTO warehouses_by_month (
			warehouses_by_month.total_stores,
			warehouses_by_month.`level`,
			warehouses_by_month.stakeholder_id,
			warehouses_by_month.district_id,
			warehouses_by_month.reporting_date
		) SELECT
			COUNT(DISTINCT A.wh_id) AS totalStores,
			A.lvl,
			A.stkid,
			A.dist_id,
			DATE_FORMAT(CURDATE(), '%Y-%m-01') AS rptDate
		FROM
			(
				SELECT DISTINCT
					tbl_warehouse.stkid,
					tbl_warehouse.dist_id,
					stakeholder.lvl,
					tbl_warehouse.wh_id
				FROM
					tbl_warehouse
				INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				WHERE
					stakeholder.lvl IN (3, 4, 7)
				AND tbl_warehouse.is_active = 1
				ORDER BY
					stakeholder.lvl ASC
			) A
		GROUP BY
			A.dist_id,
			A.stkid,
			A.lvl
		ORDER BY
			A.lvl";
//execute query
mysql_query($qry);

//---------------------------------
// Update In-active warehouses list
//---------------------------------
$qry = "INSERT INTO warehouse_inactive_by_month (
			warehouse_inactive_by_month.warehouse_by_month_id,
			warehouse_inactive_by_month.warehouse_id
		) SELECT DISTINCT
			warehouses_by_month.pk_id,
			tbl_warehouse.wh_id
		FROM
			warehouses_by_month
		INNER JOIN tbl_warehouse ON warehouses_by_month.stakeholder_id = tbl_warehouse.stkid
		AND warehouses_by_month.district_id = tbl_warehouse.dist_id
		INNER JOIN stakeholder ON warehouses_by_month.`level` = stakeholder.lvl
		AND tbl_warehouse.stkofficeid = stakeholder.stkid
		INNER JOIN wh_user ON wh_user.wh_id = tbl_warehouse.wh_id
		WHERE
			warehouses_by_month.reporting_date = DATE_FORMAT(CURDATE(), '%Y-%m-01')
		AND tbl_warehouse.is_active = 0
		ORDER BY
			warehouses_by_month.pk_id ASC";
//execute query
mysql_query($qry);

$msg = "District wise deployed CMW list <pre>" . print_r($cmwsArr, true) . '</pre>';
// use wordwrap() if lines 
// are longer than 70 characters
$msg = wordwrap($msg, 150);
// send email
mail("ahussain@ghsc-psm.org","Active CMW's List",$msg);