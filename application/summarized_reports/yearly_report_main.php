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
$indicatorLvl='all';
$type=1;


$report_type    = (isset($_REQUEST['report_type'])?$_REQUEST['report_type']:'');
$selYear        = (isset($_REQUEST['year_sel'])?$_REQUEST['year_sel']:'');
$selMonth       = (isset($_REQUEST['ending_month'])?$_REQUEST['ending_month']:'');
$selItem        = (isset($_REQUEST['item_id'])?$_REQUEST['item_id']:'');
$selPro         = (isset($_REQUEST['prov_sel'])?$_REQUEST['prov_sel']:'');
$selStk         = (isset($_REQUEST['stk_sel'])?$_REQUEST['stk_sel']:'');
$type           = (isset($_REQUEST['type'])?$_REQUEST['type']:'');
$sel_indicator = $type;
$sector         = (isset($_REQUEST['sector'])?$_REQUEST['sector']:'');
$whType         = (isset($_REQUEST['wh_type'])?$_REQUEST['wh_type']:'');

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
                        <h3 class="page-title row-br-b-wp"> Yearly Reports</h3>
                        
                        <?php
                            include("yearly_report_filter.php");
			?>
						
						
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        
                        <?php
                        
                            if($report_type=='d_rep')
                            {
                                include "district_stock_yearly.php";
                            }
                            elseif($report_type=='p_rep')
                            {
                                include "provincial_warehouse_report.php";
                            }
                            elseif($report_type=='cp_rep')
                            {
                                include "central_warehouse_report.php";
                            }
                            elseif($report_type=='ps_rep')
                            {
                                include "private_sector_report.php";
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
        if(rep_id == 'd_rep' )
        {
            $('.filter1').hide();
            $('#td_ending_month').show();
            $('#td_sect').show();
            $('#td_stk').show();
            $('#td_prov').show();
            $('#td_prod').show();
            $('#item_id').prop('required', true);
            $('#td_ind').show();
            //$('#td_ind_lvl').show();
        }
        else if(rep_id == 'p_rep' )
        {
            $('.filter1').hide();
            $('#td_sect').show();
            $('#td_stk').show();
            $('#td_prov').show();
            $('#td_ind').show();
            $('#item_id').prop('required', false);
        }
        else if(rep_id == 'cp_rep')
        {
            $('.filter1').hide();
            
            $('#td_stk').show();
            $('#td_ind').show();
            $('#td_wh_type').show();
            
            $('#item_id').prop('required',false);
            
        }
        else if(rep_id == 'ps_rep')
        {
            $('.filter1').hide();
            $('#td_stk').show();
            $('#td_ind').show();
           
            $('#item_id').prop('required',false);
        }
       
        $('#type option').hide();
        $('#type option[rep-type="'+rep_id+'"]').show();
        
       
        if(rep_id == 'ps_rep')
        {
            $('#stk_sel option[stk-type="0"]').hide();
            $('#stk_sel option[stk-type="1"]').show();
            
            $('#stk_sel').val('all');
        }
        else
        { 
            $('#sector').val('all');
            $('#stk_sel option').show();
        }
        
        if(rep_id == 'cp_rep')
        {
            $('#stk_sel option[value=1]').text('PPW/CWH');
        }
        else
        {
            $('#stk_sel option[value=1]').text('PWD');
        }
    })
    
    
    //new script
     function showAllProducts(indVal) {
            if (indVal == 5) {
                $("#item_id option[value='']").text('All');
                $("#item_id").removeAttr('required');
            } else {
                $("#item_id option[value='']").text('Select');
                //$("#item_id").attr('required', 'required');
            }
        }
        function showIndLvl(val) {
            var r_type = $('#report_type').val();
            
            if (val == 4 && r_type=='d_rep') {
                $('#td_ind_lvl').show();
            } else {
                $('#td_ind_lvl').hide();
            }
        }
        function showProducts(pid) {
            var stk = $('#stk_sel').val();
            $.ajax({
                url: 'ajax_calls.php',
                type: 'POST',
                data: {stakeholder: stk, productId: pid},
                success: function (data) {
                    $('#item_id').html(data);
                }
            })
        }
        function showProvinces(pid) {
            var stk = $('#stk_sel').val();
            if (typeof stk !== 'undefined')
            {
                $.ajax({
                    url: 'ajax_stk.php',
                    type: 'POST',
                    data: {stakeholder: stk, provinceId: pid, showProvinces: 1},
                    success: function (data) {
                        $('#prov_sel').html(data);
                    }
                })
            }
        }
        
        $(function () {
            //showIndLvl('<?php //echo $type; ?>');
            $('#type').change(function (e) {
                showIndLvl($(this).val());
            });
            $('#stk_sel').change(function (e) {
                showProducts('');
                $('#prov_sel').html('<option value="">Select</option>');
                showProvinces('');
            });

            $('#sector').change(function (e) {
                $('#item_id').html('<option>Select</option>');
                var val = $('#sector').val();
                getStakeholder(val, '');
            });

            $('#type').change(function (e) {
                showAllProducts($(this).val());
                $("#item_id").val('');
            });
            
            setTimeout(
                    function ()
                    {
                        showAllProducts('<?php echo $type; ?>');
                    }, 1000);
        })
        
        function getStakeholder(val, stk)
        {
            $.ajax({
                url: 'ajax_stk.php',
                data: {type: val, stk: stk},
                type: 'POST',
                success: function (data) {
                    $('#stk_sel').html(data);
                    showProducts('<?php echo (!empty($selItem)) ? $selItem : ''; ?>');
                }
            })
        }
    
</script>         
</body>
<!-- END BODY -->
</html>