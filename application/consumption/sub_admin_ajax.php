<?php
/**
 * sub_admin_ajax
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");

// Show Districts
if (isset($_REQUEST['provId']) && isset($_REQUEST['stkId'])) {
    //get provId
    $provId = $_REQUEST['provId'];
    //get stkId
    $stkId = $_REQUEST['stkId'];
    //get distId
    $distId = $_REQUEST['distId'];
    //query 
    //gets
    //PkLocID
    //LocName
    $qry = "SELECT DISTINCT
				tbl_locations.PkLocID,
				tbl_locations.LocName
			FROM
				tbl_locations
			INNER JOIN tbl_warehouse ON tbl_locations.PkLocID = tbl_warehouse.dist_id
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			WHERE
				tbl_locations.LocLvl = 3
			AND tbl_locations.ParentID = $provId
			AND tbl_warehouse.stkid = $stkId
			ORDER BY
				tbl_locations.LocName ASC";
    //query result
    $qryRes = mysql_query($qry);
    ?>
    <label class="control-label">District</label>
    <select name="district" id="district" class="form-control input-sm" onchange="showStores(this.value)">
        <option value="">Select</option>
        <?php
        while ($row = mysql_fetch_array($qryRes)) {
            ?>
            <option value="<?php echo $row['PkLocID']; ?>" <?php echo ($distId == $row['PkLocID']) ? 'selected="selected"' : ''; ?>><?php echo $row['LocName']; ?></option>
            <?php
        }
        ?>
    </select>
    <?php
} else {
    $sel_province = '';
}

// Show Store / Facility
if (isset($_REQUEST['distId']) && !empty($_REQUEST['distId']) && !isset($_REQUEST['provId'])) {
    //get whid
    $whId = (!empty($_REQUEST['whId'])) ? $_REQUEST['whId'] : '';
    //get stkid
    $stkId = $_REQUEST['stkId'];
    //get distId
    $distId = $_REQUEST['distId'];
    //query checkHF
    $checkHF = "SELECT
					COUNT(tbl_warehouse.wh_id) AS num
				FROM
					tbl_warehouse
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				WHERE
					tbl_warehouse.dist_id = $distId
				AND tbl_warehouse.stkid = $stkId
				AND stakeholder.lvl = 7";
    //result
    $checkHFRes = mysql_fetch_array(mysql_query($checkHF));
    $lvl = '';
    if ($checkHFRes['num'] == 0) {
        $lvl = ' AND stakeholder.lvl IN (3, 4)';
    } else {
        $lvl = ' AND stakeholder.lvl IN (3, 7)';
    }

    $qry = "SELECT
					*
				FROM
					(
						SELECT
							tbl_warehouse.wh_id,
							tbl_warehouse.wh_name,
							stakeholder.lvl,
							tbl_hf_type_rank.hf_type_rank,
							tbl_warehouse.wh_rank
						FROM
							wh_user
						INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
						INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
						LEFT JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
						AND tbl_warehouse.prov_id = tbl_hf_type_rank.province_id
						AND tbl_warehouse.stkid = tbl_hf_type_rank.stakeholder_id
						WHERE
							tbl_warehouse.dist_id = " . $distId . "
						AND tbl_warehouse.stkid = " . $stkId . "
						$lvl
					) A
				GROUP BY
					A.wh_id
				ORDER BY
					A.lvl,
					IF (A.wh_rank = '' OR A.wh_rank IS NULL, 1, 0),
					A.wh_rank,
					IF (A.hf_type_rank = '' OR A.hf_type_rank IS NULL, 1, 0),
					A.hf_type_rank ASC,
					A.wh_name ASC";
    //query result
    $qryRes = mysql_query($qry);
    $num = mysql_num_rows($qryRes);
    ?>
    <label class="control-label">Store/Facility</label>
    <select name="warehouse" id="warehouse" class="form-control input-sm" required="required">
        <option value="">Select</option>
        <?php
        //get data from qryRes
        while ($row = mysql_fetch_array($qryRes)) {
            ?>
            <option value="<?php echo $row['wh_id']; ?>" <?php echo ($whId == $row['wh_id']) ? 'selected="selected"' : ''; ?>><?php echo $row['wh_name']; ?></option>
            <?php
        }
        ?>
    </select>
    <?php
}
//check wharehouse_id
if (isset($_POST['wharehouse_id'])) {
    //get wharehouse_id
    $whId = $_POST['wharehouse_id'];
    //query
    //gets
    //stkid
    //stkofficeid
    //level
    //dist_id
    $qry = "SELECT
				tbl_warehouse.stkid,
				tbl_warehouse.stkofficeid,
				stakeholder.lvl,
				tbl_warehouse.dist_id
			FROM
				tbl_warehouse
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
				tbl_warehouse.wh_id = $whId";
    //query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    $arr['Stakeholder'] = $qryRes['stkid'];
    $arr['StakeholderOfficeId'] = $qryRes['stkofficeid'];
    $arr['StoreLevel'] = $qryRes['lvl'];

    //Check Health Facilities
    //gets
    //count
    $checkHF = "SELECT
					COUNT(tbl_warehouse.wh_id) AS num
				FROM
					tbl_warehouse
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				WHERE
					tbl_warehouse.dist_id = " . $qryRes['dist_id'] . "
				AND tbl_warehouse.stkid = " . $qryRes['stkid'] . "
				AND stakeholder.lvl = 7";
    //query result
    $checkHFRes = mysql_fetch_array(mysql_query($checkHF));
    $arr['TotalHF'] = $checkHFRes['num'];
    //encode in json
    echo json_encode($arr);
}