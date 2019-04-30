<?php
/**
 * ajax
 * @package graph
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//get ctype
$ctype = isset($_POST['ctype']) ? $_POST['ctype'] : '';
//check ctype
switch($ctype){
	case 1: // Get Stakeholder Product
		$and = '';
		if(!empty($_POST['stakeholder']) && $_POST['stakeholder'] != 'all'){
			$and = " AND stakeholder_item.stkid = " . mysql_real_escape_string($_POST['stakeholder']);
		}else if(!empty($_POST['stakeholders'])){
			$and = " AND stakeholder_item.stkid IN (" . mysql_real_escape_string(implode(',', $_POST['stakeholders'])).')';
		}
		//select query
                //gets
                //item id
                //item name
		$qry = "SELECT DISTINCT
					itminfo_tab.itm_id,
					itminfo_tab.itm_name
				FROM
					itminfo_tab
				INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
				/*WHERE
					itminfo_tab.itm_category = 1*/
				$and
				ORDER BY
					itminfo_tab.frmindex ASC";
                //query result
		$qryRes = mysql_query($qry);
                //fetch result
                echo '<label class="checkbox">';
                echo "<input type=\"checkbox\" name=\"product_multi_check\" id=\"product_multi_check\" value=\"all\" />All</label>";
		while ( $row = mysql_fetch_array($qryRes) )
		{
                    
                        if($row['itm_id'] == '31' || $row['itm_id'] == '32') $row['itm_name']='CS Cases - '.$row['itm_name'];
			echo '<label class="checkbox">';
			echo "<input type=\"checkbox\" name=\"product_multi[]\" id=\"product_multi\" value=\"".$row['itm_id']."\" /> " . $row['itm_name'];
			echo "</label>";
		}
	break;
	case 2: // Get Districts
                //of province
		$province = mysql_real_escape_string($_POST['province']);
		if($_POST['compare_option'] != 9)
		{
			echo '<label>District</label>';
			echo '<div class="controls">';
			echo '<select name="district" id="district" class="form-control input-sm">';
			//select query
			//gets
			//district id
			//district name
			$qry = "SELECT
						tbl_locations.PkLocID,
						tbl_locations.LocName
					FROM
						tbl_locations
					WHERE
						tbl_locations.LocLvl = 3
					AND tbl_locations.ParentID = $province
					ORDER BY
						tbl_locations.LocName ASC";
                        //query result
			$qryRes = mysql_query($qry);
                        //fetch result
			while ( $row = mysql_fetch_array($qryRes) )
			{
				echo "<option value=\"".$row['PkLocID']."\">".$row['LocName']."</option>";
			}
			echo '</select>';
			echo '</div>';
		}
		else
		{
			echo '<label>Districts</label>';
			echo '<div class="controls" style="border: 1px solid #F2F2F2; padding-left:25px; height:120px; overflow:auto;">';
                        //select query
                        //gets
                        //district id
                        //district name
			$qry = "SELECT
						tbl_locations.PkLocID,
						tbl_locations.LocName
					FROM
						tbl_locations
					WHERE
						tbl_locations.LocLvl = 3
					AND tbl_locations.ParentID = $province
					ORDER BY
						tbl_locations.LocName ASC";
                        //query result
			$qryRes = mysql_query($qry);
                        //fetch result
			while ( $row = mysql_fetch_array($qryRes) )
			{
				echo '<label class="checkbox">';
				echo "<input type=\"checkbox\" name=\"district_multi[]\" id=\"district_multi\" value=\"".$row['PkLocID']."\" /> " . $row['LocName'];
				echo "</label>";
			}
			echo '</div>';
		}
	break;
	case 3: // Get Stakeholder Product
		$stkFilter = '';
		if ($_POST['type'] != 'all') {
			$stkFilter = ' AND stk_type_id = ' . $_POST['type'];
		} else {
			$stkFilter = ' AND stk_type_id IN (0, 1)';
		}
		//select quert
                //gets
                //stk id
                //stk name
		$qry = "SELECT stkid,stkname FROM stakeholder WHERE ParentID IS NULL $stkFilter ORDER BY stkorder";
		$qryRes = mysql_query($qry) or die();
		if(in_array($_POST['compare_option'], array(4, 5, 6))){
			echo '<div class="control-group">';
			echo '<label>Stakeholders</label>';
			echo '<div class="controls" style="border: 1px solid #F2F2F2; padding-left:25px; height:120px; overflow:auto;">';
			 //fetch result
                        while ($row = mysql_fetch_array($qryRes)) {
				echo '<label class="checkbox">';
				echo "<input type=\"checkbox\" name=\"stakeholder_multi[]\" id=\"stakeholder_multi\" value=\"".$row['stkid']."\" /> " . $row['stkname'];
				echo "</label>";
			}
			echo '</div>';
		}else{
			echo '<option value="all">All</option>';
                        //fetch result
			while ($row = mysql_fetch_array($qryRes)) {
				echo "<option value=\"".$row['stkid']."\">".$row['stkname']."</option>";
			}
		}
	break;
		
	default:
	exit;
	
}