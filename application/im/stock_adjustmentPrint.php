<?php
/**
 * stock_adjustmentPrint
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//includ AllClasses
include("../includes/classes/AllClasses.php");
//includ header
include(PUBLIC_PATH."html/header.php");

$title = "Stock Adjustment";
$print = 1;
//check date_from
if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from']))
{
    //check adjustment_no
    if (isset($_REQUEST['adjustment_no']) && !empty($_REQUEST['adjustment_no'])) {
        //get adjustment_no
        $adjustment_no = $_REQUEST['adjustment_no'];
    }
    //check type
    if (isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {
        //get type
        $type = $_REQUEST['type'];
    }
    //check product
    if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
        //get product
        $product = $_REQUEST['product'];
    }
    //check date_from
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])) {
        //get date_from
        $date_from = $_REQUEST['date_from'];
    }
    //check date_to
    if (isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
        //get date_to
        $date_to = $_REQUEST['date_to'];
    }
}
//WHIDFrom
$objStockMaster->WHIDFrom = $_SESSION['user_warehouse'];
//TranNo
$objStockMaster->TranNo = (!empty($adjustment_no)) ? $adjustment_no : '';
//TranTypeID
$objStockMaster->TranTypeID = (!empty($type)) ? $type : '';
//WHIDTo
$objStockMaster->WHIDTo = $_SESSION['user_warehouse'];
//item_id
$objStockMaster->item_id = (!empty($product)) ? $product : '';
//fromDate
$objStockMaster->fromDate = (!empty($date_from)) ? dateToDbFormat($date_from) : '';
//toDate
$objStockMaster->toDate = (!empty($date_to)) ? dateToDbFormat($date_to) : '';
//Stock Adjustment Search
$adjustment_list = $objStockMaster->StockAdjustmentSearch();

$temp_arr=array();
if ($adjustment_list) {
while ($row = mysql_fetch_object($adjustment_list)){
    $temp_arr[]=$row;
    $trans_type = $row->trans_type;
    $product = $row->itm_name;
}
}
?>
<!-- Content -->
<div id="content_print">
	<style type="text/css" media="print">
    @media print
    {    
        #printButt
        {
            display: none !important;
        }
    }
    </style>
	<?php
		$rptName = 'Stock Adjustment';
                //include header
    	include('report_header.php');
        
        echo '<div style="line-height:1;">';    
        echo '<div id="report_type" style="float:left;  text-align:center; width:100%;">';
        echo '<p>Stock Issued Between <b>'.$_REQUEST['date_from'].'</b> and <b>'.$_REQUEST['date_to'].'</b></p>';

        if(!empty($_REQUEST['adjustment_no'])) echo '<p>Adjustment No : <b>'.$_REQUEST['adjustment_no'].'</b></p>';
        if(!empty($_REQUEST['type'])) echo '<p>Adjustment Type : <b>'.$trans_type.'</b></p>';
        if(!empty($_REQUEST['product'])) echo '<p>Product : <b>'.$product.'</b></p>';
        echo '';
        echo '</div>';
        echo '</div>';
	?>
    <table id="myTable" class="table-condensed" cellpadding="3">
        <!-- Table heading -->
        <thead>
            <tr>
                <th class="span2">Date</th>
                <th class="span2">Adjustment No.</th>
                <th>Ref. No.</th>
                <th>Product</th>
                <th>Batch No.</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Carton</th>
                <th class="span2">Adjustment Type</th>
                <th>Comments</th>
            </tr>
        </thead>
        <!-- // Table heading END -->
        
        <!-- Table body -->
        <tbody>				
            <!-- Table row -->
		<?php
        $i = 1;
		$totalQty = 0;
		$totalCartons = 0;
                //check adjustment list
        if ($adjustment_list) {
            //fetch data from adjustment_list
            
                foreach($temp_arr as $k=>$row)
                {
				$totalQty += abs($row->Qty);
				$totalCartons += abs($row->Qty) / $row->qty_carton;
			?>
                <tr class="gradeX">
                    <td style="text-align:center;"><?php echo date("d/m/y", strtotime($row->TranDate)); ?></td>
                    <td><?php echo $row->TranNo; ?></td>
                    <td><?php echo (!empty($row->TranRef)) ? $row->TranRef : '&nbsp;' ; ?></td>
                    <td><?php echo $row->itm_name; ?></td>
                    <td><?php echo $row->batch_no; ?></td>
                    <td style="text-align:right;"><?php echo number_format(abs($row->Qty));?></td>
                    <td style="text-align:right;"><?php echo $row->itm_type;?></td>
                    <td style="text-align:right;"><?php echo number_format(abs($row->Qty) / $row->qty_carton);?></td>
                    <td><?php echo $row->trans_type; ?></td>
                    <td><?php echo !empty($row->ReceivedRemarks) ? $row->ReceivedRemarks : '&nbsp;' ; ?></td>
                </tr>
                <?php
                $i++;
            }
		}
        ?>
            <!-- // Table row END -->
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align:right;">Total</th>
                <th style="text-align:right;"><?php echo number_format($totalQty);?></th>
                <th>&nbsp;</th>
                <th style="text-align:right;"><?php echo number_format($totalCartons);?></th>
                <th colspan="2">&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    
	<?php 
        //report_footer_issue
        include('report_footer_issue.php');?>
        
    <div style="clear:both;float:right; margin:20px;" id="printButt">
        <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
    </div>
    
</div>

<script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script language="javascript">
$(function(){
	printCont();
})
function printCont()
{
	window.print();
}
</script>