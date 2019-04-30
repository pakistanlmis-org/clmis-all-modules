<?php
/**
 * xml Resources
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//For getting resources
$qry =  "SELECT
			resources.pk_id,
			resources.resource_name,
			resources.page_title,
			resources.description,
			resource_types.resource_type
		FROM
			resources
		INNER JOIN resource_types ON resources.resource_type_id = resource_types.pk_id";
$qryRes = mysql_query($qry);

//Generating xml for grid
$xmlstore="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while($row = mysql_fetch_array($qryRes))
{
	$temp = "\"$row[pk_id]\"";
	$xmlstore .="<row>";
	$xmlstore .="<cell>".$counter++."</cell>";
        //resource_type
	$xmlstore .="<cell><![CDATA[".$row['pk_id']."]]></cell>";
	$xmlstore .="<cell><![CDATA[".$row['resource_type']."]]></cell>";
        //resource_name
	$xmlstore .="<cell><![CDATA[".$row['resource_name']."]]></cell>";
        //page_title
	$xmlstore .="<cell><![CDATA[".$row['page_title']."]]></cell>";
        //description
	$xmlstore .="<cell><![CDATA[".$row['description']."]]></cell>";
	$xmlstore .="<cell type=\"img\">".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
	//$xmlstore .="<cell type=\"img\">".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
	$xmlstore .="<cell></cell>";
        $xmlstore .="</row>";
}

//used for grid
$xmlstore .="</rows>";