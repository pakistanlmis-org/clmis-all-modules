<?php
/**
 * add_stock_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");

$strDo = "Add";
$nstkId = 0;
$remarks = '';
//Getting loc_id
if (isset($_REQUEST['loc_id']) && !empty($_REQUEST['loc_id'])) {
    //Setting loc_id
    $locId = $_REQUEST['loc_id'];
}
//Getting stock_detail_id
if (isset($_REQUEST['stock_detail_id']) && !empty($_REQUEST['stock_detail_id'])) {
    //Setting stock_detail_id
    $stock_detail_id= array_filter($_REQUEST['stock_detail_id']);
    
}
//Getting batch_id
if (isset($_REQUEST['batch_id']) && !empty($_REQUEST['batch_id'])) {
    //Setting batch_id
    $batch_id = array_filter($_REQUEST['batch_id']);
}
//Getting allocate_qty
if (isset($_REQUEST['allocate_qty']) && !empty($_REQUEST['allocate_qty'])) {
    //Setting allocate_qty
    $allocate_qty = array_filter($_REQUEST['allocate_qty']);
}
//Getting qty_carton
if (isset($_REQUEST['qty_carton']) && !empty($_REQUEST['qty_carton'])) {
    //Setting qty_carton
    $qty_carton = array_filter($_REQUEST['qty_carton']);
}
//Getting allocate_qty
if (isset($_REQUEST['allocate_qty']) && !empty($_REQUEST['allocate_qty'])) {
    //Setting allocate_qty
    $allocate_qty = ($_REQUEST['allocate_qty']);
}
//Getting item_id
if (isset($_REQUEST['item_id']) && !empty($_REQUEST['item_id'])) {
    //Setting item_id
    $item_id = array_filter($_REQUEST['item_id']);
}
foreach($item_id as $key=>$value)
{
	if($allocate_qty[$key]>0){
		$arr['quantity']=$allocate_qty[$key];
		$arr['is_placed']=1;
		$arr['stock_batch_id']=$batch_id[$key];
		$arr['stock_detail_id']=$stock_detail_id[$key];
		$arr['placement_transaction_type_id']=89;
		$arr['created_date']=date('Y-m-d H:i:s');
		$arr['created_by']=$_SESSION['user_id'];
		$strSql="INSERT INTO placements SET placement_location_id=".$locId.",quantity=".$arr['quantity'].",is_placed=".$arr['is_placed'].",stock_batch_id=".$arr['stock_batch_id'].", stock_detail_id=".$arr['stock_detail_id'].",placement_transaction_type_id=".$arr['placement_transaction_type_id'].",created_date='".$arr['created_date']."',created_by=".$arr['created_by']."";
		$addPlacement=mysql_query($strSql) or die("ERR Add Placement");
	}
}

$var = $_POST['hiddFld'];

$_SESSION['success'] = 1;
header("location:add_stock.php?loc_id=".$locId."&".$var);
exit;
?>