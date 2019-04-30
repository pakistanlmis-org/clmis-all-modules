<?php
//echo '<pre>';print_r($_REQUEST);exit;
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-01", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));

$where_funding='';
$where_locations='';
$where_summary='';
$where_wh='';
if(!empty($province))
{
    $where_funding=" funding_stk_prov.province_id = $province AND ";
    $where_locations=" AND
                tbl_locations.ParentID = $province";
   $where_summary= "WHERE
                            summary_district.province_id = ".$province ." ";
    $where_wh= "tbl_warehouse.prov_id = ".$province." AND ";
}
$funding_source = (!empty($_REQUEST['stakeholder'])?$_REQUEST['stakeholder']:'');
$stk_name = $_REQUEST['stk_name'];

//$p = array();
//$p =  explode(',',$_REQUEST['product']);
//$prod_id = $p[0];

$prod_id = $_REQUEST['product_second'];

$prod_name = (!empty($_REQUEST['prod_name'])?$_REQUEST['prod_name']:'');
    if(empty($prod_name)){
        $qry_c= "SELECT
	itminfo_tab.itmrec_id,
	itminfo_tab.itm_id,
	itminfo_tab.itm_name
FROM
	itminfo_tab
WHERE
	itminfo_tab.itm_id = $prod_id ";

        //echo $qry_c;exit;
        $res1 = mysql_query($qry_c);
        $row = mysql_fetch_assoc($res1);
        $prod_name = $row['itm_name'];
    }
if(!empty($_REQUEST['stakeholder'])){
    $qry_c= "SELECT
                        GROUP_CONCAT(funding_stk_prov.stakeholder_id) as stk
                    FROM
                        funding_stk_prov
                    INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                    WHERE
                        $where_funding funding_stk_prov.funding_source_id = $funding_source ";

    //echo $qry_c;exit;
    $res1 = mysql_query($qry_c);
    $row = mysql_fetch_assoc($res1);
    $stakeholders = $row['stk'];
}
 else {
    $stakeholders= "1,2,7,73";    
}
//echo $stakeholders;exit;

//Chart caption
$caption = 'SDP Level SOH & AMC -  '.$prov_name.' '.(!empty($stk_name)?'- '.$stk_name:'').' - '.$prod_name.' for '.date('M-Y',strtotime($from_date));
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b3';

$qry_1 = "  SELECT
                tbl_locations.PkLocID,
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.LocLvl = 3 $where_locations
            ORDER BY
                tbl_locations.LocName ASC
        ";
        $res_1 = mysql_query($qry_1);
        $dist_arr = array();
        while($row_1 = mysql_fetch_array($res_1))
        {
            $dist_arr[$row_1['PkLocID']]=$row_1['LocName'];
        }
        
        
//System settings 

$userSql = "SELECT
            system_settings.pk_id,
            system_settings.setting_name,
            system_settings.setting_value
            FROM
            system_settings
";
//echo $userSql;exit;
$userResult = mysql_query($userSql) or die("Error " . $userSql);
$system_settings = array();
while($row = mysql_fetch_assoc($userResult)){
    $system_settings[$row['setting_name']] = $row['setting_value'];
}
?>

<div class=" ">    
    <div class=" ">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        $q_reporting  = "SELECT
                            summary_district.province_id,
                            summary_district.district_id,
                            summary_district.stakeholder_id,
                            avg(summary_district.reporting_rate) as rr,
                            summary_district.reporting_date,
                        tbl_locations.LocName
                        FROM
                        summary_district
                        INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
                        $where_summary
                            ";
        if(!empty($stakeholders)) $q_reporting.="   AND  summary_district.stakeholder_id   in ($stakeholders) ";
        $q_reporting.="  
                            AND summary_district.reporting_date = '".$from_date."'
                            and summary_district.item_id = 'IT-001'
                        group by 
                            summary_district.province_id,
                            summary_district.district_id
                        ";
        //echo $q_reporting;exit;
        $res_reporting = mysql_query($q_reporting);
        $reporting_rate  =  array();
        while($row=mysql_fetch_assoc($res_reporting))
        {
            $reporting_rate[$row['district_id']]=$row['rr'];
        }
