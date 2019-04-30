<?php

/**
 * graph_function
 * @package graph
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//indicators
//1  'Couple Year Protection',
//2  'Consumption',
//3  'Avg Monthly Consumption',
//4  'Months Of Stock - Field',
//5  'Month of Stock - Warehouse',
//6  'Months of Stock - Total',
//7  'Stock On Hand - Field',
//8  'Stock On Hand - Warehouse',
//9  'Stock On Hand - Total'
$indicatorsArr = array(
    1 => 'Couple Year Protection',
    2 => 'Consumption',
    3 => 'Avg Monthly Consumption',
    4 => 'Months Of Stock - Field',
    5 => 'Month of Stock - Warehouse',
    6 => 'Months of Stock - Total',
    7 => 'Stock On Hand - Field',
    8 => 'Stock On Hand - Warehouse',
    9 => 'Stock On Hand - Total'
);
$indicatorsDefArr = array(
    1 => 'The term Couple Year Protection (CYP) is used to estimate the quantity or the number of a specific type of contraceptive required to protect a couple from contraception / pregnancy for one year',
    2 => 'It is the number (quantity) of contraceptives dispensed / issued to the clients/users at the facility level. However, in case facility level issuance data is not available issuance of contraceptives to facilities by district store can be considered as proxy for consumption',
    3 => 'It is the average aggregated consumption (of a contraceptive) of the last three non-zero consumption months',
    4 => 'It is the number of months, the available stock (stock on hand) at a given time in facility level stores will last or will be sufficient for. It can be calculated by dividing the available quantity (stock on hand) in a store by AMC of that store <br /> MOS =  SOH (Stock on Hand) / AMC (Average Monthly Consumption)',
    5 => 'It is the number of months, the available stock (stock on hand) at a given time in a store will last or will be sufficient for. It can be calculated by dividing the available quantity (stock on hand) in a store by AMC of that store <br /> MOS =  SOH (Stock on Hand) / AMC (Average Monthly Consumption)',
    6 => 'It is the number of months, the available stock (stock on hand) at a given time at given level will last or will be sufficient for. It can be calculated by dividing the available quantity (stock on hand) by AMC <br /> MOS =  SOH (Stock on Hand) / AMC (Average Monthly Consumption)',
    7 => 'It is the quantity of usable stock available at facility level in a district at a given time',
    8 => 'It is the quantity of usable stock available in a store at a given time',
    9 => 'It is the aggregate / sum of the quantity of usable stock available at given level at a given time'
);
//compareOption
// 1  'Year - National',
// 2  'Year - Provincial',
// 3  'Year - District',
// 4  'Stakeholder - National',
// 5  'Stakeholder - Provincial',
// 6  'Stakeholder - District',
// 7  'Geographical - National',
// 8  'Geographical - Provinical',
// 9  'Geographical - District'
$compareOptionArr = array(
    1 => 'Year - National',
    2 => 'Year - Provincial',
    3 => 'Year - District',
    4 => 'Stakeholder - National',
    5 => 'Stakeholder - Provincial',
    6 => 'Stakeholder - District',
    7 => 'Geographical - National',
    8 => 'Geographical - Provinical',
    9 => 'Geographical - District'
);

// Select query
// Get all Products
//item id
//item rec id
//item name
$qry = "SELECT
			itminfo_tab.itm_id,
			itminfo_tab.itmrec_id,
			itminfo_tab.itm_name
		FROM
			itminfo_tab";
//query result
$qryRes = mysql_query($qry);
//fetch result
while ($row = mysql_fetch_array($qryRes)) {
    //Product id
    $productsArr['ProductId'][$row['itm_id']] = $row['itmrec_id'];
    //product name
    $productsArr['ProductName'][$row['itm_id']] = $row['itm_name'];
}
// select query
// Get all Stakeholders
//stk id
//stk name
$qry = "SELECT
			stakeholder.stkid,
			stakeholder.stkname
		FROM
			stakeholder
		WHERE
			stakeholder.stk_type_id IN (0, 1)
		AND stakeholder.ParentID IS NULL
		ORDER BY
			stakeholder.stkorder ASC";
//query result
$qryRes = mysql_query($qry);
//fecth results
while ($row = mysql_fetch_array($qryRes)) {
    $stakeholderArr[$row['stkid']] = $row['stkname'];
}
// select query
// Get all Locations
//Pk Loc id
//Loc name
$qry = "SELECT
			tbl_locations.PkLocID,
			tbl_locations.LocName
		FROM
			tbl_locations";
//query result
$qryRes = mysql_query($qry);
//fetch results
while ($row = mysql_fetch_array($qryRes)) {
    $locationArr[$row['PkLocID']] = $row['LocName'];
}
/**
 * startXML
 * 
 * @param type $caption
 * @param type $subCaption
 * @param type $downloadFileName
 * @param type $yAxisName
 * @param type $xAxisName
 * @return type
 */
