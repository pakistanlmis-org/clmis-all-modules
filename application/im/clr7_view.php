<?php
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH."html/header.php");
//title
$title = "Stock Receive from Warehouse";
//issu number
$issue_no = '';
$stockReceive = false;
    //check issue number
    if (isset($_REQUEST['issue_no']) && !empty($_REQUEST['issue_no'])) {
        //get issue number
        $issue_no = $_REQUEST['issue_no'];
    }
    //set issue number
    $objStockMaster->TranNo = $issue_no;
    $whTo = $_SESSION['user_warehouse'];
    //Get WH Stock By Issue No
     $strSql = "SELECT
        tbl_stock_detail.Qty,
        stock_batch.batch_no,
        itminfo_tab.itm_name,
        tbl_stock_detail.fkStockID,
        tbl_stock_detail.PkDetailID,
        stock_batch.batch_expiry,
        stock_batch.batch_id,
        stock_batch.item_id,
        itminfo_tab.itm_type
        FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        INNER JOIN itminfo_tab ON itminfo_tab.itm_id = stock_batch.item_id
        WHERE
        tbl_stock_master.TranNo = '" . $issue_no . "' AND
        tbl_stock_master.temp = 0 AND
        /*tbl_stock_master.WHIDTo = " . $whTo . " AND */
		( tbl_stock_detail.IsReceived = 1) AND tbl_stock_master.TranTypeID = 2";
        //query result
        $stockReceive = mysql_query($strSql) or die("Error GetWHStockByIssueNo");
        $s_count = mysql_num_rows($stockReceive);
        
   //get the data for issued quantities against clr7
        
   $q_received = "SELECT
                    tbl_stock_master.PkStockID,
                    tbl_stock_master.TranRef,
                    tbl_stock_detail.BatchID,
                    Sum(tbl_stock_detail.Qty) AS received_val,
                    tbl_stock_detail.IsReceived,
                    stock_batch.batch_no,
                    tbl_stock_master.ReceivedRemarks,
                    tbl_stock_master.CreatedOn
                    FROM
                    tbl_stock_master
                    INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                    INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                    WHERE
                    tbl_stock_master.TranRef = '".$issue_no."'
                    group by
                    tbl_stock_detail.BatchID";     
   $res_received = mysql_query($q_received);
   $arr_received = array();
   while ($row=mysql_fetch_assoc($res_received))
   {
       $received_date = date('d-M-Y', strtotime($row['CreatedOn']));
       $remarks = $row['ReceivedRemarks'];
       $arr_received[$row['batch_no']] = $row;
   }
    //echo '<pre>';print_r($arr_received);exit;
  $qry_clr = "SELECT
            clr_master.requisition_num,
            clr_master.date_from,
            clr_master.date_to,
            clr_master.requested_on,
            clr_master.approval_status,
            clr_master.pk_id,
            clr_details.stock_master_id,
            clr_details.replenishment,
            
            itminfo_tab.itmrec_id,
            itminfo_tab.itm_name,
            clr_details.desired_stock,
            itminfo_tab.itm_id,
            clr_details.approve_qty,
            clr_details.approve_date,
            clr_details.available_qty,

            clr_details.received_by_consignee,
            clr_details.var_req_n_disp,
            clr_details.var_disp_n_rec,
            clr_details.remarks_clr7,
            clr_details.qty_req_dist_lvl1,
            clr_details.qty_req_prov,

            itminfo_tab.itm_type
            FROM
            clr_master
            INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
            INNER JOIN tbl_stock_master ON tbl_stock_master.PkStockID = clr_details.stock_master_id
            INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
            WHERE
            tbl_stock_master.TranNo = '".$issue_no."'
