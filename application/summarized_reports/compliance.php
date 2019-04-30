<?php
//report id
$report_id = "CD";
//check if submitted
if (isset($_REQUEST['submit'])) {
    //Posted Data Collection
    //selected month
    $selMonth = !empty($_REQUEST['ending_month']) ? $_REQUEST['ending_month'] : '';
    //selected year
    $selYear = !empty($_REQUEST['year_sel']) ? $_REQUEST['year_sel'] : '';
    //selected stakeholder
    $selStk = !empty($_REQUEST['stk_sel']) ? $_REQUEST['stk_sel'] : '';
    //selected province
    $selPro = !empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '';
} else {
    if (date('d') > 10) {
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    //selected month
    $selMonth = date('m', strtotime($date));
    //selected year
    $selYear = date('Y', strtotime($date));
    //selected province
    $selPro = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //selected province
    $selPro = ($selPro != 10) ? $selPro : 1;
    //selected stakeholder
    $selStk = $_SESSION['user_stakeholder1'];
}
//date
$date = '';
//end date 1
$endDate1 = $selYear . '-' . ($selMonth) . '-01';
$endDate = date('Y-m-d', strtotime("-1 days", strtotime("+1 month", strtotime($endDate1))));
$startDate = date('Y-m-d', strtotime("-11 month", strtotime($endDate1)));

$header = '';
$header1 = '';
$cspan1 = '';
$cspan = '';
$width = '';
$ro = '';
//begin
$begin = new DateTime($startDate);
//end
$end = new DateTime($endDate);
//difference
$diff = $begin->diff($end);
//interval
$interval = DateInterval::createFromDateString('1 month');
//period
$period = new DatePeriod($begin, $interval, $end);
//data array
$dataArr = array();
//select query
//district id
//location name
//stakeholder name
$qry = "SELECT DISTINCT
			tbl_warehouse.dist_id,
			tbl_locations.LocName,
			stakeholder.stkname
		FROM
			tbl_warehouse
		INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
		INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
		INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
		WHERE
			tbl_warehouse.stkid = $selStk
		AND tbl_warehouse.prov_id = $selPro
		AND tbl_warehouse.is_active = 1
		GROUP BY
			tbl_warehouse.dist_id
		ORDER BY
			tbl_locations.LocName ASC";
//query result
$getDistricts = mysql_query($qry);
//fetch result
while ($row = mysql_fetch_array($getDistricts)) {
    //stakeholder name
    $stkname = $row['stkname'];
//district name
    $districtName[$row['dist_id']] = $row['LocName'];
    //period
    foreach ($period as $date) {
        //cmw
        $cmwArr[$row['dist_id']][$date->format("Y-m-d")] = 0;
        //district
        $districtArr[$row['dist_id']][$date->format("Y-m-d")] = 'No';
    }
}
//period
foreach ($period as $date) {
    $cspan1 .= ',#cspan';
    $header1 .= ',' . $date->format("M");
    $width .= ',37';
    $ro .= ',ro';
}
$cspan = '#cspan' . $cspan1 . $cspan1;
$header = '#rspan,#rspan' . $header1 . $header1;
$width = '60,*' . $width . $width;
$ro = 'ro,ro' . $ro . $ro;
//select query
//district id
//reporting date
//reported
//reported per
$qry = "SELECT
			B.PkLocID AS dist_id,
			A.reporting_date,
			A.Reported,
			ROUND(((A.Reported / A.Total) * 100), 1) AS ReportedPer
		FROM
			(
				SELECT
					tbl_warehouse.dist_id,
					COUNT(DISTINCT tbl_hf_data.warehouse_id) AS Reported,
					tbl_hf_data.reporting_date,
					(SELECT REPGetTotalWarehousesByMonth (tbl_hf_data.reporting_date, $selStk, tbl_warehouse.dist_id)) AS Total
				FROM
					tbl_warehouse
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
				WHERE
					tbl_warehouse.stkid = $selStk
				AND tbl_warehouse.prov_id = $selPro
				AND stakeholder.lvl = 7
				AND tbl_warehouse.is_active = 1
				AND tbl_hf_data.reporting_date BETWEEN '$startDate' AND '$endDate'
				GROUP BY
					tbl_warehouse.dist_id,
					tbl_hf_data.reporting_date
				ORDER BY
					tbl_hf_data.reporting_date ASC
			) A
		JOIN (
			SELECT
				tbl_locations.PkLocID
			FROM
				tbl_locations
			WHERE
				tbl_locations.ParentID = $selPro
			AND tbl_locations.LocLvl = 3
		) B ON A.dist_id = B.PkLocID";
//query result
//echo $qry;exit;
$qryRes = mysql_query($qry);
//fetch result
while ($row = mysql_fetch_array($qryRes)) {
    //reported per array
    $rr = ($row['ReportedPer'] > 100) ? 100 : $row['ReportedPer'];
    //cmw array
    $cmwArr[$row['dist_id']][$row['reporting_date']] = $rr;
}
//select query
//gets
//district id
//report date
//reporting status
$qry = "SELECT
			B.dist_id,
			A.RptDate,
			IF (A.RptDate IS NULL, 'No', 'Yes') AS ReportingStatus
		FROM
			(
				SELECT DISTINCT
					tbl_warehouse.dist_id,
					tbl_wh_data.RptDate
				FROM
					tbl_warehouse
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
				WHERE
					tbl_warehouse.stkid = $selStk
				AND tbl_warehouse.prov_id = $selPro
				AND stakeholder.lvl = 3
				AND tbl_wh_data.RptDate BETWEEN '$startDate' AND '$endDate'
			) A
		RIGHT JOIN (
			SELECT DISTINCT
				tbl_warehouse.dist_id
			FROM
				tbl_warehouse
			WHERE
				tbl_warehouse.stkid = $selStk
			AND tbl_warehouse.prov_id = $selPro
		) B ON A.dist_id = B.dist_id";
//query result
$qryRes = mysql_query($qry);
//fetch result
while ($row = mysql_fetch_array($qryRes)) {
//district array
    $districtArr[$row['dist_id']][$row['RptDate']] = $row['ReportingStatus'];
}
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$i = 1;
foreach ($cmwArr as $key => $subArr) {
    $xmlstore .= "<row>";
    $xmlstore .= "<cell style=\"text-align:center\">" . $i++ . "</cell>";
    $xmlstore .= "<cell><![CDATA[" . $districtName[$key] . "]]></cell>";
    // CMW
    foreach ($subArr as $cmwData) {
        $xmlstore .= "<cell style=\"text-align:right\">" . $cmwData . "</cell>";
    }
    // District Store
    foreach ($districtArr[$key] as $distData) {
        $color = ($distData == 'No') ? 'color:#EE2000' : '';
        $xmlstore .= "<cell style=\"text-align:center;$color\">" . $distData . "</cell>";
    }
    $xmlstore .="</row>";
}
$xmlstore .="</rows>";
?>
<style>
    /*div.gridbox table.hdr td{text-align:center !important;}*/

    div.gridbox_light table.hdr td div.hdrcell {
        padding-left: 0px !important;
        text-align: center !important;
    }
</style>
</head><!-- END HEAD -->

<div onLoad="doInitGrid()">
   
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            
                           
                            <div class="row">
                                <div class="col-md-12">
                                    <table width="100%" cellpadding="0" cellspacing="0" id="myTable">
                                        <tr>
                                            <td align="right" style="padding-right:5px;"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/> <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></td>
                                        </tr>
                                        <tr>
                                            <td><div id="mygrid_container" style="width:100%; height:390px;"></div></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
        </div>
    </div>

    <script>
        var mygrid;
        function doInitGrid()
        {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'><?php echo $stkname . ' Compliance Report From ' . date('M Y', strtotime($startDate)) . ' to ' . date('M Y', strtotime($endDate)); ?></div>,<?php echo $cspan; ?>");
            mygrid.attachHeader("Sr. No.,District,<div style='text-align:center;'><?php echo ($selStk == 73) ? 'CMW Name' : 'Facility';?> Data Reporting Compliance (%)</div><?php echo substr($cspan1, 0, -7); ?>,<div style='text-align:center;'>District Store Data (Yes/No)</div><?php echo substr($cspan1, 0, -7); ?>");
            mygrid.attachHeader("<?php echo $header; ?>");
            mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,<?php echo $cspan; ?>");
            mygrid.setInitWidths("<?php echo $width; ?>");
            mygrid.setColTypes("<?php echo $ro; ?>");
            mygrid.enableRowsHover(true, 'onMouseOver'); // `onMouseOver` is the css cla ss name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
		function showProvinces(pid) {
			var stk = $('#stk_sel').val();
			if (typeof stk !== 'undefined')
			{
				$.ajax({
					url: 'ajax_stk.php',
					type: 'POST',
					data: {stakeholder: stk, provinceId: pid, showProvinces: 1, hfProvOnly: 1},
					success: function(data) {
						$('#prov_sel').html(data);
					}
				})
			}
		}
		$(function() {
			$('#stk_sel').change(function(e) {
				$('#prov_sel').html('<option value="">Select</option>');
				showProvinces('');
			});
		})
		<?php
		if (isset($selPro) && !empty($selPro)) {
			?>
				showProvinces('<?php echo $selPro; ?>');
			<?php
		}
		?>
    </script>
</div>
