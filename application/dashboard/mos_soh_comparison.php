<?php
//print_r($_REQUEST);
//Including Configuration file
include("../includes/classes/Configuration.inc.php");
//Login
Login();
//Checking user_role
//if ($_SESSION['user_level'] != '1') {
//    //Redirecting to shipment
//    header("Location: shipment.php");
//    exit;
//}

//Including db file
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
//Including FusionCharts file
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
//echo '<pre>';print_r($_SESSION);exit;

$todays_date =  date('d');
//if($todays_date >=10) $open_month =  date('m')-1;
//else $open_month =  date('m')-2;

//current month is opened as per requirement
$open_month =  date('m')-1;
$open_year =  date('Y');

$a_date = date('Y').'-'.($open_month+1).'-1';
$open_month_d= date("t", strtotime($a_date));

$min_delivery_time = 6;             //no of months it takes to deliver
$min_stock_months = 12 ;            //minimum mos to be maintained
$sufficiency_upper_level = 18; 
                
//Getting user_level
$level = $_SESSION['user_level'];
//Getting user_province1
$province = $_SESSION['user_province1'];
//Getting user_district
$district = (!empty($_REQUEST['dist_id']))?$_REQUEST['dist_id']:'';
$stakeholder    = $_SESSION['user_stakeholder1'];
@$stk_name       = $_SESSION['stk_name'];

$selProv = (!empty($_REQUEST['province']))?$_REQUEST['province']:'1';
if(!empty($_SESSION['user_level']) && $_SESSION['user_level'] >= 2 &&!empty($_SESSION['user_province1']) && $_SESSION['user_province1'] !=10)
       $selProv =  $province;                                                                   

$show_all_products = true;

$fromDate = (!empty($_REQUEST['from_date']))?$_REQUEST['from_date']:date("Y-m-d");
 
$itm_arr_request = (!empty($_REQUEST['product']))?$_REQUEST['product']:'';
$full_supply_prods=array();
$full_supply_prods[1] = '1';
$full_supply_prods[5] = '5';
$full_supply_prods[7] = '7';
$full_supply_prods[9] = '9';
//$toDate = $fromDate;
//echo '<pre>';print_r($itm_arr_request);exit;
 
$d_exp = explode('-',$fromDate);
$from_y_m = $d_exp[0].'-'.$d_exp[1];

$selected_month = date('m',strtotime($fromDate));
$selected_year = date('Y',strtotime($fromDate));

$funding_source     = (!empty($_REQUEST['stakeholder']))?$_REQUEST['stakeholder']:'1';
if( ($selected_month == date('m') && $selected_year == date('Y'))  || $fromDate > date("Y-m-d"))
{
   $hide_graphs = TRUE;
}
else
{
   $hide_graphs = FALSE;
}


$wh_comm='';
if(!empty($stakeholder)) $wh_comm  .= " and stakeholder_id  = '".$stakeholder."' ";
if(!empty($selProv)) $wh_comm  .= " and location_id     = '".$selProv."' ";

 $qry_comments= "SELECT
            dashboard_comments.pk_id,
            dashboard_comments.dashlet_id,
            dashboard_comments.dashboard_id,
            dashboard_comments.stakeholder_id,
            dashboard_comments.location_id,
            dashboard_comments.comments
            FROM
            dashboard_comments
            WHERE
            dashboard_comments.dashboard_id = 111
             AND dashboard_comments.month_year like  '".$from_y_m."%'
            $wh_comm
            ";
 //echo $qry_comments;exit;
