<?php
/**
* stock_status
* @package dashboard
* 
* @author     Ajmal Hussain
* @email <ahussain@ghsc-psm.org>
* 
* @version    2.2
* 
*/

// Create Sub Caption
function getSubCaption($sector, $product_filter, $stakeholder_id, $level, $reporting_date, $location_id = '', $store_type)
{
	/******* Start: Sector Filter******/
	// Public Sector
	if ($sector == '0') {
        //set sector text
        $sector_text = 'Public Sector';
    }
    // Private Sector
    else if ($sector == '1') {
        //set sector text	
        $sector_text = 'Private Sector';
    }
	/******* End: Sector Filter******/
	
	/******* Start: Product Filter******/
	if ($product_filter == 2) {
        //set province filter text
        $product_filter_text = "All Products Without Condom";
    } else {
        //set province filter text
        $product_filter_text = "All Products";
    }
	/******* End: Sector Filter******/
	
	// National Level
	if ($level == 1) {
        $level_text = 'All Pakistan ' . $store_type;
    }
	// Provincial and District level
    else{
        $level_text = getLocationName($location_id) . " " . $store_type;
    }
	/******* End: Level Filter******/
	
	//sub caption
	$sub_caption = "Filter By: ".$sector_text."&#44; ".getStakeholderName($stakeholder_id)." Stakeholder&#44; ". $product_filter_text."&#44; ".$level_text."&#44; ".date('M Y', strtotime($reporting_date));
	return $sub_caption;
}

// Get Location Name
function getLocationName($location_id)
{
	// Query to get location name
	$qry = "SELECT
				tbl_locations.LocName
			FROM
				tbl_locations
			WHERE
				tbl_locations.PkLocID = $location_id";
	// Execute query
	$qry_res = mysql_fetch_array(mysql_query($qry));
	// Return location name
	return $qry_res['LocName'];
}
function getStakeholderName($stakeholder_id)
{
	// Query to get stakeholder name
	$qry = "SELECT
				stakeholder.stkid,
				stakeholder.stkname
			FROM
				stakeholder
			WHERE
				stakeholder.stkid = $stakeholder_id";
	// Execute query
	$qry_res = mysql_fetch_array(mysql_query($qry));
	// Return stakeholder name
	return $qry_res['stkname'];
}

