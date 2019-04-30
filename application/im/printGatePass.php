<?php
/**
 * printGatePass
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
//Including header file
include(PUBLIC_PATH."html/header.php");

$title = "Gate Pass Voucher";
$print = 1;

$stockId = $_GET['id'];
//Gets
//TranNo
//TranDate
//batch_no
//quantity
//itm_name
//wh_name
//batch_expiry
//
//
$qry = "SELECT
		tbl_stock_master.TranNo,
		tbl_stock_master.TranDate,
		gatepass_master.transaction_date,
		stock_batch.batch_no,
		gatepass_master.number,
		gatepass_detail.quantity,
		itminfo_tab.itm_name,
		CONCAT(tbl_warehouse.wh_name, ' (', stakeholder.stkname, ')') AS wh_name,
		stock_batch.batch_expiry,
                gatepass_master.warehouse_id
	FROM
		gatepass_detail
	INNER JOIN tbl_stock_detail ON gatepass_detail.stock_detail_id = tbl_stock_detail.PkDetailID
	INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
	INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
	INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
	INNER JOIN gatepass_master ON gatepass_detail.gatepass_master_id = gatepass_master.pk_id
	INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
	INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
	WHERE
		gatepass_detail.gatepass_master_id =".$_GET['id'];

$qryRes2= mysql_fetch_array(mysql_query($qry));
$qryRes = mysql_query($qry);
$data_arr = array();
while($row= mysql_fetch_array($qryRes)){
    $data_arr[] = $row;
    $wh_id      =  $row['warehouse_id'];
}
?>

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
		$rptName = 'Gate Pass Voucher';
    	include('report_header.php');
	?>
    
    	<div style="text-align:center;">
            <b style="float:left;">Gate Pass No.: <?php echo $qryRes2['number']; ?></b>
            <b style="float:right;">Gate Pass Date: <?php echo date("d-M-Y", strtotime($qryRes2['transaction_date'])); ?></b>
        </div>
        <div style="clear:both;">
            <b>Transaction Date: <?php echo date("d-M-Y", strtotime($qryRes2['TranDate'])); ?></b>
        </div>
    <br>
        <table id="myTable" class="table-condensed">
            <tr>
                <th width="6%">S.No.</th>
                <th>Product</th>
                <th width="15%">Batch No.</th>
                <th width="35%">Issued To</th>
                <th width="15%" align="center">Quantity</th>
                <th width="15%">Expiry Date</th>
            </tr>
            <tbody>
                <?php
                $i = 1;
               //
                foreach($data_arr as $k => $row){
                    
                        ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $i; ?></td>
                            <td><?php echo $row['itm_name']; ?></td>
                            <td><?php echo $row['batch_no']; ?></td>
                            <td><?php echo $row['wh_name']; ?></td>
                            <td style="text-align:right;"><?php echo number_format($row['quantity']); ?></td>
                            <td style="text-align:center;"> <?php echo date("d-M-Y", strtotime($row['batch_expiry'])); ?></td>
                        </tr>
                        <?php
              $i++;  }
                ?>
            </tbody>

        </table>
                
        <div style="float:right; margin-top:20px;" id="printButt">
        	<input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
        </div>
    </div>
<script language="javascript">
$(function(){
	printCont();
})
function printCont()
{
	window.print();
}
</script>