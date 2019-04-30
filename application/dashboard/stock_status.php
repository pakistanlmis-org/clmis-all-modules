<?php
/**
 * stock_status
 * @package dashboard
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include Configuration.inc
include("../includes/classes/Configuration.inc.php");
//include db
include(APP_PATH . "includes/classes/db.php");
//include FusionCharts
include(PUBLIC_PATH . "/FusionCharts/Code/PHP/includes/FusionCharts.php");
include("stock_status_functions.php");

// When the request has been made
if ($_POST['year']) {
    //stakeholder
    $stakeholder_id = $_POST['stkId'];
	// Sector
    $sector = $_POST['sector'];
    //Year
    $year = $_POST['year'];
    //Month
    $month = $_POST['month'];
    //Reporting date
    $reporting_date = $year . '-' . str_pad($month, 2, 0, STR_PAD_LEFT) . '-01';
    //level
    $level = $_POST['lvl'];
    //Product filter
    $product_filter_id = $_POST['proFilter'];
	// Province Level
	if ($level == 2) {
        //Get province id
        $province_id = $_POST['prov_id'];
	}
	// District Level
	if ($level == 3) {
        //Get District id
        $district_id = $_POST['dist_id'];
	}
}
//select query
// Get Stakeholders
//Stakeholders name
//Stakeholders pkid
$stk = "SELECT
			stakeholder.stkid,
			stakeholder.stkname
		FROM
			stakeholder
		WHERE
			stakeholder.stkid = $stakeholder_id";
//query result
$stkQuery = mysql_fetch_array(mysql_query($stk));
$stkName = $stkQuery['stkname'];
//chart id
$chart_id = "DistrictStockStatus";
$xmlstore = '';
?>
<div class="widget widget-tabs">
    <div class="widget-body">
		<?php
		$total_hf = 0;
		// If not national level
		if($level != 1)
		{
			// If Province Level is selected check if province have Health Facilties
			$qry_to_check_hf = "SELECT
									COUNT(tbl_warehouse.wh_id) AS totalHF
								FROM
									tbl_warehouse
								INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
								INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
								WHERE
									stakeholder.lvl = 7
								AND tbl_warehouse.prov_id = ".$province_id."
								AND tbl_warehouse.stkid = $stakeholder_id ";
			// Execute Query
			$check_hf_res = mysql_fetch_array(mysql_query($qry_to_check_hf));
			$total_hf = $check_hf_res['totalHF'];
		}
		?>    
		<!-- Tab content -->
		<div class="tab-pane <?php echo $active; ?>" id="stock-status-<?php echo $stakeholder_id; ?>">
			<div id="stock-<?php echo $stakeholder_id; ?>">
				
                <?php
				// For National Level
				if($level == 1)
				{
					$xmlstore = districtWise($chart_id, $reporting_date, $product_filter_id, $stakeholder_id, $province_id, $sector, $level);
					// If data is found 
					if(!empty($xmlstore))
					{
						//include chart
						FC_SetRenderer('javascript');
						echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, $chart_id . $stakeholder_id, '100%', 322, false, false);
					}
					else
					{
						echo "No data found";
					}
				}
				// For Provincial Level
				else if ($level == 2)
				{	
				?>
					<ul class="nav nav-tabs">
				        <li class="active"><a href="#tab-district<?php echo $stakeholder_id; ?>" data-toggle="tab">District Strores</a></li>
                        <?php
						// If Province does not have Health Facilities
						if($total_hf == 0)
						{
						?>
                        <li><a href="#tab-field<?php echo $stakeholder_id; ?>" data-toggle="tab">Field Stores</a></li>
                        <?php
						}
						// If Province have Health Facilities
						else
						{
						?>
                        <li><a href="#tab-hf<?php echo $stakeholder_id; ?>" data-toggle="tab">Health Facilities</a></li>
                        <?php
						}?>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab-district<?php echo $stakeholder_id; ?>">
                            <?php
                            // District Wise Status for Provincial Level
                            $xmlstore = districtWise($chart_id, $reporting_date, $product_filter_id, $stakeholder_id, $province_id, $sector, $level);
                            // If data is found 
                            if(!empty($xmlstore))
                            {
                                //include chart
                                FC_SetRenderer('javascript');
                                echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, $chart_id . $stakeholder_id, '100%', 322, false, false);
                            }
                            else
                            {
                                echo "No data found";
                            }
                            ?>
                        </div>
                    <?php
					// If Province does not have Health Facilities
					if($total_hf == 0)
					{
					?>
                        <div class="tab-pane fade" id="tab-field<?php echo $stakeholder_id; ?>">
                            <?php
                            // Show Field Stores data
							$xmlstore = fieldWise($chart_id, $reporting_date, $product_filter_id, $stakeholder_id, $province_id, $sector, $level);
							// If data is found 
							if(!empty($xmlstore))
							{
								//include chart
								FC_SetRenderer('javascript');
								echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, $chart_id . $stakeholder_id.'field', '100%', 322, false, false);
							}
							else
							{
								echo "No data found";
							}
                            ?>
                        </div>
                    <?php
					}
					// If Province have Health Facilities
					else
					{
					?>
                        <div class="tab-pane fade" id="tab-hf<?php echo $stakeholder_id; ?>">
                            <?php
							 // Show health Facilities data
							$xmlstore = healthFacilityWise($chart_id, $reporting_date, $product_filter_id, $stakeholder_id, $province_id, $district_id, $sector, $level, $hfArr);
							// If data is found 
							if(!empty($xmlstore))
							{
								//include chart
								FC_SetRenderer('javascript');
								echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, $chart_id . $stakeholder_id.'hf', '100%', 322, false, false);
							}
							else
							{
								echo "No data found";
							}
							?>
                        </div>
                    <?php
					}?>
				</div>
				<?php
				}
				else if ($level == 3)
				{
				?>
					<ul class="nav nav-tabs">
				        <li class="active"><a href="#tab-district<?php echo $stakeholder_id; ?>" data-toggle="tab">District Strore</a></li>
                        <li><a href="#tab-hf<?php echo $stakeholder_id; ?>" data-toggle="tab">Health Facilities</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab-district<?php echo $stakeholder_id; ?>" style="max-height:326px; overflow:auto;">
                            <?php
                            // District Store MOS
							$districtMOS = districtMOS($reporting_date, $product_filter_id, $stakeholder_id, $district_id);
							if ($districtMOS)
							{
							?>
                            	<script src="<?php echo PUBLIC_URL;?>/common/theme/scripts/demo/tables.js" type="text/javascript"></script>
                            	<table class="districtMOS table table-bordered table-condensed datatable">
                                	<thead>
                                    	<tr>
                                        	<th width="80">Sr. No.</th>
                                        	<th>Product</th>
                                        	<th>MOS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            <?php
								// Loop through results
								$counter = 1;
								while ($row = mysql_fetch_array($districtMOS))
								{
									echo "<tr>";
									echo "<td class=\"text-center\">".$counter++."</td>";
									echo "<td>".$row['itm_name']."</td>";
									echo "<td class=\"text-right\">".$row['MOS']."</td>";
									echo "</tr>";
								}
							?>
                            		</tbody>
								</table>
							<?php
							}
							else
							{
								echo "No data found";
							}
                            ?>
                        </div>
                        <div class="tab-pane fade" id="tab-hf<?php echo $stakeholder_id; ?>">
                            <?php
							 // Show health Facilities data
							$xmlstore = healthFacilityWise($chart_id, $reporting_date, $product_filter_id, $stakeholder_id, $province_id, $district_id, $sector, $level, $hfArr);
							// If data is found 
							if(!empty($xmlstore))
							{
								//include chart
								FC_SetRenderer('javascript');
								echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, $chart_id . $stakeholder_id.'hf', '100%', 322, false, false);
							}
							else
							{
								echo "No data found";
							}
							?>
                            <mark>For details please click the graph bars</mark>
                        </div>
                    </div>
				<?php
				}
				?>
			</div>
		</div>
        <?php if($level != 3){?>
        <mark>For details please click the graph bars</mark>
        <?php }?>
    </div>
</div>