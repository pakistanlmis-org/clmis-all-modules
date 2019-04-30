<?php

/**
 * xml Warehouse
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//including Configuration.inc file
include("../includes/classes/Configuration.inc.php");
//Login
Login();
//Including db file
include(APP_PATH . "includes/classes/db.php");

header("Content-type:text/xml");
ini_set('max_execution_time', 600);

$and = '';
//$and .= (!empty($arr['stk'])) ? " AND tbl_warehouse.stkid IN (" . implode(',', $arr['stk']) . ")" : '';
//$and .= (!empty($arr['prov'])) ? " AND province.PkLocID IN (" . implode(',', $arr['prov']) . ")" : '';

if( $_SESSION['user_level'] > 1 || $_SESSION['user_role'] =='2' || $_SESSION['user_role'] =='26' )
$and .=  " AND province.PkLocID = " . $_SESSION['user_province1'] ." " ;


$and .= ($_SESSION['user_level']>1) ? " AND tbl_warehouse.stkid  = " . $_SESSION['user_stakeholder1'] ." " : '';

$loc_name=array();

$qry = "SELECT
			province.PkLocID AS prov_id,
			province.LocName AS prov_name,
			district.PkLocID AS dist_id,
			district.LocName AS dist_name,
			stakeholder.stkname,
			tbl_warehouse.wh_id,
			CONCAT(tbl_warehouse.wh_name , ' (', stakeholder.stkname, ')') AS wh_name,
			stakeholder.lvl
		FROM
			tbl_warehouse
		INNER JOIN tbl_locations AS district ON tbl_warehouse.dist_id = district.PkLocID
		INNER JOIN tbl_locations AS province ON tbl_warehouse.prov_id = province.PkLocID
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		LEFT JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
		LEFT JOIN tbl_hf_type_rank ON tbl_hf_type_rank.stakeholder_id = tbl_warehouse.stkid
		AND tbl_hf_type_rank.province_id = province.PkLocID
		AND tbl_hf_type_rank.hf_type_id = tbl_hf_type.pk_id
		WHERE
			tbl_warehouse.is_lock_data_entry = 0
			$and
		ORDER BY
			prov_id ASC,
			dist_name ASC,
			tbl_hf_type_rank.hf_type_rank ASC,
			tbl_warehouse.wh_name ASC";
//echo $qry;exit;
//Query result
$qryRes = mysql_query($qry);
$num = mysql_num_rows($qryRes);
$data_arr = array();
//Getting results
while ($row = mysql_fetch_array($qryRes)) {
    if (!in_array($row['prov_id'], $loc_name)) {
        $loc_name[$row['prov_id']] = $row['prov_name'];
    }
    if (!in_array($row['dist_id'], $loc_name)) {
        $loc_name[$row['dist_id']] = $row['dist_name'];
    }
    $data_arr[$row['prov_id']][$row['dist_id']][$row['wh_id']] = $row['wh_name'];
}
//xml

$xml_string = '<?xml version="1.0" encoding="iso-8859-1"?>';
$xml_string .= '<tree id="0" radio="1">';

//checked=\"checked\" 
$counter = 1;
//Populate xml
foreach ($data_arr as $prov_id => $pro_data) {
    $xml_string .= "<item text=\"" . $loc_name[$prov_id] . "\" id=\"" . $prov_id . "\" open=\"0\">";
    foreach ($pro_data as $dis_id => $dist_data) {
        $open = ($counter == 1) ? "open=\"0\"" : '';
        $xml_string .= "<item text=\"" . $loc_name[$dis_id] . "\" id=\"" . $dis_id . "\" $open  >";
        foreach ($dist_data as $wh_id => $wh_name) {
            $xml_string .= "<item text=\"" . str_replace('&', '&amp;', $wh_name) . "\" id=\"w_" . $wh_id . "\"  >";
            $xml_string .= '</item>';
        }
        $xml_string .= '</item>';
        $counter++;
    }
    $xml_string .= '</item>';
}
$xml_string .= '</tree>';

/*$xml_string = '<?xml version="1.0" encoding="iso-8859-1"?>
<tree id="0" radio="1">

<item text="NewYork" id="2" open="0">
<item text="Badin" id="21" open="0"  >
<item text="Ftestttting" id="wwww"  ></item>

</item>
</item>

</tree>';*/
echo $xml_string;
?>