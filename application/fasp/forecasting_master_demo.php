<?php
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//echo '<pre>';print_r($_REQUEST);exit;

if(!empty($_REQUEST['master_id'])){
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
                fq_master_data.forecasting_methods,
                fq_master_data.created_by,
                fq_master_data.modified_by,
                fq_master_data.created_at,
                fq_master_data.modified_at,
                fq_master_data.level,
                fq_master_data.item_group,
tbl_locations.LocName as dist_name
            FROM
                fq_master_data
LEFT JOIN tbl_locations ON fq_master_data.district_id = tbl_locations.PkLocID
            WHERE
                fq_master_data.pk_id = ".$_REQUEST['master_id']."
            ";
    $rsQry = mysql_query($qry) or die();
    $master_data = array();
    $master_data = mysql_fetch_assoc($rsQry);
    
    
    $qry = " SELECT
                itminfo_tab.itm_id,
                itminfo_tab.itm_name
                FROM
                itminfo_tab
                WHERE
                itminfo_tab.itm_category = 1 AND
                itminfo_tab.method_rank IS NOT NULL

            ";
    $rsQry = mysql_query($qry) or die();
    $item_data = array();
    while($row = mysql_fetch_assoc($rsQry)){
        $item_data[$row['itm_id']] = $row;
    }
    
    
    
    $qry = " SELECT
 
                fq_fp_products_data.pk_id,
                fq_fp_products_data.master_id,
                fq_fp_products_data.prod_id,
                fq_fp_products_data.base_year_1,
                fq_fp_products_data.base_year_2,
                fq_fp_products_data.base_year_3,
                fq_fp_products_data.average_amc_of_base_years,
                fq_fp_products_data.adjustment,
                fq_fp_products_data.remarks,
                fq_fp_products_data.last_modified
                FROM
                fq_fp_products_data
                WHERE  
                fq_fp_products_data.master_id = ".$_REQUEST['master_id']."


            ";
    $rsQry = mysql_query($qry) or die();
    $saved_data = array();
    while($row = mysql_fetch_assoc($rsQry)){
        $saved_data[$row['prod_id']] = $row;
    }
}
else
{
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

}

