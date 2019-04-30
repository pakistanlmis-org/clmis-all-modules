<?php
/**
 * printIssue
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

$title = "Stock Issue Voucher";
$print = 1;
//get id
//$stockId = $_GET['id'];
//$stockId = 9732;

$qry = "SELECT
				tbl_stock_master.WHIDFrom,
				tbl_stock_master.CreatedBy
			FROM
				tbl_stock_master
			WHERE
				tbl_stock_master.PkStockID = ".$stockId;
//query result
$qryRes = mysql_fetch_array(mysql_query($qry));
$wh_id = $qryRes['WHIDFrom'];
$userid = $qryRes['CreatedBy'];
//Get Stocks Issue List
$stocks = $objStockMaster->GetStocksIssueList($userid, $wh_id, 2, $stockId);
$receiveArr = array();
//fetching data from stocks
while ($row = mysql_fetch_object($stocks)) {
    //issue_no 
    $issue_no = $row->TranNo;
    //comments
    $comments = $row->ReceivedRemarks;
    //tran_ref
    $tran_ref = $row->TranRef;
    //issue_date
    $issue_date = $row->TranDate;
    //wh_to_id
    $wh_to_id = $row->wh_id;
    //issue_to
    $issue_to = $row->wh_name;
    //issued_by
    $issued_by = $row->issued_by;
    //receiveArr
    $receiveArr[] = $row;
}
// Get district Name
$getDist = "SELECT
			tbl_locations.LocName
		FROM
			tbl_warehouse
		INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
		WHERE
			tbl_warehouse.wh_id = $wh_to_id";
//query result
$rowDist = mysql_fetch_object(mysql_query($getDist));
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

	<style type="text/css" media="print">
    @media print
    {    
        #printButt
        {
            display: none !important;
        }
    }
    </style>
	<?php
            $rptName = 'Stock Issue Voucher';
            //  include('report_header.php');
        
        
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
            
            <p><b><?php echo $rptName;?> as on: <?php echo date('d/M/y');?></b>  </p>
            <p> <b style="float:center;">District: </b><?php echo $rowDist->LocName; ?></p>
            <p> <b style="float:center;">Issue Voucher: </b><a target="_blank" href="<?php echo APP_URL.'im/printIssue.php?id='.$stockId;?>"><?=$issue_no?></a></p>
            <p> <b style="float:center;">Date of Departure: </b><?php echo date("d/M/Y", strtotime($issue_date)); ?></p>
            
            <p> <b style="float:center;">Reference No.: </b><?php echo $tran_ref; ?></p>
            <p> <b style="float:center;">Issue To: </b><?php echo $issue_to;?></p>
            <p> <b style="float:center;">Issue By: </b><?php echo $issued_by;?></p>
            
            
            
        </div>
    </div>
    
    
        
        <table id="myTable" class="table-condensed" cellpadding="3">
            <tr>
                <th width="8%">S. No.</th>
                <th>Product</th>
                <th width="15%">Batch No.</th>
                <th width="15%">Expiry Date</th>
                <th width="15%" align="center">Quantity</th>
                <th width="10%" align="center">Unit</th>
                <th width="15%" align="center">Cartons</th>
            </tr>
            <tbody>
                <?php
                $i = 1;
				$totalQty = 0;
				$totalCartons = 0;
				$product = '';
                //check receiveArr
                                if (!empty($receiveArr)) {
                    foreach ($receiveArr as $val) {
						if ( $val->itm_name != $product && $i > 1 )
						{
						?>
                        <tr>
                            <th colspan="4" style="text-align:right;">Total</th>
                            <th style="text-align:right;"><?php echo number_format($totalQty);?></th>
                            <th>&nbsp;</th>
                            <th style="text-align:right;"><?php echo number_format($totalCartons);?></th>
                        </tr>
                        <?php
							$totalQty = abs($val->Qty);
							$totalCartons = abs($val->Qty) / $val->qty_carton;
						}
						else
						{	
							$totalQty += abs($val->Qty);
							$totalCartons += abs($val->Qty) / $val->qty_carton;
						}
						$product = $val->itm_name;
                        ?>
                        <tr>
                            <td style="text-align:center;"><?php echo $i++; ?></td>
                            <td><?php echo $val->itm_name; ?></td>
                            <td><?php echo $val->batch_no; ?></td>
                            <td style="text-align:center;"> <?php echo date("d/M/Y", strtotime($val->batch_expiry)); ?></td>
                            <td style="text-align:right;"><?php echo number_format(abs($val->Qty)); ?></td>
                            <td style="text-align:center;"><?php echo $val->UnitType; ?></td>
                            <td style="text-align:right;"><?php echo number_format(abs($val->Qty) / $val->qty_carton); ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr>
                    <th colspan="4" style="text-align:right;">Total</th>
                    <th style="text-align:right;"><?php echo number_format($totalQty);?></th>
                    <th>&nbsp;</th>
                    <th style="text-align:right;"><?php echo number_format($totalCartons);?></th>
                </tr>
            </tbody>
        </table>
        <?php if(!empty($comments)){?>
        <div style="font-size:12px; padding-top:3px;"><b>Comments:</b> <?php echo $comments;?></div>
        <?php }?>
        <?php if(!empty($_SESSION['user_name'])){?>
        <div style="font-size:12px; padding-top:3px;"><b>Created By:</b> <?php echo $_SESSION['user_name'].' ('.$_SERVER['SERVER_NAME'].')';?></div>
        <?php }?>
       
        
       
    </div>
