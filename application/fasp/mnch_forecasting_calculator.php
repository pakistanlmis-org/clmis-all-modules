<?php
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
/*
  $str = 'before-str-after';
  preg_match('/before-(.*?)-after/', $str, $match);
  echo $match[1];
 *  */

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
$fq_master = mysql_fetch_array($rsQry);

$years_arr = array();
for ($i = $fq_master['start_year']; $i <= $fq_master['end_year']; $i++) {
    $years_arr[$i] = $i;
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

//fetch demographics
//fetch the forecasting master data
$qry = "SELECT
                fq_demographics_master.pk_id,
                fq_demographics_master.date_of_data_entry,
                fq_demographics_master.`year`,
                fq_demographics_master.source,
                fq_demographics_child.location_id,
                fq_demographics_child.col_id,
                fq_demographics_child.`value`
            FROM
                fq_demographics_master
            INNER JOIN fq_demographics_child ON fq_demographics_child.master_id = fq_demographics_master.pk_id
            WHERE
                fq_demographics_master.`year` = " . $fq_master['base_year'] . " AND
                fq_demographics_child.location_id = " . $fq_master['province_id'] . "
";
$rsQry = mysql_query($qry);
$demographics_arr = $sources_arr = array();
while ($row = mysql_fetch_array($rsQry)) {
    $sources_arr[$row['source']] = $row['source'];
    $demographics_arr[$row['col_id']] = $row['value'];
}
$total_population_of_base_year = $demographics_arr[5];
if(empty($total_population_of_base_year))$total_population_of_base_year=0;
//echo '<pre>';print_r($demographics_arr);print_r($sources_arr);exit;
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
                             
                            <span class="caption-subject font-green-sharp bold  ">Forecasting Calculator</span>
                            <span class="caption-helper">MNCH</span>
                            <?php include("back_include.php"); ?>
                        </div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Forecasting Detail</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="mnch_forecasting_calculator_action.php" method="post" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-condensed ">
                                                <tr style="display:none;">
                                                    <td width="20%">
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Entry No.</label>
                                                        </div>
                                                    </td>
                                                    <td colspan="3">
                                                        <div class="control-group">
                                                            <label><?= $fq_master['pk_id'] ?></label>
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
                                                                    $st = explode(',',$fq_master['stakeholders']);
                                                                    $st2=array();
                                                                    foreach($st as $k => $stk_id){
                                                                        $st2[]=$stk_arr[$stk_id];
                                                                    }
                                                                    echo implode(',',$st2);
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
                                <h3 class="heading">Forecasting Calculator</h3>
                            </div>
                            <div class="widget-body">
                                <table class="table table-bordered table-condensed">
                                    <tbody>
                                        <tr>
                                            <td width="3%">1</td>
                                            <td width="25%">Disease/Clinical Condition</td>
                                            <td>
                                                <select name="clinical_condition" id="clinical_condition" class="form-control input-sm" required>
                                                    <option value="">Select</option>
                                                    <?php
                                                    $qry = "SELECT
                                                                fq_clinical_conditions.pk_id,
                                                                fq_clinical_conditions.short_name,
                                                                fq_clinical_conditions.full_name,
                                                                fq_clinical_conditions.description,
                                                                fq_clinical_conditions.is_active,
                                                                fq_clinical_conditions.rank
                                                            FROM
                                                                fq_clinical_conditions
                                                            ";
                                                    $rsQry = mysql_query($qry);
                                                    while ($row = mysql_fetch_array($rsQry)) {
                                                        echo '<option value="' . $row['pk_id'] . '">' . $row['short_name'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr class="hide_rows" style=" display: none;">
                                            <td>2</td>
                                            <td>Medicine Name</td>
                                            <td id="medicines_td">
                                                <select name="medicines[]" id="medicines" class="form-control input-sm" required>
                                                    <option value="">Select</option>
                                                </select>
                                            </td>
                                        </tr>


                                        <tr class="hide_rows" style=" display: none;">
                                            <td>3</td>
                                            <td>Total Population of Base Year</td>
                                            <td> <h4><?= number_format($total_population_of_base_year) ?></h4></td>
                                        </tr>

                                        <tr class="hide_rows" style=" display: none;">
                                            <td>4</td>
                                            <td>Indicator</td>
                                            <td>
                                                <select name="indicator" id="indicator" class="form-control input-sm" required>
                                                    <option value="1">Incidence</option>
                                                    <option value="2">Prevalence</option>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr class="hide_rows" style=" display: none;">
                                            <td>5</td>
                                            <td>Age Group</td>
                                            <td>
                                                <select name="age_group" id="age_group" class="form-control input-sm" required>
                                                    <option value="">Select</option>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr class="hide_rows" style=" display: none;">
                                            <td colspan="4"><a class="btn btn-large green pull-right" id="forecast_go_btn">Show Forecasting Calculations</a></td>

                                        </tr>
                                    </tbody>
                                </table>

                                <div class="row " id="disease_lbl">
                                    <div class=" col-md-12">
                                        <span class="label label-lg label-danger">Please select one of the Clinical Conditions, to perform Forecasting Calculations.</span>
                                    </div>
                                </div>

                                <div id="calculator_div" style="display: none;"></div>
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

            $("#forecast_go_btn").click(function (event) {
                $('#calculator_div').slideDown();
                $('#calculator_div').hide();
                var age_group = $('#age_group').val();
                var cc = $('#clinical_condition').val();
                $.ajax({
                    type: "POST",
                    url: "mnch_forecasting_calculator_ajax.php",
                    data: {get_calculator: 'yes', clinical_condition: cc, age_group: age_group, start_year:<?= $fq_master['start_year'] ?>, end_year:<?= $fq_master['end_year'] ?>, base_year:<?= $fq_master['base_year'] ?>, total_population:<?= $total_population_of_base_year ?>},
                    dataType: 'html',
                    success: function (data)
                    {
                        $('#calculator_div').html(data);
                        $('#calculator_div').slideDown();
                         $('html, body').delay(300).animate({ scrollTop: $('#calculator_div').offset().top }, 'slow');
                    }
                });
                //$('#calculator_div').slideDown(2000);
                
               
                
            });
            $("#clinical_condition").bind("change", function (event) {
                $('#disease_lbl').hide();
                $('#calculator_div').html('').hide();
                $(".hide_rows").hide();
                var id = $(this).val();
                if(id == 'undefined' || id == '')
                {
                    return;
                }
                /*$.ajax({
                 type: "POST",
                 url: "load_ajax.php",
                 data: {get_medicines:'yes', clinical_condition: id},
                 dataType: 'html',
                 success: function(data)
                 {
                 $('#medicines').html(data);
                 }
                 });*/

                
                $.ajax({
                    type: "POST",
                    url: "load_ajax.php",
                    data: {get_medicines_names: 'yes', clinical_condition: id},
                    dataType: 'html',
                    success: function (data)
                    {
                        $('#medicines_td').html(data);
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "load_ajax.php",
                    data: {get_age_groups: 'yes', clinical_condition: id},
                    dataType: 'html',
                    success: function (data)
                    {
                        $('#age_group').html(data);
                        $(".hide_rows").first().show("fast", function showNext() {
                            $(this).next(".hide_rows").show("fast", showNext);
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>