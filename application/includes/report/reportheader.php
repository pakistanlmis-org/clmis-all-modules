<?php
/**
 * reportfooter
 * @package includes/reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
?>

<!--  BEGIN: REPORT Header -->
<?php
//check selected month
if (!empty($sel_month)) {
    //set report month
    $reportMonth = date('F', mktime(0, 0, 0, $sel_month));
} else {
    //set report month
    $reportMonth = "";
}
//check districts
if (isset($_REQUEST['districts'])) {
    //set districts
    $_SESSION['districts'] = $_POST['districts'];
}
?>
<script language="JavaScript" src="<?php
//iclude gen_validatorv31.js
echo PUBLIC_URL;
?>js/gen_validatorv31.js" type="text/javascript"></script>
<form name="searchfrm" id="searchfrm" action="<?php $actionpage ?>" method="post">
    <div class="row">
        <div class="col-md-12">
            <h3 class="page-title row-br-b-wp"> <?php echo $report_title; ?> <span class="green-clr-txt"><?php
                    //show report month       
                    echo $reportMonth;
                    //show selected year
                    echo ' ' . $sel_year;
                    ?></span> </h3>
            <div style="display: block;" id="alert-message" class="alert alert-info text-message"><?php echo stripslashes(getReportDescription($report_id)); ?></div>
            <?php
            // Do not include summary section for non reporting districts reports and availability reate reports
            if (!in_array($report_id, array('STOCKOUTRPT', 'SNASUMSTOCKLOC', 'SNONREPDIST', 'CENTRALWAREHOUSE', 'PROVINCIALWAREHOUSE', 'SNASUMSTK', 'TRACK20'))) {
                //include ratesummary
                include('ratesummary.php');
            }
            ?>
        </div>
    </div>

    <div class="widget" data-toggle="collapse-widget">
        <div class="widget-head">
            <h3 class="heading">Filter by</h3>
        </div>
        <div class="widget-body">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tbody>
                    <tr bgcolor="#FFFFFF">
                        <td colspan="2" style="font-family: Arial, Verdana, Helvetica, sans-serif;font-size: 12px;"><?php echo stripslashes(getReportStockDescription($report_id)); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" bgcolor="#FFFFFF">
                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr> 

                                    <!-- TimePeriod -->
                                    <?php
                                    //initialize flag for Select and All options in the drop down
                                    //posT11
                                    $posT11 = False;
                                    //posT10
                                    $posT10 = False;
                                    //posT01
                                    $posT01 = False;
                                    //paramTsel
                                    $paramTsel = False;
                                    //paramTall
                                    $paramTall = False;
//posI11
                                    $posI11 = False;
                                    //posI10
                                    $posI10 = False;
                                    //posI01
                                    $posI01 = False;
                                    //paramIsel
                                    $paramIsel = False;
                                    //paramIall
                                    $paramIall = False;
                                    //posS11 
                                    $posS11 = False;
                                    //posS10 
                                    $posS10 = False;
                                    //posS01 
                                    $posS01 = False;
                                    //paramSsel 
                                    $paramSsel = False;
                                    //paramSall 
                                    $paramSall = False;

                                    //posP11 
                                    $posP11 = False;
                                    //posP10 
                                    $posP10 = False;
                                    //posP01 
                                    $posP01 = False;
                                    //paramPsel 
                                    $paramPsel = False;
                                    //paramPall 
                                    $paramPall = False;
                                    //set pos
                                    $pos = strrpos($parameters, "T");
                                    //check po
                                    if ($pos !== FALSE) {
                                        //set posT11 
                                        $posT11 = strpos($parameters, "T11");
                                        //set posT10 
                                        $posT10 = strpos($parameters, "T10");
                                        //set posT01 
                                        $posT01 = strpos($parameters, "T01");
                                        //check posT11 
                                        if ($posT11 !== FALSE) {
                                            //set paramTsel
                                            $paramTsel = 1;
                                            //set paramTall
                                            $paramTall = 1;
                                        }
                                        //check posT10
                                        if ($posT10 !== FALSE) {
                                            //set paramTsel
                                            $paramTsel = 1;
                                            //set paramTall
                                            $paramTall = 0;
                                        }
                                        //check posT01
                                        if ($posT01 !== FALSE) {
                                            //set paramTsel
                                            $paramTsel = 0;
                                            //set paramTall
                                            $paramTall = 1;
                                        }
                                        ?>
                                        <?php
                                        //check report id
                                        if ($report_id !== "QTRREPORT") {
                                            ?>
                                            <td class="col-md-2"><label class="control-label"><?= (($report_id == "CENTRALWAREHOUSE" || $report_id == "PROVINCIALWAREHOUSE") ? 'Ending ' : '') ?>Month</label>
                                                <select name="month_sel" id="month_sel" class="form-control input-sm">
                                                    <?php
                                                    //check paramTsel
                                                    if ($paramTsel == 1) {
                                                        ?>
                                                        <option value="">Select</option>
                                                    <?php }
                                                    ?>
                                                    <?php
                                                    //check paramTall
                                                    if ($paramTall == 1) {
                                                        ?>
                                                        <option value="all">All</option>
                                                    <?php }
                                                    ?>
                                                    <?php
                                                    for ($i = 1; $i <= 12; $i++) {
                                                        //check selected month
                                                        if ($sel_month == $i) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            if ($i == 1) {
                                                                $sel = "selected='selected'";
                                                            } else {
                                                                $sel = "";
                                                            }
                                                        }
                                                        //populate month_sel combo
                                                        ?>
                                                        <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            <?php }
                                            ?></td>
                                        <td class="col-md-2"><label class="control-label">Year</label>
                                            <select name="year_sel" id="year_sel" class="form-control input-sm">
                                                <?php
                                                //check paramTsel
                                                if ($paramTsel == 1) {
                                                    ?>
                                                    <option value="">Select</option>
                                                <?php }
                                                ?>
                                                <?php
                                                //populate year_sel combo
                                                if ($paramTall == 1) {
                                                    ?>
                                                    <option value="all">All</option>
                                                <?php }
                                                ?>
                                                <?php
                                                for ($j = date('Y'); $j >= 2010; $j--) {
                                                    //check selected year
                                                    if ($sel_year == $j) {
                                                        $sel = "selected='selected'";
                                                    } else {
                                                        if ($j == date("Y")) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }
                                                    }
                                                    ?>
                                                    <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select></td>
                                        <?php
                                    }
//check report id
                                    if ($report_id == "STOCKOUTRPT") {
                                        ?>
                                        <td class="col-md-2"><label class="control-label">Year</label>
                                            <select name="year_sel" id="year_sel" class="form-control input-sm">
                                                <?php
                                                //check paramTsel
                                                if ($paramTsel == 1) {
                                                    ?>
                                                    <option value="">Select</option>
                                                <?php }
                                                ?>
                                                <?php
                                                //populate year_sel
                                                //check paramTall
                                                if ($paramTall == 1) {
                                                    ?>
                                                    <option value="all">All</option>
                                                <?php }
                                                ?>
                                                <?php
                                                for ($j = date('Y'); $j >= 2010; $j--) {
                                                    if ($sel_year == $j) {
                                                        $sel = "selected='selected'";
                                                    } else {
                                                        if ($j == date("Y")) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }
                                                    }
                                                    ?>
                                                    <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select></td>
                                        <?php
                                    }
                                    //check report id
                                    if ($report_id == "STOCKOUTRPT") {
                                        ?>
                                        <td class="col-md-2"><label class="control-label">MOS</label>
                                            <input type="text" name="in_MOS" id="in_MOS" class="form-control input-sm" style="width:60px" onblur="frmvalidate();"/></td>
                                        <?php
                                    }

                                    if (in_array($report_id, array("SNASUM", "STOCKOUTRPT", "SPROVINCEREPORT", "SDISTRICTREPORT", "PROVINCIALWAREHOUSE", "SNASUM", "SNONREPDIST"))) {
                                        //|| $report_id == "SDISTRICTREPORT"
                                        ?>
                                        <td class="col-md-2"><label class="control-label">Sector</label>
                                            <?php
                                            $selected = "";
                                            //check sector
                                            if (isset($_POST['sector'])) {
                                                if ($_POST['sector'] == 'public') {
                                                    //set selected
                                                    $selected = 'public';
                                                }
                                                if ($_POST['sector'] == 'private') {
                                                    //set selected
                                                    $selected = 'private';
                                                }
                                            } else {
                                                if ($lvl_stktype == '0') {
                                                    //set selected
                                                    $selected = 'public';
                                                }

                                                if ($lvl_stktype == '1') {
                                                    //set selected
                                                    $selected = 'private';
                                                }
                                            }
                                            ?>
                                            <select name="sector" id="sector" class="form-control input-sm">
                                                <?php
                                                if (in_array($report_id, array("SPROVINCEREPORT", "SDISTRICTREPORT", "PROVINCIALWAREHOUSE", "SNASUM", "SNONREPDIST"))) {
                                                    ?>
                                                    <option value="all" <?php echo ($selected == 'all') ? 'selected=selected' : ''; ?>>All</option>
                                                <?php }
                                                ?>
                                                <option value="public" <?php echo ($selected == 'public') ? 'selected=selected' : ''; ?>>Public</option>
                                                <option value="private" <?php echo ($selected == 'private') ? 'selected=selected' : ''; ?>>Private</option>
                                            </select></td>
                                        <?php
                                    }
                                    ?>
                                    <?php
//report id 
                                    if ($report_id == "QTRREPORT") {
                                        ?>
                                        <td class="col-md-2"><label class="control-label">Qtr</label>
                                            <select name="qtr_sel" id="qtr_sel" class="form-control input-sm">
                                                <option value="1" <?php echo ($_POST['qtr_sel'] == 1) ? 'selected="selected"' : ''; ?>>1</option>
                                                <option value="2" <?php echo ($_POST['qtr_sel'] == 2) ? 'selected="selected"' : ''; ?>>2</option>
                                                <option value="3" <?php echo ($_POST['qtr_sel'] == 3) ? 'selected="selected"' : ''; ?>>3</option>
                                                <option value="4" <?php echo ($_POST['qtr_sel'] == 4) ? 'selected="selected"' : ''; ?>>4</option>
                                            </select></td>
                                    <?php } ?>

                                    <!-- Stakeholder -->
                                    <?php
                                    //set stakeholder filter
                                    $stkFilter = '';
                                    $pos = strrpos($parameters, "S");
                                    //check pos
                                    if ($pos !== FALSE) {
                                        //set posS11
                                        $posS11 = strpos($parameters, "S11");
                                        //set posS10
                                        $posS10 = strpos($parameters, "S10");
                                        //set posS01
                                        $posS01 = strpos($parameters, "S01");
                                        //set posS00
                                        $posS00 = strpos($parameters, "SPI");
                                        //check posS11 
                                        if ($posS11 !== FALSE) {
                                            //set paramSsel 
                                            $paramSsel = 1;
                                            //set paramSall
                                            $paramSall = 1;
                                        }
                                        if ($posS10 !== FALSE) {
                                            //set paramSsel
                                            $paramSsel = 1;
                                            //set paramSall
                                            $paramSall = 0;
                                        }
                                        if ($posS01 !== FALSE) {
                                            //set paramSsel
                                            $paramSsel = 0;
                                            //set paramSall
                                            $paramSall = 1;
                                        }
                                        if ($posS00 !== FALSE) {
                                            //set paramSsel
                                            $paramSsel = 0;
                                            //set paramSall
                                            $paramSall = 1;
                                            //set stakeholder filter
                                            $stkFilter = " AND stk_type_id = 1";
                                        }
                                        //set display
                                        $display = ( $report_id == 'SNASUM' ) ? 'style="display:none;"' : '';
                                        ?>
                                                                            <!-- <td class="sb1NormalFont" bgcolor="#FFFFFF">Stakeholder:</td>-->
                                        <td class="col-md-2" <?php echo $display; ?>><label class="control-label">Stakeholder</label>
                                            <?php
                                            if ($report_id !== "CENTRALWAREHOUSE") {
                                                $sel_stk = isset($proStkID) ? $proStkID : $sel_stk;
                                                ?>
                                                <select name="stk_sel" id="stk_sel" class="form-control input-sm">
                                                    <?php
                                                } else {
                                                    echo '<select name="stk_sel" id="stk_sel" class="form-control input-sm" onChange="func()">';
                                                }
                                                if ($paramSsel == 1) {
                                                    ?>
                                                    <option value="">Select</option>
                                                <?php }
                                                ?>
                                                <?php if ($paramSall == 1 || $stakeHolder == 1) { ?>
                                                    <option value="all">All</option>
                                                <?php }
                                                ?>
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
															INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
															INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
															WHERE
																stakeholder.stk_type_id IN (0, 1)
															AND tbl_warehouse.is_active = 1
															ORDER BY
																stakeholder.stk_type_id ASC,
																stakeholder.stkorder ASC";
                                                //query result
                                                $rsstk = mysql_query($querystk) or die();
                                                //fetch result
                                                while ($rowstk = mysql_fetch_array($rsstk)) {
                                                    //check seleted stakeholder
                                                    if ($sel_stk == $rowstk['stkid']) {
                                                        $sel = "selected='selected'";
                                                    } else {
                                                        $sel = "";
                                                    }

                                                    if ($report_id == "CENTRALWAREHOUSE" && $rowstk['stkname'] == 'PWD') {
                                                        //set stakeholder name
                                                        $stkName = 'PPW/CWH';
                                                    } else {
                                                        //set stakeholder name
                                                        $stkName = $rowstk['stkname'];
                                                    }
                                                    ?>
                                                    <option value="<?php echo $rowstk['stkid']; ?>" <?php echo $sel; ?>><?php echo $stkName; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select></td>
                                        <?php
                                    }
                                    ?>

                                    <!-- Province -->
                                    <?php
                                    $pos = strrpos($parameters, "P");
                                    if ($pos !== FALSE) {
                                        // note: three equal signs
                                        // add product element

                                        $posP11 = strpos($parameters, "P11");
                                        $posP10 = strpos($parameters, "P10");
                                        $posP01 = strpos($parameters, "P01");

                                        if ($posP11 !== FALSE) {
                                            $paramPsel = 1;
                                            $paramPall = 1;
                                        }
                                        if ($posP10 !== FALSE) {
                                            $paramPsel = 1;
                                            $paramPall = 0;
                                        }
                                        if ($posP01 !== FALSE) {
                                            $paramPsel = 0;
                                            $paramPall = 1;
                                        }
                                        ?>
                                                                            <!-- <td class="sb1NormalFont" bgcolor="#FFFFFF">Province/Region:</td>-->
                                        <td class="col-md-2"><label class="control-label">Province/Region</label>
                                            <select name="prov_sel" id="prov_sel" class="form-control input-sm">

                                                <?php if ($paramPsel == 1) { ?>
                                                    <option value="">Select</option>
                                                <?php }
                                                ?>
                                                <option value="all">All</option>
                                                <?php
                                                //select query
                                                //gets
                                                //province id
                                                //province title
                                                $filter = (!empty($sel_stk) && $sel_stk != 'all') ? " AND tbl_warehouse.stkid = $sel_stk" : "";
                                                $queryprov = "SELECT DISTINCT
																tbl_locations.PkLocID,
																tbl_locations.LocName
															FROM
																tbl_locations
															INNER JOIN tbl_warehouse ON tbl_locations.PkLocID = tbl_warehouse.prov_id
															INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
															WHERE
																tbl_locations.ParentID IS NOT NULL
															AND tbl_locations.LocLvl = 2
															AND tbl_warehouse.is_active = 1
															$filter";
                                                //query result
                                                $rsprov = mysql_query($queryprov) or die();
                                                while ($rowprov = mysql_fetch_array($rsprov)) {

                                                    if ($sel_prov == $rowprov['PkLocID']) {
                                                        $sel = "selected='selected'";
                                                    } else {
                                                        $sel = "";
                                                    }
                                                    ?>
                                                    <option value="<?php echo $rowprov['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowprov['LocName']; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select></td>
                                        <!--<td id="stkheading" style="display:none" class="sb1NormalFont" bgcolor="#FFFFFF">Stakeholder..:</td>--> 
                                        <!--<div class="sb1NormalFont">Stakeholder:</div><br>-->
                                        <td id="whID" style="display:none;" class="col-md-2"></td>
                                        <?php
                                        if ($report_id == 'SNONREPDIST' || $report_id == 'TRACK20') {
                                            ?>
                                            <td id="districtsCol" class="col-md-2"><label class="control-label">District</label>
                                                <select name="dist_id" id="dist_id" class="form-control input-sm">
                                                    <option value=''>Select Province First</option>
                                                </select></td>
                                            <?php
                                        }
                                        if ($report_id == 'TRACK20') {
                                            ?>
                                </tr><tr>
                                            <td id="sdp_catCol" class="col-md-2"><label class="control-label">SDP Category</label>
                                                <select name="sdp_cat" id="sdp_cat" class="form-control input-sm">
                                                    <option value='1' <?php if ($sel_report_type == 1) { ?>selected=""<?php } ?>>Primary</option>
                                                    <option value='2' <?php if ($sel_report_type == 2) { ?>selected=""<?php } ?>>Secondary/Tertiary</option>
                                                </select></td>
                                            <td id="rpt_by" class="col-md-2"><label class="control-label">Report By</label>
                                                <select name="report_by" id="report_by" class="form-control input-sm">
                                                    <option value='1' <?php if ($sel_report_by == 1) { ?>selected=""<?php } ?>>Stock availability</option>
                                                    <option value='2' <?php if ($sel_report_by == 2) { ?>selected=""<?php } ?>>Stock out</option>
                                                </select></td>
                                            <?php
                                        }

                                        if ($report_id == 'SNONREPDIST' || $report_id == 'QTRREPORT') {
                                            if ($report_id == 'SNONREPDIST') {
                                                echo '</tr><tr>';
                                            }
                                            ?>
                                            <td class="col-md-2">
                                                <label class="control-label">Type</label>
                                                <select name="lvl_type" id="lvl_type" class="form-control input-sm">
                                                    <option value='all'>All</option>
                                                    <?php
                                                    if ($report_id == 'SNONREPDIST') {
                                                        $sel = (isset($_REQUEST['lvl_type']) && $_REQUEST['lvl_type'] == 'df') ? 'selected="selected"' : '';
                                                        echo "<option value='df' $sel>District and Field</option>";
                                                        //select query
                                                        //gets
                                                        //district level
                                                        //level id
                                                        //level name
                                                        $getDistrLvl = mysql_query("SELECT tbl_dist_levels.lvl_id, tbl_dist_levels.lvl_name 
                                                                                        FROM 
                                                                                            tbl_dist_levels 
                                                                                        WHERE
                                                                                            tbl_dist_levels.lvl_id IN (3, 4, 7)");
                                                    }
                                                    if ($report_id == 'QTRREPORT') {
                                                        //select query
                                                        //gets
                                                        //district level
                                                        //level id
                                                        //level name
                                                        $getDistrLvl = mysql_query("SELECT tbl_dist_levels.lvl_id, tbl_dist_levels.lvl_name 
                                                                                        FROM 
                                                                                            tbl_dist_levels 
                                                                                        WHERE
                                                                                            tbl_dist_levels.lvl_id IN(2,3)");
                                                    }
                                                    //fetch results
                                                    while ($distrLvlRow = mysql_fetch_array($getDistrLvl)) {
                                                        if ((isset($_REQUEST['lvl_type']) && $_REQUEST['lvl_type'] == $distrLvlRow['lvl_id']) || $sel_lvl_type == $distrLvlRow['lvl_id']) {
                                                            $sel = "selected='selected'";
                                                        } else {
                                                            $sel = "";
                                                        }
                                                        ?>
                                                        <option value="<?php echo $distrLvlRow['lvl_id']; ?>" <?php echo $sel; ?>><?php echo $distrLvlRow['lvl_name']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </td>

                                            <td class="col-md-2">
                                                <label class="control-label">Report Type</label>
                                                <select name="rptType" id="rptType" class="form-control input-sm">
                                                    <option value="non-reported" <?php echo ($_POST['rptType'] == 'non-reported') ? 'selected' : ''; ?>>Non-reported</option>
                                                    <option value="reported" <?php echo ($_POST['rptType'] == 'reported') ? 'selected' : ''; ?>>Reported - All</option>
                                                    <option value="ontime" <?php echo ($_POST['rptType'] == 'ontime') ? 'selected' : ''; ?>>Reported - On-time</option>
                                                    <option value="late" <?php echo ($_POST['rptType'] == 'late') ? 'selected' : ''; ?>>Reported - Late</option>
                                                </select>
                                            </td>

                                            <td id="districts_td_id" class="col-md-2"></td>

                                            <?php
                                        }
                                        ?>
                                        <?php
                                    }
                                    ?>

                                    <!-- Product -->
                                    <?php
                                    $pos = strrpos($parameters, "I");
                                    if ($pos !== FALSE) {
                                        // note: three equal signs
                                        // add province element

                                        $posI12 = strpos($parameters, "I12");
                                        $posI11 = strpos($parameters, "I11");
                                        $posI10 = strpos($parameters, "I10");
                                        $posI01 = strpos($parameters, "I01");

                                        if ($posI11 !== FALSE) {
                                            $paramIsel = 1;
                                            $paramIall = 1;
                                        }
                                        $parammulti = 0;
                                        if ($posI12 !== FALSE) {
                                            $parammulti = 1;
                                        }
                                        if ($posI10 !== FALSE) {
                                            $paramIsel = 1;
                                            $paramIall = 0;
                                        }
                                        if ($posI01 !== FALSE) {
                                            $paramIsel = 0;
                                            $paramIall = 1;
                                        }
                                        ?>
                                        <?php if ($report_id !== "CENTRALWAREHOUSE" && $report_id !== "QTRREPORT" && $report_id !== "PROVINCIALWAREHOUSE") { ?>
                                                                                                                    <!--                        <td width="9%" class="sb1NormalFont" bgcolor="#FFFFFF">Product:</td>-->


                                            <td class="col-md-2"><label class="control-label">Product</label>
                                                <select name="prod_sel<?php if ($parammulti) { ?>[]<?php } ?>" id="prod_sel" class="form-control input-sm bs-select " required <?php if ($parammulti) { ?>multiple="multiple" <?php } ?>>
                                                    <option value="">Select</option>
        <?php if ($paramIsel == 1) { ?>
                                                    <?php }
                                                    ?>
                                                    <?php if ($paramIall == 1) { ?>
                                                        <option value="all">All</option>
                                                    <?php }
                                                    ?>
                                                    <?php
                                                    //select query
                                                    //gets
                                                    //item rec id
                                                    //item name
                                                    $querypro = "SELECT itmrec_id,itm_id,itm_name FROM itminfo_tab WHERE itm_status=1 AND itminfo_tab.itm_category = 1 ORDER BY frmindex";
                                                    //query result
                                                    $rspro = mysql_query($querypro) or die();
                                                    //fetch result
                                                    while ($rowpro = mysql_fetch_array($rspro)) {
                                                        if ($parammulti && is_array($sel_items)) {
                                                            if (in_array($rowpro['itmrec_id'], $sel_items)) {
                                                                $sel = "selected='selected'";
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            ?>
                                                            <option value="<?php echo $rowpro['itmrec_id']; ?>" <?php echo $sel; ?>><?php echo $rowpro['itm_name']; ?></option>
                                                            <?php
                                                        } else {
                                                            if ($rowpro['itmrec_id'] == $sel_item) {
                                                                $sel = "selected='selected'";
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            ?>
                                                            <option value="<?php echo $rowpro['itmrec_id']; ?>" <?php echo $sel; ?>><?php echo $rowpro['itm_name']; ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </td>
    <?php } ?>
    <!--<td id="ppiuList" style="display:none" class="sb1NormalFont" bgcolor="#FFFFFF">CWH/PPIU Name:</td>
            <td id="ppiuList1" style="display:none" bgcolor="#FFFFFF">
    <?php
    $ppiuQryRes = mysql_query("SELECT * FROM `tbl_warehouse` WHERE (wh_type_id = 'PPIU' OR wh_type_id = 'CWH') AND stkid = 2 ");
    ?>
                <select class="form-control input-sm" name="ppiuName" id="ppiuName">
                                        <?php
                                        //fetch result
                                        while ($ppiuRow = mysql_fetch_array($ppiuQryRes)) {
                                            if ($ppiuRow['wh_id'] == $ppiuName) {
                                                $ppiuSel = "selected='selected'";
                                            } else {
                                                $ppiuSel = "";
                                            }
                                            ?>
                                                                <option value="<?php echo $ppiuRow['wh_id']; ?>"<?php echo $ppiuSel; ?>><?php
                                            if ($ppiuRow['wh_name'] == "LHW") {
                                                echo "CWH";
                                            } else {
                                                echo $ppiuRow['wh_name'];
                                            }
                                            ?></option>
                                        <?php }
                                        ?>
                </select>	
            </td>-->

    <?php if ($report_id == "CENTRALWAREHOUSE") { ?>
                                                                                <!--                                 <td class="sb1NormalFont" bgcolor="#FFFFFF">Indicator:</td>-->
                                            <td class="col-md-2"><label class="control-label">Indicator</label>
                                                <select class="form-control input-sm" name="repIndicators" id="repIndicators">
                                                    <option value="1" <?php
        if ($sel_indicator == 1) {
            echo 'selected="selected"';
        }
        ?>>Issued</option>
                                                    <option value="2" <?php
                                                            if ($sel_indicator == 2) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>>Stock on Hand</option>
                                                    <option value="3" <?php
                                                            if ($sel_indicator == 3) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>>Received</option>
                                                </select></td>
                                                            <?php
                                                        }
                                                        if ($report_id == "PROVINCIALWAREHOUSE") {
                                                            ?>
                                            <!--                                 <td class="sb1NormalFont" bgcolor="#FFFFFF">Indicator:</td>-->
                                            <td class="col-md-2"><label class="control-label">Indicator</label>
                                                <select class="form-control input-sm" name="repIndicators" id="repIndicators">
                                                    <option value="1" <?php
                                    if ($sel_indicator == 1) {
                                        echo 'selected="selected"';
                                    }
                                                            ?>>Consumption</option>
                                                    <option value="3" <?php
                                                            if ($sel_indicator == 3) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>>CYP</option>
                                                    <option value="2" <?php
                                                            if ($sel_indicator == 2) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>>Stock on Hand</option>
                                                    <option value="4" <?php
                                                            if ($sel_indicator == 4) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>>Received(District)</option>
                                                    <option value="5" <?php
                                                            if ($sel_indicator == 5) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>>Received(Field)</option>
                                                    <option value="6" <?php
        if ($sel_indicator == 6) {
            echo 'selected="selected"';
        }
        ?>>MOS</option>
                                                </select></td>
                                                            <?php
                                                        }
                                                        if ($report_id == "CENTRALWAREHOUSE") {
                                                            ?>
                                            <td class="col-md-2">
                                                <label class="control-label">Warehouse</label>
                                                <select class="form-control input-sm" name="wh_type" id="wh_type">
                                                    <option value="all" <?php
                                    if ($_REQUEST['wh_type'] == 'all') {
                                        echo 'selected="selected"';
                                    }
                                                            ?>>All</option>
                                                    <option value="1" <?php
                                                            if ($_REQUEST['wh_type'] == 1) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>>Central</option>
                                                    <option value="2" <?php
                                                            if ($_REQUEST['wh_type'] == 2) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>>Provincial</option>
                                                </select>
                                            </td>
        <?php
    }
}
?>
                                    <td class="col-md-1">
                                        <label class="control-label">&nbsp;</label>
                                        <input type="submit" name="go" id="go" value="GO" class="btn btn-primary input-sm" style="display:block" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="8"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</form>

<!--  END: REPORT Header -->
<script>
    $(function () {
        $('#stk_sel').change(function (e) {
            $('#prod_sel, #prov_sel').html('<option value="">Select</option>');
            showProducts('');
            showProvinces('');
        });
    })
<?php
if (isset($sel_item) && !empty($sel_item) && $report_id != "SDISTRICTREPORT") {
    ?>
        showProducts('<?php echo $sel_item; ?>');
        showProvinces('<?php echo $sel_prov; ?>');
    <?php
}
?>
    function showProducts(pid) {
        var stk = $('#stk_sel').val();
        if (typeof stk !== 'undefined')
        {
            $.ajax({
                url: 'ajax_calls.php',
                type: 'POST',
                data: {stakeholder: stk, productId: pid},
                success: function (data) {
                    $('#prod_sel').html(data);
                }
            })
        }
    }
    function showProvinces(pid) {
        var stk = $('#stk_sel').val();
        if (typeof stk !== 'undefined')
        {
            $.ajax({
                url: 'ajax_stk.php',
                type: 'POST',
                data: {stakeholder: stk, provinceId: pid, showProvinces: 1},
                success: function (data) {
                    $('#prov_sel').html(data);
                }
            })
        }
    }
</script>