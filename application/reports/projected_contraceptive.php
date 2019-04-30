<?php
/**
 * projected_contraceptive
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
$report_id = "PROJECTEDCONTRACEPTIVE";
//if submitted
if (isset($_POST['submit'])) {
    //get year
    $year = mysql_real_escape_string($_POST['year']);
    //get month
    $month = mysql_real_escape_string($_POST['month']);
    //get demand for
    $demand_for = mysql_real_escape_string($_POST['demand_for']);
    //get sector
    $stk_type = mysql_real_escape_string($_POST['sector']);
    //get selected stakeholder
    $stk_id = mysql_real_escape_string($_POST['stk_sel']);
    //get province
    $province = mysql_real_escape_string($_POST['province']);
    //get product
    $product = mysql_real_escape_string($_POST['product']);

    $p_type = mysql_real_escape_string($_POST['p_type']);
    //get selected item
    $sel_item = mysql_real_escape_string($_POST['product']);
    //reporting Date
    $reportingDate = $year . '-' . $month . '-01';
} else {
    //check date
    if (date('d') > 10) {
        //set date
        $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
        //set date
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    //set month
    $month = date('m', strtotime($date));
    //set year
    $year = date('Y', strtotime($date));
    //set demand for
    $demand_for = 1;
    //set stakeholder type
    $stk_type = 'all';
    //set stakeholder id
    $stk_id = 1;
    //get province
    $province = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //set province
    $province = ($province != 10) ? $province : 'all';
    //set selected item
    $sel_item = "IT-001";
    //set reporting Date 
    $reportingDate = $year . '-' . $month . '-01';
    $p_type = 1;
}
$provFilter = $stkFilter = $rptType = $sel_stk = '';
//check stakeholder
if (!empty($stk_id) && $stk_id != 'all') {
    //set stakeholder filter
    $stkFilter = " AND summary_district.stakeholder_id = '" . $stk_id . "'";
} else if ($_POST['sector'] == 'public' && $_POST['stk_sel'] == 'all') {
    //set stakeholder filter
    $stkFilter = " AND stakeholder.stk_type_id = 0";
} else if ($_POST['sector'] == 'private' && $_POST['stk_sel'] == 'all') {
    //set stakeholder filter
    $stkFilter = " AND stakeholder.stk_type_id = 1";
}
//check province
if ($province != 'all') {
    //check province filter
    $provFilter = " AND summary_district.province_id = $province";
}
//check selected item
if (!empty($sel_item)) {
    //set product filter
    $prodFilter = " AND summary_district.item_id = '$sel_item'";
}

$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$counter = 1;
//check province
if ($province != 'all') {
    //select query
    //gets
    //District id
    //District name
    $qry_dist = "SELECT
					District.PkLocID AS distId,
					District.LocName AS distName
				FROM
					tbl_locations AS District
				WHERE
					District.LocLvl = 3
					AND District.ParentID = $province
				ORDER BY
					distName";
    //query result
    $distRes = mysql_query($qry_dist);
    //select query
    //gets
    //province name
    $getProvQry = "SELECT
						tbl_locations.LocName
					FROM
						tbl_locations
					WHERE
						tbl_locations.PkLocID = $province";
    //reqult
    $getProvQry = mysql_fetch_array(mysql_query($getProvQry));
    //set province name
    $provinceName = $getProvQry['LocName'];
} else {
    //set province name
    $provinceName = 'All';
}

if ($p_type == 1) {
    //select query 
//gets
//Pk Location id,
    //district name,
    //province name,
    //stakeholder name,
    //item name,
    //qty carton,
    //avg consumption,
    //SOH district,
    //SOH field,
    //SOH total
    $qry = "SELECT
			tbl_locations.PkLocID,
			tbl_locations.LocName AS distName,
			Province.LocName AS provName,
			stakeholder.stkname,
			itminfo_tab.itm_name,
			itminfo_tab.qty_carton,
			summary_district.avg_consumption,
			summary_district.soh_district_store AS SOH_district,
			(summary_district.soh_district_lvl - summary_district.soh_district_store) AS SOH_field,
			summary_district.soh_district_lvl AS SOH_total
		FROM
		summary_district
		INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
		INNER JOIN tbl_locations AS Province ON summary_district.province_id = Province.PkLocID
		INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
		INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
		WHERE
			summary_district.reporting_date = '$reportingDate'
		$prodFilter
		$provFilter
		$stkFilter
		GROUP BY
			summary_district.district_id,
			summary_district.stakeholder_id,
			summary_district.item_id
		ORDER BY
			Province.PkLocID ASC,
			tbl_locations.LocName ASC,
			stakeholder.stkorder ASC,
			itminfo_tab.frmindex ASC";
} else {
//select query 
//gets
//Pk Location id,
    //district name,
    //province name,
    //stakeholder name,
    //item name,
    //qty carton,
    //avg consumption,
    //SOH district,
    //SOH field,
    //SOH total
    $qry = "SELECT
			Province.LocName AS provName,
			stakeholder.stkname,
			itminfo_tab.itm_name,
			itminfo_tab.qty_carton,
			SUM(summary_district.avg_consumption) avg_consumption,
			SUM(summary_district.soh_district_store) AS SOH_district,
			(SUM(summary_district.soh_district_lvl) - SUM(summary_district.soh_district_store)) AS SOH_field,
			SUM(summary_district.soh_district_lvl) AS SOH_total
		FROM
		summary_district
		INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
		INNER JOIN tbl_locations AS Province ON summary_district.province_id = Province.PkLocID
		INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
		INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
		WHERE
			summary_district.reporting_date = '$reportingDate'
		$provFilter
		$stkFilter
		GROUP BY
			summary_district.stakeholder_id,
			summary_district.item_id
		ORDER BY
			Province.PkLocID ASC,
			tbl_locations.LocName ASC,
			stakeholder.stkorder ASC,
			itminfo_tab.frmindex ASC";
}

//query result
$qryRes = mysql_query($qry);
//num of result
$num = mysql_num_rows(mysql_query($qry));
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$i = 1;
//fetch results
while ($row = mysql_fetch_array($qryRes)) {
    $cartonQty = $row['qty_carton'];
    $desiredStock = $row['avg_consumption'] * $demand_for;
    $repRequest = ($desiredStock > $row['SOH_total']) ? $desiredStock - $row['SOH_total'] : 0;
    $xmlstore .= "<row>";
    $xmlstore .= "<cell>" . $i++ . "</cell>";
    $xmlstore .= "<cell><![CDATA[" . $row['provName'] . "]]></cell>";
    if ($p_type == 1)
        $xmlstore .= "<cell><![CDATA[" . $row['distName'] . "]]></cell>";
    $xmlstore .= "<cell><![CDATA[" . $row['stkname'] . "]]></cell>";
    $xmlstore .= "<cell><![CDATA[" . $row['itm_name'] . "]]></cell>";
    $xmlstore .= "<cell>" . number_format($row['avg_consumption']) . "</cell>";
    $xmlstore .= "<cell>" . number_format($row['SOH_district']) . "</cell>";
    $xmlstore .= "<cell>" . number_format($row['SOH_field']) . "</cell>";
    $xmlstore .= "<cell>" . number_format($row['SOH_total']) . "</cell>";
    $xmlstore .= "<cell>" . number_format($desiredStock) . "</cell>";
    $xmlstore .= "<cell>" . number_format($repRequest) . "</cell>";
    $xmlstore .= "<cell>" . number_format($repRequest / $cartonQty) . "</cell>";
    $xmlstore .= "</row>";
}
$xmlstore .= "</rows>";

$disabled = (isset($_GET['view']) && $_GET['view'] == 1) ? 'disabled="disabled"' : '';
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
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
                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp"> <?php echo "Projected Contraceptive Requirement"; ?> <span class="green-clr-txt"></span> </h3>
                        <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="POST">
                                    <table width="100%">
                                        <tr> 
                                            <!--Month-->
                                            <td class="col-md-2"><label class="control-label">Ending Month</label>
                                                <select name="month" id="month" required="required" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    <?php
                                                    for ($i = 1; $i <= 12; $i++) {
                                                        if ($month == $i) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select></td>
                                            <!--Year-->
                                            <td class="col-md-2"><label class="control-label">Year</label>
                                                <select name="year" id="year" required="required" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    <?php
                                                    for ($i = date('Y'); $i >= 2010; $i--) {
                                                        $sel = ($year == $i) ? 'selected="selected"' : '';
                                                        echo "<option value=\"$i\" $sel>$i</option>";
                                                    }
                                                    ?>
                                                </select></td>
                                            <!--Demand For (months)-->
                                            <td class="col-md-2"><label class="control-label">Demand For(Months)</label>
                                                <select name="demand_for" id="demand_for" required="required" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    <?php
                                                    for ($i = 1; $i < 10; $i++) {
                                                        $sel = ($demand_for == $i) ? 'selected="selected"' : '';
                                                        echo "<option value=\"$i\" $sel>$i</option>";
                                                    }
                                                    ?>
                                                </select></td>
                                            <td class="col-md-2"><label class="control-label">Sector</label>
                                                <select style="width:90%;" name="sector" id="sector" required="required" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    <option value="all" <?php echo ($stk_type == 'all') ? 'selected="selected"' : ''; ?>>All</option>
                                                    <option value="public" <?php echo ($stk_type == 'public') ? 'selected="selected"' : ''; ?>>Public</option>
                                                    <option value="private" <?php echo ($stk_type == 'private') ? 'selected="selected"' : ''; ?>>Private</option>
                                                </select></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr> 
                                            <!--Type-->

                                            <!--Stakeholder-->
                                            <td class="col-md-2"><label class="control-label">Stakeholder</label>
                                                <select name="stk_sel" id="stk_sel" required="required" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                </select></td>

                                            <!--Province-->
                                            <td class="col-md-2"><label class="control-label">Province/Region</label>
                                                <select name="province" id="province" required="required" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    <option value="all" <?php echo ($province == 'all') ? 'selected="selected"' : ''; ?>>All</option>
                                                    <?php
                                                    $qry = "SELECT
														tbl_locations.LocName AS prov_name,
														tbl_locations.PkLocID
													FROM
														tbl_locations
													WHERE
														tbl_locations.LocLvl = 2
													AND ParentID IS NOT NULL";
                                                    $qryRes = mysql_query($qry);
                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                        $sel = ($province == $row['PkLocID']) ? 'selected="selected"' : '';
                                                        echo "<option value=\"$row[PkLocID]\" $sel>$row[prov_name]</option>";
                                                    }
                                                    ?>
                                                </select></td>
                                            <!--Product-->
                                            <td class="col-md-2"><label class="control-label">Product</label>
                                                <select name="product" id="product" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                </select>
                                            </td>
                                            <td class="col-md-2"><label class="control-label">Report Type</label>
                                                <select name="p_type" id="p_type" class="form-control input-sm">
                                                    <option value="1" <?php
                                                    if ($p_type == 1) {
                                                        echo "selected='selected'";
                                                    }
                                                    ?>>Detail</option>
                                                    <option value="2" <?php
                                                    if ($p_type == 2) {
                                                        echo "selected='selected'";
                                                    }
                                                    ?>>Summary</option>
                                                </select>
                                            </td>
                                            <?php
                                            if (!isset($_GET['view'])) {
                                                ?>
                                                <td class="col-md-2" style="margin-left:20px; padding-top: 28px;" valign="middle"><input type="submit" id="submit" name="submit" value="Go" class="btn btn-primary input-sm" /></td>
<?php } ?>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
<?php
if ($num > 0) {
    ?>
                            <table width="100%" cellpadding="0" cellspacing="0" id="myTable">
                                <tr>
                                    <td style="float:left;"><label>Note: If D > E then F= 0 else F= E-D</label></td>
                                </tr>
                                <tr>
                                    <td align="right" style="padding-right:5px;"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/> <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></td>
                                </tr>
                                <tr>
                                    <td><div id="mygrid_container" style="width:100%; height:390px; background-color:white;overflow:hidden"></div></td>
                                </tr>
                            </table>
                            <?php
                        } else {
                            echo "No record found";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('#sector').change(function (e) {
                $('#stk_sel').html('<option value="">Select</option>');
                $('#product').html('<option value="">Select</option>');
                var val = $('#sector').val();
                getStakeholder(val, '');
            });
            $('#stk_sel').change(function (e) {
                $('#product').html('<option value="">Select</option>');
                showProducts('');
            });
            getStakeholder('<?php echo $rptType; ?>', '<?php echo $sel_stk; ?>');

            $("select[name=p_type]").change(function () {
                $("#product").prop("disabled", this.value != "1");
                $("#product").val("");
            }).change();

//            $("#p_type").change(function () {
//                var val = $("#p_type").val();
//                if (val == 1) {
//                    $("#product").prop("disabled", this.value != "");
//                } else {
//                    $("#product").val("");
//                    $("#product").disabled(true);
//                }
//            });
        });
        getStakeholder('<?php echo $stk_type; ?>', '<?php echo $stk_id; ?>');

        function getStakeholder(val, stk)
        {
            if (val != '')
            {
                $.ajax({
                    url: 'ajax_stk.php',
                    data: {type: val, stk: stk},
                    type: 'POST',
                    success: function (data) {
                        $('#stk_sel').html(data);
                        showProducts('<?php echo $sel_item; ?>');
                    }
                })
            }
        }

        function showProducts(pid) {
            var stk = $('#stk_sel').val();
            if (typeof stk !== 'undefined')
            {
                $.ajax({
                    url: 'ajax_calls.php',
                    type: 'POST',
                    data: {stakeholder: stk, productId: pid, validate: 'no'},
                    success: function (data) {
                        $('#product').html(data);
                    }
                })
            }
        }
    </script>
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    //include reports_includes
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'>Projected Contraceptive Requirement for Province='<?php echo $provinceName; ?>' (<?php
    echo $reportMonth = date('F', mktime(0, 0, 0, $month)) . ' ' . $year;
    ;
    ?>)</div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan<?php if ($p_type == 1) { ?>,#cspan<?php } ?>");
            mygrid.attachHeader("Sr. No., Province,<?php if ($p_type == 1) { ?> District,<?php } ?> Stakeholder, Product, <div style='text-align:center'>AMC<br><br><br><br>(A)</div>,<div title='Stock at the end of the month' style='text-align:center;'><?php echo "Stock at the end of " . date('M', mktime(0, 0, 0, $month, 1)) . " " . $year; ?></div>,#cspan,#cspan,<div title='Desired stock level for' style='text-align:center;'><?php echo "Desired stock level for " . $demand_for . " months<br><br>(E)"; ?></div>,<div title='Replenishment to be Requested' style='text-align:center;'>Replenishment Requested<br>(F= E-D)</div>,#cspan");
            mygrid.attachHeader("#rspan,#rspan,<?php if ($p_type == 1) { ?>#rspan,<?php } ?>#rspan,#rspan,#rspan,<div style='text-align:center;'>District<br>(B)</div>,<div style='text-align:center;'>Field<br>(C)</div>,<div style='text-align:center;'>Total<br>(D)</div>,#rspan,<div style='text-align:center;'>Quantity</div>,<div style='text-align:center;'>Quantity (Cartons)</div>");
            mygrid.attachFooter("<div style='font-size: 10px;'><?php echo $lastUpdateText; ?></div>,#cspan,<?php if ($p_type == 1) { ?>#cspan,<?php } ?>#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setColAlign("center,left,<?php if ($p_type == 1) { ?>left,<?php } ?>left,left,right,right,right,right,right,right,right");
            mygrid.setInitWidths("60,150,<?php if ($p_type == 1) { ?>*,<?php } ?>*,*,*,*,*,*,*,*,*");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
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
                    data: {stakeholder: stk, provinceId: pid, showProvinces: 1},
                    success: function (data) {
                        $('#province').html(data);
                    }
                })
            }
        }
        $(function () {
            $('#stk_sel').change(function (e) {
                $('#province').html('<option value="">Select</option>');
                showProvinces('');
            });
        })
<?php
if (isset($province) && !empty($province)) {
    ?>
            showProvinces('<?php echo $province; ?>');
    <?php
}
?>
    </script>
</body>
<!-- END BODY -->
</html>