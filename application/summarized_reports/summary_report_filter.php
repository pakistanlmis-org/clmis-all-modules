<?php
$sector_filter_arr = array('n_rep','p_rep','d_rep');
$product_filter_arr =  array('s_rep','p_rep','d_rep');
$stakeholder_filter_arr =  array('p_rep','d_rep');
$district_filter_arr =  array('d_rep');
?>
<div class="widget" data-toggle="collapse-widget">
    <div class="widget-head">
        <h3 class="heading">Filter by</h3>
    </div>
    <div class="widget-body">
        <form name="frm" id="frm" action="" method="get">
            <div class="row">
                <div class="col-md-12">
                    
                        <div colspan="2" style="font-family: Arial, Verdana, Helvetica, sans-serif;font-size: 12px;">
                            <?php echo stripslashes(getReportStockDescription('SNASUM')); ?>
                        </div>
                   
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-3" id="td_report_type"><label class="sb1NormalFont">Report Type</label>
                        <select name="report_type" id="report_type" required class="form-control input-sm">
                            <option value="">Select Report</option>
                            <option value="n_rep" <?=(($report_type=='n_rep')?' selected ':'')?> >National Report</option>
                            <option value="s_rep" <?=(($report_type=='s_rep')?' selected ':'')?>>Stakeholder Report</option>
                            <option value="p_rep" <?=(($report_type=='p_rep')?' selected ':'')?>>Provincial Report</option>
                            <option value="d_rep" <?=(($report_type=='d_rep')?' selected ':'')?>>District Report</option>
                            <option style="display:none" value="pps_rep" <?=(($report_type=='pps_rep')?' selected ':'')?>>Public-Private Sector Report</option>
                        </select>
                    </div>

                    <div class="col-md-2 " id="td_month"  ><label class="sb1NormalFont">Month</label>
                        <select name="month_sel" id="month" class="form-control input-sm">
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
                                    <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                    <?php
                                }
                                ?>
                        </select>
                    </div>
                    <div class="col-md-2" id="td_year"><label class="sb1NormalFont">Year</label>
                        <select name="year_sel" id="year" class="form-control input-sm">
                            <?php
                            for ($j = date('Y'); $j >= 2010; $j--) {
                                if ($sel_year == $j) {
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

                    <div class="col-md-2 filter1" id="td_sect" style="<?=(in_array($report_type,$sector_filter_arr)?'':'display: none;')?>"><label class="sb1NormalFont">Sector</label>
                        <select name="sector" id="sector" class="form-control input-sm">
                            <option value="all" >All</option>
                            <option value="public" <?=($sector=='public')?' selected ':''?> >Public</option>
                            <option value="private" <?=($sector=='private')?' selected ':''?> >Private</option>
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
                    <div class="col-md-2 filter1" id="td_prov" style="<?=(in_array($report_type,$district_filter_arr)?'':'display: none;')?>"><label class="sb1NormalFont">Province</label>
                        <select name="prov_sel" id="prov" class="form-control input-sm">
                            
                            <option value="all">All</option>
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
                    <div class="col-md-2 filter1" id="td_dist" style="display:none;"><label class="sb1NormalFont">Distict</label>
                        <select name="dist_id" id="dist" class="form-control input-sm">
                           
                        </select>
                    </div>
                    <div class="col-md-2 filter1" id="td_prod" style="<?=(in_array($report_type,$product_filter_arr)?'':'display: none;')?>"><label class="sb1NormalFont">Product</label>
                        <select name="prod_sel" id="product" class="form-control input-sm">
                            
                           
                            <?php
                            
                            $querypro = "SELECT itmrec_id,itm_id,itm_name 
                                    FROM itminfo_tab 
                                    WHERE itm_status=1 AND itminfo_tab.itm_category = 1 
                                    ORDER BY frmindex";
                            
                            //query result
                            $rspro = mysql_query($querypro) or die();
                            //fetch result
                            while ($rowpro = mysql_fetch_array($rspro)) {
                                $sel = "";
                                if ($rowpro['itmrec_id'] == $sel_item) {
                                    $sel = " selected='selected' ";
                                } 
                                ?>
                                <option value="<?php echo $rowpro['itmrec_id']; ?>" <?php echo $sel; ?> ><?php echo $rowpro['itm_name']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 filter1" id="td_level" style="display: none"><label class="sb1NormalFont">Level</label>
                        <select name="level" id="level" class="form-control input-sm">
                            <option value="1" >National</option>
                            <option value="2" >Provincial</option>
                        </select>
                    </div>
                    <div class="col-md-2" style="margin-left:20px; padding-top: 20px;" valign="middle"><input type="submit" name="go" id="go" value="GO" class="btn btn-primary input-sm" /></div>
                </div>
            </div>
        </form>
    </div>
</div>