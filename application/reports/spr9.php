<?php
/**
 * spr9
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
$rptId = 'spr9';
//check if submitted
if (isset($_POST['submit'])) {
    //get from date
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //get to date
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //get selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //get district id
    $districtId = mysql_real_escape_string($_POST['district']);

    // Get district name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $districtId";
    //fetch result
    $row = mysql_fetch_array(mysql_query($qry));
    //disttrict name
    $distrctName = $row['LocName'];
    //file name
    $fileName = 'SPR9_' . $distrctName . '_for_' . $fromDate . '-' . $toDate;
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
                        <h3 class="page-title row-br-b-wp">District Monthly Contraceptive Performance Report</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php
                                //sub dist form
                                include('sub_dist_form.php');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                //chech if form submitted
                if (isset($_POST['submit'])) {
                    //select query
                    //gets
                    //hf type id
                    //hf type
                    //hf type
                    //item id
                    //item name
                    //item category
                    //item type
                    //total outlets
                    //performance
                    //cs performed
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
                                A.CS_Performed
                        FROM
                                (
                                        SELECT
                                                tbl_hf_data.item_id,
                                                tbl_warehouse.hf_type_id,
                                                SUM(IF (tbl_warehouse.hf_type_id IN (4, 5) AND itminfo_tab.itm_category = 2, (IF (tbl_hf_data_reffered_by.hf_type_id IN(4, 5), tbl_hf_data_reffered_by.ref_surgeries, 0)), tbl_hf_data.issue_balance)) AS performance,
                                                SUM(tbl_hf_data_reffered_by.static) + SUM(tbl_hf_data_reffered_by.camp) AS CS_Performed
                                        FROM
                                                tbl_warehouse
                                        INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                        LEFT JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                                        WHERE
                                                tbl_warehouse.dist_id = $districtId
                                        AND tbl_warehouse.stkid = $stakeholder
                                        AND DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') BETWEEN '$fromDate' AND '$toDate'
                                            AND itminfo_tab.itm_category IN (1,2)
                                        GROUP BY
                                                tbl_warehouse.hf_type_id,
                                                tbl_hf_data.item_id
                                ) A
                        RIGHT JOIN (
                                SELECT
                                        COUNT(distinct tbl_warehouse.wh_id) AS total_outlets,
                                        tbl_warehouse.hf_type_id,
                                        tbl_hf_type.hf_type,
                                        tbl_hf_type_rank.hf_type_rank,
                                        itminfo_tab.itm_id,
                                        itminfo_tab.itm_name,
                                        itminfo_tab.frmindex,
                                        itminfo_tab.itm_category,
                                        itminfo_tab.itm_type
                                FROM
                                        tbl_warehouse
                                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                                INNER JOIN tbl_hf_type_rank ON tbl_hf_type.pk_id = tbl_hf_type_rank.hf_type_id,
                                itminfo_tab
                        INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                        WHERE
                                tbl_warehouse.dist_id = $districtId
                        AND tbl_warehouse.stkid = $stakeholder
                        AND stakeholder.lvl = 7
                        AND tbl_hf_type_rank.stakeholder_id = $stakeholder
                        AND tbl_hf_type_rank.province_id = $selProv
                        AND stakeholder_item.stkid = $stakeholder
                        AND DATE_FORMAT(tbl_warehouse.reporting_start_month, '%Y-%m') <= '$toDate'
                        AND tbl_warehouse.is_active = 1
                        AND itminfo_tab.itm_category IN (1,2)
                        GROUP BY
                                tbl_warehouse.hf_type_id,
                                itminfo_tab.itm_id
                        ) B ON A.hf_type_id = B.hf_type_id
                        AND A.item_id = B.itm_id 
                        ORDER BY
                                B.hf_type_rank ASC,
                                B.frmindex";
                     //echo $qry;exit;
                    //query result
                    $qryRes = mysql_query($qry);
                    //check if result exists
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        ?>
                        <?php
                        //include sub dist reports
                        include('sub_dist_reports.php');
                        ?>
                        <div class="col-md-12" style="overflow:auto;">
                            <?php
                            $hfType = $items = array();
                            //fetch results
                            while ($row = mysql_fetch_array($qryRes)) {
                                //check item name
                                if (!in_array($row['itm_name'], $items) && $row['itm_category'] != 2) {
                                    //item name
                                    $items[] = $row['itm_name'];
                                    //item type
                                    $unit[] = $row['itm_type'];
                                }
                                if (!in_array($row['hf_type'], $hfType)) {
                                    $hfType[$row['hf_type_id']] = $row['hf_type'];
                                    //check selProv
                                    if ($selProv == 3) {
                                        
                                        if (!in_array($row['hf_type_id'], $hfPrograms)) {
                                            // Get Facilities Count
                                            $qry = "SELECT REPgetNonProgramFacilities('HFTD', $stakeholder, " . $row['hf_type_id'] . ", " . $districtId . ", '$toDate') AS total_outlets FROM DUAL ";
                                            //hf row count
                                            $hfCountRow = mysql_fetch_array(mysql_query($qry));
                                            //total outlets
                                            $totalOutlets[$row['hf_type_id']] = (!empty($hfCountRow['total_outlets'])) ? $hfCountRow['total_outlets'] : '';
                                        } else {
                                            $totalOutlets[$row['hf_type_id']] = $row['total_outlets'];
                                        }
                                    } else {
                                        $totalOutlets[$row['hf_type_id']] = $row['total_outlets'];
                                    }
                                }
                                //check item category
                                //item category == 2
                                if ($row['itm_category'] == 2) {
                                    //	
                                    $data[$row['hf_type_id']]['cs_reffer'][] = $row['performance'];
                                    $data[$row['hf_type_id']]['cs_done'][] = $row['CS_Performed'];
                                    $total['cs_done'][] = $row['CS_Performed'];
                                    $total['cs_reffer'][] = $row['performance'];
                                } else {
                                    $data[$row['hf_type_id']][$row['itm_name']] = $row['performance'];
                                    $total[$row['itm_name']][] = $row['performance'];
                                }
                            }
                            ?>
                            <table width="100%">
                                <tr>
                                    <td align="center">
                                        <h4 class="center">
                                            District Contraceptive Performance <br>
                                            <?php
                                            //if from date != to date						
                                            if ($fromDate != $toDate) {
                                                //reporting period							
                                                $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
                                            } else {
                                                //reporting period	
                                                $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
                                            }
                                            ?>
                                            <?php echo $reportingPeriod . ', District ' . $distrctName; ?>
                                        </h4>
                                    </td>
                                    <td>
                                        <h4 class="right">SPR-9</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 10px;">
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">S.No</th>
                                                    <th rowspan="2" width="10%">Service Outlets</th>
                                                    <th rowspan="2" width="5%">No. of Outlets</th>
                                                    <?php
                                                    //fetch results
                                                    foreach ($items as $methodName) {
                                                        echo "<th width=" . (70 / sizeof($items)) . "%>$methodName</th>";
                                                    }
                                                    ?>
                                                    <th colspan="2">Contraceptive Surgery</th>
                                                    <th rowspan="2" width="10%">Remarks</th>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    foreach ($unit as $val) {
                                                        echo "<th>($val)</th>";
                                                    }
                                                    ?>
                                                    <th>Reffered</th>
                                                    <th>Performed</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $counter = 1;
                                                foreach ($hfType as $id => $hfName) {
                                                    ?>
                                                    <tr>
                                                        <td class="center"><?php echo $counter++; ?></td>
                                                        <td><?php echo $hfName; ?></td>
                                                        <td class="center"><?php echo $totalOutlets[$id]; ?></td>
                                                        <?php
                                                        //fetch results
                                                        foreach ($items as $methodName) {
                                                            echo "<td class=\"right\">" . number_format($data[$id][$methodName]) . "</td>";
                                                        }
                                                        echo "<td class=\"right\">" . number_format(array_sum($data[$id]['cs_reffer'])) . "</td>";
                                                        echo "<td class=\"right\">" . number_format(array_sum($data[$id]['cs_done'])) . "</td>";
                                                        ?>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="right" colspan="2">Total</th>
                                                    <th class="center"><?php echo array_sum($totalOutlets); ?></th>
                                                        <?php
                                                        //fetch results
                                                        foreach ($items as $methodName) {
                                                            echo "<th class=\"right\">" . number_format(array_sum($total[$methodName])) . "</th>";
                                                        }
                                                        echo "<th class=\"right\">" . number_format(array_sum($total['cs_reffer'])) . "</th>";
                                                        echo "<th class=\"right\">" . number_format(array_sum($total['cs_done'])) . "</th>";
                                                        ?>
                                                    <th>&nbsp;</th>
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
            unset($data, $items, $unit, $hfType, $totalOutlets);
            ?>
        </div>
    </div>
</div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
?>
<?php
//include combos
include ('combos.php');
?>
</body>
</html>