<?php
/**
 * countrywise_distribution
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$report_id = "DISTRIBUTIONREPORT";
//initialize variables
$distId = $provId = $stkId = $itmId = $transMode = $dateFrom = $dateTo = $title = '';
//check if submitted
if (isset($_REQUEST['submit'])) {
    //get selected province
    $provId = $_REQUEST['prov_sel'];
    //get district id
    $distId = $_REQUEST['district'];
    //get stakeholder id
    $stkId = $_REQUEST['stk_sel'];
    //get item id
    $itmId = $_REQUEST['item_id'];
    //get date from
    $dateFrom = $_REQUEST['from_date'];
    //get date to
    $dateTo = $_REQUEST['to_date'];
    //get transaction mode
    $transMode = $_REQUEST['trasn_mode'];
    //set 
    $count = 1;
//set province name 
    $provName = '';
//set district name
    $distName = '';
//set stakeholder name
    $stkName = '';
//set item name
    $itmName = '';

    //where array
    $where = array();
//data array
    $data = array();
    if ($provId != 'all') {
        $where[] = "Province.PkLocID = $provId";
    }
    if ($stkId != 'all') {
        $where[] = "tbl_warehouse.stkid = $stkId";
    }
    if ($distId != '') {
        $where[] = "District.PkLocID = $distId";
    }
    if ($itmId != '') {
        $where[] = "itminfo_tab.itmrec_id = '$itmId' ";
    }
    if ($dateFrom != '' && $dateTo != '') {
        $title = "  From '$dateFrom' To '$dateTo' ";
        $where[] = "tbl_stock_master.TranDate BETWEEN '" . dateToDbFormat($dateFrom) . "' AND '" . dateToDbFormat($dateTo) . "'";
    }
    if ($transMode != 'all') {
        $where[] = "tbl_stock_master.issued_by = $transMode";
    }
    $where = !empty($where) ? 'AND ' . implode(' AND ', $where) : '';
    //select query 
    //gets
    //Province,
    //District,
    //Stakeholder,
    //Product,
    //Batch,
    //Qty,
    //Cartons,
    //PickDate,
    //Expiry,
    //Trans Mode
    $qry = "SELECT
				Province.LocName AS Province,
				District.LocName AS District,
				stakeholder.stkname AS Stakeholder,
				itminfo_tab.itm_name AS Product,
				stock_batch.batch_no AS Batch,
				SUM(ABS(placements.quantity)) AS Qty,
				ROUND((SUM(ABS(placements.quantity)) / itminfo_tab.qty_carton)) AS Cartons,
				DATE_FORMAT(placements.created_date, '%d/%m/%Y') AS PickDate,
				DATE_FORMAT(stock_batch.batch_expiry, '%d/%m/%Y') AS Expiry,
				list_detail.list_value AS TransMode
			FROM
				tbl_stock_master
			INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
			INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
			INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
			INNER JOIN tbl_locations AS District ON tbl_warehouse.dist_id = District.PkLocID
			INNER JOIN tbl_locations AS Province ON tbl_warehouse.prov_id = Province.PkLocID
			INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
			INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
			LEFT JOIN placements ON tbl_stock_detail.PkDetailID = placements.stock_detail_id
			AND stock_batch.batch_id = placements.stock_batch_id
			LEFT JOIN list_detail ON tbl_stock_master.issued_by = list_detail.pk_id
			WHERE
			tbl_stock_master.WHIDFrom = 123
			AND tbl_stock_master.TranTypeID = 2
			AND placements.placement_transaction_type_id = 91
				$where
			GROUP BY
				Province.PkLocID,
				District.PkLocID,
				stakeholder.stkid,
				itminfo_tab.itm_id,
				stock_batch.batch_id
			ORDER BY
				Province.PkLocID ASC,
				District ASC,
				stakeholder.stkid ASC,
				itminfo_tab.frmindex ASC";
    //query result
    $rows = mysql_query($qry);
    //nun of record
    $num = mysql_num_rows($rows);
    //fetch result
    while ($row = mysql_fetch_array($rows)) {
        //set province
        $provName = $row['Province'];
        //set district
        $distName = $row['District'];
        //set stakeholder
        $stkName = $row['Stakeholder'];
        //set product
        $itmName = $row['Product'];
        //put in data array
        //province name
        $data[$count][] = $provName;
        //district name
        $data[$count][] = $distName;
        //stakeholder name
        $data[$count][] = $stkName;
        //item name
        $data[$count][] = $itmName;
        //batch
        $data[$count][] = $row['Batch'];
        //qty
        $data[$count][] = $row['Qty'];
        //cartons
        $data[$count][] = $row['Cartons'];
        //pick date
        $data[$count][] = $row['PickDate'];
        //expiry
        $data[$count][] = $row['Expiry'];
        //transaction mode
        $data[$count][] = $row['TransMode'];
        $count++;
    }

    $provName = ($provId != 'all') ? $provName : 'All';
    $distName = ($distId != '') ? $distName : 'All';
    $stkName = ($stkId != 'all') ? $stkName : 'All';
    $itmName = ($itmId != '') ? $itmName : 'All';
    //xml
    $xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xmlstore .= "<rows>";

    foreach ($data as $count => $subArr) {
        $xmlstore .= "<row>";
        $xmlstore .= "<cell style=\"text-align:center\">" . $count . "</cell>";
        $xmlstore .= "<cell>" . $subArr[0] . "</cell>";
        $xmlstore .= "<cell>" . $subArr[1] . "</cell>";
        $xmlstore .= "<cell>" . $subArr[2] . "</cell>";
        $xmlstore .= "<cell>" . $subArr[3] . "</cell>";
        $xmlstore .= "<cell>" . $subArr[4] . "</cell>";
        $xmlstore .= "<cell style=\"text-align:right\">" . number_format($subArr[5]) . "</cell>";
        $xmlstore .= "<cell style=\"text-align:right\">" . number_format($subArr[6]) . "</cell>";
        $xmlstore .= "<cell>" . $subArr[7] . "</cell>";
        $xmlstore .= "<cell></cell>";
        $xmlstore .= "<cell>" . $subArr[8] . "</cell>";
        $xmlstore .= "<cell>" . $subArr[9] . "</cell>";
        $xmlstore .= "<cell></cell>";
        $xmlstore .="</row>";
    }
    $xmlstore .="</rows>";
}
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <div class="page-container">
<?php 
//include top
include PUBLIC_PATH . "html/top.php"; 
//include top_im
include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Contraceptive Distribution Report</h3>
                        <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form">
                                    <table id="myTable">
                                        <tr>
                                            <td class="col-md-2"><label class="control-label">Province</label>
                                                <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    <option value="all" <?php echo ($provId == 'all') ? 'selected' : ''; ?>>All</option>
<?php
//select query 
//gets
//pk id
//location name
                                              $queryprov = "SELECT
                                                                tbl_locations.PkLocID,
                                                                tbl_locations.LocName
                                                            FROM
                                                                tbl_locations
                                                            WHERE
                                                                LocLvl = 2
                                                            AND parentid IS NOT NULL";
$rsprov = mysql_query($queryprov) or die();
while ($rowprov = mysql_fetch_array($rsprov)) {
    ?>
                                                        <option value="<?php echo $rowprov['PkLocID']; ?>" <?php echo ($provId == $rowprov['PkLocID']) ? 'selected="selected"' : ''; ?>><?php echo $rowprov['LocName']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select></td>
                                            <td class="col-md-2" id="districts"><label class="control-label">District</label>
                                                <select name="district_id" id="district_id" required class="form-control input-sm">
                                                    <option value="">Select</option>
                                                </select></td>
                                            <td class="col-md-2"><label class="control-label">Stakeholder</label>
                                                <select name="stk_sel" id="stk_sel" required class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    <option value="all" <?php echo ($stkId == 'all') ? 'selected' : ''; ?>>All</option>
                                                    <?php
                                                    $querystk = "SELECT stkid,stkname FROM stakeholder Where ParentID is null AND stakeholder.stk_type_id IN (0,1) order by stkorder";
                                                    $rsstk = mysql_query($querystk) or die();
                                                    while ($rowstk = mysql_fetch_array($rsstk)) {
                                                        ?>
                                                        <option value="<?php echo $rowstk['stkid']; ?>" <?php echo ($stkId == $rowstk['stkid']) ? 'selected="selected"' : ''; ?>><?php echo $rowstk['stkname']; ?></option>
    <?php
}
?>
                                                </select></td>
                                            <td class="col-md-2"><label class="control-label">Product</label>
                                                <select name="item_id" id="item_id" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                </select></td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-2"><label class="control-label">Date From</label>
                                                <input name="from_date" id="from_date" class="form-control input-sm" readonly value="<?php echo $dateFrom; ?>" /></td>
                                            <td class="col-md-2"><label class="control-label">Date To</label>
                                                <input name="to_date" id="to_date" class="form-control input-sm" readonly value="<?php echo $dateTo; ?>" /></td>
                                            <td class="col-md-2"><label class="control-label">Transportation Mode</label>
                                                <select name="trasn_mode" id="trasn_mode" required class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    <option value="all" <?php echo ($transMode == 'all') ? 'selected' : ''; ?>>All</option>
<?php
//select query 
//gets
//pk id
//list value
													$qry = "SELECT
																list_detail.pk_id,
																list_detail.list_value
															FROM
																list_detail
															WHERE
																list_detail.list_master_id = 21
															ORDER BY
															list_detail.list_value ASC";
$qryRes = mysql_query($qry);
while ($row = mysql_fetch_array($qryRes)) {
    $sel = ($transMode == $row['pk_id']) ? 'selected="selected"' : '';
    echo "<option value=\"$row[pk_id]\" $sel>$row[list_value]</option>";
}
?>
                                                </select>
                                                </select></td>
                                            <td class="col-md-1" style="margin-left:20px; padding-top: 20px;" valign="middle"><input type="submit" name="submit" id="go" value="GO" class="btn btn-primary input-sm" /></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                                                    <?php
                                                    if (isset($_REQUEST['submit'])) {
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
<?php 
//include footer
include PUBLIC_PATH . "/html/footer.php"; 
//include reports_includes
include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'><?php echo "Contraceptive Distribution Report for Province/Region = '$provName' District = '$distName' Stakeholder = '$stkName' Product = '$itmName' $title"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("S. No., Province, District, Stakeholder, Product, Batch, Quantity, Cartons, Pick Date, Deliver Date, Expiry Date, Transportation Mode, Remarks");
            mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("50,100,110,90,80,80,70,55,70,70,70,110,*");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script> 
    <script>
        $(function() {

            showDistricts(<?php echo $distId; ?>);
            showProducts('<?php echo $itmId; ?>');

            $('#stk_sel, #prov_sel').change(function(e) {
                $('#item_id').html('<option value="">Select</option>');
                showProducts('');
            });
            $('#prov_sel').change(function(e) {
                $('#district_id').html('<option value="">Select</option>');
                $('#item_id').html('<option value="">Select</option>');
                showDistricts('');
            });
            $("#from_date, #to_date").datepicker({
                dateFormat: 'dd/mm/yy',
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                maxDate: 0
            });
        })
<?php
if (isset($selItem) && !empty($selItem)) {
    ?>
            showProducts('<?php echo $selItem; ?>');
    <?php
}
?>
        function showProducts(pId) {
            var stk = $('#stk_sel').val();
            if ($('#prov_sel').val() != '' && stk != '')
            {
                $.ajax({
                    url: 'ajax_calls.php',
                    type: 'POST',
                    data: {stakeholder: stk, productId: pId, validate: 'no'},
                    success: function(data) {
                        $('#item_id').html(data);
                    }
                })
            }
        }
        function showDistricts(dId) {
            var provId = $('#prov_sel').val();
            if (provId != '')
            {
                $.ajax({
                    url: 'ajax_calls.php',
                    type: 'POST',
                    data: {provinceId: provId, dId: dId, validate: 'no'},
                    success: function(data) {
                        $('#districts').html(data);
                    }
                })
            }
        }
    </script>
</body>
<!-- END BODY -->
</html>