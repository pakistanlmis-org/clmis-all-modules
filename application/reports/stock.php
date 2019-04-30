<?php

/**
 * stock
 * @package reports
 * 
 * @author     Ajmal Hussain 
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include FusionCharts
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");

$varArr = explode('|', base64_decode($_REQUEST['param']));
//start date
$startDate = $varArr[0];
//end date
$endDate = $varArr[1];
//wh id
$whId = $varArr[2];
//wh name
$whName = $varArr[3];

//begin
$begin = new DateTime( $startDate );
//end
$end = new DateTime( $endDate );
//diff
$diff = $begin->diff($end);
//total months
$totalMonths = (($diff->format('%y') * 12) + $diff->format('%m')) + 1; // +1 to include the current Month
//interval
$interval = DateInterval::createFromDateString('1 month');
//period
$period = new DatePeriod($begin, $interval, $end);
//file save name
$fileSaveName = $whName.' Shipment '.$begin->format('M Y')." to ".$end->format('M Y');
?>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL;?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL;?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>
<?php
# lineColor='8E9D51'
//xml
$xmlstore = "<chart theme='fint' exportFileName='$fileSaveName' labelPadding='3' canvasPadding='30' exportEnabled='1' exportAction='Download' caption='$whName Shipment Report' subCaption='' yAxisName='Number of Transactions' xAxisName='".$begin->format('M Y')." to ".$end->format('M Y')."' showValues='1' formatNumberScale='0' exportAtClient='0' >\n";
							
$xmlstore .="<categories>\n";
foreach ( $period as $date )
{
	$xmlstore .="\t<category label='".$date->format( "M" )."'/>\n";
}
$xmlstore .="</categories>\n";
$xmlstore1 ="<dataset seriesName='Receive'>\n";
$xmlstore2 ="<dataset seriesName='Issue'>\n";

foreach ( $period as $date )
{
    //query
    //gets
    //wh id
    //stock issue
    //stock receive
	$newQry = "SELECT 
					wh_id,
					wh_name,
					SUM(stockIssue) AS stockIssue,
					SUM(stockRcv) AS stockRcv
				FROM (
					SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						SUM(IF(tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id && tbl_stock_master.TranTypeID = 2, 1, 0)) AS stockIssue,
						SUM(IF(tbl_stock_master.WHIDTo = tbl_warehouse.wh_id && tbl_stock_master.TranTypeID = 1, 1, 0)) AS stockRcv,
						tbl_stock_master.TranNo
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					INNER JOIN tbl_stock_master ON tbl_warehouse.wh_id = tbl_stock_master.WHIDFrom OR tbl_warehouse.wh_id = tbl_stock_master.WHIDTo
					WHERE
						tbl_warehouse.wh_id = ".$whId."
						AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m' ) = '".$date->format( "Y-m" )."'
					GROUP BY
						tbl_warehouse.wh_id,
						tbl_stock_master.TranNo
					)AS A 
				GROUP BY
					wh_id
				ORDER BY
					wh_name";
	
	$qryRes = mysql_fetch_array(mysql_query($newQry));
	$rcv = (!empty($qryRes['stockRcv'])) ? $qryRes['stockRcv'] : 0;
	$issue = (!empty($qryRes['stockIssue'])) ? $qryRes['stockIssue'] : 0;
	$xmlstore1 .= "\t<set value='" .$rcv. "' />\n";
	$xmlstore2 .= "\t<set value='" .$issue. "' />\n";
}
$xmlstore1 .="</dataset>\n";
$xmlstore2 .="</dataset>\n";

$xmlstore .= $xmlstore1.$xmlstore2;
$xmlstore .= "</chart>";

// Call chart function to plot graph
FC_SetRenderer('javascript');
echo renderChart(PUBLIC_URL."FusionCharts/Charts/StackedColumn2D.swf", "", $xmlstore, 'Shipment', '900', 450, false, false);