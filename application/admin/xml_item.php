<?php

/**
 * xml Item
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Query for items
$objitem = "SELECT
			itminfo_tab.itmrec_id,
			itminfo_tab.itm_id,
			itminfo_tab.itm_name,
			itminfo_tab.generic_name,
			itminfo_tab.method_type,
			tbl_itemunits.UnitType,
			itminfo_tab.itm_des,
			tbl_product_category.ItemCategoryName,
			tbl_product_status.ItemStatusName,
			itminfo_tab.frmindex
		FROM
			itminfo_tab
		LEFT JOIN tbl_itemunits ON tbl_itemunits.pkUnitID = itminfo_tab.item_unit_id
		INNER JOIN tbl_product_category ON itminfo_tab.itm_category = tbl_product_category.PKItemCategoryID
		INNER JOIN tbl_product_status ON itminfo_tab.itm_status = tbl_product_status.PKItemStatusID
		ORDER BY
			itminfo_tab.frmindex ASC";
//echo $objitem;exit;
$result_xmlw = mysql_query($objitem);
//xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$counter = 1;
//populate xml
while ($Rowrsadditem = mysql_fetch_object($result_xmlw)) {
    $temp = "\"$Rowrsadditem->itm_id\"";
    $xmlstore .="<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    //itm_name
    $xmlstore .="<cell><![CDATA[" . $Rowrsadditem->itm_name . "]]></cell>";
    //generic_name
    $xmlstore .="<cell><![CDATA[" . $Rowrsadditem->generic_name . "]]></cell>";
    //method_type
    $xmlstore .="<cell><![CDATA[" . $Rowrsadditem->method_type . "]]></cell>";
    //UnitType
    $xmlstore .="<cell><![CDATA[" . $Rowrsadditem->UnitType . "]]></cell>";
    //ItemStatusName
    $xmlstore .="<cell><![CDATA[" . $Rowrsadditem->ItemCategoryName . "]]></cell>";
    //frmindex
    $xmlstore .="<cell><![CDATA[" . $Rowrsadditem->frmindex . "]]></cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    //$xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
    $xmlstore .="<cell></cell>";
    $xmlstore .="</row>";
}
//end xml
$xmlstore .="</rows>";
