<?php
/**
 * Commodity Security Data entry Drilldown
 * @package dashboard
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//echo '<pre>';print_r($_REQUEST);exit; 
//include Configuration
include("../includes/classes/Configuration.inc.php");
//login
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include  functions
include APP_PATH . "includes/classes/functions.php";

include(PUBLIC_PATH . "html/header.php");


$requisition_status=false;
$f_date = (!empty($_REQUEST['from_date']) ? $_REQUEST['from_date'] : date("Y-m") . '-01');
$req_date=(!empty($_REQUEST['from_date']) ? date("Y-m-t", strtotime($_REQUEST['from_date'])) : date("Y-m") );
$r_date=date("Y-m-d", strtotime($req_date));
$dist_id = (!empty($_REQUEST['dist_id']) ? $_REQUEST['dist_id'] : 'all');
$dist_name = (!empty($_REQUEST['dist_name']) ? $_REQUEST['dist_name'] : 'all');
$prod_id = (!empty($_REQUEST['prod_id']) ? $_REQUEST['prod_id'] : '');
$indicator = (!empty($_REQUEST['indicator']) ? $_REQUEST['indicator'] : '');

//echo $req_date;exit;
//print_r(substr($req_date,  5, 2));exit;
$from_date = date("Y-m-d", strtotime($f_date));

$stk = (!empty($_REQUEST['stk']) ? $_REQUEST['stk'] : 'all');
$stk_name = (!empty($_REQUEST['stk_name']) ? $_REQUEST['stk_name'] : 'All');

$where_clause = "";
if (!empty($stk) && $stk != 'all') {
    $where_clause .= " AND tbl_warehouse.stkid=$stk ";
}
if (!empty($dist_id) && $dist_id != 'all') {
    $where_clause .= " AND tbl_warehouse.dist_id=$dist_id ";
}

$qry = "SELECT
            list_detail.pk_id,
            list_detail.list_value,
            list_detail.description,
            list_detail.rank,
            list_detail.reference_id,
            list_detail.parent_id,
            list_detail.list_master_id
        FROM
            list_detail
        WHERE
            list_detail.list_master_id in (22,23,25,26,27,28)
        ORDER BY
            list_detail.rank ,
            list_detail.list_value ASC
    ";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$comments_arr = $actions_arr = $comments_arr_dist = $actions_arr_dist = array();
while ($row = mysql_fetch_assoc($qryRes)) {
    if ($row['list_master_id'] == '22')
        $comments_arr[$row['pk_id']] = $row['list_value'];
    if ($row['list_master_id'] == '23')
        $actions_arr[$row['pk_id']] = $row['list_value'];

    if ($row['list_master_id'] == '25')
        $comments_arr_dist_store[$row['pk_id']] = $row['list_value'];
    if ($row['list_master_id'] == '26')
        $actions_arr_dist_store[$row['pk_id']] = $row['list_value'];

    if ($row['list_master_id'] == '27')
        $comments_arr_dist_level[$row['pk_id']] = $row['list_value'];
    if ($row['list_master_id'] == '28')
        $actions_arr_dist_level[$row['pk_id']] = $row['list_value'];
}
//print_r($comments_arr);
//fetch district store id
$qry = "SELECT
                tbl_warehouse.wh_id,
                tbl_warehouse.wh_name,
                tbl_warehouse.dist_id,
                tbl_warehouse.prov_id,
                tbl_warehouse.stkid,
                tbl_warehouse.hf_type_id,
                stakeholder.lvl,
                summary_district.consumption,
                summary_district.avg_consumption,
                summary_district.soh_district_store,
                summary_district.soh_district_lvl,
                itminfo_tab.itm_id,
                itminfo_tab.itm_name
            FROM
            tbl_warehouse
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            INNER JOIN summary_district ON tbl_warehouse.dist_id = summary_district.district_id
            INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
            WHERE
            tbl_warehouse.dist_id = $dist_id AND
            tbl_warehouse.stkid = $stk AND
            stakeholder.lvl = 3 AND
            summary_district.reporting_date = '$from_date' AND
            itminfo_tab.itm_id = $prod_id AND
            summary_district.stakeholder_id = $stk";
//  print_r($qry);
$qryRes = mysql_query($qry);
$district_stores = array();
while ($row = mysql_fetch_assoc($qryRes)) {
    $district_stores[$row['wh_id']] = $row;
}


//fetch stock out reasons already saved for SDP
$qry = "SELECT
        stock_out_reasons.wh_id,
        stock_out_reasons.itm_id,
        stock_out_reasons.`month`,
        stock_out_reasons.reason,
        stock_out_reasons.comments,
        action_suggested
        FROM
        stock_out_reasons
        INNER JOIN tbl_warehouse ON stock_out_reasons.wh_id = tbl_warehouse.wh_id
        WHERE
        stock_out_reasons.`month` = '" . $from_date . "' ";
$qry .= "AND stock_out_reasons.itm_id = $prod_id ";
if (!empty($dist_id) && $dist_id != 'all')
    $qry .= " AND tbl_warehouse.dist_id = $dist_id ";
if (!empty($stk) && $stk != 'all')
    $qry .= " AND tbl_warehouse.stkid = $stk ";


//  print_r($qry);
$qryRes = mysql_query($qry);
$reasons_saved_arr = $actions_saved_arr = array();
$remarks_array =array();
while ($row = mysql_fetch_assoc($qryRes)) {
    $reasons_saved_arr[$row['wh_id']] = $row['reason'];
    $actions_saved_arr[$row['wh_id']] = $row['action_suggested'];
    $remarks_array[$row['wh_id']]=$row['comments'];
}
//print_r($reasons_saved_arr);
//print_r($remarks_array);
//fetch stock out reasons already saved for District Level
$qry = "SELECT
        stock_out_reasons_district_level.dist_id,
        stock_out_reasons_district_level.stk_id,
        stock_out_reasons_district_level.itm_id,
        stock_out_reasons_district_level.`month`,
        stock_out_reasons_district_level.reason,
        action_suggested
        FROM
        stock_out_reasons_district_level
        WHERE
            stock_out_reasons_district_level.`month` = '" . $from_date . "' ";
$qry .= "AND stock_out_reasons_district_level.itm_id = $prod_id ";
if (!empty($dist_id) && $dist_id != 'all')
    $qry .= " AND stock_out_reasons_district_level.dist_id = $dist_id ";
if (!empty($stk) && $stk != 'all')
    $qry .= " AND stock_out_reasons_district_level.stk_id = $stk ";


//echo $qry;    
$qryRes = mysql_query($qry);
$reasons_saved_arr_dist = $actions_saved_arr_dist = array();
while ($row = mysql_fetch_assoc($qryRes)) {
    $reasons_saved_arr_dist[$row['dist_id']] = $row['reason'];
    $actions_saved_arr_dist[$row['dist_id']] = $row['action_suggested'];
}
//echo '<pre>';print_r($reasons_saved_arr);print_r($actions_saved_arr);exit;
//Query for shipment main dashboard
$qry = "SELECT
                tbl_warehouse.prov_id,
                tbl_warehouse.stkid,
                tbl_hf_data.item_id ,
                itminfo_tab.itm_name,
                tbl_hf_data.pk_id,
                (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption) as mos,
                tbl_hf_data.closing_balance,
                tbl_hf_data.avg_consumption,
                tbl_warehouse.wh_name,
                tbl_warehouse.wh_id,
                tbl_warehouse.dist_id,
                st2.stkname,
                tbl_locations.LocName as district_name

            FROM
                    tbl_warehouse
            INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
            INNER JOIN stakeholder st2 ON st2.stkid = tbl_warehouse.stkid
            
            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
            INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
            INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
            INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
            WHERE
                    stakeholder.lvl = 7
            
            AND tbl_warehouse.wh_id NOT IN (
                    SELECT
                            warehouse_status_history.warehouse_id
                    FROM
                            warehouse_status_history
                    INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                    WHERE
                            warehouse_status_history.reporting_month = '" . $from_date . "'
                    AND warehouse_status_history.`status` = 0

            )
            AND tbl_hf_data.reporting_date = '" . $from_date . "'
            AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)

            AND itminfo_tab.itm_category = 1
            /*AND itminfo_tab.itm_id NOT IN(4,6,10,33)*/
            AND itminfo_tab.itm_id = $prod_id
                
            and tbl_hf_data.avg_consumption > 0
             AND (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption) <= 0.5

            $where_clause
        ORDER BY
                 
                tbl_hf_data.item_id,
                tbl_warehouse.wh_name

