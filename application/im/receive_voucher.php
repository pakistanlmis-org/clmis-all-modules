<?php
/**
 * new_receive_wh
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
include(PUBLIC_PATH."html/header.php");
//title
$title = "Stock Receive from Warehouse";
//issu number
//echo '<pre>';print_r($_SESSION);exit;
$issue_no = '';
$stockReceive = false;
if (isset($_REQUEST['search']) && !empty($_REQUEST['search'])) {
    //check issue number
    if (isset($_REQUEST['issue_no']) && !empty($_REQUEST['issue_no'])) {
        //get issue number
        $issue_no = $_REQUEST['issue_no'];
    }
    //set issue number
    $objStockMaster->TranNo = $issue_no;
    $objStockMaster->WHIDTo = $_SESSION['user_warehouse'];
     $strSql = "SELECT
                    tbl_stock_detail.Qty,
                    stock_batch.batch_no,
                    itminfo_tab.itm_name,
                    tbl_stock_detail.fkStockID,
                    tbl_stock_detail.PkDetailID,
                    tbl_stock_master.TranDate,
                    tbl_stock_master.CreatedOn
                FROM
                tbl_stock_master
                INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                INNER JOIN itminfo_tab ON itminfo_tab.itm_id = stock_batch.item_id
                WHERE
                tbl_stock_master.TranNo = '" . $issue_no . "' AND
                tbl_stock_master.temp = 0 AND 
		(tbl_stock_detail.IsReceived is NULL or tbl_stock_detail.IsReceived = 0) AND tbl_stock_master.TranTypeID = 2";
        //query result
        //echo $strSql;exit;
        $stockReceive = mysql_query($strSql) or die("Error GetWHStockByIssueNo");
         
}

$types = $objTransType->find_all();
$count = 0;
if(!empty($stockReceive)){
    $count = mysql_num_rows($stockReceive);
}

$q4 = "SELECT  DISTINCT
                                            tbl_stock_master.TranNo,
                                            tbl_stock_detail.IsReceived,
                                            frm.wh_name AS from_wh,
                                            tbl_warehouse.wh_name AS to_wh,
                                            tbl_stock_master.CreatedOn,
                                            tbl_stock_master.CreatedBy,
                                            stakeholder.stkname,
                                            tbl_stock_master.temp,
                                            tbl_stock_master.TranDate
                                    FROM
                                    tbl_stock_master
                                    INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                    INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
                                    INNER JOIN tbl_warehouse AS frm ON tbl_stock_master.WHIDFrom = frm.wh_id
                                    INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                                WHERE
                                        tbl_stock_master.TranTypeID = 2 AND
                                        tbl_stock_detail.IsReceived = 0 AND
                                        tbl_warehouse.dist_id = '".$_SESSION['user_district']."' AND
                                        tbl_warehouse.stkid = '".$_SESSION['user_stakeholder1']."' AND 
                                        tbl_stock_master.WHIDTo <> '".$_SESSION['user_warehouse']."' AND
                                        tbl_stock_master.temp = 0
                                ORDER BY
                                        tbl_stock_master.PkStockID ASC";
//echo $q4;exit;
$getStockIssues = mysql_query($q4) or die("Err Get pending vouchers");


    //chech if record exists
 $issueVoucher = '';
 $a='';
    if (mysql_num_rows($getStockIssues) > 0) {
       
        //fetch results
        while ($resStockIssues = mysql_fetch_assoc($getStockIssues)) {
            $is_new = false;
            if(!empty($resStockIssues['CreatedOn']) && $resStockIssues['CreatedOn']>='2018-10-01') $is_new = true;
            
            $a .= "<tr class=\" ".(($is_new)?'warning':'')." \">";
            $a .= "<td>".$resStockIssues['from_wh']."</td>";
            $a .= "<td>".$resStockIssues['to_wh']."</td>";
            $a .= "<td>".$resStockIssues['stkname']."</td>";
            $a .= "<td>".date('Y-M-d',strtotime($resStockIssues['TranDate']))."</td>";
            $a .= "<td>".date('Y-M-d',strtotime($resStockIssues['CreatedOn']))."</td>";
            $a .= "<td>";
            if($is_new)
                $a .= "<a href=\"../im/receive_voucher.php?issue_no=" . $resStockIssues['TranNo'] . "&search=true\">" . $resStockIssues['TranNo'] . "</a>";
            else    
                $a .= "".$resStockIssues['TranNo']." (OLD)";
            $a .= "</td>";
            $a .= "</tr>";

            
        }
        
    }
    $issueVoucher = $a;
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
<!-- BEGIN HEADER -->
<div class="page-container">
    <?php 
    //include top
    include PUBLIC_PATH."html/top.php";
    //include top_im
    include PUBLIC_PATH."html/top_im.php";?>
    <div class="page-content-wrapper">
        <div class="page-content"> 
            <!-- BEGIN PAGE HEADER-->
            
            <div class="row">
                <div class="col-md-12">
                    <div class="widget" data-toggle="collapse-widget">
                        <div class="widget-head">
                            <h3 class="heading">Pending Vouchers for SDPs in this district </h3>
                        </div>
                        <div class="widget-body">
                            <div class="row">
                                <div class="col-md-12"><?php 
                                if(!empty($issueVoucher) && count($issueVoucher) > 0) {
                                    echo 'Pending Vouchers are : ';
                                    echo '<table class="table table-condensed">';
                                    echo '<tr>';
                                    echo '<th>Issued From</th>';
                                    echo '<th>Issued To</th>';
                                    echo '<th>Stakeholder</th>';
                                    echo '<th>Voucher Issuance Date</th>';
                                    echo '<th>Voucher Created On</th>';
                                    echo '<th>Voucher Number</th>';
                                    echo '</tr>';
                                    echo  $issueVoucher;
                                    echo '</table>';
                                }
                                 ?></div>
                            </div>
                            <form method="POST" name="batch_search" action="" >
                                <!-- Row -->
                                <?php 
                                //get e
                                if(isset($_GET['e'])){?>
                                <span style="padding-left:15px; color:#F00;">Please select at least one product by clicking checkboxes at right</span>
                                <?php }?>
                                <div class="row ">
                                    <div class="col-md-12">
                                        <div class="col-md-3"> 
                                            <!-- Group Receive No-->
                                            <div class="">
                                                <label for="issue_no"> Issue No </label>
                                                <input class="form-control" tabindex="1" id="issue_no" value="<?php echo $issue_no; ?>" name="issue_no" type="text" required />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-medium" style="margin-top: 21px;">
                                                <button type="submit" class="btn btn-primary" name="search" value="Search"> Search </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- // Row END --> 
                    <!-- Widget -->
                    <form name="receive_stock" id="receive_stock" action="receive_voucher_action.php" method="POST">
                        <?php 
                        //check stock receive
                        if ($stockReceive != FALSE) : ?>
                        <div class="widget">
                            <div class="widget-body"> 
                                
                                <!-- Table -->
                                <table class="table table-bordered table-condensed table-striped table-vertical-center checkboxs js-table-sortable">
                                    
                                    <!-- Table heading -->
                                    <thead>
                                        <tr>
                                            <th> Product </th>
                                            <th> Batch </th>
                                            <th> Quantity </th>
                                            <th> Adjusted Qty </th>
                                            <th> Adjustment </th>
                                            <th style="width: 1%;"> <input type="checkbox" id="checkAll" /></th>
                                        </tr>
                                    </thead>
                                    <!-- // Table heading END --> 
                                    
                                    <!-- Table body -->
                                    <tbody>
                                        <!-- Table row -->
                                        <?php
                                            $i = 1;
                                            while ($row = mysql_fetch_object($stockReceive)) :
                                                $stockID = $row->fkStockID;
                                                $tran_date = $row->TranDate;
                                                $created_on = $row->CreatedOn;
                                                ?>
                                        <tr>
                                            <td><?php echo $row->itm_name; ?></td>
                                            <td><?php echo $row->batch_no; ?></td>
                                            <td class="right"><?php echo number_format(abs($row->Qty)); ?>
                                                <input type="hidden" id="<?php echo $i; ?>-qty" value="<?php echo abs($row->Qty); ?>" /></td>
                                            <td class="col-md-3"><input type="text" name="missing[]" id="<?php echo $i; ?>-missing" value="" class="form-control input-sm input-small" /></td>
                                            <td class="col-md-3"><select name="types[]" id="<?php echo $i; ?>-types" class="form-control input-sm input-small">
                                                    <?php
                                                        if (!empty($types)) {
                                                            foreach ($types as $type) {
                                                                echo "<option value=" . $type->trans_id . ">" . $type->trans_type . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                </select></td>
                                            <td class="center"><input type="checkbox" name="stockid[]" value="<?php echo $row->PkDetailID; ?>" /></td>
                                        </tr>
                                        <?php $i++;
                                            endwhile; ?>
                                        <!-- // Table row END -->
                                    </tbody>
                                    <!-- // Table body END -->
                                    
                                </table>
                                <!-- // Table END --> 
                                
                            </div>
                        </div>
                        
                        <!-- Widget -->
                        <div class="widget">
                            <div class="widget-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="control-group">
                                            <label class="control-label" for="remarks"> Remarks </label>
                                            <div class="controls">
                                                <input name="remarks" id="remarks" type="text" class="form-control input-sm input-small" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="control-group">
                                            <label class="control-label" for="rec_ref"> Receive Reference </label>
                                            <div class="controls">
                                                <input name="rec_ref" readonly id="rec_ref" type="text" class="form-control input-sm input-small" value="<?=$issue_no?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="control-group">
                                            <label class="control-label" for="rec_date"> Receive Date </label>
                                            <div class="controls">
                                                <input name="rec_date" class="form-control input-sm input-small" id="rec_date" value="<?php echo date("d/m/Y"); ?>" type="text" readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 right">
                                        <div class="control-group">
                                            <label class="control-label">&nbsp;</label>
                                        </div>
                                        <div class="controls">
                                            <button type="submit" id="save" class="btn btn-primary"> Save </button>
                                            <input type="hidden" name="stock_id" id="stock_id" value="<?php echo $stockID; ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php elseif(!empty($issue_no)): ?>
                        <div class="widget">
                            <div class="widget-body red"> Voucher not found! </div>
                        </div>
                        <?php elseif(isset($_GET['msg']) && !empty($_GET['msg'])): ?>
                        <div class="widget">
                            <div class="widget-body green"> <?php echo $_GET['msg']; ?> </div>
                        </div>
                        <?php endif; ?>
                        <input id="issue_no" value="<?php echo $issue_no; ?>" name="issue_no" type="hidden"/>
                        <input id="count" value="<?php echo $count; ?>" name="count" type="hidden"/>
                    </form>
                </div>
            </div>
            <!-- // Content END --> 
        </div>
    </div>
</div>
<?php 
//include footer
include PUBLIC_PATH."/html/footer.php";?>
<!--<script src="<?php echo PUBLIC_URL; ?>js/dataentry/newreceive_wh.js"></script> -->
<!-- END FOOTER -->

<script>

$(function() {
	$('#checkAll').attr('checked', false);
	$('#checkAll').click(function(e) {
        if ($('#checkAll').is(':checked') )
		{
			$('input[type="checkbox"]').attr('checked', 'checked');
		}
		else
		{
			$('input[type="checkbox"]').attr('checked', false);
		}
    });
    
    
    $('input:checkbox').attr('checked',true);
    
    $("#rec_date").datepicker({
        minDate: "<?=date('d/m/y',strtotime($tran_date))?>",
        maxDate: 0,
        dateFormat: 'dd/mm/yy'
    });
});

</script>
</body>
<!-- END BODY -->
</html>