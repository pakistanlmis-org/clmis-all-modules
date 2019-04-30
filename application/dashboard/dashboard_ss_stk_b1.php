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
//Chart caption
$caption = "Average Stock Availability Rate by Stakeholders at District Level ";
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
        
        //making list of items , to display list incase no data entry is found
        $qry_1 = "  SELECT
                        stakeholder.stkid,
                        stakeholder.stkname,
                        stakeholder.lvl,
                        stakeholder.MainStakeholder
                    FROM
                        stakeholder
                    INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkid
                    INNER JOIN wh_user ON wh_user.wh_id = tbl_warehouse.wh_id
                    WHERE
                        stakeholder.stk_type_id = 0 AND
                        stakeholder.lvl = 1
                        AND stakeholder.stkid IN ($stakeholder) AND
                        tbl_warehouse.prov_id = $province
                ";
                $res_1 = mysql_query($qry_1);
                $stk_arr = array();
                while($row_1 = mysql_fetch_array($res_1))
                {
                    $stk_arr[$row_1['stkid']]=$row_1['stkname'];
                }
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
                            AND tbl_warehouse.stkid  in ($stakeholder)

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
                 AND (summary_district.soh_district_store/avg_consumption) > 0
                  AND itminfo_tab.itm_category = 1
                  AND stakeholder.stk_type_id = 0
                  AND stakeholder.stkid in ($stakeholder)
                  AND itminfo_tab.itm_id NOT IN(4,6,10,33)
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
     $r_arr  = $reverse_arr   = $so_arr = array();

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
        $reverse_arr[$row['stkid']] = 100-$perc;
    }  
    
   //echo '<pre>';print_r($r_arr);print_r($reverse_arr);print_r($stk_arr);print_r($so_arr);exit;    
        
    //xml for chart
    $xmlstore = '<chart caption="'.$caption.'" yaxismaxvalue="100" subcaption="Stakeholder Wise" xaxisname="Stakeholders" yaxisname="Percentage" numberprefix="" exportEnabled="1" theme="fint">';

   
    //$xmlstore .= ' <dataset >';
    foreach($stk_arr as $id => $name)
    {
        $val = (isset($r_arr[$id])?$r_arr[$id]:'0');
        //$xmlstore .= '     <set value="'.(number_format(!empty($val)?$val:0,1)).'" link="JavaScript:showDrillDown_b1('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$id.',\''.$name.'\');" />';
         $xmlstore .= '     <set label="'.$name.'" value="'.(number_format(!empty($val)?$val:0,1)).'" link="JavaScript:showDrillDown_b1('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$id.',\''.$name.'\');"  tooltext="'.$name.':'.(number_format(!empty($val)?$val:0,1)).' {br} Click bar to drill down"  />';
 
        
    }
    //$xmlstore .= '  </dataset>';
    

    $xmlstore .= ' </chart>';
    //end chart

    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/Column2D.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    ?>
	</div>
</div>