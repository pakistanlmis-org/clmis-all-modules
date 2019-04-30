<style>
    .sorting_disabled{
        background-color:  white !important;
        color: black !important;
    }
</style>
<?php
//Including AllClasses
include("../includes/classes/AllClasses.php");
//Including FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//Including header
include(PUBLIC_PATH . "html/header.php");
//Initialing variable report_id
//Checking date
if (date('d') > 10) {
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
$selMonth = date('m', strtotime($date));
$selYear = date('Y', strtotime($date));
//$fundingSource = $_REQUEST['funding_source'];
//Initialing variables
$date_from = $date_to = $product = $provinceID = $district = $stakeholder = $warehouse = $xmlstore = $selProv = '';
//Checking search
?>
<!-- Content -->
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
                    <h3 class="page-title row-br-b-wp">Stock Ledger</h3>
                    <div class="widget" data-toggle="collapse-widget">
                        <div class="widget-head">
                            <h3 class="heading">Filter by</h3>
                        </div>
                        <div class="widget-body">
                            <div class="row">
                                <form method="POST" name="ledger" id="ledger" action="">
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
                                                    <label class="control-label">Product</label>
                                                    <select name="product" id="product" class="form-control input-sm" required="required">
                                                        <option value="">Select</option>

                                                        <?php
//Product query
//gets
//itm_id
//itm_name
                                                        $category = '1,4';
                                                        if($_SESSION['user_stakeholder1'] == 145) $category='5';
                                                        
                                                        
                                                        $qry = "SELECT
                                                                        itminfo_tab.itm_id,
                                                                        itminfo_tab.itm_name
                                                                FROM
                                                                        itminfo_tab
                                                                INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                                                                WHERE
                                                                        stakeholder_item.stkid = " . $_SESSION['user_stakeholder1'] . "
                                                                AND itminfo_tab.itm_category in ($category)
                                                                ORDER BY
                                                                        itminfo_tab.frmindex";
                                                        //echo $qry;exit;
                                                        $qryRes = mysql_query($qry);
                                                        if ($qryRes != FALSE) {
                                                            while ($row = mysql_fetch_object($qryRes)) {
                                                                ?>
                                                                <?php //Populate product combo?>
                                                                <option value="<?php echo $row->itm_id; ?>" <?php echo ($product == $row->itm_id) ? 'selected="selected"' : ''; ?>><?php echo $row->itm_name; ?></option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Funding Source</label>
                                                    <select name="funding_source" id="funding_source" class="form-control input-sm">
                                                        <option value="all">All</option>
                                                         <?php
                                                            $qry = "SELECT
                                                                            tbl_warehouse.wh_id,
                                                                            tbl_warehouse.wh_name
                                                                    FROM
                                                                            stakeholder
                                                                    INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                                                    WHERE
                                                                            stakeholder.stk_type_id = 2
                                                                    AND tbl_warehouse.is_active = 1
                                                                    ORDER BY
                                                                            stakeholder.stkorder ASC";
                                                            $qryRes = mysql_query($qry);
                                                            while ($row = mysql_fetch_array($qryRes)) {
                                                                $selected = ($row['wh_id'] == $fundingSource) ? 'selected' : '';
                                                                //populate funding_source combo
                                                                echo "<option value=\"$row[wh_id]\" $selected>$row[wh_name]</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4" style="text-align:right;">
                                                <label for="firstname">&nbsp;</label>
                                                <div class="form-group">
                                                    <input type="submit" class="btn btn-success" name="submit" id="submit" value="Submit" />
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
            <div id="product_ledger">

            </div>
        </div>
    </div>
</div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
//reports_includes
include PUBLIC_PATH . "/html/reports_includes.php";
?>
<script>
    $(function () {
        var startDateTextBox = $('#date_from');
        var endDateTextBox = $('#date_to');

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

            },
            onSelect: function (selectedDateTime) {
                startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
            }
        });
    })

    $('#submit').click(function (e) {

        e.preventDefault();
        var formdata = $("#ledger").serialize();
        Metronic.startPageLoading('Please wait...');
        $.ajax({
            type: "POST",
            url: "ajax-product-ledger.php",
            data: {data: formdata},
            dataType: 'html',
            success: function (data) {
                $('#product_ledger').html(data);
                Metronic.stopPageLoading();


                $('.stkledger').dataTable({
                    "bPaginate": false,
                    "bInfo": false,
                    "aoColumnDefs": [
                        {"sType": 'date-uk', "aTargets": [1]},
                        {"bSortable": false, "aTargets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]}
                        /*{
                         "aTargets": [-1],
                         "bVisible": false
                         }*/
                    ],
                    "aaSorting": [[0, 'asc']],
                    "aLengthMenu": [
                        [5, 15, 20, -1],
                        [5, 15, 20, "All"] // change per page values here
                    ],
                    "sDom": "<'row'<'col-md-12 col-sm-12 right'T>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
                    //"sDom": "<'row'<'col-md-4 col-sm-12'l><'col-md-4 col-sm-12'T><'col-md-4 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

//"sDom ": 'T<"clear ">lfrtip',
                    // set the initial value
                    "bDestroy": true,
                    "iDisplayLength": 1000,
                    "oTableTools": {
                        "sSwfPath": basePath + "/common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf",
                        "aButtons": [{
                                "sExtends": "xls",
                                "sButtonText": "<img src=../../public/images/excel-32_1.png> Export to Excel"
                            }, {
                                "sExtends": "copy",
                                "sButtonText": "<img src=../../public/images/copy.png> Copy Table"
                            }]
                    }
                });


            }
        });


    });
</script>
</body>
<!-- END BODY -->
</html>