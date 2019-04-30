<?php

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


$todays_date =  date('d');
if($todays_date >=20) $open_month =  date('m')-2;
else $open_month =  date('m')-3;

$a_date = date('Y').'-'.($open_month+1).'-1';
$open_month_d= date("t", strtotime($a_date));

$open_month2 =  date('m')-1;
$a_date2 = date('Y').'-'.($open_month2+1).'-1';
$open_month_d2= date("t", strtotime($a_date2));


$open_year2 =  date('Y');

//Getting user_level
$level = $_SESSION['user_level'];
//Getting user_district
$district = $_SESSION['user_district'];

$selProv = (!empty($_REQUEST['province']))?$_REQUEST['province']:'1';
$fromDate = (!empty($_REQUEST['from_date']))?$_REQUEST['from_date']:date("Y-m-01");
//$toDate = $fromDate
$funding_source     = (!empty($_REQUEST['stakeholder']))?$_REQUEST['stakeholder']:'20646';


$qry_comments= "SELECT
                    GROUP_CONCAT(funding_stk_prov.stakeholder_id) as stk
                FROM
                    funding_stk_prov
                INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                WHERE
                    funding_stk_prov.province_id = $selProv AND
                    funding_stk_prov.funding_source_id = $funding_source

            ";
$res = mysql_query($qry_comments);
$comments_arr =array();
$row=mysql_fetch_assoc($res);
//print_r($row);exit;
$stakeholder        = (!empty($row['stk']))?$row['stk']:'1';

$wh_comm='';
if(!empty($stakeholder)) $wh_comm  .= " and stakeholder_id  in (".$stakeholder.") ";
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
            dashboard_comments.dashboard_id = 112
             AND dashboard_comments.month_year like  '".$fromDate."%'
            $wh_comm
            ";
$res = mysql_query($qry_comments);
$comments_arr =array();
while($row=mysql_fetch_assoc($res))
{
    $comments_arr[$row['dashlet_id']][]=$row['comments'];
}

