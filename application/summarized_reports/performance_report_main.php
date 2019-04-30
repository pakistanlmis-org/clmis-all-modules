<?php
/**
 * quarterly-rate
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");



$report_type    = (isset($_REQUEST['report_type'])?$_REQUEST['report_type']:'');
$sel_month      = (isset($_REQUEST['ending_month'])?$_REQUEST['ending_month']:'');
$selYear       = (isset($_REQUEST['year_sel'])?$_REQUEST['year_sel']:'');
$quarter        = (isset($_REQUEST['quarter'])?$_REQUEST['quarter']:'');
$sel_item       = (isset($_REQUEST['prod_sel'])?$_REQUEST['prod_sel']:'');
$sel_stk        = (isset($_REQUEST['stk_sel'])?$_REQUEST['stk_sel']:'');
$sel_prov       = (isset($_REQUEST['prov_sel'])?$_REQUEST['prov_sel']:'');
$selDist        = (isset($_REQUEST['dist'])?$_REQUEST['dist']:'');
?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp"> Performance Reports</h3>
                        
                        <?php
							include("performance_report_filter.php");
						?>
						
						
                    </div>
                </div>
                
                
                <div class="row">
                    <div class="col-md-12">
                        
                        <?php
                        
                            if($report_type=='compliance_report_dist')
                            {
                                include "compliance.php";
                            }
                            elseif($report_type=='compliance_report_hf')
                            {
                                include "compliance_hf.php";
                            }
                            elseif($report_type=='q_reporting_rate')
                            {
                                include "quarterly_rate.php";
                            }
                            elseif($report_type=='p_reporting_rate')
                            {
                                include "province_rate.php";
                            }
                            elseif($report_type=='non_rep_districts')
                            {
                                
                            }
                            elseif($report_type=='inv_turnover')
                            {
                                
                            }
                        ?>			
                    </div>
                </div>
               
            </div>
        </div>
        <!-- END FOOTER -->
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
//include reports_includes
include PUBLIC_PATH . "/html/reports_includes.php";
?>
        
</body>
<!-- END BODY -->

<script>
    function showDistricts()
    {
        var provinceId = $('#prov').val();
        $.ajax({
            url: 'ajax_calls.php',
            type: 'POST',
            data: {provinceId: provinceId, validate: 'no', dId: '', stkId: ''},
            success: function (data) {
                $('#dist').html(data);
            }
        });
    }
        
    $('#report_type').on('change',function(){
        var rep_id = $(this).val();
        if(rep_id == 'q_reporting_rate' )
        {
            $('.filter1').hide();
            $('#td_quarter').show();
        }
        else if(rep_id == 'p_reporting_rate' )
        {
            $('.filter1').hide();
            $('#td_month').show();
        }
        else if(rep_id == 'compliance_report_dist')
        {
            $('.filter1').hide();
            $('#td_month').show();
            $('#td_prov').show();
            $('#td_stk').show();
        }
        else if(rep_id == 'compliance_report_hf')
        {
            $('.filter1').hide();
            $('#td_month').show();
            $('#td_prov').show();
            $('#td_dist').show();
            $('#td_stk').show();
        }
        else if(rep_id == 'non_rep_districts')
        {
            $('.filter1').hide();
            $('#td_month').show();
        }
        else if(rep_id == 'inv_turnover')
        {
            $('.filter1').hide();
            $('#td_month').show();
        }
        
    })
</script>
</html>