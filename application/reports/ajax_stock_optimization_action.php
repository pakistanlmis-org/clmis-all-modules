<?php

include_once("../includes/classes/AllClasses.php");
//include_once(PUBLIC_PATH . "html/header.php");
$date = $_REQUEST['month'];
$districtId = $_REQUEST['dist_id'];
$stakeholder = $_REQUEST['stk_id'];
$itm_id = $_REQUEST['prod_id'];
$selProv = $_REQUEST['prov_id'];
$to_array = $_REQUEST['to_array'];
$from_array = $_REQUEST['from_array'];
$trs_qty = $_REQUEST['trs_qty'];
$to_array_imp = implode(',', $to_array);
$from_array_imp = implode(',', $from_array);
$to_wh_name = $_REQUEST['whto'];
$from_wh_name = $_REQUEST['wh'];
$soh=$_REQUEST['soh'];
$mos=$_REQUEST['mos'];
$amc=$_REQUEST['amc'];
$os_qty=$_REQUEST['os_qty'];
$res=0;
//echo '<pre>';
//print_r($date);
for ($i = 0; $i < (count($to_array)); $i++) {
    $qry = "INSERT INTO stock_optimization_draft (stock_optimization_draft.from_wh_id,stock_optimization_draft.to_wh_id,stock_optimization_draft.transfer_qty,stock_optimization_draft.item_id,stock_optimization_draft.district_id,stock_optimization_draft.stk_id,stock_optimization_draft.date,stock_optimization_draft.created_on,stock_optimization_draft.soh,stock_optimization_draft.mos,stock_optimization_draft.amc,stock_optimization_draft.over_stock_qty)
      VALUES($from_array[$i],$to_array[$i],$trs_qty[$i],$itm_id,$districtId,$stakeholder,'$date','".date('Y/m/d')."',$soh[$i],$mos[$i],$amc[$i],$os_qty[$i])";
   $res= mysql_query($qry);
}
print_r($res);
return $res;
