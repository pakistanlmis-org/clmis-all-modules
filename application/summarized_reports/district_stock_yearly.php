<?php
$report_id = "STOCK";
//initialize variables
$selStk = $selPro = $type = $sector = $indicatorLvl = $rptType = $stkFilter = '';
//if submitted
if (isset($_REQUEST['submit'])) {
    //selected year
    $selYear = $_REQUEST['year_sel'];
    //selected month
    $selMonth = $_REQUEST['ending_month'];
    //selected item
    $selItem = $_REQUEST['item_id'];
    //selected province
    $selPro = $_REQUEST['prov_sel'];
    //selected stakeholder
    $selStk = $_REQUEST['stk_sel'];
    //type
    $type = $_REQUEST['type'];
    //sector
    $sector = $_REQUEST['sector'];
    //indicator level
    $indicatorLvl = $_REQUEST['indicator_lvl'];
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
    //indicator level
    $indicatorLvl = 'all';
    //selected item
    $selItem = "IT-001";
    //get selected province
    $selPro = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //set selected province
    $selPro = ($selPro != 10) ? $selPro : 'all';
    //check user stakeholder type
    if ($_SESSION['user_stakeholder_type'] == 0) {
        $sector = 'public';
    } else if ($_SESSION['user_stakeholder_type'] == 1) {
        $sector = 'private';
    }
    //selected stakeholder
    $selStk = $_SESSION['user_stakeholder1'];
    //type
    $type = 4;
    //indicator level
    $indicatorLvl = 'all';
}
//check selected province
if ($selPro != 'all') {
    //province filter
    $provFilter = " AND tbl_locations.ParentID = $selPro";
    //province filter1
    $provFilter1 = " AND summary_district.province_id = $selPro";
}
// Check selected Product
if ($selItem != '') {
    //province filter
    $productFilter = " AND tbl_wh_data.item_id = '" . $selItem . "' ";
    //query result
    $itmQry = mysql_fetch_array(mysql_query("SELECT
				itminfo_tab.itm_name
			FROM
				itminfo_tab
			WHERE
				itminfo_tab.itmrec_id = '$selItem'"));
    $proName = $itmQry['itm_name'];
} else {
    $proName = 'All Products';
}


if ($type == 1) {
    // type text
    $typeText = "Issue";
    //set col name
    $colName = 'tbl_wh_data.wh_issue_up';
    //set level filter
    $lvlFilter = ' AND stakeholder.lvl = 3';
} else if ($type == '2') {
    // type text
    $typeText = "Receive";
    //set col name
    $colName = 'tbl_wh_data.wh_received';
    //set level filter
    $lvlFilter = ' AND stakeholder.lvl = 3';
} else if ($type == '3') {
    // type text
    $typeText = "Consumption";
    //set col name
    $colName = 'tbl_wh_data.wh_issue_up';
    //set level filter
    $lvlFilter = ' AND stakeholder.lvl = 4';
} else if ($type == '4') {
    // type text
    $typeText = "Stock on Hand";
    //set col name
    $colName = 'tbl_wh_data.wh_cbl_a';
    if ($indicatorLvl == 'all') {
        //set level filter
        $lvlFilter = ' AND stakeholder.lvl IN(3, 4)';
    } else {
        //set level filter
        $lvlFilter = " AND stakeholder.lvl  = $indicatorLvl";
    }
} else if ($type == '5') {
    // type text
    $typeText = "CYP";
    //set col name
    $colName = 'tbl_wh_data.wh_issue_up * itminfo_tab.extra';
    //set level filter
    $lvlFilter = ' AND stakeholder.lvl = 4';
}

if ($selPro == 'all') {
    //set province filter
    $provFilter = '';
    //set province name
    $provinceName = 'All';
} else {
    //set province filter
    $provFilter = "AND tbl_warehouse.prov_id = '" . $selPro . "' ";
    //select query
    //gets
    //province name
    $provinceQryRes = mysql_fetch_array(mysql_query("SELECT LocName FROM tbl_locations WHERE PkLocID = '" . $selPro . "' "));
    $provinceName = "\'$provinceQryRes[LocName]\'";
}
//check sector
if ($sector == 'All') {
    //set report type
    $rptType = 'All';
} else {
    //set report type
    $rptType = $sector;
}
if (!empty($selStk) && $selStk != 'all') {
    //set stakeholder filter
    $stkFilter = " AND MainStk.MainStakeholder = '" . $selStk . "'";
} else if ($rptType == 'public' && $selStk == 'all') {
    //set stakeholder filter
    $stkFilter = " AND MainStk.stk_type_id = 0";
} else if ($rptType == 'private' && $selStk == 'all') {
    //set stakeholder filter
    $stkFilter = " AND MainStk.stk_type_id = 0";
}
//end date
$endDate = $selYear . '-' . ($selMonth) . '-01';
//echo $endDate ;exit;
//end date
$endDate = date('Y-m-d', strtotime("-1 days", strtotime("+1 month", strtotime($endDate))));
//start date
$startDate = date('Y-m-d', strtotime("-364 days", strtotime($endDate)));
// Start date and End date
//begin
$begin = new DateTime($startDate);
//end
$end = new DateTime($endDate);
//diff
$diff = $begin->diff($end);
//interval
$interval = DateInterval::createFromDateString('1 month');
//period
$period = new DatePeriod($begin, $interval, $end);
//data array
$dataArr = array();

$qry = "SELECT
	A.DistrictID,
	A.DistrictName,
	B.stkname,
	B.stkid
        FROM
	(
		SELECT
			tbl_locations.PkLocID AS DistrictID,
			tbl_locations.LocName AS DistrictName
		FROM
			tbl_warehouse
		INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
		INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
		WHERE
		DATE_FORMAT(tbl_wh_data.RptDate, '%Y-%m') BETWEEN '" . date('Y-m', strtotime($startDate)) . "' AND '" . date('Y-m', strtotime($endDate)) . "'
		$productFilter
		$provFilter
		GROUP BY
			tbl_warehouse.dist_id
		ORDER BY
			DistrictName ASC
	) A
        LEFT JOIN (
	SELECT
		tbl_warehouse.dist_id,
		MainStk.stkid,
		MainStk.stkname
	FROM
		tbl_warehouse
	INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
	INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
	WHERE
	tbl_warehouse.dist_id = tbl_warehouse.dist_id	
	$provFilter
        $stkFilter
	$lvlFilter
	
	GROUP BY
		tbl_warehouse.dist_id,
		tbl_warehouse.stkid
        ) B ON A.DistrictID = B.dist_id ";
//query result
$qryRes = mysql_query($qry);
//num of record
$num = mysql_num_rows($qryRes);
//fetch results
while ($row = mysql_fetch_array($qryRes)) {
    //data array
    $dataArr[$row['DistrictID'] . '-' . $row['stkid']][] = $row['DistrictName'];
    //data array
    $dataArr[$row['DistrictID'] . '-' . $row['stkid']][] = $row['stkname'];
    //count
    $count = 2;
    //get period
    foreach ($period as $date) {
        //data array
        $dataArr[$row['DistrictID'] . '-' . $row['stkid']][$count] = 'NR';
        //increment count
        $count++;
    }
}

// Headers of the Grid
//header
$header = 'District Id, Sr. No., District, Stakeholder';
//width
$width = '50,60,*,85';
//row
$ro = 'ro,ro,ro,ro';
//count
$count = 2;
//get period
foreach ($period as $date) {
    //month array
    $monthArr[] = $date->format("Y-m");
    //header
    $header .= ',<span>' . $date->format("M-y") . '</span>';
    //width
    $width .= ',65';
    //row
    $ro .= ',ro';
    $newQry = "SELECT
					tbl_locations.PkLocID AS DistrictID,
					tbl_locations.LocName AS DistrictName,
					SUM($colName) AS total,
					MainStk.stkname,
					tbl_warehouse.stkid
				FROM
					tbl_warehouse
				INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
				INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
				INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
				WHERE
					DATE_FORMAT(tbl_wh_data.RptDate, '%Y-%m') = '" . $date->format("Y-m") . "'
					$productFilter
					$provFilter
					$stkFilter
					$lvlFilter
				GROUP BY
					tbl_warehouse.dist_id,
					tbl_warehouse.stkid
				ORDER BY
					DistrictName ASC";
    //query result
     // echo $newQry;exit;
    $qryRes = mysql_query($newQry);
    //fetch result
    while ($row = mysql_fetch_array($qryRes)) {
        //data array
        $dataArr[$row['DistrictID'] . '-' . $row['stkid']][$count] = $row['total'];
    }
    $count++;
}
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
//sum array
$sumArr = array();
$i = 1;
foreach ($dataArr as $disId => $subArr) {
    $xmlstore .= "<row>";

    list($distId, $stkOfcId) = explode('-', $disId);
    //district id
    $xmlstore .= "<cell>$distId</cell>";
    $xmlstore .= "<cell style=\"text-align:center\">" . $i++ . "</cell>";
    $xmlstore .= "<cell>$subArr[0]</cell>";
    $xmlstore .= "<cell>$subArr[1]</cell>";

    foreach ($subArr as $key => $value) {
        if (!isset($sumArr[$key])) {
            //sum array
            $sumArr[$key] = 0;
        }
        $sumArr[$key] += $value;

        if ($key > 1) {
            if ($value != 'NR') {
                $xmlstore .= "<cell style=\"text-align:right\">" . number_format($value) . "</cell>";
            } else {
                $xmlstore .= "<cell style=\"text-align:center;color:#EE2000\">" . $value . "</cell>";
            }
        }
    }
    $xmlstore .= "</row>";
}
$xmlstore .= "<row>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell></cell>";
$xmlstore .= "<cell style=\"text-align:right\">Total</cell>";
foreach ($sumArr as $key => $value) {
    if ($key > 1) {
        $xmlstore .= "<cell style=\"text-align:right\">" . number_format($value) . "</cell>";
    }
}
$xmlstore .= "</row>";
$xmlstore .= "</rows>";
//check selected stakeholder
if ($selStk == 'all') {
    //set stakeholder name
    $stkName = "\'All\'";
} else {
    //select query
    //gets
    //stakeholder
    $stakeNameQryRes = mysql_fetch_array(mysql_query("SELECT stkname FROM stakeholder WHERE stkid = '" . $selStk . "' "));
    //stakeholder name
    $stkName = "\'$stakeNameQryRes[stkname]\'";
}
?>

<div onLoad="doInitGrid()">
   
                
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if ($num > 0) {
                            ?>
                            <table width="100%" cellpadding="0" cellspacing="0" id="myTable">
                                <tr>
                                    <td align="right" style="padding-right:5px;">
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');" title="Export to PDF"/>
                                        <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div id="mygrid_container" style="width:100%; height:390px;"></div>
                                    </td>
                                </tr>
                            </table>
                            <?php
                        } else {
                            echo '<h6>No record found.</h6>';
                        }
                        ?>
                    </div>
                
    </div>

    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("#cspan,<div style='text-align:center;'><?php echo "District " . ucwords($typeText) . " Yearly Report for Sector = '" . ucwords($rptType) . "' Stakeholder(s) = $stkName Province/Region = $provinceName And Product = '$proName'"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.attachHeader("<?php echo $header; ?>");
            mygrid.attachFooter(",<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
            mygrid.setInitWidths("<?php echo $width; ?>");
            mygrid.setColTypes("<?php echo $ro; ?>");
            mygrid.setColumnHidden(0, true);
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.enableCollSpan(true);
            mygrid.setSkin("light");
            mygrid.init();
            //mygrid.loadXML("xml/stock.xml");
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }

        function getStakeholder(val, stk)
        {
            $.ajax({
                url: 'ajax_stk.php',
                data: {type: val, stk: stk},
                type: 'POST',
                success: function (data) {
                    $('#stk_sel').html(data);
                    showProducts('<?php echo (!empty($selItem)) ? $selItem : ''; ?>');
                }
            })
        }

        $(function () {
            showIndLvl('<?php echo $type; ?>');
            $('#type').change(function (e) {
                showIndLvl($(this).val());
            });
            $('#stk_sel').change(function (e) {
                showProducts('');
                $('#prov_sel').html('<option value="">Select</option>');
                showProvinces('');
            });

            $('#sector').change(function (e) {
                $('#item_id').html('<option>Select</option>');
                var val = $('#sector').val();
                getStakeholder(val, '');
            });

            $('#type').change(function (e) {
                showAllProducts($(this).val());
                $("#item_id").val('');
            });
            getStakeholder('<?php echo $rptType; ?>', '<?php echo $selStk; ?>');
            setTimeout(
                    function ()
                    {
                        showAllProducts('<?php echo $type; ?>');
                    }, 1000);
        })
<?php
if (isset($selItem) && !empty($selItem)) {
    ?>
            showProducts('<?php echo $selItem; ?>');
            showProvinces('<?php echo $selPro; ?>');
    <?php
}
?>
        function showAllProducts(indVal) {
            if (indVal == 5) {
                $("#item_id option[value='']").text('All');
                $("#item_id").removeAttr('required');
            } else {
                $("#item_id option[value='']").text('Select');
                $("#item_id").attr('required', 'required');
            }
        }
        function showIndLvl(val) {
            if (val == 4) {
                $('#indicator_col').show();
            } else {
                $('#indicator_col').hide();
            }
        }
        function showProducts(pid) {
            var stk = $('#stk_sel').val();
            $.ajax({
                url: 'ajax_calls.php',
                type: 'POST',
                data: {stakeholder: stk, productId: pid},
                success: function (data) {
                    $('#item_id').html(data);
                }
            })
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
                        $('#prov_sel').html(data);
                    }
                })
            }
        }
    </script>
</div>