<?php
//print_r($_REQUEST);exit;
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

        <option value="" <?php echo ($sel_district == '') ? 'selected' : ''; ?>><?php echo $select; ?></option>
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
        
}

if (isset($_REQUEST['stakeholder'])) {
    //get stakeholder
    $stk = $_REQUEST['stakeholder'];
    //get product Id
    $pro = $_REQUEST['productId'];
    //get show pk id
    $showPkId = (isset($_REQUEST['showPkId'])) ? $_REQUEST['showPkId'] : '';
    //get validate
    $select = (isset($_POST['validate']) && $_POST['validate'] == 'no') ? 'All' : 'Select';
    //check stakeholder
    if (!empty($stk) && $stk != 'all') {
        //set stakeholder filter
        $stkFilter = " AND stakeholder_item.stkid = $stk";
    } else if (empty($stk)) {
        //set stakeholder filter
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
    //select
    $sel = ($pro == '') ? 'selected' : '';
    echo "<option value='' $sel>$select</option>";
    //fetch results
    while ($rowpro = mysql_fetch_array($rspro)) {
        //check item rec id
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
