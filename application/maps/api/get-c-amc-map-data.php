<?php
/**
 * get-c-amc-map-data
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
//get month
$month = $_REQUEST["month"];
//get stakeholder
$stk = $_REQUEST["stk"];
//get sector
$sector = $_REQUEST["sector"];
//get province
$province = $_REQUEST["province"];
//get product
$product = $_REQUEST["product"];
//get type
$type = $_REQUEST["type"];
//check month
if($month>9){}else{$month = "0".$month;}
$day = $year."-".$month;
//check province
if($province == "all"){
    //set provFilter
    $provFilter = '';    
}
else{
    //set provFilter
    $provFilter = 'AND map_district_mapping.province_id ='.$province;
}
 //check sector and stakeholder 
if($sector == "0" &&  $stk == "all"){
    //stakeholder type
    $stkType = "AND Stk.stk_type_id = 0";
    //stakeholder id
    $stkId = "";
    //stakeholder
    $stk = "0";
    //start
    $start = 'SUM(';
    //end
    $end = ')';
}
else if($sector == "1" &&  $stk == "all"){
    //stakeholder type
    $stkType = "AND Stk.stk_type_id = 1" ;
    //stakeholder id
    $stkId = "";
    //stakeholder
    $stk = "0";
    //start
    $start = 'SUM(';
    //end
    $end = ')';
}
else{
    //stakeholder type
    $stkType ='';
    //stakeholder id
    $stkId = "AND summary_district.stakeholder_id= ".$stk;
    //start
    $start = '';
    //end
    $end = '';
}
  
//check type
if($type == 'C'){
    //set type
   $type = ''.$start.'consumption'.$end.' AS consumption';
}
else{
    //set type
   $type = ''.$start.'avg_consumption'.$end.' AS consumption';
}
//select query
//gets
//mapping id
//district id
//district name
//consumption
$query = "SELECT
				D.mapping_id,
				D.district_id,
				D.district_name,
				COALESCE(C.consumption, NULL, 0) AS consumption
		FROM
		(
			SELECT
					map_district_mapping.district_id,
					map_district_mapping.district_name,
					ROUND(B.consumption) AS consumption
			FROM
					(
						SELECT
								A.district_id,
								A.district_name,
								SUM(A.consumption) AS consumption
						FROM
								(
									SELECT
											map_district_mapping.mapping_id AS district_id,
											map_district_mapping.district_name,
											$type
									FROM
											tbl_warehouse
									INNER JOIN tbl_locations ON tbl_locations.PkLocID = tbl_warehouse.dist_id
									INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
									INNER JOIN map_district_mapping ON tbl_warehouse.dist_id = map_district_mapping.district_id
									INNER JOIN summary_district ON map_district_mapping.district_id = summary_district.district_id
									INNER JOIN stakeholder AS Stk ON Stk.stkid = summary_district.stakeholder_id
									WHERE
											stakeholder.lvl = 4
									$stkType  
									$stkId
									AND summary_district.item_id = '".$product."'
									AND DATE_FORMAT(summary_district.reporting_date,'%Y-%m') = '".$day."'
									$provFilter
									AND map_district_mapping.stakeholder_id = ".$stk."
									GROUP BY
											map_district_mapping.district_id
			) A
		GROUP BY
			A.district_id
	) B
				INNER JOIN map_district_mapping ON map_district_mapping.mapping_id = B.district_id
				INNER JOIN tbl_locations ON map_district_mapping.district_id = tbl_locations.PkLocID
				WHERE
						map_district_mapping.stakeholder_id = ".$stk."
				GROUP BY
						map_district_mapping.district_id
						) C
				RIGHT JOIN (
						SELECT DISTINCT
								map_district_mapping.district_id,
								map_district_mapping.mapping_id,
								map_district_mapping.district_name
						FROM
								map_district_mapping
						WHERE
								map_district_mapping.stakeholder_id = ".$stk."
								$provFilter
				) D ON C.district_id = D.district_id
				GROUP BY D.district_id";
//query result
$result = mysql_query($query);
//check result 
if($result){
	 $row = mysql_fetch_all($result);
}
else{
   echo "Failed";
   return;
}
//encode in json
echo json_encode($row);

//fetch results
function mysql_fetch_all($result)
{
	$all = array();
        //fetch results
	while ($row = mysql_fetch_assoc($result)){
		$all[] = $row;
	}
	return $all;
}