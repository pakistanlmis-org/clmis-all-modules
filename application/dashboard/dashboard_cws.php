<?php

//Including Configuration file
include("../includes/classes/Configuration.inc.php");
//Login
Login();
//Including db file
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
//Including FusionCharts file
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
//Checking year
if (!isset($_POST['year'])) {
    if (date('d') > 10) {
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m'))));
    } else {
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m'))));
    }
}
//month
$month = date('m', strtotime($date));
//year
$year = date('Y', strtotime($date));
//Getting user_level
$level = $_SESSION['user_level'];
//Getting user_province1
$province = $_SESSION['user_province1'];
//Getting user_district
$district = $_SESSION['user_district'];
//itemid
$itemId = 1;
//proFilter
$proFilter = 2;
//sel_stk
$sel_stk = '';
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="tabsbar">
                            <ul>
                                <li class="active"><a href="dashboard_cws.php"> <b>Main Dashboard</b></a></li>
                                <li><a href="shipment.php"> <b>Distribution and SOH</b></a></li>
                                <li><a href="expiry_schedule.php"> <b>Expiry Status</b></a></li>
                                <li><a href="stock_summary.php"> <b>Product Summary</b></a></li>
                                <!--<li><a href="mos.php"> <b>Month of Stock</b></a></li-->
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp center"> Central Warehouse & Supplies - FP Products Transactional Dashboard </h3>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 my_dash_cols">
                         <div class="col-md-12 my_dashlets">
                            <div class="dashlet_graph" id="dashboard_cws_b1" href='dashboard_cws_b1.php' ></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 my_dash_cols">
                        
                        <div class="col-md-12 my_dashlets">
                            <div class="dashlet_graph" id="dashboard_cws_b2" href='dashboard_cws_b2.php'></div>
                        </div>
                        <div class="col-md-12 my_dashlets">
                            <div class="dashlet_graph" id="dashboard_cws_c1" href='dashboard_cws_c1.php'></div>
                        </div>
                       <div class="col-md-12 my_dashlets">
                             <div class="dashlet_graph" id="dashboard_cws_c2" href='dashboard_cws_c2.php'></div>
                        </div>
                        
                    </div>
                    <div class="col-md-6 my_dash_cols">
                       
                        <div class="col-md-12 my_dashlets"  >
                            <div class="dashlet_graph" id="dashboard_cws_a2" href='dashboard_cws_a2.php'></div>
                        </div>
                        
                        <div class="col-md-12 my_dashlets">
                            <div class="dashlet_graph" id="dashboard_cws_b3" href='dashboard_cws_b3.php'></div>
                        </div>
                       
                        <div class="col-md-12 my_dashlets"  >
                           <div class="dashlet_graph" id="dashboard_cws_a1" href='dashboard_cws_a1.php'></div>
                        </div>
                    </div>
                </div>
                
                
               
            </div>
        </div>
    </div>

    <?php 
    //Including footer file
    include PUBLIC_PATH . "/html/footer.php"; ?>

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
		
		$(function() {
			loadDashlets();
		})

		function loadDashlets(stkId='1')
		{
			$('.dashlet_graph').each(function(i, obj) {
				
				var url = $(this).attr('href');
				var id = $(this).attr('id');
				
                                var dataStr;
                                //dataStr = 'month=' + $('#month').val();
                                //dataStr += '&year=' + $('#year').val();
                                //dataStr += '&lvl=' + $('#ofc_level').val();
                                //dataStr += '&proFilter=' + $('#product_filter').val();
                                //dataStr += '&stkId=' + stkId;
                                //dataStr += '&sector=0';

                                $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

                                $.ajax({
                                        type: "POST",
                                        url: '<?php echo APP_URL; ?>dashboard/' + url,
                                        data: dataStr,
                                        dataType: 'html',
                                        success: function(data) {
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
				success: function(data) {
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
				success: function(data) {
					$("#districtArea").html(data);
				}
			});
		}
		function showProvinces1(provId) {
			$.ajax({
				type: "POST",
				url: '<?php echo APP_URL; ?>dashboard/ajax.php',
				data: {lvl: 2, provId: provId},
				success: function(data) {
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
				success: function(data) {
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
				success: function(data) {
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
				success: function(data) {
					$("#stock-" + tabId).html(data);
				}
			});
		}
    </script>
</body>
<!-- END BODY -->
</html>