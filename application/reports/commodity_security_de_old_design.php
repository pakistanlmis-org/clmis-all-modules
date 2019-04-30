<?php
ini_set('max_execution_time', 0);
/**
 * commodity security data entry
 * @package dashboard
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include Configuration
include("../includes/classes/Configuration.inc.php");
//login
Login();
if(isset($_REQUEST['submit_btn'])){
//    echo '<pre>';print_r($_REQUEST);exit;
}

//include db
include(APP_PATH . "includes/classes/db.php");
//include  functions
include APP_PATH . "includes/classes/functions.php";
//include fusion chart
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//funding source
$fundingSourceText = 'All Funding Sources';

//caption
$caption = 'Product wise Distribution and SOH';
//sub caption
$subCaption = '';
//downloadFileName 
$downloadFileName = 'a';
//chart id
$chart_id = 'distributionAndSOH';


$f_date= (!empty($_REQUEST['from_date'])?$_REQUEST['from_date']:date("Y-m").'-01');
$t_date= (!empty($_REQUEST['to_date'])?$_REQUEST['to_date']:$f_date);
$from_date = date("Y-m-d", strtotime($f_date));
$to_date = date("Y-m-t", strtotime($t_date));

$time1  = strtotime($from_date); 
$time2  = strtotime($to_date); 
$my     = date('mY', $time2); 

$months_list = array(date('Y-m-01', $time1)); 

if($f_date != $t_date){
    while($time1 < $time2) { 
       $time1 = strtotime(date('Y-m-d', $time1).' +1 month'); 
       if(date('mY', $time1) != $my && ($time1 < $time2)) 
          $months_list[] = date('Y-m-01', $time1); 
    } 

    $months_list[] = date('Y-m-01', $time2); 
}
$number_of_months = count($months_list);
//echo '<pre>';print_r($months_list);exit;

$tracer_product = array();
$tracer_product[]=1;
$tracer_product[]=5;
$tracer_product[]=7;
$tracer_product[]=9;

$province_arr = (!empty($_REQUEST['province'])?$_REQUEST['province']:'');
$stk_arr = (!empty($_REQUEST['stakeholder'])?$_REQUEST['stakeholder']:'');
$itm_arr_request = (!empty($_REQUEST['product'])?$_REQUEST['product']:$tracer_product);


if(isset($_REQUEST['submit_btn'])){
    $province = implode(',',$province_arr);
    $stk = implode(',',$stk_arr);
    $itm = implode(',',$itm_arr_request);
}
$where_clause ="";

if(!empty($province))   $where_clause .= " AND tbl_warehouse.prov_id in (".$province.")  ";
if(!empty($stk))        $where_clause .= " AND tbl_warehouse.stkid in (".$stk.")  ";

?>

    <style>
            span.multiselect-native-select {
                position: relative
            }
            span.multiselect-native-select select {
                border: 0!important;
                clip: rect(0 0 0 0)!important;
                height: 1px!important;
                margin: -1px -1px -1px -3px!important;
                overflow: hidden!important;
                padding: 0!important;
                position: absolute!important;
                width: 1px!important;
                left: 50%;
                top: 30px
            }
            .multiselect-container {
                position: absolute;
                list-style-type: none;
                margin: 0;
                padding: 0
            }
            .multiselect-container .input-group {
                margin: 5px
            }
            .multiselect-container>li {
                padding: 0
            }
            .multiselect-container>li>a.multiselect-all label {
                font-weight: 700
            }
            .multiselect-container>li.multiselect-group label {
                margin: 0;
                padding: 3px 20px 3px 20px;
                height: 100%;
                font-weight: 700
            }
            .multiselect-container>li.multiselect-group-clickable label {
                cursor: pointer
            }
            .multiselect-container>li>a {
                padding: 0
            }
            .multiselect-container>li>a>label {
                margin: 0;
                height: 100%;
                cursor: pointer;
                font-weight: 400;
                padding: 3px 0 3px 30px
            }
            .multiselect-container>li>a>label.radio, .multiselect-container>li>a>label.checkbox {
                margin: 0
            }
            .multiselect-container>li>a>label>input[type=checkbox] {
                margin-bottom: 5px
            }
            

        </style>
    
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>

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
                            <div class="widget" data-toggle="">
                                <div class="widget-head">
                                    <h3 class="heading">Filter by</h3>
                                </div>
                                <div class="widget-body collapse in">
                                    <form name="frm" id="frm" action="" method="get">
                                        <table width="100%">
                                            <tbody>
                                            <tr>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">From</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php echo date('Y-m',strtotime($from_date)); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">To</label>
                                                            <div class="form-group">
                                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php echo date('Y-m',strtotime($to_date)); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Province</label>
                                                                <select required name="province[]" id="province"  class="multiselect-ui form-control input-sm" multiple>
                                                                         <?php

                                                                            $queryprov = "SELECT
                                                                                                tbl_locations.PkLocID AS prov_id,
                                                                                                tbl_locations.LocName AS prov_title
                                                                                            FROM
                                                                                                tbl_locations
                                                                                            WHERE
                                                                                                LocLvl = 2
                                                                                                AND parentid IS NOT NULL
                                                                                                AND tbl_locations.LocType = 2";
                                                                            //query result
                                                                            $rsprov = mysql_query($queryprov) or die();
                                                                            
                                                                            while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                                if (in_array($rowprov['prov_id'],$province_arr)) {
                                                                                    $sel = "selected='selected'";
                                                                                    $prov_name[]=$rowprov['prov_title'];
                                                                                } else {
                                                                                    $sel = "";
                                                                                }
                                                                                ?>
                                                                                    <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                                         <?php
                                                                                }

                                                                        ?>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Stakeholder</label>
                                                                <select  required name="stakeholder[]" id="stakeholder" class="multiselect-ui form-control input-sm" multiple>
                                                                    <?php

                                                                    $queryprov = "SELECT
                                                                            stakeholder.stkname,
                                                                            stakeholder.stkid
                                                                            FROM
                                                                            stakeholder
                                                                            WHERE
                                                                            stakeholder.stk_type_id = 0 AND
                                                                            stakeholder.lvl = 1 AND
                                                                            stakeholder.is_reporting = 1
                                                                        ";
                                                                    //query result
                                                                    $rsprov = mysql_query($queryprov) or die();

                                                                    while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                        if (in_array($rowprov['stkid'],$stk_arr)) {
                                                                            $sel = "selected='selected'";
                                                                            $stk_name[]=$rowprov['stkname'];
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        ?>
                                                                            <option value="<?php echo $rowprov['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowprov['stkname']; ?></option>
                                                                 <?php
                                                                        }

                                                                ?>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Product</label>
                                                                <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>
                                                                    <?php
                                                                    $queryprov = "SELECT
                                                                                itminfo_tab.itm_id,
                                                                                itminfo_tab.itm_name
                                                                                FROM
                                                                                itminfo_tab
                                                                                WHERE
                                                                                itminfo_tab.itm_category = 1 AND
                                                                                itminfo_tab.method_type IS NOT NULL
                                                                                ORDER BY
                                                                                itminfo_tab.method_rank ASC
                                                                        ";
                                                                    //query result
                                                                    $rsprov = mysql_query($queryprov) or die();

                                                                    while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                        if (in_array($rowprov['itm_id'],$itm_arr_request)) {
                                                                            $sel = "selected='selected'";
                                                                            $itm_name[]=$rowprov['itm_name'];
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        ?>
                                                                            <option value="<?php echo $rowprov['itm_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['itm_name']; ?></option>
                                                                 <?php
                                                                        }

                                                                ?>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-md-2">
                                                   
                                                    <label class="control-label">&nbsp;</label>
                                                    <input name="submit_btn" type="submit" class="btn btn-succes" value="Go">
                                                    
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                   </div>
               </div>
                 <?php
                 if(isset($_REQUEST['submit_btn'])){
                                    //get total number of facilities in province
                                    $qry_1 = "  
                                        SELECT
                                        tbl_warehouse.prov_id,
                                        stakeholder_item.stk_item as itm,
                                                COUNT(
                                                        DISTINCT tbl_warehouse.wh_id
                                                ) AS totalWH
                                        FROM
                                                tbl_warehouse
                                        INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                        INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
                                        WHERE
                                        stakeholder.lvl = 7
                                        AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                                        $where_clause
                                        GROUP BY
                                        tbl_warehouse.prov_id,
                                        stakeholder_item.stk_item
                                            ";
                                        //echo $qry_1;exit;
                                            $res_1 = mysql_query($qry_1);
                                            $total_sdps= array();
                                            while($row_1 = mysql_fetch_array($res_1))
                                            {
                                                $total_sdps[$row_1['prov_id']][$row_1['itm']]=$row_1['totalWH'];
                                                
                                                if(!isset($total_sdps['all'][$row_1['itm']])) $total_sdps['all'][$row_1['itm']]=0;
                                                $total_sdps['all'][$row_1['itm']]+=$row_1['totalWH'];
                                            }
                                          
                                     
                                     //counting the disabled facilities 
                                     $disabled_qry = "
                                                SELECT
                                                        
                                                    COUNT(DISTINCT warehouse_status_history.warehouse_id) as cnt,
                                                    tbl_warehouse.prov_id,
                                                    stakeholder_item.stk_item,
                                                    warehouse_status_history.reporting_month
                                                FROM
                                                        warehouse_status_history
                                                INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                                                INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
                                                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                WHERE
                                                        warehouse_status_history.reporting_month BETWEEN '".$from_date."' and '".$to_date."'
                                                AND warehouse_status_history.`status` = 0
                                                AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                                                AND stakeholder.lvl=7
                                                $where_clause
                                                GROUP BY
                                                        tbl_warehouse.prov_id,
                                                        stakeholder_item.stk_item,warehouse_status_history.reporting_month
                                        ";
                                     //echo $disabled_qry;exit;
                                    $res_d = mysql_query($disabled_qry);
                                    $disabled_count= array();
                                    while($row_d = mysql_fetch_array($res_d))
                                    {
                                        $disabled_count[$row_d['prov_id']][$row_d['stk_item']][$row_d['reporting_month']]=$row_d['cnt'];
                                        if(empty($disabled_count['all'][$row_d['stk_item']][$row_d['reporting_month']])) $disabled_count['all'][$row_d['stk_item']][$row_d['reporting_month']]=0;
                                        $disabled_count['all'][$row_d['stk_item']][$row_d['reporting_month']] +=$row_d['cnt'];
                                    }   
                                            
                                    //making list of items , to display list incase no data entry is found
                                    //echo '<pre>';print_r($total_sdps);print_r($disabled_count);exit;       
                                    $w_clause="";
                                    if(!empty($stk))             
                                        $w_clause .= " AND stakeholder_item.stkid in (".$stk.")  ";    
                                    
                                    if(!empty($itm))             
                                        $w_clause .= " AND itminfo_tab.itm_id in (".$itm.")  ";    
                                    
                                    $qry_1 = "  SELECT
                                                    itminfo_tab.itmrec_id,
                                                    itminfo_tab.itm_name,
                                                    itminfo_tab.itm_id
                                                FROM
                                                    itminfo_tab
                                                    INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
                                                WHERE
                                                    itminfo_tab.itm_id in (1,2,9,3,5,7,8,13)
                                                    $w_clause
                                                        
                                                ORDER BY
                                                    itminfo_tab.frmindex ASC
                                            ";
                                        //echo $qry_1;exit;
                                            $res_1 = mysql_query($qry_1);
                                            $itm_arr= array();
                                            while($row_1 = mysql_fetch_array($res_1))
                                            {
                                                $itm_arr[$row_1['itm_id']]=$row_1['itm_name'];
                                            }


                                            //query for getting reported facilities
                                            $q_reporting  = "SELECT
                                                                    tbl_warehouse.stkid,
                                                                    COUNT(
                                                                            DISTINCT tbl_warehouse.wh_id
                                                                    ) AS reportedWH,
                                                                    
                                                                    tbl_warehouse.prov_id,
                                                                    tbl_hf_data.item_id,
                                                                    tbl_locations.LocName,tbl_hf_data.reporting_date
                                                            FROM
                                                                    tbl_warehouse
                                                            INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                                            INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
                                                            WHERE
                                                                 stakeholder.lvl = 7
                                                                 AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                                                            AND tbl_hf_data.reporting_date BETWEEN '".$from_date."' and '".$to_date."'
                                                            $where_clause
                                                            GROUP BY
                                                                    tbl_warehouse.prov_id,
                                                                    tbl_hf_data.item_id,tbl_hf_data.reporting_date";
                                            //echo $q_reporting;exit;
                                            $res_reporting = mysql_query($q_reporting);
                                            $reporting_wh_arr  = $prov_arr = array();
                                            $total_reporting_wh = 0;
                                            $prov_arr['all']='Aggregated';
                                            while($row=mysql_fetch_assoc($res_reporting))
                                            {
                                                $prov_arr[$row['prov_id']] = $row['LocName'];
                                                
                                                if(empty($reporting_wh_arr['all'][$row['item_id']][$row['reporting_date']])) $reporting_wh_arr['all'][$row['item_id']][$row['reporting_date']]=0;
                                                if(empty($reporting_wh_arr[$row['prov_id']][$row['item_id']][$row['reporting_date']])) $reporting_wh_arr[$row['prov_id']][$row['item_id']][$row['reporting_date']]=0;
                                                if(empty($reporting_wh_arr2[$row['item_id']])) $reporting_wh_arr2[$row['item_id']]=0;
                                                
                                                $reporting_wh_arr[$row['prov_id']][$row['item_id']][$row['reporting_date']]+=$row['reportedWH'];
                                                $reporting_wh_arr['all'][$row['item_id']][$row['reporting_date']]+=$row['reportedWH'];
                                                $reporting_wh_arr2[$row['item_id']] +=$row['reportedWH'];
                                                $total_reporting_wh +=$row['reportedWH'];
                                            }

//echo '<pre>';print_r($reporting_wh_arr);print_r($reporting_wh_arr2);exit;    

                                            //Query for mos
                                            $qry = "SELECT
                                                    tbl_warehouse.prov_id,
                                                    tbl_warehouse.stkid,
                                                    tbl_hf_data.item_id ,
                                                    itminfo_tab.itm_name,
                                                    tbl_hf_data.pk_id,
                                                    tbl_hf_data.closing_balance,
                                                    tbl_hf_data.avg_consumption,
                                                    tbl_hf_data.reporting_date
                                                        FROM
                                                                tbl_warehouse
                                                        INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                                        INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                                        INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                                                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                                        WHERE
                                                                stakeholder.lvl = 7
                                                        $where_clause

                                                        AND tbl_hf_data.reporting_date BETWEEN '".$from_date."' and '".$to_date."'

                                                        AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                                                        
                                                        AND itminfo_tab.itm_category = 1
                                                        AND itminfo_tab.itm_id IN(".implode(',',$itm_arr_request).")
                                                    

                                            ";
                                        //echo $qry;exit;
                                        $qryRes = mysql_unbuffered_query($qry);
                                        $xc=1;
                                        $unk_arr= $so_arr2 = $so_arr = $us_arr = $sat_arr = $os_arr = array();
                                        $so_arr['all']='';

                                        while($row = mysql_fetch_assoc($qryRes))
                                        {
                                            //echo '<br/>'.$xc;
                                            $xc++;
                                            //$itm_arr[$row['item_id']] = $row['itm_name'];
                                            //$rep_year = date('Y',$row['reporting_date']);
                                            $rep_month= date('Y-m-01',strtotime($row['reporting_date']));

                                            if(empty($so_arr['all'][$row['item_id']][$rep_month])) $so_arr['all'][$row['item_id']][$rep_month]=0;
                                            if(empty($so_arr[$row['prov_id']][$row['item_id']][$rep_month])) $so_arr[$row['prov_id']][$row['item_id']][$rep_month]=0;
                                            if(empty($so_arr2[$row['item_id']][$rep_month])) $so_arr2[$row['item_id']][$rep_month]=0;
                                            if( $row['closing_balance'] <= '0' )
                                            {
                                                $so_arr[$row['prov_id']][$row['item_id']][$rep_month] += 1;
                                                $so_arr['all'][$row['item_id']][$rep_month] += 1;
                                                $so_arr2[$row['item_id']][$rep_month] += 1;
                                            }
                                                                                        
                                        }    
    //echo '<pre>'.$total_reporting_wh;print_r($itm_arr);echo 'SO Array:';print_r($so_arr);echo'wh_rep';print_r($reporting_wh_arr2);exit;    
                ?>
                
                <div class="row">
                    <div class="col-md-12">
                    <div class="note note-danger">
                        <div style="font-size: 12px;">
                            <u><b>Disclaimer</b></u>: <br/>
                            The SDPs having <b>MOS 0 to 0.5</b> are considered to be stock out in this report.<br/>
                            The following facility types are NOT included in the calculation of this report: MSU,Social Mobilizer,RHS-B,RMPS,Hakeems,Homopaths,PLDs,TBAs,Counters,DDPs.
                        </div>
                    </div> 
                    </div>
                </div> 
                <div class="portlet box green">
                    <div class="portlet-title">
                            <div class="caption">
                                    <i class="fa fa-medkit"></i>Commodity Security -  Stock-Out Reasons Data Entry
                            </div>
                            <div class="tools"><a href="javascript:;" class="collapse" data-original-title="" title=""></a></div>
                    </div>
                    
                    <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="page-title1   center"> Reporting Period : <?php echo date('M-Y',strtotime($from_date)); ?> To <?php echo date('M-Y',strtotime($to_date)); ?></h3>
                        <h4 class="page-title1 row  center"> 
                            <div class=" col-md-11">
                               <?php  echo implode(',',$prov_name); ?> - <?php  echo implode(',',$stk_name); ?> 
                            </div>
                            <div class=" col-md-1 right">
                                <a id="btnExport" onclick="javascript:xport.toCSV('AvgStockOut');"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png"  title="Export to Excel" /></a>
                            </div>
                        </h4>
                        
                   </div>
                    
                </div>
                 <div class="row">
                    <div class="col-md-12">
                        <div class="widget1 widget-tabs">
                            <div class="widget-body" style="overflow:auto">
                                <table id="AvgStockOut" name="tbl" class="table table-bordered table-condensed" border="">    
                                    <tr>
                                        <th rowspan=""></th>
                                        <th rowspan=""></th>
                                        <?php
                                        foreach($months_list as $k => $v){
                                            echo '<th colspan="5" class="months_td" style="text-align:center !important;">'.date('M Y',strtotime($v)).'</th>';
                                        }
                                        ?>
                                        <th colspan="4" style="text-align:center !important;">Totals</th>

                                    </tr>
                                    <tr>
                                        <th rowspan="">#</th>
                                        <th rowspan="" align="center">Product</th>
                                        <?php
                                        foreach($months_list as $k => $v){
                                            echo '<td align="center" class="so_n">SO</td>';
                                            echo '<td align="center" class="so_r">SO Rate</td>';
                                            echo '<td align="center" class="reported_n">Reported</td>';
                                            echo '<td align="center" class="total_n">Total</td>';
                                            echo '<td align="center" class="reporting_r">RR</td>';
                                        }
                                        ?>
                                        <td align="center">Total SO</td>
                                        <td align="center">SO Rate</td>
                                        <td align="center">Total reported</td>
                                        <td align="center">Total SDPs</td>

                                    </tr>
                            <?php
                            $rep_rate = $so_rate = array();
                                foreach($so_arr as $prov_id => $prov_data)
                                {
                                    $month_total_so = $month_total_sdp = $month_total_reported = array();
                                    
                                    if($prov_id == 'all'){ 
                                        $pro_class = " aggregated_row ";
                                        $pro_style = " ";
                                        $pro_color = "#9C78D6";
                                        $st = ' style="color:#FFFFFF" ' ;
                                        $onclick=false;
                                    }
                                    else{
                                        $pro_class = " prov_row ";
                                        $pro_style = " display:none ";
                                        $pro_color = "#9cd39c";
                                        $st = "" ;
                                        $onclick=true;
                                    }
                                    
                                    echo '<tr class="'.$pro_class.'" style="'.$pro_style.'">';
                                    echo '<td colspan="99" bgcolor="'.$pro_color.'" '.$st.'>'.$prov_arr[$prov_id].'</td>';
                                    echo '</tr>';
                                    
                                    $c=1;
                                    foreach($itm_arr as $itm_id => $itm_name)
                                    {
                                        $prod_total_so = $prod_total_sdp = $prod_total_reported = 0;
                                        
                                        echo '<tr  class="'.$pro_class.'" style="'.$pro_style.'">';
                                        echo '<td>'.$c++.'</td>';
                                        echo '<td>'.$itm_name.'</td>';
                                        
                                        foreach($months_list as $k => $v){
                                            $master_total = $total_sdps[$prov_id][$itm_id];
                                            $disabled_fac = (isset($disabled_count[$prov_id][$itm_id][$v])?$disabled_count[$prov_id][$itm_id][$v]:0);
                                            $to_be_reported = $master_total - $disabled_fac;
                                            
                                            $val = (isset($prov_data[$itm_id][$v])?$prov_data[$itm_id][$v]:0);
                                            
                                            if($reporting_wh_arr[$prov_id][$itm_id][$v]>0 && isset($val))
                                                $so_r=  ($val*100)/$reporting_wh_arr[$prov_id][$itm_id][$v];
                                            else
                                                $so_r = 0;
                                            
                                            if($to_be_reported>0 && isset($reporting_wh_arr[$prov_id][$itm_id][$v]))
                                                $r_r = ($reporting_wh_arr[$prov_id][$itm_id][$v]*100)/$to_be_reported;
                                            else
                                                $r_r=0;
                                            $so_rate[$prov_id][$itm_id][$v] = $so_r;
                                            $rep_rate[$prov_id][$itm_id][$v] = $r_r;
                                            
                                            $onclick_func = '';
                                            if($onclick) $onclick_func='  onclick="showDrillDown(\''.$prov_id.'\',\''.$prov_arr[$prov_id].'\',\''.$from_date.'\','.$itm_id.',\''.$itm_name.'\',\'SO\',\''.$stk.'\',\''.$stk_name.'\')" ';

                                            
                                            echo '<td align="right" class="danger so_n" '.$onclick_func.'>'.number_format($val).'</td>';
                                            echo '<td align="right" class="so_r">'.number_format($so_r,2).'</td>';
                                            echo '<td align="right" class="reported_n">'.number_format($reporting_wh_arr[$prov_id][$itm_id][$v]).'</td>';
                                            echo '<td align="right" class="total_n" title="All:'.$master_total.',Disabled:'.$disabled_fac.'">'.number_format($to_be_reported).'</td>';
                                            echo '<td align="right" class="reporting_r">'.number_format($r_r,2).'</td>';
                                            
                                            $prod_total_so          += $val;
                                            $prod_total_sdp         += $to_be_reported;
                                            $prod_total_reported    += (!empty($reporting_wh_arr[$prov_id][$itm_id][$v])?$reporting_wh_arr[$prov_id][$itm_id][$v]:0);
                                            
                                            @$month_total_so[$v]         += $val;
                                            @$month_total_sdp[$v]        += $to_be_reported;
                                            @$month_total_reported[$v]   += $reporting_wh_arr[$prov_id][$itm_id][$v];
                                        }
                                        echo '<td align="right" class="info">'.number_format($prod_total_so).'</td>';
                                        echo '<td align="right" class="info">'.number_format($prod_total_so*100/$prod_total_reported,2).'</td>';
                                        echo '<td align="right" class="info">'.number_format($prod_total_reported).'</td>';
                                        echo '<td align="right" class="info">'.number_format($prod_total_sdp).'</td>';
                                        
                                        echo '</tr>';
                                    }
                                    
                                        echo '<tr class="'.$pro_class.' warning" style="'.$pro_style.'">';
                                        echo '<td></td>';
                                        echo '<td><b>TOTAL</b></td>';
                                        
                                        foreach($months_list as $k => $v){
                                            
                                            echo '<td align="right" class="danger so_n">'.number_format($month_total_so[$v]).'</td>';
                                            echo '<td align="right" class="so_r">'.number_format(($month_total_so[$v]*100)/$month_total_reported[$v],2).'</td>';
                                            echo '<td align="right" class="reported_n">'.number_format($month_total_reported[$v]).'</td>';
                                            echo '<td align="right" class="total_n">'.number_format($month_total_sdp[$v]).'</td>';
                                            echo '<td align="right" class="reporting_r">'.number_format(($month_total_reported[$v]*100)/$month_total_sdp[$v],2).'</td>';
                                            
                                        }
                                        echo '<td align="right" class="">'.number_format(array_sum($month_total_so)).'</td>';
                                        echo '<td align="right" class="">'.number_format(array_sum($month_total_so)*100/array_sum($month_total_reported),2).'</td>';
                                        
                                        echo '<td align="right" class="">'.number_format(array_sum($month_total_reported)).'</td>';
                                        echo '<td align="right" class="">'.number_format(array_sum($month_total_sdp)).'</td>';
                                        
                                        if($prov_id == 'all'){ 
                                            echo '<tr class="" id="show_breakdown">';
                                            echo '<td colspan="99"  class="alert1 alert-error center" >';
                                            echo '<img width="25px" src="../../public/images/arrowdown.gif">';
                                            echo 'Show Province Wise Breakdown</td>';
                                            echo '</tr>';
                                        }
                                }
                                ?>
                                </table>
                          </div>
                      </div>
                   </div>
                 </div>
                    </div>
                </div>
                
                <div class="row hide">
                    <div class="col-md-12">
                    <div class=" ">
                        <a class="col_btn btn btn-sm blue" hide_class="so_r">Hide Stock Out Rate</a>
                        <a class="col_btn btn btn-sm blue" hide_class="reported_n">Hide Reported Column</a>
                        <a class="col_btn btn btn-sm blue" hide_class="total_n">Hide Total SDPs Column</a>
                        <a class="col_btn btn btn-sm blue" hide_class="reporting_r">Hide Reporting Rate</a>
                    </div> 
                    </div>
                </div> 
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget widget-tabs">
                            <div class="widget-body" id="drilldown_div">
                               
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget widget-tabs">
                            <div class="widget-body" id="drilldown_div_sdp">
                               
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

    <?php 
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; ?>
    <script src="<?=PUBLIC_URL?>js/bootstrap_multiselect.js"></script>


    <script>
        $(function() {
           
           
            $('.col_btn').click(function(){
                var hide_cls = $(this).attr('hide_class');
                console.log(hide_cls);
                $("."+hide_cls).hide();
                var colspan = $(".months_td").attr('colspan');
                colspan = colspan - 1;
                $(".months_td").attr('colspan',colspan);
                $(this).hide();
            });
            
            $('#show_breakdown').click(function(){
               $( ".prov_row" ).first().toggle( "fast", function showNext() {
                    $( this ).next( ".prov_row" ).toggle( "fast", showNext );
                  });
                $(this).hide();  
            });
            $('#from_date, #to_date').datepicker({
                dateFormat: "yy-mm",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                maxDate: '' 
            });
            
            $('.multiselect-ui').multiselect({
                    includeSelectAllOption: true
                });

            
            
        })
        
       
 
         
     function showDrillDown(prov,prov_name,from_date,prod_id,prod_name,indicator,stk,stk_name) {
       
        var url = 'commodity_security_de_dist_level.php';
        var div_id = "drilldown_div";
        var dataStr='';
        dataStr += "province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&prod_id="+prod_id+"&prod_name="+prod_name+"&indicator="+indicator+"&stk="+stk+"&stk_name="+stk_name;

        $('#'+div_id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

        $.ajax({
            type: "POST",
            url: '<?php echo APP_URL; ?>reports/' + url,
            data: dataStr,
            dataType: 'html',
            success: function(data) {
                    $("#"+div_id).html(data);
            }
        });
        
        $('html, body').animate({ scrollTop: $('#'+div_id).offset().top }, 'slow');
    
    }
    
    
    
         
     function showDrillDown_sdp_level(dist,dist_name,from_date,prod_id,prod_name,indicator,stk,stk_name) {
       
        var url = 'commodity_security_de_sdp_level.php';
        var div_id = "drilldown_div_sdp";
        var dataStr='';
        dataStr += "dist_id="+dist+"&dist_name="+dist_name+"&from_date="+from_date+"&prod_id="+prod_id+"&prod_name="+prod_name+"&indicator="+indicator+"&stk="+stk+"&stk_name="+stk_name;

        $('#'+div_id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

        $.ajax({
            type: "POST",
            url: '<?php echo APP_URL; ?>reports/' + url,
            data: dataStr,
            dataType: 'html',
            success: function(data) {
                    $("#"+div_id).html(data);
            }
        });
        
        $('html, body').animate({ scrollTop: $('#'+div_id).offset().top }, 'slow');
    
    }
    </script>
    <script>
     var xport = {
  _fallbacktoCSV: true,  
  toXLS: function(tableId, filename) {   
    this._filename = (typeof filename == 'undefined') ? tableId : filename;
    
    //var ieVersion = this._getMsieVersion();
    //Fallback to CSV for IE & Edge
    if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
      return this.toCSV(tableId);
    } else if (this._getMsieVersion() || this._isFirefox()) {
      alert("Not supported browser");
    }

    //Other Browser can download xls
    var htmltable = document.getElementById(tableId);
    var html = htmltable.outerHTML;

    this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls'); 
  },
  toCSV: function(tableId, filename) {
    this._filename = (typeof filename === 'undefined') ? tableId : filename;
    // Generate our CSV string from out HTML Table
    var csv = this._tableToCSV(document.getElementById(tableId));
    // Create a CSV Blob
    var blob = new Blob([csv], { type: "text/csv" });

    // Determine which approach to take for the download
    if (navigator.msSaveOrOpenBlob) {
      // Works for Internet Explorer and Microsoft Edge
      navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
    } else {      
      this._downloadAnchor(URL.createObjectURL(blob), 'csv');      
    }
  },
  _getMsieVersion: function() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
    }

    var trident = ua.indexOf("Trident/");
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf("rv:");
      return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
    }

    var edge = ua.indexOf("Edge/");
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
    }

    // other browser
    return false;
  },
  _isFirefox: function(){
    if (navigator.userAgent.indexOf("Firefox") > 0) {
      return 1;
    }
    
    return 0;
  },
  _downloadAnchor: function(content, ext) {
      var anchor = document.createElement("a");
      anchor.style = "display:none !important";
      anchor.id = "downloadanchor";
      document.body.appendChild(anchor);

      // If the [download] attribute is supported, try to use it
      
      if ("download" in anchor) {
        anchor.download = this._filename + "." + ext;
      }
      anchor.href = content;
      anchor.click();
      anchor.remove();
  },
  _tableToCSV: function(table) {
    // We'll be co-opting `slice` to create arrays
    var slice = Array.prototype.slice;

    return slice
      .call(table.rows)
      .map(function(row) {
        return slice
          .call(row.cells)
          .map(function(cell) {
            return '"t"'.replace("t", cell.textContent);
          })
          .join(",");
      })
      .join("\r\n");
  }
};

    </script>
</body>
<!-- END BODY -->
</html>