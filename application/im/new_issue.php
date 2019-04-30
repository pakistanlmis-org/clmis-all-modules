<?php
/**
 * new_issue
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
//echo '<pre>';print_r($_SESSION);exit;
//title
$title = "New Issue";
//transaction number
$TranNo = '';
//stock id
$stock_id = 0;
//user id
$userid = $_SESSION['user_id'];
//warehouse id
$wh_id = $_SESSION['user_warehouse'];
//Received Remarks 
$ReceivedRemarks = $issued_by = $funding_source = $js = '';
//Get Temp Stock Issue
$tempstocksIssue = $objStockMaster->GetTempStockIssue($userid, $wh_id, 2);
if ($tempstocksIssue != FALSE) {
    //result
    $result = mysql_fetch_object($tempstocksIssue);
    //stock id
    $stock_id = $result->PkStockID;
    //transaction date
    $TranDate = date('d/m/Y', strtotime($result->TranDate));
    //funding source
    $funding_source = $result->funding_source;
    //receive remarks
    $ReceivedRemarks = $result->ReceivedRemarks;
    //transaction number
    $TranNo = $result->TranNo;
    //transaction ref
    $TranRef = $result->TranRef;
    //warehouse name
    $wh_name = $result->wh_name;
    //warehouse id
    $whouse_id = $result->WHIDTo;
    //issued by
    $issued_by = $result->issued_by;
    //to warehouse
    $whTo = $result->WHIDTo;
} else {
    //transaction date
    $TranDate = date("d/m/Y");
    //warehouse name
    $wh_name = '';
    //transaction ref
    $TranRef = '';
}
//get user warehouse
$warehouses = $warehouses1 = $objwarehouse->GetUserWarehouses();
//GetTempStocksIssueList
$tempstocks = $objStockMaster->GetTempStocksIssueList($userid, $wh_id, 2);
//GetAllWHProduct
$items = $objManageItem->GetAllWHProduct();

// Query to get the last transaction information of the warehouse

unset($_SESSION['lastTransStk']);
unset($_SESSION['lastTransWH']);
unset($_SESSION['lastTransOffice']);
unset($_SESSION['lastTransProv']);
unset($_SESSION['lastTransDist']);

if ($wh_id == 123) { // If central warehouse
    //select query
    //gets
    //WHIDTo,
    //stkofficeid,
    //stkid,
    //lvl,
    //prov_id,
    //dist_id
    $qry = "SELECT
				tbl_stock_master.WHIDTo,
				tbl_warehouse.stkofficeid,
				tbl_warehouse.stkid,
				stakeholder.lvl,
				tbl_warehouse.prov_id,
				tbl_warehouse.dist_id
			FROM
				tbl_stock_master
			INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
				tbl_stock_master.WHIDFrom = $wh_id
			AND tbl_stock_master.TranTypeID = 2
			ORDER BY
				tbl_stock_master.PkStockID DESC
			LIMIT 1";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    $_SESSION['lastTransStk'] = $row['stkid'];
    $_SESSION['lastTransWH'] = $row['WHIDTo'];
    $_SESSION['lastTransOffice'] = $row['lvl'];
    $_SESSION['lastTransProv'] = $row['prov_id'];
    $_SESSION['lastTransDist'] = $row['dist_id'];
}
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //include top
        //include top_im
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">New Issue</h3>
                            </div>
                            <div class="widget-body">
<?php if (isset($_GET['warehouse']) && $_GET['warehouse'] == 1) { ?>
                                    <div class="alert alert-danger">
                                        <button data-dismiss="alert" class="close" type="button"> X</button>
                                        Please select warehouse! </div>
<?php } ?>
                                <form method="POST" name="new_issue_form" id="new_issue_form" action="new_issue_action.php">
                                    <input type="hidden" id="show_national" value="yes">
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12"> </div>
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="firstname"> Issue No </label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" id="issue_no" name="issue_no" type="text" disabled=""
                                                               value="<?php echo $TranNo; ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="firstname"> Date </label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" id="issue_date" readonly name="issue_date" required type="text" value="<?php echo $TranDate; ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="firstname"> Issue Reference </label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" id="issue_ref" name="issue_ref" type="text" value="<?php echo $TranRef; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="firstname"> Issued By </label>
                                                    <div class="controls">
                                                        <select class="form-control input-medium" name="issued_by" id="issued_by">
                                                            <?php
                                                            //select quey
                                                            //gets
                                                            //pk id
                                                            //list value
                                                            $qry = "SELECT
                                                                            list_detail.pk_id,
                                                                            list_detail.list_value
                                                                    FROM
                                                                            list_detail
                                                                    WHERE
                                                                            list_detail.list_master_id = 21
                                                                    ORDER BY
                                                                            -list_detail.rank desc,
                                                                            list_value";
                                                            //query result
                                                            $qryRes = mysql_query($qry);
                                                            //fetch result
                                                            while ($row = mysql_fetch_array($qryRes)) {
                                                                $sel = ($issued_by == $row['pk_id']) ? 'selected="selected"' : '';
                                                                echo "<option value=\"$row[pk_id]\" $sel>$row[list_value]</option>";
                                                            }
                                                            ?>
                                                        </select>
<?php ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($wh_id == 123) { ?>
                                            <input type="hidden" id="showSelection" value="<?php echo $_SESSION['lastTransStk']; ?>" />
                                        <?php } ?>
                                        <?php
                                        if ($wh_name == '') {
                                            ?>
                                            <?php
                                            $button = 'true';

                                            $user_lvl = (!empty($_SESSION['user_level']) ? $_SESSION['user_level'] : '');
                                            switch ($user_lvl) {
                                                case 1:
                                                case 2:
                                                case 3:
                                                case 4:
                                                    //include levelcombos_all_levels
                                                    include("levelcombos_all_levels.php");
                                                    $js = 'levelcombos_all_levels.js';
                                                    break;
                                                /* case 4:
                                                  include("levelcombos.php");
                                                  $js = 'levelcombos.js';
                                                  break; */
                                            }
                                            ?>
                                            <?php
                                        } else {
                                            ?>
                                            <div class="col-md-12">
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <input class="form-control input-medium" id="recipient" name="" type="text" disabled="" value="<?php echo $wh_name; ?>"/>
                                                            <input class="form-control input-medium" id="warehouse" name="warehouse" type="hidden" value="<?php echo $whouse_id; ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                        ?>
                                        <div class="col-md-12">
<?php if ($_SESSION['user_warehouse'] == 123) { ?>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="firstname"> Funding Source <span class="red">*</span> </label>
                                                        <div class="controls">
                                                            <select name="funding_source" id="funding_source" required="true" class="form-control input-medium" <?php if (!empty($funding_source) && !empty($TranNo)) { ?>disabled="" <?php } ?>>
                                                                <option value="">Select</option>
                                                                <?php
                                                                //check if result exists
                                                                if (mysql_num_rows($warehouses) > 0) {
                                                                    //fetch result
                                                                    while ($row = mysql_fetch_object($warehouses)) {
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
                                                </div>
<?php } ?>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="firstname"> Product <span class="red">*</span> </label>
                                                    <div class="controls">
                                                        <select name="product" id="product" class="form-control input-medium" required="true">
                                                            <option value=""> Select </option>
                                                            <?php
                                                            foreach ($items as $item) {
                                                                echo "<option value=" . $item['id'] . ">" . $item['name'] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="firstname"> Batch <span class="red">*</span> </label>
                                                    <div class="controls">
                                                        <select name="batch" id="batch" class="form-control input-medium" required="true">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label" for="firstname"> Quantity <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control input-medium num" name="qty" id="qty" autocomplete="off" required/>
                                                    <span id="product-unit"></span> <span id="product-unit1" style="display:none;"></span> </div>
                                            </div>
                                            <div class="" id="itembatches">
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="firstname"> Available </label>
                                                        <div class="controls">
                                                            <input type="text" class="form-control input-medium num" name="available_qty" id="available_qty" readonly/>
                                                            <input type="hidden" value="" id="ava_qty" name="ava_qty" class="form-control input-medium">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" id="expiry_div">
                                                    <div class="control-group">
                                                        <label class="control-label" for="firstname"> Expiry date </label>
                                                        <div class="controls">
                                                            <input type="text" class="form-control input-medium num" name="expiry_date" id="expiry_date" readonly />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label" for="firstname"> Comments (Max 300 Char) </label>
                                                <textarea name="comments" id="comments" maxlength="300" style="resize:none;" class="form-control input-medium"><?php echo $ReceivedRemarks; ?></textarea>
                                            </div>
                                            <div class="col-md-9 right" style="margin-top:35px;">
                                                <label class="control-label" for="firstname"> &nbsp; </label>
                                                <div class="controls">
                                                    <button type="submit" class="btn btn-primary" id="add_issue"> Add Issue </button>
                                                    <button type="reset" class="btn btn-info"> Reset </button>
                                                    <input type="hidden" name="trans_no" id="trans_no" value="<?php echo $TranNo; ?>"/>
                                                    <input type="hidden" name="stock_id" id="stock_id" value="<?php echo $stock_id; ?>"/>
                                                    <input type="hidden" name="prov_id" id="prov_id" value="<?php echo $_SESSION['user_province']; ?>"/>
                                                    <input type="hidden" name="dist_id" id="prov_id" value="<?php echo isset($_SESSION['dist_id']) ? $_SESSION['dist_id'] : ''; ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- // Row END -->
<?php if ($tempstocks != FALSE) { ?>
                            <!-- Widget -->
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Issue List</h3>
                                </div>
                                <!-- // Widget heading END -->
                                <div class="widget-body" id="gridData">
                                    <!-- Table -->
                                    <!-- Table -->
                                    <table class="table table-striped table-bordered table-condensed" id="myTable">
                                        <!-- Table heading -->
                                        <thead>
                                            <tr bgcolor="#009C00" style="color:#FFF;">
                                                <th>Date</th>
                                                <th>Product</th>
                                                <th>Unit</th>
                                                <th>Issue To</th>
                                                <th class="span2">Quantity</th>
                                                <th>Cartons</th>
                                                <th class="span2">Batch</th>
                                                <th>Expiry Date</th>
                                                <th class="center" width="50">Action</th>
                                            </tr>
                                        </thead>
                                        <!-- // Table heading END -->
                                        <!-- Table body -->
                                        <tbody>
                                            <!-- Table row -->
                                            <?php
                                            $i = 1;
                                            $checksumVials = array();
                                            //fetch result
                                            while ($row = mysql_fetch_object($tempstocks)) {
                                                ?>
                                                <tr class="gradeX">
                                                    <td><?php echo date("d/m/y", strtotime($row->TranDate)); ?></td>
        <?php $_SESSION['trans_date'] = $row->TranDate; ?>
                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td><?php echo $row->UnitType; ?></td>
                                                    <td><?php echo $row->wh_name; ?></td>
                                                    <td class="editableSingle Qty id<?php echo $row->PkDetailID; ?> right"><?php echo number_format(abs($row->Qty)); ?></td>
                                                    <td class="Qty id<?php echo $row->PkDetailID; ?> right"><?php echo number_format((abs($row->Qty) / $row->qty_carton), 2); ?></td>
                                                    <td class="Batch id<?php echo $row->PkDetailID; ?>"><!--editableSingle --> 
        <?php echo $row->batch_no; ?></td>
                                                    <td><?php echo date("d/m/y", strtotime($row->batch_expiry)); ?></td>
                                                    <td class="center"><span data-toggle="notyfy" id="<?php echo $row->PkDetailID; ?>" data-type="confirm" data-layout="top"><img class="cursor" src="<?php echo PUBLIC_URL; ?>images/cross.gif" /></span></td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                            ?>
                                            <!-- // Table row END -->
                                        </tbody>
                                        <!-- // Table body END -->

                                    </table>
                                    <!-- // Table END --> 
                                </div>
                            </div>
                            <!-- Widget -->
                            <div class="widget-body right">
                                <form name="receive_stock" id="receive_stock" action="new_issue_action.php" method="POST">
                                    <button type="submit" class="btn btn-primary" onClick="return confirm('Are you sure you want to save the list?');"> Save </button>
                                    <button id="print_issue" type="button" class="btn btn-warning"> Print </button>
                                    <input type="hidden" name="stockid" id="stockid" value="<?php echo $stock_id; ?>"/>
                                    <input type="hidden" name="whTo" id="whTo" value="<?php echo $whTo; ?>"/>
                                </form>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (!empty($_SESSION['user_level']) && $_SESSION['user_level'] < 2 && $_SERVER['SERVER_ADDR'] != '::1') {
        ?>
        <div id="wms_alert" class="modal fade col-md-6 center-block" style="margin-top: 20px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <h3 id="myModalLabel"><b class="red">WMS Alert !</b></h3>
            </div>
            <div class="modal-body" style="background-color:white;">
                <pre>Dear WM User, 

                     <b class="red">Very Soon </b> “Central Warehouse Direct  Stock Issuance Form” 
                    shall be blocked. 

                    Central Warehouse shall be able to issue stock against CLR-6 in requisitions section.

                    We are encouraging all users to <b>start following the requisition cycle.</b>

                    For any queries , feel free to contact our support.
                </pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default red" data-dismiss="modal">Close</button>
            </div>
        </div>
        <?php
    }
    ?>
    <!-- // Content END -->
    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>

    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.validate.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/newissue.js"></script> 
    <script >
        $('#issue_date').datepicker({
		dateFormat: "dd/mm/yy",
		constrainInput: false,
		maxDate: 0,
                minDate: "<?=date('d/m/y',strtotime($_SESSION['im_start_month']))?>",
                changeMonth: true
	});
    </script>
    <?php
    if (!empty($js)) {
        ?>
        <script src="<?php echo PUBLIC_URL; ?>js/dataentry/<?php echo $js; ?>"></script>
        <?php
    }
    if (!empty($_SESSION['success'])) {
        if ($_SESSION['success'] == 1) {
            $text = 'Data has been saved successfully';
        }
        if ($_SESSION['success'] == 2) {
            $text = 'Data has been deleted successfully';
        }
        ?>
        <script>
                                        var self = $('[data-toggle="notyfy"]');
                                        notyfy({
                                            force: true,
                                            text: '<?php echo $text; ?>',
                                            type: 'success',
                                            layout: self.data('layout')
                                        });

        </script>
        <?php
        unset($_SESSION['success']);
    }
    ?>
    <script>
<?php
if ($_SESSION['user_role'] == 4) {
    echo "$('#wms_alert').modal('show');";
}
?>
    </script>
    <!-- // Content END -->

</div>
<!-- END FOOTER -->


</body>
<!-- END BODY -->
</html>