<?php
//echo '<pre>';print_r($_REQUEST);exit;
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
include PUBLIC_PATH . "html/top_im.php";
$subCaption='';
?>

<link href="<?php echo PUBLIC_URL;?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<div class="widget widget-tabs">    
    <div class="widget-body">
        
<?php
$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$stakeholder = $_REQUEST['stakeholder'];
$stk_name = $_REQUEST['stk_name'];
$prod_id = $_REQUEST['prod_id'];
$prod_name = $_REQUEST['prod_name'];

$dist_id = $_REQUEST['dist_id'];
$dist_name = $_REQUEST['dist_name'];

   $qry = "
            SELECT
                	tbl_warehouse.wh_id,
                        tbl_warehouse.wh_name,itminfo_tab.itmrec_id,
                        tbl_locations.LocName as dist_name,
                        tbl_hf_data.closing_balance as soh,
                        ROUND( (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 2 ) as mos
                    FROM
                            tbl_warehouse
                    INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkid
                    INNER JOIN stakeholder  st ON st.stkid = tbl_warehouse.stkofficeid
                    INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                    WHERE
                         tbl_warehouse.stkid = $stakeholder
                    AND st.lvl=7
                    AND tbl_warehouse.prov_id = ".$province."
                    AND tbl_hf_type_rank.province_id = ".$province."

                    AND tbl_warehouse.wh_id NOT IN (
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

                    AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                    
                    AND itminfo_tab.itm_category = 1
                    AND tbl_hf_data.item_id = '".$prod_id."'
                    AND tbl_warehouse.dist_id = $dist_id
                ORDER BY 
                
                    tbl_warehouse.wh_name 
            
 ";
//    echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c=1;
     $itm_arr   = $so_arr = array();
    
     echo '<h3 align="center">MOS at SDP\'s of District : '.$dist_name.'  - '.$stk_name.'  - '.$prod_name.'</h3>';
     echo '<table width="80%" class="table table-condensed table-striped left">';
     
     echo '<tr>';
     echo '<th>Sr No <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>District <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Warehouse <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Stock on hand - (SOH) <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Months of stock - (MOS) <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th></th>';
     echo '</tr>';
     $c=1;
    while($row = mysql_fetch_assoc($qryRes))
    {
       
       $mos_1 = ((isset($row['mos']) && $row['mos']>0)?number_format($row['mos'],2):'0');
       $soh = ((isset($row['soh']) && $row['soh']>0)?number_format($row['soh']):'0');
        
        $q_mos = "SELECT getMosColor('".$mos_1."', '".$row['itmrec_id']."','".$stakeholder."',4)";
        $rs_mos = mysql_query($q_mos);
        $bgcolor = mysql_result($rs_mos, 0, 0);
        
        echo '<tr>';
        echo '<td>'.$c++.'</td>';
        echo '<td>'.$row['dist_name'].'</td>';
        echo '<td>'.$row['wh_name'].'</td>';
        //echo '<td>'.$row['stkname'].'</td>';
        echo '<td align="right">'.$soh.'</td>';
        echo '<td align="right">'.$mos_1.'</td>';
        echo '<td><div style="width:10px; height:12px; background-color:'.$bgcolor.';" title=""></div></td>';
        echo '</tr>';
       
        
    }  
    echo '</table>';
?>
        	</div>
</div>
<script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>
    