function startXML($caption = '', $subCaption = '', $downloadFileName = '', $yAxisName = '', $xAxisName = '') {
    $xmlstore = "<chart theme='fint' showBorder='0' formatNumberScale='0' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$subCaption' exportFileName='$downloadFileName' yAxisName='$yAxisName' xAxisName='$xAxisName' showValues='1'>";
    return $xmlstore;
}

/**
 * endXML
 * 
 * @param type $caption
 * @param type $subCaption
 * @param type $downloadFileName
 * @param type $yAxisName
 * @param type $xAxisName
 * @return string
 */
function endXML($caption = '', $subCaption = '', $downloadFileName = '', $yAxisName = '', $xAxisName = '') {
    $xmlstore .= "</chart>";
    return $xmlstore;
}
/**
 * getInterval
 * 
 * @param type $option
 * @return type
 */
function getInterval($option) {
    if ($option == 1) {
        $startDate = date('Y') . '-01-01';
        $endDate = date('Y') . '-03-31';
    }if ($option == 2) {
        $startDate = date('Y') . '-04-01';
        $endDate = date('Y') . '-06-31';
    }if ($option == 3) {
        $startDate = date('Y') . '-07-01';
        $endDate = date('Y') . '-09-30';
    }if ($option == 4) {
        $startDate = date('Y') . '-10-01';
        $endDate = date('Y') . '-12-31';
    }if ($option == 5) {
        $startDate = date('Y') . '-01-01';
        $endDate = date('Y') . '-06-31';
    }if ($option == 6) {
        $startDate = date('Y') . '-07-01';
        $endDate = date('Y') . '-12-31';
    }if ($option == 7) {
        $startDate = date('Y') . '-01-01';
        $endDate = date('Y') . '-12-31';
    }
    return array($startDate, $endDate);
}
/**
 * drawMonthCategories
 * 
 * @param type $option
 * @return string
 */
function drawMonthCategories($option) {
    $dates = getInterval($option);
    $startDate = $dates[0];
    $endDate = $dates[1];

    $xmlstore = '';
    $begin = new DateTime($startDate);
    $end = new DateTime($endDate);
    $diff = $begin->diff($end);
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($begin, $interval, $end);
    foreach ($period as $date) {
        $xmlstore .= "<category label='" . $date->format("M") . "' />";
    }
    return $xmlstore;
}
/**
 * getIndicatorValue
 * 
 * @param type $row
 * @param type $indicator
 * @return type
 */
function getIndicatorValue($row, $indicator) {
    if ($indicator == 1) {
        $val = round($row['CYP']);
    } else if ($indicator == 2) {
        $val = round($row['consumption']);
    } else if ($indicator == 3) {
        $val = round($row['AVG_consumption']);
    } else if ($indicator == 4) {
        $val = round(($row['SOH_field'] / $row['AVG_consumption']), 1);
    } else if ($indicator == 5) {
        $val = round(($row['SOH_store'] / $row['AVG_consumption']), 1);
    } else if ($indicator == 6) {
        $val = round(($row['SOH_level'] / $row['AVG_consumption']), 1);
    } else if ($indicator == 7) {
        $val = round($row['SOH_field']);
    } else if ($indicator == 8) {
        $val = round($row['SOH_store']);
    } else if ($indicator == 9) {
        $val = round($row['SOH_level']);
    }
    return $val;
}
