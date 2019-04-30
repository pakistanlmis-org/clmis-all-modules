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
$f_date     = (!empty($_REQUEST['date']) ? date("Y-m-t", strtotime($_REQUEST['date'])) : date("Y-m"));
$dist_id    = (!empty($_REQUEST['dist_id']) ? $_REQUEST['dist_id'] : 'all');
$dist_name  = (!empty($_REQUEST['dist_name']) ? $_REQUEST['dist_name'] : 'all');
$prod_id    = (!empty($_REQUEST['item_id']) ? $_REQUEST['item_id'] : '');
$indicator  = (!empty($_REQUEST['indicator']) ? $_REQUEST['indicator'] : '');
$t          = (!empty($_REQUEST['t']) ? $_REQUEST['t'] : '');

$from_date = date("Y-m-d", strtotime($f_date));
//echo $f_date;exit;
$stk = (!empty($_REQUEST['stk_id']) ? $_REQUEST['stk_id'] : 'all');
$count = 1;
$balance = 0;
if (isset($_REQUEST['id'])) {
    $batch_id = $_REQUEST['id'];
}
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
                $qry_req = "SELECT
	clr_master.pk_id,
	clr_master.requisition_num,
	clr_master.requisition_to,
	tbl_warehouse.wh_name,
	clr_details.itm_id,
	clr_details.qty_req_dist_lvl1 AS requested_by_district,
	clr_master.requested_on AS REQUESTED_DATE,
	clr_master.approval_status,
	clr_details.stock_master_id,
	tbl_stock_detail.BatchID,
	tbl_stock_detail.Qty AS issued_from_cwh,
	clr_details.approve_qty AS approval_by_province,
	tbl_stock_master.CreatedOn AS ISSUED_date,
        stock_batch.item_id
FROM
	clr_master
INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
LEFT JOIN tbl_stock_master ON clr_details.stock_master_id = tbl_stock_master.PkStockID
LEFT JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
LEFT JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
WHERE
	tbl_warehouse.dist_id = $dist_id
AND tbl_warehouse.stkid = $stk
 AND  clr_master.date_to = '$f_date' 
  AND  clr_details.itm_id = $prod_id  ";
                
//                echo $qry_req; exit;
                
if(!empty($t)){echo $qry_req; exit;}
                $res_req = mysql_query($qry_req);
                //secho '>>>'.mysql_num_rows($res_req);exit;
                if (mysql_num_rows($res_req)>0) {
                    ?>
                    <div class="widget" >
                        <div class="widget-head">
                            <h4 class="heading">Requisition Detail</h4></div></div>
                    <table  width="100%" cellpadding="0" cellspacing="0" id="myTable" class="table table-bordered">
                        <thead >
                            <tr style="text-align: center;">   <th colspan="2">District</th>
                                <th>Provincial</th>
                                <th colspan="3">Central Warehouse</th>
                            </tr>
                            <tr>
                                <th>Qty Requested</th>
                                <th>Requested On</th>
                                <th>Approved Qty</th>
                                <th>Issued Qty</th>
                                <th>Issuance Date</th>
                                <th>Current Requisition Status</th>

                            </tr>
                        </thead>
                        <?php while ($row = mysql_fetch_assoc($res_req)) { 
                            if(empty($row['item_id']) || $row['item_id'] == $prod_id  ){
                            ?>
                            <tbody>
                                <tr>
                                <td><?php echo $row['requested_by_district']; ?></td>
                                <td><?php echo date('d-M-Y',strtotime($row['REQUESTED_DATE'])); ?></td>
                                <td><?php echo $row['approval_by_province']; ?></td> 
                                <td><?php echo $row['issued_from_cwh']; ?></td> 
                                <td><?php if(!empty($row['ISSUED_date'])) echo date('d-M-Y',strtotime($row['ISSUED_date'])); ?></td>
                                <td><?php echo $row['approval_status']; ?></td></tr>
                            </tbody>
                            <?php
                            
                            }
                            } ?>
                    </table>
                </div>
            </div>

        </div>

        <?php
    } else {
        echo "No data found";
    }
    ?>

</body>

</html>
