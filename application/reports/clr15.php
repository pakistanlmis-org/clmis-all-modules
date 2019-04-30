<?php
ini_set('max_execution_time', 0);
/**
 * clr15
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
$rptId = 'clr15';

//default stakeholder
if(empty($stakeholder))
$stakeholder = (!empty($_SESSION['user_stakeholder1'])?$_SESSION['user_stakeholder1']:'1');


//if submitted
if (isset($_POST['submit'])) {
    //get selected stakeholder
    $stakeholder = mysql_real_escape_string($_POST['stakeholder']);
    $stk_is_public = false;
if($stakeholder=='1' || $stakeholder=='2' ||$stakeholder=='7' ||$stakeholder=='73' ||$stakeholder=='9' || $stakeholder=='145')
    $stk_is_public=true;


    //get  from date
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //get  to date
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //get selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //get district id
    $districtId = mysql_real_escape_string($_POST['district']);
    //select query
    // Get 
    // district name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $districtId";
    //query result
    $row = mysql_fetch_array(mysql_query($qry));
    $distrctName = $row['LocName'];
    $fileName = 'CLR15_' . $distrctName . '_for_' . $fromDate . '-' . $toDate;

    // Get District warehouse
    $qry = "SELECT
                                GROUP_CONCAT(tbl_warehouse.wh_id) as wh_id
                        FROM
                                tbl_warehouse
                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                        WHERE
                            tbl_warehouse.prov_id = $selProv AND
                            tbl_warehouse.stkid = $stakeholder
                        AND stakeholder.lvl = 3 ";
    if(!empty($districtId) && $districtId>0)
    $qry .= "            AND tbl_warehouse.dist_id = $districtId ";
    $qry .= "            ORDER BY
				tbl_warehouse.wh_id ASC
			LIMIT 1";
    //query result
    //echo $qry;exit;
    $res = mysql_query($qry);
    $qryRes = mysql_fetch_array($res);
    //district warehouse
    $distWH = $qryRes['wh_id'];
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
                        <h3 class="page-title row-br-b-wp">District Contraceptive Stock Report</h3>
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
                    // Query for Part I
                    //gets
                    //opening balance
                    //receive
                    //issue
                    //item id
                    //generic name
                    //item category
                    //method type
                    //method rank
                    //CB district store
                    //CB field store
                    //CB total
                    $qry = "SELECT
								A.OB,
								A.Rcv,
								A.Issue,
								B.itm_id,
								B.itm_name,
								B.generic_name,
								B.itm_category,
								B.method_type,
								B.method_rank,
								B.frmindex,
								B.CBDistStore,
								B.CBFldStore,
								(B.CBDistStore + B.CBFldStore) AS CBTotal
							FROM
								(
									SELECT
										tbl_warehouse.dist_id,
										itminfo_tab.itm_id,
										SUM(tbl_wh_data.wh_obl_a) AS OB,
										SUM(tbl_wh_data.wh_received) AS Rcv,
										SUM(tbl_wh_data.wh_issue_up) AS Issue
									FROM
										tbl_wh_data
									INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
									INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
									WHERE
										DATE_FORMAT(tbl_wh_data.RptDate, '%Y-%m') BETWEEN '$fromDate' AND '$toDate'
									AND tbl_wh_data.wh_id in ($distWH)
									AND tbl_warehouse.stkid = $stakeholder
                                                                            AND itminfo_tab.itm_category IN (1,2)
									GROUP BY
										tbl_wh_data.item_id
								) A
						RIGHT JOIN (
								SELECT
									itminfo_tab.itm_id,
									itminfo_tab.itm_name,
									itminfo_tab.method_type,
									itminfo_tab.method_rank,
									itminfo_tab.frmindex,
									itminfo_tab.generic_name,
									itminfo_tab.itm_category,
									tbl_warehouse.dist_id AS district_id,
									SUM(IF(stakeholder.lvl = 3, tbl_wh_data.wh_cbl_a, 0)) AS CBDistStore,
									SUM(IF(stakeholder.lvl = 4, tbl_wh_data.wh_cbl_a, 0)) AS CBFldStore
								FROM
									tbl_warehouse
								INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
								INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
								INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
								WHERE
								
                                                                tbl_warehouse.prov_id = $selProv  
                                                                ".(((!empty($districtId) && $districtId>0))?" AND tbl_warehouse.dist_id = $districtId  ":"")."
								AND tbl_warehouse.stkid = $stakeholder
								AND stakeholder.lvl IN (3, 4)
								AND DATE_FORMAT(tbl_wh_data.RptDate, '%Y-%m') BETWEEN '$fromDate' AND '$toDate'
                                                                    AND itminfo_tab.itm_category IN (1,2)
								GROUP BY
									tbl_wh_data.item_id
						) B ON A.itm_id = B.itm_id
						AND A.dist_id = B.district_id
						ORDER BY
							B.method_rank ASC,
							B.frmindex ASC";
                    //query result
//                    echo $qry;exit;
                    $qryRes = mysql_query($qry);

                    // Query for Part II
                    //gets
                    // tbl_hf_type.pk_id AS hf_type_id,
                    //hf type,
                    //item id,
                    //item id,
                    //item category,
                    //opening balance,
                    //received balance,
                    //issue balance,
                    //closing balance
                    $qry1 = "SELECT
								*,
								(COALESCE(A.opening_balance,0) + COALESCE(A.received_balance,0) + COALESCE(A.adjustment_positive,0) - COALESCE(A.issue_balance,0) - COALESCE(A.adjustment_negative,0) ) AS closing_balance
							FROM (SELECT
                                tbl_hf_type.pk_id AS hf_type_id,
                                tbl_hf_type.hf_type,
                                tbl_hf_data.item_id,
                                itminfo_tab.itm_id,
                                itminfo_tab.itm_category,
                                SUM(tbl_hf_data.opening_balance) AS opening_balance,
                                SUM(tbl_hf_data.received_balance) AS received_balance,
                                SUM(tbl_hf_data.adjustment_positive) AS adjustment_positive,
                                SUM(tbl_hf_data.adjustment_negative) AS adjustment_negative,
                                SUM(IF(itminfo_tab.itm_category = 2, (tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp), tbl_hf_data.issue_balance)) AS issue_balance,
                                SUM(tbl_hf_data.closing_balance) AS closing_balance_old
                            FROM
                                tbl_warehouse
                            INNER JOIN tbl_hf_type ON tbl_hf_type.pk_id = tbl_warehouse.hf_type_id
							INNER JOIN tbl_hf_type_rank ON tbl_hf_type.pk_id = tbl_hf_type_rank.hf_type_id
                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                            INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
							LEFT JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                            WHERE
                           tbl_warehouse.prov_id = $selProv  
                             ".(((!empty($districtId) && $districtId>0))?" AND tbl_warehouse.dist_id = $districtId  ":"")."
								
                            AND tbl_warehouse.stkid = $stakeholder
							AND tbl_hf_type_rank.province_id = $selProv
							AND tbl_hf_type_rank.stakeholder_id = $stakeholder
							AND DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') BETWEEN '$fromDate' AND '$toDate'
                                                            AND itminfo_tab.itm_category IN (1,2)
                            GROUP BY
                                tbl_hf_type.pk_id,
                                itminfo_tab.itm_id
                            ORDER BY
                                tbl_hf_type_rank.hf_type_rank ASC,
                                itminfo_tab.frmindex ASC) A";
                    //query result
                    
                    $qryRes1 = mysql_query($qry1);
                    //if result exists
                    if (mysql_num_rows(mysql_query($qry)) > 0 && mysql_num_rows(mysql_query($qry1)) > 0) {
                        ?>
                        <?php include('sub_dist_reports.php'); ?>
                        <div class="col-md-12" style="overflow:auto;">
                            <table width="100%">
                                <tr>
                                    <td align="center">
                                        <h4 class="center bold">
                                            District Contraceptive Stock Report <?php echo $stk_name;?> <br>
                                            <?php
                                            if ($fromDate != $toDate) {
                                                $reportingPeriod = "For the period of " . date('M-Y', strtotime($fromDate)) . ' to ' . date('M-Y', strtotime($toDate));
                                            } else {
                                                $reportingPeriod = "For the month of " . date('M-Y', strtotime($fromDate));
                                            }
                                            ?>
                                            <?php echo $reportingPeriod . ', District ' . $distrctName; ?>
                                        </h4>
                                    </td>
                                    <td>
                                        <h4 class="right">CLR-15</h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <?php
                                        while ($row = mysql_fetch_array($qryRes)) {
                                            //item id
                                            $itemIds[] = $row['itm_id'];
                                            //product
                                            $product[$row['method_type']][] = $row['itm_name'];
                                            //check item category						
                                            if ($row['itm_category'] == 1) {
                                                //OB
                                                $ob[$row['itm_id']] = $row['OB'];
                                                //receive
                                                $rcv[$row['itm_id']] = $row['Rcv'];
                                                //issue
                                                $issue[$row['itm_id']] = $row['Issue'];
                                                //CB district store
                                                $CBDistStore[$row['itm_id']] = $row['CBDistStore'];
                                                //CB field store
                                                $CBFldStore[$row['itm_id']] = $row['CBFldStore'];
                                                //CB total
                                                $CBTotal[$row['itm_id']] = $row['CBTotal'];
                                            } else {
                                                //OB
                                                $ob[$row['itm_id']] = 0;
                                                //receive
                                                $rcv[$row['itm_id']] = 0;
                                                //issue
                                                $issue[$row['itm_id']] = 0;
                                                //CB district store
                                                $CBDistStore[$row['itm_id']] = 0;
                                                //CB total
                                                $CBTotal[$row['itm_id']] = 0;
                                            }
                                            //check method type						
                                            if (strtoupper($row['method_type']) == strtoupper($row['generic_name'])) {
                                                $methodType[$row['method_type']]['rowspan'] = 2;
                                            } else {
                                                $genericName[$row['generic_name']][] = $row['itm_name'];
                                            }
                                        }
                                        ?>
                                        <h5 class="center bold">Part-I</h5>
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="3" width="20%">District Store</th>
                                                    <?php
                                                    //product
                                                    foreach ($product as $proType => $proNames) {
                                                        echo "<th colspan=" . sizeof($proNames) . " rowspan='" . (isset($methodType[$proType]['rowspan']) ? $methodType[$proType]['rowspan'] : '') . "'>$proType</th>";
                                                    }
                                                    ?>
                                                    <th rowspan="3" width="10%">Remarks</th>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $col = '';
                                                    //generic name
                                                    foreach ($genericName as $name => $proNames) {
                                                        echo "<th colspan=" . sizeof($proNames) . ">$name</th>";
                                                    }
                                                    ?>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $col = '';
                                                    //product
                                                    foreach ($product as $proType => $proNames) {
                                                        foreach ($proNames as $name) {
                                                            echo "<th width='" . (70 / count($itemIds)) . "%'>$name</th>";
                                                        }
                                                    }
                                                    ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="text-align:center">1</td>
                                                    <?php
                                                    $counter = 2;
                                                    //item ids
                                                    foreach ($itemIds as $id) {
                                                        echo "<td style=\"text-align:center !important;\">" . $counter++ . "</td>";
                                                    }
                                                    ?>
                                                    <td class="center"><?php echo $counter; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Opening Balance</td>
                                                    <?php
                                                    foreach ($itemIds as $id) {
                                                        echo "<td class=\"right\">" . (isset($ob[$id]) ? number_format($ob[$id]) : '0') . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>Received from Central Warehouse</td>
                                                    <?php
                                                    foreach ($itemIds as $id) {
                                                        echo "<td class=\"right\">" . number_format($rcv[$id]) . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>Issued to field</td>
                                                    <?php
                                                    foreach ($itemIds as $id) {
                                                        echo "<td class=\"right\">" . number_format($issue[$id]) . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="<?php echo sizeof($itemIds) + 2; ?>">Closing Balance</td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;&nbsp;(i) District Store</td>
                                                    <?php
                                                    foreach ($itemIds as $id) {
                                                        echo "<td class=\"right\">" . number_format($CBDistStore[$id]) . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;&nbsp;(ii) Field Store</td>
                                                    <?php
                                                    // Get surgery Cases
                                                    //gets
                                                    //item id
                                                    //CS done
                                                    $csQry = "SELECT
                                                                        tbl_hf_data.item_id,
                                                                        SUM(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS CS_Done
                                                                    FROM
                                                                        tbl_hf_data
                                                                    INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                                                                    INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                                                    WHERE
                                                                        tbl_warehouse.prov_id = $selProv  
                                                                        ".(((!empty($districtId) && $districtId>0))?" AND tbl_warehouse.dist_id = $districtId  ":"")."
                                                                        AND DATE_FORMAT(tbl_hf_data.reporting_date, '%Y-%m') BETWEEN '$fromDate' AND '$toDate'
                                                                    GROUP BY
                                                                        tbl_hf_data.item_id
                                                                    ORDER BY
                                                                        tbl_hf_data.item_id ASC";
                                                    //query result
                                                    $csRows = mysql_query($csQry);
                                                    //fetch result
                                                    while ($row = mysql_fetch_array($csRows)) {
                                                        //CB field store    
                                                        $CBFldStore[$row['item_id']] = $row['CS_Done'];
                                                    }

                                                    foreach ($itemIds as $id) {
                                                        echo "<td class=\"right\">" . number_format($CBFldStore[$id]) . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Total</b></td>
                                                    <?php
                                                    foreach ($itemIds as $id) {
                                                        echo "<td class=\"right\">" . number_format($CBTotal[$id]) . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <?php
                                        // Unset variables
                                        unset($ob, $rcv, $issue, $CBDistStore, $CBFldStore, $CBTotal);
                                        ?>                   

                                        <h5 style="margin-top:20px; text-align:center;" class="center bold">Part-II</h5>

                                        <?php
                                        //hf type id
                                        $hfTypeId = '';
                                        //fetch result
                                        while ($row = mysql_fetch_array($qryRes1)) {
                                            //check item category
                                            if ($row['itm_category'] == 1) {
                                                //opening balance
                                                $data[$row['hf_type_id']]['ob'][$row['itm_id']] = $row['opening_balance'];
                                                //receive balance
                                                $data[$row['hf_type_id']]['rcv'][$row['itm_id']] = $row['received_balance'];
                                                //issue balance
                                                $data[$row['hf_type_id']]['issue'][$row['itm_id']] = $row['issue_balance'];
                                                //closing balance
                                                $data[$row['hf_type_id']]['cb'][$row['itm_id']] = $row['closing_balance'];
                                                //opening balance
                                                $ob[$row['itm_id']][] = $row['opening_balance'];
                                                //receive balance
                                                $rcv[$row['itm_id']][] = $row['received_balance'];
                                                //issue balance
                                                $issue[$row['itm_id']][] = $row['issue_balance'];
                                                //closing balance
                                                $cb[$row['itm_id']][] = $row['closing_balance'];
                                            } else {
                                                //OB
                                                $data[$row['hf_type_id']]['ob'][$row['itm_id']] = 0;
                                                //receive
                                                $data[$row['hf_type_id']]['rcv'][$row['itm_id']] = 0;
                                                //CB
                                                $data[$row['hf_type_id']]['cb'][$row['itm_id']] = 0;
                                                if ($row['hf_type_id'] == 4 || $row['hf_type_id'] == 5) {
                                                    //issue balance
                                                    $data[$row['hf_type_id']]['issue'][$row['itm_id']] = $row['issue_balance'];
                                                    //issue balance
                                                    $issue[$row['itm_id']][] = $row['issue_balance'];
                                                } else {
                                                    $data[$row['hf_type_id']]['issue'][$row['itm_id']] = 0;
                                                }
                                            }
                                            if ($hfTypeId != $row['hf_type_id']) {
                                                $hfTypes[$row['hf_type_id']] = $row['hf_type'];
                                                $hfTypeId = $row['hf_type_id'];
                                            }
                                        }
                                        ?>
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="3" width="20%">Field</th>
                                                    <?php
                                                    foreach ($product as $proType => $proNames) {
                                                        echo "<th colspan=" . sizeof($proNames) . " rowspan='" . (isset($methodType[$proType]['rowspan']) ? $methodType[$proType]['rowspan'] : '') . "'>$proType</th>";
                                                    }
                                                    ?>
                                                    <th rowspan="3" width="10%">Remarks</th>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $col = '';
                                                    foreach ($genericName as $name => $proNames) {
                                                        echo "<th colspan=" . sizeof($proNames) . ">$name</th>";
                                                    }
                                                    ?>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $col = '';
                                                    //product
                                                    foreach ($product as $proType => $proNames) {
                                                        //product name
                                                        foreach ($proNames as $name) {
                                                            echo "<th width='" . (70 / count($itemIds)) . "%'>$name</th>";
                                                        }
                                                    }
                                                    ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="text-align:center">1</td>
                                                    <?php
                                                    $counter = 2;
                                                    //item ids
                                                    foreach ($itemIds as $id) {
                                                        echo "<td style=\"text-align:center !important;\">" . $counter++ . "</td>";
                                                    }
                                                    ?>
                                                    <td class="center"><?php echo $counter; ?></td>
                                                </tr>
                                                <?php
                                                //hf types
                                                foreach ($hfTypes as $hfTypeid => $hfType) {
                                                    ?>
                                                    <tr>
                                                        <td colspan="<?php echo sizeof($itemIds) + 2; ?>"><b><?php echo $hfType; ?></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Opening Balance</td>
                                                        <?php
                                                        //item ids
                                                        foreach ($itemIds as $id) {
                                                            echo "<td class=\"right\">" . number_format($data[$hfTypeid]['ob'][$id]) . "</td>";
                                                        }
                                                        ?>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Received from <?=(($stk_is_public)?' : ':'Provincial Store / ')?> district warehouse</td>
                                                        <?php
                                                        //item ids
                                                        foreach ($itemIds as $id) {
                                                            echo "<td class=\"right\">" . number_format($data[$hfTypeid]['rcv'][$id]) . "</td>";
                                                        }
                                                        ?>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sold/Issued</td>
                                                        <?php
                                                        //item ids
                                                        foreach ($itemIds as $id) {
                                                            echo "<td class=\"right\">" . number_format($data[$hfTypeid]['issue'][$id]) . "</td>";
                                                        }
                                                        ?>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Closing Balance</td>
                                                        <?php
                                                        //item ids
                                                        foreach ($itemIds as $id) {
                                                            echo "<td class=\"right\">" . number_format($data[$hfTypeid]['cb'][$id]) . "</td>";
                                                        }
                                                        ?>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <?php ?>
                                                <tr>
                                                    <td colspan="<?php echo sizeof($itemIds) + 2; ?>"><b>Field Total</b></td>
                                                </tr>
                                                <tr>
                                                    <td>Opening Balance</td>
                                                    <?php
                                                    //item ids
                                                    foreach ($itemIds as $id) {
                                                        echo "<td class=\"right\">" . ((($id == '31' || $id == '32') && !isset($ob[$id])) ? '' : number_format(array_sum($ob[$id]))) . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>Received from <?=(($stk_is_public)?' : ':'Provincial Store / ')?> district warehouse</td>
                                                    <?php
                                                    //item ids
                                                    foreach ($itemIds as $id) {
                                                        echo "<td class=\"right\">" . ((($id == '31' || $id == '32') && !isset($rcv[$id])) ? '' : number_format(array_sum($rcv[$id]))) . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>Sold/Issued</td>
                                                    <?php
                                                    //item ids
                                                    foreach ($itemIds as $id) {
                                                        echo "<td class=\"right\">" . ((($id == '31' || $id == '32') && !isset($issue[$id])) ? '0' : number_format(array_sum($issue[$id]))) . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td>Closing Balance</td>
                                                    <?php
                                                    //item ids
                                                    foreach ($itemIds as $id) {
                                                        echo "<td class=\"right\">" . ((($id == '31' || $id == '32') && !isset($cb[$id])) ? '' : number_format(array_sum($cb[$id]))) . "</td>";
                                                    }
                                                    ?>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </tbody>
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

            // Unset variables
            unset($ob, $cb, $rcv, $issue, $data, $hfTypes, $itemIds, $product);
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