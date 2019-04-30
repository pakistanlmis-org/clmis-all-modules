<?php
/**
 * shipment
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
$prod_id = (!empty($_REQUEST['prod_id'])?$_REQUEST['prod_id']:'');
$indicator = (!empty($_REQUEST['indicator'])?$_REQUEST['indicator']:'');
$from_date = date("Y-m-d", strtotime($f_date));

$stk = (!empty($_REQUEST['stk'])?$_REQUEST['stk']:'all');
$stk_name = (!empty($_REQUEST['stk_name'])?$_REQUEST['stk_name']:'All');

$where_clause="";
if(!empty($stk) && $stk != 'all')
{
    $where_clause .= " AND tbl_warehouse.stkid in ($stk) ";
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
    $qry .= " AND tbl_warehouse.stkid in ($stk) ";
    
    
    
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
                stakeholder.stkname,
                tbl_locations.LocName as district_name

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
        ORDER BY
                tbl_warehouse.prov_id,
                tbl_locations.LocName,
                tbl_warehouse.stkid,
                tbl_hf_data.item_id,
                tbl_warehouse.wh_name

";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$c=1;
$unk_arr = $so_arr2 = $so_arr = $us_arr = $sat_arr = $os_arr = array();

while($row = mysql_fetch_assoc($qryRes))
{
    $itm_arr[$row['item_id']] = $row['itm_name'];

    if( $row['closing_balance']==NULL )
    {
        $unk_arr[] = $row;
    }
    elseif( $row['closing_balance'] <= '0')
    {
        $so_arr[] = $row;
    }
    /*elseif( $row['mos'] > 0 && $row['mos'] < 1 )
    {
        $us_arr[] = $row;
    }
    elseif( $row['mos'] >= 1 && $row['mos'] < 3 )
    {
        $sat_arr[] = $row;
    }
    elseif( $row['mos'] >= 3 )
    {
        $os_arr[] = $row;
    }*/

    if($indicator == 'UNK') 
    {
        $ind_name= 'Un-Known MOS ';
        $display_arr =  $unk_arr;
    }
    elseif($indicator == 'SO') 
    {
        $ind_name= 'Stock Out';
        $display_arr =  $so_arr;
    }
    elseif($indicator == 'US') 
    {
        $ind_name= 'Under Stock';
        $display_arr =  $us_arr;
    }
    elseif($indicator == 'SAT') 
    {
        $ind_name= 'Satisfactory Stock';
        $display_arr =  $sat_arr;
    }
    elseif($indicator == 'OS') 
    {
        $ind_name= 'Over Stock';
        $display_arr =  $os_arr;
    }
}    
//echo '<pre>';print_r($so_arr);echo'display_arr';print_r($display_arr);exit;    

?>


    <div class="row">
        <div class="col-md-10">
            <h4 class="center">
                <div class="label label-success" style="font-size: 20px">
                    List of '<?=$ind_name?>' facilities - <?=$_REQUEST['prov_name'].'-'.$_REQUEST['prod_name']?>
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
        <th>Reason of stock out</th>
        <th>Action advised</th>
        <th> </th>
    </tr>
    </thead>
    
    <tbody>
   
<?php

$c=1;
foreach($display_arr as $k => $v)
{
    if($v['mos'] == NULL){
        $mos = 'UNK';
    }
    else
         $mos = number_format($v['mos'],2);
    
    echo '<tr  id="row_'.$c.'">';
    echo '<td>'.$c.'</td>';
    echo '<td>'.$v['district_name'].'</td>';
    echo '<td>'.$v['stkname'].'</td>';
    echo '<td>'.$v['wh_name'].'</td>';
    echo '<td>'.$v['itm_name'].'</td>';
    echo '<td align="right">'.number_format($v['closing_balance']).'</td>';
    echo '<td align="right">'.number_format($v['avg_consumption']).'</td>';
    echo '<td align="right">'.$mos.'</td>';
    
    
    echo '<td align="right">';
    echo '<form id="frmr_'.$c.'" name="frmr_'.$c.'" class="forms">';
    echo '<input type="hidden" name = "wh_id"     value="'.$v['wh_id'].'">';
    echo '<input type="hidden" name = "item_id"   value="'.$v['item_id'].'">';
    echo '<input type="hidden" name = "month"     value="'.$from_date.'">';
    echo '<select class="multiselect-ui form-control" name="comments_'.$v['wh_id'].'_'.$v['item_id'].'_'.$from_date.'[]" multiple>';
        //echo '<option value="">Select</option>';
        foreach($comments_arr as $k => $r_txt){
            $sel = '';
            $sv = array ();
            $sv = explode(',', $reasons_saved_arr[$v['wh_id']]);
            if(!empty($v['wh_id']) && in_array($k, $sv)){
                $sel = ' selected ';
            }
            elseif($mos == 'UNK' && $k == '130' && empty($reasons_saved_arr[$v['wh_id']])){
                $sel = ' selected ';
            }
            echo '<option value="'.$k.'" '.$sel.' >'.$r_txt.'</option>';
        }
    echo '</select>';
    echo '</form>';
    echo '</td>';
    
    echo '<td align="right">';
    echo '<form id="frma_'.$c.'" name="frma_'.$c.'" class="forms">';
    echo '<select class="multiselect-ui form-control" name="actions_'.$v['wh_id'].'_'.$v['item_id'].'_'.$from_date.'[]" multiple>';
        //echo '<option value="">Select</option>';
        foreach($actions_arr as $k => $r_txt){
            $sel = '';
            $sv = array ();
            $sv = explode(',', $actions_saved_arr[$v['wh_id']]);
            if(!empty($v['wh_id']) && in_array($k, $sv)){
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
}
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