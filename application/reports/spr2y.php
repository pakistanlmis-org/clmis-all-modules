<?php
ini_set('max_execution_time',0);
/**
 * spr2
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
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$rptId = 'spr2y';
//item type 
$itemType = '';

//if submitted
if (isset($_POST['submit'])) {
    //get selected month
    //$selMonth = mysql_real_escape_string($_POST['month_sel']);
    //get selected year
    //$selYear = mysql_real_escape_string($_POST['year_sel']);
    //get selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    
    $stakeholder = mysql_real_escape_string($_POST['stakeholder']);
    //get hf type Id 
    $hfTypeId = mysql_real_escape_string($_POST['hf_type_sel']);
    $fromDate = $_POST['from_date'];
    $toDate = $_POST['to_date'];
    //get reporting date
    //$reportingDate = mysql_real_escape_string($_POST['year_sel']) . '-' . $selMonth . '-01';
    $reportingDate = "BETWEEN '" . $fromDate . "' AND '" . $toDate . "'";
    //get reporting date 
    //$reportingDate = mysql_real_escape_string($_POST['year_sel']) . '-' . $selMonth . '-01';

    if ($fromDate != $toDate) {
        //reporting period							
        $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
    } else {
        //reporting period	
        $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
    }

    $and = ($hfTypeId == 0) ? '' : " AND tbl_warehouse.hf_type_id = $hfTypeId";
    $csArr = array(0, 4, 5);
//select query   
//gets 
// Get Province name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $selProv";
    //fetch results
    $row = mysql_fetch_array(mysql_query($qry));
    $provinceName = $row['LocName'];
    if ($hfTypeId == 0) {
        $hfType = 'All Health Facilities';
    } else {
        //select query   
//gets 
        //Health Facility Type name
        $qry = "SELECT
                    tbl_hf_type.hf_type
                FROM
                    tbl_hf_type
                WHERE
                    tbl_hf_type.pk_id = $hfTypeId";
        //fetch results
        $row = mysql_fetch_array(mysql_query($qry));
        //set hf type
        $hfType = $row['hf_type'];
    }
    //set file name
    $fileName = 'SPR2_' . $hfType . '_' . $provinceName . '_for_' . str_replace(" ", "", str_replace("'", "", str_replace("-", "", $reportingDate)));
}
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
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
                        <h3 class="page-title row-br-b-wp">District Wise Monthly Report on Acceptor by Method</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php
                                //include sub_dist_form
                                include('sub_dist_form.php');
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
                <?php
                //if submitted
                if (isset($_POST['submit'])) {
                    if ($selProv == 2 && (!in_array($hfTypeId, $csArr) || $hfTypeId == '0')) {
                        //select query   
//gets 
                        //method type
                        //district id
                        //new
                        //old
                        //total
                        $csCases = "SELECT
                                        itminfo_tab.method_type,
                                        tbl_warehouse.dist_id,
                                        SUM(IF(tbl_hf_data.item_id = 31, tbl_hf_data.issue_balance, 0)) AS new,
                                        SUM(IF(tbl_hf_data.item_id = 32, tbl_hf_data.issue_balance, 0)) AS old,
                                        SUM(tbl_hf_data.issue_balance) AS total
                                FROM
                                        tbl_warehouse
                                INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                WHERE
                                        DATE_FORMAT(tbl_hf_data.reporting_date,'%Y-%m') $reportingDate
                                AND tbl_warehouse.prov_id = $selProv
                                $and
                                AND itminfo_tab.itm_category = 2
                                GROUP BY
                                        tbl_warehouse.dist_id,
                                        itminfo_tab.method_type";
                    } else {
                        //select query   
//gets 
                        //method type
                        //district id
                        //new
                        //old
                        //total
                        $csCases = "SELECT
										itminfo_tab.method_type,
										tbl_warehouse.dist_id,
										SUM(IF(tbl_hf_data.item_id = 31, (tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp), 0)) AS new,
										SUM(IF(tbl_hf_data.item_id = 32, (tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp), 0)) AS old,
										SUM(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS total
									FROM
										tbl_warehouse
									INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
									INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
									INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
									WHERE
										DATE_FORMAT(tbl_hf_data.reporting_date,'%Y-%m') $reportingDate
									AND tbl_warehouse.prov_id = $selProv
									$and
									AND itminfo_tab.itm_category = 2
									GROUP BY
										tbl_warehouse.dist_id,
										itminfo_tab.method_type";
                    }

                    //select query   
//gets 
//pk location id
//location name
                    //method type
                    //new
                    //old
                    //total
                    $qry = "SELECT
								B.PkLocID,
								B.LocName,
								B.method_type,
								A.new,
								A.old,
								A.total
							FROM
								(
									SELECT
										itminfo_tab.method_type,
										tbl_warehouse.dist_id,
										SUM(tbl_hf_data.new) AS new,
										SUM(tbl_hf_data.old) AS old,
										SUM(tbl_hf_data.new + tbl_hf_data.old) AS total
									FROM
										tbl_warehouse
									INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
									INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
									WHERE
										DATE_FORMAT(tbl_hf_data.reporting_date,'%Y-%m') $reportingDate
									AND tbl_warehouse.prov_id = $selProv
									$and
									AND itminfo_tab.itm_category = 1
									GROUP BY
										tbl_warehouse.dist_id,
										itminfo_tab.method_type
									UNION 
									$csCases
								) A
							RIGHT JOIN (
								SELECT DISTINCT
									itminfo_tab.method_type,
									itminfo_tab.method_rank,
									tbl_locations.PkLocID,
									tbl_locations.LocName
								FROM
									itminfo_tab
								INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item,
								tbl_warehouse
							INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
							INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
							WHERE
								tbl_warehouse.stkid = $stakeholder
							AND tbl_warehouse.prov_id = $selProv
							AND stakeholder_item.stkid = $stakeholder
							#$and
							) B ON A.dist_id = B.PkLocID
							AND A.method_type = B.method_type
							ORDER BY
								B.LocName ASC,
								B.method_rank ASC";

                    //fetch results
                    $qryRes = mysql_query($qry);
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        ?>
                        <?php
                        //include sub_dist_reports
                        include('sub_dist_reports.php');
                        ?>
                        <div class="col-md-12" style="overflow:auto;">
                            <?php
                            //district id
                            $distId = '';
                            //item name
                            $itemId = '';
                            //item type array 				
                            $itemType = array();
                            //all method total				
                            $allMethodTotal['new'] = $allMethodTotal['old'] = $allMethodTotal['total'] = '';
                            //fetch results
                            while ($row = mysql_fetch_array($qryRes)) {
                                //check method type
                                if (!in_array($row['method_type'], $itemType)) {
                                    //set item type
                                    $itemType[] = $row['method_type'];
                                }
                                //check district id
                                if ($distId != $row['PkLocID']) {
                                    //set district name
                                    $distName[$row['PkLocID']] = $row['LocName'];
                                    //set district id
                                    $distId = $row['PkLocID'];
                                }
                                //method type new
                                $data[$row['PkLocID']]['new'][$row['method_type']] = $row['new'];
                                //method type old
                                $data[$row['PkLocID']]['old'][$row['method_type']] = $row['old'];
                                //method type total
                                $data[$row['PkLocID']]['total'][$row['method_type']] = $row['total'];
                                //method type new
                                $grand['new'][$row['method_type']][] = $row['new'];
                                //method type old
                                $grand['old'][$row['method_type']][] = $row['old'];
                                //method type total
                                $grand['total'][$row['method_type']][] = $row['total'];
                            }
                            ?>
                            <table width="100%">
                                <tr>
                                    <td align="center">
                                        <h4 class="center">
                                            District wise Monthly Report on Acceptor by Method for <?php echo $hfType; ?> <br>
                                            <?php echo $reportingPeriod . ' , Province ' . $provinceName; ?>
                                        </h4>
                                    </td>
                                    <td>
                                        <h4 class="right">SPR-2</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 10px;">
                                        <table id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">S.No</th>
                                                    <th rowspan="2" width="100">District</th>
                                                    <?php
                                                    foreach ($itemType as $type) {
                                                        if ($type == 'Contraceptives Surgery') {
                                                            if (in_array($hfTypeId, $csArr) || $selProv == 2) {
                                                                echo "<th colspan=\"3\">$type</th>";
                                                            }
                                                        } else {
                                                            echo "<th colspan=\"3\">$type</th>";
                                                        }
                                                    }
                                                    ?>
                                                    <th colspan="3">All Methods</th>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    foreach ($itemType as $type) {
                                                        if ($type == 'Contraceptives Surgery') {
                                                            if (in_array($hfTypeId, $csArr) || $selProv == 2) {
                                                                echo "<th width=\"50\">Male</th>";
                                                                echo "<th width=\"50\">Female</th>";
                                                                echo "<th width=\"50\">Total</th>";
                                                            }
                                                        } else {
                                                            echo "<th width=\"50\">New</th>";
                                                            echo "<th width=\"50\">Old</th>";
                                                            echo "<th width=\"50\">Total</th>";
                                                        }
                                                    }
                                                    ?>
                                                    <th width="50">New</th>
                                                    <th width="50">Old</th>
                                                    <th width="50">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $counter = 1;
                                                foreach ($distName as $id => $name) {
                                                    ?>
                                                    <tr>
                                                        <td class="center"><?php echo $counter++; ?></td>
                                                        <td><?php echo $name; ?></td>
                                                        <?php
                                                        //district new 
                                                        $districtNew = 0;
                                                        //district old
                                                        $districtOld = 0;
                                                        //district total
                                                        $districtTotal = 0;
                                                        foreach ($itemType as $type) {
                                                            if ($type == 'Contraceptives Surgery') {
                                                                if (in_array($hfTypeId, $csArr) || $selProv == 2) {
                                                                    echo "<td class=\"right\">" . ((!empty($data[$id]['new'][$type])) ? number_format($data[$id]['new'][$type]) : '0') . "</td>";
                                                                    echo "<td class=\"right\">" . ((!empty($data[$id]['old'][$type])) ? number_format($data[$id]['old'][$type]) : '0') . "</td>";
                                                                    echo "<td class=\"right\">" . ((!empty($data[$id]['total'][$type])) ? number_format($data[$id]['total'][$type]) : '0') . "</td>";
                                                                }
                                                            } else {
                                                                echo "<td class=\"right\">" . ((!empty($data[$id]['new'][$type])) ? number_format($data[$id]['new'][$type]) : '0') . "</td>";
                                                                echo "<td class=\"right\">" . ((!empty($data[$id]['old'][$type])) ? number_format($data[$id]['old'][$type]) : '0') . "</td>";
                                                                echo "<td class=\"right\">" . ((!empty($data[$id]['total'][$type])) ? number_format($data[$id]['total'][$type]) : '0') . "</td>";
                                                            }
                                                        }
                                                        //set district 
                                                        $districtNew = array_sum($data[$id]['new']);
                                                        //set district old
                                                        $districtOld = array_sum($data[$id]['old']);
                                                        //set district total		
                                                        $districtTotal = array_sum($data[$id]['total']);

                                                        $allMethodTotal['new'] += $districtNew;
                                                        $allMethodTotal['old'] += $districtOld;
                                                        $allMethodTotal['total'] += $districtTotal;

                                                        echo "<td class=\"right\">" . ((!empty($districtNew)) ? number_format($districtNew) : '0') . "</td>";
                                                        echo "<td class=\"right\">" . ((!empty($districtOld)) ? number_format($districtOld) : '0') . "</td>";
                                                        echo "<td class=\"right\">" . ((!empty($districtTotal)) ? number_format($districtTotal) : '0') . "</td>";
                                                        ?>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="right">Grand Total</th>
                                                        <?php
                                                        foreach ($itemType as $type) {
                                                            //set new	
                                                            $new = array_sum($grand['new'][$type]);
                                                            //set old	
                                                            $old = array_sum($grand['old'][$type]);
                                                            //set total	
                                                            $total = array_sum($grand['total'][$type]);

                                                            if ($type == 'Contraceptives Surgery') {
                                                                if (in_array($hfTypeId, $csArr) || $selProv == 2) {
                                                                    echo "<th class=\"right\">" . ((!empty($new)) ? number_format($new) : '0') . "</th>";
                                                                    echo "<th class=\"right\">" . ((!empty($old)) ? number_format($old) : '0') . "</th>";
                                                                    echo "<th class=\"right\">" . ((!empty($total)) ? number_format($total) : '0') . "</th>";
                                                                }
                                                            } else {
                                                                echo "<th class=\"right\">" . ((!empty($new)) ? number_format($new) : '0') . "</th>";
                                                                echo "<th class=\"right\">" . ((!empty($old)) ? number_format($old) : '0') . "</th>";
                                                                echo "<th class=\"right\">" . ((!empty($total)) ? number_format($total) : '0') . "</th>";
                                                            }
                                                        }
                                                        echo "<th class=\"right\">" . ((!empty($allMethodTotal['new'])) ? number_format($allMethodTotal['new']) : '0') . "</th>";
                                                        echo "<th class=\"right\">" . ((!empty($allMethodTotal['old'])) ? number_format($allMethodTotal['old']) : '0') . "</th>";
                                                        echo "<th class=\"right\">" . ((!empty($allMethodTotal['total'])) ? number_format($allMethodTotal['total']) : '0') . "</th>";
                                                        ?>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                } else {
                    echo "No record found";
                }
            }
// Unset varibles
            unset($data, $distName, $itemType, $grand);
            ?>
        </div>
    </div>
</div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
//include combos
include ('combos.php');
?><script>
    $(function() {
        $('#stakeholder').change(function(e) {
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
                data: {stakeholder: stk, provinceId: pid, showProvinces: 1},
                success: function(data) {
                    $('#prov_sel').html(data);
                }
            })
        }
    }
</script>
</body>
</html>