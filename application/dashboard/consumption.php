<?php
/**
 * consumption
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
//include db
include(APP_PATH . "includes/classes/db.php");
//include fusion chart
include(PUBLIC_PATH . "/FusionCharts/Code/PHP/includes/FusionCharts.php");

$rrWhere = '';
//province id
$prov_id = '';

if ($_POST['year']) {
    //filter
    $where = '';
    //stakeholder
    $stakeholder = $_POST['stkId'];
    //sector
    $sector = $_POST['sector'];
    //year
    $year = $_POST['year'];
    //month
    $month = $_POST['month'];
    //report date
    $rptDate = $year . '-' . $month . '-01';
    //level
    $lvl = $_POST['lvl'];
    //province filter
    $proFilter = $_POST['proFilter'];
    //check provfilter
    if ($proFilter == 2) {
        $proFilterText = "All Products Without Condom";
        $proFilter = " AND itminfo_tab.itmrec_id != 'IT-001' ";
    } else {
        $proFilterText = "All Products";
        $proFilter = "";
    }

    if ($lvl == 1) {
        $level = 'All Pakistan Districts';
    } else if ($lvl == 2) {
        //prov id
        $prov_id = $_POST['prov_id'];
        //query
        //gets
        //pro
        $prov = mysql_fetch_array(mysql_query("SELECT
													tbl_locations.LocName
												FROM
													tbl_locations
												WHERE
													tbl_locations.PkLocID = $prov_id"));
        //prov name
        $provName = $prov['LocName'];
        //level
        $level = "$provName Districts";
        $rrWhere .= " AND tbl_warehouse.prov_id = $prov_id";
    }
    //check level
    else if ($lvl == 3) {
        //dist id	
        $dist_id = $_POST['dist_id'];
        //query 
        //gets
        //dist
        $dist = mysql_fetch_array(mysql_query("SELECT
													tbl_locations.LocName
												FROM
													tbl_locations
												WHERE
													tbl_locations.PkLocID = $dist_id"));
        //dist name	
        $distName = $dist['LocName'];
        //level	
        $level = "District $distName";
        //filter	
        $rrWhere .= " AND tbl_warehouse.dist_id = $dist_id";
    }
	//query        
    // Get Stakeholders
    //id
    //name
    $stk = "SELECT
                stakeholder.stkid,
                stakeholder.stkname
            FROM
                stakeholder
            WHERE
                stakeholder.stkid = $stakeholder";
    //query result
    $stkQuery = mysql_fetch_array(mysql_query($stk));
	$stkName = $stkQuery['stkname'];
	
    //check sector
    if ($sector == '0') {
        $sectorText = 'Public Sector';
    } else if ($sector == '1') {
        $sectorText = 'Private Sector';
    }
}
//heading
$heading = date('M Y', strtotime($rptDate));
?>
<div class="widget widget-tabs">
    <div class="widget-body">
	<?php
    //xml
    $xmlstore = '';
    //item name
    $itmName = '';
    //comsumption
    $consumption = '';
    //amc
    $AMC = '';
    //reporting rate
    $reportingRate = '';
    //check level
    if ($lvl == 1) {
        //query
        //gets
        //item id
        //comsumption
        //AMC
        //RR
        $conSumm = "SELECT
                        summary_national.item_id,
                        COALESCE(SUM(summary_national.consumption), NULL, 0) AS consumption,
                        COALESCE(SUM(summary_national.avg_consumption), NULL, 0) AS AMC,
                        AVG(summary_national.reporting_rate) AS RR
                    FROM
                        summary_national
                    INNER JOIN stakeholder ON summary_national.stakeholder_id = stakeholder.stkid
                    WHERE
                        summary_national.reporting_date = '$rptDate'
                    AND summary_national.stakeholder_id = $stakeholder
                    GROUP BY
                        summary_national.item_id";
    }
    //check level
    else if ($lvl == 2) {
        //query
        //gets
        //item id
        //comsumption
        //AMC
        //RR
        $conSumm = "SELECT
                        summary_province.item_id,
                        COALESCE(SUM(summary_province.consumption), NULL, 0) AS consumption,
                        COALESCE(SUM(summary_province.avg_consumption), NULL, 0) AS AMC,
                        AVG(summary_province.reporting_rate) AS RR
                    FROM
                        summary_province
                    INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
                    WHERE
                        summary_province.reporting_date = '$rptDate'
                    AND summary_province.stakeholder_id = $stakeholder
                    AND summary_province.province_id = $prov_id
                    GROUP BY
                        summary_province.item_id";
    }
    //check level
    else if ($lvl == 3) {
        //query
        //gets
        //item id
        //comsumption
        //AMC
        //RR
        $conSumm = "SELECT
                        summary_district.item_id,
                        COALESCE(SUM(summary_district.consumption), NULL, 0) AS consumption,
                        COALESCE(SUM(summary_district.avg_consumption), NULL, 0) AS AMC,
                        AVG(summary_district.reporting_rate) AS RR
                    FROM
                        summary_district
                    INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
                    WHERE
                        summary_district.reporting_date = '$rptDate'
                    AND summary_district.stakeholder_id = $stakeholder
                    AND summary_district.district_id = $dist_id
                    GROUP BY
                        summary_district.item_id";
    }
    //query
    //gets
    //itemrec id
    //consumption
    //RR
    $consQry = "SELECT
                    B.itmrec_id,
                    B.itm_name,
                    ROUND(SUM(COALESCE(A.consumption, NULL, 0))) AS consumption,
                    ROUND(SUM(COALESCE(A.AMC, NULL, 0))) AS AMC,
                    ROUND(AVG(A.RR)) AS RR
                FROM
                    (
                        $conSumm
                    ) A
                RIGHT JOIN (
                    SELECT
                        itminfo_tab.itm_name,
                        itminfo_tab.itmrec_id,
                        itminfo_tab.frmindex
                    FROM
                        itminfo_tab
                    INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                    WHERE
                        stakeholder_item.stkid = $stakeholder
                    AND itminfo_tab.itm_category = 1
                    $proFilter
                ) B ON A.item_id = B.itmrec_id
                GROUP BY
                    B.itmrec_id
                ORDER BY
                    B.frmindex ASC";
    //result
    $consQryRes = mysql_query($consQry);
    $num = mysql_num_rows(mysql_query($conSumm));
    //check if record exists
    if ($num > 0) {
        //fetch result
        while ($row1 = mysql_fetch_array($consQryRes)) {
            //item name
            $itmName[$row1['itmrec_id']] = $row1['itm_name'];
            //consumption
            $consumption[$row1['itmrec_id']] = $row1['consumption'];
            //AMC
            $AMC[$row1['itmrec_id']] = $row1['AMC'];
        }

        // Get Reporting Rate (All Warehouses)
        $reportedQry = "SELECT
                            ((A.reportedWH / B.totalWH) * 100) AS RR
                        FROM
                            (
                                SELECT
                                    COUNT(DISTINCT tbl_warehouse.wh_id) AS reportedWH,
                                    tbl_warehouse.stkid
                                FROM
                                    tbl_warehouse
                                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                WHERE
                                    tbl_wh_data.item_id = 'IT-001'
                                AND tbl_wh_data.report_month = $month
                                AND tbl_wh_data.report_year = $year
                                AND tbl_warehouse.stkid = $stakeholder
                                AND stakeholder.lvl IN(3, 4) $rrWhere
                            ) A
                        JOIN (
                            SELECT
                                COUNT(DISTINCT tbl_warehouse.wh_id) AS totalWH,
                                tbl_warehouse.stkid
                            FROM
                                tbl_warehouse
                            INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                            WHERE
                                tbl_warehouse.stkid = $stakeholder
                            AND stakeholder.lvl IN(3, 4) $rrWhere
                        ) B ON A.stkid = B.stkid";
        //query result
        $reportedQryRes = mysql_fetch_array(mysql_query($reportedQry));
        $reportingRate = round($reportedQryRes['RR'], 1);
        //caption
        $caption = "Consumption (Reporting Rate $reportingRate%)";
        //sub caption
        $subCaption = "Filter By: $sectorText&#44; $stkName Stakeholder&#44; $proFilterText&#44; $level&#44; $heading";
       //download File Name 
        $downloadFileName = $caption . ' - ' . $subCaption . ' - ' . date('Y-m-d H:i:s');
        $chart_id = 'Consumption';
        ?>
        <a href="javascript:exportChart('<?php echo $chart_id . $stakeholder; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
        <?php
        //xml
        $xmlstore = "<chart xAxisNamePadding='0' yAxisNamePadding='0' chartLeftMargin='0' chartRightMargin='0' chartTopMargin='0' chartBottomMargin='0' showDivLineSecondaryValue='0' showSecondaryLimits='0' theme='fint' labelDisplay='rotate' rotateValues='1' placeValuesInside='1' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$subCaption' exportFileName='$downloadFileName' pyaxisname='Units'>";
        // Start Making Categories (Products)
        $xmlstore .= "<categories>";
       //populate xml
        foreach ($itmName as $itemId => $itemName) {
            $xmlstore .= "<category label='$itemName' />";
        }
        $xmlstore .= "</categories>";

        // Consumption
        $xmlstore .= "<dataset seriesName='Consumption' parentyaxis='P'>";
        foreach ($consumption as $itemId => $value) {
            $xmlstore .= "<set value='$value' />";
        }
        $xmlstore .= "</dataset>";

        // AMC
        $xmlstore .= "<dataset seriesName='Average Monthly Consumption' parentyaxis='P' renderas='Line' showvalues='0'>";
        //populate xml
        foreach ($AMC as $itemId => $value) {
            $xmlstore .= "<set value='$value' />";
        }
        $xmlstore .= "</dataset>";
        $xmlstore .= "</chart>";
        //include chart file
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSCombiDY2D.swf", "", $xmlstore, $chart_id . $stakeholder, '100%', 350, false, false);
    } else {
        echo "No record found";
    }
    ?>
    </div>
</div>