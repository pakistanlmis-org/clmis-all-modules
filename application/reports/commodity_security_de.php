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
//$t_date= (!empty($_REQUEST['to_date'])?$_REQUEST['to_date']:$f_date);
$t_date= $f_date;
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
            td{
                line-height: 5px !important;
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
                                                            <label class="control-label">Month</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php echo date('Y-m',strtotime($from_date)); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td class="col-md-2 hide">
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
                                        tbl_warehouse.dist_id,
                                        tbl_warehouse.stkid,
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
                                        tbl_warehouse.dist_id,
                                        tbl_warehouse.stkid,
                                        stakeholder_item.stk_item
                                            ";
                                        //echo $qry_1;exit;
                                            $res_1 = mysql_query($qry_1);
                                            $total_sdps= array();
                                            while($row_1 = mysql_fetch_array($res_1))
                                            {
                                                $total_sdps[$row_1['dist_id']][$row_1['stkid']][$row_1['itm']]=$row_1['totalWH'];
                                                
                                            }
                                          
                                     
                                     //counting the disabled facilities 
                                     $disabled_qry = "
                                                SELECT
                                                        
                                                    COUNT(DISTINCT warehouse_status_history.warehouse_id) as cnt,
                                                    tbl_warehouse.prov_id,
                                        tbl_warehouse.dist_id,
                                        tbl_warehouse.stkid,
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
                                        tbl_warehouse.dist_id,
                                        tbl_warehouse.stkid,
                                                        stakeholder_item.stk_item,warehouse_status_history.reporting_month
                                        ";
                                     //echo $disabled_qry;exit;
                                    $res_d = mysql_query($disabled_qry);
                                    $disabled_count= array();
                                    while($row_d = mysql_fetch_array($res_d))
                                    {
                                        $disabled_count[$row_d['dist_id']][$row_d['stkid']][$row_d['stk_item']]=$row_d['cnt'];
                                        //if(empty($disabled_count['all'][$row_d['stk_item']][$row_d['reporting_month']])) $disabled_count['all'][$row_d['stk_item']][$row_d['reporting_month']]=0;
                                        //$disabled_count['all'][$row_d['stk_item']][$row_d['reporting_month']] +=$row_d['cnt'];
                                    }   
                                            
                                    

                                            //query for getting reported facilities
                                            $q_reporting  = "SELECT
                                                                    tbl_warehouse.stkid,
                                                                    COUNT(
                                                                            DISTINCT tbl_warehouse.wh_id
                                                                    ) AS reportedWH,
                                                                    
                                                                    tbl_warehouse.prov_id,
                                        tbl_warehouse.dist_id,
                                        tbl_warehouse.stkid,
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
                                        tbl_warehouse.dist_id,
                                        tbl_warehouse.stkid,
                                                                    tbl_hf_data.item_id";
                                            //echo $q_reporting;exit;
                                            $res_reporting = mysql_query($q_reporting);
                                            $reporting_wh_arr  = $prov_arr = array();
                                            $total_reporting_wh = 0;
                                            $prov_arr['all']='Aggregated';
                                            while($row=mysql_fetch_assoc($res_reporting))
                                            {
                                                //$prov_arr[$row['prov_id']] = $row['LocName'];
                                                
//                                                if(empty($reporting_wh_arr['all'][$row['item_id']][$row['reporting_date']])) $reporting_wh_arr['all'][$row['item_id']][$row['reporting_date']]=0;
                                                if(empty($reporting_wh_arr[$row['dist_id']][$row['stkid']][$row['item_id']])) $reporting_wh_arr[$row['dist_id']][$row['stkid']][$row['item_id']]=0;
//                                                if(empty($reporting_wh_arr2[$row['item_id']])) $reporting_wh_arr2[$row['item_id']]=0;
                                                
                                                $reporting_wh_arr[$row['dist_id']][$row['stkid']][$row['item_id']]+=$row['reportedWH'];
//                                                $reporting_wh_arr['all'][$row['item_id']][$row['reporting_date']]+=$row['reportedWH'];
//                                                $reporting_wh_arr2[$row['item_id']] +=$row['reportedWH'];
//                                                $total_reporting_wh +=$row['reportedWH'];
                                            }
                     
                     
                     
                        $qry ="SELECT
                                tbl_locations.PkLocID as dist_id,
                                tbl_locations.LocName as district_name,
                                tbl_locations.ParentID,
                                province.LocName AS prov_name
                            FROM
                                tbl_locations
                            INNER JOIN tbl_locations AS province ON tbl_locations.ParentID = province.PkLocID
                                WHERE
                                tbl_locations.ParentID in ($province)
                                ORDER BY ParentID,tbl_locations.LocName
                                ";
                        //echo $qry;exit;
                        $qryRes = mysql_query($qry);
                        $c=1;
                        $dist_arr   = $dist_prov = $prov_dist  = $provinces_names =    array();

                        while($row = mysql_fetch_assoc($qryRes))
                        {
                            $dist_arr[$row['dist_id']]          = $row['district_name'];
                            $dist_prov[$row['dist_id']]         = $row['ParentID'];
                            $prov_dist[$row['ParentID']][$row['dist_id']]        = $row['district_name'];
                            $provinces_names[$row['ParentID']]     = $row['prov_name'];
                        }    
                        //echo '<pre>';print_r($prov_dist);print_r($stk_arr);exit;
                        //Query for shipment main dashboard
                        $qry = "SELECT
                                        tbl_warehouse.dist_id,
                                        tbl_locations.LocName AS district_name,
                                        itminfo_tab.itm_name,
                                        tbl_hf_data.item_id,
                                        tbl_warehouse.stkid,
                                        st2.stkname,
                                        count(*) as stock_out_count

                                    FROM
                                            tbl_warehouse
                                    INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid

                                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                    INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                    INNER JOIN stakeholder st2 ON tbl_warehouse.stkid = st2.stkid
                                    WHERE
                                            stakeholder.lvl = 7

                                    AND tbl_warehouse.wh_id NOT IN (
                                            SELECT
                                                    warehouse_status_history.warehouse_id
                                            FROM
                                                    warehouse_status_history
                                            INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                                            WHERE
                                                    warehouse_status_history.reporting_month = '".$from_date."'
                                            AND warehouse_status_history.`status` = 0

                                    )
                                    AND tbl_hf_data.reporting_date = '".$from_date."'
                                    AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)

                                    AND itminfo_tab.itm_category = 1 
                                    AND itminfo_tab.itm_id in ($itm)
                                    $where_clause 
                                        
                                    and tbl_hf_data.avg_consumption > 0
                                    AND (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption) <= 0.5
                                GROUP BY 
                                        tbl_warehouse.dist_id,
                                        stakeholder.stkname,
                                        itminfo_tab.itm_id
                                ORDER BY
                                        tbl_warehouse.stkid,
                                        tbl_locations.LocName,
                                        tbl_hf_data.item_id,
                                        tbl_warehouse.wh_name

                        ";
