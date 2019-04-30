<?php
/**
 * issue
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
$wh_id = $_SESSION['user_warehouse'];
if (isset($_REQUEST['id']) && isset($_REQUEST['wh_id'])) {
    //to warehouse
    $whTo = mysql_real_escape_string($_REQUEST['wh_id']);
    //get id 
    $id = mysql_real_escape_string($_REQUEST['id']);
    //select query
    //district id
    //province id
    //stakeholder id
    //location name
    //main stakeholder 
    $qry = "SELECT
				tbl_warehouse.dist_id,
				tbl_warehouse.prov_id,
				tbl_warehouse.stkid,
				tbl_locations.LocName,
				MainStk.stkname AS MainStk
			FROM
			tbl_warehouse
			INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
			WHERE
			tbl_warehouse.wh_id = " . $whTo;
    //query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //district id
    $distId = $qryRes['dist_id'];
    //province id
    $provId = $qryRes['prov_id'];
    //stakeholder
    $stkid = $qryRes['stkid'];
    //district name
    $distName = $qryRes['LocName'];
    //main stakeholder
    $mainStk = $qryRes['MainStk'];
    //select query
    //gets
    //requisition num,
    //date from,
    //date to,
    //replenishment,
    //stock master_id,
    //requested on,
    //item id,
    //itmrec id,
    //item name,
    //desired stock,
    //approve qty,
    //approval status,
    //available qty
     $qry = "SELECT
				clr_master.requisition_num,
				clr_master.date_from,
				clr_master.date_to,
				clr_details.replenishment,
				clr_details.stock_master_id,
				DATE_FORMAT(clr_master.requested_on, '%d/%m/%Y') AS requested_on,
				itminfo_tab.itm_id,
				itminfo_tab.itmrec_id,
				itminfo_tab.itm_name,
				clr_details.desired_stock,
				clr_details.approve_qty,
				clr_details.approval_status,
				clr_details.available_qty,
                                tbl_warehouse.dist_id,
                                tbl_warehouse.prov_id,
                                tbl_warehouse.stkid
			FROM
				clr_master
			INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
			INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
                        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
			WHERE
				clr_master.pk_id = " . $id . "
			AND	(clr_details.approval_status = 'Approved' OR clr_details.approval_status = 'Issued')
			AND	clr_details.approve_qty > 0
			ORDER BY
				itminfo_tab.frmindex ASC";
    //query result
//     echo $qry;exit;
    $qryRes = mysql_query($qry);
    $batchno = '';
    while ($row = mysql_fetch_array($qryRes)) {
        //requisition num
        $requisitionNum = $row['requisition_num'];
        $req_stkid      = $row['stkid'];
        $req_prov       = $row['prov_id'];
        
        //date from
        $dateFrom = date('M-Y', strtotime($row['date_from']));
        //date to
        $dateTo = date('M-Y', strtotime($row['date_to']));
        //requested on
        $requestedOn = $row['requested_on'];
        //item id
        $item_id[] = $row['itm_id'];
        //product
        $product[$row['itm_id']] = $row['itm_name'];
        //stock master id
        $stock_master_id[$row['itm_id']] = $row['stock_master_id'];
        //desired stock
        $desiredStock[$row['itm_id']] = $row['replenishment'];
        //item rec id
        $itemrec_id[$row['itm_id']] = $row['itm_id'];
        //approved
        $approved[$row['itm_id']] = $row['approve_qty'];
        //avaialable
        $available[$row['itm_id']] = $row['available_qty'];
        //status
        $status[$row['itm_id']] = $row['approval_status'];
        //app status
        $appStatus[] = $row['approval_status'];
    }
    $duration = $dateFrom . ' to ' . $dateTo;
}


$qry_f = "SELECT
            funding_stk_prov.funding_source_id
            FROM
            funding_stk_prov
            WHERE
            
                funding_stk_prov.stakeholder_id = ".$req_stkid." AND
                funding_stk_prov.province_id = ".$req_prov."";
$res_f = mysql_query($qry_f);
$funding_stks=array();
while($row_f=mysql_fetch_assoc($res_f))
{
    $funding_stks[$row_f['funding_source_id']]=$row_f['funding_source_id'];
}

$funding_stks[6891]=6891;
?>

<script>
    function printContents() {
        var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes, left=100, top=25";
        var printingContents = document.getElementById("printing").innerHTML;

        var docprint = window.open("", "", printing);
        
        
        docprint.document.open();
        docprint.document.write('<html><head><title>Stock Issuance Form</title>');
        
        //setting up CSS for the watermark
                       
        docprint.document.write('</head><body onLoad="self.print();"><center>');
        docprint.document.write(printingContents);
        
        docprint.document.write('</center>');
        
        docprint.document.write('</body></html>');
        docprint.document.close();
        docprint.focus();   
    }
</script>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">

    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" id="printing">
                            <style>
                                
                                table#myTable2 {
                                    margin-top: 20px;
                                    border-collapse: collapse;
                                    border-spacing: 0;
                                }

                                /* Print styles */
                                @media only print {
                                    table#myTable2 {
                                        
                                        padding-left: 2 !important;
                                        text-align: left;
                                        border: 1px solid #999;
                                    }

                                    #doNotPrint {
                                        display: none !important;
                                    }
                                }
                            </style>
                            <div class="widget-head">
                                <h3 class="heading">Stock Issuance Form [Requisition No.: <?php echo $_GET['rq']; ?>, Requisition Period: <?php echo $dateFrom . ' to ' . $dateTo . ', Store: ' . $mainStk . ' ' . $distName; ?>]</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="<?php echo APP_URL ?>im/clr6_new_issue_action.php" method="post" onSubmit="return formValidation()">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="issueref">Issue Reference</label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" id="issue_ref" name="issue_ref" value="<?php echo $requisitionNum ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="issuedby">Issued By</label>
                                                    <div class="controls">
                                                        <select class="form-control input-medium" name="issued_by" id="issued_by">
                                                            <option value="">Select</option>
                                                            <?php
