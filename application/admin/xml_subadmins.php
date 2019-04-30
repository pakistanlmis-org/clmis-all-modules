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
$objuser1 = "SELECT
	sysuser_tab.usrlogin_id,
	sysuser_tab.sysusr_name,
	getUserProvinces(sysuser_tab.UserID) AS provinces,
	getUserStakeholders(sysuser_tab.UserID) AS stakeholders,
	sysuser_tab.sysusr_email,
	sysuser_tab.sysusr_ph,
	sysuser_tab.UserID
	FROM
	sysuser_tab
	WHERE
	sysuser_tab.sysusr_type = '2'";

$result_xmlw = mysql_query($objuser1);

//Generating xml for grid
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .="<rows>";
$counter = 1;
//populate xml
while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
    $temp = "\"$row_xmlw[UserID]\"";
    $xmlstore .="<row>";
    $xmlstore .="<cell>" . $counter++ . "</cell>";
    //sysusr_name
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['usrlogin_id'] . "]]></cell>";
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['sysusr_name'] . "]]></cell>";
    //sysusr_ph
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['sysusr_ph'] . "]]></cell>";
    //sysusr_email
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['sysusr_email'] . "]]></cell>";
    //provinces
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['provinces'] . "]]></cell>";
    //stakeholders
    $xmlstore .="<cell><![CDATA[" . $row_xmlw['stakeholders'] . "]]></cell>";

    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/edit.gif^javascript:editFunction($temp)^_self</cell>";
    $xmlstore .="<cell type=\"img\">" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^" . PUBLIC_URL . "dhtmlxGrid/dhtmlxGrid/codebase/imgs/Delete.gif^javascript:delFunction($temp)^_self</cell>";
    $xmlstore .="</row>";
}

//Used for grid
$xmlstore .="</rows>";
