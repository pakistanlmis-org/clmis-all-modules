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
$rep_date = $_POST['from_date'] . '-01';
$hf_arr_request = (!empty($_REQUEST['hf']) ? $_REQUEST['hf'] : '');
if (!empty($hf_arr_request)) {
    $hf = implode(',', $hf_arr_request);
$where_hf="AND tbl_hf_type.pk_id IN (".$hf.")";
}
//$province = implode(',', $province_arr);
$stk = implode(',', $stk_arr);
$itm = implode(',', $itm_arr_request);
//print_r($itm);exit;
$where = '';
$where_itm = '';
if (count($itm_arr_request) > 1) {
    $where_itm .= " AND itminfo_tab.itm_id IN(";

    foreach ($itm_arr_request as $item) {
        $where_itm .= "$item" . ",";
//        echo $stk;
    }


    $where_itm = rtrim($where_itm, ',') . ')';
//echo $where;exit;
} else if (count($itm_arr_request) == 1) {
    $where_itm .= " AND itminfo_tab.itm_id =$itm_arr_request[0]";
//        echo $where;exit;
}
if (count($stk_arr) > 1) {
    $where .= " AND tbl_warehouse.stkid IN (";

    foreach ($stk_arr as $stk) {
        $where .= "$stk" . ",";
//        echo $stk;
    }


    $where = rtrim($where, ',') . ')';
//echo $where;exit;
} else if (count($stk_arr) == 1) {
    $where .= " AND  tbl_warehouse.stkid =$stk_arr[0]";
//        echo $where;exit;
}
$hf_arr = [2, 7, 73];
$sdo_hf = '';
if (count($stk_arr) >= 1) {
    if ($stk_arr == [1, 2, 7, 73] || $stk_arr == [1, 2] || $stk_arr == [1, 2, 7] || $stk_arr == [1, 7] || $stk_arr == [1, 73]) {

        $sdo_hf = "SDO/HF";
    } else {
        foreach ($stk_arr as $key => $value) {
            if ($value == 1) {
                $sdo_hf = "SDO";
            } else if (in_array($value, $hf_arr)) {
                $sdo_hf = "HF";
            } else {
                $sdo_hf = "SDO";
            }
        }
    }
}
$itemId = 1;
?>

<?php if ($province == 1) {
    
} else { ?>
    <div class="widget widget-tabs">
         <div class="widget-body" id="a6" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
                        <li><a   id="panel-fullscreen_a6" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
            <?php
//query
            $qry = "SELECT
itminfo_tab.itm_id,
itminfo_tab.itm_name,
 
ROUND(SUM(tbl_hf_data.closing_balance)/ SUM(tbl_hf_data.avg_consumption),2) AS stock_status,
tbl_hf_data.item_id
FROM
tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
WHERE tbl_hf_data.reporting_date = '$rep_date' 
$where $where_itm
 $where_hf
AND tbl_warehouse.prov_id = $province
GROUP BY itminfo_tab.itm_id
ORDER by itminfo_tab.frmindex
 ";

//            echo $qry;exit;
            $data_1 = '';
            $q_res = mysql_query($qry);

            $c = 1;
            while ($row1 = mysql_fetch_array($q_res)) {
                $rows[$c++] = $row1;
            }
//print_r($rows);
//exit;
            if (isset($rows)) {
                foreach ($rows as $row) {

                    $data_1 .= "<set color=' #f5b041 ' label='" . $row['itm_name'] . "' value=' " . $row['stock_status'] . " ' />";
                }
            }
//       echo 'label is '+$data_1;exit;
//        echo $sat;exit;

            $xmlstore = "<chart showLegend='1' theme='fint' yaxismaxvalue='10'   exportEnabled='1' exportAction='Download' caption='$sdo_hf Months of Stock' exportFileName='Hfs Months of Stock" . date('Y-m-d H:i:s') . "'>";
            $xmlstore .= $data_1;
            $xmlstore .= "<trendlines>
        <line startvalue='0.5'  color='#FF0000' valueonright='1' displayvalue='Min:0.5' />
                <line startvalue='3'  color='#FF0000' valueonright='1' displayvalue='Max:3' />

    </trendlines>";
            $xmlstore .= "</chart>";
            FC_SetRenderer('javascript');
            echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Column2D.swf", "", $xmlstore, 'hf_stock_status', '100%', 450, false, false);
            ?>
        </div>
    </div>
<?php
}
?>
<script>
$(document).ready(function () {
    //Toggle fullscreen
    $("#panel-fullscreen_a6").click(function (e) {
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