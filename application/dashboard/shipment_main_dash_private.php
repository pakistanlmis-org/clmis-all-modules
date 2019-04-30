<?php
/**
 * shipment_main_dash_private
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

if ($_POST['year']) {
    //filter
    $where = '';
    //stakeholder
    $stakeholder = $_POST['stkId'];
    //year
    $year = $_POST['year'];
//month
    $month = $_POST['month'];
    //start date
    $startDate = $year . '-' . $month . '-01';
    //end date
    $endDate = date('Y-m-t', strtotime($startDate));
    //prov filter
    $proFilter = $_POST['proFilter'];
    //check prov filter
    if ($proFilter == 2) {
        //prov filter text
        $proFilterText = "All Products Without Condom";
        //prov filter
        $proFilter = " AND itminfo_tab.itmrec_id != 'IT-001' ";
    } else {
        //prov filter text
        $proFilterText = "All Products";
        //prov filter 
        $proFilter = "";
    }
}
//heading
$heading = date('M Y', strtotime($startDate));
?>
<div class="widget widget-tabs">
	<?php
    // Get Stakeholders
    //gets
    //stk id
    //stk name
    //wh id
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
	?>
    <div class="widget-body">
        <div class="tab-content">
		<?php
        $xmlstore = '';
        $data = '';
        //select query
        //gets
        //item id
        //itemname
        //issue
        //CB
        $dataQry = "SELECT
                        itminfo_tab.itmrec_id AS itm_id,
                        itminfo_tab.itm_name,
                        SUM(tbl_wh_data.wh_issue_up) AS Issue,
                        SUM(tbl_wh_data.wh_cbl_a) AS CB
                    FROM
                        itminfo_tab
                    INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                    INNER JOIN tbl_wh_data ON itminfo_tab.itmrec_id = tbl_wh_data.item_id
                    WHERE
                        stakeholder_item.stkid = $stakeholder
                    AND itminfo_tab.itm_category = 1
                    AND tbl_wh_data.wh_id = $whId
                    AND tbl_wh_data.RptDate BETWEEN '$startDate' AND '$endDate'
                    $proFilter
                    GROUP BY
                        itminfo_tab.itm_id
                    ORDER BY
                        itminfo_tab.frmindex ASC";
        //result
        $qryRes = mysql_query($dataQry);
        //fetch results
        $num = mysql_num_rows(mysql_query($dataQry));
        if ($num > 0) {
            //caption
            $caption = "Distribution and Stock on Hand(SOH)";
            //sub caption
            $subCaption = "Filter By: Private Sector&#44; $stkName Stakeholder&#44; $proFilterText $heading";
            //downloadFileName 
            $downloadFileName = $caption . ' - ' . $subCaption . ' - ' . date('Y-m-d H:i:s');
            //chart id
            $chart_id = 'distributionAndSOH';
            ?>
                <a href="javascript:exportChart('<?php echo $chart_id . $stakeholder; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                <?php
                $xmlstore = "<chart theme='fint' numberScaleValue='1000,1000,1000' numberScaleUnit='K,M,B' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$subCaption' exportFileName='$downloadFileName' yAxisName='Units' xAxisName='Products' showValues='1'>";
                while ($row = mysql_fetch_array($qryRes)) {
                    //item name
                    $data[$row['itm_id']]['name'] = $row['itm_name'];
                    //issue
                    $data[$row['itm_id']]['issue'] = (is_null($row['Issue'])) ? 0 : $row['Issue'];
                    //CB
                    $data[$row['itm_id']]['CB'] = (is_null($row['CB'])) ? 0 : $row['CB'];
                }
                $xmlstore .= "<categories>";
                foreach ($data as $key => $name) {
                    //name
                    $xmlstore .= "<category label='$name[name]' />";
                }
                $xmlstore .= "</categories>";

                $xmlstore .= "<dataset seriesName='Issue'>";
                foreach ($data as $key => $name) {
                    //issue	
                    $xmlstore .= "<set value='$name[issue]' />";
                }
                $xmlstore .= "</dataset>";

                $xmlstore .= "<dataset seriesName='Stock on Hand'>";
                foreach ($data as $key => $name) {
                    //CB
                    $xmlstore .= "<set value='$name[CB]' />";
                }
                $xmlstore .= "</dataset>";

                $xmlstore .= "</chart>";
                //include chart
                FC_SetRenderer('javascript');
                echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, $chart_id . $stakeholder, '100%', 380, false, false);
            } else {
                echo "No record found";
            }
            ?>
        </div>
    </div>
</div>