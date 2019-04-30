<?php
ini_set('max_execution_time', 0);
include("../includes/classes/Configuration.inc.php");
Login();
include(APP_PATH . "includes/classes/db.php");
include APP_PATH . "includes/classes/functions.php";
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
include(PUBLIC_PATH . "html/header.php");
$fundingSourceText = 'All Funding Sources';
$caption = 'Product wise Distribution and SOH';
$subCaption = '';
$downloadFileName = 'a';
$chart_id = 'distributionAndSOH';
$f_date= (!empty($_REQUEST['from_date'])?$_REQUEST['from_date']:date("Y-m").'-01');
$province = (!empty($_REQUEST['province'])?$_REQUEST['province']:'all');
$stk = (!empty($_REQUEST['stakeholder'])?$_REQUEST['stakeholder']:'all');
$from_date = date("Y-m-d", strtotime($f_date));

$where_clause ="";
$where_clause .= " AND ( ";
$where_clause .= "          ( tbl_warehouse.prov_id = 1 AND tbl_warehouse.stkid in (1,73,7) ) ";
$where_clause .= "          OR ( tbl_warehouse.prov_id = 2 AND tbl_warehouse.stkid in (1,9) ) ";
$where_clause .= "          OR ( tbl_warehouse.prov_id = 3 AND tbl_warehouse.stkid = 1 ) ";
$where_clause .= "          OR ( tbl_warehouse.prov_id = 4 AND tbl_warehouse.stkid = 1 ) ";
$where_clause .= "      ) ";

