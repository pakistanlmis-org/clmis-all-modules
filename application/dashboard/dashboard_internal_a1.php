<?php

//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));

//Previously: the to date becomes the last day of that month
//$to_date = date("Y-m-t",strtotime($from_date));

//Now
$to_date = $from_date;

//list of last 6 months from the date ...
$ex = explode('-',$from_date);
$months_list = array();
$months_list[] = $from_date;
for ($i = 1; $i < 6; $i++) {
    
    $months_list[]= date('Y-m-01', mktime(0, 0, 0, $ex[1]-$i, 1,   $ex[0]));
  
}
krsort($months_list);
  
// echo '<pre>';print_r($months_list);
// exit;   
 
 
$qry_1 = "SELECT
            itminfo_tab.itmrec_id,
            itminfo_tab.itm_name,
            itminfo_tab.itm_id
            FROM
            itminfo_tab
            WHERE
            itminfo_tab.itm_id in (1,2,9,3,5,7,8,13)
        ";
$res_1 = mysql_query($qry_1);
$itm_arr =$itm_arr2 = array();
while($row_1 = mysql_fetch_array($res_1))
{
    $itm_arr[$row_1['itmrec_id']]=$row_1['itm_name'];
     $itm_arr2[$row_1['itmrec_id']]=$row_1['itm_id'];
}


$qry_f = "SELECT
            funding_stk_prov.funding_source_id
            FROM
            funding_stk_prov
            WHERE
            funding_stk_prov.province_id = $province";
$res_f = mysql_query($qry_f);
$funding_stks=array();
while($row_f=mysql_fetch_assoc($res_f))
{
    $funding_stks[$row_f['funding_source_id']]=$row_f['funding_source_id'];
}


$qry_2 = "SELECT
                tbl_locations.LocName,
                tbl_locations.PkLocID,
                year(summary_province.reporting_date) as yr,
                LPAD(month(summary_province.reporting_date), 2, '0')  as mon,
                sum(summary_province.avg_consumption) as avg_consumption,
                summary_province.item_id
            FROM
                summary_province
                INNER JOIN tbl_locations ON summary_province.province_id = tbl_locations.PkLocID
                INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
            WHERE
                    summary_province.reporting_date in (".sprintf("'%s'", implode("','", $months_list ) ).")
                    
                    AND stakeholder.stk_type_id = 0 
                    AND tbl_locations.ParentID IS NOT NULL
                    AND summary_province.province_id = $province
           GROUP BY
                summary_province.province_id,
                summary_province.item_id,
                year(summary_province.reporting_date),
                month(summary_province.reporting_date)
            ORDER BY
                summary_province.province_id,
                summary_province.item_id,
                year(summary_province.reporting_date),
                month(summary_province.reporting_date)
        ";
//echo $qry_2;exit;
$res_2 = mysql_query($qry_2);
$q_data = $total_cons_arr = array();

$total_cons=0;
while($row_2 = mysql_fetch_assoc($res_2))
{
    if($row_2['PkLocID'] == $province ) 
        $q_data[$row_2['item_id']][$row_2['yr'].'-'.$row_2['mon'].'-01']['amc']=$row_2['avg_consumption'];
}
//echo '<pre>';print_r($q_data);exit;

//soh from stock batch table, which is provincial share
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
                    AND stock_batch.funding_source = 6891
		GROUP BY
			itminfo_tab.itm_id
		ORDER BY
			itminfo_tab.frmindex
        ";

$res_3 = mysql_query($qry_3);


while($row_3 = mysql_fetch_assoc($res_3))
{
    $q_data[$row_3['itmrec_id']]['soh']=$row_3['vials'];
    $q_data[$row_3['itmrec_id']]['unit']=$row_3['UnitType'];
}
//echo '<pre>';print_r($itm_arr);print_r($total_cons_arr);print_r($q_data);exit;

    $and = '';

    if(!empty($province))
    {
        $and .= " AND national_stock.prov_id = $province  ";
    }
    if(!empty($from_date))
    {
        $and .= " AND national_stock.tr_date < '$from_date'  ";
    }

//calculating the share of federal stock from national_stock table 

     $qry = "SELECT
                tbl_locations.LocName,
                stakeholder.stkname,
                itminfo_tab.itm_name,
                itminfo_tab.itmrec_id,
                Sum(national_stock.quantity) as opening,
                tbl_locations.LocType
            FROM
                national_stock
                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
            WHERE

                national_stock.ref = 'ob'
                $and
            GROUP BY
                tbl_locations.LocName,

                itminfo_tab.itm_name
            ORDER BY
                national_stock.prov_id,
                national_stock.stk_id,
                national_stock.item_id
    ";
    //query result
     //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $opening_bal=$issue_arr=$stk_arr=$closing_bal=$prov_arr=$prod_arr=array();

    while($row = mysql_fetch_assoc($qryRes))
    {
        $opening_bal[$row['itmrec_id']]=$row['opening'];
        foreach($months_list as $k=> $v)
        {
            $closing_bal[$row['itmrec_id']][$v]=$row['opening']; 
        }
    }
