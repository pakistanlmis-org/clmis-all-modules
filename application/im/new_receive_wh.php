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
    //Get WH Stock By Issue No
    $stockReceive = $objStockMaster->GetWHStockByIssueNo();
}

$types = $objTransType->find_all();
$count = 0;
if(!empty($stockReceive)){
    $count = mysql_num_rows($stockReceive);
}
//echo '<pre>';print_r($_SESSION);exit;


$qry_vouchers= "SELECT DISTINCT tbl_stock_master.TranNo,
                                        tbl_stock_detail.IsReceived
                                FROM
                                        tbl_stock_master
                                
                                INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                WHERE
                                        tbl_stock_master.TranTypeID = 2 AND
                                        tbl_stock_detail.IsReceived = 0 AND
                                        tbl_stock_master.WHIDTo = '".$_SESSION['user_warehouse']."' ";

if($_SESSION['is_allowed_im'] == 1 && !empty($_SESSION['im_start_month']) && $_SESSION['im_start_month'] > '2017-01-01')
$qry_vouchers.= "  AND tbl_stock_master.TranDate > '".$_SESSION['im_start_month']."' ";
                                            
$qry_vouchers.= "               ORDER BY
                                        tbl_stock_master.PkStockID ASC";
$getStockIssues = mysql_query($qry_vouchers) or die("Err GetStockIssueId");


    //chech if record exists
 $issueVoucher = '';
 $a='';
    if (mysql_num_rows($getStockIssues) > 0) {
       
        //fetch results
        while ($resStockIssues = mysql_fetch_assoc($getStockIssues)) {
            $a= " <a href=\"../im/new_receive_wh.php?issue_no=" . $resStockIssues['TranNo'] . "&search=true\">" . $resStockIssues['TranNo'] . "</a>";
            $issueVoucher[ $resStockIssues['TranNo']] = $a;
        }
        
    }
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
                            <h3 class="heading">Stock Receive (From Warehouse)</h3>
                        </div>
                        <div class="widget-body">
                            <div class="row">
                                <div class="col-md-12"><?php 
                                if(!empty($issueVoucher) && count($issueVoucher) > 0) {
                                    echo 'Pending Vouchers are : ';
                                    echo implode(',', $issueVoucher);
                                    echo '<hr>';
                                }
                                 ?></div>
                            </div>
                            <form method="POST" name="batch_search" action="new_receive_wh.php" >
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
                    <form name="receive_stock" id="receive_stock" action="new_receive_wh_action.php" method="POST">
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
                                            <th> Adjustment Type</th>
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
                                                ?>
                                        <tr>
                                            <td><?php echo $row->itm_name; ?></td>
                                            <td><?php echo $row->batch_no; ?></td>
                                            <td class="right"><?php echo number_format(abs($row->Qty)); ?>
                                                <input type="hidden" id="<?php echo $i; ?>-qty" value="<?php echo abs($row->Qty); ?>" /></td>
                                            <td class="col-md-3"><input type="number" name="missing[<?php echo $row->PkDetailID; ?>]" id="<?php echo $i; ?>-missing" value="" min="0" class="form-control input-sm input-small" /></td>
                                            <td class="col-md-3">
                                                <select name="types[<?php echo $row->PkDetailID; ?>]" id="<?php echo $i; ?>-types" class="form-control input-sm input-small types_select">
                                                    <?php
                                                        if (!empty($types)) {
                                                            
                                                            foreach ($types as $type) {
                                                                if($type->trans_id != 20 && $type->trans_id != 24)
                                                                echo "<option value=" . $type->trans_id . ">" . $type->trans_type . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                </select></td>
                                            <td class="center"><input type="checkbox" name="stockid[<?php echo $row->PkDetailID; ?>]" value="<?php echo $row->PkDetailID; ?>" /></td>
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
                                                <input name="rec_date" id="rec_date"  class="form-control input-sm input-small" value="<?php echo date("d/m/Y"); ?>" type="text" readonly />
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
<script src="<?php echo PUBLIC_URL; ?>js/dataentry/newreceive_wh.js"></script> 
<!-- END FOOTER -->

</body>
<!-- END BODY -->
</html>