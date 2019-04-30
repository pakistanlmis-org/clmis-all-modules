<?php
ini_set('max_execution_time',60);
include("../includes/classes/Configuration.inc.php");
if(!isset($_SESSION['user_id']) && $_REQUEST['map']==1){
    $_SESSION[user_id] = 2054;
    $_SESSION[user_role] = 16;
    $_SESSION[user_name] = Guest;
    $_SESSION[user_warehouse] = 123;
    $_SESSION[user_stakeholder] = 1;
    $_SESSION[user_stakeholder_office] = 1;
   
    $_SESSION[user_province] = 10;
    $_SESSION[user_district] = 15;
    $_SESSION[is_allowed_im] = 0;
    
    $_SESSION[user_stakeholder_type] = 0;
    $_SESSION[user_province1] = 10;
    $_SESSION[user_stakeholder1] = 1;
    $_SESSION[landing_page] = "application/dashboard/dashboard.php";
    $_SESSION[menu] = "C:/xampp/htdocs/clmis/public/html/top.php";
   

    $_SESSION[im_open] = 0;
}
Login();
if(isset($_REQUEST['submit'])){
    //echo '<pre>';print_r($_REQUEST);exit;
}
include(APP_PATH . "includes/classes/db.php");
include APP_PATH . "includes/classes/functions.php";
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
include(PUBLIC_PATH . "html/header.php");

