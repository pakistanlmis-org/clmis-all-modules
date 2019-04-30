<?php
//echo '<pre>';print_r($_REQUEST);exit;
//report id
$report_id = "CHF";
//selected month
$selMonth = date('m');
//selected year
$selYear = date('Y');
//selected province
$selPro = '';
//selected district
$selDist = '';
//initialize variables
$cspan = $header = $width = $ro = $align = $stkName = $locName = '';
//if submitted
if (isset($_REQUEST['submit'])) {
    //Posted Data Collection

    $selMonth = !empty($_REQUEST['ending_month']) ? $_REQUEST['ending_month'] : '';
    //selected year
    $selYear = !empty($_REQUEST['year_sel']) ? $_REQUEST['year_sel'] : '';
    //selected stakeholder
    $selStk = !empty($_REQUEST['stk_sel']) ? $_REQUEST['stk_sel'] : '';
    //selected province
    $selPro = !empty($_REQUEST['prov_sel']) ? $_REQUEST['prov_sel'] : '';
    //selected district
    $selDist = !empty($_REQUEST['dist']) ? $_REQUEST['dist'] : '';
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
    //get selected province
    $selPro = ($_SESSION['user_id'] == 2054) ? 1 : $_SESSION['user_province1'];
    //set selected province
    $selPro = ($selPro != 10) ? $selPro : 1;
    //selected stakeholder 
    $selStk = $_SESSION['user_stakeholder1'];
    //get selected district
    $selDist = $_SESSION['user_district'];
    //selected district
    $selDist = ($selDist != 10) ? $selDist : 102;
}
//date
$date = '';
//end date1
$endDate1 = $selYear . '-' . ($selMonth) . '-01';
//end date
$endDate = date('Y-m-d', strtotime("-1 days", strtotime("+1 month", strtotime($endDate1))));
//start date
$startDate = date('Y-m-d', strtotime("-11 month", strtotime($endDate1)));
//warehouse info
$whInfo = $dataArr = $inActive = $active = array();
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

// Get all facilities that goes in-active during the selected period
//gets
//warehouse id
//reportnig minth
$getInactiveQry = "SELECT
                            tbl_warehouse.wh_id,
                            DATE_FORMAT(warehouse_status_history.reporting_month, '%Y-%m') AS reporting_month
                    FROM
                            warehouse_status_history
                    INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                    WHERE
                            warehouse_status_history.reporting_month BETWEEN '$startDate' AND '$endDate'
                    AND tbl_warehouse.dist_id = $selDist
                    AND tbl_warehouse.stkid = $selStk
                    AND warehouse_status_history.`status` = 0";
//query result
$getInactiveQryRes = mysql_query($getInactiveQry);
//fetch result
while ($row = mysql_fetch_array($getInactiveQryRes)) {
    $inActive[$row['reporting_month']][] = $row['wh_id'];
}

// Get all facilities that were active during the selected period
foreach ($period as $date) {
    $activeWHQry = "SELECT
						tbl_warehouse.wh_id
					FROM
						tbl_warehouse
					INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
					WHERE
						tbl_warehouse.dist_id = $selDist
					AND tbl_warehouse.stkid = $selStk
					AND stakeholder.lvl = 7
					AND tbl_warehouse.wh_id NOT IN (
						SELECT
							warehouse_status_history.warehouse_id
						FROM
							warehouse_status_history
						INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
						WHERE
							warehouse_status_history.reporting_month = '" . $date->format("Y-m-d") . "'
						AND tbl_warehouse.dist_id = $selDist
						AND tbl_warehouse.stkid = $selStk
						AND warehouse_status_history.`status` = 0
					)";
    //query result
    $activeWHQryRes = mysql_query($activeWHQry);
    //fetch result
    while ($row = mysql_fetch_array($activeWHQryRes)) {
        $active[$date->format("Y-m")][] = $row['wh_id'];
        // Total Active
        $totalActive['month_wise'][$date->format("Y-m")][] = 1;
    }
}
//select query

$getAll = "SELECT
                *
        FROM
                (
                        SELECT
                                tbl_warehouse.wh_id,
                                tbl_warehouse.dhis_code,
                                tbl_warehouse.wh_name,
                                tbl_warehouse.reporting_start_month,
                                tbl_hf_type_rank.hf_type_rank,
                                tbl_warehouse.wh_rank,
                                mainStk.stkname,
                                tbl_locations.LocName
                        FROM
                                tbl_warehouse
                        INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                        INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                        INNER JOIN stakeholder AS mainStk ON tbl_warehouse.stkid = mainStk.stkid
                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                        WHERE
                                stakeholder.lvl = 7
                        AND tbl_hf_type_rank.stakeholder_id = $selStk
                        AND tbl_hf_type_rank.province_id = $selPro
                        AND tbl_warehouse.dist_id = $selDist
                ) A
        GROUP BY
                A.wh_id
        ORDER BY
                IF(A.wh_rank = '' OR A.wh_rank IS NULL, 1, 0),
                A.wh_rank,
                A.hf_type_rank ASC,
                A.wh_name ASC";
