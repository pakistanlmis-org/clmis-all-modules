<?php
ini_set('max_execution_time',60);
include("../includes/classes/Configuration.inc.php");
//login
Login();
if(isset($_REQUEST['submit'])){
    //echo '<pre>';print_r($_REQUEST);exit;
}

//include db
include(APP_PATH . "includes/classes/db.php");
//include  functions
include APP_PATH . "includes/classes/functions.php";
//include fusion chart
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
//include header
include(PUBLIC_PATH . "html/header.php");
$report_id = "CD";
$rep_level='';
if (isset($_POST['submit'])) {
    $rep_level=!empty($_REQUEST['facility_level']) ? $_REQUEST['facility_level'] : '3';
    $selMonth = !empty($_REQUEST['ending_month']) ? $_REQUEST['ending_month'] : '';
    $selYear = !empty($_REQUEST['year_sel']) ? $_REQUEST['year_sel'] : '';
    $selStk = !empty($_REQUEST['stk_sel']) ? $_REQUEST['stk_sel'] : '';
    $selPro = !empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '';
    
    $last_date = date("Y-m-t", strtotime($selYear ."-".$selMonth."-01"));;
    $months_list=array();
    $months_list[] = date("Y-m-01", strtotime($selYear ."-".$selMonth."-01"));
    for ($i = 1; $i < 12; $i++) {
        $months_list[]  =   date('Y-m-01', mktime(0, 0, 0, $selMonth-$i, 1,   $selYear));
        $start_date       =   date('Y-m-01', mktime(0, 0, 0, $selMonth-$i, 1,   $selYear));
    }
    krsort($months_list);
    //echo '<pre>';print_r($months_list);exit;
    //echo $last_date.' , '.$start_date;
} 
$endDate = '';
$startDate = '';
  

?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="">
    
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>
    <div class="page-container">
        <?php 
            include PUBLIC_PATH . "html/top.php";
            include PUBLIC_PATH . "html/top_im.php"; 
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">District Wise Compliance Report</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post">
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Ending Month</label>
                                                    <div class="controls">
                                                        <select name="ending_month" id="ending_month" class="form-control input-sm">
                                                            <?php
                                                            for ($i = 1; $i <= 12; $i++) {
                                                                //check selected month
                                                                if ($selMonth == $i) {
                                                                    $sel = "selected='selected'";
                                                                } else if ($i == 1) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo date('m', mktime(0, 0, 0, $i, 1)); ?>"<?php echo $sel; ?> ><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Year</label>
                                                    <div class="controls">
                                                        <select name="year_sel" id="year_sel" class="form-control input-sm">
                                                            <?php
                                                            for ($j = date('Y'); $j >= 2010; $j--) {
                                                                //check selected year
                                                                if ($selYear == $j) {
                                                                    $sel = "selected='selected'";
                                                                } else if ($j == 1) {
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
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Level</label>
                                                    <div class="controls">
                                                        <select name="facility_level" id="facility_level" class="form-control input-sm">
                                                            <option value="3" <?=($rep_level=='3')?' selected ':''?>>District Stores</option>
                                                            <option value="7" <?=($rep_level=='7')?' selected ':''?>>Health Facility</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Stakeholder</label>
                                                    <div class="controls">
                                                        <select name="stk_sel" id="stk_sel"  class="form-control input-sm" required>
                                                            <option value="">Select</option>
                                                            <?php
                                                            $querystk = "SELECT
                                                                                    stkid,
                                                                                    stkname
                                                                            FROM
                                                                                    stakeholder
                                                                            WHERE
                                                                                    stakeholder.ParentID IS NULL AND
                                                                                    stakeholder.stk_type_id = 0 AND
                                                                                    stakeholder.lvl = 1 AND
                                                                                    stakeholder.is_reporting = 1
                                                                            ORDER BY
                                                                                    stkorder";
                                                            //query result
                                                            $rsstk = mysql_query($querystk) or die();
                                                            //fetch result
                                                                $stk_name = 'All Stakeholders';
                                                            while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                //selected stakeholder
                                                                if ($selStk == $rowstk['stkid']) {
                                                                    $stk_name= $rowstk['stkname'];
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $rowstk['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowstk['stkname']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="province_div" style="<?=(!empty($_REQUEST['facility_level']) && $_REQUEST['facility_level']=='7')?'':'display:none;'?>">
                                                <div class="control-group">
                                                    <label>Province</label>
                                                    <div class="controls">
                                                        <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                                            
                                                            <?php
                                                            $queryprov = "SELECT DISTINCT
                                                                                    tbl_locations.PkLocID,
                                                                                    tbl_locations.LocName
                                                                            FROM
                                                                                    tbl_locations
                                                                            
                                                                            WHERE
                                                                                    tbl_locations.ParentID IS NOT NULL
                                                                                    AND tbl_locations.LocLvl = 2  
                                                                                    AND tbl_locations.LocType = 2  
                                                                            ORDER BY
                                                                                    tbl_locations.PkLocID";
                                                            //result
                                                            $rsprov = mysql_query($queryprov) or die();
                                                            //fetch result
                                                            $province_name= 'All Provinces';
                                                            while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                if ($selPro == $rowprov['PkLocID']) {
                                                                    $province_name= $rowprov['LocName'];
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $rowprov['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowprov['LocName']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name="submit" id="go" value="GO" class="btn btn-primary input-sm" />
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
                
        <?php
        if(isset($_REQUEST['submit'])){
            if($rep_level=='7')
                include "compliance_inc_sdp.php";
            else
                include "compliance_inc_dist_stores.php";
        }
        ?>
            </div>
        </div>
    </div>
    
    <?php 
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; 
    //include reports_include
    include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    
<!--    <script src="<?=PUBLIC_URL?>js/jspdf.min.js" type="text/javascript"></script>
    <script src="<?=PUBLIC_URL?>js/html2canvas.js" type="text/javascript"></script>-->

    <script> 
         <?php
        if(isset($_REQUEST['submit'])){
           echo ' doInitGrid(); ';
            echo ' $("#mygrid_container").hide();';
        }
        ?>
       
        
        
        function showProvinces(pid) {
            var stk = $('#stk_sel').val();
            if (typeof stk !== 'undefined')
            {
                $.ajax({
                    url: 'ajax_stk.php',
                    type: 'POST',
                    data: {stakeholder: stk, provinceId: pid, showProvinces: 1, hfProvOnly: 1,showAllOpt:0},
                    success: function(data) {
                            $('#prov_sel').html(data);
                    }
                })
            }
        }
        
        $(function() {
                $('#stk_sel').change(function(e) {
                        $('#prov_sel').html('<option value="">Select</option>');
                        showProvinces('');
                });
                $('#facility_level').change(function(e) {
                    var v = $(this).val();
                    if(v==7){
                        $('#province_div').show();
                    }
                    else{
                        $('#province_div').hide();
                    }
                });
        })
        <?php
        if (isset($selPro) && !empty($selPro)) {
                ?>
                        showProvinces('<?php echo $selPro; ?>');
                <?php
        }
        ?>
    </script>
</body>
</html>