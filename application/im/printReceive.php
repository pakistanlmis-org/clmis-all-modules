<?php
/**
 * printReceive
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
include(PUBLIC_PATH . "html/header.php");

$title = "Stock Recieve Voucher";
$print = 1;
$stockId = $_GET['id'];
//query
//gets
//WHIDTo
//CreatedBy
$qry = "SELECT
				tbl_stock_master.WHIDTo,
				tbl_stock_master.CreatedBy
			FROM
				tbl_stock_master
			WHERE
				tbl_stock_master.PkStockID = " . $_GET['id'];
//query result
$qryRes = mysql_fetch_array(mysql_query($qry));
$wh_id = $qryRes['WHIDTo'];
$userid = $qryRes['CreatedBy'];
//Get Stocks Receive List
$stocks = $objStockMaster->GetStocksReceiveList($userid, $wh_id, 1, $stockId);
$receiveArr = array();
//fetch data from stocks
while ($row = mysql_fetch_object($stocks)) {
    //rec_no
    $rec_no = $row->TranNo;
    //tran_ref
    $tran_ref = $row->TranRef;
    //rec_date
    $rec_date = $row->TranDate;
    //rec_from
    $rec_from = $row->wh_name;
    //IsSupplier
    $IsSupplier = $row->stk_type_id;
    //receiveArr
    $receiveArr[] = $row;
}
if ($IsSupplier == 2) {
    $rcvFrom = 'Supplier';
} else {
    $rcvFrom = 'Warehouse';
}
?>

<div id="content_print">
    <div style="float:right; font-size:12px;">QR/016/01.08.12</div>
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
    $rptName = "Stock Recieve From $rcvFrom Voucher";
//include header
    include('report_header.php');
    ?>
    <div style="text-align:center;">
        <b style="float:left;">Receive Voucher: <?php echo $rec_no; ?></b>
        <b style="float:right;">Receiving Time: <?php echo date("d/m/y h:i A", strtotime($rec_date)); ?></b>
    </div>
    <div style="clear:both;">
        <b style="float:left;">Reference No.: <?php echo $tran_ref; ?></b>
        <b style="float:right;">Source: <?php echo $rec_from; ?></b>
    </div>

    <table id="myTable" class="table-condensed">
        <tr>
            <td width="8%"><b>S. No.</b></td>
            <td><b>Product</b></td>
            <td width="15%"><b>Batch No.</b></td>
            <td width="16%"><b>Production Date</b></td>
            <td width="12%"><b>Expiry Date</b></td>
            <td align="center"><b>Quantity</b></td>
            <td align="center" width="8%"><b>Unit</b></td>
            <td align="center"><b>Cartons</b></td>
        </tr>
        <tbody>
            <?php
            $i = 0;
            $totalQty = 0;
            $totalCartons = 0;
            if (!empty($receiveArr)) {
                foreach ($receiveArr as $val) {
                    $batch[] = $val->batch_no;
                    $i++;
                    $totalQty += abs($val->Qty);
                    $totalCartons += abs($val->Qty) / $val->qty_carton;
                    ?>
                    <tr>
                        <td style="text-align:center;"><?php echo $i; ?></td>
                        <td><?php echo $val->itm_name; ?></td>
                        <td><?php echo $val->batch_no; ?></td>
                        <td style="text-align:center;"><?php echo!empty($val->production_date) ? date("d/m/y", strtotime($val->production_date)) : ''; ?></td>
                        <td style="text-align:center;"> <?php echo date("d/m/y", strtotime($val->batch_expiry)); ?></td>
                        <td style="text-align:right;"><?php echo number_format($val->Qty); ?></td>
                        <td style="text-align:right;"><?php echo $val->UnitType; ?></td>
                        <td style="text-align:right;"><?php echo number_format(abs($val->Qty) / $val->qty_carton); ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align:right;">Total</th>
                <th style="text-align:right;"><?php echo number_format($totalQty); ?></th>
                <th>&nbsp;</th>
                <th style="text-align:right;"><?php echo number_format($totalCartons); ?></th>
            </tr>
        </tfoot>
        </tbody>

    </table>
    <?php
// Check if adjustments exists
    $batchNums = implode(',', $batch);
//query
//gets
//tbl_stock_master.TranDate,
//TranNo,
//TranRef,
//itm_name,
//batch_no,
//Qty,
//ReceivedRemarks,
//trans_type,
//itminfo_tab.itm_type
    $qry = "SELECT
					tbl_stock_master.TranDate,
					tbl_stock_master.TranNo,
					tbl_stock_master.TranRef,
					itminfo_tab.itm_name,
					stock_batch.batch_no,
					tbl_stock_detail.Qty,
					tbl_stock_master.ReceivedRemarks,
					tbl_trans_type.trans_type,
					itminfo_tab.itm_type
				FROM
					tbl_stock_master
				INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
				INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
				INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
				INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
				WHERE
					tbl_stock_master.WHIDFrom = '$wh_id'
				AND tbl_stock_master.WHIDTo = '-1'
				AND stock_batch.batch_no IN ('" . $batchNums . "')
				ORDER BY
					tbl_stock_master.PkStockID DESC";
    $qryRes = mysql_query($qry);
    if (mysql_num_rows(mysql_query($qry)) > 0) {
        ?>

        <h3 style="margin-bottom:0px;">Adjustments</h3>
        <table id="myTable" cellpadding="3">

            <!-- Table heading -->
            <thead>
                <tr>
                    <th width="8%">Date</th>
                    <th>Adjustment No.</th>
                    <th>Product</th>
                    <th>Batch No.</th>
                    <th>Quantity</th>
                    <th>Adjustment Type</th>
                </tr>
            </thead>
            <!-- // Table heading END -->

            <!-- Table body -->
            <tbody>
                <!-- Table row -->
                <?php
                $i = 1;
                while ($row = mysql_fetch_object($qryRes)) :
                    ?>
                    <tr class="gradeX">
                        <td><?php echo date("d/m/y", strtotime($row->TranDate)); ?></td>
                        <td><?php echo $row->TranNo; ?></td>
                        <td><?php echo $row->itm_name; ?></td>
                        <td><?php echo $row->batch_no; ?></td>
                        <td style="text-align:right;"><?php echo number_format(abs($row->Qty)); ?></td>
                        <td><?php echo $row->trans_type; ?></td>
                    </tr>
                    <?php
                    $i++;
                endwhile;
                ?>
                <!-- // Table row END -->
            </tbody>
            <!-- // Table body END -->

        </table>

        <?php
    }
    ?>

    <?php include('report_footer_rcv.php'); ?>

    <div style="float:right;margin:20px;" id="printButt">
        <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
    </div>
</div>


<script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script language="javascript">
            $(function() {
                printCont();
            })
            function printCont()
            {
                window.print();
            }
</script>