//query result
$getAllRes = mysql_query($getAll);
//num of record
$num = mysql_num_rows(mysql_query($getAll));
if ($num > 0) {
    //fetch result
    while ($row = mysql_fetch_array($getAllRes)) {
        //stakeholder name
        $stkName = $row['stkname'];
        //location name
        $locName = $row['LocName'];
        //warehouse info
        $whInfo[$row['wh_id']]['code'] = $row['dhis_code'];
        //warehouse info
        $whInfo[$row['wh_id']]['name'] = (!empty($row['dhis_code'])) ? $row['dhis_code'] . ' - ' . $row['wh_name'] : $row['wh_name'];
        //fetch period
        foreach ($period as $date) {
            //total
            $total['wh_wise'][$row['wh_id']][] = 0;
            if (strtotime($date->format("Y-m")) < strtotime($row['reporting_start_month'])) {
                //data array
                $dataArr[$row['wh_id']][$date->format("Y-m")] = '*';
            } else if (in_array($row['wh_id'], $active[$date->format("Y-m")])) {
                //data array
                $dataArr[$row['wh_id']][$date->format("Y-m")] = '&Chi;';
                if (strtotime($date->format("Y-m")) >= strtotime(date('Y-m')) && $dataArr[$row['wh_id']][date("Y-m", strtotime($date->format("Y-m-01") . " -1 months"))] == 'Left') {
                    //data array
                    $dataArr[$row['wh_id']][$date->format("Y-m")] = 'Left';
                } else {
                    //data array
                    $dataArr[$row['wh_id']][$date->format("Y-m")] = '&Chi;';
                }
            } else if (in_array($row['wh_id'], $inActive[$date->format("Y-m")])) {
                //data array
                $dataArr[$row['wh_id']][$date->format("Y-m")] = 'Left';
            }
        }
    }
    //fetch period
    foreach ($period as $date) {
        //total
        $total['month_wise'][$date->format("Y-m")][] = 0;
        //cspan
        $cspan .= ',#cspan';
        //header
        $header .= ',' . $date->format("M-y");
        //width
        $width .= ',55';
        //row
        $ro .= ',ro';
        //align
        $align .= ',center';
    }
    //cspan
    $cspan = $cspan . ',#cspan,#cspan';
    //header
    $header = ($selStk == 73) ? 'Sr. No.,CMW Name' . $header . ',Total' : 'Sr. No.,Health Facility' . $header . ',Total';
    //width
    $width = '50,*' . $width . ',50';
    //row
    $ro = 'ro,ro,ro' . $ro;
    //align
    $align = 'center,left' . $align . ',center';
    //select query
    //gets
    //warehouse id
    //reporting date
    $getData = "SELECT
                        tbl_warehouse.wh_id,
                        DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') AS reporting_date
                FROM
                        tbl_warehouse
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                WHERE
                        stakeholder.lvl = 7
                AND tbl_hf_data.item_id = 1
                AND	tbl_warehouse.stkid = $selStk
                AND tbl_warehouse.dist_id = $selDist
                AND tbl_hf_data.reporting_date BETWEEN '$startDate' AND '$endDate'
                AND tbl_warehouse.wh_id NOT IN (
                        SELECT
                                warehouse_status_history.warehouse_id
                        FROM
                                warehouse_status_history
                        INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                        WHERE
                                warehouse_status_history.reporting_month = tbl_hf_data.reporting_date
                        AND tbl_warehouse.dist_id = $selDist
                        AND	tbl_warehouse.stkid = $selStk
                        AND warehouse_status_history.`status` = 0
                )
                ORDER BY
                        tbl_hf_data.reporting_date ASC";
    //query result
    $getDataRes = mysql_query($getData);
    //fetch result
    while ($row = mysql_fetch_array($getDataRes)) {
        //data array
        $dataArr[$row['wh_id']][$row['reporting_date']] = '&radic;';
        //total
        $total['wh_wise'][$row['wh_id']][] = 1;
        //total
        $total['month_wise'][$row['reporting_date']][] = 1;
    }

    $imgTrue = PUBLIC_URL . 'assets/img/tick.png';
    $imgFalse = PUBLIC_URL . 'assets/img/cross.png';

    // Generate XML for the Grid
    $xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xmlstore .= "<rows>";
    $totalCount = 0;
    $count = 1;
    foreach ($dataArr as $wh_id => $subArr) {
        $xmlstore .= "<row>";
        $xmlstore .= "<cell>" . $count++ . "</cell>";
        $xmlstore .= "<cell><![CDATA[" . $whInfo[$wh_id]['name'] . "]]></cell>";
        foreach ($subArr as $value) {
            $color = ($value == '&Chi;') ? 'color:#EE2000' : '';
            $xmlstore .= "<cell style=\"text-align:center;$color\"><![CDATA[" . $value . "]]></cell>";
        }
        $totalCount = $totalCount + array_sum($total['wh_wise'][$wh_id]);
        $xmlstore .= "<cell>" . array_sum($total['wh_wise'][$wh_id]) . "</cell>";
        $xmlstore .= "</row>";
    }

    $xmlstore .= "<row>";
    $xmlstore .= "<cell></cell><cell>Total</cell>";
    foreach ($period as $date) {
        $xmlstore .= "<cell>" . array_sum($total['month_wise'][$date->format("Y-m")]) . "</cell>";
    }
    $xmlstore .= "<cell>" . $totalCount . "</cell>";
    $xmlstore .= "</row>";

    $xmlstore .= "<row>";
    $xmlstore .= "<cell></cell><cell>Total Active</cell>";
    foreach ($period as $date) {
        $xmlstore .= "<cell>" . array_sum($totalActive['month_wise'][$date->format("Y-m")]) . "</cell>";
    }
    $xmlstore .= "<cell>" . $totalCount . "</cell>";
    $xmlstore .= "</row>";

    $xmlstore .= "</rows>";
}
?>
</head>
<!-- END HEAD -->

