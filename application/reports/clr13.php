<?php
/**
 * clr13
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
include(PUBLIC_PATH."html/header.php");
//echo '<pre>';print_r($_SESSION);exit;

//report id
$rptId = 'clr13';

//default stakeholder
if(empty($stakeholder))
$stakeholder = (!empty($_SESSION['user_stakeholder1'])?$_SESSION['user_stakeholder1']:'1');

//if submitted
if (isset($_POST['submit'])) {
    //get selected stakeholder
    $stakeholder = mysql_real_escape_string($_POST['stakeholder']);
    //get 
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //get to date 
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //get selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //get district
    $districtId = mysql_real_escape_string($_POST['district']);

    // Get Province name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $selProv";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    $provinceName = $row['LocName'];

     // Get district name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $districtId";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    //set district name
    $distrctName = $row['LocName'];
    //file name
    $fileName = 'CLR13_' . $distrctName . '_' . $provinceName . '_for_' . $fromDate . '-' . $toDate;
}
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
		<?php 
                //include top
                include PUBLIC_PATH."html/top.php";
        //include top_im
                include PUBLIC_PATH."html/top_im.php";?>

        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">District Wise Provincial Contraceptive Stock and Sale Report</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php 
                                //include sub_dist_form
                                include('sub_dist_form.php'); ?>
                            </div>
                        </div>

                    </div>
                </div>
                <?php
                //if submitted
                if (isset($_POST['submit'])) {
                    //select query
                    //get
                    //warehouse id
                    //warehouse name
                    //iten id
                    //item name
                    //Opening balance
                    //receive
                    //issue
                    //closing balance
                     $qry = "SELECT
                                B.wh_id,
                                B.wh_name,
                                B.itm_id,
                                B.itm_name,
                                A.opening_balance AS OB,
                                A.received_balance AS Rcv,
                                A.issue_balance AS Issue,
                                A.closing_balance AS CB
                            FROM
                                    (
                                        SELECT
                                            tbl_warehouse.wh_id,
                                            tbl_warehouse.wh_name,
                                            SUM(IF(DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') = '$fromDate', tbl_hf_data.opening_balance, 0)) AS opening_balance,
                                            SUM(tbl_hf_data.received_balance) AS received_balance,
                                            SUM(tbl_hf_data.issue_balance) AS issue_balance,
                                            SUM(IF(DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') = '$toDate', tbl_hf_data.closing_balance, 0)) AS closing_balance,
                                            itminfo_tab.itm_id,
                                            itminfo_tab.itm_name,
                                            tbl_hf_type_rank.hf_type_id
                                        FROM
                                                tbl_warehouse
                                        INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                        INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                                        AND tbl_warehouse.prov_id = tbl_hf_type_rank.province_id
                                        WHERE
                                                tbl_warehouse.dist_id = $districtId
                                        AND tbl_warehouse.stkid = $stakeholder
                                        AND DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') BETWEEN '$fromDate' AND '$toDate'
                                        AND itminfo_tab.itm_category = 1
                                    GROUP BY
                                            tbl_hf_data.item_id,
                                            tbl_warehouse.wh_id
                                    ) A
                            RIGHT JOIN (
                                    SELECT
                                            tbl_warehouse.wh_id,
                                            tbl_warehouse.wh_name,
                                            tbl_warehouse.wh_rank,
                                            itminfo_tab.itm_id,
                                            itminfo_tab.itm_name,
                                            itminfo_tab.frmindex,
                                            tbl_hf_type_rank.hf_type_id,
                                            tbl_hf_type_rank.hf_type_rank
                                    FROM
                                            tbl_warehouse
                                    INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                    INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id,
                                    itminfo_tab
                                    INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                            WHERE
                                    tbl_warehouse.dist_id = $districtId
                            AND tbl_warehouse.stkid = $stakeholder
                            AND stakeholder_item.stkid = $stakeholder
                            AND DATE_FORMAT(tbl_warehouse.reporting_start_month, '%Y-%m') <= '$toDate'
                            AND itminfo_tab.itm_category = 1
                            GROUP BY
                                    itminfo_tab.itm_id,
                                    tbl_warehouse.wh_id
                            ) B ON A.wh_id = B.wh_id
                            AND A.itm_id = B.itm_id
                            ORDER BY
                                     IF (B.wh_rank = '' OR B.wh_rank IS NULL, 1, 0),
                                     B.wh_rank ASC,
                                     B.hf_type_rank ASC,
                                     B.wh_name ASC,
                                     B.frmindex ASC";
                    //query result
                    $qryRes = mysql_query($qry);
                    //check if result exists
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        ?>
                            <?php 
                            //include sub_dist_reports
                            include('sub_dist_reports.php'); ?>
                            <div class="col-md-12" style="overflow:auto;">
                                <?php
                                //district id
                                $distId = '';
				//warehouse name		
                                $whName = array();
								$gtotal_facilities = '';
                                                                //fetch result
                                while ($row = mysql_fetch_array($qryRes)) {
                                        if (!in_array($row['wh_name'], $whName)){
                                                $whName[$row['wh_id']] = $row['wh_name'];
                                        }
                                        //item name
                                        $items[$row['itm_id']] = $row['itm_name'];
                                    //OB                        
                                    $data[$row['wh_id']]['OB'][$row['itm_id']] = $row['OB'];
                                    //receive
                                    $data[$row['wh_id']]['Rcv'][$row['itm_id']] = $row['Rcv'];
                                    //Issue
                                    $data[$row['wh_id']]['Issue'][$row['itm_id']] = $row['Issue'];
                                    //CB
                                    $data[$row['wh_id']]['CB'][$row['itm_id']] = $row['CB'];
                                }
                                ?>
                                <table width="100%">
                                    <tr>
                                        <td align="center">
                                            <h4 class="center">
                                                Health Facility Wise Contraceptive Stock Report for <?php echo $distrctName; ?>  - <?php echo $stk_name; ?><br>
                                                <?php
                                                        if( $fromDate != $toDate )
                                                        {
                                                                $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
                                                        }
                                                        else
                                                        {
                                                                $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
                                                        }
                                                        ?>
                                                        <?php echo $reportingPeriod . ', District ' . $distrctName; ?>
                                            </h4>
                                        </td>
                                        <td>
                                            <h4 class="right">CLR-11</h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding-top: 10px;">
                                            <table width="100%" id="myTable" cellspacing="0" align="center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2" width="4%">S.No.</th>
                                                        <th rowspan="2" width="10%">Health Facility</th>
                                                        <?php
                                                        foreach ($items as $id => $name) {
                                                            echo "<th colspan=\"4\">$name</th>";
                                                        }
                                                        ?>
                                                    </tr>
                                                    <tr>
                                                        <?php
                                                        foreach ($items as $name) {
                                                            echo "<th width='" . (85 / (count($items) * 4)) . "%'>Opening Balance</th>";
                                                            echo "<th width='" . (85 / (count($items) * 4)) . "%'>Receive</th>";
                                                            echo "<th width='" . (85 / (count($items) * 4)) . "%'>Issue</th>";
                                                            echo "<th width='" . (85 / (count($items) * 4)) . "%'>Closing Balance</th>";
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $counter = 1;
                                                    $opening_gtotal_dist = $receive_gtotal_dist = $issue_gtotal_dist = $closing_gtotal_dist = 0;
                                                    foreach ($whName as $id => $name) {
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?php echo $counter++; ?></td>
                                                            <td><?php echo $name; ?></td>
                                                            <?php
                                                            $district_total_html = $field_total_html = $facilities_html = "";
                                                            foreach ($items as $itmId => $hfName) {
                                                                //opening balance
                                                                $opening[$itmId][] = $data[$id]['OB'][$itmId];
                                                                //receive
                                                                $receive[$itmId][] = $data[$id]['Rcv'][$itmId];
                                                                //issue
                                                                $issue[$itmId][] = $data[$id]['Issue'][$itmId];
                                                                //closing balance
                                                                $closing[$itmId][] = $data[$id]['CB'][$itmId];
                                                                //OB
                                                                $facilities_html .= "<td class=\"right\">" . number_format($data[$id]['OB'][$itmId]) . "</td>";
                                                                //receive
                                                                $facilities_html .= "<td class=\"right\">" . number_format($data[$id]['Rcv'][$itmId]) . "</td>";
                                                                //issue
                                                                $facilities_html .= "<td class=\"right\">" . number_format($data[$id]['Issue'][$itmId]) . "</td>";
                                                                //CB
                                                                $facilities_html .= "<td class=\"right\">" . number_format($data[$id]['CB'][$itmId]) . "</td>";
                                                            }
                                                            echo $facilities_html;
                                                            ?>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="right">Total</th>
                                                            <?php
                                                            foreach ($items as $itmId => $hfName) {
                                                                //opening balance
                                                                $gtotal_facilities .= "<th class=\"right\">" . number_format(array_sum($opening[$itmId])) . "</th>";
                                                                //receive
                                                                $gtotal_facilities .= "<th class=\"right\">" . number_format(array_sum($receive[$itmId])) . "</th>";
                                                                //issue
                                                                $gtotal_facilities .= "<th class=\"right\">" . number_format(array_sum($issue[$itmId])) . "</th>";
                                                                //closing balance
                                                                $gtotal_facilities .= "<th class=\"right\">" . number_format(array_sum($closing[$itmId])) . "</th>";
                                                            }
                                                            echo $gtotal_facilities;
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
                unset($data, $items, $whName);
                ?>
            </div>
        </div>
    </div>
	<?php 
        //include footer
        include PUBLIC_PATH."/html/footer.php";
    //include combos
    include ('combos.php'); ?>
</body>
</html>