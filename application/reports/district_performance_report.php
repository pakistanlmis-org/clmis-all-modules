<?php

/**
 * spr8
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//echo '<pre>';print_r($_REQUEST);exit;
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH."html/header.php");
//report id
$rptId = 'dpr';
//user province id
$userProvId = $_SESSION['user_province1'];
//if submitted
if (isset($_POST['submit'])) {
    //get from date
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //get to date
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //get selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //get district id
    $districtId = mysql_real_escape_string($_POST['district']);
    //get stakeholder
    $stakeholder     = mysql_real_escape_string($_POST['stakeholder']);
//select query
    // Get district name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $districtId";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    //district name
    $distrctName = $row['LocName'];
    
    // Get item data
    $qry = "SELECT
                *
            FROM
                itminfo_tab";
    //query result
    $qryRes = mysql_query($qry);
    $item_arr = array();
    while($row = mysql_fetch_assoc($qryRes))
    {
        $item_arr[$row['itmrec_id']] = $row;
    }
   // echo '<pre>';
   // print_r($item_arr);
    //file name
    $fileName = 'DPR_' . $distrctName . '_for_' . $fromDate . '-' . $toDate;
}
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
		<?php 
                //include top
                include PUBLIC_PATH."html/top.php";
        //include top_im
        include PUBLIC_PATH."html/top_im.php";?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">District Performance Report</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php 
                                //sub_dist_form
                                include('sub_dist_form.php'); ?>
                            </div>
                        </div>

                    </div>
                </div>
                <?php
                
                if(empty($fromDate) || empty($toDate))
                {
                    $where ="  ";
                    $where2 ="  ";
                    
                    //reporting period
                    $reportingPeriod = " Until " . date('M-Y', strtotime('today'));
                }
                    else if( $fromDate != $toDate )
                {
                    $where =" AND RptDate BETWEEN '$fromDate-01' AND '$toDate-01' ";
                    $where2 =" AND tbl_hf_data.reporting_date BETWEEN '$fromDate-01' AND '$toDate-01' ";
                    
                    //reportint period
                    $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
                }
                else
                {
                    $where =" AND RptDate BETWEEN '$fromDate-01' AND '$toDate-01' ";
                    $where2 =" AND tbl_hf_data.reporting_date BETWEEN '$fromDate-01' AND '$toDate-01' ";
                    //reportint period
                    $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
                }
                
                //check if submitted
                if (isset($_POST['submit']))
				{
                    //select query
                    // get all counts of district performance
                     $qry = "
                                SELECT
                                    Sum(tbl_wh_data.wh_issue_up) AS item_count,

                                    tbl_wh_data.report_month,
                                    tbl_wh_data.report_year,
                                    tbl_wh_data.item_id,
                                    tbl_wh_data.wh_id,
                                    tbl_wh_data.RptDate,
                                    tbl_warehouse.wh_name,
                                    tbl_warehouse.dist_id,
                                    tbl_warehouse.stkid,
                                    stakeholder.stkname
                                    FROM
                                    tbl_wh_data
                                    INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                                    INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                                    INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
                                    WHERE
                                    stakeholder.stkid = $stakeholder
                                    AND tbl_warehouse.dist_id = $districtId
                                    AND tbl_warehouse.stkofficeid = 18
                                     AND itminfo_tab.itm_category = 1
                                    $where
                                    GROUP BY


                                    tbl_wh_data.report_year,
                                    tbl_wh_data.report_month,
                                    tbl_wh_data.item_id,
                                    tbl_warehouse.dist_id
                                    
                                    
                                    

                                    UNION



                                    SELECT
                                        SUM(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS item_count,
                                        TRIM(LEADING '0' FROM SUBSTRING_INDEX(	SUBSTRING_INDEX(tbl_hf_data.reporting_date,'-',2),'-',-1))  as report_month,
                                        SUBSTRING_INDEX(tbl_hf_data.reporting_date,'-',1) as report_year,
                                                itminfo_tab.itmrec_id as item_id,
                                                tbl_warehouse.wh_id,
                                                tbl_hf_data.reporting_date as RptDate,
                                                tbl_warehouse.wh_name,
                                                tbl_warehouse.dist_id,
                                                tbl_warehouse.stkid,
                                                '' as stkname
                                        FROM
                                                tbl_warehouse
                                                INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                                INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                                INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                                        WHERE
                                                
                                                tbl_warehouse.dist_id = $districtId
                                                AND tbl_warehouse.stkid = $stakeholder
                                                $where2
                                                AND itminfo_tab.itm_category = 2
                                        GROUP BY
                                        
                                    report_year,
                                    report_month,
                                    itminfo_tab.itmrec_id,
                                    tbl_warehouse.dist_id
                                                
                                                   
                                        

							";
                    //query result
                        $qryRes = mysql_query($qry);
					//check if result exists
                    if (mysql_num_rows($qryRes) > 0) {
                        ?>
                            <?php 
                            //include sub_dist_reports
                            include('sub_dist_reports.php'); ?>
                            <div class="col-md-12" style="overflow:auto;">
                                <?php
                               
                                    $disp_arr=$temp = array();
                                    //fetch results
                                   
                                    while ($row = mysql_fetch_assoc($qryRes)) 
                                    {
                                        //print_r($row);
                                        
                                        $disp_arr[$row['report_year']][$row['report_month']][$row['item_id']]=$row['item_count'];
                                        $temp[$row['report_year'].'-'.$row['report_month']]=1;
                                    }
                                    // echo '<pre>';
                                    //print_r($_SESSION);
                                    $rows=count($temp);
                                    
                                     
								
                                ?>
                                <table id="myTable" width="100%" border="1">
                                    <tr>
                                        <td align="center" colspan="22">
                                            <h4 class="center">
                                            <?php
                                            
                                            echo "Monthwise Performance Report - $reportingPeriod<br>";
                                            
                                            
                                            ?>
                                            <?php echo 'Inrespect of  District ' . $distrctName.' , Population Welfare Department '.$prov_name; ?>
                                            </h4>
                                        </td>
                                       
                                    </tr>
                                    

<tr>
                                        <td   style="text-align:center"rowspan="2" >S.No.</td>
                                        
                                        <td  align="center" rowspan="2" >Month</td>
                                        <td    style="text-align:center"rowspan="" >Condoms (Pcs)</td>
                                        <td colspan="4" rowspan=""   style="text-align:center">Oral Pills (Cycles)</td>
                                        <td colspan="3" rowspan=""   style="text-align:center">IUD (IUD)</td>
                                        <td colspan="3" rowspan=""   style="text-align:center">Injectables (Vials)</td>
                                        <td colspan="3" rowspan=""   style="text-align:center">Implant (Pcs)</td>
                                        <td colspan="3" rowspan=""   style="text-align:center">Contraceptive Surgery (Cases)</td>
                                        <td   style="text-align:center" rowspan="2" >CYP</td>
                                        <td    style="text-align:center" rowspan="2" >Users</td>
                                    </tr>
                                    
                                    


<tr>
                                        <td   style="text-align:center">Condom</td>
                                        <td   style="text-align:center">POP</td>
                                        <td   style="text-align:center">COC</td>
                                        <td   style="text-align:center">ECP</td>
                                        <td   style="text-align:center">Total</td>
                                        <td   style="text-align:center">Copper-T-380A</td>
                                        <td   style="text-align:center">Multiload</td>
                                        <td   style="text-align:center">Total</td>
                                        <td   style="text-align:center">2-Month Inj</td>
                                        <td   style="text-align:center">3-Month Inj</td>
                                        <td   style="text-align:center">Total</td>
                                        <td   style="text-align:center">Implanon</td>
                                        <td   style="text-align:center">Jadelle</td>
                                        <td   style="text-align:center">Total</td>
                                        <td   style="text-align:center">Male</td>
                                        <td   style="text-align:center">Female</td>
                                        <td   style="text-align:center">Total</td>
                                    </tr>
                                    <tr>
                                        <td   style="text-align:center">1</td>
                                        
                                        <td   style="text-align:center">2</td>
                                        <td   style="text-align:center">3</td>
                                        <td   style="text-align:center">4</td>
                                        <td   style="text-align:center">5</td>
                                        <td   style="text-align:center">6</td>
                                        <td   style="text-align:center">7</td>
                                        <td   style="text-align:center">8</td>
                                        <td   style="text-align:center">9</td>
                                        <td   style="text-align:center">10</td>
                                        <td   style="text-align:center">11</td>
                                        <td   style="text-align:center">12</td>
                                        <td   style="text-align:center">13</td>
                                        <td   style="text-align:center">14</td>
                                        <td   style="text-align:center">15</td>
                                        <td   style="text-align:center">16</td>
                                        <td   style="text-align:center">17</td>
                                        <td   style="text-align:center">18</td>
                                        <td   style="text-align:center">19</td>
                                        <td   style="text-align:center">20</td>
                                        <td   style="text-align:center">21</td>
                                       
                                    </tr>    

                                        
                                    <?php
                                    $products_arr = array();
                                    $products_arr[0]='IT-001';
                                    $products_arr[1]='IT-002';
                                    $products_arr[2]='IT-009';
                                    $products_arr[3]='IT-003';
                                    $products_arr[4]='IT-005';
                                    $products_arr[5]='IT-004';
                                    $products_arr[6]='IT-006';
                                    $products_arr[7]='IT-007';
                                    $products_arr[8]='IT-008';
                                    $products_arr[9]='IT-013';
                                    $products_arr[10]='IT-031';
                                    $products_arr[11]='IT-032';
                                    
                                    $count=0;
                                                                           
                                    $total_1 = 0;
                                    $total_2 = 0;
                                    $total_9 = 0;
                                    $total_3 = 0;
                                    $total_5 = 0;
                                    $total_4 = 0;
                                    $total_6 = 0;
                                    $total_7 = 0;
                                    $total_8 = 0;
                                    $total_13 = 0;
                                    $total_31 = 0;
                                    $total_32 = 0;
                                    
                                   
                                    $prod_totals = $master_total = array();
                                    $master_total['CYP']=0;
                                    $master_total['USER']=0;
                                    foreach($disp_arr as $year => $m_data)
                                    {
                                       
                                        $month_totals  = array();
                                        foreach ($m_data as $mon => $item_data)
                                        {
                                            $fdate=date_create($year.'-'.$mon.'-'.'01');
                                            $month= date_format($fdate,"M-Y");
                                            $count++;
                                            
                                            $it_1=(!empty($item_data['IT-001'])?$item_data['IT-001']:'0');
                                            $it_2=(!empty($item_data['IT-002'])?$item_data['IT-002']:'0');
                                            $it_9=(!empty($item_data['IT-009'])?$item_data['IT-009']:'0');
                                            $it_3=(!empty($item_data['IT-003'])?$item_data['IT-003']:'0');

                                            $it_5=(!empty($item_data['IT-005'])?$item_data['IT-005']:'0');
                                            $it_4=(!empty($item_data['IT-004'])?$item_data['IT-004']:'0');

                                            $it_6=(!empty($item_data['IT-006'])?$item_data['IT-006']:'0');
                                            $it_7=(!empty($item_data['IT-007'])?$item_data['IT-007']:'0');

                                            $it_8=(!empty($item_data['IT-008'])?$item_data['IT-008']:'0');
                                            $it_13=(!empty($item_data['IT-013'])?$item_data['IT-013']:'0');

                                            $it_31=(!empty($item_data['IT-031'])?$item_data['IT-031']:'0');
                                            $it_32=(!empty($item_data['IT-032'])?$item_data['IT-032']:'0');

                                            $total_1+=$it_1;
                                            $total_2+=$it_2;
                                            $total_9+=$it_9;
                                            $total_3+=$it_3;
                                            $total_5+=$it_5;
                                            $total_4+=$it_4;
                                            $total_6+=$it_6;
                                            $total_7+=$it_7;
                                            $total_8+=$it_8;
                                            $total_13+=$it_13;
                                            $total_31+=$it_31;
                                            $total_32+=$it_32;
                                            
                                            
                                            foreach($products_arr as $k => $p_id)
                                            {
                                                $month_totals['CYP'][$p_id]     = $item_arr[$p_id]['extra']         * (!empty($item_data[$p_id])?$item_data[$p_id]:'0');
                                                $month_totals['USER'][$p_id]    = $item_arr[$p_id]['user_factor']   * (!empty($item_data[$p_id])?$item_data[$p_id]:'0');
                                                
                                                $prod_totals[$p_id]['CYP'][]      = $item_arr[$p_id]['extra']         * (!empty($item_data[$p_id])?$item_data[$p_id]:'0');
                                                $prod_totals[$p_id]['USER'][]     = $item_arr[$p_id]['user_factor']   * (!empty($item_data[$p_id])?$item_data[$p_id]:'0');
                                                
                                            }
                                            
                                            //echo '<pre>';
                                            //echo array_sum($month_totals['USER']);
                                            //print_r($month_totals);
                                            //print_r($item_arr);
                                            
                                           // exit;

                                            
                                            echo '<tr>
                                                        <td style="text-align:center">'.$count.'</td>';
                                                //if($count==1) echo ' <td rowspan="'.$rows.'"  style="text-align:center" >'.$distrctName.'</td>';
                                                

                                                        echo ' <td  style="text-align:left">'.$month.'</td>
                                                                <td style="text-align:right">'.number_format($it_1).'</td>
                                                                <td  style="text-align:right">'.number_format($it_2).'</td>
                                                                <td  style="text-align:right">'.number_format($it_9).'</td>
                                                                <td  style="text-align:right">'.number_format($it_3).'</td>
                                                                <td  style="text-align:right">'.number_format(($it_2+$it_9+$it_3)).'</td>
                                                                <td  style="text-align:right">'.number_format($it_5).'</td>
                                                                <td  style="text-align:right">'.number_format($it_4).'</td>
                                                                <td  style="text-align:right">'.number_format(($it_5+$it_4)).'</td>
                                                                <td  style="text-align:right">'.number_format($it_6).'</td>
                                                                <td  style="text-align:right">'.number_format($it_7).'</td>
                                                                <td  style="text-align:right">'.number_format(($it_6+$it_7)).'</td>
                                                                <td  style="text-align:right">'.number_format($it_8).'</td>
                                                                <td  style="text-align:right">'.number_format($it_13).'</td>
                                                                <td  style="text-align:right">'.number_format(($it_8+$it_13)).'</td>
                                                                <td  style="text-align:right">'.number_format($it_31).'</td>
                                                                <td  style="text-align:right">'.number_format($it_32).'</td>
                                                                <td  style="text-align:right">'.number_format(($it_31+$it_32)).'</td>
                                                                <td  style="text-align:right">'.number_format(array_sum($month_totals['CYP'])).'</td>
                                                                <td  style="text-align:right">'.number_format(array_sum($month_totals['USER'])).'</td>
                                                    </tr> ';
                                                        
                                                    $master_total['CYP']+=array_sum($month_totals['CYP']);
                                                    $master_total['USER']+=array_sum($month_totals['USER']);  
                                                        
                                        }
                                    }
                                    
                                    
                                    echo '  <td  style="text-align:center"  colspan="2">Total</td>
                                                <td  style="text-align:right">'.number_format($total_1).'</td>
                                                <td  style="text-align:right">'.number_format($total_2).'</td>
                                                <td  style="text-align:right">'.number_format($total_9).'</td>
                                                <td  style="text-align:right">'.number_format($total_3).'</td>
                                                <td  style="text-align:right">'.number_format(($total_2+$total_9+$total_3)).'</td>
                                                <td  style="text-align:right">'.number_format($total_5).'</td>
                                                <td  style="text-align:right">'.number_format($total_4).'</td>
                                                <td  style="text-align:right">'.number_format(($total_5+$total_4)).'</td>
                                                <td  style="text-align:right">'.number_format($total_6).'</td>
                                                <td  style="text-align:right">'.number_format($total_7).'</td>
                                                <td  style="text-align:right">'.number_format(($total_6+$total_7)).'</td>
                                                <td  style="text-align:right">'.number_format($total_8).'</td>
                                                <td  style="text-align:right">'.number_format($total_13).'</td>
                                                <td  style="text-align:right">'.number_format(($total_8+$total_13)).'</td>
                                                <td  style="text-align:right">'.number_format($total_31).'</td>
                                                <td  style="text-align:right">'.number_format($total_32).'</td>
                                                <td  style="text-align:right">'.number_format(($total_31+$total_32)).'</td>
                                                <td  style="text-align:right">'.number_format( $master_total['CYP']).'</td>
                                                <td  style="text-align:right">'.number_format( $master_total['USER']).'</td>
                                      </tr> ';
                                    
                                      echo '  <td  style="text-align:center"  colspan="2">CYP</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-001']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-002']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-009']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-003']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format((array_sum($prod_totals['IT-002']['CYP'])+array_sum($prod_totals['IT-009']['CYP'])+array_sum($prod_totals['IT-003']['CYP']))).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-005']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-004']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format((array_sum($prod_totals['IT-005']['CYP'])+array_sum($prod_totals['IT-004']['CYP']))).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-006']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-007']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format((array_sum($prod_totals['IT-006']['CYP'])+array_sum($prod_totals['IT-007']['CYP']))).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-008']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-013']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format((array_sum($prod_totals['IT-008']['CYP'])+array_sum($prod_totals['IT-013']['CYP']))).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-031']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-032']['CYP'])).'</td>
                                                <td  style="text-align:right">'.number_format((array_sum($prod_totals['IT-031']['CYP'])+array_sum($prod_totals['IT-032']['CYP']))).'</td>
                                                <td  style="text-align:right"></td>
                                                <td  style="text-align:right"></td>
                                      </tr> ';
                                      
                                      echo '  <td style="text-align:center" colspan="2">USERS</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-001']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-002']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-009']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-003']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format((array_sum($prod_totals['IT-002']['USER'])+array_sum($prod_totals['IT-009']['USER'])+array_sum($prod_totals['IT-003']['USER']))).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-005']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-004']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format((array_sum($prod_totals['IT-005']['USER'])+array_sum($prod_totals['IT-004']['USER']))).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-006']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-007']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format((array_sum($prod_totals['IT-006']['USER'])+array_sum($prod_totals['IT-007']['USER']))).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-008']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-013']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format((array_sum($prod_totals['IT-008']['USER'])+array_sum($prod_totals['IT-013']['USER']))).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-031']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format(array_sum($prod_totals['IT-032']['USER'])).'</td>
                                                <td  style="text-align:right">'.number_format((array_sum($prod_totals['IT-031']['USER'])+array_sum($prod_totals['IT-032']['USER']))).'</td>
                                                <td  style="text-align:right"></td>
                                                <td  style="text-align:right"></td>
                                      </tr> ';
                                    
                                    
                                    ?>
                                    
                                    
                                </table>
                            </div>
                        </div>
                        <?php
                    } else {
                        echo "No record found";
                    }
                }
                // Unset varibles
                unset( $data, $total);
                ?>
            </div>
        </div>
    </div>
	<?php 
        //include footer
        include PUBLIC_PATH."/html/footer.php";
        //include combos
     include ('combos.php'); ?>
</body>
</html>