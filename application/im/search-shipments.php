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

if(!empty($_SESSION['user_warehouse']))
    $wh_id = $_SESSION['user_warehouse'];
else
    $wh_id  = 123;

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
$procured_by = '';
//echo '<pre>';print_r($_SESSION);exit;
//check if submitted
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
//check search by
    if (!empty($_REQUEST['status']) && !empty($_REQUEST['status'])) {
        //get search by
        $searchby = $_REQUEST['status'];
        $sCriteria['status'] = $searchby;
        $objshipments->status = $searchby;
    }
    //check warehouse
    if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
        //get warehouse
        $warehouse = $_REQUEST['warehouse'];
        $sCriteria['warehouse'] = $warehouse;
        //set from warehouse
        $objshipments->WHID = $warehouse;
    }
    //check product
    if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
        //get product
        $product = $_REQUEST['product'];
        $sCriteria['product'] = $product;
        //set product
        $objshipments->item_id = $product;
    }
    //check manufacturer
    if (isset($_REQUEST['manufacturer']) && !empty($_REQUEST['manufacturer'])) {
        //get manufacturer
        $manufacturer = $_REQUEST['manufacturer'];
        //set manufacturer	
        $objshipments->manufacturer = $manufacturer;
    }
    //check procured by
    if (isset($_REQUEST['procured_by']) && !empty($_REQUEST['procured_by'])) {
        //get manufacturer
        $procured_by = $_REQUEST['procured_by'];
        //set manufacturer	
        $objshipments->procured_by = $procured_by;
        $sCriteria['procured_by'] = $procured_by;
    }
    //check date from
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from'])) {
        //get date from
        $date_from = $_REQUEST['date_from'];
        $dateArr = explode('/', $date_from);
        $sCriteria['date_from'] = dateToDbFormat($date_from);
        //set date from	
        $objshipments->fromDate = dateToDbFormat($date_from);
    }
    //check to date
    if (isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
        //get to date
        $date_to = $_REQUEST['date_to'];
        $dateArr = explode('/', $date_to);
        $sCriteria['date_to'] = dateToDbFormat($date_to);
        //set to date	
        $objshipments->toDate = dateToDbFormat($date_to);
    }
    $_SESSION['sCriteria'] = $sCriteria;
} else {
    //date from
    $date_from = date('01' . '/m/Y');
    //date to
    $date_to = date('t/12/Y');
    //set from date
    $objshipments->fromDate = dateToDbFormat($date_from);
    //set to date
    $objshipments->toDate = dateToDbFormat($date_to);

    $sCriteria['date_from'] = dateToDbFormat($date_from);
    $sCriteria['date_to'] = dateToDbFormat($date_to);
    ;
    $_SESSION['sCriteria'] = $sCriteria;
}

//Stock Search
$gp_by = " GROUP BY shipments.pk_id  ";
$result = $objshipments->ShipmentSearch(1, $wh_id,$gp_by);
//title
$title = "Shipments";
//Get User Receive From WH
$join1=$where1="";
if(isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2) {
    $join1.= " INNER JOIN funding_stk_prov ON tbl_warehouse.wh_id = funding_stk_prov.funding_source_id ";
    if(isset($_SESSION['user_province1'])) {
        $where1 .= " AND funding_stk_prov.province_id = ".$_SESSION['user_province1']." ";
    }
    if(isset($_SESSION['user_stakeholder1'])) {
        $where1 .= " AND funding_stk_prov.stakeholder_id = ".$_SESSION['user_stakeholder1']." ";
    }
} else {
    $where1 .= " AND tbl_stock_master.WHIDTo = $wh_id ";
}
//query copied from clsswharehouse
$strSql = "SELECT
                    tbl_warehouse.wh_name,
                    tbl_warehouse.wh_id
            FROM
            tbl_warehouse
            INNER JOIN tbl_stock_master ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            $join1
            WHERE
            tbl_stock_master.TranTypeID = 1
            $where1
            GROUP BY tbl_warehouse.wh_name
            ORDER BY
            stakeholder.stkorder ASC";
