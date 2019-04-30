<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$wh_id = $_SESSION['user_warehouse'];
$stk_id = $_SESSION['user_stakeholder1'];

 $category = '1';
    if($_SESSION['user_stakeholder1'] == 145) $category='5';


if ( isset($_REQUEST['wh_id'])) {
    $whTo = mysql_real_escape_string($_REQUEST['wh_id']);
    
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
    //    echo $qry; exit;
    $qryRes = mysql_fetch_array(mysql_query($qry));
    $distId = $qryRes['dist_id'];
    $provId = $qryRes['prov_id'];
    $distName = $qryRes['LocName'];
    $mainStk = $qryRes['MainStk'];
   
   
     $qry = "SELECT
            itminfo_tab.itm_id,
            itminfo_tab.itmrec_id,
            itminfo_tab.itm_name
        FROM
            itminfo_tab
        INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
        WHERE
            itminfo_tab.itm_category = $category AND
            stakeholder_item.stkid = $stk_id
        ";
        
    if(!empty($_SESSION['user_province1']) && $_SESSION['user_province1']==1 && !empty($_SESSION['user_stakeholder1']) && $_SESSION['user_stakeholder1']==1)
    {

        $qry .= "   and  itminfo_tab.itm_id NOT in (34,30,81) ";
    }

    $qry .= "ORDER BY
            itminfo_tab.frmindex ASC
    ";
    //query result
    $qryRes = mysql_query($qry);
    $batchno = '';
    while ($row = mysql_fetch_array($qryRes)) {
        $item_id[] = $row['itm_id'];
        $product[$row['itm_id']] = $row['itm_name'];
    }
}
?>

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
                                <h3 class="heading">Stock Issuance Form </h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="<?php echo APP_URL ?>im/issue_to_wh_action.php" method="post" onSubmit="return formValidation()">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label" for="issueref">Issue Reference</label>
                                                    <div class="controls">
                                                        <input class="form-control input-medium" id="issue_ref" name="issue_ref" value=""/>
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
                                                            <th width="13%">Approved / Suggested Qty</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $count = 1;
                                                            foreach ($product as $proId => $proName) {
                                                                $this_est_qty = $_REQUEST['itm_'.$proId];
                                                                $this_est_qty_remaining = $this_est_qty;
                                                                
                                                                $this_issuance = 0;
                                                                ?>
                                                                <tr>
                                                                    <td class="center"><?php echo $count++; ?></td>
                                                                    <td><span id="<?php echo $proId ?>"><?php echo $proName; ?></span>
                                                                        <input type="hidden" name="product[<?php echo $proId ?>]" id="product" value="<?php echo $proId ?>" />
                                                                        <input type="hidden" name="itmrec[<?php echo $proId ?>]" id="itmrec" value="" /></td>
                                                                    <td class="right"> </td>
                                                                    <td><table  width="100%" class="table-condensed" id="myTable">
                                                                            <tbody>
                                                                                <?php
                                                                                if (true) {
                                                                                    
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
                                                                                                LEFT JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                                                                                                WHERE
                                                                                                        stock_batch.Qty <> 0 AND
                                                                                                        stock_batch.`status` = 'Running' AND 
                                                                                                        stock_batch.item_id = $proId AND
                                                                                                        stock_batch.wh_id = $wh_id AND
                                                                                                        tbl_stock_detail.temp = 0
                                                                                                        
                                                                                                GROUP BY
                                                                                                        stock_batch.batch_no
                                                                                                ORDER BY
                                                                                                        stock_batch.batch_expiry ASC,
                                                                                                        tbl_warehouse.wh_name,
                                                                                                        stock_batch.batch_no";

                                                                                    //query result
                                                                                     //echo $strSql;exit;
                                                                                    $rsSql = mysql_query($strSql) or die("Error: GetAllRunningBatches");
                                                                                    $num = mysql_num_rows($rsSql);
                                                                                    while ($resStockIssues = mysql_fetch_assoc($rsSql)) {
                                                                                        $avail = $resStockIssues['Qty'];
                                                                                        
                                                                                        $issue_from_this_batch = 0;
                                                                                        
                                                                                        if($this_est_qty_remaining <= 0){
                                                                                            $issue_from_this_batch = 0;
                                                                                        }
                                                                                        elseif( $this_est_qty_remaining > $avail)
                                                                                        {
                                                                                            $issue_from_this_batch = $avail;
                                                                                        }
                                                                                        elseif( $this_est_qty_remaining <= $avail)
                                                                                        {
                                                                                            $issue_from_this_batch = $this_est_qty_remaining;
                                                                                        }
                                                                                        
                                                                                        $this_est_qty_remaining -= $issue_from_this_batch;
                                                                                        $this_issuance+=$issue_from_this_batch;
                                                                                        ?>
                                                                                        <tr>
                                                                                            <td width="15%"><?php echo $resStockIssues['batch_no']; ?></td>
                                                                                            <td width="30%"><?php echo $resStockIssues['wh_name']; ?></td>
                                                                                            <td width="15%"><?php echo date('d/m/Y', strtotime($resStockIssues['batch_expiry'])); ?></td>
                                                                                            <td width="15%"><input class="form-control input-small input-sm" type="text" value="<?php echo number_format($avail) ?>" disabled style="text-align:right;"/></td>
                                                                                            <td width="15%" align="right"><input value="<?=$issue_from_this_batch?>" autocomplete="off" max="<?php echo $avail; ?>" class="qty form-control input-small input-sm" style="text-align:right" type="text" name="qty_issued[<?php echo $proId . "|" . $resStockIssues['batch_id']; ?>]" id="<?php echo $resStockIssues['batch_id'] . "-" . $proId; ?>-qty_issued" /></td>
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
                                                                                        <td <?=$td1_style?> align="right"><b>Total Issuance :</b></td>
                                                                                        <td <?=$td2_style?> align="right"><input type="text" readonly class="issued_qty form-control input-small input-sm" id="<?php echo $proId ?>-total_issued" value="<?=$this_issuance?>" /></td>
                                                                                    </tr>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </tbody>
                                                                        </table></td>
                                                                    <td><input class="form-control input-small input-sm" type="text" name="approved[<?php echo $proId ?>]" id="<?php echo $proId; ?>-approved" value="<?=$_REQUEST['itm_'.$proId]?>" style="text-align:right;" readonly /></td>
                                                                </tr>
                                                                
                                                                <?php
                                                            }
                                                            ?>
                                                                
                                                                <tr id="">
                                                                    <td colspan="3">Comments:</td>
                                                                    <td colspan="5" style=" border:none; padding-top:10px;">                                                        
                                                                        <textarea id="comments" name="comments" maxlength="290" rows="3" cols="60"></textarea>
                                                                    </td>
                                                                </tr>
                                                                
                                                            <?php
                                                            if (true) {
                                                                ?>
                                                                <tr id="doNotPrint">
                                                                    <td colspan="5" style="text-align:right; border:none; padding-top:10px;">                                                        
                                                                        <button type="submit" id="submit" name="submit" class="btn btn-primary"   > Issue </button>
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
                                    
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12 form-group">
                                                <label class="control-label">
                                                        <span class="note note-info">Formula for calculating estimated demand : (4*AMC - Current SOH - Already issued in Current Month)</span>
                                                </label>

                                        </div>
                                    </div>
                                </div>
                                    
                                    <input type="hidden" name="warehouse" id="warehouse" value="<?php echo $_REQUEST['wh_id'] ?>"/>
                                    <input type="hidden" name="issue_date" id="issue_date" value="<?php echo date("d/m/Y") ?>"/>
                                    <input type="hidden" name="trans_no" id="trans_no" value="-1"/>
                                    <input type="hidden" name="stock_id" id="stock_id" value="0"/>
                                    
                                    <input type="hidden" name="ref_page" value="<?php echo (!empty($_REQUEST['ref_page'])?$_REQUEST['ref_page']:'') ?>"/>
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
                                                                         $("form").submit(function() {
                                                                                    // submit more than once return false
                                                                                    $(this).submit(function() {
                                                                                            return false;
                                                                                    });
                                                                                    // submit once return true
                                                                                    return true;
                                                                            });
//                                                                        $('#submit').click(function(e){
//                                                                            $('#submit').attr('disabled', true);
//                                                                            $('#submit').val('Submitting...');
//                                                                        });
                                                                        
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