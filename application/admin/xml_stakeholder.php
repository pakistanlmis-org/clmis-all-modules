<?php

/**
 * xml Stakeholder
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
					stakeholder.stkid,
					stakeholder.stkname,
					stakeholder.stkorder,
					stakeholder_type.stk_type_descr,
					IF(ifnull(parentstk.stkname,stakeholder.stkname) ='', parentstk.stkname, ifnull(parentstk.stkname,stakeholder.stkname) ) AS Parent,
					stakeholder.ParentID,
					stakeholder.stk_type_id,
					stakeholder.lvl,
					tbl_dist_levels.lvl_name,
					stakeholder.MainStakeholder
					FROM
					stakeholder
					Left Join stakeholder AS parentstk ON stakeholder.ParentID = parentstk.stkid
					Inner Join stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
					Left Join tbl_dist_levels ON stakeholder.lvl = tbl_dist_levels.lvl_id
					where stakeholder.ParentID is null
					ORDER BY stakeholder.stkid";
//query result
$result_xmlw = mysql_query($query_xmlw);

//Generating xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    $temp = "\"$row_xmlw[stkid]\"";
    $xmlstore .="<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    //stkname
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['stkname'] . "]]></cell>";
    //stk_type_descr
    $xmlstore .="<cell>" . $row_xmlw['stk_type_descr'] . "</cell>";
    //lvl_name
    $xmlstore .="<cell>" . $row_xmlw['lvl_name'] . "</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
//    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
    $xmlstore .="<cell  > </cell>";
    $xmlstore .="</row>";
}

//Used for grid
$xmlstore .="</rows>";
