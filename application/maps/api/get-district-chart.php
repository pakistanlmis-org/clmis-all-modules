<?php

/**
 * get-district-chart
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
include(APP_PATH . "includes/classes/db.php");
//get year
$year = $_REQUEST["year"];
//get month
$month = $_REQUEST["month"];
//get stakeholder
$stk = $_REQUEST["stk"];
//get sector
$sector = $_REQUEST["sector"];
//get product
$product = $_REQUEST["product"];
//get district id
$district_id = $_REQUEST["district_id"];
//get level
$level = $_REQUEST["level"];
//get type
$consumtionType = $_REQUEST["type"];
//check month
if ($month > 9) {
    
} else {
    $month = "0" . $month;
}
//set day
$day = $year . "-" . $month;
//check sector
if ($sector == "0" && $stk == "all") {
    //set stakeholder id
    $stkId = '';
    //set stakeholder type
    $stkType = "AND stakeholder.stk_type_id =" . $sector;
    //set stakeholder 
    $stk = $sector;
    //set start
    $start = 'SUM(';
    //set end
    $end = ')';
} else if ($sector == "1" && $stk == "all") {

    $stkId = '';
    //set stakeholder id
    $stkType = "AND stakeholder.stk_type_id =" . $sector;
    //set stakeholder
    $stk = $sector;
    //set start
    $start = 'SUM(';
    //set end
    $end = ')';
} else {
    //set stakeholder id
    $stkId = "AND summary_district.stakeholder_id =" . $stk;
    //set stakeholder type
    $stkType = '';
    $start = '';
    $end = '';
}
//f month
$f_month = date('Y-m', strtotime("-1 month", strtotime($day)));
//t month
$t_month = date('Y-m', strtotime("-2 month", strtotime($day)));
//check level
if ($level == "all") {
    //set type
    $type = 'ROUND(' . $start . 'summary_district.soh_district_lvl' . $end . ' / ' . $start . 'summary_district.avg_consumption' . $end . ',2) AS VALUE';
} else if ($level == "3") {
    //set type
    $type = 'ROUND(' . $start . 'summary_district.soh_district_store' . $end . ' / ' . $start . 'summary_district.avg_consumption' . $end . ',2) AS VALUE';
} else {
    //set type
    $type = 'ROUND((' . $start . 'summary_district.soh_district_lvl' . $end . ' - ' . $start . 'summary_district.soh_district_store' . $end . ') / ' . $start . 'summary_district.avg_consumption' . $end . ',2) AS VALUE';
}
//check comsumption type
if ($consumtionType == "C") {
    //set type
    $type = 'ROUND(' . $start . 'summary_district.consumption' . $end . ') AS VALUE';
} else if ($consumtionType == "A") {
    //set type
    $type = 'ROUND(' . $start . 'summary_district.avg_consumption' . $end . ') AS VALUE';
} else {
    
}
//select query
//gets
//label
//value
$query = "SELECT 
			 B.MONTH AS label,
			 COALESCE(A.value,null,0) AS value
		FROM
		(SELECT
				A.MONTH,
				SUM(A.VALUE) AS value
		FROM
				(
						SELECT
								map_district_mapping.district_id,
								DATE_FORMAT(summary_district.reporting_date,'%Y-%m') AS MONTH,
								$type
						FROM
								summary_district
						INNER JOIN map_district_mapping ON map_district_mapping.district_id = summary_district.district_id
						INNER JOIN stakeholder ON stakeholder.stkid = summary_district.stakeholder_id
						WHERE
								summary_district.item_id = '" . $product . "'
						AND map_district_mapping.mapping_id = " . $district_id . "
						AND DATE_FORMAT(summary_district.reporting_date,'%Y-%m') BETWEEN '" . $t_month . "' AND '" . $day . "'
						$stkType 
					$stkId
						GROUP BY
								summary_district.reporting_date,
								map_district_mapping.district_id
				) A
		GROUP BY
				A.MONTH)A 
		RIGHT JOIN (
			SELECT '$t_month' AS MONTH UNION
			SELECT '$f_month' AS MONTH UNION
			SELECT '$day' AS MONTH ) B ON B.MONTH = A.MONTH";

$result = mysql_query($query);

if ($result) {
    $row = mysql_fetch_all($result);
} else {
    echo "Failed";
    return;
}
//encode in json
echo json_encode($row);

//fetch results
function mysql_fetch_all($result) {
    $all = array();
    //fetch results
    while ($row = mysql_fetch_assoc($result)) {
        $all[] = $row;
    }
    return $all;
}
