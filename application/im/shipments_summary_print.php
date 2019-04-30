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



//Stock Search

$r = array();
array_walk($_SESSION['sCriteria'], create_function('$b, $c', 'global $r; $r[]="$c: $b";'));
//criteria
$sCriteria = implode(', ', $r);


if ($_GET['type'] == 'funding_source_wise') {
    $groupby = ' GROUP BY shipments.pk_id  ';
} else if ($_GET['type'] == 'product_wise') {
    $groupby = ' GROUP BY shipments.pk_id   ';
} else {
    $title = 'Shipment List';
}

$result = $objshipments->ShipmentSearch(1, $wh_id , $groupby);

while ($row = mysql_fetch_object($result)) {
        $productArr[$row->itm_name][] = $row;
        $fundingArr[$row->stkname][] = $row;
        
        
        $productArr2[$row->itm_name][$row->stkname]['UnitType'] = $row->UnitType;    
        $productArr2[$row->itm_name][$row->stkname]['qty_carton'] = $row->qty_carton;    
        if(empty($productArr2[$row->itm_name][$row->stkname]['shipment_quantity']))$productArr2[$row->itm_name][$row->stkname]['shipment_quantity']=0;
        if(empty($productArr2[$row->itm_name][$row->stkname]['received_qty'])) $productArr2[$row->itm_name][$row->stkname]['received_qty']=0;
        $productArr2[$row->itm_name][$row->stkname]['shipment_quantity'] += $row->shipment_quantity;
        $productArr2[$row->itm_name][$row->stkname]['received_qty'] += $row->received_qty;
        
        $fundingArr2[$row->stkname][$row->itm_name]['UnitType'] = $row->UnitType;    
        $fundingArr2[$row->stkname][$row->itm_name]['qty_carton'] = $row->qty_carton;    
        if(empty($fundingArr2[$row->stkname][$row->itm_name]['shipment_quantity']))$fundingArr2[$row->stkname][$row->itm_name]['shipment_quantity']=0;
        if(empty($fundingArr2[$row->stkname][$row->itm_name]['received_qty'])) $fundingArr2[$row->stkname][$row->itm_name]['received_qty']=0;
        $fundingArr2[$row->stkname][$row->itm_name]['shipment_quantity'] += $row->shipment_quantity;
        $fundingArr2[$row->stkname][$row->itm_name]['received_qty'] += $row->received_qty;
        
        $funding_source = $row->stkname;
        $product = $row->itm_name;
        $procured_by = $row->procured_by;
    }
    //echo '<pre>';print_r($fundingArr2);print_r($fundingArr);exit;
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
    $rptName = 'Shipment List';
