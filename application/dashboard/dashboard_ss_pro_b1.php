<?php
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));

//Chart caption
$caption = "Stock Out Status in Reporting Districts";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b1';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        //query for total districts of the stakeholder
         $qry_dist ="SELECT 
                    count( DISTINCT tbl_warehouse.dist_id ) as total_districts

                    FROM
                            tbl_warehouse
                    INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                    INNER JOIN sysuser_tab ON wh_user.sysusrrec_id = sysuser_tab.UserID
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN stakeholder AS mainStk ON tbl_warehouse.stkid = mainStk.stkid
                    WHERE
                            stakeholder.lvl = 3 AND
                            tbl_warehouse.prov_id = $province 

";
        $res_dist= mysql_query($qry_dist);
        $row = mysql_fetch_assoc($res_dist);
        $total_districts = $row['total_districts'];
        //Query for shipment main dashboard
         $qry = "
            select stkid,stkname,sum(stock_out_districts) as total_so_dist,count(stock_out_districts) as itm_count
            FROM(
                SELECT
                  stakeholder.stkid,
                  stakeholder.stkname,
                  summary_district.item_id,
                  count(distinct summary_district.district_id) as stock_out_districts,
                  itminfo_tab.itm_name
                FROM
                          summary_district
                  INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
                  INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
                  INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
                  INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
                WHERE
                  summary_district.reporting_date = '".$from_date."' AND
                 
                  Province.PkLocID = $province
                 AND (summary_district.soh_district_store) = 0
                  AND itminfo_tab.itm_category = 1
                  AND stakeholder.stk_type_id = 0
                  AND stakeholder.lvl=1
                  AND itminfo_tab.itm_id NOT IN(4,6,8,13,33)
                GROUP BY
                stakeholder.stkid,
                summary_district.item_id
            )A
            group by
            stkid
 ";
    //echo $qry;

    $qryRes = mysql_query($qry);
    $c=1;
     $r_arr =$stk_arr   = $so_arr = array();

    $arr_item[1]['itm_count'] = 6;
    $arr_item[2]['itm_count'] = 3;
    $arr_item[73]['itm_count'] = 6;
    $arr_item[7]['itm_count'] = 6;

    while($row = mysql_fetch_assoc($qryRes))
    {
        $stk_arr[$row['stkid']] = $row['stkname'];
        $so_arr[$row['stkid']]  = $row['total_so_dist'];

        $val = $row['total_so_dist'];
        $perc1 = ((!empty($val)?$val:0)* 100)/$total_districts;
        $perc = $perc1 / (!empty($arr_item[$row['stkid']]['itm_count'])?$arr_item[$row['stkid']]['itm_count']:6);
		
		//echo '</br>perc1'.$perc1.',val:'.$val.',total:'.$total_districts.',perc='.$perc.' , itm c:'.$row['itm_count'];
		
        $r_arr[$row['stkid']] = $perc;
    }  
    
   // echo '<pre>';print_r($r_arr);print_r($stk_arr);print_r($so_arr);exit;    
        
    //xml for chart
    $xmlstore = '<chart caption="Avg Stock Out Rate at Districts of '.$prov_name.'" yaxismaxvalue="100" subcaption="Stakeholder Wise" xaxisname="Stakeholders" yaxisname="Percentage" numberprefix="" exportEnabled="1" theme="fint">';
    $xmlstore .= ' <categories>';
    
    foreach($stk_arr as $id => $val)
    {
        $xmlstore .= '<category label="'.$val.'" />';
    }
    $xmlstore .= '</categories>';
   
    $xmlstore .= ' <dataset >';
    foreach($stk_arr as $id => $name)
    {
        $val = $r_arr[$id];
        //$xmlstore .= '     <set value="'.(number_format(!empty($val)?$val:0,1)).'" link="JavaScript:showDrillDown_b1('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$id.',\''.$name.'\');" />';
         $xmlstore .= '     <set value="'.(number_format(!empty($val)?$val:0,1)).'" link="JavaScript:showDrillDown_b1('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$id.',\''.$name.'\');"  tooltext="'.$name.':'.(number_format(!empty($val)?$val:0,1)).' {br} Click for details"  />';
 
        
    }
    $xmlstore .= '  </dataset>';
    

    $xmlstore .= ' </chart>';
    //end chart

    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSCombi2D.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    ?>
	</div>
</div>