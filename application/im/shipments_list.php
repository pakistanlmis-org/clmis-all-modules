<?php
/**
 * stock_received_list
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//ob_start
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
    $objStockMaster->fromDate = $_SESSION['sCriteria']['date_from'];
}
//date to
if (isset($_SESSION['sCriteria']['date_to']) && !empty($_SESSION['sCriteria']['date_to'])) {
    $objStockMaster->toDate = $_SESSION['sCriteria']['date_to'];
}



//Stock Search

$r = array();
array_walk($_SESSION['sCriteria'], create_function('$b, $c', 'global $r; $r[]="$c: $b";'));
//criteria
$sCriteria = implode(', ', $r);

$result = $objshipments->ShipmentSearch(1, $wh_id);
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
    ?>
    <table id="myTable" class="table-condensed" cellpadding="3">
        <!-- Table heading -->
        <thead>
            <tr>
                <th>Sr. No.</th>
                <th>Shipment Date</th>
                <th>Ref No</th>

                <th>Product</th>
                <th>Funding Source</th>
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
            if ($result != FALSE) {
                //fetch result
                while ($row = mysql_fetch_object($result)) {
                    ?>
                    <tr class="gradeX" >
                        <td class="text-center"><?php echo $i; ?></td>
                        <td><?php echo date("d/m/y", strtotime($row->shipment_date)); ?></td>

                        <td><?php echo $row->reference_number; ?></td>
                        <td><?php echo $row->itm_name; ?></td>
                        <td><?php echo $row->stkname; ?></td>
                        <td><?php echo $row->status; ?></td>

                        </td>
                    </tr>
                    <?php
                    $i++;
                }
            }
            ?>
            <!-- // Table row END -->
        </tbody>

    </table>
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