<?php

/**
 * pp_sector_report
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
//include  FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");

//report_title
$report_title = "Public Private Sector Report for ";
//report_id
$report_id = "PPSECTORREPORT";
//report Month 
$reportMonth = $lvl_type = $district = $province = $provFilter = $distFilter = $provFilter = '';
//check date
if (date('d') > 10) {
//set date
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    //set date
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
//level type
$lvl_type = 'national';
//selected month
$sel_month = date('m', strtotime($date));
//selected year
$sel_year = date('Y', strtotime($date));
//num
$num = 0;
//if submitted
if (isset($_POST['go'])) {
    //check selected month
    if (isset($_POST['month_sel']) && !empty($_POST['month_sel'])) {
        //selected month
        $sel_month = $_POST['month_sel'];
    }
    //check selected month
    if (!empty($sel_month)) {
        //set report Month 
        $reportMonth = date('F', mktime(0, 0, 0, $sel_month));
    } else {
        //set report Month 
        $reportMonth = "";
    }
//get year_sel
    if (isset($_POST['year_sel']) && !empty($_POST['year_sel'])) {
        //set year_sel
        $sel_year = $_POST['year_sel'];
    }
//get level_type
    if (isset($_POST['level_type']) && !empty($_POST['level_type'])) {
        //set level_type
        $lvl_type = $_POST['level_type'];
    }
//get province
    if (isset($_POST['province']) && !empty($_POST['province'])) {
        //set province
        $province = $_POST['province'];
    }
//get district
    if (isset($_POST['district']) && !empty($_POST['district'])) {
        //set district
        $district = $_POST['district'];
    }
}
//select query
//gets
// District name
$getDist = mysql_fetch_array(mysql_query("SELECT
											tbl_locations.LocName
										FROM
											tbl_locations
										WHERE
											tbl_locations.PkLocID = '" . $district . "'"));
//district name
$distName = $getDist['LocName'];
//select query
//gets
//Province name
$getProv = mysql_fetch_array(mysql_query("SELECT
											tbl_locations.LocName
										FROM
											tbl_locations
										WHERE
											tbl_locations.PkLocID = '" . $province . "'"));
//Province name
$provName = $getProv['LocName'];
//check level 
if ($lvl_type == 'national') {
    //set title
	$title = 'National Level Public-Private Sector Report';
} elseif ($lvl_type == 'provincial') {
	//set title
    $title = "Provincial Level Public-Private Sector Report for Province = '$provName'";
} elseif ($lvl_type == 'district') {
	//set title
    $title = "District Level Public-Private Sector Report for Province = '$provName' and District = '$distName'";
} elseif ($lvl_type == 'field') {
	//set title
    $title = "Field Level Public-Private Sector Report for Province = '$provName' and District = '$distName'";
}
//reporting Date 
$reportingDate = $sel_year . '-' . $sel_month . '-01';
//check level
if ($lvl_type == 'national') {
	//sel level
    $sel_lvl = 1;
//table name	
    $tblName = 'summary_national';
//SOH
        $SOH = "SUM(summary_national.soh_national_lvl) AS SOH";
	//MOS
        $MOS = "(SUM(summary_national.soh_national_lvl) / SUM(summary_national.avg_consumption)) AS MOS";
}
//check level
if ($lvl_type == 'provincial') {
	
    $sel_lvl = 2;
//table name
    $tblName = 'summary_province';
//SOH
    $SOH = "SUM(summary_province.soh_province_lvl) AS SOH";
//MOS
    $MOS = "(SUM(summary_province.soh_province_lvl) / SUM(summary_province.avg_consumption)) AS MOS";
//provFilter 
    $provFilter = " AND summary_province.province_id = $province";
}
//check level
if ($lvl_type == 'district') {
	
    $sel_lvl = 3;
//table name
    $tblName = 'summary_district';
//SOH
    $SOH = "SUM(summary_district.soh_district_lvl) AS SOH";
//SOH
    $MOS = "(SUM(summary_district.soh_district_lvl) / SUM(summary_district.avg_consumption)) AS MOS";
//distFilter 
    $distFilter = " AND summary_district.district_id = $district";
}
//check level
if ($lvl_type == 'field') {
	
    $sel_lvl = 4;
	//table name
        $tblName = 'summary_district';
//SOH
        $SOH = "SUM(summary_district.soh_district_lvl - summary_district.soh_district_store) AS SOH";
	//MOS
        $MOS = "(SUM(summary_district.soh_district_lvl - summary_district.soh_district_store) / SUM(summary_district.avg_consumption)) AS MOS";
	//distFilter 
        $distFilter = " AND summary_district.district_id = $district";
}
//select query
//gets
//itmrec_id,
				//itEm name,
				//frmindex,
				//stk type id,
				//consumption,
				//avg consumption,
				//SOH,
				//MOS
$qry = "SELECT 
			* 
		FROM (
			SELECT
				itminfo_tab.itmrec_id,
				itminfo_tab.itm_name,
				itminfo_tab.frmindex,
				stakeholder.stk_type_id,
				SUM($tblName.consumption) AS consumption,
				SUM($tblName.avg_consumption) AS avg_consumption,
				$SOH,
				$MOS
			FROM
				$tblName
			INNER JOIN stakeholder ON $tblName.stakeholder_id = stakeholder.stkid
			INNER JOIN itminfo_tab ON $tblName.item_id = itminfo_tab.itmrec_id
			WHERE
				$tblName.reporting_date = '$reportingDate'
			AND itminfo_tab.itm_category = 1
			AND stakeholder.stk_type_id = 0
			$provFilter
			$distFilter
			GROUP BY
				$tblName.item_id
		UNION ALL
			SELECT
				itminfo_tab.itmrec_id,
				itminfo_tab.itm_name,
				itminfo_tab.frmindex,
				stakeholder.stk_type_id,
				Sum($tblName.consumption) AS consumption,
				Sum($tblName.avg_consumption) AS avg_consumption,
				$SOH,
				$MOS
			FROM
				$tblName
			INNER JOIN stakeholder ON $tblName.stakeholder_id = stakeholder.stkid
			INNER JOIN itminfo_tab ON $tblName.item_id = itminfo_tab.itmrec_id
			WHERE
				$tblName.reporting_date = '$reportingDate'
			AND itminfo_tab.itm_category = 1
			AND stakeholder.stk_type_id = 1
			$provFilter
			$distFilter
			GROUP BY
				$tblName.item_id
		)A
	ORDER BY
		frmindex";
$qryRes = mysql_query($qry);
$num = mysql_num_rows(mysql_query($qry));
while ($row = mysql_fetch_array($qryRes)) {
	$items[$row['itmrec_id']] = $row['itm_name'];

	if ($row['stk_type_id'] == 0) {
		$data['public'][$row['itmrec_id']]['cons'] = $row['consumption'];
		$data['public'][$row['itmrec_id']]['avg_cons'] = $row['avg_consumption'];
		$data['public'][$row['itmrec_id']]['SOH'] = $row['SOH'];
		$data['public'][$row['itmrec_id']]['MOS'] = $row['MOS'];
	} else {
		$data['private'][$row['itmrec_id']]['cons'] = $row['consumption'];
		$data['private'][$row['itmrec_id']]['avg_cons'] = $row['avg_consumption'];
		$data['private'][$row['itmrec_id']]['SOH'] = $row['SOH'];
		$data['private'][$row['itmrec_id']]['MOS'] = $row['MOS'];
	}
}
// Generate XML
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
$i = 1;
foreach ($items as $itemId => $itemname) {
	$xmlstore .= "<row>";
	$xmlstore .= "<cell>" . $i++ . "</cell>";
	$xmlstore .= "<cell>" . $itemname . "</cell>";

	// For Public
	$xmlstore .= "<cell>" . (isset($data['public'][$itemId]['cons']) ? number_format($data['public'][$itemId]['cons']) : 0) . "</cell>";
	$xmlstore .= "<cell>" . (isset($data['public'][$itemId]['avg_cons']) ? number_format($data['public'][$itemId]['avg_cons']) : 0) . "</cell>";
	$xmlstore .= "<cell>" . (isset($data['public'][$itemId]['SOH']) ? number_format($data['public'][$itemId]['SOH']) : 0) . "</cell>";
	$xmlstore .= "<cell>" . (isset($data['public'][$itemId]['MOS']) ? number_format($data['public'][$itemId]['MOS'], 1) : 0) . "</cell>";

	

	// For Private
	$xmlstore .= "<cell>" . (isset($data['private'][$itemId]['cons']) ? number_format($data['private'][$itemId]['cons']) : 0) . "</cell>";
	$xmlstore .= "<cell>" . (isset($data['private'][$itemId]['avg_cons']) ? number_format($data['private'][$itemId]['avg_cons']) : 0) . "</cell>";
	$xmlstore .= "<cell>" . (isset($data['private'][$itemId]['SOH']) ? number_format($data['private'][$itemId]['SOH']) : 0) . "</cell>";
	$xmlstore .= "<cell>" . (isset($data['private'][$itemId]['MOS']) ? number_format($data['private'][$itemId]['MOS'], 1) : 0) . "</cell>";

	

	$xmlstore .= "</row>";
}
$xmlstore .= "</rows>";
?>
<script>
    $(document).ready(function() {
        showProvinces();
    });
    function showProvinces()
    {
        var val = $('#level_type').val();
        var colId;
        var selectedVal = '';
        if (val == 'national')
        {
            $('#provincesCol').hide();
            $('#districtsCol').hide();
            $('#provincesCol').parent().hide();
            $('#districtsCol').parent().hide();
        }
        if (val == 'provincial')
        {
            $('#provincesCol').show();
            $('#districtsCol').hide();
            $('#provincesCol').parent().show();
            $('#districtsCol').parent().hide();
            colId = 'provincesCol';
        }
        if (val == 'district' || val == 'field')
        {
            $('#provincesCol').show();
            $('#districtsCol').html('');
            $('#districtsCol').show();
            $('#provincesCol').parent().show();
            colId = 'provincesCol';
        }
        if (val == 'provincial' || val == 'district' || val == 'field')
        {
            selectedVal = "pId=<?php echo $province; ?>";

            $.ajax({
                type: 'POST',
                url: 'ajax_calls.php',
                data: "val=" + val + "&" + selectedVal,
                success: function(data) {
                    $("#" + colId).html(data);
                }
            });

<?php
if ($_POST['go']) {
    ?>
                showDistricts('<?php echo $province; ?>');
    <?php
}
?>

            /*if ( $('#province').val() != '' )
             {
             showDistricts();
             }*/
        }
    }

    function showDistricts(provId)
    {
        if ($('#level_type').val() == 'provincial')
        {
            $('#districtsCol').hide();
            $('#districtsCol').parent().hide();
        }
        else
        {
            $('#districtsCol').show();
            $('#districtsCol1').show();
            $('#provincesCol').parent().show();
            $('#districtsCol').parent().show();
            //var val = $('#province').val();
            valStk = $('#stk_sel').val();

            var data = '';
            //var provinceId = (provId == '') ? val : provId;
            data = "dId=<?php echo $district; ?>&provinceId=" + provId;

            $.ajax({
                type: 'POST',
                url: 'ajax_calls.php',
                data: data,
                success: function(data) {
                    $("#districtsCol").html(data);
                }
            });
        }
    }

    // Form validation
    function formValidate()
    {
        if ($('#month_sel').val() == '')
        {
            alert('Select month.');
            $('#month_sel').focus();
            $('#month_sel').css('border', '1px solid red');
            return false;
        } else
        {
            $('#month_sel').css('border', '1px solid #D1D1D1');
        }
        if ($('#year_sel').val() == '')
        {
            alert('Select year.');
            $('#year_sel').focus();
            $('#year_sel').css('border', '1px solid red');
            return false;
        } else
        {
            $('#year_sel').css('border', '1px solid #D1D1D1');
        }
        if ($('#level_type').val() == '')
        {
            alert('Select level.');
            $('#level_type').focus();
            $('#level_type').css('border', '1px solid red');
            return false;
        }
        else
        {
            $('#level_type').css('border', '1px solid #D1D1D1');
        }
        if ($('#level_type').val() != '')
        {
            if ($('#province').val() == '')
            {
                alert('Select province.');
                $('#province').focus();
                $('#province').css('border', '1px solid red');
                return false;
            }
            else
            {
                $('#province').css('border', '1px solid #D1D1D1');
            }
        }
    }

