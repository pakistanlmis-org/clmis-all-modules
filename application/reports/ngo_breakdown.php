<?php
include("../includes/classes/AllClasses.php");


$province    = $_REQUEST['prov'];
$product     = $_REQUEST['prod'];
$date=$last_date   = $_REQUEST['date'];
//print_r($_REQUEST);exit;


$qry = "SELECT
            national_stock_control.pk_id,
            national_stock_control.stkid,
            national_stock_control.provid,
            national_stock_control.checked,
            national_stock_control.last_modified_at,
            national_stock_control.date_from
        FROM
            national_stock_control
        WHERE
            date_from < '".$date."'
        ORDER BY
            national_stock_control.date_from
";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$rules  = $rules_slabs  = $rules_slabs2  = $rules_slabs3  = array();
while ($row = mysql_fetch_assoc($qryRes)) {
//   if($row['provid'] != '10')
   $rules[$row['provid']][$row['stkid']] = 1; 
   $rules_slabs[$row['date_from']][$row['provid']][$row['stkid']] = 1; 
   $rules_slabs3[] = $row['date_from']; 
}
$rules_slabs2 = array_unique($rules_slabs3); 
sort($rules_slabs2, SORT_NUMERIC);

$genesis = '2016-10-01';
//$d[] = $genesis;
$key_date = $key_date_nxt = 0;
$apply_key = $apply_date = '0';
$date_1 = $genesis;
$date_2 = $date;
$i=0;
$apply_rule_arr = array();
while($d<$date_2){
    $s = $i * 1;
    $d = date('Y-m-d',strtotime($genesis.'+'.$s.' days'));
    //echo '<br/> '.$i.' , d:  '.$d.' , k:'.$key_date.', nxt:'.$key_date_nxt.' = '.$rules_slabs2[$key_date];
    if(!empty($rules_slabs2[$key_date_nxt]) && $d >= $rules_slabs2[$key_date_nxt]){
        //echo ' -------- d:'.$d.' key:'.$key_date_nxt.' date:'.$rules_slabs2[$key_date_nxt];
        $apply_key = $key_date_nxt;
        $apply_date = $rules_slabs2[$key_date_nxt];
        $key_date_nxt++;
        if($key_date!=0)
        {
            $key_date = $key_date_nxt;
        }
    }
    //echo ' ,Final:'.$apply_key.' , '.$apply_date.'>>';
    $apply_rule_arr[$d]=$apply_key;
    $i++;
    //exit;
}
//echo '<pre>';print_r($apply_rule_arr);
//echo '<pre>';print_r($rules_slabs2);print_r($rules_slabs);exit;

$and=$and2=$and3='';
if (!empty($date)){
    $and .= " AND DATE_FORMAT(national_stock.tr_date,'%Y-%m-%d') <= '$date'  ";
    $and2 .= " AND DATE_FORMAT(national_stock.tr_date,'%Y-%m-%d') <= '$date'  ";
}

if($province != '10')
    $and3 .= " AND tbl_warehouse.prov_id = $province  ";
$and3 .= " AND stock_batch.item_id = $product  ";

$strSql = "SELECT
                    tbl_warehouse.stkid,
                    stk.stkname,
                    tbl_locations.PkLocID,
                    itminfo_tab.itm_id,
                    DATE_FORMAT(
                            tbl_stock_master.TranDate,
                            '%Y-%m-%d'
                    ) TranDate,
                    (tbl_stock_detail.Qty) qty,
                    itminfo_tab.itm_name,
                    tbl_locations.LocName,tbl_warehouse.wh_name
            FROM
                    tbl_stock_master
            INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
            INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
            INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
            LEFT JOIN tbl_warehouse AS fundingSource ON stock_batch.funding_source = fundingSource.wh_id
            INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
            INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
            LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
            LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
            LEFT JOIN stakeholder AS stk_ofc ON tbl_warehouse.stkofficeid = stk_ofc.stkid
            LEFT JOIN stakeholder AS stk ON tbl_warehouse.stkid = stk.stkid
            LEFT JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
            WHERE
                    DATE_FORMAT(
                            tbl_stock_master.TranDate,
                            '%Y-%m-%d'
                    ) BETWEEN '2016-10-01'
            AND '".$last_date."'
            AND stock_batch.funding_source = 6891
            AND tbl_stock_master.TranTypeID = 2
            AND stock_batch.wh_id = 123
            AND tbl_stock_detail.temp = 0
            AND stk.stk_type_id <> 0 
            $and3
            ORDER BY
                    TranDate";
//echo $strSql;exit;
$issuance_data_ruled = array();
$rsSql = mysql_query($strSql) or die("Error is here");
//query result
?>
    <table class="table table-condensed table-hover" cellpadding="4" cellspacing="0" border="1">
        <tr>
            <th >S.No</th>
            <th >Province</th>
            <th >Stakeholder</th>
            <th >Issued To</th>
            <th >Item</th>
            <th >Quantity</th>
            <th colspan="2">Date of Issuance</th>        
        </tr>
        <?php
        $count = 1;
        $total = 0;
        while($row = mysql_fetch_array($rsSql)){
            
        $rules = $rules_slabs[$rules_slabs2[$apply_rule_arr[$row['TranDate']]]];
    
            if($province == '10')
            {
                if(empty($rules[$row['PkLocID']][$row['stkid']]) || $rules[$row['PkLocID']][$row['stkid']] !=1  )
                    $issuance_data_ruled[] = $row;
            }
            else{
                if(isset($rules[$row['PkLocID']][$row['stkid']]) && $rules[$row['PkLocID']][$row['stkid']]==1  ){
                    $issuance_data_ruled[] = $row;
                }
            }
            
        }
            
        foreach($issuance_data_ruled as $k=>$row){
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><?php echo $row['LocName']; ?></td>
                <td><?php echo $row['stkname']; ?></td>
                <td><?php echo $row['wh_name']; ?></td>
                <td><?php echo $row['itm_name']; ?></td>
                <td align="right"><?php echo number_format(abs($row['qty'])); ?></td>
                <td><?php echo $row['TranDate']; ?></td>
            </tr>
            <?php
            $count++;
            $total += $row['qty'];
        }
        ?>
            <tr>
                <td colspan="5">Total</td>
                <td align="right"><?php echo number_format(abs($total)); ?></td>
                <td colspan="2"> </td>
            </tr>
    </table>
