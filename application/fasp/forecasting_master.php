<?php
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//echo '<pre>';print_r($_REQUEST);exit;
$office_level = (!empty($_REQUEST['office_level']))?$_REQUEST['office_level']:'';
$source = (!empty($_REQUEST['source']))?$_REQUEST['source']:'';
$selYear = (!empty($_REQUEST['year']))?$_REQUEST['year']:'';
$product_category     = (!empty($_REQUEST['product_category']))?$_REQUEST['product_category']:'';

if($product_category=='fp') 
{
    $product_category_full_name = 'Family Planning';
    $itm_cat = '1';
}    
if($product_category=='mnch'){
    $product_category_full_name = 'MNCH';
    $itm_cat = '5';
}
if(empty($selYear))  $selYear = date('Y');


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
                            Forecasting Master Data   
                            <?php include("back_include.php"); ?>
                        </h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Forecasting Parameters</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="forecasting_master_action.php" method="post" role="form">
                                    <div class="row">
                                        <div class="col-md-7">
                                    <div class="row hide">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-6 ">
                                                <div class="control-group">
                                                    <label>Entry No.</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6 ">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <input class="form-control" readonly="" value="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                            
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Purpose of forecasting</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <input  required class="form-control" name="purpose" value="" />
                                                    </div>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>        
                                    
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Product Category</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <select   required name="task_order" id="task_order" class="form-control input-sm">
                                                            <?php
                                                            $sel_mnch='';
                                                            if($product_category=='mnch'){
                                                                $sel_mnch = " selected ";
                                                            }
                                                            echo '<option  value="to3">Family Planning</option>';
                                                            echo '<option '.$sel_mnch.' value="to4">MNCH</option>';
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>        
                                            
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Base Year</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <select   required name="base_year" id="base_year" class="form-control input-sm">
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
                                    
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Starting Year</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <select   required name="start_year" id="start_year" class="form-control input-sm">
                                                            <?php
                                                            for ($j = date('Y')+10; $j >= date('Y')+1; $j--) {
                                                                if ((date('Y')+1) == $j) {
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
                                    
                                        </div>
                                    </div>         
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>End Year</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <select   required name="end_year" id="end_year" class="form-control input-sm">
                                                            <?php
                                                            for ($j = date('Y')+10; $j >= date('Y')+1; $j--) {
                                                                if ((date('Y')+1) == $j) {
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
                                    
                                        </div>
                                    </div>   
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Data Sources</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <input  required class="form-control" name="source" value="<?=$source?>" />
                                                    </div>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>
                                    <div class="row hide">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Level</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <select   required name="office_level" id="office_level" class="form-control input-sm">
                                                            
                                                            <option <?=($office_level==2)?' selected ':''?> value="2">Provincial</option>
                                                            <option style="display:none;" <?=($office_level==3)?' selected ':''?>  value="3">District</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Stakeholder</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <?php
                                                            if($product_category=='fp'){
                                                                $stk_ids = '1,2,7';
                                                            }
                                                            else{
                                                                $stk_ids = '73';
                                                            }
                                                        ?>
                                                        <select name="stakeholder[]" id="stakeholder" class="form-control input-sm" multiple="">
                                                            <option value="">Select</option>
                                                            <?php
                                                            $qry = "SELECT
                                                                        stakeholder.stkid,
                                                                        stakeholder.stkname
                                                                        FROM
                                                                        stakeholder
                                                                        WHERE
                                                                        stakeholder.stkid in ($stk_ids)
                                                                    ";
                                                            $rsQry = mysql_query($qry) or die();
                                                            while ($row = mysql_fetch_array($rsQry)) {
                                                               echo '<option value="'.$row['stkid'].'">'.$row['stkname'].'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>        
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6" id="province_div">
                                                <div class="control-group">
                                                    <label>Province</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6" id="province_div">
                                                <div class="control-group">
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
                                    
                                        </div>
                                    </div>
                                            
                                    
                                    <div class="row hide">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Forecasting Methods</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <input class="form-control" name="fc_methods" value="" />
                                                    </div>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>        
                                            
                                            
                                    <div class="row">
                                        <div class="col-md-12 pull-right">
                                            
                                            <div class="col-md-1  pull-right">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls  pull-right">
                                                        <input type="submit" name ="submit" id="submit" value="Save" class="btn input-sm green">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    </div>
                                    </div>
                                    </form>
                                    
                                
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
    
    <?php 
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; 
    ?>
    <script>
    $('#task_order').on('change',function(){
       var to = $(this).val();
       if(to=='to3') to='fp';
       else to = 'mnch';
       window.location.href = "forecasting_master.php?product_category="+to; 
    });
    </script>
</body>
</html>