//select query
                                                            //gets
                                                            $qry = "SELECT
                                                                list_detail.pk_id,
                                                                list_detail.list_value
                                                            FROM
                                                                list_detail
                                                            WHERE
                                                                list_detail.list_master_id = 21
                                                            ORDER BY
                                                            list_detail.list_value ASC";
//query result
                                                            $qryRes = mysql_query($qry);
                                                            while ($row = mysql_fetch_array($qryRes)) {
                                                                echo "<option value=\"$row[pk_id]\" $sel>$row[list_value]</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                        <?php ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls right">
                                                        <a class="btn btn-default green" href="batch_management.php" target="_blank">Open Batch Management</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <table id="myTable2" class="table table-striped table-bordered table-condensed">
                                                    <?php
                                                    if (mysql_num_rows($qryRes) > 0) {
                                                        ?>
                                                        <thead>
                                                            <tr>
                                                                <th width="5%" style="text-align:center;">S. No.</th>
                                                                <th width="7%">Product</th>
                                                                <th width="10%">Requested Qty</th>
                                                                <th width="55%"> 
                                                                    <table width="100%" class="table table-condensed" id="myTable">
                                                                        <thead>
                                                                            <tr>
                                                                                <th width="12%">Batch No</th>
                                                                                <th width="25%">Funding Source</th>
                                                                                <th width="15%" style="text-align:left">Expiry</th>
                                                                                <th width="20%" style="text-align:left">Available Qty</th>
                                                                                <th width="15%" style="text-align:left">Issue Qty</th>
                                                                            </tr>
                                                                        </thead>
                                                                    </table>
                                                                </th>
                                                            <th width="13%">Approved Qty</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $count = 1;
                                                            foreach ($product as $proId => $proName) {
                                                                ?>
                                                                <tr>
                                                                    <td class="center"><?php echo $count++; ?></td>
                                                                    <td><span id="<?php echo $proId ?>"><?php echo $proName; ?></span>
                                                                        <input type="hidden" name="product[<?php echo $proId ?>]" id="product" value="<?php echo $proId ?>" />
                                                                        <input type="hidden" name="itmrec[<?php echo $proId ?>]" id="itmrec" value="<?php echo $itemrec_id[$proId] ?>" /></td>
                                                                    <td class="right"><?php echo number_format($desiredStock[$proId]); ?></td>
                                                                    <td><table  width="100%" class="table-condensed" id="myTable">
                                                                            <tbody>
                                                                                <?php
                                                                                if ($status[$proId] == 'Approved') {
                                                                                    //select query
                                                                                    //gets
                                                                                    //batch number
                                                                                    //batch id
                                                                                    //batch expiry
                                                                                    //qty
                                                                                     $strSql = "SELECT
                                                                                                        stock_batch.batch_no,
                                                                                                        stock_batch.batch_id,
                                                                                                        stock_batch.batch_expiry,
                                                                                                        stock_batch.item_id,
                                                                                                        SUM(tbl_stock_detail.Qty) as Qty,
                                                                                                        stock_batch.funding_source,
                                                                                                        tbl_warehouse.wh_name
                                                                                                FROM
                                                                                                        stock_batch
                                                                                                INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                                                                                                INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                                                                                                WHERE
                                                                                                        stock_batch.Qty <> 0 AND
                                                                                                        stock_batch.`status` = 'Running' AND 
                                                                                                        stock_batch.item_id = $proId AND
                                                                                                        stock_batch.wh_id = $wh_id AND
                                                                                                        tbl_stock_detail.temp = 0
                                                                                                        AND
                                                                                                        stock_batch.funding_source in (
                                                                                                             ".implode(',',$funding_stks)."
                                                                                                            

                                                                                                        )
                                                                                                GROUP BY
                                                                                                        stock_batch.batch_no
                                                                                                ORDER BY
                                                                                                        stock_batch.batch_expiry ASC,
                                                                                                        tbl_warehouse.wh_name,
                                                                                                        stock_batch.batch_no";

                                                                                    //query result
                                                                                    // echo $strSql;exit;
                                                                                    $rsSql = mysql_query($strSql) or die("Error: GetAllRunningBatches here");
                                                                                    $num = mysql_num_rows($rsSql);
                                                                                    while ($resStockIssues = mysql_fetch_assoc($rsSql)) {
                                                                                        $avail = $resStockIssues['Qty'];
                                                                                        ?>
                                                                                        <tr>
                                                                                            <td width="15%"><?php echo $resStockIssues['batch_no']; ?></td>
                                                                                            <td width="30%"><?php echo $resStockIssues['wh_name']; ?></td>
                                                                                            <td width="15%"><?php echo date('d/m/Y', strtotime($resStockIssues['batch_expiry'])); ?></td>
                                                                                            <td width="15%"><input class="form-control input-small input-sm" type="text" value="<?php echo number_format($avail) ?>" disabled style="text-align:right;"/></td>
                                                                                            <td width="15%" align="right"><input autocomplete="off" max="<?php echo $avail; ?>" class="qty form-control input-small input-sm" style="text-align:right" type="text" name="qty_issued[<?php echo $proId . "|" . $resStockIssues['batch_id']; ?>]" id="<?php echo $resStockIssues['batch_id'] . "-" . $proId; ?>-qty_issued" /></td>
                                                                                        </tr>
                                                                                        <?php
                                                                                    }
                                                                                    if ($num == 1) {
                                                                                        $style = 'style="display:none;"';
                                                                                    } else {
                                                                                        $style = 'style="display:table-row;"';
                                                                                    }
                                                                                    
                                                                                    
                                                                                    if ($num >= 1) {
                                                                                       
                                                                                        $td1_style=' colspan="3" ';
                                                                                        $td2_style='  ';
                                                                                    } else {
                                                                                         $td1_style=' width="75%" ';
                                                                                         $td2_style=' width="25%" ';
                                                                                    }
                                                                                    ?>
                                                                                    <tr <?php echo $style; ?>>
                                                                                        <td <?=$td1_style?> align="right"><b>Total Issued :</b></td>
                                                                                        <td <?=$td2_style?> align="right"><input type="text" readonly class="issued_qty form-control input-small input-sm" id="<?php echo $proId ?>-total_issued" value="0" /></td>
                                                                                    </tr>
                                                                                    <?php
                                                                                } else {
                                                                                    //select query
                                                                                    //gets
                                                                                    $strSql = "SELECT
                                                                                                            stock_batch.batch_no,
                                                                                                            stock_batch.batch_id,
                                                                                                            stock_batch.batch_expiry,
                                                                                                            stock_batch.item_id,
                                                                                                            SUM(ABS(tbl_stock_detail.Qty)) AS issue_qty,
                                                                                                            stock_batch.Qty AS batch_qty
                                                                                                    FROM
                                                                                                            stock_batch
                                                                                                    INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                                                                                                    WHERE
                                                                                                            stock_batch.item_id = " . $proId . "
                                                                                                    AND stock_batch.wh_id = $wh_id
                                                                                                    AND tbl_stock_detail.temp = 0
                                                                                                    AND tbl_stock_detail.fkStockID = " . $stock_master_id[$proId] . "
                                                                                                    GROUP BY
                                                                                                            stock_batch.batch_no
                                                                                                    ORDER BY
                                                                                                            stock_batch.batch_expiry ASC,
                                                                                                            stock_batch.batch_no";

                                                                                    //query result
                                                                                    //echo $strSql;exit;
                                                                                    $rsSql = mysql_query($strSql) or die("Error: GetAllRunningBatches");
                                                                                    $num = mysql_num_rows($rsSql);
                                                                                    $totalIssued = 0;
                                                                                    while ($resStockIssues = mysql_fetch_assoc($rsSql)) {
                                                                                        $batch_qty = $resStockIssues['batch_qty'];
                                                                                        $issue_qty = $resStockIssues['issue_qty'];
                                                                                        $totalIssued += $resStockIssues['issue_qty'];
                                                                                        ?>
                                                                                        <tr>
                                                                                            <td width="25%"><?php echo $resStockIssues['batch_no']; ?></td>
                                                                                            <td width="25%"><?php echo date('d/M/Y', strtotime($resStockIssues['batch_expiry'])); ?></td>
                                                                                            <td width="25%"><input class="form-control input-small input-sm" type="text" value="<?php echo number_format($batch_qty) ?>" disabled style="text-align:right;"/></td>
                                                                                            <td width="25%" align="right"><?php echo '<span style="padding-right:10px;">' . number_format($issue_qty) . '</span>'; ?></td>
                                                                                        </tr>
                                                                                        <?php
                                                                                    }
                                                                                    if ($num == 1) {
                                                                                        $style = 'style="display:none;"';
                                                                                    } else {
                                                                                        $style = 'style="display:table-row;"';
                                                                                    }
                                                                                    
                                                                                    
                                                                                    if ($num >= 1) {
                                                                                       
                                                                                        $td1_style=' colspan="3" ';
                                                                                        $td2_style='  ';
                                                                                    } else {
                                                                                        
                                                                                         $td1_style=' width="75%" ';
                                                                                         $td2_style=' width="25%" ';
                                                                                    }
                                                                                    
                                                                                    ?>
                                                                                    <tr <?php echo $style; ?>>
                                                                                        <td <?=$td1_style?>  align="right"><b>Total Issued</b></td>
                                                                                        <td <?=$td2_style?> align="right"><input type="text" readonly class="issued_qty form-control input-small input-sm" value="<?php echo ($status[$proId] == 'Issued') ? number_format($totalIssued) : '0'; ?>" /></td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </tbody>
                                                                        </table></td>
                                                                    <td><input class="form-control input-small input-sm" type="text" name="approved[<?php echo $proId ?>]" id="<?php echo $proId; ?>-approved" value="<?php echo number_format($approved[$proId]); ?>" style="text-align:right;" readonly /></td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            if (in_array('Approved', $appStatus)) {
                                                                ?>
                                                                <tr id="doNotPrint">
                                                                    <td colspan="5" style="text-align:right; border:none; padding-top:10px;">                                                        
                                                                        <button type="submit" id="submit" name="submit" class="btn btn-primary"> Issue </button>
                                                                        <button type="button" onClick="javascript: history.go(-1)" class="btn btn-warning"> Cancel </button>
                                                                        <a class="btn btn-warning"  onClick="printContents()" href="javascript:void(0);">Print</a>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <tr>
                                                                <td colspan="7" style="text-align:Center;font-size:14px; border:none; padding-top:10px;"> No Approved Items to Issue. </td>
                                                            </tr>
                                                        <?php }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="warehouse" id="warehouse" value="<?php echo $_REQUEST['wh_id'] ?>"/>
                                    <input type="hidden" name="issue_date" id="issue_date" value="<?php echo date("d/m/Y") ?>"/>
                                    <input type="hidden" name="trans_no" id="trans_no" value="-1"/>
                                    <input type="hidden" name="stock_id" id="stock_id" value="0"/>
                                    <input type="hidden" name="clr6_id" id="clr6_id" value="<?php echo $_REQUEST['id'] ?>"/>
                                    <input type="hidden" name="rq_no" value="<?php echo $requisitionNum ?>"/>
                                    
                                    <input type="hidden" name="ref_page" value="<?php echo (!empty($_REQUEST['ref_page'])?$_REQUEST['ref_page']:'') ?>"/>
                                    <input type="hidden" name="plan_id" value="<?php echo (!empty($_REQUEST['plan_id'])?$_REQUEST['plan_id']:'') ?>"/>
                                    <input type="hidden" name="plan_num" value="<?php echo (!empty($_REQUEST['plan_num'])?$_REQUEST['plan_num']:'') ?>"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- END FOOTER -->
        <?php include PUBLIC_PATH . "/html/footer.php"; ?>
        <script src="<?php echo PUBLIC_URL; ?>js/dataentry/clr6issue.js"></script> 
        <script>
                                                                    function openPopUp(pageURL)
                                                                    {
                                                                        var w = screen.width;
                                                                        var h = screen.height;
                                                                        var left = 0;
                                                                        var top = 0;

                                                                        return window.open(pageURL, 'Requisition Approved', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
                                                                    }
                                                                    $(function() {
                                                                        $('.qty').priceFormat({
                                                                            prefix: '',
                                                                            thousandsSeparator: ',',
                                                                            suffix: '',
                                                                            centsLimit: 0,
                                                                            limit: 10,
                                                                            clearOnEmpty: true
                                                                        });

                                                                        $("input[id$='-qty_issued']").keyup(function(e) {
                                                                            var arr = $(this).attr('id').split('-');
                                                                            var proId = arr[1];
                                                                            var sum = 0;
                                                                            $("input[id$='" + proId + "-qty_issued']").each(function(index, element) {
                                                                                var qty = $(this).val().replace(/\,/g, '');
                                                                                if (qty > 0)
                                                                                {
                                                                                    sum += parseFloat(qty);
                                                                                }
                                                                            });
                                                                            $('#' + proId + '-total_issued').val(sum).priceFormat({
                                                                                prefix: '',
                                                                                thousandsSeparator: ',',
                                                                                suffix: '',
                                                                                centsLimit: 0,
                                                                                limit: 10,
                                                                                clearOnEmpty: true
                                                                            });
                                                                        });
                                                                    })

                                                                    function formValidation()
                                                                    {
                                                                        var q = 0;
                                                                        var inp = $('.qty');
                                                                        for (var i = 0; i < inp.length; i++) {
                                                                            if (inp[i].value != '') {
                                                                                q++;
                                                                                var qtyValue = inp[i].value;
                                                                                qtyValue = parseInt(qtyValue.replace(/\,/g, ''));
                                                                                if (qtyValue == 0)
                                                                                {
                                                                                    alert('Quantity can not be 0');
                                                                                    inp[i].focus();
                                                                                    return false;
                                                                                }
                                                                                else if (qtyValue > parseInt(inp[i].getAttribute('max'))) {
                                                                                    alert('Quantity can not be greater than ' + inp[i].getAttribute('max'));
                                                                                    inp[i].focus();
                                                                                    return false;
                                                                                }
                                                                            }
                                                                        }

                                                                        if (q == 0) {
                                                                            alert('Please enter at least one quantity to issue');
                                                                            return false;
                                                                        }
                                                                        var flag = true;
                                                                        var errMsg = '';
                                                                        $("input[id$='-total_issued']").each(function(index, element) {
                                                                            var issuedQty = $(this).val().replace(/\,/g, '');
                                                                            var arr = $(this).attr('id').split('-');
                                                                            var proId = arr[0];
                                                                            var approvedQty = $('#' + proId + '-approved').val().replace(/\,/g, '');


                                                                            if (parseInt(issuedQty) > 0 && parseInt(approvedQty) != parseInt(issuedQty))
                                                                            {
                                                                                //this part allows to issue quantity only equal to approved qty.
                                                                                //disabling this part , to allow changes in issuance , as per requirement change as on 26 may 2017...
                                                                                //errMsg += 'Total issued quantity must be equal to approved quantity for ' + $('#' + proId).html() + '\n';
                                                                                //flag = false;
                                                                            }
                                                                        });
                                                                        if (errMsg.length > 0) {
                                                                            alert(errMsg);
                                                                        }
                                                                        return flag;

                                                                        $('#submit').attr('disabled', true);
                                                                        $('#submit').val('Submitting...');
                                                                    }
        </script> 
        <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>