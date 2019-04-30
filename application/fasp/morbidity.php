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
$office_level = (!empty($_REQUEST['office_level']))?$_REQUEST['office_level']:'';
$source = (!empty($_REQUEST['source']))?$_REQUEST['source']:'';
$selYear = (!empty($_REQUEST['year']))?$_REQUEST['year']:'';
$task_order     = (!empty($_REQUEST['task_order']))?$_REQUEST['task_order']:'';

if($task_order=='to3') 
{
    $task_order_full_name = 'Family Planning';
    $itm_cat = '1';
}    
if($task_order=='to4'){
    $task_order_full_name = 'MNCH';
    $itm_cat = '5';
}
if(empty($selYear))  $selYear = date('Y');

$already_saved =0;
if(isset($_REQUEST['submit']))
{
//    $level_name = "";
//    if($_REQUEST['office_level'] == 2) $level_name = 'Provincial';
//    if($_REQUEST['office_level'] == 3) $level_name = 'District';
//    
//    $qry = " SELECT count(*) as already_saved
//            FROM
//                fq_demographics_master
//            WHERE
//                fq_demographics_master.`year` = '$selYear' AND
//                fq_demographics_master.source = '$source' AND
//                fq_demographics_master.`level` = $office_level
//     ";
//    $rsQry = mysql_query($qry);
//    $all_cols = $all_cols = array();
//    $row = mysql_fetch_array($rsQry); 
//    $already_saved = $row['already_saved'];
}

//echo '<pre>';print_r($all_prods);

$disabled = '';
if(isset($_REQUEST['submit']) && $already_saved<3)
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
                            Morbidity / Prevalence Entry Form
                            <?php include("back_include.php"); ?>

                        </h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Morbidity / Prevalence Parameters</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form">
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
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Date</label>
                                                    <div class="controls">
                                                        <input <?=$disabled?>  class="form-control" readonly="" value="<?=date('d-M-Y');?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Year</label>
                                                    <div class="controls">
                                                        <select <?=$disabled?>  required name="year" id="year" class="form-control input-sm">
                                                            <?php
                                                            for ($j = date('Y')+10; $j >= 2015; $j--) {
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
                                            
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Source</label>
                                                    <div class="controls">
                                                        <input <?=$disabled?>  required class="form-control" name="source" value="<?=$source?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Level</label>
                                                    <div class="controls">
                                                        <select <?=$disabled?>  required name="office_level" id="office_level" class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option <?=($office_level==2)?' selected ':''?> value="2">Provincial</option>
                                                            <option <?=($office_level==3)?' selected ':''?>  value="3">District</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="province_div" style="<?=($office_level==3)?'':' display:none;'?> ">
                                                <div class="control-group">
                                                    <label>Province/Region</label>
                                                    <div class="controls">
                                                        <select name="province" id="province" class="form-control input-sm">
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
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Product Category</label>
                                                    <div class="controls">
                                                        <select <?=$disabled?>  required name="task_order" id="task_order" class="form-control input-sm">
                                                            <option <?=($task_order=='to4')?' selected ':''?> value="to4">MNCH TO4</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                             <?php
                                             if(!isset($_REQUEST['submit']))
                                             {
                                            ?>
                                            <div class="col-md-1">
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
                                <form name="frm2" id="frm2" action="morbidity_action.php" method="post" role="form">
                                    <input type="hidden" name="year"      value="<?=(isset($_REQUEST['year']))?$_REQUEST['year']:''?>" >
                                    <input type="hidden" name="source"    value="<?=(isset($_REQUEST['source']))?$_REQUEST['source']:''?>" >
                                    <input type="hidden" name="task_order"     value="<?=(isset($_REQUEST['task_order']))?$_REQUEST['task_order']:''?>" >
                                    
                                    <input type="hidden" name="office_level"     value="<?=(isset($_REQUEST['office_level']))?$_REQUEST['office_level']:''?>" >
                                    <input type="hidden" name="province"  value="<?=(isset($_REQUEST['province']))?$_REQUEST['province']:''?>" >
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="portlet box green">
						
						<div class="portlet-body">
                                                        <div class="row">
								
								<div class="col-md-12">
								<div class="col-md-12 btn green justify"><?=$task_order_full_name?></div>
                                                                    <div class="active" id="tab11">
                                                                        <div class="portlet  ">
                                                                            <div class="portlet-body">
                                                                                <div style="overflow:auto !important;">
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
                                                                                                       echo '<td><input name="input_to3_'.$pro_id.'_'.$col_id.'"  id="input_to3_'.$pro_id.'_'.$col_id.'" type="number" min="0" class="grid_input_number form-control" ></td>';
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
                                                                            <div class="right"><input class="btn btn-green" type="submit" value="Save"></div>
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
                                    if(isset($_REQUEST['submit']) &&  (int)$already_saved>=3)
                                    {
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12"> 
                                                 <div class="portlet light">
                                                    <div class="portlet-body">
                                                        Already 3 Demographic Data Entries have been saved for the year <b><?=$_REQUEST['year']?></b> of <b><?=$_REQUEST['source']?> ,  <?=($level_name)?></b> Level
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
            $('input').bind('keyup blur',function(e){
                var v = $(this).val();
                var l = v.length;
                var min_pix = 8;
                var min_l = 64;
                var min_chars = 8;
                if(l<8) min_chars=8;
                else min_chars = l+1;
                min_l = min_chars * min_pix;
                //console.log('V:'+v+', L:'+l+', Chars:'+min_chars+', Min:'+min_l);
                //$(this).(' style=') = min_l+ 'px';
                
                $(this).css("width", min_l+"px");
            })
    </script>
    <?php
    if($_REQUEST['err']=='0')
    {
    ?>
    <script>
        var self = $('[data-toggle="notyfy"]');
        notyfy({
            force: true,
            text: 'Morbidity Data Entry Saved Successfully.',
            type: 'success',
            layout: self.data('layout')
        });

    </script>
    <?php
    }
    ?>
</body>
</html>