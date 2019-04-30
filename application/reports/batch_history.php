<?php
ini_set('max_execution_time', 0);
/**
 * clr15
 * @package reports
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
//report id
$rptId = 'sdp_batches';
$count = 1;
$balance = 0;
//default stakeholder
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
//include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">

                <?php
                $batch_id = $_GET['id'];
                $qry = "SELECT
                            stock_batch.batch_no,
                            stock_batch.batch_expiry,
                            stock_batch.item_id,
                            stock_batch.Qty,
                            stock_batch.`status`,
                            stock_batch.unit_price,
                            stock_batch.production_date,
                            stock_batch.vvm_type,
                            stock_batch.wh_id,
                            stock_batch.funding_source,
                            stock_batch.manufacturer,
                            stock_batch.phy_inspection,
                            stock_batch.dtl,
                            stock_batch.dist_plan,
                            itminfo_tab.itm_name,
                            tbl_warehouse.wh_name,
                            tbl_warehouse.stkid,
                            CONCAT(
                                            stakeholder.stkname,
                                            ' | ',
                                            stakeholder_item.brand_name
                                    ) AS manuf_name
                            FROM
                            stock_batch
                            INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                            LEFT JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                            LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                            LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                            WHERE stock_batch.batch_id='$batch_id'

                            ";
                //print_r($qry);exit;
                $qryRes = mysql_query($qry);
                ?>
                <div class="widget" >
                    <div class="widget-head">
                        <h4 class="heading">Detailed Information of Batch</h4></div></div>
                <table class='table table-bordered table-condensed'>
                    <?php while ($row = mysql_fetch_assoc($qryRes)) { ?>
                        <tr>
                            <th>Batch Number</th><td><?php echo $row['batch_no'] ?></td> <br></tr>
                        <tr><th>Batch Expiry</th><td><?php echo $row['batch_expiry'] ?></td></tr>
                        <tr><th>Item Name</th><td><?php echo $row['itm_name'] ?></td></tr>

                        <tr><th>Quantity</th><td><?php echo number_format($row['Qty']) ?></td></tr>
                        <tr><th>Current Status</th><td class="<?=(($row['status']=='Running')?'green bold':'bold')?>"><?php echo $row['status'] ?></td></tr>
                         <tr> <th>Funding Source</th><td><?php echo $row['wh_name'] ?></td></tr>
                        <tr> <th>Manufacturer</th><td><?php echo $row['manuf_name'] ?></td></tr>

                        <tr>
                            <th>Phy Inspection Status</th>
                            <td><?php
                                if ($row['phy_inspection'] == '2') {
                                    echo 'N/A';
                                } else if ($row['phy_inspection'] == '0') {
                                    echo 'In process';
                                } else if ($row['phy_inspection'] == '1') {
                                    echo 'Completed';
                                } else {
                                    echo '';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr> 
                            <th>DTL Status (Drug Testing Lab)</th>
                            <td><?php
                                if ($row['dtl'] == '2') {
                                    echo 'N/A';
                                } else if ($row['dtl'] == '0') {
                                    echo 'In process';
                                } else if ($row['dtl'] == '1') {
                                    echo 'Completed';
                                } else {
                                    echo '';
                                }
                                ?>
                            </td> 
                        </tr>
                        <tr> 
                            <th>District Plan [Received / Not Received]</th>
                            <td><?php
                                if ($row['dist_plan'] == '2') {
                                    echo 'N/A';
                                } else if ($row['dist_plan'] == '0') {
                                    echo 'Not Received';
                                } else if ($row['dist_plan'] == '1') {
                                    echo 'Received';
                                } else {
                                    echo '';
                                }
                                ?>
                            </td>
                        </tr>

                        </tr>
                    <?php }
                    ?>
                </table><?php
                $qry_trail = "SELECT tbl_stock_master.WHIDFrom,
                                tbl_stock_master.WHIDTo,
                                stock_batch.batch_id,
                                stock_batch.batch_no,
                                tbl_stock_master.CreatedOn,
                                tbl_stock_detail.Qty,
                                stock_batch.`status`,
                                tbl_warehouse.wh_name AS From_warehouse,
                                tt.wh_name AS to_warehouse,
                                stakeholder.stkname,
                                tbl_locations.LocName AS province_name,
                                tbl_stock_master.TranDate,
                                tbl_stock_detail.adjustmentType,
                                tbl_trans_type.trans_type 
                               FROM
                                       stock_batch
                               INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                               INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                               INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
                               INNER JOIN tbl_warehouse AS tt ON tbl_stock_master.WHIDTo = tt.wh_id
                               INNER JOIN stakeholder ON tt.stkid = stakeholder.stkid
                               INNER JOIN tbl_locations ON tt.prov_id = tbl_locations.PkLocID
                                INNER JOIN tbl_trans_type ON tbl_stock_detail.adjustmentType = tbl_trans_type.trans_id
                               WHERE stock_batch.batch_id='$batch_id'
                               ORDER BY
                                       tbl_stock_master.TranDate ASC";
//                echo $qry_trail;exit;
                $res_trail = mysql_query($qry_trail);
                if (mysql_num_rows($res_trail) != 0) {
                    ?>
                    <div class="widget" >
                        <div class="widget-head">
                            <h4 class="heading">Batch Trail</h4></div></div>
                    <table width="100%" cellpadding="0" cellspacing="0" id="myTable" class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Batch No</th>
                                <th>Created On</th>
                                <th>Quantity</th>
                                <th>Balance</th>
<!--                                <th>Status</th>-->
                                <th>From Warehouse</th>
                                <th>To Warehouse</th>
                                <th>Stakeholder</th>
                                <th>Province</th>
                                <th>Transaction Date</th>
                                <th>Transaction Type</th>

                            </tr>

                        </thead>
                        <?php
                        $numResults = mysql_num_rows($res_trail);
                        $counter = 0;
                        $c=1;
                        while ($row = mysql_fetch_assoc($res_trail)) {
                            ?>
                            <tbody>
                                <tr>
                                    <td><?php echo $c++?></td>
                                    <td><?php echo $row['batch_no'] ?></td>
                                    <td><?php echo date('Y-M-d',strtotime($row['CreatedOn'])) ?></td>
                                    <td align="right"><?php echo number_format($row['Qty']); ?></td>
                                    <td align="right"><?php
                                        if ($count == 1) {
                                            echo number_format($row['Qty']);
                                            $balance = $row['Qty'];
                                        } else {
                                            $balance = $row['Qty'] + $balance;
                                            echo number_format($balance);
                                        }
                                        ?></td>
                                    <!--<td><?php
                                        if (++$counter == $numResults) {
                                            echo $row['status'];
                                        }
                                        ?></td>-->
                                    <td><?php echo $row['From_warehouse'] ?></td>
                                    <td><?php echo $row['to_warehouse'] ?></td>
                                    <td><?php echo $row['stkname'] ?></td>
                                    <td><?php echo $row['province_name'] ?></td>
                                    <td><?php echo date('Y-M-d',strtotime($row['TranDate']))  ?></td>
                                    <td><?php echo ($row['adjustmentType']<=2)?'Issuance':'Adjustment' ?></td>

                                </tr>
                                <?php $count++; ?>
                            </tbody>
                        <?php } ?>
                    </table>
                    <?php
                } else
                    echo 'No Batch Trail Found';
                ?>
            </div>
        </div>

    </div>

    <?php
//include footer
//include combos
//    include ('combos.php');
    ?>

</body>
</html>