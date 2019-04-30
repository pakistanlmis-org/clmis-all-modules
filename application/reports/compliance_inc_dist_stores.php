<?php

$chart_id ='dist_stores_compliance';
//print_r($_REQUEST);
        $qry_1 = "  
            SELECT
                        tbl_warehouse.prov_id,
                        tbl_warehouse.stkid,
                        mainStk.stkname,
                        prov.LocName AS prov_name,
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
                INNER JOIN tbl_locations prov ON tbl_warehouse.prov_id = prov.PkLocID
                WHERE
                        mainStk.stk_type_id = 0
                AND mainStk.lvl = 1
                AND mainStk.is_reporting = 1
                AND tbl_warehouse.is_active = 1 AND
                prov.LocType IN (2,3)
                AND tbl_warehouse.stkid = $selStk
                GROUP BY
                        tbl_warehouse.prov_id,
                        tbl_warehouse.stkid
            ";
            //echo $qry_1;exit;
            $res_1 = mysql_query($qry_1);
            $total_dist=$provinces =  array();
            while($row_1 = mysql_fetch_array($res_1))
            {
                $provinces[$row_1['prov_id']] = $row_1['prov_name'];
                
                $total_dist[$row_1['prov_id']]=$row_1['total_districts'];

                if(!isset($total_dist['all'])) $total_dist['all']=0;
                $total_dist['all']+=$row_1['total_districts'];
            }
            //echo '<pre>';print_r($total_dist);exit;
            
            $qry = "SELECT
                        count(distinct tbl_wh_data.wh_id) as reported,
                        tbl_wh_data.report_month,
                        tbl_wh_data.report_year,
                        tbl_wh_data.RptDate,
                        tbl_warehouse.stkid,
                        tbl_warehouse.prov_id
                    FROM
                    tbl_wh_data
                    INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    WHERE
                        stakeholder.lvl = 3 AND
                        tbl_wh_data.RptDate between  '$start_date' AND '$last_date'
                                
                        AND tbl_warehouse.stkid = $selStk
                    group BY
                        tbl_warehouse.prov_id,
                        tbl_warehouse.stkid,
                        tbl_wh_data.RptDate
                ";
            //echo $qry;  
            $qryRes = mysql_query($qry);
            $reporting_wh_arr = $reporting_prov_wise = array();
            while($row = mysql_fetch_assoc($qryRes))
            {
                $reporting_wh_arr[$row['prov_id']][$row['stkid']][$row['RptDate']]= $row['reported'];
                if(empty($reporting_prov_wise[$row['prov_id']][$row['RptDate']])) $reporting_prov_wise[$row['prov_id']][$row['RptDate']]=0;
                $reporting_prov_wise[$row['prov_id']][$row['RptDate']] += $row['reported'];
//                if(empty($reporting_wh_arr['all'][$row['stkid']][$row['RptDate']])) $reporting_wh_arr['all'][$row['stkid']][$row['RptDate']]=0;
//                $reporting_wh_arr['all'][$row['stkid']][$row['RptDate']]+=1;
            }
            
            //echo'<pre>'; print_r($reporting_wh_arr);exit;
        ?>
                
                
                <div class="row" id="div_main">
                    <div class="col-md-12" id="div_main2">
                        <table width="100%" cellpadding="0" cellspacing="0" id="myTable" class="table table-bordered table-condensed">
                             <tr>
                                 <td align="center" width="95%"   ><h3 class="text-info">Compliance Report - District Stores - <?=$stk_name?></h3></td>
                                <td align="center" width="5%"   >
                                     <img title="Click here to export data to PDF file" style="cursor:pointer;" src="<?php echo PUBLIC_URL ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');"/>
                                     <img title="Click here to export data to Excel file" style="cursor:pointer;" src="<?php echo PUBLIC_URL ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');"/>
                                    
                                </td>
                             </tr>
                        </table>
                        <div>
                            <table width="100%" cellpadding="0" cellspacing="0" id="compliance_Report" class="table table-bordered table-condensed">
                             <tr class=" bg-blue-madison">
                                 <td rowspan="2">Province</td>
                                <?php
                                foreach($months_list as $k => $month ){
                                    echo '<td colspan="2" align="center"><b>'.date('M-Y',strtotime($month)).'</b></td>';
                                    echo '<td style="display:none;"></td>';
                                }
                                
                                ?>
                                
                            </tr>
                            <tr class=" bg-blue-madison">
                                 
                                <?php
                                foreach($months_list as $k => $month ){
                                    echo '<td title="Reported Warehouses / Total Active Warehouses">R / T</td>';
                                    echo '<td>Rep. Rate</td>';
                                }
                                
                                ?>
                                
                            </tr>
                            <?php
                                foreach($total_dist as $prov => $t_dist ){
                                    if($prov=='all') continue;
                                    echo ' <tr>
                                            <td>'.$provinces[$prov].'</td>';
                                    foreach($months_list as $k => $month ){
                                        $reported =0;
                                        if(!empty($reporting_prov_wise[$prov][$month]))
                                        $reported = $reporting_prov_wise[$prov][$month];
                                        
                                        $perc = 0 ;
                                        if( !empty($t_dist) && $t_dist>0 )
                                        $perc = 100 * ($reported / $t_dist);
                                        
                                        $clr= 'green';
                                        if($perc < 85) $clr ='orange';
                                        if($perc < 50) $clr ='red';
                                        
                                        
                                        echo '<td  align="center">';
                                        echo '<span style="display:none;">\'</span>';
                                        echo '<span style="font-size:11px;vertical-align:top;">'.$reported.'</span>';;
                                        echo '<span style="font-size:18px;">/</span>';
                                        echo '<span style="font-size:11px;padding-top:30px">'.$t_dist.'</span></td>';
                                        echo '<td align="right"><span style="font-size:15px; color:'.$clr.';"><b>'.number_format($perc,2).'</b></span>%</td>';
                                    }
                                     
                                    echo ' </tr>';
                                }
                                
                                ?>
                            
                        </table>
                        </div>
                        <div >
                        <?php
                        $xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
                        $xmlstore .= "<rows>";
