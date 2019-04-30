<?php 
/**
 * compeltenessForm
 * @package includes/maps
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
?>
<!------  Heading Of Map      --------->	
<table width="100%">
    <tr height="40">
        <td colspan="2" align="center" style=" background:url(<?php echo PUBLIC_URL; ?>images/grn-top-bg.jpg); background-repeat:repeat-x; font-family:Verdana, Geneva, sans-serif; font-weight:bold; color:#FFF;"><?php echo "Completeness Of Reporting Map"; ?></td>
    </tr>
</table>
<!------   End of Heading Of Map      --------->	






<!------   Input parameter for Completeness Of Reporting Map      --------->

<table width="95%">
    <tr><td class="sb1NormalFont">Filter By</td></tr>
    <tr>
        <td colspan="2">
    <tr>
        <td><div class="sb1NormalFont">Year</div>
            <select name="year_sel" id="year_sel" class="input_select" style="width:120px">
                <?php
                //populate combo year_sel
                for ($j = date('Y'); $j >= 2010; $j--) {
                    //check seleced year
                    if ($sel_year == $j) {
                        $sel = "selected='selected'";
                    } else {
                        if ($j == date("Y")) {
                            $sel = "selected='selected'";
                        } else {
                            $sel = "";
                        }
                    }
                    //populate year_sel combo ?>
                    <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                    <?php
                }
                ?>
            </select>
        </td>
        <td><div class="sb1NormalFont">Month</div>
            <select name="month_sel" id="month_sel" class="input_select" style="width:120px">                                               
                <?php
                //for populate month_sel combo
                for ($i = 1; $i <= 12; $i++) {
                    //check sel_month
                    if ($sel_month == $i) {
                        $sel = "selected='selected'";
                    } else {
                        if ($i == 1) {
                            $sel = "selected='selected'";
                        } else {
                            $sel = "";
                        }
                    }
                    //populate month_sel combo?>
                    <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                    <?php
                }
                ?>
            </select>
        </td>
        <td><div class="sb1NormalFont">Stakeholder</div>
            <select name="stk_sel" id="stk_sel" width="150px" class="input_select" style="width:120px">    
                <option value="all">All</option>
                <?php
                //stakeholder query
                //gets
                //stkid,
                //stkname
                $querystk = "SELECT stkid,stkname FROM stakeholder Where ParentID is null order by stkorder";
                //query result
                $rsstk = mysql_query($querystk) or die();
                //fetch result from rsstk
                while ($rowstk = mysql_fetch_array($rsstk)) {
                    //check sel_stk
                    if ($sel_stk == $rowstk['stkid']) {
                        $sel = "selected='selected'";
                    } else {
                        $sel = "";
                    }
                    //populate stk_sel combo?>
                    <option value="<?php echo $rowstk['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowstk['stkname']; ?></option>
                    <?php
                }
                ?>
            </select>
        </td>
        <td><div class="sb1NormalFont">Province/Region</div>
            <select name="prov_sel" id="prov_sel" class="input_select">
                <option value="all">All</option>
                <?php
                //province query
                //gets
                //prov_title
                $queryprov = "SELECT tbl_locations.PkLocID as prov_id, tbl_locations.LocName as prov_title
						FROM tbl_locations where LocLvl=2 and parentid is not null";
                //result
                $rsprov = mysql_query($queryprov) or die();
                //fetch result from rsprov
                while ($rowprov = mysql_fetch_array($rsprov)) {
                    if ($sel_prov == $rowprov['prov_id']) {
                        $sel = "selected='selected'";
                    } else {
                        $sel = "";
                    }
                    //populate prov_sel combo?>
                    <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                    <?php
                }
                ?>
                /select>
        </td>
        <td><div class="sb1NormalFont">Product</div>
            <select name="prod_sel" id="prod_sel" class="input_select" style="width:120px">
                <option value="all">All</option>
                <?php
                //product query
                //gets
                //item id
                //item name
                $querypro = "SELECT itmrec_id,itm_id,itm_name FROM itminfo_tab WHERE itm_status=1 ORDER BY frmindex";
                //result
                $rspro = mysql_query($querypro) or die();
                //fetching result from rspro
                while ($rowpro = mysql_fetch_array($rspro)) {
                    if ($rowpro['itmrec_id'] == $sel_item) {
                        $sel = "selected='selected'";
                    } else {
                        $sel = "";
                    }
                    //populate prod_sel combo?>
                    <option value="<?php echo $rowpro['itmrec_id']; ?>" <?php echo $sel; ?>><?php echo $rowpro['itm_name']; ?></option>
                    <?php
                }
                ?>
            </select>
        </td>
        <td><div class="sb1NormalFont">Level</div>
            <select name="level_sel" id="level_sel" class="input_select" style="width:120px">
                <option value="2">Province</option>
                <option value="4">District</option>
            </select>
        </td>
        <td valign="bottom">
            <input type="submit" name="go" id="go" value="Submit" class="btn btn-primary input-sm" />
        </td>
    </tr>
</td>
</tr>
</table>

<!------  End of Input parameters for Completeness Of Reporting Map      --------->