$pipeline_arr =array();
$provincial_soh =array();
    foreach($months_list as $k=> $v)
    {
        
        //issuance of the federal stock
        $qry = "
            SELECT
                tbl_locations.LocName,
                stakeholder.stkname,
                itminfo_tab.itm_name,
                itminfo_tab.itmrec_id,
                Sum(national_stock.quantity) as qty,
                tbl_locations.LocType
            FROM
                national_stock
                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
            WHERE
                national_stock.ref = 'issue'
                AND stakeholder.stk_type_id = 0
                AND national_stock.prov_id = $province 
                AND national_stock.tr_date < '$v'
            GROUP BY
                tbl_locations.LocName,

                itminfo_tab.itm_name
            ORDER BY
                national_stock.prov_id,
                national_stock.stk_id,
                national_stock.item_id
        ";
        //echo $qry;
        $qryRes = mysql_query($qry);
        
        while($row = mysql_fetch_assoc($qryRes))
        {
            $closing_bal[$row['itmrec_id']][$v] = $opening_bal[$row['itmrec_id']];
                    
            if(empty($closing_bal[$row['itmrec_id']][$v]))
                   $closing_bal[$row['itmrec_id']][$v]=0;
        
            
            //this is federal share only , at every month
            $closing_bal[$row['itmrec_id']][$v]+=$row['qty'];
            //$q_data[$row['itmrec_id']]['share_of_stock']=$closing_bal[$row['itmrec_id']][$v];
        }
    
    
        //now fetching the provincial share...
        $qry_5 = "SELECT
                             itminfo_tab.itm_name,
                             itminfo_tab.qty_carton,
                             SUM(tbl_stock_detail.Qty)  AS vials,
                             tbl_itemunits.UnitType,
                             itminfo_tab.itmrec_id,
                             stock_batch.funding_source,
                             tbl_warehouse.wh_name as funding_source_name
                     FROM
                             stock_batch
                     INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                     INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                     INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                     INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                     INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                     WHERE

                             DATE_FORMAT(
                                     tbl_stock_master.TranDate,
                                     '%Y-%m-%d'
                             ) <= '".$v."'
                         AND (
                                 tbl_stock_master.WHIDFrom = 123
                                 OR tbl_stock_master.WHIDTo = 123
                         )
                         AND stock_batch.funding_source in  (".implode(',',$funding_stks).") 
                     GROUP BY
                             itminfo_tab.itm_id,
                             stock_batch.funding_source
                     ORDER BY
                             itminfo_tab.frmindex
             ";
            //echo $qry_5;exit;
            $res_5 = mysql_query($qry_5);
            
            while($row_5 = mysql_fetch_assoc($res_5))
            {
                //this is the prov stock
                if(empty($provincial_soh[$row_5['itmrec_id']][$v]['soh'])) $provincial_soh[$row_5['itmrec_id']][$v]['soh']=0;
                
                $provincial_soh[$row_5['itmrec_id']][$v]['soh']+=$row_5['vials'];

               // if(!empty($row_5['vials']) && !empty($q_data[$row_5['itmrec_id']]['share_of_stock']))
                //    $q_data[$row_5['itmrec_id']]['share_of_stock']+=$row_5['vials'];

               // if(!empty($row_5['vials']))
               // $q_data[$row_5['itmrec_id']]['stock_of_funding_sources'][$row_5['funding_source_name']] = $row_5['vials'];
            }    
        
        //getting shipments data 
            
            $qry_6 = "
                    SELECT
                    itminfo_tab.itmrec_id,
                    sum(shipments.shipment_quantity) as shipment_quantity

                    FROM
                            shipments
                    INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
                    INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
                    INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
                    WHERE
                    shipments.shipment_date > '$v' AND
                    shipments.created_date < '$v' AND
                    shipments.stk_id IN  (".implode(',',$funding_stks).") 

                    GROUP BY
                    itminfo_tab.itmrec_id

                    ";
//echo $qry_6;exit;
            $res_6 = mysql_query($qry_6);
            
            while($row_6 = mysql_fetch_assoc($res_6))
            {
                //echo $v.':'.$row_6['shipment_quantity'].' > ';
                $pipeline_arr[$row_6['itmrec_id']][$v]=$row_6['shipment_quantity'];
            }

        
    }//end of foreach monthlist
    
    //echo '<pre>closing:';print_r($closing_bal);echo ',prov';print_r($provincial_soh);echo ',q ';print_r($q_data);

    //echo '<pre>';print_r($pipeline_arr);

