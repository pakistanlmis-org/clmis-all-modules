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
$already_rcvd = 0;



        
$strSql = " SELECT
            tbl_stock_master.shipment_id,
            sum(tbl_stock_detail.Qty) as qty
            FROM
            tbl_stock_master
            INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
            WHERE
            tbl_stock_master.shipment_id =  ".$_REQUEST['shipment_id'];
$ress= mysql_query($strSql);
$shipment_data  = mysql_fetch_assoc($ress);
    
if(isset($shipment_data['qty']))
    $already_rcvd = $shipment_data['qty'] ;

if (isset($_REQUEST['shipment_id'])) {
    //get pk stock id
    $shipment_id = $_REQUEST['shipment_id'];
    $shipment_detail = $objshipments->get_shipment_by_id($shipment_id);
    
    $TranRef        = $shipment_detail->reference_number;
    $from_id        = $shipment_detail->stk_id;
    $productID      = $shipment_detail->item_id;
    $manufacturer   = $shipment_detail->manufacturer;
    $shipment_qty   = $shipment_detail->shipment_quantity;
} 
//  echo '<pre>';print_r($shipment_detail);exit;

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
            //$manufacturer = $result1->manufacturer;
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
$items = $objManageItem->GetAllManageItem();
//Get All Item Units
$units = $objItemUnits->GetAllItemUnits();

$manufacturers_list  = mysql_query("
    SELECT
            stakeholder_item.stk_id as stk_itm_id,
            CONCAT(stakeholder.stkname, ' | ' ,IFNULL(stakeholder_item.brand_name, '')) AS manufacturer_name
    FROM
            stakeholder
    INNER JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
    WHERE
            stakeholder.stk_type_id = 3
    ORDER BY
            stakeholder.stkname ASC");
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
                                <h3 class="heading">Receive Shipment</h3>
                            </div>
                            <div id="w_body" class="widget-body ">
                                <form method="POST" name="new_receive" id="new_receive" action="receive_shipment_action.php" >
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
                                                        <input readonly="readonly" class="form-control input-medium" required id="receive_ref" name="receive_ref" type="text" value="<?php echo $TranRef; ?>" />
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
                                                <label class="control-label" for="receive_from"> Received From (Funding Source)<span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select readonly name="receive_from" id="receive_from" required="true" class="form-control input-medium" <?php if (!empty($from_id) && !empty($TranNo)) { ?>disabled="" <?php } ?>>
                                                        
                                                        <?php
                                                        //check if result exists
                                                        if (mysql_num_rows($warehouses) > 0) {
                                                            //fetch result
                                                            while ($row = mysql_fetch_object($warehouses)) {
                                                                //populate receive_from combo
                                                                if ($from_id == $row->wh_id)
                                                                {
                                                                
                                                                    echo '<option value="'.$row->wh_id.'" selected >'.$row->wh_name.'</option>';
                                                                
                                                                }
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
                                                    <select readonly name="product" id="product" required="true" class="form-control input-medium">
                                                       
                                                        <?php
                                                        //check if result exists
                                                        if (mysql_num_rows($items) > 0) {
                                                            //fetch results
                                                            while ($row = mysql_fetch_object($items)) {

                                                                $sel = '';
                                                                if ($productID == $row->itm_id) {
                                                                    $prod_unit = $row->itm_type;
                                                                    $prod_unit_id = $row->item_unit_id;
                                                                    $sel = ' selected ';
                                                                    echo "<option value=" . $row->itm_id . " " . $sel . " >" . $row->itm_name . "</option>";
                                                                }
                                                                
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="col-md-6">
                                                    <label class="control-label" for="manufacturer"> Manufacturer <span class="red">*</span> </label>
                                                    <div class="controls">
                                                        <select readonly name="manufacturer" id="manufacturer" class="form-control input-medium">
                                                           
                                                            <?php
                                                                //check if result exists
                                                                if (mysql_num_rows($manufacturers_list) > 0) {
                                                                    //fetch results
                                                                    while ($row = mysql_fetch_object($manufacturers_list)) {

                                                                        $sel = '';
                                                                        if ($manufacturer == $row->stk_itm_id) {
                                                                            $sel = ' selected ';
                                                                             echo "<option value=" . $row->stk_itm_id . " " . $sel . " >" . $row->manufacturer_name . "</option>";
                                                                        }
                                                                       
                                                                    }
                                                                }
                                                                ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
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
                                            
                                            <div class="col-md-3" id="expiry_div">
                                                <label class="control-label" for="expiry_date"> Expiry date <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <input type="text" class="form-control input-medium readonly_cls" name="expiry_date" id="expiry_date"  required style="background:#FFF;"/>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label" for="qty"> Quantity (<?=$prod_unit?>)<span class="red">*</span> </label>
                                                <div class="controls">
                                                    <input type="number" required min="1" max="<?=(int)($shipment_qty-$already_rcvd)?>" class="form-control input-medium num" name="qty" id="qty" autocomplete="off" />
                                                    <span id="product-unit"> </span> <span id="product-unit1" style="display:none;"> </span> </div>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="control-label" for="qty"><mark>Total quantity of shipment : <?=$shipment_qty.' '.$prod_unit?></mark></label>
                                                <label class="control-label" for="qty"><mark>Already in receiving: <?=$already_rcvd.' '.$prod_unit?></mark></label>
                                                <label class="control-label" for="qty"><mark>Remaining: <?=(int)($shipment_qty-$already_rcvd).' '.$prod_unit?></mark></label>
                                        
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <label class="control-label" for="firstname"> &nbsp; </label>
                                                <div class="controls right">
                                                    <button  type="submit" class="btn btn-primary <?=(($shipment_qty-$already_rcvd)==0)?' hide ':''?>" id="add_receive"> Save Entry </button>
                                                    <button type="reset" class="btn btn-info hide" id="reset"> Reset </button>
                                                    <input type="hidden" name="trans_no" id="trans_no" value="<?php echo $TranNo; ?>" />
                                                    <input type="hidden" name="stock_id" id="stock_id" value="<?php echo $stock_id; ?>" />
                                                    <input type="hidden" name="shipment_id" id="shipment_id" value="<?php echo $shipment_id; ?>" />
                                                    <input type="hidden" name="unit" id="unit" value="<?php echo $prod_unit_id; ?>" />

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
                                                    <td class="center"><span data-toggle="notyfy" shipment_id="<?=$_REQUEST['shipment_id']?>" id="<?php echo $row->PkDetailID; ?>" data-type="confirm" data-layout="top"><img class="cursor" src="<?php echo PUBLIC_URL; ?>images/cross.gif" /></span></td>
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
                            <form name="receive_stock" id="receive_stock" action="receive_shipment_action.php" method="POST">
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


    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/shipment_receive.js"></script> 
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
<script>
            
            $( document ).ready(function() {

                 $("#expiry_date").datepicker({
                        minDate: 0,
                        maxDate: "+10Y",
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true
                    });
                    
                    <?php
                        if(($shipment_qty-$already_rcvd)==0)
                            echo "$('#w_body').collapse();"
                    ?>
                            
                 $(".readonly_cls").keydown(function(e){
                    e.preventDefault();
                });            
            });
        </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>