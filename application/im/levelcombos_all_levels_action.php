<?php
/**
 * levelcombos_all_levels_action
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
//Checking user_province
if (!empty($_SESSION['user_province'])) {
    //Getting user_province
    $UserProvID = $_SESSION['user_province'];
}


if (isset($_REQUEST['office']) && !empty($_REQUEST['office'])) {
    //Getting office
    $office = $_REQUEST['office'];
    $objloc->office = $office;
    //Getting mainstkid
    $stk_id = $_REQUEST['mainstkid'];
    if ($office == '1') {
        $result = $objwarehouse->GetFederalWarehouses($stk_id);
        ?>
        <option value="">Select</option>
        <?php
        while ($row = mysql_fetch_object($result)) {
            ?>
            <option value="<?php echo $row->wh_id; ?>">
                <?php echo $row->wh_name; ?></option>
            <?php
        }
    } else {
        $objloc->LocLvl = 2;
        
        if(!empty($_REQUEST['show_national']))
            $objloc -> show_national=$_REQUEST['show_national'];
        //Get All Locations
        $result = $objloc->GetAllLocations();
        ?>
        <option value="">Select</option>
        <?php while ($row = mysql_fetch_object($result)) {
            ?>
            <option value="<?php echo $row->PkLocID; ?>" <?php if (!empty($_SESSION['lastTransProv']) && $row->PkLocID == $_SESSION['lastTransProv']) {
                print ' selected="selected"';
            } ?>>
                <?php echo $row->LocName; ?></option>
            <?php
        }
    }
}?>