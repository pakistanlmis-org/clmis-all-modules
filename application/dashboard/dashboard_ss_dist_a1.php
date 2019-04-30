<?php
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];

$dist_name= $_REQUEST['dist_name'];
$dist = $_REQUEST['dist'];
$from_date = date("Y-m-01", strtotime($_REQUEST['from_date']));


//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));

//Previously: the to date becomes the last day of that month
//$to_date = date("Y-m-t",strtotime($from_date));

//Now
$to_date = $from_date;

//list of last 6 months from the date ...
$ex = explode('-',$from_date);
$months_list = array();
$months_list[] = $from_date;

$months_list2 = array();
if(FALSE && date('Y-m',strtotime($from_date)) == date('Y-m'))
{
$a= strtotime($from_date);
$months_list2[] = date('Y-m-01',strtotime('-1 month',$a));  
}
 else {  
$months_list2 = $months_list;  
}

for ($i = 1; $i < 6; $i++) {
    //commented this part to make it back to single month table
    //$months_list[]= date('Y-m-01', mktime(0, 0, 0, $ex[1]-$i, 1,   $ex[0]));
}
krsort($months_list);


$product = (!empty($_REQUEST['product'])?$_REQUEST['product']:'1,2,9,3,5,7,8,13');

$funding_source = (!empty($_REQUEST['stakeholder'])?$_REQUEST['stakeholder']:'');
if(!empty($funding_source))
{
        $qry_c= "SELECT
                    GROUP_CONCAT(funding_stk_prov.stakeholder_id) as stk
                FROM
                    funding_stk_prov
                INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                WHERE
                    funding_stk_prov.province_id = $province 
                    AND  funding_stk_prov.funding_source_id = $funding_source 
                 ";   
            
    $res = mysql_query($qry_c);
    $comments_arr =array();
    $row=mysql_fetch_assoc($res);
}
$stakeholder    = (!empty($row['stk']))?$row['stk']:'';
//print_r($stakeholder);exit;

 
$qry_1 = "  SELECT
                itminfo_tab.itmrec_id,
                itminfo_tab.itm_name,
                itminfo_tab.itm_id
            FROM
                itminfo_tab
            WHERE
                itminfo_tab.itm_id in ($product)
            ORDER BY
                itminfo_tab.frmindex ASC
        ";
$res_1 = mysql_query($qry_1);
$itm_arr=$itm_arr2 = array();
while($row_1 = mysql_fetch_array($res_1))
{
    $itm_arr[$row_1['itmrec_id']]=$row_1['itm_name'];
    $itm_arr2[$row_1['itmrec_id']]=$row_1['itm_id'];
}


$qry_2 = "SELECT
                tbl_locations.LocName,
                tbl_locations.PkLocID,
                summary_district.stakeholder_id ,
                stakeholder.stkname,
                year(summary_district.reporting_date) as yr,
                LPAD(month(summary_district.reporting_date), 2, '0')  as mon,
                sum(summary_district.avg_consumption) as avg_consumption ,
                summary_district.item_id,
                sum(summary_district.soh_district_store) as soh_district_store,
                (
                    summary_district.soh_district_store / summary_district.avg_consumption
                ) AS mos,
                itminfo_tab.itm_type,
                sum(summary_district.consumption) as consumption
            FROM
                summary_district
                INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
                INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
                INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
            WHERE
                    summary_district.reporting_date in (".sprintf("'%s'", implode("','", $months_list2 ) ).")
                    
                    AND stakeholder.stk_type_id = 0 
                    AND tbl_locations.ParentID IS NOT NULL
                    AND summary_district.province_id = $province
                    AND summary_district.district_id = $dist
        ";
        if(!empty($stakeholder))
        $qry_2 .=" AND summary_district.stakeholder_id in ($stakeholder)";
        $qry_2 .=   "AND itminfo_tab.itm_id in ($product)
           GROUP BY
                summary_district.district_id,
                summary_district.item_id,
                summary_district.stakeholder_id ,
                year(summary_district.reporting_date),
                month(summary_district.reporting_date)
            ORDER BY
                summary_district.district_id,
                summary_district.item_id,
                summary_district.stakeholder_id ,
                year(summary_district.reporting_date),
                month(summary_district.reporting_date)
        ";
//echo $qry_2;exit;
$res_2 = mysql_query($qry_2);
$q_data = $total_cons_arr = array();

