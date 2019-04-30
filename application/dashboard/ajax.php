<?php
/**
 * ajax
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
include(APP_PATH . "includes/classes/db.php");
//get level
$level = $_POST['lvl'];
//get prov id
$provId = isset($_POST['provId']) ? $_POST['provId'] : '';
//get dist id
$distId = isset($_POST['distId']) ? $_POST['distId'] : '';
//check level
if ($level == 2) {
    //query
    //gets
    //PkLocID
    //Loc name
    $qry = "SELECT
				tbl_locations.PkLocID,
				tbl_locations.LocName
			FROM
				tbl_locations
			WHERE
				tbl_locations.LocLvl = 2
			AND tbl_locations.ParentID IS NOT NULL";
    //query result
    $qryRes = mysql_query($qry);
    ?>
    <label for="office-Level">Province</label>
    <div class="form-group">
        <select name="prov_id" id="prov_id" class="form-control input-sm" onchange="showDistricts()">
            <?php
            //get query result
            while ($row = mysql_fetch_array($qryRes)) {
                $sel = ($provId == $row['PkLocID']) ? 'selected="selected"' : '';
                //populate prov_id combo
                echo "<option value=\"$row[PkLocID]\" $sel>$row[LocName]</option>";
            }
            ?>

        </select>
    </div>
    <?php
}
//check level
if ($level == 3) {
    //get prov id
    $prov_id = $_POST['prov_id'];
//query
    //gets
    //PkLocid
    //LocName
    $qry = "SELECT
				tbl_locations.PkLocID,
				tbl_locations.LocName
			FROM
				tbl_locations
			WHERE
				tbl_locations.ParentID = $prov_id
			ORDER BY
				tbl_locations.LocName ASC";
    //query result
    $qryRes = mysql_query($qry);
    ?>
    <label for="office-Level">District</label>
    <div class="form-group">
        <select name="dist_id" id="dist_id" class="form-control input-sm">
    <?php
    if(!empty($_REQUEST['all_option']) && $_REQUEST['all_option']=='yes')
        echo '<option value="">All</option>';
    //fetch results
    while ($row = mysql_fetch_array($qryRes)) {
        $sel = ($distId == $row['PkLocID']) ? 'selected="selected"' : '';
        //populate dist_id combo
        echo "<option value=\"$row[PkLocID]\" $sel>$row[LocName]</option>";
    }
    ?>

        </select>
    </div>
            <?php
        }
if (isset($_POST['required_value']) && $_POST['required_value']=='funding_sources') {
    $prov_id = $_POST['prov_id'];
    $qry = "SELECT
                DISTINCT 
                tbl_warehouse.wh_id,
                tbl_warehouse.wh_name
                FROM
                funding_stk_prov
                INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                WHERE
                funding_stk_prov.province_id = $prov_id";
    //query result
    $qryRes = mysql_query($qry);
    //fetch results
    
    echo "<option value=\"\">All</option>";
    while ($row = mysql_fetch_array($qryRes)) {
        //populate dist_id combo
        echo "<option value=\"$row[wh_id]\">$row[wh_name]</option>";
    }
}
if (isset($_POST['required_value']) && $_POST['required_value']=='stakeholders') {
    $prov_id = $_POST['prov_id'];
    $qry = "SELECT
stakeholder.stkid,
stakeholder.stkname
FROM
stakeholder
WHERE
stakeholder.stk_type_id in (0,1) AND
stakeholder.lvl = 1
";
    //query result
    $qryRes = mysql_query($qry);
    //fetch results
    
    echo "<option value=\"\">All</option>";
    while ($row = mysql_fetch_array($qryRes)) {
        //populate dist_id combo
        echo "<option value=\"$row[stkid]\">$row[stkname]</option>";
    }
}
// Stock Status
if (isset($_REQUEST['stockStatus'])) {
	$param = explode('|', base64_decode(mysql_real_escape_string($_REQUEST['stockStatus'])));
	//item id
	$itemId = $param[0];
	//stk id
	$stkid = $param[1];
	//report date
	$rptDate = $param[2];
	//type
	$type = $param[3];
	//level
	$lvl = $param[4];
	//g type
	$gType = (!empty($param[7])) ? $param[7] : '';

	$where = '';
	$where1 = '';
	// If Provincial Level then get Province Name
	if ($lvl == 2) {
		//get prov id	
		$prov_id = $param[5];
		//prov query
		//gets
		//prov
		$prov = mysql_fetch_array(mysql_query("SELECT
											tbl_locations.LocName
										FROM
											tbl_locations
										WHERE
											tbl_locations.PkLocID = $prov_id"));
		$provName = $prov['LocName'];
		$where .= " AND summary_district.province_id = $prov_id";
		// For Health Facilities
		$where1 .= " AND tbl_warehouse.prov_id = $prov_id";
	}
	// If District Level then get District Name
	else if ($lvl == 3) {
		//get dist id
		$dist_id = $param[6];
		//dist query
		//gets
		//dist
		$dist = mysql_fetch_array(mysql_query("SELECT
											tbl_locations.LocName
										FROM
											tbl_locations
										WHERE
											tbl_locations.PkLocID = $dist_id"));
		//dist name
		$distName = $dist['LocName'];
		//filter
		$where .= " AND summary_district.district_id = $dist_id";
		//For health facilities
		$where1 .= " AND tbl_warehouse.dist_id = $dist_id";
	}

	// For District Store
	if($gType == 1){
		//if type is Stock Out	
		if ($type == 'SO') {
			//title	
			$title = 'Stock Out ';
			//Query
			//gets
			//district_id
			//district_name
			//MOS
			$qry = "SELECT * FROM (SELECT
				summary_district.district_id,
				tbl_locations.LocName AS districtName,
				ROUND(IFNULL((summary_district.soh_district_store / summary_district.avg_consumption), 0), 2) AS MOS
			FROM
				summary_district
			INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
			INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
			WHERE
				summary_district.reporting_date = '$rptDate'
			AND summary_district.stakeholder_id = $stkid
			AND summary_district.item_id = '$itemId'
			$where
			GROUP BY
				summary_district.item_id,
				summary_district.district_id
			ORDER BY
				tbl_locations.ParentID ASC,
				districtName ASC
			) A
			WHERE A.MOS <= REPgetMOSScale('$itemId', $stkid, 3, 'SO', 'E')";
		}
		//if type is Over Stock
		else if ($type == 'OS') {
			//title

			$title = 'Over Stock ';
			//Query
			//gets
			//district_id
			//district_name
			//MOS
			$qry = "SELECT * FROM (SELECT
				summary_district.district_id,
				tbl_locations.LocName AS districtName,
				ROUND(IFNULL((summary_district.soh_district_store / summary_district.avg_consumption), 0), 2) AS MOS
			FROM
				summary_district
			INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
			INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
			$where
			WHERE
				summary_district.reporting_date = '$rptDate'
			AND summary_district.stakeholder_id = $stkid
			AND summary_district.item_id = '$itemId'
			GROUP BY
				summary_district.item_id,
				summary_district.district_id
			ORDER BY
				tbl_locations.ParentID ASC,
				districtName ASC
			) A
			WHERE A.MOS >= REPgetMOSScale('$itemId', $stkid, 3, 'OS', 'S')";
		}
	}
	// For Field Store
	else if ($gType == 2) {
		//if type is Stock Oout
		if ($type == 'SO') {
			//title
			$title = 'Stock Out ';
			//select query
			//gets
			//district_id
			//district_name
			//MOS

			$qry = "SELECT * FROM (SELECT
				summary_district.district_id,
				tbl_locations.LocName AS districtName,
				ROUND(IFNULL(((summary_district.soh_district_lvl - summary_district.soh_district_store) / summary_district.avg_consumption), 0), 2) AS MOS
			FROM
				summary_district
			INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
			INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
			WHERE
				summary_district.reporting_date = '$rptDate'
			AND summary_district.stakeholder_id = $stkid
			AND summary_district.item_id = '$itemId'
			$where
			GROUP BY
				summary_district.item_id,
				summary_district.district_id
			ORDER BY
				tbl_locations.ParentID ASC,
				districtName ASC
			) A
			WHERE A.MOS <= REPgetMOSScale('$itemId', $stkid, 4, 'SO', 'E')";
		}
		//if type is Over Stock
		else if ($type == 'OS') {
			//title	
			$title = 'Over Stock ';
			//select
			//query
			//gets
			//district_id
			//district_name
			//MOS
			$qry = "SELECT * FROM (SELECT
				summary_district.district_id,
				tbl_locations.LocName AS districtName,
				ROUND(IFNULL(((summary_district.soh_district_lvl - summary_district.soh_district_store) / summary_district.avg_consumption), 0), 2) AS MOS
			FROM
				summary_district
			INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
			INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
			$where
			WHERE
				summary_district.reporting_date = '$rptDate'
			AND summary_district.stakeholder_id = $stkid
			AND summary_district.item_id = '$itemId'
			GROUP BY
				summary_district.item_id,
				summary_district.district_id
			ORDER BY
				tbl_locations.ParentID ASC,
				districtName ASC
			) A
			WHERE A.MOS >= REPgetMOSScale('$itemId', $stkid, 4, 'OS', 'S')";
		}
	}
	// For Health Facilities
	else if($gType == 3)
	{
		// Get Stock Out Limit 
		$qry_SO = "SELECT REPgetMOSScale('IT-001', 1, 4, 'SO', 'E') AS SO FROM DUAL";
		$qry_SO_res = mysql_fetch_array(mysql_query($qry_SO));
		$SO_end = $qry_SO_res['SO'];
		
		// Get Over Stock Limit 
		$qry_OS = "SELECT REPgetMOSScale('IT-001', 1, 4, 'OS', 'S') AS OS FROM DUAL";
		$qry_OS_res = mysql_fetch_array(mysql_query($qry_OS));
		$OS_start = $qry_OS_res['OS'];
		
		//if type is Stock Out	
		if ($type == 'SO') {
			//title	
			$title = 'Stock Out ';
			//Query
			//gets
			//district_id
			//district_name
			//MOS
			$qry = "SELECT
						*
					FROM
						(
							SELECT
								tbl_locations.LocName AS districtName,
								tbl_warehouse.wh_name,
								ROUND(IFNULL((tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 0), 2) AS MOS
							FROM
								tbl_hf_data
							INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
							INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
							WHERE
								tbl_hf_data.reporting_date = '$rptDate'
							AND tbl_warehouse.stkid = $stkid
							AND tbl_hf_data.item_id = '$itemId'
							AND tbl_warehouse.hf_type_id NOT IN (" . implode(',', $hfArr) . ")
							$where1
							GROUP BY
								tbl_hf_data.warehouse_id
							ORDER BY
								tbl_locations.ParentID ASC,
								tbl_locations.LocName ASC,
								IF(tbl_warehouse.wh_rank = '' OR tbl_warehouse.wh_rank IS NULL, 1, 0),
								tbl_warehouse.wh_rank,
								tbl_warehouse.wh_name ASC
						) A
					WHERE
						A.MOS <= $SO_end";
		}
		//if type is Over Stock
		else if ($type == 'OS') {
			//title

			$title = 'Over Stock ';
			//Query
			//gets
			//district_id
			//district_name
			//MOS
			$qry = "SELECT
						*
					FROM
						(
							SELECT
								tbl_locations.LocName AS districtName,
								tbl_warehouse.wh_name,
								ROUND(IFNULL((tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 0), 2) AS MOS
							FROM
								tbl_hf_data
							INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
							INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
							WHERE
								tbl_hf_data.reporting_date = '$rptDate'
							AND tbl_warehouse.stkid = $stkid
							AND tbl_hf_data.item_id = '$itemId'
							AND tbl_warehouse.hf_type_id NOT IN (" . implode(',', $hfArr) . ")
							$where1
							GROUP BY
								tbl_hf_data.warehouse_id
							ORDER BY
								tbl_locations.ParentID ASC,
								tbl_locations.LocName ASC,
								IF(tbl_warehouse.wh_rank = '' OR tbl_warehouse.wh_rank IS NULL, 1, 0),
								tbl_warehouse.wh_rank,
								tbl_warehouse.wh_name ASC
						) A
					WHERE
						A.MOS >= $OS_start";
		}
	}
	//query result
	$qryRes = mysql_query($qry);
	// Prepare Title as per user selected level
	if($lvl == 1){
		$level = 'All Pakistan District Stores';
	}else if($lvl == 2){
		if($gType == 1){
			$level = $provName . ' District Stores';
		}else if($gType == 2){
			$level = $provName . ' Field Stores';
		}else if($gType == 3){
			$level = $provName . (($stkid == 73) ? ' CMWs' : ' Health Facilities');
		}
	}else if($lvl == 3){
		$level = $distName . (($stkid == 73) ? ' CMWs' : ' Health Facilities');
	}
	
	echo '<b>' . $title . ' - ' . $level . '</b>';
	?>
    <table class="table table-striped table-bordered table-condensed dataTable" style="margin-bottom:10px;">
        <thead>
            <tr>
                <th width="10%">Sr. No.</th>
                <th>District</th>
                <?php
                if($gType == 3){
                    echo "<th>";
                    echo ($stkid == 73) ? 'CMW' : 'Health Facility';
                    echo "</th>";
                }?>
                <th width="15%">MOS</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $count = 1;
        //fetch result
        while ($row = mysql_fetch_array($qryRes)) {
        ?>
            <tr>
                <td class="center"><?php echo $count++; ?></td>
                <td><?php echo $row['districtName']; ?></td>
                <?php if($gType == 3){?>
                <td><?php echo $row['wh_name']; ?></td>
                <?php }?>
                <td class="right"><?php echo $row['MOS']; ?></td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
	* MOS - Month of Stock<br />
    <?php if($gType == 3){?>
	<mark>Note: This report does not inclulde (RHS-B, MSU, Social Mobilizer, PLDs, RMPS, Hakeems, Homopaths, DDPs, TBAs, Counters)</mark>
    <?php }?>
	<?php
}