$res = mysql_query($qry_comments);
$comments_arr =array();
while($row=mysql_fetch_assoc($res))
{
    $comments_arr[$row['dashlet_id']][]=$row['comments'];
}
?>
    <style>
        .my_dash_cols{
            padding-left: 1px;
            padding-right: 0px;
            padding-top: 1px;
            padding-bottom: 0px;
        }
        .my_dashlets{
            /*padding-left: 1px;
            padding-right: 0px;
            padding-top: 1px;
            padding-bottom: 0px;*/
        }
        
    </style>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
		//Including top file
        include PUBLIC_PATH . "html/top.php";
		//Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                
                <div class="container-fluid">
                    
                        <div class="row">
                            <div class="widget" data-toggle="">
                                <div class="widget-head">
                                    <h3 class="heading">Filter by</h3>
                                </div>
                                <div class="widget-body collapse in">
                                    <form name="frm" id="frm" action="" method="POST">
                                        <table width="100%">
                                            <tbody>
                                            <tr>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Reporting Month</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php echo $fromDate; ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Province</label>
                                                                <select name="province" id="province"  onchange="showDistricts()" required="required" class="form-control input-sm">
                                                                        <?php
                                                                            $pro_where = '';
                                                                            if(!empty($_SESSION['user_level']) && $_SESSION['user_level'] >= 2 &&!empty($_SESSION['user_province1']) && $_SESSION['user_province1'] !=10)
                                                                                $pro_where= " AND tbl_locations.PkLocID = ".$_SESSION['user_province1']."  ";
                                                                                
                                                                            $queryprov = "SELECT
                                                                                                tbl_locations.PkLocID AS prov_id,
                                                                                                tbl_locations.LocName AS prov_title
                                                                                            FROM
                                                                                                tbl_locations
                                                                                            WHERE
                                                                                                LocLvl = 2
                                                                                                AND parentid IS NOT NULL
                                                                                                 
                                                                                                 $pro_where";
                                                                            //query result
                                                                            $rsprov = mysql_query($queryprov) or die();
                                                                            $prov_name='';
                                                                            while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                                if ($selProv == $rowprov['prov_id']) {
                                                                                    $sel = "selected='selected'";
                                                                                    $prov_name=$rowprov['prov_title'];
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
                                                
                                                <td class="col-md-2 filter1" id="td_dist" style=""><label class="sb1NormalFont">District</label>
                                                    <select name="dist_id" id="dist_id" class="form-control input-sm">
                                                        <option value="">All</option>
                                                            <?php
                                                            $queryDist = "SELECT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 3
                                                                        AND tbl_locations.parentid = '" . $selProv . "'
                                                                        ORDER BY
                                                                                tbl_locations.LocName ASC";
                                                            //query result
                                                            $rsDist = mysql_query($queryDist) or die();
                                                            //fetch result
                                                            $dist_name = "Attock";
                                                            while ($rowDist = mysql_fetch_array($rsDist)) {
                                                                if ($district == $rowDist['PkLocID'] ) {
                                                                    $sel = "selected='selected'";
                                                                    $dist_name=$rowDist['LocName'];
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                
                                                                //if($_SESSION['user_level'] == 3 && isset($_SESSION['user_district']) && $_SESSION['user_district'] == $rowDist['PkLocID'] ) 
                                                                    echo '<option value="'.$rowDist['PkLocID'].'" '.$sel.'>'.$rowDist['LocName'].'</option>';
                                                            }
                                                            ?>
                                                    </select>
                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Stakeholder</label>
                                                            <select name="stakeholder" id="stakeholder"  class="form-control input-sm" required="">
                                                                    <?php

                                                                         $querys = "SELECT
stakeholder.stkid,
stakeholder.stkname
FROM
stakeholder
WHERE
stakeholder.ParentID IS NULL AND
stakeholder.lvl = 1 AND
stakeholder.is_reporting = 1 AND
stakeholder.stk_type_id IN (0,1)
";
                                                                        //query result
                                                                        $rsprov = mysql_query($querys) or die();
                                                                        $stk_name='';
                                                                        while ($rowp = mysql_fetch_array($rsprov)) {
                                                                                if ($funding_source == $rowp['stkid']) {
                                                                                    $sel = "selected='selected'";
                                                                                    $stk_name=$rowp['stkname'];
                                                                                } else {
                                                                                    $sel = "";
                                                                                }
                                                                                //Populate prov_sel combo
                                                                                ?>
                                                                                <option value="<?php echo $rowp['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowp['stkname']; ?></option>
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
                                                             <select    name="product"  id="product" class=" form-control input-sm" required="">
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
                                                                        
                                                                        $sel='';
                                                                        $styleit = "display:none;";
                                                                        $cls2 = "";
                                                                        if($show_all_products){
                                                                            if (!empty($itm_arr_request) && $rowprov['itm_id'] == $itm_arr_request) {
                                                                                $sel = "selected='selected'";
                                                                                $prod_name=$rowprov['itm_name'];
                                                                                
                                                                            } 
                                                                            $styleit = "";
                                                                            if(in_array($rowprov['itm_id'], $full_supply_prods) ){
                                                                                $cls2 = "full_funded";
                                                                            }
                                                                        }
                                                                        else{
                                                                             if(in_array($rowprov['itm_id'], $full_supply_prods) ){
                                                                                 if(empty($itm_arr_request))
                                                                                 {
                                                                                    $sel = "selected='selected' ";
                                                                                    $cls2 = "full_funded";
                                                                                    $styleit = "";
                                                                                 }
                                                                                 elseif (!empty($itm_arr_request) && in_array($rowprov['itm_id'],$itm_arr_request)) {
                                                                                    $sel = "selected='selected' ";
                                                                                    $itm_name[]=$rowprov['itm_name'];
                                                                                    $cls2 = "full_funded";
                                                                                    $styleit = "";
                                                                                }
                                                                                else{
                                                                                    $sel = " ";
                                                                                    $cls2 = "full_funded";
                                                                                    $styleit = "";
                                                                                }
                                                                                
                                                                            }
                                                                            else{
                                                                                $sel = " ";
                                                                                $styleit = "display:none";
                                                                                $cls2 = "";
                                                                            }
                                                                        }
                                                                         
                                                                        
                                                                        
                                                                        ?>
                                                                            <option class="<?=$cls2?>" value="<?php echo $rowprov['itm_id']; ?>" <?php echo $sel; ?> style="<?=$styleit?>"><?php echo $rowprov['itm_name']; ?></option>
                                                                 <?php
                                                                        }

                                                                ?>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-md-2">
                                                    <input type="hidden" name="stk_name" id="stk_name" value="<?php echo $stk_name?>">
                                                    <input type="hidden" name="dist_name" id="dist_name" value="<?php echo $dist_name?>">
                                                    <input type="hidden" name="prov_name" id="prov_name" value="<?php echo $prov_name?>">
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
                    
                        <div id="filter_note" class="row " style="display:none;">
                            <div class="col-md-12">
                                <div class="note note-info">To view graphs , please click GO button after selecting filters.</div>
                            </div>
                        </div>
                    
                      <div class="row hide_divs">
                            <div class="col-md-12">
                                <h3 class="page-title row-br-b-wp center"> <?php echo $prov_name;?><?=(!empty($district)?' - '.$dist_name:'')?> <?=(!empty($stk_name)?' - '.$stk_name:'')?> - MOS / SOH Comparison of <?=(!empty($prod_name)?' - '.$prod_name:'')?> - <?php echo date('M-Y',strtotime($fromDate)); ?> 
                                </h3>

                            </div>
                        </div>
                        <div class="col-md-12 "  style=" ">
                            <div class="dashlet_graph_html" id="dashboard_ss_a1" href='mos_soh_comparison_graph.php'></div>
                        </div>
                    
                </div>
                
                
               
            </div>
        </div>
    </div>

    <?php 
    //Including footer file
    include PUBLIC_PATH . "/html/footer.php"; ?>

    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
    <?php /* ?><script language="Javascript" src="<?php echo PUBLIC_URL;?>js/maps/cyp_dashlet.js"></script>
      <script language="Javascript" src="<?php echo PUBLIC_URL;?>js/maps/dashlet_Interval.js"></script><?php */ ?>
    <script type="text/javascript">

                $(function() {
                        
                        //--------Full funded prods only
                        $("#prod_check").change(function(){
                            if(this.checked) {
                                $("#product option").show();
                                $('#product option').prop('selected', true);
                            }
                            else
                            {
                                
                                $('#product option').prop('selected', false);
                                $('#product option.full_funded').prop('selected', true);
                                
                                $("#product option").hide();
                                $("#product option.full_funded").show();
                            }
                        });
                        //--------
                    
                        $("#sel_all").click(function(){
                            $('#product option').prop('selected', true);
                        });
                    
			if(!$('#accordion').hasClass('page-sidebar-menu-closed'))
                        {
                            $(".sidebar-toggler").trigger("click");
                        }
                        $("#general_summary_main_div").hide();
			loadDashlets();

                        if(!$('#accordion').hasClass('page-sidebar-menu-closed'))
                        {
                            $(".sidebar-toggler").trigger("click");
                        }
                       
                       $("#province").change(function(){
                           $.ajax({
				type: "POST",
				url: '<?php echo APP_URL; ?>dashboard/ajax.php',
				data: { required_value : "stakeholders", prov_id: $(this).val()},
				success: function(data) {
					$("#stakeholder").html(data);
				}
                            });
                       });
                        
		});
                
		function loadDashlets(stkId='1')
		{
                    var trace_count = 0;
			$('.dashlet_graph_html').each(function(i, obj) {
				
				var url = $(this).attr('href');
				var id = $(this).attr('id');
				
                                var dataStr='';
                                dataStr += 'province=' + $('#province').val();
                                dataStr += '&prov_name=' + $('#prov_name').val();
                                dataStr += '&from_date=' + $('#from_date').val();
                                dataStr += '&to_date=' + $('#to_date').val();
                                dataStr += '&stakeholder=' + $('#stakeholder').val();
                                dataStr += '&stk_name=' + $('#stk_name').val();
                                dataStr += '&product=' + $('#product').val();
                                dataStr += '&district=' + $('#dist_id').val();
                                //dataStr += '&sector=0';

                                $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");
                                
                                var prods = $('#product').val()
                                console.log('Open:'+url+',Products:'+prods);
                                
                                $.ajax({
                                        type: "POST",
                                        url: '<?php echo APP_URL; ?>dashboard/' + url,
                                        data: dataStr,
                                        dataType: 'html',
                                        success: function(data) {
                                                
                                                $("#" + id).html(data);
                                                
                                        }
                                });
				
			});
                        $('.dashlet_graph').each(function(i, obj) {
				
				var url = $(this).attr('href');
				var id = $(this).attr('id');
				
                                var dataStr='';
                                dataStr += 'province=' + $('#province').val();
                                dataStr += '&prov_name=' + $('#prov_name').val();
                                dataStr += '&from_date=' + $('#from_date').val();
                                dataStr += '&to_date=' + $('#to_date').val();
                                dataStr += '&dist=' + $('#dist_id').val();
                                dataStr += '&dist_name=' + $('#dist_name').val();
                                dataStr += '&stakeholder=' + $('#stakeholder').val();
                                dataStr += '&stk_name=' + $('#stk_name').val();
                                dataStr += '&product=' + $('#product').val();

                                $('#' + id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");

                                $.ajax({
                                        type: "POST",
                                        url: '<?php echo APP_URL; ?>dashboard/' + url,
                                        data: dataStr,
                                        dataType: 'html',
                                        success: function(data) {
                                                $("#" + id).html(data);
                                        }
                                });
				
			});
                        
		}
               
               
		function showProvinces() {
			$.ajax({
				type: "POST",
				url: '<?php echo APP_URL; ?>dashboard/ajax.php',
				data: {lvl: 2},
				success: function(data) {
					$("#provinceArea").html(data);
					showDistricts();
				}
			});
		}

		function showDistricts() {
			$.ajax({
				type: "POST",
				url: '<?php echo APP_URL; ?>dashboard/ajax.php',
				data: {lvl: 3,all_option:'no', prov_id: $('#province').val()},
				success: function(data) {
					$("#td_dist").html(data);
				}
			});
		}
		function showProvinces1(provId) {
			$.ajax({
				type: "POST",
				url: '<?php echo APP_URL; ?>dashboard/ajax.php',
				data: {lvl: 2, provId: provId},
				success: function(data) {
					$("#provinceArea").html(data);
					showDistricts1('<?php echo $district; ?>');
				}
			});
		}

		function showDistricts1(distId) {
			$.ajax({
				type: "POST",
				url: '<?php echo APP_URL; ?>dashboard/ajax.php',
				data: {lvl: 3, prov_id: $('#prov_id').val(), distId: distId},
				success: function(data) {
					$("#districtArea").html(data);
					loadDashlets();
				}
			});
		}
		function showData(param) {
			$.ajax({
				type: "POST",
				url: '<?php echo APP_URL; ?>dashboard/ajax.php',
				data: {stockStatus: param},
				success: function(data) {
					$("#modalData").html(data);
				}
			});
			$('#modalId').trigger('click');
		}
		function loadGraph(stkId, type, tabId)
		{
			var dataStr;
			dataStr = 'month=' + $('#month').val();
			dataStr += '&year=' + $('#year').val();
			dataStr += '&lvl=' + $('#ofc_level').val();
			dataStr += '&proFilter=' + $('#product_filter').val();
			if ($('#ofc_level').val() == 2)
			{
				dataStr += '&prov_id=' + $('#prov_id').val();
			}
			if ($('#ofc_level').val() == 3)
			{
				dataStr += '&dist_id=' + $('#dist_id').val();
			}
			dataStr += '&sector=0';
			dataStr = dataStr + '&stkId=' + stkId + '&type=' + type;
			$.ajax({
				type: "POST",
				url: '<?php echo APP_URL; ?>dashboard/stock_status_ajax.php',
				data: dataStr,
				success: function(data) {
					$("#stock-" + tabId).html(data);
				}
			});
		}
                
                $(function() {
                    $('#from_date, #to_date').datepicker({
                        dateFormat: "yy-mm-dd",
                        constrainInput: false,
                        changeMonth: true,
                        changeYear: true,
                        
                        <?php
                        //if($_SERVER['REMOTE_ADDR']=='localhost' || $_SERVER['REMOTE_ADDR']=='::1' || $_SERVER['REMOTE_ADDR']=='127.0.0.1' || $_SERVER['SERVER_NAME'] == 'beta.lmis.gov.pk')
                        if($_SERVER['REMOTE_ADDR']=='localhost' || $_SERVER['REMOTE_ADDR']=='::1' || $_SERVER['REMOTE_ADDR']=='127.0.0.1' )
                        {
                            echo "minDate: new Date( 2013, 0, 1 ),
                                    maxDate:  new Date( ".$open_year.", ".$open_month." , ".$open_month_d.") ";
                        }
                        else
                        {
                            echo 'minDate: new Date( 2013, 0, 1 ),
                                maxDate: new Date( '.$open_year.', '.$open_month.' , '.$open_month_d.')';
                        }
                        
                        if(!empty($fromDate))
                        {
                            $d1 = explode('-',$fromDate);
                            echo ' 
                                 ,setDate: new Date('.$d1[0].', '.$d1[1].','.$d1[2] .') ';
                        }
                        ?>
                        
                    });
                    
                    $('#from_date,#province,#stakeholder,#product').change(function() {
                        $('.hide_divs').hide('500');
                        $('#filter_note').show(1000);
                    });
                })
                
    </script>
    
  
 <div class="modal fade" id="pipeline_modal" tabindex="-1" role="basic" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                    <h4 class="modal-title">Requisition Details</h4>
                                                </div>
                                                <div class="modal-body" id="pipeline_modal_body">
                                                    <div class="col-md-12 "  style=" ">
                                                        <div class="" id="pipeline_modal_graph" ></div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                                                    
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>  
</body>
<!-- END BODY -->
</html>