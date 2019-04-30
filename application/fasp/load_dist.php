<?php

/**
 * load_districts
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
//stakeholder filter
$stkFilter = '';
// Show districts
if (isset($_REQUEST['provinceId'])) {
    //get province id
    $sel_district = (isset($_REQUEST['dId'])) ? $sel_district = $_REQUEST['dId'] : '';
    //get stakeholder id
    $stkFilter = (isset($_REQUEST['stkId']) && !empty($_REQUEST['stkId']) && $_REQUEST['stkId'] != 'all') ? " AND tbl_warehouse.stkid = " . $_REQUEST['stkId'] . " " : '';
    //get validate
    $validate = (isset($_POST['validate']) && $_POST['validate'] == 'no') ? '' : 'required';
    //get validate
    $select = (isset($_POST['validate']) && $_POST['validate'] == 'no') ? 'All' : 'Select';
    //select query
    //gets
    //pk id
    //location name
    $qry = "SELECT DISTINCT
				tbl_locations.PkLocID,
				tbl_locations.LocName
			FROM
				tbl_warehouse
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
			WHERE
				tbl_warehouse.prov_id = " . $_REQUEST['provinceId'] . "
			$stkFilter
			ORDER BY
				tbl_locations.LocName ASC";
    //query result
    $qryRes = mysql_query($qry);
    ?>
    <div class="col-md-6" id="province_div">
        <div class="control-group">
        <label class="control-label">District</label>
        </div>
    </div>
    <div class="col-md-6" id="province_div">
        <div class="control-group">
            <select name="district" id="district" class="form-control input-sm" <?php echo $validate; ?>>

                <?php
                //select
                $sel = ($sel_district == 'all') ? 'selected' : '';
                echo (isset($_POST['allOpt']) && $_POST['allOpt'] == 'yes') ? "<option value='all' $sel>All</option>" : '';
                ?>
                <?php
                //fetch results
                while ($row = mysql_fetch_array($qryRes)) {
                    //populate combo
                    ?>
                    <option value="<?php echo $row['PkLocID']; ?>" <?php echo ($sel_district == $row['PkLocID']) ? 'selected=selected' : '' ?>><?php echo $row['LocName']; ?></option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <?php
}