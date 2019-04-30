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
//$date = date("dmYm");
//$batch_cache = "bathces$date.txt";

//if (file_exists($batch_cache)) {
  //  $xmlstore = file_get_contents($batch_cache);
//} else {
    //For getting stakeholder
    $query_xmlw = "SELECT DISTINCT
	itminfo_tab.itm_name,
	stock_batch.batch_no,
	stakeholder.stkname,
	stock_batch.batch_id
FROM
	stock_batch
LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
LEFT JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
WHERE itminfo_tab.itm_id IS NOT NULL
ORDER BY
	itminfo_tab.itm_id,
	stock_batch.batch_no,
	stakeholder.stkname";
    //query result
    $result_xmlw = mysql_query($query_xmlw);

    //Generating xml for grid
    $xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xmlstore .= "<rows>";
    $counter = 1;
    //populate xml
    while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
        $temp = "\"$row_xmlw[batch_id]\"";
        $xmlstore .= "<row>";
        $xmlstore .= "<cell>" . $counter++ . "</cell>";
        //stkname
        $xmlstore .= "<cell><![CDATA[" . $row_xmlw['itm_name'] . "]]></cell>";
        //stk_type_descr
        $xmlstore .= "<cell>" . $row_xmlw['batch_no'] . "</cell>";
        //lvl_name
        $xmlstore .= "<cell>" . $row_xmlw['stkname'] . "</cell>";
        $xmlstore .= "<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
        //$xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
        $xmlstore .= "</row>";
    }

    //Used for grid
    $xmlstore .= "</rows>";
    
  //  $handler = fopen($batch_cache, 'w');
    //fwrite($handler, $xmlstore);
    //fclose($handler);
//}