";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$c = 1;
$unk_arr = $so_arr2 = $so_arr = $us_arr = $sat_arr = $os_arr = array();

while ($row = mysql_fetch_assoc($qryRes)) {
    $itm_arr[$row['item_id']] = $row['itm_name'];
    $so_arr[] = $row;

    $ind_name = 'Stock Out';
    $display_arr = $so_arr;
}


$qry_requisition="SELECT
	count(clr_master.pk_id) as c
FROM
	clr_master
INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
 
WHERE
	tbl_warehouse.dist_id = $dist_id
AND tbl_warehouse.stkid = $stk
AND  clr_master.date_to = '$req_date' AND 
clr_details.itm_id = $prod_id";
//echo $qry_requisition;exit;
$qryReqRes = mysql_query($qry_requisition);
$row = mysql_fetch_assoc($qryReqRes);
if (!empty($row['c']) && $row['c']>0) {
    $requisition_status=true;
}
else
{
    $requisition_status=false;
}
//print_r($qry_requisition);exit;
//echo '<pre>';print_r($so_arr);echo'display_arr';print_r($display_arr);exit;    
?>
<style>
    span.multiselect-native-select {
        position: relative
    }
    span.multiselect-native-select select {
        border: 0!important;
        clip: rect(0 0 0 0)!important;
        height: 1px!important;
        margin: -1px -1px -1px -3px!important;
        overflow: hidden!important;
        padding: 0!important;
        position: absolute!important;
        width: 1px!important;
        left: 50%;
        top: 30px
    }
    .multiselect-container {
        position: absolute;
        list-style-type: none;
        margin: 0;
        padding: 0
    }
    .multiselect-container .input-group {
        margin: 5px
    }
    .multiselect-container>li {
        padding: 0
    }
    .multiselect-container>li>a.multiselect-all label {
        font-weight: 700
    }
    .multiselect-container>li.multiselect-group label {
        margin: 0;
        padding: 3px 20px 3px 20px;
        height: 100%;
        font-weight: 700
    }
    .multiselect-container>li.multiselect-group-clickable label {
        cursor: pointer
    }
    .multiselect-container>li>a {
        padding: 0
    }
    .multiselect-container>li>a>label {
        margin: 0;
        height: 100%;
        cursor: pointer;
        font-weight: 400;
        padding: 3px 0 3px 30px
    }
    .multiselect-container>li>a>label.radio, .multiselect-container>li>a>label.checkbox {
        margin: 0
    }
    .multiselect-container>li>a>label>input[type=checkbox] {
        margin-bottom: 5px
    }
    td{
        line-height: 5px !important;
    }

