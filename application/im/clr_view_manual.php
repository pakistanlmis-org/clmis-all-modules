<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
if (isset($_REQUEST['id']) && isset($_REQUEST['wh_id'])) {
    $whTo = mysql_real_escape_string($_REQUEST['wh_id']);
    $id = mysql_real_escape_string($_REQUEST['id']);
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
    //distrct id
    $distId = $qryRes['dist_id'];
    //province id
    $provId = $qryRes['prov_id'];
    //stakeholder id
    $stkid = $qryRes['stkid'];
    //location name
    $distName = $qryRes['LocName'];
    //main stakeholder
    $mainStk = $qryRes['MainStk'];
    $qry = "SELECT
				clr_master.requisition_num,
				clr_master.date_from,
				clr_master.date_to,
				clr_master.approval_status as master_approval_status,
                                clr_master.attachment_name,
				clr_details.approval_status as detail_approval_status,
				clr_details.pk_id,
				clr_details.pk_master_id,
				clr_details.avg_consumption,
				clr_details.soh_dist,
				clr_details.soh_field,
				clr_details.total_stock,
				clr_details.desired_stock,
				clr_details.replenishment,
				clr_details.qty_req_dist_lvl1,
                                clr_details.qty_req_dist_lvl2,
                                clr_details.qty_req_prov,
                                clr_details.qty_req_central,
                                clr_details.remarks_dist_lvl1,
                                clr_details.remarks_dist_lvl2,
                                clr_details.remarks_prov,
                                clr_details.remarks_central,
                                clr_details.sale_of_last_month,
                                clr_details.sale_of_last_3_months,
				DATE_FORMAT(clr_master.requested_on, '%d/%m/%Y') AS requested_on,
				itminfo_tab.itm_name,
				itminfo_tab.itm_id,
				itminfo_tab.itmrec_id,
				itminfo_tab.itm_type,
				itminfo_tab.generic_name,
				itminfo_tab.method_type
			FROM
				clr_master
				INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
				INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
			WHERE
				clr_master.pk_id = " . $id;
    //query result
    $qryRes = mysql_query($qry);
    //fetch result
    $items_arr =array();$show_prov_remarks=$show_dist_remarks=false;
    while ($row = mysql_fetch_array($qryRes)) {
        $master_approval_status = $row['master_approval_status'];
        $requisitionNum = $row['requisition_num'];
        $dateFrom = date('M-Y', strtotime($row['date_from']));
        $dateTo = date('M-Y', strtotime($row['date_to']));
        $requestedOn = $row['requested_on'];
        $attachment_name = $row['attachment_name'];
        
        $itemIds[] = $row['itm_id'];
        $product[$row['method_type']][] = $row['itm_name'];
        $items_arr[$row['itm_id']] = $row['itm_name'];
        
        // implanon is now opened .
        //if ($row['itm_id'] == 8) 
            //set avg Consumption
            $avgConsumption[$row['itm_id']] = number_format($row['avg_consumption']);
            $sale_of_last_month[$row['itm_id']] = number_format($row['sale_of_last_month']);;
            $sale_of_last_3_months[$row['itm_id']] = number_format($row['sale_of_last_3_months']);;
            //set SOH Dist
            $SOHDist[$row['itm_id']] = number_format($row['soh_dist']);
            //set SOH Field
            $SOHField[$row['itm_id']] = number_format($row['soh_field']);
            //set total Stock
            $totalStock[$row['itm_id']] = number_format($row['total_stock']);
            //set desired Stock
            $desiredStock[$row['itm_id']] = number_format($row['desired_stock']);
            //set replenishment
            $replenishment[$row['itm_id']] = number_format($row['replenishment']);
            
             //set qty requested and remarks
            $qty_req_dist_lvl1[$row['itm_id']]  = number_format($row['qty_req_dist_lvl1']);;
            $qty_req_dist_lvl2[$row['itm_id']]  = number_format($row['qty_req_dist_lvl2']);;
            $qty_req_prov[$row['itm_id']]       = number_format($row['qty_req_prov']);;
            $qty_req_central[$row['itm_id']]    = number_format($row['qty_req_central']);;
            
            $remarks_dist_lvl1[$row['itm_id']]  = $row['remarks_dist_lvl1'];
            $remarks_dist_lvl2[$row['itm_id']]  = $row['remarks_dist_lvl2'];
            $remarks_prov[$row['itm_id']]       = $row['remarks_prov'];
            $remarks_central[$row['itm_id']]    = $row['remarks_central'];
        
            if(!empty($row['remarks_prov'])) $show_prov_remarks=true;
            if(!empty($row['remarks_dist_lvl1'])) $show_dist_remarks=true;

        if (strtoupper($row['method_type']) == strtoupper($row['generic_name'])) {
            $methodType[$row['method_type']]['rowspan'] = 2;
        } else {
            $genericName[$row['generic_name']][] = $row['itm_name'];
        }
    }
    $duration = $dateFrom . ' to ' . $dateTo;
}
//echo '<pre>';print_r($remarks_dist_lvl1);print_r($items_arr);exit;

