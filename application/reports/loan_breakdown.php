<?php
include("../includes/classes/AllClasses.php");


$province    = $_REQUEST['prov'];
$product     = $_REQUEST['prod'];
$last_date   = $_REQUEST['date'];
//print_r($_REQUEST);exit;
$and=$and2='';
if (!empty($date)){
    $and .= " AND DATE_FORMAT(national_stock.tr_date,'%Y-%m-%d') <= '$date'  ";
    $and2 .= " AND DATE_FORMAT(national_stock.tr_date,'%Y-%m-%d') <= '$date'  ";
}

$and .= " AND national_stock.prov_id = $province  ";
$and .= " AND national_stock.item_id = $product  ";

$and2 .= " AND national_stock.prov_id = 10 AND national_stock.prov_id2 = $province  ";
$and2 .= " AND national_stock.item_id = $product  ";

$strSql = "(SELECT
                tbl_locations.LocName,
                'Loans/Relocations' as stkname,
                itminfo_tab.itm_name,
                (national_stock.quantity) as qty,
                national_stock.prov_id,
                national_stock.item_id,
                national_stock.ref,
                national_stock.comments,
                national_stock.pk_id
            FROM
                national_stock
                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
            WHERE
                national_stock.ref in ('loan','relocate')
                $and

            ORDER BY
                    national_stock.prov_id,
                    national_stock.item_id) 
      UNION 
                    
(SELECT
                tbl_locations.LocName,
                'Loans/Relocations' as stkname,
                itminfo_tab.itm_name,
                (national_stock.quantity) as qty,
                national_stock.prov_id,
                national_stock.item_id,
                national_stock.ref,
                national_stock.comments,
                national_stock.pk_id
            FROM
                national_stock
                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
            WHERE
                national_stock.ref in ('loan','relocate')
                $and2

            ORDER BY
                    national_stock.prov_id,
                    national_stock.item_id)";

$rsSql = mysql_query($strSql) or die("Error");
//query result
?>
    <table class="table table-condensed table-hover" cellpadding="4" cellspacing="0" border="1">
        <tr>
            <th nowrap>S.No</th>
            <th nowrap>Pk</th>
            <th nowrap>Province / Region</th>
            <th nowrap>Item</th>
            <th nowrap>Quantity</th>
            <th colspan="2">Type of Transaction</th>        
        </tr>
        <?php
        $count = 1;
        $total = 0;
        while($row = mysql_fetch_array($rsSql)){
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><?php echo $row['pk_id']; ?></td>
                <td><?php echo $row['LocName']; ?></td>
                <td><?php echo $row['itm_name']; ?></td>
                <td align="right"><?php echo number_format($row['qty']); ?></td>
                <td><?php echo ($row['ref']=='relocate')?'Realocate':$row['ref']; ?></td>
                <td><?php echo $row['comments']; ?></td>
            </tr>
            <?php
            $count++;
            $total += $row['qty'];
        }
        ?>
            <tr>
                <td colspan="4">Total</td>
                <td align="right"><?php echo number_format($total); ?></td>
                <td colspan="2"> </td>
            </tr>
    </table>
