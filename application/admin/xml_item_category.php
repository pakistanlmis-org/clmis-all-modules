<?php

/**
 * xml Item Category
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//For getting Item Category
$query_xmlw = "SELECT
				tbl_product_category.PKItemCategoryID,
				tbl_product_category.ItemCategoryName
				FROM
				tbl_product_category";
$result_xmlw = mysql_query($query_xmlw);

//Generating xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {

    $temp = "\"$row_xmlw[PKItemCategoryID]\"";
    $xmlstore .="<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    //ItemCategoryName
    $xmlstore .="<cell>" . $row_xmlw['ItemCategoryName'] . "</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    //$xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
    $xmlstore .="<cell></cell>";
    $xmlstore .="</row>";
}
//end xml
$xmlstore .="</rows>";
