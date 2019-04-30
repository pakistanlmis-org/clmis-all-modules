<?php
/**
 * cls11
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
$rptId = 'clr11';

//default stakeholder
if(empty($stakeholder))
$stakeholder = (!empty($_SESSION['user_stakeholder1'])?$_SESSION['user_stakeholder1']:'1');

//if submitted
if (isset($_POST['submit'])) {
    //get selected stakeholder
    $stakeholder = mysql_real_escape_string($_POST['stakeholder']);
    //get selected month
    $selMonth = mysql_real_escape_string($_POST['month_sel']);
    //get selected year
    $selYear = mysql_real_escape_string($_POST['year_sel']);
    //get selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //get selected item
    $selItem = mysql_real_escape_string($_POST['itm_id']);
    //get reporting date
    $reportingDate = mysql_real_escape_string($_POST['year_sel']) . '-' . $selMonth . '-01';
    // Get Province name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $selProv";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    //province name
    $provinceName = $row['LocName'];
    //select query
    // Get 
    // Product name
    //product id
    $qry = "SELECT
                itminfo_tab.itmrec_id,
                itminfo_tab.itm_name
            FROM
                itminfo_tab
            WHERE
                itminfo_tab.itm_id = '$selItem' ";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    //item name
    $itemName = $row['itm_name'];
    //item rec id
    $itemRecId = $row['itmrec_id'];
    //file name
    $fileName = 'CLR11_' . $itemName . '_' . $provinceName . '_for_' . date('M-Y', strtotime($reportingDate));
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
                        <h3 class="page-title row-br-b-wp">District Wise Provincial Contraceptive Stock and Sale Report</h3>
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
                    //B.PkLocID,
                    //Loccation Name,
                    //pk id,
                    //hf type,
                    //OB,
                    //Rcv,
                    //Issue,
                    //CB,
                    //hf type rank
                      $qry = "SELECT
                                                B.PkLocID,
                                                B.LocName,
                                                A.pk_id,
                                                A.hf_type,
                                                A.OB,
                                                A.Rcv,
                                                A.Issue,
                                                A.CB,
                                                A.hf_type_rank
                                        FROM(
                                                SELECT
                                                        tbl_locations.PkLocID,
                                                        tbl_locations.LocName,
                                                        tbl_hf_type.pk_id,
                                                        tbl_hf_type.hf_type,
                                                        tbl_hf_type_rank.hf_type_rank,
                                                        SUM(tbl_hf_type_data.opening_balance) AS OB,
                                                        SUM(tbl_hf_type_data.received_balance) AS Rcv,
                                                        SUM(tbl_hf_type_data.issue_balance) AS Issue,
                                                        SUM(tbl_hf_type_data.closing_balance) AS CB
                                                FROM
                                                        tbl_hf_type_data
                                                INNER JOIN tbl_hf_type ON tbl_hf_type.pk_id = tbl_hf_type_data.facility_type_id
                                                INNER JOIN tbl_locations ON tbl_locations.PkLocID = tbl_hf_type_data.district_id
                                                INNER JOIN tbl_hf_type_rank ON tbl_hf_type.pk_id = tbl_hf_type_rank.hf_type_id
                                                WHERE
                                                        tbl_locations.ParentID = $selProv
                                                AND tbl_hf_type_data.reporting_date = '$reportingDate'
                                                AND tbl_hf_type_data.item_id = $selItem
                                                AND tbl_hf_type_rank.province_id = $selProv
                                                AND tbl_hf_type_rank.stakeholder_id = $stakeholder
                                                GROUP BY
                                                        tbl_hf_type_data.district_id,
                                                        tbl_hf_type.hf_type
                                                ORDER BY
                                                        tbl_locations.LocName ASC,
                                                        tbl_hf_type_rank.hf_type_rank ASC
                                                ) A
                                        RIGHT JOIN (
                                                SELECT DISTINCT
                                                        tbl_locations.PkLocID,
                                                        tbl_locations.LocName
                                                FROM
                                                        tbl_warehouse
                                                INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                                                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                WHERE
                                                        tbl_warehouse.prov_id = $selProv
                                                AND tbl_warehouse.stkid = $stakeholder
                                        ) B ON A.PkLocID = B.PkLocID
                                ORDER BY
                                        B.LocName ASC,
                                        A.hf_type_rank";
                    //get query result
                    $qryRes = mysql_query($qry);
                  
                    $qryRes1 = mysql_query($qry);

                    // Get Distinct Health facility Types
                    while ($row = mysql_fetch_array($qryRes1)) {
                        if(!empty($row['pk_id']) && !empty($row['hf_type']))
                        $hfType[$row['pk_id']] = $row['hf_type'];
                    }
                    //echo '<pre>';print_r($hfType);
                    // Sort the array
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        //include sub_dist_reports
                        include('sub_dist_reports.php');
                        ?>
                        <div class="col-md-12" style="overflow:auto;">
                        <?php
                        $distId = '';
                        //fetch result
                        while ($row = mysql_fetch_array($qryRes)) {
                            if ($distId != $row['PkLocID']) {
                                //location name
                                $districtName[$row['PkLocID']] = $row['LocName'];
                                //pk location id
                                $distId = $row['PkLocID'];
                            }
                            //Opening Balance
                            $data[$row['PkLocID']]['OB'][$row['pk_id']] = $row['OB'];
                            //receive
                            $data[$row['PkLocID']]['Rcv'][$row['pk_id']] = $row['Rcv'];
                            //issue
                            $data[$row['PkLocID']]['Issue'][$row['pk_id']] = $row['Issue'];
                            //Closing Balance
                            $data[$row['PkLocID']]['CB'][$row['pk_id']] = $row['CB'];
                        }
                        ?>
                            <table width="100%">
                                <tr>
                                    <td align="center"><h4 class="center"> District Wise Provincial Contraceptive Stock and Sale Report for <?php echo $itemName; ?> <br>
                                            For the month of <?php echo date('M', mktime(0, 0, 0, $selMonth, 1)) . '-' . $selYear . ', Province ' . $provinceName; ?> - <?php echo $stk_name; ?> </h4></td>
                                    <td><h4 class="right">CLR-11</h4></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 10px;"><table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" width="4%">S.No.</th>
                                                    <th rowspan="2" width="11%">District</th>
                                                    <th colspan="4">District Store Total</th>
                                                    <th colspan="4">Field Total</th>
        <?php
        //fetch result
        foreach ($hfType as $id => $name) {
            echo "<th colspan=\"4\">$name</th>";
        }
        ?>
                                                </tr>
                                                <tr>
        <?php
        //District Store Total Columns
        //Opening Balance
        echo "<th width='" . (85 / (count($hfType) * 4 + 8)) . "%'>Opening Balance</th>";
        //Receive
        echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Receive</th>";
        //Issue
        echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Issue</th>";
        //Closing Balance
        echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Closing Balance</th>";

        //Field Total Columns
        //Opening Balance
        echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Opening Balance</th>";
        //Receive
        echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Receive</th>";
        //Issue
        echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Issue</th>";
        //Closing Balance
        echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Closing Balance</th>";

        foreach ($hfType as $name) {
            //Opening Balance
            echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Opening Balance</th>";
            //Receive
            echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Receive</th>";
            //Issue
            echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Issue</th>";
            //Closing Balance
            echo "<th width='" . (85 / (count($hfType) * 4+ 8)) . "%'>Closing Balance</th>";
        }
        ?>
                                                </tr>
                                            </thead>
                                            <tbody>
        <?php
        $counter = 1;
        $opening_gtotal_dist = $receive_gtotal_dist = $issue_gtotal_dist = $closing_gtotal_dist = 0;
        //fetch results
        foreach ($districtName as $id => $name) {

            // Get District warehouse
            $qry = "SELECT
                            tbl_warehouse.wh_id
                    FROM
                            tbl_warehouse
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    WHERE
                            tbl_warehouse.dist_id = $id
                    AND tbl_warehouse.stkid = $stakeholder
                    AND stakeholder.lvl = 3
                    ORDER BY
                            tbl_warehouse.wh_id ASC
                    LIMIT 1";
            //query result
            $qryRes = mysql_fetch_array(mysql_query($qry));
            //district warehouse
            $distWH = $qryRes['wh_id'];
            //select query
            //gets
            //tbl_warehouse.dist_id,
            //item id,
            //Opening Balance,
            //Receive,
            //Issue,
            //Closing Balance DistStore
           $qry_dist = "SELECT
                    tbl_warehouse.dist_id,
                    itminfo_tab.itm_id,
                    tbl_wh_data.wh_obl_a AS OB,
                    tbl_wh_data.wh_received AS Rcv,
                    tbl_wh_data.wh_issue_up AS Issue,
                    tbl_wh_data.wh_cbl_a AS CBDistStore
            FROM
                    tbl_wh_data
            INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
            INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
            WHERE
                    tbl_wh_data.RptDate = '$reportingDate'
            AND tbl_wh_data.wh_id = $distWH
            AND tbl_wh_data.item_id = '$itemRecId'
            AND tbl_warehouse.stkid = $stakeholder
            GROUP BY
                    tbl_wh_data.item_id";
            //query result
            $qryRes_dist = mysql_query($qry_dist);
            //if result exists							
            $opening_dist = $receive_dist = $issue_dist = $CBDistStore = 0;
            if (mysql_num_rows($qryRes_dist) > 0) {
                $row_dist = mysql_fetch_array($qryRes_dist);
                $opening_dist = $row_dist['OB'];
                $receive_dist = $row_dist['Rcv'];
                $issue_dist = $row_dist['Issue'];
                $CBDistStore = $row_dist['CBDistStore'];

                $opening_gtotal_dist += $opening_dist;
                $receive_gtotal_dist += $receive_dist;
                $issue_gtotal_dist += $issue_dist;
                $closing_gtotal_dist += $CBDistStore;
            }
            ?>
                                                    <tr>
                                                        <td class="center"><?php echo $counter++; ?></td>
                                                        <td><?php echo $name; ?></td>
            <?php
            $district_total_html = $field_total_html = $facilities_html = "";
            $opening = $receive = $issue = $closing = 0;
            foreach ($hfType as $hfId => $hfName) {
                $opening += (isset($data[$id]['OB'][$hfId]) ? $data[$id]['OB'][$hfId] : 0);
                $receive += (isset($data[$id]['Rcv'][$hfId]) ? $data[$id]['Rcv'][$hfId] : 0);
                $issue += (isset($data[$id]['Issue'][$hfId]) ? $data[$id]['Issue'][$hfId] : 0);
                $closing += (isset($data[$id]['CB'][$hfId]) ? $data[$id]['CB'][$hfId] : 0);

                $facilities_html .= "<td class=\"right\">" . (isset($data[$id]['OB'][$hfId]) ? number_format($data[$id]['OB'][$hfId]) : 0) . "</td>";
                $facilities_html .= "<td class=\"right\">" . (isset($data[$id]['Rcv'][$hfId]) ? number_format($data[$id]['Rcv'][$hfId]) : 0) . "</td>";
                $facilities_html .= "<td class=\"right\">" . (isset($data[$id]['Issue'][$hfId]) ? number_format($data[$id]['Issue'][$hfId]) : 0) . "</td>";
                $facilities_html .= "<td class=\"right\">" . (isset($data[$id]['CB'][$hfId]) ? number_format($data[$id]['CB'][$hfId]) : 0) . "</td>";
            }
            $district_total_html .= "<td class=\"right\">" . number_format($opening_dist) . "</td>";
            $district_total_html .= "<td class=\"right\">" . number_format($receive_dist) . "</td>";
            $district_total_html .= "<td class=\"right\">" . number_format($issue_dist) . "</td>";
            $district_total_html .= "<td class=\"right\">" . number_format($CBDistStore) . "</td>";

            $field_total_html .= "<td class=\"right\">" . number_format($opening) . "</td>";
            $field_total_html .= "<td class=\"right\">" . number_format($receive) . "</td>";
            $field_total_html .= "<td class=\"right\">" . number_format($issue) . "</td>";
            $field_total_html .= "<td class=\"right\">" . number_format($closing) . "</td>";

            echo $district_total_html;
            echo $field_total_html;
            echo $facilities_html;
            ?>
                                                    </tr>
                                                        <?php
                                                    }
                                                    //select query
                                                    //gets
                                                    //tbl_locations.PkLocID,
                                                    //Location Name,
                                                    //pk id,
                                                    //hf type,
                                                    //Opening Balance,
                                                    //Receive,
                                                    //Issue,
                                                    //Closing Balance
                                                    $qry = "SELECT
                                                                tbl_locations.PkLocID,
                                                                tbl_locations.LocName,
                                                                tbl_hf_type.pk_id,
                                                                tbl_hf_type.hf_type,
                                                                SUM(tbl_hf_type_data.opening_balance) AS OB,
                                                                SUM(tbl_hf_type_data.received_balance) AS Rcv,
                                                                SUM(tbl_hf_type_data.issue_balance) AS Issue,
                                                                SUM(tbl_hf_type_data.closing_balance) AS CB
                                                        FROM
                                                                tbl_hf_type_data
                                                        INNER JOIN tbl_hf_type ON tbl_hf_type.pk_id = tbl_hf_type_data.facility_type_id
                                                        INNER JOIN tbl_locations ON tbl_locations.PkLocID = tbl_hf_type_data.district_id
                                                        WHERE
                                                                tbl_locations.ParentID = $selProv
                                                        AND tbl_hf_type_data.reporting_date = '$reportingDate'
                                                        AND tbl_hf_type_data.item_id = $selItem
                                                        GROUP BY
                                                                tbl_hf_type.hf_type
                                                        ORDER BY
                                                                tbl_hf_type.hf_rank ASC";
                                                    //query result
                                                    $qryRes = mysql_query($qry);
                                                    unset($data);
                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                        $data[$row['pk_id']]['OB'] = $row['OB'];
                                                        $data[$row['pk_id']]['Rcv'] = $row['Rcv'];
                                                        $data[$row['pk_id']]['Issue'] = $row['Issue'];
                                                        $data[$row['pk_id']]['CB'] = $row['CB'];
                                                    }
                                                    ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="right">Grand Total</th>
        <?php
        $gtotal_district = $gtotal_field_total = $gtotal_facilities = "";
        $opening = $receive = $issue = $closing = 0;
        foreach ($hfType as $id => $name) {
            $opening += ((!empty($data[$id]['OB']))?$data[$id]['OB']:'0');
            $receive += ((!empty($data[$id]['Rcv']))?$data[$id]['Rcv']:'0');
            $issue   += ((!empty($data[$id]['Issue']))?$data[$id]['Issue']:'0');
            $closing += ((!empty($data[$id]['CB']))?$data[$id]['CB']:'0');
            $gtotal_facilities .= "<th class=\"right\">" . number_format(!empty($data[$id]['OB'])?$data[$id]['OB']:0) . "</th>";
            $gtotal_facilities .= "<th class=\"right\">" . number_format(!empty($data[$id]['Rcv'])?$data[$id]['Rcv']:0) . "</th>";
            $gtotal_facilities .= "<th class=\"right\">" . number_format(!empty($data[$id]['Issue'])?$data[$id]['Issue']:0) . "</th>";
            $gtotal_facilities .= "<th class=\"right\">" . number_format(!empty($data[$id]['CB'])?$data[$id]['CB']:0) . "</th>";
        }

        $gtotal_district .= "<th class=\"right\">" . number_format($opening_gtotal_dist) . "</th>";
        $gtotal_district .= "<th class=\"right\">" . number_format($receive_gtotal_dist) . "</th>";
        $gtotal_district .= "<th class=\"right\">" . number_format($issue_gtotal_dist) . "</th>";
        $gtotal_district .= "<th class=\"right\">" . number_format($closing_gtotal_dist) . "</th>";

        $gtotal_field_total .= "<th class=\"right\">" . number_format($opening) . "</th>";
        $gtotal_field_total .= "<th class=\"right\">" . number_format($receive) . "</th>";
        $gtotal_field_total .= "<th class=\"right\">" . number_format($issue) . "</th>";
        $gtotal_field_total .= "<th class=\"right\">" . number_format($closing) . "</th>";

        echo $gtotal_district;
        echo $gtotal_field_total;
        echo $gtotal_facilities;
        ?>
                                                </tr>
                                            </tfoot>
                                        </table></td>
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
unset($data, $issue, $hfType, $districtName);
?>
        </div>
    </div>
</div>
<?php include PUBLIC_PATH . "/html/footer.php"; ?>
</body>
</html>