</style>


<div class="row">
    <div class="col-md-10">
        <h4 class="text-center">
            <div class="label label-success" style="font-size: 22px">
                Stock Out Reasons & actions advised - <?= $dist_name . ' - ' . $_REQUEST['prod_name'] . ' - ' . date('M Y', strtotime($from_date)) ?>
            </div>
    </div>
</div>

<div class="row">
    <div class="col-md-10">
        <h4 class="text-center">
            <div class="label label-info" style="font-size: 16px">
                District Level
            </div>
        </h4>
    </div>
    <div class="col-md-2">
        <div class="btn green hide" id="save_all">Save All</div>
    </div>

</div>

<table name="tbl" class="table table-bordered table-condensed" border="">    
    <thead>
        <tr>
            <th>District</th>
            <th>Stakeholder</th>
            <th>Reasons of stock out ( District Level )</th>
            <th>Actions advised ( District Level )</th>
            <th> </th>
        </tr>
    </thead>
    <tbody>
<?php
$clr = '';
if (!empty($reasons_saved_arr_dist[$dist_id]))
    $clr = 'success';
?>
        <tr class="<?= $clr ?>">
            <td><?= $dist_name ?></td>
            <td><?= $_REQUEST['stk_name'] ?></td>
            <td><?php
echo '<form id="frmr_dist_level" name="frmr_dist_level" class="forms">';

