<?php
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include APP_PATH . "includes/classes/functions.php";
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
?>
<div class="widget widget-tabs">
    <div class="widget-body" id="a1" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
                        <li><a   id="panel-fullscreen_a1" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
        <?php
        //query
        $qry = "
        
            SELECT 
                sum(1) as total,
                Sum(if(tbl_hf_data.closing_balance>0,1,0)) AS avail,
                Sum(if(tbl_hf_data.closing_balance<=0,1,0)) AS so,
                sum(if(tbl_hf_data.closing_balance<=0,1,0))*100 / sum(1) as so_rate,
                Sum(if(tbl_hf_data.closing_balance>0,1,0))*100 / sum(1) as availability_rate,
                tbl_warehouse.prov_id
            FROM
                tbl_hf_data
            INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
            WHERE
                tbl_hf_data.reporting_date = '2018-01-01' AND
                tbl_hf_data.item_id IN (1,5,7,9 ) AND
                tbl_warehouse.stkid IN (1, 2, 7, 73) AND 
                tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
            group BY 
                tbl_warehouse.prov_id

        ";

        $prov_arr = array();
        $prov_arr[1]='Punjab';
        $prov_arr[2]='Sindh';
        $prov_arr[3]='Khyber Pakhtunkhwa';
        $prov_arr[4]='Balochistan';
        for($i=1;$i<=4;$i++)
        {
            echo '<div class="col-md-3 my_dash_cols">';
            $data_1 = '';
            $center_val='';
            $q_res = mysql_query($qry);
            while ($row = mysql_fetch_assoc($q_res)) {
                if($row['prov_id']==$i)
                   if(!empty($row['availability_rate'])){    
                        $center_val=round((($row['availability_rate'])),1).'%';
                        $data_1 .= "<set label='' value='".(100-(($row['availability_rate'])))."' showValue= '0' color='#ff7c7c' />" ;
                        $data_1 .= "<set label='Reporting Rate' value='".(($row['availability_rate']))."' color='#2ac928' />" ;
                        $data_1 .= "<set  value='-100' alpha='0' showValue= '0'/>";
                    }
            }
            $xmlstore = "<chart  numberSuffix='%' defaultCenterLabel= '$center_val' showLegend='1' theme='fint' animateClockwise='1'  exportEnabled='1' exportAction='Download' caption='Avg Stock Availability Rate of ".$prov_arr[$i]."' exportFileName='Provincial Reporting Rate by SDOs " . date('Y-m-d H:i:s') . "'>";
            $xmlstore .= $data_1;
            $xmlstore .= "</chart>";
            FC_SetRenderer('javascript');
            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Doughnut2D.swf", "", $xmlstore, 'prov_reporting_rate_'.$i, '100%', 400, false, false);
        
            echo '</div>';}
        ?> 
    </div>
</div>