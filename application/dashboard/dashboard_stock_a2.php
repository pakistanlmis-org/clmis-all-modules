<?php
ini_set('max_execution_time',120);
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
$province = (!empty($_POST['prov_sel']) ? $_POST['prov_sel'] : '');
$stk_arr = (!empty($_POST['stakeholder']) ? $_POST['stakeholder'] : '');
$itm_arr_request = (!empty($_POST['product']) ? $_POST['product'] : '');
$rep_date=$_POST['from_date'].'-01';
$hf_arr_request = (!empty($_REQUEST['hf']) ? $_REQUEST['hf'] : '');
if (!empty($hf_arr_request)) {
    $hf = implode(',', $hf_arr_request);
$where_hf="AND tbl_hf_type.pk_id IN (".$hf.")";
}
//$province = implode(',', $province_arr);
$stk = implode(',', $stk_arr);
$itm = implode(',', $itm_arr_request);
$where = '';
$where_itm = '';
if (count($itm_arr_request) > 1) {
    $where_itm .= " AND tbl_hf_data.item_id IN(";

    foreach ($itm_arr_request as $item) {
        $where_itm.="$item".",";
//        echo $stk;
    }
    
  
$where_itm = rtrim($where_itm, ',') . ')';
//echo $where;exit;
   } 
    else if(count($itm_arr_request) == 1)
    {
         $where_itm .= " AND tbl_hf_data.item_id =$itm_arr_request[0]";
//        echo $where;exit;

    }
if (count($stk_arr) > 1) {
    $where .= " AND tbl_warehouse.stkid IN(";

    foreach ($stk_arr as $stk) {
        $where.="$stk".",";
//        echo $stk;
    }
    
  
$where = rtrim($where, ',') . ')';
//echo $where;exit;
   } 
    else if(count($stk_arr) == 1)
    {
         $where .= " AND tbl_warehouse.stkid =$stk_arr[0]";
//        echo $where;exit;

    }
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
    <div class="widget-body" id="a2" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
                        <li><a   id="panel-fullscreen_a2" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
        <?php
        $so = $us = $sat = $os = 0;

        //query
        $qry = "SELECT
ROUND((tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption),2) AS stock_status,
tbl_hf_data.item_id,
tbl_hf_data.reporting_date
FROM
tbl_hf_data
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
 INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
WHERE
 
tbl_hf_data.reporting_date='$rep_date' $where $where_itm
    AND
tbl_warehouse.prov_id = $province
    $where_hf
 ";


//            echo $qry; 
        $data_1 = '';
        $q_res = mysql_query($qry);
        $num_rows = mysql_num_rows($q_res);
//        print_r($num_rows);
//        exit;
        $unk=0;
        while ($row = mysql_fetch_assoc($q_res)) {
            if ( $row['stock_status'] <= 0.5 || $row['stock_status'] == null) {
                $so++;
            } else if ($row['stock_status'] >= 0.51 && $row['stock_status'] <= 0.99) {
                $us++;
            } else if ($row['stock_status'] >= 1 && $row['stock_status'] <= 2.99) {
                $sat++;
            } else if ($row['stock_status'] >= 3&& $row['stock_status'] <= 9999) {
                $os++;
            }
           
        }
        //echo $so.' , unk :'. $unk;exit;
        if($num_rows>0){
                      $data_1 .= "<set label='Stock out'    value='" . round(( $so/$num_rows)*100,2)  ."' color='#FF0000' />";
                      $data_1 .= "<set label='Under Stock'  value='" . round(( $us/$num_rows)*100,2) . "' color='#0B2161'/>";
                      $data_1 .= "<set label='Satisfactory' value='" . round(($sat/$num_rows)*100,2) . "' color='#31B404'/>";
                      $data_1 .= "<set label='Overstock'    value='" . round(( $os/$num_rows)*100,2) . "' color='#F6D64C'/>";
        }
        $xmlstore = "<chart numberSuffix='%' showLegend='1' theme='fint'   baseFontColor='#000' valueFontSize='11' valueFontColor='#000'   exportEnabled='1' exportAction='Download' caption='Provincial Stock Status by $sdo_hf' exportFileName='Provincial Stock Status by SDOs " . date('Y-m-d H:i:s') . "'>";
        $xmlstore .= $data_1;
        $xmlstore .= "</chart>";
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Doughnut2D.swf", "", $xmlstore, 'prov_stock_status', '100%', 400, false, false);
        ?>
    </div>
</div>
<script>
$(document).ready(function () {
    //Toggle fullscreen
    $("#panel-fullscreen_a2").click(function (e) {
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