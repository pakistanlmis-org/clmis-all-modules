<?php
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));

//for now the to date becomes the last day of that month
//$to_date = date("Y-m-t",strtotime($from_date));
//Now
$to_date = $from_date;

$qry_1 = "SELECT
            itminfo_tab.itmrec_id,
            itminfo_tab.itm_id,
            itminfo_tab.itm_name,
            itminfo_tab.generic_name,
            itminfo_tab.method_type,
            itminfo_tab.method_rank,
            itminfo_tab.itm_type
            FROM
            itminfo_tab
            WHERE
            itminfo_tab.itm_id in (1,2,9,3,5,7,8,13)
        ";
$res_1 = mysql_query($qry_1);
$itm_arr = $itm_detail = array();
while($row_1 = mysql_fetch_array($res_1))
{
    $itm_arr[$row_1['itmrec_id']]=$row_1['itm_name'];
    $itm_detail[$row_1['itmrec_id']]['unit']=$row_1['itm_type'];
    
}


$qry_2 = "SELECT
            tbl_locations.LocName,
            tbl_locations.PkLocID,
            sum(summary_province.avg_consumption) as avg_consumption,
            summary_province.item_id
            FROM
            summary_province
            INNER JOIN tbl_locations ON summary_province.province_id = tbl_locations.PkLocID
            INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
            WHERE
                    summary_province.reporting_date = '".$from_date."'
                    
                    AND stakeholder.stk_type_id = 0 
                    AND tbl_locations.ParentID IS NOT NULL
           GROUP BY
            summary_province.province_id,
            summary_province.item_id
            ORDER BY
            summary_province.province_id,
            summary_province.item_id
        ";

$res_2 = mysql_query($qry_2);
$q_data  = array();


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


while($row_2 = mysql_fetch_assoc($res_2))
{
    if($row_2['PkLocID'] == $province ) 
        $q_data[$row_2['item_id']]['amc']=$row_2['avg_consumption'];
}

//echo '<pre>';print_r($itm_arr);print_r($q_data);exit;

//fetching data for pipelines shipemnts of all the stakeholders of the province..
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
        shipments.shipment_date > now() AND
        shipments.stk_id IN  (".implode(',',$funding_stks).") 

        GROUP BY
        itminfo_tab.itmrec_id

        ";

$res_6 = mysql_query($qry_6);
$pipeline_arr =array();
while($row_6 = mysql_fetch_assoc($res_6))
{
    $pipeline_arr[$row_6['itmrec_id']]=$row_6['shipment_quantity'];
}

$new_soh=array();
//starting calculation of new soh

 $and = '';
                
                if(!empty($province))
                {
                    $and .= " AND national_stock.prov_id = $province  ";
                }
                if(!empty($from_date))
                {
                    $and .= " AND national_stock.tr_date < '$from_date'  ";
                }
                
                 
                 $qry = "SELECT
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
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
                    $opening_bal[$row['itm_name']]=$row['opening'];
                    $closing_bal[$row['itm_name']]=$row['opening']; 
                }

                
                $qry = "
                    SELECT
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
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
                        $and
                    GROUP BY
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name
                    ORDER BY
                        national_stock.prov_id,
                        national_stock.stk_id,
                        national_stock.item_id
                ";
                
                $qryRes = mysql_query($qry);
                while($row = mysql_fetch_assoc($qryRes))
                {
                    if(empty($closing_bal[$row['itm_name']]))
                           $closing_bal[$row['itm_name']]=0;

                    $closing_bal[$row['itm_name']]+=$row['qty'];
                    
                }

//echo '<pre>';print_R($itm_arr);print_R($q_data);exit;

?>

    <div class="" style="height:750px;overflow: auto;">
        <table width="100%" border="1" class="table table-condensed ">
        
            <tr style="background-color: #179417;color:#ffffff;">
            <th rowspan="2" style="text-align:left;vertical-align:middle;color:#fffff;" width="25%">Products</th>
            <th rowspan="2" style="text-align:left;vertical-align:middle;color: #ffffff">Status</th>
            <th rowspan="2"  style="text-align:center;vertical-align:middle;" width="10%">Unit</th>
            <th colspan="2"  style="text-align:center;vertical-align:middle;"><?php echo $prov_name;?></th>
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
               if(isset($closing_bal[$name]) && $closing_bal[$name] > 0)
                    $mos = $closing_bal[$name]/$q_data[$id]['amc'];
               else
                   $mos = 0;
               if(!empty($pipeline_arr[$id])) $mos2 = $pipeline_arr[$id] /$q_data[$id]['amc'];
               else $mos2 = 0;
            }
            echo '<tr style="background-color:'.$row_clr.'">
                    <td rowspan="3" style="vertical-align:middle;">'.$name.'</td>
                    <td style="text-align:left">Stock</td>
                    <td rowspan="3"  style="text-align:center;vertical-align:middle;">'.$itm_detail[$id]['unit'].'</td>';
            
            $tooltip_title = ''; 
              
            echo '  <td style="text-align:right"><a href="#" data-toggle="tooltip" title="'.$tooltip_title.'">'.((isset($closing_bal[$name]) && $closing_bal[$name] > 0)?(number_format( $closing_bal[$name] )):'0').'</a></td>
                    <td style="text-align:right">'.(number_format($mos,2)).'</td>
                  </tr>';
            
            echo '<tr style="background-color:'.$row_clr.'">
                    <td style="text-align:left">Pipeline</td>
                    <td style="text-align:right">'.(!empty($pipeline_arr[$id])?number_format($pipeline_arr[$id]):'').'</td>
                    <td style="text-align:right">'.(number_format($mos2,2)).'</td>
                  </tr>';
            echo '<tr style="background-color:'.$row_clr.'">
                    <td style="text-align:left">AMC</td>
                    <td style="text-align:right">'.((isset($q_data[$id]['amc']))?(number_format($q_data[$id]['amc'])):'0').'</td>
                    <td style="text-align:right">-</td>
                  </tr>';
            $row_count++;
        }
           
        ?>
      </table>
    </div>