$report_id = "CD";
$rep_level='';
$chart_id = 'compliance_sdp';
$selDist='';
$selPro=$selStk='1';
if (isset($_REQUEST['submit'])) {
      //echo '<pre>';print_r($_REQUEST);exit;
    $selMonth = !empty($_REQUEST['ending_month']) ? $_REQUEST['ending_month'] : '';
    $selYear = !empty($_REQUEST['year_sel']) ? $_REQUEST['year_sel'] : '';
    $selStk = !empty($_REQUEST['stk_sel']) ? $_REQUEST['stk_sel'] : '';
    $selPro = !empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '';
    $selDist = !empty($_REQUEST['district']) ? $_REQUEST['district'] : '';
    
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
                        <h3 class="page-title row-br-b-wp">SDP  Wise Compliance Report</h3>
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
                                                    <label>Stakeholder</label>
                                                    <div class="controls">
                                                        <select name="stk_sel" id="stk_sel"  class="form-control input-sm">
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
                                                            $stk_name = '';
                                                            while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                //selected stakeholder
                                                                if ($selStk == $rowstk['stkid']) {
                                                                    $sel = "selected='selected'";
                                                                    $stk_name = $rowstk['stkname']; 
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
                                            <div class="col-md-2">
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
                                                            $province_name='';
                                                            while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                if ($selPro == $rowprov['PkLocID']) {
                                                                    $sel = "selected='selected'";
                                                                    $province_name = $rowprov['LocName'];
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
                                            <div class="control-group" id="districtsCol">
                                                <label>District</label>
                                                <div class="controls">
                                                    <select name="district" id="district"  class="form-control input-sm" required>
                                                        <?php
                                                        //select query
                                                        //gets
                                                        //district id
                                                        //district name
                                                        $queryDist = "SELECT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 3
                                                                        AND tbl_locations.parentid = '" . $selPro . "'
                                                                        ORDER BY
                                                                                tbl_locations.LocName ASC";
                                                        //query result
                                                        $rsDist = mysql_query($queryDist) or die();
                                                        //fetch result
                                                        $dist_name ='';
                                                        while ($rowDist = mysql_fetch_array($rsDist)) {
                                                            if ($selDist == $rowDist['PkLocID']) {
                                                                $sel = "selected='selected'";
                                                                $dist_name = $rowDist['LocName'];
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            //populate district combo
                                                            ?>
                                                            <option
                                                                value="<?php echo $rowDist['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowDist['LocName']; ?></option>
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
if (isset($_REQUEST['submit'])) {
$where_clause ="   ";
$where_clause .=" AND tbl_warehouse.prov_id =$selPro ";
$where_clause .=" AND tbl_warehouse.stkid =$selStk ";
$where_clause .=" AND tbl_warehouse.dist_id =$selDist ";
        //get total number of facilities in province
        $qry_1 = "  
                SELECT
                    
                    DISTINCT tbl_warehouse.wh_id,
                    tbl_warehouse.wh_name,
                    tbl_warehouse.dhis_code,
                    tbl_warehouse.reporting_start_month
                FROM
                        tbl_warehouse
                                             
                     ";
        if(!($selPro == 3 && $selStk ==7 ))
        $qry_1 .= "  INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id ";
        $qry_1 .= "
            
                
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
                WHERE
                stakeholder.lvl = 7
                /*AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)*/
                $where_clause
                
                 
                ORDER BY
                    tbl_warehouse.wh_rank,
                    tbl_warehouse.wh_name ASC
                ";
            //echo $qry_1;exit;
                $res_1 = mysql_query($qry_1);
                $total_sdps= $rep_start_months= array();
                while($row_1 = mysql_fetch_array($res_1))
                {
                    $total_sdps[$row_1['wh_id']]=$row_1['wh_name'].(!empty($row_1['dhis_code'])?' - '.$row_1['dhis_code']:'');
                    $rep_start_months[$row_1['wh_id']]=$row_1['reporting_start_month'];
                    
                    if(!isset($total_sdps['all'])) $total_sdps['all']=0;
                    $total_sdps['all']+=1;
                }
        //echo'<pre>'; print_r($rep_start_months);exit;

         //counting the disabled facilities 
         $disabled_qry = "
                    SELECT
                        DISTINCT warehouse_status_history.warehouse_id,
                        
                        warehouse_status_history.reporting_month
                    FROM
                            warehouse_status_history
                    INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                    INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
                                                      
                     ";
        if(!($selPro == 3 && $selStk ==7 ))
        $disabled_qry .= "  INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id ";
        $disabled_qry .= "
            
                    
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    WHERE
                            warehouse_status_history.reporting_month BETWEEN '".$start_date."' and '".$last_date."'
                            AND warehouse_status_history.`status` = 0
                            /*AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)*/
                            AND stakeholder.lvl=7
                            $where_clause
                     
            ";
        //echo $disabled_qry;exit;
        $res_d = mysql_query($disabled_qry);
        $disabled_count= array();
        while($row_d = mysql_fetch_array($res_d))
        {
            $disabled_count[$row_d['warehouse_id']][$row_d['reporting_month']]='disabled';
        }   
        //echo '<pre>';print_r($disabled_count);exit;      
        //making list of items , to display list incase no data entry is found
         
        $w_clause="";
        if(!empty($stk))             
            $w_clause .= " AND stakeholder_item.stkid in (".$stk.")  ";    

        if(!empty($itm))             
            $w_clause .= " AND itminfo_tab.itm_id in (".$itm.")  ";    

                //query for getting reported facilities
                $q_reporting  = "SELECT
                                        
                                        DISTINCT tbl_warehouse.wh_id,
                                        tbl_warehouse.stkid,
                                        tbl_warehouse.dist_id,
                                        tbl_locations.LocName,tbl_hf_data.reporting_date
                                FROM
                                        tbl_warehouse
                                                                             
                     ";
        if(!($selPro == 3 && $selStk ==7 ))
        $q_reporting .= "  INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id ";
        $q_reporting .= "
            
                                
                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                WHERE
                                     stakeholder.lvl = 7
                                     /*AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)*/
                                     AND tbl_warehouse.reporting_start_month <=tbl_hf_data.reporting_date
                                AND tbl_hf_data.reporting_date BETWEEN '".$start_date."' and '".$last_date."'
                                
                                $where_clause
                                GROUP BY
                                        tbl_warehouse.wh_id,
                                        tbl_hf_data.reporting_date 
                                ";
                //echo $q_reporting;exit;
                $res_reporting = mysql_query($q_reporting);
                $reporting_wh_arr  = $dist_arr = array();
                $total_reporting_wh = 0;
                //$prov_arr['all']='Aggregated';
                while($row=mysql_fetch_assoc($res_reporting))
                {
                    $reporting_wh_arr[$row['wh_id']][$row['reporting_date']]=1;
                    $total_reporting_wh +=1;
                }

                 
        ?>
                <div class="row">
                    <div class="col-md-12">
                        <table width="100%" cellpadding="0" cellspacing="0" id="myTable1" class="table table-bordered table-condensed">
                             <tr>
                                <td align="center" width="90%"   ><h3 class="text-info">Compliance Report SDP  Wise - <?=$province_name?> - <?=$dist_name?> - <?=$stk_name?></h3></td>
                                <td align="center" width="10%"   >
                                     <img title="Click here to export data to PDF file" style="cursor:pointer;" src="<?php echo PUBLIC_URL ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');"/>
                                     <img title="Click here to export data to Excel file" style="cursor:pointer;" src="<?php echo PUBLIC_URL ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');"/>
                                    </td>
                             </tr>
                        </table>
                         
                        <div>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" id="compliance_HF" class="table table-bordered table-condensed">
                            <tr class=" bg-blue-madison">
                                <td colspan="">#</td>
                                <td colspan="">Health Facility</td>
                                <?php
                                foreach($months_list as $k => $month ){
                                    echo '<td align="center">'.date('M-Y',strtotime($month)).'</td>';
                                }
                                ?>
                            </tr>
                            <?php
                                $c=1;
                                $total_reported = $total_active= array();
                                foreach($total_sdps as $wh_id => $wh_name ){
                                    if($wh_id == 'all') continue;
                                    echo ' <tr>
                                            <td>'.$c++.'</td>
                                            <td>'.$wh_name.'</td>';
                                    foreach($months_list as $k => $month ){
                                        $m1=date('m',strtotime($month));
                                        $y1=date('Y',strtotime($month));
                                        $perc=0;
                                        if( !empty($reporting_wh_arr[$wh_id][$month])   ){
                                            $perc = $reporting_wh_arr[$wh_id][$month];
                                            @$total_reported[$month]++;
                                            @$total_active[$month]++;
                                            $display = '<span style="display:none">1</span>';
                                            $display .= '<span onclick="window.open(\'wh_info.php?whId='.$wh_id.'&month='.$m1.'&year='.$y1.'\', \'_blank\', \'scrollbars=1,width=900,height=500\')">';
                                            $display .= '<i class="fa fa-check" style="font-size:20px;color:green !important;"></i>';
                                            $display .= '</span>';
                                        }
                                        elseif($rep_start_months[$wh_id] > $month){
                                            $display = '*';
                                        }
                                        elseif(!empty($disabled_count[$wh_id][$month])){
                                             $display = 'Left';
                                        }
                                        else{
                                            
                                            $display = '<span style="display:none">0</span>';
                                            $display .= '<i class="fa fa-times" style="font-size:15px;color:red !important;"></i>';
                                            @$total_active[$month]++;
                                        }
                                        
                                        
                                        
                                        if($display=='*'){  
                                            echo '<td  align="center" class="bg-grey-silver">';
                                            echo '<span style="display:none">Not_present</span>';
                                            echo '</td>';
                                        }
                                        elseif($display=='Left'){  
                                            echo '<td  align="center" class="danger">';
                                            echo '<span style="display:none">Left</span>';
                                            echo '</td>';
                                        }
                                        else{
                                            echo '<td  align="center">';
                                            echo $display;
                                            echo '</td>';
                                        }
                                            
                                    }
                                    echo ' </tr>';
                                }
                                
                                $rep_percentage = array();
                                echo ' <tr style="background-color:#a3cced">
                                        <td colspan=""> </td>
                                        <td colspan="">Reported Facilities</td> ';
                                    foreach($months_list as $k => $month ){
                                        echo '<td  align="center">';
                                        echo (!empty($total_reported[$month])?$total_reported[$month]:'0');
                                        echo '</td>';
                                        
                                        if(!empty($total_active[$month]) && $total_active[$month]>0)
                                            $rep_percentage[$month] = 100*(!empty($total_reported[$month])?$total_reported[$month]:'0') / $total_active[$month];
                                        else
                                            $rep_percentage[$month] = 0;
                                    }
                                echo ' </tr>';
                                echo ' <tr style="background-color:#6EA6D4">
                                        <td colspan=""> </td>
                                        <td colspan="">Total Active Facilities</td> ';
                                    foreach($months_list as $k => $month ){
                                        echo '<td  align="center">';
                                        echo (!empty($total_active[$month])?$total_active[$month]:'0');
                                        echo '</td>';
                                    }
                                echo ' </tr>';
                                ?>
                        </table>
                            
                        </div>
                        
                        <?php
                        $xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
                        $xmlstore .= "<rows>";
                       
                                $c=1;
                                $total_reported = $total_active= array();
                                foreach($total_sdps as $wh_id => $wh_name ){
                                    if($wh_id == 'all') continue;
                                    $xmlstore .= "<row>";
                                    $xmlstore .= "<cell>".$c++."</cell>";
                                    $xmlstore .= "<cell>".$wh_name."</cell>";
                                    foreach($months_list as $k => $month ){
                                        $m1=date("m",strtotime($month));
                                        $y1=date('Y',strtotime($month));
                                        $perc=0;
                                        if( !empty($reporting_wh_arr[$wh_id][$month])   ){
                                            $perc = $reporting_wh_arr[$wh_id][$month];
                                            @$total_reported[$month]++;
                                            @$total_active[$month]++;
                                            $xmlstore .= "<cell><![CDATA[&radic;]]></cell>";
                                        }
                                        elseif($rep_start_months[$wh_id] > $month){
                                            $xmlstore .= "<cell>*</cell>";
                                        }
                                        elseif(!empty($disabled_count[$wh_id][$month])){
                                             $xmlstore .= "<cell>Left</cell>";
                                        }
                                        else{
                                            $xmlstore .= "<cell><![CDATA[&Chi;]]></cell>";
                                            @$total_active[$month]++;
                                        }
                                    }
                                    $xmlstore .= "</row>";
                                }
                                
                                $rep_percentage = array();
                                
                                $xmlstore .= "<row>";
                                    $xmlstore .= "<cell> </cell>";
                                    $xmlstore .= "<cell>Reported Facilities</cell>";
                                    foreach($months_list as $k => $month ){
                                            
                                    $xmlstore .= "<cell>".(!empty($total_reported[$month])?$total_reported[$month]:'0')."</cell>";
                                        
                                        if(!empty($total_active[$month]) && $total_active[$month]>0)
                                            $rep_percentage[$month] = 100*(!empty($total_reported[$month])?$total_reported[$month]:'0') / $total_active[$month];
                                        else
                                            $rep_percentage[$month] = 0;
                                    }
                                    $xmlstore .= "</row>";
                                    $xmlstore .= "<row>";
                                        $xmlstore .= "<cell> </cell>";
                                        $xmlstore .= "<cell>Total Active Facilities</cell>";
                                    
                                    foreach($months_list as $k => $month ){
                                        $xmlstore .= "<cell>".(!empty($total_active[$month])?$total_active[$month]:'0')."</cell>";
                                    }
                                $xmlstore .= "</row>";
                            $xmlstore .= "</rows>";
                                ?>
                            <div id="mygrid_container" style="width:100%; height:450px;"  ></div>  
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="note note-info">
                            <p>
                                <b>Legend:</b><br/> 
                                <div class="col-md-1">
                                    <div style="width:30px; height:15px; background-color:#bfbfbf;border:1px solid green;" title=""></div> 
                                </div>
                                <div  class="col-md-4">= Not present / Reporting not started </div>
                                <br/> 
                                <div class="col-md-1">
                                    <div style="width:30px; height:15px; background-color:#ffcdc9;border:1px solid green;" title=""></div> 
                                </div>
                                <div  class="col-md-4">=Left the job / Deactivated</div> <br/>
                                <div class="col-md-1"><i class="fa fa-check" style="color:green !important;"></i> </div>
                                <div  class="col-md-4">= Reported </div><br/> 
                                <div class="col-md-1"><i class="fa fa-times" style="color:red !important;"></i> </div>
                                <div  class="col-md-4">= Not reported</div>
                            </p>
                        </div>
                    </div>
                </div>

<div class="widget widget-tabs">    
    <div class="widget-body">
<?php
//xml for chart
    $chart_data = '<chart caption="Monthly Reporting Rate - '.$province_name.' - '.$stk_name.' - '.$dist_name.' " exportenabled="0"  subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Months" yaxisname="Percentage" showvalues="1" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
 
    $chart_data .= ' <categories>';
    foreach($months_list as $k => $month)
    {
        $chart_data .= ' <category label="'.date('Y-M',strtotime($month)).'" />';
    }
    $chart_data .= ' </categories>';
    
    $chart_data .= ' <dataset seriesname="Total Reporting Rate">';
    foreach($months_list as $k => $month)
    {   
        $val=(!empty($rep_percentage[$month])? $rep_percentage[$month]:'0');
        $chart_data .= '    <set  value="'.number_format($val,2).'"  />';
    }
    $chart_data .= '  </dataset>';
    
    $chart_data .= ' </chart>';
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSSpline.swf", "", $chart_data, $chart_id, '100%', 300, false, false);
}//end of submit
?>
    </div>
</div>     
            </div>
        </div>
    </div>
    <?php 
 
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; 
    //include reports_include
    include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script> 
        function showProvinces(pid) {
            
                var stk = $('#stk_sel').val();
                if (typeof stk !== 'undefined')
                {
                        $.ajax({
                                url: 'ajax_stk.php',
                                type: 'POST',
                                data: {stakeholder: stk, provinceId: pid, showProvinces: 1, hfProvOnly: 1,showAllOpt:0},
                                success: function(data) {
                                        $('#prov_sel').html('<option value="">Select</option>');
                                        $('#prov_sel').append(data);
                                }
                        })
                }
        }
        
        function showDistricts(prov, stk) {
            if (stk != '' && prov != '')
            {
                $.ajax({
                    type: 'POST',
                    url: 'my_report_ajax.php',
                    data: {provId: prov, stkId: stk, distId: '<?php echo $selDist; ?>', showAll: 1},
                    success: function(data) {
                        $("#district").html(data);
                    }
                });
            }
        }
        $(function() {
                showDistricts('<?php echo $selPro; ?>', '<?php echo $selStk; ?>');
                $('#stk_sel').change(function(e) {
                        $('#prov_sel').html('<option value="">Select</option>');
                        showProvinces('');
                });
                
                $('#prov_sel, #stk_sel').change(function(e) {
                    $('#district').html('<option value="">Select</option>');
                    showDistricts($('#prov_sel').val(), $('#stk_sel').val());
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
    

<?php
//echo '<pre>';print_r($months_list);exit;
$cspan = $header = $width = $ro = $align = $stkName = $locName = '';
$header .= "<span title='#'>#</span>";
$header .= ",<span title='SDP Name'>SDP  Name </span>";
$cspan .= ",#cspan";
foreach($months_list as $k => $month ){
    echo $month.' , ';
    $header .= ",<span title='".date('M Y',strtotime($month))."'>".date('M Y',strtotime($month))."</span>";
    $cspan .= ",#cspan";
}
?>
<script>
var mygrid;
    function doInitGrid() {
        mygrid = new dhtmlXGridObject('mygrid_container');
        mygrid.selMultiRows = true;
        mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
        mygrid.setHeader("<div style='text-align:center;'>Compliance Report SDP  Wise - <?=$province_name?> - <?=$dist_name?> - <?=$stk_name?></div><?=$cspan?>");
        mygrid.attachHeader("<?=$header?>");
        mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?> <br> <br>* = Not present <br> Left = Left the job <br>&radic; = Reported <br> &Chi; = Not reported</div><?php echo $cspan; ?>");

        mygrid.setColAlign("left,left,right,right,right,right,right,right,right,right,right,right,right,right");
        mygrid.setInitWidths("40,*,80,80,80,80,80,80,80,80,80,80,80,80");
        mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
        mygrid.enableRowsHover(true, 'onMouseOver'); // `onMouseOver` is the css cla ss name.
        mygrid.setSkin("light");
        mygrid.init();
        mygrid.clearAll();
        mygrid.loadXMLString('<?php echo $xmlstore; ?>');
    }
    doInitGrid();    
    $("#mygrid_container").hide();
</script>
</body>
</html>