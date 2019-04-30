<?php
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$funding_source = $_REQUEST['stakeholder'];

$qry_comments= "SELECT
                    GROUP_CONCAT(funding_stk_prov.stakeholder_id) as stk
                FROM
                    funding_stk_prov
                INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                WHERE
                    funding_stk_prov.province_id = $province AND
                    funding_stk_prov.funding_source_id = $funding_source

            ";
$res = mysql_query($qry_comments);
$comments_arr =array();
$row=mysql_fetch_assoc($res);
//print_r($row);exit;
$stakeholder    = (!empty($row['stk']))?$row['stk']:'1';
$stk_name       = $_REQUEST['stk_name'];

//for now the to date becomes the last day of that month
$to_date = date("Y-m-t",strtotime($from_date));

 $qry_1 = "SELECT
            itminfo_tab.itmrec_id,
            itminfo_tab.itm_name,itminfo_tab.itm_type
            FROM
            itminfo_tab
            WHERE
            itminfo_tab.itm_id in (1,2,9,3,5,7,8,13) ORDER BY method_rank
        ";
$res_1 = mysql_query($qry_1);
$itm_arr =$q_data= array();
while($row_1 = mysql_fetch_array($res_1))
{
    $itm_arr[$row_1['itmrec_id']]=$row_1['itm_name'];
    $q_data[$row_1['itmrec_id']]['unit']=$row_1['itm_type'];
}


$qry_2 = "SELECT
            tbl_locations.LocName,
            tbl_locations.PkLocID,
            summary_province.stakeholder_id,
            sum(summary_province.avg_consumption) as avg_consumption,
            summary_province.item_id
            FROM
            summary_province
            INNER JOIN tbl_locations ON summary_province.province_id = tbl_locations.PkLocID
            INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
            WHERE
                    summary_province.reporting_date = '".$from_date."'
                    AND summary_province.province_id = $province
                    AND stakeholder.stk_type_id = 0 
                    AND tbl_locations.ParentID IS NOT NULL
                    AND summary_province.stakeholder_id in ($stakeholder)
           GROUP BY
            summary_province.province_id,
            summary_province.item_id
            ORDER BY
            summary_province.province_id,
            summary_province.item_id
        ";
//echo $qry_2;
$res_2 = mysql_query($qry_2);
 $total_cons_arr = array();

$total_cons=0;
while($row_2 = mysql_fetch_assoc($res_2))
{
    $q_data[$row_2['item_id']]['amc']=$row_2['avg_consumption'];
    
    $total_cons += $row_2['avg_consumption'];
    
    if(empty($total_cons_arr[$row_2['item_id']])) $total_cons_arr[$row_2['item_id']]=0;
    $total_cons_arr[$row_2['item_id']] += $row_2['avg_consumption'];
}
//echo '<pre>';print_r($q_data);exit;

$qry_3 = "SELECT
			itminfo_tab.itm_name,
			itminfo_tab.qty_carton,
			SUM(tbl_stock_detail.Qty)  AS vials,
			tbl_itemunits.UnitType,
                        itminfo_tab.itmrec_id
		FROM
			stock_batch
		INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
		INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
		INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
		WHERE
			
                        DATE_FORMAT(
                                tbl_stock_master.TranDate,
                                '%Y-%m-%d'
                        ) <= '".$to_date."'
                    AND (
                            tbl_stock_master.WHIDFrom = 123
                            OR tbl_stock_master.WHIDTo = 123
                    )
                    AND stock_batch.funding_source = $funding_source
		GROUP BY
			itminfo_tab.itm_id
		ORDER BY
			itminfo_tab.frmindex
        ";
//echo $qry_3;
$res_3 = mysql_query($qry_3);


while($row_3 = mysql_fetch_assoc($res_3))
{
    $q_data[$row_3['itmrec_id']]['soh']=$row_3['vials'];
}
//echo '<pre>';print_r($itm_arr);print_r($total_cons_arr);print_r($q_data);exit;
foreach($itm_arr as $itm_id => $itm_name)
{
    if(empty($q_data[$itm_id]['amc'])) $q_data[$itm_id]['amc'] =0;   
}

//fetching data for pipelines shipemnts of all the stakeholders of the province..

$qry_6 = "
        SELECT
            itminfo_tab.itmrec_id,
            (shipments.shipment_quantity) as shipment_quantity,
            sum(tbl_stock_detail.Qty) as received_qty,
            shipments.reference_number,
            itminfo_tab.itm_name,
            tbl_warehouse.wh_name,
            tbl_locations.LocName,
            shipments.shipment_date,
            shipments.`status`

        FROM
                shipments
        INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
        INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
        INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
        LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = shipments.pk_id
        LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
        WHERE
            shipments.shipment_date > '$from_date' AND
            shipments.stk_id = $funding_source
            AND shipments.status NOT IN ('Cancelled','Received')
        GROUP BY
            shipments.pk_id,
            itminfo_tab.itm_id

        ";

$res_6 = mysql_query($qry_6);
$pipeline_arr =array();
$pipeline_detail_arr = array();
while($row_6 = mysql_fetch_assoc($res_6))
{
    $pipeline_detail_arr[$row_6['itmrec_id']][] = $row_6;
    $s_q = isset($row_6['shipment_quantity'])?$row_6['shipment_quantity']:'0';
    $r_q = isset($row_6['received_qty'])?$row_6['received_qty']:'0';
    $remaining_q = $s_q - $r_q;
    if(empty($pipeline_arr[$row_6['itmrec_id']]))$pipeline_arr[$row_6['itmrec_id']]=0;
    $pipeline_arr[$row_6['itmrec_id']] += $remaining_q;
}