?>

    <div class="" style="height:750px;overflow: auto;">
        <table width="100%" border="1" class="table table-condensed ">
        
            <tr style="background-color: #179417;color:#ffffff;">
                <th rowspan="2" style="text-align:left;vertical-align:middle;color:#fffff;" width="15%">Products</th>
            <th rowspan="2"  style="text-align:center;vertical-align:middle;" width="5%">Unit</th>
            
            <th rowspan="2" style="text-align:left;vertical-align:middle;color: #ffffff">Status</th>
            <?php
            foreach($months_list as $k=> $v)
            {
                echo '<th colspan="2"  style="text-align:center;vertical-align:middle;">'.date('M-Y',strtotime($v)).'</th>';
            }
            ?>
            
        </tr>
        
        <tr  style="background-color: #179417;color:#ffffff;">
        
          <?php
            foreach($months_list as $k=> $v)
            {
                echo '      <th style="text-align:center;vertical-align:middle;">Quantity</th>
                            <th style="text-align:center;vertical-align:middle;">MOS</th>';
            }
            ?>
        </tr>
        <?php
        $row_count=1;
        foreach($itm_arr as $id=>$name)
        {
            if($row_count%2 == 0) $row_clr='#a6d785';
            else $row_clr = '';
            
            echo '<tr style="background-color:'.$row_clr.'">
                    <td rowspan="4" style="vertical-align:middle;">'.$name.'</td>
                    <td rowspan="4"  style="text-align:center;vertical-align:middle;">'.$q_data[$id]['unit'].'</td>
                    
                    <td style="text-align:left">Stock</td>
                   ';
            foreach($months_list as $k=> $v)
            {
                $stock_val=0;
                $stock_val = (isset($closing_bal[$id][$v])?$closing_bal[$id][$v]:'0') + (isset($provincial_soh[$id][$v]['soh'])?$provincial_soh[$id][$v]['soh']:'0') ;
               
                if(empty($q_data[$id][$v]['amc']) || $q_data[$id][$v]['amc']==0) $mos =0;
                else $mos = (isset($stock_val)?$stock_val:'0')/$q_data[$id][$v]['amc'];
                
                echo '  <td style="text-align:right">'.(number_format($stock_val)).'</td>
                        <td style="text-align:right">'.(number_format($mos,2)).'</td>
                    ';
            }
            echo '</tr> ';   
            
            
            echo '<tr style="background-color:'.$row_clr.'">
                    <td style="text-align:left">Pipeline</td>';
            foreach($months_list as $k=> $v)
            {
                $pipeline_val=0;
                $pipeline_val = (isset($pipeline_arr[$id][$v])?$pipeline_arr[$id][$v]:'0');
                
                if(empty($pipeline_val) || empty($q_data[$id][$v]['amc'])|| $q_data[$id][$v]['amc']==0) $mos2 =0;
                else $mos2 = (isset($pipeline_val)?$pipeline_val:'0')/$q_data[$id][$v]['amc'];
                
                 echo  '<td style="text-align:right" class="'.((isset($pipeline_val)&& $pipeline_val>0)?'pipeline_anchor':'').'" data-date = "'.($v).'" data-id = "'.($itm_arr2[$id]).'">'.(number_format($pipeline_val)).'</td>
                    <td style="text-align:right">'.(number_format($mos2,2)).'</td>
                  ';
            }
            echo '</tr> ';   
            
            echo '<tr style="background-color:'.$row_clr.'">
                    <td style="text-align:left">Total</td>';
            foreach($months_list as $k=> $v)
            {
                $tot_val=0;
                $tot_val += (isset($closing_bal[$id][$v])?$closing_bal[$id][$v]:'0') + (isset($provincial_soh[$id][$v]['soh'])?$provincial_soh[$id][$v]['soh']:'0') ;
               
                $tot_val += (isset($pipeline_arr[$id][$v])?$pipeline_arr[$id][$v]:'0');
                
                if(empty($tot_val) || empty($q_data[$id][$v]['amc'])|| $q_data[$id][$v]['amc']==0) $mos3 =0;
                else $mos3 = (isset($tot_val)?$tot_val:'0')/$q_data[$id][$v]['amc'];
                
                echo  '<td style="text-align:right" class="pipeline_anchor ">'.(number_format($tot_val)).'</td>
                    <td style="text-align:right">'.(number_format($mos3,2)).'</td>
                  ';
            }
            echo '</tr> ';   
            
            echo '<tr style="background-color:'.$row_clr.'">
                    <td style="text-align:left">AMC</td>';
            foreach($months_list as $k=> $v)
            {
                 echo '<td style="text-align:right">'.((isset($q_data[$id][$v]['amc'])?number_format($q_data[$id][$v]['amc']):'0')).'</td>
                    <td style="text-align:right"> </td>
                    ';
            }
             echo '</tr> ';   
            $row_count++;
        }
           
        ?>
      </table>
    </div>