echo '<input type="hidden" name = "dist_id"     value="' . $dist_id . '">';
echo '<input type="hidden" name = "stk_id"     value="' . $stk . '">';
echo '<input type="hidden" name = "item_id"   value="' . $prod_id . '">';
echo '<input type="hidden" name = "month"     value="' . $from_date . '">';
echo '<select class="multiselect-ui form-control" name="commentsDistLevel_' . $dist_id . '_' . $stk . '_' . $prod_id . '_' . $from_date . '[]" multiple>';
//echo '<option value="">Select</option>';
foreach ($comments_arr_dist_level as $k => $r_txt) {
    $sel = '';
    $sv = array();
    $sv = explode(',', $reasons_saved_arr_dist[$dist_id]);
    if (!empty($dist_id) && in_array($k, $sv)) {
        $sel = ' selected ';
    } elseif ($mos == 'UNK' && $k == '130' && empty($reasons_saved_arr_dist[$v['wh_id']])) {
        $sel = ' selected ';
    }
    echo '<option value="' . $k . '" ' . $sel . ' >' . $r_txt . '</option>';
}
echo '</select>';
echo '</form>';
?></td>
            <td>
                <?php
                echo '<form id="frma_dist_level" name="frma_dist_level" class="forms">';
                echo '<select class="multiselect-ui form-control" name="actionsDistLevel_' . $dist_id . '_' . $stk . '_' . $prod_id . '_' . $from_date . '[]" multiple>';
                //echo '<option value="">Select</option>';
                foreach ($actions_arr_dist_level as $k => $r_txt) {
                    $sel = '';
                    $sv = array();
                    $sv = explode(',', $actions_saved_arr_dist[$dist_id]);
                    if (!empty($dist_id) && in_array($k, $sv)) {
                        $sel = ' selected ';
                    }

                    echo '<option value="' . $k . '" ' . $sel . ' >' . $r_txt . '</option>';
                }
                echo '</select>';
                echo '</form>';
                ?>
            </td>
            <td><div id="btn_dist_level" data-c="dist_level" class="btn btn-sm green save_cls_dist" > Save </div></td> 
        </tr>
    </tbody>
</table>



<div class="row">
    <div class="col-md-10">
        <h4 class="text-center">
            <div class="label label-info" style="font-size: 18px">
                District Store (Stock out if MOS < 1.0)
            </div>
        </h4>
    </div>
    <div class="col-md-2">
        <div class="btn green hide" id="save_all">Save All</div>
    </div>

</div>

<table name="tbl" class="table table-bordered table-condensed" border="">    
    <thead>
        <tr>
            <th>District Store</th>
            <th>Stakeholder</th>
            <th>Product</th>
            <th>SOH</th>
            <th>AMC</th>
            <th>MOS</th>
            <th>Reasons of stock out ( District Store )</th>
            <th>Actions advised ( District Store )</th>
           <th>Requisition Submitted</th>
            <th>Comments</th>
            <th> </th>
        </tr>
    </thead>
    <tbody>
