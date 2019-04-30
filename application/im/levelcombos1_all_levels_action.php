<?php
/**
 * levelcombos1_all_levels_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
$stk_id = '';
//Checking combo1
if (isset($_REQUEST['combo1']) && !empty($_REQUEST['combo1'])) {
    //Getting office
    $office = $_REQUEST['office'];
    //Getting combo1
    $combo1 = $_REQUEST['combo1'];
    //Getting mainstkid
    $mainstk = $_REQUEST['mainstkid'];
    switch ($office) {
        case 2:
            //Get Provincial Warehouses
            $result = $objwarehouse->GetProvincialWarehouses($combo1, $stk_id, $mainstk);
            ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) {
                if ($_SESSION['user_warehouse'] != $row->wh_id) {
                    ?>
                    <option value="<?php echo $row->wh_id; ?>">
                    <?php echo $row->wh_name . ' &#8212; ' . $row->stkname; ?>
                    </option>
                        <?php
                    }
                }
                break;

            case 3: 
                //Get District Warehouses of Province
                $result = $objwarehouse->GetDistrictWarehousesofProvince($combo1, $stk_id, $mainstk);
                ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) {
                if ($_SESSION['user_warehouse'] != $row->wh_id) {
                    ?>
                    <option value="<?php echo $row->wh_id; ?>" <?php echo (isset($_SESSION['lastTransWH']) && $row->wh_id == $_SESSION['lastTransWH']) ? 'selected="selected"' : ''; ?>><?php echo $row->wh_name . ' &#8212; ' . $row->stkname; ?></option>
                    <?php
                }
            }
            break;

        case 4: 
            //Get Field Warehouses of Province
            $result = $objwarehouse->GetFieldWarehousesofProvince($combo1, $stk_id, $mainstk);
            ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) {
                if ($_SESSION['user_warehouse'] != $row->wh_id) {
                    ?>
                    <option value="<?php echo $row->wh_id; ?>">
                    <?php echo $row->wh_name . ' &#8212; ' . $row->stkname; ?></option>
                    <?php
                }
            }
            break;

        case 6: 
            //Get Locations By Level By Province
            $result = $objloc->GetLocationsByLevelByProvince($combo1, 4);
            ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) {
                ?>
                <option value="<?php echo $row->PkLocID; ?>">
                <?php echo $row->LocName; ?></option>
                <?php
            }
            break;

        case 7: 
            //Get Locations By Level
            if(!empty($_SESSION['user_level']) && $_SESSION['user_level'] >=3 && !empty($_SESSION['user_district']))
            {
                $objloc->PkLocID = $_SESSION['user_district'];
                $result = $objloc->GetLocationById();
            }
            else{
                $result = $objloc->GetLocationsByLevel($combo1, 3);
            }
            ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) {
                {
                    ?>
                    <option value="<?php echo $row->PkLocID; ?>" <?php echo (isset($_SESSION['lastTransDist']) && $row->PkLocID == $_SESSION['lastTransDist']) ? 'selected="selected"' : ''; ?>><?php echo $row->LocName; ?></option>
                    <?php
                }
            }
            break;

        case 8:
            //Get Locations By Level
            $result = $objloc->GetLocationsByLevel($combo1, 3);
            ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) { {
                    ?>
                    <option value="<?php echo $row->PkLocID; ?>"><?php echo $row->LocName; ?></option>
                    <?php
                }
            }
            break;
    }
}
?>