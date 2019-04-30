<?php
/**
 * stock_status_ajax
 * @package dashboard
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include Configuration
include("../includes/classes/Configuration.inc.php");
//include db
include(APP_PATH."includes/classes/db.php");
////include FusionCharts
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");

$rrWhere = '';
//province id
$prov_id = '';
//district id
$dist_id = '';
//chart id
$chart_id = 'StockStatus';
//check year
if ( $_POST['year'] )
{
//filter	
    $where = '';
	//sector
        $sector = $_POST['sector'];
	//year
        $year = $_POST['year'];
	//month
        $month = $_POST['month'];
	//report date
        $rptDate = $year.'-'.$month.'-01';
	//level
        $lvl = $_POST['lvl'];
	//province filter
        $proFilter = $_POST['proFilter'];
	//stakeholder id
        $stkId = $_POST['stkId'];
	//type
        $type = $_POST['type'];
	//check type
        if($type == 1){
		$typeText = "District";
	}else if($type == 2){
		$typeText = "Field";
	}else if($type == 3){
		$typeText = "District &#38; Field";
	}
	//check province filter
	if ($proFilter == 2)
	{
	//province filter text	
            $proFilterText = "All Products Without Condom";
	//province filter text
            $proFilter = " AND itminfo_tab.itmrec_id != 'IT-001' ";
	}
	else
	{
            //province filter text
		$proFilterText = "All Products";
		//province filter
                $proFilter = "";
	}
        //check level
        //if level ==1
	if ( $lvl == 1 )
	{
		$level = "All Pakistan $typeText Stores";
	}
        //if level ==2
	else if ( $lvl == 2 )
	{
            //get province id
		$prov_id = $_POST['prov_id'];
		//select query
                //gets
                //province name
                $prov = mysql_fetch_array(mysql_query("SELECT
													tbl_locations.LocName
												FROM
													tbl_locations
												WHERE
													tbl_locations.PkLocID = $prov_id"));
		//province name
                $provName = $prov['LocName'];
		//level
                $level = "$provName $typeText Stores";
		//set where
                $where .= " AND summary_district.province_id = $prov_id";
		//set rrWhere
                $rrWhere .= " AND tbl_warehouse.prov_id = $prov_id";
	}
        //if level ==3
	else if ( $lvl == 3 )
	{
	//get district id	
            $dist_id = $_POST['dist_id'];
	//select query
            //gets
            //district name
            $dist = mysql_fetch_array(mysql_query("SELECT
													tbl_locations.LocName
												FROM
													tbl_locations
												WHERE
													tbl_locations.PkLocID = $dist_id"));
	//set district name	
            $distName = $dist['LocName'];
	//set level	
            $level = "District $distName";
	//set where	
            $where .= " AND summary_district.district_id = $dist_id";
	//set rrWhere	
            $rrWhere .= " AND tbl_warehouse.dist_id = $dist_id";
	}
        //check sector
        //if sector ==0
	if ($sector == '0')
	{
	//set sector Text	
            $sectorText = 'Public Sector';
	}
        //if sector ==1
	else if ($sector == '1')
	{
	//set sector Text
            $sectorText = 'Private Sector';
	}
}
//heading
$heading = date('M Y', strtotime($rptDate));
//select query
//gets
//stakeholder name
$stk = "SELECT
			stakeholder.stkname
		FROM
			stakeholder
		WHERE
			stakeholder.stkid = $stkId;";
//fetch result
$stkQuery = mysql_fetch_array(mysql_query($stk));
$stkName = $stkQuery['stkname'];

//xml
$xmlstore = '';
//iten mane
$itmName = '';
//overStock 
$overStock = '';
//stockOut 
$stockOut = '';
//reportingRate 
$reportingRate = '';
//check type
//if type ==1
if ( $type == 1 )
{
    //select query
    //gets
    //item id
    //OverStockPer
    //StockOutPer
	$dataQry = "SELECT
					A.item_id,
					itminfo_tab.itm_name,
					CEIL((SUM(IF(A.MOS >= REPgetMOSScale(itminfo_tab.itmrec_id, $stkId, 3, 'OS', 'S'), 1, 0)) / COUNT(A.district_id) * 100)) AS OverStockPer,
					CEIL((SUM(IF(A.MOS <= REPgetMOSScale(itminfo_tab.itmrec_id, $stkId, 3, 'SO', 'E'), 1, 0)) / COUNT(A.district_id) * 100)) AS StockOutPer
				FROM
					(
						SELECT
							summary_district.district_id,
							summary_district.item_id,
							ROUND((summary_district.soh_district_store / summary_district.avg_consumption), 2) AS MOS
						FROM
							summary_district
						INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
						WHERE
							summary_district.reporting_date = '$rptDate'
						AND summary_district.stakeholder_id = $stkId
						$where
						GROUP BY
							summary_district.item_id,
							summary_district.district_id
					) A
				JOIN itminfo_tab ON A.item_id = itminfo_tab.itmrec_id
				INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
				WHERE
					stakeholder_item.stkid = $stkId
				AND itminfo_tab.itm_category = 1
				$proFilter
				GROUP BY A.item_id
				ORDER BY itminfo_tab.frmindex ASC";
        //query result
	$qryRes = mysql_query($dataQry);
        //num of result 
	$num = mysql_num_rows(mysql_query($dataQry));	
	//if record exists
        if( $num > 0 )
	{	
	//fetch result	
            while ($row1 = mysql_fetch_array($qryRes)) {
		//item name	
                $itmName[$row1['item_id']] = $row1['itm_name'];
		//over Stock	
                $overStock[$row1['item_id']] = $row1['OverStockPer'];
		//stock Out	
                $stockOut[$row1['item_id']] = $row1['StockOutPer'];
		}
		// Get Reporting Rate (All Warehouses)
		$reportedQry = "SELECT
							((A.reportedWH / B.totalWH) * 100) AS RR
						FROM
							(
								SELECT
									COUNT(DISTINCT tbl_warehouse.wh_id) AS reportedWH,
									tbl_warehouse.stkid
								FROM
									tbl_warehouse
								INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
								INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
								INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
								WHERE
									tbl_wh_data.item_id = 'IT-001'
								AND tbl_wh_data.report_month = $month
								AND tbl_wh_data.report_year = $year
								AND tbl_warehouse.stkid = $stkId
								AND stakeholder.lvl = 3 $rrWhere
							) A
						JOIN (
							SELECT
								COUNT(DISTINCT tbl_warehouse.wh_id) AS totalWH,
								tbl_warehouse.stkid
							FROM
								tbl_warehouse
							INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
							INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
							WHERE
								tbl_warehouse.stkid = $stkId
							AND stakeholder.lvl = 3 $rrWhere
						) B ON A.stkid = B.stkid";
                //query result
		$reportedQryRes = mysql_fetch_array(mysql_query($reportedQry));
		$reportingRate = round($reportedQryRes['RR'], 1);
	}
}
//if type==2
else if ( $type == 2 )
{
    //select query
    //gets
    //item id
    //OverStockPer
    //StockOutPer
	$dataQry = "SELECT
					A.item_id,
					itminfo_tab.itm_name,
					CEIL((SUM(IF(A.MOS >= REPgetMOSScale(itminfo_tab.itmrec_id, $stkId, 4, 'OS', 'S'), 1, 0)) / COUNT(A.district_id) * 100)) AS OverStockPer,
					CEIL((SUM(IF(A.MOS <= REPgetMOSScale(itminfo_tab.itmrec_id, $stkId, 4, 'SO', 'E'), 1, 0)) / COUNT(A.district_id) * 100)) AS StockOutPer
				FROM
					(
						SELECT
							summary_district.district_id,
							summary_district.item_id,
							ROUND(((summary_district.soh_district_lvl - summary_district.soh_district_store) / summary_district.avg_consumption), 2) AS MOS
						FROM
							summary_district
						INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
						WHERE
							summary_district.reporting_date = '$rptDate'
						AND summary_district.stakeholder_id = $stkId
						$where
						GROUP BY
							summary_district.item_id,
							summary_district.district_id
					) A
				JOIN itminfo_tab ON A.item_id = itminfo_tab.itmrec_id
				INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
				WHERE
					stakeholder_item.stkid = $stkId
				AND itminfo_tab.itm_category = 1
				$proFilter
				GROUP BY A.item_id
				ORDER BY itminfo_tab.frmindex ASC";
        //query result
	$qryRes = mysql_query($dataQry);
        //num of reord
	$num = mysql_num_rows(mysql_query($dataQry));
	//check if record exists
        if( $num > 0 )
	{
	//fetch result	
            while ($row1 = mysql_fetch_array($qryRes)) {
		//item name	
                $itmName[$row1['item_id']] = $row1['itm_name'];
		//over Stock
                $overStock[$row1['item_id']] = $row1['OverStockPer'];
		//stock Out
                $stockOut[$row1['item_id']] = $row1['StockOutPer'];
		}
		// Get Reporting Rate (All Warehouses)
		$reportedQry = "SELECT
							((A.reportedWH / B.totalWH) * 100) AS RR
						FROM
							(
								SELECT
									COUNT(DISTINCT tbl_warehouse.wh_id) AS reportedWH,
									tbl_warehouse.stkid
								FROM
									tbl_warehouse
								INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
								INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
								INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
								WHERE
									tbl_wh_data.item_id = 'IT-001'
								AND tbl_wh_data.report_month = $month
								AND tbl_wh_data.report_year = $year
								AND tbl_warehouse.stkid = $stkId
								AND stakeholder.lvl = 4 $rrWhere
							) A
						JOIN (
							SELECT
								COUNT(DISTINCT tbl_warehouse.wh_id) AS totalWH,
								tbl_warehouse.stkid
							FROM
								tbl_warehouse
							INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
							INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
							WHERE
								tbl_warehouse.stkid = $stkId
							AND stakeholder.lvl = 4 $rrWhere
						) B ON A.stkid = B.stkid";
                //query result
		$reportedQryRes = mysql_fetch_array(mysql_query($reportedQry));
		$reportingRate = round($reportedQryRes['RR'], 1);
	}
}
//if type == 3
else if ( $type == 3 )
{
//select query
    //gets
    //item id
    //item name
    //OverStockPer
    //StocOutkPer
    $dataQry = "SELECT
					A.item_id,
					itminfo_tab.itm_name,
					CEIL((SUM(IF(A.MOS >= REPgetMOSScale(itminfo_tab.itmrec_id, $stkId, 3, 'OS', 'S'), 1, 0)) / COUNT(A.district_id) * 100)) AS OverStockPer,
					CEIL((SUM(IF(A.MOS <= REPgetMOSScale(itminfo_tab.itmrec_id, $stkId, 3, 'SO', 'E'), 1, 0)) / COUNT(A.district_id) * 100)) AS StockOutPer
				FROM
					(
						SELECT
							summary_district.district_id,
							summary_district.item_id,
							ROUND((summary_district.soh_district_store / summary_district.avg_consumption), 2) AS MOS
						FROM
							summary_district
						INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
						WHERE
							summary_district.reporting_date = '$rptDate'
						AND summary_district.stakeholder_id = $stkId
						$where
						GROUP BY
							summary_district.item_id,
							summary_district.district_id
					) A
				JOIN itminfo_tab ON A.item_id = itminfo_tab.itmrec_id
				INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
				WHERE
					stakeholder_item.stkid = $stkId
				AND itminfo_tab.itm_category = 1
				$proFilter
				GROUP BY A.item_id
				ORDER BY itminfo_tab.frmindex ASC";
        //query result
	$qryRes = mysql_query($dataQry);
        //num of record
	$num = mysql_num_rows(mysql_query($dataQry));
	//check if record exists
        if( $num > 0 )
	{
            //fetch result
		while ($row1 = mysql_fetch_array($qryRes)) {
		//item name	
                    $itmName[$row1['item_id']] = $row1['itm_name'];
		//over stock
                    $overStock[$row1['item_id']] = $row1['OverStockPer'];
		//stock out
                    $stockOut[$row1['item_id']] = $row1['StockOutPer'];
		}
	
		// Get Reporting Rate (All Warehouses)
		$reportedQry = "SELECT
							((A.reportedWH / B.totalWH) * 100) AS RR
						FROM
							(
								SELECT
									COUNT(DISTINCT tbl_warehouse.wh_id) AS reportedWH,
									tbl_warehouse.stkid
								FROM
									tbl_warehouse
								INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
								INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
								INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
								WHERE
									tbl_wh_data.item_id = 'IT-001'
								AND tbl_wh_data.report_month = $month
								AND tbl_wh_data.report_year = $year
								AND tbl_warehouse.stkid = $stkId
								AND stakeholder.lvl IN(3, 4) $rrWhere
							) A
						JOIN (
							SELECT
								COUNT(DISTINCT tbl_warehouse.wh_id) AS totalWH,
								tbl_warehouse.stkid
							FROM
								tbl_warehouse
							INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
							INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
							WHERE
								tbl_warehouse.stkid = $stkId
							AND stakeholder.lvl IN(3, 4) $rrWhere
						) B ON A.stkid = B.stkid";
                //query result
		$reportedQryRes = mysql_fetch_array(mysql_query($reportedQry));
		$reportingRate = round($reportedQryRes['RR'], 1);
	}
}
//check if record exists
if( $num > 0 )
{			
//caption	
    $caption = "Stock Out Vs Over Stock (Reporting Rate $reportingRate%)";
//sub caption
    $subCaption = "Filter By: $sectorText&#44; $stkName Stakeholder&#44; $proFilterText&#44; $level&#44; $heading";					
//download File Name 	
    $downloadFileName = $caption . ' - ' . $subCaption . ' - ' . date('Y-m-d H:i:s');
	?>
	<a href="javascript:exportChart('<?php echo $chart_id . $stkId;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel_dashlet_left" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php
	$xmlstore = "<chart xAxisNamePadding='0' yAxisNamePadding='0' chartLeftMargin='0' chartRightMargin='0' chartTopMargin='0' chartBottomMargin='0' theme='fint' yAxisMaxValue='100' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$subCaption' exportFileName='$downloadFileName' yAxisName='Percentage' xAxisName='Products' numberSuffix='%' showValues='1' formatNumberScale='0'>";
	// Start Making Categories (Products)
	$xmlstore .= "<categories>";
	foreach ($itmName as $itemId => $itemName) {
		$xmlstore .= "<category label='$itemName' />";
	}
	$xmlstore .= "</categories>";
	
	// Stock Out Series
	$xmlstore .= "<dataset seriesName='Stock Out'>";
	foreach ($stockOut as $itemId => $value) {
		$param = base64_encode($itemId.'|'.$stkId.'|'.$rptDate.'|SO'.'|'.$lvl.'|'.$prov_id.'|'.$dist_id.'|'.$type);
		$xmlstore .= "<set value='$value' link=\"JavaScript:showData('$param');\" />";
	}
	$xmlstore .= "</dataset>";
	
	// Over Stock Series
	$xmlstore .= "<dataset seriesName='Over Stock'>";
	foreach ($overStock as $itemId => $value) {					
		$param = base64_encode($itemId.'|'.$stkId.'|'.$rptDate.'|OS'.'|'.$lvl.'|'.$prov_id.'|'.$dist_id.'|'.$type);
		$xmlstore .= "<set value='$value' link=\"JavaScript:showData('$param');\" />";
	}
	$xmlstore .= "</dataset>";
	
	$xmlstore .= "<trendlines>
					<line startvalue='$reportingRate' displayvalue='Reporting Rate' valueonright='1' showvalue='1' color='009C00' />
				  </trendlines>";
	
	
	$xmlstore .= "</chart>";
	//include chart
	FC_SetRenderer('javascript');
	echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, $chart_id . $stkId, '100%', 350, false, false);
	//unset item name
        unset($itemName);
        //unset Stock Out
	unset($stockOut);
        //unset Over Stock
	unset($overStock);
}
else
{
	echo "No record found";
}