<?php
foreach ($district_stores as $dist_store_id => $data) {

    $clr = '';
    if (!empty($reasons_saved_arr[$dist_store_id]))
        $clr = 'success';
    ?>
            <tr class="<?= $clr ?>">
                <td><?php
    $pop = 'onclick="window.open(\'update_poc_info.php?dist_id=' . $data['dist_id'] . '&stk_id=' . $data['stkid'] . '\',\'_blank\',\'scrollbars=1,width=840,height=595\')"';
    echo "<a style='color:red;' class='alert-link' " . $pop . " ></br>" .  substr($data['wh_name'],0,-14) . "</a>";

//                                echo $b['batch_no']; 
    ?></td>
    <!--                <td><?= $data['wh_name'] ?></td>-->
                <td><?= $_REQUEST['stk_name'] ?></td>
                <td><?= $data['itm_name'] ?></td>
                <td align="right"><?= number_format($data['soh_district_store']) ?></td>
                <td align="right"><?= number_format($data['avg_consumption']) ?></td>
            <?php
            $mos = 0;
            if (!empty($data['avg_consumption']))
                $mos = $data['soh_district_store'] / $data['avg_consumption'];

            $clr = '';
            if ($mos < 1)
                $clr = "red";
            ?>
                <td align="right" style="color:<?= $clr ?>"><?= number_format($mos, 2) ?></td>
                <td><?php
                    echo '<form id="frmr_' . $dist_store_id . '" name="frmr_' . $dist_store_id . '" class="forms">';

                    echo '<input type="hidden" name = "wh_id"     value="' . $dist_store_id . '">';
                    echo '<input type="hidden" name = "item_id"   value="' . $prod_id . '">';
                    echo '<input type="hidden" name = "month"     value="' . $from_date . '">';
                    echo '<select class="multiselect-ui form-control" name="comments_' . $dist_store_id . '_' . $prod_id . '_' . $from_date . '[]" multiple>';
                    //echo '<option value="">Select</option>';
                    foreach ($comments_arr_dist_store as $k => $r_txt) {
                        $sel = '';
                        $sv = array();
                        $sv = explode(',', $reasons_saved_arr[$dist_store_id]);
                        if (!empty($dist_store_id) && in_array($k, $sv)) {
                            $sel = ' selected ';
                        } elseif ($mos == 'UNK' && $k == '130' && empty($reasons_saved_arr[$v['wh_id']])) {
                            $sel = ' selected ';
                        }
                        echo '<option value="' . $k . '" ' . $sel . ' >' . $r_txt . '</option>';
                    }
                    echo '</select>';
                    echo '</form>';
                    ?></td>
                <td>
                    <?php
                    echo '<form id="frma_' . $dist_store_id . '" name="frma_' . $dist_store_id . '" class="forms">';
                    echo '<select class="multiselect-ui form-control" name="actions_' . $dist_store_id . '_' . $prod_id . '_' . $from_date . '[]" multiple>';
                    //echo '<option value="">Select</option>';
                    foreach ($actions_arr_dist_store as $k => $r_txt) {
                        $sel = '';
                        $sv = array();
                        $sv = explode(',', $actions_saved_arr[$dist_store_id]);
                        if (!empty($dist_store_id) && in_array($k, $sv)) {
                            $sel = ' selected ';
                        }

                        echo '<option value="' . $k . '" ' . $sel . ' >' . $r_txt . '</option>';
                    }
                    echo '</select>';
                    echo '</form>';
                    ?>
                </td>
                <td><?php $pop = 'onclick="window.open(\'requistion_status.php?dist_id=' . $dist_id . '&stk_id='.$data['stkid'].'&item_id='.$prod_id.'&date='.$from_date.'\',\'_blank\',\'scrollbars=1,width=840,height=595\')"';
                               $req_stat=  "  <a class='alert-link' " . $pop . " > View</a>";
if($requisition_status) echo "Yes $req_stat"; else echo 'No';?></td><?php
                foreach ($display_arr as $k => $v) {
                    $c = $v['wh_id'];
                }
                echo '<td><form id="frmd_' . $dist_store_id . '" name="frmd_' . $dist_store_id . '" class="forms">';
                echo '    <textarea class="form-control" name="remarks_' . $dist_store_id . '_' . $prod_id . '_' . $from_date . '" maxlength="255">'.@$remarks_array[$dist_store_id].'</textarea>';
                echo '</form></td>';
                    ?>
                <td><div id="btn_<?= $dist_store_id ?>" data-c="<?= $dist_store_id ?>" class="btn btn-sm green save_cls" > Save </div></td> 
            </tr>
                    <?php
                }
                ?>
    </tbody>
