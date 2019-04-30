<?php

/**
 * XML Manufacturers
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
stakeholder_item.stk_id,
itminfo_tab.itm_name,
stakeholder.stkname,
stakeholder_item.brand_name,
stakeholder_item.net_capacity,
stakeholder_item.quantity_per_pack,
stakeholder_item.carton_per_pallet
FROM
	stakeholder_item
INNER JOIN itminfo_tab ON stakeholder_item.stk_item = itminfo_tab.itm_id
INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE
	stakeholder.stk_type_id = 3
ORDER BY
	itminfo_tab.itm_name ASC,
	stakeholder.stkname ASC";
//query result
$result_xmlw = mysql_query($query_xmlw);

//Generating xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    
    $br_name  = str_replace("\n", '', $row_xmlw['brand_name']);
    $br_name  = str_replace("\r", '', $row_xmlw['brand_name']);
    $br_name  = str_replace(PHP_EOL, '', $row_xmlw['brand_name']);
    
    
    $temp = "\"$row_xmlw[stk_id]\"";
    $xmlstore .="<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    //stkname
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['itm_name'] . "]]></cell>";
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['stkname'] . "]]></cell>";
    //stk_type_descr
    $xmlstore .="<cell><![CDATA[" . $br_name . "]]></cell>";
    //lvl_name
    $xmlstore .="<cell>" . $row_xmlw['net_capacity'] . "</cell>";
    $xmlstore .="<cell>" . $row_xmlw['quantity_per_pack'] . "</cell>";
    $xmlstore .="<cell>" . $row_xmlw['carton_per_pallet'] . "</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
    $xmlstore .="</row>";
}

//Used for grid
$xmlstore .="</rows>";
