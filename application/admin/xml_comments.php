<?php

/**
 * XML Commments
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//For getting stakeholder
$query_xmlw = "SELECT
    dashboard_comments.pk_id,
	dash.page_title dashboard_name,
	dashlet.page_title dashlet_name,
	stakeholder.stkname,
	tbl_locations.LocName,
	dashboard_comments.comments,
	DATE_FORMAT(dashboard_comments.month_year,'%m/%Y') month_year
FROM
	dashboard_comments
LEFT JOIN resources AS dashlet ON dashboard_comments.dashlet_id = dashlet.pk_id
LEFT JOIN resources AS dash ON dashboard_comments.dashboard_id = dash.pk_id
LEFT JOIN stakeholder ON dashboard_comments.stakeholder_id = stakeholder.stkid
LEFT JOIN tbl_locations ON dashboard_comments.location_id = tbl_locations.PkLocID
ORDER BY
dashboard_name ASC,
dashlet_name ASC";
//query result
$result_xmlw = mysql_query($query_xmlw);

//Generating xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    $temp = "\"$row_xmlw[pk_id]\"";
    $xmlstore .="<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    //stkname
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['dashboard_name'] . "]]></cell>";
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['dashlet_name'] . "]]></cell>";
    //stk_type_descr
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['stkname'] . "]]></cell>";
    //lvl_name
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['LocName'] . "]]></cell>";
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['month_year'] . "]]></cell>";
    $xmlstore .="<cell><![CDATA[" . addslashes($row_xmlw['comments'])  . "]]></cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
    $xmlstore .="</row>";
}

//Used for grid
$xmlstore .="</rows>";
