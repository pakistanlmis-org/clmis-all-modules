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
//echo '<pre>';print_r($_SESSION);

$qry_f = "SELECT
                funding_stk_prov.funding_source_id
            FROM
                funding_stk_prov
            WHERE
                funding_stk_prov.stakeholder_id = ".$_SESSION['user_stakeholder1']." AND
                funding_stk_prov.province_id = ".$_SESSION['user_province1']."
                 ";
$res_f = mysql_query($qry_f);
$funding_stks=array();
while($row_f=mysql_fetch_assoc($res_f))
{
    $funding_stks[$row_f['funding_source_id']]=$row_f['funding_source_id'];
}


$qry2 = " SELECT
                requisition_module_flow.action_id,
                requisition_module_flow.can_submit_to,
                requisition_module_flow.is_active,
                requisition_module_flow.prov_id,
                requisition_module_flow.stk_id
            FROM
                requisition_module_flow
            WHERE
                requisition_module_flow.action_id = 1 AND
                requisition_module_flow.can_submit_to = 3 AND
                requisition_module_flow.is_active = 1 AND
                requisition_module_flow.prov_id = ".$_SESSION['user_province1']." AND
                requisition_module_flow.stk_id = ".$_SESSION['user_stakeholder1']." ";
$qryRes2 = mysql_query($qry2);
$num2 = mysql_num_rows($qryRes2);

if($num2>0)
{
    $approve_dist_reqs_is_active = TRUE;
}
else
{
    $approve_dist_reqs_is_active = FALSE;
}

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
    
    $reserved_qry = "SELECT
                        sum(clr_details.qty_req_prov) as reserved,
                        clr_details.itm_id
                        FROM
                        clr_master
                        INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
                        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
                        WHERE
                        clr_master.approval_status in ('RS_Approved','Prov_Approved','Approved') AND
                        tbl_warehouse.prov_id = ".$_SESSION['user_province1']." AND
                        tbl_warehouse.stkid = ".$_SESSION['user_stakeholder1']."
                        GROUP BY
                        clr_details.itm_id";
    $reserved_res = mysql_query($reserved_qry);
    $reserved_arr=array();
    while($row = mysql_fetch_assoc($reserved_res))
    {
        $reserved_arr[$row['itm_id']] = $row['reserved'];
    }
