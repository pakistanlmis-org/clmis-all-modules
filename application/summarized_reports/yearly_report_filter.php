<?php
$month_filter_arr = array('d_rep');
$sector_filter_arr = array('d_rep','p_rep');
$product_filter_arr =  array('d_rep');
$stakeholder_filter_arr =  array('d_rep','p_rep','cp_rep','ps_rep');
$province_filter_arr =  array('d_rep','p_rep');
$level_filter_arr =  array('d_rep');
$type_filter_arr =  array('d_rep','p_rep','cp_rep','ps_rep');
$wh_type_filter_arr =  array('cp_rep');
?>
<div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="get" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                           <div class="col-md-3" id="td_report_type">
                                               <div class="control-group">
                                                   <label class="sb1NormalFont">Report Type</label>
                                                    <select name="report_type" id="report_type" class="form-control input-sm">
                                                        <option value="">Select Report</option>
                                                        <option value="d_rep"  <?=(($report_type=='d_rep')?' selected ':'')?> >District Stock Report</option>
                                                        <option value="p_rep"  <?=(($report_type=='p_rep')?' selected ':'')?> >Provincial Report</option>
                                                        <option value="cp_rep" <?=(($report_type=='cp_rep')?' selected ':'')?> >Central / Provincial Report</option>
                                                        <option value="ps_rep" <?=(($report_type=='ps_rep')?' selected ':'')?> >Private Sector Report</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="td_ending_month" class="col-md-2 filter1" style="<?=(in_array($report_type,$month_filter_arr)?'':'display: none;')?>">
                                                <div class="control-group">
                                                    <label>Ending Month</label>
                                                    <div class="controls">
                                                        <select name="ending_month" id="ending_month" class="form-control input-sm">
                                                            <?php
                                                            for ($i = 1; $i <= 12; $i++) {
                                                                if ($selMonth == $i) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //populate ending_month combo
                                                                ?>
                                                                <option value="<?php echo date('m', mktime(0, 0, 0, $i, 1)); ?>"<?php echo $sel; ?> ><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="td_year"  class="col-md-2">
                                                <div class="control-group">
                                                    <label>Year</label>
                                                    <div class="controls">
                                                        <select name="year_sel" id="year_sel" class="form-control input-sm">
                                                            <?php
                                                            for ($j = date('Y'); $j >= 2010; $j--) {
                                                                if ($selYear == $j) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //populate year_sel combo
                                                                ?>
                                                                <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="td_sect"  class="col-md-2 filter1" style="<?=(in_array($report_type,$sector_filter_arr)?'':'display: none;')?>">
                                                <div class="control-group">
                                                    <label>Sector</label>
                                                    <div class="controls">
                                                        <select class="form-control input-sm" id="sector" name="sector">
                                                            <option  value="all"   <?=($sector=='all')?' selected ':''?>>All</option>
                                                            <option  value="public"  <?=($sector=='public')?' selected ':''?> >Public</option>
                                                            <option  value="private"  <?=($sector=='private')?' selected ':''?>>Private</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="td_stk"  class="col-md-2 filter1" style="<?=(in_array($report_type,$stakeholder_filter_arr)?'':'display: none;')?>">
                                                <div class="control-group">
                                                    <label>Stakeholder</label>
                                                    <div class="controls">
                                                        <select name="stk_sel" id="stk_sel" required class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            
                                                            <?php
                                                            //if($report_type != 'ps_rep')
                                                            echo '<option value="all"'.(($selStk == 'all') ? "selected='selected'" : "").'>All</option>';
                                                            
                                                            $querystk = "SELECT DISTINCT
                                                                                        stakeholder.stkid,
                                                                                        stakeholder.stkname,
                                                                                        stakeholder.stk_type_id
                                                                                FROM
                                                                                        tbl_warehouse
                                                                                INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                                                                                INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                                                                                WHERE
                                                                                        tbl_warehouse.is_active = 1
                                                                                        
                                                                                ORDER BY
                                                                                        stakeholder.stk_type_id ASC,
                                                                                        stakeholder.stkorder ASC";
                                                            $rsstk = mysql_query($querystk) or die();
                                                            while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                if ($selStk == $rowstk['stkid']) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                
                                                                if($report_type == 'd_rep' )
                                                                {
                                                                    $disp_stk = ' ';
                                                                }
                                                                elseif($report_type == 'p_rep')
                                                                {
                                                                    $disp_stk = ' ';
                                                                }
                                                                elseif($report_type == 'cp_rep')
                                                                {
                                                                      $disp_stk = ' ';
                                                                }
                                                                elseif($report_type == 'ps_rep')
                                                                {
                                                                    if($rowstk['stk_type_id']=='0') $disp_stk = 'display:none;';
                                                                     else $disp_stk = ' ';
                                                                }
                                                                ?>
                                                                <option value="<?php echo $rowstk['stkid']; ?>" stk-type="<?=$rowstk['stk_type_id']?>" <?php echo $sel; ?> style="<?=$disp_stk?>"><?=($report_type=='cp_rep' && $rowstk['stkid']=='1')?'PPW/CWH':$rowstk['stkname']?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="td_prov"  class="col-md-3 filter1" style="<?=(in_array($report_type,$province_filter_arr)?'':'display: none;')?>">
                                                <div class="control-group">
                                                    <label>Province</label>
                                                    <div class="controls">
                                                        <select name="prov_sel" id="prov_sel" required class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option value="all" <?php echo ($selPro == 'all') ? "selected='selected'" : ""; ?>>All</option>
                                                            <?php
                                                            $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                            AND LocType= 2
                                                                            AND parentid IS NOT NULL";
//query result
                                                            $rsprov = mysql_query($queryprov) or die();
//fetch result
                                                            while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                if ($selPro == $rowprov['prov_id']) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //populate prov_sel
                                                                ?>
                                                                <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                            <div id="td_ind"  class="col-md-2 filter1" style="<?=(in_array($report_type,$type_filter_arr)?'':'display: none;')?>">
                                                <div class="control-group">
                                                    <label>Indicator</label>
                                                    <div class="controls">
                                                        <select name="type" id="type" required class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <?php
                                                            if($report_type == 'd_rep' )
                                                            {
                                                                $d_rep_view = '';
                                                                $p_rep_view = ' display:none; ';
                                                                $cp_rep_view = ' display:none; ';
                                                                $ps_rep_view = ' display:none; ';
                                                            }
                                                            elseif($report_type == 'p_rep')
                                                            {
                                                                $d_rep_view = ' display:none; ';
                                                                $p_rep_view = '';
                                                                $cp_rep_view = ' display:none; ';
                                                                $ps_rep_view = ' display:none; ';
                                                            }
                                                            elseif($report_type == 'cp_rep')
                                                            {
                                                                $d_rep_view = ' display:none ';
                                                                $p_rep_view = ' display:none; ';
                                                                $cp_rep_view = '';
                                                                $ps_rep_view = ' display:none; ';
                                                                
                                                            }
                                                            elseif($report_type == 'ps_rep')
                                                            {
                                                                $d_rep_view = ' display:none; ';
                                                                $p_rep_view = ' display:none; ';
                                                                $cp_rep_view = ' display:none; ';
                                                                $ps_rep_view = '';
                                                                
                                                            }
                                                            ?>
                                                                <option value="1" rep-type="d_rep" style="<?=$d_rep_view?>" <?php echo ($type == 1 && empty($d_rep_view)) ? "selected='selected'" : ""; ?>>Issue</option>
                                                                <option value="2" rep-type="d_rep" style="<?=$d_rep_view?>"  <?php echo ($type == 2 && empty($d_rep_view)) ? "selected='selected'" : ""; ?>>Receive</option>
                                                                <option value="3" rep-type="d_rep" style="<?=$d_rep_view?>"  <?php echo ($type == 3 && empty($d_rep_view)) ? "selected='selected'" : ""; ?>>Consumption</option>
                                                                <option value="4" rep-type="d_rep" style="<?=$d_rep_view?>"  <?php echo ($type == 4 && empty($d_rep_view)) ? "selected='selected'" : ""; ?>>Stock on Hand</option>
                                                                <option value="5" rep-type="d_rep" style="<?=$d_rep_view?>"  <?php echo ($type == 5 && empty($d_rep_view)) ? "selected='selected'" : ""; ?>>CYP</option>
                                                                
                                                                <option value="1" rep-type="p_rep" style="<?=$p_rep_view?>"  <?php echo ($type == 1 && empty($p_rep_view)) ? "selected='selected'" : ""; ?>>Consumption</option>
                                                                <option value="2" rep-type="p_rep" style="<?=$p_rep_view?>"   <?php echo ($type == 2 && empty($p_rep_view)) ? "selected='selected'" : ""; ?>>Stock on Hand</option>
                                                                <option value="3" rep-type="p_rep" style="<?=$p_rep_view?>"   <?php echo ($type == 3 && empty($p_rep_view)) ? "selected='selected'" : ""; ?>>CYP</option>
                                                                <option value="4" rep-type="p_rep" style="<?=$p_rep_view?>"   <?php echo ($type == 4 && empty($p_rep_view)) ? "selected='selected'" : ""; ?>>Received (District)</option>
                                                                <option value="5" rep-type="p_rep" style="<?=$p_rep_view?>"   <?php echo ($type == 5 && empty($d_rep_view)) ? "selected='selected'" : ""; ?>>Received (Field)</option>
                                                           
                                                                <option value="1" rep-type="cp_rep" style="<?=$cp_rep_view?>"   <?php echo ($type == 1 && empty($cp_rep_view)) ? "selected='selected'" : ""; ?>>Issued</option>
                                                                <option value="2" rep-type="cp_rep" style="<?=$cp_rep_view?>"   <?php echo ($type == 2 && empty($cp_rep_view)) ? "selected='selected'" : ""; ?>>Stock on Hand</option>
                                                                <option value="3" rep-type="cp_rep" style="<?=$cp_rep_view?>"   <?php echo ($type == 3 && empty($cp_rep_view)) ? "selected='selected'" : ""; ?>>Received</option>
                                                           
                                                                <option value="1" rep-type="ps_rep" style="<?=$ps_rep_view?>"   <?php echo ($type == 1 && empty($ps_rep_view)) ? "selected='selected'" : ""; ?>>Consumption</option>
                                                                <option value="2" rep-type="ps_rep" style="<?=$ps_rep_view?>"   <?php echo ($type == 2 && empty($ps_rep_view)) ? "selected='selected'" : ""; ?>>Stock on Hand</option>
                                                                <option value="3" rep-type="ps_rep" style="<?=$ps_rep_view?>"   <?php echo ($type == 3 && empty($ps_rep_view)) ? "selected='selected'" : ""; ?>>Received</option>
                                                            
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 filter1"  id="td_ind_lvl" style="<?=(in_array($report_type,$level_filter_arr)?'':'display: none;')?>">
                                                <div class="control-group">
                                                    <label>Indicator Level</label>
                                                    <div class="controls">
                                                        <select name="indicator_lvl" id="indicator_lvl" required class="form-control input-sm">
                                                            <option value="all" <?php echo ($indicatorLvl == 'all') ? "selected='selected'" : ""; ?>>District and Field</option>
                                                            <option value="3" <?php echo ($indicatorLvl == 3) ? "selected='selected'" : ""; ?>>District</option>
                                                            <option value="4" <?php echo ($indicatorLvl == 4) ? "selected='selected'" : ""; ?>>Field</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="td_prod"  class="col-md-2 filter1" style="<?=(in_array($report_type,$product_filter_arr)?'':'display: none;')?>">
                                                <div class="control-group">
                                                    <label>Product</label>
                                                    <div class="controls">
                                                        <select name="item_id" id="item_id" <?=($report_type=='d_rep')?' required ':''?> class="form-control input-sm">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div  id="td_wh_type"  class="col-md-2 filter1"  style="<?=(in_array($report_type,$wh_type_filter_arr)?'':'display: none;')?>">
                                                <div class="control-group">
                                                    <label class="control-label">Warehouse</label>
                                                    <select class="form-control input-sm" name="wh_type" id="wh_type">
                                                        <option value="all" <?=($whType == 'all')?'selected="selected"':''?>>All</option>
                                                        <option value="1" <?=($whType == 1)?'selected="selected"':''?>>Central</option>
                                                        <option value="2" <?=($whType == 2)?'selected="selected"':''?> >Provincial</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-1">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name="submit" id="go" value="GO" class="btn btn-primary input-sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>