<?php
ini_set('max_execution_time', 0);
/**
 * shipment
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
$province = (!empty($_REQUEST['province'])?$_REQUEST['province']:'all');
$stk = (!empty($_REQUEST['stakeholder'])?implode(',',$_REQUEST['stakeholder']):'');
$product = (!empty($_REQUEST['product'])?implode(',',$_REQUEST['product']):'');
$stk_arr = (!empty($_REQUEST['stakeholder']) ? $_REQUEST['stakeholder'] : '');
$itm_arr_request = (!empty($_REQUEST['product']) ? $_REQUEST['product'] : '');
$from_date = date("Y-m-d", strtotime($f_date));

$where_clause ="";

if(!empty($province) && $province!='all')   $where_clause .= " AND summary_district.province_id = ".$province."  ";
if(!empty($stk) && $stk!='all')             $where_clause .= " AND summary_district.stakeholder_id in (".$stk.")  ";

?>

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
                                                            <label class="control-label">Last Month</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php echo $from_date; ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Province/Region</label>
                                                                <select name="province" id="province" required="required" class="form-control input-sm">
                                                                       <option <?=(($province=='all')?' selected ':'')?> value="all">All</option>
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
                                                                                if ($province == $rowprov['prov_id']) {
                                                                                    $sel = "selected='selected'";
                                                                                    $prov_name=$rowprov['prov_title'];
                                                                                } else {
                                                                                    $sel = "";
                                                                                }
                                                                                $prov_name=(!empty($prov_name)?$prov_name:'All');
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
                                                                $querystake = "SELECT
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
                                                                $rsstake = mysql_query($querystake) or die();
                                                                $stk_name = array();
                                                                while ($rowprov = mysql_fetch_array($rsstake)) {
                                                                    if (!isset($_REQUEST['stakeholder'])) {
                                                                        if ($rowprov['stkid'] == 1 || $rowprov['stkid'] == 2 || $rowprov['stkid'] == 7 || $rowprov['stkid'] == 73) {
                                                                            $stk_arr[] = $rowprov['stkid'];
                                                                            $sel = "selected='selected'";
                                                                            $stk_name[$rowprov['stkid']] = $rowprov['stkname'];
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                    }
                                                                    if (in_array($rowprov['stkid'], $stk_arr)) {
                                                                        $sel = "selected='selected'";
                                                                        $stk_name[$rowprov['stkid']] = $rowprov['stkname'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowprov['stkname']; ?></option>
                                                                    <?php
                                                                }
//                                                                print_r($stk_arr);exit;
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
                                                    <input type="submit" class="btn btn-succes" value="Go">
                                                    
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                
                 <?php
                 $where_total= '';
                 
                 
if(!empty($province) && $province!='all')   $where_total .= " AND tbl_warehouse.prov_id = ".$province."  ";
if(!empty($stk) && $stk!='all')             $where_total .= " AND mainStk.stkid in (".$stk.")  ";  

                     
                                    //get total number of facilities in province
                                    $qry_1 = "  
                                            select prov_id,stk_item,sum(total_districts) as total_districts
                                            FROM
                                            (
                                                SELECT
                                                    tbl_warehouse.prov_id,
                                                    tbl_warehouse.stkid,
                                                    mainStk.stkname,
                                                    stakeholder_item.stk_item,
                                                            count(
                                                                    DISTINCT tbl_warehouse.dist_id
                                                            ) AS total_districts
                                                FROM
                                                        tbl_warehouse
                                                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                INNER JOIN sysuser_tab ON wh_user.sysusrrec_id = sysuser_tab.UserID
                                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                                INNER JOIN stakeholder AS mainStk ON tbl_warehouse.stkid = mainStk.stkid
                                                INNER JOIN stakeholder_item ON mainStk.stkid = stakeholder_item.stkid
                                                WHERE
                                                    mainStk.stk_type_id = 0 AND
                                                    mainStk.lvl = 1 AND
                                                    mainStk.is_reporting = 1
                                                    AND tbl_warehouse.is_active = 1
                                                    $where_total
                                                GROUP BY
                                                    tbl_warehouse.prov_id,
                                                    tbl_warehouse.stkid,
                                                    stakeholder_item.stk_item
                                            )
                                            AS A
                                            GROUP BY 
                                                prov_id,stk_item
                                            ";
                                            //echo $qry_1;
                                            $res_1 = mysql_query($qry_1);
                                            $total_sdps= array();
                                            while($row_1 = mysql_fetch_array($res_1))
                                            {
                                                $total_sdps[$row_1['prov_id']][$row_1['stk_item']]=$row_1['total_districts'];
                                                
                                                if(!isset($total_sdps['all'][$row_1['stk_item']])) $total_sdps['all'][$row_1['stk_item']]=0;
                                                $total_sdps['all'][$row_1['stk_item']]+=$row_1['total_districts'];
                                            }
                                          //echo $qry_1;print_r($total_sdps);exit;  
                                     //making list of items , to display list incase no data entry is found
                                    
                                    $w_clause="";
                                    if(!empty($stk) && $stk!='all')             
                                        $w_clause .= " AND stakeholder_item.stkid in (".$stk.")  ";     
                                    
                                    $qry_1 = "  SELECT
                                                    itminfo_tab.itmrec_id,
                                                    itminfo_tab.itm_name,
                                                    itminfo_tab.itm_id
                                                FROM
                                                    itminfo_tab
                                                    INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
                                                WHERE
                                                    itminfo_tab.itm_id in ($product)
                                                    $w_clause
                                                ORDER BY
                                                    itminfo_tab.frmindex ASC
                                            ";
                                    //echo $qry_1;exit;
                                            $res_1 = mysql_query($qry_1);
                                            $itm_arr=$itm_arr2 = array();
                                            while($row_1 = mysql_fetch_array($res_1))
                                            {
                                                $itm_arr[$row_1['itm_id']]=$row_1['itm_name'];
                                            }


                                            //Query for mos
                                             $qry = "
                                                    SELECT
                                                    YEAR (summary_district.reporting_date) AS `Year`,
                                                    MONTH (summary_district.reporting_date) AS `Month`,
                                                    DATE_FORMAT(summary_district.reporting_date,'%Y-%m') AS `Reporting Date`,
                                                    Province.LocName AS Province,
                                                    tbl_locations.LocName AS District,
                                                    itminfo_tab.itm_name,
                                                    stakeholder.stkname AS Stakeholder,
                                                    ROUND((summary_district.soh_district_store / summary_district.avg_consumption),2) AS `mos`,
                                                    summary_district.soh_district_store,
                                                    summary_district.avg_consumption,
                                                    summary_district.province_id as prov_id,
                                                    itminfo_tab.itm_id as item_id,
                                                    summary_district.total_health_facilities,
                                                    summary_district.reporting_rate,
                                                    summary_district.stakeholder_id
                                            FROM
                                                    summary_district
                                            INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
                                            INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
                                            INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
                                            INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
                                            WHERE summary_district.reporting_date = '$from_date'
                                             $where_clause
                                            AND itminfo_tab.itm_category = 1
                                            AND itminfo_tab.itm_id NOT IN(4,6,10,33)
                                            AND stakeholder.stk_type_id = 0 
                                            AND stakeholder.lvl = 1 
                                            AND stakeholder.is_reporting = 1
                                            ORDER BY
                                            tbl_locations.ParentID,
                                            summary_district.item_id,
                                            mos
                                            ";
                                        //echo $qry;
                                        $qryRes = mysql_query($qry);
                                        $xc=1;
                                        $reporting_wh_arr= $unk_arr= $so_arr2 = $so_arr = $us_arr = $sat_arr = $os_arr = array();
                                        $so_arr['all']='';
                                        $prov_arr = array();
                                        $prov_arr['all']='Aggregated';
                                        while($row = mysql_fetch_assoc($qryRes))
                                        {
//                                            if($row['stakeholder_id'] == 1) $sat_max = 6;
//                                            else $sat_max = 7;
                                            $sat_max = 6;
                                            
                                             $prov_arr[$row['prov_id']] = $row['Province'];
                                                
                                            $xc++;
                                            //$itm_arr[$row['item_id']] = $row['itm_name'];
                                            
                                            if(!isset($reporting_wh_arr[$row['prov_id']][$row['item_id']])) $reporting_wh_arr[$row['prov_id']][$row['item_id']]=0;
                                            $reporting_wh_arr[$row['prov_id']][$row['item_id']]+=1;
                                            
                                            if(empty($reporting_wh_arr['all'][$row['item_id']])) $reporting_wh_arr['all'][$row['item_id']]=0;
                                            $reporting_wh_arr['all'][$row['item_id']]+=1;
                                                    
                                            if(empty($so_arr['all'][$row['item_id']])) $so_arr['all'][$row['item_id']]=0;
                                            if(empty($so_arr[$row['prov_id']][$row['item_id']])) $so_arr[$row['prov_id']][$row['item_id']]=0;
                                            if(empty($so_arr2[$row['item_id']])) $so_arr2[$row['item_id']]=0;

//                                            $so_arr[$row['prov_id']][$row['item_id']] += $row['stock_outs'];
//                                            $so_arr2[$row['item_id']] += $row['stock_outs'];
                                            
                                            /*if(!($row['mos'] > '0'))
                                            {
                                                echo '>'.$row['item_id'].':';echo $row['mos'];
                                                if( $row['mos']==NULL )echo 'YES NULL';echo '</br>';
                                            }*/
                                            
                                            if( $row['soh_district_store']==NULL )
                                            {
                                                if(empty($unk_arr[$row['prov_id']][$row['item_id']])) $unk_arr[$row['prov_id']][$row['item_id']]=0;
                                                if(empty($unk_arr['all'][$row['item_id']])) $unk_arr['all'][$row['item_id']]=0;
                                                
                                                $unk_arr[$row['prov_id']][$row['item_id']] += 1;
                                                $unk_arr['all'][$row['item_id']] += 1;
                                               
                                            }
                                            elseif( $row['mos'] <= '0' )
                                            {
                                                $so_arr[$row['prov_id']][$row['item_id']] += 1;
                                                $so_arr['all'][$row['item_id']] += 1;
                                                $so_arr2[$row['item_id']] += 1;
                                            }
                                            elseif( $row['mos'] > 0 && $row['mos'] < 3 )
                                            {
                                                if(empty($us_arr[$row['prov_id']][$row['item_id']])) $us_arr[$row['prov_id']][$row['item_id']]=0;
                                                if(empty($us_arr['all'][$row['item_id']])) $us_arr['all'][$row['item_id']]=0;
                                                
                                                $us_arr[$row['prov_id']][$row['item_id']] += 1;
                                                $us_arr['all'][$row['item_id']] += 1;
                                               
                                            }
                                            elseif( $row['mos'] >= 3 && $row['mos'] < $sat_max )
                                            {
                                                if(empty($sat_arr[$row['prov_id']][$row['item_id']])) $sat_arr[$row['prov_id']][$row['item_id']]=0;
                                                if(empty($sat_arr['all'][$row['item_id']])) $sat_arr['all'][$row['item_id']]=0;
                                                
                                                $sat_arr[$row['prov_id']][$row['item_id']] += 1;
                                                $sat_arr['all'][$row['item_id']] += 1;
                                                
                                            }
                                            elseif( $row['mos'] >= $sat_max )
                                            {
                                                if(empty($os_arr[$row['prov_id']][$row['item_id']])) $os_arr[$row['prov_id']][$row['item_id']]=0;
                                                if(empty($os_arr['all'][$row['item_id']])) $os_arr['all'][$row['item_id']]=0;
                                                
                                                $os_arr[$row['prov_id']][$row['item_id']] += 1;
                                                $os_arr['all'][$row['item_id']] += 1;
                                                
                                            }
                                                                                        
                                        }    
                                        //echo $xc ;
                                        //echo '<pre>'.$total_reporting_wh;print_r($itm_arr);print_r($so_arr);echo 'UNK';print_r($unk_arr);echo'wh_rep';print_r($reporting_wh_arr2);exit;    
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp center"> Pakistan DevResults for Reporting Period : <?php echo date('M-Y',strtotime($from_date)); ?></h3>
                        <h4 class="page-title row  center"> 
                            <div class=" col-md-11">
                                Stock according to plan rate at District Stores : <?php echo $prov_name; ?>
                            </div>
                            <div class=" col-md-1 right">
                                <a id="btnExport" onclick="javascript:xport.toCSV('DevResultsDist');"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></a>
                            </div>
                        </h4>

                   </div>
                </div>
                 <div class="row">
                    <div class="col-md-12">
                        <div class="widget widget-tabs">
                            <div class="widget-body">
                                <table id="DevResultsDist" name="tbl" class="table table-bordered table-condensed" border="">    
                                    <tr>
                                        <th>#</th>
                                        <th align="center">Product</th>
                                        <th align="center">Total Districts</th>
                                        <th align="center">Reported Districts</th>
                                        <!--<th>UNK</th>-->
                                        <th align="center">Stock Outs</th>
                                        <th align="center">Under Stock</th>
                                        <th align="center">Satisfactory</th>
                                        <th align="center">Over Stock</th>
                                        <th align="center">Total</th>
                                        <th align="center">Stock According To Plan Rate</th>
                                    </tr>
                            <?php
                             $grand_total = array();
                                foreach($so_arr as $prov_id => $prov_data)
                                {
                                    if($province != 'all' && $prov_id =='all') continue;
                                    
                                    echo '<tr>';
                                    echo '<td colspan="11" bgcolor="#9cd39c">'.$prov_arr[$prov_id].'</td>';
                                    echo '</tr>';
                                    
                                    $c=1;
                                    foreach($itm_arr as $itm_id => $itm_name)
                                    {
                                        $val = (isset($prov_data[$itm_id])?$prov_data[$itm_id]:0);
                                        
                                        $sat_val = (isset($sat_arr[$prov_id][$itm_id])?$sat_arr[$prov_id][$itm_id]:0);
                                        if(!empty($reporting_wh_arr[$prov_id][$itm_id]) && $reporting_wh_arr[$prov_id][$itm_id] > 0) $perc = ((!empty($sat_val)?$sat_val:0)* 100)/$reporting_wh_arr[$prov_id][$itm_id];
                                        else $perc = 0;
                                        

                                        echo '<tr>';
                                        echo '<td>'.$c++.'</td>';
                                        echo '<td>'.$itm_name.'</td>';
                                        echo '<td align="right">'.$total_sdps[$prov_id][$itm_id].'</td>';
                                        echo '<td align="right">'.$reporting_wh_arr[$prov_id][$itm_id].'</td>';
                                        //echo '<td align="right" title="Click to view list of Districts" onclick="showDrillDown(\''.$prov_id.'\',\''.$prov_arr[$prov_id].'\',\''.$from_date.'\','.$itm_id.',\''.$itm_name.'\',\'UNK\',\''.$stk.'\',\''.$stk_name.'\')">'.number_format($unk_arr[$prov_id][$itm_id]).'</td>';
                                        echo '<td align="right" title="Click to view list of Districts" onclick="showDrillDown(\''.$prov_id.'\',\''.$prov_arr[$prov_id].'\',\''.$from_date.'\','.$itm_id.',\''.$itm_name.'\',\'SO\',\''.$stk.'\',\''.(implode(',',$stk_name)).'\')"><label style="font-size:16px;font-style:bold;">'.number_format($val).'</label>   (<label style="font-size:12px">'.number_format(($val/$reporting_wh_arr[$prov_id][$itm_id])*100,2).'%</label>)'.'</td>';
                                        echo '<td align="right" title="Click to view list of Districts" onclick="showDrillDown(\''.$prov_id.'\',\''.$prov_arr[$prov_id].'\',\''.$from_date.'\','.$itm_id.',\''.$itm_name.'\',\'US\',\''.$stk.'\',\''.(implode(',',$stk_name)).'\')"><label style="font-size:16px;font-style:bold;">'.number_format($us_arr[$prov_id][$itm_id]).' </label>   (<label style="font-size:12px">'.number_format((($us_arr[$prov_id][$itm_id]/$reporting_wh_arr[$prov_id][$itm_id])*100),2).'%</label>)'.'</td>';
                                        echo '<td align="right" title="Click to view list of Districts" onclick="showDrillDown(\''.$prov_id.'\',\''.$prov_arr[$prov_id].'\',\''.$from_date.'\','.$itm_id.',\''.$itm_name.'\',\'SAT\',\''.$stk.'\',\''.(implode(',',$stk_name)).'\')"><label style="font-size:16px;font-style:bold;">'.number_format($sat_arr[$prov_id][$itm_id]).'</label>   (<label style="font-size:12px">'.number_format((($sat_arr[$prov_id][$itm_id]/$reporting_wh_arr[$prov_id][$itm_id])*100),2).'%</label>)'.'</td>';
                                        echo '<td align="right" title="Click to view list of Districts" onclick="showDrillDown(\''.$prov_id.'\',\''.$prov_arr[$prov_id].'\',\''.$from_date.'\','.$itm_id.',\''.$itm_name.'\',\'OS\',\''.$stk.'\',\''.(implode(',',$stk_name)).'\')"><label style="font-size:16px;font-style:bold;"> '.number_format($os_arr[$prov_id][$itm_id]).'</label>   (<label style="font-size:12px">'.number_format((($os_arr[$prov_id][$itm_id]/$reporting_wh_arr[$prov_id][$itm_id])*100),2).'%</label>)'.'</td>';
                                        $t_c = $val + $us_arr[$prov_id][$itm_id] + $sat_arr[$prov_id][$itm_id] + $os_arr[$prov_id][$itm_id];
                                        echo '<td align="right" title=""> '.number_format($t_c).'</td>';
                                        echo '<td align="right">'.number_format($perc,2).'</td>';
                                        echo '</tr>';
                                        
                                        @$grand_total['total_sdps']+=$total_sdps[$prov_id][$itm_id];
                                        @$grand_total['reported_sdps']+=$reporting_wh_arr[$prov_id][$itm_id];
                                        @$grand_total['stock_outs']+=$val;
                                        @$grand_total['under_stock']+=$us_arr[$prov_id][$itm_id];
                                        @$grand_total['sat']+=$sat_arr[$prov_id][$itm_id];
                                        @$grand_total['over_stock']+=$os_arr[$prov_id][$itm_id];
                                        @$grand_total['total_c']+=$t_c;
                                        @$grand_total['plan_rate']+=$perc;
                                                
                                    }
                                }
                                echo '<tr class="warning">';
                                    echo '<td colspan="2">TOTAL</td>';
                                    echo '<td align="right">'.$grand_total['total_sdps'].'</td>';
                                    echo '<td align="right">'.$grand_total['reported_sdps'].'</td>';
                                    
                                    echo '<td align="right">'.$grand_total['stock_outs'].'</td>';
                                    echo '<td align="right">'.$grand_total['under_stock'].'</td>';
                                    echo '<td align="right">'.$grand_total['sat'].'</td>';
                                    echo '<td align="right">'.$grand_total['over_stock'].'</td>';
                                    echo '<td align="right">'.$grand_total['total_c'].'</td>';
                                    echo '<td align="right">'.number_format($grand_total['plan_rate'],2).'</td>';
                                    echo '</tr>';
                                ?>
                                </table>
                          </div>
                      </div>
                   </div>
                 </div>
                <div class="row">
                    <div class="col-md-12">
                    <div class="note note-info">
                        <div style="font-size: 10px;">
                            Note: The facility/store having MOS = 0.00 is considered to be stock out in this report. (MOS Rounded off to 2 decimal places). 
                        </div>
                    </div> 
                    </div>
                </div> 
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget widget-tabs">
                            <div class="widget-body">
                                <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                                  
                                        <?php
                                        //xml for chart
                                        $xmlstore = '<chart caption="Pakistan DevResults at Districts for reporting period : '.date('M-Y',strtotime($from_date)).' - '.$prov_name.' " yaxismaxvalue="100"  subcaption="" xaxisname="Products" exportEnabled="1"  yaxisname="Percentage" numberprefix="" theme="fint">';
                                         $xmlstore .= ' <categories>';
                                        foreach($itm_arr as $itm_id => $itm_name)
                                        {
                                            $xmlstore .= '     <category label="'.$itm_name.'"  />';
                                        }
                                         $xmlstore .= ' </categories>';
                                        
                                        foreach($so_arr as $prov_id => $p)
                                        { 
                                            if($province == 'all' && $prov_id!='all') continue;
                                            
                                            if($province != 'all' && $prov_id =='all') continue;
                                            $xmlstore .= ' <dataset>';
                                                $xmlstore .= ' <dataset seriesname = "Out of stock - '.$prov_arr[$prov_id].' ">';

                                                foreach($itm_arr as $itm_id => $itm_name)
                                                {
                                                    $val = (isset($so_arr[$prov_id][$itm_id])?$so_arr[$prov_id][$itm_id]:0);
                                                    if(!empty($reporting_wh_arr[$prov_id][$itm_id]) && $reporting_wh_arr[$prov_id][$itm_id] > 0)
                                                        $perc = ((!empty($val)?$val:0)* 100)/$reporting_wh_arr[$prov_id][$itm_id];
                                                    else
                                                        $perc = 0;


                                                    $xmlstore .= '     <set label="'.(($perc )).'" value="'.(number_format($perc  , 1)).'"   />';
                                                }

                                                $xmlstore .= '  </dataset>';

                                                
                                                 
                                                $xmlstore .= ' <dataset seriesname = "Under Stock - '.$prov_arr[$prov_id].'">';
                                                foreach($itm_arr as $itm_id => $itm_name)
                                                {
                                                    $val = (isset($us_arr[$prov_id][$itm_id])?$us_arr[$prov_id][$itm_id]:0);
                                                    if(!empty($reporting_wh_arr[$prov_id][$itm_id]) && $reporting_wh_arr[$prov_id][$itm_id] > 0)
                                                        $perc = ((!empty($val)?$val:0)* 100)/$reporting_wh_arr[$prov_id][$itm_id];
                                                    else
                                                        $perc = 0;


                                                    $xmlstore .= '     <set value="'.(number_format($perc  , 1)).'"   />';
                                                }
                                                $xmlstore .= '  </dataset>';


                                                $xmlstore .= ' <dataset seriesname = "Satisfactory Stock - '.$prov_arr[$prov_id].'">';
                                                foreach($itm_arr as $itm_id => $itm_name)
                                                {
                                                    $val = (isset($sat_arr[$prov_id][$itm_id])?$sat_arr[$prov_id][$itm_id]:0);
                                                    if(!empty($reporting_wh_arr[$prov_id][$itm_id]) && $reporting_wh_arr[$prov_id][$itm_id] > 0)
                                                        $perc = ((!empty($val)?$val:0)* 100)/$reporting_wh_arr[$prov_id][$itm_id];
                                                    else
                                                        $perc = 0;


                                                    $xmlstore .= '     <set value="'.(number_format($perc  , 1)).'"   />';
                                                }
                                                $xmlstore .= '  </dataset>';


                                                $xmlstore .= ' <dataset seriesname = "Over Stock - '.$prov_arr[$prov_id].'">';
                                                foreach($itm_arr as $itm_id => $itm_name)
                                                {
                                                    $val = (isset($os_arr[$prov_id][$itm_id])?$os_arr[$prov_id][$itm_id]:0);
                                                    if(!empty($reporting_wh_arr[$prov_id][$itm_id]) && $reporting_wh_arr[$prov_id][$itm_id] > 0)
                                                        $perc = ((!empty($val)?$val:0)* 100)/$reporting_wh_arr[$prov_id][$itm_id];
                                                    else
                                                        $perc = 0;


                                                    $xmlstore .= '     <set value="'.$perc.'"   />';
                                                }
                                                $xmlstore .= '  </dataset>';
                                                
                                                 
                                                $xmlstore .= '  </dataset>';
                                        }
                                    $xmlstore .= ' </chart>';
                                    //include chart
                                    FC_SetRenderer('javascript');
                                    echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSStackedColumn2D.swf", "", $xmlstore, $chart_id, '100%', 300, false, false);
                                    ?>
                            </div>
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
                
            </div>
        </div>
    </div>

    <?php 
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; ?>
    <script>
        $(function() {

            $('#from_date, #to_date').datepicker({
                dateFormat: "yy-mm",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                maxDate: '' 
            });
        })
        
       
 
         
     function showDrillDown(prov,prov_name,from_date,prod_id,prod_name,indicator,stk,stk_name) {
       
        var url = 'dev_results_dist_drilldown.php';
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