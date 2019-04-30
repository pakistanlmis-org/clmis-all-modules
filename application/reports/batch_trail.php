<?php
/**
 * form14
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
$count=1;
$balance=0;
$batch_no = "";
 
//if submitted
if (isset($_REQUEST['batch_no'])) {
    $batch_no = $_REQUEST['batch_no'];
     
}
 

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
                $qry = "SELECT tbl_stock_master.WHIDFrom,
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
WHERE stock_batch.batch_no = '$batch_no'
ORDER BY
	tbl_stock_master.TranDate ASC";
//                echo $qry;exit;
                $res = mysql_query($qry);
               if (mysql_num_rows($res)!=0){
                ?>
                
                <h4>Batch Trail</h4>
                <table width="100%" cellpadding="0" cellspacing="0" id="myTable" class="table table-bordered">
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
                    <?php while ($row = mysql_fetch_assoc($res)) { ?>
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
                                <td><?php if(($balance==0) )echo $row['status']  ?></td>
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
        include PUBLIC_PATH . "/html/footer.php";
        ?>
</body>
</html>