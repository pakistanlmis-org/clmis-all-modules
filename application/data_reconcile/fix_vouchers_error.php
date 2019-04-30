<?php
echo '---Started Fixing---<br/>';
ini_set('max_execution_time', 500);

$limit = 400;
$fix_now = true;
if($fix_now) $limit = 200;
        
include("../includes/classes/Configuration.inc.php");
include("../includes/classes/AllClasses.php");
 
// Get all districts
$qry = "
            SELECT DISTINCT
tbl_stock_master.WHIDTo,
tbl_stock_detail.BatchID,
stock_batch.batch_no,
tbl_stock_detail.PkDetailID,
stock_batch.item_id,
tbl_stock_detail.Qty
FROM
	tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
WHERE
tbl_stock_master.CreatedOn = '2018-10-03' AND
tbl_stock_master.TranTypeID = 1 AND
tbl_stock_master.WHIDFrom = 158
and stock_batch.wh_id = 158
ORDER BY
stock_batch.batch_no ASC,
tbl_stock_master.WHIDTo
limit $limit
";
//echo $qry;exit;
$qryRes = mysql_query($qry);
echo '<table border="1" >';
    echo '<tr>';
    echo '<td>#</td>';
    echo '<td>To</td>';
    echo '<td>Batch ID</td>';
    echo '<td>Batch NO</td>';
    echo '<td>Detail ID</td>';
    echo '<td>Item</td>';
    echo '<td>Qty</td>'; 
    echo '<td>Batch Exists ?</td>'; 
    echo '<td>NewBATCH created</td>'; 
    echo '<td>Adjusted Qty at District</td>'; 
    echo '<td>Adjusted Qty at SDP</td>'; 
    echo '</tr>';
$c=1;
while ($row = mysql_fetch_array($qryRes)) {
    
    
    $qry2 = "SELECT
                count(*) as c, stock_batch.batch_id
                FROM
                stock_batch
                WHERE
                stock_batch.batch_no = '".$row['batch_no']."' AND
                stock_batch.wh_id = ".$row['WHIDTo']."
            ";
    $qryRes2 = mysql_query($qry2);
    $row2 = mysql_fetch_assoc($qryRes2);
    $batch_exist = 'Yesss';
    $new_batch = '';
    if($row2['c'] == '0')
    {   
        $batch_exist = 'x';
        
        $q3 = " SELECT
                        *
                        FROM
                        stock_batch
                        WHERE

                        stock_batch.batch_no = '".$row['batch_no']."' AND
                        stock_batch.wh_id = 158
        ";
        $res3 = mysql_query($q3);
        $row3 = mysql_fetch_assoc($res3);
        
//        echo $q3;
//        echo ' R3: ';
//        print_r($row3);
//        exit;
        
        if($fix_now)
        {
        $q4 = " INSERT INTO `stock_batch` 

                (`batch_no`, `batch_expiry`, `item_id`, `Qty`, `status`,    `wh_id`, `funding_source`, 
                `manufacturer` ) 
                VALUES 
                ('".$row3['batch_no']."', '".$row3['batch_expiry']."', '".$row3['item_id']."', '".$row3['Qty']."', '".$row3['status']."' , '".$row['WHIDTo']."', '".$row3['funding_source']."', "
                . "'".$row3['manufacturer']."'  );
        ";
//        echo $q4;
        mysql_query($q4);
        $new_batch = mysql_insert_id();
        }
    }//end of: batch does NOT exist
    else
    {
        $new_batch = $row2['batch_id'];
    }
 
    
    if($fix_now)
    {
        $q4 = "  UPDATE  `tbl_stock_detail` SET BatchID='".$new_batch."' WHERE `PkDetailID`='".$row['PkDetailID']."'  AND  BatchID='".$row['BatchID']."'  ";
        $res4 = mysql_query($q4);


         $q6 = "     SELECT AdjustQty('".$row['BatchID']."',158) as a from DUAL ";
        $res6 = mysql_query($q6);
        $row6 = mysql_fetch_array($res6);
        //print_r($row6);

        $q5 = "   SELECT AdjustQty('".$new_batch."',".$row['WHIDTo'].") as a from DUAL ";
        $res5 = mysql_query($q5);
        $row5 = mysql_fetch_assoc($res5);

    }
    
    
    echo '<tr>';
    echo '<td>'.$c++.'</td>';
    echo '<td>'.$row['WHIDTo'].'</td>';
    echo '<td>'.$row['BatchID'].'</td>';
    echo '<td>'.$row['batch_no'].'</td>';
    echo '<td>'.$row['PkDetailID'].'</td>';
    echo '<td>'.$row['item_id'].'</td>';
    echo '<td>'.$row['Qty'].'</td>';
    echo '<td>'.$batch_exist.'</td>';
    echo '<td>'.@$new_batch.'</td>';
    echo '<td>'.@$row['BatchID'].',158 :'.@$row6['a'].'</td>';
    echo '<td>'.@$new_batch.' , '.@$row['WHIDTo'].' :'.@$row5['a'].'</td>';
    echo '</tr>';
    
    
    
    
}//end of First while

echo '</table >';

echo '<br/>----END-----';
ob_end_flush(); 
