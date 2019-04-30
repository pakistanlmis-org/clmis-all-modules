<?php

/**
 * rcp1
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
$rptId = 'rcp1';
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

    $fd = explode('-',$fromDate);
    $td = explode('-',$toDate);
    
   
    $fdate=date_create($fromDate);
    $f_date_string= date_format($fdate,"M-Y");
    $tdate=date_create($toDate);
    $t_date_string= date_format($tdate,"M-Y");
    
    if(!empty($fromDate) && empty($fd[2])) $fromDate.='-01';
    if(!empty($toDate) &&empty($td[2])) $toDate.='-01';
    
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
    //file name
    //$fileName = 'SPR8_' . $distrctName . '_for_' . $fromDate . '-' . $toDate;
    
    //get the hf types array
     $qry = "SELECT
                pk_id,hf_type
            FROM
                tbl_hf_type
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
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content"  onLoad="doInitGrid()">
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
                        <h3 class="page-title row-br-b-wp">District Wise Summary of Referral Cases Performed</h3>
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
                                    tbl_hf_data.item_id,
                                    tbl_warehouse.dist_id,
                                    tbl_warehouse.prov_id
                                    FROM
                                    tbl_hf_data
                                    INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                                    INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data_reffered_by.hf_data_id = tbl_hf_data.pk_id
                                    WHERE
                                    tbl_warehouse.dist_id = $districtId
                                    ".$where."    
                                    and tbl_hf_data.item_id in ('31','32')
                                    GROUP BY 

                                    tbl_warehouse.prov_id,
                                    tbl_warehouse.dist_id,
                                    tbl_hf_data_reffered_by.hf_type_id,
                                    tbl_hf_data.item_id
";
                    //query result
                        $qryRes = mysql_query($qry);
			//check if result exists
                        // echo '<pre>';
                         $disp_arr=array();
                        $num = mysql_num_rows($qryRes);
                        while($row=mysql_fetch_array($qryRes))
                        {
                           
                           //print_r($row);
                           
                            $disp_arr[$row['prov_id']][$row['dist_id']][$row['hf_type_id']][$row['item_id']]['surg']=$row['surg'];
                            
                            
                        }
                       // echo '<pre>';
                        //print_r($disp_arr);
                        ?>
                
                
                
                <?php
                 $xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
                 $xmlstore .= "<rows>";
                 $male_total=0;
                 $female_total=0;
                        foreach($disp_arr as $prov_k  => $prov_d)
                        {
                            foreach($prov_d as $dist_k => $dist_d)
                            {
                                foreach($dist_d as $wh_k=>$wh_d)
                                {
                                    $xmlstore .= "<row>";
                                    //
                                    $xmlstore .=  '<cell><![CDATA['.$hf_type_arr[$wh_k].']]></cell>';
                                    $xmlstore .=  '<cell>'.$wh_d['31']['surg'].'</cell>';
                                    $xmlstore .=  '<cell>'.$wh_d['32']['surg'].'</cell>';
                                    $xmlstore .=  '<cell>'.((int)$wh_d['31']['surg']+(int)$wh_d['32']['surg']).'</cell>';

                                    $xmlstore .= "</row>";
                                    
                                    $male_total     += $wh_d['31']['surg'];
                                    $female_total   += $wh_d['32']['surg'];
                                    
                                }
                            }
                        }
                        
                    $xmlstore .= "<row>";
                    $xmlstore .=  '<cell>Totals</cell>';
                    $xmlstore .=  '<cell>'.$male_total.'</cell>';
                    $xmlstore .=  '<cell>'.$female_total.'</cell>';
                    $xmlstore .=  '<cell>'.((int)$male_total+(int)$female_total).'</cell>';
                    $xmlstore .= "</row>";
                    
                    $xmlstore .= "</rows>";
                    
                }
               //print_r($xmlstore);
               //exit;
                ?>
   <?php

if (isset($_REQUEST['district'])) {
    if ($num > 0) {
        ?>             
                
                <table width="100%" cellpadding="0" cellspacing="0" id="myTable">
                    <tr>
                        <td align="right" style="padding-right:5px;"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/> <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></td>
                    </tr>
                    <tr>
                        <td><div id="mygrid_container" style="width:100%; height:390px;"></div></td>
                    </tr>
                </table>  
  <?php
    } else {
        echo '<h6>No record found.</h6>';
    }
}
?>        
                
            </div>
        </div>
    </div>
	<?php 
        //include footer
        include PUBLIC_PATH."/html/footer.php";
        include PUBLIC_PATH . "/html/reports_includes.php";
        //include combos
     include ('combos.php'); ?>
    
     <script>
        var dist_name = "<?php echo $distrctName; ?>";
        var prov_name = $("#prov_sel option:selected").text();
        //alert('dist:'+dist_name+'prov:'+prov_name);
        var loc_label ='Province:'+prov_name+' District:'+dist_name+' - ';
         
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold; font-family:Helvetica'>"+loc_label+"<?php echo "Referral Cases Performed". $label; ?></div>,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Referred By, <div style='width:100%; text-align:center;'>CS Male</div> ,<div style='width:100%; text-align:center;'>CS Female</div> , <div style='width:100%; text-align:center;'>Total CS Cases</div> ");
            mygrid.setColAlign("left,right,right,right");
            mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,#cspan,#cspan,#cspan");
            
           // mygrid.setInitWidths("150,120,120,120");
            mygrid.enableRowsHover(true, 'onMouseOver');
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script> 
</body>
</html>