<?php
/**
 * print_batch_management
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
$title = "Batch Management";
$print = 1;
$print = true;
//echo '<pre>';print_r($_SESSION);exit;
//check type
if (isset($_REQUEST['type']) && $_REQUEST['type'] == 1) {
    //query 
    //gets
    //itm_name,
    //qty_carton,
    //Vials,
    //UnitType
    $qry = "SELECT
			itminfo_tab.itm_name,
			itminfo_tab.qty_carton,
			SUM(stock_batch.Qty) AS Vials,
			tbl_itemunits.UnitType
		FROM
			stock_batch
		INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
		LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
		WHERE
			stock_batch.`wh_id` = '" . $_SESSION['user_warehouse'] . "'
		GROUP BY
			itminfo_tab.itm_id
		ORDER BY
			itminfo_tab.frmindex";

    //query result
    $qryRes = mysql_query($qry);
    $num = mysql_num_rows($qryRes);
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
        $rptName = 'Batch Management Summary';
        //include header
        include('report_header.php');
        ?>
        <table id="myTable" class="table-condensed">
            <thead>
                <tr>
                    <th>S. No.</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Cartons</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //check num
                if ($num > 0) {
                    $i = 1;
                    $totalQty = $totalCartons = '';
                    //fetch data from qryRes
                    while ($row = mysql_fetch_object($qryRes)) {
                        //total qty
                        $totalQty += abs($row->Vials);
                        //total cartons
                        $totalCartons += abs($row->Vials) / $row->qty_carton;
                        ?>
                        <!-- Table row -->
                        <tr>
                            <td style="text-align:center;"><?php echo $i; ?></td>
                            <td><?php echo $row->itm_name; ?></td>
                            <td style="text-align:right;"><?php echo number_format($row->Vials); ?></td>
                            <td style="text-align:right;"><?php echo $row->UnitType; ?></td>
                            <td style="text-align:right;"><?php echo number_format($row->Vials / $row->qty_carton); ?></td>
                        </tr>
            <?php $i++;
        }
    } ?>
                <!-- // Table row END -->
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" style="text-align:right;">Total</th>
                    <th style="text-align:right;"><?php echo number_format($totalQty); ?></th>
                    <th>&nbsp;</th>
                    <th style="text-align:right;"><?php echo number_format($totalCartons); ?></th>
                </tr>
            </tfoot>
        </table>
        <div style="float:right; margin-top:10px;" id="printButt">
            <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
        </div>

    </div>

    <?php
} else {
    //check product
    if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
        //get product
        $product = trim($_REQUEST['product']);
    }
    //check status
    if (isset($_REQUEST['status']) && !empty($_REQUEST['status'])) {
        //get status
        $status = trim($_REQUEST['status']);
    }
    //check batch_no
    if (isset($_REQUEST['batch_no']) && !empty($_REQUEST['batch_no'])) {
        //get batch_no
        $batch_no = trim($_REQUEST['batch_no']);
    }
    //check ref_no
    if (isset($_REQUEST['ref_no']) && !empty($_REQUEST['ref_no'])) {
        //get ref_no
        $ref_no = trim($_REQUEST['ref_no']);
    }
    //check funding_source
    if (isset($_REQUEST['funding_source']) && !empty($_REQUEST['funding_source'])) {
        //get funding_source
        $funding_source = trim($_REQUEST['funding_source']);
        $objStockBatch->funding_source = $funding_source;
    }

    $result = $objStockBatch->search($product, $batch_no, $ref_no, $status);
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
        $rptName = 'Batch Management';
        include('report_header.php');
        ?>
        <div>
            <table id="myTable" class="table-condensed">
                <thead>
                    <tr>
                        <th>S. No.</th>
                        <th>Product</th>
                        <th>Batch No.</th>
                        <th>Expiry Date</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Cartons</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysql_num_rows($result) > 0) {
                        $i = 1;
                        $totalCartons = $totalQty = '';
                        while ($row = mysql_fetch_object($result)) {
                            $totalQty += abs($row->BatchQty);
                            $totalCartons += abs($row->BatchQty) / $row->qty_carton;
                            ?>
                            <!-- Table row -->
                            <tr class="gradeX">
                                <td style="text-align:center;"><?php echo $i; ?></td>
                                <td style="padding-left:5px;"><?php echo $row->itm_name; ?></td>
                                <td><?php echo $row->batch_no; ?></td>
                                <td style="text-align:center;"><?php echo date("d/m/y", strtotime($row->batch_expiry)); ?></td>
                                <td style="text-align:right;"><?php echo number_format($row->BatchQty); ?></td>
                                <td style="text-align:right;"><?php echo $row->UnitType; ?></td>
                                <td style="text-align:right;"><?php echo number_format($row->BatchQty / $row->qty_carton); ?></td>
                                <td id="batch<?php echo $row->batch_id; ?>-status" style="padding-left:5px;"><?php echo $row->status; ?></td>
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
                        <th colspan="4" style="text-align:right;">Total</th>
                        <th style="text-align:right;"><?php echo number_format($totalQty); ?></th>
                        <th>&nbsp;</th>
                        <th style="text-align:right;"><?php echo number_format($totalCartons); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <div style="float:right;" id="printButt">
                <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
            </div>
        </div>

    </div>
    <?php
}
?>
<script src="<?php echo ASSETS; ?>global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script language="javascript">
                $(function() {
                    printCont();
                })
                function printCont()
                {
                    window.print();
                }
</script>