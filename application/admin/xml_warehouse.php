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
ini_set('max_execution_time', 600);
header("Content-type:text/xml");
?>
<?php

/**
 * microtime float
 * 
 * @return type
 */
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

//Getting posStart
$posStart = (isset($_GET["posStart"])) ? $_GET['posStart'] : '0';
//Getting count
$count = (isset($_GET["count"])) ? $_GET['count'] : '100';
//Getting prov
$prov = (isset($_GET["prov"])) ? $_GET['prov'] : '';
//Getting dist
$dist = (isset($_GET["dist"])) ? $_GET['dist'] : '';
//Getting stk
$stk = (isset($_GET["stk"])) ? $_GET['stk'] : '';
//Getting stkofc
$stkofc = (isset($_GET["stkofc"])) ? $_GET['stkofc'] : '';
//Getting wh
$wh = (isset($_GET["wh"])) ? $_GET['wh'] : '';
//Checking orderBy
if (isset($_GET["orderBy"]) && $_GET["orderBy"]!='undefined') {
    $order_by = $_GET["orderBy"];
    
    
    //Checking direction
    if (!isset($_GET["direction"]) || $_GET["direction"] == "asc") {
        $_GET["direction"] = "ASC";
    } else {
        $_GET["direction"] = "DESC";
    }
    $direction =  $_GET["direction"];
}
else
{
    $order_by = '1';
    $direction = "ASC";
            
}

$fields = array("province", "province", "district", "stkname", "officeName", "wh_name");
//get Data From DB
getDataFromDB('', '', '', '', '', $fields[$order_by], $direction);

//print one level of the tree, based on parent_id
/**
 * getDataFromDB
 * 
 * @global type $posStart
 * @global type $count
 * @global type $prov
 * @global type $dist
 * @global type $stk
 * @global type $stkofc
 * @global type $wh
 * @param type $prov
 * @param type $dist
 * @param type $stk
 * @param type $stkofc
 * @param type $wh
 * @param type $sort_by
 * @param string $sort_dir
 */
function getDataFromDB($prov, $dist, $stk, $stkofc, $wh, $sort_by, $sort_dir) {
    global $posStart, $count, $prov, $dist, $stk, $stkofc, $wh;
    //Gets
    //stk_id
    //prov_id
    $qry = "SELECT
					user_stk.stk_id,
					user_prov.prov_id
				FROM
					user_stk
				JOIN user_prov ON user_stk.user_id = user_prov.user_id
				WHERE
					user_stk.user_id = " . $_SESSION['user_id'];
    //Query result
    $qryRes = mysql_query($qry);
    $arr['stk'] = array();
    $arr['prov'] = array();
    //Getting result
    while ($row = mysql_fetch_array($qryRes)) {
        if (!in_array($row['stk_id'], $arr['stk'])) {
            $arr['stk'][] = $row['stk_id'];
        }
        if (!in_array($row['prov_id'], $arr['prov'])) {
            $arr['prov'][] = $row['prov_id'];
        }
    }
    $where = 'WHERE 1=1 ';
        
    if($_SESSION['user_level'] == '2')
    {
        if(!empty($_SESSION['user_stakeholder1']) )
        $where .= (!empty($_SESSION['user_stakeholder1'])) ? " AND  tbl_warehouse.stkid IN (" . $_SESSION['user_stakeholder1'] . ")" : '';
        
        $where .= (!empty($_SESSION['user_province1'])) ? " AND province.PkLocID IN (" .$_SESSION['user_province1'] . ")" : '';
    }
    else
    {
        $where .= (!empty($arr['stk'])) ? " AND tbl_warehouse.stkid IN (" . implode(',', $arr['stk']) . ")" : '';
        $where .= (!empty($arr['prov'])) ? " AND Province.PkLocID IN (" . implode(',', $arr['prov']) . ")" : '';
    }
    //Gets
    //wh_id
    //wh_name
    //stkofficeid
    //stkname
    //stkid
    //dist_id
    //district
    //prov_id
    //province
    //officeName
    //is_allowed_im
    //
    $sql = "SELECT
					tbl_warehouse.wh_id,
					tbl_warehouse.wh_name,
					tbl_warehouse.stkofficeid,
					stakeholder.stkname,
					tbl_warehouse.stkid,
					district.PkLocID AS dist_id,
					district.LocName AS district,
					province.PkLocID AS prov_id,
					province.LocName AS province,
					office.stkname AS officeName,
					tbl_warehouse.is_allowed_im
				FROM
					tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
					INNER JOIN stakeholder AS office ON tbl_warehouse.stkofficeid = office.stkid
					LEFT JOIN tbl_locations AS district ON tbl_warehouse.dist_id = district.PkLocID
					LEFT JOIN tbl_locations AS province ON tbl_warehouse.prov_id = province.PkLocID
				$where ";
    // Filters
    if ($prov != '') {
        $sql .= " AND tbl_warehouse.prov_id = " . $prov;
    }if ($dist != '') {
        $sql .= " AND tbl_warehouse.dist_id = " . $dist;
    }if ($stk != '') {
        $sql .= " AND tbl_warehouse.stkid = " . $stk;
    }if ($stkofc != '') {
        $sql .= " AND tbl_warehouse.stkofficeid = " . $stkofc;
    }if ($wh != '') {
        $sql .= " AND tbl_warehouse.wh_name LIKE '%$wh%' ";
    }

    // Sorting
    if ($sort_dir == '') {
        $sort_dir = "asc";
    }

    if ($sort_by != '') {
        $sql .= " ORDER BY $sort_by $sort_dir";
    } else {
        $sql .= " ORDER BY tbl_warehouse.prov_id ASC, tbl_warehouse.dist_id ASC, stakeholder.stkorder ASC, office.lvl ASC";
    }

    if ($posStart == 0) {
        $sqlCount = "SELECT COUNT(*) AS cnt FROM ($sql) AS tbl";
        $resCount = mysql_query($sqlCount);
        while ($rowCount = mysql_fetch_array($resCount)) {
            $totalCount = $rowCount["cnt"];
        }
    }
    $sql.= " LIMIT " . $posStart . "," . $count;
    //echo '<pre>';print_r($_SESSION);
    //echo $sql;exit;
    //Query result
    $res = mysql_query($sql);
    if(empty($totalCount)) $totalCount='';
    echo "<rows total_count='" . $totalCount . "' pos='" . $posStart . "'>";
    if ($res) {
        $counter = $posStart + 1;
        while ($row = mysql_fetch_array($res)) {
            if (($row['is_allowed_im']) == 1) {
                $checked = "checked=checked";
            } else {
                $checked = '';
            }
            $temp = "\"$row[wh_id]\"";
            echo "<row>";
            echo "<cell>$counter</cell>";
            echo "<cell><![CDATA[" . $row['province'] . "]]></cell>";
            echo "<cell><![CDATA[" . $row['district'] . "]]></cell>";
            echo "<cell><![CDATA[" . $row['stkname'] . "]]></cell>";
            echo "<cell><![CDATA[" . $row['officeName'] . "]]></cell>";
            echo "<cell><![CDATA[" . $row['wh_name'] . "]]></cell>";
            echo "<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
            if ($_SESSION['user_role'] == 1) {
                echo "<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
            } else {
                echo "<cell type=\"str\"></cell>";
            }
            echo "</row>";
            $counter++;
            $posStart++;
        }
    } else {
        echo mysql_errno() . ": " . mysql_error() . " at " . __LINE__ . " line in " . __FILE__ . " file<br>";
    }

    echo "</rows>";
}

?>