$total_cons=0;
$breakdown_data = array();
while($row_2 = mysql_fetch_assoc($res_2))
{
    if(date('Y-m',strtotime($from_date)) == date('Y-m'))
    {
        if(empty($q_data[$row_2['item_id']][$from_date]['amc']))$q_data[$row_2['item_id']][$from_date]['amc']=0;
        $q_data[$row_2['item_id']][$from_date]['amc']+=$row_2['avg_consumption'];
        
        $breakdown_data[$row_2['item_id']][$from_date]['amc'][] = $row_2['stkname'].' : '.number_format($row_2['avg_consumption']);
    
    }
    else
    {
        if(empty($q_data[$row_2['item_id']][$row_2['yr'].'-'.$row_2['mon'].'-01']['amc']))$q_data[$row_2['item_id']][$row_2['yr'].'-'.$row_2['mon'].'-01']['amc']=0;
        $q_data[$row_2['item_id']][$row_2['yr'].'-'.$row_2['mon'].'-01']['amc']+=$row_2['avg_consumption'];
        
        $breakdown_data[$row_2['item_id']][$row_2['yr'].'-'.$row_2['mon'].'-01']['amc'][] = $row_2['stkname'].' : '.number_format($row_2['avg_consumption']);
    
    }
    
    if(empty($q_data[$row_2['item_id']][$from_date]['soh']))$q_data[$row_2['item_id']][$from_date]['soh']=0;
    if(empty($q_data[$row_2['item_id']][$from_date]['consumption']))$q_data[$row_2['item_id']][$from_date]['consumption']=0;
    
    $q_data[$row_2['item_id']][$from_date]['soh']+=$row_2['soh_district_store'];
    $q_data[$row_2['item_id']][$from_date]['consumption']+=$row_2['consumption'];
    
    $breakdown_data[$row_2['item_id']][$from_date]['soh'][] = $row_2['stkname'].' District Store: '.number_format($row_2['soh_district_store']);
    $breakdown_data[$row_2['item_id']][$from_date]['consumption'][] = $row_2['stkname'].' : '.number_format($row_2['consumption']);
    
    //$q_data[$row_2['item_id']][$from_date]['mos']=$row_2['mos'];
    $q_data[$row_2['item_id']]['unit']=$row_2['itm_type'];
}

//echo '<pre>';print_r($q_data);print_r($q_data);
//echo '<pre>bd';print_r($q_data);print_r($breakdown_data);exit;




//override the data in case of IM
// code here ------
// override the following array values
// $breakdown_data[$row_2['item_id']][$from_date]['soh'][]
// $q_data[$row_2['item_id']][$from_date]['soh']
//end of IM



$last_date2  = date("Y-m-t", strtotime($to_date));
//echo '<pre>';print_r($itm_arr);print_r($total_cons_arr);print_r($q_data);exit;

    $and = '';

    if(!empty($province))
    {
        $and .= " AND national_stock.prov_id = $province  ";
    }
    if(!empty($last_date))
    {
        $and .= " AND national_stock.tr_date < '$last_date'  ";
    }

//calculating the share of federal stock from national_stock table 
$pipeline_arr =array();
    foreach($months_list as $k=> $v)
    {
        $last_date  = date("Y-m-t", strtotime($v));

 
             $qry_7 = "
                   SELECT
                        clr_master.pk_id,
                        clr_master.requisition_num,
                        clr_master.requisition_to,
                        clr_master.wh_id,
                        clr_master.stk_id,
                        clr_master.requested_by,
                        clr_master.requested_on,
                        clr_master.approval_status,
                        clr_details.approval_status as item_approval_status,
                        clr_master.distribution_plan_id,
                        clr_details.itm_id,
                        clr_details.pk_id,
                        sum(clr_details.qty_req_dist_lvl1) as requested_qty,
                        itminfo_tab.itmrec_id
                    FROM
                        clr_master
                        INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
                        INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
                        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
                    WHERE
                        tbl_warehouse.dist_id = $dist AND
                        clr_master.approval_status <> 'Issued'
                        AND YEAR(requested_on) = '".$ex[0]."'
                        AND MONTH(requested_on) = '".$ex[1]."' ";
             if(!empty($stakeholder))
                $qry_7 .= " AND tbl_warehouse.stkid in ($stakeholder) ";
             
                $qry_7 .= " GROUP BY
                        clr_details.itm_id     ";
//echo $qry_7;exit;
            $res_7 = mysql_query($qry_7);
            $requisitions_arr = array();
            while($row_7 = mysql_fetch_assoc($res_7))
            {
                $requisitions_arr[$row_7['itmrec_id']][$v]=abs($row_7['requested_qty']);
            }

        
    }//end of foreach monthlist
    
    //echo '<pre>closing:';print_r($closing_bal);echo ',q ';print_r($q_data);

   // echo '<pre>';print_r($pipeline_arr);

