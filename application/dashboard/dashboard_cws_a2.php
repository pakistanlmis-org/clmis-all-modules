<?php
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';
$whId = 123;
$stkId = 1;

//Chart caption
$caption = "Storage Vs Space Occupation Trend";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'storage_vs_space';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        $quarter_arr =array();
        $quarter_arr[]='2017-1';
        $quarter_arr[]='2017-2';
        $quarter_arr[]='2017-3';
        $quarter_arr[]='2017-4';
        $quarter_arr[]='2018-1';
        $quarter_arr[]='2018-2';
        $quarter_arr[]='2018-3';
        $quarter_arr[]='2018-4';
        
        //echo '<pre>';print_r($quarter_arr);
        
        //Query for shipment main dashboard
	 $qry = "SELECT
                    YEAR(shipments.shipment_date) as y,
                    QUARTER(shipments.shipment_date) as q,
                            shipments.item_id,

                            SUM(shipments.shipment_quantity) as ship_quantity,
                            (
                                    SELECT
                                            stakeholder_item.carton_per_pallet
                                    FROM
                                            stakeholder_item
                                    WHERE
                                            stakeholder_item.stk_id = shipments.manufacturer
                                    AND stakeholder_item.stk_item = itminfo_tab.itm_id
                            ) AS carton_per_pallet,
                            (
                                    SELECT
                                            stakeholder_item.quantity_per_pack
                                    FROM
                                            stakeholder_item
                                    WHERE
                                            stakeholder_item.stk_id = shipments.manufacturer
                                    AND stakeholder_item.stk_item = itminfo_tab.itm_id
                            ) AS qty_carton,
                    shipments.manufacturer

                    FROM
                            shipments
                    INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
                    INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
                    INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
                    WHERE
                            shipments.shipment_date > now()
                            AND shipments.status NOT IN ('Cancelled','Received')
                    GROUP BY
                    shipments.item_id,
                    shipments.manufacturer,
                    YEAR(shipments.shipment_date),
                    QUARTER (shipments.shipment_date)
            ";
            //echo $qry;exit;
            $res = mysql_query($qry);
            $incoming=$quarter_wise=array();
            while($row = mysql_fetch_assoc($res))
            {
                $incoming[] = $row;
                if(empty($quarter_wise[$row['y']][$row['q']]['incoming_pallets'])) $quarter_wise[$row['y']][$row['q']]['incoming_pallets'] = 0;
                if(     (!empty($row['ship_quantity']))
                        && (!empty($row['qty_carton'])) 
                        && (!empty($row['carton_per_pallet']))
                  )
                {
                    $b= $row['ship_quantity'] / $row['qty_carton'];
                    $pal_val = $b / $row['carton_per_pallet'];
                }
                else
                {
                    $pal_val= 0;
                }
                $quarter_wise[$row['y']][$row['q']]['incoming_pallets'] += $pal_val;
            }
           $qry = "SELECT
                        Sum(stock_batch.Qty) AS soh,
			sum(tbl_stock_detail.qty) as soh_for_other_case,
                        stock_batch.item_id,
                        itminfo_tab.itm_name,
                         (select stakeholder_item.carton_per_pallet 
                                    from stakeholder_item where stakeholder_item.stk_id = stock_batch.manufacturer
                                    and stakeholder_item.stk_item = itminfo_tab.itm_id) as carton_per_pallet,
                            (
                                    SELECT
                                            stakeholder_item.quantity_per_pack 
                                    from stakeholder_item where stakeholder_item.stk_id = stock_batch.manufacturer
                                    and stakeholder_item.stk_item = itminfo_tab.itm_id
                            ) AS qty_carton,
                        stock_batch.manufacturer
                        FROM
                        tbl_stock_master
                        INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                        INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
                        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                        WHERE
                        (tbl_stock_master.WHIDFrom IN ((
                                        SELECT
                                                tbl_warehouse.wh_id
                                        FROM
                                                stakeholder
                                        INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                        WHERE
                                                stakeholder.stk_type_id = 2
                                        AND tbl_warehouse.is_active = 1
                                ))  ) AND
                        itminfo_tab.itm_category = 1
                        /*AND tbl_stock_master.TranDate < '2017-01-01'
                        AND stock_batch.wh_id = 123  */
                        GROUP BY
                                stock_batch.item_id,
                                stock_batch.manufacturer
            ";
            
           
            //echo $qry;exit;
            $curr_stock='';
            $curr_pallets=0;
            $q_res = mysql_query($qry);
            while($row = mysql_fetch_assoc($q_res))
            {
                $curr_stock[]=$row;
                
                
                if(     (!empty($row['soh']))
                        && (!empty($row['qty_carton'])) 
                        && (!empty($row['carton_per_pallet']))
                  )
                {
                    $b= $row['soh'] / $row['qty_carton'];
                    $pal_val = $b / $row['carton_per_pallet'];
                }
                else
                {
                    $pal_val= 0;
                }
                $curr_pallets += $pal_val;
                
            }
            //echo 'CURR_PALLETS:::'.$curr_pallets;
             $qry = "SELECT
                        stock_batch.item_id,
                        Sum(ABS(tbl_stock_detail.Qty)) AS issued,
                        stock_batch.manufacturer,
                        (select carton_per_pallet from stakeholder_item where stakeholder_item.stk_id = stock_batch.manufacturer) as carton_per_pallet,
                        (select quantity_per_pack from stakeholder_item where stakeholder_item.stk_id = stock_batch.manufacturer) as qty_carton 
                        FROM
                                tbl_stock_master
                        INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                        INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
                        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
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
                        tbl_stock_master.TranDate >=  DATE_SUB(now() , INTERVAL 1 YEAR)
                        GROUP BY
                                stock_batch.item_id,
                                stock_batch.manufacturer

            ";
            //echo $qry;exit;
            $issued='';
            $issued_in_last_year =0;
            $q_res = mysql_query($qry);
            while($row = mysql_fetch_assoc($q_res))
            {
                $issued[]=$row;
                
                
                if(     (!empty($row['issued']))
                        && (!empty($row['qty_carton'])) 
                        && (!empty($row['carton_per_pallet']))
                  )
                {
                    $b= $row['issued'] / $row['qty_carton'];
                    $pal_val = $b / $row['carton_per_pallet'];
                }
                else
                {
                    $pal_val= 0;
                }
                $issued_in_last_year += $pal_val;
            }
            $avg_issuance = $issued_in_last_year/12;
            
    