//                        echo $qry;exit;
                        $qryRes = mysql_query($qry);
                        $c=1;
                        $disp_arr  =$itm_arr =   array();

                        while($row = mysql_fetch_assoc($qryRes))
                        {
                            //$stk_arr[$row['stkid']]         = $row['stkname'];
                            $itm_arr[$row['item_id']]       = $row['itm_name'];
                            //$dist_arr[$row['dist_id']]      = $row['district_name'];
                            $disp_arr[$row['dist_id']][$row['stkid']][$row['item_id']] = $row['stock_out_count'];
                        }    
                        //Query for shipment main dashboard
                        $qry = "SELECT
                                    itminfo_tab.itm_id,
                                    itminfo_tab.itm_name
                                    FROM
                                    itminfo_tab
                                    WHERE
                                    itminfo_tab.itm_category = 1 AND
                                    itminfo_tab.method_type IS NOT NULL
                        ";
                        $qryRes = mysql_query($qry);
                        $itm_arr2 =   array();
                        while($row = mysql_fetch_assoc($qryRes))
                        {
                            $itm_arr2[$row['itm_id']]       = $row['itm_name'];
                        }
                        if(empty($itm_arr)) $itm_arr = $itm_arr2;
                        
                        $qry = "SELECT
                                    stakeholder.stkid,
                                    stakeholder.stkname
                                    FROM
                                    stakeholder
                                    WHERE
                                    stakeholder.stkid IN ($stk)
                        ";
                        $qryRes = mysql_query($qry);
                        $stk_arr =   array();
                        while($row = mysql_fetch_assoc($qryRes))
                        {
                            $stk_arr[$row['stkid']]       = $row['stkname'];
                        }
                        
                        //echo '<pre>';print_r($stk_arr);exit;
                ?>
                
                <div class="row">
                    <div class="col-md-12">
                    <div class="note note-danger">
                        <div style="font-size: 12px;">
                            <u><b>Disclaimer</b></u>: <br/>
                            The SDPs having <b>MOS 0 to 0.5</b> are considered to be stock out in this report. UnKnown MOS is not considered as Stock Out.<br/>
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
                        <h4 class="page-title1   center"><div class=" col-md-12"> Reporting Period : <?php echo date('M-Y',strtotime($from_date)); ?> To <?php echo date('M-Y',strtotime($to_date)); ?></div></h3>
                        <h4 class="page-title1 row  center"> 
                            <div class=" col-md-11">
                               <?php  echo implode(',',$prov_name); ?> - <?php  echo implode(',',$stk_name); ?> 
                            </div>
                            <div class=" col-md-1 right">
                                <a id="btnExport" onclick="javascript:xport.toCSV('CommoditySecurity');"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png"  title="Export to Excel" /></a>
                            </div>
                        </h4>
                        
                   </div>
                    
                </div>
                 <div class="row">
                    <div class="col-md-12">
                        <div class="widget1 widget-tabs">
                            <div class="widget-body" style="overflow:auto"> 
                                
                                <table id="CommoditySecurity" name="CommoditySecurity" class="table table-bordered table-condensed" border="">    
                                    <thead>
                                    <tr class="success">
                                        <th>#</th>
                                        <th  width="12%">District</th>
                                        <th >Stakeholder</th>
                                        <?php
                                        foreach($itm_arr as $itm_id => $itm_name)
                                        { 
                                            echo '<th colspan ="5" style="text-align:center">'.$itm_name.'</th>';
                                            //echo '<th>'.$itm_name.' %</th>';
                                        }
                                        ?>
                                    </tr>
                                    <tr  class="success">
                                        <th colspan="3"> </th>
                                        <?php
                                        foreach($itm_arr as $itm_id => $itm_name)
                                        { 
                                            echo '<th colspan ="" style="align:center">Total</th>';
                                            echo '<th colspan ="" style="align:center">Reported</th>';
                                            echo '<th colspan ="" style="align:center">Rep Rate</th>';
                                            echo '<th colspan ="" style="align:center">SO</th>';
                                            echo '<th colspan ="" style="align:center">SO Rate</th>';
                                            //echo '<th>'.$itm_name.' %</th>';
                                        }
                                        ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                <?php                                
                                $c=1;
                                $onclick_func='';
                                $old_dist='';
                                $dist_row = count($stk_arr);
                                $totals_arr = $prov_totals_arr =  array();
                                $t_rows= '';
                                
                                foreach($prov_dist as $prov_id => $districts_list)
                                {
                                        //echo 'A';
                                    $t_rows .= '<tr class="prov_row" style="display:none">';
                                    $t_rows .= '<td colspan="99" bgcolor="#9cd39c">'.$provinces_names[$prov_id].'</td>';
                                    $t_rows .= '</tr>';
                                foreach($districts_list as $dist_id => $dist_name)
                                { 
                                        //echo 'B';print_r($stk_arr);
                                    foreach($stk_arr as $stk_id => $stk_name)
                                    { 
                                        //echo 'C';
                                        /// Total facilities and reported facilities are to be moved into item wise loop
                                        $t_rows .= '<tr  id="row_'.$c.'"  class="prov_row" style="display:none">';
                                        $t_rows .= '<td>'.$c.'</td>';
                                        if($dist_id!= $old_dist )
                                            $t_rows .= '<td rowspan="'.$dist_row.'">'.$dist_name.'</td>';
                                        
                                        $old_dist=$dist_id;
                                        $t_rows .= '<td>'.$stk_name.'</td>';
                                        
                                        
                                        foreach($itm_arr as $itm_id => $itm_name)
                                        { 

                                            @$master_total = $total_sdps[$dist_id][$stk_id][$itm_id];
                                            $disabled_fac = (isset($disabled_count[$dist_id][$stk_id][$itm_id])?$disabled_count[$dist_id][$stk_id][$itm_id]:0);
                                            $to_be_reported = $master_total - $disabled_fac;

                                            if(!empty($reporting_wh_arr[$dist_id][$stk_id][$itm_id]) && $reporting_wh_arr[$dist_id][$stk_id][$itm_id]>0)
                                                $reported = ($reporting_wh_arr[$dist_id][$stk_id][$itm_id]);
                                            else
                                                $reported = 0;
                                            
                                            $t_rows .= '<td align="right">'.$to_be_reported.'</td>';
                                            $t_rows .= '<td align="right">'.$reported.'</td>';
                                            
                                            $r_rate = 0;
                                            if(!empty($to_be_reported) && $to_be_reported > 0) $r_rate =$reported*100/$to_be_reported;
                                            $t_rows .= '<td align="right">'.number_format($r_rate,1).' %</td>';
                                     
                                            $onclick_func='  onclick="showDrillDown_sdp_level(\''.$dist_id.'\',\''.$dist_arr[$dist_id].'\',\''.$from_date.'\','.$itm_id.',\''.$itm_arr[$itm_id].'\',\'SO\',\''.$stk_id.'\',\''.$stk_name.'\')" ';
                                            
                                            $val = (!empty($disp_arr[$dist_id][$stk_id][$itm_id])?$disp_arr[$dist_id][$stk_id][$itm_id]:'0');
                                            
                                            if($val>0){
                                                $clr='red';
                                                $size='14px';
                                            }
                                            else {
                                                $clr='green';
                                                $size='10px';
                                            }
                                            
                                            $t_rows .= '<td style="color:'.$clr.';font-size:'.$size.'; "'.$onclick_func.'" align="right">'.$val.'</td>';
                                            $so_r = 0;
                                            if(!empty($reported) && $reported > 0) $so_r = $val*100/$reported;
                                            $t_rows .= '<td style="color:'.$clr.';font-size:'.$size.'; "'.$onclick_func.'" title="Reported :'.$reported.'" align="right">'.number_format($so_r,1).' %</td>';
                                            
                                            @$totals_arr[$itm_id]['total']+=$to_be_reported;
                                            @$totals_arr[$itm_id]['reported']+=$reported;
                                            @$totals_arr[$itm_id]['so']+=$val;
                                            @$totals_arr[$itm_id]['so_rate']+=$so_r;
                                            @$totals_arr[$itm_id]['multiplied']+=($val * $reported);
                                            
                                            
                                            @$prov_totals_arr[$dist_prov[$dist_id]][$stk_id][$itm_id]['total']     +=  $to_be_reported;
                                            @$prov_totals_arr[$dist_prov[$dist_id]][$stk_id][$itm_id]['reported']  +=  $reported;
                                            @$prov_totals_arr[$dist_prov[$dist_id]][$stk_id][$itm_id]['so']        +=  $val;
                                            @$prov_totals_arr[$dist_prov[$dist_id]][$stk_id][$itm_id]['multiplied']+=($val * $reported);
                                        }
                                        $t_rows .= '</tr>';
                                        $c++;
                                    }
                                }
                                }
                                //echo '<pre>';print_r(@$prov_totals_arr);exit;
                                $t_rows .= '<tr>';
                                $t_rows .= '<td colspan="3">Totals:</td>';
                                foreach($itm_arr as $itm_id => $itm_name)
                                {
                                    $t_rows .= '<td colspan="" align="right">'.number_format($totals_arr[$itm_id]['total']).'</td>';
                                    
                                    $t_rows .= '<td colspan="" align="right">'.number_format($totals_arr[$itm_id]['reported']).'</td>';
                                    
                                    $t_rows .= '<td colspan="" align="right">'.number_format(($totals_arr[$itm_id]['reported']*100/$totals_arr[$itm_id]['total']),2).' %</td>';
                                    
                                    $t_rows .= '<td colspan="" align="right">'.number_format($totals_arr[$itm_id]['so']).'</td>';
                                    
                                    $weighted = $totals_arr[$itm_id]['multiplied'] / $totals_arr[$itm_id]['reported'];
                                    $t_rows .= '<td colspan="" align="right">'.number_format($weighted,2).' %</td>';
                                }
                                $t_rows .= '</tr>';
                                
                                
                                $prov_t_rows = '';
                                
                                    $prov_t_rows .= '<tr  id="">';
                                    $prov_t_rows .= '<td colspan="99" bgcolor="#9C78D6" style="color:#FFFFFF">Aggregated</td>';
                                    $prov_t_rows .= '</tr>';
                                foreach($prov_totals_arr as $prov_id => $pro_data){
                                    foreach($pro_data as $stk_id => $stk_data){
                                        $prov_t_rows .= '<tr>';
                                        $prov_t_rows .= '<td> </td>';
                                        $prov_t_rows .= '<td>'.$provinces_names[$prov_id].'</td>';
                                        $prov_t_rows .= '<td>'.$stk_arr[$stk_id].'</td>';
                                        
                                        foreach($itm_arr as $itm_id => $itm_name)
                                        {
                                            $prov_r_rate =$prov_so_rate= 0;
                                            if($prov_totals_arr[$prov_id][$stk_id][$itm_id]['total']>0)
                                                $prov_r_rate = 100*$prov_totals_arr[$prov_id][$stk_id][$itm_id]['reported']/$prov_totals_arr[$prov_id][$stk_id][$itm_id]['total'];
                                            
                                            if($prov_totals_arr[$prov_id][$stk_id][$itm_id]['reported']>0)
                                                $prov_so_rate= 100*$prov_totals_arr[$prov_id][$stk_id][$itm_id]['so']/$prov_totals_arr[$prov_id][$stk_id][$itm_id]['reported'];
                                            
                                            $prov_t_rows .= '<td align="right">'.$prov_totals_arr[$prov_id][$stk_id][$itm_id]['total'].'</td>';
                                            $prov_t_rows .= '<td align="right">'.$prov_totals_arr[$prov_id][$stk_id][$itm_id]['reported'].'</td>';
                                            $prov_t_rows .= '<td align="right">'.number_format($prov_r_rate,2).' %</td>';
                                            $prov_t_rows .= '<td align="right">'.$prov_totals_arr[$prov_id][$stk_id][$itm_id]['so'].'</td>';
                                            
                                            $weighted = $prov_totals_arr[$prov_id][$stk_id][$itm_id]['multiplied'] / $prov_totals_arr[$prov_id][$stk_id][$itm_id]['reported'];
                                            $prov_t_rows .= '<td align="right">'.number_format($weighted,2).' %</td>';
                                        }
                                        $prov_t_rows .= '</tr>';
                                    }
                                }
                                
                                $t_btn='';
                                $t_btn .= '<tr class="" id="show_breakdown">';
                                $t_btn .= '<td colspan="99"  class="alert1 alert-error center" >';
                                $t_btn .= '<img width="25px" src="../../public/images/arrowdown.gif">';
                                $t_btn .= 'Show District Wise Breakdown</td>';
                                $t_btn .= '</tr>';
                                
                                echo $prov_t_rows;
                                echo $t_btn;
                                echo $t_rows;
                                ?>

                                    </tbody>
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
               $( ".prov_row" ).first().toggle( 14, function showNext() {
                    $( this ).next( ".prov_row" ).toggle( 14, showNext );
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
        //console.log('DD'+dist);
        var url = 'commodity_security_de_sdp_level.php';
        var div_id = "drilldown_div_sdp";
        var dataStr='';
        dataStr += "dist_id="+dist+"&dist_name="+dist_name+"&from_date="+from_date+"&prod_id="+prod_id+"&prod_name="+prod_name+"&indicator="+indicator+"&stk="+stk+"&stk_name="+stk_name;

        //$('#'+div_id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");
        window.open(url+'?'+dataStr ,"", "width=1200,height=600");
//        $.ajax({
//            type: "POST",
//            url: '<?php echo APP_URL; ?>reports/' + url,
//            data: dataStr,
//            dataType: 'html',
//            success: function(data) {
//                    //$("#"+div_id).html(data);
//                    window.open(url+'?'+dataStr);
//            }
//        });
        
        //$('html, body').animate({ scrollTop: $('#'+div_id).offset().top }, 'slow');
    
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