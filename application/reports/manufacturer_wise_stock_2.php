<?php
include("../includes/classes/AllClasses.php");
include(APP_PATH . "includes/report/FunctionLib.php");
include(PUBLIC_PATH . "html/header.php");
if (date('d') > 10) {
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
$selMonth = date('m', strtotime($date));
$selYear = date('Y', strtotime($date));
$date_from = $date_to = $product = $provinceID = $funding_source = $warehouse = $xmlstore = $selProv = '';
if (isset($_REQUEST['search'])) {
    $date_from = $_REQUEST['date_from'];
    $date_to = $_REQUEST['date_to'];
    $product = $_REQUEST['product'];
    $pro_implode = implode($product, ',');
    $dateFrom = substr(dateToDbFormat($date_from), 0, 7);
    $dateTo = substr(dateToDbFormat($date_to), 0, 7);
    $funding_source = $_REQUEST['stakeholder'];
    $provinceID = $_REQUEST['province'];
    $where_prov = $where_dist = $where_prod = $where_stk = '';
    if ($provinceID != '') {
        $where_prov = " AND tbl_warehouse.prov_id =$provinceID";
    }
    if ($stakeholder != 'all') {
        $where_stk = "AND tbl_warehouse.stkid =$stakeholder";
    }

    if (!empty($product)) {
        $where_prod = " AND itminfo_tab.itm_id IN($pro_implode)";
    }
}

    if(empty($provinceID)) $provinceID = 1;
$fileName = "SDP level report";
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


</style>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <div class="page-container">
        <?php
//Including top
        include PUBLIC_PATH . "html/top.php";
//Including top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">

                        <h3 class="page-title row-br-b-wp">SDP level Consumption Report</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <div class="row">
                                    <form method="POST" name="frm" id="frm" action="">
                                        <!-- Row -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Date From</label>
                                                        <input type="text" readonly class="form-control input-sm" name="date_from" id="date_from" value="<?php echo $date_from; ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Date To</label>
                                                        <input type="text" readonly class="form-control input-sm" name="date_to" id="date_to" value="<?php echo $date_to; ?>"/>
                                                    </div>
                                                </div>
<div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Province</label>
                                                        <select required  name="province" id="province" class="form-control input-sm" >

                                                            <?php
//Province query
//gets
//Province id
//Province name
                                                            $qry = "SELECT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 2
                                                                        AND tbl_locations.ParentID IS NOT NULL";
                                                            $qryRes = mysql_query($qry);
                                                            if ($qryRes != FALSE) {
                                                                while ($row = mysql_fetch_object($qryRes)) {
                                                                    ?>
                                                                    <?php
                                                                    //Populate province combo

                                                                    if ($row->PkLocID == $provinceID) {
                                                                        $sel = "selected='selected'";
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>

                                                                    <option value="<?php echo $row->PkLocID; ?>"<?php echo $sel; ?>><?php echo $row->LocName; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Stakeholder</label>
                                                        <select    name="stakeholder" id="stakeholder" class=" form-control input-sm" >

                                                            <option value="" <?php echo ($funding_source == '') ? 'selected="selected"' : ''; ?>>All</option>
                                                            <?php
                                                            $querys = "SELECT
                                                                                    DISTINCT 
                                                                                    tbl_warehouse.wh_id,
                                                                                    tbl_warehouse.wh_name
                                                                                    FROM
                                                                                    funding_stk_prov
                                                                                    INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                                                                                    WHERE
                                                                                    funding_stk_prov.province_id = $provinceID";
                                                            //query result
                                                            //echo $querys;exit;
                                                            $rsprov = mysql_query($querys) or die();
                                                            $stk_name = '';
                                                            while ($rowp = mysql_fetch_array($rsprov)) {
                                                                if ($funding_source == $rowp['wh_id']) {
                                                                    $sel = "selected='selected'";
                                                                    $stk_name = $rowp['wh_name'];
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //Populate prov_sel combo
                                                                ?>
                                                                <option value="<?php echo $rowp['wh_id']; ?>" <?php echo $sel; ?>><?php echo $rowp['wh_name']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                


                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Product</label>
                                                        <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>
                                                            <?php
                                                            $queryprod = "SELECT
                                                                                itminfo_tab.itm_id,
                                                                                itminfo_tab.itm_name
                                                                                FROM
                                                                                itminfo_tab
                                                                                WHERE
                                                                                itminfo_tab.itm_category = 1 AND
                                                                                itminfo_tab.method_type IS NOT NULL
                                                                                ORDER BY
                                                                                itminfo_tab.method_rank ASC
                                                                        ";
//query result
                                                            $rsprod = mysql_query($queryprod) or die();

                                                            while ($rowprov = mysql_fetch_array($rsprod)) {
                                                                if (!isset($_REQUEST['product'])) {

                                                                    if ($rowprov['itm_id'] == 1 || $rowprov['itm_id'] == 5 || $rowprov['itm_id'] == 7 || $rowprov['itm_id'] == 8 || $rowprov['itm_id'] == 9 || $rowprov['itm_id'] == 13) {
                                                                        $itm_arr_request[] = $rowprov['itm_id'];
                                                                        $sel = "selected='selected'";
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                }
                                                                if (in_array($rowprov['itm_id'], $product)) {
                                                                    $sel = "selected='selected'";
                                                                    $itm_name[] = $rowprov['itm_name'];
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $rowprov['itm_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['itm_name']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12" style="text-align:right;">
                                                    <label for="firstname">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                        <button type="reset" class="btn btn-info">Reset</button>
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
                <?php if (isset($_REQUEST['search'])) { ?>
                    <div style="col-md-12">
                        <?php
                        include('new_report.php');
                        ?>
                    </div>
                    <?php } ?>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
<?php include PUBLIC_PATH . "/html/footer.php"; ?>

    <script src="<?= PUBLIC_URL ?>js/bootstrap_multiselect.js"></script>
<?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>


    <script>
        $(function () {
            var startDateTextBox = $('#date_from');
            var endDateTextBox = $('#date_to');

            $('.multiselect-ui').multiselect({
                includeSelectAllOption: false
            });

            startDateTextBox.datepicker({
                minDate: "-10Y",
                maxDate: 0,
                dateFormat: 'yy/mm/dd',
                changeMonth: true,
                changeYear: true,
                onClose: function (dateText, inst) {
                    if (endDateTextBox.val() != '') {
                        var testStartDate = startDateTextBox.datepicker('getDate');
                        var testEndDate = endDateTextBox.datepicker('getDate');
                        if (testStartDate > testEndDate)
                            endDateTextBox.datepicker('setDate', testStartDate);
                    } else {
                        endDateTextBox.val(dateText);
                    }

                },
                onSelect: function (selectedDateTime) {
                    endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
                }
            });
            endDateTextBox.datepicker({
                maxDate: 0,
                dateFormat: 'yy/mm/dd',
                changeMonth: true,
                changeYear: true,
                onClose: function (dateText, inst) {
                    if (startDateTextBox.val() != '') {
                        var testStartDate = startDateTextBox.datepicker('getDate');
                        var testEndDate = endDateTextBox.datepicker('getDate');
                        if (testStartDate > testEndDate)
                            startDateTextBox.datepicker('setDate', testEndDate);
                    } else {
                        startDateTextBox.val(dateText);
                    }

                },
                onSelect: function (selectedDateTime) {
                    startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
                }
            });
        })
        $(function () {

            $('#product').change(function (e) {

            });
            $(document).on('change', '#district', function () {
                $('#warehouse').html('<option value="">Select</option>');

            });
            $('#stakeholder').change(function (e) {


                $('#warehouse').html('<option value="">Select</option>');
            });
            $("#province").change(function(){
                $.ajax({
                     type: "POST",
                     url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                     data: { required_value : "funding_sources", prov_id: $(this).val()},
                     success: function(data) {
                             $("#stakeholder").html(data);
                     }
                 });
            });
        })


    </script>
</body>
<!-- END BODY -->
</html>