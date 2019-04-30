<?php
/**
 * fetchDistricts
* @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Including required files
include("../includes/classes/AllClasses.php");

//Province Id
$pid = $_REQUEST['pid'];
//Distrist Id
$distId = $_REQUEST['distId'];
$user_level = $_REQUEST['user_level'];
$result = "";
//for getting districts
$where=$rd='';
if(!empty($distId) && $user_level >= '3') 
{
    $where = " AND PkLocID = $distId "; 
    $rd = ' readonly ';
}
$result .= " <select $rd name=\"districts\" id=\"districts\" class=\"form-control input-medium\">";
if ($_SESSION['user_level'] != 3) {
    $result .="<option value=\"all\" >All</option>";
}
 $qry  = "SELECT
			PkLocID,
			LocName
		FROM
			tbl_locations
		WHERE
			ParentID = ".$pid."
                        $where    
		ORDER BY
			LocName";
$rsfd = mysql_query($qry) or die(mysql_error());
while($row = mysql_fetch_array($rsfd)){
	$sel = ($distId == $row['PkLocID']) ? 'selected="selected"' : '';
	$result .="<option value=\"".$row['PkLocID']."\" $sel>".$row['LocName']."</option>";
}	
$result .="</select>";
echo $result;