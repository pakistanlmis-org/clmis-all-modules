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
$caption = "Quarter Wise Issue Trend - Last Year";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'issue_trend';
?>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        //Query for shipment main dashboard
        $qry_1 = "SELECT
                    itminfo_tab.itm_id,
                    itminfo_tab.itm_name
                    FROM
                    itminfo_tab
                    WHERE
                    itminfo_tab.itm_id in (1,2,3,9,5,7,8,13)
                ";
        $res_1 = mysql_query($qry_1);
        $itm_arr = array();
        while($row_1 = mysql_fetch_array($res_1))
        {
            $itm_arr[$row_1['itm_id']]=$row_1['itm_name'];
        }
        
        
        //Query for shipment main dashboard
	 $qry = "
                SELECT
                    itminfo_tab.itm_id as item_id,
                    Sum(ABS(tbl_stock_detail.Qty)) AS consumption,
                    itminfo_tab.itm_name,
                    YEAR(tbl_stock_master.TranDate) as y,
                    QUARTER(tbl_stock_master.TranDate) as q
                    FROM
                                            itminfo_tab
                                    INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
                                    INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                                    INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                                    
                    WHERE 

                    quarter(curdate() - INTERVAL 4 QUARTER) <= QUARTER(tbl_stock_master.TranDate) AND 
                    year(curdate() - INTERVAL 4 QUARTER) <=YEAR(tbl_stock_master.TranDate)
                    AND tbl_stock_master.TranTypeID = 2
                    GROUP BY
                    YEAR(tbl_stock_master.TranDate),
                    QUARTER(tbl_stock_master.TranDate),
                    itminfo_tab.itm_id
";
    //Query result
    $qryRes = mysql_query($qry);
    $d_arr = array();
    while($row = mysql_fetch_assoc($qryRes)) {
        $yq = 'Q'.$row['q']. ' ' .$row['y'];
        $d_arr[$yq][$row['item_id']]=$row['consumption'];
    }
    //echo '<pre>';print_r($d_arr);exit;
    //Render chart
    $xmlstore = '<chart caption="'.$caption.'" subcaption="For last Year" xaxisname="Products" exportEnabled="1" yaxisname="Units" showsum="1" numberprefix="" theme="fint">';
    
    $xmlstore .= '<categories>';
    foreach($itm_arr as $itm_id => $itm_name) {
        $xmlstore .= '<category label="'.$itm_name.'" />';
    }
    $xmlstore .= '</categories>';
    
    foreach($d_arr as $quarter => $val) {
        $xmlstore .= '<dataset seriesname="'.$quarter.'">';
        foreach($itm_arr as $itm_id => $itm_name) {
            $cons = (!empty($val[$itm_id])?$val[$itm_id]:'0');
            $xmlstore .= '  <set value="'.$cons.'" />';      
        }
        $xmlstore .= '</dataset>';
    }

    $xmlstore .= '</chart>';
    
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/StackedColumn2D.swf", "", $xmlstore, $chart_id, '100%', 450, false, false);
    ?>
	</div>
</div>