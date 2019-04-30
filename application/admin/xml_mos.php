<?php
/**
 * xml MOS
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Query for mos
$query_xmlw = "SELECT
				itminfo_tab.itm_name,stakeholder.stkname,
				tbl_dist_levels.lvl_name,
				mosscale_tab.shortterm,
				mosscale_tab.longterm,
				mosscale_tab.sclstart,
				mosscale_tab.sclsend,
				mosscale_tab.colorcode,mosscale_tab.row_id
			FROM
				mosscale_tab
			INNER JOIN stakeholder ON mosscale_tab.stkid = stakeholder.stkid
			INNER JOIN tbl_dist_levels ON mosscale_tab.lvl_id = tbl_dist_levels.lvl_id
			INNER JOIN itminfo_tab ON itminfo_tab.itmrec_id = mosscale_tab.itmrec_id";
$result_xmlw = mysql_query($query_xmlw);
//xml for grid
$xmlstore="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate grid
while($row_xmlw = mysql_fetch_array($result_xmlw)) {
	$colorcodebg=$row_xmlw['colorcode'];
	$temp = "\"$row_xmlw[row_id]\"";
	$xmlstore .="<row>";
	$xmlstore .="<cell>".$counter++."</cell>";
        //itm_name
	$xmlstore .="<cell><![CDATA[".$row_xmlw['itm_name']."]]></cell>";
        //stkname
	$xmlstore .="<cell><![CDATA[".$row_xmlw['stkname']."]]></cell>";
        //lvl_name
	$xmlstore .="<cell><![CDATA[".$row_xmlw['lvl_name']."]]></cell>";
        //shortterm
	$xmlstore .="<cell><![CDATA[".$row_xmlw['shortterm']."]]></cell>";
        //longterm
	$xmlstore .="<cell><![CDATA[".$row_xmlw['longterm']."]]></cell>";
        //sclstart
	$xmlstore .="<cell><![CDATA[".$row_xmlw['sclstart']."]]></cell>";
        //sclsend
	$xmlstore .="<cell><![CDATA[".$row_xmlw['sclsend']."]]></cell>";
	$xmlstore .="<cell><![CDATA[<a href=\"#\" style=\"text-decoration:none;color:$colorcodebg\">$colorcodebg</a>]]>^_self</cell>";
	$xmlstore .="<cell type=\"img\">".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
	//$xmlstore .="<cell type=\"img\">".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
	$xmlstore .="<cell></cell>";
	$xmlstore .="</row>";
}
//end xml
$xmlstore .="</rows>";