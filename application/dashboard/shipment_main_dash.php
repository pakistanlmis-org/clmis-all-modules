<?php
/**
 * shipment_main_dash
 * @package dashboard
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");

if ( $_POST['year'] )
{
	$where = '';
        //Getting year
	$year = $_POST['year'];
        //Getting month
	$month = $_POST['month'];
	$startDate = $year.'-'.$month.'-01';
        //end date
	$endDate = date('Y-m-t', strtotime($startDate));
	$proFilter = $_POST['proFilter'];
	
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
}
$whId = 123;


//Chart heading
$heading = date('M Y', strtotime($startDate));
//Chart caption
$caption = "Central Warehouse Distribution and Stock on Hand(SOH)";
//Chart heading sub Caption
$subCaption = $proFilterText.'('.$heading.')';
//download File Name
$downloadFileName = $caption . ' - ' . $subCaption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'distributionAndSOH';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
        <?php 
        
        $qry=" SELECT
                    funding_stk_prov.pk_id,
                    funding_stk_prov.stakeholder_id,
                    funding_stk_prov.province_id,
                    funding_stk_prov.funding_source_id,
                    tbl_warehouse.wh_name
                FROM
                    funding_stk_prov
                INNER JOIN tbl_warehouse ON funding_stk_prov.funding_source_id = tbl_warehouse.wh_id
                WHERE
                    funding_stk_prov.stakeholder_id = ".$_REQUEST['stkId']." AND
                    funding_stk_prov.province_id = ".$_SESSION['user_province1']."
            ";
        $qryRes1 = mysql_query($qry);
        $row  = mysql_fetch_assoc($qryRes1);
        $funding_source = $row['funding_source_id'];
        
        if(empty($funding_source)){
                echo 'No Data Found.';
                exit;
            }
            $stkId = 1;
            ?>
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        //Query for shipment main dashboard
	$qry = "SELECT
                    itminfo_tab.itm_name,
                    itminfo_tab.qty_carton,
                    Sum(tbl_stock_detail.Qty) AS CB,
                    tbl_itemunits.UnitType,
                    itminfo_tab.itm_id,
                    stock_batch.funding_source,
                    tbl_warehouse.wh_name AS funding_source_name,
                    stock_batch.batch_expiry
                FROM
                        stock_batch
                INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                WHERE
                        DATE_FORMAT(
                                tbl_stock_master.TranDate,
                                '%Y-%m-%d'
                        ) <= '2018-01-01'
                AND (
                        tbl_stock_master.WHIDFrom = $whId
                        OR tbl_stock_master.WHIDTo = $whId
                )
                AND stock_batch.funding_source = $funding_source
                AND tbl_stock_master.temp = 0
                GROUP BY
                        itminfo_tab.itm_id
                ORDER BY
                        itminfo_tab.frmindex
";
    //Query result
    $qryRes = mysql_query($qry);
    //xml for chart
    $xmlstore = "<chart xAxisNamePadding='0' yAxisNamePadding='0' chartLeftMargin='0' chartRightMargin='0' chartTopMargin='0' chartBottomMargin='0' theme='fint' numberScaleValue='1000,1000,1000' numberScaleUnit='K,M,B' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$subCaption' exportFileName='$downloadFileName' yAxisName='Units' xAxisName='Products' showValues='1'>";
    //Populate xml
    while ( $row = mysql_fetch_array($qryRes) )
    {
        //name
        $data[$row['itm_id']]['name'] = $row['itm_name'];
        //issue
        @$data[$row['itm_id']]['issue'] = (is_null($row['Issue'])) ? 0 : $row['Issue'];
        //CB
        $data[$row['itm_id']]['CB'] = (is_null($row['CB'])) ? 0 : $row['CB'];
    }
    
    $xmlstore .= "<categories>";
    foreach( $data as $key=>$name )
    {
        //name
        $xmlstore .= "<category label='$name[name]' />";
    }
    $xmlstore .= "</categories>";
    
//    $xmlstore .= "<dataset seriesName='Issue'>";
//    foreach( $data as $key=>$name )
//    {
//        //issue
//        $xmlstore .= "<set value='$name[issue]' />";
//    }
//    $xmlstore .= "</dataset>";
    
    $xmlstore .= "<dataset seriesName='Stock on Hand'>";
    foreach( $data as $key=>$name )
    {
        //CB
        $xmlstore .= "<set value='$name[CB]' />";
    }
    $xmlstore .= "</dataset>";
    //end chart
    $xmlstore .= "</chart>";
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, $chart_id, '100%', 350, false, false);
    ?>
	</div>
</div>