<style>

    .watermark {
        position: absolute;
        color: grey;
        opacity: 0.25;
        font-size: 9em;
        margin-left: 138px;
        z-index: 1000;
        -webkit-transform: rotate(-45deg);
        -moz-transform: rotate(-45deg);
        -o-transform: rotate(-45deg);
        -ms-transform: rotate(-45deg);
        transform: rotate(-45deg);
    }
</style>
<?php
/**
 * stockReceivePrint
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
$title = "Stock Recieve List";
$print = 1;
//get id
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
			tbl_stock_master.PkStockID = " . $stockId;
//query result
$qryRes = mysql_fetch_array(mysql_query($qry));
$wh_id = $qryRes['WHIDTo'];
$userid = $qryRes['CreatedBy'];
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
    $rptName = 'Shipment Receive Voucher';
    //include report_header
    include('report_header.php');
    ?>        
    <div style="text-align:center;">
        <b style="float:left;">Receive Voucher: <?php echo $_REQUEST['rec_no']; ?></b>
        <b style="float:right;">Receiving Time: <?php echo $_REQUEST['rec_date']; ?></b>
    </div>
    <div style="clear:both;">
        <b style="float:left;">Reference No.: <?php echo $_REQUEST['ref_no']; ?></b>
        <b style="float:right;">Source: <?php echo $_REQUEST['rec_from']; ?></b>
    </div>
    <div class="watermark">Draft</div>
    <table id="myTable" cellpadding="3">
        <tr>
            <th width="7%">S. No.</th>
            <th>Product</th>
            <th width="13%">Batch No.</th>
            <th width="12%">Production Date</th>
            <th width="10%">Expiry Date</th>
            <th width="12%">Quantity</th>
            <th width="8%">Unit</th>
            <th width="10%">Cartons</th>
        </tr>
        <tbody>
            <?php
            $summaryArr = array();
            $product = '0';
            $i = 0;
            $totalQty = 0;
            $totalCartons = 0;
            //Get Stocks Receive List
            $result = $objStockMaster->GetStocksReceiveList($userid, $wh_id, 1, $stockId);
            //if result
            if (!empty($result)) {
                //fetch data from result
                while ($row = mysql_fetch_object($result)) {
                    //total qty
                    $totalQty += abs($row->Qty);
                    //total cartons
                    $totalCartons += abs($row->Qty) / $row->qty_carton;
                    //check product
                    if ($product != $row->itm_name) {
                        $summaryArr[$row->itm_name]['Qty'][] = abs($row->Qty);
                        $summaryArr[$row->itm_name]['qty_carton'][] = abs($row->Qty) / $row->qty_carton;
                        $product = $row->itm_name;
                    } else {
                        $summaryArr[$row->itm_name]['Qty'][] = abs($row->Qty);
                        $summaryArr[$row->itm_name]['qty_carton'][] = abs($row->Qty) / $row->qty_carton;
                    }
                    $i++;
                    ?>
                    <tr>
                        <td style="text-align:center;"><?php echo $i; ?></td>
                        <td><?php echo $row->itm_name; ?></td>	
                        <td><?php echo $row->batch_no; ?></td>
                        <td style="text-align:center;"><?php echo!empty($row->production_date) ? date("d/m/y", strtotime($row->production_date)) : ''; ?></td>
                        <td style="text-align:center;"> <?php echo date("d/m/y", strtotime($row->batch_expiry)); ?></td>
                        <td style="text-align:right;"><?php echo number_format($row->Qty); ?></td>
                        <td style="text-align:right;"><?php echo $row->UnitType; ?></td>
                        <td style="text-align:right;"><?php echo number_format($row->Qty / $row->qty_carton); ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align:right;">Total</th>
                <th style="text-align:right;"><?php echo number_format($totalQty); ?></th>
                <th>&nbsp;</th>
                <th style="text-align:right;"><?php echo number_format($totalCartons); ?></th>
                <th colspan="2">&nbsp;</th>
            </tr>
        </tfoot>
    </table>

    <h5 style="margin-top:30px;" class="heading">Summary</h5>
    <table id="myTable" style="width:70%;" cellpadding="3">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Cartons</th>
            </tr>
        </thead>
        <tbody>
            <?php
//get data from summaryArr
            foreach ($summaryArr as $product => $qty) {
                ?>
                <tr>
                    <td><?php echo $product; ?></td>
                    <td style="text-align:right;"><?php echo number_format(array_sum($summaryArr[$product]['Qty'])); ?></td>
                    <td style="text-align:right;"><?php echo number_format(array_sum($summaryArr[$product]['qty_carton'])); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>

    <?php
//include footer
    include('report_footer_rcv.php');
    ?>

    <div style="float:right;margin:20px;" id="printButt">
        <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
    </div>

</div>

<?php
//unset session
unset($_SESSION['stock_id']);
?>
<script src="<?php echo PUBLIC_URL; ?>assets/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script language="javascript">
            $(function () {
                printCont();
            })
            function printCont()
            {
                window.print();
            }
</script>