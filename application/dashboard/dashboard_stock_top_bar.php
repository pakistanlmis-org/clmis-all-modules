<?php
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");

include APP_PATH . "includes/classes/functions.php";
$count_dist = 0;
$count_rep_dist = 0;
$so_dist = 0;
$sdo_dist = 0;
$total_sdo=0;
$so_sdo = 0;
$margin='';
$where_hf='';
$province = (!empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '');
$stk_arr = (!empty($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : '');
$itm_arr_request = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');
$from_date = (!empty($_REQUEST['from_date']) ? $_REQUEST['from_date'] . '-01' : '');
$hf_arr_request = (!empty($_REQUEST['hf']) ? $_REQUEST['hf'] : '');
if (!empty($hf_arr_request)) {
    $hf = implode(',', $hf_arr_request);
$where_hf="AND tbl_hf_type.pk_id IN (".$hf.")";
}
$prov_name = '';
$qry_prov_name = "SELECT
	LocName
FROM
	tbl_locations
WHERE
	PKLocID = $province";
$qry_name_res = mysql_query($qry_prov_name);
while ($row_name = mysql_fetch_assoc($qry_name_res)) {
    $prov_name = $row_name['LocName'];
}
if (!empty($stk_arr)) {
    $stk = implode(',', $stk_arr);
}
if (!empty($itm_arr_request)) {
    $itm = implode(',', $itm_arr_request);
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

$qry_total_districts = "SELECT DISTINCT
tbl_locations.PkLocID,
tbl_locations.LocName,
tbl_locations.LocLvl,
tbl_locations.ParentID
FROM
tbl_locations
INNER JOIN tbl_warehouse ON tbl_warehouse.dist_id = tbl_locations.PkLocID
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
 
tbl_warehouse.prov_id = $province AND
tbl_warehouse.stkid IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ") AND
tbl_warehouse.is_active = 1 AND
stakeholder.lvl > 2
ORDER BY
tbl_locations.LocName ASC";

//echo $qry_total_districts;
$q_res_dist = mysql_query($qry_total_districts);
//if (!$q_res_dist) {
//    print_r( mysql_error());
//}
while ($row = mysql_fetch_assoc($q_res_dist)) {
    $count_dist++;
}
$qry_rep_dist = "SELECT

COUNT( DISTINCT tbl_warehouse.dist_id) AS count_dist
FROM
	tbl_hf_data
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
WHERE
 
tbl_hf_data.item_id IN (" . (!empty($itm_arr_request) ? $itm : $default_itm) . ") AND
tbl_warehouse.prov_id = $province AND
tbl_warehouse.stkid IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ") AND
tbl_hf_data.reporting_date = '$from_date'";
//echo $qry_rep_dist;
$res_rep_dist = mysql_query($qry_rep_dist);
while ($row_dist = mysql_fetch_assoc($res_rep_dist)) {
    $count_rep_dist = $row_dist['count_dist'];
}
$qry_so_dist = "SELECT
summary_district.avg_consumption,
summary_district.stakeholder_id,
summary_district.item_id,
COUNT(summary_district.item_id) AS pro_count,
summary_district.reporting_date,
summary_district.soh_district_store,
ROUND((summary_district.soh_district_store/summary_district.avg_consumption),2) AS stock_status,
itminfo_tab.itm_name,
itminfo_tab.itm_id
FROM
summary_district
INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
WHERE

summary_district.reporting_date = '$from_date' AND
ROUND(summary_district.soh_district_store/summary_district.avg_consumption) >= 0 AND
ROUND(summary_district.soh_district_store/summary_district.avg_consumption) <= 0.99 
AND summary_district.stakeholder_id IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ") AND
    itminfo_tab.itm_id IN (" . (!empty($itm_arr_request) ? $itm : $default_itm) . ") 
    AND summary_district.province_id = $province
GROUP BY summary_district.item_id";
//echo $qry_so_dist;
$res_so_dist = mysql_query($qry_so_dist);
while ($row_so_dist = mysql_fetch_assoc($res_so_dist)) {
    $so_dist += $row_so_dist['pro_count'];
}
$qry_rep_sdo = "SELECT
		COUNT(
			DISTINCT tbl_warehouse.wh_id
		) AS rep_sdo_count
FROM
tbl_warehouse
INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
WHERE
	tbl_warehouse.prov_id = $province
AND tbl_warehouse.stkid IN   (" . (!empty($stk_arr) ? $stk : $default_stk) . ")
AND tbl_hf_data.item_id = 1
AND tbl_hf_data.reporting_date = '$from_date'
    $where_hf
AND tbl_warehouse.wh_id NOT IN (
		SELECT
			warehouse_status_history.warehouse_id
		FROM
			warehouse_status_history
		INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
		WHERE
			warehouse_status_history.reporting_month = '$from_date'
		AND warehouse_status_history.`status` = 0
		AND tbl_warehouse.stkid IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ")
                   $where_hf
	)
 ";

//echo $qry_rep_sdo;
$qry_total_sdo="SELECT
			COUNT(
				DISTINCT tbl_warehouse.wh_id
			) AS totalWH
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
				warehouse_status_history.reporting_month = '$from_date'
			AND warehouse_status_history.`status` = 0
			AND tbl_warehouse.stkid IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ")
                            $where_hf
		)
		AND tbl_warehouse.stkid IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ")  
		AND tbl_warehouse.prov_id = $province
                   $where_hf
		AND stakeholder.lvl = 7"
        ;
$res_sdo_total = mysql_query($qry_total_sdo);
while ($row_sdo_total = mysql_fetch_assoc($res_sdo_total)) {
    $total_sdo = $row_sdo_total['totalWH'];
}
$res_sdo = mysql_query($qry_rep_sdo);
while ($row_sdo = mysql_fetch_assoc($res_sdo)) {
    $sdo_dist = $row_sdo['rep_sdo_count'];
}
$qry_so_sdo = "SELECT
	ROUND(
		tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption
	) AS stock_status,
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
	(ROUND(
		tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption
	) <= 0.99 OR 
(tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption) IS NULL)
 
AND itminfo_tab.itm_category = 1
AND tbl_hf_data.reporting_date = '$from_date'
AND tbl_hf_data.item_id IN (" . (!empty($itm_arr_request) ? $itm : $default_itm) . ") AND
tbl_warehouse.prov_id = $province
AND tbl_warehouse.stkid IN (" . (!empty($stk_arr) ? $stk : $default_stk) . ")
   $where_hf
GROUP BY
	tbl_hf_data.item_id";
//echo $qry_so_sdo;
$res_so_sdo = mysql_query($qry_so_sdo);
while ($row_so_sdo = mysql_fetch_assoc($res_so_sdo)) {
    $so_sdo += $row_so_sdo['count_pro'];
}
$col="";
$col_1="";
$spec="";
if($province==1)
{
    $col="col-md-3";
    $col_1="col-md-3";
    $spec="col-md-3";
}
 else 
    
 {
     $col_1="col-md-2";
     $col="col-md-2";
    $spec=" col-md-2";
 }

?>
<div class="<?php echo $col_1;?> center">
    <div class="dashboard-stat red-pink" style="height:100px;background-color: #81ce4b;">
        <div class="visual"><i class="fa fa-<?php if($province==3)echo "kpk";else if($province==4) echo"baloochistan"; else echo lcfirst($prov_name);?>"></i></div>
        <div class="details">
            <div class="number" id="general_av_dist"><?php echo $count_dist ?></div>
            <div class="desc" style="font-size:13px;"><?php echo $prov_name; ?> <br/>Total Districts</div>
        </div>
    </div>
</div>
<div class="<?php echo $col?>">
    <div class="dashboard-stat red-pink" style="height:100px;background-color: #81ce4b;">
        <div class="visual"><i class="fa fa-bar-chart-o"></i></div>
        <div class="details">
            <div class="number" id="general_av_sdp"><?php echo $count_rep_dist; ?></div>
            <div class="desc" style="font-size:13px;">Reporting Districts </div>
        </div>
    </div>
</div>
<?php if ($province==1){} else{?>
<div class="<?php echo $col?> center">
    <div class="dashboard-stat red-pink " style="height:100px;background-color: #81ce4b;">
        <div class="visual"><i class="fa fa-bar-chart-o"></i></div>
        <div class="details">

            <div class="number" id="general_av_sdp"><?php echo $so_dist; ?></div>

            <div class="desc" style="font-size:12px;">Stockout Instances at <br> District Store </div>
        </div>
    </div>
</div>
<?php }?>
<div class="<?php echo $col?> center ">
    <div class="dashboard-stat red-pink" style="height:100px;background-color: #81ce4b;">
        <div class="visual"><i class="fa  fa-bar-chart-o"></i></div>
        <div class="details">

            <div class="number" id="general_av_sdp"><?php echo $total_sdo; ?></div>

            <div class="desc" style="font-size:12px;">  Total <?php echo $sdo_hf; ?> </div>
        </div>
    </div>
</div>
<div class="<?php echo $spec?> center ">
    <div class="dashboard-stat red-pink" style="height:100px;background-color: #81ce4b;">
        <div class="visual"><i class="fa  fa-bar-chart-o"></i></div>
        <div class="details">

            <div class="number" id="general_av_sdp"><?php echo $sdo_dist; ?></div>

            <div class="desc" style="font-size:12px;">  Reporting <?php echo $sdo_hf; ?> </div>
        </div>
    </div>
</div>
<?php if ($province==1){} else{?>
<div class="<?php echo $col_1?> center">
    <div class="dashboard-stat red-pink" style="height:100px;background-color: #81ce4b;">
        <div class="visual"><i class="fa  fa-bar-chart-o"></i></div>
        <div class="details">

            <div class="number" id="general_av_sdp"><?php echo $so_sdo; ?></div>

            <div class="desc" style="font-size:12px;">  Stockout Instances <br>at <?php echo $sdo_hf; ?> </div>
        </div>
    </div>
</div>
<?php }