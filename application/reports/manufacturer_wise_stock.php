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
$fundingSource = 'all';
$funding_s = 'All';
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
        $qry_funding = "SELECT
	tbl_warehouse.wh_id,
	tbl_warehouse.wh_name
        FROM
                stakeholder
        INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
        WHERE
	tbl_warehouse.wh_id = $fundingSource";
        $qryResFun = mysql_query($qry_funding);
        $res_fun = mysql_fetch_array($qryResFun);
        $funding_s = $res_fun['wh_name'];
    } else {
        $funding_s = 'All';
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
$qry = "
        SELECT
            itminfo_tab.itm_id,
            stakeholder.stkname manu,
            stock_batch.batch_id,
            stock_batch.batch_no,
            stakeholder_item.quantity_per_pack,
            stakeholder_item.carton_per_pallet,

            SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') < '$startDate', tbl_stock_detail.Qty, 0)) AS OB,
                
            SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDate' AND tbl_stock_master.TranTypeID = 2, ABS(tbl_stock_detail.Qty), 0)) AS Issue,
            SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$startDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDate' AND tbl_stock_master.TranTypeID = 1, ABS(tbl_stock_detail.Qty), 0)) AS rcv,
            SUM(tbl_stock_detail.Qty) AS CB,
            tbl_warehouse.wh_name FundingSource,
            itminfo_tab.itm_name,
            itm_type 
            FROM
			itminfo_tab
		INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
		INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
		INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
		INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
		INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
		INNER JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
		INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
            WHERE
                DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDate'
            AND (
			(tbl_stock_master.WHIDFrom = $whId AND tbl_stock_master.TranTypeID = 2)
			OR (tbl_stock_master.WHIDTo = $whId AND tbl_stock_master.TranTypeID = 1)
			OR (tbl_stock_master.WHIDFrom = $whId AND tbl_stock_master.WHIDTo = $whId AND tbl_stock_master.TranTypeID > 2)
		)
            AND tbl_stock_master.temp=0
            $and
           GROUP BY
			stock_batch.funding_source,
			stock_batch.manufacturer,
			itminfo_tab.itm_id
		ORDER BY
			stock_batch.funding_source ASC,
			itminfo_tab.itm_id,
			stock_batch.manufacturer
";
//query result
//echo $qry;
//exit;
$qryRes = mysql_query($qry);
$num = mysql_num_rows($qryRes);
//xml
$xmlstore = '<?xml version="1.0"?><rows>';
$i = 1;
$funding = 'All';


while ($row = mysql_fetch_array($qryRes)) {
    $itemName = $row['itm_name'];
    if (!empty($row['FundingSource'])) {
        $funding = $row['FundingSource'];
    
        
    @$ob_cartons = round($row['OB'] / $row['quantity_per_pack']);    
    @$ob_pallets = round($ob_cartons/ $row['carton_per_pallet']);
        
    @$issue_cartons = round($row['Issue'] / $row['quantity_per_pack']);    
    @$issue_pallets = round($issue_cartons/ $row['carton_per_pallet']);
        
    @$rcv_cartons = round($row['rcv'] / $row['quantity_per_pack']);    
    @$rcv_pallets = round($rcv_cartons/ $row['carton_per_pallet']);
    
    @$cb_cartons = round($row['CB'] / $row['quantity_per_pack']);    
    @$cb_pallets = round($cb_cartons/ $row['carton_per_pallet']);
        
    $xmlstore .= '<row>';

    $xmlstore .= '<cell>' . $i++ . '</cell>';
    //item name
    $xmlstore .= '<cell>' . htmlspecialchars($row['FundingSource']) . '</cell>';
    $xmlstore .= '<cell>' . $row['itm_name'] . '</cell>';
    //item type
    $xmlstore .= '<cell>' . $row['itm_type'] . '</cell>';
    //Opening Balance
    $xmlstore .= '<cell>' . $row['manu'] . '</cell>';
    //Receive
    //Closing balance
        $xmlstore .= '<cell>' . number_format($row['OB']) . '</cell>';
        $xmlstore .= '<cell>' . number_format($ob_cartons) . '</cell>';
        $xmlstore .= '<cell>' . number_format($ob_pallets) . '</cell>';
        $xmlstore .= '<cell>' . number_format($row['rcv']) . '</cell>';
        $xmlstore .= '<cell>' . number_format($rcv_cartons) . '</cell>';
        $xmlstore .= '<cell>' . number_format($rcv_pallets) . '</cell>';
        $xmlstore .= '<cell>' . number_format($row['Issue']) . '</cell>';
        $xmlstore .= '<cell>' . number_format($issue_cartons) . '</cell>';
        $xmlstore .= '<cell>' . number_format($issue_pallets) . '</cell>';
        $xmlstore .= '<cell>' . number_format($row['CB']) . '</cell>';
        $xmlstore .= '<cell>' . number_format($cb_cartons) . '</cell>';
        $xmlstore .= '<cell>' . number_format($cb_pallets) . '</cell>';
        
    $xmlstore .= '</row>';
    }
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
                            <div class="col-md-2 pull-right">
                                <label>&nbsp;</label>
                                <div class="form-group">
                                    <a   class="btn blue " onclick="window.open('cartons_info.php', '_blank', 'scrollbars=1,width=600,height=500');"><i class="fa fa-info-circle"></i> Cartons / Pallets Information</a>
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
    include PUBLIC_PATH . "/html/footer.php";
    
    $fromDate_display   = str_replace('/', '-', $fromDate);
    $toDate_display     = str_replace('/', '-', $toDate);
    ?>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold;'><?php echo $funding_s; ?> - Manufacturer Wise Stock Detail(<?php echo date('d-M-Y',strtotime($fromDate_display)) . '   to  ' . date('d-M-Y',strtotime($toDate_display)); ?>)</div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("<div></div>,#cspan,#cspan,#cspan,#cspan,<div>Opening Quantity</div>,#cspan,#cspan,<div>Received</div>,#cspan,#cspan,<div>Issuance</div>,#cspan,#cspan,<div>Closing Balance</div>,#cspan,#cspan");
            mygrid.attachHeader("<span>Sr. No.</span>,<span>Funding Source</span>,<span>Product</span>,<span>Unit</span>,<span>Manufacturer</span>,<span>Opening Quantity</span>,<span>Cartons</span>,<span>Pallets</span>,<span>Recieved</span>,<span>Cartons</span>,<span>Pallets</span>,<span>Issued</span>,<span>Cartons</span>,<span>Pallets</span>,<span>Closing Quantity</span>,<span>Cartons</span>,<span>Pallets</span>");
             mygrid.setInitWidths("30,170,100,50,*,100,60,60,100,60,60,100,60,60,100,60,60");
            mygrid.setColAlign("center,left,left,left,left,right,right,right,right,right,right,right,right,right,right,right,right");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");

            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>')
        }

    </script>
    <script type="text/javascript">
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