<?php
/**
 * shipment
 * @package dashboard
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include Configuration
include("../includes/classes/Configuration.inc.php");
//login
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include  functions
include APP_PATH . "includes/classes/functions.php";



$f_date= (!empty($_REQUEST['from_date'])?$_REQUEST['from_date']:date("Y-m").'-01');
$province = (!empty($_REQUEST['province'])?$_REQUEST['province']:'all');
$prod_id = (!empty($_REQUEST['prod_id'])?$_REQUEST['prod_id']:'');
$indicator = (!empty($_REQUEST['indicator'])?$_REQUEST['indicator']:'');
$from_date = date("Y-m-d", strtotime($f_date));

$stk = (!empty($_REQUEST['stk'])?$_REQUEST['stk']:'all');
$stk_name = (!empty($_REQUEST['stk_name'])?$_REQUEST['stk_name']:'All');

$where_clause="";
if(!empty($stk) && $stk != 'all')
{
    $where_clause .= " AND summary_district.stakeholder_id IN($stk) ";
}
if(!empty($province) && $province != 'all')
{
    $where_clause .= " AND summary_district.province_id =$province ";
}
//echo '<pre>';print_r($_REQUEST);exit;    

//Query for shipment main dashboard
$qry = " SELECT
            YEAR (
                    summary_district.reporting_date
            ) AS `Year`,
            MONTH (
                    summary_district.reporting_date
            ) AS `Month`,
            DATE_FORMAT(
                    summary_district.reporting_date,
                    '%Y-%m'
            ) AS `Reporting Date`,
            Province.LocName AS Province,
            dist.LocName AS dist_name,
            itminfo_tab.itm_name,
            stakeholder.stkname AS Stakeholder,
            ROUND(
                    (
                            summary_district.soh_district_store / summary_district.avg_consumption
                    ),
                    2
            ) AS `mos`,
            summary_district.soh_district_store,
            summary_district.avg_consumption,
            summary_district.province_id as prov_id,
            itminfo_tab.itm_id as item_id,
            summary_district.total_health_facilities,
            summary_district.reporting_rate,
            summary_district.stakeholder_id
    FROM
            summary_district
    INNER JOIN tbl_locations as dist ON summary_district.district_id = dist.PkLocID
    INNER JOIN tbl_locations AS Province ON dist.ParentID = Province.PkLocID
    INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
    INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
    WHERE summary_district.reporting_date = '$from_date'
    $where_clause
    AND itminfo_tab.itm_id = $prod_id
    AND stakeholder.stk_type_id = 0
    ORDER BY
    dist.LocName ASC

";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$c=1;
$unk_arr = $so_arr2 = $so_arr = $us_arr = $sat_arr = $os_arr = array();

while($row = mysql_fetch_assoc($qryRes))
{
    //in this report definition of MOS is ZERO. according to the requirements
    $itm_arr[$row['item_id']] = $row['itm_name'];
    
    
    //according to excel sheet given by mne
//    if($row['stakeholder_id'] == 1) $sat_max = 6;
//    else $sat_max = 7;
    $sat_max = 6;
    
    
    if( $row['soh_district_store']==NULL )
    {
        $unk_arr[] = $row;
    }
    elseif( $row['mos'] <= '0')
    {
        $so_arr[] = $row;
    }
    elseif( $row['mos'] > 0 && $row['mos'] < 3 )
    {
        $us_arr[] = $row;
    }
    elseif( $row['mos'] >= 3 && $row['mos'] < $sat_max )
    {
        $sat_arr[] = $row;
    }
    elseif( $row['mos'] >= $sat_max )
    {
        $os_arr[] = $row;
    }

    if($indicator == 'UNK') 
    {
        $ind_name= 'Un-Known MOS ';
        $display_arr =  $unk_arr;
    }
    elseif($indicator == 'SO') 
    {
        $ind_name= 'Stock Out';
        $display_arr =  $so_arr;
    }
    elseif($indicator == 'US') 
    {
        $ind_name= 'Under Stock';
        $display_arr =  $us_arr;
    }
    elseif($indicator == 'SAT') 
    {
        $ind_name= 'Satisfactory Stock';
        $display_arr =  $sat_arr;
    }
    elseif($indicator == 'OS') 
    {
        $ind_name= 'Over Stock';
        $display_arr =  $os_arr;
    }
}    
//echo '<pre>';print_r($so_arr);echo'display_arr';print_r($display_arr);exit;    

?>

    <h4 class="center">
        <div class="label label-success" style="font-size: 20px">
            List of '<?=$ind_name?>' District Stores - <?=$_REQUEST['prov_name'].'-'.$_REQUEST['prod_name']?>
        </div>
    </h4>

<table name="tbl" class="table table-bordered table-condensed table-striped" border="">    
    <tr>
        <th>#</th>
        <th align="center">District</th>
        <th align="center">Stakeholder</th>
        
        <th align="center">Product</th>
        <th align="center">SOH</th>
        <th align="center">AMC</th>
        <th align="center">MOS</th>
    </tr>
<?php

$c=1;
foreach($display_arr as $k => $v)
{
    if($v['mos'] == NULL)
        $mos = 'UNK';
    else
         $mos = number_format($v['mos'],2);
    
    echo '<tr>';
    echo '<td>'.$c++.'</td>';
    echo '<td>'.$v['dist_name'].'</td>';
    echo '<td>'.$v['Stakeholder'].'</td>';
    
    echo '<td>'.$v['itm_name'].'</td>';
    echo '<td align="right">'.number_format($v['soh_district_store']).'</td>';
    echo '<td align="right">'.number_format($v['avg_consumption']).'</td>';
    echo '<td align="right">'.$mos.'</td>';
    echo '</tr>';
}
?>
</table>