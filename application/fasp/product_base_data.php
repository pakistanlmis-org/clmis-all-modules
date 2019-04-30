<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/Configuration.inc.php");
Login();

//echo '<pre>';print_r($_SESSION);exit;
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
 

$master_id = $_REQUEST['master_id'];
    $qry = "SELECT
                fq_master_data.pk_id,
                fq_master_data.base_year,
                fq_master_data.start_year,
                fq_master_data.end_year,
                fq_master_data.purpose,
                fq_master_data.source,
                fq_master_data.stakeholders,
                fq_master_data.province_id,
                fq_master_data.level,
                fq_master_data.district_id,
                fq_master_data.forecasting_methods,
                fq_master_data.created_by,
                fq_master_data.modified_by,
                fq_master_data.created_at,
                fq_master_data.modified_at,
                fq_master_data.item_group
            FROM
                fq_master_data
            WHERE
                fq_master_data.pk_id = ".$_REQUEST['master_id']."
            ";
    $rsQry = mysql_query($qry) or die();
    $master_data = array();
    $master_data = mysql_fetch_array($rsQry);


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
                
                <div class="container-fluid">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-title row-br-b-wp">Base Data Selection for <?=$_REQUEST['prod_name']?></h3>

                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            Select the base years to view Monthly Consumption
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-md-12">
                                    <div class="row">
                                            <div class="col-md-6">
                                                
                                                    <div class="row">
                                                            <div class="col-md-4">
                                                                Base Year 1: 
                                                            </div>
                                                            <div class="col-md-8">
                                                                <select   required name="base_year" id="base_year" class="form-control input-sm">
                                                                    <option value="" >Select</option>
                                                                    <?php
                                                                    for ($j = date('Y'); $j >= 2005; $j--) {
                                                                        if ($selYear == $j) {
                                                                            //$sel = "selected='selected'";
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        $fiscal_year = $j.'-'.substr($j+1,2);
                                                                        ?>
                                                                        <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $fiscal_year; ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <a class="btn btn-sm purple btn_load_dashlet hide" data-dashlet="a1">Show</a>
                                                            </div>
                                                    </div>
                                                    <div class="row">
                                                            <div class="col-md-4">
                                                                Base Year 2: 
                                                            </div>
                                                            <div class="col-md-8">
                                                                <select   required name="base_year" id="base_year" class="form-control input-sm">
                                                                    <option value="" >Select</option>
                                                                    <?php
                                                                    for ($j = date('Y'); $j >= 2005; $j--) {
                                                                        if ($selYear == $j) {
                                                                            //$sel = "selected='selected'";
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        $fiscal_year = $j.'-'.substr($j+1,2);
                                                                        ?>
                                                                        <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $fiscal_year; ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <a class="btn btn-sm purple btn_load_dashlet hide" data-dashlet="a1">Show</a>
                                                            </div>
                                                    </div>
                                                    <div class="row">
                                                            <div class="col-md-4">
                                                                Base Year 3: 
                                                            </div>
                                                            <div class="col-md-8">
                                                                 <select   required name="base_year" id="base_year" class="form-control input-sm">
                                                                    <option value="" >Select</option>
                                                                    <?php
                                                                    for ($j = date('Y'); $j >= 2005; $j--) {
                                                                        if ($selYear == $j) {
                                                                            $sel = "selected='selected'";
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        $fiscal_year = $j.'-'.substr($j+1,2);
                                                                        ?>
                                                                        <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $fiscal_year; ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <a class="btn btn-sm purple btn_load_dashlet " data-dashlet="a1">Show</a>
                                                            </div>
                                                    </div> 
                                            </div>
                                            <div class="col-md-10">
                                                    <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="dashlet_graph1" id="dashlet_a1" href='product_base_data_graph.php?dashlet=1'>
                                                                    <img width="100%" src="images/blurred_trendline.png">
                                                                </div>
                                                            </div>
                                                    </div>
                                            </div>
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
                    var arr = $('select').map(function(){
                                    return this.value
                                }).get();
                    dataStr = '&prod_id=<?=$_REQUEST['prod_id']?>&master_id=<?=$_REQUEST['master_id']?>&stakeholders=<?=$master_data['stakeholders']?>&level=<?=$master_data['level']?>&prov_id=<?=$master_data['province_id']?>&dist_id=<?=$master_data['district_id']?>&years='+arr+'';           
                     JSON.stringify(arr);
                      
                    $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");
                    $.ajax({
                        type: "POST",
                        url: '<?php echo APP_URL; ?>fasp/' + url,
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
    
    
</body>
<!-- END BODY -->
</html>