$print_word  = 'Manually Received';
$print_right = '30';
$print_size='60';
?>
<script>
    function printContents() {
        var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes, left=100, top=25";
        var printingContents = document.getElementById("printing").innerHTML;

        var docprint = window.open("", "", printing);
        
        
        docprint.document.open();
        docprint.document.write('<html><head><title>CLR6</title>');
        
        //setting up CSS for the watermark
                       
        docprint.document.write('</head><body onLoad="self.print();"><center>');
        docprint.document.write(printingContents);
        //docprint.document.write('<div id="watermark"><?php echo $print_word;?></div>');
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

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Requisitions</h3>
                            </div>
                            <div class="widget-body">
                                <div id="printing" style="clear:both;margin-top:20px;">
                                    <div style="margin-left:0px !important; width:100% !important;">
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
                                            }
                                        </style>
                                        
                                        <p style="color: #000000; font-size: 20px;text-align:center">
                                            <span style="float:left; font-weight:normal;"><i style="color:black !important"  onClick="history.go(-1)" class="fa fa-arrow-left" /></i></span>
                                            <b><u> Manually Received Requisition </u></b>
                                            <span style="float:right; font-weight:normal;">CLR-6</span>
                                        </p>
                                        <p style="text-align:center;margin-right:35px;">
                                            (<?php echo "For $mainStk District $distName"; ?>)
                                        </p>
                                        <table width="200" id="headerTable" align="right">
                                            <tr>
                                                <td align="left"><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 20px;"> From : </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $duration; ?></span> </p></td>
                                            </tr>
                                            <tr>
                                                <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 75px;">Requisition No: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $requisitionNum; ?></span> </p></td>
                                            </tr>
                                            <tr>
                                                <td><p style="width: 100%; display: table;"> <span style="display: table-cell; width: 83px;">Requisition Date: </span> <span style="display: table-cell; border-bottom: 1px solid black;"><?php echo $requestedOn; ?></span> </p></td>
                                            </tr>
                                        </table>
                                        <div style="clear:both;"></div>
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <td rowspan="2" width="150" id="desc">Description</td>
                                                    <?php
                                                    foreach ($product as $proType => $proNames) {
                                                        echo "<td style=\"text-align:center !important;\" colspan=" . sizeof($proNames) . ">$proType</td>";
                                                    }
                                                    ?>
                                                    <td rowspan="2" style="width:80px;">Remarks</td>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $col = '';
                                                    foreach ($product as $proType => $proNames) {
                                                        foreach ($proNames as $name) {
                                                            $names[] = $name;
                                                            echo "<td style=\"text-align:center\">$name</td>";
                                                            $col .= "<td>&nbsp;</td>";
                                                        }
                                                    }
                                                    ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Quantity Requested</td>
                                                    <?php
                                                    foreach ($qty_req_dist_lvl1 as $key => $val) {
                                                        echo "<td class=\"TAR\">" . $val . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                
                                                <tr style="display:none;">
                                                    <td style="text-align:center;"><?php echo $colNum++; ?></td>
                                                    <td>Quantity Approved By District</td>
                                                    <?php
                                                    foreach ($qty_req_dist_lvl2 as $key => $val) {
                                                        echo "<td class=\"TAR\">" . $val . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                
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
                                                                                        clr_details.pk_master_id = " . $_REQUEST['id'] . "
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
                                                                    $a .= " <a onClick=\"window.open('" . APP_URL . "im/printIssue.php?id=" . $row['PKStockId'] . "', '_blank', 'scrollbars=1,width=842,height=595')\" href=\"javascript:void(0);\">" . $row['TranNo'] . "</a>,</br>";
                                                                    
                                                                }
                                                            }
                                                ?>
                                                <tr>
                                                    <td>Relevant Issue Voucher/s</td>
                                                    <?php echo '<td colspan="'.count($names).'">'.$a.'</td>'; ?>
                                                    <td colspan="">&nbsp;</td>
                                                </tr>
                                                
                                               
                                                
                                            </tbody>
                                        </table>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php
                                                $path_parts = pathinfo($attachment_name);
                                                //print_r($path_parts);exit;
                                                if($path_parts['extension']=='png'
                                                        ||$path_parts['extension']=='jpg'
                                                        ||$path_parts['extension']=='jpeg'
                                                        ||$path_parts['extension']=='gif'
                                                        ){
                                                            echo '<hr/>';
                                                            echo '<div class="row"></div>';
                                                            echo '<div class="col-md-12 well well-dark"><span class=" h3 font-blue-loki">Scanned image of manually received requisition:</span></div>';
                                                            echo '<img width="100%" src="../../user_uploads/requisitions_attachments/'.$attachment_name.'">';
                                                        }
                                                        else{
                                                            
                                                            echo '<div class="row"></span></div>';
                                                            echo '<div class="col-md-6">Download attachment: <a download href="../../user_uploads/requisitions_attachments/'.$attachment_name.'"><span class="fa fa-download font-blue" style="font-size:35px;padding-top:20px"></span></a></div>';
                                                        }
                                                ?>
                                                
                                            </div>
                                        </div>
                                        
                                        <div id="watermark" style="font-size:<?php echo $print_size;?>px;font-color:#eeeee;opacity: 0.2;z-index: 5;right: <?php echo $print_right;?>%;top: 30%;position: absolute;display: block;   -ms-transform: rotate(340deg);  -webkit-transform: rotate(340deg);  transform: rotate(340deg);"><?php echo $print_word;?></div>
                                   
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>