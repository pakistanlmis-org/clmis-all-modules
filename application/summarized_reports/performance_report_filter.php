<?php
$quarter_filter_arr =  array('q_reporting_rate','inv_turnover');
$month_filter_arr =  array('compliance_report_dist','compliance_report_hf','p_reporting_rate','non_rep_districts');
$stakeholder_filter_arr =  array('compliance_report_dist','compliance_report_hf');
$province_filter_arr =  array('compliance_report_dist','compliance_report_hf');
$district_filter_arr =  array('compliance_report_hf');
?>
<div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="get">
                                   <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3" id="td_report_type"><label class="sb1NormalFont">Report Type</label>
                                                <select name="report_type" id="report_type" required class="form-control input-sm">
                                                    <option value="">Select Report</option>
                                                    <option value="compliance_report_dist"  <?=(($report_type=='compliance_report_dist')?' selected ':'')?> >Compliance Report (District)</option>
                                                    <option value="compliance_report_hf"    <?=(($report_type=='compliance_report_hf')?' selected ':'')?> >Compliance Report (HF)</option>
                                                    <option value="q_reporting_rate"        <?=(($report_type=='q_reporting_rate')?' selected ':'')?> >Quarterly Reporting Rate</option>
                                                    <option value="p_reporting_rate"        <?=(($report_type=='p_reporting_rate')?' selected ':'')?> >Provincial Reporting Rate</option>
                                                    <option style="display:none;" value="non_rep_districts"     <?=(($report_type=='non_rep_districts')?' selected ':'')?> >Non-Reported Districts</option>
                                                    <option style="display:none;" value="inv_turnover"          <?=(($report_type=='inv_turnover')?' selected ':'')?> >Inventory Turnover</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 filter1" id="td_quarter" style="<?=(in_array($report_type,$quarter_filter_arr)?'':'display: none;')?>"><label class="sb1NormalFont">Quarter</label>
                                                <select name="quarter" id="quarter" class="form-control input-sm">
                                                        <option value="1" name="quarter1" <?=($quarter == "1")?"selected='selected'":""?> >First Quarter</option>
                                                        <option value="2" name="quarter2" <?=($quarter == "2")?"selected='selected'":""?> >Second Quarter</option>
                                                        <option value="3" name="quarter3" <?=($quarter == "3")?"selected='selected'":""?> >Third Quarter</option>
                                                        <option value="4" name="quarter4" <?=($quarter == "4")?"selected='selected'":""?> >Fourth Quarter</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2 filter1" id="td_month"  style="<?=(in_array($report_type,$month_filter_arr)?'':'display: none;')?>"><label class="sb1NormalFont">Month</label>
                                                <select name="ending_month" id="month" class="form-control input-sm">
                                                    <?php
                                                        for ($i = 1; $i <= 12; $i++) {
                                                            //check selected month
                                                            if ($sel_month == $i) {
                                                                $sel = "selected='selected'";
                                                            } else {
                                                                if ($i == 1) {
                                                                    $sel = " selected='selected' ";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                            }
                                                            //populate month_sel combo
                                                            ?>
                                                            <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2" id="td_year"><label class="sb1NormalFont">Year</label>
                                                <select name="year_sel" id="year" class="form-control input-sm">
                                                    <?php
                                                    for ($j = date('Y'); $j >= 2010; $j--) {
                                                        if ($selYear == $j) {
                                                            $sel = "selected='selected'";
                                                        } else if ($j == 1) {
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
                                            
                                            <div class="col-md-2 filter1" id="td_stk" style="<?=(in_array($report_type,$stakeholder_filter_arr)?'':'display: none;')?>"><label class="sb1NormalFont">Stakeholder</label>
                                                <select name="stk_sel" id="stk" class="form-control input-sm">
                                                    
                               
                                                    <?php
                                                    $querystk = "
                                                        SELECT DISTINCT
                                                                stakeholder.stkid,
                                                                stakeholder.stkname,
                                                                stakeholder.stk_type_id
                                                        FROM
                                                                tbl_warehouse
                                                        INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                                                        INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                        WHERE
                                                                stakeholder.stk_type_id IN (0)
                                                        AND tbl_warehouse.is_active = 1
                                                        ORDER BY
                                                                stakeholder.stk_type_id ASC,
                                                                stakeholder.stkorder ASC
                                                        ";
                                                    //query result
                                                    $rsstk = mysql_query($querystk) or die();
                                                    //fetch result
                                                    while ($rowstk = mysql_fetch_array($rsstk)) {
                                                        $sel="";
                                                        //check seleted stakeholder
                                                        if ($sel_stk == $rowstk['stkid']) {
                                                            $sel = " selected='selected' ";
                                                        } 
                                                        $stkName = $rowstk['stkname'];
                                                        ?>
                                                        <option value="<?php echo $rowstk['stkid']; ?>" <?=$sel?> stk_type="<?=$rowstk['stk_type_id']?>"><?php echo $stkName; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2 filter1" id="td_prov" style="<?=(in_array($report_type,$province_filter_arr)?'':'display: none;')?>"><label class="sb1NormalFont">Province</label>
                                                <select name="prov_sel" id="prov" onchange="showDistricts('')" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                        <?php


                                                        $queryprov = "
                                                        SELECT 
                                                                tbl_locations.PkLocID,
                                                                tbl_locations.LocName
                                                        FROM
                                                                tbl_locations
                                                        WHERE
                                                                tbl_locations.ParentID IS NOT NULL
                                                        AND tbl_locations.LocLvl = 2
                                                        AND tbl_locations.LocType = 2
                                                        ";
                                                        //query result
                                                        $rsprov = mysql_query($queryprov) or die();
                                                        while ($rowprov = mysql_fetch_array($rsprov)) {
                                                            $sel = "";
                                                            if ($sel_prov == $rowprov['PkLocID']) {
                                                                $sel = "selected='selected'";
                                                            } 
                                                            ?>
                                                            <option value="<?php echo $rowprov['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowprov['LocName']; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2 filter1" id="td_dist" style="<?=(in_array($report_type,$district_filter_arr)?'':'display: none;')?>"><label class="sb1NormalFont">Distict</label>
                                                <select name="dist" id="dist" class="form-control input-sm">
                                                     <option value="">Select</option>
                                                        <?php
                                                        //select query
                                                        //gets
                                                        //district id
                                                        //district name
                                                        $queryDist = "SELECT
                                                                                tbl_locations.PkLocID,
                                                                                tbl_locations.LocName
                                                                        FROM
                                                                                tbl_locations
                                                                        WHERE
                                                                                tbl_locations.LocLvl = 3
                                                                        AND tbl_locations.parentid = '" . $sel_prov . "'
                                                                        ORDER BY
                                                                                tbl_locations.LocName ASC";
                                                        //query result
                                                        $rsDist = mysql_query($queryDist) or die();
                                                        //fetch result
                                                        while ($rowDist = mysql_fetch_array($rsDist)) {
                                                            if ($selDist == $rowDist['PkLocID']) {
                                                                $sel = "selected='selected'";
                                                            } else {
                                                                $sel = "";
                                                            }
                                                            //populate district combo
                                                            ?>
                                                            <option
                                                                value="<?php echo $rowDist['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowDist['LocName']; ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                </select>
                                            </div>
                                            <div class="col-md-1" style="  padding-top: 20px;" valign="middle"><input type="submit" name="submit" id="go" value="GO" class="btn btn-primary input-sm" /></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>


