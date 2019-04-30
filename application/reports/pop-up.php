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
$rptId = 'clr15_hf';

//default stakeholder
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
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
itminfo_tab.itm_name
FROM
stock_batch
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
WHERE stock_batch.batch_id='$batch_id'
    AND stock_batch.Qty>0
";
//                print_r($qry);exit;
                $qryRes = mysql_query($qry);
                ?>
                <table class='table table-bordered'>
                    <th>Batch Number</th>
                    <th>Batch Expiry</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Unit Price</th>
                    <th>Production Date</th>
                    <th>VVM Type</th>
                    <th>Funding Source</th>
                    <th>Manufacturer</th>
                    <th>Phy Inspection</th>
                    <th>Dtl</th>
                    <th>District Plan</th>
                    <?php while ($row = mysql_fetch_assoc($qryRes)) { ?>
                        <tr>
                            <td><?php echo $row['batch_no'] ?></td>
                            <td><?php echo $row['batch_expiry'] ?></td>
                            <td><?php echo $row['itm_name'] ?></td>

                            <td><?php echo $row['Qty'] ?></td>
                            <td><?php echo $row['status'] ?></td>
                            <td><?php echo $row['unit_price'] ?></td>
                            <td><?php echo $row['production_date'] ?></td>
                            <td><?php echo $row['vvm_type'] ?></td>
                            <td><?php echo $row['funding_source'] ?></td>
                            <td><?php echo $row['manufacturer'] ?></td>
                            <td><?php echo $row['phy_inspection'] ?></td>
                            <td><?php echo $row['dtl'] ?></td>
                            <td><?php echo $row['dist_plan'] ?></td>

                        </tr>
                    <?php }
                    ?>
                </table>
            </div>
        </div>

    </div>

    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
//include combos
    include ('combos.php');
    ?>

</body>
</html>