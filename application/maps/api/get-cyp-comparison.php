<?php
/**
 * get-cyp-comparison
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
//get date from
$datefrom = $_REQUEST["datefrom"];
//get date to
$dateto = $_REQUEST["dateto"];
//get stakeholder
$stk = $_REQUEST["stk"];
//get sector 
$sector = $_REQUEST["sector"];
//get product
$product = $_REQUEST["product"];
//get district id
$district_id = $_REQUEST["district_id"];
//start
$start = $datefrom;
//end
$end = $dateto;
//check sector
if($sector == "0" &&  $stk == "all"){
        //set stakeholder id
	$stkId = '';
	//set stakeholder type
        $stkType = "AND stakeholder.stk_type_id =".$sector;
	//set stakeholder
        $stk = $sector;
	//start
        $start = 'SUM(';
	//end
        $end = ')';
}
//check sector
else if($sector == "1" &&  $stk == "all"){
        //set stakeholder id
    $stkId = '';
	//set stakeholder type
        $stkType = "AND stakeholder.stk_type_id =".$sector;
	//set stakeholder
        $stk = $sector;
	//start
        $start = 'SUM(';
	//end
        $end = ')';
}
else{
        //set stakeholder id
	$stkId = "AND summary_district.stakeholder_id =".$stk;
	//set stakeholder type
        $stkType ='';
	//start
        $start = '';
	//end
        $end = '';
}
//year
$year = date('Y');
//f month
$f_month = date('Y', strtotime("-1 year", strtotime($year)));
//t month
$t_month = date('Y', strtotime("-3 year", strtotime($year)));
//type
$type = 'ROUND('.$start.'summary_district.consumption * itminfo_tab.extra'.$end.') AS VALUE';
 //check product
if($product == "all"){
    //select query
    //gets
    //label
    //value
	$query = "SELECT
				A.YEAR AS label,
				SUM(A.VALUE) AS value
		FROM
				(
						SELECT
								map_district_mapping.district_id,
								DATE_FORMAT(summary_district.reporting_date,'%Y') AS YEAR,
								itminfo_tab.itm_id,
								$type
						FROM
								summary_district
						INNER JOIN map_district_mapping ON map_district_mapping.district_id = summary_district.district_id
						INNER JOIN stakeholder ON stakeholder.stkid = summary_district.stakeholder_id
						INNER JOIN itminfo_tab ON itminfo_tab.itmrec_id = summary_district.item_id
						WHERE
						  map_district_mapping.mapping_id = ".$district_id."
						AND DATE_FORMAT(summary_district.reporting_date,'%Y-%m') BETWEEN '".$t_month."' AND '".$year."'
						$stkType 
					$stkId
						GROUP BY
								summary_district.reporting_date,
								map_district_mapping.district_id,
								itminfo_tab.itm_id
				) A
		GROUP BY
				A.YEAR";
}
else{
     //select query
    //gets
    //label
    //value
   $query = "SELECT
				A.YEAR AS label,
				SUM(A.VALUE) AS value
		FROM
				(
						SELECT
								map_district_mapping.district_id,
								DATE_FORMAT(summary_district.reporting_date,'%Y') AS YEAR,
								$type
						FROM
								summary_district
						INNER JOIN map_district_mapping ON map_district_mapping.district_id = summary_district.district_id
						INNER JOIN stakeholder ON stakeholder.stkid = summary_district.stakeholder_id
						INNER JOIN itminfo_tab ON itminfo_tab.itmrec_id = summary_district.item_id
						WHERE
								summary_district.item_id = '".$product."'
						AND map_district_mapping.mapping_id = ".$district_id."
						AND DATE_FORMAT(summary_district.reporting_date,'%Y-%m') BETWEEN '".$t_month."' AND '".$year."'
						$stkType 
					$stkId
						GROUP BY
								summary_district.reporting_date,
								map_district_mapping.district_id
				) A
		GROUP BY
				A.YEAR";

}
//query results
$result = mysql_query($query);
//check if result
 if($result){
     //if result
	 $row = mysql_fetch_all($result);
}
else{
   echo "Failed";
   return;
}

echo json_encode($row);


function mysql_fetch_all($result)
{
	$all = array();
	
		while ($row = mysql_fetch_assoc($result)){
			$all[] = $row;
	}
	return $all;
}