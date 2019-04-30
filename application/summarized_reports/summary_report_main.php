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
$sel_month      = (isset($_REQUEST['month_sel'])?$_REQUEST['month_sel']:'');
$sel_year       = (isset($_REQUEST['year_sel'])?$_REQUEST['year_sel']:'');
$sector         = (isset($_REQUEST['sector'])?$_REQUEST['sector']:'');
$sel_item       = (isset($_REQUEST['prod_sel'])?$_REQUEST['prod_sel']:'');
$sel_stk        = (isset($_REQUEST['stk_sel'])?$_REQUEST['stk_sel']:'');
$sel_prov       = (isset($_REQUEST['prov_sel'])?$_REQUEST['prov_sel']:'');

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

                <div class="row">
                    <div class="col-md-12">
                       
                        <h3 class="page-title row-br-b-wp"> Summary Reports</h3>
                        <?php
                          if (isset($_REQUEST['go']))
                          {
                              //include('ratesummary.php');
                          }
                        ?>			
                    </div>
                </div>
                
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <?php
                         include("summary_report_filter.php");
                        ?>			
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        
                        <?php
                        
                            if($report_type=='n_rep')
                            {
                                include "nationalreport.php";
                            }
                            elseif($report_type=='s_rep')
                            {
                                include "nationalreportstk.php";
                            }
                            elseif($report_type=='p_rep')
                            {
                                include "provincialreport.php";
                            }
                            elseif($report_type=='d_rep')
                            {
                                include "diststkreport.php";
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

<script>
    $('#report_type').on('change',function(){
        var rep_id = $(this).val();
        if(rep_id == 'n_rep' )
        {
            $('.filter1').hide();
            $('#td_sect').show();
            
            //$('#product').val('');
            //$('#prov').val('all');
            //$('#stk').val('all');
        }
        else if(rep_id == 's_rep' )
        {
            $('.filter1').hide();
            $('#td_prod').show();
        }
        else if(rep_id == 'p_rep')
        {
            $('.filter1').hide();
            
            $('#td_sect').show();
            $('#td_stk').show();
            $('#td_prod').show();
        }
        else if(rep_id == 'd_rep')
        {
            $('.filter1').hide();
            $('#td_sect').show();
            $('#td_stk').show();
            $('#td_prov').show();
            $('#td_prod').show();
        }
        else if(rep_id == 'pps_rep')
        {
            $('.filter1').hide();
            $('#td_prov').show();
            $('#td_dist').show();
            $('#td_level').show();
            
        }
        
    })
    
    $('#stk').change(function (e) {
        showProducts('');
        $('#prov').html('<option value="">Select</option>');
        showProvinces('');
    });

    $('#sector').change(function (e) {
        $('#product').html('<option>Select</option>');
        var val = $('#sector').val();
        getStakeholder(val, '');
    });
    
    
    function getStakeholder(val, stk)
    {
        $.ajax({
            url: 'ajax_stk.php',
            data: {type: val, stk: stk},
            type: 'POST',
            success: function (data) {
                $('#stk').html(data);
                showProducts('<?php echo (!empty($sel_item)) ? $sel_item : ''; ?>');
            }
        })
    }
    
    function showProducts(pid) {
        var stk = $('#stk').val();
        $.ajax({
            url: 'ajax_calls.php',
            type: 'POST',
            data: {stakeholder: stk, productId: pid},
            success: function (data) {
                $('#product').html(data);
            }
        })
    }
    function showProvinces(pid) {
        var stk = $('#stk').val();
        if (typeof stk !== 'undefined')
        {
            $.ajax({
                url: 'ajax_stk.php',
                type: 'POST',
                data: {stakeholder: stk, provinceId: pid, showProvinces: 1},
                success: function (data) {
                    $('#prov').html(data);
                }
            })
        }
    }
</script>        
</body>
<!-- END BODY -->
</html>