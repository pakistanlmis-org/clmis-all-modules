<?php
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");

$qry_1 = "SELECT
            itminfo_tab.itm_id,
            itminfo_tab.itm_name
            FROM
            itminfo_tab
            WHERE
            itminfo_tab.itm_id in (1,2,3,9,5,7,8,13)
        ";
$res_1 = mysql_query($qry_1);
$itm_arr = array();
while($row_1 = mysql_fetch_array($res_1))
{
    $itm_arr[$row_1['itm_id']]=$row_1['itm_name'];
}


//get stk
$qry_1 = "SELECT
                    tbl_warehouse.wh_id,
                    tbl_warehouse.wh_name
            FROM
                    stakeholder
            INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
            WHERE
                    stakeholder.stk_type_id = 2
            AND tbl_warehouse.is_active = 1
            ORDER BY
                    stakeholder.stkorder ASC";
$res_1 = mysql_query($qry_1);
$stk_arr = array();
while($row_1 = mysql_fetch_array($res_1))
{
    $stk_arr[$row_1['wh_id']]=$row_1['wh_name'];
}

//fetch data

 $qry_1 = "SELECT
            quarter(curdate() - INTERVAL 1 QUARTER) as q,
            YEAR(curdate() - INTERVAL 1 QUARTER) as y,
            stock_batch.funding_source,
            stock_batch.item_id,
                    Sum(ABS(tbl_stock_detail.Qty)) AS consumption
            FROM
                    tbl_stock_master
            INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
            INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
            WHERE
                    stock_batch.funding_source IN (
                        SELECT
                                    tbl_warehouse.wh_id
                            FROM
                                    stakeholder
                            INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
                            WHERE
                                    stakeholder.stk_type_id = 2
                            AND tbl_warehouse.is_active = 1
                            ORDER BY
                                    stakeholder.stkorder ASC
                    )
            AND tbl_stock_master.TranTypeID = 2 AND
            QUARTER(tbl_stock_master.TranDate) = quarter(curdate() - INTERVAL 1 QUARTER) AND 
            YEAR(tbl_stock_master.TranDate) = year(curdate() - INTERVAL 1 QUARTER) 
            GROUP BY
              stock_batch.funding_source,
                    stock_batch.item_id
        ";
$res_1 = mysql_query($qry_1);
$issue_arr = array();
while($row_1 = mysql_fetch_array($res_1))
{
    $issue_arr[$row_1['funding_source']][$row_1['item_id']]=$row_1['consumption'];
}


?>

<div class="widget widget-tabs" style="">    
    <div class="widget-body"  style="height:480px;overflow: auto">
        <div class="text-center">Issue / Dispatch Status - Last Quarter</div>
        
            <table class="table table-striped table-hover table-condensed table-responsive ">

                    <tbody  style="font-size: 10px; ">
                         
                        <?php
                           echo '<tr class="row">';
                                echo '<td>&nbsp;</td>';
                                foreach($itm_arr as $id=>$name)
                                {
                                    echo '<td  style="text-align:right">'.$name.'</td>';
                                }
                           echo '</tr>';
                           
                           foreach($stk_arr as $wh_id=>$wh_name)
                           {
                               echo '<tr class="row">';
                                echo '<td>'.$wh_name.'</td>';
                                foreach($itm_arr as $id=>$name)
                                {
                                    echo '<td style="text-align: right">'.number_format((!empty($issue_arr[$wh_id][$id])?$issue_arr[$wh_id][$id]:'0')).'</td>';
                                }
                           echo '</tr>';
                           }
                           
                        ?>
                           
                    </tbody>
            </table>
        
            </div>
</div>