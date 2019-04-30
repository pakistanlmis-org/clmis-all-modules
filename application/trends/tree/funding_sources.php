<?php
include("../../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include APP_PATH . "includes/classes/functions.php";
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
//include(PUBLIC_PATH . "html/header.php");

 $qry_pre_calc = "  
            SELECT
                tbl_locations.LocName prov,
                tbl_warehouse.wh_name,
                stakeholder.stkname,
                funding_stk_prov.stakeholder_id,
                funding_stk_prov.province_id,
                funding_stk_prov.funding_source_id
            FROM
                funding_stk_prov
            INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
            INNER JOIN stakeholder ON funding_stk_prov.stakeholder_id = stakeholder.stkid
            INNER JOIN tbl_locations ON funding_stk_prov.province_id = tbl_locations.PkLocID
            ORDER BY
                funding_stk_prov.province_id ASC,
                funding_stk_prov.stakeholder_id ASC
    ";
    //echo $qry_pre_calc;
    $res_pre = mysql_query($qry_pre_calc);
    $disp_data = $provinces =  array();
    while($row_pre = mysql_fetch_array($res_pre))
    {
        $provinces[$row_pre['province_id']]=$row_pre['prov'];
        $disp_data[$row_pre['province_id']][$row_pre['wh_name']][]=$row_pre['stkname'];
    }
    //echo '<pre>';print_r($disp_data);
?>
 
   <script type='text/javascript' src='google_jsapi.js'></script>  
   <script type='text/javascript'>  
    google.load('visualization', '1', {packages:['orgchart']});
    <?php
        foreach($provinces as $prov => $prov_name){
            //if($prov != '1') continue;
            ?>
            
                  
                google.setOnLoadCallback(drawChart_<?=$prov?>);
                
                function drawChart_<?=$prov?>() {  
                 var data = new google.visualization.DataTable();  
                 data.addColumn('string', 'Node');  
                 data.addColumn('string', 'Parent');  
                 data.addRows([  
                     <?php
                     $tree_arr =array();
                     //$tree_arr[] = "['Funding Sources', '']";
                     //foreach($disp_data[$prov] as $prov => $funding_source_arr){
                         $tree_arr[] = "['".$prov_name."', '']";
                         foreach($disp_data[$prov] as $funding_source => $stk_arr){

                            $tree_arr[] = "['".$funding_source."', '".$prov_name."']";
                             foreach($stk_arr as $stk_id => $stk_name){
                                $tree_arr[] = "['".$stk_name."', '".$funding_source."']";
                            }
                        }
                     //}
                     echo implode(',',$tree_arr);
                     ?>

                 ]);  
                 var chart = new google.visualization.OrgChart(document.getElementById('chart_div<?=$prov?>'));  
                 chart.draw(data);  
                }
                //alert('nxt');
        <?php
        }
    ?>  
      
   </script>  
  </head>  
  <body>  
      <div align="center"><h2>cLMIS Province Wise Funding Sources Status </h2>
        <?php
             foreach($provinces as $prov => $prov_name){
                 echo '<div><h3>'.$prov_name.'</h3>';
                 echo "<div id='chart_div$prov'></div>";
                 echo '</div>';
             }
         ?> 
      </div>
  </body>  
 </html>  