//select query
    //gets
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
        $remarks_dist_lvl1[$row['itm_id']] = $row['remarks_dist_lvl1'];
        $remarks_dist_lvl2[$row['itm_id']] = $row['remarks_dist_lvl2'];
        $remarks_prov[$row['itm_id']]      = $row['remarks_prov'];
        
        
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
                                <h3 class="heading center">Reporting Stakeholder - Requisition Approval</h3>
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
                                                    <th>Replenishment Required</th>
                                                    <th>Actually Requested (Manual)</th>
                                                    <?php
                                                    if($approve_dist_reqs_is_active || $masterStatus=='Dist_Approved')
                                                    {
                                                        ?>
                                                        <th>Approved By District</th>
                                                        <th>Remarks By District</th>
                                                        <?php
                                                    }
                                                    ?>
                                                    <!--<th>Available Qty</th>-->
                                                    <th>Reserved Qty</th>
                                                    <th>Approved Qty</th>
                                                     
                                                    <th width="150" class="hide">Action</th>
                                                     
                                                    <th>Remarks</th>
                                                </tr>                                                
                                            </thead>
                                            <!-- // Table heading END --> 

                                            <!-- Table body -->
                                            <tbody>
                                                <!-- Table row -->
                                            <form name="approve_clr6" id="approve_clr6" action="clr6_approve_prov_action3.php" method="POST" onSubmit="return formValidation()">
                                                <?php
                                                $disabled = '';
                                                $readonly = '';
                                                $deniedSel = '';
                                                $approvedSel = '';
                                                $count = 0;
                                                foreach ($product as $proId => $proName) {

                                                    if ($masterStatus == 'Pending') {
                                                        //dist level condition
                                                        $disabled = '';
                                                        $readonly = ' ';
                                                        $deniedSel = 'checked="checked"';
                                                        $approvedSel = '';
                                                    } else if ($masterStatus == 'Dist_Approved') {
                                                        //dist level
                                                        if ($status[$proId] == 'Dist_Approved' || $status[$proId] == 'Prov_Approved' || $status[$proId] == 'Approved') {
                                                            $readonly = '';
                                                            $approvedSel = 'checked="checked"';
                                                            $deniedSel = '';
                                                        }else if ($status[$proId] == 'Denied') {
                                                            $readonly = 'readonly="readonly"';
                                                            $deniedSel = 'checked="checked"';
                                                            $approvedSel = '';
                                                        }
                                                        $disabled = '';
                                                    }else if ($masterStatus == 'Prov_Approved' || $masterStatus == 'Denied') {
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
                                                    
                                                    if($_SESSION['user_level'] == '3')$readonly='';
                                                    
                                                    $count++;
                                                   
                                                                        
                                                    $strSql = "SELECT
                                                                       stock_batch.batch_no,
                                                                       stock_batch.batch_id,
                                                                       stock_batch.batch_expiry,
                                                                       stock_batch.item_id,
                                                                       SUM(tbl_stock_detail.Qty) as Qty,
                                                                       itminfo_tab.qty_carton
                                                               FROM
                                                                       stock_batch
                                                               INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                                                               INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                                                               WHERE
                                                                       stock_batch.Qty <> 0 AND
                                                                       
                                                                       stock_batch.item_id = $proId AND
                                                                       stock_batch.wh_id = 123 AND
                                                                       tbl_stock_detail.temp = 0
                                                                       AND
                                                                       stock_batch.funding_source in  (".implode(',',$funding_stks).") 
                                                               GROUP BY
                                                                   stock_batch.batch_expiry,
                                                                   itminfo_tab.qty_carton
                                                               ORDER BY
                                                                       stock_batch.batch_expiry ASC,
                                                                       stock_batch.batch_no";

                                                   //query result
                                                    //echo $strSql;exit;
                                                   $rsSql = mysql_query($strSql) or die("Error: GetAllRunningBatches");
                                                   $num = mysql_num_rows($rsSql);
                                                   $prod_avail=0;
                                                   $c2=1;
                                                   $stock_row='';
                                                   while ($row_2 = mysql_fetch_assoc($rsSql)) {
                                                       if(!empty($row_2['Qty']) && $row_2['Qty']>0 && !empty($row_2['qty_carton']) && $row_2['qty_carton']>0)
                                                           $carton_available=$row_2['Qty']/$row_2['qty_carton'];
                                                       else
                                                           $carton_available=0;


                                                       $stock_row.= '<tr>
                                                               <td class="center">'.$c2++.'</td>
                                                               <td class="center">'.$row_2['batch_expiry'].'</td>
                                                               <td class="right">'.$row_2['qty_carton'].'</td>
                                                               <td class="right">'.number_format($row_2['Qty']).'</td>
                                                               <td class="right">'.number_format(floor($carton_available)).'</td>
                                                           </tr>';
                                                       $prod_avail += $row_2['Qty'];

                                                   }
                                                ?>
                                                    <tr >
                                                        <td class="center"><?php echo $count; ?></td>
                                                        <td><?php echo $proName; ?>
                                                            <input type="hidden" name="product_status[<?php echo $proId; ?>]" id="product_status" value="<?php echo $status[$proId] ?>" />
                                                            <input type="hidden" name="itmrec[<?php echo $proId ?>]" id="itmrec" value="<?php echo $itemrec_id[$proId] ?>" /></td>
                                                        <td class="right"><?php echo number_format($replenishment[$proId]); ?></td>
                                                        <td class="right"><?php echo number_format($qty_req_dist_lvl1[$proId]); ?></td>
                                                        
                                                        <?php
                                                        if($approve_dist_reqs_is_active || $masterStatus=='Dist_Approved')
                                                        {
                                                        ?>
                                                            <td class="right"><?php echo number_format($qty_req_dist_lvl2[$proId]); ?></td>
                                                            <td class="left"><?php echo $remarks_dist_lvl2[$proId]; ?></td>
                                                        <?php
                                                        }
                                                        ?>
                                                        <td class="right">
                                                            <?=(!empty($reserved_arr[$proId])?$reserved_arr[$proId]:'')?>
                                                        </td>
                                                        <?php
                                                        //$approve_dist_reqs_is_active
                                                        
                                                            if($masterStatus == 'Pending')
                                                            {
                                                                if($approve_dist_reqs_is_active)
                                                                    $this_prov_qty  = ((!empty($qty_req_dist_lvl2[$proId]))?$qty_req_dist_lvl2[$proId]:'');
                                                                else
                                                                    $this_prov_qty  = ((!empty($qty_req_dist_lvl1[$proId]))?$qty_req_dist_lvl1[$proId]:'');
                                                                    
                                                            }
                                                            else if($masterStatus == 'Dist_Approved')
                                                            {
                                                                if($approve_dist_reqs_is_active)
                                                                    $this_prov_qty  = ((!empty($qty_req_dist_lvl2[$proId]))?$qty_req_dist_lvl2[$proId]:'');
                                                                else
                                                                    $this_prov_qty  = ((!empty($qty_req_dist_lvl2[$proId]))?$qty_req_dist_lvl2[$proId]:'');
                                                            }
                                                            else if($masterStatus == 'Prov_Saved' ||$masterStatus=='Prov_Approved' || $masterStatus == 'RS_Saved' ||$masterStatus=='RS_Approved')
                                                                $this_prov_qty  = ((!empty($qty_req_prov[$proId]))?$qty_req_prov[$proId]:'');
                                                            else
                                                                $this_prov_qty = '';
                                                        ?>
                                                        
                                                        <td><input autocomplete="off" <?php echo $readonly; ?> data-prod-name="<?php echo $proName;?>" <?php echo $readonly; ?> data-id="<?php echo $proId ?>" max="<?php echo $prod_avail; ?>" class="qty form-control input-small input-sm" type="text" name="qty_approved[<?php echo $proId ?>]" style="text-align:right;" id="qty_approved-<?php echo $proId ?>" value="<?php echo $this_prov_qty?>" data-orig-val="<?php echo $this_prov_qty?>" <?php echo $disabled; ?> />
                                                        </td>
                                                       
                                                        <td class="center hide">
    <?php /* ?><input type="checkbox" name="approve[<?php echo $proId?>]"  <?php if($status[$proId]=='Approved' || $status[$proId]=='Denied' || $status[$proId]=='Issued' ){echo "disabled=disabled";} else {echo "checked=checked";}?>/><?php */ ?>
                                                            <input type="radio" name="approve[<?php echo $proId ?>]" id="approve_<?php echo $proId ?>" value="1" onClick="checkAction(this, '<?php echo $proId ?>')" <?php echo $approvedSel; ?> <?php echo $disabled; ?> /> Approve
                                                            <input type="radio" name="approve[<?php echo $proId ?>]" id="decline_<?php echo $proId ?>" value="0" onClick="checkAction(this, '<?php echo $proId ?>')" <?php echo $deniedSel; ?> <?php echo $disabled; ?> /> Decline
                                                        </td>
                                                       
                                                        
                                                         <td class="left"><input class="form-control input-small input-sm remarks_box" maxlength="250" type="text" name="remarks_prov[<?php echo $proId ?>]" id="remarks_prov[<?php echo $proId ?>]" value="<?php echo $remarks_prov[$proId]; ?>" style="text-align:right;" /></td>
                                                       
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <tr>
                                                    <td colspan="10" style="text-align:right;" id="printButt">
                                                        <?php
                                                        if ($masterStatus != 'Issued') {
                                                            ?>
                                                        <div class="pull-left">
                                                            <div class="note note-info">Suggestion : Please save this form as draft first. APPROVE only after you have analyzed quantities for all districts.</div>
                                                        </div>
                                                        <div class="pull-right"> 
                                                        <a href="clr6_recalculate.php?referral=approve_clr6_prov&<?php echo $_SERVER['QUERY_STRING']; ?>" class="btn btn-success hide"><i class="fa fa-refresh" /></i> Refresh </a>
                                                            <input type="submit" id="save_btn"    name="submit" value="Save" class="btn btn-primary">
                                                            <input type="submit" id="approve_btn" name="submit" value="Approve" class="btn btn-primary">
                                                            <button type="button" onClick="javascript: history.go(-1)" class="btn btn-warning"> Cancel </button>
                                                        </div>
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
                                                <input type="hidden" name="ref_page" value="<?php echo (!empty($_REQUEST['ref_page'])?$_REQUEST['ref_page']:'') ?>"/>
                                                <input type="hidden" name="month" value="<?php echo (!empty($_REQUEST['month'])?$_REQUEST['month']:'') ?>"/>
                                                <input type="hidden" name="year" value="<?php echo (!empty($_REQUEST['year'])?$_REQUEST['year']:'') ?>"/>
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
            if (confirm('Are you sure you want to submit changes?'))
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
                        //console.log ('prod:'+prod_name+' orig:'+orig_val+',remarks:'+remark);
                        
                       
                        qtyValue = parseInt(qtyValue.replace(/\,/g, ''));
                        if (qtyValue == 0)
                        {
                            alert('Quantity of '+prod_name+' can not be ZERO');
                            inp[i].focus();
                            return false;
                        }
                        
                        
                        if (qtyValue != orig_val && remark =='' )
                        {
                            //alert('Please mention the reason for changing the quantity of '+prod_name+'.');
                            //inp[i].focus();
                            //return false;
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