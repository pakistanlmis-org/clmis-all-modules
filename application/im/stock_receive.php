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
//number
$number = '';
//date from
$date_from = '';
//date to 
$date_to = '';
//search by
$searchby = '';
//warehouse
$warehouse = '';
//product
$product = '';
//manufacturer
$manufacturer = '';

//check if submitted
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
//check search by
    if (!empty($_REQUEST['searchby']) && !empty($_REQUEST['number'])) {
        //get search by
        $searchby = $_REQUEST['searchby'];
        //get number
        $number = trim($_REQUEST['number']);
        $sCriteria['searchby'] = $searchby;
        $sCriteria['number'] = $number;
        switch ($searchby) {
            case 1:
                //transaction number
                $objStockMaster->TranNo = $number;
                break;
            case 2:
                //transaction reference
                $objStockMaster->TranRef = $number;
                break;
            case 3:
                //batch number
                $objStockMaster->batch_no = $number;
                break;
        }
    }
    //check warehouse
    if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
        //get warehouse
        $warehouse = $_REQUEST['warehouse'];
        $sCriteria['warehouse'] = $warehouse;
        //set from warehouse
        $objStockMaster->WHIDFrom = $warehouse;
    }
    //check product
    if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
        //get product
        $product = $_REQUEST['product'];
        $sCriteria['product'] = $product;
        //set product
        $objStockMaster->item_id = $product;
    }
    //check manufacturer
    if (isset($_REQUEST['manufacturer']) && !empty($_REQUEST['manufacturer'])) {
        //get manufacturer
        $manufacturer = $_REQUEST['manufacturer'];
        //set manufacturer	
        $objStockMaster->manufacturer = $manufacturer;
    }
    //check date from
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])) {
        //get date from
        $date_from = $_REQUEST['date_from'];
        $dateArr = explode('/', $date_from);
        $sCriteria['date_from'] = dateToDbFormat($date_from);
        //set date from	
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
} else {
    //date from
    $date_from = date('01' . '/m/Y');
    //date to
    $date_to = date('d/m/Y');
    //set from date
    $objStockMaster->fromDate = dateToDbFormat($date_from);
    //set to date
    $objStockMaster->toDate = dateToDbFormat($date_to);

    $sCriteria['date_from'] = dateToDbFormat($date_from);
    $sCriteria['date_to'] = dateToDbFormat($date_to);
    ;
    $_SESSION['sCriteria'] = $sCriteria;
}

