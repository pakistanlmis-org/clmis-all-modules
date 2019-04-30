<?php
/**
 * get-cyp-pop-generated
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
$year       = $_REQUEST["year"];
//get date from
$datefrom   = $_REQUEST["datefrom"];
//get date to
$dateto     = $_REQUEST["dateto"];
//get sector
$sector     = $_REQUEST["sector"];
//get stakeholder
$stk        = $_REQUEST["stk"];
//get province
$province   = $_REQUEST["province"];
//get prouct
$product    = $_REQUEST["product"];

//check year
if($year == ""){
    //start
    $start = $datefrom;
    //end
    $end = $dateto;
}
else{
    //start
    $start = $year."-01-01";
    //end
    $end   = $year."-12-31";
}
//check province
if($province == "all"){
    //set province filter
    $provFilter = '';
}
else{
    //set province filter
    $provFilter = 'AND map_district_mapping.province_id ='.$province;
}
//check sector
if($sector == "0" &&  $stk == "all"){
    //set stakeholder type
    $stkType = "AND stakeholder.stk_type_id = 0";
    //set stakeholder id
    $stkId = "";
    //set stakeholder
    $stk = "0";
}
else if($sector == "1" &&  $stk == "all"){
    //set stakeholder type
    $stkType = "AND stakeholder.stk_type_id = 1" ;
    //set stakeholder id
    $stkId = "";
    //set stakeholder
    $stk = "0";
}
else{
    //set stakeholder type
    $stkType ='';
    //set stakeholder id
    $stkId = "AND summary_district.stakeholder_id = ".$stk;
}
//check product
if($product == "all"){
    //set product
    $product = "%";
}
//select query
//gets
//mapping id
//district name
//cyp
//pop
//cyp_pop
$query = "SELECT
			map_district_mapping.mapping_id,
			map_district_mapping.district_id,
			tbl_locations.LocName AS district_name,
			COALESCE(A.CYP, NULL, 0) AS cyp,
			map_population.population as pop,
			Round((COALESCE(A.CYP, NULL, 0) / map_population.population * 100),3) AS cyp_pop
			FROM
			(
				 SELECT
					map_district_mapping.mapping_id,
					ROUND(SUM(summary_district.consumption * itminfo_tab.extra)) AS CYP
					FROM
					 tbl_locations
					INNER JOIN summary_district ON tbl_locations.PkLocID = summary_district.district_id
					INNER JOIN map_district_mapping ON map_district_mapping.district_id = summary_district.district_id
					INNER JOIN tbl_locations AS Mapping ON map_district_mapping.district_id = Mapping.PkLocID
					INNER JOIN itminfo_tab ON itminfo_tab.itmrec_id = summary_district.item_id
					INNER JOIN stakeholder ON stakeholder.stkid = summary_district.stakeholder_id
					WHERE
					 summary_district.item_id like '".$product."'
					$stkType  
					$stkId
					AND map_district_mapping.stakeholder_id = ".$stk."
					AND summary_district.reporting_date BETWEEN '".$start."'
					AND '".$end."'
					GROUP BY
					 map_district_mapping.mapping_id
					) AS A
			  INNER JOIN map_district_mapping ON map_district_mapping.mapping_id = A.mapping_id
			  INNER JOIN tbl_locations ON map_district_mapping.district_id = tbl_locations.PkLocID
			  INNER JOIN map_population ON A.mapping_id = map_population.district_id
			  WHERE
			  map_district_mapping.stakeholder_id = ".$stk." $provFilter";
//query result
$result = mysql_query($query);
 if($result){
	 $row = mysql_fetch_all($result);
}
else{
   echo "Failed";
}
//encode in json
echo json_encode($row);

//fetch result
function mysql_fetch_all($result)
{
	$all = array();
//fetch result
        while ($row = mysql_fetch_assoc($result)){
		$all[] = $row;
	}
	return $all;
}