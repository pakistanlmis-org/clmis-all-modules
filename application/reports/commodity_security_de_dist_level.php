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
//include Configuration
include("../includes/classes/Configuration.inc.php");
//login
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include  functions
include APP_PATH . "includes/classes/functions.php";



$f_date= (!empty($_REQUEST['from_date'])?$_REQUEST['from_date']:date("Y-m").'-01');
$province = (!empty($_REQUEST['province'])?$_REQUEST['province']:'all');
$province_name = (!empty($_REQUEST['prov_name'])?$_REQUEST['prov_name']:'all');
$prod_id = (!empty($_REQUEST['prod_id'])?$_REQUEST['prod_id']:'');
 
$indicator = (!empty($_REQUEST['indicator'])?$_REQUEST['indicator']:'');
$from_date = date("Y-m-d", strtotime($f_date));

$stk = (!empty($_REQUEST['stk'])?$_REQUEST['stk']:'all');
$stk_name = (!empty($_REQUEST['stk_name'])?$_REQUEST['stk_name']:'All');

$where_clause="";
if(!empty($stk) && $stk != 'all')
{
    $where_clause .= " AND tbl_warehouse.stkid=$stk ";
}
if(!empty($province) && $province != 'all')
{
    $where_clause .= " AND tbl_warehouse.prov_id=$province ";
}
//echo '<pre>';print_r($_REQUEST);exit;    

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
            list_detail.list_master_id in (22,23)
        ORDER BY
            list_detail.rank ,
            list_detail.list_value ASC
    ";
$qryRes = mysql_query($qry);
$comments_arr = $actions_arr = array();
while($row = mysql_fetch_assoc($qryRes))
{
    if($row['list_master_id'] == '22')
        $comments_arr[$row['pk_id']]=$row['list_value'];
    if($row['list_master_id'] == '23')
        $actions_arr[$row['pk_id']]=$row['list_value'];
}
//print_r($comments_arr);


$qry = "SELECT
        stock_out_reasons.wh_id,
        stock_out_reasons.itm_id,
        stock_out_reasons.`month`,
        stock_out_reasons.reason,
        action_suggested
        FROM
        stock_out_reasons
        INNER JOIN tbl_warehouse ON stock_out_reasons.wh_id = tbl_warehouse.wh_id
        WHERE
        stock_out_reasons.`month` = '".$from_date."' ";
$qry .= "AND stock_out_reasons.itm_id = $prod_id ";
if(!empty($province) && $province != 'all')
    $qry .= " AND tbl_warehouse.prov_id = $province ";
if(!empty($stk) && $stk != 'all')
    $qry .= " AND tbl_warehouse.stkid = $stk ";
    
    
    
$qryRes = mysql_query($qry);
$reasons_saved_arr = $actions_saved_arr = array();
while($row = mysql_fetch_assoc($qryRes))
{
    $reasons_saved_arr[$row['wh_id']]=$row['reason'];
    $actions_saved_arr[$row['wh_id']]=$row['action_suggested'];
}
//echo '<pre>';print_r($reasons_saved_arr);print_r($actions_saved_arr);exit;

//Query for shipment main dashboard
$qry = "SELECT
                tbl_warehouse.dist_id,
                stakeholder.stkname,
                tbl_locations.LocName AS district_name,
                itminfo_tab.itm_name,
                tbl_hf_data.item_id,
                tbl_warehouse.stkid,
                count(*) as stock_out_count
 
            FROM
                    tbl_warehouse
            INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
            
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
                            warehouse_status_history.reporting_month = '".$from_date."'
                    AND warehouse_status_history.`status` = 0

            )
            AND tbl_hf_data.reporting_date = '".$from_date."'
            AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)

            AND itminfo_tab.itm_category = 1
            /*AND itminfo_tab.itm_id NOT IN(4,6,10,33)*/
            AND itminfo_tab.itm_id = $prod_id
            $where_clause
            AND tbl_hf_data.closing_balance <= 0
            
        GROUP BY 
                tbl_warehouse.dist_id,
                stakeholder.stkname,
                itminfo_tab.itm_id
        ORDER BY
                tbl_warehouse.prov_id,
                tbl_locations.LocName,
                tbl_hf_data.item_id,
                tbl_warehouse.wh_name

";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$c=1;
$disp_arr = $dist_arr =$itm_arr =$stk_arr =   array();

while($row = mysql_fetch_assoc($qryRes))
{
    $stk_arr[$row['stkid']]         = $row['stkname'];
    $itm_arr[$row['item_id']]       = $row['itm_name'];
    $dist_arr[$row['dist_id']]      = $row['district_name'];
    $disp_arr[$row['dist_id']][$row['stkid']][$row['item_id']] = $row['stock_out_count'];
}    
//echo '<pre>';print_r($disp_arr); exit;    

?>


    <div class="row">
        <div class="col-md-10">
            <h4 class="center">
                <div class="label label-success" style="font-size: 20px">
                    District Wise Stock Out Count - <?=$_REQUEST['prov_name'].'-'.$_REQUEST['prod_name']?>
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
        <th>Product</th>
        <th>Stock Outs</th>
        <th>Reason of stock out</th>
        <th>Action advised</th>
        <th> </th>
    </tr>
    </thead>
    
    <tbody>
   
