<?php
/**
 * levelcombos_all_levels
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//user level
$user_lvl = (!empty($_SESSION['user_level']) ? $_SESSION['user_level'] : '' );

$category = '1,4';
$stk_where = " ";
if($_SESSION['user_stakeholder1'] == 145){
    $category='5';
    $stk_where = " AND stakeholder.stkid in (2,73) ";
}

if($_SESSION['user_stakeholder1'] == 2){
    $category='5';
    $stk_where = " AND stakeholder.stkid in (2,7,73) ";
}

if($_SESSION['user_level'] >= 3){
 
    $stk_where = " AND stakeholder.stkid = ".$_SESSION['user_stakeholder1']." ";
}
//page name
$_SESSION['page_name'] = basename($_SERVER['PHP_SELF']);
//check user level
switch ($user_lvl) {
    case 1:
        $arrayProv = array(
            '1' => 'National',
            '2' => 'Province',
            '3' => 'District',
            '4' => 'Field/Tehsil/Town',
            '7' => 'Health Facility',
            '8' => 'Individuals'
        );
        break;
    case 2:
        if($_SESSION['user_stakeholder1'] == 145){
            $arrayProv = array(
                '1' => 'Central',
                '2' => 'Province',
                // '3' => 'Division',
                '3' => 'District',
                '7' => 'Health Facility',
                '8' => 'Individuals'
                );
        }
        elseif($_SESSION['user_stakeholder1'] == 2){
            $arrayProv = array(
                //'1' => 'Central',
                //'2' => 'Province',
                // '3' => 'Division',
                '3' => 'District',
                '7' => 'Health Facility',
                //'8' => 'Individuals'
                );
        }
        else{
            $arrayProv = array(
                '1' => 'Central',
                '2' => 'Province',
                // '3' => 'Division',
                '3' => 'District'
                );
        }
        
        break;
    case 3:
        $arrayProv = array(
            //'1' => 'Central',
            //  '3' => 'Division',
            '3' => 'District',
            '7' => 'Health Facility',
        );
        break;
    case 4:
        $arrayProv = array(
            '3' => 'District',
            '4' => 'Tehsil/Town',
            '7' => 'Health Facility'
        );
        break;


    default:
        $arrayProv = array(
            '1' => 'Central',
            '2' => 'Province',
            //   '3' => 'Division',
            '3' => 'District'
                //  '6' => 'Union Council'
        );
        break;
}
?>
<style>
    .input-small{width:140px !important;}
</style>
<div class="control-group col-md-12">
    <div class="col-md-2">
        <label for="office">Stakeholder <span class="red">*</span></label>
        <div class="controls">
            <select name="mainstkid" id="mainstkid" class="form-control input-small">
                <option value="">Select</option>
                <?php
                //query get Main Stakeholder
                //gets
                //stakeholder.stkid,
                //stakeholder.stkname
                $getMainStakeholder = 'SELECT DISTINCT
										stakeholder.stkid,
										stakeholder.stkname
									FROM
										stakeholder
									INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkid
									WHERE
										stakeholder.ParentID IS NULL
									AND stakeholder.stk_type_id IN (0, 1, 4)
                                                                        '.$stk_where.'
									ORDER BY
										stakeholder.stkorder ASC';
                //result
                $resMainStk = mysql_query($getMainStakeholder) or die('Error MainStakeholder');
                //fetch result
                while ($arryStk = mysql_fetch_assoc($resMainStk)) {
                    $sel = '';
                    if ($_SESSION['lastTransStk'] == $arryStk['stkid']) {
                        $sel = 'selected="selected"';
                    }
                    //populate mainstkid combo
                    ?>
                    <option value="<?php echo $arryStk['stkid']; ?>" <?php echo $sel; ?>><?php echo $arryStk['stkname']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="col-md-2" style="display:none;" id="office-span">
        <label for="office" >Office <span class="red">*</span></label>
        <div class="controls">
            <select name="office" id="office" class="form-control input-small">
                <option value="">Select</option>
                <?php
                //fetch result
                foreach ($arrayProv as $key => $value) {
                    $sel = '';
                    if ($_SESSION['lastTransOffice'] == $key) {
                        $sel = 'selected="selected"';
                    }
                    //populate office combo
                    ?>
                    <option value="<?php echo $key; ?>" <?php echo $sel; ?>><?php echo $value; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="col-md-2" id="div_combo1" <?php if (empty($prov_id) || isset($office_id) == 1 || empty($office_id)) { ?> style="display:none;" <?php } else { ?> style="display:block;"<?php } ?>>
        <label class="control-label" id="lblcombo1">Province1 <span class="red">*</span></label>
        <div class="controls">
            <select name="combo1" id="combo1" class="form-control input-small">
                <option value="">Select</option>
                <?php 
                //fetch result
                while ($row = mysql_fetch_object($arrayProvince)) {
                    //populate combo1 combo
                    ?>
                    <option value="<?php echo $row->PkLocID; ?>" <?php if ($lastTransProv == $row->PkLocID) { ?>selected="selected"<?php } ?>>
                        <?php echo $row->LocName; ?></option>
                <?php }
                ?>
            </select>
        </div>
    </div>	
    <div class="col-md-2" id="div_combo2" <?php if (empty($dist_id) || isset($office_id) == 1 || empty($office_id)) { ?> style="display:none;" <?php } ?>>
        <label class="control-label" id="lblcombo2">District <span class="red">*</span></label>
        <div class="controls">
            <select name="combo2" id="combo2" class="form-control input-small">
                <option value="">Select</option>
                <?php
                //fetch result
                while ($row = mysql_fetch_object($arrayDistricts)) {
                    //populate combo2 combo
                    ?>
                    <option value="<?php echo $row->PkLocID; ?>" <?php if ($dist_id == $row->PkLocID) { ?>selected=""<?php } ?>>
                        <?php echo $row->LocName; ?></option>
                <?php }
                ?>
            </select>
        </div>
    </div>
    <!--style="display:none;"
            <div class="span3" id="div_combo3" >		
                    <label class="control-label" id="lblcombo3">Combo3</label>
                    <div class="controls">
                            <select name="combo3" id="combo3" class="span10">
                            </select>
                    </div>
            </div>
    
            <div class="span3" id="div_combo4" style="display:none;">		
                    <label class="control-label" id="lblcombo4">Combo4</label>
                    <div class="controls">
                            <select name="combo4" id="combo4" class="span10">
                            </select>
                    </div>
            </div>
            
            <div class="span3" id="div_combo5" style="display:none;">		
                    <label class="control-label" id="lblcombo5">Combo5</label>
                    <div class="controls">
                            <select name="combo5" id="combo5" class="span10">
                            </select>
                    </div>
            </div>-->
    <div class="col-md-3" id="wh_combo" <?php if (empty($warehouse)) { ?> style="display:none;" <?php } ?>>
        <label class="control-label" id="wh_l"> Store <span class="red">*</span></label>
        <div class="controls">
            <select name="warehouse" id="warehouse" class="form-control input-medium">
                <option value="">Select</option>
                <?php
                //fetch result
                while ($row = mysql_fetch_object($arrayWarehouse)) {
                    //populate warehouse combo
                    ?>
                    <option value="<?php echo $row->wh_id; ?>" <?php if ($warehouse == $row->wh_id) { ?>selected=""<?php } ?>>
                        <?php echo $row->wh_name; ?></option>
                <?php }
                ?>
            </select>
            <div class="help-block" id="store-help-block" style="font-size:10px;"><i class="glyphicon glyphicon-info-sign" style="font-size:10px;"></i> Store name &#8212; Stakeholder</div>
        </div>
    </div>
    <div class="span1" id="loader" style="display:none;"><img src="<?php echo SITE_URL; ?>plmis_img/loader.gif" style="margin-top:8px; float:left" id="loader" alt="" /></div>
</div>
<input type="hidden" name="user_level" id="user_level" value="<?php echo $user_lvl; ?>" />