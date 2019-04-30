<?php

/**
 * ajax_combos
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
//get SkOfcLvl
$skOfcLvl = isset($_REQUEST['SkOfcLvl']) ? $_REQUEST['SkOfcLvl'] : '';
//get provId
$provId = isset($_REQUEST['provId']) ? $_REQUEST['provId'] : '';
//get distId
$distId = isset($_REQUEST['distId']) ? $_REQUEST['distId'] : '';
//get provSelId
$provSelId = isset($_REQUEST['provSelId']) ? $_REQUEST['provSelId'] : '';
//get distSelId
$distSelId = isset($_REQUEST['distSelId']) ? $_REQUEST['distSelId'] : '';
//get tehSelId
$tehSelId = isset($_REQUEST['tehSelId']) ? $_REQUEST['tehSelId'] : '';

if ($skOfcLvl != 1 && empty($provId) && empty($distId)) {
    echo "<option value=\"all\">All</option>";
//query 
    //gets
    //PkLocID
    //LocName
    $qry = "SELECT
				Province.PkLocID,
				Province.LocName
			FROM
				tbl_locations AS Province
			WHERE
				Province.LocLvl = 2
			AND Province.ParentID IS NOT NULL
			ORDER BY
				Province.PkLocID ASC";
    //query result
    $rsQry = mysql_query($qry) or die();
    //fetch data from rsQry
    while ($row = mysql_fetch_array($rsQry)) {
        if ($provSelId == $row['PkLocID']) {
            $sel = "selected='selected'";
        } else {
            $sel = "";
        }
        ?>
        <option value="<?php echo $row['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $row['LocName']; ?></option>
        <?php
    }
}
if ($skOfcLvl != 1 && !empty($provId)) {
    echo "<option value=\"all\">All</option>";
    //query 
    //gets
    //PkLocID
    //LocName
    $qry = "SELECT DISTINCT
				tbl_locations.PkLocID,
				tbl_locations.LocName
			FROM
				tbl_warehouse
			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
			INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
			WHERE
				tbl_warehouse.prov_id = " . $provId . "
			$stkFilter
			ORDER BY
				tbl_locations.LocName ASC";
    //query result
    $rsQry = mysql_query($qry) or die();
    //fetch data from rsQry
    while ($row = mysql_fetch_array($rsQry)) {
        if ($distSelId == $row['PkLocID']) {
            $sel = "selected='selected'";
        } else {
            $sel = "";
        }
        ?>
        <option value="<?php echo $row['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $row['LocName']; ?></option>
        <?php
    }
}
if ($skOfcLvl != 1 && !empty($distId)) {
    echo "<option value=\"all\">All</option>";
    //query
    //gets
    //PkLocID
    //LocName
    $qry = "SELECT
				Tehsil.PkLocID,
				Tehsil.LocName
			FROM
				tbl_locations AS Tehsil
			WHERE
				Tehsil.LocLvl = 5
			AND Tehsil.ParentID = $distId
			GROUP BY
				Tehsil.PkLocID
			ORDER BY
				Tehsil.LocName ASC";
    //query result
    $rsQry = mysql_query($qry) or die();
    //fetch data from rsQry
    while ($row = mysql_fetch_array($rsQry)) {
        if ($tehSelId == $row['PkLocID']) {
            $sel = "selected='selected'";
        } else {
            $sel = "";
        }
        ?>
        <option value="<?php echo $row['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $row['LocName']; ?></option>
        <?php
    }
}
?>