";
//  echo $qry_clr;exit;
$res_clr = mysql_query($qry_clr);
$r_count = mysql_num_rows($res_clr);
$clr_arr=array();
while($row=mysql_fetch_assoc($res_clr))
{
    $requested_on = date('d-M-Y', strtotime($row['requested_on']));
    //$requested_on = $row['requested_on'];
    $requisitionNum= $row['requisition_num'];
    $approval_status= $row['approval_status'];
    $clr_master_id= $row['pk_id'];
    $dateFrom = date('M-Y', strtotime($row['date_from']));
    $dateTo = date('M-Y', strtotime($row['date_to']));
    $clr_arr[$row['itm_id']] = $row;
}
//echo '<pre>';print_r($clr_arr);exit;


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


$types = $objTransType->find_all();
$count = 0;
if(!empty($stockReceive)){
    $count = mysql_num_rows($stockReceive);
}

?>
<script>
    function printContents() {
        var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes, left=100, top=25";
        var printingContents = document.getElementById("printing").innerHTML;

        var docprint = window.open("", "", printing);
        
        
        docprint.document.open();
        docprint.document.write('<html><head><title>CLR7</title>');
        
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
    <?php 
    //include top
    include PUBLIC_PATH."html/top.php";
    //include top_im
    include PUBLIC_PATH."html/top_im.php";?>
    <div class="page-content-wrapper">
        <div class="page-content"> 
            <!-- BEGIN PAGE HEADER-->
            
            <div class="row">
                <div class="col-md-12" id="printing">
                    <style>
                                            body {
                                                margin: 0px !important;
                                                font-family: Arial, Helvetica, sans-serif;
                                            }

                                            table#myTable {
                                                margin-top: 20px;
                                                border-collapse: collapse;
                                                border-spacing: 0;
                                            }

                                            table#myTable tr td, table#myTable tr th {
                                                font-size: 11px;
                                                padding-left: 5px;
                                                text-align: left;
                                                border: 1px solid #999;
                                            }
                                            
                                            .print_font{
                                                font-size: 11px;
                                            }

                                            table#myTable tr td.TAR {
                                                text-align: right;
                                                padding: 5px;
                                                width: 50px !important;
                                            }

                                            .sb1NormalFont {
                                                color: #444444;
                                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                                font-size: 11px;
                                                font-weight: bold;
                                                text-decoration: none;
                                            }

                                            p {
                                                margin-bottom: 5px;
                                                font-size: 11px !important;
                                                line-height: 1 !important;
                                                padding: 0 !important;
                                            }

                                            table#headerTable tr td {
                                                font-size: 11px;
                                            }

                                            /* Print styles */
                                            @media only print {
                                                table#myTable tr td, table#myTable tr th {
                                                    font-size: 8px;
                                                    padding-left: 2 !important;
                                                    text-align: left;
                                                    border: 1px solid #999;
                                                }

                                                #doNotPrint {
                                                    display: none !important;
                                                }
                                                .dontPrintCls {
                                                    display: none !important;
                                                }
                                            }
                                        </style>
                    
                    <!-- // Row END --> 
                    <!-- Widget -->
                    <form name="receive_stock" id="receive_stock" action="clr7_create_action.php" method="POST">
                        <?php 
                        //check stock receive
                        if ($stockReceive != FALSE) : ?>
                        <div class="widget">
                            <div class="widget-body"> 
                                <?php
                                if( $s_count== 0)
                                {
                                    echo ' <p style="color: #000000; text-align:center">
                                            <b> No Record Found</b></p>';
                                    exit;
                                }
                                ?>
                                
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
                                        <td width="50%"><p style=""> <span style="display: table-cell; width: 120px;">Requisition No: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo (!empty($requisitionNum)?$requisitionNum:''); ?></span> </p></td>

                                        <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 83px;">Dated: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $requested_on; ?></span> </p></td>
                                    </tr>
                                </table>
                                <div style="clear:both;"></div>
                                <table width="700" id="headerTable" align="Left">
                                    <tr>
                                        <td  align="left"><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 130px;">Name of Consignee: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $mainStk; ?></span> </p></td>
                                    </tr>
                                    <tr>
                                        <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 80px;">Designation/Address: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $distName; ?></span> </p></td>
                                    </tr>
                                    <tr>
                                        <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 160px;">Requisition for the Month: </span> <span style="display: table-cell; border-bottom: 1px solid black;">
                                                    <!--As per Distribution of USAID Deliver Project and Approved by P &amp; D Division-->
                                                    <?php echo (!empty($dateFrom)?$dateFrom:'').' to  '.(!empty($dateTo)?$dateTo:''); ?>

                                                </span> </p></td>
                                    </tr>
                                    <tr>
                                        <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 280px;">Mode of Dispatch (Truck , Program Vehicle etc): </span> <span style="display: table-cell; border-bottom: 1px solid black;"><!--Handover to UPS Authorized Corrier Agent of USAID Deliver Project for Destination Delivery--></span> </p></td>
                                    </tr>
                                    <tr>
                                        <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 220px;">Dispatch Document: Challan / Bilty No: </span> <span style="display: table-cell;">__________________________</span> <span style="display: table-cell; width: 100px;">Program Vehicle No: </span> <span style="display: table-cell;">__________________________</span> </p></td>
                                    </tr>
                                </table>
                                
                                
                                
                                <!-- Table -->
                                <table id="myTable" class="table table-bordered table-condensed  table-vertical-center checkboxs js-table-sortable">
                                    
                                    <!-- Table heading -->
                                    <thead>
                                        <tr>
                                                <td rowspan="2" width="5%"><b>S. No.</b></td>
                                                <td class="bg-info" colspan="3" style="text-align:center;"><b>Contraceptives</b>
                                                <td class="bg-info" colspan="3" style="text-align:center;"><b>Details (Quantity) </b>
                                                <td class="bg-info center" colspan="2"><b>Variation (if any) in Quantities</b>
                                                <td class="bg-info" colspan="2" width="10%"><b>Remarks</b></td>
                                        </tr>
                                        <tr>
                                            <th  width="20%"> Product </th>
                                            <th> Unit </th>
                                            <th  width="20%"> Batch Details </th>
                                            <th> Requisitioned </th>
                                            <th> Dispatched </th>
                                            <th> Received By Consignee </th>
                                            
                                            <th> Requisitioned & Despatched </th>
                                            <th> Despatched & Received </th>
                                            
                                            <th> Remarks</th>
                                        </tr>
                                    </thead>
                                    <!-- // Table heading END --> 
                                    
                                    <!-- Table body -->
                                    <tbody>
                                        <!-- Table row -->
                                        <?php
                                            $i = 1;
                                            $itm_arr = $issue_arr = array();
                                           
                                            while ($row = mysql_fetch_assoc($stockReceive)){
                                               
                                                $issue_arr[] = $row;
                                                $itm_arr[$row['item_id']]['name']=$row['itm_name'];
                                                $itm_arr[$row['item_id']]['unit']=$row['itm_type'];
                                                if(!isset($itm_arr[$row['item_id']]['count'])) $itm_arr[$row['item_id']]['count'] = 0;
                                                $itm_arr[$row['item_id']]['count']+=1;
                                                $itm_arr[$row['item_id']]['temp']=0;
                                                
                                                if(empty($itm_arr[$row['item_id']]['dispatched'])) $itm_arr[$row['item_id']]['dispatched']=0;
                                                    $itm_arr[$row['item_id']]['dispatched']+=abs($row['Qty']);
                                            }
                                           // echo '<pre>';print_r($itm_arr);print_r($issue_arr);print_r($arr_received);exit;
                                            foreach($issue_arr as $itm_id => $iss)
                                            {
                                                $row = (object)$iss;
                                                $stockID = $row->fkStockID;
                                            
                                                ?>
                                        <tr>
                                            <?php
                                            if($itm_arr[$row->item_id]['temp'] == 0)
                                            {
                                                
                                                echo '<td rowspan="'.$itm_arr[$row->item_id]['count'].'">'.$i.'</td>';
                                                echo '<td rowspan="'.$itm_arr[$row->item_id]['count'].'">'.$row->itm_name.'</td>';
                                                echo '<td rowspan="'.$itm_arr[$row->item_id]['count'].'">'.$row->itm_type.'</td>';
                                            }
                                            
                                            ?>
                                            
                                            <td> <div>Batch No:<?php echo $row->batch_no; ?></div>
                                                 <div>Batch Expiry: <?php echo $row->batch_expiry; ?></div>
                                                 <div>Quantity: <?php echo number_format(abs($row->Qty)); ?></div>
                                            </td>
                                            <?php
                                            if($itm_arr[$row->item_id]['temp'] == 0)
                                            {
                                                if($approval_status == 'Issued' && !empty($clr_arr[$row->item_id]['qty_req_prov'])) $this_req=$clr_arr[$row->item_id]['qty_req_prov'];
                                                if($approval_status == 'Issue in Process' && !empty($clr_arr[$row->item_id]['qty_req_prov'])) $this_req=$clr_arr[$row->item_id]['qty_req_prov'];
                                                elseif($approval_status == 'Hard_Copy' && !empty($clr_arr[$row->item_id]['qty_req_dist_lvl1'])) $this_req=$clr_arr[$row->item_id]['qty_req_dist_lvl1'];
                                                else $this_req = 0;
                                            ?>
                                            <td rowspan="<?php echo $itm_arr[$row->item_id]['count']?>" class="right"><?php echo number_format(abs($this_req)); ?>
                                                <input type="hidden" id="<?php echo $i; ?>-requested" value="<?php echo abs($this_req); ?>" />
                                            </td>
                                            <?php
                                            }
                                            
                                            ?>
                                            
                                            <td class="right"><?php echo number_format(abs($row->Qty)); ?>
                                                <input type="hidden" id="<?php echo $i; ?>-qty" value="<?php echo abs($row->Qty); ?>" />
                                            </td>
                                            <td class="col-md-2 right">
                                                <?php 
                                                //echo '<pre>';print_r($iss);print_r($arr_received);exit;
                                                $received_val= (!empty($arr_received[$iss['batch_no']]['received_val'])?$arr_received[$iss['batch_no']]['received_val']:'');
                                                echo $received_val;
                                                ?>
                                                
                                            </td>
                                            
                                            <?php
                                            if($itm_arr[$row->item_id]['temp'] == 0)
                                            {
                                            ?>
                                            <td  rowspan="<?=$itm_arr[$row->item_id]['count']?>"  class="right">
                                                
                                              <?php
                                               //echo $this_req . ' AND ' .$itm_arr[$row->item_id]['dispatched'];
                                               echo ((float)abs($this_req) -(float)abs($itm_arr[$row->item_id]['dispatched']));
                                              ?>
                                            </td>
                                            <?php
                                            }
                                            ?>
                                            <td class="right">
                                                
                                              <?php
                                               echo ((float)abs($row->Qty) - (float)$received_val);
                                              ?>
                                                
                                            </td>
                                            <td class="center"></td>
                                        </tr>
                                        <?php 
                                        $i++;
                                        $itm_arr[$row->item_id]['temp']=1;
                                            } ?>
                                        <!-- // Table row END -->
                                    </tbody>
                                    <!-- // Table body END -->
                                    
                                </table>
                                <!-- // Table END --> 
                                
                                
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
                                        <table id="headerTable" width="100%" >
                                            <tr>
                                                <td width="15%">&nbsp;</td>
                                                <td>Issuer</td>
                                                <td style="width:20%">&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td>Receiver</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:left;" width="100" class="sb1NormalFont">Signature:</td>
                                                <td>__________________________</td>
                                                <td>&nbsp;</td>
                                                <td style="text-align:left;" width="100" class="sb1NormalFont">Signature:</td>
                                                <td>__________________________</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:left;" class="sb1NormalFont">Name:</td>
                                                <td>__________________________</td>
                                                <td>&nbsp;</td>
                                                <td style="text-align:left;" class="sb1NormalFont">Name:</td>
                                                <td>__________________________</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:left;" class="sb1NormalFont">Title:</td>
                                                <td>__________________________</td>
                                                <td>&nbsp;</td>
                                                <td style="text-align:left;" class="sb1NormalFont">Title:</td>
                                                <td>__________________________</td>
                                            </tr>
                                            <tr>
                                                <td colspan="5">&nbsp;</td>
                                            </tr>
                                            
                                            <tr>
                                                
                                                <td colspan="" style="text-align: left;">
                                                    <div class="row form-inline">
                                                        <div class="col-md-12 ">
                                                            <div class="control-group">
                                                                <label class="control-label">Receive Reference:</label>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <?php
                                                 $q2="SELECT DISTINCT
                                                                                        tbl_stock_master.TranNo,
                                                                                        tbl_stock_master.PKStockId,
                                                                                        tbl_stock_detail.IsReceived
                                                                                FROM
                                                                                        clr_details
                                                                                INNER JOIN tbl_stock_master ON clr_details.stock_master_id = tbl_stock_master.PkStockID
                                                                                INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                                                                WHERE
                                                                                        tbl_stock_master.TranTypeID = 2 AND
                                                                                        clr_details.pk_master_id = " . $clr_master_id . " AND
                                                                                        tbl_stock_master.TranNo = '".$issue_no."'
                                                                                ORDER BY
                                                                                        tbl_stock_master.PkStockID ASC";
                                                $getStockIssues = mysql_query($q2) or die("Err GetStockIssueId");

                                                            //chech if record exists
                                                $issueVoucher = array();
                                                $a='';
                                                            if (mysql_num_rows($getStockIssues) > 0) {
                                                                
                                                                //fetch results
                                                                while ($row = mysql_fetch_assoc($getStockIssues)) {
                                                                    $issueVoucher[]=$row['TranNo'];
                                                                    $a .= " <a onClick=\"window.open('" . APP_URL . "im/printIssue.php?id=" . $row['PKStockId'] . "', '_blank', 'scrollbars=1,width=842,height=595')\" href=\"javascript:void(0);\">" . $row['TranNo'] . "</a> </br>";
                                                                    
                                                                }
                                                            }
                                                ?>
                                                <td colspan="" style="text-align: left;">
                                                    <div class="row form-inline">
                                                        <div class="col-md-12 ">
                                                            <div class="control-group">
                                                                <div><?php echo $a;?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td colspan="2" style="text-align: left;">
                                                    <div class="row form-inline">
                                                        <div class="col-md-12 right">
                                                            <div class="control-group">
                                                                <label class="control-label">Supply Received On:</label>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td colspan="2" style="text-align: left;">
                                                    <div class="row form-inline">
                                                        <div class="col-md-12 center">
                                                            <div class="control-group">
                                                                <label class="control-label"><?php echo $received_date; ?></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="" style="text-align: left;">
                                                    <div class="row form-inline">
                                                        <div class="col-md-12 ">
                                                            <div class="control-group">
                                                                <label class="control-label" for="remarks"> Remarks </label>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td colspan="" style="text-align: left;">
                                                    <div class="row form-inline">
                                                        <div class="col-md-12">
                                                            <div class="control-group">
                                                                <div class=""><?php echo $remarks;?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td  id="doNotPrint" colspan="3" style="text-align:right; border:none; padding-top:15px;">
                                                    <input type="hidden" name="stock_id" id="stock_id" value="<?php echo $stockID; ?>"/>
                                                    <input type="button" onClick="history.go(-1)" value="Back" class="btn btn-primary" />
                                                    <input type="button" onClick="printContents()" value="Print" class="btn btn-warning" />
                                                </td>
                                            </tr>
                                        </table>
                                
                                
                                
                            </div>
                        </div>
                        
                        <!-- Widget -->
                        
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
<script src="<?php echo PUBLIC_URL; ?>js/dataentry/clr7_create.js"></script> 


<!-- END FOOTER -->

</body>
<!-- END BODY -->
</html>