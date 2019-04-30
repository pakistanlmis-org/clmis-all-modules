<?php
/**
 * explorer
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
//check date
if (date('d') > 10) {
    //set date
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    //set date
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
//selected month
$sel_month = date('m', strtotime($date));
//selected year
$sel_year = date('Y', strtotime($date));
//selected stakeholder
$sel_stk = $sel_prov = $sel_dist = $sel_wh = $stkName = $provName = $distName = $whName = $where = $where1 = $where2 = $lvl = $whid = '';
//colspan
$colspan = $header = $header1 = $header2 = $lvl = $width = $colAlign = $colType = $xmlstore = '';
//if submitted
if (isset($_POST['submit'])) {
    //check report month
    if (!empty($_REQUEST['report_month'])) {
        //set selected month
        $sel_month = $_REQUEST['report_month'];
        //set where
        $where[] = "tbl_wh_data.report_month = " . $_POST['report_month'] . " ";
        //set where1
        $where1[] = "MONTH (tbl_hf_data.reporting_date) = " . $_POST['report_month'] . " ";
    }
    //check report year
    if (!empty($_REQUEST['report_year'])) {
        //set selected year
        $sel_year = $_REQUEST['report_year'];
        //set where
        $where[] = "tbl_wh_data.report_year = " . $_POST['report_year'] . " ";
        //set where1
        $where1[] = "YEAR (tbl_hf_data.reporting_date) = " . $_POST['report_year'] . " ";
    }
    //check selected stakeholder
    if (!empty($_REQUEST['stk_sel'])) {
        //set selected stakeholder
        $sel_stk = $_REQUEST['stk_sel'];
        //set where
        $where[] = "tbl_warehouse.stkid = " . $sel_stk . " ";
        //set where1
        $where1[] = "tbl_warehouse.stkid = " . $sel_stk . " ";
        //set where2
        $where2 .= " AND tbl_hf_type_rank.stakeholder_id = " . $sel_stk . " ";
    }
    //check province
    if (!empty($_REQUEST['province'])) {
        //set selected province
        $sel_prov = $_REQUEST['province'];
        //set where
        $where[] = "tbl_warehouse.prov_id = " . $sel_prov . " ";
        //set where1
        $where1[] = "tbl_warehouse.prov_id = " . $sel_prov . " ";
        //set where2
        $where2 .= " AND tbl_hf_type_rank.province_id = " . $sel_prov . " ";
    }
    //check district
    if (!empty($_REQUEST['district'])) {
        //get selected district
        $sel_dist = $_REQUEST['district'];
        //set where
        $where[] = "tbl_warehouse.dist_id = " . $_POST['district'] . " ";
        //set where1
        $where1[] = "tbl_warehouse.dist_id = " . $_POST['district'] . " ";
    }
    //check warehouse
    if (!empty($_REQUEST['warehouse'])) {
        //set selected warehouse
        $sel_wh = $_REQUEST['warehouse'];
        //set where
        $where[] = "tbl_warehouse.wh_id = " . $_POST['warehouse'] . " ";
        //set where1
        $where1[] = "tbl_warehouse.wh_id = " . $_POST['warehouse'] . " ";
    }
    //set where
    $where = implode(' AND ', $where);
    //set where1
    $where1 = implode(' AND ', $where1);

    include("xml_explorer.php");

    // Get Store name
    $getDist = mysql_fetch_array(mysql_query("SELECT
												tbl_warehouse.wh_name
											FROM
												tbl_warehouse
											WHERE
												tbl_warehouse.wh_id = '" . $sel_wh . "' "));
    $whName = $getDist['wh_name'];
    $whName = empty($whName) ? 'All' : $whName;

    // Get Stakeholder name
    $getStk = mysql_fetch_array(mysql_query("SELECT
												stakeholder.stkname
											FROM
												stakeholder
											WHERE
												stakeholder.stkid = '" . $sel_stk . "'"));
    $stkName = $getStk['stkname'];
    $stkName = empty($stkName) ? 'All' : $stkName;

    // Get District name
    $getDist = mysql_fetch_array(mysql_query("SELECT
												tbl_locations.LocName
											FROM
												tbl_locations
											WHERE
												tbl_locations.PkLocID = '" . $sel_dist . "'"));
    $distName = $getDist['LocName'];
    $distName = empty($distName) ? 'All' : $distName;

    // Get Province name
    $getProv = mysql_fetch_array(mysql_query("SELECT
												tbl_locations.LocName
											FROM
												tbl_locations
											WHERE
												tbl_locations.PkLocID = '" . $_POST['province'] . "'"));
    $provName = $getProv['LocName'];
    $provName = empty($provName) ? 'All' : $provName;
    ?>
    <link rel="STYLESHEET" type="text/css" href="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxgrid.css">
    <style>
        div.gridbox div.ftr td {
            background-color: #a6d785;
            font-style: normal;
            font-weight: bold;
            color: #179417;
        }
    </style>
    <script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxcommon.js"></script>
    <script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxgrid.js"></script>
    <script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/dhtmlxgridcell.js"></script>
    <script src='<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/client/dhtmlxgrid_export.js'></script>
    <script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/excells/dhtmlxgrid_excell_link.js"></script>
    <script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/ext/dhtmlxgrid_filter.js"></script>
    <script>
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<div style='text-align:center; font-size:14px; font-weight:bold; font-family:Helvetica'>Monthly Store/Facility Report for Stakeholder = <?php echo "'$stkName'"; ?> Province = <?php echo "'$provName'"; ?> District = <?php echo "'$distName'"; ?> and Store/Facility = '<?php echo $whName; ?>' <?php echo "(" . date('F', mktime(0, 0, 0, $sel_month)) . ' ' . $sel_year . ")"; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan<?php echo $colspan; ?>,#cspan");
            mygrid.attachHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Province name'>Province</span>,<span title='District name'>District</span>,<span title='Stakeholder'>Stakeholder</span>,<span title='Store/Facility name'>Store/Facility</span>,<span title='Product name'>Product</span>,<span title='Product name'>Category</span>,<span title='Opening Balance'>Opening Balance</span>,<span title='Balance received'>Received</span>,<span title='Balance issued'>Issued</span>,<div style='text-align:center;'>Adjustments</div>,#cspan,<span title='Closing balance'>Closing Balance</span><?php echo $header; ?>,<span title='Last Modified'>Last Modified</span>");
            mygrid.attachHeader("#rspan,#rspan,#rspan,#rspan,#rspan,#rspan,#rspan,#rspan,#rspan,#rspan,<div style='text-align:center;'>(+)</div>,<div style='text-align:center;'>(-)</div>,#rspan<?php echo $header1; ?>,#rspan");
            mygrid.attachHeader(",#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,,,,,,<?php echo $header2; ?>,");
    <?php if ($lvl == 7 && in_array($type, array(4, 5))) { ?>
                mygrid.attachFooter("<div><?php echo $xmlstore1; ?></div>,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan<?php echo $colspan; ?>,#cspan");
    <?php } ?>
            mygrid.setInitWidths("50,*,100,110,100,90,60,60,60,60,50,50,60<?php echo $width; ?>,120");
            mygrid.setColAlign("center,left,left,left,left,left,left,right,right,right,right,right,right<?php echo $colAlign; ?>,center");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro<?php echo $colType; ?>,ro");
            mygrid.enableRowsHover(true, 'onMouseOver');   // `onMouseOver` is the css class name.
            mygrid.setSkin("light");
            mygrid.enableMultiline(true);
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script>
    <?php
}
?>
<script>
    $(function() {
        showDistricts('<?php echo $sel_prov; ?>', '<?php echo $sel_stk; ?>');
        showStores('<?php echo $sel_dist; ?>');

        $('#province, #stk_sel').change(function(e) {
            $('#district').html('<option value="">All</option>');
            $('#warehouse').html('<option value="">Select</option>');
            showDistricts($('#province').val(), $('#stk_sel').val());
        });
        $('#stk_sel').change(function(e) {
            $('#warehouse').html('<option value="">All</option>');
        });

        $(document).on('change', '#province, #stk_sel, #district', function() {
            showStores($('#district option:selected').val());
        })
    })
    function showDistricts(prov, stk) {
        if (stk != '' && prov != '')
        {
            $.ajax({
                type: 'POST',
                url: 'my_report_ajax.php',
                data: {provId: prov, stkId: stk, distId: '<?php echo $sel_dist; ?>', showAll: 1},
                success: function(data) {
                    $("#districts").html(data);
                    $('#district').select2();
                    $('#district').removeClass('form-control').addClass('select2me');
                    $('#district').removeClass('input-sm').addClass('input-medium');
                }
            });
        }
    }
    function showStores(dist) {
        var stk = $('#stk_sel').val();
        if (stk != '' && dist != '')
        {
            $.ajax({
                type: 'POST',
                url: 'my_report_ajax.php',
                data: {distId: dist, stkId: stk, whId: '<?php echo $sel_wh; ?>'},
                success: function(data) {
                    $("#stores").html(data);
                    $('#warehouse').select2();
                    $('#warehouse').removeClass('form-control').addClass('select2me');
                    $('#warehouse').removeClass('input-sm').addClass('input-medium');
                }
            });
        }
    }
</script>

    <link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">

    <!-- BEGIN HEADER -->
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
                        <h3 class="page-title row-br-b-wp">View Monthly Store/Facility Report</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <table width="99%">
                                    <tr>
                                        <td><form action="" method="post">
                                                <table>
                                                    <tr>
                                                        <td class="col-md-2"><label class="control-label">Month</label>
                                                            <SELECT NAME="report_month" id="report_month" class="form-control input-sm" TABINDEX="3">
                                                                <?php
                                                                for ($i = 1; $i <= 12; $i++) {
                                                                    if ($sel_month == $i) {
                                                                        $sel = "selected='selected'";
                                                                    } elseif ($i == 1) {
                                                                        $sel = "selected='selected'";
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </SELECT></td>
                                                        <td class="col-md-2"><label class="control-label">Year</label>
                                                            <select name="report_year" id="report_year" class="form-control input-sm" tabindex="2">
                                                                <?php
                                                                //end year
                                                                $EndYear = 2010;
                                                                //start year
                                                                $StartYear = date('Y');
                                                                for ($i = $StartYear; $i >= $EndYear; $i--) {
                                                                    if ($i == $sel_year) {
                                                                        $chk4 = "Selected = 'Selected'";
                                                                    } else {
                                                                        $chk4 = "";
                                                                    }
                                                                    echo"<OPTION VALUE='$i' $chk4>$i</OPTION>";
                                                                }
                                                                ?>
                                                            </select></td>
                                                        <td class="col-md-2"><label class="control-label">Stakeholder</label>
                                                            <select name="stk_sel" id="stk_sel" class="form-control input-sm">
                                                                <?php
                                                                //select query
                                                                //gets
                                                                //stakeholder id
                                                                //stakeholder name
                                                                $querystk = "SELECT DISTINCT
																				stakeholder.stkid,
																				stakeholder.stkname
																			FROM
																				tbl_warehouse
																			INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
																			INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
																			WHERE
																				stakeholder.stk_type_id IN (0, 1)
																			ORDER BY
																				stakeholder.stkorder ASC";
                                                                //query result
                                                                $rsstk = mysql_query($querystk) or die();
                                                                //fetch result
                                                                while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                    ?>
                                                                    <option value="<?php echo $rowstk['stkid']; ?>" <?php echo ($sel_stk == $rowstk['stkid']) ? 'selected=selected' : '' ?>><?php echo $rowstk['stkname']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select></td>
                                                        <td class="col-md-2"><?php
                                                            //select query
                                                            //gets
                                                            //pk location id
                                                            //location name
                                                            $qry = "SELECT
																			tbl_locations.PkLocID,
																			tbl_locations.LocName
																		FROM
																			tbl_locations
																		WHERE
																			tbl_locations.LocLvl = 2
																		AND tbl_locations.ParentID IS NOT NULL";
                                                            //query result
                                                            $qryRes = mysql_query($qry);
                                                            ?>
                                                            <label class="control-label">Province/Region</label>
                                                            <select name="province" id="province"   style="width:250px" class="   input-medium select2me" data-placeholder="Select..." required="required">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //fetch result
                                                                while ($row = mysql_fetch_array($qryRes)) {
                                                                    ?>
                                                                    <option value="<?php echo $row['PkLocID']; ?>" <?php echo ($sel_prov == $row['PkLocID']) ? 'selected=selected' : '' ?>><?php echo $row['LocName']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select></td>
                                                        <td id="districts" class="col-md-2"><label class="control-label">District</label>
                                                            <select name="district" id="district"  style="width:150px"  class="   input-medium  select2me" data-placeholder="Select..." >
                                                                <option value="">Select</option>
                                                            </select></td>
                                                        <td id="stores" class="col-md-2"><label class="control-label">Store/Facility</label>
                                                            <select name="warehouse" id="warehouse" style="width:150px"  class=" input-sm select2me">
                                                                <option value="">Select</option>
                                                            </select></td>
                                                        <td class="col-md-2"><input type="submit" value="Go" name="submit" class="btn btn-primary input-sm" style="margin-top:28px;" /></td>
                                                    </tr>
                                                </table>
                                            </form></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (isset($_POST['submit'])) {
                            if ($numOfRows > 0) {
                                ?>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="right" style="padding-right:5px;">
                                            <img title="Click here to export data to PDF file" style="cursor:pointer;" src="<?php echo PUBLIC_URL ?>images/pdf-32.png" onClick="mygrid.toPDF('<?php echo PUBLIC_URL ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');"/>
                                            <img title="Click here to export data to Excel file" style="cursor:pointer;" src="<?php echo PUBLIC_URL ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><div id="mygrid_container" style="width:100%; height:410px; background-color:white;overflow:hidden"></div></td>
                                    </tr>
                                </table>
                                <?php
                            } else {
                                $qqry = "SELECT * FROM `tbl_warehouse` WHERE `wh_id`='" . $whid . "'";
                                $rez = mysql_query($qqry);

                                $disMonth = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                                $tempVar = $_POST['report_month'] - 1;
                                ?>
                                <!--<script type="text/javascript">fetchDistricts();</script>-->
                                <div style="font-size:12px; font-weight:bold; color:#F00; text-align:left">
                                    <?php
                                    if (mysql_num_rows($rez) > 0) {
                                        $qryRes = mysql_fetch_array($rez);
                                        echo "No data entered for $qryRes[wh_name]($qryRes[wh_type_id]) in $disMonth[$tempVar], $_POST[report_year].";
                                    } else {
                                        echo "No data entered in $disMonth[$tempVar], $_POST[report_year].";
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- END FOOTER -->
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>
</body>
<!-- END BODY -->
</html>