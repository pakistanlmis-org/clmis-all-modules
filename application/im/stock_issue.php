<?php
/**
 * stock_issue
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
//echo '<pre>';print_r($_SESSION);exit;
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
//selected stakeholder
$selStk = '';
//selected province
$selProv = '';
//bg color
$bgColor = '';
//funding source
$funding_source = '';
//check if submitted
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {

    if (!empty($_REQUEST['searchby']) && !empty($_REQUEST['number'])) {
        //get searcg by
        $searchby = $_REQUEST['searchby'];
        //get number
        $number = trim($_REQUEST['number']);
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
        //set warehouse	
        $objStockMaster->WHIDTo = $warehouse;
    }
    //check product
    if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
        //get product
        $product = $_REQUEST['product'];
//set product
        $objStockMaster->item_id = $product;
    }
    //check funding source
    if (isset($_REQUEST['funding_source']) && !empty($_REQUEST['funding_source'])) {
        //get funding source
        $funding_source = $_REQUEST['funding_source'];
        //set funding source	
        $objStockMaster->funding_source = $funding_source;
    }
    //check date from
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])) {
        //get date from
        $date_from = $_REQUEST['date_from'];
        $dateArr = explode('/', $date_from);
        //set date from
        $objStockMaster->fromDate = dateToDbFormat($date_from);
    }
    //check date to
    if (isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
        //get date to 
        $date_to = $_REQUEST['date_to'];
        $dateArr = explode('/', $date_to);
        //set date to 
        $objStockMaster->toDate = dateToDbFormat($date_to);
    }
    //get selected province
    $selProv = (!empty($_REQUEST['province'])) ? $_REQUEST['province'] : '';
    //get selected stakeholder
    $selStk = (!empty($_REQUEST['stakeholder'])) ? $_REQUEST['stakeholder'] : '';
    //set selected province
    $objStockMaster->province = $selProv;
    //set selected stakeholder
    $objStockMaster->stakeholder = $selStk;
} else {
    //date from
    $date_from = date('01' . '/m/Y');
    //date to
    $date_to = date('d/m/Y');
    //set date from
    $objStockMaster->fromDate = dateToDbFormat($date_from);
    //set date to
    $objStockMaster->toDate = dateToDbFormat($date_to);
}
//Stock Issue Search
$result = $objStockMaster->StockIssueSearch(2, $wh_id);

$title = "Stock Receive";
//Get User Warehouses
$fundingSources = $objwarehouse->GetUserWarehouses();
//Get User Issue To WH
$warehouses = $objwarehouse->GetUserIssueToWH($wh_id);
//Get All Manage Item
//$items = $objManageItem->GetAllManageItem();
$stk = $_SESSION['user_stakeholder1'];
$items = $objManageItem->GetAllProduct_of_stk($stk);
//Get All Main Stakeholders
$stakeholders = $objstk->GetAllMainTransStakeholders();
//Get All Provinces
$provinces = $objloc->GetAllProvinces();
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
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
                                <h3 class="heading">Stock Issue</h3>
                            </div>
                            <div class="widget-body">
                                <form method="POST" name="batch_search" action="" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12"></div>
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="firstname">Search by</label>
                                                    <select name="searchby" id="searchby" class="form-control input-medium">
                                                        <option value="">Select</option>
                                                        <option value="1" <?php if ($searchby == 1) { ?> selected <?php } ?>>Issue No</option>
                                                        <option value="2" <?php if ($searchby == 2) { ?> selected <?php } ?>>Issue Ref</option>
                                                        <option value="3" <?php if ($searchby == 3) { ?> selected <?php } ?>>Batch No</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="number">&nbsp;</label>
                                                    <input class="form-control input-medium" id="number" name="number" type="text" value="<?php echo $number; ?>" />
                                                </div>
                                            </div>
                                            <?php if ($wh_id == 123) { ?>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label> Funding Source </label>
                                                        <select name="funding_source" id="funding_source" class="form-control input-medium" <?php if (!empty($funding_source) && !empty($TranNo)) { ?>disabled="" <?php } ?>>
                                                            <option value="">Select</option>
                                                            <?php
                                                            if (mysql_num_rows($fundingSources) > 0) {
                                                                //fetch result
                                                                while ($row = mysql_fetch_object($fundingSources)) {
                                                                    //populate funding source combo
                                                                    ?>
                                                                    <option value="<?php echo $row->wh_id; ?>" <?php if ($funding_source == $row->wh_id) { ?> selected="" <?php } ?>> <?php echo $row->wh_name; ?> </option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <?php if (!empty($funding_source) && !empty($TranNo)) { ?>
                                                            <input type="hidden" name="funding_source" id="funding_source" value="<?php echo $funding_source; ?>" />
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label  for="firstname">Product</label>
                                                    <select name="product" id="product" class="form-control input-medium">
                                                        <option value="">Select</option>
                                                        <?php
                                                        if ($items != FALSE) {
                                                            //fetch result
                                                            while ($row = mysql_fetch_object($items)) {
                                                                //populate product combo
                                                                ?>
                                                                <option value="<?php echo $row->itm_id; ?>" <?php if ($product == $row->itm_id) { ?> selected="" <?php } ?>><?php echo $row->itm_name; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label  for="stakeholder">Stakeholder</label>
                                                    <select name="stakeholder" id="stakeholder" class="form-control input-medium">
                                                        <option value="">Select</option>
                                                        <?php
                                                        if ($stakeholders != FALSE) {
                                                            //fetch result
                                                            while ($row = mysql_fetch_object($stakeholders)) {
                                                                //populate stakeholder combo
                                                                $stk_where = " ";
                                                                if($_SESSION['user_stakeholder1'] == 145 )
                                                                    if( $row->stkid !=2 && $row->stkid !=73 ) continue;
                                                                ?>
                                                                <option value="<?php echo $row->stkid; ?>" <?php if ($selStk == $row->stkid) { ?> selected="selected" <?php } ?>><?php echo $row->stkname; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label  for="province">Province</label>
                                                    <select name="province" id="province" class="form-control input-medium">
                                                        <option value="">Select</option>
                                                        <?php
                                                        if ($provinces != FALSE) {
                                                            //fetch results
                                                            while ($row = mysql_fetch_object($provinces)) {
                                                                ?>
                                                                <option value="<?php echo $row->PkLocID; ?>" <?php if ($selProv == $row->PkLocID) { ?> selected="selected" <?php } ?>><?php echo $row->LocName; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="warehouse">Issued To</label>
                                                    <select name="warehouse" id="warehouse" class="form-control input-medium">
                                                        <?php
                                                        if (!empty($selProv) || !empty($selStk)) {
                                                            $and = " 1=1 ";
                                                            if (!empty($selProv)) {
                                                                $and .= " AND tbl_warehouse.prov_id = " . $selProv . " ";
                                                            }if (!empty($selStk)) {
                                                                $and .= " AND tbl_warehouse.stkid = " . $selStk . "";
                                                            }
                                                            //select query
                                                            //gets
                                                            //warehouse id 
                                                            //warehouse name
                                                            $qry = "SELECT DISTINCT
																		tbl_warehouse.wh_id,
																		CONCAT(tbl_warehouse.wh_name,	'(', stakeholder.stkname, ')') AS wh_name
																	FROM
																		tbl_warehouse
																	INNER JOIN tbl_stock_master ON tbl_warehouse.wh_id = tbl_stock_master.WHIDTo
																	INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
																	WHERE
																		$and
																	ORDER BY
																		tbl_warehouse.wh_name ASC";
                                                            $qryRes = mysql_query($qry);
                                                            echo '<option value="">Select</option>';
                                                            while ($row = mysql_fetch_array($qryRes)) {
                                                                $sel = ($warehouse == $row['wh_id']) ? 'selected="selected"' : '';
                                                                echo "<option value=\"$row[wh_id]\" $sel>$row[wh_name]</option>";
                                                            }
                                                        } else {
                                                            echo '<option value="">Select</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12"> 
                                            <!-- Group -->
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="firstname">Date From</label>
                                                    <input type="text" readonly class="form-control input-medium" name="date_from" id="date_from" value="<?php echo $date_from; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label  for="firstname">Date To</label>
                                                    <input type="text" readonly class="form-control input-medium" name="date_to" id="date_to" value="<?php echo $date_to; ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-6" style="text-align:right;">
                                                <label for="firstname">&nbsp;</label>
                                                <div class="form-group">
                                                    <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                    <button type="reset" class="btn btn-info">Reset</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Widget -->
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Issue Search</h3>
                            </div>
                            <!-- // Widget heading END -->

                            <div class="widget-body"> 

                                <!-- Table -->
                                <table class="issuesearch table table-bordered table-condensed">

                                    <!-- Table heading -->
                                    <thead>
                                        <tr>
                                            <th width="2%">Sr. No</th>
                                            <th>Issue No</th>
                                            <th>Ref No</th>
                                            <th>Issue To</th>
                                            <th width="8%">Issue Date</th>
                                            <th>Product</th>
                                            <th>Funding Source</th>
                                            <th>Batch No.</th>
                                            <th>Manufacturer</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th width="8%">Expiry Date</th>
                                                <th width="2%">Action</th>
                                        </tr>
                                    </thead>
                                    <!-- // Table heading END --> 

                                    <!-- Table body -->
                                    <tbody>
                                        <!-- Table row -->
                                        <?php
                                        $i = 1;
                                        if ($result != FALSE) {
                                            $transNo = '';
                                            //fetch results
                                            while ($row = mysql_fetch_object($result)) {
                                                if ($transNo != $row->PkStockID) {
                                                    $bgColor = ($bgColor == '#CCC') ? '#FFF' : '#CCC';
                                                } else {
                                                    $bgColor = $bgColor;
                                                }
                                                $transNo = $row->PkStockID;
                                                ?>
                                                <tr class="gradeX" style="background-color:<?php echo $row->field_color; ?> !important" id="<?php echo $row->PkDetailID; ?>">
                                                    <td class="text-center"><?php echo $i; ?></td>
                                                    <td><a  onclick="window.open('printIssue.php?id=<?php echo $row->PkStockID; ?>', '_blank', 'scrollbars=1,width=842,height=595');" href="javascript:void(0);"><?php echo $row->TranNo; ?></a></td>
                                                    <td><?php echo $row->TranRef; ?>&nbsp;</td>
                                                    <td><?php echo $row->wh_name.' ('.$row->stk_office_name.')'; ?></td>
                                                    <td><?php echo date("d-M-y", strtotime($row->TranDate)); ?></td>
                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td><?php echo $row->funding_source; ?></td>
                                                    <td><?php
                                $pop = 'onclick="window.open(\'product-ledger-history.php?id=' .$row->BatchID . '\',\'_blank\',\'scrollbars=1,width=840,height=595\')"';
                                echo "<a class='alert-link' " . $pop . " ></br>" .  $row->batch_no . "</a>";

//                                echo $b['batch_no']; 
                                ?>
                                                    </td>
                                                    <td><?php echo $row->stkname; ?></td>
                                                    <td class="text-right"><?php echo number_format(abs($row->Qty)); ?></td>
                                                    <td><?php echo $row->UnitType; ?></td>
                                                    <td><?php echo date("d-M-y", strtotime($row->batch_expiry)); ?></td>                                                    
                                                    <?php
                                                    echo '<td class="text-center">';
                                                    if ($_SESSION['user_level']< 3 && $_SESSION['user_id'] == $row->CreatedBy) {
                                                        //national and prov level only, can delete only vouchers which have not been recieved Yet.
                                                        if ($row->IsReceived == 0) {
                                                            echo "<a href=\"javascript: void(0)\" onClick=\"deleteRecord(" . $row->PkDetailID . ", " . $row->BatchID . ")\"><img src=\"" . PUBLIC_URL . "images/cross.gif\" class=\"cursor\"></a>";
                                                        }
                                                    }
                                                    elseif($_SESSION['user_level']==3 && $_SESSION['user_id'] == $row->CreatedBy && $row->lvl >=7){
                                                        //district user only, can delete the already received vouchers
                                                        //put a check here , to allow deletion for only non reported sdps
                                                        if( $row->CreatedOn >= '2018-11-01')
                                                        {
                                                            //temporary check to restrict only for this month
                                                            echo "<a href=\"javascript: void(0)\" onClick=\"deleteIssuanceAndReceiving(" . $row->PkDetailID . ", " . $row->BatchID . ")\"><img src=\"" . PUBLIC_URL . "images/cross.gif\" class=\"cursor\"></a>";
                                                        }
                                                    }
                                                    echo '</td>';
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
<script src="<?php echo PUBLIC_URL; ?>js/dataentry/stockissue.js"></script>
<?php
if (isset($_REQUEST['s']) && $_REQUEST['s'] == 't') {
    ?>
    <script>
                                                        var self = $('[data-toggle="notyfy"]');
                                                        notyfy({
                                                            force: true,
                                                            text: 'Data has been deleted successfully!',
                                                            type: 'success',
                                                            layout: self.data('layout')
                                                        });
    </script>
<?php } ?>
</body>
<!-- END BODY -->
</html>