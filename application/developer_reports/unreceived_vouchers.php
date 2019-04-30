<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");


//****************************
//Quick Query Settings
$title= 'Un Received Vouchers of SDP Level';
//****************************
$month = date('Y-m-01');
if(!empty($_REQUEST['month']))
@$month = $_REQUEST['month'];
$month = date('Y-m-01',strtotime($month));
?>
<html>
    <h3 align="center"><?=$title?></h3>
    <body>
<?php

$qry_summary_dist= "
    SELECT
        distinct tbl_stock_master.PkStockID,
        tbl_stock_master.TranNo,
        tbl_stock_master.TranRef, 
        tbl_stock_master.WHIDFrom,
        tbl_stock_master.WHIDTo,
        tbl_stock_master.TranTypeID,
        tbl_warehouse.wh_name as wh_from,
        t.wh_name as wh_to,
        tbl_stock_master.TranDate,
        (SELECT
            group_concat(sd.PkDetailID)
            FROM
            tbl_stock_master AS sm
            INNER JOIN tbl_stock_detail AS sd ON sm.PkStockID = sd.fkStockID
            INNER JOIN stock_batch AS sb ON sd.BatchID = sb.batch_id
            WHERE
            sm.TranTypeID = 1 AND
            sm.WHIDFrom = tbl_stock_master.WHIDFrom AND
            sm.WHIDTo = tbl_stock_master.WHIDTo AND
            sb.item_id = stock_batch.item_id
            and sm.TranRef = tbl_stock_master.TranNo AND
            sb.batch_no = stock_batch.batch_no AND
            IFNULL(sb.manufacturer, 0) =  IFNULL(stock_batch.manufacturer, 0)) AS rcv_detail_id
        FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
        INNER JOIN tbl_warehouse t ON tbl_stock_master.WHIDTo = t.wh_id
        INNER JOIN stakeholder ON t.stkofficeid = stakeholder.stkid
        WHERE
            tbl_stock_master.WHIDFrom <> 123 AND
            tbl_stock_master.WHIDTo <> 123 AND
            tbl_stock_master.TranTypeID = 2 AND
            tbl_warehouse.wh_name LIKE '%district%' AND
            stakeholder.lvl = 7  AND
            tbl_stock_master.temp=0
        having 
            rcv_detail_id is null
        ORDER BY 
            tbl_warehouse.wh_name
";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);
$display_data  = $columns_data = array();

while($row = mysql_fetch_assoc($Res2))
{
   $display_data[] = $row;
   $row2=$row;
   //echo '<pre>';print_r($row);
}

foreach($row2 as $k=>$v)
{
   $columns_data[] = $k;
}
//echo '<pre>';print_r($columns_data);print_r($display_data);
?>
<table border="1" class="table table-condensed table-striped left" >
    <tr bgcolor="#afb5ea">
        <?php
        echo '<td>#</td>';
        foreach($columns_data as $k=>$v)
        {
           echo '<td>'.$v.'</td>';
        }
        ?>
    </tr>
    
    <?php
    $count_of_row = 0;
        foreach($display_data as $k => $disp)
        {
           echo '<tr>';
           echo '<td>'.++$count_of_row.'</td>';
           foreach($columns_data as $k2=>$col)
           {
            echo ' <td>'.$disp[$col].'</td>';
           }   
           echo '<tr>';
        }
        ?>
</table>
    </body>
    
<script src="<?php echo PUBLIC_URL;?>js/jquery-1.4.4.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>
</html>
