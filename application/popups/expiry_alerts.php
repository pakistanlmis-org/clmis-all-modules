<?php
include("../includes/classes/AllClasses.php");

include(PUBLIC_PATH . "html/header.php");
$caption = "Expiry Alerts - Batches Expiring in next One Year";
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
$chart_id = 'incoming_pipeline';
?>
<div class="page-content" style="">
<div class="container" >
<div class="widget widget-tabs" style="">    
    <div class="widget-body" >
        
<div class="widget widget-tabs" style="">    
    <div class="widget-body" >
        
        <div class="text-center"><h4><?=$caption?></h4></div>
        <div  style="">
            <table class="table table-striped table-hover table-condensed">
                    <thead style="font-size: 10px">
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Batch No</th>
                            <th>Funding Source</th>
                            <th>Expiry Date</th>
                            <th>Quantity</th>
                            <th>Cartons</th>
                            <th>Status</th>
                        </tr>
                </thead>
                <tbody  style="font-size: 10px">
                    <?php
                    
                                                   
                                                                        
                    
                    $rsSql = $objAlerts->get_expiry_alerts();

                   $num = mysql_num_rows($rsSql);
                   $prod_avail=0;
                   $c2=1;
                   $stock_row='';
                   while ($row_2 = mysql_fetch_assoc($rsSql)) {
                       if(!empty($row_2['Qty']) && $row_2['Qty']>0 && !empty($row_2['qty_carton']) && $row_2['qty_carton']>0)
                           $carton_available=$row_2['Qty']/$row_2['qty_carton'];
                       else
                           $carton_available=0;


                       echo '<tr>
                               <td class="center">'.$c2++.'</td>
                               <td class="center">'.$row_2['itm_name'].'</td>
                               <td class="center">'.$row_2['batch_no'].'</td>
                               <td class="center">'.$row_2['wh_name'].'</td>
                               <td class="center">'.$row_2['batch_expiry'].'</td>
                               <td class="right" align="right">'.number_format($row_2['Qty']).'</td>
                               <td class="right" align="right">'.number_format(floor($carton_available)).'</td>
                               <td class="right">'.$row_2['status'].'</td>
                           </tr>';
                       $prod_avail += $row_2['Qty'];

                   }
                    ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>
         </div>
</div>
</div>
</div>