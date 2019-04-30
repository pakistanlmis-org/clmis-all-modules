<?php
//include Configuration
include("../includes/classes/Configuration.inc.php");
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include functions
include APP_PATH . "includes/classes/functions.php";

//include FusionCharts
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");

//whId
$whId = $_SESSION['user_warehouse'];
//stk id
//print_r($_POST['stakeholder']);exit;
//$province='';$stk='';$itm='';
//print_r($_REQUEST);exit;
$where_hf='';
$province = (!empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '');
$stk_arr = (!empty($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : '');
$itm_arr_request = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');
$rep_date = $_REQUEST['from_date'] . '-01';
$hf_arr_request = (!empty($_REQUEST['hf']) ? $_REQUEST['hf'] : '');
if (!empty($hf_arr_request)) {
    $hf = implode(',', $hf_arr_request);
    $where_hf="AND tbl_hf_type.pk_id IN (".$hf.")";
}
//print_r('jhdfgdjkf');
//print_r($province);exit;
//$province = implode(',',$province_arr);
if (!empty($stk_arr)) {
    $stk = implode(',', $stk_arr);
}
if (!empty($itm_arr_request)) {
    $itm = implode(',', $itm_arr_request);
}
//print_r((!empty($stk_arr) ? $stk : $default_stk));exit;
 
 $hf_arr = [2, 7, 73];
 $sdo_hf = '';
if (count($stk_arr) >= 1) {
    if ($stk_arr == [1, 2, 7, 73]||$stk_arr==[1,2]||$stk_arr==[1,2,7]||$stk_arr==[1,7]||$stk_arr==[1,73]) {
        $sdo_hf = "SDO/HF";
    } else {
        foreach ($stk_arr as $key => $value) {
            if ($value == 1) {
                $sdo_hf = "SDO";
            }
            else if (in_array($value, $hf_arr)) {
                $sdo_hf = "HF";
            } 
            else {
                $sdo_hf = "SDO";
            }
        }
    }
}

$itemId = 1;
?>


<div class="widget widget-tabs">
    
    <div class="widget-body" id="a1" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
                        <li><a   id="panel-fullscreen_a1" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
        <?php
        //query
        $qry = "SELECT
	*
FROM
	(
		SELECT
			COUNT(
				DISTINCT tbl_warehouse.wh_id
			) AS total 
		FROM
			tbl_warehouse
		INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                	INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
		WHERE
			1 = 1
		AND tbl_warehouse.wh_id NOT IN (
			SELECT
				warehouse_status_history.warehouse_id
			FROM
				warehouse_status_history
			INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                        	INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
			WHERE
				warehouse_status_history.reporting_month = '$rep_date'
			AND warehouse_status_history.`status` = 0
			AND tbl_warehouse.stkid IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ")
                            	$where_hf
		)
		AND tbl_warehouse.stkid IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ")
		AND tbl_warehouse.prov_id = $province
		AND stakeholder.lvl = 7
                $where_hf
	) A
JOIN (
	SELECT
		COUNT(
			DISTINCT tbl_warehouse.wh_id
		) AS reported 
	FROM
		tbl_warehouse
	INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
	INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
	INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
        	INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
	WHERE
		1 = 1
	AND tbl_warehouse.wh_id NOT IN (
		SELECT
			warehouse_status_history.warehouse_id
		FROM
			warehouse_status_history
		INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                	INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
		WHERE
			warehouse_status_history.reporting_month = '$rep_date'
		AND warehouse_status_history.`status` = 0
		AND tbl_warehouse.stkid IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ")
                    $where_hf
	)
	AND tbl_warehouse.stkid IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ")
	AND tbl_warehouse.prov_id = $province
	AND stakeholder.lvl = 7
	AND tbl_hf_data.item_id IN (" . (!empty($itm_arr_request) ? $itm : $default_itm) . ")
	AND tbl_hf_data.reporting_date = '$rep_date'
             $where_hf
) B

	";


//            echo $qry;  
        $data_1 = '';
        $center_val='';
        $q_res = mysql_query($qry);
        while ($row = mysql_fetch_assoc($q_res)) {
//                if (isset($row['itm_name']))
//                {
//                    $cartons = (!empty($row['qty_carton'] && $row['qty_carton']>0)?round($row['soh']/$row['qty_carton']):'0');
//                    $pallets = (!empty($row['carton_per_pallet'] && $row['carton_per_pallet']>0)?round($cartons/$row['carton_per_pallet']):'0');
               if((($row['reported']/$row['total'])*100)!=null){    
                    $center_val=round((($row['reported']/$row['total'])*100),2).'%';
                    $data_1 .= "<set label='' value='".(100-(($row['reported']/$row['total'])*100))."' showValue= '0' color='#F6CECE' />" ;
                    $data_1 .= "<set label='Reporting Rate' value='".(($row['reported']/$row['total'])*100)."' color='#2ecc71' />" ;
                    $data_1 .= "<set  value='-100' alpha='0' showValue= '0'/>";
                }
        }
        $xmlstore = "<chart  numberSuffix='%' defaultCenterLabel= '$center_val' showLegend='1' theme='fint'  baseFontColor='#000' valueFontSize='11' valueFontColor='#000'   animateClockwise='1'  exportEnabled='1' exportAction='Download' caption='Provincial Reporting Rate by $sdo_hf' exportFileName='Provincial Reporting Rate by SDOs " . date('Y-m-d H:i:s') . "'>";
        $xmlstore .= $data_1;
        $xmlstore .= "</chart>";
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Doughnut2D.swf", "", $xmlstore, 'prov_reporting_rate', '100%', 400, false, false);
        ?>
        
    </div>
</div>

<script>
$(document).ready(function () {
    //Toggle fullscreen
    $("#panel-fullscreen_a1").click(function (e) {
        e.preventDefault();
//        console.log('into js');
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('div').toggleClass('panel-fullscreen');
    });
});


</script>