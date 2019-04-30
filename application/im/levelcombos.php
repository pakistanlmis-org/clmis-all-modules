<?php
/**
 * levelcombos
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Getting user level
$user_lvl = $_SESSION['UserLvl'];

//Checking user level
switch ($user_lvl) {
    case 1:
        $arrayProv = array(
            '1' => 'Central',
            //'3' => 'Division',
            '3' => 'District'
        );
        break;
    case 2:
        $arrayProv = array(
            '1' => 'Central',
            //'2' => 'Province',
            //'3' => 'Division',
            '3' => 'District'
        );
        break;
    case 3:
        $arrayProv = array(
            '1' => 'Central',
            //	'6' => 'Division',
            '3' => 'District'
        );
        break;
    default:
        $arrayProv = array(
            '1' => 'Central',
            //'6' => 'Division',
            '3' => 'District'
                //'8' => 'Tehsil-Taluka',
                //'9' => 'UC'
        );
        break;
}
?>
<div class="control-group span12">
    <div class="span3">
        <label class="control-label" for="office" class="span7">Stakeholder <span class="red">*</span></label>
        <div class="controls">
            <select name="mainstkid" id="mainstkid" class="span10">
                <option value="">Select</option>
                <?php
                //Get Main Stakeholder
                //Gets
                //stkid
                //stkname
                $getMainStakeholder = 'SELECT
									stakeholder.stkid,
									stakeholder.stkname
									FROM
									stakeholder
									WHERE
									stakeholder.ParentID IS NULL AND
									stakeholder.stk_type_id <> 2
									order by stakeholder.stkname ASC';
                $resMainStk = mysql_query($getMainStakeholder) or die('Error MainStakeholder');
                $arryStk = mysql_fetch_assoc($resMainStk);
                while ($arryStk = mysql_fetch_assoc($resMainStk)) {
                    $sel = '';
                    if ($mainStkId == $arryStk['stkid']) {
                        $sel = 'selected="selected"';
                    }
                    ?>
                    <option value="<?php echo $arryStk['stkid']; ?>" <?php echo $sel; ?>><?php echo $arryStk['stkname']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="span3">
        <label class="control-label" for="office" class="span7">Office <span class="red">*</span></label>
        <div class="controls">
            <select name="office" id="office" class="span10"  required="true">
                <option value="">Select</option>
                <?php foreach ($arrayProv as $key => $value) { ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="span3" id="wh_combo" style="display:none;">
        <label class="control-label" id="wh_l">Issue To <span class="red">*</span></label>
        <div class="controls">
            <select name="warehouse" id="warehouse" class="span10"  required="true">
            </select>
        </div>
    </div>
    <?php if ($button == 'true') { ?>
        <div class="span3" id="wh_link" style="display:none;">
            <label class="control-label" id="wh_l">&nbsp;</label>
            <span id="wh_button"></span>
        </div>
    <?php } ?>
    <div class="span1" id="loader" style="display:none;"><img src="<?php echo ADMIN_IMGS; ?>/loader.gif" style="margin-top:8px; float:left" id="loader" alt="" />	</div>
</div>
<input type="hidden" name="user_level" id="user_level" value="<?php echo $user_lvl; ?>" />