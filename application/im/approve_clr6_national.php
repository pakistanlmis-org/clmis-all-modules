<?php
/**
 * approve_clr6_prov
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
//tytle
$title = "New Issue";
//echo '<pre>';print_r($_SESSION);exit;
if (isset($_REQUEST['id']) && isset($_REQUEST['wh_id'])) {
    //to warwhouse
    $whTo = mysql_real_escape_string($_REQUEST['wh_id']);
    //get id
    $id = mysql_real_escape_string($_REQUEST['id']);
    //select query
    //gets
    //dist id
    //prov id
    //stk id
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
    //dist id
    $distId = $qryRes['dist_id'];
    //prov id
    $provId = $qryRes['prov_id'];
    //stk id
    $stkid = $qryRes['stkid'];
    //dist name
    $distName = $qryRes['LocName'];
    //main stk
    $mainStk = $qryRes['MainStk'];
//select query
    //gets
    //clr_master.requisition_num,
    //date_from,
    //date_to,
    //replenishment,
    //requested_on,
    //itm_id,
    //itmrec_id,
    //itm_name,
    //desired_stock,
    //batch_no,
    //approve_qty,
    //approval_status,
    //available_qty,
    //masterStatus
    $qry = "SELECT
                    clr_master.requisition_num,
                    clr_master.date_from,
                    clr_master.date_to,
                    clr_details.replenishment,
                    
                    clr_details.qty_req_dist_lvl1,
                    clr_details.remarks_dist_lvl1,
                    clr_details.qty_req_dist_lvl2,
                    clr_details.remarks_dist_lvl2,
                    clr_details.qty_req_prov,
                    clr_details.remarks_prov,
                    clr_details.qty_req_central,
                    clr_details.remarks_central,
                    
                    DATE_FORMAT(clr_master.requested_on,'%d/%m/%Y') AS requested_on,
                    itminfo_tab.itm_id,
                    itminfo_tab.itmrec_id,
                    itminfo_tab.itm_name,
                    clr_details.desired_stock,
                    stock_batch.batch_no,
                    clr_details.approve_qty,
                    clr_details.approval_status,
                    SUM(stock_batch.Qty) AS available_qty,
                    clr_master.approval_status AS masterStatus
            FROM
                    clr_master
            INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
            INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
            LEFT JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
            WHERE
                    clr_master.pk_id = $id
            GROUP BY
                    itminfo_tab.itmrec_id
            ORDER BY
                    itminfo_tab.frmindex ASC";
    //query result
    $qryRes = mysql_query($qry);
    //batch number
    $batchno = '';
    //fetch result
    while ($row = mysql_fetch_array($qryRes)) {
        //requisitionNum 
        $requisitionNum = $row['requisition_num'];
        //date from 
        $dateFrom = date('M-Y', strtotime($row['date_from']));
        //date to
        $dateTo = date('M-Y', strtotime($row['date_to']));
        //requested on
        $requestedOn = $row['requested_on'];
        //item id
        $item_id[] = $row['itm_id'];
        //batch num
        $batchno[$row['itm_id']] = $row['batch_no'];
        //product
        $product[$row['itm_id']] = $row['itm_name'];
        //replenishment
        $replenishment[$row['itm_id']] = $row['replenishment'];
        
        ///new qty and remarks cols
        $qty_req_dist_lvl1[$row['itm_id']] = $row['qty_req_dist_lvl1'];
        $qty_req_dist_lvl2[$row['itm_id']] = $row['qty_req_dist_lvl2'];
        $qty_req_prov[$row['itm_id']]      = $row['qty_req_prov'];
        $qty_req_central[$row['itm_id']]   = $row['qty_req_central'];
        $remarks_dist_lvl1[$row['itm_id']] = $row['remarks_dist_lvl1'];
        $remarks_dist_lvl2[$row['itm_id']] = $row['remarks_dist_lvl2'];
        $remarks_prov[$row['itm_id']]      = $row['remarks_prov'];
        $remarks_central[$row['itm_id']]   = $row['remarks_central'];
        
        
        //desiredStock
        $desiredStock[$row['itm_id']] = $row['desired_stock'];
        //itemrec_id
        $itemrec_id[$row['itm_id']] = $row['itm_id'];
        //approved
        $approved[$row['itm_id']] = $row['approve_qty'];
        //status
        $status[$row['itm_id']] = $row['approval_status'];
        //availableQty
        $availableQty[$row['itm_id']] = $row['available_qty'];
        //masterStatus 
        $masterStatus = $row['masterStatus'];
    }
    $duration = $dateFrom . ' to ' . $dateTo;
}
//echo '<pre>';
//print_r($availableQty);
//print_r($replenishment);
//exit;
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 
                <div class="row">
                    <div class="col-md-12">

                        <div class="widget" id="printing">

                            <?php include PUBLIC_PATH . "html/top_im.php"; ?>
                            <style type="text/css" media="print">
                                @media print
                                {    
                                    #printButt
                                    {
                                        display: none !important;
                                    }
                                }
                            </style>
                            <div class="">
                                <h3 class="heading center">Central Requisition Approval</h3>
                            </div>
                            <div class="widget-head">
                                <h3 class="heading">Stock Issuance Approval Form [Requisition No.: <?php echo $_GET['rq']; ?>, Requisition Period: <?php echo $dateFrom . ' to ' . $dateTo . ', Store: ' . $mainStk . ' ' . $distName; ?>]</h3>
                            </div>
                            <div class="widget-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-condensed">

                                            <!-- Table heading -->
                                            <thead>
                                                <tr>
                                                    <th width="70">S. No.</th>
                                                    <th>Product</th>
                                                    <th>Replenishment</th>
                                                    <th>Requested (Manual)</th>
                                                    <th>Appr By Dist</th>
                                                    <th>Appr By Province</th>
                                                    <th>Remarks By Prov</th>
                                                    <th>Available Qty</th>
                                                    <th>Approved Qty</th>
                                                     
                                                    <!--<th width="150">Action</th>-->
                                                     
                                                    <th>Remarks</th>
                                                </tr>                                                
                                            </thead>
                                            <!-- // Table heading END --> 

                                            <!-- Table body -->
                                            <tbody>
                                                <!-- Table row -->
                                            <form name="approve_clr6" id="approve_clr6" action="clr6_approve_central_action.php" method="POST" onSubmit="return formValidation()">
                                                <?php
                                                $disabled = '';
                                                $readonly = '';
                                                $deniedSel = '';
                                                $approvedSel = '';
                                                $count = 1;
                                                foreach ($product as $proId => $proName) {

                                                    if ($masterStatus == 'Prov_Approved' || $masterStatus == 'Denied') {
                                                        //prov level
                                                        if ($status[$proId] == 'Prov_Approved' || $status[$proId] == 'Approved') {
                                                            $readonly = '';
                                                            $approvedSel = 'checked="checked"';
                                                            $deniedSel = '';
                                                        } else if ($status[$proId] == 'Denied') {
                                                            $readonly = 'readonly="readonly"';
                                                            $deniedSel = 'checked="checked"';
                                                            $approvedSel = '';
                                                        }
                                                        $disabled = '';
                                                    } else if ($masterStatus == 'Issue in Process') {
                                                        //national level
                                                        if ($status[$proId] == 'Issued') {
                                                            $disabled = 'disabled="disabled"';
                                                            $readonly = '';
                                                            $approvedSel = 'checked="checked"';
                                                            $deniedSel = '';
                                                        } else if ($status[$proId] == 'Approved') {
                                                            $disabled = '';
                                                            $readonly = '';
                                                            $approvedSel = 'checked="checked"';
                                                            $deniedSel = '';
                                                        } else if ($status[$proId] == 'Denied') {
                                                            $disabled = '';
                                                            $readonly = 'readonly="readonly"';
                                                            $deniedSel = 'checked="checked"';
                                                            $approvedSel = '';
                                                        }
                                                    } else if ($masterStatus == 'Issued' || $masterStatus == 'Approved') {
                                                        //national level
                                                        if ($status[$proId] == 'Issued') {
                                                            $approvedSel = 'checked="checked"';
                                                        } else if ($status[$proId] == 'Denied') {
                                                            $deniedSel = 'checked="checked"';
                                                        }
                                                        $disabled = 'disabled="disabled"';
                                                    }
                                                    //hardcoded for national , to skip the radio btns
                                                    $readonly ='';
                                                    ?>
                                                    <tr>
                                                        <td class="center"><?php echo $count++; ?></td>
                                                        <td><?php echo $proName; ?>
                                                            <input type="hidden" name="product_status[<?php echo $proId; ?>]" id="product_status" value="<?php echo $status[$proId] ?>" />
                                                            <input type="hidden" name="itmrec[<?php echo $proId ?>]" id="itmrec" value="<?php echo $itemrec_id[$proId] ?>" /></td>
                                                        <td class="right"><?php echo number_format($replenishment[$proId]); ?></td>
                                                        <td class="right"><?php echo number_format($qty_req_dist_lvl1[$proId]); ?></td>
                                                        <td class="right"><?php echo number_format($qty_req_dist_lvl2[$proId]); ?></td>
                                                        <td class="right"><?php echo number_format($qty_req_prov[$proId]); ?></td>
                                                        <td class="left"><?php echo $remarks_prov[$proId]; ?></td>
                                                        
                                                        
                                                        <td class="right"><input class="form-control input-small input-sm" type="text" name="qty_available[<?php echo $proId ?>]" id="qty_available[<?php echo $proId ?>]" value="<?php echo number_format($availableQty[$proId]); ?>" style="text-align:right;" readonly/></td>
                                                        <td><input autocomplete="off" <?php echo $readonly; ?> data-prod-name="<?php echo $proName;?>" <?php echo $readonly; ?> data-id="<?php echo $proId ?>" max="<?php echo $availableQty[$proId]; ?>" class="qty form-control input-small input-sm" type="text" name="qty_approved[<?php echo $proId ?>]" style="text-align:right;" id="qty_approved-<?php echo $proId ?>" value="<?php echo ((!empty($qty_req_prov[$proId]))?$qty_req_prov[$proId]:'')?>"  data-orig-val="<?php echo ((!empty($qty_req_prov[$proId]))?$qty_req_prov[$proId]:'')?>" <?php echo $disabled; ?> />
                                                        </td>
                                                       
                                                        <!--<td class="center">
                                                            <input type="radio" name="approve[<?php echo $proId ?>]" id="approve_<?php echo $proId ?>" value="1" onClick="checkAction(this, '<?php echo $proId ?>')" <?php echo $approvedSel; ?> <?php echo $disabled; ?> /> Approve
                                                            <input type="radio" name="approve[<?php echo $proId ?>]" id="decline_<?php echo $proId ?>" value="0" onClick="checkAction(this, '<?php echo $proId ?>')" <?php echo $deniedSel; ?> <?php echo $disabled; ?> /> Decline
                                                        </td>
                                                        -->
                                                        
                                                         <td class="left"><input class="form-control input-small input-sm remarks_box" type="text" name="remarks_central[<?php echo $proId ?>]" id="remarks_central[<?php echo $proId ?>]" value="<?php echo $remarks_central[$proId]; ?>" style="text-align:right;" /></td>
                                                       
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <tr>
                                                    <td colspan="10" style="text-align:right;" id="printButt">
                                                        <?php
                                                        if ($masterStatus != 'Issued') {
                                                            ?>
                                                        <a href="clr6_recalculate.php?referral=approve_clr6_national&<?php echo $_SERVER['QUERY_STRING']; ?>" class="btn btn-success hide"><i class="fa fa-refresh" /></i> Refresh </a>
                                                            <button type="submit" id="submit" class="btn btn-primary"> Save </button>
                                                            <button type="button" onClick="javascript: history.go(-1)" class="btn btn-warning"> Cancel </button>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <button type="button" onClick="javascript: history.go(-1)" class="btn btn-primary"> Back </button>
                                                            <button type="button" onClick="printContents()" class="btn btn-warning"> Print </button>
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <input type="hidden" name="warehouse" id="warehouse" value="<?php echo $_REQUEST['wh_id'] ?>"/>
                                                <input type="hidden" name="clr6_id" id="clr6_id" value="<?php echo $_REQUEST['id'] ?>"/>
                                                <input type="hidden" name="rq_no" value="<?php echo $requisitionNum ?>"/>
                                            </form>
                                            </tbody>

                                            <!-- // Table body END -->
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include PUBLIC_PATH . "/html/footer.php"; ?>

    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/clr6issue.js"></script>

    <script>
                                                            function printContents() {
                                                                var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes, left=100, top=25";
                                                                var printingContents = document.getElementById("printing").innerHTML;

                                                                var docprint = window.open("", "", printing);
                                                                docprint.document.open();
                                                                docprint.document.write('<html><head><title>Approve CLR-6</title>');
                                                                docprint.document.write('</head><body onLoad="self.print(); self.close()"><center>');
                                                                docprint.document.write(printingContents);
                                                                docprint.document.write('</center></body></html>');
                                                                docprint.document.close();
                                                                docprint.focus();
                                                            }
    </script>
    <script>
<?php
if (isset($_REQUEST['success']) && $_REQUEST['success'] == '1') {
    ?>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: 'Data has been saved successfully!',
                type: 'success',
                layout: self.data('layout')
            })
<?php } ?>
        function checkAction(checkBox, id)
        {
            if ($(checkBox).val() == 1)
            {
                $('#qty_approved-' + id).removeAttr('readonly');

            }
            else if ($(checkBox).val() == 0)
            {
                $('#qty_approved-' + id).val('');
                $('#qty_approved-' + id).attr('readonly', 'readonly');

            }
        }
        function formValidation()
        {
            if (confirm('Are you sure you want to save?'))
            {
                var q = 0;
                var inp = $('.qty');
                var inp2 = $('.remarks_box');
                for (var i = 0; i < inp.length; i++) {
                    if (inp[i].value != '') {
                        q++;
                        var qtyValue = inp[i].value;
                        var prod_name = inp[i].getAttribute('data-prod-name');
                        var orig_val  = inp[i].getAttribute('data-orig-val');
                        var remark    = inp2[i].value;
                        console.log ('prod:'+prod_name+' orig:'+orig_val+',remarks:'+remark);
                        
                       
                        qtyValue = parseInt(qtyValue.replace(/\,/g, ''));
                        if (qtyValue == 0)
                        {
                            alert('Quantity of '+prod_name+' can not be ZERO');
                            inp[i].focus();
                            return false;
                        }
                        else if (qtyValue > parseInt(inp[i].getAttribute('max'))) {
                            alert('Quantity of '+prod_name+'  can not be greater than ' + inp[i].getAttribute('max'));
                            inp[i].focus();
                            return false;
                        }
                        
                        if (qtyValue != orig_val && remark =='' )
                        {
                            alert('Please mention the reason for changing the quantity of '+prod_name+'.');
                            inp[i].focus();
                            return false;
                        }
                        
                    }
                }

                /*if (q == 0) {
                 alert('Please enter at least one quantity');
                 return false;
                 }*/
            }
            else
            {
                return false;
            }

            $('#submit').attr('disabled', true);
            $('#submit').html('Submitting...');
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
        })
    </script> 
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>