<?php
include("application/includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
include(PUBLIC_PATH . "html/header.php");

$salt = 'jboFHjeQK5mc1K0cdSz5';
$token = sha1(md5($salt . date('Y-m-d')));

if ($token != $_GET['token']) {
    echo "Invalid Token.";
    exit;
}

if (!isset($_POST['year'])) {
    if (date('d') > 10) {
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m'))));
    } else {
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m'))));
    }
}
$month = date('m', strtotime($date));
$year = date('Y', strtotime($date));
$level = 1;
$province = 1;
$district = '';
$itemId = 1;
$proFilter = 2;
?>
<style>
    .widget-head ul{padding-left:0px !important;}
    #map{width:100%;height:390px;position: relative}
    #loader{display:none;width: 70px;height: 70px;position:absolute;left:45%;top:40%;z-index: 2000}
    #inputForm{width:50%;height:25px;position: absolute;top:4px;left:10%;z-index: 2000}
    #mapTitle{position:absolute;top:24%;left:2%;width:150px;height:15px;text-align:center;}
    #legendDiv{display:none;position:absolute;padding:2px;border-radius:6px;font-size:8px;background-color:none;border:1px solid black;width:auto;height:auto;top:57%;left:70%;z-index: 3000;}
    .pageLoader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url('public/images/ajax-loader.gif') 50% 50% no-repeat rgb(249,249,249);
    }
    /*.col-md-6{min-height:450px !important;}*/
    #loadingmessage{height:450px !important;}
    #loadingmessage img{margin-top:150px !important;}
    select.input-sm{padding:0px !important;}
    .page-content-wrapper .page-content{margin-left: 0px !important;}
</style>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>
<!--[if IE]>
<style type="text/css">
    .box { display: block; }
    #box { overflow: hidden;position: relative; }
    b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
</style>

