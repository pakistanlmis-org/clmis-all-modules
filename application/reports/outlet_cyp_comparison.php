<?php

/**
 * outlet_cyp_comparison
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
//include header.php
include(PUBLIC_PATH."html/header.php");
//report id
$rptId = 'outletCYP';

//default stakeholder
if(empty($stakeholder))
$stakeholder = (!empty($_SESSION['user_stakeholder1'])?$_SESSION['user_stakeholder1']:'1');

//if submitted
if (isset($_POST['submit'])) {
    //get selected stakeholder
    $stakeholder = mysql_real_escape_string($_POST['stakeholder']);
    //selected month
    $selMonth = mysql_real_escape_string($_POST['month_sel']);
    //selected year
    $selYear = mysql_real_escape_string($_POST['year_sel']);
    //selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //end date
    $endDate = $selYear . '-' . $selMonth;
    //start date
    $startDate = date('Y-m', strtotime("-1 month", strtotime($endDate)));
    //select query
    // Get Province name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $selProv";
    //result
    $row = mysql_fetch_array(mysql_query($qry));
    //province Name 
    $provinceName = $row['LocName'];
//file Name 
    $fileName = 'outlet_CYP_comparison_' . $provinceName . '_of_' . date('M-Y', strtotime($startDate)) . '_and_' . date('M-Y', strtotime($endDate));
}
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
		<?php 
                //include top
                include PUBLIC_PATH."html/top.php";
                //include top
        include PUBLIC_PATH."html/top_im.php";?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Outlet Wise Comparison of Performance (In Terms of CYP)</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php include('sub_dist_form.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (isset($_POST['submit'])) {
                    //select query
                    //gets
                    //pk id
                    //hf type
                    //Month1
                    //Month2
                    //diff
                    //diffpercent
                	$qry = "SELECT
                                        A.pk_id,
                                        A.hf_type,
                                        A.Month1,
                                        A.Month2,
                                        (A.Month2 - A.Month1) AS diff,
                                        (((A.Month2 - A.Month1) / A.Month1) * 100) AS diffPer
                                FROM
                                        (
                                                SELECT
                                                        A.pk_id,
                                                        A.hf_type,
                                                        (IFNULL(A.Month1, 0) + IFNULL(B.Month1, 0)) AS Month1,
                                                        (IFNULL(A.Month2, 0) + IFNULL(B.Month2, 0)) AS Month2
                                                FROM
                                                        (
                                                                SELECT
                                                                        tbl_hf_type.pk_id,
                                                                        tbl_hf_type.hf_type,
                                                                        tbl_hf_type_rank.hf_type_rank,
                                                                        SUM(IF (DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') = '$startDate', (tbl_hf_data.issue_balance * provincial_cyp_factors.cyp_factor), 0)) AS Month1,
                                                                        SUM(IF (DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') = '$endDate', (tbl_hf_data.issue_balance * provincial_cyp_factors.cyp_factor), 0)) AS Month2
                                                                FROM
                                                                        tbl_hf_data
                                                                INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                                                INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                                                INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                                                                INNER JOIN tbl_hf_type_rank ON tbl_hf_type.pk_id = tbl_hf_type_rank.hf_type_id
                                                                INNER JOIN provincial_cyp_factors ON tbl_warehouse.prov_id = provincial_cyp_factors.province_id AND tbl_hf_data.item_id = provincial_cyp_factors.item_id AND tbl_warehouse.stkid = provincial_cyp_factors.stakeholder_id
                                                                WHERE
                                                                        tbl_hf_type_rank.stakeholder_id = $stakeholder
                                                                AND tbl_hf_type_rank.province_id = $selProv
                                                                AND tbl_warehouse.prov_id = $selProv
                                                                AND tbl_warehouse.stkid = $stakeholder
                                                                AND itminfo_tab.itm_category = 1
                                                                AND DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') BETWEEN '$startDate' AND '$endDate'
                                                                GROUP BY
                                                                        tbl_hf_type.pk_id
                                                        ) A
                                                LEFT JOIN (
                                                        SELECT
                                                                tbl_warehouse.hf_type_id,
                                                                SUM(IF (DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') = '$startDate', (tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) * provincial_cyp_factors.cyp_factor, 0)) AS Month1,
                                                                SUM(IF (DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') = '$endDate', (tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) * provincial_cyp_factors.cyp_factor, 0)) AS Month2
                                                        FROM
                                                                tbl_hf_data
                                                        INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                                                        INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                                        INNER JOIN provincial_cyp_factors ON tbl_warehouse.prov_id = provincial_cyp_factors.province_id AND tbl_hf_data.item_id = provincial_cyp_factors.item_id AND tbl_warehouse.stkid = provincial_cyp_factors.stakeholder_id
                                                        WHERE
                                                                DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') BETWEEN '$startDate' AND '$endDate'
                                                        AND tbl_warehouse.stkid = $stakeholder
                                                        AND tbl_warehouse.prov_id = $selProv
                                                        AND itminfo_tab.itm_category = 2
                                                        GROUP BY
                                                                tbl_warehouse.hf_type_id
                                                ) B ON A.pk_id = B.hf_type_id
                                                ORDER BY
                                                        A.hf_type_rank
                                        ) A";
                        //query result
                    $qryRes = mysql_query($qry);
                    //if result exists
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        ?>
                            <?php 
                            // include sub_dist_reports
                            include('sub_dist_reports.php'); ?>
                            <div class="col-md-12" style="overflow:auto;">
                                <table width="100%">
                                    <tr>
                                        <td align="center">
                                            <h4 class="center">
                                                Outlet Wise Comparison of Performance (In Terms of CYP) <br>
                                                For the month of  <?php echo date('M-Y', strtotime($startDate)) . ' and ' . date('M-Y', strtotime($endDate)) . ', Province ' . $provinceName; ?>  - <?php echo $stk_name; ?>
                                            </h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 10px;">
                                            <table width="100%" id="myTable" cellspacing="0" align="center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2" width="5%">S.No</th>
                                                        <th rowspan="2">Outlet</th>
                                                        <th colspan="2">CYP</th>
                                                        <th rowspan="2" width="20%">Increase / Decrease</th>
                                                        <th rowspan="2" width="20%">Percentage Increase / Decrease</th>
                                                    </tr>
                                                    <tr>
                                                        <th><?php echo date('M-Y', strtotime($startDate)); ?></th>
                                                        <th><?php echo date('M-Y', strtotime($endDate)); ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    //counter 
                                                    $counter = 1;
                                                    //m1 Total 
                                                    $m1Total = 0;
                                                    //m2 Total 
                                                    $m2Total = 0;
						//color							
                                                    $color = '';
                                                    //fetch result
                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                        //m1 Total 
                                                        $m1Total += $row['Month1'];
                                                        //m2 Total 
                                                        $m2Total += $row['Month2'];
														$color = ($row['Month2'] < $row['Month1']) ? '#ED4E66' : '#000';
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?php echo $counter++; ?></td>
                                                            <td><?php echo $row['hf_type']; ?></td>
                                                            <td class="right"><?php echo ($row['Month1'] != 0) ? number_format($row['Month1']) : 0; ?></td>
                                                            <td class="right"><?php echo ($row['Month2'] != 0) ? number_format($row['Month2']) : 0; ?></td>
                                                            <td class="right" style="color: <?php echo $color;?>"><?php echo ($row['diff'] != 0) ? number_format($row['diff']) : 0; ?></td>
                                                            <td class="right" style="color: <?php echo $color;?>"><?php echo ($row['Month1'] != 0 && $row['Month2']) ? number_format($row['diffPer']) : ''; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
						//difference							
                                                    $diff = ($m2Total - $m1Total);
						//difference percent
                                                    $diffPer = (($m2Total - $m1Total) / $m1Total) * 100;
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                <?php 
                                                //color
                                                $color = ($diff > 0) ? '#000' : '#ED4E66';?>
                                                    <tr>
                                                        <th colspan="2" class="center">Total</th>
                                                        <th class="right"><?php echo ($m1Total != 0) ? number_format($m1Total) : 0; ?></th>
                                                        <th class="right"><?php echo ($m2Total != 0) ? number_format($m2Total) : 0; ?></th>
                                                        <th class="right" style="color: <?php echo $color;?>"><?php echo ($diff != 0) ? number_format($diff) : 0; ?></th>
                                                        <th class="right" style="color: <?php echo $color;?>"><?php echo ($m1Total != 0 && $m2Total != 0) ? number_format($diffPer) : ''; ?></th>
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
                ?>
            </div>
        </div>
    </div>
	<?php 
        //include footer
        include PUBLIC_PATH."/html/footer.php";?>
</body>
</html>