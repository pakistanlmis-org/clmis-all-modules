<?php
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';
if (!empty($_POST['year']) )
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
$stkId = 1;

//Chart caption
$caption = "National( USAID) & Provincial MOS";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'mos';
?>
<div class="widget widget-tabs">
    <div class="widget-body">
        <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
        <?php
        //query
        //gets
        //item name
        //item type
        $qry = "SELECT DISTINCT
	itminfo_tab.itm_name,
	A.wh_name,
	itminfo_tab.itm_type,
	ROUND(A.CB / A.avgCons, 1) AS MOS,
	itminfo_tab.itm_id
FROM
	(
		SELECT
			tbl_warehouse.wh_name,
			itminfo_tab.itm_id,
			SUM(tbl_stock_detail.Qty) AS CB,
			(
				SELECT
					SUM(
						summary_national.consumption
					) / 12
				FROM
					summary_national
				INNER JOIN stakeholder ON summary_national.stakeholder_id = stakeholder.stkid
				INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
				INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
				WHERE
					DATE_FORMAT(
						summary_national.reporting_date,
						'%Y-%m-%d'
					) BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
				AND CURDATE()
				AND summary_national.item_id = itminfo_tab.itmrec_id
				AND stakeholder.lvl = 1
				AND tbl_warehouse.wh_id = 123
				GROUP BY
					summary_national.stakeholder_id,
					stakeholder.stkid
			) AS avgCons
		FROM
			itminfo_tab
		INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
		INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
		INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
		INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
		INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = stock_batch.funding_source
		WHERE
			DATE_FORMAT(
				tbl_stock_master.TranDate,
				'%Y-%m-%d'
			) <= CURDATE()
		AND (
			tbl_stock_master.WHIDFrom = 123
			OR tbl_stock_master.WHIDTo = 123
		)
		GROUP BY
			itminfo_tab.itm_id,
			stock_batch.funding_source
		ORDER BY
			itminfo_tab.frmindex ASC
	) A
LEFT JOIN itminfo_tab ON itminfo_tab.itm_id = A.itm_id
INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
WHERE
	itminfo_tab.itm_category = 1
ORDER BY
	itminfo_tab.frmindex";
//echo $qry;
        $qryRes = mysql_query($qry);
        $itm_arr = $mos_arr = array();
        while ($row = mysql_fetch_array($qryRes)) {
            $itm_arr[$row['itm_id']] = $row['itm_name'];
            $mos_arr[$row['wh_name']][$row['itm_name']] = 0;
            $mos_arr[$row['wh_name']][$row['itm_name']] = $row['MOS'];
        }
        //echo "<pre>";
        //print_r($itm_arr);
        //print_r($mos_arr);
        //xml for chart
        $xmlstore = "<chart xAxisNamePadding='0' yAxisNamePadding='0' chartLeftMargin='0' chartRightMargin='0' chartTopMargin='0' chartBottomMargin='0' theme='fint' numberScaleValue='1000,1000,1000' numberScaleUnit='K,M,B' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$subCaption' exportFileName='$downloadFileName' yAxisName='Months' xAxisName='Products' showValues='0'>";



        $xmlstore .= "<categories>";
        foreach( $itm_arr as $itm_id=>$itm_name )
        {
            $xmlstore .= "<category label='$itm_name' ></category>";
        }
        $xmlstore .= "</categories>";


        foreach( $mos_arr as $wh_name=>$val )
        {
            $xmlstore .= "<dataset seriesName='".$wh_name."'>";
            foreach( $itm_arr as $itm_id=>$itm_name )
            {
                if(!empty($val[$itm_name])){
                    $xmlstore .= "<set value='".number_format($val[$itm_name],2)."' ></set>";
                } else {
                    $xmlstore .= "<set value='0' ></set>";
                }

            }
            $xmlstore .= "</dataset>";
        }


        $xmlstore .= "</chart>";
        //echo $xmlstore;
        //Render chart
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, $chart_id, '100%', 450, false, false);
        ?>
    </div>
</div>