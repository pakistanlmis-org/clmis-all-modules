<?php
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");

$caption = "Incoming Pipeline Supplies";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
$chart_id = 'incoming_pipeline';
?>

<div class="widget widget-tabs" style="height:480px;overflow: auto">    
    <div class="widget-body" >
        
        <div class="text-center"><?=$caption?></div>
        <div  style="">
            <table class="table table-striped table-hover table-condensed">
                    <thead style="font-size: 10px">
                        <tr>
                            <th>#</th>
                            <th>Commodity</th>
                            <th>Procured By</th>
                            <th width="20%">EDA</th>
                            <th style="text-align:right">Quantity</th>
                            <th style="text-align:right">Cartons</th>
                            <th style="text-align:right">Pallets</th>
                        </tr>
                </thead>
                <tbody  style="font-size: 10px">
                    <?php
                    $qry = "SELECT
                                shipments.pk_id,
                                shipments.item_id,
                                shipments.shipment_date,
                                shipments.shipment_quantity,
                                sum(tbl_stock_detail.Qty) as received_qty,
                                shipments.stk_id,
                                shipments.procured_by,
                                shipments.`status`,
                                tbl_locations.LocName,
                                tbl_warehouse.wh_name,
                                itminfo_tab.itm_name,
                                itminfo_tab.qty_carton,
                                (select stakeholder_item.carton_per_pallet 
                                    from stakeholder_item where stakeholder_item.stkid = ".$_SESSION['user_stakeholder1']." 
                                    and stakeholder_item.stk_item = itminfo_tab.itm_id) as carton_per_pallet

                            FROM
                                shipments
                            INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
                            INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
                            INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
                            LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = shipments.pk_id
                            LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                            WHERE
                                /*shipments.shipment_date > now() AND */
                                shipments.status NOT IN ('Cancelled','Received')
                            GROUP BY
                                shipments.pk_id    
     ";
                    //Query result
                    //echo $qry;exit;
                    $qryRes = mysql_query($qry);
                    $c=1;
                    while($row = mysql_fetch_assoc($qryRes))
                    {
                        $s_q = isset($row['shipment_quantity'])?$row['shipment_quantity']:'0';
                        $r_q = isset($row['received_qty'])?$row['received_qty']:'0';
                        $remaining_q = $s_q - $r_q;
                        
                        $cartons = (!empty($row['qty_carton'] && $row['qty_carton']>0)?round($row['shipment_quantity']/$row['qty_carton']):'0');
                        $pallets = (!empty($row['carton_per_pallet'] && $row['carton_per_pallet']>0)?round($cartons/$row['carton_per_pallet']):'0');
                        
                        $cls = "";
                        if($row['shipment_date'] < date('Y-m-d')) $cls = "error";
                        
                        if($remaining_q > 0)
                        echo '<tr class="'.$cls.'">
                                <td>'.$c++.'</td>
                                <td>'.$row['itm_name'].'</td>
                                <td>'.$row['LocName'].'</td>
                                <td>'.date('Y-M-d',strtotime($row['shipment_date'])).'</td>
                                <td style="text-align:right">'.number_format(max($remaining_q,0)).'</td>
                                <td style="text-align:right">'.number_format($cartons).'</td>
                                <td style="text-align:right">'.number_format($pallets).'</td>
                                
                            </tr>';

                    }
                    ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>