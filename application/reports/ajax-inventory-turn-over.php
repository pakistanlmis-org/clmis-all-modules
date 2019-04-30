<?php
/**
 * ajax_calls
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
//include Fusion Charts
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
?>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>
<?php
// If data is posted
if ($_POST) {
    // Get parameters
    //district
    $district = mysql_real_escape_string($_POST['district']);
    //item id
    $item_id = mysql_real_escape_string($_POST['item_id']);
    //province
    $province = mysql_real_escape_string($_POST['province']);
    //quarter
    $quarter = mysql_real_escape_string($_POST['quarter']);
    //sector
    $sector = mysql_real_escape_string($_POST['sector']);
    //stakeholder
    $stakeholder = mysql_real_escape_string($_POST['stk_sel']);
    //year
    $year = mysql_real_escape_string($_POST['year']);
    //level
    $level = mysql_real_escape_string($_POST['office_level']);

    // End Date Array
    $qtrArr = array(
        1 => $year . '-03-01',
        2 => $year . '-06-01',
        3 => $year . '-09-01',
        4 => $year . '-12-01',
    );
    //end date
    $endDate = $qtrArr[$quarter];
    //sub caption
    $subCaption = 'Filter by: ';
    // Select table based on Office Level
    if ($level == 1) {
        $subCaption .= 'National';
    } else if ($level == 2) {
        $prov = mysql_fetch_array(mysql_query("SELECT
													tbl_locations.LocName
												FROM
													tbl_locations
												WHERE
													tbl_locations.PkLocID = $province"));
        //prov name
        $provName = $prov['LocName'];
        $subCaption .= $provName;
    } else if ($level == 3) {
        $dist = mysql_fetch_array(mysql_query("SELECT
													tbl_locations.LocName
												FROM
													tbl_locations
												WHERE
													tbl_locations.PkLocID = $district"));
        //dist name	
        $distName = $dist['LocName'];
        //sub caption
        $subCaption .= $distName;
    }

    // Declare filter variables
    $sectorFilter = $stakeholderFilter = $provinceFilter = $districtFilter = '';

    // Sector Filter
    if ($sector != 'all') {
        $sectorFilter = " AND stakeholder.stk_type_id = $sector";
        if ($sector == '0') {
            $subCaption .= '&#44; Public Sector';
        } else if ($sector == '1') {
            $subCaption .= '&#44; Private Sector';
        }
    } else {
        $subCaption .= '&#44; All Sectors';
    }
    // Stakeholder Filter
    if ($stakeholder != 'all') {
        $stakeholderFilter = " AND stakeholder.stkid = $stakeholder";
        $dist = mysql_fetch_array(mysql_query("SELECT
													stakeholder.stkname
												FROM
													stakeholder
												WHERE
													stakeholder.stkid = $stakeholder"));
        //stk name	
        $stkname = $dist['stkname'];
        //sub caption
        $subCaption .= "&#44; " . $stkname;
    }
    // Province Filter
    if ($level == 2 && $province != 'all') {
        $provinceFilter = " AND summary_district.province_id = $province";
    }
    // District Filter
    if ($level == 3 && $district != 'all') {
        $districtFilter = " AND summary_district.district_id = $district";
    }

    // Zero fill
    /* $endDate1 = date('Y-m-01', strtotime($endDate));
      $endDate = date('Y-m-01', strtotime('+1 month', strtotime($endDate)));
      $startDate = date('Y-m-01', strtotime("-35 months", strtotime($endDate1)));

      // Start date and End date
      $begin = new DateTime($startDate);
      $end = new DateTime($endDate);
      $diff = $begin->diff($end);
      $interval = DateInterval::createFromDateString('1 month');
      $period = new DatePeriod($begin, $interval, $end);
      foreach ($period as $date) {
      //$data['Q'.ceil($date->format("m")/3) . '-' . $date->format("Y")][] = 0;
      } */

    // Query to get the data
   	$qry = "SELECT
				summary_district.reporting_date,
				SUM(summary_district.consumption) AS consumption,
				itminfo_tab.itm_name
			FROM
				summary_district
			INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
			INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
			WHERE
				summary_district.item_id = '$item_id'
			AND summary_district.reporting_date BETWEEN DATE_ADD('$qtrArr[$quarter]', INTERVAL -35 MONTH) AND '$qtrArr[$quarter]'
			$sectorFilter
			$stakeholderFilter
			$provinceFilter
			$districtFilter
			GROUP BY
				summary_district.reporting_date
			ORDER BY
				summary_district.reporting_date ASC";
    //query result
    $qryRes = mysql_query($qry);
    //fetch result
    while ($row = mysql_fetch_array($qryRes)) {
        $product = $row['itm_name'];
        $data['Q' . ceil(date('m', strtotime($row['reporting_date'])) / 3) . '-' . date('Y', strtotime($row['reporting_date']))]['consumption'][] = $row['consumption'];

        // Query to get Avg. SOH
        $qrySOH = "SELECT
						AVG(A.SOH) AS SOH
					FROM
						(
							SELECT
								SUM(summary_district.soh_district_store) AS SOH
							FROM
								summary_district
							INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
							WHERE
								summary_district.item_id = '$item_id'
							$sectorFilter	
							$stakeholderFilter
							$provinceFilter
							$districtFilter
							AND summary_district.reporting_date <= '" . $row['reporting_date'] . "'
							GROUP BY
								summary_district.reporting_date
							ORDER BY
								summary_district.reporting_date DESC
							LIMIT 3
						) A";
        //fetch results
        $rowSOH = mysql_fetch_array(mysql_query($qrySOH));
        $data['Q' . ceil(date('m', strtotime($row['reporting_date'])) / 3) . '-' . date('Y', strtotime($row['reporting_date']))]['SOH'][] = $rowSOH['SOH'];
    }
    // Chart ID
    $chart_id = 'chart_id';
    // Caption
    $caption = 'Inventory Turnover Analysis';
    //sub caption
    $subCaption .= '&#44; ' . $product;
    // Download filename
    $downloadFileName = $caption . ' - ' . $subCaption;
    ?>
    <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
    <?php
    // XML string variable
    $xmlstore = "<chart theme='fint' labelDisplay='' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$subCaption' exportFileName='$downloadFileName' yAxisName='Value' xAxisName='Quarters' showValues='1'>";
    // Start Loop
    foreach ($data as $key => $arr) {
        $value = round(array_sum($arr['consumption']) / (array_sum($arr['SOH']) / 3), 2);
        $xmlstore .= "<set label='$key' value='$value' />";
    }
    $xmlstore .= "</chart>";
    // Render Chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL . "FusionCharts/Charts/SPLine.swf", "", $xmlstore, $chart_id, '100%', 500, false, false);
}