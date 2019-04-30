<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
//echo '<pre>';print_r($_REQUEST);exit;


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

$disabled = ' disabled ';
if(TRUE)
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
                fq_demographics_master.`level`,
                tbl_locations.LocName,
                fq_demographics_child.`last_modified`
            FROM
                fq_demographics_master
            INNER JOIN fq_demographics_child ON fq_demographics_child.master_id = fq_demographics_master.pk_id
            INNER JOIN tbl_locations ON fq_demographics_child.location_id = tbl_locations.PkLocID
            ORDER BY
                fq_demographics_master.`year` DESC,
                fq_demographics_master.`level`

";
//echo $qry;
    $rsQry = mysql_query($qry);
    $dmg_previous_data = $loc_arr = $modified_arr =$src_arr=$level_arr = array();
    while ($row = mysql_fetch_assoc($rsQry)) {
        $level_arr[$row['year']][$row['master_id']]=$row['level'];
        $src_arr[$row['year']][$row['master_id']]=$row['source'];
        $modified_arr[$row['year']][$row['master_id']]=$row['last_modified'];
        $loc_arr[$row['location_id']]=$row['LocName'];
        $master_id = $row['master_id'];
        $source = $row['source'];
        $dmg_previous_data[$row['year']][$row['master_id']][$row['location_id']][$row['col_id']] = $row['value'];
    }
}
//echo '<pre>';print_r($dmg_previous_data);
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
                        <h3 class="page-title row-br-b-wp">Demographics Data List
                         <?php include("back_include.php"); ?>                            

                        </h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Demographic Data</h3>
                            </div>
                            <div class="widget-body">
                               
                                    <?php
                                    if(TRUE)
                                    {
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
                                            
                                            <div class="">
						
						<div class="">
                                                        <div class="">
                                                                <div class="" id="div_table">
                                                                        <div class="portlet  ">
                                                                            <div class="portlet-body" >
                                                                                <div style="overflow:auto !important;">
                                                                                <table  class="table table-condensed table-hover table-bordered">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <?php
                                                                                            $to3_count = 0;
                                                                                            //echo '<td>Year</td>';
                                                                                            echo '<td>Demographics Info<br/>';
                                                                                            echo '<a class="btn btn-sm green-jungle" href="demographics_data_entry.php"><i class="fa fa-plus"></i> Add New</a>';
                                                                                            echo '</td>';
                                                                                            echo '<td>Location</td>';
                                                                                            foreach($all_cols as $col_id => $col_data)
                                                                                            {
                                                                                                echo '<td title="'.$col_data['long_name'].'">'.wordwrap($col_data['long_name'],30," <br />");
                                                                                                if($col_id != 5) {
                                                                                                    echo '<br /><span style="color:grey">('.$col_data['default_percentage'].' %)</span>';
                                                                                                    //echo '<a class="pull-right btn btn-xs blue dmg_calc_btn" data-col-id="'.$col_id.'"> <i style="" class=" fa fa-angle-double-down "></i></a>';
                                                                                                }
                                                                                                echo '</td>';
                                                                                                $to3_count++;
                                                                                            }
                                                                                            ?>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <?php
                                                                                           foreach($dmg_previous_data as $year => $yr_data)
                                                                                           {
                                                                                                $rowspan_y      = count($yr_data) ;
                                                                                                        
                                                                                                echo '<tr>';
                                                                                                echo '<td style="background-color:green;color:#fff;font-size:16px;"  colspan="20"><b>'.$year.'</b></td>';

                                                                                                foreach($yr_data as $master_id => $dmg_data)
                                                                                                {
                                                                                                    $rowspan_dmg    = count($dmg_data);
                                                                                                    echo '<tr>';
                                                                                                    echo '<td style="background-color:#E0D8E8;font-size:12px;"  rowspan="'.$rowspan_dmg.'">ID :<b>DMG_'.$master_id.'</b>';
                                                                                                    echo '<br/>Source :('.$src_arr[$year][$master_id].')';
                                                                                                    echo '<br/>Last modified at :('.date('Y-M-d H:i:s',strtotime($modified_arr[$year][$master_id])).')';
                                                                                                    echo '<br/> <a class="btn btn-sm blue-hoki" href="demographics_data_entry.php?year='.$year.'&source='.$src_arr[$year][$master_id].'&office_level='.$level_arr[$year][$master_id].'&province=&submit=Go"><i class="fa fa-edit"></i> Edit these values.</a>';
                                                                                                    echo '</td>';
                                                                                                    foreach($dmg_data as $loc_id => $loc_data)
                                                                                                    {
                                                                                                        $loc_name = $loc_arr[$loc_id];
                                                                                                        
                                                                                                        echo '<td>'.$loc_name.'</td>';
                                                                                                        foreach($all_cols as $col_id => $col_data)
                                                                                                        {
                                                                                                            echo '<td align="right">';
                                                                                                            $val = isset($dmg_previous_data[$year][$master_id][$loc_id][$col_id])?$dmg_previous_data[$year][$master_id][$loc_id][$col_id]:'';

                                                                                                            if(!empty($val))
                                                                                                            echo number_format($val);

                                                                                                        }
                                                                                                        echo '</tr>';
                                                                                                    }
                                                                                                }
                                                                                           }
                                                                                           ?>
                                                                                    </tbody>
                                                                                    
                                                                                </table>
                                                                                </div>
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
                                    ?>
                                    
                                
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
    <?php 
    include PUBLIC_PATH . "/html/footer.php"; ?>
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