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
    $where_itm .= " AND itminfo_tab.itm_id  =$itm_arr_request[0]";
//        echo $where;exit;
}
if (count($stk_arr) > 1) {
    $where .= " AND stakeholder.stkid IN(";

    foreach ($stk_arr as $stk) {
        $where .= "$stk" . ",";
//        echo $stk;
    }


    $where = rtrim($where, ',') . ')';
//echo $where;exit;
} else if (count($stk_arr) == 1) {
    $where .= " AND stakeholder.stkid =$stk_arr[0]";
//        echo $where;exit;
}
//print_r($stk);exit;
//$stkId = $_POST['stakeholder'];
//item id

$itemId = 1;
?>


<div class="widget widget-tabs">
     <div class="widget-body" id="a5" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
                        <li><a   id="panel-fullscreen_a5" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
<?php
//query
$qry = "SELECT
tbl_locations.PkLocID AS distId,
tbl_locations.LocName AS distName,
Province.PkLocID AS provId,
Province.LocName AS provName,
stakeholder.stkname,
Sum(summary_district.avg_consumption) AS amc,
Sum(summary_district.soh_district_store) AS SOH_district,
ROUND(
		(
			sum(
				summary_district.soh_district_store
			) / sum(
				summary_district.avg_consumption
			)
		),
		2
	) AS MOS_district_Stores,
itminfo_tab.itm_id,
itminfo_tab.itm_name
FROM
summary_district
INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
INNER JOIN tbl_hf_type ON stakeholder.stkid = tbl_hf_type.stakeholder_id
WHERE
	summary_district.reporting_date = '$rep_date'
$where $where_itm
 
AND summary_district.province_id = $province
    $where_hf
GROUP BY
	summary_district.item_id
ORDER BY
         itminfo_tab.frmindex


 ";

//            echo $qry;exit;
$data_1 = '';
$q_res = mysql_query($qry);
$count = 0;
$c = 1;

while ($row1 = mysql_fetch_array($q_res)) {
    $rows[$c++] = $row1;
}
//        print_r($rows);exit;
if (isset($rows)) {
    foreach ($rows as $row) {
        if ($row['MOS_district_Stores'] < 0) {
            $row['MOS_district_Stores'] = 0;
        }
        $data_1 .= "<set color='#e59866' label='" . $row['itm_name'] . "' value=' " . $row['MOS_district_Stores'] . " ' />";
    }
}
//       echo 'label is '+$data_1;exit;
//        echo $sat;exit;

$xmlstore = "<chart showLegend='1' theme='fint' yaxismaxvalue='10'  exportEnabled='1' exportAction='Download' caption='District Store Months of Stock' exportFileName='District Store Months of Stock" . date('Y-m-d H:i:s') . "'>";
$xmlstore .= $data_1;
$xmlstore .= "<trendlines>
        <line startvalue='1'  color='#FF0000' valueonright='1' displayvalue='Min:1' />
                <line startvalue='6'  color='#FF0000' valueonright='1' displayvalue='Max:6' />

    </trendlines>";
$xmlstore .= "</chart>";
FC_SetRenderer('javascript');
echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Column2D.swf", "", $xmlstore, 'dist_stock_status', '100%', 450, false, false);
?>
    </div>
</div>
<script>
$(document).ready(function () {
    //Toggle fullscreen
    $("#panel-fullscreen_a5").click(function (e) {
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