function districtWise($chart_id, $reporting_date, $product_filter_id, $stakeholder_id, $province_id, $sector, $level)
{
	//xml
	$xmlstore = '';
	//item name
	$item_name = '';
	//over stock
	$over_stock = '';
	//stock out
	$tock_out = '';
	// Where
	$where = '';
	//reporting Rate Where
	$rr_where = '';
	
	/*********** Start: Product Filter ***********/
	if ($product_filter_id == 2) {
        //set Product filter
        $product_filter = " AND itminfo_tab.itmrec_id != 'IT-001' ";
    } else {
        //set Product  filter
        $product_filter = "";
    }
	/*********** End: Product Filter ***********/
	/*********** Start: Province Filter ***********/
	if(!empty($province_id))
	{
		//set where	
        $where .= " AND summary_district.province_id = $province_id";
        //set rrWhere	
        $rr_where .= " AND tbl_warehouse.prov_id = $province_id";
	}
	/*********** End: Province Filter ***********/
	/*********** Start: District Filter ***********/
	if(!empty($district_id))
	{
		//set where
        $where .= " AND summary_district.district_id = $district_id";
        //set rrWhere
        $rr_where .= " AND tbl_warehouse.dist_id = $district_id";
	}
	
	
	//select query
	//gets
	//item id
	//item name
	//OverStockPer
	//StockOutPer
	$data_qry = "SELECT
					A.item_id,
					itminfo_tab.itm_name,
					CEIL((SUM(IF(A.MOS >= REPgetMOSScale(itminfo_tab.itmrec_id, ".$stakeholder_id.", 3, 'OS', 'S'), 1, 0)) / COUNT(A.district_id) * 100)) AS OverStockPer,
					CEIL((SUM(IF(A.MOS <= REPgetMOSScale(itminfo_tab.itmrec_id, ".$stakeholder_id.", 3, 'SO', 'E'), 1, 0)) / COUNT(A.district_id) * 100)) AS StockOutPer
				FROM
					(
						SELECT
							summary_district.district_id,
							summary_district.item_id,
							ROUND(IFNULL((summary_district.soh_district_store / summary_district.avg_consumption), 0), 2) AS MOS
						FROM
							summary_district
						INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
						WHERE
							summary_district.reporting_date = '".$reporting_date."'
						AND summary_district.stakeholder_id = ".$stakeholder_id."
						".$where."
						GROUP BY
							summary_district.item_id,
							summary_district.district_id
					) A
				JOIN itminfo_tab ON A.item_id = itminfo_tab.itmrec_id
				INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
				WHERE
					stakeholder_item.stkid = ".$stakeholder_id."
				AND itminfo_tab.itm_category = 1
				".$product_filter."
				GROUP BY A.item_id
				ORDER BY itminfo_tab.frmindex ASC";
	//query result
	$qry_res = mysql_query($data_qry);
	//number of result
	$num = mysql_num_rows(mysql_query($data_qry));
	//check number of result
	if ($num > 0) {
		//fetch query result
		while ($row1 = mysql_fetch_array($qry_res)) {
			//item name
			$item_name[$row1['item_id']] = $row1['itm_name'];
			//Over Stock Per
			$over_stock[$row1['item_id']] = $row1['OverStockPer'];
			//Stock Out Per	
			$tock_out[$row1['item_id']] = $row1['StockOutPer'];
		}
		// Get Reporting Rate (All Warehouses)
		$reported_qry = "SELECT
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
									tbl_wh_data.RptDate = '".$reporting_date."'
								AND tbl_warehouse.stkid = ".$stakeholder_id."
								".$rr_where."
								AND stakeholder.lvl = 3
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
								tbl_warehouse.stkid = ".$stakeholder_id."
								".$rr_where."
							AND stakeholder.lvl = 3
						) B ON A.stkid = B.stkid";
		//fetch query result
		$reported_qry_res = mysql_fetch_array(mysql_query($reported_qry));
		$reporting_rate = round($reported_qry_res['RR'], 1);
		//caption
		$caption = "Stock Out Vs Over Stock (Reporting Rate $reporting_rate%)";
		
		// Subcaption
		
		$sub_caption = getSubCaption($sector, $product_filter_id, $stakeholder_id, $level, $reporting_date, $province_id, 'District Stores');
		
		//download File Name 
		$download_file_name = $caption . ' - ' . $sub_caption . ' - ' . date('Y-m-d H:i:s');
		?>
		<a href="javascript:exportChart('<?php echo $chart_id . $stakeholder_id; ?>', '<?php echo $download_file_name; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
			<?php
			$xmlstore = "<chart xAxisNamePadding='0' yAxisNamePadding='0' chartLeftMargin='0' chartRightMargin='0' chartTopMargin='0' chartBottomMargin='0' theme='fint' yAxisMaxValue='100' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$sub_caption' exportFileName='$download_file_name' yAxisName='Percentage' xAxisName='Products' numberSuffix='%' showValues='1' formatNumberScale='0'>";
			// Start Making Categories (Products)
			$xmlstore .= "<categories>";
			foreach ($item_name as $itemId => $itemName) {
				$xmlstore .= "<category label='$itemName' />";
			}
			$xmlstore .= "</categories>";

			// Stock Out Series
			$xmlstore .= "<dataset seriesName='Stock Out'>";
			foreach ($tock_out as $itemId => $value) {
				$param = base64_encode($itemId . '|' . $stakeholder_id . '|' . $reporting_date . '|SO' . '|' . $level . '|' . $province_id . '|' . $district_id . '|1');
				$xmlstore .= "<set value='$value' link=\"JavaScript:showData('$param');\" />";
			}
			$xmlstore .= "</dataset>";

			// Over Stock Series
			$xmlstore .= "<dataset seriesName='Over Stock'>";
			foreach ($over_stock as $itemId => $value) {
				$param = base64_encode($itemId . '|' . $stakeholder_id . '|' . $reporting_date . '|OS' . '|' . $level . '|' . $province_id . '|' . $district_id . '|1');
				$xmlstore .= "<set value='$value' link=\"JavaScript:showData('$param');\" />";
			}
			$xmlstore .= "</dataset>";

			$xmlstore .= "<trendlines>
				<line startvalue='$reporting_rate' displayvalue='Reporting Rate' valueonright='1' showvalue='1' showontop='1' color='009C00' />
			  </trendlines>";


			$xmlstore .= "</chart>";
			
			//unset item name
			unset($itemName);
			//unset Stock out
			unset($tock_out);
			//unset over stock
			unset($over_stock);
		}
		return $xmlstore;
}
function fieldWise($chart_id, $reporting_date, $product_filter_id, $stakeholder_id, $province_id, $sector, $level)
{
	//xml
	$xmlstore = '';
	//item name
	$item_name = '';
	//over stock
	$over_stock = '';
	//stock out
	$tock_out = '';
	// Where
	$where = '';
	//reporting Rate Where
	$rr_where = '';
	
	/*********** Start: Product Filter ***********/
	if ($product_filter_id == 2) {
        //set Product filter
        $product_filter = " AND itminfo_tab.itmrec_id != 'IT-001' ";
    } else {
        //set Product  filter
        $product_filter = "";
    }
	/*********** End: Product Filter ***********/
	/*********** Start: Province Filter ***********/
	if(!empty($province_id))
	{
		//set where	
        $where .= " AND summary_district.province_id = $province_id";
        //set rrWhere	
        $rr_where .= " AND tbl_warehouse.prov_id = $province_id";
	}
	/*********** End: Province Filter ***********/
	/*********** Start: District Filter ***********/
	if(!empty($district_id))
	{
		//set where
        $where .= " AND summary_district.district_id = $district_id";
        //set rrWhere
        $rr_where .= " AND tbl_warehouse.dist_id = $district_id";
	}
	
	
	//select query
	//gets
	//item id
	//item name
	//OverStockPer
	//StockOutPer
	$data_qry = "SELECT
					A.item_id,
					itminfo_tab.itm_name,
					CEIL((SUM(IF(A.MOS >= REPgetMOSScale(itminfo_tab.itmrec_id, ".$stakeholder_id.", 4, 'OS', 'S'), 1, 0)) / COUNT(A.district_id) * 100)) AS OverStockPer,
					CEIL((SUM(IF(A.MOS <= REPgetMOSScale(itminfo_tab.itmrec_id, ".$stakeholder_id.", 4, 'SO', 'E'), 1, 0)) / COUNT(A.district_id) * 100)) AS StockOutPer
				FROM
					(
						SELECT
							summary_district.district_id,
							summary_district.item_id,
							ROUND(IFNULL(((summary_district.soh_district_lvl - summary_district.soh_district_store) / summary_district.avg_consumption), 0), 2) AS MOS
						FROM
							summary_district
						INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
						WHERE
							summary_district.reporting_date = '".$reporting_date."'
						AND summary_district.stakeholder_id = ".$stakeholder_id."
						".$where."
						GROUP BY
							summary_district.item_id,
							summary_district.district_id
					) A
				JOIN itminfo_tab ON A.item_id = itminfo_tab.itmrec_id
				INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
				WHERE
					stakeholder_item.stkid = ".$stakeholder_id."
				AND itminfo_tab.itm_category = 1
				".$product_filter."
				GROUP BY A.item_id
				ORDER BY itminfo_tab.frmindex ASC";
	//query result
	$qry_res = mysql_query($data_qry);
	//number of result
	$num = mysql_num_rows(mysql_query($data_qry));
	//check number of result
	if ($num > 0) {
		//fetch query result
		while ($row1 = mysql_fetch_array($qry_res)) {
			//item name
			$item_name[$row1['item_id']] = $row1['itm_name'];
			//Over Stock Per
			$over_stock[$row1['item_id']] = $row1['OverStockPer'];
			//Stock Out Per	
			$tock_out[$row1['item_id']] = $row1['StockOutPer'];
		}
		// Get Reporting Rate (All Warehouses)
		$reported_qry = "SELECT
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
									tbl_wh_data.RptDate = '".$reporting_date."'
								AND tbl_warehouse.stkid = ".$stakeholder_id."
								".$rr_where."
								AND stakeholder.lvl = 4
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
								tbl_warehouse.stkid = ".$stakeholder_id."
								".$rr_where."
							AND stakeholder.lvl = 4
						) B ON A.stkid = B.stkid";
		//fetch query result
		$reported_qry_res = mysql_fetch_array(mysql_query($reported_qry));
		$reporting_rate = round($reported_qry_res['RR'], 1);
		//caption
		$caption = "Stock Out Vs Over Stock (Reporting Rate $reporting_rate%)";
		
		// Subcaption
		$sub_caption = getSubCaption($sector, $product_filter_id, $stakeholder_id, $level, $reporting_date, $province_id, 'Field Stores');
		
		//download File Name 
		$download_file_name = $caption . ' - ' . $sub_caption . ' - ' . date('Y-m-d H:i:s');
		?>
		<a href="javascript:exportChart('<?php echo $chart_id . $stakeholder_id.'field'; ?>', '<?php echo $download_file_name; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
			<?php
			$xmlstore = "<chart xAxisNamePadding='0' yAxisNamePadding='0' chartLeftMargin='0' chartRightMargin='0' chartTopMargin='0' chartBottomMargin='0' theme='fint' yAxisMaxValue='100' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$sub_caption' exportFileName='$download_file_name' yAxisName='Percentage' xAxisName='Products' numberSuffix='%' showValues='1' formatNumberScale='0'>";
			// Start Making Categories (Products)
			$xmlstore .= "<categories>";
			foreach ($item_name as $itemId => $itemName) {
				$xmlstore .= "<category label='$itemName' />";
			}
			$xmlstore .= "</categories>";

			// Stock Out Series
			$xmlstore .= "<dataset seriesName='Stock Out'>";
			foreach ($tock_out as $itemId => $value) {
				$param = base64_encode($itemId . '|' . $stakeholder_id . '|' . $reporting_date . '|SO' . '|' . $level . '|' . $province_id . '|' . $district_id . '|2');
				$xmlstore .= "<set value='$value' link=\"JavaScript:showData('$param');\" />";
			}
			$xmlstore .= "</dataset>";

			// Over Stock Series
			$xmlstore .= "<dataset seriesName='Over Stock'>";
			foreach ($over_stock as $itemId => $value) {
				$param = base64_encode($itemId . '|' . $stakeholder_id . '|' . $reporting_date . '|OS' . '|' . $level . '|' . $province_id . '|' . $district_id . '|2');
				$xmlstore .= "<set value='$value' link=\"JavaScript:showData('$param');\" />";
			}
			$xmlstore .= "</dataset>";

			$xmlstore .= "<trendlines>
				<line startvalue='$reporting_rate' displayvalue='Reporting Rate' valueonright='1' showvalue='1' showontop='1' color='009C00' />
			  </trendlines>";


			$xmlstore .= "</chart>";
			
			//unset item name
			unset($itemName);
			//unset Stock out
			unset($tock_out);
			//unset over stock
			unset($over_stock);
		}
		return $xmlstore;
}
function healthFacilityWise($chart_id, $reporting_date, $product_filter_id, $stakeholder_id, $province_id, $district_id, $sector, $level, $hfArr)
{
	//xml
	$xmlstore = '';
	//item name
	$item_name = '';
	//over stock
	$over_stock = '';
	//stock out
	$tock_out = '';
	// Where
	$where = '';
	//reporting Rate Where
	$rr_where = '';
	
	/*********** Start: Product Filter ***********/
	if ($product_filter_id == 2) {
        //set Product filter
        $product_filter = " AND itminfo_tab.itmrec_id != 'IT-001' ";
    } else {
        //set Product  filter
        $product_filter = "";
    }
	/*********** End: Product Filter ***********/
	/*********** Start: Province Filter ***********/
	if(!empty($province_id))
	{
        //set Where	condition
        $where .= " AND tbl_warehouse.prov_id = $province_id";
	}
	/*********** End: Province Filter ***********/
	/*********** Start: District Filter ***********/
	if(!empty($district_id))
	{
		//set where condition
        $where .= " AND tbl_warehouse.dist_id = $district_id";
	}
	
	// Get Stock Out Limit 
	$qry_SO = "SELECT REPgetMOSScale('IT-001', 1, 4, 'SO', 'E') AS SO FROM DUAL";
	$qry_SO_res = mysql_fetch_array(mysql_query($qry_SO));
	$SO_end = $qry_SO_res['SO'];
	
	// Get Over Stock Limit 
	$qry_OS = "SELECT REPgetMOSScale('IT-001', 1, 4, 'OS', 'S') AS OS FROM DUAL";
	$qry_OS_res = mysql_fetch_array(mysql_query($qry_OS));
	$OS_start = $qry_OS_res['OS'];
	
	//select query
	//gets
	//item id
	//item name
	//OverStockPer
	//StockOutPer
	$data_qry = "SELECT
					A.item_id,
					itminfo_tab.itm_name,
					CEIL((SUM(IF(A.MOS >= $OS_start, 1, 0)) / COUNT(A.warehouse_id) * 100)) AS OverStockPer,
					CEIL((SUM(IF(A.MOS <= $SO_end, 1, 0)) / COUNT(A.warehouse_id) * 100)) AS StockOutPer
				FROM
					(
						SELECT
							tbl_hf_data.warehouse_id,
							tbl_hf_data.item_id,
							ROUND(IFNULL((tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 0), 2) AS MOS
						FROM
							tbl_hf_data
						INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
						WHERE
							tbl_hf_data.reporting_date = '$reporting_date'
						AND tbl_warehouse.stkid = $stakeholder_id
						AND tbl_warehouse.hf_type_id NOT IN (" . implode(',', $hfArr) . ")
						$where
						GROUP BY
							tbl_hf_data.warehouse_id,
							tbl_hf_data.item_id
					) A
				JOIN itminfo_tab ON A.item_id = itminfo_tab.itm_id
				INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
				WHERE
					stakeholder_item.stkid = ".$stakeholder_id."
				AND itminfo_tab.itm_category = 1
				".$product_filter."
				GROUP BY A.item_id
				ORDER BY itminfo_tab.frmindex ASC";
	//query result
	$qry_res = mysql_query($data_qry);
	//number of result
	$num = mysql_num_rows(mysql_query($data_qry));
	//check number of result
	if ($num > 0) {
		//fetch query result
		while ($row1 = mysql_fetch_array($qry_res)) {
			//item name
			$item_name[$row1['item_id']] = $row1['itm_name'];
			//Over Stock Per
			$over_stock[$row1['item_id']] = $row1['OverStockPer'];
			//Stock Out Per	
			$tock_out[$row1['item_id']] = $row1['StockOutPer'];
		}
		// Get Reporting Rate (All Warehouses)
		$reported_qry = "SELECT
							((B.reportedWH / A.totalWH) * 100) AS RR
						FROM
							(
								SELECT
									COUNT(DISTINCT tbl_warehouse.wh_id) AS totalWH
								FROM
									tbl_warehouse
								INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
								INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
								WHERE
									tbl_warehouse.wh_id NOT IN (
										SELECT
											warehouse_status_history.warehouse_id
										FROM
											warehouse_status_history
										INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
										WHERE
											warehouse_status_history.created_date <= '$reporting_date'
										AND warehouse_status_history.`status` = 0
										AND tbl_warehouse.stkid = 1
									)
								AND tbl_warehouse.reporting_start_month < '$reporting_date'
								AND tbl_warehouse.stkid = $stakeholder_id
								AND stakeholder.lvl = 7
								AND tbl_warehouse.hf_type_id NOT IN (" . implode(',', $hfArr) . ")
								$where
							) A
						JOIN (
							SELECT
								COUNT(DISTINCT tbl_warehouse.wh_id) AS reportedWH
							FROM
								tbl_warehouse
							INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
							INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
							INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
							WHERE
								tbl_warehouse.reporting_start_month < '$reporting_date'
							AND tbl_warehouse.stkid = $stakeholder_id
							AND stakeholder.lvl = 7
							AND tbl_hf_data.item_id = 1
							AND tbl_warehouse.hf_type_id NOT IN (" . implode(',', $hfArr) . ")
							$where
							AND tbl_hf_data.reporting_date = '$reporting_date'
						) B";
		//fetch query result
		$reported_qry_res = mysql_fetch_array(mysql_query($reported_qry));
		$reporting_rate = round($reported_qry_res['RR'], 1);
		//caption
		$caption = "Stock Out Vs Over Stock (Reporting Rate $reporting_rate%)";
		
		$location_id = ($level == 2) ? $province_id : $district_id;
		
		// Subcaption
		$sub_caption = getSubCaption($sector, $product_filter_id, $stakeholder_id, $level, $reporting_date, $location_id, 'Health Facilities');
		
		//download File Name 
		$download_file_name = $caption . ' - ' . $sub_caption . ' - ' . date('Y-m-d H:i:s');
		?>
		<a href="javascript:exportChart('<?php echo $chart_id . $stakeholder_id.'hf'; ?>', '<?php echo $download_file_name; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
		<?php
        $xmlstore = "<chart xAxisNamePadding='0' yAxisNamePadding='0' chartLeftMargin='0' chartRightMargin='0' chartTopMargin='0' chartBottomMargin='0' theme='fint' yAxisMaxValue='100' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$sub_caption' exportFileName='$download_file_name' yAxisName='Percentage' xAxisName='Products' numberSuffix='%' showValues='1' formatNumberScale='0'>";
        // Start Making Categories (Products)
        $xmlstore .= "<categories>";
        foreach ($item_name as $itemId => $itemName) {
            $xmlstore .= "<category label='$itemName' />";
        }
        $xmlstore .= "</categories>";

        // Stock Out Series
        $xmlstore .= "<dataset seriesName='Stock Out'>";
        foreach ($tock_out as $itemId => $value) {
            $param = base64_encode($itemId . '|' . $stakeholder_id . '|' . $reporting_date . '|SO' . '|' . $level . '|' . $province_id . '|' . $district_id . '|3');
            $xmlstore .= "<set value='$value' link=\"JavaScript:showData('$param');\" />";
        }
        $xmlstore .= "</dataset>";

        // Over Stock Series
        $xmlstore .= "<dataset seriesName='Over Stock'>";
        foreach ($over_stock as $itemId => $value) {
            $param = base64_encode($itemId . '|' . $stakeholder_id . '|' . $reporting_date . '|OS' . '|' . $level . '|' . $province_id . '|' . $district_id . '|3');
            $xmlstore .= "<set value='$value' link=\"JavaScript:showData('$param');\" />";
        }
        $xmlstore .= "</dataset>";

        $xmlstore .= "<trendlines>
            <line startvalue='$reporting_rate' displayvalue='Reporting Rate' valueonright='1' showvalue='1' showontop='1' color='009C00' />
          </trendlines>";


        $xmlstore .= "</chart>";
        
        //unset item name
        unset($itemName);
        //unset Stock out
        unset($tock_out);
        //unset over stock
        unset($over_stock);
    }
    return $xmlstore;
}
function districtMOS($reporting_date, $product_filter_id, $stakeholder_id, $district_id)
{
	/*********** Start: Product Filter ***********/
	if ($product_filter_id == 2) {
        //set Product filter
        $product_filter = " AND itminfo_tab.itmrec_id != 'IT-001' ";
    } else {
        //set Product  filter
        $product_filter = "";
    }
	/*********** End: Product Filter ***********/
	$qry = "SELECT
				itminfo_tab.itm_name,
				IFNULL(ROUND((summary_district.soh_district_lvl / summary_district.avg_consumption), 2), 0) AS MOS
			FROM
				summary_district
			INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
			WHERE
				itminfo_tab.itm_category = 1
			AND summary_district.reporting_date = '$reporting_date'
			AND summary_district.district_id = $district_id
			AND summary_district.stakeholder_id = $stakeholder_id
			$product_filter ";
	$qryRes = mysql_query($qry) or die(mysql_error());
	if (mysql_num_rows($qryRes) > 0) {
		return $qryRes;
	} else {
		return false;
	}
}