//echo '<pre>';print_r($itm_arr);print_r($pipeline_detail_arr);print_r($pipeline_arr);exit;

?>
<div >    
    <div  style="height:750px;overflow: auto;">
        <table width="100%" border="1" class="table table-condensed ">
        
            <tr style="background-color: #179417;color:#ffffff;">
            <th rowspan="2" style="text-align:left;vertical-align:middle;color:#fffff;" width="25%">Products</th>
            <th rowspan="2" style="text-align:left;vertical-align:middle;color: #ffffff">Status</th>
            <th rowspan="2"  style="text-align:center;vertical-align:middle;" width="10%">Unit</th>
            <th colspan="2"  style="text-align:center;vertical-align:middle;"><?php echo $stk_name;?></th>
        </tr>
        
        <tr  style="background-color: #179417;color:#ffffff;">
          <th style="text-align:center;vertical-align:middle;">Quantity</th>
          <th style="text-align:center;vertical-align:middle;">MOS</th>
        </tr>
        <?php
        $row_count=1;
        foreach($itm_arr as $id=>$name)
        {
            if($row_count%2 == 0) $row_clr='#a6d785';
            else $row_clr = '';
            if(empty ($q_data[$id]['amc']) || $q_data[$id]['amc'] ==0) 
            {
                $mos=0;
                $mos2=0;
            }
            else
            {
               $mos = (!empty($q_data[$id]['soh'])?$q_data[$id]['soh']:'0')/$q_data[$id]['amc'];
               if(!empty($pipeline_arr[$id])) $mos2 = $pipeline_arr[$id] /$q_data[$id]['amc'];
               else $mos2 = 0;
            }
            echo '<tr style="background-color:'.$row_clr.'">
                    <td rowspan="3" style="vertical-align:middle;">'.$name.'</td>
                    <td style="text-align:left">Stock</td>
                    <td rowspan="3"  style="text-align:center;vertical-align:middle;">'.(!empty($q_data[$id]['unit'])?$q_data[$id]['unit']:'0').'</td>
                    <td style="text-align:right">'.(number_format((!empty($q_data[$id]['soh'])?$q_data[$id]['soh']:'0'))).'</td>
                    <td style="text-align:right">'.(number_format($mos,2)).'</td>
                  </tr>';
            
//            echo '<tr style="background-color:'.$row_clr.'">
//                    <td style="text-align:left">Pipeline</td>
//                    <td style="text-align:right">'.(!empty($pipeline_arr[$id])?number_format($pipeline_arr[$id]):'0').'</td>
//                    <td style="text-align:right">'.(number_format($mos2,2)).'</td>
//                  </tr>';
            
             echo '<tr style="background-color:' . $row_clr . '">
                    <td style="text-align:left">Pipeline</td>';
            $pipeline_val = 0;
            $pipeline_val = (isset($pipeline_arr[$id]) ? $pipeline_arr[$id] : '0');

            if (empty($pipeline_val) || empty($q_data[$id]['amc']) || $q_data[$id]['amc'] == 0)
                $mos2 = 0;
            else
                $mos2 = (isset($pipeline_val) ? $pipeline_val : '0') / $q_data[$id]['amc'];

            //echo '<td style="text-align:right" class="' . ((isset($pipeline_val) && $pipeline_val > 0) ? 'pipeline_anchor' : '') . '" title="Click for pipeline details"  data-date = "' . ($v) . '" data-id = "' . ($itm_arr2[$id]) . '">' . ((isset($pipeline_val) && $pipeline_val > 0) ? '<i class="fa fa-bar-chart-o" style="color:#000 !important";></i>';
            echo '  <td style="text-align:right">';
            if(!empty($pipeline_val) && $pipeline_val>0) 
            {
                $data_content2 = "";
                $data_content2 .= '<b>Shipments in pipeline </b>';
                foreach($pipeline_detail_arr[$id] as $k => $pipe_data)
                {
                    $s_q = isset($pipe_data['shipment_quantity'])?$pipe_data['shipment_quantity']:'0';
                    $r_q = isset($pipe_data['received_qty'])?$pipe_data['received_qty']:'0';
                    $remaining_q = $s_q - $r_q;
                    $data_content2 .= '<br/>*'.$pipe_data['wh_name'].' '.date('Y/M/d',strtotime($pipe_data['shipment_date'])).': '.number_format($remaining_q).'';
                }
                $data_content2 .= ' <br/>';
                echo '<a data-toggle="popover" title="Pipeline Details" data-trigger="hover" data-placement="left" data-html="true" data-content="'.$data_content2.'"><i style="color:black !important;" class="fa fa-table"></i></a>';
            }
            echo ' '.( isset($pipeline_val)?number_format($pipeline_val) : '0');
            echo '</td>
            <td style="text-align:right">' . (($mos2 > 0) ? number_format($mos2, 2) : '0') . '</td>
          ';

        echo '</tr> ';
            
            
            
            echo '<tr style="background-color:'.$row_clr.'">
                    <td style="text-align:left">AMC</td>
                    <td style="text-align:right">'.(number_format($q_data[$id]['amc'])).'</td>
                    <td style="text-align:right">-</td>
                  </tr>';
            $row_count++;
        }
           
        ?>
      </table>
    </div>
</div>