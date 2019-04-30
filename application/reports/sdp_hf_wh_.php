<?php
/**
 * stock_status
 * @package reports
 * 
 * @author     Ajmal Hussain 
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses
include("../includes/classes/AllClasses.php");
//Including FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//Including header
include(PUBLIC_PATH . "html/header.php");
//Initialing variable report_id
//$report_id = "STOCKISSUANCE";
//Checking date

if (date('d') > 10) {
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
$selMonth = date('m', strtotime($date));
$selYear = date('Y', strtotime($date));
//Initialing variables
$date_from = $date_to = $product = $provinceID = $district = $stakeholder = $warehouse = $xmlstore = $selProv = '';
//Checking search
if (isset($_REQUEST['search'])) {
    //Getting date_from
    $date_from = $_REQUEST['date_from'];
    //Getting date_to
    $date_to = $_REQUEST['date_to'];
    $product = $_REQUEST['product'];
    $pro_implode = implode($product, ',');
//     print_r($pro_implode);exit;
    //Setting date_from
    $dateFrom = substr(dateToDbFormat($date_from), 0, 7);
    //Setting dateTo
    $dateTo = substr(dateToDbFormat($date_to), 0, 7);
//    print_r($dateTo);exit;
    //Getting stakeholder
    $stakeholder = $_REQUEST['stakeholder'];
    $stk = implode($stakeholder, ',');
    //Getting province
    $provinceID = $_REQUEST['province'];
    $prov = implode($provinceID, ',');
    //Getting district
    $district = $_REQUEST['district'];
    $consumption=$_REQUEST['consum'];
//    print_r($consumption);exit;
    $where_consum='';
    if($consumption==0)
    {
        $where_consum="HAVING consum = 0";
    }
    elseif ($consumption==1) {
    $where_consum="HAVING consum > 0";
}
else{
    $where_consum='';
}
  
    //Getting warehouse
    $where_prov = $where_dist = $where_prod = $where_stk = '';
    if ($provinceID != 'all') {
        $where_prov = " AND tbl_warehouse.prov_id IN($prov)";
    }
    if ($stakeholder != 'all') {
        $where_stk = "AND tbl_warehouse.stkid IN($stk)";
    }
    if ($district != '') {
        $where_dist = "AND tbl_warehouse.dist_id=$district";
    }
    if (!empty($product)) {
        $where_prod = " AND itminfo_tab.itm_id IN($pro_implode)";
    }
}
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
                                                        <label class="control-label">Stakeholder</label>
                                                        <select required  name="stakeholder[]" id="stakeholder" class="multiselect-ui form-control input-sm" multiple>

                                                            <option value="all" <?php echo ($stakeholder == 'all') ? 'selected="selected"' : ''; ?>>All</option>
                                                            <?php
//stakeholder query
//gets
//stkid
//stkname
                                                            $qry = "SELECT
																	stakeholder.stkid,
																	stakeholder.stkname
																FROM
																	stakeholder
																WHERE
																	stakeholder.ParentID IS NULL
																AND stakeholder.stk_type_id IN (0, 1)
																ORDER BY
																	stakeholder.stkorder ASC";
                                                            $qryRes = mysql_query($qry);
                                                            if ($qryRes != FALSE) {
                                                                while ($row = mysql_fetch_object($qryRes)) {
                                                                    ?>
                                                                    <?php
                                                                    //Populate stakeholder combo
                                                                    if (in_array($row->stkid, $stakeholder)) {
                                                                        $sel = "selected='selected'";
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $row->stkid; ?>" <?php echo $sel; ?>><?php echo $row->stkname; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Province</label>
                                                        <select required  name="province[]" id="province" class="multiselect-ui form-control input-sm" multiple>

                                                            <option value="all" <?php echo ($provinceID == 'all') ? 'selected="selected"' : ''; ?>>All</option>
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

                                                                    if (in_array($row->PkLocID, $provinceID)) {
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
                                                    <div class="form-group" id="districtsCol">
                                                        <label class="control-label">District</label>
                                                        <select name="district" id="district" class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option value="" <?php echo ($district == '') ? 'selected="selected"' : ''; ?>>All</option>
                                                            <?php
//District query
//gets
//District id
//District name
                                                            $qry = "SELECT
																	tbl_locations.PkLocID,
																	tbl_locations.LocName
																FROM
																	tbl_locations
																WHERE
																	tbl_locations.LocLvl = 3
																AND tbl_locations.ParentID = '$selProv'";
                                                            $qryRes = mysql_query($qry);
                                                            if ($qryRes != FALSE) {
                                                                while ($row = mysql_fetch_object($qryRes)) {
                                                                    ?>                              
                                                                    <?php //Populate District combo?>
                                                                    <option value="<?php echo $row->PkLocID; ?>" <?php echo ($district == $row->PkLocID) ? 'selected="selected"' : ''; ?>><?php echo $row->LocName; ?></option>
                                                                    <?php
                                                                }
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
                                                <div class="col-md-2" style="text-align:left;">
                                                    <div class="form-group" >
                                                        <label class="control-label">Consumption</label>
                                                        <select name="consum" id="consum" class="form-control input-sm" required="true">
                                                            <option value="">Select</option>
                                                            <option value="-1" <?php if($consumption==-1) echo 'selected=selected'?>>All</option>
                                                              <option value="0"  <?php if($consumption==0) echo 'selected=selected'?>>Consumption = 0</option>
                                                              <option value="1" <?php if($consumption==1) echo 'selected=selected'?> >Consumption > 0</option>
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
                    <div class="row">
                        <div class="col-md-12">


                            <div class="widget">
                                <div class="widget-body">
                                    <?php include('sub_dist_reports.php'); ?>
                                    <div class="row"><br></div>
                                    <?php
                                    $prod_array = $wh_array = $test_array = array();
                                    $qry = "SELECT
	tbl_hf_data.reporting_date AS rep_date,
	itminfo_tab.itm_name,
	tbl_warehouse.wh_name,
	tbl_hf_data.warehouse_id AS wh_id,
	itminfo_tab.itm_id,
	tbl_warehouse.prov_id,
	tbl_warehouse.stkid,
	tbl_warehouse.dist_id,
	prov.LocName AS prov_name,
	dist.LocName AS district_name,
	stakeholder.stkname,
	Sum(tbl_hf_data.issue_balance) AS consum,
         tbl_hf_data.closing_balance  as soh
FROM
	tbl_hf_data
INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
INNER JOIN tbl_locations AS dist ON tbl_warehouse.dist_id = dist.PkLocID
INNER JOIN tbl_locations AS prov ON tbl_warehouse.prov_id = prov.PkLocID
INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
WHERE
tbl_hf_data.reporting_date BETWEEN '$dateFrom-01' AND '$dateTo-01' $where_prov $where_dist $where_stk $where_prod
    AND stakeholder.lvl = 1
    AND tbl_hf_data.closing_balance=0
                                        AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                                        AND NOT (tbl_warehouse.prov_id = 3 and tbl_warehouse.stkid = 9)
                                        AND NOT (tbl_warehouse.prov_id = 3 and tbl_warehouse.stkid = 2)
                                        AND tbl_warehouse.is_active = 1
    GROUP BY
	tbl_hf_data.warehouse_id,itminfo_tab.itm_id
$where_consum
  
";
//                                    print_r($qry);
//                                    exit;
                                    $res = mysql_query($qry);
                                    $num = mysql_num_rows($res);
                                    $count = 0;
                                    $cons_array = array();
                                    if ($num > 0) {
                                        ?>
                                        <table style="width:95%;margin-left: 2%;" align="center"   id="myTable" class="table table-striped table-bordered table-condensed">
                                            <thead style="background-color:lightgray">
                                            <th  >S. No.</th>
                                            <th  >Province</th>
                                            <th   >District</th>
                                            <th   >Stakeholder</th>
                                            <th   >Warehouse</th>
                                            <th   >Product</th>
                                            <th  >SOH</th>
                                            <th  >Consumption</th>


                                            </thead>
                                            <?php
                                            $counter = 1;
                                            while ($row = mysql_fetch_assoc($res)) {
                                                ?>
                                                <tbody>

                                                    <tr>
                                                        <td><?php echo $counter++; ?></td>
                                                        <td><?php echo $row['prov_name']; ?></td>
                                                        <td><?php echo $row['district_name']; ?></td>
                                                        <td ><?php echo $row['stkname']; ?></td>
                                                        <td><?php echo $row['wh_name']; ?></td>
                                                        <td><?php echo $row['itm_name']; ?></td>
                                                        <td><?php echo $row['soh']; ?></td>
                                                        <td><?php echo $row['consum']; ?></td>
                                                    </tr>

                                                </tbody>
                                            <?php }
                                            ?>


                                        </table>
                                    <?php } else {
                                        ?><div style="margin-left: 15px;"><label> <?php echo 'No record found'; ?>  </label> </div><?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
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
                dateFormat: 'dd/mm/yy',
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
                    showWarehouses('');
                },
                onSelect: function (selectedDateTime) {
                    endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
                }
            });
            endDateTextBox.datepicker({
                maxDate: 0,
                dateFormat: 'dd/mm/yy',
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
                    showWarehouses('');
                },
                onSelect: function (selectedDateTime) {
                    startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
                }
            });
        })
        $(function () {
            showDistricts('<?php echo $district; ?>');

            $('#province').change(function (e) {
                $('#warehouse').html('<option value="">Select</option>');
                showDistricts('');
            });
            $('#product').change(function (e) {
                showWarehouses('');
            });
            $(document).on('change', '#district', function () {
                $('#warehouse').html('<option value="">Select</option>');
                showWarehouses('');
            });
            $('#stakeholder').change(function (e) {
                 
                
                $('#warehouse').html('<option value="">Select</option>');
            });
        })
        function showDistricts(dId) {
            var pid = $('#province').val();
            if (pid != '')
            {
                if (pid != 'all')
                {
                    $.ajax({
                        url: 'ajax_calls.php',
                        type: 'POST',
                        data: {provinceId: pid, dId: dId, validate: 'no', rep_id: 'sdp_hf'},
                        success: function (data) {
                            $('#districtsCol').html(data);
//                             console.log(data);
                        }
                    })
                } else
                {
                    $('#district').html('<option value="">All</option>');
                    $('#warehouse').html('<option value="all">All</option>');
                }
            } else
            {
                $('#district').html('<option value="">Select</option>');
            }
        }
        function showWarehouses(whId)
        {
            var stkId = $('#stakeholder').val();
            var provId = $('#province').val();
            var distId = $('#district').val();
            var dateFrom = $('#date_from').val();
            var dateTo = $('#date_to').val();
            var product = $('#product').val();

            if (distId != '')
            {
                $.ajax({
                    url: 'ajax_calls.php',
                    data: {stkId: stkId, provId: provId, distId: distId, whId: whId, dateFrom: dateFrom, dateTo: dateTo, product: product},
                    type: 'POST',
                    success: function (data) {
                        $('#warehouse').html(data);
                    }
                })
            } else
            {
                $('#warehouse').html('<option value="all">All</option>');
            }
        }
    </script>
</body>
<!-- END BODY -->
</html>