</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->

    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <form name="frm" id="frm" action="" method="post">
                            <div class="col-md-1">
                                <label for="month">Month</label>
                                <div class="form-group">
                                    <select name="month" id="month" class="form-control input-sm">
                                        <?php
                                        for ($i = 1; $i <= 12; $i++) {
                                            if ($month == $i)
                                                $sel = "selected='selected'";
                                            else
                                                $sel = "";
                                            ?>
                                            <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <label for="year">Year</label>
                                <div class="form-group">
                                    <select name="year" id="year" class="form-control input-sm" style="width:60px;">
                                        <?php
                                        for ($j = date('Y'); $j >= 2010; $j--) {
                                            if ($year == $j)
                                                $sel = "selected='selected'";
                                            else
                                                $sel = "";
                                            ?>
                                            <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="office-Level">Office Level</label>
                                <div class="form-group">
                                    <select name="ofc_level" id="ofc_level" class="form-control input-sm">
                                        <option></option>
                                        <option value="1" <?php echo ($level == 1) ? 'selected="selected"' : ''; ?>>National</option>
                                        <option value="2" <?php echo ($level == 2) ? 'selected="selected"' : ''; ?>>Provincial</option>
                                        <option value="3" <?php echo ($level == 3) ? 'selected="selected"' : ''; ?>>District</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2" id="provinceArea" style="display:none;"></div>
                            <div class="col-md-2" id="districtArea" style="display:none;"></div>
                            <div class="col-md-2">
                                <label for="office-Level">Product</label>
                                <div class="form-group">
                                    <select name="product_filter" id="product_filter" class="form-control input-sm">
                                        <option value="1" <?php echo ($proFilter == 1) ? 'selected="selected"' : ''; ?>>All With Condom</option>
                                        <option value="2" <?php echo ($proFilter == 2) ? 'selected="selected"' : ''; ?>>All Without Condom</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="firstname">&nbsp;</label>
                                <div class="form-group">
                                    <button type="button" id="search" name="search" value="search" class="btn btn-primary input-sm">Go</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>

                <div class="widget widget-tabs">
                    <!-- Tabs Heading -->
                    <div class="widget-head" style="border-bottom:0;">
                        <ul>
                            <?php
                            // Get Stakeholders
                            $stk = "SELECT DISTINCT
								MainStk.stkid,
								MainStk.stkname
							FROM
								stakeholder
							INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
							WHERE
								stakeholder.stk_type_id = 0
							AND MainStk.stkid NOT IN(92, 74)
							ORDER BY
								MainStk.stkorder ASC";
                            $stkQuery = mysql_query($stk);
                            $stkQuery1 = mysql_query($stk);
                            $counter = 1;
                            while ($row = mysql_fetch_array($stkQuery)) {
                                $active = ($counter == 1) ? 'class="active"' : '';
                                $counter++;
                                ?>
                                <li <?php echo $active; ?>><a onClick="selectStk(<?php echo $counter; ?>,<?php echo $row['stkid']; ?>)" href="#stock-status-<?php echo $counter; ?>" data-toggle="tab"><?php echo $row['stkname']; ?></a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <!-- // Tabs Heading END -->
                </div>
                <div class="row">
                    <div class="col-md-6" id="dashlet1" href='stock_status.php'></div>
                    <div class="col-md-6" id="dashlet2" href='shipment_main_dash.php'></div>
                </div>
                <div class="row">
                    <div class="col-md-6" id="dashlet3" href='consumption.php'></div>
                    <div class="col-md-6" id="dashlet4" href='cyp.php'></div>
                </div>            
            </div>
        </div>
    </div>

    <a class="btn btn-primary" data-toggle="modal" href="#modal-simple" id="modalId" style="display:none;">Modal</a>
    <div class="modal fade" id="modal-simple" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">         
                <!-- Modal body -->
                <div class="modal-body" id="modalData" style="height:70% !important; overflow:scroll; overflow-x:hidden;"></div>
                <!-- // Modal body END -->
                <!-- Modal footer -->
                <div class="modal-footer"> <a href="#" class="btn btn-default" data-dismiss="modal">Close</a> </div>
            </div>
        </div>
        <!-- // Modal footer END -->
    </div>
    <?php
    $hideDesc = true;
    include PUBLIC_PATH . "/html/footer.php";
    ?>

    <script type="text/javascript">
        /*$(window).load(function() {
         $(".pageLoader").fadeOut("slow");
         })*/

        function selectStk(tabId, stkId)
        {
            $('a[href="#consumption-' + tabId + '"]').trigger('click');
            $('a[href="#CYP-' + tabId + '"]').trigger('click');
            loadDashlets(stkId);
            return false;
        }

        $(function () {
            if ($('#ofc_level').val() == 1)
            {
                loadDashlets();
                $("#provinceArea").hide();
                $("#districtArea").hide();
            }
            if ($('#ofc_level').val() == 2)
            {
                showProvinces1('<?php echo $province; ?>');
                $("#provinceArea").show();
                $("#districtArea").hide();
            }
            if ($('#ofc_level').val() == 3)
            {
                showProvinces1('<?php echo $province; ?>');
                $("#provinceArea").show();
                $("#districtArea").show();
            }

            // When search button is clicked
            $('#search').click(function (e) {
                loadDashlets();
            });
            // Show provinces
            $('#ofc_level').change(function (e) {
                var lvl = $('#ofc_level').val();
                if (lvl == 1)
                {
                    $("#provinceArea").hide();
                    $("#districtArea").hide();
                }
                if (lvl == 2)
                {
                    showProvinces();
                    $("#provinceArea").show();
                    $("#districtArea").hide();
                }
                if (lvl == 3)
                {
                    showProvinces();
                    $("#provinceArea").show();
                    $("#districtArea").show();
                }
            });

        })

        function loadDashlets(stkId = '1')
        {
            $('.col-md-6').each(function (i, obj) {
                $('.widget-head ul li').removeClass('active');
                $('a[href="#stock-status-2"]').parent('li').addClass('active');

                var url = $(this).attr('href');
                var id = $(this).attr('id');
                if (id != 'dashlet6')
                {
                    var dataStr;
                    dataStr = 'month=' + $('#month').val();
                    dataStr += '&year=' + $('#year').val();
                    dataStr += '&lvl=' + $('#ofc_level').val();
                    dataStr += '&proFilter=' + $('#product_filter').val();
                    dataStr += '&stkId=' + stkId;
                    if ($('#ofc_level').val() == 2)
                    {
                        dataStr += '&prov_id=' + $('#prov_id').val();
                    }
                    if ($('#ofc_level').val() == 3)
                    {
                        dataStr += '&dist_id=' + $('#dist_id').val();
                    }
                    dataStr += '&sector=0';

                    $('#' + id).html("<center><div id='loadingmessage'><img src='public/images/ajax-loader.gif'/></div></center>");

                    $.ajax({
                        type: "POST",
                        url: '<?php echo APP_URL; ?>dashboard/' + url,
                        data: dataStr,
                        dataType: 'html',
                        success: function (data) {
                            //setTimeout(function () {
                            $("#" + id).html(data);
                            //}, 5000);
                        }
                    });
                }
            });
        }
        function showProvinces() {
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                data: {lvl: 2},
                success: function (data) {
                    $("#provinceArea").html(data);
                    showDistricts();
                }
            });
        }

        function showDistricts() {
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                data: {lvl: 3, prov_id: $('#prov_id').val()},
                success: function (data) {
                    $("#districtArea").html(data);
                }
            });
        }
        function showProvinces1(provId) {
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                data: {lvl: 2, provId: provId},
                success: function (data) {
                    $("#provinceArea").html(data);
                    showDistricts1('<?php echo $district; ?>');
                }
            });
        }

        function showDistricts1(distId) {
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                data: {lvl: 3, prov_id: $('#prov_id').val(), distId: distId},
                success: function (data) {
                    $("#districtArea").html(data);
                    loadDashlets();
                }
            });
        }
        function showData(param) {
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/ajax.php',
                data: {stockStatus: param},
                success: function (data) {
                    $("#modalData").html(data);
                }
            });
            $('#modalId').trigger('click');
        }
        function loadGraph(stkId, type, tabId)
        {
            var dataStr;
            dataStr = 'month=' + $('#month').val();
            dataStr += '&year=' + $('#year').val();
            dataStr += '&lvl=' + $('#ofc_level').val();
            dataStr += '&proFilter=' + $('#product_filter').val();
            if ($('#ofc_level').val() == 2)
            {
                dataStr += '&prov_id=' + $('#prov_id').val();
            }
            if ($('#ofc_level').val() == 3)
            {
                dataStr += '&dist_id=' + $('#dist_id').val();
            }
            dataStr += '&sector=0';
            dataStr = dataStr + '&stkId=' + stkId + '&type=' + type;
            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/stock_status_ajax.php',
                data: dataStr,
                success: function (data) {
                    $("#stock-" + tabId).html(data);
                }
            });
        }
    </script>
</body>
<!-- END BODY -->
</html>