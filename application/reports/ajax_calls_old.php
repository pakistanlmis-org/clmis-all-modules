<?php
/**
 * ajax_calls
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
if (isset($_REQUEST['val'])) {
    // Show provinces
    if ($_REQUEST['val'] == 'provincial' || $_REQUEST['val'] == 'district' || $_REQUEST['val'] == 'field') {
        //check pk id
        if (isset($_REQUEST['pId'])) {
            //set selected province
            $sel_province = $_REQUEST['pId'];
        } else {
            //set selected province
            $sel_province = '';
        }
        //select query
        //gets
        //pk location id
        //location name
        $qry = "SELECT  PkLocID, LocName
				FROM    tbl_locations
				WHERE   LocLvl = 2 AND ParentID IS NOT NULL";
        //result
        $qryRes = mysql_query($qry);
        ?>
        <label class="control-label">Province</label>
        <select name="province" id="province" class="form-control input-sm" onchange="showDistricts(this.value)" required>
            <option value="">Select</option>
            <?php
            //fetch result
            while ($row = mysql_fetch_array($qryRes)) {
                //populate combo
                ?>
                <option value="<?php echo $row['PkLocID']; ?>" <?php echo ($sel_province == $row['PkLocID']) ? 'selected=selected' : '' ?>><?php echo $row['LocName']; ?></option>
                <?php
            }
            ?>
        </select>
        <?php
    }
}

// Show districts
if (isset($_REQUEST['provinceId'])) {
    //get province id
    $sel_district = (isset($_REQUEST['dId'])) ? $sel_district = $_REQUEST['dId'] : '';
    //get stakeholder id
//    print_r($_REQUEST['stkId']);
//    exit;
    if (is_array($_REQUEST['stkId'])) {
        if (!empty($_REQUEST['stkId'])) {
            $stk_chk = implode(',', ($_REQUEST['stkId']));
        } else {
            $stk_chk = '';
        }
    } else {
        $stk_chk = $_REQUEST['stkId'];
    }
//    print_r($stk_chk);

    $stkFilter = (isset($_REQUEST['stkId']) && !empty($_REQUEST['stkId']) && $_REQUEST['stkId'] != 'all') ? " AND tbl_warehouse.stkid IN (" . $stk_chk . " )" : '';
//    print_r($stkFilter);
//    print_r((isset($_REQUEST['stkId']) && !empty($_REQUEST['stkId']) && $_REQUEST['stkId'] != 'all') ? " AND tbl_warehouse.stkid IN (" . $stk_chk . " )" : '');
//get validate
    $validate = (isset($_POST['validate']) && $_POST['validate'] == 'no') ? '' : 'required';
    //get validate
    $select = (isset($_POST['validate']) && $_POST['validate'] == 'no') ? 'All' : 'All';
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
//    echo $qry;exit;
    //query result
    $qryRes = mysql_query($qry);
    ?>
    <label class="control-label">District</label>
    <select name="district" id="district" class="form-control input-sm"  >
<!--        <option value="" <?php echo ($sel_district == '') ? 'selected' : ''; ?>><?php echo $select; ?></option>-->
        <?php
        //select
        $sel = ($sel_district == 'all') ? 'selected' : '';
        echo (isset($_POST['allOpt']) && $_POST['allOpt'] == 'yes') ? "<option value='' $sel>All</option>" : '';
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
    <?php
}

if (isset($_REQUEST['stakeholder'])) {
    //get stakeholder
//    print_r($_REQUEST['stakeholder']);
    if (is_array($_REQUEST['stakeholder'])) {
        $stk = implode(',', $_REQUEST['stakeholder']);
    } else {
        $stk = $_REQUEST['stakeholder'];
    }
    //get product Id
    $pro = $_REQUEST['productId'];
    //get show pk id
    $showPkId = (isset($_REQUEST['showPkId'])) ? $_REQUEST['showPkId'] : '';
    //get validate
    $select = (isset($_POST['validate']) && $_POST['validate'] == 'no') ? 'All' : 'Select';
    //check stakeholder
    if (!empty($stk) && $stk != 'all') {
        //set stakeholder filter
        $stkFilter = " AND stakeholder_item.stkid IN ($stk)";
    } else if (empty($stk)) {
        //set stakeholder filter
        $stkFilter = " AND stakeholder_item.stkid = 0";
    }
       
    if(!empty($_REQUEST['stakeholder']) && $_REQUEST['stakeholder'] == 2 )
    $catFilter = " AND itminfo_tab.itm_category in (1,5) ";
    else
    $catFilter = " AND itminfo_tab.itm_category  = 1  ";
    //select query
    //gets
    //item rec id
    //item id
    //item name
    $querypro = "SELECT DISTINCT
					itminfo_tab.itmrec_id,
					itminfo_tab.itm_id,
					itminfo_tab.itm_name,
                                        itminfo_tab.itm_category
				FROM
					itminfo_tab
				INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
				WHERE
					itminfo_tab.itm_status = 1
				$stkFilter
				$catFilter
				ORDER BY
					itminfo_tab.frmindex ASC";
//    echo $querypro;
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
        $grp = '';
        if($rowpro['itm_category'] == '1' ) $grp = 'FP Products';
        elseif($rowpro['itm_category'] == '2' ) $grp = 'Surgery Cases';
        elseif($rowpro['itm_category'] == '5' ) $grp = 'MCH Products';
        
        if($last_cat != $rowpro['itm_category'] && !empty($_REQUEST['stakeholder']) && $_REQUEST['stakeholder']=='2') echo '<optgroup label="'.$grp.'">';
        ?>
        <option value="<?php echo $rowpro['itmrec_id']; ?>" <?php echo $sel; ?>><?php echo $rowpro['itm_name']; ?></option>
        <?php
        $last_cat = $rowpro['itm_category'];
    }
}


if (isset($_REQUEST['stakeholder_id'])) {
    //get stakeholder
    $stk = $_REQUEST['stakeholder_id'];
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
        if ($rowpro['itm_id'] == $pro) {
            $sel = "selected='selected'";
        } else {
            $sel = "";
        }
        ?>
        <option value="<?php echo $rowpro['itm_id']; ?>" <?php echo $sel; ?>><?php echo $rowpro['itm_name']; ?></option>
        <?php
    }
}
// Warehouses
if (isset($_REQUEST['stkId']) && isset($_REQUEST['provId']) && isset($_REQUEST['distId'])) {
    //get warehouse id
    $whId = isset($_REQUEST['whId']) ? $_REQUEST['whId'] : '';
    //get district id
    $distId = isset($_REQUEST['distId']) ? $_REQUEST['distId'] : '';
    //province id
    $provId = isset($_REQUEST['provId']) ? $_REQUEST['provId'] : '';
    //stakeholder id
    $stkId = isset($_REQUEST['stkId']) ? $_REQUEST['stkId'] : '';
    //product
    $product = isset($_REQUEST['product']) ? $_REQUEST['product'] : '';
    //date from
    $dateFrom = isset($_REQUEST['dateFrom']) ? dateToDbFormat($_REQUEST['dateFrom']) : '';
    //date to
    $dateTo = isset($_REQUEST['dateTo']) ? dateToDbFormat($_REQUEST['dateTo']) : '';
    //where
    $where = '1=1';
    $where .= (!empty($distId)) ? " AND tbl_warehouse.dist_id = $distId" : '';
    $where .= (!empty($provId)) ? " AND tbl_warehouse.prov_id = $provId" : '';
    $where .= (!empty($stkId) && $stkId != 'all') ? " AND tbl_warehouse.stkid = $stkId" : '';
    $where .= (!empty($product) && $product != 'all') ? " AND stock_batch.item_id = $product" : '';
    $where .= (!empty($dateFrom) && !empty($dateTo)) ? " AND tbl_stock_master.TranDate BETWEEN '$dateFrom' AND '$dateTo' " : '';
    //select query
    //gets
    //warehouse id
    //warehouse name
    $qry = "SELECT DISTINCT
				tbl_warehouse.wh_id,
				CONCAT(tbl_warehouse.wh_name,	'(', stakeholder.stkname, ')') AS wh_name
			FROM
				tbl_warehouse
			INNER JOIN tbl_stock_master ON tbl_warehouse.wh_id = tbl_stock_master.WHIDTo
			INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
			INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
				$where
			ORDER BY
				tbl_warehouse.wh_name ASC";
    //query result
    $qryRes = mysql_query($qry);
    echo '<option value="">Select</option>';
    echo '<option value="all">All</option>';
    //fetch result
    while ($row = mysql_fetch_array($qryRes)) {
        $sel = ($row['wh_id'] == $whId) ? 'selected="selected"' : '';
        echo "<option value=\"$row[wh_id]\" $sel>$row[wh_name]</option>";
    }
}


// Show HF types of the selected Province
if (isset($_REQUEST['hfTypeId']) && $_REQUEST['provId']) {
    //get hf type
    $hfType = $_REQUEST['hfTypeId'];
    //get province id
    $provId = $_REQUEST['provId'];
    //select query
    //gets
    //pk id
    //hf type
    $qry = "SELECT
				tbl_hf_type.pk_id,
				tbl_hf_type.hf_type
			FROM
				tbl_hf_type
			INNER JOIN tbl_hf_type_rank ON tbl_hf_type.pk_id = tbl_hf_type_rank.hf_type_id
			WHERE
				tbl_hf_type_rank.stakeholder_id = 1
			AND tbl_hf_type_rank.province_id = $provId
			ORDER BY
				tbl_hf_type_rank.hf_type_rank ASC";
    //query result
    $qryRes = mysql_query($qry) or die();

    $sel = ($hfType == 0) ? 'selected="selected"' : '';
    echo "<option value=''>Select</option>";


    while ($row = mysql_fetch_array($qryRes)) {
        if ($row['pk_id'] == $hfType) {
            $sel = "selected='selected'";
        } else {
            $sel = "";
        }
        ?>
        <option value="<?php echo $row['pk_id']; ?>" <?php echo $sel; ?>><?php echo $row['hf_type']; ?></option>
        <?php
    }
}
// Show Satellite Camps for the Selected District
if (isset($_REQUEST['campId']) && $_REQUEST['districtId']) {
    //get campaign id
    $campId = $_REQUEST['campId'];
    //get district id
    $districtId = $_REQUEST['districtId'];
    //get province id
    $provId = $_REQUEST['provId'];
    $qry = "SELECT
				A.wh_id,
				A.wh_name
			FROM
				(
					SELECT
						tbl_warehouse.wh_id,
						tbl_warehouse.wh_name,
						tbl_hf_type_rank.hf_type_rank,
						tbl_warehouse.wh_rank
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
					INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
					WHERE
						stakeholder.lvl > 4
					AND tbl_warehouse.dist_id = $districtId
					AND tbl_hf_type_rank.stakeholder_id = 1
					AND tbl_hf_type_rank.province_id = $provId
					AND tbl_warehouse.hf_type_id IN (1, 2)
				) A
			GROUP BY
				A.wh_id
			ORDER BY
				IF(A.wh_rank = '' OR A.wh_rank IS NULL, 1, 0),
				A.wh_rank,
				A.hf_type_rank ASC,
				A.wh_name ASC";
    $qryRes = mysql_query($qry) or die();
    ?>
    <option value="">Select</option>
    <option value="all" <?php echo ($campId == 'all') ? 'selected="selected"' : ''; ?>>All (FWCs & MSUs)</option>
    <optgroup label="Type wise">
        <option value="fwc" <?php echo ($campId == 'fwc') ? 'selected="selected"' : ''; ?>>All FWCs</option>
        <option value="msu" <?php echo ($campId == 'msu') ? 'selected="selected"' : ''; ?>>All MSUs</option>
    </optgroup>
    <optgroup label="Individual Satellite Camp">
        <?php
        while ($row = mysql_fetch_array($qryRes)) {
            if ($row['wh_id'] == $campId) {
                $sel = "selected='selected'";
            } else {
                $sel = "";
            }
            ?>
            <option value="<?php echo $row['wh_id']; ?>" <?php echo $sel; ?>><?php echo $row['wh_name']; ?></option>
        <?php }
        ?>
    </optgroup>
    <?php
}