<?php


ob_start();
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//wh id
$wh_id = $_SESSION['user_warehouse'];
//title
$title = "Shipment List";
$print = 1;
//echo '<pre>';print_r($_SESSION);exit;
$groupby = '';
//check group by
if ($_GET['type'] == 'funding_source_wise') {
    $title = 'Funding Source wise ';
    //set group by
    //$groupby = ' GROUP BY stkname,itm_name,shipment_date ORDER BY shipments.stk_id';
    $groupby = ' GROUP BY shipments.pk_id  ';
} else if ($_GET['type'] == 'product_wise') {
    $title = 'Product wise ';
    //set group by
    //$groupby = ' GROUP BY itm_name,stkname,shipment_date ORDER BY shipments.item_id ';
    $groupby = ' GROUP BY shipments.pk_id   ';
} else {
    $title = 'Shipment List';
}

//check search by

if (isset($_SESSION['sCriteria']['status']) && !empty($_SESSION['sCriteria']['status'])) {
    //get search by
    $searchby = $_SESSION['sCriteria']['status'];

    $objshipments->status = $searchby;
}
//check warehouse

if (isset($_SESSION['sCriteria']['warehouse']) && !empty($_SESSION['sCriteria']['warehouse'])) {
    //get warehouse
    $warehouse = $_SESSION['sCriteria']['warehouse'];


    //set from warehouse
    $objshipments->WHID = $warehouse;
}
//check product

if (isset($_SESSION['sCriteria']['product']) && !empty($_SESSION['sCriteria']['product'])) {
    //get product
    $product = $_SESSION['sCriteria']['product'];

    //set product
    $objshipments->item_id = $product;
}
//check manufacturer

if (isset($_SESSION['sCriteria']['manufacturer']) && !empty($_SESSION['sCriteria']['manufacturer'])) {
    //get manufacturer
    $manufacturer = $_SESSION['sCriteria']['manufacturer'];
    //set manufacturer	
    $objshipments->manufacturer = $manufacturer;
}
//check date from

if (isset($_SESSION['sCriteria']['date_from']) && !empty($_SESSION['sCriteria']['date_from'])) {
    $objshipments->fromDate = $_SESSION['sCriteria']['date_from'];
}
//date to
if (isset($_SESSION['sCriteria']['date_to']) && !empty($_SESSION['sCriteria']['date_to'])) {
    $objshipments->toDate = $_SESSION['sCriteria']['date_to'];
}
if (isset($_SESSION['sCriteria']['procured_by']) && !empty($_SESSION['sCriteria']['procured_by'])) {
    $objshipments->procured_by = $_SESSION['sCriteria']['procured_by'];
}



//Stock Search

$r = array();
array_walk($_SESSION['sCriteria'], create_function('$b, $c', 'global $r; $r[]="$c: $b";'));
//criteria
$sCriteria = implode(', ', $r);

$result = $objshipments->ShipmentSearch(1, $wh_id,$groupby,'summary');

//echo '<pre>';
while ($row = mysql_fetch_object($result)) {
    //print_r($row);
        $productArr[$row->itm_name][] = $row;
        $fundingArr[$row->stkname][] = $row;
        
        $funding_source = $row->stkname;
        $product = $row->itm_name;
        $procured_by = $row->procured_by;
    }
    //exit;
    
?>

<!-- Content -->

<div id="content_print">
    <style type="text/css" media="print">
        .page
        {
            -webkit-transform: rotate(-90deg); -moz-transform:rotate(-90deg);
            filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
        }
        @media print
        {    
            #printButt
            {
                display: none !important;
            }
        }
    </style>
    <?php
    $rptName = $title.'Shipment List';
