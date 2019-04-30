<?php
/**
 * comparison_graph
 * @package graph
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include  header
include(PUBLIC_PATH."html/header.php");
?>
<style>
label.checkbox{margin:0px !important;}
.modal {
	display:    none;
	position:   fixed;
	z-index:    10000;
	top:       	0;
	left:       0;
	height:     100%;
	width:      100%;
	background: rgba( 255, 255, 255, 0.3 )
				url('../../public/images/loader.gif')
				50% 50%
				no-repeat;
}
/* When the body has the loading class, we turn
   the scrollbar off with overflow:hidden */
body.loading {
	overflow: auto;
}
/* Anytime the body has the loading class, our
   modal element will be visible */
body.loading .modal {
	display: block;
}
</style>
<body class="page-header-fixed page-quick-sidebar-over-content">
<div class="modal"></div>
<div class="page-container">
    <?php 
    //include top
    include PUBLIC_PATH."html/top.php";
      //include top_im
    include PUBLIC_PATH."html/top_im.php";?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <h3 class="page-title row-br-b-wp" style="margin-left:10px;">Comparison Graphs</h3>
                <div class="col-md-3">
                    <div class="widget">
                        <div class="widget-head">
                            <h3 class="heading">Filter by</h3>
                        </div>
                        <div class="widget-body">
                            <form name="frm" id="frm" action="" method="post" role="form">
                                <div class="col-md-12">
                                    <div class="control-group">
                                        <label>Indicators</label>
                                        <div class="controls">
                                            <select name="indicator" id="indicator" class="form-control input-sm">
                                                <optgroup label="CYP">
                                                	<option value="1">Couple Year Protection</option>
                            					</optgroup>
                                                <optgroup label="Dispensed">
                                                    <option value="2">Consumption</option>
                                                    <option value="3">Avg Monthly Consumption</option>
                            					</optgroup>
                                                <optgroup label="MOS">
                                                    <option value="4">Months Of Stock - Field</option>
                                                    <option value="5">Month of Stock - Warehouse</option>
                                                    <option value="6">Months of Stock - Total</option>
                            					</optgroup>
                                                <optgroup label="OnHand">
                                                    <option value="7">Stock On Hand - Field</option>
                                                    <option value="8">Stock On Hand - Warehouse</option>
                                                    <option value="9">Stock On Hand - Total</option>
                                            	</optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="control-group">
                                        <label>Compare Options</label>
                                        <div class="controls">
                                            <select name="compare_option" id="compare_option" class="form-control input-sm">
                                                <optgroup label="Years">
                                                    <option value="1">Year - National</option>
                                                    <option value="2">Year - Provincial</option>
                                                    <option value="3">Year - District</option>
                                                </optgroup>
                                                <optgroup label="Stakeholder">
                                                    <option value="4">Stakeholder - National</option>
                                                    <option value="5">Stakeholder - Provincial</option>
                                                    <option value="6">Stakeholder - District</option>
                                                </optgroup>
                                                <optgroup label="Geographical">   
                                                    <option value="8">Geographical - Provinical</option>
                                                    <option value="9">Geographical - District</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" id="province_div" style="display:none;">
                                    <div class="control-group">
                                        <label>Province</label>
                                        <div class="controls">
                                            <select name="province" id="province" class="form-control input-sm">
                                            	<option value="all">All</option>
                                                <?php
                                                //select query
                                                //gets
                                                //province
                                                //pk id
                                                //name
												$qry = "SELECT
															tbl_locations.PkLocID,
															tbl_locations.LocName
														FROM
															tbl_locations
														WHERE
															tbl_locations.LocLvl = 2
														AND tbl_locations.ParentID IS NOT NULL";
												//query result
                                                                                                $qryRes = mysql_query($qry);
												//fetch result
                                                                                                while ( $row = mysql_fetch_array($qryRes) )
												{
                                                                                                    //populate province combo
													echo "<option value=\"".$row['PkLocID']."\">".$row['LocName']."</option>";
												}
												?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" id="province_multi_div" style="display:none;">
                                    <div class="control-group">
                                        <label>Provinces</label>
                                        <div class="controls" style="border: 1px solid #F2F2F2; padding-left:25px; height:120px; overflow:auto;">
										<?php
                                                                                //select query
                                                                                //gets
                                                                                //loc id
                                                                                //Loc name
                                            $qry = "SELECT
														tbl_locations.PkLocID,
														tbl_locations.LocName
													FROM
														tbl_locations
													WHERE
														tbl_locations.LocLvl = 2
													AND tbl_locations.ParentID IS NOT NULL";
                                            //query result
                                            $qryRes = mysql_query($qry);
                                            //fetch result
                                            while ( $row = mysql_fetch_array($qryRes) )
                                            {
                                                echo '<label class="checkbox">';
                                                echo "<input type=\"checkbox\" name=\"province_multi[]\" id=\"province_multi\" value=\"".$row['PkLocID']."\" /> " . $row['LocName'];
                                                echo "</label>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="control-group" id="district_div" style="display:none;">
                                        <label>District</label>
                                        <div class="controls">
                                            <select name="district" id="district" class="form-control input-sm">
                                            	<option value="">Select</option>
                                        	</select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" id="stakeholder_type_div">
                                    <div class="control-group">
                                        <label>Stakeholder Type</label>
                                        <div class="controls">
                                            <select class="form-control input-sm" id="sector" name="sector">
                                                <option value="all">All</option>
                                                <option value="0">Public</option>
                                                <option value="1">Private</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" id="stakeholder_div">
                                    <div class="control-group">
                                        <label>Stakeholder</label>
                                        <div class="controls">
                                            <select name="stakeholder" id="stakeholder" class="form-control input-sm">
                                                <option value="all">All</option>
                                                <?php
						//select query
                                                //gets
                                                //stk id
                                                //stk name
                                                $qry = "SELECT
                                                                stakeholder.stkid,
                                                                stakeholder.stkname
                                                        FROM
                                                                stakeholder
                                                        WHERE
                                                                stakeholder.stk_type_id IN (0, 1)
                                                        AND stakeholder.ParentID IS NULL
                                                        ORDER BY
                                                                stakeholder.stkorder ASC";
						//query result
                                                
                                                $qryRes = mysql_query($qry);
                                                //fetch result
                                                while ( $row = mysql_fetch_array($qryRes) )
                                                {
                                                    //populate stakeholder combo
                                                        echo "<option value=\"".$row['stkid']."\">".$row['stkname']."</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" id="stakeholder_multi_div" style="display:none;">
                                    <div class="control-group">
                                        <label>Stakeholders</label>
                                        <div class="controls" style="border: 1px solid #F2F2F2; padding-left:25px; height:120px; overflow:auto;">
                                            <?php
                                            //select query
                                            //gets
                                            //stk name
                                            //stk id
                                            $qry = "SELECT
                                                    stakeholder.stkid,
                                                    stakeholder.stkname
                                            FROM
                                                    stakeholder
                                            WHERE
                                                    stakeholder.stk_type_id IN (0, 1)
                                            AND stakeholder.ParentID IS NULL
                                            ORDER BY
                                                    stakeholder.stkorder ASC";
                                            //query result
                                            $qryRes = mysql_query($qry);
                                            //fetch result
                                            while ( $row = mysql_fetch_array($qryRes) )
                                            {
                                                echo '<label class="checkbox">';
                                                echo "<input type=\"checkbox\" name=\"stakeholder_multi[]\" id=\"stakeholder_multi\" value=\"".$row['stkid']."\" /> " . $row['stkname'];
                                                echo "</label>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="control-group">
                                        <label>Products</label>
                                        <div class="controls" id="product_multi_div" style="border: 1px solid #F2F2F2; padding-left:25px; height:120px; overflow:auto;">
                                            <?php
                                            //select query
                                            //gets
                                            //item id
                                            //item name
                                            $qry = "SELECT DISTINCT
                                                            itminfo_tab.itm_id,
                                                            itminfo_tab.itm_name
                                                    FROM
                                                            itminfo_tab
                                                    INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                                                    /*WHERE
                                                            itminfo_tab.itm_category = 1*/
                                                    ORDER BY
                                                            itminfo_tab.frmindex ASC";
                                           //query result
                                            $qryRes = mysql_query($qry);
                                            //fetch result
                                            while ( $row = mysql_fetch_array($qryRes) )
                                            {
                                                if($row['itm_id'] == '31' || $row['itm_id'] == '32') $row['itm_name']='CS Cases - '.$row['itm_name'];
						echo '<label class="checkbox">';
                                                echo "<input type=\"checkbox\" name=\"product_multi[]\" id=\"product_multi\" value=\"".$row['itm_id']."\" /> " . $row['itm_name'];
						echo "</label>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="control-group">
                                        <label>Time Interval</label>
                                        <div class="controls">
                                            <select name="time_interval" id="time_interval" class="form-control input-sm">
                                                <optgroup label="Quarter">
                                                    <option value="1">First Quarter</option>
                                                    <option value="2">Second Quarter</option>
                                                    <option value="3">Third Quarter</option>
                                                    <option value="4">Fourth Quarter</option>
                                                </optgroup>
                                                <optgroup label="Half">
                                                    <option value="5">First Half</option>
                                                    <option value="6">Second Half</option>
                                                </optgroup>
                                                <optgroup label="Annual">
	                                                <option value="7">Annual</option>
                                                </optgroup>
                                        	</select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" id="year_multi_div">
                                    <div class="control-group">
                                        <label>Years</label>
                                        <div class="controls" style="border: 1px solid #F2F2F2; padding-left:25px; height:120px; overflow:auto;">
                                            <?php
                                            for( $year=date('Y'); $year>=2010; $year-- )
                                            {
                                                echo '<label class="checkbox">';
                                                echo "<input type=\"checkbox\" name=\"year_multi[]\" id=\"year_multi\" value=\"".$year."\" /> " . $year;
                                                echo "</label>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" id="year_div" style="display:none;">
                                    <div class="control-group">
                                        <label>Year</label>
                                        <div class="controls">
                                        	<select name="year" id="year" class="form-control input-sm">
											<?php
                                            for( $year=date('Y'); $year>=2010; $year-- )
                                            {
                                                echo "<option value=\"".$year."\">".$year."</option>";
                                            }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="control-group">
                                        <label>Graph Type</label>
                                        <div class="controls">
                                            <select name="graph_type" id="graph_type" class="form-control input-sm">
                                                <option value="1">Bar</option>
                                                <option value="2">Line</option>
                                                <option value="3">Spline</option>
                                        	</select>
                                            <input type="hidden" name="type" value="comparison" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="control-group">
                                        <label>&nbsp;</label>
                                        <div class="controls">
                                            <input type="button" id="submit_button" value="Generate Graph" class="btn btn-primary input-sm" />
                                            <input type="button" value="Reset" class="btn btn-warning input-sm" onClick="window.location=window.location" />
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="widget">
                        <div class="widget-head">
                            <h3 class="heading" id="grpah_heading">Graph for the selected criteria</h3>
                        </div>
                        <div class="widget-body" id="graphArea"> Select criteria and press submit button to see the Graphs </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- END FOOTER -->
<?php 
//include footer
include PUBLIC_PATH."/html/footer.php";
//include reports_includes
include PUBLIC_PATH."/html/reports_includes.php";?>
<script language="javascript" src="<?php echo PUBLIC_URL;?>FusionCharts/Charts/FusionCharts.js"></script>
<script language="javascript" src="<?php echo PUBLIC_URL;?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
<script>
	$(function(){
		$('#grpah_heading').html($("#indicator option:selected").text());
		// Show stakeholders
		$('#sector').change(function(e) {
			showStakeholder();
		});
		
		// Display Graph Title
		$('#indicator').change(function(e) {
            $('#grpah_heading').html($("#indicator option:selected").text());
        });
		
		// Show Stakeholder Products
		$('#stakeholder').change(function(e) {
            showProducts();
        });
		
		// Show Stakeholder Products
		$('[name="stakeholder_multi[]"]').click(function(e) {
			var checkedValues = $('input:checkbox:checked').map(function() {
				return this.value;
			}).get();
            $.ajax({
				url: 'ajax.php',
				data: {stakeholders: checkedValues, ctype: 1},
				type: 'POST',
				success: function(data){
					$('#product_multi_div').html(data);
				}
			})
        });
		
		// Show/Hide Provinces
		$('#compare_option').change(function(e) {
			var compare_option = $(this).val();
			$('#sector').val('all');
			// If District level is selected then Province is required. Remove All Option
			if(jQuery.inArray(parseInt(compare_option), [3, 6, 9]) != -1){
				$('#province option[value="all"]').remove();
				$('#province option[value=""]').remove();
				$('#province').prepend('<option value="">Select</option>');
			}else{
				$('#province option[value="all"]').remove();
				$('#province').prepend('<option value="all">All</option>');
			}
			
			showStakeholder();
			/*var option_val = $(this).val();
			$('#frm')[0].reset();
			$('#compare_option').val(option_val);*/
			if(jQuery.inArray(parseInt(compare_option), [1, 4]) == -1){
				$('#province_div').show();
				//$('#province').val('all');
				$('#province option:first-child').attr("selected", "selected");
			}else{
				$('#province_div').hide();
				$('#district_div').hide();
				$('#year_multi_div').show();
				$('#year_div').hide();
			}
			
			if(jQuery.inArray(parseInt(compare_option), [1, 2, 3]) != -1){
				$('#stakeholder_div').show();
				$('#stakeholder_multi_div').hide();
				$('#year_multi_div').show();
				$('#year_div').hide();
				$('#province_multi_div').hide();
				if(compare_option != 1)
				{
					$('#province_div').show();
					$('#province_multi_div').hide();
					$('#district_div').hide();
				}
			}else if(jQuery.inArray(parseInt(compare_option), [4, 5, 6]) != -1){
				$('#stakeholder_div').hide();
				$('#stakeholder_multi_div').show();
				$('#year_multi_div').hide();
				$('#year_div').show();
				if(compare_option != 4)
				{
					$('#province_div').show();
					$('#province_multi_div').hide();
					$('#district_div').hide();
				}
			}else if(jQuery.inArray(parseInt(compare_option), [8, 9]) != -1){
				$('#stakeholder_div').show();
				$('#stakeholder_multi_div').hide();
				$('#year_multi_div').hide();
				$('#year_div').show();
				if(compare_option == 8){
					$('#province_multi_div').show();
					$('#province_div').hide();
					$('#district_div').hide();
				}else{
					$('#province_multi_div').hide();
					$('#province_div').show();
				}
			}
			
        });
		
		// Show Districts
		$('#province').change(function(e) {
            var arr = [3, 6, 9];
			if(jQuery.inArray(parseInt($('#compare_option').val()), arr) != -1 && $(this).val() != ''){
				$.ajax({
					url: 'ajax.php',
					data: {province: $(this).val(), ctype: 2, compare_option: $('#compare_option').val()},
					type: 'POST',
					success: function(data){
						$('#district_div').show().html(data);
					}
				})
			}else{
				$('#district_div').hide();
			}
        });
		
		// Form submission
		$('#submit_button').click(function(e) {
			var compare_option = $('#compare_option').val();
			// Validation
            if(jQuery.inArray(parseInt(compare_option), [1, 2, 3]) != -1){
				if(jQuery.inArray(parseInt(compare_option), [3]) != -1 && $('[name="province"]').val() == ''){
					alert('Select province');
					return false;
				}
				if(jQuery.inArray(parseInt(compare_option), [3]) != -1 && $('[name="district').val() == ''){
					alert('Select district');
					return false;
				}
				if($('[name="product_multi[]"]:checked').length == 0){
					alert('Select at least one product');
					return false;
				}
				if($('[name="year_multi[]"]:checked').length < 2){
					alert('Select at least two years');
					return false;
				}				
			}if(jQuery.inArray(parseInt(compare_option), [4, 5, 6]) != -1){
				if(jQuery.inArray(parseInt(compare_option), [6]) != -1 && $('[name="province"]').val() == ''){
					alert('Select province');
					return false;
				}
				if(jQuery.inArray(parseInt(compare_option), [6]) != -1 && $('[name="district').val() == ''){
					alert('Select district');
					return false;
				}
				if($('[name="stakeholder_multi[]"]:checked').length < 2){
					alert('Select at least two stakeholder');
					return false;
				}
				if($('[name="product_multi[]"]:checked').length == 0){
					alert('Select at least one product');
					return false;
				}
			}if(jQuery.inArray(parseInt(compare_option), [8, 9]) != -1){
				if(compare_option == 8){
					if($('[name="province_multi[]"]:checked').length < 2){
						alert('Select at least two provinces');
						return false;
					}
					if($('[name="product_multi[]"]:checked').length == 0){
						alert('Select at least one product');
						return false;
					}
				}else if(compare_option == 9){
					if($('[name="province"]').val() == ''){
						alert('Select province');
						return false;
					}
					if($('[name="district_multi[]"]:checked').length < 2){
						alert('Select at least two districts');
						return false;
					}
					if($('[name="product_multi[]"]:checked').length == 0){
						alert('Select at least one product');
						return false;
					}
				}
			}
			// Submit data
			$('#graphArea').html('');
			$('body').addClass("loading");
			$.ajax({
				url: 'plot_graph.php',
				data: $('#frm').serialize(),
				type: 'POST',
				success: function(data){
					$('body').removeClass("loading");
					$('#graphArea').html(data);
				}
			})
        });
	})
	function showStakeholder()
	{
		$.ajax({
			url: 'ajax.php',
			data: {type: $('#sector').val(), ctype: 3, compare_option: $('#compare_option').val()},
			type: 'POST',
			success: function(data) {
				if(jQuery.inArray(parseInt($('#compare_option').val()), [4, 5, 6]) != -1){
					$('#stakeholder_multi_div').html(data);
				}else{						
					$('#stakeholder').html(data);
				}
				showProducts();
			}
		})
	}
	function showProducts()
	{
		$.ajax({
			url: 'ajax.php',
			data: {stakeholder: $('#stakeholder').val(), ctype: 1},
			type: 'POST',
			success: function(data){
				$('#product_multi_div').html(data);
			}
		})
	}
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>