<?php

/**
 * rcp2
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH."html/header.php");
//report id
$rptId = 'rcp2';
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
    $stakeholder = mysql_real_escape_string($_POST['stakeholder']);

    $fd = explode('-',$fromDate);
    $td = explode('-',$toDate);
    
   
    $fdate=date_create($fromDate);
    $f_date_string= date_format($fdate,"M-Y");
    $tdate=date_create($toDate);
    $t_date_string= date_format($tdate,"M-Y");
    
    if(!empty($fromDate) && empty($fd[2])) $fromDate.='-01';
    if(!empty($toDate) &&empty($td[2])) $toDate.='-01';
    
   
    //file name
    $fileName = 'Ref_cases_for_' . $fromDate . '-' . $toDate;
    
    //get the hf types array
     $qry = "SELECT
                pk_id,hf_type
            FROM
                tbl_hf_type
            where stakeholder_id = $stakeholder
           ";
    //query result
     $hf_type_arr=array();
     $rs_hf= mysql_query($qry);
    while($row = mysql_fetch_array($rs_hf))
    {
        $hf_type_arr[$row['pk_id']] = $row['hf_type'];
    }
    
        
   
}
?>
    
    <style>
                .txt { text-align:center !important; }
                .nmbr { text-align:right !important; }
    </style>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content" >
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
                        <h3 class="page-title row-br-b-wp">Facility Type Wise Referral Cases</h3>
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
                $where='';
                $label=' - Until '.date('M-Y');
                
                if(!empty($fromDate) && !empty($toDate))
                {
                    $where.=" AND tbl_hf_data.reporting_date BETWEEN '".$fromDate."' AND '".$toDate."' ";
                    $label=' - From '.$f_date_string.' To '.$t_date_string;
                }    
                
                //check if submitted
                if (isset($_POST['submit']))
		{
                    //select query
                 $qry = "SELECT
                                    tbl_hf_data_reffered_by.hf_type_id,
                                    sum(tbl_hf_data_reffered_by.ref_surgeries) as surg,
                                    tbl_warehouse.dist_id,
                                    tbl_warehouse.prov_id,
                                    tbl_locations.LocName
                                    FROM
                                    tbl_hf_data
                                    INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                                    INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data_reffered_by.hf_data_id = tbl_hf_data.pk_id
                                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                    WHERE
                                    tbl_warehouse.prov_id = $selProv
                                    AND tbl_warehouse.stkid = $stakeholder
                                    ".$where."    
                                    and tbl_hf_data.item_id in ('31','32')
                                    
                                    GROUP BY 

                                    tbl_warehouse.prov_id,
                                    tbl_warehouse.dist_id,
                                    tbl_hf_data_reffered_by.hf_type_id
                                    
                                    ORDER BY 
                                    tbl_locations.LocName,
                                    tbl_warehouse.dist_id,
                                    tbl_hf_data_reffered_by.hf_type_id
                                    
";
                 //echo $qry;exit;
                    //query result
                        $qryRes = mysql_query($qry);
			//check if result exists
                        //echo '<pre>';
                        $disp_arr=$ftype_arr  = array();
                        
                        // for PWD , hardcoding some fac types , so that these necessary facilities are displayed
                        if($stakeholder == '1')
                        {
                            $ftype_arr['1']=0;
                            $ftype_arr['2']=0;
                            $ftype_arr['4']=0;
                            $ftype_arr['5']=0;
                            $ftype_arr['13']=0;
                            $ftype_arr['16']=0;
                        }
                        
                        $num = mysql_num_rows($qryRes);
                        while($row=mysql_fetch_assoc($qryRes))
                        {
                           
                           //print_r($row);
                           
                            $disp_arr[$row['dist_id']][$row['hf_type_id']]=$row['surg'];
                            
                            isset($ftype_arr[$row['hf_type_id']]) ? $ftype_arr[$row['hf_type_id']]+=$row['surg'] :  $ftype_arr[$row['hf_type_id']]=$row['surg'] ;
                            isset($disp_arr[$row['dist_id']]['total']) ? $disp_arr[$row['dist_id']]['total']+=$row['surg'] :  $disp_arr[$row['dist_id']]['total']=$row['surg'] ;
                            $disp_arr[$row['dist_id']]['dist_name'] = $row['LocName'];
                            
                            
                        }
                       // echo '<pre>';
                        $col_count = count($ftype_arr);
                        ksort($ftype_arr);
                       // print_r($hf_type_arr);
                        //print_r($ftype_arr);
                        //print_r($disp_arr);
                        
                        
                        
                 $tbl_data = "";
                 
                  $tbl_data .= "<tr>";
                  $tbl_data .=  '<td class="txt" colspan="'.($col_count+3).'"><h4 class="center">District Wise Referral For CS (TL and NSV) Cases</h4></td>';
                  $tbl_data .= "</tr>";
                  
                  $tbl_data .= "<tr>";
                  $tbl_data .=  '<td class="txt" colspan="'.($col_count+3).'"><h4 class="center">For '.$prov_name.' '.$label.'</h4></td>';
                  $tbl_data .= "</tr>";
                    
                 
                    $tbl_data .= '<tr style="text-align:center;background-color:#EEEEEE" >';
                    //
                    $tbl_data .=  '<th class="txt">Sr No</th>';
                    $tbl_data .=  '<th class="txt">District</th>';
                    foreach($ftype_arr as $k => $v)
                    {
                        $tbl_data .=  '<th class="txt">'.(!empty($hf_type_arr[$k])?$hf_type_arr[$k]:'-').'</th>';
                    }
                    $tbl_data .=  '<th class="txt">Total</th>';
                    $tbl_data .= "</tr>";
                    $count=1;
                        foreach($disp_arr as $dist_k  => $dist_data)
                        {
                            
                            $tbl_data .= "<tr>";
                            //
                            $tbl_data .=  '<td class="txt">'.$count++.'</td>';
                            $tbl_data .=  '<td class="">'.$dist_data['dist_name'].'</td>';
                            
                            foreach($ftype_arr as $k => $v)
                            {
                                $tbl_data .=  '<td class="nmbr">'.(!empty($dist_data[$k])?number_format($dist_data[$k]):'0').'</td>';
                            }
                            $tbl_data .=  '<td class="nmbr">'.(!empty($dist_data['total'])?number_format($dist_data['total']):'0').'</td>';

                            $tbl_data .= "</tr>";

                           
                              
                        }
                        
                    $tbl_data .= '<tr style="text-align:center;background-color:#EEEEEE" >';
                    $tbl_data .=  '<th colspan="2">Totals</th>';
                    $grand_total=0;
                    foreach($ftype_arr as $k => $v)
                    {
                        $tbl_data .=  '<th class="nmbr">'.(!empty($v)?number_format($v):'0').'</th>';
                        $grand_total += $v;
                    }
                    $tbl_data .=  '<th class="nmbr">'.(!empty($grand_total)?number_format($grand_total):'0').'</th>';
                    $tbl_data .= "</tr>";
                    
                    
                }
               //print_r($xmlstore);
               //exit;
                
                
                include('sub_dist_reports.php'); 
                ?>
                <div class="col-md-12"  style="overflow:auto;">    
                    <table id="myTable" width="100%" border="1">
                       <?php
                       if(!empty($tbl_data)) echo $tbl_data;
                       ?>
                    </table>
                </div>
     
                
            </div>
        </div>
    </div>
	<?php 
        //include footer
        include PUBLIC_PATH."/html/footer.php";
        //include PUBLIC_PATH . "/html/reports_includes.php";
        //include combos
     include ('combos.php'); ?>

    
        
</body>
</html>