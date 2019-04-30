<?php
/**
 * clr7_view
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
//get wh id
$wh_id = $_SESSION['user_warehouse'];
//login
Login();
//to wh
$whTo = mysql_real_escape_string($_REQUEST['wh_id']);
//get id
$id = mysql_real_escape_string($_REQUEST['id']);
//check id
if (isset($_REQUEST['id']) && isset($_REQUEST['wh_id'])) {
    //select query
    //gets
    //dist id
    //prov id
    //stk id
    //loc name
    //main stk
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
    //requisition number
    //date from
    //date to
    //stock master id
    //replenishment
    //requested_on
    //itmrec_id
    //item name
    //desired stock
    //item id
    //
	 $qry = "SELECT
				clr_master.requisition_num,
				clr_master.date_from,
				clr_master.date_to,
				clr_details.stock_master_id,
				clr_details.replenishment,
				DATE_FORMAT(clr_master.requested_on, '%d/%m/%Y') AS requested_on,
				itminfo_tab.itmrec_id,
				itminfo_tab.itm_name,
				clr_details.desired_stock,
				itminfo_tab.itm_id,
				clr_details.approve_qty,
				clr_details.approve_date,
				clr_details.available_qty,
				clr_details.approval_status,
                                
				clr_details.received_by_consignee,
				clr_details.var_req_n_disp,
				clr_details.var_disp_n_rec,
				clr_details.remarks_clr7,
				clr_details.qty_req_dist_lvl1,
                                
				itminfo_tab.itm_type
			FROM
				clr_master
			INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
			INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
			WHERE
				clr_master.pk_id =" . $id . " 
			ORDER BY
				itminfo_tab.frmindex ASC";
    //query result
    $qryRes = mysql_query($qry);
//fetch result
    while ($row = mysql_fetch_array($qryRes)) {
        $requisitionNum = $row['requisition_num'];
        $approveDate[$row['itm_id']] = $row['approve_date'];
        $dateFrom = date('M-Y', strtotime($row['date_from']));
        $dateTo = date('M-Y', strtotime($row['date_to']));
        $requestedOn = $row['requested_on'];
        $item_id[] = $row['itm_id'];
        $product[$row['itm_id']] = $row['itm_name'];
        $requestedOn = $row['requested_on'];
        $stock_master_id[$row['itm_id']] = (!empty($row['stock_master_id'])) ? $row['stock_master_id'] : '0';
        $replenishment[$row['itm_id']] = $row['replenishment'];
        $desiredStock[$row['itm_id']] = $row['desired_stock'];
        $itemrec_id[$row['itm_id']] = $row['itmrec_id'];
        $approved[$row['itm_id']] = $row['approve_qty'];
        $available[$row['itm_id']] = $row['available_qty'];
        $approvalStatus[$row['itm_id']] = $row['approval_status'];
        
        $qty_req_dist_lvl1[$row['itm_id']] = $row['qty_req_dist_lvl1'];
        
        $received_by_consignee[$row['itm_id']]  = $row['received_by_consignee'];
        $var_req_n_disp[$row['itm_id']]         = $row['var_req_n_disp'];
        $var_disp_n_rec[$row['itm_id']]         = $row['var_disp_n_rec'];
        $remarks_clr7[$row['itm_id']]           = $row['remarks_clr7'];
        
        $units[$row['itm_id']] = $row['itm_type'];
    }
    $duration = $dateFrom . ' to ' . $dateTo;
}
?>
<style>
    .btn-link {
        color: #fff !important;
        text-shadow: none;
    }
</style>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!-- BEGIN HEADER -->
    <div class="page-container">
<?php
//include top
include PUBLIC_PATH . "html/top.php";
//include top_im
include PUBLIC_PATH . "html/top_im.php";
?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12"> 
                        <!-- Widget -->
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">CLR-7</h3>
                            </div>
                            <div class="widget-body">
                                <div id="printing" style="clear:both;margin-top:20px;">
                                    <div style="margin-left:0px !important; width:100% !important;">
                                        <form id="clr7_form" action="clr7_action.php">
                                        <style>
                                            body{margin:0px !important;font-family:Arial,Helvetica,sans-serif; }
                                            table#myTable{margin-top:20px;border-collapse: collapse;border-spacing: 0;}
                                            table#myTable tr td, table#myTable tr th{font-size:11px;padding-left:5px; text-align:left; border:1px solid #999;}
                                            table#myTable tr td.TAR{text-align:right; padding:5px;width:50px !important;}
                                            .sb1NormalFont {
                                                color: #444444;
                                                font-family: Verdana,Arial,Helvetica,sans-serif;
                                                font-size: 11px;
                                                font-weight: bold;
                                                text-decoration: none;
                                            }
                                            p{margin-bottom:5px; font-size:11px !important; line-height:1 !important; padding:0 !important;}
                                            table#headerTable tr td{ font-size:11px;}

                                            /* Print styles */
                                            @media only print
                                            {
                                                table#myTable tr td, table#myTable tr th{font-size:8px;padding:2 !important; text-align:left; border:1px solid #999;}
                                                #doNotPrint{display: none !important;}
                                            }
                                        </style>
                                        <p style="color: #000000; font-size: 20px;text-align:center">
                                            <b>Government of Pakistan<br/>
                                                Planning and Development Division<br/>
                                                Directorate of Central Warehouse &amp; Supplies<br/>
                                                F-508, S.I.T.E Karachi
                                                <hr style="margin:3px 10px;" />
                                                <p style="text-align:center;margin-left:35px;"><u><b>Contraceptive Issue and Receive Voucher(IRV)</b></u></b><span style="float:right; font-weight:normal;">CLR-7</span></p>
                                        <p style="text-align:center;margin-left:15px;">(<?php echo "For $mainStk District $distName"; ?>)</p>
                                        <table width="700" id="headerTable" align="left">
                                            <tr>
                                                <td width="40%"><p style=""> <span style="display: table-cell; width: 80px;">Requisition No: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $requisitionNum; ?></span> </p></td>
                                            
                                                <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 83px;">Dated: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo date('d/m/Y'); ?></span> </p></td>
                                            </tr>
                                        </table>
                                        <div style="clear:both;"></div>
                                        <table width="700" id="headerTable" align="Left">
                                            <tr>
                                                <td  align="left"><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 105px;">Name of Consignee: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $mainStk; ?></span> </p></td>
                                            </tr>
                                            <tr>
                                                <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 80px;">Designation/Address: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $distName; ?></span> </p></td>
                                            </tr>
                                            <tr>
                                                <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 135px;">Requisition for the Month: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><!--As per Distribution of USAID Deliver Project and Approved by P &amp; D Division-->

                                                        </span> </p></td>
                                            </tr>
                                            <tr>
                                                <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 280px;">Mode of Dispatch (Truck , Program Vehicle etc): </span> <span style="display: table-cell; border-bottom: 1px solid black;"><!--Handover to UPS Authorized Corrier Agent of USAID Deliver Project for Destination Delivery--></span> </p></td>
                                            </tr>
                                            <tr>
                                                <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 200px;">Dispatch Document: Challan / Bilty No: </span> <span style="display: table-cell;">__________________________</span> <span style="display: table-cell; width: 105px;">Program Vehicle No: </span> <span style="display: table-cell;">__________________________</span> </p></td>
                                            </tr>
                                        </table>
                                        <div style="clear:both;"></div>
                                        <table id="myTable" cellpadding="3"  width="100%">
                                            <tr>
                                                <td rowspan="2" width="5%"><b>S. No.</b></td>
                                                <td class="bg-info" colspan="3" style="text-align:center;"><b>Contraceptives</b>
                                                <td class="bg-info" colspan="3" style="text-align:center;"><b>Details</b>
                                                <td class="bg-info center" colspan="2"><b>Variation (if any) in Quantities</b>
                                                <td class="bg-info" colspan="2" width="20%"><b>Remarks</b></td>
                                            </tr>
                                            <tr>
                                                <td rowspan="" width="12%"><b>Product</b></td>
                                                <td rowspan="" width="20%"><b>Date of Expiry &amp; Batch No.</b></td>
                                                <td rowspan="" width="5%"><b>Unit</b></td>
                                                <td width="10%"><b>Requisitoned</b></td>
                                                <td width="10%"><b>Dispatched</b></td>
                                                <td width="10%"><b>Received by the Consignee</b></td>
                                                <td width="8%"><b>Requisitioned &amp; Despatched (col 4-5)</b></td>
                                                <td width="8%"><b>Despatched &amp; Received (col 5-6)</b></td>
                                                <td><b>Packing</b></td>
                                            </tr>
                                            <tbody>
                                                <?php
                                                $count = 1;
                                                foreach ($product as $proId => $proName) {
                                                    ?>
                                                    <tr>
                                                        <td class="TAC"><?php echo $count++; ?></td>
                                                        <td><?php echo $proName; ?>
                                                            <input type="hidden" name="product[<?php echo $proId ?>]" id="product" value="<?php echo $proId ?>" />
                                                            <input type="hidden" name="itmrec[<?php echo $proId ?>]" id="itmrec" value="<?php echo $itemrec_id[$proId] ?>" /></td>
                                                        <td><?php
                                                         $qry_batch = "SELECT
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
                                                                                    stock_batch.batch_no ASC";
                                                        
                                                $getBatches = mysql_query($qry_batch) or die("Err GetCLRDetailBatches");
                                                while ($rowBatch = mysql_fetch_assoc($getBatches)) {
                                                        ?>
                                                                <div>Batch No: <?php echo $rowBatch['batch_no']; ?></div>
                                                                <div>Batch Expiry: <?php echo date('d/m/Y', strtotime($rowBatch['batch_expiry'])); ?></div>
                                                                <div>Quantity: <?php echo number_format($rowBatch['issue_qty']); ?></div>
                                                                <br/>
    <?php }
    ?></td>
                                                        <td><?php echo $units[$proId] ?></td>
                                                        <td class="TAR"><?php echo number_format($qty_req_dist_lvl1[$proId]); ?></td>
                                                        <td class="TAR"><?php if ($available[$proId]) {
                                                            echo number_format($approved[$proId]);
                                                        } ?></td>
                                                        <td><input name="received_by_consignee[<?php echo $proId ?>]" class="form-control qty"  style="text-align:right;width:90%;"  value="<?php echo $received_by_consignee[$proId] ?>" /></td>
                                                        <td><input name="var_req_n_disp[<?php echo $proId ?>]" class="form-control qty" style="text-align:right;width:90%;"  value="<?php echo $var_req_n_disp[$proId] ?>" /></td>
                                                        <td><input name="var_disp_n_rec[<?php echo $proId ?>]" class="form-control qty"  style="text-align:right;width:90%;"    value="<?php echo $var_disp_n_rec[$proId] ?>" /></td>
                                                        <td><input name="remarks_clr7[<?php echo $proId ?>]" class="form-control"  value="<?php echo $remarks_clr7[$proId] ?>" /></td>
                                                    </tr>
                                                <?php }
                                                ?>
                                            </tbody>
                                        </table>
                                        <table width="700" id="headerTable" align="Left">
                                            <tr>
                                                <td align="left"><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 105px;">IRV Voucher CLR-7 checked by Store Supervisor (CW &amp; S) </span> </p></td>
                                            </tr>
                                            <tr>
                                                <td><p style="width: 100%;"> <span style="display: table-cell;;">Note Below: <br/>
                                                        </span>

                                                    <ol style="padding:0 0 0 15px !important;">
                                                        <li style="list-style:lower-roman!important;">Please submitt CLR-6 on 3 month average sale and also indicate last month sale alongwith original challan of sale proceeds.</li>
                                                        <li style="list-style:lower-roman!important;">Please attach this CLR-7 duly acknowledged with next CLR-6 failing which supply could be delayed/withheld.</li>
                                                        <li style="list-style:lower-roman!important;">Date of receipt of consignment and page No. of the CLR-5 (FOR EACH CC) must be mentioned on the acknowledgement.</li>
                                                        <li style="list-style:lower-roman!important;">Mejestron injections be placed as instruction given on it&#39;s box.</li>
                                                    </ol>
                                                    </p>
                                                    <p style="width: 100%; display: table;"> </p></td>
                                            </tr>
                                        </table>
                                        <table width="100%">
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>Issuer</td>
                                                <td>&nbsp;</td>
                                                <td>Receiver</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:left;" width="100" class="sb1NormalFont">Signature:</td>
                                                <td>__________________________</td>
                                                <td style="text-align:left;" width="100" class="sb1NormalFont">Signature:</td>
                                                <td>__________________________</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:left;" class="sb1NormalFont">Name:</td>
                                                <td>__________________________</td>
                                                <td style="text-align:left;" class="sb1NormalFont">Name:</td>
                                                <td>__________________________</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:left;" class="sb1NormalFont">Title:</td>
                                                <td>__________________________</td>
                                                <td style="text-align:left;" class="sb1NormalFont">Title:</td>
                                                <td>__________________________</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="text-align: right">Supply Received On:__________________</td>
                                            </tr>
                                            <tr id="doNotPrint">
                                                <td colspan="4" style="text-align:right; border:none; padding-top:15px;">
                                                    <input type="hidden" name="id"  value="<?php echo $_REQUEST['id'] ?>"/>
                                                    <input type="hidden" name="wh_id" value="<?php echo $_REQUEST['wh_id'] ?>"/>
                                                    <input type="submit" value="Save" class="btn btn-success" />
                                                    <input type="button" onClick="history.go(-1)" value="Back" class="btn btn-primary" />
                                                    <input type="button" onClick="printContents()" value="Print" class="btn btn-warning" />
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; ?>

    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/stockplacement.js"></script> 
    <script>
            function printContents() {
                var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes, left=100, top=25";
                var printingContents = document.getElementById("printing").innerHTML;

                var docprint = window.open("", "", printing);
                docprint.document.open();
                docprint.document.write('<html><head><title>CLR-7</title>');
                docprint.document.write('</head><body onLoad="self.print(); self.close()"><center>');
                docprint.document.write(printingContents);
                docprint.document.write('</center></body></html>');
                docprint.document.close();
                docprint.focus();
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
</body>
<!-- END BODY -->
</html>