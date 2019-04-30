<?php
/**
 * approved_print
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
if (isset($_REQUEST['id']) && isset($_REQUEST['wh_id'])) {
    //warehouse to 
    $whTo = mysql_real_escape_string($_REQUEST['wh_id']);
    //get id
    $id = mysql_real_escape_string($_REQUEST['id']);
    //select query
    //disttrict id
    //province id
    //stk id
    //Location name
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
    //district id
    $distId = $qryRes['dist_id'];
    //prov id
    $provId = $qryRes['prov_id'];
    //stk id
    $stkid = $qryRes['stkid'];
    //district name
    $distName = $qryRes['LocName'];

    $mainStk = $qryRes['MainStk'];
//select query
    //gets
    //clr_master.requisition_num,
    //date_from,
    //date_to,
    //replenishment,
    //stock_master_id,
    //requested_on,
    //itm_id,
    //itmrec_id,
    //itm_name,
    //desired_stock,
    //approve_qty,
    //approval_status,
    //available_qty
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
				clr_details.available_qty
			FROM
				clr_master
			INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
			INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
			WHERE
				clr_master.pk_id = " . $id . "
			AND	(clr_details.approval_status = 'Approved' OR clr_details.approval_status = 'Issued')
			AND	clr_details.approve_qty > 0
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
        //requestedOn 
        $requestedOn = $row['requested_on'];
        //item id
        $item_id[] = $row['itm_id'];
        //product
        $product[$row['itm_id']] = $row['itm_name'];
        //requestedOn 
        $requestedOn = $row['requested_on'];
        // ]stock master
        
        $stock_master_id[$row['itm_id']] = $row['stock_master_id'];
        //desiredStock
        $desiredStock[$row['itm_id']] = $row['replenishment'];
        //itemrec_id
        $itemrec_id[$row['itm_id']] = $row['itm_id'];
        //approved
        $approved[$row['itm_id']] = $row['approve_qty'];
        //available
        $available[$row['itm_id']] = $row['available_qty'];
        //status
        $status[$row['itm_id']] = $row['approval_status'];
        //appStatus
        $appStatus[] = $row['approval_status'];
    }
    //duration
    $duration = $dateFrom . ' to ' . $dateTo;
}
?>
<style>
    table tr td, table tr th {
        font-size: 10px;
        padding-left: 5px;
        text-align: left;
    }
    p {
        font-size: 11px !important;
    }
    /* Print styles */
    @media only print {
        table tr td, table tr th {
            font-size: 10px;
            padding-left: 2 !important;
            text-align: left;
            border:none !important;
        }
    }
</style>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">

    <!-- BEGIN HEADER -->
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content" style="margin-left:0px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <p>Requisition No.: <?php echo $_GET['rq']; ?>, Requisition Period: <?php echo $dateFrom . ' to ' . $dateTo . ', Store: ' . $mainStk . ' ' . $distName; ?></p>
                            </div>
                            <div class="widget-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-striped table-bordered table-condensed">
<?php
if (mysql_num_rows($qryRes) > 0) {
    ?>
                                                <thead>
                                                    <tr>
                                                        <th style="text-align:center;">S. No.</th>
                                                        <th>Product</th>
                                                        <th class="text-center">Requested Qty</th>
                                                        <th>
                                                <table class="table table-condensed" id="myTable" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th style="border:none !important;">Batch No</th>
                                                            <th width="25%" style="border:none !important;">Expiry</th>
                                                            <th width="25%" style="text-align:center;border:none !important;">Available Qty</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                                </th>
                                                <th class="text-center">Approved Qty</th>
                                                </tr>
                                                </thead>
                                                <tbody>
    <?php
    $count = 1;
    foreach ($product as $proId => $proName) {
        ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo $count++; ?></td>
                                                            <td><span id="<?php echo $proId ?>"><?php echo $proName; ?></span></td>
                                                            <td class="text-right"><?php echo number_format($desiredStock[$proId]); ?></td>
                                                            <td>
                                                                <table class="table-condensed" id="myTable" width="100%">
                                                                    <tbody>
        <?php
        if ($status[$proId] == 'Approved') {
            //select query
            //gets
            //batch number
            //batch id
            //expiry
            //item id
            //qty
            $strSql = "SELECT
                                                                            stock_batch.batch_no,
                                                                            stock_batch.batch_id,
                                                                            stock_batch.batch_expiry,
                                                                            stock_batch.item_id,
                                                                            SUM(tbl_stock_detail.Qty) as Qty
                                                                        FROM
                                                                            stock_batch
                                                                        INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
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
                                                                            stock_batch.batch_no";

            $rsSql = mysql_query($strSql) or die("Error: GetAllRunningBatches");
            $num = mysql_num_rows($rsSql);
            while ($resStockIssues = mysql_fetch_assoc($rsSql)) {
                $avail = $resStockIssues['Qty'];
                ?>
                                                                                <tr>
                                                                                    <td style="border:none !important;"><?php echo $resStockIssues['batch_no']; ?></td>
                                                                                    <td width="25%" style="border:none !important;"><?php echo date('d/m/Y', strtotime($resStockIssues['batch_expiry'])); ?></td>
                                                                                    <td width="25%" class="text-right" style="border:none !important;"><?php echo number_format($avail) ?></td>
                                                                                </tr>
                <?php
            }
        } else {
            //select query
            //batch num
            //expiry
            //item id
            //issue qty
            //batch qty
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
            $rsSql = mysql_query($strSql) or die("Error: GetAllRunningBatches");
            $num = mysql_num_rows($rsSql);
            $totalIssued = 0;
            while ($resStockIssues = mysql_fetch_assoc($rsSql)) {
                $batch_qty = $resStockIssues['batch_qty'];
                $issue_qty = $resStockIssues['issue_qty'];
                $totalIssued += $resStockIssues['issue_qty'];
                ?>
                                                                                <tr>
                                                                                    <td><?php echo $resStockIssues['batch_no']; ?></td>
                                                                                    <td><?php echo date('d/m/Y', strtotime($resStockIssues['batch_expiry'])); ?></td>
                                                                                    <td><?php echo number_format($batch_qty) ?></td>
                                                                                </tr>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                            <td class="text-right"><?php echo number_format($approved[$proId]); ?></td>
                                                        </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
        <script language="javascript">
            $(function() {
                printCont();
            })
            function printCont()
            {
                window.print();
            }
        </script>
</body>

<!-- END BODY -->
</html>