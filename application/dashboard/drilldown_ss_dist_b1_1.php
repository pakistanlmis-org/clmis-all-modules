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
$dist_name= $_REQUEST['dist_name'];
$dist = $_REQUEST['dist_id'];

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
                            tbl_warehouse.dist_id = $dist AND
                            tbl_warehouse.stkid = $stakeholder

";
         //echo $qry_dist;exit;
        $res_dist= mysql_query($qry_dist);
        $row = mysql_fetch_assoc($res_dist);
        $total_districts = $row['total_districts'];
        //Query for shipment main dashboard
  $qry = "
            SELECT
              summary_district.item_id,
              itminfo_tab.itmrec_id,
              (summary_district.soh_district_store / summary_district.avg_consumption ) as mos,
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
              AND summary_district.district_id = $dist

              AND itminfo_tab.itm_category = 1
              AND itminfo_tab.itm_id NOT IN(4,6,10,33)
            GROUP BY 
              summary_district.item_id
 ";
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c=1;


     echo '<h3 align="center">MOS at SDP\'s of District : '.$dist_name.'  - '.$stk_name.'</h3>';
     echo '<table width="80%" class="table table-condensed table-striped left">';
     
     echo '<tr>';
     echo '<th>Sr No  <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Product <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Months of stock - (MOS) <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th></th>';
     echo '</tr>';
     $c=1;
    while($row = mysql_fetch_assoc($qryRes))
    {
       
       $mos_1 = ((isset($row['mos']) && $row['mos']>0)?number_format($row['mos'],2):'0');
        
        $q_mos = "SELECT getMosColor('".$mos_1."', '".$row['itmrec_id']."','".$stakeholder."',3)";
        $rs_mos = mysql_query($q_mos);
        $bgcolor = mysql_result($rs_mos, 0, 0);
        
        echo '<tr>';
        echo '<td>'.$c++.'</td>';
        echo '<td>'.$row['itm_name'].'</td>';
        //echo '<td>'.$row['stkname'].'</td>';
        echo '<td align="right">'.$mos_1.'</td>';
        echo '<td><div style="width:10px; height:12px; background-color:'.$bgcolor.';" title=""></div></td>';
        echo '</tr>';
       
        
    }  
    echo '</table>';?>
	</div>
</div>
    
  
    <script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    <script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>
    <script>
         
     function showDrillDown_lvl2(prov,prov_name,from_date,stk,stk_name,prod_id,prod_name) {
        //window.open("drilldown_stk_1.php?province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name ,"", "width=800,height=700");
        var url = 'drilldown_fp2020_b1_2.php';
        
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