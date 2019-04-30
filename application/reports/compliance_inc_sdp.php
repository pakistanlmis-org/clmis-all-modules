<?php
 
$chart_id = 'compliance_sdp';
$where_clause ="   ";
$where_clause .=" AND tbl_warehouse.prov_id =$selPro ";
$where_clause .=" AND tbl_warehouse.stkid =$selStk ";
        //get total number of facilities in province
        $qry_1 = "  
            SELECT
                tbl_warehouse.dist_id,
                COUNT( DISTINCT tbl_warehouse.wh_id ) AS totalWH,
                tbl_warehouse.reporting_start_month
            FROM
                    tbl_warehouse ";
        if(!($selPro == 3 && $selStk ==7 ))
        $qry_1 .= " INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id ";
        $qry_1 .= "
            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
            INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
            WHERE
                stakeholder.lvl = 7
                
            $where_clause
            GROUP BY
                tbl_warehouse.dist_id,
                tbl_warehouse.reporting_start_month
                ";
            //echo $qry_1;exit;
                $res_1 = mysql_query($qry_1);
                $total_sdps= array();
                while($row_1 = mysql_fetch_array($res_1))
                {
                    $total_sdps[$row_1['dist_id']][$row_1['reporting_start_month']]=$row_1['totalWH'];

                    if(!isset($total_sdps['all'][$row_1['reporting_start_month']])) $total_sdps['all'][$row_1['reporting_start_month']]=0;
                    $total_sdps['all'][$row_1['reporting_start_month']]+=$row_1['totalWH'];
                }
        //echo'<pre>'; print_r($total_sdps);exit;

         //counting the disabled facilities 
         $disabled_qry = "
                    SELECT

                        COUNT(DISTINCT warehouse_status_history.warehouse_id) as cnt,
                        tbl_warehouse.dist_id,
                        warehouse_status_history.reporting_month
                    FROM
                            warehouse_status_history
                    INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                    INNER JOIN stakeholder_item ON tbl_warehouse.stkid = stakeholder_item.stkid
                     ";
        if(!($selPro == 3 && $selStk ==7 ))
        $disabled_qry .= " INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id";
        $disabled_qry .= "
            
                    
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    WHERE
                            warehouse_status_history.reporting_month BETWEEN '".$start_date."' and '".$last_date."'
                            AND warehouse_status_history.`status` = 0
                            /*AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)*/
                            AND stakeholder.lvl=7
                            $where_clause
                    GROUP BY
                            tbl_warehouse.dist_id,
                            warehouse_status_history.reporting_month
            ";
        //echo $disabled_qry;exit;
        $res_d = mysql_query($disabled_qry);
        $disabled_count= array();
        while($row_d = mysql_fetch_array($res_d))
        {
            $disabled_count[$row_d['dist_id']][$row_d['reporting_month']]=$row_d['cnt'];
            if(empty($disabled_count['all'][$row_d['reporting_month']])) $disabled_count['all'][$row_d['reporting_month']]=0;
            $disabled_count['all'][$row_d['reporting_month']] +=$row_d['cnt'];
        }   
        //echo '<pre>';print_r($disabled_count);exit;      
        //making list of items , to display list incase no data entry is found
         
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
                                        tbl_warehouse.dist_id,
                                        tbl_hf_data.reporting_date
                                ORDER BY LocName ";
                //echo $q_reporting;exit;
                $res_reporting = mysql_query($q_reporting);
                $reporting_wh_arr  = $dist_arr = array();
                $total_reporting_wh = 0;
                //$prov_arr['all']='Aggregated';
                while($row=mysql_fetch_assoc($res_reporting))
                {
                    $dist_arr[$row['dist_id']] = $row['LocName'];

                    if(empty($reporting_wh_arr['all'][$row['reporting_date']])) $reporting_wh_arr['all'][$row['reporting_date']]=0;
                    if(empty($reporting_wh_arr[$row['dist_id']][$row['reporting_date']])) $reporting_wh_arr[$row['dist_id']][$row['reporting_date']]=0;
                    
                    $reporting_wh_arr[$row['dist_id']][$row['reporting_date']]+=$row['reportedWH'];
                    $reporting_wh_arr['all'][$row['reporting_date']]+=$row['reportedWH'];
                    
                    $total_reporting_wh +=$row['reportedWH'];
                }

                $rep_rate=$to_be_reported_arr= array();
                foreach($dist_arr as $dist_id => $prov_data){
                    
                    foreach($months_list as $k => $v){
                        $this_t_sdp=0;
                        foreach($total_sdps[$dist_id] as $mn => $t_sdp){
                            if($mn<=$v)
                            $this_t_sdp+=$t_sdp;
                        }
                        //$master_total = $total_sdps[$dist_id];
                        $master_total = $this_t_sdp;
                        $disabled_fac = (isset($disabled_count[$dist_id][$v])?$disabled_count[$dist_id][$v]:0);
                        $to_be_reported = $master_total - $disabled_fac;
                        
                        //overriding the 'to be reported' value to the reported , in case of greater value
                        if(isset($reporting_wh_arr[$dist_id][$v]) && isset($disabled_count[$dist_id][$v]) && $to_be_reported < $reporting_wh_arr[$dist_id][$v])  $to_be_reported = $reporting_wh_arr[$dist_id][$v];
                        
                        $to_be_reported_arr[$dist_id][$v] = $to_be_reported;
                        
                        $val = (isset($prov_data[$v])?$prov_data[$v]:0);

                        if($to_be_reported>0 && isset($reporting_wh_arr[$dist_id][$v]))
                            $r_r = ($reporting_wh_arr[$dist_id][$v]*100)/$to_be_reported;
                        else
                            $r_r=0;

                        $rep_rate[$dist_id][$v] = $r_r;
                    }
                    
                }
        //echo'<pre>';print_r($reporting_wh_arr); print_r($rep_rate);exit;
        ?>
                
                
                <div class="row">
                    <div class="col-md-12">
                        <table width="100%" cellpadding="0" cellspacing="0" id="myTable" class="table table-bordered table-condensed">
                             <tr>
                                <td align="center" width="95%"   ><h3 class="text-info">Compliance Report - SDPs- <?=$province_name?> - <?=$stk_name?></h3></td>
                                <td align="center" width="5%"   >
                                     <img title="Click here to export data to PDF file" style="cursor:pointer;" src="<?php echo PUBLIC_URL ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');"/>
                                     <img title="Click here to export data to Excel file" style="cursor:pointer;" src="<?php echo PUBLIC_URL ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');"/>
                                    </td>
                             </tr>
                        </table>
                        
                        <div>
                            <table width="100%" cellpadding="0" cellspacing="0" id="compliance_Report_SDP" class="table table-bordered table-condensed">
                            <tr class=" bg-blue-madison">
                                <td rowspan="2">District</td>
                                <?php
                                foreach($months_list as $k => $month ){
                                    echo '<td colspan="2" align="center">'.date('M-Y',strtotime($month)).'</td>';
                                    echo '<td style="display:none;"></td>';
                                }
                                
                                ?>
                                
                            </tr>
                            <tr class=" bg-blue-madison">
                                 
                                <?php
                                foreach($months_list as $k => $month ){
                                    echo '<td title="Reported SDPs / Total Active SDPs">R / T</td>';
                                    echo '<td>Rep. Rate (%)</td>';
                                }
                                ?>
                            </tr>
                            <?php
                                foreach($dist_arr as $dist_id => $dist_name ){
                                    if($dist_id=='all') continue;
                                    echo ' <tr>
                                            <td>'.$dist_name.'</td>';
                                    foreach($months_list as $k => $month ){
                                        $perc=0;
                                        if( !empty($rep_rate[$dist_id][$month])   )
                                        $perc = $rep_rate[$dist_id][$month];
                                        
                                        $clr= 'green';
                                        if($perc < 85) $clr ='orange';
                                        if($perc < 50) $clr ='red';
                                        
                                        echo '<td  align="center"><span style="font-size:11px;vertical-align:top;">'.(!empty($reporting_wh_arr[$dist_id][$month])?$reporting_wh_arr[$dist_id][$month]:0).'</span>';;
                                        echo '<span style="font-size:18px;">/</span>';
                                        echo '<span style="font-size:11px;padding-top:30px">'.$to_be_reported_arr[$dist_id][$month].'</span></td>';
                                        echo '<td align="right">';
                                        echo '      <a target="_blank" href="compliance_hf.php?ending_month='.$selMonth.'&year_sel='.$selYear.'&stk_sel='.$selStk.'&prov_sel='.$selPro.'&district='.$dist_id.'&submit=submit">';
                                        
                                        echo '  <span style="font-size:15px; color:'.$clr.' !important;">';
                                        echo '          <b>'.number_format($perc,2).'</b>';
                                        echo '  </span>';
                                        echo '      </a>';
                                        echo '</td>';
                                    }
                                    echo ' </tr>';
                                }
                                ?>
                        </table>
                            
                        </div>
                        
                         <?php
                        $xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
                        $xmlstore .= "<rows>";
                            foreach($dist_arr as $dist_id => $dist_name ){
                                    if($dist_id=='all') continue;
                                    $xmlstore .= "<row>";
                                    $xmlstore .= "<cell>".$dist_name."</cell>";
                                    foreach($months_list as $k => $month ){
                                        $perc=0;
                                        if( !empty($rep_rate[$dist_id][$month])   )
                                        $perc = $rep_rate[$dist_id][$month];
                                        
                                        $xmlstore .= "<cell>".number_format($perc,1)."</cell>";
                                    }
                                    $xmlstore .= "</row>";
                                }
                                
                            $xmlstore .= "</rows>";
                                ?>
                        
                        
                            <div id="mygrid_container" style="width:100%; height:1100px;"  ></div>  
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="note note-info">
                            <p>
                                <b>Note:</b> Click on Percentage to View SDP Wise Compliance Report
                                 
                            </p>
                        </div>
                    </div>
                </div>