//Stock Search
$result = $objStockMaster->StockSearch(1, $wh_id);
//title
$title = "Stock Receive";
//Get User Receive From WH
$warehouses = $objwarehouse->GetUserReceiveFromWH($wh_id);
//get all item
//$items = $objManageItem->GetAllManageItem();
$stk = $_SESSION['user_stakeholder1'];
$items = $objManageItem->GetAllProduct_of_stk($stk);
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
    //Get All Manufacturers Update
    $manufacturers = $manufacturer_product = $objstk->GetAllManufacturersUpdate($_REQUEST['product']);
}
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
                                <h3 class="heading">Stock Receive Search</h3>
                            </div>
                            <div class="widget-body">
                                <form method="POST" name="batch_search" action="" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label">Search by</label>
                                                    <div class="controls">
                                                        <select name="searchby" id="searchby" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <option value="1" <?php if ($searchby == 1) { ?> selected <?php } ?>>Receive No</option>
                                                            <option value="2" <?php if ($searchby == 2) { ?> selected <?php } ?>>Receive Ref</option>
                                                            <option value="3" <?php if ($searchby == 3) { ?> selected <?php } ?>>Batch No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="number">&nbsp;</label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" id="number" name="number" type="text" value="<?php echo $number; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="warehouse">Warehouse/Supplier</label>
                                                    <div class="controls">
                                                        <select name="warehouse" id="warehouse" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //check if record exists
                                                            if (mysql_num_rows($warehouses) > 0) {
                                                                while ($row = mysql_fetch_object($warehouses)) {
                                                                    ?>
                                                                    <option value="<?php echo $row->wh_id; ?>" <?php if ($warehouse == $row->wh_id) { ?> selected="" <?php } ?>><?php echo $row->wh_name; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
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
                                        </div>
                                        <div class="col-md-12"> 
                                            <!-- Group -->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Date From</label>
                                                    <input type="text" class="form-control input-medium" name="date_from" readonly id="date_from" value="<?php echo $date_from; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">Date To</label>
                                                    <input type="text" class="form-control input-medium" name="date_to"  readonly="" id="date_to" value="<?php echo $date_to; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6 right">
                                                <div class="form-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                        <button type="reset" class="btn btn-info" id="reset">Reset</button>
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
                                <h4 class="heading">Receive Search</h4>
                            </div>

                            <!-- // Widget heading END -->

                            <div class="widget-body"> 

                                <!-- Table --> 
                                <!-- Table -->
                                <table class="receivesearch table table-bordered table-condensed">

                                    <!-- Table heading -->
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Receive No</th>
                                            <th>Ref No</th>
                                            <th>Receive From</th>
                                            <th>Receive Date</th>
                                            <th>Product</th>
                                            <th>Batch No</th>
                                            <th>Manufacturer</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Expiry Date</th>
                                            <?php
                                            if ($_SESSION['user_role'] == 4) {
                                                ?>
                                                <th>Action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <!-- // Table heading END --> 

                                    <!-- Table body -->
                                    <tbody>

                                        <!-- Table row -->
                                        <?php
                                        $i = 1;
                                        $transNo = '';
                                        if ($result != FALSE) {
                                            //fetch result
                                            while ($row = mysql_fetch_object($result)) {
                                                if ($transNo != $row->PkStockID) {
                                                    $bgColor = (!empty($bgColor) &&  $bgColor == '#CCC') ? '#FFF' : '#CCC';
                                                } else {
                                                    $bgColor = $bgColor;
                                                }
                                                $transNo = $row->PkStockID;
                                                ?>
                                                <tr class="gradeX" style="background-color:<?php echo $row->field_color; ?> !important" id="<?php echo $row->PkDetailID; ?>">
                                                    <td class="text-center"><?php echo $i; ?></td>
                                                    <td><a onClick="window.open('printReceive.php?id=<?php echo $row->PkStockID; ?>', '_blank', 'scrollbars=1,width=842,height=595');" href="javascript:void(0);"><?php echo $row->TranNo; ?></a></td>
                                                    <td><?php echo '&nbsp;' . $row->TranRef; ?></td>
                                                    <td><?php echo $row->wh_name; ?></td>
                                                    <td width="8%"><?php echo date("d-M-y", strtotime($row->TranDate)); ?></td>
                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td><?php echo $row->batch_no; ?></td>
                                                    <td><?php echo $row->stkname; ?></td>
                                                    <td class="text-right"><?php echo number_format($row->Qty); ?></td>
                                                    <td><?php echo (!empty($row->itm_type)?$row->itm_type:''); ?></td>
                                                    <td width="8%"><?php echo date("d-M-y", strtotime($row->batch_expiry)); ?></td>                                                    
                                                    <?php
                                                    if ($_SESSION['user_role'] == 4) {
                                                        echo '<td class="text-center">';
                                                        // Check if Stock is issued
                                                        $qry = "SELECT
                                                                        tbl_stock_detail.Qty
                                                                FROM
                                                                        tbl_stock_master
                                                                INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                                                WHERE
                                                                        tbl_stock_detail.BatchID = " . $row->BatchID . "
                                                                AND tbl_stock_master.TranTypeID = 2";
                                                        $qryRes = mysql_fetch_array(mysql_query($qry));

                                                        if (!$qryRes) {
                                                            echo "<a href=\"javascript: void(0)\" onClick=\"deleteRecord(" . $row->PkDetailID . ", " . $row->BatchID . ")\"><img src=\"" . PUBLIC_URL . "images/cross.gif\" class=\"cursor\"></a>";
                                                        }
                                                        echo '</td>';
                                                    }
                                                    ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                        }
                                        ?>
                                        <!-- // Table row END -->
                                    </tbody>
                                    <!-- // Table body END -->

                                </table>
                                <!-- // Table END -->
                                <?php if ($result != FALSE) { ?>
                                    <div class="right" style="margin-top:10px;">
                                        <div style="float:right;"> <b>Detail: </b>
                                            <input type="radio" name="groupBy" id="none" value="none" checked="checked" />
                                            None
                                            <input type="radio" name="groupBy" id="loc" value="loc" />
                                            Location wise
                                            <input type="radio" name="groupBy" id="prod" value="prod" />
                                            Product wise
                                            <button id="print_vaccine_issue" type="button" class="btn btn-warning">Print</button>
                                        </div>
                                        <div style="float:left;"> <b>Summary: </b>
                                            <input type="radio" name="summary" id="prod" value="prod" checked="checked" />
                                            Product Wise
                                            <input type="radio" name="summary" id="loc" value="loc" />
                                            Location wise
                                            <button id="print_vaccine_summary" type="button" class="btn btn-warning">Print</button>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>
                                <?php } ?>
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
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/stockreceive.js"></script>
    <?php
    //unset stock id
    unset($_SESSION['stock_id']);
    if (!empty($_REQUEST['s']) && $_REQUEST['s'] == 't') {
        ?>
        <script type="text/javascript">
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: 'Data has been deleted successfully!',
                type: 'success',
                layout: self.data('layout')
            });
        </script>
    <?php } ?>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>