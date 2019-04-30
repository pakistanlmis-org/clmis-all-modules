<?php
//echo '<pre>';print_r($_REQUEST);exit;
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$dist_name= $_REQUEST['dist_name'];
$dist = $_REQUEST['dist'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$itm_id = $_REQUEST['itm_id'];
$ex = explode('-',$from_date);
//Chart caption
$caption = "";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'pipeline_pie';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
        <h4 class="center">Breakdown of Quantities requested by <?=$dist_name?> in <?=$ex[0]?>-<?=date('M',strtotime($from_date))?></h4>
	<?php 
        //query for total districts of the stakeholder
        
 $qry_6 = "
                   SELECT
                            clr_master.pk_id,
                            clr_master.requisition_num,
                            clr_master.approval_status,
                            clr_master.distribution_plan_id,
                            clr_details.itm_id,
                            clr_details.qty_req_dist_lvl1,
                            clr_details.approve_qty
                    FROM
                        clr_master
                        INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
                        INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
                        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
                    WHERE
                        tbl_warehouse.dist_id = $dist 
                        AND clr_master.approval_status <> 'Issued'
                        AND YEAR(requested_on) = '".$ex[0]."'
                        AND MONTH(requested_on) = '".$ex[1]."'
                        AND clr_details.itm_id = '".$itm_id."'

                    ";
        //echo $qry_6;exit;
            $res_6 = mysql_query($qry_6);
            echo '<table class="table table-condensed table-striped">';
            echo '<tr>';
            echo '<td>Requisition Number</td>';
            echo '<td>Status</td>';
            echo '<td>Requested By Dist</td>';
            echo '<td>Approved By Province</td>';
            
            echo '</tr>';
                
            while($row = mysql_fetch_assoc($res_6))
            {
                if($row['approval_status'] == 'Hard_Copy')
                    $row['approval_status']='Manually Submitted';
                
                 echo '<tr>';
                echo '<td>'.$row['requisition_num'].'</td>';
                echo '<td>'.$row['approval_status'].'</td>';
                echo '<td>'.$row['qty_req_dist_lvl1'].'</td>';
                echo '<td>'.$row['approve_qty'].'</td>';
                echo '</tr>';
            }
            echo '</table>';

    ?>
	</div>
</div>