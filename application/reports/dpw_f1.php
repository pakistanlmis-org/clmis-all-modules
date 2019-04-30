<?php
/**
 * dpw_f1
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
$rptId = 'dpw_f1';
//district Id 
$districtId = '';
//if submitted
if (isset($_POST['submit'])) {
    //from date
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //to date
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //district Id 
    $districtId = mysql_real_escape_string($_POST['district']);
    //select query
    // Get district name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $districtId";
    //fetch result
    $row = mysql_fetch_array(mysql_query($qry));
    //set  distrct name 
    $distrctName = $row['LocName'];
    //set file name 
    $fileName = 'DPW-F1_' . $distrctName . '_for_' . $fromDate . '-' . $toDate;
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
                        <h3 class="page-title row-br-b-wp">District Monthly Report of Family Planning Activities</h3>
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
                if (isset($_POST['submit'])) {
                    //select query 
                    //gets
                    //warehouse id,
                    //warehouse name,
                    //method type,
                    //method rank,
                    //new,
                    //old,
                    //sold,
                    //surgery Cases,
                    //pre natal new,
                    //pre_natal old,
                    //post_natal new,
                    //post_natal old,
                    //ailment children,
                    //ailment adults,
                    //general ailment
                    $qry = "SELECT
								A.wh_id,
								A.wh_name,
								A.method_type,
								A.method_rank,
								SUM(A.new) AS new,
								SUM(A.old) AS old,
								SUM(A.issue_balance) AS sold,
								SUM(A.surCases) AS surCases,
								B.pre_natal_new,
								B.pre_natal_old,
								B.post_natal_new,
								B.post_natal_old,
								B.ailment_children,
								B.ailment_adults,
								B.general_ailment
							FROM
								(
									SELECT
										tbl_warehouse.wh_id,
										tbl_warehouse.wh_name,
										tbl_warehouse.wh_rank,
										tbl_hf_type_rank.hf_type_rank,
										SUM(tbl_hf_data.new) AS new,
										SUM(tbl_hf_data.old) AS old,
										SUM(IF (tbl_warehouse.hf_type_id IN(4, 5), (IF (tbl_hf_data_reffered_by.hf_type_id IN(4, 5), tbl_hf_data_reffered_by.ref_surgeries, 0)),  if(itminfo_tab.itm_category=2, tbl_hf_data.issue_balance,0) )) AS surCases,
										SUM(tbl_hf_data.issue_balance) AS issue_balance,
										itminfo_tab.method_type,
										itminfo_tab.method_rank
									FROM
										tbl_warehouse
									INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
									INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
									LEFT JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
									INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
									WHERE
										tbl_warehouse.dist_id = $districtId
									AND tbl_warehouse.stkid = $stakeholder
									AND tbl_hf_type_rank.stakeholder_id = $stakeholder
									AND tbl_hf_type_rank.province_id = $selProv
									AND DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') BETWEEN '$fromDate' AND '$toDate'
                                                                            AND itminfo_tab.itm_category IN (1,2)
									GROUP BY
										tbl_warehouse.wh_id,
										itminfo_tab.itm_id
								) A
							LEFT JOIN (
								SELECT
									tbl_warehouse.wh_id,
									SUM(tbl_hf_mother_care.pre_natal_new) pre_natal_new,
									SUM(tbl_hf_mother_care.pre_natal_old) AS pre_natal_old,
									SUM(tbl_hf_mother_care.post_natal_new) AS post_natal_new,
									SUM(tbl_hf_mother_care.post_natal_old) AS post_natal_old,
									SUM(tbl_hf_mother_care.ailment_children) AS ailment_children,
									SUM(tbl_hf_mother_care.ailment_adults) AS ailment_adults,
									SUM(tbl_hf_mother_care.general_ailment) AS general_ailment
								FROM
									tbl_warehouse
								INNER JOIN tbl_hf_mother_care ON tbl_warehouse.wh_id = tbl_hf_mother_care.warehouse_id
								WHERE
									tbl_warehouse.dist_id = $districtId
								AND tbl_warehouse.stkid = $stakeholder
								AND DATE_FORMAT(tbl_hf_mother_care.reporting_date, '%Y-%m') BETWEEN '$fromDate' AND '$toDate'
								GROUP BY
									tbl_warehouse.wh_id
							) B ON A.wh_id = B.wh_id
							GROUP BY
								A.wh_id,
								A.method_type
							ORDER BY
								IF (A.wh_rank = '' OR A.wh_rank IS NULL, 1, 0),
								A.wh_rank ASC,
								IF (A.hf_type_rank = '' OR A.hf_type_rank IS NULL, 1, 0),
								A.hf_type_rank ASC,
								A.wh_name ASC,
								A.method_rank ASC";
                    //query result
                    $qryRes = mysql_query($qry);
                    //if result exists
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        ?>
                        <?php
                        //include sub_dist_reports
                        include('sub_dist_reports.php');
                        ?>
                        <div class="col-md-12" style="overflow:auto;">
                            <?php
                            //method				
                            $method = '';
                            //warehouse
                            $whName = '';
                            //warehouse id
                            $wh_id = '';
                            //method 
                            $method = $whName = array();
                            //fetch result
                            while ($row = mysql_fetch_array($qryRes)) {
                                //method type
                                if (!in_array($row['method_type'], $method)) {
                                    //set method					
                                    $method[] = $row['method_type'];
                                }
                                //check warehouse name
                                if (!in_array($row['wh_name'], $whName)) {
                                    //set warehouse name	
                                    $whName[$row['wh_id']] = $row['wh_name'];
                                }
                                //new
                                $data[$row['wh_id']][$row['method_type']]['new'] = $row['new'];
                                //old
                                $data[$row['wh_id']][$row['method_type']]['old'] = $row['old'];
                                //sold
                                $data[$row['wh_id']][$row['method_type']]['sold'] = $row['sold'];
                                //pre natal new
                                $data[$row['wh_id']]['pre_natal_new'] = $row['pre_natal_new'];
                                //pre natal old
                                $data[$row['wh_id']]['pre_natal_old'] = $row['pre_natal_old'];
                                //post natal new
                                $data[$row['wh_id']]['post_natal_new'] = $row['post_natal_new'];
                                //post natal old
                                $data[$row['wh_id']]['post_natal_old'] = $row['post_natal_old'];
                                //ailment children
                                $data[$row['wh_id']]['ailment_children'] = $row['ailment_children'];
                                //ailment adults
                                $data[$row['wh_id']]['ailment_adults'] = $row['ailment_adults'];
                                //general ailment
                                $data[$row['wh_id']]['general_ailment'] = $row['general_ailment'];
                                //surgery Cases
                                @$data[$row['wh_id']]['surCases'] += $row['surCases'];

                                // To show Total
                                if ($row['method_type'] == 'Contraceptives Surgery') {
                                    //surgery Cases total
                                    @$total['surCases'][] += $row['surCases'];
                                } else {
                                    //new total
                                    $total[$row['method_type']]['new'][] = $row['new'];
                                    //old total
                                    $total[$row['method_type']]['old'][] = $row['old'];
                                    //sold total
                                    $total[$row['method_type']]['sold'][] = $row['sold'];
                                }

                                //check warehouse id
                                if ($wh_id != $row['wh_id']) {
                                    //pre natal new total
                                    $total['pre_natal_new'][] = $row['pre_natal_new'];
                                    //pre natal old total
                                    $total['pre_natal_old'][] = $row['pre_natal_old'];
                                    //post natal new total
                                    $total['post_natal_new'][] = $row['post_natal_new'];
                                    //post natal old total
                                    $total['post_natal_old'][] = $row['post_natal_old'];
                                    //ailment children total
                                    $total['ailment_children'][] = $row['ailment_children'];
                                    //ailment adult total
                                    $total['ailment_adults'][] = $row['ailment_adults'];
                                    //general ailment total
                                    $total['general_ailment'][] = $row['general_ailment'];
                                    //set warehouse id
                                    $wh_id = $row['wh_id'];
                                }
                            }
                            //echo '<pre>';print_r($data);exit;
                            ?>
                            <table width="100%">
                                <tr>
                                    <td align="center">

                                        <h4 class="center bold">
                                            CONTRACEPTIVE PERFORMANCE IN RELATION TO TARGETS AND PERCENTAGE	<br>
                                            Achievement
                                            <?php
                                            //check	from Date 				
                                            if ($fromDate != $toDate) {
                                                //set reporting Period							
                                                $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
                                            } else {
                                                //set reporting Period								
                                                $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
                                            }
                                            ?>
                                            <?php
                                            //show reporting Period							
                                            echo $reportingPeriod . ', District ' . $distrctName;
                                            ?>
                                        </h4>
                                    </td>
                                    <td>
                                        <h4 class="right">DPW F1</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <?php
                                        //check selected province
                                        if ($selProv == 3) {
                                            ?>
                                            <table id="myTable" cellspacing="0" align="center" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="3">S.No</th>
                                                        <th rowspan="3" style="width:200px !important;">Health Facility</th>
                                                        <th colspan="<?php echo sizeof($method) + 9; ?>">Family Planning</th>
                                                        <th colspan="6">MCH and General Patient</th>
                                                    </tr>
                                                    <tr>
                                                        <?php
                                                        //get method
                                                        foreach ($method as $methodName) {
                                                           if ($methodName == 'Contraceptives Surgery') {
                                                                //show method name
                                                                echo "<th>Surgery Cases</th>";
                                                            } else {
                                                                //show method name
                                                                echo "<th colspan=\"3\">$methodName</th>";
                                                            }
                                                        }
                                                        ?>
                                                        <th colspan="2">Ante-natal</th>
                                                        <th colspan="2">Post-natal</th>
                                                        <th colspan="2">MCH</th>
                                                    </tr>
                                                    <tr>
                                                        <?php
                                                        //get method
                                                        foreach ($method as $methodName) {
                                                           if ($methodName == 'Contraceptives Surgery') {
                                                                //show method name
                                                                echo "<th>No. of CS Cases</th>";
                                                            } else {
                                                                //show method name	
                                                                echo "<th>New Case</th>";
                                                                //show method name
                                                                echo "<th>Old Case</th>";
                                                                if ($methodName == 'Injectables') {
                                                                    //show method name
                                                                    echo "<th>No. of Injectables</th>";
                                                                }
                                                                if ($methodName == 'IUD') {
                                                                    //show method name
                                                                    echo "<th>No. of IUDs</th>";
                                                                }
                                                                if ($methodName == 'Oral Pills') {
                                                                    //show method name
                                                                    echo "<th>No. of Oral Pills</th>";
                                                                }
                                                                if ($methodName == 'Condoms') {
                                                                    //show method name
                                                                    echo "<th>No. of Condoms Sold</th>";
                                                                }
                                                                if ($methodName == 'Implant') {
                                                                    //show method name
                                                                    echo "<th>Implant</th>";
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <th>New Case</th>
                                                        <th>Old Case</th>
                                                        <th>New Case</th>
                                                        <th>Old Case</th>
                                                        <th>Children</th>
                                                        <th>Adults</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    //increment counter
                                                    $counter = 1;
                                                    //get warehouse
                                                    foreach ($whName as $whId => $whName) {
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?php echo $counter++; ?></td>
                                                            <td><?php echo $whName; ?></td>
                                                            <?php
                                                            //get method							
                                                            foreach ($method as $methodName) {
                                                                //check method								
//                                                                if ($methodName == 'IUD') {
//                                                                    //show method								
//                                                                    echo "<td class=\"right\">" . ( ($data[$whId][$methodName]['new'] != 0) ? number_format($data[$whId][$methodName]['new']) : 0 ) . "</td>";
//                                                                    //show method								
//                                                                    echo "<td class=\"right\">" . ( ($data[$whId][$methodName]['old'] != 0) ? number_format($data[$whId][$methodName]['old']) : 0 ) . "</td>";
//                                                                } else
                                                                    if ($methodName == 'Contraceptives Surgery') {
                                                                    //show method	
                                                                    echo "<td class=\"right\">" . ( ($data[$whId]['surCases'] != 0) ? number_format($data[$whId]['surCases']) : 0 ) . "</td>";
                                                                } else {
                                                                    //show method
                                                                    echo "<td class=\"right\">" . ( ($data[$whId][$methodName]['new'] != 0) ? number_format($data[$whId][$methodName]['new']) : 0 ) . "</td>";
                                                                    //show method
                                                                    echo "<td class=\"right\">" . ( ($data[$whId][$methodName]['old'] != 0) ? number_format($data[$whId][$methodName]['old']) : 0 ) . "</td>";
                                                                    //show method
                                                                    echo "<td class=\"right\">" . ( ($data[$whId][$methodName]['sold'] != 0) ? number_format($data[$whId][$methodName]['sold']) : 0 ) . "</td>";
                                                                }
                                                            }
                                                            ?>
                                                            <td class="right"><?php echo ($data[$whId]['pre_natal_new'] != 0) ? number_format($data[$whId]['pre_natal_new']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['pre_natal_old'] != 0) ? number_format($data[$whId]['pre_natal_old']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['post_natal_new'] != 0) ? number_format($data[$whId]['post_natal_new']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['post_natal_old'] != 0) ? number_format($data[$whId]['post_natal_old']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['ailment_children'] != 0) ? number_format($data[$whId]['ailment_children']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['ailment_adults'] != 0) ? number_format($data[$whId]['ailment_adults']) : 0; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="right">Total</th>
                                                        <?php
                                                        //get method							
                                                        foreach ($method as $methodName) {
                                                            //check method							
                                                            if ($methodName != 'Contraceptives Surgery') {
                                                                //show method
                                                                echo "<th class=\"right\">" . ( (array_sum($total[$methodName]['new']) != 0) ? number_format(array_sum($total[$methodName]['new'])) : 0 ) . "</th>";
                                                                //show method
                                                                echo "<th class=\"right\">" . ( (array_sum($total[$methodName]['old']) != 0) ? number_format(array_sum($total[$methodName]['old'])) : 0 ) . "</th>";
                                                                //show method
                                                                echo "<th class=\"right\">" . ( (array_sum($total[$methodName]['sold']) != 0) ? number_format(array_sum($total[$methodName]['sold'])) : 0 ) . "</th>";
                                                            }
                                                        }
                                                        ?>
                                                        <th class="right"><?php echo (array_sum($total['surCases']) != 0) ? number_format(array_sum($total['surCases'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['pre_natal_new']) != 0) ? number_format(array_sum($total['pre_natal_new'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['pre_natal_old']) != 0) ? number_format(array_sum($total['pre_natal_old'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['post_natal_new']) != 0) ? number_format(array_sum($total['post_natal_new'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['post_natal_old']) != 0) ? number_format(array_sum($total['post_natal_old'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['ailment_children']) != 0) ? number_format(array_sum($total['ailment_children'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['ailment_adults']) != 0) ? number_format(array_sum($total['ailment_adults'])) : 0; ?></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <?php
                                        } else {
                                            ?>    
                                            <table id="myTable" cellspacing="0" align="center" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="3">S.No</th>
                                                        <th rowspan="3" style="width:200px !important;">Health Facility</th>
                                                        <th colspan="<?php echo sizeof($method) + 9; ?>">Family Planning</th>
                                                        <th colspan="7">MCH and General Patient</th>
                                                    </tr>
                                                    <tr>
                                                        <?php
                                                        //check method
                                                        foreach ($method as $methodName) {
                                                          if ($methodName == 'Contraceptives Surgery') {
                                                                //show method
                                                                echo "<th>Surgery Cases</th>";
                                                            } else {
                                                                //show method
                                                                echo "<th colspan=\"3\">$methodName</th>";
                                                            }
                                                        }
                                                        ?>
                                                        <th colspan="2">Ante-natal</th>
                                                        <th colspan="2">Post-natal</th>
                                                        <th colspan="2">MCH</th>
                                                        <th rowspan="2">No. of General Patients</th>
                                                    </tr>
                                                    <tr>
                                                        <?php
                                                        //check method
                                                        foreach ($method as $methodName) {
                                                            if ($methodName == 'Contraceptives Surgery') {
                                                                echo "<th>No. of CS Cases</th>";
                                                            } else {
                                                                echo "<th>New Case</th>";
                                                                echo "<th>Old Case</th>";
                                                                if ($methodName == 'Injectables') {
                                                                    echo "<th>No. of Injectables</th>";
                                                                }
                                                                if ($methodName == 'Oral Pills') {
                                                                    echo "<th>No. of Oral Pills</th>";
                                                                }if ($methodName == 'IUD') {
                                                                    echo "<th>No. of IUDs</th>";
                                                                }
                                                                if ($methodName == 'Condoms') {
                                                                    echo "<th>No. of Condoms Sold</th>";
                                                                }
                                                                if ($methodName == 'Implant') {
                                                                    echo "<th>Implant</th>";
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <th>New Case</th>
                                                        <th>Old Case</th>
                                                        <th>New Case</th>
                                                        <th>Old Case</th>
                                                        <th>New Case</th>
                                                        <th>Old Case</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $counter = 1;
                                                    foreach ($whName as $whId => $whName) {
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?php echo $counter++; ?></td>
                                                            <td><?php echo $whName; ?></td>
                                                            <?php
                                                            foreach ($method as $methodName) {
                                                                  if ($methodName == 'Contraceptives Surgery') {
                                                                    echo "<td class=\"right\">" . ( ($data[$whId]['surCases'] != 0) ? number_format($data[$whId]['surCases']) : 0 ) . "</td>";
                                                                } else {
                                                                    echo "<td class=\"right\">" . ( (!empty($data[$whId][$methodName]['new'])) ? number_format($data[$whId][$methodName]['new']) : 0 ) . "</td>";
                                                                    echo "<td class=\"right\">" . ( (!empty($data[$whId][$methodName]['old'])) ? number_format($data[$whId][$methodName]['old']) : 0 ) . "</td>";
                                                                    echo "<td class=\"right\">" . ( (!empty($data[$whId][$methodName]['sold'])) ? number_format($data[$whId][$methodName]['sold']) : 0 ) . "</td>";
                                                                }
                                                            }
                                                            ?>
                                                            <td class="right"><?php echo ($data[$whId]['pre_natal_new'] != 0) ? number_format($data[$whId]['pre_natal_new']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['pre_natal_old'] != 0) ? number_format($data[$whId]['pre_natal_old']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['post_natal_new'] != 0) ? number_format($data[$whId]['post_natal_new']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['post_natal_old'] != 0) ? number_format($data[$whId]['post_natal_old']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['ailment_children'] != 0) ? number_format($data[$whId]['ailment_children']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['ailment_adults'] != 0) ? number_format($data[$whId]['ailment_adults']) : 0; ?></td>
                                                            <td class="right"><?php echo ($data[$whId]['general_ailment'] != 0) ? number_format($data[$whId]['general_ailment']) : 0; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="right">Total</th>
                                                        <?php
                                                        foreach ($method as $methodName) {
                                                           if ($methodName != 'Contraceptives Surgery') {
                                                                echo "<th class=\"right\">" . ( (array_sum($total[$methodName]['new']) != 0) ? number_format(array_sum($total[$methodName]['new'])) : 0 ) . "</th>";
                                                                echo "<th class=\"right\">" . ( (array_sum($total[$methodName]['old']) != 0) ? number_format(array_sum($total[$methodName]['old'])) : 0 ) . "</th>";
                                                                echo "<th class=\"right\">" . ( (array_sum($total[$methodName]['sold']) != 0) ? number_format(array_sum($total[$methodName]['sold'])) : 0 ) . "</th>";
                                                            }
                                                        }
                                                        ?>
                                                        <th class="right"><?php echo (array_sum($total['surCases']) != 0) ? number_format(array_sum($total['surCases'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['pre_natal_new']) != 0) ? number_format(array_sum($total['pre_natal_new'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['pre_natal_old']) != 0) ? number_format(array_sum($total['pre_natal_old'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['post_natal_new']) != 0) ? number_format(array_sum($total['post_natal_new'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['post_natal_old']) != 0) ? number_format(array_sum($total['post_natal_old'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['ailment_children']) != 0) ? number_format(array_sum($total['ailment_children'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['ailment_adults']) != 0) ? number_format(array_sum($total['ailment_adults'])) : 0; ?></th>
                                                        <th class="right"><?php echo (array_sum($total['general_ailment']) != 0) ? number_format(array_sum($total['general_ailment'])) : 0; ?></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <?php
                                        }
                                        ?>
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
            unset($method, $whName, $data, $total);
            ?>
        </div>
    </div>
</div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
//include combos
include ('combos.php');
?>
</body>
</html>