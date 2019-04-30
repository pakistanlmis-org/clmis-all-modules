<?php
ini_set('max_execution_time', 0);
//echo '<pre>';print_r($_REQUEST);exit;
include_once("../includes/classes/AllClasses.php");
include_once(PUBLIC_PATH . "html/header.php");



$min_date = date("Y-m-01", strtotime("-2 months"));



$show = '';
if (!empty($_REQUEST['lvl']))
    $lvl = $_REQUEST['lvl'];

$stakeholder = (!empty($_SESSION['user_stakeholder1']) ? $_SESSION['user_stakeholder1'] : '1');
if (isset($_REQUEST['submit'])) {
    $d_1 = '2018-07-01';
    if ($_SERVER['SERVER_ADDR'] == '::1') {
        $d_1 = '2018-01-01';
        $min_date = '2016-01-01';
    }
    $date = (!empty($_REQUEST['date']) ? $_REQUEST['date'] : $d_1);
    $date = date('Y-m-01', strtotime($date));
    $stakeholder = mysql_real_escape_string($_REQUEST['stakeholder']);
    $selProv = mysql_real_escape_string($_REQUEST['prov_sel']);
    $districtId = mysql_real_escape_string($_REQUEST['dist_id']);
    $itm_id = mysql_real_escape_string($_REQUEST['product']);
}
?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">(3) Analysis for stock optimization (<?= date('M-Y', strtotime($date)) ?>)</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php
                                //include('sub_dist_form.php');
                                ?>
                            </div>
                            <form id="frm">
                                <table>

                                    <td class="col-md-2">
                                        <label class="control-label">Month</label>
                                        <input class="form-control input-sm" type="date" min="<?= $min_date ?>"  name="date" id="date" value="<?= $date ?>">
                                    </td>
                                    <td class="col-md-2">
                                        <label class="control-label">Stakeholder</label>
                                        <select name="stakeholder" id="stakeholder" required class="form-control input-sm">
                                            <option value="">Select</option>
                                            <?php
                                            $querys = "SELECT
                                        stakeholder.stkid,
                                        stakeholder.stkname
                                        FROM
                                        stakeholder
                                        WHERE
                                        stakeholder.ParentID IS NULL
                                        AND stakeholder.stk_type_id IN (0, 1) AND
                                        stakeholder.is_reporting = 1
                                        
                                        ORDER BY
                                        stakeholder.stkorder ASC";
                                            $rsprov = mysql_query($querys) or die();
                                            $stk_name = '';
                                            while ($rowp = mysql_fetch_array($rsprov)) {
                                                if ($stakeholder == $rowp['stkid']) {
                                                    $sel = "selected='selected'";
                                                    $stk_name = $rowp['stkname'];
                                                } else {
                                                    $sel = "";
                                                }
                                                ?>
                                                <option value="<?php echo $rowp['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowp['stkname']; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td class="col-md-2">
                                        <label class="control-label">Province</label>
                                        <select name="prov_sel" id="prov_sel" onchange="showDistricts()" required="required" class="form-control input-sm">
                                            <option>Select</option>
                                            <?php
                                            $queryprov = "SELECT
                                                                                    tbl_locations.PkLocID AS prov_id,
                                                                                    tbl_locations.LocName AS prov_title
                                                                                FROM
                                                                                    tbl_locations
                                                                                WHERE
                                                                                    LocLvl = 2
                                                                                    AND parentid IS NOT NULL
                                                                                    AND tbl_locations.LocType = 2";
                                            //query result
                                            $rsprov = mysql_query($queryprov) or die();
                                            $prov_name = 'Punjab';
                                            while ($rowprov = mysql_fetch_array($rsprov)) {
                                                if ($selProv == $rowprov['prov_id']) {
                                                    $sel = "selected='selected'";
                                                    $prov_name = $rowprov['prov_title'];
                                                } else {
                                                    $sel = "";
                                                }

                                                //if($_SESSION['user_level'] > 1 && isset($_SESSION['user_province1']) && $_SESSION['user_province1'] == $rowprov['prov_id'] ) 
                                                echo '<option value="' . $rowprov['prov_id'] . '" ' . $sel . '> ' . $rowprov['prov_title'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td class="col-md-2 filter1" id="td_dist"  ><label class="sb1NormalFont">District:</label>
                                        <select name="dist_id" id="dist_id" class="form-control input-sm">
                                            <?php
                                            $queryDist = "SELECT
                                                                    tbl_locations.PkLocID,
                                                                    tbl_locations.LocName
                                                            FROM
                                                                    tbl_locations
                                                            WHERE
                                                                    tbl_locations.LocLvl = 3
                                                            AND tbl_locations.parentid = '" . $selProv . "'
                                                            ORDER BY
                                                                    tbl_locations.LocName ASC";
//query result
                                            $rsDist = mysql_query($queryDist) or die();
//fetch result
                                            $dist_name = "Attock";
                                            while ($rowDist = mysql_fetch_array($rsDist)) {
                                                if ($districtId == $rowDist['PkLocID']) {
                                                    $sel = "selected='selected'";
                                                    $dist_name = $rowDist['LocName'];
                                                } else {
                                                    $sel = "";
                                                }

                                                //if($_SESSION['user_level'] == 3 && isset($_SESSION['user_district']) && $_SESSION['user_district'] == $rowDist['PkLocID'] ) 
                                                echo '<option value="' . $rowDist['PkLocID'] . '" ' . $sel . '>' . $rowDist['LocName'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td class="col-md-2">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="control-label">Product</label>
                                                <select required  name="product" id="product" class=" form-control input-sm" >
                                                    <?php
                                                    $itm_name = '';
                                                    $queryprov = "SELECT
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
                                                    $rsprov = mysql_query($queryprov) or die();

                                                    while ($rowprov = mysql_fetch_array($rsprov)) {
                                                        if ($rowprov['itm_id'] == $itm_id) {
                                                            $sel = "selected='selected'";
                                                            $itm_name = $rowprov['itm_name'];
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
                                    </td>
                                    <td class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="form-group">
                                                <button type="submit" name="submit" class="btn btn-primary input-sm" value="Submit">Go</button>
                                            </div>
                                        </div>
                                    </td>
                                </table></form>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <?php
                    if (isset($_REQUEST['submit'])) {

                        echo '<div class="col-md-12">';
                        echo '<a class="btn btn-sm green" href="stock_optimization.php?date=' . $date . '&stakeholder=' . $stakeholder . '&prov_sel=' . $selProv . '&dist_id=' . $districtId . '&product=' . $itm_id . '&submit=Submit">(1) List of Stock Status at SDP Level</a>';
                        echo '  <i class="fa fa-arrow-right " style="color:black !important;"></i>  <a class="btn btn-sm green" href="stock_optimization_2.php?date=' . $date . '&stakeholder=' . $stakeholder . '&prov_sel=' . $selProv . '&dist_id=' . $districtId . '&product=' . $itm_id . '&submit=Submit">(2) Filter SDPs with unusual stock</a>';
                        echo '  <i class="fa fa-arrow-right " style="color:black !important;"></i>  <a class="btn btn-sm green" href="stock_optimization_3.php?date=' . $date . '&stakeholder=' . $stakeholder . '&prov_sel=' . $selProv . '&dist_id=' . $districtId . '&product=' . $itm_id . '&submit=Submit">(3) Analyze Stock</a>';
                        echo '</div>';
                        ?>
                    </div>

                    <?php
                    include('stock_optimization_func.php');
                    $display = generate_stock_table($date, $districtId, $stakeholder, $itm_id, $selProv);
                    echo $display;
                    ?>

                </div>
            </div>  
            <?php
        }
        ?>
    </div>
</div>
</div>
<?php
include PUBLIC_PATH . "/html/footer.php";
?>
<script>
    function showDistricts() {
        $.ajax({
            type: "POST",
            url: '<?php echo APP_URL; ?>dashboard/ajax.php',
            data: {lvl: 3, prov_id: $('#prov_sel').val()},
            success: function (data) {
                $("#td_dist").html(data);
            }
        });
    }
    $(function () {

        if ($("input:checked").length == 0)
        {

            $("#modal_btn").css("display", "none");
        }
        //showDistricts();
        $(document).on('click', '#hide_cols', function () {
            if ($(this).is(":checked"))
            {
                $('.prod_head').each(function () {
                    var prod = $(this).data('itm');
                    var hide = 'true';
                    $('.prod_' + prod).each(function () {
                        if ($(this).data('status') == 'full') {
                            //$(this).css('background-color', 'red');
                            hide = 'false';
                        }
                    });
                    if (hide == 'true') {
                        $(this).html('').html(hide);
                        //$(this).css('background-color', 'red');
                        //$('.prod_'+prod).css('background-color', 'red');
                        $('.prod_' + prod).hide(500);
                        $(this).hide(500);
                    }
                });
                console.log('clicked ');
            } else
            {
                console.log(' off ');
                $('.prod_head').each(function () {
                    $('.prod_' + prod).show(300);
                    $(this).show(300);
                });
            }

        });

        $(document).on('change', '#prov_sel', function () {
            console.log('b');
            var prov = $(this).val();
            console.log('val:' + prov);



            $("#dist_id").children("option[value='']").wrap('<span/>');
            $("#dist_id").attr('required', true);
            console.log('d');


        });

        $(document).on('change', '#lvl', function () {
            var lvl = $(this).val();
            if (lvl == '3')
            {
                $("#td_dist").hide(100);
            } else
            {
                $("#td_dist").show(100);
            }
        });

        $("#modal_btn").click(function () {
            var to_array = [];
            var from_array = [];
            var transferrable = [];
            var trs_qty = [];
            var prod_id;
            var month;
            var dist_id;
            var prov_id;
            var stk_id;
            var wh = [];
            var whto = [];
            var soh = [];
            var mos = [];
            var amc = [];
            var os_qty = [];
            $("input:checked").not("#checkAll").each(function () {

                to_array.push($(this).data('to'));
                from_array.push($(this).data('from'));
                transferrable.push($(this).data('qty'));
                trs_qty.push($(this).data('trs'));
                prod_id = $(this).data('prod');
                month = $(this).data('month');
                dist_id = $(this).data('dist');
                prov_id = $(this).data('prov');
                stk_id = $(this).data('stk');
                wh.push($(this).data('wh'));
                whto.push($(this).data('whto'));
                soh.push($(this).data('soh'));
                mos.push($(this).data('mos'));
                amc.push($(this).data('amc'));
                os_qty.push($(this).data('osqty'));
            });
            console.log(trs_qty);
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>reports/ajax_stock_optimization.php',
                data: {soh: soh, amc: amc, mos: mos, os_qty: os_qty, whto: whto, wh: wh, to_array: to_array, from_array: from_array, transferrable: transferrable, trs_qty: trs_qty, prod_id: prod_id, month: month, dist_id: dist_id, prov_id: prov_id, stk_id: stk_id},
                success: function (data) {
                    $(".modal-body").html(data);
                }
            });

            $('#myModal').modal('show');
        });
        $("#save_btn").click(function () {
            var to_array = [];
            var from_array = [];
            var transferrable = [];
            var trs_qty = [];
            var prod_id;
            var month;
            var dist_id;
            var prov_id;
            var stk_id;
            var wh = [];
            var whto = [];
            var soh = [];
            var mos = [];
            var amc = [];
            var os_qty = [];
            $("input:checked").not("#checkAll").each(function () {

                to_array.push($(this).data('to'));
                from_array.push($(this).data('from'));
                transferrable.push($(this).data('qty'));
                trs_qty.push($(this).data('trs'));
                prod_id = $(this).data('prod');
                month = $(this).data('month');
                dist_id = $(this).data('dist');
                prov_id = $(this).data('prov');
                stk_id = $(this).data('stk');
                wh.push($(this).data('wh'));
                whto.push($(this).data('whto'));
                soh.push($(this).data('soh'));
                mos.push($(this).data('mos'));
                amc.push($(this).data('amc'));
                os_qty.push($(this).data('osqty'));
            });
            console.log(trs_qty);
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>reports/ajax_stock_optimization_action.php',
                data: {soh: soh, amc: amc, mos: mos, os_qty: os_qty, whto: whto, wh: wh, to_array: to_array, from_array: from_array, transferrable: transferrable, trs_qty: trs_qty, prod_id: prod_id, month: month, dist_id: dist_id, prov_id: prov_id, stk_id: stk_id},
                success: function (data) {
                    $('#myModal').modal('toggle');
                    if (data == 1)
                    {
                        toastr.success('Data has been saved successfully');
                    }
                    else{
                        toastr.error('Something went wrong');
                    }
                    
                }
            });

        });
        $("#checkAll").click(function () {
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
        $("input[type='checkbox']").click(function () {
            if ($("input:checked").length == 0)
            {

                $("#modal_btn").css("display", "none");
            } else {
                $("#modal_btn").css("display", "block");
            }
        });

    });

</script>
</body>

</html>