//xml for chart
    $xmlstore = '<chart caption="Storage vs Space Occupation Trend" subcaption="" exportEnabled="1" xaxisname="Quarters" yaxisname="No. of Pallets" linethickness="2" palettecolors="#0075c2" basefontcolor="#333333" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" showborder="0" bgcolor="#ffffff" showshadow="0" canvasbgcolor="#ffffff" canvasborderalpha="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlineisdashed="1" divlinedashlen="1" divlinegaplen="1" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" showalternatehgridcolor="0">';
    
    //$temp_val=0;
    $q_final_val=0;
    $temp_val = $curr_pallets;
    foreach($quarter_arr as $k => $yq)
    {
        $q_final_val=0;
        $a = explode('-',$yq);
        
        $y = $a[0];
        $q = $a[1];
        
        //starting month
        $q_final_val = $temp_val;
        
        if(!empty($quarter_wise[$y][$q]['incoming_pallets']))
        {
            $incom= $quarter_wise[$y][$q]['incoming_pallets'];
        }
        else
        {
            $incom = 0;
        }
        $q_final_val += $incom;
        
        $q_final_val -= $avg_issuance;
        
        //$xmlstore .= '                <set label="Mon" value="15123" />';
        $xmlstore .= '                <set label="'.$yq.'" value="'.number_format($q_final_val,2,'.','').'" />';
        
        //echo '</br>--Final:'.$q_final_val.',TEMP:'.$temp_val.',AVG'.$avg_issuance.',INCOM'.$incom;
        $temp_val=$q_final_val;
    }

    $xmlstore .= '                <trendlines>';
    $xmlstore .= '                    <line startvalue="3200" color="#1aaf5d" displayvalue="Warehouse{br}Stock{br}Capacity" valueonright="1" thickness="2" />';
    $xmlstore .= '                </trendlines>';
    $xmlstore .= '            </chart>';
    //end chart
   
    
    //echo '>curr_palets:'.$curr_pallets.', AVG ISSUANCE PER MONTH:'.$avg_issuance.'..ISSUED LAST YEAR >>'.$issued_in_last_year.'<pre>CURRENT';print_r($curr_stock);echo 'ISSUED';print_r($issued);echo 'INCOMING';print_r($incoming);echo 'QUART';print_r($quarter_wise);exit;
            
    
    
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/Line.swf", "", $xmlstore, $chart_id, '100%', 450, false, false);
    ?>
	</div>
</div>