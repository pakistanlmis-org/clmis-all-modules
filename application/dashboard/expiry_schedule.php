<?php
/**
 * expiry_schedule
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
Login();
//include db
include(APP_PATH."includes/classes/db.php");
//include functions
include APP_PATH."includes/classes/functions.php";
//include header
include(PUBLIC_PATH."html/header.php");
//include FusionCharts
include(PUBLIC_PATH."FusionCharts/Code/PHP/includes/FusionCharts.php");

//whId
$whId = $_SESSION['user_warehouse'];
//stk id
$stkId = $_SESSION['user_stakeholder'];
//item id
$itemId = 'all';
?>
<!--[if IE]>
<style type="text/css">
    .box { display: block; }
    #box { overflow: hidden;position: relative; }
    b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
</style>

</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
<!--<div class="pageLoader"></div>-->
<!-- BEGIN HEADER -->
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL;?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL;?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>

<div class="page-container">
	<?php 
        //including top
        include PUBLIC_PATH."html/top.php";
        //including top_im
        include PUBLIC_PATH."html/top_im.php";?>

    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="tabsbar">
                        <ul>
                            <li><a href="dashboard_cws.php"> <b>Main Dashboard</b></a></li>
                            <li><a href="shipment.php"> <b>Distribution and SOH</b></a></li>
                            <li class="active"><a href="#"> <b>Expiry Status</b></a></li>
                            <li><a href="stock_summary.php"> <b>Product Summary</b></a></li>
                            <!--<li><a href="mos.php"> <b>Month of Stock</b></a></li>-->
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
            	<form name="frm" id="frm" action="" method="post">
                	<div class="col-md-12">
                    	<div class="col-md-2">
                            <label for="product">Product</label>
                            <div class="form-group">
                                <select name="product" id="product" class="form-control input-sm">
                                    <option value="all">All</option>
                                <?php
                                //query
                                //gets
                                //item id
                                //item name
                                $qry = "SELECT
                                                itminfo_tab.itm_id,
                                                itminfo_tab.itm_name
                                        FROM
                                                itminfo_tab
                                        INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                                        WHERE
                                                itminfo_tab.itm_category = 1
                                        AND stakeholder_item.stkid = 1
                                        ORDER BY
                                                itminfo_tab.frmindex ASC";
								$qryRes = mysql_query($qry);
								while ($row = mysql_fetch_array($qryRes))
								{
								?>
                                	<option value="<?php echo $row['itm_id'];?>"><?php echo $row['itm_name'];?></option>
                                <?php
								}
								?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Funding Source</label>
                            <div class="form-group">
                                <select name="funding_source" id="funding_source" class="form-control input-sm">
                                	<option value="all">All</option>
                                    <?php
                                    //query
                                    //gets
                                    //wh id
                                    //wh name
									$qry = "SELECT
												tbl_warehouse.wh_id,
												tbl_warehouse.wh_name
											FROM
												stakeholder
											INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
											WHERE
												stakeholder.stk_type_id = 2
											AND tbl_warehouse.is_active = 1
											ORDER BY
												stakeholder.stkorder ASC";
                                                                        //query result
									$qryRes = mysql_query($qry);
									while ($row = mysql_fetch_array($qryRes))
									{
										$selected = ($row['wh_id'] == $fundingSource) ? 'selected' : '';
										echo "<option value=\"$row[wh_id]\" $selected>$row[wh_name]</option>";
									}
									?>
                                </select>
                            </div>
                        </div>
                    	<div class="col-md-2">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <button type="button" id="expiry_search" name="expiry_search" value="search" class="btn btn-primary input-sm">Go</button>
                            </div>
                        </div>
                	</div>
				</form>
            </div>
            <div class="row">
            	<div class="col-md-5">
                	<div class="widget widget-tabs">
                    	<div class="widget-body" id="expirySchedule">
                        	<?php
                                //query
                                $and1 ="";
                                if($itemId!= 'all') $and1 .= " AND stock_batch.item_id = $itemId ";
							$qry = "SELECT
                                                                        stock_batch.item_id,
                                                                        itminfo_tab.itm_name,
                                                                        SUM(tbl_stock_detail.Qty) AS qty,
                                                                        YEAR(stock_batch.batch_expiry) as yr	
                                                                        FROM
										stock_batch
									INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
									INNER JOIN tbl_stock_master ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
									INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
									WHERE
									stock_batch.item_id IS NOT NULL
									AND stock_batch.Qty > 0
									$and1
									AND stock_batch.wh_id = $whId
                                                                        GROUP BY 
                                                                            YEAR(stock_batch.batch_expiry)
                                                                        ORDER BY
                                                                            YEAR(stock_batch.batch_expiry)
									";
							$xmlstore1='';
                                                        $res=mysql_query($qry);
                                                        while($row = mysql_fetch_assoc($res))
                                                        {
                                                            //Expire 18 Greater
                                                            $xmlstore1 .= "<set label='Year ".$row['yr']."' value='".$row['qty']."' link=\"JavaScript:showData('$row[item_id], ".$row['yr']."');\" />";
                                                            if($itemId!= 'all') $itm_name= $row['itm_name'];
                                                            else $itm_name= 'All Products';
                                                        }
                                                        $xmlstore = "<chart theme='fint' numberSuffix='' exportEnabled='1' exportAction='Download' caption='Expiry Schedule $itm_name' exportFileName='Expiry Schedule $itm_name" . date('Y-m-d H:i:s') . "'>";
                                                        $xmlstore .= $xmlstore1;
							$xmlstore .= "</chart>";
							FC_SetRenderer('javascript');
							echo renderChart(PUBLIC_URL."FusionCharts/Charts/Pie3D.swf", "", $xmlstore, 'Expiry' . $row['item_id'], '100%', 350, false, false);
							?>
                            <p class="center" style="color:#060">Note:- Click on the graph to see batch wise expiry details</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-7" style="display:none;" id="expiryData"></div>
            </div>
        </div>
    </div>
</div>

<?php 
//including footer
include PUBLIC_PATH."/html/footer.php";?>

<SCRIPT LANGUAGE="JavaScript">
	function showData(myVar){
		var paramArr = myVar.split(',');
		var fund_source = $('#funding_source').val();
		$('#expiryData').html("<center><div id='loadingmessage'><img src='../plmis_img/ajax-loader.gif'/></div></center>");
		$.ajax({
			type: "POST",
			url: 'expiry_ajax_year_wise.php',
			data: {itemId: paramArr[0], year: paramArr[1], fund_source: fund_source},
			dataType: 'html',
			success: function(data) {
				$('#expiryData').show().html(data);
			}
		});
	}
	$(function(){
		// Show Expity Grid
		//showData('1,1');	
	})
	
	$('#expiry_search').click(function(e) {
		var product = $('#product').val();
		var fund_source = $('#funding_source').val();
		var param = product+','+1;
		$('#expiryData').hide().html('');
		$('#expirySchedule').html("<center><div id='loadingmessage'><img src='../plmis_img/ajax-loader.gif'/></div></center>");
		$.ajax({
			type: "POST",
			url: 'expiry_ajax_year_wise.php',
			data: {product: product, fund_source: fund_source},
			success: function(data) {
				$("#expirySchedule").html(data);
				// Show Expity Grid
				//showData(param);
			}
		});
	});
</SCRIPT>
</body>
<!-- END BODY -->
</html>