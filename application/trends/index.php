<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/Configuration.inc.php");
Login();

//echo '<pre>';print_r($_SESSION);exit;
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
if (date('d') > 10) {
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
$sel_month = date('m', strtotime($date));
$sel_year = date('Y', strtotime($date));
$sel_stk = $sel_prov = $sel_dist = $sel_wh = $stkName = $provName = $distName = $whName = $where = $where1 = $where2 = $lvl = $whid = '';
$colspan = $header = $header1 = $header2 = $lvl = $width = $colAlign = $colType = $xmlstore = '';

?>
<style>
    .panel-actions {
  margin-top: -20px;
  margin-bottom: 0;
  text-align: right;
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
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                
                <div class="container-fluid">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-title row-br-b-wp">Trend Lines (PWD Only - Prototype)</h3>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <a class="btn btn-sm purple btn_load_dashlet" data-dashlet="a1">1-National Consumption</a>
                            <a class="btn btn-sm purple btn_load_dashlet" data-dashlet="a2">2-SOH National Store</a>
                            <a class="btn btn-sm purple btn_load_dashlet" data-dashlet="a3">3-No of SDPs Stock Out Over Time</a>
                            <a class="btn btn-sm purple btn_load_dashlet" data-dashlet="a4">4-No of SDPs Stock Out Over Time (Ignoring Individuals/Others)</a>
                            <a class="btn btn-sm purple btn_load_dashlet" data-dashlet="a5">5-MOS at CWH</a>
                            <a class="btn btn-sm purple btn_load_dashlet" data-dashlet="a6">6-Province Wise Avg Reporting Rate</a>
                            <a class="btn btn-sm purple btn_load_dashlet" data-dashlet="a7">7-District Wise Avg Reporting Rate</a>

                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                           
                            <div class="portlet portlet-sortable light bg-inverse">
                                    <div class="portlet-title">
                                            <div class="caption">
                                                <i class="icon-puzzle font-red-flamingo"></i>
                                                <span class="caption-subject bold font-red-flamingo uppercase">Tools </span>
                                                <span class="caption-helper">actions...</span>
                                            </div>
                                            <div class="tools">
                                                <a href="" class="collapse"></a>
                                                <a href="" class="fullscreen"></a>
                                                <a href="" class="remove"></a>
                                                <a href="#" role="button " class="fullscreen" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a>
                                            </div>
                                    </div>
                                    <div class="portlet-body">
                                            <div class="dashlet_graph" id="dashlet_a6" href='trends_a6.php'></div>
                                    </div>
                            </div>
                            

                        </div>
                    </div>
                        <div class="row">
                                <div class="col-md-12 my_dash_cols">
                                    <div class="col-md-12 ">
                                        <div class="dashlet_graph1" id="dashlet_a1" href='trends_a1.php'></div>
                                    </div>
                                </div>
                                <div class="col-md-12 my_dash_cols">
                                    <div class="col-md-12 ">
                                        <div class="dashlet_graph1" id="dashlet_a2" href='trends_a2.php'></div>
                                    </div>
                                </div>
                                <div class="col-md-12 my_dash_cols">
                                    <div class="col-md-12 ">
                                        <div class="dashlet_graph1" id="dashlet_a3" href='trends_a3.php'></div>
                                    </div>
                                </div>
                                <div class="col-md-12 my_dash_cols">
                                    <div class="col-md-12 ">
                                        <div class="dashlet_graph1" id="dashlet_a4" href='trends_a4.php'></div>
                                    </div>
                                </div>
                                <div class="col-md-12 my_dash_cols">
                                    <div class="col-md-12 ">
                                        <div class="dashlet_graph1" id="dashlet_a5" href='trends_a5.php'></div>
                                    </div>
                                </div>
                                <div class="col-md-12 my_dash_cols">
                                    <div class="col-md-12 ">
                                        <div class="dashlet_graph" id="dashlet_a7" href='trends_a7.php'></div>
                                    </div>
                                </div>
                            
                        </div>
                        
                </div>
            </div>
        </div>
    </div>

    <?php 
    //Including footer file
    include PUBLIC_PATH . "/html/footer.php"; ?>

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
    //Toggle fullscreen
    $(".fullscreen").click(function (e) {
        e.preventDefault();
        
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('.portlet').toggleClass('panel-fullscreen');
    });
});

                $(function() {
			if(!$('#accordion').hasClass('page-sidebar-menu-closed'))
                        {
                            $(".sidebar-toggler").trigger("click");
                        }
		})
                
                
		$(function() {
			//loadDashlets();

                        if(!$('#accordion').hasClass('page-sidebar-menu-closed'))
                        {
                            $(".sidebar-toggler").trigger("click");
                        }
                        
                        $('.btn_load_dashlet').click(function(){
                            var dashlet = $(this).data('dashlet');
                            load_this_dashlet('dashlet_'+dashlet);
                        });
                       
                       
                        
		})
                function load_this_dashlet(id){
                    
                    var url = $('#'+id).attr('href');
                    var id = $('#'+id).attr('id');

                    var dataStr='';
                    $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");
                    $.ajax({
                        type: "POST",
                        url: '<?php echo APP_URL; ?>trends/' + url,
                        data: dataStr,
                        dataType: 'html',
                        success: function(data) {
                                $("#" + id).html(data);
                        }
                    });
                }
                
		function loadDashlets(stkId='1')
		{
			$('.dashlet_graph').each(function(i, obj) {
				
				var url = $(this).attr('href');
				var id = $(this).attr('id');
				
                                var dataStr='';
                                dataStr += 'province=' + $('#province').val();
                                //dataStr += '&prov_name=' + $('#prov_name').val();
                                dataStr += '&from_date=' + $('#report_year').val()+'-'+ $('#report_month').val()+'-01';
                                //dataStr += '&to_date=' + $('#to_date').val();
                                dataStr += '&dist=' + $('#district_id').val();
                                //dataStr += '&dist_name='    + $('#dist_name').val();
                                dataStr += '&stk='          + $('#stk_sel').val();
                                dataStr += '&products='     + $('#products').val();
                                dataStr += '&warehouse='    + $('#warehouse_id').val();

                                $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

                                $.ajax({
                                        type: "POST",
                                        url: '<?php echo APP_URL; ?>trends/' + url,
                                        data: dataStr,
                                        dataType: 'html',
                                        success: function(data) {
                                                $("#" + id).html(data);
                                        }
                                });
				
			});
                        
                        
		}
                
    </script>
    
    <script>
        $(function() {
            showDistricts('<?php echo $sel_prov; ?>', '<?php echo $sel_stk; ?>');
            showStores('<?php echo $sel_dist; ?>');

            $('#province, #stk_sel').change(function(e) {
                $('#district').html('<option value="">All</option>');
                $('#warehouse').html('<option value="">Select</option>');
                showDistricts($('#province').val(), $('#stk_sel').val());
            });
            $('#stk_sel').change(function(e) {
                $('#warehouse').html('<option value="">All</option>');
            });

            $(document).on('change', '#province, #stk_sel, #district', function() {
                showStores($('#district option:selected').val());
            })
        })
        function showDistricts(prov, stk) {
            if (stk != '' && prov != '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'my_report_ajax.php',
                    data: {provId: prov, stkId: stk, distId: '<?php echo $sel_dist; ?>', showAll: 1},
                    success: function(data) {
                        $("#districts").html(data);
                    }
                });
            }
        }
        function showStores(dist) {
            var stk = $('#stk_sel').val();
            if (stk != '' && dist != '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'my_report_ajax.php',
                    data: {distId: dist, stkId: stk, whId: '<?php echo $sel_wh; ?>'},
                    success: function(data) {
                        $("#stores").html(data);
                    }
                });
            }
        }
    </script>
    
</body>
<!-- END BODY -->
</html>