<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
//echo '<pre>';print_r($_REQUEST);exit;
$growth_rate = 2.1; //population
$consumption_increase_rate = 3;
$non_consumption_increase_rate = 4;
$master_id = $_REQUEST['id'];

$qry = "   SELECT * FROM `fq_forecasting_child` 
           WHERE 
                `master_id`     = $master_id ";
$rsQry = mysql_query($qry) or die();
$already_saved_data = mysql_fetch_assoc($rsQry);

if (!empty($fq_master)) {
    //echo $fq_master;exit;
//    redirect('forecasting_adjustment_edit.php?id=' . $master_id);
//    exit;
}
$qry = " SELECT
            fq_fp_assumptions.pk_id,
            fq_fp_assumptions.text_value
            FROM
            fq_fp_assumptions
            ORDER BY order_by
";
//echo $qry;exit;
$rsQry = mysql_query($qry);
$assumptions = array();
while ($row = mysql_fetch_array($rsQry)) {
    $assumptions[$row['pk_id']] = $row['text_value'];
}

$qry = "SELECT
            distinct fq_master_data.pk_id,
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
        INNER JOIN fq_fp_products_data ON fq_master_data.pk_id = fq_fp_products_data.master_id
        LEFT JOIN fq_fp_products_forecasting ON fq_fp_products_forecasting.fp_product_key = fq_fp_products_data.pk_id
        WHERE fq_master_data.pk_id = " . $master_id . " ";
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
$task_order_full_name = 'Family Planning';
$itm_cat = '1';
$column_name = "Consumption";
$default_dg_col = '1';
 
$source = (!empty($fq_master['source'])) ? $fq_master['source'] : '';
$selYear = (!empty($fq_master['base_year'])) ? $fq_master['base_year'] : '';
if (empty($selYear))
    $selYear = date('Y');
$already_saved = 0;

$all_ofc_levels = array();
$all_ofc_levels['2'] = 'Provincial';
$all_ofc_levels['3'] = 'District';

 
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
fq_docs.pk_id,
fq_docs.fq_master_id,
fq_docs.document_name,
fq_docs.created_at
FROM
fq_docs
WHERE
fq_docs.fq_master_id = $master_id

    ";
$rsQry = mysql_query($qry);
$docs_arr = array();
while ($row = mysql_fetch_array($rsQry)) {
    $docs_arr[$row['pk_id']] = $row['document_name'];
}

    $qry = "SELECT
                    itminfo_tab.itmrec_id,
                    itminfo_tab.itm_id,
                    itminfo_tab.itm_name,
                    itminfo_tab.method_type,
                    fq_fp_products_data.average_amc_of_base_years,
                    fq_fp_products_data.adjustment,
                    fq_fp_products_data.remarks,
                    fq_fp_products_data.pk_id as product_key,
fq_fp_products_forecasting.`year`,
fq_fp_products_forecasting.percent_increase,
fq_fp_products_forecasting.forecasted_val
            FROM
            itminfo_tab
            INNER JOIN fq_fp_products_data ON itminfo_tab.itm_id = fq_fp_products_data.prod_id
LEFT JOIN fq_fp_products_forecasting ON fq_fp_products_forecasting.fp_product_key = fq_fp_products_data.pk_id
            WHERE
            fq_fp_products_data.master_id = $master_id
            ORDER BY
            itminfo_tab.method_rank ASC";
    $rsQry = mysql_query($qry);
    $all_prods = $years_data = array();
    while ($row = mysql_fetch_array($rsQry)) {
        $all_prods[$row['itm_id']]['adjustment'] = $row['adjustment'];
        $all_prods[$row['itm_id']]['remarks'] = $row['remarks'];
        $all_prods[$row['itm_id']]['product_key'] = $row['product_key'];
        $all_prods[$row['itm_id']]['itm_name'] = $row['itm_name'];
        $all_prods[$row['itm_id']]['average_amc_of_base_years'] = $row['average_amc_of_base_years'];
        
        $years_data[$row['year']]['percent_increase'] = $row['percent_increase'];
        $all_prods[$row['itm_id']]['forecasted_val'][$row['year']] = $row['forecasted_val'];
    }
//echo '<pre>';print_r($all_prods);exit;
?>