</table>
<div class="row">
    <div class="col-md-10">
        <h4 class="text-center">
            <div class="label label-info" style="font-size: 18px">
                Stocked Out facilities ( MOS = 0 to 0.5)
            </div>
        </h4>
    </div>
    <div class="col-md-2">
        <div class="btn green hide" id="save_all">Save All</div>
    </div>

</div>



<table name="tbl" class="table table-bordered table-condensed" border="">    
    <thead>
        <tr>
            <th>#</th>
            <th>District</th>
            <th>Stakeholder</th>
            <th>Facility</th>
            <th>Product</th>
            <th>SOH</th>
            <th>AMC</th>
            <th>MOS</th>
            <th>Reasons of stock out</th>
            <th>Actions advised</th>
            <th>Comments</th>
            <th> </th>
        </tr>
    </thead>

    <tbody>

<?php
$d = 1;
foreach ($display_arr as $k => $v) {
    $c = $v['wh_id'];
    if ($v['mos'] == NULL) {
        $mos = 'UNK';
    } else
        $mos = number_format($v['mos'], 2);

    $clr = '';
    if (!empty($reasons_saved_arr[$v['wh_id']]))
        $clr = 'success';

    echo '<tr class="' . $clr . '" id="row_' . $c . '">';
    echo '<td>' . $d . '</td>';
    echo '<td>' . $v['district_name'] . '</td>';
    echo '<td>' . $v['stkname'] . '</td>';
    echo '<td>' . $v['wh_name'] . '</td>';
    echo '<td>' . $v['itm_name'] . '</td>';
    echo '<td align="right">' . number_format($v['closing_balance']) . '</td>';
    echo '<td align="right">' . number_format($v['avg_consumption']) . '</td>';
    echo '<td align="right">' . $mos . '</td>';


    echo '<td align="right">';
    echo '<form id="frmr_' . $c . '" name="frmr_' . $c . '" class="forms">';
    echo '<input type="hidden" name = "wh_id"     value="' . $v['wh_id'] . '">';
    echo '<input type="hidden" name = "item_id"   value="' . $v['item_id'] . '">';
    echo '<input type="hidden" name = "month"     value="' . $from_date . '">';
    echo '<select class="multiselect-ui form-control" name="comments_' . $v['wh_id'] . '_' . $v['item_id'] . '_' . $from_date . '[]" multiple>';
    //echo '<option value="">Select</option>';
    foreach ($comments_arr as $k => $r_txt) {
        $sel = '';
        $sv = array();
        $sv = explode(',', $reasons_saved_arr[$v['wh_id']]);
        if (!empty($v['wh_id']) && in_array($k, $sv)) {
            $sel = ' selected ';
        } elseif ($mos == 'UNK' && $k == '130' && empty($reasons_saved_arr[$v['wh_id']])) {
            $sel = ' selected ';
        }
        echo '<option value="' . $k . '" ' . $sel . ' >' . $r_txt . '</option>';
    }
    echo '</select>';
    echo '</form>';
    echo '</td>';

    echo '<td align="right">';
    echo '<form id="frma_' . $c . '" name="frma_' . $c . '" class="forms">';
    echo '<select class="multiselect-ui form-control" name="actions_' . $v['wh_id'] . '_' . $v['item_id'] . '_' . $from_date . '[]" multiple>';
    //echo '<option value="">Select</option>';
    foreach ($actions_arr as $k => $r_txt) {
        $sel = '';
        $sv = array();
        $sv = explode(',', $actions_saved_arr[$v['wh_id']]);
        if (!empty($v['wh_id']) && in_array($k, $sv)) {
            $sel = ' selected ';
        }

        echo '<option value="' . $k . '" ' . $sel . ' >' . $r_txt . '</option>';
    }
    echo '</select>';
    echo '</form>';
    echo '</td>';
    echo '<td><form id="frmc_' . $c . '" name="frmc_' . $c . '" class="forms">';
    echo '    <textarea class="form-control" name="remarks_' . $v['wh_id'] . '_' . $v['item_id'] . '_' . $from_date . '"   maxlength="255">'.@$remarks_array[$v['wh_id']].'</textarea>';
    echo '</form></td>';
    echo '<td><div id="btn_' . $c . '" data-c="' . $c . '" class="btn btn-sm green save_cls " > Save </div></td>';

    echo '</tr>';
    //echo '</form>';
    $d++;
}
?>

    </tbody>
