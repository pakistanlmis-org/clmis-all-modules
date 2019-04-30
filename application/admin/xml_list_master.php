<?php

/**
 * xml Location
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Query for locations
$where = '' ;

$qry = "SELECT
            list_master.list_master_name,
            list_detail.pk_id,
            list_detail.list_value,
            list_detail.description,
            list_detail.rank,
            list_detail.reference_id,
            list_detail.parent_id,
            list_detail.list_master_id,
            list_detail.created_by,
            list_detail.created_date,
            list_detail.modified_by,
            list_detail.modified_date
            FROM
            list_master
            INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
            
";
//echo $qry;exit;
$qryRes = mysql_query($qry);
//xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate grid
while ($row = mysql_fetch_array($qryRes)) {
    $temp = "\"$row[pk_id]\"";
    $xmlstore .="<row>";
    
    
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    $xmlstore .="<cell>" . $row['list_master_name'] . "</cell>";
    
    $xmlstore .="<cell>" . $row['list_value'] . "</cell>";
    $xmlstore .="<cell>" . $row['rank'] . "</cell>";
    
    $xmlstore .="<cell>" . $row['description'] . "</cell>";
    
    
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    //$xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
    $xmlstore .="<cell></cell>";
    $xmlstore .="</row>";
}
//end xml
$xmlstore .="</rows>";
