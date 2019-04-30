<?php
/**
 * get-stock-frequency-data
 * @package maps/api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include Configuration
include("../../includes/classes/Configuration.inc.php");
//include db
include(APP_PATH."includes/classes/db.php");
//get year
$year = $_REQUEST["year"];
//get stakeholder
$stk = $_REQUEST["stk"];
//get sector
$sector = $_REQUEST["sector"];
//get province
$province = $_REQUEST["province"];
//get product
$product = $_REQUEST["product"];
//check province
if($province == "all"){
    //set province filter
	$provFilter = '';
        //set province filter 2
	$provFilter2 = '';   
}
else{
    //set province filter
	$provFilter = 'AND map_district_mapping.province_id ='.$province;
	//set province filter 2
        $provFilter2 = 'AND summary_district.province_id = '.$province;  
}
//start 
$start = $year."-01-01";
//end
$end = $year."-12-31"; 
//select query
//gets
//mapping id
//district name
//stock out
$query = "SELECT map_district_mapping.district_id,
				map_district_mapping.mapping_id,   
				map_district_mapping.district_name,
				ROUND(B.StockOut) AS StockOut
				FROM
				(SELECT map_district_mapping.district_id,
				map_district_mapping.district_name,
				map_district_mapping.mapping_id,
				AVG(A.StockOut) AS StockOut
				FROM
				(SELECT
				 A.district_id,
				 SUM(IF(A.MOS <= 0, 1, 0)) AS NoData,
				 SUM(IF(A.MOS > 0 && A.MOS <= REPgetMOSScale('IT-001', 1, 3, 'SO', 'E'), 1, 0)) AS StockOut,
				 SUM(IF(A.MOS > REPgetMOSScale('IT-001', 1, 3, 'SO', 'E') && A.MOS <= REPgetMOSScale('IT-001', 1, 3, 'US', 'E'), 1, 0)) AS UnderStock,
				 SUM(IF(A.MOS > REPgetMOSScale('IT-001', 1, 3, 'US', 'E') && A.MOS <= REPgetMOSScale('IT-001', 1, 3, 'SAT', 'E'), 1, 0)) AS Satisfactory,
				 SUM(IF(A.MOS > REPgetMOSScale('IT-001', 1, 3, 'SAT', 'E'), 1, 0)) AS OverStock
				FROM
				 (
				   SELECT
					summary_district.district_id,
					summary_district.item_id,
					summary_district.stakeholder_id,
					ROUND((summary_district.soh_district_store / summary_district.avg_consumption),2) AS MOS
				   FROM
					summary_district
				   INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
				   WHERE
					summary_district.reporting_date BETWEEN '$start'
				   AND '$end'
				   AND summary_district.stakeholder_id = $stk
				   AND summary_district.item_id = '$product'
				   $provFilter2
				   GROUP BY
					summary_district.item_id,
					summary_district.district_id,
					summary_district.reporting_date
				 ) A
				WHERE
				 A.stakeholder_id = $stk
				GROUP BY A.district_id)A
				RIGHT JOIN map_district_mapping ON map_district_mapping.district_id = A.district_id
				WHERE map_district_mapping.stakeholder_id = $stk
				$provFilter
				GROUP BY map_district_mapping.mapping_id)B
				INNER JOIN map_district_mapping ON map_district_mapping.mapping_id = B.mapping_id
				INNER JOIN tbl_locations ON map_district_mapping.district_id = tbl_locations.PkLocID
				 WHERE
				 map_district_mapping.stakeholder_id = $stk";
//query result
$result = mysql_query($query);
 if($result){
	 $row = mysql_fetch_all($result);
}
else{
   echo "Failed";
   return;
}
//encode in json
echo json_encode($row);

//fetch result
function mysql_fetch_all($result)
{
        //declare array
	$all = array();
        //fetch result
        while ($row = mysql_fetch_assoc($result)){
		$all[] = $row;
	}
        //return result
	return $all;
}