</table>

<script>

    $(function () {
        $('.multiselect-ui').multiselect({
        });
        $('.save_cls').click(function (e) {

            var c = $(this).data('c');
            var form1 = $('#frmr_' + c).attr('id');
            var form2 = $('#frma_' + c).attr('id');
            var form3 = $('#frmc_' + c).attr('id');
            var form4 = $('#frmd_' + c).attr('id');

            $(this).attr('disabled', 'disabled');
            $(this).html('Saving ...');
            var th = $(this);
            $.ajax({
                url: "commodity_security_de_action.php",
                data: $("#" + form1 + ",#" + form2 + ",#" + form3 + ",#" + form4).serialize(),
                success: function (result) {
                    //alert(result);
                    $(th).attr('disabled', false);
                    $(th).html('Saved');
                    $(th).removeClass('green').addClass('yellow');
                    $(th).parents('tr').addClass('warning');
                    toastr.success('Saved');
                },
                error: function (result) {
                    $(th).attr('disabled', false);
                    $(th).html('Save');
                    $(th).removeClass('green').addClass('red');
                    toastr.error('Some error while saving');
                }
            });
        });
 


        $('.save_cls_dist').click(function (e) {
            var form1 = $('#frmr_dist_level').attr('id');
            var form2 = $('#frma_dist_level').attr('id');

            $(this).attr('disabled', 'disabled');
            $(this).html('Saving ...');
            var th = $(this);
            $.ajax({
                url: "commodity_security_de_action.php",
                data: $("#" + form1 + ",#" + form2).serialize(),
                success: function (result) {
                    //alert(result);
                    $(th).attr('disabled', false);
                    $(th).html('Saved');
                    $(th).removeClass('green').addClass('yellow');
                    $(th).parents('tr').addClass('warning');
                    toastr.success('Saved');
                },
                error: function (result) {
                    $(th).attr('disabled', false);
                    $(th).html('Save');
                    $(th).removeClass('green').addClass('red');
                    toastr.error('Some error while saving');
                }
            });
        });


        $('#save_all').click(function (e) {

            //var c = $(this).data('c');
            //var form1 = $('#frmr_'+c).attr('id');
            //var form2 = $('#frma_'+c).attr('id');

            $(this).attr('disabled', 'disabled');
            $(this).html('Saving ...');
            var th = $(this);
            $.ajax({
                url: "commodity_security_de_action.php",
                data: $(".forms ").serialize(),
                success: function (result) {
                    //alert(result);
                    $(th).attr('disabled', false);
                    $(th).html('Saved All');
                    $(th).removeClass('green').addClass('yellow');
                    toastr.success('All "Stockout Reasons" and "Advised Actions" have been saved.');
                },
                error: function (result) {
                    $(th).attr('disabled', false);
                    $(th).html('Save All');
                    $(th).removeClass('green').addClass('red');
                    toastr.error('Some error while saving');
                }
            });
        });

    })
</script>
<?php
include PUBLIC_PATH . "/html/footer.php";
?>
<script src="<?= PUBLIC_URL ?>js/bootstrap_multiselect.js"></script>