//include report_header
    include('report_header.php');
   
    echo '<div style="line-height:1;">';    
    echo '<div id="report_type" style="float:left;  text-align:center; width:100%;">';
    echo '<p>'. ucwords(str_replace('_', ' ', $_GET['type'])).' Shipments Summary Between <b>'.$_REQUEST['date_from'].'</b> and <b>'.$_REQUEST['date_to'].'</b></p>';
    
    if(!empty($_REQUEST['warehouse'])) echo '<p>Funding Source : <b>'.$funding_source.'</b></p>';
    if(!empty($_REQUEST['product'])) echo '<p>Product : <b>'.$product.'</b></p>';
    if(!empty($_REQUEST['procured_by'])) echo '<p>Procured By: <b>'.$procured_by.'</b></p>';
    if(!empty($_REQUEST['status'])) echo '<p>Status: <b>'.$_REQUEST['status'].'</b></p>';
    echo '';
    echo '</div>';
    echo '</div>';
    
    if ( $_GET['type'] == 'product_wise') {
        foreach ($productArr2 as $key => $r_data) {

    ?>
    <b><?php echo $key; ?></b>
    <table id="myTable" class="table-condensed" cellpadding="3">
        <!-- Table heading -->
        <thead>
            <tr>
                <th>Sr. No.</th>
               
                <th>Funding Source</th>
                <th>Total Shipment Quantity</th>
                <th>Received Quantity</th>
                <th>Remaining Shipment</th>
                <th>Unit</th>
                <th>Cartons</th>
            </tr>
        </thead>
        <!-- // Table heading END --> 

        <!-- Table body -->
        <tbody>
            <!-- Table row -->
            <?php
            $i = 1;
            $transNo = '';
            $carton_total=$prod_total=$rec_total=0;
            foreach ($r_data as  $stkname=>$row2) {
                $row=(object)$row2;
                
                $s_q = $row->shipment_quantity;
                $r_q = $row->received_qty;
                $remaining_qty = $s_q-$r_q;
                
                 $prod_total+=$s_q;
                 $rec_total+=$r_q;
                $unit=$row->UnitType;
                //fetch result
                    ?>
                    <tr class="gradeX" >
                        <td class="text-center"><?php echo $i; ?></td>
                        <td><?php echo $stkname; ?></td>
                        
                        <td class="right" align="right"><?php echo number_format($s_q); ?></td>
                        <td class="right" align="right"><?php echo number_format($r_q); ?></td>
                        <td class="right" align="right"><?php echo number_format($remaining_qty); ?></td>
                        <td class="right"><?php echo $row->UnitType ?></td>
                        <td class="right" align="right"><?php
                            //carton qty
                            $cartonQty = $row->shipment_quantity / $row->qty_carton;
                            $cartonVal = (floor($cartonQty) != $cartonQty) ? number_format($cartonQty, 0) : number_format($cartonQty);
                            echo $cartonVal;
                            $carton_total +=$cartonVal;
                            ?>
                        </td>
                        
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                 echo '<tr><td colspan="2">Total :</td>';
                echo '<td align="right">'.number_format($prod_total).'</td>';
                echo '<td align="right">'.number_format($rec_total).'</td>';
                echo '<td align="right">'.number_format(($prod_total - $rec_total)).'</td>';
                echo '<td>'.$unit.'</td>';
                echo '<td align="right">'.number_format($carton_total,0).'</td></tr>';
            
            ?>
            <!-- // Table row END -->
        </tbody>

    </table>
    <?php
        }
    }
    
    //for the funding source wise report
    
    if ( $_GET['type'] == 'funding_source_wise') {
        foreach ($fundingArr2 as $key => $r_data) {

    ?>
    <b><?php echo $key; ?></b>
    <table id="myTable" class="table-condensed" cellpadding="3">
        <!-- Table heading -->
        <thead>
            <tr>
                <th>Sr. No.</th>
                <th>Product</th>
                
                <th>Total Shipment Quantity</th>
                <th>Received Quantity</th>
                <th>Remaining Shipment</th>
                <th>Unit</th>
                <th>Cartons</th>
            </tr>
        </thead>
        <!-- // Table heading END --> 

        <!-- Table body -->
        <tbody>
            <!-- Table row -->
            <?php
            $i = 1;
            $transNo = '';
            foreach ($r_data as $prod_name=>$row2) {
                $row=(object)$row2;
                
                
                $s_q = $row->shipment_quantity;
                $r_q = $row->received_qty;
                $remaining_qty = $s_q-$r_q;
                //fetch result
                    ?>
                    <tr class="gradeX" >
                        <td class="text-center"><?php echo $i; ?></td>
                        <td><?php echo $prod_name; ?></td>
                        
                        <td class="right" align="right"><?php echo number_format($s_q); ?></td>
                        <td class="right" align="right"><?php echo number_format($r_q); ?></td>
                        <td class="right" align="right"><?php echo number_format($remaining_qty); ?></td>
                        <td class="right"><?php echo $row->UnitType ?></td>
                        <td class="right" align="right"><?php
                            //carton qty
                            $cartonQty = $row->shipment_quantity / $row->qty_carton;
                            echo (floor($cartonQty) != $cartonQty) ? number_format($cartonQty, 0) : number_format($cartonQty);
                            ?>
                        </td>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
            
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