//                            $xmlstore .= "<row>";
//                            $xmlstore .= "<cell>Province</cell>";
//                                foreach($months_list as $k => $month ){
//                                    $xmlstore .=  '<cell>'.date('M-Y',strtotime($month)).'</cell>';
//                                    $xmlstore .=  '<cell></cell>';
//                                }
//                                $xmlstore .= "</row>";
//                                $xmlstore .= "<row>";
//                                foreach($months_list as $k => $month ){
//                                    $xmlstore .=  '<cell>R / T</cell>';
//                                    $xmlstore .=  '<cell>Rep. Rate</cell>';
//                                }
//                            $xmlstore .= "</row>";
                                foreach($total_dist as $prov => $t_dist ){
                                    if($prov=='all') continue;
                                   $xmlstore .= "<row>";
                                    $xmlstore .=  '<cell>'.$provinces[$prov].'</cell>';
                                    foreach($months_list as $k => $month ){
                                        $reported =0;
                                        if(!empty($reporting_prov_wise[$prov][$month]))
                                        $reported = $reporting_prov_wise[$prov][$month];
                                        
                                        $perc = 0 ;
                                        if( !empty($t_dist) && $t_dist>0 )
                                        $perc = 100 * ($reported / $t_dist);
                                        
                                        $clr= 'green';
                                        if($perc < 85) $clr ='orange';
                                        if($perc < 50) $clr ='red';
                                        
                                        
                                        //echo '<td  align="center">';
                                        //echo '<span style="display:none;">\'</span>';
                                        //echo '<span style="font-size:11px;vertical-align:top;">'.$reported.'</span>';;
                                        //echo '<span style="font-size:18px;">/</span>';
                                        //echo '<span style="font-size:11px;padding-top:30px">'.$t_dist.'</span></td>';
                                        $xmlstore .=  '<cell>'.number_format($perc,1).'</cell>';
                                    }
                            $xmlstore .= "</row>";
                                }
                                
                            $xmlstore .= "</rows>";
                                ?>
                     
                            <div id="mygrid_container" style="width:100%; height:450px;"  ></div>       
                    </div>
                    </div>
                </div>
                

<div class="widget widget-tabs">    
    <div class="widget-body">
<?php

//
    $chart_data = '<chart caption="District Stores Reporting Rate" exportenabled="0"    subcaption="" captionfontsize="14" subcaptionfontsize="14" basefontcolor="#333333" basefont="Helvetica Neue,Arial" subcaptionfontbold="0" xaxisname="Months" yaxisname="Percentage" showvalues="0" palettecolors="#0075c2,#1aaf5d,#AF1AA5,#AF711A,#D93636" bgcolor="#ffffff" showborder="0" showshadow="0" showalternatehgridcolor="0" showcanvasborder="0" showxaxisline="1" xaxislinethickness="1" xaxislinecolor="#999999" canvasbgcolor="#ffffff" legendborderalpha="0" legendshadow="0" divlinealpha="100" divlinecolor="#999999" divlinethickness="1" divlinedashed="1" divlinedashlen="1" >';
 
    $chart_data .= ' <categories>';
    foreach($months_list as $k => $month)
    {
        $chart_data .= ' <category label="'.date('Y-M',strtotime($month)).'" />';
    }
    $chart_data .= ' </categories>';
    
    
    $temp_count = 1;
    foreach($total_dist as $prov => $t_dist )
    {
        if($prov=='all')continue;
        $chart_data .= ' <dataset seriesname="'.$provinces[$prov].'" '.(($temp_count>1) ? ' initiallyHidden="1" ':'').'>';
        foreach($months_list as $k => $month)
        {   
            $reported =0;
            if(!empty($reporting_prov_wise[$prov][$month]))
            $reported = $reporting_prov_wise[$prov][$month];
            $perc = 0 ;
            if( !empty($t_dist) && $t_dist>0 )
            $perc = 100 * ($reported / $t_dist);
            $chart_data .= '    <set  value="'.$perc.'"  />';
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
$header .= "<span title='Province'>Province</span>";
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
        mygrid.setHeader("<div style='text-align:center;'>Compliance Report - District Stores - <?=$stk_name?></div><?=$cspan?>");
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