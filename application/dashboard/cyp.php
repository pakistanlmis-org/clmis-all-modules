<?php
/**
 * cyp
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
include(APP_PATH."includes/classes/db.php");
//include fusion chart
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");

$rrWhere = '';
//prov id
$prov_id = '';

if ( $_POST['year'] )
{
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
	//rpt date
    $rptDate = $year.'-'.$month.'-01';
	//level
    $lvl = $_POST['lvl'];
	//prov filter
    $proFilter = $_POST['proFilter'];
	//check prov filter
	if ($proFilter == 2)
	{
		$proFilterText = "All Products Without Condom";
		$proFilter = " AND itminfo_tab.itmrec_id != 'IT-001' ";
	}
	else
	{
		$proFilterText = "All Products";
		$proFilter = "";
	}
    //check level
	if ( $lvl == 1 )
	{
		$level = 'All Pakistan Districts';
	}
        //check level
	else if ( $lvl == 2 )
	{
		//get prov id	
		$prov_id = $_POST['prov_id'];
		//query 
		//gets
		//prov
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
		//where
		$rrWhere .= " AND tbl_warehouse.prov_id = $prov_id";
	}
    //check level
	else if ( $lvl == 3 )
	{
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
		//where
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
	if ($sector == '0')
	{
		$sectorText = 'Public Sector';
	}
	else if ($sector == '1')
	{
		$sectorText = 'Private Sector';
	}
}
//heading
$heading = date('M Y', strtotime($rptDate));
?>
<div class="widget widget-tabs">
    <!-- Tabs Heading -->
    <div class="widget-body">
		<?php
        //xml
        $xmlstore = '';
        //check level		
        if ( $lvl == 1 )
        {
            //cypSumm  query
            //gets
            //item id
            //consumption
            $cypSumm = "SELECT
                            summary_national.item_id,
                            SUM(summary_national.consumption) AS consumption
                        FROM
                            summary_national
                        WHERE
                            summary_national.stakeholder_id = $stakeholder
                        AND summary_national.reporting_date = '$rptDate'
                        GROUP BY
                            summary_national.item_id";
        }
                        //check level
        else if ( $lvl == 2 )
        {
                            //cypSumm query
                            //gets
                            //item
                            //consumption
            $cypSumm = "SELECT
                            summary_province.item_id,
                            SUM(summary_province.consumption) AS consumption
                        FROM
                            summary_province
                        WHERE
                            summary_province.stakeholder_id = $stakeholder
                        AND summary_province.reporting_date = '$rptDate'
                        AND summary_province.province_id = $prov_id
                        GROUP BY
                            summary_province.item_id";
        }
                        //check level
        else if ( $lvl == 3 )
        {
                            //cypSumm query
                            //gets
                            //item
                            //consumption
            $cypSumm = "SELECT
                            summary_district.item_id,
                            SUM(summary_district.consumption) AS consumption
                        FROM
                            summary_district
                        WHERE
                            summary_district.stakeholder_id = $stakeholder
                        AND summary_district.reporting_date = '$rptDate'
                        AND summary_district.district_id = $dist_id
                        GROUP BY
                            summary_district.item_id";
        }
        //rrQry query
        //gets
        //item name
                        //CYP
        $rrQry = "SELECT
                        B.itm_name,
                        ROUND(COALESCE(A.consumption * B.extra, NULL, 0)) AS CYP
                    FROM
                        (
                            $cypSumm
                        ) A
                    RIGHT JOIN (
                        SELECT
                            itminfo_tab.itm_name,
                            itminfo_tab.itmrec_id,
                            itminfo_tab.frmindex,
                            itminfo_tab.extra
                        FROM
                            itminfo_tab
                        INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                        WHERE
                            stakeholder_item.stkid = $stakeholder
                        AND itminfo_tab.itm_category = 1
                        $proFilter
                    ) B ON A.item_id = B.itmrec_id
                    ORDER BY
                        B.frmindex";
        //query result
        $rrQryRes = mysql_query($rrQry);
        //fetch result
        $num = mysql_num_rows(mysql_query($cypSumm));
        if( $num > 0 )
        {
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
            $caption = "Couple Year Protection (Reporting Rate $reportingRate%)";
            //subCaption
            $subCaption = "Filter By: $sectorText&#44; $stkName Stakeholder&#44; $proFilterText&#44; $level&#44;  $heading";
            //downloadFileName 
            $downloadFileName = $caption . ' - ' . $subCaption . ' - ' . date('Y-m-d H:i:s');
            //chart id
            $chart_id = 'CYP';
            ?>
                <a href="javascript:exportChart('<?php echo $chart_id . $stakeholder;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
            <?php
            
            //xml
            $xmlstore = "<chart xAxisNamePadding='0' yAxisNamePadding='0' chartLeftMargin='0' chartRightMargin='0' chartTopMargin='0' chartBottomMargin='0' theme='fint' numberScaleValue='1000,1000,1000' numberScaleUnit='K,M,B' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$subCaption' exportFileName='$downloadFileName' yAxisName='Years' xAxisName='Products' showValues='1'>";
            //fetch result
            while ($data = mysql_fetch_array($rrQryRes)) {
                $xmlstore .= "<set label='$data[itm_name]' value='$data[CYP]' />";
            }
            $xmlstore .= "</chart>";
                                //include chart
            FC_SetRenderer('javascript');
            echo renderChart(PUBLIC_URL."FusionCharts/Charts/Column2D.swf", "", $xmlstore, $chart_id . $stakeholder, '100%', 350, false, false);
        }
        else
        {
                            //error msg
            echo "No record found";
        }
        ?>
    </div>
</div>