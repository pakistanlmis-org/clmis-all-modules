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

?>


<div class="widget widget-tabs">
    <div class="widget-body" id="expirySchedule">
            <?php
            //query
             $qry = "SELECT
                        QUARTER(curdate() - INTERVAL 1 QUARTER) as last_q,
                        YEAR(curdate() - INTERVAL 1 QUARTER) as last_y,
                        tbl_stock_master.WHIDFrom,
                        tbl_warehouse.wh_name AS wh_from,
                        tbl_stock_detail.fkUnitID,
                        SUM(tbl_stock_detail.Qty) as total_qty,
                        stock_batch.item_id,
                        itminfo_tab.itm_name
                    FROM
                        tbl_stock_master
                        INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
                        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                    WHERE
                        tbl_stock_master.WHIDTo = 123 AND
                        tbl_stock_master.TranTypeID = 1
                       AND
                        quarter(curdate() - INTERVAL 1 QUARTER) = QUARTER(tbl_stock_master.TranDate)AND 
                        YEAR(curdate() - INTERVAL 1 QUARTER) =  YEAR(tbl_stock_master.TranDate)
                    GROUP BY
                        tbl_stock_master.WHIDFrom,
                        stock_batch.item_id
                    ORDER  BY
                        tbl_stock_master.WHIDFrom,
                        stock_batch.item_id";
            
            /*AND
                        quarter(curdate() - INTERVAL 1 QUARTER) = QUARTER(tbl_stock_master.TranDate)AND 
                        YEAR(curdate() - INTERVAL 1 QUARTER) =  YEAR(tbl_stock_master.TranDate)*/
            //echo $qry;exit;
            $data_1='';
            $q_res = mysql_query($qry);
            while($row = mysql_fetch_assoc($q_res))
            {
                $last_q = $row['last_q'];
                $last_y = $row['last_y'];
                
                if (isset($row['itm_name']))
                {
                    $data_1 .= "<set label='".$row['wh_from']." - ".$row['itm_name']."' value='".$row['total_qty']."' link=\"JavaScript:showData('$row[item_id], 1');\" />";
                }
            }
            $xmlstore = "<chart showLegend='1' theme='fint' numberSuffix='' exportEnabled='1' exportAction='Download' caption='Receipt During Quarter $last_q-$last_y' exportFileName='Receipt During Quarter" . date('Y-m-d H:i:s') . "'>";
            $xmlstore .= $data_1;
            $xmlstore .= "</chart>";
            FC_SetRenderer('javascript');
            echo renderChart(PUBLIC_URL."FusionCharts/Charts/Pie2D.swf", "", $xmlstore, 'receipt', '100%', 450, false, false);
            ?>
    </div>
</div>
