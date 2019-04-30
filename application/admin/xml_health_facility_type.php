<?php

/**
 * xml Health Facility Type
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Query to get health facility type
$query_xmlw = "SELECT
	tbl_hf_type.pk_id,
	tbl_hf_type.hf_type AS health_facility_type,
	tbl_hf_type.stakeholder_id,
	tbl_hf_type.hf_rank AS health_facility_rank,
	stakeholder.stkname AS stakeholder_name
FROM
	tbl_hf_type
INNER JOIN stakeholder ON tbl_hf_type.stakeholder_id = stakeholder.stkid
    ";
$result_xmlw = mysql_query($query_xmlw);

//start xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    $temp = "\"$row_xmlw[pk_id]\"";
    $xmlstore .="<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    //stakeholder name
    $xmlstore .="<cell>" . $row_xmlw['stakeholder_name'] . "</cell>";
    //health facility type
    $xmlstore .="<cell>" . $row_xmlw['health_facility_type'] . "</cell>";
    //health facility rank
    $xmlstore .="<cell>" . $row_xmlw['health_facility_rank'] . "</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
    $xmlstore .="</row>";
}
//end xml
$xmlstore .="</rows>";
