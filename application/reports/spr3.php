<?php
/**
 * spr3
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
$rptId = 'spr3';
//distrcit id
$districtId = '';
//stakeholder 
$stakeholder = 1;
//selected year
$selYear = '';
////selected province
$selProv = '';
//check if submitted
if (isset($_POST['submit'])) {
    //selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //from date
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //to date
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //start date
    $startDate = $fromDate . '-01';
    //end date
    $endDate = date("Y-m-t", strtotime($toDate));
    //select query
    // Get Province name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $selProv";
    //fetch result
    $row = mysql_fetch_array(mysql_query($qry));
    //province name
    $provinceName = $row['LocName'];
    //file name
    $fileName = 'SPR3_' . $provinceName . '_from_' . date('M-Y', strtotime($startDate)) . '_to_' . date('M-Y', strtotime($endDate));
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
                        <h3 class="page-title row-br-b-wp">Provincial Summary of Contraceptive Performance</h3>
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
                    //select query
                    //Wise
                    //hf_type_id,
                    //hf_type,
                    //hf_type_rank,
                    //itm_id,
                    //itm_name,
                    //itm_category,
                    //itm_type,
                    //total_outlets,
                    //performance,
                    //CYP,
                    //Users
                    $qry = "SELECT
								B.hf_type_id,
								B.hf_type,
								B.hf_type_rank,
								B.itm_id,
								B.itm_name,
								B.itm_category,
								B.itm_type,
								B.total_outlets,
								A.performance,
								(A.CYPFactor * A.performance) AS CYP,
								(A.userFactor * A.performance) AS Users
							FROM
								(
									SELECT
										tbl_hf_data.item_id,
										tbl_warehouse.hf_type_id,
										SUM(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS performance,
										itminfo_tab.user_factor AS userFactor,
										itminfo_tab.extra AS CYPFactor,
										itminfo_tab.itm_category
									FROM
										tbl_warehouse
									INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
									INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
									INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
									WHERE
										tbl_warehouse.prov_id = $selProv
									AND tbl_warehouse.stkid = $stakeholder
									AND tbl_hf_data.reporting_date BETWEEN '$startDate' AND '$endDate'
									GROUP BY
										tbl_warehouse.hf_type_id,
										tbl_hf_data.item_id
									UNION
										SELECT
											tbl_hf_data.item_id,
											tbl_warehouse.hf_type_id,
											SUM(tbl_hf_data.issue_balance) AS performance,
											itminfo_tab.user_factor AS userFactor,
											itminfo_tab.extra AS CYPFactor,
											itminfo_tab.itm_category
										FROM
											tbl_warehouse
										INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
										INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
										WHERE
											tbl_warehouse.prov_id = $selProv
										AND tbl_warehouse.stkid = $stakeholder
										AND tbl_hf_data.reporting_date BETWEEN '$startDate' AND '$endDate'
										AND itminfo_tab.itm_category = 1
										GROUP BY
											tbl_warehouse.hf_type_id,
											tbl_hf_data.item_id
								) A
							RIGHT JOIN (
								SELECT
									COUNT(DISTINCT tbl_warehouse.wh_id) AS total_outlets,
									tbl_warehouse.hf_type_id,
									tbl_hf_type.hf_type,
									tbl_hf_type_rank.hf_type_rank,
									itminfo_tab.itm_id,
									itminfo_tab.itm_name,
									itminfo_tab.frmindex,
									itminfo_tab.itm_category,
									CONCAT(itminfo_tab.method_type, '(', itminfo_tab.itm_type, ')') AS itm_type,
									itminfo_tab.method_rank
								FROM
									tbl_warehouse
								INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
								INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
								INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
								INNER JOIN tbl_hf_type_rank ON tbl_hf_type.pk_id = tbl_hf_type_rank.hf_type_id,
								itminfo_tab
							INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
							WHERE
								tbl_warehouse.prov_id = $selProv
							AND tbl_warehouse.stkid = $stakeholder
							AND stakeholder.lvl = 7
							AND tbl_hf_type_rank.stakeholder_id = $stakeholder
							AND tbl_hf_type_rank.province_id = $selProv
							AND stakeholder_item.stkid = $stakeholder
							AND tbl_warehouse.reporting_start_month <= '$endDate'
							GROUP BY
								tbl_warehouse.hf_type_id,
								itminfo_tab.itm_id
							) B ON A.hf_type_id = B.hf_type_id
							AND A.item_id = B.itm_id
							ORDER BY
								B.hf_type_rank ASC,
								B.method_rank ASC,
								B.frmindex ASC";
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
                            //items
                            $items = $hfType = array();
                            //fetch result
                            while ($row = mysql_fetch_array($qryRes)) {
                                //check item name
                                if (!in_array($row['itm_name'], $items)) {
                                    //items
                                    $items[] = $row['itm_name'];
                                    //product
                                    $product[$row['itm_type']][] = $row['itm_name'];
                                }
                                //check hf type
                                if (!in_array($row['hf_type'], $hfType)) {
                                    //hf type
                                    $hfType[$row['hf_type_id']] = $row['hf_type'];
                                    //check selected province
                                    if ($selProv == 3) {
                                        //check hf type id
                                        if (!in_array($row['hf_type_id'], $hfPrograms)) {
                                            // Get Facilities Count
                                            $qry = "SELECT REPgetNonProgramFacilities('HFT', $stakeholder, " . $row['hf_type_id'] . ", " . $selProv . ", '$endDate') AS total_outlets FROM DUAL ";
                                            //hf row count
                                            $hfCountRow = mysql_fetch_array(mysql_query($qry));
                                            //total outlets
                                            $totalOutlets[$row['hf_type_id']] = (!empty($hfCountRow['total_outlets'])) ? $hfCountRow['total_outlets'] : '';
                                        } else {
                                            //total outlets
                                            $totalOutlets[$row['hf_type_id']] = $row['total_outlets'];
                                        }
                                    } else {
                                        //total oulet
                                        $totalOutlets[$row['hf_type_id']] = $row['total_outlets'];
                                    }
                                }

                                //cyp
                                $data[$row['hf_type_id']]['CYP'][] = $row['CYP'];
                                //users
                                $data[$row['hf_type_id']]['Users'][] = $row['Users'];
                                //item name
                                $data[$row['hf_type_id']][$row['itm_name']] = $row['performance'];
                                //cyp
                                $total['CYP'][] = $row['CYP'];
                                //item name
                                $totalCYP[$row['itm_name']][] = $row['CYP'];
                                //users
                                $total['Users'][] = $row['Users'];
                                //item name
                                $totalUsers[$row['itm_name']][] = $row['Users'];
                                //performance
                                $total[$row['itm_name']][] = $row['performance'];
                            }
                            ?>
                            <table width="100%">
                                <tr>
                                    <td style="padding-top: 10px;" align="center">
                                        <h4 class="center bold">
                                            Outlet wise Performance Report <?php echo 'From ' . date('M-Y', strtotime($startDate)) . ' to ' . date('M-Y', strtotime($endDate)); ?><br>
                                            Inrespect of Population Welfare Department <?php echo $provinceName ?>
                                        </h4>
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">S.No.</th>
                                                    <th rowspan="2" width="13%">Name of Service Outlet</th>
                                                    <th rowspan="2" width="7%">No. of Outlets</th>
                                                    <?php
                                                    //get results
                                                    foreach ($product as $proType => $proNames) {
                                                        //check product type
                                                        if ($proType == 'Condoms(PCs)') {
                                                            echo "<th colspan=" . sizeof($proNames) . ">$proType</th>";
                                                        } else {
                                                            echo "<th colspan=" . (sizeof($proNames) + 1) . ">$proType</th>";
                                                        }
                                                    }
                                                    ?>
                                                    <th rowspan="2">CYP</th>
                                                    <th rowspan="2">Users</th>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count
                                                    $count = 1;
                                                    //get results
                                                    foreach ($product as $proType => $proNames) {
                                                        foreach ($proNames as $name) {
                                                            //show item
                                                            echo "<th width='" . (70 / count($items)) . "%'>$name</th>";
                                                        }
                                                        //check count
                                                        if ($proType != $var && $count > 1) {
                                                            //show total
                                                            echo "<th width='100'>Total</th>";
                                                        }
                                                        //var
                                                        $var = $proType;
                                                        //count
                                                        $count++;
                                                    }
                                                    ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                //counter
                                                $counter = 1;
                                                foreach ($hfType as $id => $hfName) {
                                                    ?>
                                                    <tr>
                                                        <td class="center"><?php echo $counter++; ?></td>
                                                        <td><?php echo $hfName; ?></td>
                                                        <td class="center"><?php echo $totalOutlets[$id]; ?></td>
                                                        <?php
                                                        //var
                                                        $var = '';
                                                        //count
                                                        $count = 1;
                                                        foreach ($product as $proType => $proNames) {
                                                            //method type total
                                                            $methodTypeTotal = 0;
                                                            foreach ($proNames as $methodName) {
                                                                $methodTypeTotal = $methodTypeTotal + $data[$id][$methodName];
                                                                //show method name
                                                                echo "<td class=\"right\">" . number_format($data[$id][$methodName]) . "</td>";
                                                            }
                                                            //check count
                                                            if ($proType != $var && $count > 1) {
                                                                //show method type total
                                                                echo "<td class=\"right\">" . number_format($methodTypeTotal) . "</td>";
                                                            }
                                                            $var = $proType;
                                                            $count++;
                                                        }
                                                        //shwo CYP
                                                        echo "<th class=\"right\">" . number_format(array_sum($data[$id]['CYP'])) . "</th>";
                                                        //show users
                                                        echo "<th class=\"right\">" . number_format(array_sum($data[$id]['Users'])) . "</th>";
                                                        ?>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="right" colspan="2">Total</th>
                                                    <th class="center"><?php echo number_format(array_sum($totalOutlets)); ?></th>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count
                                                    $count = 1;
                                                    //get result
                                                    foreach ($product as $proType => $proNames) {
                                                        //method type total
                                                        $methodTypeTotal = 0;
                                                        foreach ($proNames as $methodName) {
                                                            $methodTypeTotal = $methodTypeTotal + array_sum($total[$methodName]);
                                                            //show method name
                                                            echo "<th class=\"right\">" . number_format(array_sum($total[$methodName])) . "</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            //show method type total
                                                            echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                        }
                                                        //var
                                                        $var = $proType;
                                                        //count
                                                        $count++;
                                                    }
                                                    //show CYP
                                                    echo "<th class=\"right\">" . number_format(array_sum($total['CYP'])) . "</th>";
                                                    //show users
                                                    echo "<th class=\"right\">" . number_format(array_sum($total['Users'])) . "</th>";
                                                    ?>
                                                </tr>
                                                <tr>
                                                    <th class="right" colspan="3">CYP</th>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count
                                                    $count = 1;
                                                    foreach ($product as $proType => $proNames) {
                                                        //set method Type Total 
                                                        $methodTypeTotal = 0;
                                                        foreach ($proNames as $methodName) {
                                                            //set method Type Total
                                                            $methodTypeTotal = $methodTypeTotal + array_sum($totalCYP[$methodName]);
                                                            //show method name
                                                            echo "<th class=\"right\">" . number_format(array_sum($totalCYP[$methodName])) . "</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            //show method type total
                                                            echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                        }
                                                        //var
                                                        $var = $proType;
                                                        //count
                                                        $count++;
                                                    }
                                                    ?>
                                                </tr>
                                                <tr>
                                                    <th class="right" colspan="3">Users</th>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count
                                                    $count = 1;
                                                    foreach ($product as $proType => $proNames) {
                                                        $methodTypeTotal = 0;
                                                        foreach ($proNames as $methodName) {
                                                            $methodTypeTotal = $methodTypeTotal + array_sum($totalUsers[$methodName]);
                                                            echo "<th class=\"right\">" . number_format(array_sum($totalUsers[$methodName])) . "</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                        }
                                                        //var
                                                        $var = $proType;
                                                        //count
                                                        $count++;
                                                    }
                                                    ?>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <h5 style="margin-top:20px;"  class="center bold">
                                            District wise Performance Report <?php echo 'From ' . date('M-Y', strtotime($startDate)) . ' to ' . date('M-Y', strtotime($endDate)); ?><br>
                                            Inrespect of Population Welfare Department <?php echo $provinceName ?>
                                        </h5>
                                        <?php
                                        // Unset varibles
                                        unset($data, $total, $issue, $totalUsers, $totalCYP, $items, $hfType, $totalOutlets, $product);
                                        //select query
                                        //gets
                                        // District Wise
                                        //district id
                                        //location name
                                        //item id
                                        //item id
                                        //item category
                                        //item type
                                        //total outlets
                                        //performance
                                        //CYP
                                        //users
                                        $qry = "SELECT
														B.dist_id,
														B.LocName,
														B.itm_id,
														B.itm_name,
														B.itm_category,
														B.itm_type,
														B.total_outlets,
														A.performance,
														(A.CYPFactor * A.performance) AS CYP,
														(A.userFactor * A.performance) AS Users
													FROM
														(
															SELECT
																tbl_hf_data.item_id,
																tbl_warehouse.dist_id,
																SUM(tbl_hf_data.issue_balance) AS performance,
																itminfo_tab.user_factor AS userFactor,
																itminfo_tab.extra AS CYPFactor,
																itminfo_tab.itm_category
															FROM
																tbl_warehouse
															INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
															INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
															WHERE
																tbl_warehouse.prov_id = $selProv
															AND tbl_warehouse.stkid = $stakeholder
															AND tbl_hf_data.reporting_date BETWEEN '$startDate' AND '$endDate'
															AND itminfo_tab.itm_category = 1
															GROUP BY
																tbl_warehouse.dist_id,
																tbl_hf_data.item_id
															UNION
																SELECT
																	tbl_hf_data.item_id,
																	tbl_warehouse.dist_id,
																	SUM(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS performance,
																	itminfo_tab.user_factor AS userFactor,
																	itminfo_tab.extra AS CYPFactor,
																	itminfo_tab.itm_category
																FROM
																	tbl_warehouse
																INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
																INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
																INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
																WHERE
																	tbl_warehouse.prov_id = $selProv
																AND tbl_warehouse.stkid = $stakeholder
																AND tbl_hf_data.reporting_date BETWEEN '$startDate' AND '$endDate'
																AND itminfo_tab.itm_category = 2
																GROUP BY
																	tbl_warehouse.dist_id,
																	tbl_hf_data.item_id
														) A
													RIGHT JOIN (
														SELECT
															COUNT(DISTINCT tbl_warehouse.wh_id) AS total_outlets,
															tbl_warehouse.dist_id,
															tbl_locations.LocName,
															itminfo_tab.itm_id,
															itminfo_tab.itm_name,
															itminfo_tab.frmindex,
															itminfo_tab.itm_category,
															CONCAT(itminfo_tab.method_type, '(', itminfo_tab.itm_type, ')') AS itm_type,
															itminfo_tab.method_rank
														FROM
															tbl_warehouse
														INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
														INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
														INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
														INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid,
														itminfo_tab
													INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
													WHERE
														tbl_warehouse.prov_id = $selProv
													AND tbl_warehouse.stkid = $stakeholder
													AND stakeholder.lvl = 7
													AND stakeholder_item.stkid = $stakeholder
													AND tbl_warehouse.reporting_start_month <= '$endDate'
													GROUP BY
														tbl_warehouse.dist_id,
														itminfo_tab.itm_id
													) B ON A.dist_id = B.dist_id
													AND A.item_id = B.itm_id
													ORDER BY
														B.LocName ASC,
														B.method_rank ASC,
														B.frmindex ASC";
                                        //query results
                                        $qryRes = mysql_query($qry);
                                        //items
                                        $items = $distName = array();
                                        //fetch result
                                        while ($row = mysql_fetch_array($qryRes)) {
                                            if (!in_array($row['itm_name'], $items)) {
                                                //items
                                                $items[] = $row['itm_name'];
                                                //product
                                                $product[$row['itm_type']][] = $row['itm_name'];
                                            }
                                            if (!in_array($row['LocName'], $distName)) {
                                                //district name
                                                $distName[$row['dist_id']] = $row['LocName'];
                                                if ($selProv == 3) {
                                                    // Get Facilities Count
                                                    $qry = "SELECT REPgetNonProgramFacilities('D', $stakeholder, " . $row['dist_id'] . ", " . $selProv . ", '$endDate') AS total_outlets FROM DUAL ";
                                                    //hf count row
                                                    $hfCountRow = mysql_fetch_array(mysql_query($qry));

                                                    $totalOutlets[$row['dist_id']] = (!empty($hfCountRow['total_outlets'])) ? $hfCountRow['total_outlets'] : '';
                                                } else {
                                                    $totalOutlets[$row['dist_id']] = $row['total_outlets'];
                                                }
                                            }

                                            //cyp
                                            $data[$row['dist_id']]['CYP'][] = $row['CYP'];
                                            //users
                                            $data[$row['dist_id']]['Users'][] = $row['Users'];
                                            //performance
                                            $data[$row['dist_id']][$row['itm_name']] = $row['performance'];
                                            //cyp
                                            $total['CYP'][] = $row['CYP'];
                                            //item name
                                            $totalCYP[$row['itm_name']][] = $row['CYP'];
                                            //users
                                            $total['Users'][] = $row['Users'];
                                            //item name
                                            $totalUsers[$row['itm_name']][] = $row['Users'];
                                            //performance
                                            $total[$row['itm_name']][] = $row['performance'];
                                        }
                                        ?>
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">S.No.</th>
                                                    <th rowspan="2" width="13%">District</th>
                                                    <th rowspan="2" width="7%">No. of Outlets</th>
                                                    <?php
                                                    foreach ($product as $proType => $proNames) {
                                                        if ($proType == 'Condoms(PCs)') {
                                                            echo "<th colspan=" . sizeof($proNames) . ">$proType</th>";
                                                        } else {
                                                            echo "<th colspan=" . (sizeof($proNames) + 1) . ">$proType</th>";
                                                        }
                                                    }
                                                    ?>
                                                    <th rowspan="2">CYP</th>
                                                    <th rowspan="2">Users</th>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count
                                                    $count = 1;
                                                    foreach ($product as $proType => $proNames) {
                                                        foreach ($proNames as $name) {
                                                            echo "<th width='" . (70 / count($items)) . "%'>$name</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            echo "<th width='100'>Total</th>";
                                                        }
                                                        //var
                                                        $var = $proType;
                                                        //count
                                                        $count++;
                                                    }
                                                    ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                //set counter
                                                $counter = 1;
                                                foreach ($distName as $id => $name) {
                                                    ?>
                                                    <tr>
                                                        <td class="center"><?php echo $counter++; ?></td>
                                                        <td><?php echo $name; ?></td>
                                                        <td class="center"><?php echo $totalOutlets[$id]; ?></td>
                                                        <?php
                                                        //var
                                                        $var = '';
                                                        //count
                                                        $count = 1;
                                                        foreach ($product as $proType => $proNames) {
                                                            $methodTypeTotal = 0;
                                                            foreach ($proNames as $methodName) {
                                                                $methodTypeTotal = $methodTypeTotal + $data[$id][$methodName];
                                                                echo "<td class=\"right\">" . number_format($data[$id][$methodName]) . "</td>";
                                                            }
                                                            if ($proType != $var && $count > 1) {
                                                                echo "<td class=\"right\">" . number_format($methodTypeTotal) . "</td>";
                                                            }
                                                            //var
                                                            $var = $proType;
                                                            //count
                                                            $count++;
                                                        }
                                                        //show CYP
                                                        echo "<th class=\"right\">" . number_format(array_sum($data[$id]['CYP'])) . "</th>";
                                                        //show users
                                                        echo "<th class=\"right\">" . number_format(array_sum($data[$id]['Users'])) . "</th>";
                                                        ?>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="right" colspan="2">Total</th>
                                                    <th class="center"><?php echo number_format(array_sum($totalOutlets)); ?></th>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count
                                                    $count = 1;
                                                    foreach ($product as $proType => $proNames) {
                                                        $methodTypeTotal = 0;
                                                        foreach ($proNames as $methodName) {
                                                            $methodTypeTotal = $methodTypeTotal + array_sum($total[$methodName]);
                                                            echo "<th class=\"right\">" . number_format(array_sum($total[$methodName])) . "</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                        }
                                                        //var
                                                        $var = $proType;
                                                        //cyp
                                                        $count++;
                                                    }
                                                    //show CYP
                                                    echo "<th class=\"right\">" . number_format(array_sum($total['CYP'])) . "</th>";
                                                    //show users
                                                    echo "<th class=\"right\">" . number_format(array_sum($total['Users'])) . "</th>";
                                                    ?>
                                                </tr>
                                                <tr>
                                                    <th class="right" colspan="3">CYP</th>
                                                    <?php
                                                    //var
                                                    $var = '';
                                                    //count
                                                    $count = 1;
                                                    foreach ($product as $proType => $proNames) {
                                                        $methodTypeTotal = 0;
                                                        foreach ($proNames as $methodName) {
                                                            $methodTypeTotal = $methodTypeTotal + array_sum($totalCYP[$methodName]);
                                                            echo "<th class=\"right\">" . number_format(array_sum($totalCYP[$methodName])) . "</th>";
                                                        }
                                                        if ($proType != $var && $count > 1) {
                                                            echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                        }
                                                        $var = $proType;
                                                        $count++;
                                                    }
                                                    ?>
                                                </tr>
                                                <tr>
                                                    <th class="right" colspan="3">Users</th>
                                                        <?php
                                                        $var = '';
                                                        $count = 1;
                                                        foreach ($product as $proType => $proNames) {
                                                            $methodTypeTotal = 0;
                                                            foreach ($proNames as $methodName) {
                                                                $methodTypeTotal = $methodTypeTotal + array_sum($totalUsers[$methodName]);
                                                                echo "<th class=\"right\">" . number_format(array_sum($totalUsers[$methodName])) . "</th>";
                                                            }
                                                            if ($proType != $var && $count > 1) {
                                                                echo "<th class=\"right\">" . number_format($methodTypeTotal) . "</th>";
                                                            }
                                                            $var = $proType;
                                                            $count++;
                                                        }
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
            unset($data, $issue, $totalUsers, $totalCYP, $items, $distName, $totalOutlets, $product);
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