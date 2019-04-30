<?php
/**
 * demographics data entry
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
$office_level   = (!empty($_REQUEST['office_level']))?$_REQUEST['office_level']:'';
$task_order     = (!empty($_REQUEST['task_order']))?$_REQUEST['task_order']:'';
if($task_order=='to3') 
{
    $itm_cat = '1';
}    
if($task_order=='to4'){
    $itm_cat = '5';
}
$source = (!empty($_REQUEST['source']))?$_REQUEST['source']:'';
$selYear = (!empty($_REQUEST['year']))?$_REQUEST['year']:'';
if(empty($selYear))  $selYear = date('Y');


if(isset($_REQUEST['submit']))
{
    $level_name = "";
    if($_REQUEST['office_level'] == 2) $level_name = 'Provincial';
    if($_REQUEST['office_level'] == 3) $level_name = 'District';
    
    $qry = " SELECT count(*) as already_saved
            FROM
                fq_demographics_master
            WHERE
                fq_demographics_master.`year` = '$selYear' AND
                fq_demographics_master.source = '$source' AND
                fq_demographics_master.`level` = $office_level AND
                item_group = '$task_order'
     ";
    $rsQry = mysql_query($qry);
    $row = mysql_fetch_array($rsQry); 
    $already_saved = $row['already_saved'];
}

$qry = "SELECT
                fq_cols.*
            FROM
                fq_cols
            WHERE
                fq_cols.is_active = 1 
            ORDER BY
                order_by";
$rsQry = mysql_query($qry);
$all_cols = array();
while ($row = mysql_fetch_array($rsQry)) 
{
    $all_cols[$row['pk_id']]['short_name']              = $row['short_name'];
    $all_cols[$row['pk_id']]['long_name']               = $row['long_name'];
    $all_cols[$row['pk_id']]['col_type']                = $row['col_type'];
    $all_cols[$row['pk_id']]['default_percentage']      = $row['default_percentage'];
    $all_cols[$row['pk_id']]['percentage_of']           = $row['percentage_of'];
}
//echo '<pre>';print_r($all_cols);

$disabled = '';
if(isset($_REQUEST['submit'])  )
{
    $disabled = ' disabled ';
}

if(isset($_REQUEST['submit']))
{
                                        
//fetch existing data if any 
//print_r($_REQUEST);
    $qry = "SELECT
            fq_demographics_child.pk_id,
            fq_demographics_child.master_id,
            fq_demographics_child.location_id,
            fq_demographics_child.col_id,
            fq_demographics_child.`value`,
            fq_demographics_master.`year`,
            fq_demographics_master.source,
            fq_demographics_master.`level`
        FROM
            fq_demographics_master
        INNER JOIN fq_demographics_child ON fq_demographics_child.master_id = fq_demographics_master.pk_id
        WHERE
            fq_demographics_master.`year` = '" . $_REQUEST['year'] . "' AND
            fq_demographics_master.`level` = '" . $_REQUEST['office_level'] . "'

";
//echo $qry;
    $rsQry = mysql_query($qry);
    $dmg_previous_data = array();
    while ($row = mysql_fetch_assoc($rsQry)) {
        $master_id = $row['master_id'];
        $source = $row['source'];
        $dmg_previous_data[$row['location_id']][$row['col_id']] = $row['value'];
    }
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
                        <h3 class="page-title row-br-b-wp">Demographics Data Entry
                         <?php include("back_include.php"); ?>                            

                        </h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Demographic Parameters</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="get" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-2 hide">
                                                <div class="control-group">
                                                    <label>Entry No.</label>
                                                    <div class="controls">
                                                        <input class="form-control" readonly="" value="" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 hide">
                                                <div class="control-group">
                                                    <label>Date</label>
                                                    <div class="controls">
                                                        <input <?=$disabled?> class="form-control" readonly="" value="<?=date('d-M-Y');?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Demographics Base Year</label>
                                                    <div class="controls">
                                                        <select <?=$disabled?>  required  name="year" id="year" class="form-control input-sm">
                                                            <?php
                                                            for ($j = date('Y'); $j >= 2005; $j--) {
                                                                if ($selYear == $j) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label>Source of Demogrphics Data</label>
                                                    <div class="controls">
                                                        <input <?=$disabled?>  required class="form-control input-sm" name="source" value="<?=$source?>" />
                                                    </div>
                                                </div>
                                            </div>
                                         
                                            <div class="col-md-2 hide">
                                                <div class="control-group">
                                                    <label>Product Category</label>
                                                    <div class="controls">
                                                        <select <?=$disabled?>   name="task_order" id="task_order" class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option <?=($task_order=='to3')?' selected ':''?> value="to3">Family Planning</option>
                                                            <option <?=($task_order=='to4')?' selected ':''?>  value="to4">MNCH</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Level</label>
                                                    <div class="controls">
                                                        <select <?=$disabled?>  required name="office_level" id="office_level" class="form-control input-sm">
<!--                                                            <option  value="">Select</option>-->
                                                            <option <?=($office_level==2)?' selected ':''?> value="2">Provincial</option>
<!--                                                            <option   <?=($office_level==3)?' selected ':''?>  value="3">District</option>-->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="province_div" style="<?=($office_level==3)?'':' display:none;'?> ">
                                                <div class="control-group">
                                                    <label>Province/Region</label>
                                                    <div class="controls">
                                                        <select <?=$disabled?>  name="province" id="province" class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //select query
                                                            //gets
                                                            //province id
                                                            //province name
                                                            $qry = "SELECT DISTINCT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        INNER JOIN tbl_warehouse ON tbl_locations.PkLocID = tbl_warehouse.prov_id
                                                                        INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                                        WHERE
                                                                                tbl_locations.ParentID IS NOT NULL
                                                                        AND tbl_locations.LocLvl = 2
                                                                        AND tbl_warehouse.is_active = 1
                                                                        ORDER BY
                                                                                tbl_locations.PkLocID";
                                                            //query result
                                                            $rsQry = mysql_query($qry) or die();
                                                            //fetch result
                                                            while ($row = mysql_fetch_array($rsQry)) {
                                                                //pipulate province combo
                                                                $sel ='';
                                                                if($_REQUEST['province'] == $row['PkLocID']) $sel="selected";
                                                                ?>
                                                                <option value="<?php echo $row['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $row['LocName']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                             if(!isset($_REQUEST['submit']) || $already_saved>=3)
                                             {
                                            ?>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name ="submit" id="submit" value="Go" class="btn btn-primary input-sm">
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                             }
                                            ?>
                                        </div>
                                    </div>
                                    </form>
                                    <?php
                                    if(isset($_REQUEST['submit']) && (int)$already_saved<3)
                                    {
                                        
                                        
                                        $qry = " SELECT
                                                    tbl_locations.PkLocID,
                                                    tbl_locations.LocName
                                                    from
                                                    tbl_locations
                                                    WHERE
                                                    
                                                ";
                                        if($_REQUEST['office_level'] == 3)
                                        {
                                            $qry .= "   tbl_locations.LocLvl = 3 AND
                                                        tbl_locations.LocType = 4 AND
                                                        tbl_locations.ParentID = ".$_REQUEST['province']." ";
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
                                <form name="frm2" id="frm2" action="demographics_data_entry_action.php" method="post" role="form">
                                    <input type="hidden" name="year"      value="<?=(isset($_REQUEST['year']))?$_REQUEST['year']:''?>" >
                                    <input type="hidden" name="source"    value="<?=(isset($_REQUEST['source']))?$_REQUEST['source']:''?>" >
                                    <input type="hidden" name="task_order"     value="<?=(isset($_REQUEST['task_order']))?$_REQUEST['task_order']:''?>" >
                                    <input type="hidden" name="office_level"     value="<?=(isset($_REQUEST['office_level']))?$_REQUEST['office_level']:''?>" >
                                    <input type="hidden" name="province"  value="<?=(isset($_REQUEST['province']))?$_REQUEST['province']:''?>" >
                                    <input type="hidden" name="master_id"  value="<?=(isset($master_id))?$master_id:''?>" >
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="portlet box ">
						
						<div class="portlet-body">
                                                        <div class="">
								
                                                            <div class="col-md-12 btn green justify">Demographic Values <span class="badge badge-warning"><?=(!empty($master_id)?'Already Entered - Edit Mode':'New Entry')?></span></div>
                                                            <div class="col-md-12">
                                                                <div class="note note-info"> Press the 'Arrow Button' <i style="background-color:#000" class=" fa fa-angle-double-down "></i>  to apply default values on Total Population. </div>
                                                                    
                                                            </div>
                                                                <div class="" id="div_table">
                                                                        <div class="portlet  ">
                                                                            <div class="portlet-body" >
                                                                                <div style="overflow:auto !important;">
                                                                                <table  class="table table-condensed table-hover table-bordered">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <?php
                                                                                            $to3_count = 0;
                                                                                            echo '<td>Location</td>';
                                                                                            foreach($all_cols as $col_id => $col_data)
                                                                                            {
                                                                                                echo '<td title="'.$col_data['long_name'].'">'.wordwrap($col_data['long_name'],30," <br />");
                                                                                                if($col_id != 5) {
                                                                                                    echo '<br /><span style="color:grey">('.$col_data['default_percentage'].' %)</span>';
                                                                                                    echo '<a class="pull-right btn btn-xs blue dmg_calc_btn" data-col-id="'.$col_id.'"> <i style="" class=" fa fa-angle-double-down "></i></a>';
                                                                                                }
                                                                                                echo '</td>';
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
                                                                                               foreach($all_cols as $col_id => $col_data)
                                                                                               {
                                                                                                   echo '<td>';
                                                                                                   $val = isset($dmg_previous_data[$pro_id][$col_id])?$dmg_previous_data[$pro_id][$col_id]:'';
                                                                                                   
                                                                                                   echo '<input value="'.$val.'" data-prov-id="'.$pro_id.'" data-col-id="'.$col_id.'" data-perc="'.$col_data['default_percentage'].'" data-perc-of="'.$col_data['percentage_of'].'"  name="input_'.$task_order.'_'.$pro_id.'_'.$col_id.'"  id="input_'.$task_order.'_'.$pro_id.'_'.$col_id.'" type="text" class="grid_input_number dmg_values form-control pull-right right"></td>';
                                                                                                   
                                                                                               }
                                                                                               echo '</tr>';
                                                                                           }
                                                                                           ?>
                                                                                    </tbody>
                                                                                    
                                                                                </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row col-md-12">
                                                                            <div class="right">
                                                                                <a href="" onclick="return confirm('Are you sure to discard unsaved changes, and proceed to new data entry?')"  class="btn btn-primary btn-red" >Cancel and exit</a>
                                                                                <input class="btn btn-red" type="reset" value="Reset">
                                                                                <input class="btn btn-green green" type="submit" value="<?=(isset($master_id))?'Update':'Save'?>">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row col-md-12 hide">
                                                                            <?php
                                                                            
                                                                                echo '<div class=" col-md-3"><b>Column Name</b></div>';
                                                                                echo '<div class=" col-md-9"><b>Description</b></div>';
                                                                                foreach($all_cols as $col_id => $col_data)
                                                                                {
                                                                                    if($col_data['col_type'] == 'main')
                                                                                    {
                                                                                        echo '<div class=" col-md-3">'.$col_data['short_name'].'</div>';
                                                                                        echo '<div class=" col-md-9">'.$col_data['long_name'].'</div>';
                                                                                    }
                                                                                }
                                                                            ?>
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
                                    if(isset($_REQUEST['submit']) &&  (int)$already_saved>=3)
                                    {
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12"> 
                                                 <div class="portlet light">
                                                    <div class="portlet-body">
                                                        Already 3 Demographic Data Entries of <b><?=strtoupper($_REQUEST['task_order'])?></b> have been saved for the year <b><?=$_REQUEST['year']?></b> of <b><?=$_REQUEST['source']?> ,  <?=($level_name)?></b> Level
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
            $('.dmg_calc_btn').click(function(e) {
                var col_id = $(this).data('col-id');
                console.log('ID:'+col_id);
                var c_val=0;
                $('.dmg_values[data-col-id='+col_id+']').each(function(e){
                        var prov_id = $(this).data('prov-id');
                        var perc    = $(this).data('perc');
                        var perc_of = $(this).data('perc-of');
                        var v1 = $('.dmg_values[data-prov-id='+prov_id+'][data-col-id='+perc_of+']').val();
                        
                        c_val = (v1/100)*perc ;
                        console.log('Province:'+prov_id+' Perc Of :'+perc_of+',Perc:'+perc+',Integer:'+v1+'Total:'+c_val);
                        //$('.dmg_values[data-prov-id='+prov_id+'][data-col-id='+col_id+']').val(c_val);
                        c_val=c_val.toFixed();
                        $(this).val(c_val);
                });
                
            });
            
            
            $("#frm2").submit(function() {
                var c = confirm("Save all changes ?");
                return c; 
            });
            $(".grid_input_number").keyup(function(event) {
                console.log($(this).val());
                console.log('>>'+isNaN($(this).val()));
                if (isNaN($(this).val())){
                    //$(this).focus(); 
                    //$(this).attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
                    this.value = this.value.replace(/[^0-9\.]/g,'');
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
            text: 'Demographics Data Entry Saved Successfully.',
            type: 'success',
            layout: self.data('layout')
        });

    </script>
    <?php
    $_REQUEST['err']='disabled';
    }
    ?>
</body>
</html>