<?php

/**
 * iccl_data_render
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

// retrieve values from values string

$vid = $tmp[0];
if ($vid != 'UNK') {
$vid = number_format($tmp[0] / PLMIS_CBL_UNIT, 0);
}

$vamc = $tmp[1];
if ($vamc != 'UNK') {
$vamc = number_format($tmp[1] / PLMIS_CBL_UNIT, 1);
}

$vcb = $tmp[2];
if ($vcb != 'UNK') {
$vcb = number_format($tmp[2] / PLMIS_CBL_UNIT, 0);
}

$vmos = $tmp[3];
if ($vmos != 'UNK') {
  $vmos = number_format($vmos / PLMIS_CBL_UNIT, 1);
}

$vcyp = $tmp[4];
if ($vcyp != 'UNK') {
	$vcyp = number_format($tmp[4] / PLMIS_CBL_UNIT, 0);
}

//query result
$rs_mos = mysql_query("SELECT getMosColor('" . $vmos . "','" . $sel_item . "','".$sel_stk."'," . $sel_lvl . ")");
//bg color
$bgcolor = mysql_result($rs_mos, 0, 0);
//xml
$xmlstore .="<cell>".$vid."</cell>";
$xmlstore .="<cell>".$vamc."</cell>";
$xmlstore .="<cell>".$vcb."</cell>";
$xmlstore .="<cell><![CDATA[<div style=\"width:10px; height:12px; background-color:$bgcolor;\"></div>]]></cell>";
$xmlstore .="<cell>".$vmos."</cell>";
$xmlstore .="<cell>".$vcyp."</cell></row>";
//end xml

?>
