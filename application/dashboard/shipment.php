<?php
/**
 * shipment
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
//include  functions
include APP_PATH . "includes/classes/functions.php";
//include fusion chart
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//funding source
$fundingSourceText = 'All Funding Sources';

if (isset($_REQUEST['from_date']) && isset($_REQUEST['to_date'])) {
//from date	
    $fromDate = $_REQUEST['from_date'];
//to date	
    $toDate = $_REQUEST['to_date'];
//funding source	
    $fundingSource = $_REQUEST['funding_source'];
//checking funding source	
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
    //end date
    $endDate = dateToDbFormat($_REQUEST['to_date']);
    //start date
    $startDate = dateToDbFormat($_REQUEST['from_date']);
} else {
//to date	
    $toDate = date('d/m/Y');
//from date	
    $fromDate = date('01/m/Y', strtotime("-2 month", strtotime(date('Y-m-d'))));
    //end date
    $endDate = date('Y-m-d');
    //start date
    $startDate = date('Y-m-01', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
//get wh id
$whId = $_SESSION['user_warehouse'];
//get stk id
$stkId = $_SESSION['user_stakeholder'];
//caption
$caption = 'Product wise Distribution and SOH';
//sub caption
$subCaption = $fundingSourceText . '(' . $fromDate . ' to ' . $toDate . ')';
//downloadFileName 
$downloadFileName = $caption . ' - ' . $subCaption . ' - ' . date('Y-m-d H:i:s');
//chart id
$chart_id = 'distributionAndSOH';
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
                                <li class="active"><a href="#"> <b>Distribution and SOH</b></a></li>
                                <li><a href="expiry_schedule.php"> <b>Expiry Status</b></a></li>
                                <li><a href="stock_summary.php"> <b>Product Summary</b></a></li>
                                <!--<li><a href="mos.php"> <b>Month of Stock</b></a></li>-->
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
										//selects
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
                                            //populate funding_source combo
                                            echo "<option value=\"$row[wh_id]\" $selected>$row[wh_name]</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <div class="form-group">
                                    <button type="submit" id="expiry_search" value="search" class="btn btn-primary input-sm">Go</button>
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
//item id
//item name
//issue
//received
                                     $qry = "SELECT
										B.itm_id,
										B.itm_name,
										ROUND(A.CB / B.qty_carton) AS CB,
										ROUND(A.Issue / B.qty_carton) AS Issue,
										ROUND(A.Rcv / B.qty_carton) AS Rcv
									FROM
									(
										SELECT
											itminfo_tab.itm_id,
											SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') < '$startDate', tbl_stock_detail.Qty, 0)) AS OB,
											SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDate' AND tbl_stock_master.TranTypeID = 1, tbl_stock_detail.Qty, 0)) AS Rcv,
											SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDate' AND tbl_stock_master.TranTypeID = 2, ABS(tbl_stock_detail.Qty), 0)) AS Issue,
											SUM(tbl_stock_detail.Qty) AS CB
										FROM
											itminfo_tab
										INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
										INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
										INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
										INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
										WHERE
											DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDate'
										AND (tbl_stock_master.WHIDFrom = $whId OR tbl_stock_master.WHIDTo = $whId)
                                                                                AND tbl_stock_master.temp=0
										$and
										GROUP BY
											itminfo_tab.itm_id
									) A
								RIGHT JOIN (
									SELECT
										itminfo_tab.itm_id,
										itminfo_tab.itm_name,
										itminfo_tab.qty_carton,
										itminfo_tab.itm_type,
										itminfo_tab.frmindex
									FROM
										itminfo_tab
									INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
									WHERE
										stakeholder_item.stkid = $stkId
									AND itminfo_tab.itm_category = 1
								) B ON A.itm_id = B.itm_id
								ORDER BY
									B.frmindex";
//query result
                                    $qryRes = mysql_query($qry);
//fetch result
                                    while ($row = mysql_fetch_array($qryRes)) {
                                        //name
                                        $data[$row['itm_id']]['name'] = $row['itm_name'];
                                        //issue
                                        $data[$row['itm_id']]['issue'] = $row['Issue'];
                                        //receive
                                        $data[$row['itm_id']]['rcv'] = $row['Rcv'];
                                        //CB
                                        $data[$row['itm_id']]['CB'] = $row['CB'];
                                    }
                                    //xml
                                    $xmlstore = "<chart theme='fint' formatNumberScale='0' labelDisplay='rotate' slantLabels='1' exportEnabled='1' exportAction='Download' caption='$caption' subCaption='$subCaption' exportFileName='$downloadFileName' yAxisName='No. of Cartons' xAxisName='Products' showValues='1'>";

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

                                    $xmlstore .= "<dataset seriesName='Receive'>";
                                    foreach ($data as $key => $name) {
                                        //receive
                                        $xmlstore .= "<set value='$name[rcv]' />";
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
                                    echo renderChart(PUBLIC_URL . "FusionCharts/Charts/MSColumn2D.swf", "", $xmlstore, $chart_id, '100%', 500, false, false);
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
    include PUBLIC_PATH . "/html/footer.php"; ?>
    <script>
        $(function() {
            var startDateTextBox = $('#from_date');
            var endDateTextBox = $('#to_date');

            startDateTextBox.datepicker({
                minDate: "-10Y",
                maxDate: 0,
                dateFormat: 'dd/mm/yy',
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                onClose: function(dateText, inst) {
                    if (endDateTextBox.val() != '') {
                        var testStartDate = startDateTextBox.datepicker('getDate');
                        var testEndDate = endDateTextBox.datepicker('getDate');
                        if (testStartDate > testEndDate)
                            endDateTextBox.datepicker('setDate', testStartDate);
                    }
                    else {
                        endDateTextBox.val(dateText);
                    }
                },
                onSelect: function(selectedDateTime) {
                    endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
                }
            });
            endDateTextBox.datepicker({
                maxDate: 0,
                dateFormat: 'dd/mm/yy',
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                onClose: function(dateText, inst) {
                    if (startDateTextBox.val() != '') {
                        var testStartDate = startDateTextBox.datepicker('getDate');
                        var testEndDate = endDateTextBox.datepicker('getDate');
                        if (testStartDate > testEndDate)
                            startDateTextBox.datepicker('setDate', testEndDate);
                    }
                    else {
                        startDateTextBox.val(dateText);
                    }
                },
                onSelect: function(selectedDateTime) {
                    startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
                }
            });
        })
    </script>
</body>
<!-- END BODY -->
</html>