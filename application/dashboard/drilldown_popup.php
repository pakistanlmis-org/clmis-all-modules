<?php

//Including Configuration file
include("../includes/classes/Configuration.inc.php");

include(APP_PATH . "includes/classes/db.php");
//Including header file
//include(PUBLIC_PATH . "html/header.php");
//Including FusionCharts file
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");

?>
   
<html>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
       

        <div class="page-content-wrapper">
            <div class="page-content">
                
                <div class="container-fluid">
                      
                        <div class="row">
                                
                                <div class="col-md-6 my_dash_cols">
                                    <div class="col-md-12 "  style=" ">
                                        <div class="dashlet_graph" id="dashboard_ss_b3" href='drilldown_stk_1.php'></div>
                                    </div> 
                                    
                                </div>
                                
                        </div>
                        
                        
                </div>
                
                
               
            </div>
        </div>
    </div>

    <?php 
    //Including footer file
     ?>

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
    <?php /* ?><script language="Javascript" src="<?php echo PUBLIC_URL;?>js/maps/cyp_dashlet.js"></script>
      <script language="Javascript" src="<?php echo PUBLIC_URL;?>js/maps/dashlet_Interval.js"></script><?php */ ?>
    <script type="text/javascript">
		
		$(function() {
                   
		})
                $( document ).ready(function() {
                     alert('hi');
			loadDashlets();
                });
                
                 
                function showDrillDown(prov,prov_name,from_date,stk,stk_name) {
                   
                   window.open("drilldown_stk_1.php?province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name ,"", "width=800,height=800");
                  }
              
              
              
              
		function loadDashlets(stkId='1')
		{
                    alert('load');
			$('.dashlet_graph').each(function(i, obj) {
				
				var url = $(this).attr('href');
				var id = $(this).attr('id');
				alert(url);
                                var dataStr='';
                                dataStr += 'province=' + $('#province').val();
                                dataStr += '&prov_name=' + $('#prov_name').val();
                                dataStr += '&from_date=' + $('#from_date').val();
                                dataStr += '&to_date=' + $('#to_date').val();
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
    </script>
</body>
<!-- END BODY -->
</html>