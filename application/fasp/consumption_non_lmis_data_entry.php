<?php
/**
 * consumption data entry
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
//echo '<pre>';print_r($_REQUEST);exit;


$task_order_full_name = 'Family Planning';
$itm_cat = '1';

//fetch the forecasting master data
$qry = "SELECT
            fq_master_data.pk_id,
            fq_master_data.base_year,
            fq_master_data.start_year,
            fq_master_data.end_year,
            fq_master_data.purpose,
            fq_master_data.source,
            fq_master_data.stakeholders,
            fq_master_data.province_id,
            fq_master_data.forecasting_methods,
            fq_master_data.item_group,
            tbl_locations.LocName
        FROM
            fq_master_data
        INNER JOIN tbl_locations ON fq_master_data.province_id = tbl_locations.PkLocID
        WHERE fq_master_data.pk_id = " . $_REQUEST['id'] . " ";
//query result
$rsQry = mysql_query($qry) or die();
//fetch result
$c = 1;
$fq_master = mysql_fetch_array($rsQry);


$office_level = (!empty($fq_master['office_level']))?$fq_master['office_level']:'2';
$source = (!empty($fq_master['source']))?$fq_master['source']:'';
$selYear = (!empty($fq_master['base_year']))?$fq_master['base_year']:'';
$task_order     = (!empty($fq_master['item_group']))?$fq_master['item_group']:'';



$years_arr = array();
$years_count = 0;
for ($i = $fq_master['start_year']; $i <= $fq_master['end_year']; $i++) {
    $years_arr[$i] = $i;
    $years_count++;
}

$disabled = '';
if(isset($_REQUEST['id']))
{
    $disabled = ' disabled ';
    
    
    $qry = "SELECT
                itminfo_tab.itmrec_id,
                itminfo_tab.itm_id,
                itminfo_tab.itm_name,
                itminfo_tab.method_type
                FROM
                itminfo_tab
                WHERE
                itminfo_tab.method_type IS NOT NULL AND
    itminfo_tab.itm_category = $itm_cat
                ORDER BY
                itminfo_tab.method_rank ASC";
    $rsQry = mysql_query($qry);
    $all_prods = array();
    while ($row = mysql_fetch_array($rsQry)) 
    {
        $all_prods[$row['itm_id']]  = $row['itm_name'];
    }
}

//fetch stk list
$qry = "SELECT
                stakeholder.stkid,
                stakeholder.stkname
            FROM
                stakeholder
            WHERE
                stakeholder.is_reporting = 1 AND
                stakeholder.stk_type_id = 0 AND
                stakeholder.ParentID IS NULL
    ";
$rsQry = mysql_query($qry);
$stk_arr = array();
while ($row = mysql_fetch_array($rsQry)) {
    $stk_arr[$row['stkid']] = $row['stkname'];
}

                                     
//fetch existing data if any 
//print_r($_REQUEST);
    $qry = " SELECT
                fq_non_lmis_consumption_child.pk_id,
                fq_non_lmis_consumption_child.master_id,
                fq_non_lmis_consumption_child.location_id,
                fq_non_lmis_consumption_child.product_id,
                fq_non_lmis_consumption_child.`value`
            FROM
                fq_non_lmis_consumption_child
            WHERE master_id = '".$_REQUEST['id']."'
             ";
//echo $qry;
    $rsQry = mysql_query($qry);
    $previous_data = array();
    while ($row = mysql_fetch_assoc($rsQry)) {
        $master_id = $row['master_id'];
        $previous_data[$row['location_id']][$row['product_id']] = $row['value'];
    }
?>

<style>
    .nav-pills > li > a:hover,.nav-pills > li.active > a{
        color: #FFFFFF !important;
        background-color: #009C00 !important;
    } 
    .nav-pills > li > a{
        color: #000 !important;
        background-color: #FFFFFF !important;
    }
    .grid_input_number{
        padding: 0px !important;
        font-size: 11px !important;
    }
</style>
    
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
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">  
                            Quota / Consumption (Non LMIS) Data Entry   
                            <?php include("back_include.php"); ?>
                        </h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Forecasting Detail</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="mnch_forecasting_calculator_action.php" method="post" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-condensed ">
                                                <tr style="display:none;">
                                                    <td width="20%">
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Forecasting Reference</label>
                                                        </div>
                                                    </td>
                                                    <td colspan="3">
                                                        <div class="control-group">
                                                            <label><?= $reference_txt ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Purpose</label>
                                                        </div>
                                                    </td>
                                                    <td colspan="3">
                                                        <div class="control-group">
                                                            <label class=""><?= $fq_master['purpose'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>    

                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Forecast on Base Year</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= $fq_master['base_year'] ?></label>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Product Category</label>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= (($fq_master['item_group'] == 'to3') ? 'Family Planning' : 'MNCH') ?></label>
                                                        </div>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Starting From Year</label>
                                                        </div>
                                                    </td>
                                                    <td  width="30%">
                                                        <div class="control-group">
                                                            <label><?= $fq_master['start_year'] ?></label>
                                                        </div>


                                                    </td>
                                                    <td width="30%">
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">To (End Year)</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= $fq_master['end_year'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>

                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Data Sources</label>

                                                        </div>
                                                    </td>
                                                    <td  colspan="3">
                                                        <div class="control-group">
                                                            <label><?= $fq_master['source'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Stakeholder/s</label>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label>
<?php
$st = explode(',', $fq_master['stakeholders']);
$st2 = array();
foreach ($st as $k => $stk_id) {
    $st2[] = $stk_arr[$stk_id];
}
echo implode(',', $st2);
?>
                                                            </label>
                                                        </div>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Province</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= $fq_master['LocName'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </table> 


                                        </div>
                                    </div>
                                </form>


                            </div>
                        </div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Non LMIS Consumption of Base Year  
                                    <?php
                                    if(!empty($master_id))
                                        echo '<span class="label label-lg label-warning">Already Saved</span>';
                                    else
                                        echo '<span class="label label-lg label-danger">New Entry</span>';
                                    
                                    ?> 
                                    
                                </h3>
                            </div>
                            <div class="widget-body">
                                
                                    <?php
                                    if(isset($_REQUEST['id']))
                                    {
                                        
                                        $qry = " SELECT
                                                    tbl_locations.PkLocID,
                                                    tbl_locations.LocName
                                                    from
                                                    tbl_locations
                                                    WHERE
                                                    
                                                ";
                                        if($office_level == 3)
                                        {
                                            $qry .= "   tbl_locations.LocLvl = 3 AND
                                                        tbl_locations.LocType = 4 AND
                                                        tbl_locations.ParentID = ".$fq_master['province_id']." ";
                                        }
                                        else
                                        {
                                            $qry .= "   tbl_locations.LocLvl = 2
                                                        and LocType =2 ";
                                        }
                                        
                                        //echo $qry;exit;
                                        $rsQry = mysql_query($qry);
                                        $prov_arr = array();
                                        while ($row = mysql_fetch_array($rsQry)) 
                                        {
                                            $prov_arr[$row['PkLocID']] = $row['LocName'];
                                        }
                                    ?>
                                <form name="frm2" id="frm2" action="consumption_non_lmis_action.php" method="post" role="form">
                                    <input type="hidden" name="master_id"  value="<?=(isset($_REQUEST['id']))?$_REQUEST['id']:''?>" >
                                    <input type="hidden" name="already_saved"  value="<?=(isset($master_id))?'1':''?>" >
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="portlet">
						
						<div class="portlet-body">
                                                        <div class="row">
								
								<div class="col-md-12">
                                                                    <div class="active" id="tab11">
                                                                        <div class="portlet  ">
                                                                            <div class="portlet-body">
                                                                                <table class="table table-condensed table-hover table-bordered">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <?php
                                                                                            $to3_count = 0;
                                                                                            echo '<td>Province</td>';
                                                                                            foreach($all_prods as $col_id => $col_data)
                                                                                            {
                                                                                                echo '<td>'.$col_data.'</td>';
                                                                                                $to3_count++;
                                                                                            }
                                                                                            ?>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <?php
                                                                                           foreach($prov_arr as $pro_id => $pro_name)
                                                                                           {
                                                                                               echo '<tr>';
                                                                                               echo '<td>'.$pro_name.'</td>';
                                                                                               foreach($all_prods as $col_id => $col_data)
                                                                                               {
                                                                                                   $this_val = (isset($previous_data[$pro_id][$col_id])?$previous_data[$pro_id][$col_id]:'');
                                                                                                   echo '<td><input value="'.$this_val.'" name="input_to3_'.$pro_id.'_'.$col_id.'"  id="input_to3_'.$pro_id.'_'.$col_id.'" type="number" min="0" class="grid_input_number form-control"></td>';
                                                                                               }
                                                                                               echo '</tr>';
                                                                                           }
                                                                                           ?>
                                                                                    </tbody>
                                                                                    
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row col-md-12">
                                                                            <div class="right"><input class="btn btn-green" type="submit" value="<?=(!empty($master_id)?'Update':'Save')?>"></div>
                                                                        </div>
                                                                        
                                                                    </div>
                                                                    
								</div>
							</div>
						</div>
                                                
					</div>
                                   
                                        </div>
                                    </div>
                                    
                                </form>
                                    <?php
                                    }
                                    ?>
                                    
                                
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
    <?php 
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; ?>
    <script>
        $(document).ready(function() {
            $(".grid_input_number").keyup(function(event) {
                //console.log($(this).val());
                //console.log('>>'+isNaN($(this).val()));
                if (isNaN($(this).val())){
                    //$(this).focus(); 
                    $(this).attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                }
            });
        });
        $(function() {
            $('#office_level').change(function() {
                officeType($(this).val());
            });
//            $('#province').change(function() {
//                var provId = $(this).val();
//                showDistricts(provId);
//            });
            

            // Submit Form
            $('#submit').click(function(e) {
                $('body').addClass("loading");
            });
        });
        function officeType(officeLevel)
        {
            if (parseInt(officeLevel) == 2)
            {
                $('#province_div').hide();
                $('#district_div').hide();
            }
            else if (parseInt(officeLevel) == 3)
            {
                $('#province_div').show();
                //showDistricts($('#province').val());
            }
        }
        function showDistricts(provId)
        {
            var officeLevel = $('#office_level').val();
            if (officeLevel == 3)
            {
                $('#district_div').show();
                $('#district').html('<option value="">Loading...</option>');
                $.ajax({
                    type: "POST",
                    url: "ajax_calls.php",
                    data: {provinceId: provId, validate: 'yes'},
                    dataType: 'html',
                    success: function(data)
                    {
                        $('#district_data').html(data);
                    }
                });
            }
        }
        
        
		function showProvinces(pid) {
			var stk = $('#stk_sel').val();
			if (typeof stk !== 'undefined')
			{
				$.ajax({
					url: 'ajax_stk.php',
					type: 'POST',
					data: {stakeholder: stk, provinceId: pid, showProvinces: 1, showAllOpt: 0},
					success: function(data) {
						$('#province').html(data);
					}
				})
			}
		}
    </script>
    <?php
    if($_REQUEST['err']=='0')
    {
    ?>
    <script>
        var self = $('[data-toggle="notyfy"]');
        notyfy({
            force: true,
            text: 'Consumption Data Entry Saved Successfully.',
            type: 'success',
            layout: self.data('layout')
        });

    </script>
    <?php
    }
    ?>
</body>
</html>