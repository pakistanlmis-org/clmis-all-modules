<?php

/**
 * xml Subadmins
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//For subsdmins 
$objuser1 = "
SELECT

tbl_locations.LocName,
tbl_locations.PkLocID,
stk_1.stkid,
stk_1.stkname,
stk_1.stkorder,
(
SELECT
GROUP_CONCAT(sub_stk.stkname ) AS stkname_sub
FROM
integrated_stakeholders
INNER JOIN stakeholder AS sub_stk ON integrated_stakeholders.sub_stk_id = sub_stk.stkid
WHERE
integrated_stakeholders.province_id=tbl_locations.PkLocID
AND integrated_stakeholders.main_stk_id = stk_1.stkid
) as sub_stakholders
FROM
stakeholder stk_1 ,
tbl_locations
WHERE
stk_1.stk_type_id = 0 AND
stk_1.lvl = 1 AND
tbl_locations.LocType = 2 AND
tbl_locations.ParentID = 10
ORDER BY
tbl_locations.PkLocID,
stk_1.stkorder ASC

 ";

$result_xmlw = mysql_query($objuser1);

//Generating xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    $temp = "\"$row_xmlw[stkid]\"";
    $prov = "\"$row_xmlw[PkLocID]\"";
    $xmlstore .="<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    //sysusr_name
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['LocName'] . "]]></cell>";
    //sysusr_ph
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['stkname'] . "]]></cell>";
    //sysusr_email
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['sub_stakholders'] . "]]></cell>";

    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp,$prov)^_self</cell>";
    //$xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp,$prov)^_self</cell>";
    
    $xmlstore .="</row>";
}

//Used for grid
$xmlstore .="</rows>";
