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
$count=1;
$balance=0;
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
itminfo_tab.itm_name
FROM
stock_batch
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
WHERE stock_batch.batch_id='$batch_id'
     
";
//                print_r($qry);exit;
                $qryRes = mysql_query($qry);
                ?>
                 <div class="widget" >
                <div class="widget-head">
                    <h4 class="heading">Batch History</h4></div></div>
                <table class='table table-bordered table-condensed'>
<!--                    <th>Batch Number</th>
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
                    <th>District Plan</th>-->
                    <?php while ($row = mysql_fetch_assoc($qryRes)) { ?>
                    <tr>
                            <th>Batch Number</th><td><?php echo $row['batch_no'] ?></td> <br></tr>
                        <tr><th>Batch Expiry</th><td><?php echo $row['batch_expiry'] ?></td></tr>
                        <tr><th>Item Name</th><td><?php echo $row['itm_name'] ?></td></tr>

                        <tr><th>Quantity</th><td><?php echo $row['Qty'] ?></td></tr>
                        <tr><th>Status</th><td><?php echo $row['status'] ?></td></tr>
                        <tr><th>Unit Price</th><td><?php echo $row['unit_price'] ?></td></tr>
                        <tr><th>Production Date</th><td><?php echo $row['production_date'] ?></td></tr>
                        <tr><th>VVM Type</th><td><?php echo $row['vvm_type'] ?></td></tr>
                        <tr> <th>Funding Source</th><td><?php echo $row['funding_source'] ?></td></tr>
                        <tr> <th>Manufacturer</th><td><?php echo $row['manufacturer'] ?></td></tr>

                    <tr><th>Phy Inspection</th><td><?php if($row['phy_inspection']==2){echo 'N/A';}
                    else if($row['phy_inspection']==0) {echo 'In process';}
                    else{echo 'Completed';}?></td></tr>
                        <tr> <th>Dtl</th><td><?php if($row['dtl']==2){echo 'N/A';}
                    else if($row['dtl']==0) {echo 'In process';}
                    else{echo 'Completed';}?></td> </tr>
                        <tr> <th>District Plan</th><td><?php if($row['dist_plan']==2){echo 'N/A';}
                    else if($row['dist_plan']==0) {echo 'Not Received';}
                    else{echo 'Received';}?></td></tr>

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
 tbl_stock_master.TranDate
FROM
	stock_batch
INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
INNER JOIN tbl_warehouse AS tt ON tbl_stock_master.WHIDTo = tt.wh_id
INNER JOIN stakeholder ON tt.stkid = stakeholder.stkid
INNER JOIN tbl_locations ON tt.prov_id = tbl_locations.PkLocID
WHERE stock_batch.batch_id='$batch_id'
ORDER BY
	tbl_stock_master.TranDate ASC";
//                echo $qry;exit;
                $res_trail = mysql_query($qry_trail);
               if (mysql_num_rows($res_trail)!=0){
                ?>
                 <div class="widget" >
                <div class="widget-head">
                    <h4 class="heading">Batch Trail</h4></div></div>
                <table width="100%" cellpadding="0" cellspacing="0" id="myTable" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Batch No</th>
                            <th>Created On</th>
                            <th>Quantity</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>From Warehouse</th>
                            <th>To Warehouse</th>
                            <th>Stakeholder</th>
                            <th>Province</th>
                            <th>Transaction Date</th>

                        </tr>

                    </thead>
                    <?php 
                    $numResults = mysql_num_rows($res_trail);
                    $counter = 0;
                    while ($row = mysql_fetch_assoc($res_trail)) { ?>
                        <tbody>
                            <tr>
                                <td><?php echo $row['batch_no'] ?></td>
                                <td><?php echo $row['CreatedOn'] ?></td>
                                <td><?php echo $row['Qty'] ?></td>
                                <td><?php if($count==1){
                                    echo $row['Qty'];
                                    $balance=$row['Qty'];
                                } 
                                else{
                                    $balance=$row['Qty']+$balance;
                                    echo $balance;
                                }?></td>
                                <td><?php  if (++$counter == $numResults) {echo $row['status'] ;} ?></td>
                                <td><?php echo $row['From_warehouse'] ?></td>
                                <td><?php echo $row['to_warehouse'] ?></td>
                                <td><?php echo $row['stkname'] ?></td>
                                <td><?php echo $row['province_name'] ?></td>
                                <td><?php echo $row['TranDate'] ?></td>

                            </tr>
                            <?php $count++;?>
                        </tbody>
                    <?php }?>
                    </table>
               <?php }
               else echo 'No data found';?>
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