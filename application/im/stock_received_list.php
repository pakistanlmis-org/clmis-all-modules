<?php
/**
 * stock_received_list
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//ob_start
ob_start();
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH."html/header.php");
//wh id
$wh_id = $_SESSION['user_warehouse'];
//title
$title = "Stock Receive List";
$print = 1;
if (!empty($_SESSION['sCriteria']['searchby']) && !empty($_SESSION['sCriteria']['number'])) {
//search by	
    $searchby = $_SESSION['sCriteria']['searchby'];
    //number
	$number = trim($_SESSION['sCriteria']['number']);
	switch ($searchby) {
		case 1:
			$objStockMaster->TranNo = $number;
			break;
		case 2:
			$objStockMaster->TranRef = $number;
			break;
		case 3:
			$objStockMaster->batch_no = $number;
			break;
	}
}
//warehouse
if (isset($_SESSION['sCriteria']['warehouse']) && !empty($_SESSION['sCriteria']['warehouse'])) {
	$objStockMaster->WHIDFrom = $_SESSION['sCriteria']['warehouse'];
}
//product
if (isset($_SESSION['sCriteria']['product']) && !empty($_SESSION['sCriteria']['product'])) {
	$objStockMaster->item_id = $_SESSION['sCriteria']['product'];
}
//date from
if (isset($_SESSION['sCriteria']['date_from']) && !empty($_SESSION['sCriteria']['date_from'])) {
	$objStockMaster->fromDate = $_SESSION['sCriteria']['date_from'];
}
//date to
if (isset($_SESSION['sCriteria']['date_to']) && !empty($_SESSION['sCriteria']['date_to'])) {
	$objStockMaster->toDate = $_SESSION['sCriteria']['date_to'];
}
$r=array();
array_walk($_SESSION['sCriteria'], create_function('$b, $c', 'global $r; $r[]="$c: $b";'));
//criteria
$sCriteria=implode(', ', $r);
?>

<!-- Content -->

<div id="content_print">
    <style type="text/css" media="print">
    .page
    {
     -webkit-transform: rotate(-90deg); -moz-transform:rotate(-90deg);
     filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
    }
	@media print
	{    
		#printButt
		{
			display: none !important;
		}
	}
</style>
    <?php
		$rptName = 'Stock Receive List';
                //include report_header
    	include('report_header.php');
	?>
    <table id="myTable" class="table-condensed" cellpadding="3">
        <!-- Table heading -->
        <thead>
            <tr>
                <th width="6%">S. No.</th>
                <th width="10%">Receive Date</th>
                <th width="10%">Receive No.</th>
                <th>Product</th>
                <th width="13%">Receive From Warehouse / Supplier</th>
                <th width="10%">Ref.No.</th>
                <th width="10%">Batch No.</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Cartons</th>
                <th width="12%">Expiry Date</th>
            </tr>
        </thead>
        <!-- // Table heading END --> 
        
        <!-- Table body -->
        <tbody>
		<?php
        $i = 1;
        //Stock Search List
		$result = $objStockMaster->StockSearchList(1, $wh_id);
                //total qty
		$totalQty = $totalCartons = '';
                //fetch result
        while ($row = mysql_fetch_object($result))
		{
			//total qty
            $totalQty += abs($row->Qty);
			//total cartons
            $totalCartons += abs($row->Qty) / $row->qty_carton;
		?>
            <tr>
                <td class="text-center"><?php echo $i++;?></td>
                <td style="text-align:center;"><?php echo date("d/m/y", strtotime($row->TranDate)); ?>&nbsp;</td>
                <td><?php echo $row->TranNo; ?>&nbsp;</td>
                <td><?php echo $row->itm_name; ?>&nbsp;</td>
                <td><?php echo $row->wh_name; ?>&nbsp;</td>
                <td><?php echo $row->TranRef; ?>&nbsp;</td>
                <td><?php echo $row->batch_no; ?>&nbsp;</td>
                <td style="text-align:right;"><?php echo number_format($row->Qty); ?>&nbsp;</td>
                <td style="text-align:right;"><?php echo $row->itm_type; ?></td>
                <td style="text-align:right;"><?php echo number_format($row->Qty / $row->qty_carton); ?>&nbsp;</td>
                <td style="text-align:center;"><?php echo date("d/m/y", strtotime($row->batch_expiry)); ?>&nbsp;</td>
            </tr>
            <?php
            }
            ?>
            <!-- // Table row END -->
        <tfoot>
            <tr>
                <th colspan="7" style="text-align:right;">Total</th>
                <th style="text-align:right;"><?php echo number_format($totalQty);?></th>
                <th>&nbsp;</th>
                <th style="text-align:right;"><?php echo number_format($totalCartons);?></th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
            </tbody>
        
    </table>
    <?php /*?><div style="float:left; font-size:12px;"> <?php echo !empty($sCriteria) ? '<b>Criteria: </b>'.$sCriteria : ''; ?><br /><?php */?>
        <b>Print Date:</b> <?php echo date('d/m/y').' <b>by</b> '.$_SESSION['user_name'];?> </div>
    <div style="float:right; margin:20px;" id="printButt">
        <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
    </div>
</div>

<!-- // Content END -->
<script language="javascript">
$(function(){
	printCont();
})
function printCont()
{
	window.print();
}
</script>