?>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
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
                                         tbl_warehouse.wh_id NOT IN (
                                                SELECT
                                                        warehouse_status_history.warehouse_id
                                                FROM
                                                        warehouse_status_history
                                                INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                                                WHERE
                                                        warehouse_status_history.reporting_month = '".$from_date."'
                                                AND warehouse_status_history.`status` = 0
                                        )

                                        AND stakeholder.lvl = 7
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
                                          //echo $qry_1;print_r($total_sdps);exit;  
                                     //making list of items , to display list incase no data entry is found
                                    
                                   
                                    
                                    $qry_1 = "  SELECT
                                                    itminfo_tab.itmrec_id,
                                                    itminfo_tab.itm_name,
                                                    itminfo_tab.itm_id
                                                FROM
                                                    itminfo_tab
                                                    INNER JOIN stakeholder_item ON stakeholder_item.stk_item = itminfo_tab.itm_id
                                                WHERE
                                                    itminfo_tab.itm_id in (1,5,7,9)
                                                ORDER BY
                                                    itminfo_tab.frmindex ASC
                                            ";
                                            $res_1 = mysql_query($qry_1);
                                            $itm_arr=$itm_arr2 = array();
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
                                                                    tbl_locations.LocName
                                                            FROM
                                                                    tbl_warehouse
                                                            INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                                            INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
                                                            WHERE
                                                                    tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
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
                                                             
                                                            AND stakeholder.lvl = 7
                                                            AND tbl_hf_data.reporting_date = '".$from_date."'
                                                            $where_clause
                                                            GROUP BY
                                                                    tbl_warehouse.prov_id,
                                                                    tbl_hf_data.item_id";
                                            //echo $q_reporting;
                                            $res_reporting = mysql_query($q_reporting);
                                            $reporting_wh_arr  = $prov_arr = array();
                                            $total_reporting_wh = 0;
                                            $prov_arr['all']='Aggregated';
                                            while($row=mysql_fetch_assoc($res_reporting))
                                            {
                                                $prov_arr[$row['prov_id']] = $row['LocName'];
                                                
                                                if(empty($reporting_wh_arr['all'][$row['item_id']])) $reporting_wh_arr['all'][$row['item_id']]=0;
                                                if(empty($reporting_wh_arr[$row['prov_id']][$row['item_id']])) $reporting_wh_arr[$row['prov_id']][$row['item_id']]=0;
                                                if(empty($reporting_wh_arr2[$row['item_id']])) $reporting_wh_arr2[$row['item_id']]=0;
                                                
                                                $reporting_wh_arr[$row['prov_id']][$row['item_id']]+=$row['reportedWH'];
                                                $reporting_wh_arr['all'][$row['item_id']]+=$row['reportedWH'];
                                                $reporting_wh_arr2[$row['item_id']] +=$row['reportedWH'];
                                                $total_reporting_wh +=$row['reportedWH'];
                                            }



                                            //Query for mos
                                            $qry = "SELECT
                                                    tbl_warehouse.prov_id,
                                                    tbl_warehouse.stkid,
                                                    tbl_hf_data.item_id ,
                                                    itminfo_tab.itm_name,
                                                    tbl_hf_data.pk_id,
                                                    (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption) as mos,
                                                    tbl_hf_data.closing_balance,
                                                    tbl_hf_data.avg_consumption
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
                                                        AND itminfo_tab.itm_id in (1,5,7,9)
                                                        AND itminfo_tab.itm_category = 1
                                                        
                                                    ORDER BY
                                                            tbl_warehouse.prov_id,
                                                            tbl_hf_data.item_id,
                                                             mos

                                            ";
                                        //echo $qry;exit;
                                        $qryRes = mysql_query($qry);
                                        $xc=1;
                                        $unk_arr= $so_arr2 = $so_arr = $us_arr = $sat_arr = $os_arr = array();
                                        $so_arr['all']='';

                                        while($row = mysql_fetch_assoc($qryRes))
                                        {
                                            $xc++;
                                            $itm_arr[$row['item_id']] = $row['itm_name'];

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
                                            
                                            if( $row['closing_balance']==NULL )
                                            {
                                                if(empty($unk_arr[$row['prov_id']][$row['item_id']])) $unk_arr[$row['prov_id']][$row['item_id']]=0;
                                                if(empty($unk_arr['all'][$row['item_id']])) $unk_arr['all'][$row['item_id']]=0;
                                                
                                                $unk_arr[$row['prov_id']][$row['item_id']] += 1;
                                                $unk_arr['all'][$row['item_id']] += 1;
                                               
                                            }
                                            elseif( $row['closing_balance'] <= '0' )
                                            {
                                                $so_arr[$row['prov_id']][$row['item_id']] += 1;
                                                $so_arr['all'][$row['item_id']] += 1;
                                                $so_arr2[$row['item_id']] += 1;
                                            }
                                                                                        
                                        }    
                                        //echo '<pre>';print_r($so_arr2);print_r($so_arr);echo 'UNK';print_r($unk_arr);echo'wh_rep';print_r($reporting_wh_arr2);exit;    
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp center"> Stockout Data Table for Reporting Period : <?php echo date('M-Y',strtotime($from_date)); ?></h3>
                        <h4 class="page-title row  center"> 
                            <div class=" col-md-11">
                                Stock Out Rate at SDPs All Provinces
                            </div>
                            <div class=" col-md-1 right">
                                <a id="btnExport" onclick="javascript:xport.toCSV('DevResultsSDP');"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></a>
                            </div>
                        </h4>
                        
                   </div>
                    
                </div>
                 <div class="row">
                    <div class="col-md-12">
                        <div class="widget widget-tabs">
                            <div class="widget-body">
                                <table id="DevResultsSDP" name="tbl" class="table table-bordered table-condensed" border="">    
                                    <tr>
                                        <th>#</th>
                                        <th align="center">Rep Period End Date</th>
                                        <th align="center">Country</th>
                                        <th align="center">Country Program</th>
                                        <th align="center">Tracer Product</th>
                                        <!--<th align="center">Total SDPs</th>-->
                                        <th align="center">Reported SDPs</th>
                                        <th align="center">Stock Outs</th>
                                        <!--<th align="center">Stock out Rate</th>-->
                                    </tr>
                            <?php
                                $custom_items = array();
                                
                                $custom_items[0]['id']='7';
                                $custom_items[0]['name']='PRH-Depot Medroxyprogesterone Acetate 150 mg Vial, Intramuscular';
                                $custom_items[1]['id']='7';
                                $custom_items[1]['name']='PRH-Injectable contraceptives';
                                
                                $custom_items[2]['id']='9';
                                $custom_items[2]['name']='PRH-Levonorgestrel/Ethinyl Estradiol 150/30 mcg + Fe 75 mg, 28 Tablets/Cycle';
                                $custom_items[3]['id']='9';
                                $custom_items[3]['name']='PRH-Combined oral contraceptives';
                                
                                $custom_items[4]['id']='1';
                                $custom_items[4]['name']='PRH-Male condoms';
                                
                                $custom_items[5]['id']='5';
                                $custom_items[5]['name']='PRH-Copper bearing intrauterine devices';
                                
                                $e_date = $from_date;
                                for($i=1;$i<=12;$i++){
                                    $m = date('m',strtotime($from_date));
                                    if($m == $i){
                                            $d = date('n',strtotime('2018-'.$i.'-01'));

                                            $y = date('Y',strtotime($from_date));
                                            $d2 = $y ."-".sprintf("%02d", (ceil($d/3)*3))."-01";
                                            $qtr_end = date("Y-m-t",strtotime($d2));
                                            $e_date = $qtr_end;
                                            break;
                                    }
                                }

                                foreach($so_arr as $prov_id => $prov_data)
                                {
                                    if($prov_id != 'all'  ) continue;
                                    
                                    //echo '<tr>';
                                    //echo '<td colspan="11" bgcolor="#9cd39c">'.$prov_arr[$prov_id].'</td>';
                                    //echo '</tr>';
                                    
                                    $c=1;
                                    foreach($custom_items as $k => $itm_data)
                                    {
                                        $itm_id = $itm_data['id'];  
                                        $itm_name = $itm_data['name'];    
                                        
                                        $val = (isset($prov_data[$itm_id])?$prov_data[$itm_id]:0);
                                        if(!empty($reporting_wh_arr[$prov_id][$itm_id]) && $reporting_wh_arr[$prov_id][$itm_id] > 0) $perc = ((!empty($val)?$val:0)* 100)/$reporting_wh_arr[$prov_id][$itm_id];
                                        else $perc = 0;
                                        
                                        //if(!empty($reporting_wh_arr[$prov_id][$itm_id]) && $reporting_wh_arr[$prov_id][$itm_id] > 0) $perc2 = ((!empty(($val+$unk_arr[$prov_id][$itm_id]))?($val+$unk_arr[$prov_id][$itm_id]):0)* 100)/$reporting_wh_arr[$prov_id][$itm_id];
                                        //else $perc2 = 0;

                                        echo '<tr>';
                                        echo '<td>'.$c++.'</td>';
                                        echo '<td>'.$e_date.'</td>';
                                        echo '<td>Pakistan</td>';
                                        echo '<td>Pakistan</td>';
                                        echo '<td>'.$itm_name.'</td>';
                                        //echo '<td align="right">'.$total_sdps[$prov_id][$itm_id].'</td>';
                                        echo '<td align="right">'.$reporting_wh_arr[$prov_id][$itm_id].'</td>';
                                        echo '<td class="info" align="right" title="Click to view list of SDPs"  >'.number_format($val).'</td>';
                                        //echo '<td align="right">'.number_format($perc,2).'</td>';
                                        echo '</tr>';
                                    }
                                }
                                ?>
                                </table>
                          </div>
                      </div>
                   </div>
                 </div>
                
                <div class="row">
                    <div class="col-md-12">
                    <div class="note note-danger">
                        <div style="font-size: 12px;">
                            <u><b>Disclaimer</b></u>: <br/>
                            The SDPs having Zero <b>SOH</b> are considered to be stock out in this report.<br/>
                            The following facility types are NOT included in the calculation of this report: MSU,Social Mobilizer,RHS-B,RMPS,Hakeems,Homopaths,PLDs,TBAs,Counters,DDPs.
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
    <script src="<?=PUBLIC_URL?>js/bootstrap_multiselect.js"></script>
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
    </script>
</body>
<!-- END BODY -->
</html>