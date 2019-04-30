<?php
/**
 * xml Stakeholder Office
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
$query_xmlw = "SELECT
				stakeholder.stkid,
				stakeholder.stkname,
				IFNULL(parentStk.stkname, stakeholder.stkname) AS parentStk,
				mainStk.stkname AS mainStk,
				tbl_dist_levels.lvl_desc
			FROM
				stakeholder
			INNER JOIN stakeholder AS mainStk ON stakeholder.MainStakeholder = mainStk.stkid
			LEFT JOIN stakeholder AS parentStk ON stakeholder.ParentID = parentStk.stkid
			INNER JOIN tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
			ORDER BY
				stakeholder.stkorder ASC";
$result_xmlw = mysql_query($query_xmlw);
//xml for grid
$xmlstore="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while($row_xmlw = mysql_fetch_array($result_xmlw)) {
	$temp = "\"$row_xmlw[stkid]\"";
	$xmlstore .="<row>";
	$xmlstore .="<cell>".$counter++."</cell>";
        //mainStk
	$xmlstore .="<cell><![CDATA[".$row_xmlw['mainStk']."]]></cell>";
        //parentStk
	$xmlstore .="<cell><![CDATA[".$row_xmlw['parentStk']."]]></cell>";
        //lvl_desc
	$xmlstore .="<cell>".$row_xmlw['lvl_desc']."</cell>";
        //stkname
	$xmlstore .="<cell><![CDATA[".$row_xmlw['stkname']."]]></cell>";	
	$xmlstore .="<cell type=\"img\">".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
	//$xmlstore .="<cell type=\"img\">".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
	$xmlstore .="<cell ></cell>";
	$xmlstore .="</row>";
}
//end xml
$xmlstore .="</rows>";