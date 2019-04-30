<?php
//Including Configuration file
include("../includes/classes/Configuration.inc.php");
//Login
Login();


//Including db file
include(APP_PATH . "includes/classes/db.php");
include APP_PATH . "includes/classes/functions.php";
//Including header file
include(PUBLIC_PATH . "html/header.php");
//Including FusionCharts file
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
//Checking year
$date;
$from_date = '';
$default_stk = '1,2,7,73';
$default_itm = '1,5,7,8,9,13';
if (isset($_REQUEST['from_date'])) {

    $from_date = $_REQUEST['from_date'];
}
//echo $from_date;
//exit;
//Getting user_level
$level = $_SESSION['user_level'];
//Getting user_province1
//$province = $_SESSION['user_province1'];
//Getting user_district
$district = $_SESSION['user_district'];
//itemid
$itemId = 1;
//proFilter
$proFilter = 2;
//sel_stk
$sel_stk = '';

$count_dist = 0;
$count_rep_dist = 0;
$so_dist = 0;
$sdo_dist = 0;
$so_sdo = 0;
//echo '<pre>';print_r($months_list);exit;

$province = (!empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '');
$stk_arr = (!empty($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : '');
$itm_arr_request = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');
$hf_arr_request = (!empty($_REQUEST['hf']) ? $_REQUEST['hf'] : '');
$rep_date = (!empty($_REQUEST['from_date']) ? $_REQUEST['from_date'] . '-01' : '');
//print_r($stk_arr);exit;
//        $_POST['from_date'].'-01';
//print_r($province);exit;
//    $province = implode(',', $province_arr);
if (!empty($stk_arr)) {
    $stk = implode(',', $stk_arr);
}
if (!empty($itm_arr_request)) {
    $itm = implode(',', $itm_arr_request);
}
if (!empty($hf_arr_request)) {
    $hf = implode(',', $hf_arr_request);
}
//    $where = '';
//    $where_itm = '';
//    if (count($itm_arr_request) > 1) {
//        $where_itm .= " AND itminfo_tab.itm_id IN(";
//
//        foreach ($itm_arr_request as $item) {
//            $where_itm .= "$item" . ",";
////        echo $stk;
//        }
//
//
//        $where_itm = rtrim($where_itm, ',') . ')';
////echo $where;exit;
//    } else if (count($itm_arr_request) == 1) {
//        $where_itm .= " AND itminfo_tab.itm_id =$itm_arr_request[0]";
////        echo $where;exit;
//    }
////    echo $where_itm;exit;
//    if (count($stk_arr) > 1) {
//        $where .= " AND summary_district.stakeholder_id IN(";
//
//        foreach ($stk_arr as $stk) {
//            $where .= "$stk" . ",";
////        echo $stk;
//        }
//
//
//        $where = rtrim($where, ',') . ')';
////echo $where;exit;
//    } else if (count($stk_arr) == 1) {
//        $where .= " AND summary_district.stakeholder_id =$stk_arr[0]";
////        echo $where;exit;
//    }
// 
$where_clause = "";

//if(!empty($province))   $where_clause .= " AND tbl_warehouse.prov_id in (".$province.")  ";
//if(!empty($stk))        $where_clause .= " AND tbl_warehouse.stkid in (".$stk.")  ";
?>


<style>
    .my_dash_cols{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }
    .my_dashlets{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }

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


    .panel-actions a {
        color:#333;
    }
    .panel-fullscreen {
        display: block;
        z-index: 9999;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        overflow: auto;
    }

</style>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->


    <div class="page-container">
        <?php
//Including top file
        include PUBLIC_PATH . "html/top.php";
//Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                <!--                <div class="row">
                                    <div class="col-md-12">
                                        <div class="tabsbar">
                                            <ul>
                                                <li class="active"><a href="dashboard_cws.php"> <b>Main Dashboard</b></a></li>
                                                <li><a href="shipment.php"> <b>Distribution and SOH</b></a></li>
                                                <li><a href="expiry_schedule.php"> <b>Expiry Status</b></a></li>
                                                <li><a href="stock_summary.php"> <b>Product Summary</b></a></li>
                                                <li><a href="mos.php"> <b>Month of Stock</b></a></li
                                            </ul>
                                        </div>
                                    </div>
                                </div>-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body collapse in">
                                <form name="frm" id="frm" action="" method="get">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Month</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php
                                                                if (isset($_REQUEST['from_date'])) {
                                                                    echo date('Y-m', strtotime($from_date));
                                                                } else {

                                                                    echo date('Y-m');
                                                                }
                                                                ?>" required readonly="true">
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>

                                                <td class="col-md-2">

                                                    <div class="form-group">
                                                        <label class="control-label">Province</label>
                                                        <div class="form-group ">
                                                            <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                                                <option value="">Select</option>
                                                                <?php
                                                                $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                        AND parentid IS NOT NULL
                                                                        AND LocType=2";
//query result
                                                                $rsprov = mysql_query($queryprov);
//                                                                    print_r( $rsprov);exit;
                                                                $prov_name = '';
                                                                $sel = '';

                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if (!isset($_REQUEST['prov_sel'])) {
                                                                        if ($rowprov['prov_id'] == 1) {
                                                                            $prov_name = $rowprov['prov_title'];
//                                                                        echo $prov_name;
                                                                            $sel = "selected='selected'";
                                                                            $province = $rowprov['prov_id'];
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                    }


                                                                    if ($province == $rowprov['prov_id']) {
//                                                                            echo 'checkk';
                                                                        $sel = "selected='selected'";
                                                                        $prov_name = $rowprov['prov_title'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }

                                                                    //Populate prov_sel combo
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </td>

                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Stakeholder</label>
                                                            <select  required name="stakeholder[]" id="stakeholder" class="multiselect-ui form-control input-sm" multiple>
                                                                <?php
                                                                $querystake = "SELECT
                                                                            stakeholder.stkname,
                                                                            stakeholder.stkid
                                                                            FROM
                                                                            stakeholder
                                                                            WHERE
                                                                            stakeholder.stk_type_id = 0 AND
                                                                            stakeholder.lvl = 1 AND
                                                                            stakeholder.is_reporting = 1
                                                                        ";
//query result
                                                                $rsstake = mysql_query($querystake) or die();
                                                                $stk_name = array();
                                                                while ($rowprov = mysql_fetch_array($rsstake)) {
                                                                    if (!isset($_REQUEST['stakeholder'])) {
                                                                        if ($rowprov['stkid'] == 1 || $rowprov['stkid'] == 2 || $rowprov['stkid'] == 7 || $rowprov['stkid'] == 73) {
                                                                            $stk_arr[] = $rowprov['stkid'];
                                                                            $sel = "selected='selected'";
                                                                            $stk_name[$rowprov['stkid']] = $rowprov['stkname'];
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                    }
                                                                    if (in_array($rowprov['stkid'], $stk_arr)) {
                                                                        $sel = "selected='selected'";
                                                                        $stk_name[$rowprov['stkid']] = $rowprov['stkname'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowprov['stkname']; ?></option>
                                                                    <?php
                                                                }
//                                                                print_r($stk_arr);exit;
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="col-md-2">
                                                    <div class="col-md-12">
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
                                                                    if (in_array($rowprov['itm_id'], $itm_arr_request)) {
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
                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Health Facilities</label>
                                                            <select   name="hf[]" id="hf" class="multiselect-ui form-control input-sm" multiple>
                                                                <?php
                                                                $queryhf = "select tbl_hf_type.pk_id,
                            tbl_hf_type.hf_type
                        FROM
                            tbl_hf_type
                        
                                                                        ";
//query result
                                                                print_r($queryhf);
                                                                $rshf = mysql_query($queryhf) or die();

                                                                while ($rowhf = mysql_fetch_array($rshf)) {
                                                                    if (!isset($_REQUEST['hf_type'])) {
                                                                        $sel = "";
                                                                    }
                                                                    if (in_array($rowhf['pk_id'], $hf_arr_request)) {
                                                                        $sel = "selected='selected'";
                                                                        $hf_name[] = $rowhf['hf_type'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $rowhf['pk_id']; ?>" <?php echo $sel; ?>><?php echo $rowhf['hf_type']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-md-2">

                                                    <label class="control-label">&nbsp;</label>
                                                    <input name="submit_btn" type="submit" class="btn btn-succes" value="Go" id="submit_btn" style="margin-top:8%;">

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (isset($_REQUEST['submit_btn']) && $_REQUEST['prov_sel'] != null) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-title   center"> Family Planning Stock Situation in <?php echo $prov_name; ?> </h3>
                            <h4 class="page-title   center"> <?= implode(',', $stk_name) ?> - Reporting month <?php
                    if (isset($_REQUEST['from_date'])) {
                        $month = substr($from_date, 5);
                        $jd = gregoriantojd($month, 01, substr($from_date, 0, 4));
                        echo jdmonthname($jd, 0);
                        echo '-' . substr($from_date, 0, 4);
                    } else {
                        $date = date('Y-m');
                        $month = substr($date, 5);
                        $jd = gregoriantojd($month, 01, substr($date, 0, 4));
                        echo jdmonthname($jd, 0);
                        echo '-' . substr($date, 0, 4);

                        //$month = substr($from_date, 5);
//                                                                    echo date('y,m');
                    }
                    ?> </h4>
                        </div>
                    </div>
                    <?php
                    $margin = '';
                    $col = '';
                    if ($province == 1) {
                        $margin = "";
                    }
                    ?>
                    <div id="general_summary_main_div" class="col-md-12">
                        <div class="widget widget-tabs"  style="border:none;">    
                            <div class="widget-body" style="background-color:aliceblue;">
                                <div class="row " id="top_bar"   style="margin-top:2%;<?php echo $margin; ?>">

                                </div>
                            </div>
                        </div>    
                    </div>

                    <div class="row">
                        <div class="col-md-4 my_dash_cols">
                            <div class="col-md-12 my_dashlets">
                                <div class="dashlet_graph" id="dashboard_stock_a1" href='dashboard_stock_a1.php'></div>
                            </div>

                            <div class="col-md-12 my_dashlets">
                                <div class="dashlet_graph" id="dashboard_stock_a4" href='dashboard_stock_a4.php'></div>
                            </div>



                        </div>
                        <div class="col-md-4 my_dash_cols">
                            <div class="col-md-12 my_dashlets"  >
                                <div class="dashlet_graph" id="dashboard_stock_a2" href='dashboard_stock_a2.php'></div>
                            </div>

                            <div class="col-md-12 my_dashlets">
                                <div class="dashlet_graph" id="dashboard_stock_a5" href='dashboard_stock_a5.php'></div>
                            </div>



                        </div>
                        <div class="col-md-4 my_dash_cols">

                            <div class="col-md-12 my_dashlets">
                                <div class="dashlet_graph" id="dashboard_stock_a3" href='dashboard_stock_a3.php'></div>
                            </div>
                            <div class="col-md-12 my_dashlets"  >
                                <div class="dashlet_graph" id="dashboard_stock_a6" href='dashboard_stock_a6.php'></div>
                            </div>
                        </div>
                    </div>

                <?php } ?>

            </div>
        </div>
    </div>

    <?php
//Including footer file
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?= PUBLIC_URL ?>js/bootstrap_multiselect.js"></script>



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
            background: url('../../public/images/ajax-loader.gif') 50% 50% no-repeat rgb(249,249,249);
        }
        /*.col-md-6{min-height:450px !important;}*/
        #loadingmessage{height:450px !important;}
        #loadingmessage img{margin-top:150px !important;}
        select.input-sm{padding:0px !important;}
    </style>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
    <?php /* ?><script language="Javascript" src="<?php echo PUBLIC_URL;?>js/maps/cyp_dashlet.js"></script>
      <script language="Javascript" src="<?php echo PUBLIC_URL;?>js/maps/dashlet_Interval.js"></script><?php */ ?>
    <script type="text/javascript">
        $(document).on('click', '#submit_btn', function () {
            loadDashlets();
        });

        $(function () {



            $("#top_bar").html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

            $.ajax({
                type: "POST",
                url: '<?php echo APP_URL; ?>dashboard/dashboard_stock_top_bar.php',
                data: $('form').serialize(),
                dataType: 'html',
                success: function (data) {
                    console.log('message check');
                    $("#top_bar").html('');
                    $("#top_bar").html(data);
                }
            });
            loadDashlets();
            $('#from_date').datepicker({
                dateFormat: "yy-mm",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                maxDate: ''
            });
            $('.multiselect-ui').multiselect({
                includeSelectAllOption: true
            });

        })

        function loadDashlets()
        {
            $('.dashlet_graph').each(function (i, obj) {

                var url = $(this).attr('href');
                var id = $(this).attr('id');
                var form_data = $('form').serialize();

                //dataStr = 'month=' + $('#month').val();
                //dataStr += '&year=' + $('#year').val();
                //dataStr += '&lvl=' + $('#ofc_level').val();
                //dataStr += '&proFilter=' + $('#product_filter').val();
                //dataStr += '&stkId=' + stkId;
                //dataStr += '&sector=0';
                console.log('id is ' + id);
                $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

                $.ajax({
                    type: "POST",
                    url: '<?php echo APP_URL; ?>dashboard/' + url,
                    data: form_data,
                    dataType: 'html',
                    success: function (data) {
                        $("#" + id).html(data);
                    }
                });

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