?>
<!--height:750px;overflow: auto;-->
    <div class="" style="">
        <table width="100%" border="1" class="table table-condensed ">
        
            <tr style="background-color: #26C281;color:#ffffff;">
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
        
        <tr  style="background-color: #26C281;color:#ffffff;">
        
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
            $itm_id2 = $itm_arr2[$id];
            if($row_count%2 == 0) $row_clr='#e2e2e2';
            else $row_clr = '';
            
            echo '<tr style="background-color:'.$row_clr.'">
                    <td rowspan="3" style="vertical-align:middle;" title="Total AMC of all stakeholders in '.$dist_name.'">'.$name.'</br>';
             echo '(AMC:'.((isset($q_data[$id][$v]['amc'])?number_format($q_data[$id][$v]['amc']):'0')).')';
              echo '</td>
                    <td rowspan="3"  style="text-align:center;vertical-align:middle;">'.(isset($q_data[$id]['unit'])?$q_data[$id]['unit']:'').'</td>
                    
                    <td style="text-align:left">Stocks at district store</td>
                   ';
            foreach($months_list as $k=> $v)
            {
                $stock_val=0;
                $stock_val = ((isset($q_data[$id][$v]['soh']) && $q_data[$id][$v]['soh']>0)?$q_data[$id][$v]['soh']:'0') ;
               
                if(empty($q_data[$id][$v]['amc']) || $q_data[$id][$v]['amc']==0) $mos =0;
                else $mos = ((isset($stock_val)?$stock_val:'0')/$q_data[$id][$v]['amc']);
                
                $data_content = "";
                $data_content .= '<b>Details of Stock </b>';
                if(!empty($breakdown_data[$id][$v]['soh']))
                {
                    foreach($breakdown_data[$id][$v]['soh'] as $key1 => $soh_data)
                    {
                        $data_content .= ' <br/> - '.$soh_data .'';
                    }
                }
                $data_content .= ' <br/>';
                
//                echo '  <td style="text-align:right" >';
//                echo '<a data-toggle="popover" title="Stock Breakdown" data-trigger="hover" data-placement="left" data-html="true" data-content="'.$data_content.'"><i style="color:black !important;" class="fa fa-table"></i></a>';
//                echo ' '.(number_format($stock_val));
//                echo '</td>';
                 echo  '<td style="text-align:right" class="'.((isset($stock_val)&& $stock_val>0)?'soh_anchor':'').'" title="Click for details."  data-date = "'.($_REQUEST['from_date']).'" data-id = "'.($itm_arr2[$id]).'" >';
                 
                echo '<a data-toggle="popover" title="Stock Breakdown" data-trigger="hover" data-placement="left" data-html="true" data-content="'.$data_content.'"><i style="color:black !important;" class="fa fa-table"></i></a>';
                echo ((isset($stock_val)&&$stock_val>0)?' '.number_format($stock_val):'0').' ';
                echo '</td>';
                
                
                echo '<td style="text-align:right">'.(($mos>0)?number_format($mos,2):'0').'</td>
                    ';
            }
            echo '</tr> ';   
            
            
            echo '<tr style="background-color:'.$row_clr.'">
                    <td style="text-align:left">Requested</td>';
            foreach($months_list as $k=> $v)
            {
                $req_val=0;
                $req_val = (isset($requisitions_arr[$id][$v])?$requisitions_arr[$id][$v]:'0');
                
                if(empty($req_val) || empty($q_data[$id][$v]['amc'])|| $q_data[$id][$v]['amc']==0) $mos2 =0;
                else $mos2 = (isset($req_val)?$req_val:'0')/$q_data[$id][$v]['amc'];
                
                echo  '<td style="text-align:right" class="'.((isset($req_val)&& $req_val>0)?'pipeline_anchor':'').'" title="Total quantity , requisitioned in '.date('M-Y',strtotime($v)).' from '.$dist_name.' .Click for details."  data-date = "'.($v).'" data-id = "'.($itm_arr2[$id]).'" >'.((isset($req_val)&&$req_val>0)?'<i class="fa fa-table" style="color:black !important;";></i> '.number_format($req_val):'0').'</td>
                    <td style="text-align:right">'.(($mos2>0)?number_format($mos2,2):'0').'</td>
                  ';
            }
            echo '</tr> ';   
             
            
            echo '<tr style="background-color:'.$row_clr.'">
                    <td style="text-align:left">Consumption</td>';
            foreach($months_list as $k=> $v)
            {
                $c_val=0;
                $c_val = ((isset($q_data[$id][$v]['consumption']) && $q_data[$id][$v]['consumption']>0)?$q_data[$id][$v]['consumption']:'0') ;
               
                
                $data_content = "";
                $data_content .= '<b>Details of Consumption </b>';
                if(!empty($breakdown_data[$id][$v]['consumption']))
                {
                    foreach($breakdown_data[$id][$v]['consumption'] as $key1 => $soh_data)
                    {
                        $data_content .= ' <br/> - '.$soh_data .'';
                    }
                }
                $data_content .= ' <br/>';
                
                echo  '<td style="text-align:right" title="Total consumption of district '.$dist_name.'." >';
                echo '<a data-toggle="popover" title="Details" data-trigger="hover" data-placement="left" data-html="true" data-content="'.$data_content.'"><i style="color:black !important;" class="fa fa-table"></i></a>';
                echo ' '.((isset($c_val)&&$c_val>0)?''.number_format($c_val):'0');
                echo '</td>';
                echo '<td style="text-align:right"></td>
                  ';
            }
            echo '</tr> '; 

            $row_count++;
        }
           
        ?>
      </table>
    </div>
<script>
    $('[data-toggle="popover"]').popover();
</script>