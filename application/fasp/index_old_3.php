<?php
/**
 * index
 * @package fasp
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");

?>
<link href="demo.css" rel="stylesheet">
<link href="introjs.css" rel="stylesheet">
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im	
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row well well-dark">
                   <div class="portlet   dark center">
                        <h1 >Forecasting & Quantification</h1> 
                        <h4 >(Family Planning)</h4> 
                        <a class="btn btn-large btn-success" href="javascript:void(0);" onclick="javascript:introJs().start();">Show me what is this module about ?</a>
                        

                   </div>
                </div>
                <div class="row well " id="fp_div" style="">
                    <div class="col-md-12 btn justify">Family Planning</div>
                    <div class="col-md-12">
                    <div class="tiles">
                        
                                      <div data-step="1" data-intro="Enter Province Wise Demographic Data of base years."  class="tile double-down bg-green">
                                          <a href="demographics_data_entry.php">
                                              <div class="tile-body">
                                                      <i class="fa fa-bell-o1"><img width="120px" src="images/dmg.png"></i>
                                              </div>
                                              <div class="tile-object">
                                                      <div class="name">
                                                               Demographics
                                                      </div>
                                                      <div class="number">

                                                      </div>
                                              </div>
                                          </a>
                                      </div>
                                      <div data-step="2" data-intro="Set master data for the forecasting calculation you are planning."  class="tile double bg-yellow-crusta">
                                          <a href="forecasting_master.php?product_category=fp">
                                              <div class="tile-body">
                                                      <i class="fa fa-plus1"><img width="70px" src="images/add.png"></i>
                                              </div>
                                              <div class="tile-object">
                                                      <div class="name">
                                                               New Forecast
                                                      </div>
                                                      <div class="number">

                                                      </div>
                                              </div>
                                          </a>
                                      </div>

                              

                                              <div data-step="3" data-intro="This form is used for forecasting calculations and adjustments . It will generate a proposed forecasting result." class="tile double-down  bg-yellow-lemon">
                                                  <a href="forecasting_list.php">
                                                      <div class="tile-body"><i class="fa fa-bar-chart-o1"><img width="120px" src="images/fc.png"></i>
                                                      </div>
                                                      <div class="tile-object">
                                                              <div class="name">
                                                                       Forecasting Calculations/Adjustments
                                                              </div>
                                                              <div class="number">

                                                              </div>
                                                      </div>
                                                  </a>
                                              </div>
                                              <div data-step="4" data-intro="Quantification Form fetches the proposed forecasting result. Fetches Stock on hand , pipeline , and consumption data from cLMIS . And uses this data for quantifying against the prices of units." class="tile double-down bg-blue-madison">
                                                  <a href="forecasting_list.php">
                                                      <div class="tile-body">
                                                          <i class="fa fa-flask1"><img width="100px" src="images/qnt.png"></i>
                                                      </div>
                                                      <div class="tile-object">
                                                              <div class="name">
                                                                       Quantification Form
                                                              </div>
                                                              <div class="number">

                                                              </div>
                                                      </div>
                                                  </a>
                                              </div>

                                
                        </div>
                        
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    

    <script src="../../public/js/jquery-1.4.4.js" type="text/javascript"></script>

    <script type="text/javascript" src="intro.js"></script>
    <script>
        $('#mnch_btn').click(function(){
            var x = document.getElementById("mnch_div");
            x.style.display = "block";
            document.getElementById("fp_div").style.display = "none";
            $(this).removeClass("btn-default").addClass("btn-success");
            $('#fp_btn').removeClass("btn-success").addClass("btn-default");
        });
        $('#fp_btn').click(function(){
            var x = document.getElementById("fp_div");
            x.style.display = "block";
            document.getElementById("mnch_div").style.display = "none";
            $(this).removeClass("btn-default").addClass("btn-success");
            $('#mnch_btn').removeClass("btn-success").addClass("btn-default");
        });
    </script>
</body>
</html>