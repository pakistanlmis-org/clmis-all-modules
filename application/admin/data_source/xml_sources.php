<?php
/**
 * xml Roles
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//For getting roles
$qry =  "SELECT
    stock_sources.pk_id,
	tbl_locations.LocName,
	stakeholder.stkname,
	stock_sources.created_date,
	list_detail.list_value
FROM
	stock_sources
INNER JOIN tbl_locations ON stock_sources.province_id = tbl_locations.PkLocID
INNER JOIN stakeholder ON stock_sources.stakeholder_id = stakeholder.stkid
INNER JOIN list_detail ON stock_sources.stock_source_id = list_detail.pk_id
ORDER BY
	tbl_locations.LocName ASC,
	stakeholder.stkname ASC,
	list_detail.rank ASC";
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
        //role_name
	$xmlstore .="<cell>".$row['LocName']."</cell>";
        //page_title
	$xmlstore .="<cell>".$row['stkname']."</cell>";
        //description
	$xmlstore .="<cell>".$row['list_value']."</cell>";
        $xmlstore .="<cell>".$row['created_date']."</cell>";
	$xmlstore .="<cell type=\"img\">".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
	//$xmlstore .="<cell type=\"img\">".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
	$xmlstore .="<cell></cell>";
    $xmlstore .="</row>";
}

//Used for grid
$xmlstore .="</rows>";