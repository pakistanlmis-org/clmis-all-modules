<?php
/**
 * my_report-ajax
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

// Show provinces
if (isset($_REQUEST['provId']) && isset($_REQUEST['stkId'])) {
    //get province id
    $provId = $_REQUEST['provId'];
    //get stakeholder id
    $stkId = $_REQUEST['stkId'];
    //get distrcit id
    $distId = $_REQUEST['distId'];
    //select query
    //gets
    //pk location id
    //location name
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
    $required = (isset($_REQUEST['showAll']) && $_REQUEST['showAll'] == 1) ? 'required="required"' : '';
    ?>
    <label class="control-label">District</label>
    <div class="form-group">
    <select name="district" id="district" class="form-control input-sm" <?php echo $required; ?>>
        <?php
        echo (isset($_REQUEST['showAll']) && $_REQUEST['showAll'] == 1) ? '<option value="">Select</option>' : '';
        while ($row = mysql_fetch_array($qryRes)) {
            ?>
            <option value="<?php echo $row['PkLocID']; ?>" <?php echo ($distId == $row['PkLocID']) ? 'selected=selected' : '' ?>><?php echo $row['LocName']; ?></option>
            <?php
        }
        ?>
    </select>
    </div>
    <?php
} else {
    $sel_province = '';
}

// Show Stores/Facilities
if (isset($_REQUEST['distId']) && !empty($_REQUEST['distId']) && isset($_REQUEST['whId'])) {
    //get warehouse id
    $whId = (!empty($_REQUEST['whId'])) ? $_REQUEST['whId'] : '';
    //get stakeholder id
    $stkId = $_REQUEST['stkId'];
    //get district id
    $distId = $_REQUEST['distId'];
    //select query
    //gets
    //warehouse name
    //warehouse id
    //stakeholder id
    //stakeholder level
    //hf type rank
    //warehouse rank
    $qry = "SELECT
					*
				FROM
					(
						SELECT
							tbl_warehouse.wh_id,
							tbl_warehouse.wh_name,
							tbl_warehouse.stkid,
							stakeholder.lvl,
							tbl_hf_type_rank.hf_type_rank,
							tbl_warehouse.wh_rank
						FROM
							tbl_warehouse
						INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
						LEFT JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
						AND tbl_warehouse.prov_id = tbl_hf_type_rank.province_id
						AND tbl_warehouse.stkid = tbl_hf_type_rank.stakeholder_id
						WHERE
							tbl_warehouse.dist_id = " . $distId . "
						AND tbl_warehouse.stkid = " . $stkId . "
						AND stakeholder.lvl > 2
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
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    //num
    $num = mysql_num_rows($qryRes);
    ?>
    <label class="control-label">Store/Facility</label>
    <div class="form-group">
    <select name="warehouse" id="warehouse" class="form-control input-sm">
        <option value="">All</option>
        <?php
        //level
        $lvl = '';
        //fetch result
        while ($row = mysql_fetch_array($qryRes)) {
            //check level
            if ($lvl != $row['lvl'] && $row['lvl'] == 7) {
                //check level
                if ($row['lvl'] == 7 && $row['stkid'] != 73) {
                    //set group
                    $group = "Health Facilities";
                } else if ($row['lvl'] == 7 && $row['stkid'] == 73) {
                    //set group
                    $group = "CMWs";
                }
                echo "<optgroup label=\"$group\">";
                $lvl = $row['lvl'];
            }
            ?>
            <option value="<?php echo $row['wh_id']; ?>" <?php echo ($whId == $row['wh_id']) ? 'selected=selected' : '' ?>><?php echo $row['wh_name']; ?></option>
            <?php
        }
        echo "</optgroup>";
        ?>
    </select>
    </div>
    <?php
}

if (isset($_REQUEST['stakeholder'])) {
    $stk = $_REQUEST['stakeholder'];
    $pro = $_REQUEST['productId'];

    if (!empty($stk) && $stk != 'all') {
        $stkFilter = " AND stakeholder_item.stkid = $stk";
    } else if (empty($stk)) {
        $stkFilter = " AND stakeholder_item.stkid = 0";
    }

    //select query
    //gets
    //item rec id
    //item id
    //item name
    $querypro = "SELECT DISTINCT
					itminfo_tab.itmrec_id,
					itminfo_tab.itm_id,
					itminfo_tab.itm_name
				FROM
					itminfo_tab
				INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
				WHERE
					itminfo_tab.itm_status = 1
				$stkFilter
				AND itminfo_tab.itm_category = 1
				ORDER BY
					itminfo_tab.frmindex ASC";
    //query result
    $rspro = mysql_query($querypro) or die();
    echo '<option value="">Select</option>';
    //fetch result
    while ($rowpro = mysql_fetch_array($rspro)) {
        if ($rowpro['itmrec_id'] == $pro) {
            $sel = "selected='selected'";
        } else {
            $sel = "";
        }
        ?>
        <option value="<?php echo $rowpro['itmrec_id']; ?>" <?php echo $sel; ?>><?php echo $rowpro['itm_name']; ?></option>
        <?php
    }
}
?>