//include report_header
    include('report_header.php');

    echo '<div style="line-height:1;">';    
    echo '<div id="report_type" style="float:left;  text-align:center; width:100%;">';
    echo '<p>Shipments Status Between <b>'.$_SESSION['sCriteria']['date_from'].'</b> and <b>'.$_SESSION['sCriteria']['date_to'].'</b></p>';
    
    if(!empty($_REQUEST['warehouse'])) echo '<p>Funding Source : <b>'.$funding_source.'</b></p>';
    if(!empty($_REQUEST['product'])) echo '<p>Product : <b>'.$product.'</b></p>';
    if(!empty($_REQUEST['procured_by'])) echo '<p>Procured By: <b>'.$procured_by.'</b></p>';
    if(!empty($_REQUEST['status'])) echo '<p>Status: <b>'.$_REQUEST['status'].'</b></p>';
    echo '';
    echo '</div>';
    echo '</div>';
    
    
    if ( $_GET['type'] == 'product_wise') {
        foreach ($productArr as $key => $r_data) {
           $prod_total=0;
           $rec_total=0;
           $carton_total=0;
           $unit='';
    ?>
    <b><?php echo $key; ?></b>
    <table id="myTable" class="table-condensed" cellpadding="3">
        <!-- Table heading -->
        <thead>
            <tr>
                <th>Sr. No.</th>
                <th>Shipment Date</th>
                <th>Ref No</th>
                <!--<th>Product</th>-->
                <th>Funding Source</th>
                <th>Total Shipment Quantity</th>
                <th>Received Quantity</th>
                <th>Remaining Shipment</th>
                <th>Unit</th>
                <th>Cartons</th>
                <th>Status</th>
            </tr>
        </thead>
        <!-- // Table heading END --> 

        <!-- Table body -->
        <tbody>
            <!-- Table row -->
            <?php
            $i = 1;
            $transNo = '';
            foreach ($r_data as $k=>$row) {
                $unit=$row->UnitType;
                
                $s_q = $row->shipment_quantity;
                $r_q = $row->received_qty;
                $remaining_qty = $s_q-$r_q;
                
                
                $prod_total+=$s_q;
                $rec_total+=$r_q;
                //fetch result
                    ?>
                    <tr class="gradeX" >
                        <td class="text-center"><?php echo $i; ?></td>
                        <td><?php echo date("d/m/y", strtotime($row->shipment_date)); ?></td>
                        <td><?php echo $row->reference_number; ?></td>
                        <!--<td><?php echo $row->itm_name; ?></td>-->
                        <td><?php echo $row->stkname; ?></td>
                        
                        <td class="right"  align="right"><?php echo number_format($s_q); ?></td>
                        <td class="right"  align="right"><?php echo number_format($r_q); ?></td>
                        <td class="right"  align="right"><?php echo number_format($remaining_qty); ?></td>
                        <td><?php echo $row->UnitType ?></td>
                        <td class="right"  align="right"><?php
                            //carton qty
                            $cartonQty = $row->shipment_quantity / $row->qty_carton;
                            $cartonVal =  (floor($cartonQty) != $cartonQty) ? ($cartonQty) : ($cartonQty);
                            echo number_format($cartonVal);
                            $carton_total +=$cartonVal;
                            ?>
                        </td>
                        
                        <td><?php echo $row->status; ?></td>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                echo '<tr><td colspan="4">Total :</td>';
                echo '<td align="right">'.number_format($prod_total).'</td>';
                echo '<td align="right">'.number_format($rec_total).'</td>';
                echo '<td align="right">'.number_format($prod_total-$rec_total).'</td>';
                echo '<td>'.$unit.'</td>';
                echo '<td align="right">'.number_format($carton_total,0).'</td>';
                echo '<td align="right"> </td></tr>';
            
            ?>
            <!-- // Table row END -->
        </tbody>

    </table>
    <?php
        }
    }
    
    //for the funding source wise report
    
    if ( $_GET['type'] == 'funding_source_wise') {
        foreach ($fundingArr as $key => $r_data) {
             $f_total=0;

    ?>
    <b><?php echo $key; ?></b>
    <table id="myTable" class="table-condensed" cellpadding="3">
        <!-- Table heading -->
        <thead>
            <tr>
                <th>Sr. No.</th>
                <th>Shipment Date</th>
                <th>Ref No</th>
                <th>Product</th>
                <!--<th>Funding Source</th>-->
                <th>Total Shipment Quantity</th>
                <th>Received Quantity</th>
                <th>Remaining Shipment</th>
                <th>Unit</th>
                <th>Cartons</th>
                <th>Status</th>
            </tr>
        </thead>
        <!-- // Table heading END --> 

        <!-- Table body -->
        <tbody>
            <!-- Table row -->
            <?php
            $i = 1;
            $transNo = '';
            foreach ($r_data as $k=>$row) {
                
                $s_q = $row->shipment_quantity;
                $r_q = $row->received_qty;
                $remaining_qty = $s_q-$r_q;
                
                $f_total+=$row->shipment_quantity;
                //fetch result
                    ?>
                    <tr class="gradeX" >
                        <td class="text-center"><?php echo $i; ?></td>
                        <td><?php echo date("d/m/y", strtotime($row->shipment_date)); ?></td>
                        <td><?php echo $row->reference_number; ?></td>
                        <td><?php echo $row->itm_name; ?></td>
                        <!--<td><?php echo $row->stkname; ?></td>-->
                        
                        <td class="right" align="right"><?php echo number_format($s_q); ?></td>
                        <td class="right" align="right"><?php echo number_format($r_q); ?></td>
                        <td class="right" align="right"><?php echo number_format($remaining_qty); ?></td>
                        <td><?php echo $row->UnitType ?></td>
                        <td class="right" align="right"><?php
                            //carton qty
                            $cartonQty = $row->shipment_quantity / $row->qty_carton;
                            echo (floor($cartonQty) != $cartonQty) ? number_format($cartonQty, 0) : number_format($cartonQty,0);
                            ?>
                        </td>
                        
                        <td><?php echo $row->status; ?></td>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
               // echo '<tr><td colspan="5">Total :</td><td>'.number_format($f_total).'</td></tr>';
            
            ?>
            <!-- // Table row END -->
        </tbody>

    </table>
    <?php
        }
    }
    
    ?>
    <?php /* ?><div style="float:left; font-size:12px;"> <?php echo !empty($sCriteria) ? '<b>Criteria: </b>'.$sCriteria : ''; ?><br /><?php */ ?>
    <b>Print Date:</b> <?php echo date('d/m/y') . ' <b>by</b> ' . $_SESSION['user_name']; ?> </div>
<div style="float:right; margin:20px;" id="printButt">
    <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
</div>
</div>

<!-- // Content END -->
<script language="javascript">
    $(function () {
        printCont();
    })
    function printCont()
    {
        window.print();
    }
</script>