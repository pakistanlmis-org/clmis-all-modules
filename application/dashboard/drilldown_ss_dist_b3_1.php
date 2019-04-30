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
$stakeholder = $_REQUEST['stk_id'];
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
        $qry  = "
                SELECT
                    DISTINCT tbl_warehouse.wh_id,
                    stk2.stkid AS stk_id,
                    stk2.stkname,
                    (
                                    SELECT
                                            count(DISTINCT tbl_hf_data.pk_id)
                                    FROM
                                            tbl_hf_data
                                    WHERE
                                            tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                                    AND tbl_hf_data.reporting_date = '".$from_date."'
                                    AND tbl_hf_data.item_id = 1
                            ) AS is_reported,
                    tbl_warehouse.wh_name,
                    tbl_warehouse.dist_id,
                    tbl_warehouse.prov_id,
                    tbl_warehouse.stkid,
                    tbl_warehouse.hf_type_id,
                    tbl_hf_type.hf_type
                FROM
                        tbl_warehouse
                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		INNER JOIN stakeholder as stk2 ON tbl_warehouse.stkid = stk2.stkid
                INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                WHERE

                 tbl_warehouse.wh_id NOT IN (
                        SELECT
                                warehouse_status_history.warehouse_id
                        FROM
                                warehouse_status_history
                        INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                        WHERE
                                warehouse_status_history.reporting_month = '".$from_date."'
                        AND warehouse_status_history.`status` = 0
                )
                AND tbl_warehouse.prov_id = $province
                AND tbl_warehouse.dist_id = $dist
                    
                AND stakeholder.lvl = 7
                and tbl_warehouse.stkid = $stakeholder

                ";
//    echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c=1;


     echo '<h3 align="center">Reporting Status : '.$dist_name.'  - '.$stk_name.'</h3>';
     echo '<table width="80%" class="table table-condensed table-hover table-striped left">';
     
     echo '<tr>';
     echo '<th>Sr No  <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Stakeholder  <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>HF Type <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Warehouse  <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Status <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '</tr>';
     $c=1;
    while($row = mysql_fetch_assoc($qryRes))
    {
        
        echo '<tr>';
        echo '<td>'.$c++.'</td>';
        echo '<td>'.$row['stkname'].'</td>';
        echo '<td>'.$row['hf_type'].'</td>';
        echo '<td>'.$row['wh_name'].'</td>';
        if(isset($row['is_reported']) && $row['is_reported']>0)
            echo '<td class=""><i class="fa fa-check fa-6"  aria-hidden="true" style="color:green !important;" ></i><span style="display:none;">yes</span></td>';
        else
            echo '<td class=""><i class="fa fa-times fa-4" style="color:red !important;" ></i></td>';
        echo '</tr>';
       
        
    }  
    echo '</table>';?>
	</div>
</div>
    
  
    <script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    <script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>