</head>
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

                            <span class="caption-subject font-green-sharp bold  ">Saved Forecasted Values</span>
                            <span class="caption-helper">Family Planning Products</span>
                            <?php include("back_include.php"); ?>
                        </div>

                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Forecasting Detail</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" >
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-condensed ">
                                                 
                                                <tr>
                                                    <td width="20%">
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Purpose</label>
                                                        </div>
                                                    </td>
                                                    <td colspan=" ">
                                                        <div class="control-group">
                                                            <label class=""><?= $fq_master['purpose'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>    

                                                <tr> 
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
                                                            <label class="caption-subject font-green bold  ">Forecasting Years</label>
                                                        </div>
                                                    </td>
                                                    <td  width=" ">
                                                        <div class="control-group">
                                                            <label>
                                                                <?= $fq_master['start_year'].'/'.substr($fq_master['start_year']+1,2) ?>
                                                                To
                                                                <?= $fq_master['end_year'].'/'.substr($fq_master['end_year']+1,2) ?>
                                                            </label>
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
                                                    </td>
                                                    </tr>
 
                                                <tr>
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
                                <h3 class="heading">Forecasting Calculations   </h3>
                            </div>
                            <div class="widget-body">

                                <form name="frm2" id="frm2" action="forecasting_adjustment_action2.php" method="post" role="form">
                                    <input type="hidden" name="id"      value="<?= (isset($_REQUEST['id'])) ? $_REQUEST['id'] : '' ?>" >
                                    <div class="row">
                                        <div class="col-md-12">

                                            <div class="portlet box">

                                                <div class="portlet-body">
                                                    <div class="">
                                                        <div class="" id="tab11">
                                                            <div class="portlet  ">
                                                                <div class="portlet-body">

                                                                    <table class="table table-condensed table-hover table-bordered">
                                                                        <thead>

                                                                            <tr>
                                                                                <td>Product</td>
                                                                                <td align="center">AMC <br/>(1 month)</td>
                                                                                <td align="center">AMC x 12</td>
                                                                                <td align="center">Expected Increase / Decrease in <br/>Forecasting Adjustment <br/>(%) </td>
                                                                                <?php
                                                                                $c=1;
                                                                                foreach($years_arr as $k=>$year){
                                                                                    echo '<td align="center">'.$year . '-' . substr($year + 1, 2);
                                                                                    //if($c>1) echo '<br/><span class="font-blue">'.((!empty($years_data[$year]['percent_increase'])?number_format($years_data[$year]['percent_increase'],2):'')).'</span>';
                                                                                    echo ' </td>';
                                                                                    $c++;
                                                                                }
                                                                                ?>
                                                                                <td align="center">Details / Assumptions <br> (-)</td>
                                                                            </tr>

                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            foreach($all_prods as $prod_id => $prod_data){
                                                                                $prod_name=$prod_data['itm_name'];
                                                                                $fc_val = $prod_data['forecasted_val'];
                                                                                $amc = $prod_data['average_amc_of_base_years'];
                                                                                $amc12 = $prod_data['average_amc_of_base_years'] * 12 ;
                                                                               
                                                                                
                                                                                echo '<tr>
                                                                                        <td style="background-color:#E4F5E4" rowspan="">'.$prod_name.'</td>
                                                                                        <td align="right">'.number_format($amc).'</td>
                                                                                        <td align="right">'.number_format($amc12).'</td>
                                                                                        <td align="right" class="font-blue">'.((!empty($all_prods[$prod_id]['adjustment'])?number_format($all_prods[$prod_id]['adjustment'],2):'')).'</td>
                                                                                       ';
                                                                               
                                                                                $c=1;
                                                                                foreach($years_arr as $k=>$year){
                                                                                    $fc_val = $prod_data['forecasted_val'][$year];

                                                                                    $this_val = $amc;

                                                                                    if(isset($fc_val)) $this_val = $fc_val;
                                                                                    echo '<td align="right">'.number_format($this_val).'</td>';
                                                                                    $c++;
                                                                                }
                                                                               $opts = explode(',',$all_prods[$prod_id]['remarks']);
                                                                                echo '<td>';
                                                                                foreach($assumptions as $k=>$txt)
                                                                                {
                                                                                    if(in_array($k,$opts))  
                                                                                    echo ''.$txt.',<br/>';
                                                                                }
                                                                                echo '</td>
                                                                                    </tr>';
                                                                            }
                                                                            
                                                                            if(!empty($docs_arr)){
                                                                                $c=1;
                                                                                
                                                                                    echo '<tr>';
                                                                                    echo '<td colspan="99"></td>';
                                                                                    echo '</tr>';
                                                                                    echo '<tr>';
                                                                                    echo '<td colspan="99"> Documents Attached</td>';
                                                                                    echo '</tr>';
                                                                                    
                                                                                foreach ($docs_arr as $k=> $doc_name){
                                                                                            
                                                                                    echo '<tr>';
                                                                                    echo '<td>'.$c++.'.</td>';
                                                                                    echo '<td colspan="99">';
                                                                                        echo (!empty($doc_name)?'<a target="_blank" href="../../user_uploads/fasp_attachments/'.$doc_name.'">'.$doc_name.'</a>':'');
                                                                                    echo '</td>';
                                                                                    echo '</tr>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                             

                                                                        </tbody>

                                                                    </table>

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

        });
        

    </script>
</body>
</html>