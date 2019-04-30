<?php
/**
 * sub_dist_form
 * @package reports
 * 
 * @author     Ajmal Hussain 
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Initializing variables
//
if (empty($stakeholder))
    $stakeholder = 1;
//selMonth
$selMonth = isset($selMonth) ? $selMonth : '';
//selYear
$selYear = isset($selYear) ? $selYear : '';
//selProv

$selProv = isset($selProv) ? $selProv : '0';
//districtId
$districtId = isset($districtId) ? $districtId : '';
//selItem
$selItem = isset($selItem) ? $selItem : '';
//hfTypeId
$hfTypeId = isset($hfTypeId) ? $hfTypeId : '';
//fromDate
$fromDate = isset($fromDate) ? $fromDate : '';
//toDate
$toDate = isset($toDate) ? $toDate : '';
//qtr
$qtr = '';
//qtrArr
$qtrArr = array();
$show=isset($show)?$show:'';
//Populate arrays
//hfPrograms
$hfPrograms = array(1, 2, 4, 11);
//monthArr
$monthArr = array('form14', 'clr11', 'spr1', 'spr2', 'pwd3', 'outletCYP', 'distCYP', 'satellite');
//yearArr
$yearArr = array('form14', 'clr11', 'spr1', 'spr2', 'pwd3', 'outletCYP', 'distCYP', 'satellite');
//dateRange
$dateRange = array('form14y', 'clr11y', 'spr1y', 'spr2y', 'pwd3y', 'spr3', 'outletp', 'spr8', 'sdp_p', 'rcp1', 'rcp2', 'swp', 'dpr', 'spr9', 'spr10', 'spr11', 'clr13', 'clr15', 'dpw_f1', 'satellite');
//stakeholderArr
$stakeholderArr = array('dpr', 'rcp2', 'clr11', 'clr13', 'clr15', 'outletCYP', 'distCYP', 'spr2y', 'clr11y', 'form14y','sdp_batches');
//provinceArr
$provinceArr = array('form14', 'clr11', 'spr1', 'spr2', 'pwd3', 'form14y', 'outletp', 'clr11y', 'spr1y', 'spr2y', 'pwd3y', 'spr3', 'outletCYP','sdp_batches', 'distCYP', 'spr8', 'sdp_p', 'rcp2', 'rcp1', 'swp', 'dpr', 'spr9', 'spr10', 'spr11', 'clr13', 'clr15', 'dpw_f1', 'satellite');
//districtArr
$districtArr = array('spr8', 'sdp_p', 'rcp1', 'dpr', 'spr9', 'spr10', 'spr11', 'clr13', 'outletp', 'clr15', 'dpw_f1', 'satellite','sdp_batches');
//hfTypeArr
$hfTypeArr = array('spr2', 'sdp_p', 'spr2y', 'pwd3', 'pwd3y', 'outletp', 'spr10');
//producteArr
$producteArr = array('clr11', 'clr11y');
//satelliteArr
$satelliteArr = array('satellite');
//stakeholderArr
$sectorArr = array();
//Checking year_sel
//echo $show;exit;
$check_box=array('sdp_batches');
if (!isset($_POST['year_sel'])) {
    //Checking date
    if (date('d') > 10) {
        //selMonth
        $selMonth = date('m', strtotime("-1 month", strtotime(date('Y-m'))));
        //selYear
        $selYear = date('Y', strtotime("-1 month", strtotime(date('Y-m'))));
    } else {
        //selMonth
        $selMonth = date('m', strtotime("-2 month", strtotime(date('Y-m'))));
        //selYear
        $selYear = date('Y', strtotime("-2 month", strtotime(date('Y-m'))));
    }
}
?>
<form name="frm" id="frm" action="" method="post" role="form">
    <div class="row">               
        <div class="col-md-12"> 
            <?php
            if (in_array($rptId, $monthArr)) {
                ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Month</label>
                        <div class="form-group">
                            <select name="month_sel" id="month_sel" class="form-control input-sm" required>
                                <?php
                                for ($i = 1; $i <= 12; $i++) {
                                    if ($selMonth == $i) {
                                        $sel = "selected='selected'";
                                    } else {
                                        $sel = "";
                                    }
                                    ?>
                                    <option value="<?php echo date('m', mktime(0, 0, 0, $i, 1)); ?>"<?php echo $sel; ?> ><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php
            if (in_array($rptId, $yearArr)) {
                ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Year</label>
                        <div class="form-group">
                            <select name="year_sel" id="year_sel" class="form-control input-sm" required>
                                <?php
                                for ($j = date('Y'); $j >= 2010; $j--) {
                                    if ($selYear == $j) {
                                        $sel = "selected='selected'";
                                    } else {
                                        $sel = "";
                                    }
                                    ?>
                                    <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php
            } else if (in_array($rptId, $dateRange)) {
                ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Start Date</label>
                        <div class="form-group">
                            <input type="text" name="from_date" id="from_date" readonly="readonly" class="form-control input-sm" value="<?php echo $fromDate; ?>" required>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">End Date</label>
                        <div class="form-group">
                            <input type="text" name="to_date" id="to_date" readonly="readonly" class="form-control input-sm" value="<?php echo $toDate; ?>" required>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php
            if (in_array($rptId, $qtrArr)) {
                ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Quarter</label>
                        <div class="form-group">
                            <select name="qtr" id="qtr" class="form-control input-sm" required>
                                <option value="1" <?php echo ($qtr == 1) ? 'selected="selected"' : ''; ?>>1st Quarter</option>
                                <option value="2" <?php echo ($qtr == 2) ? 'selected="selected"' : ''; ?>>2nd Quarter</option>
                                <option value="3" <?php echo ($qtr == 3) ? 'selected="selected"' : ''; ?>>3rd Quarter</option>
                                <option value="4" <?php echo ($qtr == 4) ? 'selected="selected"' : ''; ?>>4th Quarter</option>
                                <option value="5" <?php echo ($qtr == 5) ? 'selected="selected"' : ''; ?>>1st Half</option>
                                <option value="6" <?php echo ($qtr == 6) ? 'selected="selected"' : ''; ?>>2nd Half</option>
                                <option value="7" <?php echo ($qtr == 7) ? 'selected="selected"' : ''; ?>>Annual</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php
            if (in_array($rptId, $stakeholderArr)) {
                ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Stakeholder</label>
                        <div class="form-group">
                            <select name="stakeholder" id="stakeholder" required class="form-control input-sm">
                                <option value="">Select</option>
                                <?php
                                $stk_rp_arr = array('clr11', 'clr13', 'outletCYP', 'distCYP');

                                $where = '';
                                if (in_array($rptId, $stk_rp_arr))
                                    $where .= ' AND stakeholder.stkid in (1,2,7,9,73) ';

                                $querys = "SELECT
                        stakeholder.stkid,
                        stakeholder.stkname
                        FROM
                        stakeholder
                        WHERE
                        stakeholder.ParentID IS NULL
                        AND stakeholder.stk_type_id IN (0, 1) AND
                        stakeholder.is_reporting = 1
                        AND stakeholder.lvl = 1
                        $where
                        ORDER BY
                        stakeholder.stkorder ASC";
                                //query result
                                $rsprov = mysql_query($querys) or die();
                                $stk_name = '';
                                while ($rowp = mysql_fetch_array($rsprov)) {
                                    if ($stakeholder == $rowp['stkid']) {
                                        $sel = "selected='selected'";
                                        $stk_name = $rowp['stkname'];
                                    } else {
                                        $sel = "";
                                    }
                                    //Populate prov_sel combo
                                    ?>
                                    <option value="<?php echo $rowp['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowp['stkname']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
    <?php
}
?>



<?php
if (in_array($rptId, $provinceArr)) {
    ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Province</label>
                        <div class="form-group">
                            <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                <option value="">Select</option>
    <?php
    if ($rptId == 'satellite') {
        $sel = ($selProv == 3) ? 'selected="selected"' : '';
        echo "<option value=\"3\" $sel>Khyber Pakhtunkhwa</option>";
    } else {
        $queryprov = "SELECT
                                        tbl_locations.PkLocID AS prov_id,
                                        tbl_locations.LocName AS prov_title
                                    FROM
                                        tbl_locations
                                    WHERE
                                        LocLvl = 2
                                    AND parentid IS NOT NULL";
        //query result
        $rsprov = mysql_query($queryprov) or die();
        $prov_name = '';
        while ($rowprov = mysql_fetch_array($rsprov)) {
            if ($selProv == $rowprov['prov_id']) {
                $sel = "selected='selected'";
                $prov_name = $rowprov['prov_title'];
            } else {
                $sel = "";
            }
            //Populate prov_sel combo
            ?>
                                        <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
    <?php
}
?>
            <?php
            if (in_array($rptId, $districtArr)) {
                ?>
                <div class="col-md-2">
                    <div class="form-group" id="districtDiv">
                        <label class="control-label">District</label>
                        <div class="form-group">
                            <select name="district" id="district" class="form-control input-sm">
                                <option value="">All</option>

                            </select>
                        </div>
                    </div>
                </div>
    <?php
}
?>
            <?php
            if (in_array($rptId, $producteArr)) {
                ?>
                <div class="col-md-2">
                    <div class="form-group" id="districtDiv">
                        <label class="control-label">Product</label>
                        <div class="form-group">
                            <select name="itm_id" id="itm_id" class="form-control input-sm" required>
                                <option value="">Select</option>
    <?php
    //Query for item
    //gets 
    //itm_name
    //itm_id
    $queryItem = "SELECT
                                        itminfo_tab.itm_name,
                                        itminfo_tab.itm_id
                                    FROM
                                        stakeholder_item
                                        INNER JOIN itminfo_tab ON stakeholder_item.stk_item = itminfo_tab.itm_id
                                    WHERE
                                        stakeholder_item.stkid = 1
                                        AND itminfo_tab.itm_category = 1
                                    ORDER BY
                                        itminfo_tab.frmindex ASC";
    //Result
    $rsprov = mysql_query($queryItem) or die();
    while ($rowItem = mysql_fetch_array($rsprov)) {
        if ($selItem == $rowItem['itm_id']) {
            $sel = "selected='selected'";
        } else {
            $sel = "";
        }
        ?>
                                    <?php //Populate itm_id combo ?>
                                    <option value="<?php echo $rowItem['itm_id']; ?>" <?php echo $sel; ?>><?php echo $rowItem['itm_name']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
    <?php
}
?>
            <?php
            if (in_array($rptId, $sectorArr)) {
                ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Sector</label>
                        <div class="form-group">
                            <select name="sector" id="sector" required class="form-control input-sm">

    <?php
    $querys = "SELECT
                        * from stakeholder_type order by stk_type_id";
    //query result
    $rsprov = mysql_query($querys) or die();
    $sector_name = '';
    while ($rowp = mysql_fetch_array($rsprov)) {
        if ($sectorId == $rowp['stk_type_id']) {
            $sel = "selected='selected'";
            $sector_name = $rowp['stk_type_descr'];
        } else {
            $sel = "";
        }
        //Populate prov_sel combo
        ?>
                                    <option value="<?php echo $rowp['stk_type_id']; ?>" <?php echo $sel; ?>><?php echo $rowp['stk_type_descr']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                                <?php
                            }
                            ?>
<?php
if (in_array($rptId, $hfTypeArr)) {
    ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Health Facility Type</label>
                        <div class="form-group">
                <?php
                if ($rptId == 'sdp_p') {
                    echo '<select name="hf_type_sel[]" size="6" multiple id="hf_type_sel"  required class="form-control input-sm">';
                } else {
                    echo '<select name="hf_type_sel" id="hf_type_sel"  required class="form-control input-sm">';
                }
                ?>
                            <option value="">Select</option>

                            <?php if ($rptId == 'spr10') { ?> <option value="0">All</option><?php
                            }
                            //Query for prov
                            //gets
                            //tbl_hf_type.pk_id,
                            //tbl_hf_type.hf_type
                            $queryprov = "SELECT
                            tbl_hf_type.pk_id,
                            tbl_hf_type.hf_type
                        FROM
                            tbl_hf_type
                        INNER JOIN tbl_hf_type_rank ON tbl_hf_type.pk_id = tbl_hf_type_rank.hf_type_id
                        WHERE
                            tbl_hf_type_rank.stakeholder_id = $stakeholder
                        AND tbl_hf_type_rank.province_id = $selProv
                        ORDER BY
                            tbl_hf_type_rank.hf_type_rank ASC";
//    echo $queryprov;
                            $rsprov = mysql_query($queryprov) or die();
                            if (isset($_REQUEST['hf_type_sel'])) {
                                if ($rptId != 'sdp_p')
                                    echo "<option value='0'" . (($hfTypeId == 0) ? 'selected=\"selected\"' : '' ) . ">All</option>";
                            }
                            while ($rowHFType = mysql_fetch_array($rsprov)) {
                                //Checking hfTypeId
                                $sel = "";
                                if ($rptId == 'sdp_p') {
                                    if (in_array($rowHFType['pk_id'], $hfTypeId)) {
                                        $sel = "selected='selected'";
                                        $hf_list[] = $rowHFType['hf_type'];
                                    }
                                } else {
                                    if ($hfTypeId == $rowHFType['pk_id']) {
                                        $sel = "selected='selected'";
                                    }
                                }
                                ?>
                                <?php //Populate hf_type_sel combo?>
                                <option value="<?php echo $rowHFType['pk_id']; ?>" <?php echo $sel; ?>><?php echo $rowHFType['hf_type']; ?></option>
                                <?php
                            }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                            <?php
                        }
                        ?>
                        <?php
                        if (in_array($rptId, $satelliteArr)) {
                            ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Satellite Camp</label>
                        <div class="form-group">
                            <select name="camps" id="camps" class="form-control input-sm">
                                <option value="">Select</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
                <?php
            if (in_array($rptId, $check_box)) {
                ?>
                <div class="col-md-2">
                    <div class="form-group" id="checkboxDiv">
                        <br><br>
                        <div class="form-group">
                            <input name="show_all" id="show_all" type="checkbox" <?php  if($show=="on") {echo 'checked';} else{ echo "";}?>> Show All 

                             
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary input-sm">Go</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>