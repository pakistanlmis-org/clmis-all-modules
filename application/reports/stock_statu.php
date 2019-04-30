<?php
/**
 * stock_status
 * @package reports
 * 
 * @author     Ajmal Hussain 
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses
include("../includes/classes/AllClasses.php");
//Including FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//Including header
include(PUBLIC_PATH . "html/header.php");
//Initialing variable report_id
$report_id = "STOCKISSUANCE";
//Checking date
if (date('d') > 10) {
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
$selMonth = date('m', strtotime($date));
$selYear = date('Y', strtotime($date));
//Initialing variables
$date_from = $date_to = $product = $provinceID = $district = $stakeholder = $warehouse = $xmlstore = $selProv = '';
//Checking search
if (isset($_REQUEST['search'])) {
    //Getting date_from
    $date_from = $_REQUEST['date_from'];
    //Getting date_to
    $date_to = $_REQUEST['date_to'];
    //Setting date_from
    $dateFrom = dateToDbFormat($date_from);
    //Setting dateTo
    $dateTo = dateToDbFormat($date_to);
    //Getting product
    $product = $_REQUEST['product'];
    //Getting stakeholder
    $stakeholder = $_REQUEST['stakeholder'];
    //Getting province
    $provinceID = $_REQUEST['province'];
    //Getting district
    $district = $_REQUEST['district'];
    //Getting warehouse
    $warehouse = $_REQUEST['warehouse'];
    //Setting filters
    $where = 'tbl_stock_master.WHIDFrom = 123';
    $where .= (!empty($_REQUEST['date_from']) && !empty($_REQUEST['date_to'])) ? " AND tbl_stock_master.TranDate BETWEEN '$dateFrom' AND '$dateTo' " : '';
    $where .= (!empty($product) && $product != 'all') ? " AND stock_batch.item_id = $product" : '';
    $where .= (!empty($stakeholder) && $stakeholder != 'all') ? " AND tbl_warehouse.stkid = $stakeholder" : '';
    $where .= (!empty($provinceID) && $provinceID != 'all') ? " AND tbl_warehouse.prov_id = $provinceID" : '';
    $where .= (!empty($district)) ? " AND tbl_warehouse.dist_id = $district" : '';
    $where .= (!empty($warehouse) && $warehouse != 'all') ? " AND tbl_stock_master.WHIDTo = $warehouse" : '';

    //This query gets
    //TranDate
    //TranNo
    //TranRef
    //itm_name
    //batch_no
    //batch_expiry
    //stkname
    //whName
    //dist_id
    //prov_id
    //Qty
    //District
    //Province
    $qry = "SELECT
				DATE_FORMAT(tbl_stock_master.TranDate, '%d/%m/%Y') AS TranDate,
				tbl_stock_master.TranNo,
				tbl_stock_master.TranRef,
				itminfo_tab.itm_name,
				stock_batch.batch_no,
				DATE_FORMAT(stock_batch.batch_expiry, '%d/%m/%Y') AS batch_expiry,
				MainStk.stkname,
				CONCAT(tbl_warehouse.wh_name, ' (', stakeholder.stkname, ')') AS whName,
				tbl_warehouse.dist_id,
				tbl_warehouse.prov_id,
				SUM(ABS(tbl_stock_detail.Qty)) AS Qty,
				District.LocName AS District,
				Province.LocName AS Province
			FROM
				tbl_stock_master
			INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
			INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
			INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
			INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = tbl_stock_master.WHIDTo
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN stakeholder AS MainStk ON tbl_warehouse.stkid = MainStk.stkid
			INNER JOIN tbl_locations AS District ON tbl_warehouse.dist_id = District.PkLocID
			INNER JOIN tbl_locations AS Province ON tbl_warehouse.prov_id = Province.PkLocID
			WHERE
				$where
			GROUP BY
				stock_batch.item_id,
				stock_batch.batch_no,
				tbl_warehouse.wh_id,
				tbl_stock_master.PkStockID
			ORDER BY
				tbl_stock_master.TranRef ASC";
    //Query result
    $rows = mysql_query($qry);
    $num = mysql_num_rows(mysql_query($qry));
    //xml
    $xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xmlstore .= "<rows>";
    $counter = 1;
    //Populate xmlstore
    while ($row = mysql_fetch_array($rows)) {
        $xmlstore .= "<row>";
        //
        $xmlstore .= "<cell>" . $counter++ . "</cell>";
        //TranDate
        $xmlstore .= "<cell>" . $row['TranDate'] . "</cell>";
        //TranRef
        $xmlstore .= "<cell>" . $row['TranRef'] . "</cell>";
        //TranNo
        $xmlstore .= "<cell>" . $row['TranNo'] . "</cell>";
        //itm_name
        $xmlstore .= "<cell>" . $row['itm_name'] . "</cell>";
        //batch_no
        $xmlstore .= "<cell>" . $row['batch_no'] . "</cell>";
        //batch_expiry
        $xmlstore .= "<cell>" . $row['batch_expiry'] . "</cell>";
        //stkname
        $xmlstore .= "<cell>" . $row['stkname'] . "</cell>";
        //whName
        $xmlstore .= "<cell><![CDATA[" . $row['whName'] . "]]></cell>";
        //District
        $xmlstore .= "<cell>" . $row['District'] . "</cell>";
        //Province
        $xmlstore .= "<cell>" . $row['Province'] . "</cell>";
        //Qty
        $xmlstore .= "<cell>" . number_format($row['Qty']) . "</cell>";
        $xmlstore .= "</row>";
    }
    //End xml
    $xmlstore .= "</rows>";
}
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <div class="page-container">
<?php 
//Including top
include PUBLIC_PATH . "html/top.php";
//Including top_im
include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Stock Issuance Status Report</h3>
                        <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <div class="row">
                                    <form method="POST" name="frm" id="frm" action="">
                                        <!-- Row -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Date From</label>
                                                        <input type="text" readonly class="form-control input-sm" name="date_from" id="date_from" value="<?php echo $date_from; ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Date To</label>
                                                        <input type="text" readonly class="form-control input-sm" name="date_to" id="date_to" value="<?php echo $date_to; ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Product</label>
                                                        <select name="product" id="product" class="form-control input-sm" required="required">
                                                            <option value="">Select</option>
                                                            <option value="all" <?php echo ($product == 'all') ? 'selected="selected"' : ''; ?>>All</option>
<?php
//Product query
//gets
//itm_id
//itm_name
$qry = "SELECT
																	itminfo_tab.itm_id,
																	itminfo_tab.itm_name
																FROM
																	itminfo_tab
																INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
																WHERE
																	stakeholder_item.stkid = " . $_SESSION['user_stakeholder1'] . "
																AND itminfo_tab.itm_category = 1
																ORDER BY
																	itminfo_tab.frmindex";
$qryRes = mysql_query($qry);
if ($qryRes != FALSE) {
    while ($row = mysql_fetch_object($qryRes)) {
        ?>
                                                                    <?php //Populate product combo?>
                                                                    <option value="<?php echo $row->itm_id; ?>" <?php echo ($product == $row->itm_id) ? 'selected="selected"' : ''; ?>><?php echo $row->itm_name; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Stakeholder</label>
                                                        <select name="stakeholder" id="stakeholder" class="form-control input-sm" required="required">
                                                            <option value="">Select</option>
                                                            <option value="all" <?php echo ($stakeholder == 'all') ? 'selected="selected"' : ''; ?>>All</option>
<?php
//stakeholder query
//gets
//stkid
//stkname
$qry = "SELECT
																	stakeholder.stkid,
																	stakeholder.stkname
																FROM
																	stakeholder
																WHERE
																	stakeholder.ParentID IS NULL
																AND stakeholder.stk_type_id IN (0, 1)
																ORDER BY
																	stakeholder.stkorder ASC";
$qryRes = mysql_query($qry);
if ($qryRes != FALSE) {
    while ($row = mysql_fetch_object($qryRes)) {
        ?>
                                                                    <?php //Populate stakeholder combo?>
                                                                    <option value="<?php echo $row->stkid; ?>" <?php echo ($stakeholder == $row->stkid) ? 'selected="selected"' : ''; ?>><?php echo $row->stkname; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Province</label>
                                                        <select name="province" id="province" class="form-control input-sm" required="required">
                                                            <option value="">Select</option>
                                                            <option value="all" <?php echo ($provinceID == 'all') ? 'selected="selected"' : ''; ?>>All</option>
<?php
//Province query
//gets
//Province id
//Province name
$qry = "SELECT
																	tbl_locations.PkLocID,
																	tbl_locations.LocName
																FROM
																	tbl_locations
																WHERE
																	tbl_locations.LocLvl = 2
																AND tbl_locations.ParentID IS NOT NULL";
$qryRes = mysql_query($qry);
if ($qryRes != FALSE) {
    while ($row = mysql_fetch_object($qryRes)) {
        ?>
                                                                    <?php //Populate province combo?>
                                                                    <option value="<?php echo $row->PkLocID; ?>" <?php echo ($provinceID == $row->PkLocID) ? 'selected="selected"' : ''; ?>><?php echo $row->LocName; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group" id="districtsCol">
                                                        <label class="control-label">District</label>
                                                        <select name="district" id="district" class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option value="" <?php echo ($district == '') ? 'selected="selected"' : ''; ?>>All</option>
<?php
//District query
//gets
//District id
//District name
$qry = "SELECT
																	tbl_locations.PkLocID,
																	tbl_locations.LocName
																FROM
																	tbl_locations
																WHERE
																	tbl_locations.LocLvl = 3
																AND tbl_locations.ParentID = '$selProv'";
$qryRes = mysql_query($qry);
if ($qryRes != FALSE) {
    while ($row = mysql_fetch_object($qryRes)) {
        ?>                              
                                                                    <?php //Populate District combo?>
                                                                    <option value="<?php echo $row->PkLocID; ?>" <?php echo ($district == $row->PkLocID) ? 'selected="selected"' : ''; ?>><?php echo $row->LocName; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Warehouse/Supplier</label>
                                                        <select name="warehouse" id="warehouse" class="form-control input-sm" required="required">
<?php
//Checking selProv and selStk
if (!empty($selProv) || !empty($selStk)) {
    $and = " 1=1 ";
    if (!empty($selDist)) {
        $and .= " AND tbl_warehouse.dist_id = " . $selDist . " ";
    }if (!empty($selProv)) {
        $and .= " AND tbl_warehouse.prov_id = " . $selProv . " ";
    }if (!empty($selStk)) {
        $and .= " AND tbl_warehouse.stkid = " . $selStk . "";
    }
    //Gets 
    //wh_id
    //wh_name
    $qry = "SELECT DISTINCT
																		tbl_warehouse.wh_id,
																		CONCAT(tbl_warehouse.wh_name,	'(', stakeholder.stkname, ')') AS wh_name
																	FROM
																		tbl_warehouse
																	INNER JOIN tbl_stock_master ON tbl_warehouse.wh_id = tbl_stock_master.WHIDTo
																	INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
																	WHERE
																		$and
																	ORDER BY
																		tbl_warehouse.wh_name ASC";
    //Query result
    $qryRes = mysql_query($qry);
    echo '<option value="">Select</option>';
    while ($row = mysql_fetch_array($qryRes)) {
        $sel = ($warehouse == $row['wh_id']) ? 'selected="selected"' : '';
        echo "<option value=\"$row[wh_id]\" $sel>$row[wh_name]</option>";
    }
} else {
    echo '<option value="">Select</option>';
}
?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4" style="text-align:right;">
                                                    <label for="firstname">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                        <button type="reset" class="btn btn-info">Reset</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
<?php
if (isset($_REQUEST['search'])) {
    if ($num > 0) {
        ?>
                                <table width="100%" cellpadding="0" cellspacing="0" id="myTable">
                                    <tr>
                                        <td align="right" style="padding-right:5px;"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/> <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></td>
                                    </tr>
                                    <tr>
                                        <td><div id="mygrid_container" style="width:100%; height:390px;"></div></td>
                                    </tr>
                                </table>
        <?php
    } else {
        echo '<h6>No record found.</h6>';
    }
}
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
<?php include PUBLIC_PATH . "/html/footer.php"; ?>
<?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold; font-family:Helvetica'><?php echo "District Stock Issuance Report"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No., Issue Date, Issue Ref., Issue No., Product, Batch No., Expiry, Stakeholder, Issue To, District, Province, Quantity");
            mygrid.attachHeader(",#select_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#select_filter,");
            mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("50,80,80,80,100,80,80,80,*,100,100,70");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
            mygrid.enableMultiline(true);
            mygrid.setColAlign("center,center,center,center,left,left,center,left,left,left,left,right");
            mygrid.enableRowsHover(true, 'onMouseOver');
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script> 
    <script>
        $(function() {
            var startDateTextBox = $('#date_from');
            var endDateTextBox = $('#date_to');

            startDateTextBox.datepicker({
                minDate: "-10Y",
                maxDate: 0,
                dateFormat: 'dd/mm/yy',
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
                    showWarehouses('');
                },
                onSelect: function(selectedDateTime) {
                    endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
                }
            });
            endDateTextBox.datepicker({
                maxDate: 0,
                dateFormat: 'dd/mm/yy',
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
                    showWarehouses('');
                },
                onSelect: function(selectedDateTime) {
                    startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
                }
            });
        })
        $(function() {
            showDistricts('<?php echo $district; ?>');

            $('#province').change(function(e) {
                $('#warehouse').html('<option value="">Select</option>');
                showDistricts('');
            });
            $('#product').change(function(e) {
                showWarehouses('');
            });
            $(document).on('change', '#district', function() {
                $('#warehouse').html('<option value="">Select</option>');
                showWarehouses('');
            });
            $('#stakeholder').change(function(e) {
                $('#province').val('');
                $('#district').html('<option value="">Select</option>');
                $('#warehouse').html('<option value="">Select</option>');
            });
        })
        function showDistricts(dId) {
            var pid = $('#province').val();
            if (pid != '')
            {
                if (pid != 'all')
                {
                    $.ajax({
                        url: 'ajax_calls.php',
                        type: 'POST',
                        data: {provinceId: pid, dId: dId, validate: 'no'},
                        success: function(data) {
                            $('#districtsCol').html(data);
                            showWarehouses('<?php echo $warehouse; ?>');
                        }
                    })
                }
                else
                {
                    $('#district').html('<option value="">All</option>');
                    $('#warehouse').html('<option value="all">All</option>');
                }
            }
            else
            {
                $('#district').html('<option value="">Select</option>');
            }
        }
        function showWarehouses(whId)
        {
            var stkId = $('#stakeholder').val();
            var provId = $('#province').val();
            var distId = $('#district').val();
            var dateFrom = $('#date_from').val();
            var dateTo = $('#date_to').val();
            var product = $('#product').val();

            if (distId != '')
            {
                $.ajax({
                    url: 'ajax_calls.php',
                    data: {stkId: stkId, provId: provId, distId: distId, whId: whId, dateFrom: dateFrom, dateTo: dateTo, product: product},
                    type: 'POST',
                    success: function(data) {
                        $('#warehouse').html(data);
                    }
                })
            }
            else
            {
                $('#warehouse').html('<option value="all">All</option>');
            }
        }
    </script>
</body>
<!-- END BODY -->
</html>