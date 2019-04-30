<?php

/**
 * xml Stakeholder Items
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//For getting Stakeholder Items
$qry = "SELECT
			stakeholder.stkid,
			stakeholder.stkname,
			GROUP_CONCAT(itminfo_tab.itm_name) AS items
		FROM
			stakeholder
		INNER JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
		INNER JOIN itminfo_tab ON stakeholder_item.stk_item = itminfo_tab.itm_id
		WHERE
			stakeholder.stk_type_id IN (0, 1)
		GROUP BY
			stakeholder.stkid
		ORDER BY
			stakeholder.stkorder ASC";
//query results
$qryRes = mysql_query($qry);

//Generating xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while ($row = mysql_fetch_array($qryRes)) {
    $temp = "\"$row[stkid]\"";
    $xmlstore .="<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    //stkname
    $xmlstore .="<cell><![CDATA[" . $row['stkname'] . "]]></cell>";
    //items
    $xmlstore .="<cell><![CDATA[" . $row['items'] . "]]></cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
    $xmlstore .="</row>";
}
//Used for grid
$xmlstore .="</rows>";
