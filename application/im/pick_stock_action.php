<?php
/**
 * pick_stock_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Include AllClasses
include("../includes/classes/AllClasses.php");
//get user warehouse
$whId = $_SESSION['user_warehouse'];
$strDo = "Add";
$nstkId = 0;
$remarks = '';
//check loc_id
if (isset($_REQUEST['loc_id']) && !empty($_REQUEST['loc_id'])) {
    //get loc_id
    $locId = array_filter($_REQUEST['loc_id']);
}
//check stock_detail_id
if (isset($_REQUEST['stock_detail_id']) && !empty($_REQUEST['stock_detail_id'])) {
    //get stock_detail_id
    $stock_detail_id= $_REQUEST['stock_detail_id'];    
}
//check batch_id
if (isset($_REQUEST['batch_id']) && !empty($_REQUEST['batch_id'])) {
    //get batch_id
    $batch_id = array_filter($_REQUEST['batch_id']);
}
//check allocate_qty
if (isset($_REQUEST['allocate_qty']) && !empty($_REQUEST['allocate_qty'])) {
    //get allocate_qty
    $allocate_qty = array_filter($_REQUEST['allocate_qty']);
}
//check carton
if (isset($_REQUEST['carton']) && !empty($_REQUEST['carton'])) {
    //get carton
    $carton = array_filter($_REQUEST['carton']);
}
//check item_id
if (isset($_REQUEST['item_id']) && !empty($_REQUEST['item_id'])) {
    //get item_id
    $item_id = $_REQUEST['item_id'];
}
//check tran_no
if (isset($_REQUEST['tran_no']) && !empty($_REQUEST['tran_no'])) {
    //get tran_no
    $tran_no = $_REQUEST['tran_no'];
}
//check dateFrom
if (isset($_REQUEST['dateFrom']) && !empty($_REQUEST['dateFrom'])) {
    //get dateFrom
    $dateFrom = $_REQUEST['dateFrom'];
}
//check dateTo
if (isset($_REQUEST['dateTo']) && !empty($_REQUEST['dateTo'])) {
    //get dateTo
    $dateTo = $_REQUEST['dateTo'];
}

foreach($locId as $key=>$value)
{
	if($allocate_qty[$key]>0){
		$arr['quantity']=round($allocate_qty[$key],0);
		
		if($arr['quantity']>0)
		{
			$arr['quantity'] = $arr['quantity'] * (-1);
		}
		$arr['stock_batch_id']=$batch_id[$key];
		$arr['stock_detail_id']=$stock_detail_id;
		$arr['placement_transaction_type_id']=91;
		$arr['created_date']=date('Y-m-d H:i:s');
		$arr['created_by']=$_SESSION['user_id'];
		$arr['loc_id']=$key;
		
		$strSql="insert into placements set placement_location_id=".$key.",quantity=".$arr['quantity'].",stock_batch_id=".$arr['stock_batch_id'].", stock_detail_id=".$arr['stock_detail_id'].",placement_transaction_type_id=".$arr['placement_transaction_type_id'].",created_date='".$arr['created_date']."',created_by=".$arr['created_by']."";
		$addPlacement=mysql_query($strSql) or die("ERR Add Placement");
	}
}

// Check if all quantity is picked
$qry = "SELECT DISTINCT
			count(*) AS num
		FROM
			tbl_stock_master
		INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
		WHERE
			tbl_stock_master.TranTypeID = 2
		AND tbl_stock_master.WHIDFrom = $whId
		AND ABS(tbl_stock_detail.Qty) - ABS(GetPicked(tbl_stock_detail.PkDetailID)) > 0
		AND tbl_stock_master.PkStockID = $tran_no
		ORDER BY
			tbl_stock_master.TranDate DESC";
$qryRes = mysql_fetch_array(mysql_query($qry));
if ( $qryRes['num'] > 0 )
{
	$url = 'pick_stock.php?date_from='.$dateFrom.'&date_to='.$dateTo.'&tran_no='.$tran_no;
}
else
{
	$url = 'pick_stock.php';
}

$_SESSION['success'] = 1;
//redirecting to pick_stock
header("location: $url");
exit;
?>