//echo '<pre>';print_r($master_data);exit;
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
                                <h3 class="heading">New Forecast</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="forecasting_master_action.php" method="post" role="form">
                                    <div class="row">
                                        <div class="col-md-8">
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
                                                    <label>Title</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <input  required class="form-control" name="purpose" <?=(!empty($_REQUEST['master_id'])?'disabled':'')?> value="<?=(!empty($_REQUEST['master_id'])?$master_data['purpose']:'')?>" />
                                                    </div>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>  
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Method</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="foncontrols">
                                                        <input  type="checkbox" readonly disabled class="" name="method" checked="checked" value="consumption"  <?=(!empty($_REQUEST['master_id'])?'disabled':'')?> /> Consumption
                                                    </div>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>        
                                    
                                    
                                    <div class="row hide">
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
                                                    <label>Forecasting Period Year</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <select   required name="start_year" id="start_year" class="form-control input-sm" <?=(!empty($_REQUEST['master_id'])?'disabled':'')?> >
                                                            <option value="" >Starting Year</option>
                                                            <?php
                                                            for ($j = date('Y'); $j <= date('Y')+10; $j++) {
                                                                if(!empty($_REQUEST['master_id']) && $master_data['start_year']== $j){
                                                                    $sel = "selected='selected'";
                                                                }
                                                                elseif ((date('Y')) == $j) {
                                                                    $sel = " ";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j.'-'.substr(($j+1),2); ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <select   required name="end_year" id="end_year" class="form-control input-sm" <?=(!empty($_REQUEST['master_id'])?'disabled':'')?> >
                                                            <option value="" >End Year</option>
                                                            <?php
                                                            for ($j = date('Y')+10; $j >= date('Y'); $j--) {
                                                                if(!empty($_REQUEST['master_id']) && $master_data['end_year']== $j){
                                                                    $sel = "selected='selected'";
                                                                }
                                                                elseif ((date('Y')) == $j) {
                                                                    //$sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j.'-'.substr(($j+1),2); ?></option>
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
                                                    <label>Data Sources</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <input    class="form-control" name="source" value="<?=$source?>" />
                                                    </div>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>
                                    <div class="row  ">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Level</label>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <div class="controls">
                                                        <select   required name="office_level" id="office_level" class="form-control input-sm"  <?=(!empty($_REQUEST['master_id'])?'disabled':'')?> >
                                                            
                                                            <option <?=($master_data['level']==2)?' selected ':''?> value="2">Provincial</option>
                                                            <option style="" <?=($master_data['level']==3)?' selected ':''?>  value="3">District</option>
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
                                                            if(!empty($product_category) && $product_category=='mnch'){
                                                                $stk_ids = '73';
                                                            }
                                                            else{
                                                                $stk_ids = '1,2,7,73,4,5,6,9';
                                                            }
                                                        ?>
                                                        <select name="stakeholder[]" id="stakeholder" class="form-control input-sm"  <?=(!empty($_REQUEST['master_id'])?'disabled':'')?> >
                                                            <option value="">Select</option>
                                                            <?php
                                                            $qry = "SELECT
                                                                        stakeholder.stkid,
                                                                        stakeholder.stkname
                                                                        FROM
                                                                        stakeholder
                                                                        WHERE
                                                                        stakeholder.stkid in ($stk_ids)
                                                                        ORDER BY
                                                                        stakeholder.stk_type_id ASC
                                                                    ";
                                                            $rsQry = mysql_query($qry) or die();
                                                            $master_stk_arr = explode(",",$master_data['stakeholders']);
                                                            //echo '<pre>';print_r($master_stk_arr);exit;
                                                            while ($row = mysql_fetch_array($rsQry)) {
                                                                $sel = "";
                                                                if(!empty($_REQUEST['master_id']) && in_array($row['stkid'], $master_stk_arr) ){
                                                                    $sel = "selected='selected'";
                                                                }
                                                               echo '<option value="'.$row['stkid'].'" '.$sel.'>'.$row['stkname'].'</option>';
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
                                                        <select name="province" id="province" class="form-control input-sm" <?=(!empty($_REQUEST['master_id'])?'disabled':'')?> >
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
                                                                if(!empty($_REQUEST['master_id']) && $row['PkLocID'] == $master_data['province_id']){
                                                                    $sel = "selected='selected'";
                                                                }
                                                                elseif($_REQUEST['province'] == $row['PkLocID']) $sel="selected";
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
                                    
                                    
                                    <?php
                                    if(!empty($master_id) && $master_data['level'] == 3)
                                    {
                                        ?>
                                            <div class="row"  >
                                                <div class="col-md-12">

                                                    <div class="col-md-6" >
                                                        District :
                                                    </div>
                                                    <div class="col-md-6" >
                                                        <input class="form-control" disabled value="<?=$master_data['dist_name']?>" >
                                                    </div>


                                                </div>
                                            </div>
                                        <?php
                                            
                                    }
                                    ?>
                                            
                                            
                                    <div class="row" style="display:none;" id="district_div">
                                        <div class="col-md-12">
                                             
                                            <div   id="district_data">
                                                 
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
                                                        <input type="submit" name ="submit" id="submit" value="Save"  style="display:<?=(!empty($master_id)?'none':'')?>;" class="btn input-sm green">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                             
                                    </div>
                                        
                                        
                                        <div class="col-md-12">
                                                <div class="row" style="display:<?=(!empty($master_id)?'':'none')?>;">
                                            <div class="col-md-12">

                                                <div class="col-md-4">
                                                    <div class="control-group">
                                                        <label>Products</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <?php
                                                            foreach($item_data as $prod_id => $prod_data){
                                                                $prod_name = $prod_data['itm_name'];
                                                                $already_saved=false;
                                                                if(!empty($saved_data[$prod_id])) $already_saved=true;
                                                                if($already_saved){
                                                                    echo '<div class="row form-group" >';
                                                                    echo '<div class="col-md-12">';
                                                                    echo '<div class="col-md-3">';
                                                                    echo '<input disabled checked type="checkbox" >&nbsp;';
                                                                     echo '<a class="btn btn-circle green-jungle"   target="_blank"  > '.$prod_name.'</a>';
                                                                     echo '</div>';
                                                                    echo '<div class="col-md-3">';
                                                                        echo '<span class="font-purple1 btn">AMC:</span> <span class="pull-right1 badge badge-primary"> '.number_format($saved_data[$prod_id]['average_amc_of_base_years']).'</span> ';
                                                                    echo '</div>';
                                                                    
                                                                    echo '<div class="col-md-5">';
                                                                        echo '<span class="font-purple1 btn">Base Years:</span>';
                                                                        if(!empty($saved_data[$prod_id]['base_year_1']))
                                                                            echo '<span class="label label-primary">'.$saved_data[$prod_id]['base_year_1'] . '-' . substr($saved_data[$prod_id]['base_year_1'] + 1, 2).'</span>  ';
                                                                        
                                                                        if(!empty($saved_data[$prod_id]['base_year_2']))
                                                                            echo '<span class="label label-primary ">'.$saved_data[$prod_id]['base_year_2']. '-' . substr($saved_data[$prod_id]['base_year_2'] + 1, 2).'</span> ';
                                                                        
                                                                        if(!empty($saved_data[$prod_id]['base_year_3']))
                                                                            echo '<span class="label label-primary ">'.$saved_data[$prod_id]['base_year_3']. '-' . substr($saved_data[$prod_id]['base_year_3'] + 1, 2).'</span>';
                                                                    echo '</div>';

                                                                    echo '</div>';
                                                                    echo '</div>';

                                                                }
                                                                else{
                                                                    echo '<div class="row form-group" >';
                                                                     echo '<div class="col-md-12">';
                                                                    echo '<div class="col-md-3">';
                                                                    echo '<input id="prod_'.$prod_id.'" data-id="'.$prod_id.'"  type="checkbox" class="product_checkbox" name="purpose" value="" />';
                                                                    echo ' '.$prod_name.' ';                                                                                        
                                                                    echo '</div>';
                                                                    echo '<div col-md-5>';
                                                                    echo '<a id="btn_prod_'.$prod_id.'" style="display:none;"  target="_blank" class="btn" href="product_base_data.php?prod_id='.$prod_id.'&master_id='.$_REQUEST['master_id'].'&prod_name='.$prod_name.'">Click here for setting the base year.</a>';
                                                                    echo '</div>';  
                                                                    echo '</div>';
                                                                    echo '</div>';
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        </div>
                                        <div class="col-md-12">
                                            
                                                <div class="" style="display:<?=(!empty($master_id)?'':'none')?>;">
                                                    <div class="row">
                                                        <div class="col-md-12">

                                                            <div class="col-md-4">

                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="control-group">
                                                                    <div class="controls">
                                                                        <?php
/*
                                                                        foreach($item_data as $prod_id => $prod_data){
                                                                            $prod_name = $prod_data['itm_name'];
                                                                            $already_saved=false;
                                                                            if(!empty($saved_data[$prod_id])) $already_saved=true;

                                                                            if($already_saved){
                                                                                echo '<div class="row form-group" id="btn_prod1_'.$prod_id.'" style=""  >';
                                                                                    echo '<div class="col-md-12">';
                                                                                        echo '<div class="col-md-3">';
                                                                                            echo '<a class="btn btn-circle green-jungle"   target="_blank"  >'.$prod_name.'</a>';
                                                                                        echo '</div>';
                                                                                        echo '<div class="col-md-3">';
                                                                                            echo '<span class="font-purple h4">AMC:</span> <span class="pull-right"> '.number_format($saved_data[$prod_id]['average_amc_of_base_years']).'</span> ';
                                                                                        echo '</div>';
                                                                                        echo '<div class="col-md-1">';echo '</div>';
                                                                                        echo '<div class="col-md-5">';
                                                                                            echo '<span class="font-purple h4">Base Years:</span> '.$saved_data[$prod_id]['base_year_1'].','.$saved_data[$prod_id]['base_year_2'].','.$saved_data[$prod_id]['base_year_3'];
                                                                                        echo '</div>';
                                                                                    echo '</div>';
                                                                                echo '</div>';
                                                                            }
                                                                            else{
                                                                                echo '<div class="row form-group" id="btn_prod_'.$prod_id.'" style="display:none;"  >';
                                                                                    echo '<div class="col-md-12">';
                                                                                        echo '<div class="col-md-3">';
                                                                                            echo '<a class="btn btn-circle blue-madison"   target="_blank" href="product_base_data.php?prod_id='.$prod_id.'&master_id='.$_REQUEST['master_id'].'&prod_name='.$prod_name.'">'.$prod_name.'</a>';
                                                                                        echo '</div>';
                                                                                        echo '<div class="col-md-6">';
                                                                                            echo '<a   target="_blank" class="btn" href="product_base_data.php?prod_id='.$prod_id.'&master_id='.$_REQUEST['master_id'].'&prod_name='.$prod_name.'">Click here for setting the base year.</a>';
                                                                                        echo '</div>';
                                                                                    echo '</div>';
                                                                                echo '</div>';
                                                                            }

                                                                        }
 * 
 */
                                                                        ?>

                                                                    </div>
                                                                </div>
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
                                                        
                                                        <a class="btn  green" href="forecasting_list.php"><i class="fa fa-list1 fa-arrow-left" style="color:#000 !important;"></i> Back to Forecasting List</a>
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
        
    $('.product_checkbox').on('change',function(){
        var this_id = $(this).data('id');
       if($(this).is(':checked'))
       {
           //alert('tick');
           $('#btn_prod_'+this_id).show();
       }
       else
       {
           //alert('empty');
           $('#btn_prod_'+this_id).hide();
       }
    });
    
    $('#start_year').on('change',function(){
       var st = $(this).val();
       $("#end_year option").show();
       $("#end_year option").filter(function(){
            return $(this).val() < st;
        }).hide(); 
        $("#end_year option[value='']").show();
    });
    
    $('#task_order').on('change',function(){
       var to = $(this).val();
       if(to=='to3') to='fp';
       else to = 'mnch';
       window.location.href = "forecasting_master.php?product_category="+to; 
    });
    
    
    function showDistricts(provId)
        {
            var officeLevel = $('#office_level').val();
            if (officeLevel == 3)
            {
                $('#district_div').show();
                $('#district').html('<option value="">Loading...</option>');
                $.ajax({
                    type: "POST",
                    url: "load_dist.php",
                    data: {provinceId: provId, validate: 'yes'},
                    dataType: 'html',
                    success: function (data)
                    {
                        $('#district_data').html(data);
                    }
                });
            }
        }
        function officeType(officeLevel)
        {
            if (parseInt(officeLevel) == 2)
            {
                $('#province_div').show();
                $('#district_div').hide();
            } else if (parseInt(officeLevel) == 3)
            {
                $('#province_div').show();
                $('#province').val('');
                $('#district_div').show();
            }
        }
        $(function () {
            $('#office_level').change(function () {
                officeType($(this).val());
            });
            $('#province').change(function () {
                var provId = $(this).val();
                showDistricts(provId);
            });
        })
    </script>
</body>
</html>