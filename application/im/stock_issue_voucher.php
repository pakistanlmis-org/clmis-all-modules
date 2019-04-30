<style>

    .watermark {
        position: absolute;
        color: grey;
        opacity: 0.25;
        font-size: 11em;
        margin-left: 138px;
        z-index: 1000;
        -webkit-transform: rotate(-45deg);
        -moz-transform: rotate(-45deg);
        -o-transform: rotate(-45deg);
        -ms-transform: rotate(-45deg);
        transform: rotate(-45deg);
    }
</style>
<?php
/**
 * stock_issue_voucher
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

$print = 1;
//title
$title = "Stock Issue Voucher";
//get stock id
$stockId = $_GET['id'];
//select query
//gets
//wh id to 
//created by
$qry = "SELECT
			tbl_stock_master.WHIDTo,
			tbl_stock_master.CreatedBy
		FROM
			tbl_stock_master
		WHERE
			tbl_stock_master.PkStockID = " . $stockId;
//query result
$qryRes = mysql_fetch_array(mysql_query($qry));
//wh id
$wh_id = $qryRes['WHIDTo'];
//user id
$userid = $qryRes['CreatedBy'];

// Get district Name
$wh_id = $_REQUEST['whTo'];
//query get district
$getDist = "SELECT
			tbl_locations.LocName
		FROM
			tbl_warehouse
		INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
		WHERE
			tbl_warehouse.wh_id = $wh_id";
//query result
$rowDist = mysql_fetch_object(mysql_query($getDist));
?>

<!-- Content -->

<div id="content_print">
    <?php
    $rptName = ' Stock Issue Voucher';
    //include report_header
    include('report_header.php');
    ?>
    <div style="text-align:center;">
        <b style="float:right;">District: <?php echo $rowDist->LocName; ?></b><br />
        <b style="float:left;">Issue Voucher: <?php echo $_REQUEST['req_no']; ?></b>
        <b style="float:right;">Date of Departure: <?php echo date('d/m/y', strtotime($_SESSION['trans_date'])); ?></b>
    </div>
    <div style="clear:both;">
        <b style="float:left;">Reference No.: <?php echo $_REQUEST['refrenceno']; ?></b>
        <b style="float:right;">Issue To: <?php echo $_REQUEST['recip']; ?></b><br />
        <b style="float:right;">Issue By: <?php echo $_REQUEST['issuedBy']; ?></b>
    </div>
    <div class="watermark">Draft</div>
    <table id="myTable" class="table-condensed">
        <thead>
            <tr>
                <th width="8%">S. No.</th>
                <th>Product</th>
                <th width="15%">Batch No.</th>
                <th width="15%">Expiry Date</th>
                <th width="15%">Quantity</th>
                <th width="8%">Unit</th>
                <th width="10%">Cartons</th>
            </tr>
        </thead>
        <tbody>
            <?php
            //summary array
            $summaryArr = array();
            //product
            $product = '';
            $product = '';
            $i = 0;
            //totla qty
            $totalQty = 0;
            //total carton
            $totalCartons = 0;
            //Get Stocks Receive List
            $result = $objStockMaster->GetStocksReceiveList($userid, $wh_id, 2, $stockId);
            if (!empty($result)) {
                //fetch results
                while ($row = mysql_fetch_object($result)) {
                    
                    
                    
                    if ($row->itm_name != $product && $i > 1) {
                        ?>
                        <tr>
                            <th colspan="4" style="text-align:right;">Total</th>
                            <th style="text-align:right;"><?php echo number_format($totalQty); ?></th>
                            <th>&nbsp;</th>
                            <th style="text-align:right;"><?php echo number_format($totalCartons); ?></th>
                        </tr>
                      
                        <?php
                        //total qty
                        $totalQty = abs($row->Qty);
                        //total carton
                        $totalCartons = abs($row->Qty) / $row->qty_carton;
                    } else {
                        //total qty
                        $totalQty += abs($row->Qty);
                        //total carton
                        $totalCartons += abs($row->Qty) / $row->qty_carton;
                    }
                    //product
                    $product = $row->itm_name;
                    
                      
                       
                       
                        if ($product != $row->itm_name) {
                            $summaryArr[$row->itm_name]['Qty'][] = abs($row->Qty);
                            $summaryArr[$row->itm_name]['qty_carton'][] = abs($row->Qty) / $row->qty_carton;
                            $product = $row->itm_name;
                        } else {
                            $summaryArr[$row->itm_name]['Qty'][] = abs($row->Qty);
                            $summaryArr[$row->itm_name]['qty_carton'][] = abs($row->Qty) / $row->qty_carton;
                        }
                        
                    
                    $i++;
                    ?>
                    <tr class="gradeX">
                        <td style="text-align:center;"><?php echo $i; ?></td>
                        <td><?php echo $row->itm_name; ?>&nbsp;</td>
                        <td><?php echo $row->batch_no; ?>&nbsp;</td>
                        <td style="text-align:center;"><?php echo date("d/m/y", strtotime($row->batch_expiry)); ?>&nbsp;</td>
                        <td style="text-align:right;"><?php echo number_format(abs($row->Qty)); ?>&nbsp;</td>
                        <td style="text-align:right;"><?php echo $row->UnitType; ?></td>
                        <td style="text-align:right;"><?php echo number_format(abs($row->Qty) / $row->qty_carton); ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            <!-- // Table row END -->
            <tr>
                <th colspan="4" style="text-align:right;">Total</th>
                <th style="text-align:right;"><?php echo number_format($totalQty); ?></th>
                <th>&nbsp;</th>
                <th style="text-align:right;"><?php echo number_format($totalCartons); ?></th>
            </tr>
        </tbody>
    </table>

    <h5 style="margin-top:30px;" class="heading">Summary</h5>
    <table id="myTable" style="width:70%;" cellpadding="3">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Cartons</th>
            </tr>
        </thead>
        <tbody>
            <?php
       
            
//get data from summaryArr
            foreach ($summaryArr as $product => $qty) {
                ?>
                <tr>
                    <td><?php echo $product; ?></td>
                    <td style="text-align:right;"><?php echo number_format(array_sum($summaryArr[$product]['Qty'])); ?></td>
                    <td style="text-align:right;"><?php echo number_format(array_sum($summaryArr[$product]['qty_carton'])); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
//include report_footer_issue
    include('report_footer_issue.php');
    ?>

    <div style="width:100%; clear:both;">
        <table width="48%" cellpadding="5" style="float:left; border:2px solid #E5E5E5 !important; border-collapse:collapse; margin-top:10px;">
            <tr>
                <td><b>Verified by</b> - Name: ________________________________________</td>
            </tr>
            <tr>
                <td>Signature: ________________________________________</td>
            </tr>
        </table>
    </div>

    <div style="float:right; margin-top:10px;" id="printButt">
        <input type="button" name="print" value="Print" class="btn btn-warning" onclick="javascript:printCont();" />
    </div>

</div>

<?php
//unset stock id
unset($_SESSION['stock_id']);
?>

<script src="<?php echo PUBLIC_URL; ?>assests/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script language="javascript">
            $(function () {
                printCont();
            })
            function printCont()
            {
                window.print();
            }
</script>