//echo $strSql;
$warehouses = mysql_query($strSql) or die("Error Getwh");



$strSql2 = "
        SELECT
            tbl_stock_detail.Qty,
            stock_batch.batch_no,
            itminfo_tab.itm_name,
            tbl_stock_master.PkStockID,
            tbl_stock_master.TranNo,tbl_stock_master.shipment_id,
            shipments.pk_id,
            shipments.shipment_date,
            shipments.shipment_quantity,
            shipments.reference_number,
            sum(tbl_stock_detail.Qty) as received_qty,     
            tbl_warehouse.wh_name as stkname,
            shipments.`status`,
            itminfo_tab.qty_carton,
            tbl_itemunits.UnitType,
            tbl_locations.LocName as procured_by
        FROM
            tbl_stock_master
             INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
             LEFT JOIN stakeholder ON tbl_stock_detail.manufacturer = stakeholder.stkid
             INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
             INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
             LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
             INNER JOIN shipments ON tbl_stock_master.shipment_id = shipments.pk_id
             INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
             INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
        WHERE
                tbl_stock_master.temp = 1 AND
                tbl_stock_master.WHIDTo = '" . $wh_id . "' AND
                tbl_stock_master.CreatedBy = " . $_SESSION['user_id'] . " AND 
                tbl_stock_master.TranTypeID = 1
                AND tbl_stock_master.shipment_id is not null
        ";

    $rsSql21 = mysql_query($strSql2);
      
    $temp_voucher_exists = false;
     $temp_stock = array();
    if (mysql_num_rows($rsSql21) > 0) {
       
        while($row = mysql_fetch_assoc($rsSql21))
        {
            if(!empty($row['shipment_id']))
            {
                $temp_stock[$row['shipment_id']] = $row;
                $temp_voucher_exists = true;
            }
        }
    } 
//    echo '<pre>';
//print_r($temp_stock);exit;