<div class="widget widget-tabs">    
    <div class="widget-body">
<?php
//xml for chart
    $chart_data = '<chart caption="District Wise SDPs Reporting Rate " exportenabled="0"  subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Months" yaxisname="Percentage" showvalues="0" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
 
    $chart_data .= ' <categories>';
    foreach($months_list as $k => $month)
    {
        $chart_data .= ' <category label="'.date('Y-M',strtotime($month)).'" />';
    }
    $chart_data .= ' </categories>';
    
    $temp_count = 1;
    foreach($rep_rate as $dist_id => $r_rate)
    {
        $chart_data .= ' <dataset seriesname="'.$dist_arr[$dist_id].'"  '.(($temp_count>1) ? ' initiallyHidden="1" ':'').'>';
        foreach($months_list as $k => $month)
        {   
            $val=(!empty($r_rate[$month])? $r_rate[$month]:'0');
            $chart_data .= '    <set  value="'.$val.'"  />';
        }
        $chart_data .= '  </dataset>';
        $temp_count++;
    }
    $chart_data .= ' </chart>';
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSSpline.swf", "", $chart_data, $chart_id, '100%', 300, false, false);
?>
    </div>
</div> 
<?php
$cspan = $header = $width = $ro = $align = $stkName = $locName = '';
$header .= "<span title='District'>District</span>";
foreach($months_list as $k => $month ){
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
        mygrid.setHeader("<div style='text-align:center;'>Compliance Report - SDPs- <?=$province_name?> - <?=$stk_name?></div><?=$cspan?>");
        mygrid.attachHeader("<?=$header?>");
        mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?> <br> </div><?php echo $cspan; ?>");

        mygrid.setColAlign("left,right,right,right,right,right,right,right,right,right,right,right,right");
        mygrid.setInitWidths("*,80,80,80,80,80,80,80,80,80,80,80,80");
        mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
        mygrid.enableRowsHover(true, 'onMouseOver'); // `onMouseOver` is the css cla ss name.
        mygrid.setSkin("light");
        mygrid.init();
        mygrid.clearAll();
        mygrid.loadXMLString('<?php echo $xmlstore; ?>');
    }
        
</script>