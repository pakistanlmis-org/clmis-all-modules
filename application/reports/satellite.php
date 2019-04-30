<?php
/**
 * satelite
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
$rptId = 'satellite';
//campaign id
$campId = '';
//district Id 
$districtId = '';
//if submitted
if (isset($_POST['submit'])) {
    //get selected month
    $selMonth = mysql_real_escape_string($_POST['month_sel']);
    //get selected year
    $selYear = mysql_real_escape_string($_POST['year_sel']);
    //get selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //get district Id 
    $districtId = mysql_real_escape_string($_POST['district']);
    //get campaign id
    $campId = mysql_real_escape_string($_POST['camps']);
    //get reporting date 
    $reportingDate = mysql_real_escape_string($_POST['year_sel']) . '-' . $selMonth . '-01';
    //set stakeholder 
    $stakeholder = 1;
    //check campaign id
    if ($campId == 'all') {
        //campaign filter
        $campFilter = "";
        //title
        $title = 'FWC & MSU';
    } else {
        if ($campId == 'msu') {
            //set title	
            $title = 'MSU';
            //set campaign filter
            $campFilter = " AND tbl_warehouse.hf_type_id = 2";
        } else if ($campId == 'fwc') {
            //set title
            $title = 'FWC';
            //set campaign filter
            $campFilter = " AND tbl_warehouse.hf_type_id = 1";
        } else {
            //set title
            $title = 'FWC & MSU';
            //set campaign filter
            $campFilter = " AND tbl_warehouse.wh_id = $campId";
        }
    }
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
    //set distrct name 
    $distrctName = $row['LocName'];
    //set file name
    $fileName = 'Satellite' . $distrctName . '_for_' . date('M-Y', strtotime($reportingDate));
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
                        <h3 class="page-title row-br-b-wp">Monthly Performance Report of Satellite Camps</h3>
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
                    //itminfo_tab.itm_id,
                    //item name,
                    //performance,
                    //prenatal,
                    //postnatal,
                    //children,
                    //adults,
                    $qry = "SELECT
								itminfo_tab.itm_id,
								itminfo_tab.itm_name,
								SUM(tbl_hf_satellite_data.issue_balance) AS performance,
								SUM(tbl_hf_satellite_mother_care.pre_natal_new + tbl_hf_satellite_mother_care.pre_natal_old) AS prenatal,
								SUM(tbl_hf_satellite_mother_care.post_natal_new + tbl_hf_satellite_mother_care.post_natal_old) AS postnatal,
								SUM(tbl_hf_satellite_mother_care.ailment_children) AS children,
								SUM(tbl_hf_satellite_mother_care.ailment_adults) AS adults,
								(
									SELECT
										COUNT(tbl_warehouse.wh_id)
									FROM
										tbl_warehouse
									/*INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id*/
									WHERE
										tbl_warehouse.dist_id = $districtId
									AND tbl_warehouse.stkid = $stakeholder
									AND tbl_warehouse.stkofficeid = 96
									AND tbl_warehouse.hf_type_id IN (1, 2)
									$campFilter
								) AS camps_target,
								COUNT(DISTINCT tbl_hf_satellite_data.warehouse_id) AS camps_held
							FROM
								tbl_warehouse
							INNER JOIN tbl_hf_satellite_data ON tbl_warehouse.wh_id = tbl_hf_satellite_data.warehouse_id
							INNER JOIN tbl_hf_satellite_mother_care ON tbl_warehouse.wh_id = tbl_hf_satellite_mother_care.warehouse_id
							INNER JOIN itminfo_tab ON tbl_hf_satellite_data.item_id = itminfo_tab.itm_id
							WHERE
								tbl_hf_satellite_mother_care.reporting_date = '$reportingDate'
							AND tbl_hf_satellite_data.reporting_date = '$reportingDate'
							AND tbl_warehouse.dist_id = $districtId
							AND itminfo_tab.itm_category = 1
							$campFilter
							GROUP BY
								tbl_hf_satellite_data.item_id
							ORDER BY
								itminfo_tab.frmindex ASC";
                    //query result
                    //echo $qry;exit;
                    $qryRes = mysql_query($qry);
                    //if result exists
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        //fetch results
                        while ($row = mysql_fetch_array($qryRes)) {
                            //camps target 
                            $campsTarget = $row['camps_target'];
                            //camps held 
                            $campsHeld = $row['camps_held'];
                            //prenatal 
                            $prenatal = $row['prenatal'];
                            //postnatal 
                            $postnatal = $row['postnatal'];
                            //children 
                            $children = $row['children'];
                            //adults
                            $adults = $row['adults'];
                            //contraceptives
                            $contraceptives[$row['itm_name']] = $row['performance'];
                        }
                        ?>
                        <?php include('sub_dist_reports.php'); ?>
                        <div class="col-md-12" style="overflow:auto;">
                            <table width="100%" align="center">
                                <tr>
                                    <td align="center">
                                        <h4 class="center">
                                            Monthly Performance Report of Satellite Camps of <?php echo $title; ?><br>
                                            For the month of <?php echo date('M', mktime(0, 0, 0, $selMonth, 1)) . '-' . $selYear . ', District ' . $distrctName; ?>
                                        </h4>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="80%" cellspacing="0" align="center" style="margin-top:30px;">
                                            <tr>
                                                <td>
                                                    <table width="35%" align="left" id="myTable">
                                                        <tr>
                                                            <td>Camps Target</td>
                                                            <td class="center"><?php echo $campsTarget; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Camps Held</td>
                                                            <td class="center"><?php echo $campsHeld; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr><td>&nbsp;</td></tr>
                                            <tr>
                                                <td><h4>Performance</h4></td>
                                            </tr>
                                            <tr><td>&nbsp;</td></tr>
                                            <tr>
                                                <td style="padding-left:10%;">
                                                    <h4>1- Contraceptives</h4>
                                                </td>
                                            </tr>
                                            <tr><td>&nbsp;</td></tr>
                                            <tr>
                                                <td style="padding-left:20%;">
                                                    <table width="50%" align="left" id="myTable">
                                                        <?php
                                                        foreach ($contraceptives as $item => $performance) {
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $item ?></td>
                                                                <td class="center"><?php echo number_format($performance); ?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>   
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr><td>&nbsp;</td></tr>
                                            <tr>
                                                <td style="padding-left:10%;">
                                                    <h4>2- MCH Cases Attended</h4>
                                                </td>
                                            </tr>
                                            <tr><td>&nbsp;</td></tr>
                                            <tr>
                                                <td style="padding-left:20%;">
                                                    <table width="50%" align="left" id="myTable">
                                                        <tr>
                                                            <td>Ante-natal</td>
                                                            <td class="center"><?php echo $prenatal; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Post-natal</td>
                                                            <td class="center"><?php echo $postnatal; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr><td>&nbsp;</td></tr>
                                            <tr>
                                                <td style="padding-left:10%;">
                                                    <h4>3- No. of Patient Provided Medicines for Minor Ailments</h4>
                                                </td>
                                            </tr>
                                            <tr><td>&nbsp;</td></tr>
                                            <tr>
                                                <td style="padding-left:20%;">
                                                    <table width="50%" align="left" id="myTable">
                                                        <tr>
                                                            <td>Adults</td>
                                                            <td class="center"><?php echo $adults; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Children</td>
                                                            <td class="center"><?php echo $children; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
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
<?php include PUBLIC_PATH . "/html/footer.php"; ?>
<script>
    $(function() {
        showDistricts();
        $('#prov_sel').change(function(e) {
            $('#district').html('<option value="">Select</option>');
            $('#camps').html('<option value="">Select</option>');
            showDistricts();
        });
        $(document).on('change', '#district', function() {
            $('#camps').html('<option value="">Select</option>');
            showCamps();
        });
    })
    function showDistricts()
    {
        var provinceId = $('#prov_sel').val();
        if (provinceId != '')
        {
            $.ajax({
                url: 'ajax_calls.php',
                data: {provinceId: provinceId, dId: '<?php echo $districtId; ?>'},
                type: 'POST',
                success: function(data)
                {
                    $('#districtDiv').html(data);
                    showCamps();
                }
            })
        }
    }
    function showCamps()
    {
        var districtId = $('#district').val();
        var provinceId = $('#prov_sel').val();
        if (districtId != '')
        {
            $.ajax({
                url: 'ajax_calls.php',
                data: {provId: provinceId, districtId: districtId, campId: '<?php echo $campId; ?>'},
                type: 'POST',
                success: function(data)
                {
                    $('#camps').html(data);
                }
            })
        }
    }
</script>
</body>
</html>