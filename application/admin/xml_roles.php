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
			roles.pk_id,
			roles.role_name,
			roles.description,
			resources.page_title
		FROM
			roles
		INNER JOIN resources ON roles.landing_resource_id = resources.pk_id";
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
	$xmlstore .="<cell>".$row['role_name']."</cell>";
        //page_title
	$xmlstore .="<cell>".$row['page_title']."</cell>";
        //description
	$xmlstore .="<cell>".$row['description']."</cell>";
	$xmlstore .="<cell type=\"img\">".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
	//$xmlstore .="<cell type=\"img\">".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^".PUBLIC_URL."dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
	$xmlstore .="<cell></cell>";
        $xmlstore .="</row>";
}

//Used for grid
$xmlstore .="</rows>";