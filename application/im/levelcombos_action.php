<?php
/**
 * levelcombos_action
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
//Getting UserLvl
$lvl = $_SESSION['UserLvl'];
//Getting stk_id
$stk_id = $_SESSION['stk_id'];
//Checking user_province
if (!empty($_SESSION['user_province'])) {
    //Getting user_province
    $UserProvID = $_SESSION['user_province'];
}
//Checking dist_id
if (!empty($_SESSION['dist_id'])) {
    //Getting dist_id
    $UserDistID = $_SESSION['dist_id'];
}
//Checking office
if (isset($_REQUEST['office']) && !empty($_REQUEST['office'])) {
    //Getting office
    $OfficeLvl = $_REQUEST['office'];
    //Getting mainstkid
    $mainstkid = $_REQUEST['mainstkid'];
    switch ($OfficeLvl) {
        case '1': break;
        //Get Locations By Level
        case '2': $result = $objloc->GetLocationsByLevel($UserProvID, $OfficeLvl);
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
                //Get District Warehouses of Province
            case '3': $result = $objwarehouse->GetDistrictWarehousesofProvince($UserProvID, $stk_id, $mainstkid);
                ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) {
                ?>
                <option value="<?php echo $row->wh_id; ?>">
                <?php echo $row->wh_name; ?></option>
                <?php
            }
            break;
            //Get Locations By Level
        case '4': $result = $objloc->GetLocationsByLevel($UserProvID, $OfficeLvl);
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
            //Get Provincial Warehouses
        case '5': $result = $objwarehouse->GetProvincialWarehouses($UserProvID, $stk_id);
            ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) {
                ?>
                <option value="<?php echo $row->wh_id; ?>">
                <?php echo $row->wh_name; ?></option>
                <?php
            }
            break;
            //Get Divsional Warehouses of Province
        case '6': $result = $objwarehouse->GetDivsionalWarehousesofProvince($UserProvID, $stk_id);
            ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) {
                ?>
                <option value="<?php echo $row->wh_id; ?>">
                <?php echo $row->wh_name; ?></option>
                <?php
            }
            break;
            //Get District Warehouses of Province
        case '7': $result = $objwarehouse->GetDistrictWarehousesofProvince($UserProvID, $stk_id);
            ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) {
                ?>
                <option value="<?php echo $row->wh_id; ?>">
                <?php echo $row->wh_name; ?></option>
                <?php
            }
            break;
        case '8':
            //Get Tehsil Warehouses of District
            $result = $objwarehouse->GetTehsilWarehousesofDistrict($UserDistID, $stk_id);
            ?>
            <option value="">Select</option>
            <?php
            //Getting result
            while ($row = mysql_fetch_object($result)) {
                ?>
                <option value="<?php echo $row->wh_id; ?>">
                <?php echo $row->wh_name; ?></option>
                <?php
            }
            break;
        case '9':
            //Get UC Warehouses of District
            $result = $objwarehouse->GetUCWarehousesofDistrict($UserDistID, $stk_id);
            ?>
            <option value="">Select</option>
            <?php
            //Get result
            while ($row = mysql_fetch_object($result)) {
                ?>
                <option value="<?php echo $row->wh_id; ?>">
                <?php echo $row->wh_name; ?></option>
                <?php
            }
            break;
    }
}
?>