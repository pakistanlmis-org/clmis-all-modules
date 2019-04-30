<?php
/**
 * printReceive
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//includ AllClasses
include("../includes/classes/AllClasses.php");
//includ header
include(PUBLIC_PATH . "html/header.php");

$title = "Stock Recieve Voucher";
$print = 1;
//$stockid = 9733;
//$_GET['id']=$stockid;

$qry = "SELECT
				tbl_stock_master.WHIDTo,
				tbl_stock_master.CreatedBy
			FROM
				tbl_stock_master
			WHERE
				tbl_stock_master.PkStockID = " . $stockid;
//query result
$qryRes = mysql_fetch_array(mysql_query($qry));
$wh_id = $qryRes['WHIDTo'];
$userid = $qryRes['CreatedBy'];
//Get Stocks Receive List
$stocks = $objStockMaster->GetStocksReceiveList($userid, $wh_id, 1, $stockid);
$receiveArr = array();
//fetch data from stocks
while ($row = mysql_fetch_object($stocks)) {
    //rec_no
    $rec_no = $row->TranNo;
    //tran_ref
    $tran_ref = $row->TranRef;
    //rec_date
    $rec_date = $row->TranDate;
    //rec_from
    $rec_from = $row->wh_name;
    //IsSupplier
    $IsSupplier = $row->stk_type_id;
    //receiveArr
    $receiveArr[] = $row;
}
//if ($IsSupplier == 2) {
//    $rcvFrom = 'Supplier';
//} else {
//    $rcvFrom = 'Warehouse';
//}
if(empty($rcvFrom)) $rcvFrom = ' Stock ';
?>

<style>
*{font-family:"Open Sans",sans-serif;}
b{font-size:12px;}
h3{font-size:13px;}
#report_type{
font-size:12px;
font-family: arial;}
#content_print
{
	width:624px;
	margin-left:50px;
}
table#myTable{
	border:1px solid #E5E5E5;
	font-size:9pt;
	width:100%;
}
table, table#myTable tr td{
	border-collapse: collapse;
	border:1px solid #E5E5E5;
	font-size:12px;
}
table, table#myTable tr th{
	border:1px solid #E5E5E5;
	border-collapse: collapse;
	font-size:12px;
}
</style>
<div id="content_print">

    <?php
    $rptName = $rcvFrom." Receive Voucher";
    
    $getWHName="select wh_name,stkid from tbl_warehouse where wh_id='".$_SESSION['user_warehouse']."'";
    $resWHName=mysql_query($getWHName) or die(mysql_error());
    $whName=mysql_fetch_row($resWHName);

    $getStkLogo="select report_logo,report_title3 from stakeholder where stkid='".$whName[1]."'";
    $resStkLogo=mysql_query($getStkLogo) or die(mysql_error());
    $logo=mysql_fetch_row($resStkLogo);
?>
<div style="line-height:1;">
    
    
    <div id="report_type" style="float:left; width:440px; text-align:center;">
        <?php if ($whName[1]==1) {?>
         <span style="line-height:20px"><b>GOVERNMENT OF PAKISTAN</b></span><br/>
          <span style="line-height:20px"><b>MINISTRY OF NATIONAL HEALTH SERVICES</b></span><br/>
           <span style="line-height:20px"><b>REGULATIONS & COORDINATION</b></span><br/>
            <span style="line-height:20px">POPULATION PLANNING WING(PPW)/DIRECTORATE OF CENTRAL WAREHOUSE & SUPPLIES</span><br/>
        <?php } else {?>
        <span style="line-height:20px"><?php echo $logo[1]?></span><br/>
        <?php }?>
        <span style="line-height:15px"><b>Store: </b><?php echo $whName[0];?></span>
        <hr style="margin:3px 10px;" />
        <p>
            <b><?php echo $rptName;?> as on: <?php echo date('d/M/y');?></b>
        </p>
        <p>
            <b style="float:center;">Receive Voucher: <a target="_blank" href="<?php echo APP_URL.'im/printReceive.php?id='.$stockid;?>"><?=$rec_no?></a></b>
        </p>
        <p>    
            <b style="float:center;">Receiving Time: <?php echo date("d/M/y h:i A", strtotime($rec_date)); ?></b>
        </p>
        <p>    
            <b style="float:center;">Reference No.: <?php echo $tran_ref; ?></b>
        </p>
        <p>    
            <b style="float:center;">Source: <?php echo $rec_from; ?></b>
        </p>
    </div>
</div>

    <table id="myTable" style="border:1px solid #E5E5E5 !important;" class="table-condensed">
        <tr>
            <td width="8%"><b>S. No.</b></td>
            <td><b>Product</b></td>
            <td width="15%"><b>Batch No.</b></td>
            <td width="16%"><b>Production Date</b></td>
            <td width="12%"><b>Expiry Date</b></td>
            <td align="center"><b>Quantity</b></td>
            <td align="center" width="8%"><b>Unit</b></td>
            <td align="center"><b>Cartons</b></td>
        </tr>
        <tbody>
            <?php
            $i = 0;
            $totalQty = 0;
            $totalCartons = 0;
            if (!empty($receiveArr)) {
                foreach ($receiveArr as $val) {
                    $batch[] = $val->batch_no;
                    $i++;
                    $totalQty += abs($val->Qty);
                    $totalCartons += abs($val->Qty) / $val->qty_carton;
                    ?>
                    <tr>
                        <td style="text-align:center;"><?php echo $i; ?></td>
                        <td><?php echo $val->itm_name; ?></td>
                        <td><?php echo $val->batch_no; ?></td>
                        <td style="text-align:center;"><?php echo!empty($val->production_date) ? date("d/m/y", strtotime($val->production_date)) : ''; ?></td>
                        <td style="text-align:center;"> <?php echo date("d/m/y", strtotime($val->batch_expiry)); ?></td>
                        <td style="text-align:right;"><?php echo number_format($val->Qty); ?></td>
                        <td style="text-align:right;"><?php echo $val->UnitType; ?></td>
                        <td style="text-align:right;"><?php echo number_format(abs($val->Qty) / $val->qty_carton); ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align:right;">Total</th>
                <th style="text-align:right;"><?php echo number_format($totalQty); ?></th>
                <th>&nbsp;</th>
                <th style="text-align:right;"><?php echo number_format($totalCartons); ?></th>
            </tr>
        </tfoot>
        </tbody>

    </table>
    <?php
// Check if adjustments exists
    $batchNums = implode(',', $batch);
//query
//gets
//tbl_stock_master.TranDate,
//TranNo,
//TranRef,
//itm_name,
//batch_no,
//Qty,
//ReceivedRemarks,
//trans_type,
//itminfo_tab.itm_type
    $qry = "SELECT
					tbl_stock_master.TranDate,
					tbl_stock_master.TranNo,
					tbl_stock_master.TranRef,
					itminfo_tab.itm_name,
					stock_batch.batch_no,
					tbl_stock_detail.Qty,
					tbl_stock_master.ReceivedRemarks,
					tbl_trans_type.trans_type,
					itminfo_tab.itm_type
				FROM
					tbl_stock_master
				INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
				INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
				INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
				INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
				WHERE
					tbl_stock_master.WHIDFrom = '$wh_id'
				AND tbl_stock_master.WHIDTo = '-1'
				AND stock_batch.batch_no IN ('" . $batchNums . "')
				ORDER BY
					tbl_stock_master.PkStockID DESC";
    $qryRes = mysql_query($qry);
    if (mysql_num_rows(mysql_query($qry)) > 0) {
        ?>

        <h3 style="margin-bottom:0px;">Adjustments</h3>
        <table id="myTable" cellpadding="3">

            <!-- Table heading -->
            <thead>
                <tr>
                    <th width="8%">Date</th>
                    <th>Adjustment No.</th>
                    <th>Product</th>
                    <th>Batch No.</th>
                    <th>Quantity</th>
                    <th>Adjustment Type</th>
                </tr>
            </thead>
            <!-- // Table heading END -->

            <!-- Table body -->
            <tbody>
                <!-- Table row -->
                <?php
                $i = 1;
                while ($row = mysql_fetch_object($qryRes)) :
                    ?>
                    <tr class="gradeX">
                        <td><?php echo date("d/m/y", strtotime($row->TranDate)); ?></td>
                        <td><?php echo $row->TranNo; ?></td>
                        <td><?php echo $row->itm_name; ?></td>
                        <td><?php echo $row->batch_no; ?></td>
                        <td style="text-align:right;"><?php echo number_format(abs($row->Qty)); ?></td>
                        <td><?php echo $row->trans_type; ?></td>
                    </tr>
                    <?php
                    $i++;
                endwhile;
                ?>
                <!-- // Table row END -->
            </tbody>
            <!-- // Table body END -->

        </table>

        <?php
    }
    ?>
        <?php if(!empty($_SESSION['user_name'])){?>
            <div style="font-size:12px; padding-top:3px;"><b>Created By:</b> <?php echo $_SESSION['user_name'].' ('.$_SERVER['SERVER_NAME'].')';?></div>
        <?php }
        
        ?>

</div>
