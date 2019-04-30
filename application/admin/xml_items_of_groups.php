<?php

/**
 * xml Items of Groups
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Query for items of groups
$query_xmlw = "SELECT
				itemgroups.PKItemGroupID,
				itemgroups.ItemGroupName
				FROM
				itemgroups";
$result_xmlw = mysql_query($query_xmlw);
//xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    $csv = '';
    //PKItemGroupID
    $temp = "\"$row_xmlw[PKItemGroupID]\"";
    $xmlstore .="<row id=\"$counter\">";
    $objMIs = mysql_query("SELECT
			itemgroups.PKItemGroupID,
			itemgroups.ItemGroupName,
			itemsofgroups.ItemID,
			itminfo_tab.itm_name
			FROM
			itemgroups
			Left Join itemsofgroups ON itemgroups.PKItemGroupID = itemsofgroups.GroupID
			Left Join itminfo_tab ON itemsofgroups.ItemID = itminfo_tab.itm_id
			Where itemgroups.PKItemGroupID=" . $row_xmlw['PKItemGroupID']);
    if ($objMIs != FALSE && mysql_num_rows($objMIs) > 0) {
        //getting results
        while ($Rowranks = mysql_fetch_array($objMIs)) {
            //itm_name
            $csv.=$Rowranks['itm_name'] . ",";
        }
    }
    if (strlen($csv) > 0) {
        $csv = substr($csv, 0, strlen($csv) - 1);
    }
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    //ItemGroupName
    $xmlstore .="<cell>" . $row_xmlw['ItemGroupName'] . "</cell>";
    $xmlstore .="<cell>" . $csv . "</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    $xmlstore .="</row>";
}
//end xml
$xmlstore .="</rows>";
