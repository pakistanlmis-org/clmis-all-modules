<?php
/**
 * stock_receive
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//get warehouse id
$wh_id = $_SESSION['user_warehouse'];
$sCriteria = array();
$date_to = '';
$product = '';
$stock_ledger = array();
$batch_ob = array();
$batch_cb = array();

//check if submitted
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
    //check product
    if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
        //get product
        $product = $_REQUEST['product'];
        $sCriteria['product'] = $product;
        //set product
        $objStockMaster->item_id = $product;
    } else {
        $sCriteria['product'] = 1;
        //set product
        $objStockMaster->item_id = 1;
    }
    //check from date
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])) {
        //get to date
        $date_from = $_REQUEST['date_from'];
        $dateArr = explode('/', $date_from);
        $sCriteria['date_from'] = dateToDbFormat($date_from);
        //set to date
        $objStockMaster->fromDate = dateToDbFormat($date_from);
    }
    //check to date
    if (isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
        //get to date
        $date_to = $_REQUEST['date_to'];
        $dateArr = explode('/', $date_to);
        $sCriteria['date_to'] = dateToDbFormat($date_to);
        //set to date
        $objStockMaster->toDate = dateToDbFormat($date_to);
    }
    $_SESSION['sCriteria'] = $sCriteria;

    $stock_ledger = $objStockMaster->GetStockLedger();
    $batch_ob = $objStockMaster->getBatchOBCB('OB',$product, dateToDbFormat($date_from));
    $batch_cb = $objStockMaster->getBatchOBCB('CB',$product, dateToDbFormat($date_to));

} else {
    //date to
    $date_to = date('d/m/Y');
    //set from date
    $date_from = date('1/m/Y');
    $objStockMaster->toDate = dateToDbFormat($date_to);
    $objStockMaster->fromDate = dateToDbFormat($date_from);

    $sCriteria['date_to'] = dateToDbFormat($date_to);
    $sCriteria['date_from'] = dateToDbFormat($date_from);
    $_SESSION['sCriteria'] = $sCriteria;
}

$items = $objManageItem->GetAllManageItem();
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Search By</h3>
                            </div>
                            <div class="widget-body">
                                <form method="POST" name="batch_search" action="" >
                                    <!-- Row -->
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="product">Product</label>
                                                    <div class="controls">
                                                        <select name="product" id="product" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //check if record exists
                                                            if (mysql_num_rows($items) > 0) {
                                                                while ($row = mysql_fetch_object($items)) {
                                                                    ?>
                                                                    <option value="<?php echo $row->itm_id; ?>" <?php if ($product == $row->itm_id) { ?> selected="" <?php } ?>><?php echo $row->itm_name; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Date From</label>
                                                    <input type="text" class="form-control input-medium" name="date_from"  readonly="" id="date_from" value="<?php echo $date_from; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Date To</label>
                                                    <input type="text" class="form-control input-medium" name="date_to"  readonly="" id="date_to" value="<?php echo $date_to; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3 right">
                                                <div class="form-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Widget -->
                        <div class="widget" data-toggle="collapse-widget"> 

                            <!-- Widget heading -->
                            <div class="widget-head">
                                <h4 class="heading">Stock Ledger</h4>
                            </div>

                            <!-- // Widget heading END -->

                            <div class="widget-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-condensed" id="stkledger">
                                            <thead>
                                            <tr>
                                                <th rowspan="2">S.No</th>
                                                <th rowspan="2">Voucher Date</th>
                                                <th rowspan="2">Voucher Number</th>
                                                <th rowspan="2">Type</th>
                                                <th rowspan="2">Particulars</th>
                                                <th rowspan="2">Batch No.</th>
                                                <th rowspan="2">Expiry</th>
                                                <th colspan="2" class="center">Quantity</th>
                                                <th class="center">Batch Balance</th>
                                                <th class="center">Product Balance</th>
                                                <th rowspan="2">Created Date</th>
                                                <th rowspan="2">Created By</th>
                                            </tr>
                                            <tr>
                                                <th>Receive</th>
                                                <th>Issue</th>
                                                <!--<th>Doses</th> -->
                                            </tr>
                                            </thead>
                                            <?php
                                            //$balance_vials = $this->ob;
                                            ?>
                                            <tbody>
                                            <?php
                                            $count = 1;
                                            $batch_cb = array();
                                            $balance_vials_ob = 0;

                                            if(count($batch_ob) > 0){

                                            foreach($batch_ob as $b){
                                                $balance_vials_ob = $balance_vials_ob + ($b['Qty']);
                                                ?>
                                                <tr>
                                                    <th><?php echo $count; ?></th>
                                                    <th><?php echo $date_from; ?></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>Opening Balance (<?php echo $b['batch_no']; ?>)</th>
                                                    <th><?php echo $b['batch_no']; ?></th>
                                                    <th></th>
                                                    <th class="right"><?php     ?></th>
                                                    <th class="right"></th>
                                                    <th class="right"><?php echo number_format($b['Qty']); ?></th>
                                                    <th class="right"></th>
                                                    <th class="right"><?php  ?></th>
                                                    <th class="right"></th>
                                                    <th class="right"><?php  ?></th>
                                                </tr>
                                                <?php
                                                $count++;
                                            }
                                            }
                                            ?>
                                            <tr>
                                                <th><?php echo $count; ?></th>
                                                <th><?php echo $date_from; ?></th>
                                                <th></th>
                                                <th></th>
                                                <th>Opening Balance (Condom) </th>
                                                <th></th>
                                                <th></th>
                                                <th class="right"><?php  ?></th>
                                                <th class="right"></th>
                                                <th class="right"><?php    ?></th>
                                                <th class="right"></th>
                                                <th class="right"><?php echo number_format($balance_vials_ob); ?></th>
                                                <th class="right"></th>
                                                <th class="right"><?php  ?></th>
                                            </tr>
                                            <?php
                                            $count++;
                                            $balance_vials = 0;
                                            foreach ($stock_ledger as $row) {

                                                $nature = $row['trans_nature'];
                                                $quantity_vials = $row['quantity'];
                                                //$quantity_doses = $quantity_vials * $row->getStockBatchWarehouse()->getStockBatch()->getPackInfo()->getStakeholderItemPackSize()->getItemPackSize()->getNumberOfDoses();
                                                $balance_vials = $balance_vials + ($nature . ABS($quantity_vials));
                                                //$balance_doses = $balance_doses + ($nature . ABS($quantity_doses));
                                                $created_date = $row['CreatedOn'];
                                                $created_by = $row['usrlogin_id'];

                                                if (array_key_exists($row['stock_batch_warehouse_id'], $batch_cb)) {
                                                    $batch_cb[$row['stock_batch_warehouse_id']] = $batch_cb[$row['stock_batch_warehouse_id']] + ($nature . ABS($quantity_vials));
                                                    //$batch_cb_doses = $batch_cb[$row->getStockBatchWarehouse()->getPkId()] * $row->getStockBatchWarehouse()->getStockBatch()->getPackInfo()->getStakeholderItemPackSize()->getItemPackSize()->getNumberOfDoses();
                                                } else {
                                                    $batch_cb[$row['stock_batch_warehouse_id']] = $objStockMaster->getBatchOB($row['stock_batch_warehouse_id'], $date_to) + ($nature . ABS($quantity_vials));
                                                    //$batch_cb_doses = $batch_cb[$row['stock_batch_warehouse_id']] * $row->getStockBatchWarehouse()->getStockBatch()->getPackInfo()->getStakeholderItemPackSize()->getItemPackSize()->getNumberOfDoses();
                                                }

                                                if ($row['TranTypeID'] == 2) {
                                                    $print_link = 'printIssue.php?id=' . $row['stock_master_id'];
                                                    $warehouse_name = "To " . $row['toWh'];
                                                } else {
                                                    $print_link = 'printReceive.php?id=' . $row['stock_master_id'] . '&type=' . $row['TranTypeID'];
                                                    $warehouse_name = "From " . $row['fromWh'];
                                                }
                                                ?>
                                                <tr>
                                                    <td><?php echo $count; ?></td>
                                                    <td title="<?php echo $row['TranNo']; ?>"><?php echo $row['TranDate']; ?></td>
                                                    <td><a onclick="window.open('<?php echo $print_link; ?>', '_blank', 'scrollbars=1,width=860,height=595');" href="javascript:void(0);"><?php echo $row['TranNo']; ?></a></td>
                                                    <td><?php echo $row['trans_type']; ?></td>
                                                    <td><?php echo $warehouse_name; ?></td>
                                                    <td title="<?php   ?>"><?php   ?><?php echo $row['batch_no']; ?></td>
                                                    <td><?php echo $row['batch_expiry']; ?></td>
                                                    <?php if ($row['quantity'] > 0) { ?>
                                                        <td class="right"><?php echo number_format(ABS($quantity_vials)); ?></td>
                                                        <td class="right"></td>
                                                    <?php } else { ?>
                                                        <td class="right"></td>
                                                        <td class="right"><?php echo number_format(ABS($quantity_vials)); ?></td>
                                                    <?php } ?>
                                                    <!--<td class="right"><?php //echo number_format($batch_cb_doses);  ?></td> -->
                                                    <td class="right"><?php echo number_format($batch_cb[$row['stock_batch_warehouse_id']]); ?></td>
                                                    <!-- <td class="right"><?php //echo number_format($balance_doses);  ?></td> -->
                                                    <td class="right"><?php echo number_format($balance_vials); ?></td>
                                                    <td><?php echo $created_date; ?></td>
                                                    <td><?php echo $created_by; ?></td>
                                                    <!-- <td class="right"><?php  ?></td> -->
                                                </tr>
                                                <?php $count++; }
                                            foreach($batch_ob as $cb){
                                            ?>
                                            <tr>
                                                <th><?php echo $count; ?></th>
                                                <th><?php echo $date_to; ?></th>
                                                <th></th>
                                                <th></th>
                                                <th>Closing Balance (<?php echo $cb['batch_no']; ?>)</th>
                                                <th><?php echo $cb['batch_no']; ?></th>
                                                <th></th>
                                                <th class="right"><?php     ?></th>
                                                <th class="right"></th>
                                                <th class="right"><?php echo number_format($cb['Qty']); ?></th>
                                                <th class="right"></th>
                                                <th class="right"><?php ?></th>
                                                <th class="right"></th>
                                                <th class="right"><?php  ?></th>
                                                <!-- <th class="right"><?php   ?></th> -->
                                                <!-- </tr>-->
                                                <?php
                                                $count++;
                                                }
                                                ?>
                                            <tr>
                                                <th><?php echo $count; ?></th>
                                                <th><?php echo $date_to; ?></th>
                                                <th></th>
                                                <th></th>
                                                <th>Closing Balance (Condom)</th>
                                                <th></th>
                                                <th></th>
                                                <th class="right"><?php  ?></th>
                                                <th class="right"></th>
                                                <th class="right"><?php     ?></th>
                                                <th class="right"></th>
                                                <th class="right"><?php echo number_format($balance_vials); ?></th>
                                                <th class="right"></th>
                                                <th class="right"><?php  ?></th>
                                                <!-- <th class="right"><?php   ?></th> -->
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- // Content END --> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; ?>
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/stockreceive.js"></script>
    <?php
    //unset stock id
    unset($_SESSION['stock_id']);
    ?>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>