//        echo '<pre>';print_r($reporting_rate);exit;    
        //Query for shipment main dashboard
        $qry = "SELECT
                tbl_warehouse.dist_id,
                tbl_locations.LocName,
                tbl_hf_data.item_id ,
                itminfo_tab.itm_name,
                sum(tbl_hf_data.closing_balance) as closing_balance,
                sum(tbl_hf_data.avg_consumption) as avg_consumption,
                sum(tbl_hf_data.issue_balance) as issue_balance
                    FROM
                            tbl_warehouse
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    WHERE
                     $where_wh tbl_warehouse.wh_id NOT IN (
                            SELECT
                                    warehouse_status_history.warehouse_id
                            FROM
                                    warehouse_status_history
                            INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                            WHERE
                                    warehouse_status_history.reporting_month = '".$from_date."'
                            AND warehouse_status_history.`status` = 0

                    )
                    AND tbl_hf_data.reporting_date = '".$from_date."'
                    AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                    AND tbl_hf_data.item_id =".$prod_id." ";
    if(!empty($stakeholders)) $qry.="  AND tbl_warehouse.stkid  in ($stakeholders)  ";
    $qry.="  
                GROUP BY
                tbl_warehouse.dist_id
                ORDER BY 
                LocName
        ";
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c=1;
    $disp_arr = array();
   
    while($row = mysql_fetch_assoc($qryRes))
    {
        $dist_arr[$row['dist_id']] = $row['LocName'];

        $disp_arr[$row['dist_id']]['closing_balance'] = $row['closing_balance'];
        $disp_arr[$row['dist_id']]['avg_consumption'] = $row['avg_consumption'];
    }    
    //echo '<pre>'.$total_reporting_wh;print_r($so_arr);print_r($reporting_wh_arr);exit;    

    //xml for chart
    $xmlstore = '<chart caption="'.$caption.'" yaxismaxvalue="100"  showtooltip="1"  exportEnabled="1" xaxisname="Districts" palettecolors="#26C281,#5850EB,#FF3888" theme="fint">';

   
    $xmlstore .= ' <categories>';
    foreach($dist_arr as $dist_id => $name)
    {
         $xmlstore .= '     <category label="'.$name.'"/>';  
    }
    $xmlstore .= ' </categories>';
    
    $xmlstore .= '<dataset>';
    $xmlstore .= ' <dataset seriesname="SOH" showvalues="0"   >';
    foreach($dist_arr as $dist_id => $name)
    {
        $val = (isset($disp_arr[$dist_id]['closing_balance'])?$disp_arr[$dist_id]['closing_balance']:0);
        $xmlstore .= '     <set label="'.$name.'"  value="'.$val.'"  link="JavaScript:click_dashlet_b3('.$province.',\''.$prov_name.'\',\''.$dist_id.'\',\''.$name.'\',\''.$from_date.'\',\''.$stakeholders.'\',\''.$stk_name.'\',\''.$prod_id.'\',\''.$prod_name.'\')" />';
    }
    $xmlstore .= '  </dataset>';
    $xmlstore .= '  </dataset>';
    
    $xmlstore .= '  <dataset>';
    $xmlstore .= ' <dataset seriesname="AMC" showvalues="0">';
    foreach($dist_arr as $dist_id => $name)
    {
        $val = (isset($disp_arr[$dist_id]['avg_consumption'])?$disp_arr[$dist_id]['avg_consumption']:0);
        $xmlstore .= '     <set label="'.$name.'"  value="'.$val.'"  link="JavaScript:click_dashlet_b3('.$province.',\''.$prov_name.'\',\''.$dist_id.'\',\''.$name.'\',\''.$from_date.'\',\''.$stakeholders.'\',\''.$stk_name.'\',\''.$prod_id.'\',\''.$prod_name.'\')"   />';
    }
    $xmlstore .= '  </dataset>';
    $xmlstore .= '  </dataset>';

    
    
    $xmlstore .= ' <lineset  seriesname="Reporting Rate (SDP Level)"  showvalues="0" >';
    foreach($dist_arr as $dist_id => $name)
    {
        if(isset($reporting_rate[$dist_id])) $r_rate = $reporting_rate[$dist_id];
        else $r_rate = 0;
        
        $xmlstore .= '     <set label="'.$name.'" value="'.(number_format($r_rate,1)).'" link="JavaScript:click_dashlet_b3('.$province.',\''.$prov_name.'\',\''.$dist_id.'\',\''.$name.'\',\''.$from_date.'\',\''.$stakeholders.'\',\''.$stk_name.'\',\''.$prod_id.'\',\''.$prod_name.'\')"  />';
        
        $dist_for_table[$dist_id]['r_rate'] = $r_rate; 
    }
    $xmlstore .= '  </lineset>';
    
    
$xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSStackedColumn2DLineDY.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    //print_r($dist_for_table);
    ?>
	</div>
</div>
