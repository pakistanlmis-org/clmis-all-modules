<?php
//include Configuration
include("../includes/classes/Configuration.inc.php");
Login();
//include db
include(APP_PATH."includes/classes/db.php");
//include functions
include APP_PATH."includes/classes/functions.php";

//include FusionCharts
include(PUBLIC_PATH."FusionCharts/Code/PHP/includes/FusionCharts.php");

//whId
$whId = $_SESSION['user_warehouse'];
//stk id
$stkId = $_SESSION['user_stakeholder'];
//item id
$itemId = 1;
?>


<div class="widget widget-tabs">
    <div class="widget-body" id="expirySchedule">
           <?php
            //query
             $qry = "SELECT
                        Sum(stock_batch.Qty) AS soh,
                        stock_batch.item_id,
                        itminfo_tab.itm_name,
                        itminfo_tab.qty_carton,
                         (select stakeholder_item.carton_per_pallet 
                                    from stakeholder_item where stakeholder_item.stkid = ".$_SESSION['user_stakeholder1']." 
                                    and stakeholder_item.stk_item = itminfo_tab.itm_id) as carton_per_pallet
                        FROM
                        tbl_stock_master
                        INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                        INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
                        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                        WHERE
                        tbl_stock_master.WHIDFrom IN ((
                                        SELECT
                                                tbl_warehouse.wh_id
                                        FROM
                                                stakeholder
                                        INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                        WHERE
                                                stakeholder.stk_type_id = 2
                                        AND tbl_warehouse.is_active = 1
                                )) AND
                        itminfo_tab.itm_category = 1
                        GROUP BY
                                stock_batch.item_id
            ";
            
           
            //echo $qry;exit;
            $data_1='';
            $q_res = mysql_query($qry);
            while($row = mysql_fetch_assoc($q_res))
            {
                if (isset($row['itm_name']))
                {
                    $cartons = (!empty($row['qty_carton'] && $row['qty_carton']>0)?round($row['soh']/$row['qty_carton']):'0');
                    $pallets = (!empty($row['carton_per_pallet'] && $row['carton_per_pallet']>0)?round($cartons/$row['carton_per_pallet']):'0');
                    
                    $data_1 .= "<set label='".$row['itm_name']."' value='".$pallets."' />";
                }
            }
            $xmlstore = "<chart showLegend='1' theme='fint' numberSuffix=' Pallets ' exportEnabled='1' exportAction='Download' caption='Product wise capacity occupation' exportFileName='Capacity Occupation" . date('Y-m-d H:i:s') . "'>";
            $xmlstore .= $data_1;
            $xmlstore .= "</chart>";
            FC_SetRenderer('javascript');
            echo renderChart(PUBLIC_URL."FusionCharts/Charts/Pie2D.swf", "", $xmlstore, 'capacity_occupation', '100%', 450, false, false);
            ?>
    </div>
</div>
