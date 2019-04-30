<?php
include("../includes/classes/AllClasses.php");
$prov_filter = '';
$province = $_REQUEST['provinceId'];
$prov_filter = "tbl_warehouse.prov_id = " . $_REQUEST['provinceId'];
$stk_chk = $_REQUEST['stkId'];

//    print_r($stk_chk);

$stkFilter = (isset($_REQUEST['stkId']) && !empty($_REQUEST['stkId']) && $_REQUEST['stkId'] != 'all') ? " AND tbl_warehouse.stkid =" . $stk_chk  : '';

$qry = "SELECT DISTINCT
				tbl_locations.PkLocID,
				tbl_locations.LocName
			FROM
				tbl_warehouse
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
			WHERE
				$prov_filter
			$stkFilter
			ORDER BY
				tbl_locations.LocName ASC";
//    echo $qry;
//query result
$qryRes = mysql_query($qry);
?>
<label class="control-label">District</label>
<select name="district" id="district" class="form-control input-sm"  >

    <option>Select</option>
    <?php
    //fetch results
    while ($row = mysql_fetch_array($qryRes)) {
        //populate combo
        ?>
        <option value="<?php echo $row['PkLocID']; ?>"><?php echo $row['LocName']; ?></option>
        <?php
    }
    ?>
</select>
