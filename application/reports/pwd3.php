<?php
/**
 * pwd3
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
$rptId = 'pwd3';
//if submitted
if (isset($_POST['submit'])) {
    //selected month
    $selMonth = mysql_real_escape_string($_POST['month_sel']);
    //selected year
    $selYear = mysql_real_escape_string($_POST['year_sel']);
    //selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //reporting Date 
    $reportingDate = mysql_real_escape_string($_POST['year_sel']) . '-' . $selMonth . '-01';
    //hf type
    $hfTypeId = mysql_real_escape_string($_POST['hf_type_sel']);
    //set and
    $and = ($hfTypeId == 0) ? '' : " AND tbl_hf_type_mother_care.facility_type_id = $hfTypeId";
    //select query
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
    //gets
    //hf type
    $queryhf = "SELECT
					hf_type
				FROM
					tbl_hf_type
				WHERE
					pk_id = $hfTypeId";
    //query result
    $rshf = mysql_query($queryhf) or die();
    //fetch result
    $rowHFType = mysql_fetch_array($rshf);
    //hf type name
    $hfTypeName = $rowHFType['hf_type'];
    //file name
    $fileName = 'PWD3' . $provinceName . '_for_' . date('M-Y', strtotime($reportingDate)) . '_' . $hfTypeName;
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
                        <h3 class="page-title row-br-b-wp">District Wise Monthly Report on Acceptor by Method <?php
                            if (!empty($hfTypeName)) {
                                echo "at $hfTypeName";
                            } else {
                                echo "";
                            }
                            ?></h3>
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
                    //district id,
                    //Location Name,
                    //pre New,
                    //pre Old,
                    //pre Total,
                    //post New,
                    //post Old,
                    //post Total,
                    //ailment Child,
                    //ailment Adult,
                    //ailment Total,
                    //general Ailment
                    $qry = "SELECT 
								B.PkLocID AS district_id,
								B.LocName,
								A.preNew,
								A.preOld,
								A.preTotal,
								A.postNew,
								A.postOld,
								A.postTotal,
								A.ailmentChild,
								A.ailmentAdult,
								A.ailmentTotal,
								A.generalAilment
							FROM (
								SELECT
									tbl_hf_type_mother_care.district_id,
									tbl_locations.LocName,
									SUM(tbl_hf_type_mother_care.pre_natal_new) AS preNew,
									SUM(tbl_hf_type_mother_care.pre_natal_old) AS preOld,
									(SUM(tbl_hf_type_mother_care.pre_natal_new) + SUM(tbl_hf_type_mother_care.pre_natal_old)) AS preTotal,
									SUM(tbl_hf_type_mother_care.post_natal_new) AS postNew,
									SUM(tbl_hf_type_mother_care.post_natal_old) AS postOld,
									(SUM(tbl_hf_type_mother_care.post_natal_new) + SUM(tbl_hf_type_mother_care.post_natal_old)) AS postTotal,
									SUM(tbl_hf_type_mother_care.ailment_children) AS ailmentChild,
									SUM(tbl_hf_type_mother_care.ailment_adults) AS ailmentAdult,
									(SUM(tbl_hf_type_mother_care.ailment_children) + SUM(tbl_hf_type_mother_care.ailment_adults)) AS ailmentTotal,
									SUM(tbl_hf_type_mother_care.general_ailment) AS generalAilment
								FROM
									tbl_hf_type_mother_care
									INNER JOIN tbl_locations ON tbl_hf_type_mother_care.district_id = tbl_locations.PkLocID
								WHERE
									tbl_hf_type_mother_care.reporting_date = '$reportingDate'
									AND tbl_locations.ParentID = $selProv
									$and
								GROUP BY
									tbl_hf_type_mother_care.district_id
								ORDER BY
									tbl_locations.LocName ASC
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
								) B ON A.district_id = B.PkLocID
							ORDER BY
								B.LocName ASC";
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
                            <table width="100%">
                                <tr>
                                    <td align="center">
                                        <h4 class="center">
                                            District Wise Monthly Report on Mother care and General Ailment <?php
                                            if (!empty($hfTypeName)) {
                                                echo "at $hfTypeName";
                                            } else {
                                                echo "";
                                            }
                                            ?><br>
                                            For the month of <?php echo date('M', mktime(0, 0, 0, $selMonth, 1)) . '-' . $selYear . ', Province ' . $provinceName; ?>
                                        </h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 10px;">
                                        <?php
                                        if ($selProv == 3) {
                                            ?>
                                            <table width="100%" id="myTable" cellspacing="0" align="center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="3" width="50">Sr. No.</th>
                                                        <th rowspan="3">Name of District</th>
                                                        <th colspan="6">Mothercare (No. of cases)</th>
                                                        <th colspan="3">General Ailment</th>
                                                        <th rowspan="3" width="100">Grand Total</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3">Ante-Natal</th>
                                                        <th colspan="3">Post-Natal</th>
                                                        <th width="70" rowspan="2">Children</th>
                                                        <th width="70" rowspan="2">Adults</th>
                                                        <th width="70" rowspan="2">Total</th>
                                                    </tr>
                                                    <tr>
                                                        <th width="70">New</th>
                                                        <th width="70">Old</th>
                                                        <th width="70">Total</th>
                                                        <th width="70">New</th>
                                                        <th width="70">Old</th>
                                                        <th width="70">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    //set count
                                                    $count = 1;
                                                    //set grand
                                                    $grand = 0;
                                                    //set pre new total
                                                    $preNewTotal = 0;
                                                    //set pre old total
                                                    $preOldTotal = 0;
                                                    //set pre total
                                                    $preTotal = 0;
                                                    //set post New Total 
                                                    $postNewTotal = 0;
                                                    //set post Old Total 
                                                    $postOldTotal = 0;
                                                    //set post Total
                                                    $PostTotal = 0;
                                                    //set ailment Child Total 
                                                    $ailmentChildTotal = 0;
                                                    //set ailment Adult Total 
                                                    $ailmentAdultTotal = 0;
                                                    //set ailment Total 
                                                    $ailmentTotal = 0;
                                                    //set grand total
                                                    $grandTotal = 0;
                                                    //fetch result
                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                        $grand = $row['preTotal'] + $row['postTotal'] + $row['ailmentTotal'];
                                                        $preNewTotal += $row['preNew'];
                                                        $preOldTotal += $row['preOld'];
                                                        $preTotal += $row['preTotal'];
                                                        $postNewTotal += $row['postNew'];
                                                        $postOldTotal += $row['postOld'];
                                                        $PostTotal += $row['postTotal'];
                                                        $ailmentChildTotal += $row['ailmentChild'];
                                                        $ailmentAdultTotal += $row['ailmentAdult'];
                                                        $ailmentTotal += $row['ailmentTotal'];
                                                        $grandTotal += $grand;
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?php echo $count++; ?></td>
                                                            <td><?php echo $row['LocName']; ?></td>
                                                            <td class="right"><?php echo number_format($row['preNew']); ?></td>
                                                            <td class="right"><?php echo number_format($row['preOld']); ?></td>
                                                            <td class="right"><?php echo number_format($row['preTotal']); ?></td>
                                                            <td class="right"><?php echo number_format($row['postNew']); ?></td>
                                                            <td class="right"><?php echo number_format($row['postOld']); ?></td>
                                                            <td class="right"><?php echo number_format($row['postTotal']); ?></td>
                                                            <td class="right"><?php echo number_format($row['ailmentChild']); ?></td>
                                                            <td class="right"><?php echo number_format($row['ailmentAdult']); ?></td>
                                                            <td class="right"><?php echo number_format($row['ailmentTotal']); ?></td>
                                                            <td class="right"><b><?php echo number_format($grand); ?></b></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="right">Total</th>
                                                        <th class="right"><?php echo number_format($preNewTotal); ?></th>
                                                        <th class="right"><?php echo number_format($preOldTotal); ?></th>
                                                        <th class="right"><?php echo number_format($preTotal); ?></th>
                                                        <th class="right"><?php echo number_format($postNewTotal); ?></th>
                                                        <th class="right"><?php echo number_format($postOldTotal); ?></th>
                                                        <th class="right"><?php echo number_format($PostTotal); ?></th>
                                                        <th class="right"><?php echo number_format($ailmentChildTotal); ?></th>
                                                        <th class="right"><?php echo number_format($ailmentAdultTotal); ?></th>
                                                        <th class="right"><?php echo number_format($ailmentTotal); ?></th>
                                                        <th class="right"><?php echo number_format($grandTotal); ?></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                            <?php
                                        } else {
                                            ?>
                                            <table width="100%" id="myTable" cellspacing="0" align="center">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="3" width="50">Sr. No.</th>
                                                        <th rowspan="3">Name of District</th>
                                                        <th colspan="6">Mothercare (No. of cases)</th>
                                                        <th colspan="3">Children</th>
                                                        <th rowspan="3">General Ailment</th>
                                                        <th rowspan="3" width="100">Grand Total</th>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3">Ante-Natal</th>
                                                        <th colspan="3">Post-Natal</th>
                                                        <th width="70" rowspan="2">Children</th>
                                                        <th width="70" rowspan="2">Adults</th>
                                                        <th width="70" rowspan="2">Total</th>
                                                    </tr>
                                                    <tr>
                                                        <th width="70">New</th>
                                                        <th width="70">Old</th>
                                                        <th width="70">Total</th>
                                                        <th width="70">New</th>
                                                        <th width="70">Old</th>
                                                        <th width="70">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    //set count
                                                    $count = 1;
                                                    //set grand
                                                    $grand = 0;
                                                    //set pre new total
                                                    $preNewTotal = 0;
                                                    //set pre old total
                                                    $preOldTotal = 0;
                                                    //set pre total
                                                    $preTotal = 0;
                                                    //set post new total
                                                    $postNewTotal = 0;
                                                    //set post old total
                                                    $postOldTotal = 0;
                                                    //set post total
                                                    $PostTotal = 0;
                                                    //set ailment child total
                                                    $ailmentChildTotal = 0;
                                                    //set 
                                                    $ailmentAdultTotal = 0;
                                                    //set ailment total
                                                    $ailmentTotal = 0;
                                                    //set gen ailment total
                                                    $genAilmentTotal = 0;
                                                    //set grand total
                                                    $grandTotal = 0;
                                                    //fetch result
                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                        $grand = $row['preTotal'] + $row['postTotal'] + $row['ailmentTotal'] + $row['generalAilment'];
                                                        $preNewTotal += $row['preNew'];
                                                        $preOldTotal += $row['preOld'];
                                                        $preTotal += $row['preTotal'];
                                                        $postNewTotal += $row['postNew'];
                                                        $postOldTotal += $row['postOld'];
                                                        $PostTotal += $row['postTotal'];
                                                        $ailmentChildTotal += $row['ailmentChild'];
                                                        $ailmentAdultTotal += $row['ailmentAdult'];
                                                        $ailmentTotal += $row['ailmentTotal'];
                                                        $genAilmentTotal += $row['generalAilment'];
                                                        $grandTotal += $grand;
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?php echo $count++; ?></td>
                                                            <td><?php echo $row['LocName']; ?></td>
                                                            <td class="right"><?php echo number_format($row['preNew']); ?></td>
                                                            <td class="right"><?php echo number_format($row['preOld']); ?></td>
                                                            <td class="right"><?php echo number_format($row['preTotal']); ?></td>
                                                            <td class="right"><?php echo number_format($row['postNew']); ?></td>
                                                            <td class="right"><?php echo number_format($row['postOld']); ?></td>
                                                            <td class="right"><?php echo number_format($row['postTotal']); ?></td>
                                                            <td class="right"><?php echo number_format($row['ailmentChild']); ?></td>
                                                            <td class="right"><?php echo number_format($row['ailmentAdult']); ?></td>
                                                            <td class="right"><?php echo number_format($row['ailmentTotal']); ?></td>
                                                            <td class="right"><?php echo number_format($row['generalAilment']); ?></td>
                                                            <td class="right"><b><?php echo number_format($grand); ?></b></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="2" class="right">Total</th>
                                                        <th class="right"><?php echo number_format($preNewTotal); ?></th>
                                                        <th class="right"><?php echo number_format($preOldTotal); ?></th>
                                                        <th class="right"><?php echo number_format($preTotal); ?></th>
                                                        <th class="right"><?php echo number_format($postNewTotal); ?></th>
                                                        <th class="right"><?php echo number_format($postOldTotal); ?></th>
                                                        <th class="right"><?php echo number_format($PostTotal); ?></th>
                                                        <th class="right"><?php echo number_format($ailmentChildTotal); ?></th>
                                                        <th class="right"><?php echo number_format($ailmentAdultTotal); ?></th>
                                                        <th class="right"><?php echo number_format($ailmentTotal); ?></th>
                                                        <th class="right"><?php echo number_format($genAilmentTotal); ?></th>
                                                        <th class="right"><?php echo number_format($grandTotal); ?></th>
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
            unset($data, $issue, $itemName, $hfType, $totalOutlets, $product);
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