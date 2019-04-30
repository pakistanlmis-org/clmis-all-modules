<?php
//echo '<pre>';print_r($_REQUEST);exit;
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$dist_name= $_REQUEST['dist_name'];
$dist = $_REQUEST['dist'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
$from_date_1 = date("Y-m-01", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$itm_id = $_REQUEST['itm_id'];
$ex = explode('-',$from_date);
//Chart caption
$caption = "";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'pipeline_pie';

$qry    = "SELECT
            tbl_warehouse.wh_id,
            tbl_warehouse.wh_name,
            tbl_warehouse.dist_id,
            tbl_warehouse.prov_id,
            tbl_warehouse.stkid,
            tbl_warehouse.im_start_month,
            tbl_warehouse.is_allowed_im,
            s.stkname
            FROM
            tbl_warehouse
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            INNER JOIN stakeholder s ON tbl_warehouse.stkid = s.stkid
            WHERE
            tbl_warehouse.dist_id = $dist AND
            stakeholder.lvl = 3 AND
            tbl_warehouse.stkid IN (1,2,7,73)
";
//echo $qry;exit;
$res_warehouse   = mysql_query($qry);
$wh_im_arr = $wh_info = $wh_stock_arr = array();
while($row_warehouse = mysql_fetch_assoc($res_warehouse))
{
//    echo '<pre>';print_r($row_warehouse);exit;
    $wh_id_this = $row_warehouse['wh_id'];
    $wh_info[$row_warehouse['wh_id']] = $row_warehouse;
//    echo '<br/>this warehouse :'.$row_warehouse['wh_id'].'.end.';
    if(!empty($row_warehouse['im_start_month']) && !empty($row_warehouse['is_allowed_im']) && $row_warehouse['is_allowed_im']==1)
    {
        $wh_im_arr[$row_warehouse['wh_id']] = true;
        $im_start_month = $row_warehouse['im_start_month'];
        if($from_date >= $im_start_month)
        {
            //fetch im txns
            $qry = "SELECT
                itminfo_tab.itm_id,
                stakeholder.stkname manu,
                stock_batch.batch_id,
                stock_batch.batch_no,
                stakeholder_item.quantity_per_pack,
                stakeholder_item.carton_per_pallet,
                SUM(tbl_stock_detail.Qty) AS CB,
                tbl_warehouse.wh_name FundingSource,
                itminfo_tab.itm_name,
                itm_type ,
                stakeholder_item.carton_per_pallet,
                stakeholder_item.quantity_per_pack,
                stakeholder.stkname
            FROM
			itminfo_tab
		INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
		INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
		INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
		LEFT JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
            WHERE
                DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$from_date'
                AND (
                            (tbl_stock_master.WHIDFrom = $wh_id_this AND tbl_stock_master.TranTypeID = 2)
                            OR (tbl_stock_master.WHIDTo = $wh_id_this AND tbl_stock_master.TranTypeID = 1)
                            OR (tbl_stock_master.WHIDFrom = $wh_id_this AND tbl_stock_master.WHIDTo = $wh_id_this AND tbl_stock_master.TranTypeID > 2)
                    )
                AND tbl_stock_master.temp=0
                AND itminfo_tab.itm_id =  $itm_id
            GROUP BY
			itminfo_tab.itm_id,stock_batch.batch_id
            ";
            //echo $qry;
            $res1 = mysql_query($qry);
            while($row_im_stock=mysql_fetch_assoc($res1))
            {
                $wh_stock_arr[$row_warehouse['wh_id']][$row_im_stock['itm_id']][$row_im_stock['batch_id']] = $row_im_stock;
            }
            
//            echo '<pre>';print_r($wh_stock_arr);exit;
        }
    }
    else
    {
        //fetching data from hf_data
        $wh_im_arr[$row_warehouse['wh_id']] = false;
        
        $qry_2 = "SELECT
                        tbl_warehouse.wh_name,
                        tbl_wh_data.RptDate,
                        tbl_wh_data.wh_cbl_a as CB,
                        itminfo_tab.itm_id,
                        itminfo_tab.itm_name,
                        tbl_warehouse.wh_id,
                        itminfo_tab.itm_type
                    FROM
                    tbl_wh_data
                    INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
                    WHERE  
                        tbl_warehouse.dist_id = $dist AND
                        tbl_wh_data.RptDate = '".$from_date_1."' AND
                        itminfo_tab.itm_id = $itm_id AND
                        stakeholder.lvl = 3
 
                ";
                
        //echo $qry_2;exit;
        $res_2 = mysql_query($qry_2);
        $hf_data  = array();
        while($row_hf = mysql_fetch_assoc($res_2))
        {
        $hf_data[$row_hf['wh_id']][$row_hf['itm_id']] = $row_hf;
        }
    }
}
//echo '<pre>';print_r($hf_data);exit;
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
	<?php 
        $total_stock = 0;
            echo '<table class="table table-condensed table-hover">';
            foreach($wh_info as $wh_id_main => $wh_information)
            {
                echo '<tr class="info bold">';
                    echo '<td colspan="10">'.$wh_information['wh_name'].' - '.$wh_information['stkname'].'</td>';
                echo '</tr>';
                
                //for IM data
                if(!empty($wh_stock_arr[$wh_id_main]))
                {
                     echo '<tr class="head bold h6">';
                        echo '<td>Product</td>';
                        echo '<td>Batch</td>';
                        echo '<td align="right">Stock</td>';
                        echo '<td>Type</td>';
                        echo '<td>Manufacturer</td>';
                        echo '<td>Qty / Pack</td>';
                        echo '<td>Cartons / Pallet</td>';
                    echo '</tr>';
                    foreach($wh_stock_arr[$wh_id_main] as $itm_id => $itm_data)
                    {
                        foreach($itm_data as $batch_id => $batch_data)
                        {
                            echo '<tr>';
                                echo '<td>'.$batch_data['itm_name'].'</td>';
                                echo '<td>'.$batch_data['batch_no'].'</td>';
                                echo '<td  align="right">'.number_format($batch_data['CB']).'</td>';
                                echo '<td>'.$batch_data['itm_type'].'</td>';
                                echo '<td>'.$batch_data['manu'].'</td>';
                                echo '<td>'.$batch_data['quantity_per_pack'].'</td>';
                                echo '<td>'.$batch_data['carton_per_pallet'].'</td>';
                            echo '</tr>';   
                            @$this_wh_total += $batch_data['CB'];
                        } 
                        echo '<tr>';
                                echo '<td colspan="2">TOTAL : </td>';
                                echo '<td>'.@$this_wh_total.'</td>';
                            echo '</tr>';
                            $total_stock+=$this_wh_total;
                    } 
                }
                elseif(!empty($hf_data[$wh_id_main]))
                {
                    //for hf data only
                    echo '<tr class="head bold h6">';
                        echo '<td>Product</td>';
                        echo '<td>Stock</td>';
                        echo '<td>Type</td>';
                    echo '</tr>';
                    foreach($hf_data[$wh_id_main] as $itm_id => $itm_data)
                    {
                        echo '<tr>';
                            echo '<td>'.$itm_data['itm_name'].'</td>';
                            echo '<td align="right">'.number_format($itm_data['CB']).'</td>';
                            echo '<td>'.$itm_data['itm_type'].'</td>';
                            echo '<td></td>';
                            echo '<td></td>';
                        echo '</tr>';   
                        $total_stock+=$itm_data['CB'];
                    }
                }
                else
                {
                    echo '<tr>';
                        echo '<td colspan="10">No Record Found</td>';
                    echo '</tr>';
                }
                
                
            }
            
                echo '<tr class="success">';
                    echo '<td colspan="">Total Stock : </td>';
                    echo '<td colspan="" align="right">'.number_format($total_stock).'</td>';
                            echo '<td></td>';
                            echo '<td></td>';
                echo '</tr>';
            echo '</table>';
    ?>
	</div>
</div>