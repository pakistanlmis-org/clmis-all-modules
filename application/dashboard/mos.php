<?php
/**
 * mos
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
//login
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include functions
include APP_PATH . "includes/classes/functions.php";
//include fusion chart
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//funding source
$fundingSourceText = 'All Funding Sources';

header('Location: stock_summary.php');

if (isset($_REQUEST['from_date']) && isset($_REQUEST['to_date'])) {
    //get from date
    $fromDate = $_REQUEST['from_date'];
    //get to date
    $toDate = $_REQUEST['to_date'];
    //get funding source
    $fundingSource = $_REQUEST['funding_source'];
    //check funding source
    if ($fundingSource != 'all') {
        $and = " AND stock_batch.funding_source = " . $fundingSource . " ";
        //query
        //gets
        //wh name
        $qry = "SELECT
					tbl_warehouse.wh_name
				FROM
					tbl_warehouse
				WHERE
					tbl_warehouse.wh_id = $fundingSource";
        //query result
        $qryRes = mysql_fetch_array(mysql_query($qry));
        $fundingSourceText = 'Funding Source: ' . $qryRes['wh_name'];
    } else {
        $fundingSourceText = 'All Funding Sources';
    }
    //start date
    $startDate = date('Y-m-d', strtotime("-3 month", strtotime(dateToDbFormat($fromDate))));
    $avg_con_Date = date('d/m/Y', strtotime("-12 month", strtotime(dateToDbFormat($toDate))));
    //end date
    $endDate = dateToDbFormat($fromDate);
    $avg_con_Date1 = dateToDbFormat($avg_con_Date);
} else {
//to date	
    $toDate = date('d/m/Y');
//from date	
    $fromDate = date('d/m/Y', strtotime("-3 month", strtotime(dateToDbFormat($toDate))));
    $avg_con_Date = date('d/m/Y', strtotime("-12 month", strtotime(dateToDbFormat($toDate))));
    //start date
    $startDate = $startDate = date('Y-m-d', strtotime("-3 month", strtotime(dateToDbFormat($fromDate))));
    //end date
    $endDate = dateToDbFormat($fromDate);
    $avg_con_Date1 = dateToDbFormat($avg_con_Date);
}
//mos date
$mosDate = dateToDbFormat($toDate);

//get wh id
$whId = $_SESSION['user_warehouse'];
//get stk id
$stkId = $_SESSION['user_stakeholder'];
//subCaption
$subCaption = $fundingSourceText . '(' . $fromDate . ' to ' . $toDate . ')';
//downloadFileName
$downloadFileName = "MOS - " . $subCaption . ' - ' . date('Y-m-d H:i:s');
$chart_id = 'MOS';
?>
<!--[if IE]>
<style type="text/css">
    .box { display: block; }
    #box { overflow: hidden;position: relative; }
    b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
</style>

</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>

    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include top_im	
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="tabsbar">
                            <ul>
                                <li><a href="dashboard_cws.php"> <b>Main Dashboard</b></a></li>
                                <li><a href="shipment.php"> <b>Distribution and SOH</b></a></li>
                                <li><a href="expiry_schedule.php"> <b>Expiry Status</b></a></li>
                                <li><a href="stock_summary.php"> <b>Product Summary</b></a></li>
                                <li class="active"><a href="#"> <b>Month of Stock</b></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <form name="frm" id="frm" action="" method="post">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <label>Date From</label>
                                <div class="form-group">
                                    <input name="from_date" id="from_date" class="form-control input-sm" readonly value="<?php echo $fromDate; ?>" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label>Date To</label>
                                <div class="form-group">
                                    <input name="to_date" id="to_date" class="form-control input-sm" readonly value="<?php echo $toDate; ?>" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>Funding Source</label>
                                <div class="form-group">
                                    <select name="funding_source" id="funding_source" class="form-control input-sm">
                                        <option value="all">All</option>
                                        <?php
                                        //query 
                                        //gets
                                        //wh id
                                        //wh name
                                        $qry = "SELECT
                                                        tbl_warehouse.wh_id,
                                                        tbl_warehouse.wh_name
                                                FROM
                                                        stakeholder
                                                INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                                WHERE
                                                        stakeholder.stk_type_id = 2
                                                AND tbl_warehouse.is_active = 1
                                                ORDER BY
                                                        stakeholder.stkorder ASC";
                                        //query result
                                        $qryRes = mysql_query($qry);
                                        //fetch result
                                        while ($row = mysql_fetch_array($qryRes)) {
                                            $selected = ($row['wh_id'] == $fundingSource) ? 'selected' : '';
                                            //populate funding_source cpmbo
                                            echo "<option value=\"$row[wh_id]\" $selected>$row[wh_name]</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <div class="form-group">
                                    <button type="submit" id="search" value="search" class="btn btn-primary input-sm">Go</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget widget-tabs">
                            <div class="widget-body">
                                <a href="javascript:exportChart('<?php echo $chart_id; ?>', '<?php echo $downloadFileName; ?>')" style="float:right;"><img class="export_excel" src="<?php echo PUBLIC_URL; ?>images/excel-16.png" alt="Export" /></a>
                                    <?php
                                    //query
                                    //gets
                                    //item name
                                    //item type
                                      $qry = "SELECT
	itminfo_tab.itm_name,
	itminfo_tab.itm_type,
	ROUND(A.CB / A.avgCons, 1) AS MOS
        FROM
	(
		SELECT
			itminfo_tab.itm_id,
			SUM(tbl_stock_detail.Qty) AS CB,
			(
				SELECT
					SUM(summary_national.consumption)/12
				FROM
					summary_national
				INNER JOIN stakeholder ON summary_national.stakeholder_id = stakeholder.stkid
				INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
				INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
				WHERE
					summary_national.reporting_date Between '$avg_con_Date1' AND '" . dateToDbFormat($toDate) . "'
				AND summary_national.item_id = itminfo_tab.itmrec_id
				AND stakeholder.lvl = 1
				AND stakeholder.stkid = 1
				AND tbl_warehouse.wh_id = $whId
				GROUP BY
					summary_national.stakeholder_id
			) AS avgCons
		FROM
			itminfo_tab
		INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
		INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
		INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
		INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
		WHERE
			DATE_FORMAT(
				tbl_stock_master.TranDate,
				'%Y-%m-%d'
			) <= '$mosDate'
		AND (
			tbl_stock_master.WHIDFrom = $whId
			OR tbl_stock_master.WHIDTo = $whId
		)
		$and
		GROUP BY
			itminfo_tab.itm_id
		ORDER BY
			itminfo_tab.frmindex ASC
	) A
            RIGHT JOIN itminfo_tab ON itminfo_tab.itm_id = A.itm_id
            INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
            WHERE
                    itminfo_tab.itm_category = 1
            AND stakeholder_item.stkid = 1
            ORDER BY
                    itminfo_tab.frmindex";

                                    /* $qry2 = "SELECT
                                      itminfo_tab.itm_name,
                                      itminfo_tab.itm_type,
                                      ROUND(A.CB / A.avgCons, 1) AS MOS
                                      FROM
                                      (
                                      SELECT
                                      itminfo_tab.itm_id,
                                      SUM(tbl_stock_detail.Qty) AS CB,
                                      (
                                      SELECT
                                      ROUND(SUM(ABS(tbl_stock_detail.Qty)) / 3)
                                      FROM
                                      tbl_stock_master
                                      INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                      INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
                                      WHERE
                                      tbl_stock_master.TranTypeID = 2
                                      AND tbl_stock_master.TranDate BETWEEN '$startDate' AND '$endDate'
                                      AND tbl_stock_master.WHIDFrom = $whId
                                      AND stock_batch.item_id = itminfo_tab.itm_id
                                      ) AS avgCons
                                      FROM
                                      itminfo_tab
                                      INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
                                      INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                                      INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                                      INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
                                      WHERE
                                      DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$mosDate'
                                      AND (tbl_stock_master.WHIDFrom = $whId OR tbl_stock_master.WHIDTo = $whId)
                                      $and
                                      GROUP BY
                                      itminfo_tab.itm_id
                                      ORDER BY
                                      itminfo_tab.frmindex ASC
                                      ) A
                                      RIGHT JOIN itminfo_tab ON itminfo_tab.itm_id = A.itm_id
                                      INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                                      WHERE
                                      itminfo_tab.itm_category = 1
                                      AND stakeholder_item.stkid = 1
                                      ORDER BY
                                      itminfo_tab.frmindex"; */
                                    //query result
                                      //echo $qry;
                                    $qryRes = mysql_query($qry);
                                    $xmlstore = "<chart theme='fint' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='MOS at the end of selected time period' subCaption='$subCaption' exportFileName='$downloadFileName' yAxisName='Months' xAxisName='Products' showValues='1'>";
                                    while ($row = mysql_fetch_array($qryRes)) {
                                        $xmlstore .= "<set label='$row[itm_name]' value='$row[MOS]' />";
                                    }
                                    $xmlstore .= "</chart>";
                                    //include chart
                                    FC_SetRenderer('javascript');
                                    echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Column2D.swf", "", $xmlstore, $chart_id, '100%', 500, false, false);
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script>
        $(function () {
            var startDateTextBox = $('#from_date');
            var endDateTextBox = $('#to_date');

            startDateTextBox.datepicker({
                minDate: "-10Y",
                maxDate: 0,
                dateFormat: 'dd/mm/yy',
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                onClose: function (dateText, inst) {
                    if (endDateTextBox.val() != '') {
                        var testStartDate = startDateTextBox.datepicker('getDate');
                        var testEndDate = endDateTextBox.datepicker('getDate');
                        if (testStartDate > testEndDate)
                            endDateTextBox.datepicker('setDate', testStartDate);
                    } else {
                        endDateTextBox.val(dateText);
                    }
                },
                onSelect: function (selectedDateTime) {
                    endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
                }
            });
            endDateTextBox.datepicker({
                maxDate: 0,
                dateFormat: 'dd/mm/yy',
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                onClose: function (dateText, inst) {
                    if (startDateTextBox.val() != '') {
                        var testStartDate = startDateTextBox.datepicker('getDate');
                        var testEndDate = endDateTextBox.datepicker('getDate');
                        if (testStartDate > testEndDate)
                            startDateTextBox.datepicker('setDate', testEndDate);
                    } else {
                        startDateTextBox.val(dateText);
                    }
                },
                onSelect: function (selectedDateTime) {
                    startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
                }
            });
        })
    </script>
</body>
<!-- END BODY -->
</html>