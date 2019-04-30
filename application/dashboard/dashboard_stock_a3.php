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
$province = (!empty($_POST['prov_sel']) ? $_POST['prov_sel'] : '');
$stk_arr = (!empty($_POST['stakeholder']) ? $_POST['stakeholder'] : '');
$itm_arr_request = (!empty($_POST['product']) ? $_POST['product'] : '');
//print_r( $itm_arr_request[0]);exit;
$rep_date=$_POST['from_date'].'-01';
//$province = implode(',', $province_arr);
$hf_arr_request = (!empty($_REQUEST['hf']) ? $_REQUEST['hf'] : '');
if (!empty($hf_arr_request)) {
    $hf = implode(',', $hf_arr_request);
$where_hf="AND tbl_hf_type.pk_id IN (".$hf.")";
}
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
//print_r($stk);exit;
//$stkId = $_POST['stakeholder'];
//item id

$itemId = 1;
?>


<div class="widget widget-tabs">
    <div class="widget-body" id="a3" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
                        <li><a   id="panel-fullscreen_a3" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
        <?php
       

        //query
        $qry = "SELECT
ROUND(tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption) AS stock_status,
COUNT(tbl_hf_data.item_id) AS count_pro,
tbl_hf_data.item_id,
tbl_hf_data.reporting_date,
itminfo_tab.itm_name
FROM
tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
WHERE
 (((tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption) >=0 
AND  (tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption) <=0.5 )
OR (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption is null))
AND itminfo_tab.itm_category = 1
AND tbl_hf_data.reporting_date='$rep_date' $where_itm
    AND tbl_warehouse.prov_id=$province
$where
  $where_hf
GROUP BY tbl_hf_data.item_id
ORDER by itminfo_tab.frmindex
 ";

//            echo $qry; 
        $data_1 = '';
        $q_res = mysql_query($qry);
        $count = 0;
         
        while ($row1 = mysql_fetch_array($q_res)) {
            $rows[] = $row1;
            $count += $row1['count_pro'];
        }
        if (isset($rows)) {
            foreach($rows as $row) {  
          
                $data_1 .= "<set label='" . $row['itm_name'] . "' value=' ".round(($row['count_pro']/$count)*100,2)." ' />";
           
        }
        }
//       echo 'label is '+$data_1;exit;
//        echo $sat;exit;

        $xmlstore = "<chart numberSuffix='%' pieRadius='100' showLegend='1' theme='fint'  baseFontColor='#000' valueFontSize='11' valueFontColor='#000'    exportEnabled='1' exportAction='Download' caption='Stockouts by Product' exportFileName='Stockouts by Product" . date('Y-m-d H:i:s') . "'>";
        $xmlstore .= $data_1;
        $xmlstore .= "</chart>";
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Doughnut2D.swf", "", $xmlstore, 'prod_stock_status', '100%', 400, false, false);
        ?>
    </div>
</div>
<script>
$(document).ready(function () {
    //Toggle fullscreen
    $("#panel-fullscreen_a3").click(function (e) {
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