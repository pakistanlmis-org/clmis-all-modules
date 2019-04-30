<?php

ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");

include APP_PATH."includes/classes/functions.php";
//include(PUBLIC_PATH . "html/header.php");
require(PUBLIC_PATH."FusionCharts/Code/PHP/includes/FusionCharts.php");

$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$stakeholder = $_REQUEST['stakeholder'];
$stk_name = $_REQUEST['stk_name'];

//Chart caption
$caption = "Stock Availability Status in Reporting Districts";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b3';

?>
<?php

include PUBLIC_PATH . "html/top_im.php";
?>
<link href="<?php echo PUBLIC_URL;?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;display:none;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
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
                            tbl_warehouse.prov_id = $province AND
                            tbl_warehouse.stkid = $stakeholder

";
        $res_dist= mysql_query($qry_dist);
        $row = mysql_fetch_assoc($res_dist);
        $total_districts = $row['total_districts'];
        //Query for shipment main dashboard
  $qry = "
            SELECT
              summary_district.item_id,
              count( DISTINCT summary_district.district_id ) AS reported_districts,
	      SUM( CASE WHEN (summary_district.soh_district_store / summary_district.avg_consumption ) > 0 THEN 1 ELSE 0 END ) as nmbr_of_districts,
              itminfo_tab.itm_name
            FROM
                      summary_district
              INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
              INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
              INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
              INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
            WHERE
              summary_district.reporting_date = '".$from_date."' AND
              stakeholder.stkid = $stakeholder AND
              Province.PkLocID = $province
              

              AND itminfo_tab.itm_category = 1
              AND itminfo_tab.itm_id NOT IN(4,6,10,33)
            GROUP BY 
              summary_district.item_id
 ";
    //echo $qry;
    $qryRes = mysql_query($qry);
    $c=1;
     $itm_arr = $reported_arr  = $so_arr = array();
    
    while($row = mysql_fetch_assoc($qryRes))
    {
       
        $itm_arr[$row['item_id']] = $row['itm_name'];
        
        $reported_arr[$row['item_id']] = $row['reported_districts'];
        $so_arr[$row['item_id']] = $row['nmbr_of_districts'];
    }  
    //echo '<pre>';print_r($so_arr);exit;
    $r_arr =$reverse_arr = $stake_wise_arr =array();
  
    $temp = $prods = 0;
    foreach($so_arr as $itm_id => $val)
    {
         
            $prods++;
            if(!empty($reported_arr[$itm_id]) && $reported_arr[$itm_id]>0)
                $perc = ((!empty($val)?$val:0)* 100)/$reported_arr[$itm_id];
            else
                $perc=0;
            
            $r_arr[$itm_id] = $perc;
            
           
    }
    //echo '<pre>'.$total_districts;print_r($r_arr);print_r($so_arr);print_r($itm_arr);exit;    
        
    //xml for chart
    $xmlstore = '<chart caption="Stock Availability Rate at Districts of '.$prov_name.' - '.$stk_name.'" yaxismaxvalue="100"  subcaption="Product Wise" xaxisname="Products" yaxisname="Percentage" numberprefix="" exportEnabled="1" theme="fint">';
    
    foreach($itm_arr as $id => $name)
    {
        if(!empty($reported_arr[$itm_id]) && $reported_arr[$itm_id]>0)
            $t_rep = $reported_arr[$itm_id];
        
        $t_rep = (isset($reported_arr[$id])?$reported_arr[$id]:0);
        $t_rep2 = (isset($so_arr[$id])?$so_arr[$id]:0);
        
        $val = (isset($r_arr[$id])?$r_arr[$id]:0);
        $xmlstore .= '     <set label="'.$name.'" tooltext="Stock available at '.$t_rep2.' out of '.$t_rep.' reported districts" value="'.(number_format(!empty($val)?$val:0,2)).'" link="JavaScript:showDrillDown_lvl2('.$province.',\''.$prov_name.'\',\''.$from_date.'\','.$stakeholder.',\''.$stk_name.'\',\''.$id.'\',\''.$name.'\');"  />';
    }
   
 $xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/Column2D.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    ?>
	</div>
</div>
    
    <div class="widget widget-tabs">    
        <div class="widget-body" id="drilldown_div_2">
            
        </div>
    </div>
    <script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    
    <script>
         
     function showDrillDown_lvl2(prov,prov_name,from_date,stk,stk_name,prod_id,prod_name) {
       
        //window.open("drilldown_stk_1.php?province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name ,"", "width=800,height=700");
        
        
        var url = 'drilldown_s_b1_2.php';
        
        var dataStr='';
        dataStr += "province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name+"&prod_id="+prod_id+"&prod_name="+prod_name;

        $('#drilldown_div_2').html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

        $.ajax({
            type: "POST",
            url: '<?php echo APP_URL; ?>dashboard/' + url,
            data: dataStr,
            dataType: 'html',
            success: function(data) {
                    $("#drilldown_div_2").html(data);
            }
        });
    
    }
    </script>

