<?php
/**

 * 
 */
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
                                                        <option value="all" <?php echo ($product == 'all') ? 'selected="selected"' : ''; ?>>All</option>
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
                $.inlineEdit({
                    expiry: '/stock/product-ledger-date-edit/type/expiry/id/'
                }, {
                    animate: false,
                    filterElementValue: function ($o) {
                        return $o.html().trim();
                    },
                    afterSave: function () {
                    }
                });

                // initTable2();

            }
        });


    });
</script>
</body>
<!-- END BODY -->
</html>