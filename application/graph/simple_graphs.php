<?php
/**
 * simple_graphs
 * @package graph
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
?>
<style>
    label.checkbox{margin:0px !important;}
    .modal {
        display:    none;
        position:   fixed;
        z-index:    10000;
        top:       	0;
        left:       0;
        height:     100%;
        width:      100%;
        background: rgba( 255, 255, 255, 0.3 )
            url('../../public/images/loader.gif')
            50% 50%
            no-repeat;
    }
    /* When the body has the loading class, we turn
       the scrollbar off with overflow:hidden */
    body.loading {
        overflow: auto;
    }
    /* Anytime the body has the loading class, our
       modal element will be visible */
    body.loading .modal {
        display: block;
    }
</style>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
    <div class="page-container">
        <?php
        //Including top file
        include PUBLIC_PATH . "html/top.php";
        //Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <h3 class="page-title row-br-b-wp" style="margin-left:10px;">Simple Graphs</h3>
                    <div class="col-md-3">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form">
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label>Indicators</label>
                                            <div class="controls">
                                                <select name="indicator" id="indicator" class="form-control input-sm">
                                                    <optgroup label="CYP">
                                                        <option value="1">Couple Year Protection</option>
                                                    </optgroup>
                                                    <optgroup label="Dispensed">
                                                        <option value="2">Consumption</option>
                                                        <option value="3">Avg Monthly Consumption</option>
                                                    </optgroup>
                                                    <optgroup label="MOS">
                                                        <option value="4">Months Of Stock - Field</option>
                                                        <option value="5">Month of Stock - Warehouse</option>
                                                        <option value="6">Months of Stock - Total</option>
                                                    </optgroup>
                                                    <optgroup label="OnHand">
                                                        <option value="7">Stock On Hand - Field</option>
                                                        <option value="8">Stock On Hand - Warehouse</option>
                                                        <option value="9">Stock On Hand - Total</option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label>Geographical Options</label>
                                            <div class="controls">
                                                <select name="compare_option" id="compare_option" class="form-control input-sm">
                                                    <optgroup label="Geographical">
                                                        <option value="7">National</option>
                                                        <option value="8">Provincial</option>
                                                        <option value="9">District</option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="province_div" style="display:none;">
                                        <div class="control-group">
                                            <label>Province</label>
                                            <div class="controls">
                                                <select name="province" id="province" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    <?php
                                                    //Gets
                                                    //PkLocID
                                                    //LocName
                                                    $qry = "SELECT
                                                                    tbl_locations.PkLocID,
                                                                    tbl_locations.LocName
                                                            FROM
                                                                    tbl_locations
                                                            WHERE
                                                                    tbl_locations.LocLvl = 2
                                                            AND tbl_locations.ParentID IS NOT NULL";
                                                    //Query result
                                                    $qryRes = mysql_query($qry);
                                                    //Populate province combo
                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                        echo "<option value=\"" . $row['PkLocID'] . "\">" . $row['LocName'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group" id="district_div" style="display:none;">
                                            <label>District</label>
                                            <div class="controls">
                                                <select name="district" id="district" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label>Stakeholder Type</label>
                                            <div class="controls">
                                                <select class="form-control input-sm" id="sector" name="sector">
                                                    <option value="all">All</option>
                                                    <option value="0">Public</option>
                                                    <option value="1">Private</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label>Stakeholder</label>
                                            <div class="controls">
                                                <select name="stakeholder" id="stakeholder" class="form-control input-sm">
                                                    <option value="all">All</option>
                                                    <?php
                                                    //Gets 
                                                    //stkid
                                                    //stkname
                                                    $qry = "SELECT
                                                                    stakeholder.stkid,
                                                                    stakeholder.stkname
                                                            FROM
                                                                    stakeholder
                                                            WHERE
                                                                    stakeholder.stk_type_id IN (0, 1)
                                                            AND stakeholder.ParentID IS NULL
                                                            ORDER BY
                                                                    stakeholder.stkorder ASC";
                                                    //Query result
                                                    $qryRes = mysql_query($qry);
                                                    //Populate stakeholder combo
                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                        echo "<option value=\"" . $row['stkid'] . "\">" . $row['stkname'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label>Category</label>
                                            <div class="controls"><select id="category" name="category" class="form-control input-sm">
                                                    <option>Select</option>
                                                    <option value="1">Contraceptives</option>
                                                    <option value="5">MNCH Products</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label>Products</label>
                                            <div class="controls" id="product_multi_div" style="border: 1px solid #F2F2F2; padding-left:25px; height:120px; overflow:auto;">
                                                <?php
                                                //Gets
                                                //itm_id
                                                //itm_name
                                                $qry = "SELECT DISTINCT
                                                                    itminfo_tab.itm_id,
                                                                    itminfo_tab.itm_name
                                                            FROM
                                                                    itminfo_tab
                                                            INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                                                            WHERE
                                                                    itminfo_tab.itm_category in (1,2,5)
                                                            ORDER BY
                                                                itm_category,
                                                                itminfo_tab.frmindex ASC";
                                                //Query result
                                                $qryRes = mysql_query($qry);

                                                echo '<label class="checkbox">';
                                                echo "<input type=\"checkbox\" name=\"product_multi_check\" id=\"product_multi_check\" value=\"all\" />All</label>";

                                                while ($row = mysql_fetch_array($qryRes)) {

                                                    if ($row['itm_id'] == '31' || $row['itm_id'] == '32')
                                                        $row['itm_name'] = 'CS Cases - ' . $row['itm_name'];
                                                    echo '<label class="checkbox">';
                                                    echo "<input type=\"checkbox\" name=\"product_multi[]\" id=\"product_multi\" value=\"" . $row['itm_id'] . "\" /> " . $row['itm_name'];
                                                    echo "</label>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label>Time Interval</label>
                                            <div class="controls">
                                                <select name="time_interval" id="time_interval" class="form-control input-sm">
                                                    <optgroup label="Quarter">
                                                        <option value="1">First Quarter</option>
                                                        <option value="2">Second Quarter</option>
                                                        <option value="3">Third Quarter</option>
                                                        <option value="4">Fourth Quarter</option>
                                                    </optgroup>
                                                    <optgroup label="Half">
                                                        <option value="5">First Half</option>
                                                        <option value="6">Second Half</option>
                                                    </optgroup>
                                                    <optgroup label="Annual">
                                                        <option value="7">Annual</option>
                                                    </optgroup>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label>Year</label>
                                            <div class="controls">
                                                <select name="year" id="year" class="form-control input-sm">
                                                    <?php
                                                    for ($year = date('Y'); $year >= 2010; $year--) {
                                                        echo "<option value=\"" . $year . "\">" . $year . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label>Graph Type</label>
                                            <div class="controls">
                                                <select name="graph_type" id="graph_type" class="form-control input-sm">
                                                    <option value="3">Stacked Bar</option>
                                                </select>
                                                <input type="hidden" name="type" value="simple" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="control-group">
                                            <label>&nbsp;</label>
                                            <div class="controls">
                                                <input type="button" id="submit_button" value="Generate Graph" class="btn btn-primary input-sm" />
                                                <input type="button" value="Reset" class="btn btn-warning input-sm" onClick="window.location = window.location" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div style="clear:both;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading" id="grpah_heading">Graph for the selected criteria</h3>
                            </div>
                            <div class="widget-body" id="graphArea"> Select criteria and press submit button to see the Graphs </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- END FOOTER -->
    <?php
//Including footer file
    include PUBLIC_PATH . "/html/footer.php";
//Including reports_includes file
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script language="javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
    <script>
                                                    $(function () {
                                                        $('#grpah_heading').html($("#indicator option:selected").text());
                                                        // Show stakeholders
                                                        $('#sector').change(function (e) {
                                                            $.ajax({
                                                                url: 'ajax.php',
                                                                data: {type: $('#sector').val(), ctype: 3},
                                                                type: 'POST',
                                                                success: function (data) {
                                                                    $('#stakeholder').html(data);
                                                                    showProducts();
                                                                }
                                                            })
                                                        });

                                                        // Display Graph Title
                                                        $('#indicator').change(function (e) {
                                                            $('#grpah_heading').html($("#indicator option:selected").text());
                                                        });
                                                        $('#category').change(function (e) {

                                                            $.ajax({
                                                                url: 'category_ajax.php',
                                                                data: {cat: $('#category').val()},
                                                                type: 'POST',
                                                                success: function (data) {
                                                                    $("#product_multi_div").html('').html(data);
                                                                }
                                                            });
                                                        });

                                                        // Show Stakeholder Products
                                                        $('#stakeholder').change(function (e) {
                                                            showProducts();
                                                        });

                                                        // Show/Hide Provinces
                                                        $('#indicator').change(function (e) {
                                                            if ($(this).val() == 1) {
                                                                $('#graph_type').html('<option value="3">Stacked Bar</option>');
                                                            } else {
                                                                $('#graph_type').html('<option value="1">Bar</option><option value="2">Line</option><option value="4">Spline</option>');
                                                            }
                                                        });

                                                        $("#product_multi_check").change(function () {

                                                            var a = $(this).prop('checked');
                                                            if (a) {
                                                                $('input:checkbox').prop('checked', true);
                                                                //$("input[id=product_multi]").checked("true");
                                                            } else {
                                                                $('input:checkbox').prop('checked', false);
                                                            }
                                                        });

                                                        $("#time_interval").change(function () {
                                                            var a = $(this).val();
                                                            if (a == 7) {
                                                                $("#year").append('<option value="all">All</option>');
                                                                $("#year").val('all');
                                                            } else {
                                                                $("#year option[value='all']").remove()
                                                            }
                                                        });

                                                        // Show/Hide Provinces
                                                        $('#compare_option').change(function (e) {
                                                            var compare_option = $(this).val();
                                                            $('#province').val('');
                                                            if (compare_option == 7) {
                                                                $('#province_div').hide();
                                                                $('#province').val('');
                                                                $('#district_div').hide();
                                                            } else if (compare_option == 8) {
                                                                $('#province_div').show();
                                                                $('#district_div').hide();
                                                            } else if (compare_option == 9) {
                                                                $('#province_div').show();
                                                            }
                                                        });

                                                        // Show Districts
                                                        $('#province').change(function (e) {
                                                            if ($(this).val() != '' && $('#compare_option').val() == 9) {
                                                                $.ajax({
                                                                    url: 'ajax.php',
                                                                    data: {province: $(this).val(), ctype: 2, compare_option: 1},
                                                                    type: 'POST',
                                                                    success: function (data) {
                                                                        $('#district_div').show().html(data);
                                                                    }
                                                                })
                                                            } else {
                                                                $('#district_div').hide();
                                                            }
                                                        });

                                                        // Form submission
                                                        $('#submit_button').click(function (e) {
                                                            var compare_option = $('#compare_option').val();
                                                            // Validation
                                                            if ((compare_option == 8 || compare_option == 9) && $('#province').val() == '') {
                                                                alert('Select province');
                                                                return false;
                                                            }
                                                            if ($('[name="product_multi[]"]:checked').length == 0) {
                                                                alert('Select at least one product');
                                                                return false;
                                                            }

                                                            // Submit data
                                                            $('#graphArea').html('');
                                                            $('body').addClass("loading");
                                                            $.ajax({
                                                                url: 'plot_graph.php',
                                                                data: $('#frm').serialize(),
                                                                type: 'POST',
                                                                success: function (data) {
                                                                    $('body').removeClass("loading");
                                                                    $('#graphArea').html(data);
                                                                }
                                                            })
                                                        });
                                                    })
                                                    function showProducts()
                                                    {
                                                        $.ajax({
                                                            url: 'ajax.php',
                                                            data: {stakeholder: $('#stakeholder').val(), ctype: 1},
                                                            type: 'POST',
                                                            success: function (data) {
                                                                $('#product_multi_div').html(data);

                                                                $("#product_multi_check").change(function () {
                                                                    var a = $(this).prop('checked');
                                                                    if (a) {
                                                                        $('input:checkbox').prop('checked', true);
                                                                        //$("input[id=product_multi]").checked("true");
                                                                    } else {
                                                                        $('input:checkbox').prop('checked', false);
                                                                    }
                                                                });

                                                            }
                                                        })
                                                    }
    </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>