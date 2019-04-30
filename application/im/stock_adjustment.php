<?php
/**
 * stock_adjustment
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

$title = "New Cold Chain Asset";

if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
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
    if (isset($_REQUEST['batch']) && !empty($_REQUEST['batch'])) {
        //get product
        $batch = $_REQUEST['batch'];
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
} else {
    $date_from = date('01' . '/m/Y');
    $date_to = date('d/m/Y');
}
//get type
$type = isset($_POST['type']) ? $_POST['type'] : '';
//get product
$product = isset($_POST['product']) ? $_POST['product'] : '';
//Get All Product
$stk = $_SESSION['user_stakeholder1'];
$items = $objManageItem->GetAllProduct_of_stk($stk);
//get Adjusment Types
$types = $objTransType->getAdjusmentTypes();
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
$objStockMaster->batch_id = (!empty($batch)) ? $batch : '';
//fromDate
$objStockMaster->fromDate = (!empty($date_from)) ? dateToDbFormat($date_from) : '';
//toDate
$objStockMaster->toDate = (!empty($date_to)) ? dateToDbFormat($date_to) : '';
//Stock Adjustment Search
$adjustment_list = $objStockMaster->StockAdjustmentSearch();
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 

                <!-- BEGIN PAGE HEADER-->

                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Stock Adjustment Search</h3>
                            </div>
                            <!-- // Widget Heading END -->

                            <div class="widget-body">
                                <form method="POST" name="batch_search" action="" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12"></div>
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label>Adjustment No.</label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" id="adjustment_no" name="adjustment_no" value="<?php echo!empty($_POST['adjustment_no']) ? $_POST['adjustment_no'] : ''; ?>" type="text" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label>Adjustment Type</label>
                                                    <div class="controls">
                                                        <select name="type" id="type" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //check count of types
                                                            if (count($types) > 0) {
                                                                //fetch data from types
                                                                foreach ($types as $row) {
                                                                    $sel = '';
                                                                    //check type
                                                                    if ($type) {
                                                                        if ($type == $row->trans_id) {
                                                                            $sel = 'selected';
                                                                        }
                                                                    }
                                                                    echo '<option value="' . $row->trans_id . '" ' . $sel . ' >' . $row->trans_type . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label>Product</label>
                                                    <div class="controls">
                                                        <select name="product" id="product" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //check count of items
                                                            if (mysql_num_rows($items) > 0) {
                                                                //get data from items
                                                                while ($row = mysql_fetch_assoc($items)) {
                                                                    $sel = '';
                                                                    //check product
                                                                    if ($product) {
                                                                        if ($product == $row['itm_id']) {
                                                                            $sel = 'selected';
                                                                        }
                                                                    }
                                                                    echo '<option value="' . $row['itm_id'] . '" ' . $sel . ' >' . $row['itm_name'] . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="firstname"> Batch </label>
                                                    <div class="controls">
                                                        <select name="batch" id="batch" class="form-control input-medium" >
                                                            <option value="">Select</option>
                                                            <?php
                                                            
                                                            if (!empty($product)) {
                                                                //get data from items
                                                                $batches = $objStockBatch->GetAllRunningBatches($product);
                                                                while ($row = mysql_fetch_assoc($batches)) {
                                                                    $sel = '';
                                                                    //check product
                                                                    if (!empty($batch)) {
                                                                        if ($batch == $row['batch_id']) {
                                                                            $sel = 'selected';
                                                                        }
                                                                    }
                                                                    echo '<option value="' . $row['batch_id'] . '" ' . $sel . ' >' . $row['batch_no'] . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-3" style="padding-top: 12px;">
                                                <div class="control-group ">
                                                    <label>Date From</label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" readonly id="date_from" name="date_from" value="<?php echo!empty($date_from) ? $date_from : ''; ?>" type="text" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3" style="padding-top: 12px;">
                                                <div class="control-group ">
                                                    <label>Date To</label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" readonly id="date_to" name="date_to" value="<?php echo!empty($date_to) ? $date_to : ''; ?>" type="text" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group center">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="controls right">
                                                        <button class="btn btn-primary" type="submit" name="search" value="Search">Search</button>
                                                        <button class="btn btn-info" type="reset" id="reset">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- // Row END --> 
                        <!-- Widget -->
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Search Results</h3>
                            </div>
                            <!-- // Widget heading END -->

                            <div class="widget-body"> 

                                <!-- Table --> 
                                <!-- Table -->
                                <table class="adjustment table table-striped table-bordered table-condensed">

                                    <!-- Table heading -->
                                    <thead>
                                        <tr>
                                            <th width="8%">Date</th>
                                            <th>Adjustment No.</th>
                                            <th>Ref. No.</th>
                                            <th>Product</th>
                                            <th>Batch No.</th> 
                                            <th>Quantity</th>
                                            <th>Adjustment Type</th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <!-- // Table heading END --> 

                                    <!-- Table body -->
                                    <tbody>
                                        <!-- Table row -->
                                        <?php
                                        $i = 1;
                                        if ($adjustment_list != FALSE) :
                                            while ($row = mysql_fetch_object($adjustment_list)) :
                                                ?>
                                                <tr class="gradeX">
                                                    <td><?php echo date("d/m/y", strtotime($row->TranDate)); ?></td>
                                                    <td><a  onclick="window.open('printAdjustment.php?id=<?php echo $row->PkStockID; ?>', '_blank', 'scrollbars=1,width=842,height=595');" href="javascript:void(0);"><?php echo $row->TranNo; ?></a></td>
                                                    <td><?php echo $row->TranRef; ?></td>
                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td><?php echo $row->batch_no; ?></td>                                                    
                                                    <td align="right"><?php echo number_format($row->Qty); ?></td>

                                                    <td><?php echo $row->trans_type; ?></td>
                                                    <td><?php echo!empty($row->ReceivedRemarks) ? $row->ReceivedRemarks : '&nbsp;'; ?></td>
                                                </tr>
                                                <?php
                                                $i++;
                                            endwhile;
                                        endif;
                                        ?>
                                        <!-- // Table row END -->
                                    </tbody>
                                    <!-- // Table body END -->

                                </table>
                                <!-- // Table END -->
                                <div class="right">
                                    <button id="print_stock" class="btn btn-warning" type="button">Print</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- // Content END --> 
        </div>
    </div>
    <?php
//includ footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/stock_adjustment.js"></script>
    <!-- END FOOTER -->

</body>
<!-- END BODY -->
</html>