</script>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php 
        //include top
        //include top_im
        include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp"> <?php echo "Public Private Sector Report for "; ?> <span class="green-clr-txt"><?php echo ' ' . $reportMonth . ' ' . $sel_year; ?></span> </h3>
                        <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="" id="searchfrm" name="searchfrm">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label">Month</label>
                                                    <div class="controls">
                                                        <select class="form-control input-sm" id="month_sel" name="month_sel" required>
                                                            <option value="">Select</option>
                                                            <?php
                                                            for ($i = 1; $i <= 12; $i++) {
                                                                ?>
                                                                <option value="<?php echo $i; ?>" <?php echo ($i == $sel_month) ? 'selected=selected' : ''; ?>><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label">Year</label>
                                                    <div class="controls">
                                                        <select class="form-control input-sm" id="year_sel" name="year_sel" required>
                                                            <option value="">Select</option>
                                                            <?php
                                                            for ($j = date('Y'); $j >= 2010; $j--) {
                                                                ?>
                                                                <option value="<?php echo $j; ?>" <?php echo ($j == $sel_year) ? 'selected=selected' : ''; ?>><?php echo $j; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label">Level</label>
                                                    <div class="controls">
                                                        <select class="form-control input-sm" id="level_type" name="level_type" onChange="showProvinces()" required>
                                                            <option value="national" <?php echo ($lvl_type == 'national') ? 'selected=selected' : ''; ?>>National</option>
                                                            <option value="provincial" <?php echo ($lvl_type == 'provincial') ? 'selected=selected' : ''; ?>>Provincial</option>
                                                            <option value="district" <?php echo ($lvl_type == 'district') ? 'selected=selected' : ''; ?>>District</option>
                                                            <option value="field" <?php echo ($lvl_type == 'field') ? 'selected=selected' : ''; ?>>Field</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2" style="display:none;">
                                                <div class="control-group" id="provincesCol"></div>
                                            </div>
                                            <div class="col-md-2" style="display:none;">
                                                <div class="control-group" id="districtsCol"></div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" class="btn btn-primary input-sm" value="GO" id="go" name="go">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if ($num > 0) {
                            ?>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="right" style="padding-right:5px;"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/> <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></td>
                                </tr>
                                <tr>
                                    <td><div id="mygrid_container" style="width:100%; height:390px;"></div></td>
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
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center;'><?php echo $title . ' (' . date('F', mktime(0, 0, 0, $sel_month)) . ' ' . $sel_year . ")"; ?> </div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("Sr. No.,<div style='text-align:center;'>Product</div>,<div style='text-align:center;'>Public</div>,#cspan,#cspan,#cspan,<div style='text-align:center;'>Private</div>,#cspan,#cspan,#cspan");
            mygrid.attachHeader("#rspan,#rspan,Consumption,AMC,Stock On Hand,Month of Stock,Consumption,AMC,Stock On Hand,Month of Stock");
            mygrid.attachFooter("<div style='font-size: 10px;'><?php echo $lastUpdateText; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("60,*,100,100,100,100,100,100,100,100");
            mygrid.setColAlign("center,left,right,right,right,right,right,right,right,right");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script>
</body>
</html>