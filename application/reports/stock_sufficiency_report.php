<?php
/**
 * stock_sufficiency_report
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
//report id
$report_id = "STOCKSUFFICIENCY";

$districtId = '';
$filter_prov='';
//If for submitted
if (isset($_POST['submit'])) {
    //Getting month_sel
    $selMonth = mysql_real_escape_string($_POST['month_sel']);
    //Getting year_sel
    $selYear = mysql_real_escape_string($_POST['year_sel']);
    //Getting prov_sel

    $stakeholder = mysql_real_escape_string($_POST['stakeholder']);
    $selProv = mysql_real_escape_string($_POST['prov_sel']); 
    if($selProv==""){
        $filter_prov="";
    }
elseif($selProv!=""){
     $filter_prov='AND summary_district.province_id = '.$selProv;
      
} 
    //Getting year_sel
    $reportingDate = mysql_real_escape_string($_POST['year_sel']) . '-' . $selMonth . '-01';
//   $objstk->m_npkId = $stakeholder;
//    $rsSql = $objstk->GetStakeholdersById();
//    $stk_data = mysql_fetch_assoc($rsSql);
//     echo '<pre>';print_r($stk_data);exit;
    // select query
    // Get Province name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $selProv";
    //Query result
    $row = mysql_fetch_array(mysql_query($qry));
    //province name
    $provinceName = $row['LocName'];

    $fileName = 'stock_sufficiency_report_' . $provinceName . '_for_' . date('M-Y', strtotime($reportingDate));
}
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        //Including top
        include PUBLIC_PATH . "html/top.php";
        //Including top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Provincial Monthly Stock Sufficiency Report</h3>
                        <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Month</label>
                                                    <div class="form-group">
                                                        <select name="month_sel" id="month_sel" class="form-control input-sm" required>
                                                            <?php
                                                            for ($i = 1; $i <= 12; $i++) {
                                                                //check selMonth
                                                                if ($selMonth == $i) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <?php // Populate month_sel combo?>
                                                                <option value="<?php echo date('m', mktime(0, 0, 0, $i, 1)); ?>"<?php echo $sel; ?> ><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Year</label>
                                                    <div class="form-group">
                                                        <select name="year_sel" id="year_sel" class="form-control input-sm" required>
                                                            <?php
                                                            for ($j = date('Y'); $j >= 2010; $j--) {
                                                                //check selYear
                                                                if ($selYear == $j) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <?php // Populate year_sel combo?>
                                                                <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Stakeholder</label>
                                                    <div class="form-group">
                                                        <select name="stakeholder" id="stakeholder" required class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <?php
                                                            $querys = "SELECT
                                                        stakeholder.stkid,
                                                        stakeholder.stkname
                                                        FROM
                                                        stakeholder
                                                        WHERE
                                                        stakeholder.ParentID IS NULL
                                                        AND stakeholder.stk_type_id IN (0, 1) AND
                                                        stakeholder.is_reporting = 1 AND stakeholder.lvl=1
                                                        $where
                                                        ORDER BY
                                                        stakeholder.stkorder ASC";
                                                            //query result
                                                            $rsprov = mysql_query($querys) or die();
                                                            $stk_name = '';
                                                            while ($rowp = mysql_fetch_array($rsprov)) {
                                                                if ($stakeholder == $rowp['stkid']) {
                                                                    $sel = "selected='selected'";
                                                                    $stk_name = $rowp['stkname'];
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //Populate prov_sel combo
                                                                ?>
                                                                <option value="<?php echo $rowp['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowp['stkname']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Province</label>
                                                    <div class="form-group">
                                                        <select name="prov_sel" id="prov_sel"  class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <?php 
                                                                $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                        AND parentid IS NOT NULL ";
                                                                //query result
                                                                $rsprov = mysql_query($queryprov) or die();
                                                                $prov_name = '';
                                                                while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                    if ($selProv == $rowprov['prov_id']) {
                                                                        $sel = "selected='selected'";
                                                                        $prov_name = $rowprov['prov_title'];
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    //Populate prov_sel combo
                                                                    ?>
                                                                    <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                                    <?php
                                                                } 
                                                            ?>                        
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="submit" class="btn btn-primary input-sm">Go</button>
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
                <?php
                //Checking submit
                if (isset($_POST['submit'])) {
                    //This query gets
                    //B.PkLocID,
                    //LocName,
                    //itm_name,
                    //distMOS,
                    //fieldMOS,
                    $qry = "SELECT
								B.PkLocID,
								B.LocName,
								B.itm_name,

                                                                if(avg_consumption >0, (A.distMOS), 'UNK') AS distMOS,
                                                                if(avg_consumption >0, (A.fieldMOS), 'UNK') AS fieldMOS,
                                                                if(avg_consumption >0, (A.distMOS + A.fieldMOS), 'UNK') AS totalMOS,
								(A.distMOS + A.fieldMOS) AS totalMOS1
							FROM
								(
									SELECT
										tbl_locations.PkLocID,
										tbl_locations.LocName,
								  		itminfo_tab.itmrec_id,

                                                                                summary_district.avg_consumption,
										ROUND(
											(
												summary_district.soh_district_store / summary_district.avg_consumption
											),
											2
										) AS distMOS,
										ROUND(
											(
												(
													summary_district.soh_district_lvl - summary_district.soh_district_store
												) / summary_district.avg_consumption
											),
											2
										) AS fieldMOS,
										itminfo_tab.itm_name
									FROM
										summary_district
									INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
									INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
									WHERE
										summary_district.stakeholder_id = $stakeholder
									$filter_prov
									AND summary_district.reporting_date = '$reportingDate'
									GROUP BY
										summary_district.district_id,
										itminfo_tab.itmrec_id
								) A
							  RIGHT JOIN (
								SELECT DISTINCT
									itminfo_tab.itmrec_id,
									itminfo_tab.frmindex,
									itminfo_tab.method_rank,
									itminfo_tab.itm_name,
									tbl_locations.PkLocID,
									tbl_locations.LocName
								FROM
									itminfo_tab
								INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item,
								tbl_warehouse
								INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
								INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
								WHERE
									tbl_warehouse.stkid = $stakeholder
								AND stakeholder_item.stkid = $stakeholder
								AND itminfo_tab.itm_category = 1
								AND tbl_warehouse.prov_id = $selProv
							) B ON A.PkLocID = B.PkLocID
							AND A.itmrec_id = B.itmrec_id
							ORDER BY
								B.LocName ASC,
								B.method_rank,
								B.frmindex";
                    //Query result
                    //echo $qry;
                    $qryRes = mysql_query($qry);
                    //fetch data from qry
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        ?>
                        <?php include('sub_dist_reports.php'); ?>
                        <div>
                            <div class="col-md-12" style="overflow: auto;">
                                <?php
                                //whid
                                $whId = '';
                                //itemid
                                $itemId = '';
                                //itemtype
                                $itemType = array();
                                //fetch data from qryRes
                                while ($row = mysql_fetch_array($qryRes)) {
                                    if (!in_array($row['itm_name'], $itemType)) {
                                        $itemType[] = $row['itm_name'];
                                    }
                                    //Checking whid
                                    if ($whId != $row['PkLocID']) {
                                        $whName[$row['PkLocID']] = $row['LocName'];
                                        $whId = $row['PkLocID'];
                                    }
                                    //new
                                    $data[$row['PkLocID']]['new'][] = $row['distMOS'];
                                    //old
                                    $data[$row['PkLocID']]['old'][] = $row['fieldMOS'];
                                    //total
                                    $data[$row['PkLocID']]['total'][] = $row['totalMOS'];
                                }
                                ?>
                                <table width="100%" >
                                    <tr>
                                        <td align="center"><h4 class="center"> Provincial Stock Sufficiency Report <br>
                                                For the month of <?php echo date('M', mktime(0, 0, 0, $selMonth, 1)) . '-' . $selYear . ', Province ' . $provinceName; ?> </h4></td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 10px;">
                                            <table id="myTable" class=" " cellspacing="0" align="center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2">S.No</th>
                                                        <th rowspan="2" width="100">District</th>
                                                        <?php
                                                        //get data from itemType
                                                        foreach ($itemType as $name) {

                                                            echo "<th colspan=\"4\">$name</th>";
                                                        }
                                                        ?>
                                                    </tr>
                                                    <tr>
                                                        <?php
                                                        foreach ($itemType as $name) {
                                                            echo "<th width=\"50\">Dist. Store MOS</th>";
                                                            echo "<th width=\"50\">Field MOS</th>";
                                                            echo "<th width=\"50\">Total MOS</th>";
                                                            echo "<th width=\"60\">Remarks</th>";
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $counter = 1;
                                                    foreach ($whName as $id => $name) {
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?php echo $counter++; ?></td>
                                                            <td><?php echo $name; ?></td>
                                                            <?php
                                                            //count
                                                            $count = 0;
                                                            //all New
                                                            $allNew = 0;
                                                            //all Old
                                                            $allOld = 0;
                                                            //all total
                                                            $allTotal = 0;
                                                            foreach ($data[$id]['new'] as $val) {
                                                                $class_red = "";
                                                                 if ( $val == 'UNK') {
                                                                    $class_red = "right bg-yellow";
                                                                } elseif ( $data[$id]['total'][$count] == 0 ) {
                                                                    $class_red = "right  bg-red-pink";
                                                                } else{
                                                                    $class_red = "right ";
                                                                }

                                                                echo "<td class=\"$class_red\" " . ( (!empty($val) && $val != 'UNK') ? '>' . number_format($val, 2) : ' style="background:#eee"> UNK ' ) . "&nbsp</td>";
                                                                echo "<td class=\"$class_red\" " . ( (!empty($data[$id]['old'][$count]) && $data[$id]['old'][$count] != 'UNK') ? '>' . number_format($data[$id]['old'][$count], 2) : ' style="background:#eee"> UNK ' ) . "&nbsp</td>";
                                                                echo "<td class=\"$class_red\" " . ( (!empty($data[$id]['total'][$count]) && $data[$id]['total'][$count] != 'UNK') ? '>' . number_format($data[$id]['total'][$count], 2) : ' style="background:#eee"> UNK ' ) . "&nbsp</td>";
                                                                echo "<td class=\"right\">&nbsp;</td>";
                                                                //all new
                                                                $allNew += $val;
                                                                //all old
                                                                $allOld += $data[$id]['old'][$count];
                                                                //all total
                                                                $allTotal += $data[$id]['total'][$count];
                                                                $count++;
                                                            }
                                                            ?>
                                                        </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                </tbody>
                                            </table></td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 10px;" colspan="15"><table cellspacing="0" align="left" id="myTable">
                                                <tbody>
                                                    <tr>
                                                        <td> Note: The stock outs measured on the basis of less than 2 months stock. </td>
                                                    </tr>
                                                </tbody>
                                            </table></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                
                    <div class="row"  >
                        <div class="col-md-12">
                            <div class=" ">
                                <div class="note note-info h6"  ><em><?=$lastUpdateText?></em></div>
                            </div>
                        </div>
                    </div>
                
                    </div>
        <?php
    } else {
        echo "No record found";
    }
}
// Unset varibles
unset($data, $issue, $itemType, $whName);
?>
        </div>
    </div>
</div>
<?php
//Including footer
include PUBLIC_PATH . "/html/footer.php";
?>
<script>
    function printContents() {
        var w = 900;
        var h = screen.height;
        var left = Number((screen.width / 2) - (w / 2));
        var top = Number((screen.height / 2) - (h / 2));
        var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes,left=" + left + ",top=" + top + ",width=" + w + ",height=" + h;
        var printingContents = document.getElementById("export").innerHTML;
        var docprint = window.open("", "", dispSetting);
        docprint.document.open();
        docprint.document.write('<html><head><title>SPR-2</title>');
        docprint.document.write('</head><body onLoad="self.print();"><center>');
        docprint.document.write(printingContents);
        docprint.document.write('</center></body></html>');
        docprint.document.close();
        docprint.focus();
    }
    $(function () {
        $('#stakeholder').change(function (e) {
            $('#itm_id, #prov_sel').html('<option value="">Select</option>');

            showProvinces('');
        });
    })
    function showProvinces(pid) {
        var stk = $('#stakeholder').val();
        if (typeof stk !== 'undefined')
        {
            $.ajax({
                url: 'ajax_stk.php',
                type: 'POST',
                data: {stakeholder: stk, provinceId: pid, showProvinces: 1, showAllOpt: 0},
                success: function (data) { 
                    $('#prov_sel').html(data);
                }
            })
        }
    }
</script>
</body>
</html>