<?php
/**
 * stock_summary
 * @package dashboard
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include configuration
include("../includes/classes/Configuration.inc.php");
//login
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//functions
include APP_PATH . "includes/classes/functions.php";
//header
include(PUBLIC_PATH . "html/header.php");
//wh id
$whId = $_SESSION['user_warehouse'];
//stk id
$stkId = $_SESSION['user_stakeholder'];
$and = '';

if (isset($_REQUEST['from_date']) && isset($_REQUEST['to_date'])) {
    //from date
    $fromDate = $_REQUEST['from_date'];
    //to date
    $toDate = $_REQUEST['to_date'];
    //funding source
    $fundingSource = $_REQUEST['funding_source'];
    //check funding source
    if ($fundingSource != 'all') {
        $and = " AND stock_batch.funding_source = " . $fundingSource . " ";
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
//select query
//gets
//B.itm_id,
//itm_name,
//itm_type,
//OB,
//Rcv,
//Issue,
//AdjPos,
//AdjNeg,
//CB
$qry = "SELECT
			B.itm_id,
			B.itm_name,
			B.itm_type,
			A.OB,
			A.Rcv,
			A.Issue,
			A.AdjPos,
			A.AdjNeg,
			A.CB
		FROM
		(
			SELECT
				itminfo_tab.itm_id,
				SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') < '$startDate', tbl_stock_detail.Qty, 0)) AS OB,
				SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDate' AND tbl_stock_master.TranTypeID = 1, tbl_stock_detail.Qty, 0)) AS Rcv,
				SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDate' AND tbl_stock_master.TranTypeID = 2, ABS(tbl_stock_detail.Qty), 0)) AS Issue,
				SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDate' AND tbl_stock_master.TranTypeID > 2 AND tbl_trans_type.trans_nature = '+', tbl_stock_detail.Qty, 0)) AS AdjPos,
				ABS(SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDate' AND tbl_stock_master.TranTypeID > 2 AND tbl_trans_type.trans_nature = '-', tbl_stock_detail.Qty, 0))) AS AdjNeg,
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
        echo $qry;
        exit;
//query result
$qryRes = mysql_query($qry);
$num = mysql_num_rows($qryRes);
//xml
$xmlstore = '<?xml version="1.0"?><rows>';
$i = 1;
while ($row = mysql_fetch_array($qryRes)) {

    $itemName = $row['itm_name'];
    $xmlstore .= '<row>';

    $xmlstore .= '<cell>' . $i++ . '</cell>';
    //item name
    $xmlstore .= '<cell>' . $row['itm_name'] . '</cell>';
    //item type
    $xmlstore .= '<cell>' . $row['itm_type'] . '</cell>';
    //Opening Balance
    $xmlstore .= '<cell>' . number_format($row['OB']) . '</cell>';
    //Receive
    $xmlstore .= '<cell>' . number_format($row['Rcv']) . '</cell>';
    //Issue
    $xmlstore .= '<cell>' . number_format($row['Issue']) . '</cell>';
    //Adj +    
    $xmlstore .= '<cell>' . number_format($row['AdjPos']) . '</cell>';
    //Adj -
    $xmlstore .= '<cell>' . number_format($row['AdjNeg']) . '</cell>';
    //Closing balance
    $xmlstore .= '<cell>' . number_format($row['CB']) . '</cell>';
    $xmlstore .= '</row>';
}
$xmlstore .= '</rows>';
//end xml
?>
<link rel="STYLESHEET" type="text/css" href="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxgrid.css">
<style>
    .objbox{overflow-x:hidden !important;}
</style>
<script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
<script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
<script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
<script src='<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/client/dhtmlxgrid_export.js'></script>
<script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_link.js"></script>

<!--[if IE]>
<style type="text/css">
    .box { display: block; }
    #box { overflow: hidden;position: relative; }
    b { position: absolute; top: 0px; right: 0px; width:1px; height: 251px; overflow: hidden; text-indent: -9999px; }
</style>

</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->


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
                                <li><a href="shipment.php"> <b>Distribution and SOH</b></a></li>
                                <li><a href="expiry_schedule.php"> <b>Expiry Status</b></a></li>
                                <li class="active"><a href="#"> <b>Product Summary</b></a></li>
                                <li><a href="mos.php"> <b>Month of Stock</b></a></li>
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
                                        //select query
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
                                        //result
                                        $qryRes = mysql_query($qry);
                                        while ($row = mysql_fetch_array($qryRes)) {
                                            $selected = ($row['wh_id'] == $fundingSource) ? 'selected' : '';
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
                        <div class="widget-head" style="padding:0px !important;">
                            <?php
                            if ($num > 0) {
                                ?>
                                <table width="100%">
                                    <tr>
                                        <td style="text-align:right;">
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div id="mygrid_container" style="width:100%; height:360px;"></div>
                                        </td>
                                    </tr>
                                </table>
                                <?php
                            } else {
                                echo "No record found.";
                            }
                            ?>
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
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold;'>Stock Summary (<?php echo $fromDate . '  to' . $toDate; ?>)</div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("<span>Sr. No.</span>,<span>Product</span>,<span>Unit</span>,<span>Opening Quantity</span>,<span>Received Quantity</span>,<span>Issued Quantity</span>,<div style='text-align:center;'>Adjustments</div>,#cspan,<span>Closing Quantity</span>");
            mygrid.attachHeader("#rspan,#rspan,#rspan,#rspan,#rspan,#rspan,<div style='text-align:center;'>(+)</div>, <div style='text-align:center !important;'>(-)</div>,#rspan");
            mygrid.setInitWidths("60,*,80,130,130,130,100,100,130");
            mygrid.setColAlign("center,left,left,right,right,right,right,right,right");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");

            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>')
        }

    </script>
    <script type="text/javascript">
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