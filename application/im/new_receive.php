<?php
/**
 * new_receive
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
//transaction number
$TranNo = '';
//transaction ref
$TranRef = '';
//from id
$from_id = 0;
//product id
$productID = 0;
//unit price
$unit_price = 0;
//vvm type
$vvmtype = 0;
//vvm stage
$vvmstage = 0;
//stock id
$stock_id = 0;
//manufacturer
$manufacturer = '';
//get user id
$userid = $_SESSION['user_id'];
//user warehouse
$wh_id = $_SESSION['user_warehouse'];
//from warehouse
$wh_from = '';
//pk stock id
$PkStockID = '';
//check pk stock id
if (isset($_GET['PkStockID'])) {
    //get pk stock id
    $PkStockID = base64_decode($_GET['PkStockID']);
    $tempstocksIssue = $objStockMaster->GetTempStockRUpdate($PkStockID);
} else {
    //Get Temp Stock Receive
    $tempstocksIssue = $objStockMaster->GetTempStockReceive($userid, $wh_id, 1);
}
if (!empty($tempstocksIssue) && mysql_num_rows($tempstocksIssue) > 0) {
    //fetch result
    $result = mysql_fetch_object($tempstocksIssue);
    //stock id
    $stock_id = $result->PkStockID;
    //from id
    $from_id = $result->WHIDFrom;
    //from warehouse
    $wh_from = $objwarehouse->GetWHByWHId($from_id);
    //transaction date
    $TranDate = $result->TranDate;
    //transaction number
    $TranNo = $result->TranNo;
    //transaction ref
    $TranRef = $result->TranRef;
    //Get Last Insered Temp Stocks Receive List
    $tempstocksIssueDet = $objStockMaster->GetLastInseredTempStocksReceiveList($userid, $wh_id, 1);
    if (!empty($tempstocksIssueDet)) {
        //fetch result
        $result1 = mysql_fetch_object($tempstocksIssueDet);
        if (!empty($result1)) {
            //product id
            $productID = $result1->itm_id;
            //unit price
            $unit_price = $result1->unit_price;
            //manufacturer
            $manufacturer = $result1->manufacturer;
        }
    }
}
if (!empty($productID)) {
    
}
//Get Temp Stocks Receive List
$tempstocks = $objStockMaster->GetTempStocksReceiveList($userid, $wh_id, 1);
if (!empty($tempstocks) && mysql_num_rows($tempstocks) > 0) {
    
} else {
    $objStockMaster->PkStockID = $stock_id;
    $objStockMaster->delete();
}
//Get User Warehouses
$warehouses = $warehouses1 = $objwarehouse->GetUserWarehouses();
//Get All Manage Item
//$category = '1,4';
//if($_SESSION['user_stakeholder1'] == 145) $category='5';
//$items = $objManageItem->GetAllManageItem($category);
//
//
$stk = $_SESSION['user_stakeholder1'];
$items = $objManageItem->GetAllProduct_of_stk($stk);
//Get All Item Units
$units = $objItemUnits->GetAllItemUnits();
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
                                <h3 class="heading">Stock Receive (From Supplier)</h3>
                            </div>
                            <div class="widget-body">
                                <form method="POST" name="new_receive" id="new_receive" action="new_receive_action.php" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3"> 
                                                <!-- Group Receive No-->
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_no"> Receipt No </label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" tabindex="1" id="receive_no" value="<?php echo $TranNo; ?>" name="receive_no" type="text" readonly />
                                                        <input type="hidden"  id="source_name" name="source_name" value="<?php echo $wh_from; ?> " />
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- // Group END Receive No-->
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_ref"> Ref. No. <span class="red">*</span> </label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" required id="receive_ref" name="receive_ref" type="text" value="<?php echo $TranRef; ?>" <?php if (!empty($TranRef)) { ?>disabled="" <?php } ?>/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_date"> Receiving Time </label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" <?php
                                                        if (!empty($TranDate)) {
                                                            echo 'disabled=""';
                                                        } else {
                                                            echo 'readonly="readonly" style="background:#FFF"';
                                                        }
                                                        ?> id="receive_date" tabindex="2" name="receive_date" type="text" value="<?php echo (!empty($TranDate)) ? date("d/m/y h:i A", strtotime($TranDate)) : date("d/m/Y h:i A"); ?>" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">&nbsp;</div>
                                        </div>
                                    </div>
                                    <div class="row">                                        
                                        <div class="col-md-12">
                                        <div class="col-md-3">
                                            <label class="control-label">
                                                Physical Inspection <span class="red">*</span>
                                            </label>
                                            <div class="controls">
                                                
                                                <select name="physical_inspection" id="physical_inspection" required class="form-control">
                                                    <option value="" selected="selected">Select</option>
                                                    <option value="2">NA</option>
                                                    <option value="0">Inprocess</option>
                                                    <option value="1">Completed</option>
                                                </select>                                            
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label" for="dtl">
                                                DTL <span class="red">*</span>
                                            </label>
                                            <div class="controls">
                                                
                                                <select name="dtl" id="dtl" class="form-control" required >
                                                    <option value="" selected="selected">Select</option>
                                                    <option value="2">NA</option>
                                                    <option value="0">Inprocess</option>
                                                    <option value="1">Completed</option>
                                                </select>
                                            
                                            </div>
                                        </div>
                                        <div class="col-md-3 hide">
                                            <label class="control-label" for="distribution_plan">
                                                Distribution Plan                                            </label> <span class="red">*</span>
                                            <div class="controls">
                                                
                                                <select name="distribution_plan" id="distribution_plan" class="form-control" >
                                                    <option value="" selected="selected">Select</option>
                                                    <option value="2">NA</option>
                                                    <option value="0">Not Received</option>
                                                    <option value="1">Received</option>
                                                </select>                                            
                                            </div>
                                        </div>
<!--                                        <div class="col-md-3">
                                            <label class="control-label" for="purpose">
                                                Category <span class="red">*</span>
                                            </label>
                                            <div class="controls">
                                                
                                                <select name="activity_id" id="activity_id" class="form-control">
                                                    <option value="" selected="selected">Select</option>
                                                    <option value="1">TB Dots Section</option>
                                                    <option value="2">EPI &amp; Instrument Section</option>
                                                    <option value="3">WHO Section</option>
                                                    <option value="4">National Program</option>
                                                    <option value="6">DHQ &amp; THQ Program</option>
                                                </select>                                            
                                            </div>
                                        </div>-->
                                    </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label" for="receive_from"> Received From (Funding Source)<span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select name="receive_from" id="receive_from" required="true" class="form-control input-medium" <?php if (!empty($from_id) && !empty($TranNo)) { ?>disabled="" <?php } ?>>
                                                        <option value="">Select</option>
                                                        <?php
                                                        //check if result exists
                                                        if (mysql_num_rows($warehouses) > 0) {
                                                            //fetch result
                                                            while ($row = mysql_fetch_object($warehouses)) {
                                                                //populate receive_from combo
                                                                if($_SESSION['user_stakeholder']=='145'){
                                                                    if($row->wh_id != '33677' && $row->wh_id != '33678' && $row->wh_id != '33680' && $row->wh_id != '20641'  && $row->wh_id != '9079') 
                                                                        continue;
                                                                }
                                                                ?>
                                                                <option value="<?php echo $row->wh_id; ?>" <?php if ($from_id == $row->wh_id) { ?> selected="" <?php } ?>> <?php echo $row->wh_name; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <?php if (!empty($from_id) && !empty($TranNo)) { ?>
                                                        <input type="hidden" name="receive_from" id="receive_from" value="<?php echo $from_id; ?>" />
<?php } ?>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label" for="product"> Product <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select name="product" id="product" required="true" class="form-control input-medium">
                                                        <option value=""> Select </option>
                                                        <?php
                                                        //check if result exists
                                                        if (mysql_num_rows($items) > 0) {
                                                            //fetch results
                                                            while ($row = mysql_fetch_object($items)) {

                                                                $sel = '';
                                                                if ($productID == $row->itm_id) {
                                                                    $sel = '';
                                                                }
                                                                echo "<option value=" . $row->itm_id . " " . $sel . " >" . $row->itm_name . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                                    <div class="col-md-1" style="margin-top: 30px;" ><a class="btn btn-xs green " href="../admin/ManageItems_by_stk.php" target="_blank" ><i class="fa fa-plus"></i></a></div>
                                            
                                            
                                            
                                            
                                            <div class="col-md-5">
                                                <div class="col-md-6">
                                                    <label class="control-label" for="manufacturer"> Manufacturer <span class="red">*</span> </label>
                                                    <div class="controls">
                                                        <select name="manufacturer" id="manufacturer" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" style="margin-top: 30px; "> <a class="btn btn-primary alignvmiddle" style="display:none;" id="add_m_p"  onclick="javascript:void(0);" data-toggle="modal"  href="#modal-manufacturer">Add</a> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label" for="batch"> Batch No <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <input class="form-control input-medium" id="batch" name="batch" type="text" required />
                                                </div>
                                            </div>
                                            <!-- div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="prod_date">
                                                        Production Date
                                                    </label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" id="prod_date" name="prod_date" type="text" value="<?php echo (!empty($prod_date)) ? $prod_date : ''; ?>" />
                                                    </div>
                                                </div>
                                            </div -->
                                            <div class="col-md-3" id="expiry_div">
                                                <label class="control-label" for="expiry_date"> Expiry date <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control input-medium" name="expiry_date" id="expiry_date" readonly required style="background:#FFF;"/>
                                                </div>
                                            </div>
                                            <!-- div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="unit_price">
                                                        Unit Price (US$)
                                                    </label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" id="unit_price" name="unit_price" type="text" value="<?php echo number_format($unit_price, 2); ?>"/>
                                                    </div>
                                                </div>
                                            </div --> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label" for="qty"> Quantity <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control input-medium num" name="qty" id="qty" autocomplete="off" />
                                                    <span id="product-unit"> </span> <span id="product-unit1" style="display:none;"> </span> </div>
                                            </div>
                                            <div class="col-md-9">
                                                <label class="control-label" for="firstname"> &nbsp; </label>
                                                <div class="controls right">
                                                    <button type="submit" class="btn btn-primary" id="add_receive"> Save Entry </button>
                                                    <button type="reset" class="btn btn-info" id="reset"> Reset </button>
                                                    <input type="hidden" name="trans_no" id="trans_no" value="<?php echo $TranNo; ?>" />
                                                    <input type="hidden" name="stock_id" id="stock_id" value="<?php echo $stock_id; ?>" />

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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
                                        <form name="addnew" id="addnew" action="add_action_manufacturer.php" method="POST">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-6">
                                                        <label class="control-label">Manufacturer<span class="red">*</span></label>
                                                        <div class="controls">
                                                            <input required class="form-control input-medium" type="text" id="new_manufacturer" name="new_manufacturer" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="control-label">Brand Name<span class="red">*</span></label>
                                                        <div class="controls">
                                                            <input required class="form-control input-medium" type="text" id="brand_name" name="brand_name" value=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <div class="controls">
                                                            <h4 style="padding-top:30px;">Carton Dimension</h4>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">Length(cm)</label>
                                                        <div class="controls">
                                                            <input class="form-control input-sm dimensions positive_number" type="text" id="pack_length" name="pack_length" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">Width(cm)</label>
                                                        <div class="controls">
                                                            <input class="form-control input-sm dimensions positive_number" type="text" id="pack_width" name="pack_width" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">Height(cm)</label>
                                                        <div class="controls">
                                                            <input class="form-control input-sm dimensions positive_number" type="text" id="pack_height" name="pack_height" value=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <label class="control-label">Net Capacity</label>
                                                        <div class="controls">
                                                            <input required class="form-control input-sm positive_number" type="text"  id="net_capacity" name="net_capacity" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">Cartons / Pallet<span class="red">*</span></label>
                                                        <div class="controls">
                                                            <input required class="form-control input-sm positive_number" type="text" id="carton_per_pallet" name="carton_per_pallet" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">Quantity/Pack<span class="red">*</span></label>
                                                        <div class="controls">
                                                            <input required class="form-control input-sm positive_number" type="text" id="quantity_per_pack" name="quantity_per_pack" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">GTIN</label>
                                                        <div class="controls">
                                                            <input required class="form-control input-sm" type="text" id="gtin" name="gtin" value=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <label class="control-label">Gross :</label> 
                                                        <div class="controls"><input class="form-control input-sm " type="text" readonly id="gross_capacity" name="gross_capacity" ></div>

                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="add_manufacturer" name="add_manufacturer" value="1"/>
                                        </form>
                                    </div>
                                    <!-- // Modal body END --> 

                                    <!-- Modal footer -->
                                    <div class="modal-footer"> <a data-dismiss="modal" class="btn btn-default" href="#">Close</a> <a class="btn btn-primary" id="save_manufacturer" data-dismiss="modal" href="#">Save changes</a> </div>
                                    <!-- // Modal footer END --> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- // Row END -->
<?php if (!empty($tempstocks) && mysql_num_rows($tempstocks) > 0) { ?>
                    <!--  -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Receive List</h3>
                                </div>
                                <div class="widget-body" id="gridData">
                                    <table class="table table-striped table-bordered table-condensed" id="myTable">
                                        <!-- Table heading -->
                                        <thead>
                                            <tr bgcolor="#009C00" style="color:#FFF;">
                                                <th> Receiving Time </th>
                                                <th> Product </th>
                                                <th> Manufacturer </th>
                                                <th> Unit </th>
                                                <th> Receive From </th>
                                                <th class="span2"> Quantity </th>
                                                <th> Cartons </th>
                                                <th class="span2"> Batch </th>
                                                <th nowrap> Expiry Date </th>
                                                <th width="50"> Action </th>
                                            </tr>
                                        </thead>
                                        <!-- // Table heading END --> 

                                        <!-- Table body -->
                                        <tbody>
                                            <!-- Table row -->
                                            <?php
                                            $i = 1;
                                            $checksumVials = array();
                                            $checksumDoses = array();
                                            //fetch result
                                            while ($row = mysql_fetch_object($tempstocks)) {
                                                // Checksum
                                                ?>
                                                <tr class="gradeX">
                                                    <td nowrap><?php echo date("d/m/y h:i A", strtotime($row->TranDate)); ?></td>
                                                    <td><?php echo $row->itm_name; ?></td>
                                                    <td>
                                                        <?php
                                                        if (!empty($row->manufacturer)) {
                                                            $getManufacturer = mysql_query("SELECT
																					CONCAT(stakeholder.stkname, ' | ', stakeholder_item.brand_name) AS stkname
																				FROM
																					stakeholder_item
																				INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
																				WHERE
																			stakeholder_item.stk_id = $row->manufacturer") or die("err  manufacturer");
                                                            $manufacturerRow = mysql_fetch_assoc($getManufacturer);
                                                            echo $manufacturerRow['stkname'];
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo $row->UnitType; ?></td>
                                                    <td><?php echo $row->wh_name; ?></td>
                                                    <td class="right editableSingle Qty id<?php echo $row->PkDetailID; ?>"><?php echo number_format(abs($row->Qty)); ?></td>
                                                    <td class="right"><?php echo number_format(abs($row->Qty) / $row->qty_carton); ?></td>
                                                    <td class="editableSingle Batch id<?php echo $row->PkDetailID; ?>"><?php echo $row->batch_no; ?></td>
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
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:10px;">
                        <div class="-body right">
                            <form name="receive_stock" id="receive_stock" action="new_receive_action.php" method="POST">
                                <button  type="submit" class="btn btn-primary" onClick="return confirm('Are you sure you want to save the form?');"> Save </button>
                                <button id="print_vaccine_placement" type="button" class="btn btn-warning"> Print </button>
                                <input type="hidden" name="stockid" id="stockid" value="<?php echo $stock_id; ?>" />
                            </form>
                        </div>
                    </div>
                <?php }
                ?>
            </div>
        </div>
        <!-- // Content END --> 

    </div>
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>

    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/newreceive.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit.js"></script>
    <?php
    if (!empty($_SESSION['success'])) {
        if ($_SESSION['success'] == 1) {
            //display message
            $text = 'Data has been saved successfully';
        }
        if ($_SESSION['success'] == 2) {
            //display message
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
    <!-- END FOOTER --> 

    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>