<div  onLoad="doInitGrid()">

            
            <div class="row">
                <div class="col-md-12">
                    <?php if ($num > 0) { ?>
                        <table width="100%" cellpadding="0" cellspacing="0" id="myTable">
                            <tr>
                                <td align="right" style="padding-right:5px;"><img style="cursor:pointer;"
                                                                                  src="<?php echo PUBLIC_URL; ?>images/pdf-32.png"
                                                                                  onClick="mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');"
                                                                                  title="Export to PDF"/> <img
                                        style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png"
                                        onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');"
                                        title="Export to Excel"/></td>
                            </tr>
                            <tr>
                                <td>
                                    <div id="mygrid_container" style="width:100%; height:390px;"></div>
                                </td>
                            </tr>
                        </table>
                        <?php
                    } else {
                        echo "No record found";
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
        mygrid.setHeader("<div style='text-align:center;'><?php echo $stkName . ' Compliance Report for ' . $locName . ' ' . '  From ' . date('M Y', strtotime($startDate)) . ' to ' . date('M Y', strtotime($endDate)); ?></div><?php echo $cspan; ?>");
        mygrid.attachHeader("<?php echo $header; ?>");
        mygrid.attachFooter("<div style='font-size: 10px;'>Note: This report is based on data as on <?php echo date('d/m/Y h:i A'); ?> <br>* = Not present <br> Left = Left the job <br> &radic; = Reported <br> &Chi; = Not reported</div><?php echo $cspan; ?>");
        mygrid.setColAlign("<?php echo $align; ?>");
        mygrid.setInitWidths("<?php echo $width; ?>");
        mygrid.setColTypes("<?php echo $ro; ?>");
        mygrid.enableRowsHover(true, 'onMouseOver'); // `onMouseOver` is the css cla ss name.
        mygrid.setSkin("light");
        mygrid.init();
        mygrid.clearAll();
        mygrid.loadXMLString('<?php echo $xmlstore; ?>');
    }
    $(function () {
        showDistricts('<?php echo $selDist; ?>');
        $('#prov_sel').change(function (e) {
            showDistricts('');
        });
    })
    function showDistricts(dId) {
        var provinceId = $('#prov_sel').val();
        if (provinceId != '') {
            $.ajax({
                url: 'ajax_calls.php',
                data: {provinceId: provinceId, dId: dId, stkId: $('#stk_sel').val()},
                type: 'POST',
                success: function (data) {
                    $('#districtsCol').html(data);
                }
            })
        }
    }
    function showProvinces(pid) {
        var stk = $('#stk_sel').val();
        if (typeof stk !== 'undefined') {
            $.ajax({
                url: 'ajax_stk.php',
                type: 'POST',
                data: {stakeholder: stk, provinceId: pid, showProvinces: 1, hfProvOnly: 1},
                success: function (data) {
                    $('#prov_sel').html(data);
                }
            })
        }
    }
    $(function () {
        $('#stk_sel').change(function (e) {
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