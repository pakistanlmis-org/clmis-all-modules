<?php
/**
 * add_adjustment
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
include(PUBLIC_PATH . "html/header.php");
//Initializing variables
$title = "New Issue";
$TranRef = '';
//Get All WH Product
//$category = '1,4';
//if($_SESSION['user_stakeholder1'] == 145) $category='5';
//$items = $objManageItem->GetAllManageItem($category);
//

$stk = $_SESSION['user_stakeholder1'];
$items = $objManageItem->GetAllProduct_of_stk($stk);
//Get Adjusment Types
$types = $objTransType->getAdjusmentTypes();
?>
<link rel="stylesheet" type="text/css" href="<?php echo PUBLIC_URL;?>assets/global/plugins/select2/select2.css"/>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
<!-- BEGIN HEADER -->
<div class="page-container">
<?php
        //Including top file
        include PUBLIC_PATH . "html/top.php";
        //Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>
<div class="page-content-wrapper">
<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <form method="POST" name="batch_search" id="batch_search" action="add_adjustment_action.php" >
                <div class="widget" data-toggle="collapse-widget">
                    <div class="widget-head">
                        <h3 class="heading">New Adjustment</h3>
                    </div>
                    <!-- // Widget Heading END -->
                    
                    <div class="widget-body"> 
                        <!-- Row -->
                        <div class="row">
                            <div class="col-md-12">&nbsp;</div>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="control-group">
                                        <label for="firstname"> Adjustment Date  <span class="red">*</span> </label>
                                        <div class="controls">
                                            <?php
                                            if($_SESSION['user_level']>=3)
                                            {
                                             ?>
                                              <input class="form-control input-medium" id="adjustment_date1" readonly name="adjustment_date" type="text" value="<?php echo date("d/m/Y"); ?>" required />
                                            <?php
                                            }
                                            else
                                            {
                                             ?>
                                              <input class="form-control input-medium" id="adjustment_date" name="adjustment_date" type="text" value="<?php echo date("d/m/Y"); ?>" required />
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="control-group">
                                        <label class="control-label" for="firstname"> Adjustment Type <span class="red">*</span> </label>
                                        <div class="controls">
                                            <select name="types" id="types" class="form-control input-medium" required="true">
                                                <option value="">Select</option>
                                                <?php
                                                
                                                    $tranNature = array();
                                                    foreach ($types as $type) {
                                                        if($_SESSION['user_province1']==1 && $_SESSION['user_level']==3)
                                                        {    
                                                            //if($type->trans_id != 16)
                                                                //continue;
                                                        }
                                                        if ($type->trans_nature == '-') {
                                                            $tranNature[] = $type->trans_id;
                                                        }
                                                        //Populate types combo
                                                        echo "<option value=" . $type->trans_id . ">" . $type->trans_type . "</option>";
                                                    }
                                                    ?>
                                            </select>
                                            <input type="hidden" id="negTransType" value="<?php echo implode(',', $tranNature); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="control-group">
                                        <label for="firstname"> Ref. No. </label>
                                        <div class="controls">
                                            <input class="form-control input-medium" id="ref_no" name="ref_no" type="text" value="" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Group Receive Date-->
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="control-group">
                                        <label class="control-label"> Product <span class="red">*</span> </label>
                                        <div class="controls">
                                            <select name="product" id="product" class="form-control input-medium" required="true">
                                                <option value="">Select</option>
                                                <?php
                                                            //Populate product combo
                                                            while ($item = mysql_fetch_array($items)) {
                                                                echo "<option value=" . $item['itm_id'] . ">" . $item['itm_name'] . "</option>";
                                                            }
                                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="control-group">
                                        <label class="control-label" for="batch_no"> Batch No <span class="red">*</span> </label>
                                        <div class="controls">
                                            <select name="batch_no" id="batch_no" class="input-medium select2me" required="true" data-placeholder="Select">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="control-group">
                                        <label class="control-label" >&nbsp;</label>
                                        <div class="controls"> <a class="btn btn-primary alignvmiddle" style="display:none;" id="add_m_p"  onclick="javascript:void(0);
                                                            document.getElementById('available_div').style.display = 'none';
                                                            document.getElementById('batch_no').value = ''" data-toggle="modal"  href="#modal-manufacturer">Add New Batch</a> </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="control-group">
                                        <label class="control-label" for="firstname"> Quantity <span class="red">*</span> </label>
                                        <div class="controls">
                                            <input class="form-control input-medium" id="quantity" name="quantity" type="text" value="<?php echo $TranRef; ?>" required style="text-align:right" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" id="available_div">
                                    <div class="control-group">
                                        <label class="control-label" for="available"> Available </label>
                                        <div class="controls"> <span id="itembatches">
                                            <input class="form-control input-medium num" id="available" name="available" type="text" value="<?php echo $TranRef; ?>" disabled="" style="display:inline !important"/>
                                            </span> <span id="product-unit">Unit</span> </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="control-group">
                                        <label class="control-label" for="firstname"> Comment </label>
                                        <div class="controls">
                                            <textarea name="comments" id="comments" class="form-control input-medium input-medium"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9 add-adjustment-btn right">
                                    <label class="control-label" for="firstname">&nbsp;</label>
                                    <div class="controls">
                                        <button type="submit" class="btn btn-primary" id="add_adjustment">Save</button>
                                        <button type="reset" class="btn btn-info" id="reset">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div id="modal-manufacturer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content"> 
                        <!-- Modal heading -->
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                            <div id="pro_loc"></div>
                        </div>
                        <!-- // Modal heading END --> 
                        
                        <!-- Modal body -->
                        <div class="modal-body">
                            <form name="addnew" id="addnew" action="#" method="POST">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <label class="control-label">Batch No<span class="red">*</span></label>
                                            <div class="controls">
                                                <input required class="form-control input" type="text" id="batch" name="batch" value="" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Expiry date<span class="red">*</span></label>
                                            <div class="controls">
                                                <input required class="form-control input" type="text" id="expiry_date" name="expiry_date" value="" readonly />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Funding Source<span class="red">*</span></label>
                                            <div class="controls">
                                                <select class="form-control input" id="receive_from" name="receive_from" required>
                                                    <option value="">Select</option>
                                                    <?php
                                                                //Get User Warehouses
                                                                $warehouses = $warehouses1 = $objwarehouse->GetUserWarehouses();
                                                                if (mysql_num_rows($warehouses) > 0) {
                                                                    while ($row = mysql_fetch_object($warehouses)) {
                                                                        ?>
                                                    <?php //populate receive_from Combo?>
                                                    <option value="<?php echo $row->wh_id; ?>"> <?php echo $row->wh_name; ?> </option>
                                                    <?php
    }
}
?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Manufacturer<span class="red">*</span></label>
                                            <div class="controls">
                                                <select name="manufacturer" id="manufacturer" class="form-control input-medium" required>
                                                    <option value="">Select</option>
                                                    <?php
                                                    $manufacturer_product = $objstk->GetAllManufacturers();
                                                                if (mysql_num_rows($manufacturer_product) > 0) {

                                                                    while ($row = mysql_fetch_object($manufacturer_product)) {
                                                                        $sel = '';
                                                                        if ($manufacturer == $row->stkid) {
                                                                            $sel = 'selected=""';
                                                                        }
                                                                        //Populate manufacturer combo
                                                                        echo "<option value=" . $row->stkid . " " . $sel . " >" . $row->stkname . "</option>";
                                                                    }
                                                                }
                                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="add_batch" name="add_batch" value="1" />
                            </form>
                        </div>
                        <!-- // Modal body END --> 
                        
                        <!-- Modal footer -->
                        <div class="modal-footer"> <a data-dismiss="modal" class="btn btn-default" href="#">Close</a> <a class="btn btn-primary" id="save_batch" data-dismiss="modal" href="#">Add As New Batch</a> </div>
                        <!-- // Modal footer END --> 
                    </div>
                </div>
            </div>
            <!-- // Content END --> 
        </div>
    </div>
</div>
<?php include PUBLIC_PATH . "/html/footer.php"; ?>
<script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script> 
<script src="<?php echo PUBLIC_URL; ?>js/jquery.validate.js"></script> 
<script src="<?php echo PUBLIC_URL; ?>js/dataentry/add_adjustment.js"></script>
<script type="text/javascript" src="<?php echo PUBLIC_URL;?>assets/global/plugins/select2/select2.min.js"></script>
<?php
$_SESSION['stockIssueArray'] = $stockArray;
if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
	?>
<script>
		var self = $('[data-toggle="notyfy"]');
		notyfy({
			force: true,
			text: 'Data has been saved successfully!',
			type: 'success',
			layout: self.data('layout')
		});
</script>
<?php
	unset($_SESSION['success']);
}
?>
<!-- END FOOTER --> 
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>