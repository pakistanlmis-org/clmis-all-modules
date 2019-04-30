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
if(!empty($_SESSION['user_warehouse']))
    $wh_id = $_SESSION['user_warehouse'];
else
    $wh_id  = 123;
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
$join1=$where1="";
if(isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2)
{
    $join1.= " INNER JOIN funding_stk_prov ON tbl_warehouse.wh_id = funding_stk_prov.funding_source_id ";

    if(isset($_SESSION['user_province1']))
    {
        $where1 .= " AND funding_stk_prov.province_id = ".$_SESSION['user_province1']." ";
    }
    if(isset($_SESSION['user_stakeholder1']))
    {
        $where1 .= " AND funding_stk_prov.stakeholder_id = ".$_SESSION['user_stakeholder1']." ";
    }
}
else
{
    //$where1 .= " AND tbl_stock_master.WHIDTo = $wh_id ";
    
}

//query copied from clsswharehouse
//$strSql = "SELECT
//                    tbl_warehouse.wh_name,
//                    tbl_warehouse.wh_id,
//                    funding_stk_prov.province_id
//            FROM
//            tbl_warehouse
//            INNER JOIN tbl_stock_master ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
//            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
//            LEFT JOIN funding_stk_prov ON tbl_warehouse.wh_id = funding_stk_prov.funding_source_id
//            $join1
//            WHERE
//            tbl_stock_master.TranTypeID = 1
//            $where1
//            GROUP BY tbl_warehouse.wh_name
//            ORDER BY
//            stakeholder.stkorder ASC";
//            

//query copied from clsswharehouse
$strSql = "SELECT
                    tbl_warehouse.wh_name,
                    tbl_warehouse.wh_id,
                    funding_stk_prov.province_id
            FROM
            tbl_warehouse
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            LEFT JOIN funding_stk_prov ON tbl_warehouse.wh_id = funding_stk_prov.funding_source_id
            $join1
            WHERE
            stakeholder.stk_type_id = 2
            $where1
            GROUP BY tbl_warehouse.wh_name
            ORDER BY
            stakeholder.stkorder ASC";

//echo $strSql;
$warehouses = mysql_query($strSql) or die("Error Getwh");
//Get Procured By

if(isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2)
{
    $objloc->LocLvl=2;
    $procured_by = $objloc->GetLocationsById($_SESSION['user_province1']);
}
else
{
    $procured_by = $objwarehouse->GetProvincialLocations();
}
//Get All Manage Item
$items = $objManageItem->GetAllManageItem();
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
                                <h3 class="heading">Add Pipeline Shipments</h3>
                            </div>
                            <div class="widget-body">
                                <form method="POST" name="new_receive" id="new_receive" action="add-shipments-action.php" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_date"> Expected Arrival Date </label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium"  id="receive_date" tabindex="2" name="receive_date" type="text" value="<?php echo (!empty($TranDate)) ? date("d/m/y h:i A", strtotime($TranDate)) : date("d/m/Y h:i A"); ?>" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label" for="refrence_number"> Reference Number<span class="red">*</span> (Max:150 chars)</label>
                                                <div class="controls">
                                                    <input class="form-control input-medium" id="refrence_number" name="refrence_number" maxlength="150" type="text" required />
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label" for="receive_from">Funding Source<span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select name="receive_from" id="receive_from" required="true" class="form-control input-medium" >
                                                        <option value="">Select</option>
                                                        <?php
                                                        //check if result exists
                                                        if (mysql_num_rows($warehouses) > 0) {
                                                            //fetch result
                                                            while ($row = mysql_fetch_object($warehouses)) {
                                                                //populate receive_from combo
                                                                ?>
                                                                <option value="<?php echo $row->wh_id; ?>" <?php if ($from_id == $row->wh_id) { ?> selected="" <?php } ?> prov_id="<?=(isset($row->province_id)?$row->province_id:'10')?>" > <?php echo $row->wh_name; ?> </option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
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


                                            <div class="col-md-3">
                                                <label class="control-label" for="manufacturer"> Manufacturer <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select name="manufacturer" id="manufacturer" class="form-control input-medium">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-1" style="margin-top: 30px; "> <a class="btn btn-primary alignvmiddle" style="display:none;" id="add_m_p"  onclick="javascript:void(0);" data-toggle="modal"  href="#modal-manufacturer">Add</a> </div>





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
                                            <div class="col-md-3">
                                                <label class="control-label" for="procured_by"> Procured For <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select name="procured_by" id="procured_by" required="true" class="form-control input-medium">
                                                        <option value=""> Select </option>
                                                        <?php
                                                        //check if result exists
                                                        if (mysql_num_rows($procured_by) > 0) {
                                                            //fetch results
                                                            while ($row = mysql_fetch_object($procured_by)) {

                                                                $sel = '';
                                                                if ($productID == $row->PkLocID) {
                                                                    $sel = '';
                                                                }
                                                                echo "<option value=" . $row->PkLocID . " " . $sel . " >" . $row->LocName . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-md-3">
                                                <label class="control-label" for="product"> Status <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select name="status" id="status" required="true" class="form-control input-medium">
                                                        <option value=""> Select </option>
                                                        <option value="Pre Shipment"> Pre Shipment </option>
                                                        <option value="Tender"> Tender </option>
                                                        <option value="PO"> PO </option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">

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

                    </div>
                </div>
                <!-- // Row END -->
                <div id="modal-manufacturer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content"> 
                            <!-- Modal heading -->
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">Ã—</button>
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
                                                    <h4 style="padding-top:30px;">Carton Dimensions</h4>
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
                                                    <input class="form-control input-sm dimensions positive_number" type="text"  id="pack_width" name="pack_width" value=""/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Height(cm)</label>
                                                <div class="controls">
                                                    <input class="form-control input-sm dimensions positive_number" type="text"  id="pack_height" name="pack_height" value=""/>
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
        <!-- // Content END --> 

    </div>
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/add-shipment.js"></script>
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