//echo '<pre>';print_r($comments_arr);exit;
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
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Filter by</h3>
                                <span class="collapse-toggle"></span></div>
                                <div class="widget-body collapse in">
                                    <form name="frm" id="frm" action="" method="POST">
                                        <table width="100%">
                                            <tbody>
                                            <tr>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Month</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php echo $fromDate; ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Province/Region</label>
                                                                <select name="province" id="province" required="required" class="form-control input-sm">
                                                                        <?php

                                                                            $queryprov = "SELECT
                                                                                                tbl_locations.PkLocID AS prov_id,
                                                                                                tbl_locations.LocName AS prov_title
                                                                                            FROM
                                                                                                tbl_locations
                                                                                            WHERE
                                                                                                LocLvl = 2
                                                                                            AND parentid IS NOT NULL
                                                                                            AND tbl_locations.LocType = 2
                                                                                            AND tbl_locations.PkLocID=1";
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
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Stakeholder</label>
                                                            <select name="stakeholder" id="stakeholder" required class="form-control input-sm">
                                                                <option value="">Select</option>
                                                                    <?php

                                                                        $querys = "SELECT
                                                                                    DISTINCT 
                                                                                    tbl_warehouse.wh_id,
                                                                                    tbl_warehouse.wh_name
                                                                                    FROM
                                                                                    funding_stk_prov
                                                                                    INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                                                                                    WHERE
                                                                                    funding_stk_prov.province_id = $selProv";
                                                                        //query result
                                                                        $rsprov = mysql_query($querys) or die();
                                                                        $stk_name='';
                                                                        while ($rowp = mysql_fetch_array($rsprov)) {
                                                                                if ($funding_source == $rowp['wh_id']) {
                                                                                    $sel = "selected='selected'";
                                                                                    $stk_name=$rowp['wh_name'];
                                                                                } else {
                                                                                    $sel = "";
                                                                                }
                                                                                //Populate prov_sel combo
                                                                                ?>
                                                                                <option value="<?php echo $rowp['wh_id']; ?>" <?php echo $sel; ?>><?php echo $rowp['wh_name']; ?></option>
                                                                            <?php
                                                                        }

                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td class="col-md-2">
                                                    <input type="hidden" name="stk_name" id="stk_name" value="<?php echo $stk_name?>">
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
                            <h3 class="page-title row-br-b-wp center"> <?php echo $stk_name;?> - Stakeholder Stock Sufficiency Dashboard - <?php echo date('M-Y',strtotime($fromDate)); ?></h3>
                            
                        </div>
                    </div>
                     <div class="row hide_divs">
                                
                                <div class="col-md-6 my_dash_cols">
                                    <div class="col-md-12 "  style=" ">
                                        <div class="dashlet_graph" id="dashboard_ss_b3" href='dashboard_ss_stk_a1.php'></div>
                                    </div> 
                                    <div class="col-md-12 "  style=" ">
                                        <div class="widget1 widget-tabs">    
                                            <div class="widget-body" style="height:auto;overflow: auto;">
                                                
                                                
                                                <p style=" font-size: 10px">
                                                    <?php 
                                                    $dashlet_id='123';
                                                    if(!empty($comments_arr[$dashlet_id]))
                                                        foreach($comments_arr[$dashlet_id] as $k=>$comment)echo (!empty($comment)?$comment.'</br></br>':'');
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 my_dash_cols">
                                    <div class="col-md-12 ">
                                        <div class="dashlet_graph" id="dashboard_ss_c1" href='dashboard_ss_stk_b1.php'></div>
                                    </div>
                                    <div class="col-md-12 ">
                                        <div class="widget1 widget-tabs">    
                                            <div class="widget-body" style="height:auto;overflow: auto;">
                                                
                                                
                                                <p style=" font-size: 10px">
                                                    <?php 
                                                    $dashlet_id='124';
                                                    if(!empty($comments_arr[$dashlet_id]))
                                                        foreach($comments_arr[$dashlet_id] as $k=>$comment)echo (!empty($comment)?$comment.'</br></br>':'');
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-md-12 ">
                                        <div class="dashlet_graph" id="dashboard_ss_c2" href='dashboard_ss_stk_b2.php'></div>
                                    </div>
                                    

                                    <div class="col-md-12 ">
                                        <div class="widget1 widget-tabs">    
                                            <div class="widget-body" style="height:auto;overflow: auto;">
                                                
                                                
                                                <p style=" font-size: 10px">
                                                    <?php 
                                                    $dashlet_id='125';
                                                    if(!empty($comments_arr[$dashlet_id]))
                                                        foreach($comments_arr[$dashlet_id] as $k=>$comment)echo (!empty($comment)?$comment.'</br></br>':'');
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                        </div>
                        
                        <div class="row">
                                <div class="col-md-12 my_dash_cols">
                                   <div class="col-md-12 ">
                                    <div class="widget1 widget-tabs">    
                                        <div class="widget-body" style="height:auto;overflow: auto;">
                                            
                                            
                                                <p style=" font-size: 10px">
                                                    <?php 
                                                    $dashlet_id='0';
                                                    if(!empty($comments_arr[$dashlet_id]))
                                                        foreach($comments_arr[$dashlet_id] as $k=>$comment)echo (!empty($comment)?$comment.'</br></br>':'');
                                                    ?>
                                                </p>
                                        </div>
                                    </div>
                                   </div>     
                                   
                                </div>
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
			loadDashlets();
		})
                
                function showDrillDown_b1(prov,prov_name,from_date,stk,stk_name,prod_id,prod_name) {
                   
                   window.open("drilldown_s_b1_1.php?province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name+"&prod_id="+prod_id+"&prod_name="+prod_name ,"", "width=800,height=600");
                }
                
                function showDrillDown_b2(prov,prov_name,from_date,stk,stk_name,prod_id,prod_name) {
                   
                   window.open("drilldown_s_b2_1.php?province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name+"&prod_id="+prod_id+"&prod_name="+prod_name ,"", "width=800,height=600");
                }
                
              

		function loadDashlets(stkId='1')
		{
			$('.dashlet_graph').each(function(i, obj) {
				
				var url = $(this).attr('href');
				var id = $(this).attr('id');
				
                                var dataStr='';
                                dataStr += 'province=' + $('#province').val();
                                dataStr += '&prov_name=' + $('#prov_name').val();
                                dataStr += '&from_date=' + $('#from_date').val();
                                dataStr += '&to_date=' + $('#to_date').val();
                                dataStr += '&stakeholder=' + $('#stakeholder').val();
                                dataStr += '&stk_name=' + $('#stk_name').val();
                                

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
				data: {lvl: 3, prov_id: $('#prov_id').val()},
				success: function(data) {
					$("#districtArea").html(data);
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
                
                    
                    $('#from_date,#province,#stakeholder').change(function() {
                        //var v = $(this).val();
                        $('.hide_divs').hide('500');
                        $('#filter_note').show(1000);
                    });
                   
                    
                
                    $('#from_date, #to_date').datepicker({
                        dateFormat: "yy-mm",
                        constrainInput: false,
                        changeMonth: true,
                        changeYear: true,
                          <?php
                        //if($_SERVER['REMOTE_ADDR']=='localhost' || $_SERVER['REMOTE_ADDR']=='::1' || $_SERVER['REMOTE_ADDR']=='127.0.0.1' || $_SERVER['SERVER_NAME'] == 'beta.lmis.gov.pk')
                        if(FALSE)
                        {
                            echo "maxDate: '' ";
                        }
                        else
                        {
                            echo 'minDate: new Date( 2013, 0, 1 ),
                                maxDate: new Date( '.$open_year2.', '.$open_month2.' , '.$open_month_d2.')';
                        }
                        if(!empty($fromDate))
                        {
                            $d1 = explode('-',$fromDate);
                            echo ' 
                                 , setDate: new Date('.$d1[0].','.$d1[1].',01) ';
                        }
                        ?>
                        
                    });
                })
    </script>

</body>
<!-- END BODY -->
</html>