<?php

$c=1;
foreach($disp_arr as $dist_id => $dist_data)
{ 
    foreach($dist_data as $stk_id => $stk_data)
    { 
    
    foreach($stk_data as $itm_id => $count)
    { 
    $onclick_func='  onclick="showDrillDown_sdp_level(\''.$dist_id.'\',\''.$dist_arr[$dist_id].'\',\''.$from_date.'\','.$itm_id.',\''.$itm_arr[$itm_id].'\',\'SO\',\''.$stk_id.'\',\''.$stk_name.'\')" ';

    echo '<tr  id="row_'.$c.'">';
    echo '<td>'.$c.'</td>';
    echo '<td>'.$dist_arr[$dist_id].'</td>';
    echo '<td>'.$stk_arr[$stk_id].'</td>';
    echo '<td>'.$itm_arr[$itm_id].'</td>';
    echo '<td align="right" '.$onclick_func.'>'.number_format($count).'</td>';
    
    echo '<td align="right">';
    echo '<form id="frmr_'.$c.'" name="frmr_'.$c.'" class="forms">';
    echo '<input type="hidden" name = "wh_id"     value="'.$dist_id.'">';
    echo '<input type="hidden" name = "item_id"   value="'.$itm_id.'">';
    echo '<input type="hidden" name = "month"     value="'.$from_date.'">';
    echo '<select class="multiselect-ui form-control" name="commentsDist_'.$dist_id.'_'.$itm_id.'_'.$from_date.'[]" multiple>';
        //echo '<option value="">Select</option>';
        foreach($comments_arr as $k => $r_txt){
            $sel = '';
            $sv = array ();
            $sv = explode(',', $reasons_saved_arr[$dist_id]);
            if(!empty($dist_id) && in_array($k, $sv)){
                $sel = ' selected ';
            }
            elseif($mos == 'UNK' && $k == '130' && empty($reasons_saved_arr[$dist_id])){
                $sel = ' selected ';
            }
            echo '<option value="'.$k.'" '.$sel.' >'.$r_txt.'</option>';
        }
    echo '</select>';
    echo '</form>';
    echo '</td>';
    
    echo '<td align="right">';
    echo '<form id="frma_'.$c.'" name="frma_'.$c.'" class="forms">';
    echo '<select class="multiselect-ui form-control" name="actionsDist_'.$dist_id.'_'.$itm_id.'_'.$from_date.'[]" multiple>';
        //echo '<option value="">Select</option>';
        foreach($actions_arr as $k => $r_txt){
            $sel = '';
            $sv = array ();
            $sv = explode(',', $actions_saved_arr[$dist_id]);
            if(!empty($dist_id) && in_array($k, $sv)){
                $sel = ' selected ';
            }
            
            echo '<option value="'.$k.'" '.$sel.' >'.$r_txt.'</option>';
        }
    echo '</select>';
    echo '</form>';
    echo '</td>';
    
    echo '<td><div id="btn_'.$c.'" data-c="'.$c.'" class="btn btn-sm green save_cls" > Save </div></td>';
    
    
    echo '</tr>';
    //echo '</form>';
    $c++;
}}}
?>
       
    </tbody>
</table>
 
<script>

$(function() {
    $('.multiselect-ui').multiselect({
      });
        $('.save_cls').click(function(e) {
            
            var c = $(this).data('c');
            var form1 = $('#frmr_'+c).attr('id');
            var form2 = $('#frma_'+c).attr('id');
            
            $(this).attr('disabled','disabled');
            $(this).html('Saving ...');
            var th  = $(this);
            $.ajax({
                url: "dev_results_sdp_drilldown_action.php", 
                data : $("#"+form1+",#"+form2).serialize(),
                success: function(result){
                    //alert(result);
                    $(th).attr('disabled',false);
                    $(th).html('Saved');
                    $(th).removeClass('green').addClass('yellow');
                    toastr.success('Saved');
                },
                error: function(result){
                    $(th).attr('disabled',false);
                    $(th).html('Save');
                    $(th).removeClass('green').addClass('red');
                    toastr.error('Some error while saving');
                }
            });
        });
        
        
        
        $('#save_all').click(function(e) {
            
            //var c = $(this).data('c');
            //var form1 = $('#frmr_'+c).attr('id');
            //var form2 = $('#frma_'+c).attr('id');
            
            $(this).attr('disabled','disabled');
            $(this).html('Saving ...');
            var th  = $(this);
            $.ajax({
                url: "dev_results_sdp_drilldown_action.php", 
                data : $(".forms ").serialize(),
                success: function(result){
                    //alert(result);
                    $(th).attr('disabled',false);
                    $(th).html('Saved All');
                    $(th).removeClass('green').addClass('yellow');
                    toastr.success('All "Stockout Reasons" and "Advised Actions" have been saved.');
                },
                error: function(result){
                    $(th).attr('disabled',false);
                    $(th).html('Save All');
                    $(th).removeClass('green').addClass('red');
                    toastr.error('Some error while saving');
                }
            });
        });
        
})
</script>