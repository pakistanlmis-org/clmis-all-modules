<?php
/**
 * levelcombos2_all_levels_action
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

$stk_id = 1;
//Checking combo2
if (isset($_REQUEST['combo2']) && !empty($_REQUEST['combo2'])) {
    //Getting combo2
    $combo2 = $_REQUEST['combo2'];
    //Getting office
    $office = $_REQUEST['office'];
    //Getting mainstkid
    $mainstk = $_REQUEST['mainstkid'];
    switch ($office) {
        //Get Tehsil Warehouses of District
        case 5: $result = $objwarehouse->GetTehsilWarehousesofDistrict($combo2, $stk_id);
            ?>
            <option value="">Select</option>
            <?php
            while ($row = mysql_fetch_object($result)) {
                if ($_SESSION['user_warehouse'] != $row->wh_id) {
                    ?>
                    <option value="<?php echo $row->wh_id; ?>">
                    <?php echo $row->wh_name; ?></option>
                    <?php
                }
            }
            break;
            //Get UC Warehouses of District
        case 6: $result = $objwarehouse->GetUCWarehousesofDistrict($combo2, $stk_id);
            ?>
            <option value="">Select</option>
            <?php
            while ($row = mysql_fetch_object($result)) {
                if ($_SESSION['user_warehouse'] != $row->wh_id) {
                    ?>
                    <option value="<?php echo $row->wh_id; ?>">
                    <?php echo $row->wh_name; ?></option>
                    <?php
                }
            }
            break;
            //Get Health Facility Warehouses of District
        case 7: $result = $objwarehouse->GetHealthFacilityWarehousesofDistrict($combo2, $mainstk);
            ?>
            <option value="">Select</option>
            <?php
            while ($row = mysql_fetch_object($result)) {
                if ($_SESSION['user_warehouse'] != $row->wh_id) {
                    ?>
                    <option value="<?php echo $row->wh_id; ?>" <?php echo (isset($_SESSION['lastTransWH']) && $row->wh_id == $_SESSION['lastTransWH']) ? 'selected="selected"' : ''; ?>><?php echo $row->wh_name; ?></option>
                    <?php
                }
            }
            break;
            //Get Level 8 Warehouses of District
        case 8: $result = $objwarehouse->GetLevel8WarehousesofDistrict($combo2, $mainstk);
            ?>
            <option value="">Select</option>
            <?php
            while ($row = mysql_fetch_object($result)) {
                if ($_SESSION['user_warehouse'] != $row->wh_id) {
                    ?>
                    <option value="<?php echo $row->wh_id; ?>" <?php echo (isset($_SESSION['lastTransWH']) && $row->wh_id == $_SESSION['lastTransWH']) ? 'selected="selected"' : ''; ?>><?php echo $row->wh_name; ?></option>
                    <?php
                }
            }
            break;
    }
}
?> 


