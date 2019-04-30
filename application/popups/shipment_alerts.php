<?php

include("../includes/classes/AllClasses.php");

include(PUBLIC_PATH . "html/header.php");
$caption = "Incoming Pipeline Supplies";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
$chart_id = 'incoming_pipeline';
?>

<div class="page-content" style="">
<div class="container" >
<div class="widget widget-tabs" style="">    
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
                        </tr>
                </thead>
                <tbody  style="font-size: 10px">
                    <?php
                    
                $qryRes = $objAlerts->get_shipment_alerts();
    
                    $c=1;
                    while($row = mysql_fetch_assoc($qryRes))
                    {
                            $cartons = (!empty($row['qty_carton'] && $row['qty_carton']>0)?round($row['shipment_quantity']/$row['qty_carton']):'0');
                            
                        echo '<tr>
                                <td>'.$c++.'</td>
                                <td>'.$row['itm_name'].'</td>
                                <td>'.$row['LocName'].'</td>
                                <td>'.$row['shipment_date'].'</td>
                                <td style="text-align:right">'.number_format($row['shipment_quantity']).'</td>
                                <td style="text-align:right">'.number_format($cartons).'</td>
                                
                            </tr>';

                    }
                    ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>