//get all item
$items = $objManageItem->GetAllManageItem();
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
<?php
$str_do = isset($_REQUEST['DO'])?$_REQUEST['DO']:'';
if(isset($_REQUEST['DO']))
{
    $strDo = $_REQUEST['DO'];
if($_REQUEST['DO']=='Edit')
{
    
    $shipment_id = $_REQUEST['shipment_id'];
    $shipment_detail = $objshipments->get_shipment_by_id($shipment_id);
    
    $shipment_date = $shipment_detail->shipment_date;
    $ref_no = $shipment_detail->reference_number;
    $funding_source = $shipment_detail->stk_id;
    $itm_id = $shipment_detail->item_id;
    $manufacturer = $shipment_detail->manufacturer;
    $qty = $shipment_detail->shipment_quantity;
    $procured_by = $shipment_detail->procured_by;
    $status = $shipment_detail->status;
    ?>
                
                 <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?=$strDo?> Pipeline Shipments</h3>
                            </div>
                            <div class="widget-body">
                                <form method="POST" name="new_receive" id="new_receive" action="search_shipments_action.php" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_date"> Expected Arrival Date </label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium"  id="receive_date" tabindex="2" name="receive_date" type="text" value="<?php echo (!empty($shipment_date)) ? $shipment_date :''?>" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label" for="refrence_number"> Reference Number<span class="red">*</span> </label>
                                                <div class="controls">
                                                    <input value="<?=$ref_no?>" class="form-control input-medium" id="refrence_number" name="refrence_number" type="text" required />
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
                                                                <option value="<?php echo $row->wh_id; ?>" <?php if ($funding_source == $row->wh_id) { ?> selected="" <?php } ?>> <?php echo $row->wh_name; ?> </option>
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

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <label class="control-label" for="qty"> Quantity <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <input  value="<?=$qty?>" type="text" class="form-control input-medium num" name="qty" id="qty" autocomplete="off" />
                                                    <span id="product-unit"> </span> <span id="product-unit1" style="display:none;"> </span> </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label class="control-label" for="product"> Status <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select name="status" id="status" required="true" class="form-control input-medium">
                                                        <option value=""> Select </option>
                                                        <option <?=(($status=='Pre Shipment')?' selected ':'')?> value="Pre Shipment"> Pre Shipment </option>
                                                        <option <?=(($status=='Tender')?' selected ':'')?> value="Tender"> Tender </option>
                                                        <option <?=(($status=='PO')?' selected ':'')?> value="PO"> PO </option>
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
                                                    <button type="submit" class="btn btn-primary" id="add_receive"> <?=$strDo?> Shipment</button>
                                                    <button type="reset" class="btn btn-info" id="reset"> Reset </button>
                                                    <input type="hidden" name="action" id="action" value="<?php echo $strDo; ?>" />
                                                    <input type="hidden" name="shipment_id" id="shipment_id" value="<?php echo $shipment_id; ?>" />

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
             
    <?php
}
}
?>
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if($str_do !='Edit')
                        {
                        ?>
                        <div  class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter By</h3>
                            </div>
                            <div id="shipment_filter_div" class="widget-body">
                                <form method="POST" name="batch_search" action="" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12">



                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="warehouse">Funding Source</label>
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
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label">Status</label>
                                                    <div class="controls">
                                                        <select name="status" id="status" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <option value="Pre Shipment" <?php if ($searchby == 'Pre Shipment') { ?> selected <?php } ?>>Pre Shipment</option>
                                                            <option value="Tender" <?php if ($searchby == 'Tender') { ?> selected <?php } ?>>Tender</option>
                                                            <option value="PO" <?php if ($searchby == 'PO') { ?> selected <?php } ?>>PO</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12"> 
                                            <div class="col-md-3" id="ProvincesCol">
                                                <div class="control-group">
                                                    <label>Procured by<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="procured_by" id="procured_by" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //Populate select3 combo
                                                            
                                                            if(isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2)
                                                            {
                                                                $objloc->LocLvl=2;
                                                                $rsloc = $objloc->GetLocationsById($_SESSION['user_province1']);
                                                            }
                                                            else
                                                            {
                                                                $rsloc = $objloc->GetAllLocationsL2();
                                                            }
                                                            
                                                            if ($rsloc != FALSE && mysql_num_rows($rsloc) > 0) {
                                                                while ($RowLoc = mysql_fetch_object($rsloc)) {
                                                                    ?>
                                                                    <option value="<?= $RowLoc->PkLocID ?>" <?php
                                                                    if ($RowLoc->PkLocID == $procured_by) {
                                                                        echo 'selected="selected"';
                                                                    }
                                                                    ?>> <?php echo $RowLoc->LocName; ?> </option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
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
                                            <div class="col-md-3 right">
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
                        <?php
                        }
                        ?>
                        <!-- Widget --> <?php
                            if($temp_voucher_exists)
                            {
                                ?>
                                <div class="note note-danger">Please process the temporary vouchers of following shipments first. (How to:Click 'Receive' button and then SAVE the temporary voucher)</div>

                            <?php
                            }
                            ?>
                        <div class="widget" data-toggle="collapse-widget"> 
                           

                            <!-- Widget heading -->
                            <div class="widget-head">
                                <h4 class="heading">Shipment Search</h4>
                            </div>

                            <!-- // Widget heading END -->

                            <div class="widget-body"> 

                                <!-- Table --> 
                                <!-- Table -->
                                <table class="shipmentsearch table table-bordered table-condensed">

                                    <!-- Table heading -->
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Shipment Date</th>
                                            <th>Ref No</th>
                                            <th>Funding Source</th>
                                            <th>Procured By</th>
                                            <th>Product</th>
                                            <th>Total Shipment Qty</th>
                                            <th>Received Qty</th>
                                            <th>Remaining Shipment</th>
                                            <th>Unit</th>
                                            <th>Total Cartons</th>
                                            <th>Status</th>
                                            <th>Actions</th>


                                        </tr>
                                    </thead>
                                    <!-- // Table heading END --> 

                                    <!-- Table body -->
                                    <tbody>

                                        <!-- Table row -->
                                        <?php
                                        $i = 1;
                                        $transNo = '';
                                        if($temp_voucher_exists ==false)
                                        {
                                            if ($result != FALSE) {
                                                //fetch result
                                                while ($row = mysql_fetch_object($result)) {

                                                    $s_q = isset($row->shipment_quantity)?$row->shipment_quantity:'0';
                                                    $r_q = isset($row->received_qty)?$row->received_qty:'0';
                                                    $remaining_q = $s_q - $r_q;

                                                    ?>
                                                    <tr class="gradeX" >
                                                        <td class="text-center"><?php echo $i; ?></td>
                                                        <td class="editableSingle id<?php echo $row->pk_id; ?>"><?php echo date("d/m/Y", strtotime($row->shipment_date)); ?></td>

                                                        <td><?php echo $row->reference_number; ?></td>
                                                        <td><?php echo $row->stkname; ?></td>
                                                        <td><?php echo ($row->stkname == 'Save the Children' ? 'Donor' : $row->procured_by ); ?></td>
                                                        <td><?php echo $row->itm_name; ?></td>
                                                        <td class="right"><?php echo number_format($s_q); ?></td>
                                                        <td class="right"><?php echo number_format($r_q); ?></td>
                                                        <td class="right"><?php echo number_format($remaining_q); ?></td>

                                                        <td class="right"><?php echo $row->UnitType ?></td>
                                                        <td class="right"><?php
                                                            //carton qty
                                                            $cartonQty = $row->shipment_quantity / $row->qty_carton;
                                                            echo (floor($cartonQty) != $cartonQty) ? number_format($cartonQty, 2) : number_format($cartonQty);
                                                            ?>
                                                        </td>
                                                        <?php

                                                            if($r_q > 0 && $remaining_q > 0)
                                                            {
                                                                $st =  'Partially Received';
                                                                $cls = "";
                                                            }
                                                            elseif($r_q > 0 && $remaining_q == 0)
                                                            {
                                                                $st =  'Received';
                                                                $cls = " success ";
                                                            }
                                                            elseif($row->status =='Received')
                                                            {
                                                                $st = $row->status; 
                                                                $cls = " success ";
                                                            }
                                                            else
                                                            {
                                                                $st = $row->status; 
                                                                $cls = "";
                                                            }
                                                            ?>
                                                        <td align="center" class=" <?=$cls?> ">
                                                            <?=$st?>
                                                        </td>

                                                        <td>
                                                            <?php
                                                            if($row->status != 'Cancelled' && ($row->status != 'Received' || $st == 'Partially Received'))
                                                            {

                                                                if(isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2  && $st != 'Partially Received' )
                                                                {
                                                                    if($remaining_q >0)
                                                                    {
                                                                        echo '<a href="search-shipments.php?DO=Edit&shipment_id='.$row->pk_id.'">Edit</a>';
                                                                        echo ' | ';
                                                                        echo '<a href="search_shipments_action.php?action=cancel&shipment_id='.$row->pk_id.'">Cancel</a>';
                                                                    }
                                                                    //echo ' | ';
                                                                }
                                                                elseif($_SESSION['user_level'] < 2)
                                                                {
                                                                    if($remaining_q >0 && ($row->status=='Pre Shipment' || $st == 'Partially Received'))
                                                                        echo '<a style="color:green !important;" href="receive_shipment.php?shipment_id='.$row->pk_id.'">Receive</br></a>';
                                                                }

                                                            }

                                                            if($st == 'Received'  || $st== 'Partially Received'){
                                                                echo $objshipments->getReceivedVouhcers($row->pk_id);
                                                            }
                                                            ?>

                                                        </td>
                                                    </tr>
                                                    <?php
                                                    $i++;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            foreach ($temp_stock as $k=> $row2) {
                                                $row = (object)$row2;
                                                    //echo '<pre>';print_r($row2);print_r($row);exit;
                                                    $s_q = isset($row->shipment_quantity)?$row->shipment_quantity:'0';
                                                    $r_q = isset($row->received_qty)?$row->received_qty:'0';
                                                    $remaining_q = $s_q - $r_q;

                                                    ?>
                                                    <tr class="gradeX" >
                                                        <td class="text-center"><?php echo $i; ?></td>
                                                        <td class="editableSingle id<?php echo $row->pk_id; ?>"><?php echo date("d/m/Y", strtotime($row->shipment_date)); ?></td>

                                                        <td><?php echo $row->reference_number; ?></td>
                                                        <td><?php echo $row->stkname; ?></td>
                                                        <td><?php echo $row->procured_by; ?></td>
                                                        <td><?php echo $row->itm_name; ?></td>
                                                        <td class="right"><?php echo number_format($s_q); ?></td>
                                                        <td class="right"><?php echo number_format($r_q); ?></td>
                                                        <td class="right"><?php echo number_format($remaining_q); ?></td>

                                                        <td class="right"><?php echo $row->UnitType ?></td>
                                                        <td class="right">
                                                        </td>
                                                        <?php

                                                            if($r_q > 0 && $remaining_q > 0)
                                                            {
                                                                $st =  'Partially Received';
                                                                $cls = "";
                                                            }
                                                            elseif($r_q > 0 && $remaining_q == 0)
                                                            {
                                                                $st =  'Received';
                                                                $cls = " success ";
                                                            }
                                                            elseif($row->status =='Received')
                                                            {
                                                                $st = $row->status; 
                                                                $cls = " success ";
                                                            }
                                                            else
                                                            {
                                                                $st = $row->status; 
                                                                $cls = "";
                                                            }
                                                            ?>
                                                        <td align="center" class=" <?=$cls?> ">
                                                            <?=$st?>
                                                        </td>

                                                        <td>
                                                            <?php
                                                             echo '<a style="color:green !important;" href="receive_shipment.php?shipment_id='.$row->pk_id.'">Receive</br></a>';
                                                                
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
								<div class="row">
									<div class="col-md-12">
										<div class="left pull-left col-md-6" style="margin-top:10px !important;">
											<div class="col-md-2">
													<button id="print_shipment_summary" type="button" class="btn btn-warning">Print</button>
                                             </div>
                                             <div class="col-md-2">
                                               	 <label class="control-label">Summary:</label>
                                             </div>
                                             <div class="col-md-2">
												<select class="form-control input-medium" id="print_summary_dd">
													<option value="funding_source_wise">Funding Source Wise</option>
													<option value="product_wise">Product Wise</option>
												</select>
											</div>
											<div style="clear:both;"></div>
										</div>
										
										<div class="right pull-right col-md-6" style="margin-top:10px !important;float:left">
											<div class="col-md-2">
												<button id="print_shipment_detail" type="button" class="btn btn-warning">Print</button>
                                                
                                                </div>
                                                <div class="col-md-1">
                                                <label class="control-label">Detail:</label>
                                                 </div>
                                                <div class="col-md-3">
												<select class="form-control input-medium"  id="print_detail_dd">
													
													<option value="funding_source_wise">Funding Source Wise</option>
													<option value="product_wise">Product Wise</option>
												</select>
											</div>
											<div style="clear:both;"></div>
										</div>
									</div>
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
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/add-shipment.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit_date.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/search-shipments.js"></script>
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
        
        <?php
                            if($temp_voucher_exists)
                            {
                                ?>
                        <script type="text/javascript">
                            $( document ).ready(function() {
                                $('#shipment_filter_div').collapse('500');
                            });
                            
                        </script>
                            <?php
                            }
                            ?>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>