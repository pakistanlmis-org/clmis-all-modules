<?php
//echo '<pre>';print_r($_SERVER);exit;
/**
 * forecasting adjustment
 * @package fasp
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//echo '<pre>';print_r($_REQUEST);exit;



$growth_rate = 2.1;//population
$consumption_increase_rate = 3;
$non_consumption_increase_rate = 4;

$master_id = $_REQUEST['id'];



$qry = "   SELECT
fq_forecasting_child.pk_id,
fq_forecasting_child.master_id,
fq_forecasting_child.location_id,
fq_forecasting_child.product_id,
fq_forecasting_child.`year`,
fq_forecasting_child.dg_1,
fq_forecasting_child.dg_2,
fq_forecasting_child.dg_3,
fq_forecasting_child.cons_1,
fq_forecasting_child.cons_2,
fq_forecasting_child.cons_3,
fq_forecasting_child.fc_1,
fq_forecasting_child.fc_2,
fq_forecasting_child.fc_3,
fq_forecasting_child.fc_4,
fq_forecasting_child.final_fc,
fq_forecasting_child.adjustment,
fq_forecasting_child.proposed_fc,
fq_forecasting_child.remarks
FROM
fq_forecasting_child
   where master_id = $master_id ";
$rsQry = mysql_query($qry) or die();
$fq_data=array();
while($row = mysql_fetch_assoc($rsQry)){
    $fq_data[$row['location_id']][$row['product_id']][$row['year']]=$row;
}
//echo '<pre>';print_r($fq_data);exit;


//fetch the forecasting master data
$qry = "SELECT
            fq_master_data.pk_id,
            fq_master_data.base_year,
            fq_master_data.start_year,
            fq_master_data.end_year,
            fq_master_data.purpose,
            fq_master_data.source,
            fq_master_data.stakeholders,
            fq_master_data.province_id,
            fq_master_data.forecasting_methods,
            fq_master_data.item_group,
            tbl_locations.LocName
        FROM
            fq_master_data
        INNER JOIN tbl_locations ON fq_master_data.province_id = tbl_locations.PkLocID
        WHERE fq_master_data.pk_id = " . $_REQUEST['id'] . " ";
//query result
$rsQry = mysql_query($qry) or die();
//fetch result
$c = 1;
$fq_master = mysql_fetch_assoc($rsQry);

$years_arr = array();
$years_count = 0;
for ($i = $fq_master['start_year']; $i <= $fq_master['end_year']; $i++) {
    $years_arr[$i] = $i;
    $years_count++;
}


$office_level = (!empty($fq_master['office_level'])) ? $fq_master['office_level'] : '2';
$task_order = (!empty($fq_master['item_group'])) ? $fq_master['item_group'] : '';
$default_dg_col = 5;
$itm_cat = '';
if ($task_order == 'to3') {
    $task_order_full_name = 'Family Planning';
    $itm_cat = '1';
    $column_name = "Consumption";
    $default_dg_col = '1';
} elseif ($task_order == 'to4') {
    $task_order_full_name = 'MNCH';
    $itm_cat = '5';
    $column_name = "Consumption / Morbidity";
    $default_dg_col = '5';
}



$source = (!empty($fq_master['source'])) ? $fq_master['source'] : '';
$selYear = (!empty($fq_master['base_year'])) ? $fq_master['base_year'] : '';
if (empty($selYear))
    $selYear = date('Y');
$already_saved = 0;

$all_ofc_levels = array();
$all_ofc_levels['2'] = 'Provincial';
$all_ofc_levels['3'] = 'District';


$all_cols = array();
$all_cols['all']['Demographic']['dg_1']['short_name'] = 'A1';
$all_cols['all']['Demographic']['dg_1']['long_name'] = 'Tot. Population';
//$all_cols['all']['Demographic']['dg_2']['short_name']='A2';
//$all_cols['all']['Demographic']['dg_2']['long_name']='Source B';
$all_cols['all']['Consumption']['cons_1']['short_name'] = 'B1';
$all_cols['all']['Consumption']['cons_1']['long_name'] = 'LMIS';
$all_cols['all']['Consumption']['cons_2']['short_name'] = 'B2';
$all_cols['all']['Consumption']['cons_2']['long_name'] = 'Non LMIS';
$all_cols['all']['Forecasting Calculations']['fc_1']['short_name'] = 'C1';
$all_cols['all']['Forecasting Calculations']['fc_1']['long_name'] = 'Forecasting A';
//$all_cols['all']['Forecasting Calculations']['fc_2']['short_name']='C2';
//$all_cols['all']['Forecasting Calculations']['fc_2']['long_name']='Forecasting B';
$all_cols['all']['Forecasting Calculations']['fc_3']['short_name'] = 'C3';
$all_cols['all']['Forecasting Calculations']['fc_3']['long_name'] = 'Forecasting C';
//$all_cols['all']['Forecasting Calculations']['fc_4']['short_name']='C4';
//$all_cols['all']['Forecasting Calculations']['fc_4']['long_name']='Forecasting D';
$all_cols['all']['Forecasting Calculations']['final_fc']['short_name'] = 'C5';
$all_cols['all']['Forecasting Calculations']['final_fc']['long_name'] = 'Final Forecasting';
$all_cols['all']['Forecasting Calculations']['adjustment']['short_name'] = 'C6';
$all_cols['all']['Forecasting Calculations']['adjustment']['long_name'] = 'Adjustment (%)';
$all_cols['all']['Forecasting Calculations']['proposed_fc']['short_name'] = 'C7';
$all_cols['all']['Forecasting Calculations']['proposed_fc']['long_name'] = 'Proposed Forecasting';
$all_cols['all']['Remarks']['remarks']['short_name'] = '-';
$all_cols['all']['Remarks']['remarks']['long_name'] = 'Details / Assumptions';

$qry = " SELECT
            tbl_locations.PkLocID,
            tbl_locations.LocName
            from
            tbl_locations
            WHERE
                tbl_locations.LocLvl = 2 AND
                LocType =2
                AND tbl_locations.PkLocID = " . $fq_master['province_id'] . "
        ";



//echo $qry;exit;
$rsQry = mysql_query($qry);
$prov_arr = array();
$dist_name = '';
while ($row = mysql_fetch_array($rsQry)) {
    $dist_name = $row['LocName'];
    $prov_arr[$row['PkLocID']] = $row['LocName'];
}





//fetch stk list
$qry = "SELECT
                stakeholder.stkid,
                stakeholder.stkname
            FROM
                stakeholder
            WHERE
                stakeholder.is_reporting = 1 AND
                stakeholder.stk_type_id = 0 AND
                stakeholder.ParentID IS NULL
    ";
$rsQry = mysql_query($qry);
$stk_arr = array();
while ($row = mysql_fetch_array($rsQry)) {
    $stk_arr[$row['stkid']] = $row['stkname'];
}
$reference_txt = 'FC-' . (($task_order == 'to3') ? "FP" : "MNCH") . '-' . $all_ofc_levels[$office_level] . '-' . $fq_master['LocName'] . '' . (($office_level == 3) ? '-' . $dist_name : '') . '-' . $fq_master['start_year'] . '-' . $fq_master['end_year'];


$qry = "SELECT
                    itminfo_tab.itmrec_id,
                    itminfo_tab.itm_id,
                    itminfo_tab.itm_name,
                    itminfo_tab.method_type
                FROM
                itminfo_tab
                WHERE
                    itminfo_tab.method_type IS NOT NULL AND
                    itminfo_tab.itm_category = $itm_cat
                ORDER BY
                itminfo_tab.method_rank ASC";
$rsQry = mysql_query($qry);
$all_prods = array();
while ($row = mysql_fetch_array($rsQry)) {
    $all_prods['to3'][$row['itm_id']] = $row['itm_name'];
    $all_prods['to4'][$row['itm_id']] = $row['itm_name'];
}
//echo '<pre>';print_r($all_prods);exit;

$fix_dd_value = '';
if (isset($_REQUEST['id']))
    $fix_dd_value = ' onfocus="this.defaultIndex=this.selectedIndex;" onchange="this.selectedIndex=this.defaultIndex;" ';
?>

<style>
    .nav-pills > li > a:hover,.nav-pills > li.active > a{
        color: #FFFFFF !important;
        background-color: #009C00 !important;
    } 
    .nav-pills > li > a{
        color: #000 !important;
        background-color: #FFFFFF !important;
    }
    .grid_input_number{
        padding: 0px !important;
        font-size: 11px !important;
    }
</style>

</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
    <div class="page-container">
<?php
//include top
include PUBLIC_PATH . "html/top.php";
//include top_im	
include PUBLIC_PATH . "html/top_im.php";
?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-title row-br-b-wp">  
                            <i class="fa fa-lightbulb-o fa-lg font-grey-gallery"></i>

                            <span class="caption-subject font-green-sharp bold  ">Forecasting Calculations</span>
                            <span class="caption-helper">Family Planning Products</span>
                        <?php include("back_include.php"); ?>
                        </div>

                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Forecasting Detail</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm3" id="frm3" action="mnch_forecasting_calculator_action.php" method="post" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-condensed ">
                                                <tr style="display:none;">
                                                    <td width="20%">
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Forecasting Reference</label>
                                                        </div>
                                                    </td>
                                                    <td colspan="3">
                                                        <div class="control-group">
                                                            <label><?= $reference_txt ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Purpose</label>
                                                        </div>
                                                    </td>
                                                    <td colspan="3">
                                                        <div class="control-group">
                                                            <label class=""><?= $fq_master['purpose'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>    

                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Forecast on Base Year</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= $fq_master['base_year'] ?></label>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Product Category</label>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= (($fq_master['item_group'] == 'to3') ? 'Family Planning' : 'MNCH') ?></label>
                                                        </div>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Starting From Year</label>
                                                        </div>
                                                    </td>
                                                    <td  width="30%">
                                                        <div class="control-group">
                                                            <label><?= $fq_master['start_year'] ?></label>
                                                        </div>


                                                    </td>
                                                    <td width="30%">
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">To (End Year)</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= $fq_master['end_year'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>

                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Data Sources</label>

                                                        </div>
                                                    </td>
                                                    <td  colspan="3">
                                                        <div class="control-group">
                                                            <label><?= $fq_master['source'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Stakeholder/s</label>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label>
                                                            <?php
                                                            $st = explode(',', $fq_master['stakeholders']);
                                                            $st2 = array();
                                                            foreach ($st as $k => $stk_id) {
                                                                $st2[] = $stk_arr[$stk_id];
                                                            }
                                                            echo implode(',', $st2);
                                                            ?>
                                                            </label>
                                                        </div>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Province</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= $fq_master['LocName'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table> 

                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>

                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Forecasting Data - Already Saved Forecasting Data</h3>
                            </div>
                            <div class="widget-body">

                                <form name="frm23" id="frm23" action="forecasting_adjustment_action.php" method="post" role="form">
                                    <input type="hidden" name="id"      value="<?= (isset($_REQUEST['id'])) ? $_REQUEST['id'] : '' ?>" >
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="portlet box">

                                                <div class="portlet-body">
                                                    <div class="">
                                                        <div class="" id="tab111">
                                                            <div class="portlet  ">
                                                                <div class="portlet-body">

                                                                    <table class="table table-condensed table-hover table-bordered">
                                                                        <thead>

                                                                            <tr>
                                                                            <?php
                                                                            echo '<td> </td>';
                                                                            echo '<td> </td>';
                                                                            echo '<td> </td>';
                                                                            foreach ($all_cols['all'] as $group_name => $group_data) {
                                                                                $cspan = count($group_data);
                                                                                if ($group_name == 'Consumption')
                                                                                    $group_name = 'Consumption';
                                                                                echo '<td align="center" colspan="' . $cspan . '">' . $group_name . '</td>';
                                                                            }
                                                                            ?>
                                                                            </tr>
                                                                            <tr>
                                                                            <?php
                                                                            $to3_count = 0;
                                                                            $d_count = 1;
                                                                            echo '<td>Location</td>';
                                                                            echo '<td>Product</td>';
                                                                            echo '<td>Year</td>';
                                                                            foreach ($all_cols['all'] as $group_name => $group_data) {
                                                                                foreach ($group_data as $col_id => $col_data) {
                                                                                    if ($col_id == 'cons_1')
                                                                                        $col_data['long_name'] = 'LMIS';
                                                                                    echo '<td align="center" >' . $col_data['long_name'] . ' <br/> ';

                                                                                    echo '(' . $col_data['short_name'] . ')';

                                                                                    if (isset($sources_arr[$d_count]) && $group_name == 'Demographic') {
                                                                                        echo '<br/><b>' . wordwrap($sources_arr[$d_count], 40, "<br />\n") . '</b>';
                                                                                        echo '<br/><span style="color:grey"> Annual Growth Rate:<b>' . $growth_rate. '%</b></span>';

                                                                                        $d_count++;
                                                                                    }
                                                                                    if($col_id == 'cons_1'){
                                                                                        echo '<br/><span style="color:grey"> Annual Inc. Rate:<b>' . $consumption_increase_rate. '%</b></span>';
                                                                                    }
                                                                                    if($col_id == 'cons_2'){
                                                                                        echo '<br/><span style="color:grey"> Annual Inc. Rate:<b>' . $non_consumption_increase_rate. '%</b></span>';
                                                                                    }

                                                                                    echo '</td>';
                                                                                    $to3_count++;
                                                                                }
                                                                            }
                                                                            ?>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                                <?php
                                                                                $old_prov = '';
                                                                                $old_prod = '';
                                                                                $prov_colors = array();
                                                                                $prov_colors[1] = "#E4F5E4";
                                                                                $prov_colors[2] = "#E4ECF5";
                                                                                $prov_colors[3] = "#F5E4F5";
                                                                                $prov_colors[4] = "#F5EDE4";
                                                                                foreach ($prov_arr as $pro_id => $pro_name) {
                                                                                    foreach ($all_prods[$task_order] as $prod_id => $prod_name) {

                                                                                        foreach ($years_arr as $yk => $year) {
                                                                                           
                                                                                            $rspan = 99;
                                                                                            echo '<tr>';
                                                                                            if ($old_prov != $pro_name) {
                                                                                                echo '<td style="vertical-align:top;background-color:' . (isset($prov_colors[$pro_id]) ? $prov_colors[$pro_id] : '') . '" rowspan="' . $rspan . '">' . $pro_name . '</td>';
                                                                                            }
                                                                                            if ($old_prod != $prod_name) {
                                                                                                echo '<td  style="background-color:' . (isset($prov_colors[$pro_id]) ? $prov_colors[$pro_id] : '') . '" rowspan="' . $years_count . '">' . $prod_name . '</td>';
                                                                                            }

                                                                                            echo '<td  style="background-color:' . (isset($prov_colors[$pro_id]) ? $prov_colors[$pro_id] : '') . '">' . $year . '</td>';
                                                                                            //echo '<pre>';print_r($fq_data);exit;
                                                                                            //$dg_1=$cons_1=$cons_2=$fc_1=$fc_3=$final_fc=$adj=$proposed_fc=0;
                                                                                            $dg_1=(!empty($fq_data[$pro_id][$prod_id][$year]['dg_1'])?$fq_data[$pro_id][$prod_id][$year]['dg_1']:'0');
                                                                                            $cons_1=(!empty($fq_data[$pro_id][$prod_id][$year]['cons_1'])?$fq_data[$pro_id][$prod_id][$year]['cons_1']:'0');
                                                                                            $cons_2=(!empty($fq_data[$pro_id][$prod_id][$year]['cons_2'])?$fq_data[$pro_id][$prod_id][$year]['cons_2']:'0');
                                                                                            $fc_1=(!empty($fq_data[$pro_id][$prod_id][$year]['fc_1'])?$fq_data[$pro_id][$prod_id][$year]['fc_1']:'0');
                                                                                            $fc_3=(!empty($fq_data[$pro_id][$prod_id][$year]['fc_3'])?$fq_data[$pro_id][$prod_id][$year]['fc_3']:'0');
                                                                                            $final_fc=(!empty($fq_data[$pro_id][$prod_id][$year]['final_fc'])?$fq_data[$pro_id][$prod_id][$year]['final_fc']:'0');
                                                                                            $adj=(!empty($fq_data[$pro_id][$prod_id][$year]['adjustment'])?$fq_data[$pro_id][$prod_id][$year]['adjustment']:'0');
                                                                                            $proposed_fc=(!empty($fq_data[$pro_id][$prod_id][$year]['proposed_fc'])?$fq_data[$pro_id][$prod_id][$year]['proposed_fc']:'0');
                                                                                            
                                                                                            
                                                                                            
                                                                                            echo '<td align="right">'.number_format($dg_1).'     <input value="' . number_format($dg_1). '"  style="display:none;"  readonly   name="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_dg_1"     id="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_dg_1"      data-prod-id="' . $prod_id . '"  type="text" class="dg_1 grid_input_number form-control right"   ></td>';
                                                                                            echo '<td align="right">'.number_format($cons_1).'<input value="' . number_format($cons_1) . '"  style="display:none;"  readonly   name="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_cons_1"   id="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_cons_1"    data-prod-id="' . $prod_id . '"  type="text" class="cons_1  form-control right"  maxlength="12" ></td>';
                                                                                            echo '<td align="right">'.number_format($cons_2).'<input value="' . number_format($cons_2) . '"  style="display:none;"  readonly   name="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_cons_2"   id="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_cons_2"    data-prod-id="' . $prod_id . '"  type="text" class="cons_2  form-control right"  maxlength="12" ></td>';
                                                                                            echo '<td align="right">'.number_format($fc_1,2).'<input value="' . number_format($fc_1,2) . '"  style="display:none;"    readonly   name="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_fc_1"     id="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_fc_1"      data-prod-id="' . $prod_id . '"  type="text" class="fc_1 grid_input_number form-control right"  maxlength="12" ></td>';
                                                                                            echo '<td align="right">'.number_format($fc_3,2).'<input value="' . number_format($fc_3,2) . '"  style="display:none;"    readonly   name="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_fc_3"     id="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_fc_3"      data-prod-id="' . $prod_id . '"  type="text" class="fc_3 grid_input_number form-control right"  maxlength="12" ></td>';
                                                                                            echo '<td align="right">'.number_format($final_fc,2).'<input value="' . number_format($final_fc,2) . '"   style="display:none;"       readonly   name="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_final_fc"           id="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_final_fc"        data-prod-id="' . $prod_id . '" data-year="'.$year.'"  type="text" class="final_fc grid_input_number form-control right"  maxlength="12" ></td>';
                                                                                            echo '<td ><input value="' . number_format($adj,2) . '"        name="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_adjustment"         id="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_adjustment"      data-prod-id="' . $prod_id . '" data-year="'.$year.'"  type="text" class="adjustment_column grid_input_number form-control right"  maxlength="12" ></td>';
                                                                                            echo '<td ><input value="' . number_format($proposed_fc,2) . '"     readonly   name="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_proposed_fc"        id="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_proposed_fc"     data-prod-id="' . $prod_id . '"  data-year="'.$year.'"   data-year="'.$year.'"  type="text" class="proposed_fc grid_input_number form-control right"  maxlength="12" ></td>';
                                                                                            
                                                                                            echo '<td ><textarea  name="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_remarks"  id="input_' . $task_order . '_' . $pro_id . '_' . $prod_id . '_' . $year.'_remarks"  type="text" class="form-control"  maxlength="250" >'.(!empty($fq_data[$prod_id][$prod_id][$year]['remarks'])?$fq_data[$prod_id][$prod_id][$year]['remarks']:'').'</textarea></td>';
                                                                                            
                                                                                            echo '</tr>';
                                                                                            $old_prov = $pro_name;
                                                                                            $old_prod = $prod_name;
                                                                                        }
                                                                                    }
                                                                                }
                                                                                ?>
                                                                        </tbody>

                                                                    </table>



                                                                </div>
                                                            </div>
                                                            <div class="row col-md-12">

                                                                <div class="col-md-8">

                                                                </div>
                                                                <div class="col-md-4  ">
                                                                    <div class="  right">
                                                                        <a href="" onclick="return confirm('Are you sure to discard unsaved changes, and proceed to new data entry?')"  class="btn btn-primary btn-red" >Cancel and exit</a>
                                                                        <input class="btn btn-red" type="reset" value="Reset">
                                                                        <input class="btn btn-green green" type="submit" value="Save">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row col-md-12">
                                                                <div class=" ">

                                                                    <h4><b>Forecasting Formulas</b></h4>
                                                                            <?php
                                                                            if (!empty($all_cols['all']['Forecasting Calculations']['fc_1']))
                                                                                echo '<pre>Forecasting A (C1)            = A1 / Average Consumption LMIS</pre>';
                                                                            if (!empty($all_cols['all']['Forecasting Calculations']['fc_2']))
                                                                                echo '<pre>Forecasting B (C2)            = A2 / Average Consumption LMIS</pre>';
                                                                            if (!empty($all_cols['all']['Forecasting Calculations']['fc_3']))
                                                                                echo '<pre>Forecasting C (C3)            = A1 / Average Consumption Non-LMIS</pre>';
                                                                            if (!empty($all_cols['all']['Forecasting Calculations']['fc_4']))
                                                                                echo '<pre>Forecasting D (C4)            = A2 / Average Consumption Non-LMIS</pre>';
                                                                            ?>



                                                                    <pre>Final Forecasting (C5)        = (C1 + C3) / <?= (count($all_cols['all']['Forecasting Calculations']) - 3) ?></pre>
                                                                    <pre>Proposed Forecasting (C7)     = C5 + (C5 * (C6/100) )</pre>
                                                                </div>
                                                            </div>

                                                        </div>


                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                </form>



                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
?>
    <script>
        $(document).ready(function () {

            $("#frm2").submit(function () {
                var c = confirm("Save all changes ?");
                return c;
            });
            $(".grid_input_number").keyup(function (e) {
                if ($.isNumeric($(this).val())) {
                    $(this).attr('style', "border-radius: 1px; border:#00000 1px solid;");
                } else
                {
                    $(this).attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
            });

            $(".adjustment_column").bind("keyup blur change", function (event) {
                $this = $(this);
                
                calculate_proposed_fc($this);
            });
            $(".cons_1").bind("keyup blur change", function (event) {
                $this = $(this);
                calculate_forecasting_cols($this);
                calculate_proposed_fc($this);
            });

            function calculate_forecasting_cols($this) {
                var prod_id = $this.data('prod-id');
                
                var dg_1 = $('.dg_1[data-prod-id=' + prod_id + ']').val().replace(/,/g, '');
                
                var dg_2 = $('.dg_2[data-prod-id=' + prod_id + ']').val().replace(/,/g, '');
                var cons_1 = $('.cons_1[data-prod-id=' + prod_id + ']').val().replace(/,/g, '');
                var cons_2 = $('.cons_2[data-prod-id=' + prod_id + ']').val().replace(/,/g, '');
                var adjustment = $('.adjustment_column[data-prod-id=' + prod_id + ']').val().replace(/,/g, '');

                if (isNaN(cons_1) || cons_1 == '' || cons_1 == '0') {
                    var fc_1 = 0;
                    var fc_2 = 0;
                } else {
                    var fc_1 = (parseFloat(dg_1) / parseFloat(cons_1)).toFixed(2);
                    var fc_2 = (parseFloat(dg_2) / parseFloat(cons_1)).toFixed(2);
                }

                if (isNaN(cons_2) || cons_2 == '' || cons_2 == '0') {
                    var fc_3 = 0;
                    var fc_4 = 0;

                } else {
                    var fc_3 = (parseFloat(dg_1) / parseFloat(cons_2)).toFixed(2);
                    var fc_4 = (parseFloat(dg_2) / parseFloat(cons_2)).toFixed(2);

                }
                var final_fc = ((parseFloat(fc_1) + parseFloat(fc_2) + parseFloat(fc_3) + parseFloat(fc_4)) / <?= (count($all_cols['all']['Forecasting Calculations']) - 3) ?>).toFixed(2);
                var proposed_fc = final_fc + (final_fc * (adjustment / 100)).toFixed(2);

                if (isNaN(proposed_fc))
                    proposed_fc = 0;
                if (isNaN(fc_1))
                    fc_1 = 0;
                if (isNaN(fc_2))
                    fc_2 = 0;
                if (isNaN(fc_3))
                    fc_3 = 0;
                if (isNaN(fc_4))
                    fc_4 = 0;

                //console.log('DG>>>>'+dg_1+','+dg_2+','+cons_1+','+cons_2);
                //console.log('FC>>>>'+fc_1+','+fc_2+','+fc_3+','+fc_4);
                $('.fc_1[data-prod-id=' + prod_id + ']').val(fc_1);
                $('.fc_2[data-prod-id=' + prod_id + ']').val(fc_2);
                $('.fc_3[data-prod-id=' + prod_id + ']').val(fc_3);
                $('.fc_4[data-prod-id=' + prod_id + ']').val(fc_4);

                $('.final_fc[data-prod-id=' + prod_id + ']').val(final_fc);
                $('.proposed_fc[data-prod-id=' + prod_id + ']').val(proposed_fc);

                console.log('Calculating FC Cols:' + prod_id + ',' + cons_1 + ',' + final_fc + ',' + proposed_fc);
            }

            function calculate_proposed_fc() {
                var prod_id = $this.data('prod-id');
                var year = $this.data('year');
                var adjustment = $('.adjustment_column[data-prod-id=' + prod_id + '][data-year=' + year + ']').val().replace(/,/g, '');
                var final_fc = $('.final_fc[data-prod-id=' + prod_id + '][data-year=' + year + ']').val().replace(/,/g, '');
                var adj = parseFloat(adjustment)
                var fin = parseFloat(final_fc);
                if (isNaN(adj))
                    adj = 0;
                var total = fin + (fin * (adj / 100));

                if (isNaN(total))
                    total = 0;
                total = total.toFixed(2);
                $('.proposed_fc[data-prod-id=' + prod_id + '][data-year=' + year + ']').val(total);
                console.log('calculate_proposed_fc > Prod ' + prod_id + ',year:' + year + ',adj:' + adj + ',final:' + final_fc + ',proposed:' + total);
            }

        });
        $(function () {
            $('#office_level').change(function () {
                officeType($(this).val());
            });
            $('#province').change(function () {
                var provId = $(this).val();
                showDistricts(provId);
            });
            $('#task_order').change(function () {
                var toid = $(this).val();
                show_demographic_cols(toid);
            });
            $('#demographic_data').change(function () {

                var col_id = $(this).val();

<?php
if (isset($_REQUEST['id'])) {
    ?>
                    if (confirm("New demographic values will be loaded, All unsaved data will be lost.Press OK to continue."))
                    {
                        $("#id").click();
                    }
    <?php
} else {
    echo '$("#submit_btn").click();';
}
?>
            });


            // Submit Form
            $('#submit').click(function (e) {
                $('body').addClass("loading");
            });
        });
        function officeType(officeLevel)
        {
            if (parseInt(officeLevel) == 2)
            {
                $('#province_div').show();
                $('#district_div').hide();
            } else if (parseInt(officeLevel) == 3)
            {
                $('#province_div').show();
                $('#province').val('');
                $('#district_div').show();
            }
        }
        function showDistricts(provId)
        {
            var officeLevel = $('#office_level').val();
            if (officeLevel == 3)
            {
                $('#district_div').show();
                $('#district').html('<option value="">Loading...</option>');
                $.ajax({
                    type: "POST",
                    url: "load_dist.php",
                    data: {provinceId: provId, validate: 'yes'},
                    dataType: 'html',
                    success: function (data)
                    {
                        $('#district_data').html(data);
                    }
                });
            }
        }
        function show_demographic_cols(toid)
        {

            $('#demographic_data_div').show();
            $('#demographic_data').html('<option value="">Loading...</option>');
            $.ajax({
                type: "POST",
                url: "load_dg_cols.php",
                data: {task_order: toid},
                dataType: 'html',
                success: function (data)
                {
                    $('#demographic_data').html(data);
                }
            });

        }


        function showProvinces(pid) {
            var stk = $('#stk_sel').val();
            if (typeof stk !== 'undefined')
            {
                $.ajax({
                    url: 'ajax_stk.php',
                    type: 'POST',
                    data: {stakeholder: stk, provinceId: pid, showProvinces: 1, showAllOpt: 0},
                    success: function (data) {
                        $('#province').html(data);
                    }
                })
            }
        }
    </script>
<?php
if (!empty($_SESSION['err']['msg'])) {
    ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: '<?= $_SESSION['err']['msg'] ?>',
                type: '<?= $_SESSION['err']['type'] ?>',
                layout: self.data('layout')
            });

        </script>
    <?php
    $_SESSION['err']['msg'] = '';
    $_SESSION['err']['type'] = '';
}
?>
</body>
</html>