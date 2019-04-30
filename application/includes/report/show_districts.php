<?php
/**
 * show_districts
 * @package includes/reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include adminhtml.inc
include("../../html/adminhtml.inc.php");
//check province_id
if (isset($_REQUEST['province_id'])) {
    echo 'District: <select name="districts" id="districts" class="input_select">';
    echo '<option value="">Select District</option>';
    $getDistricts = mysql_query("SELECT tbl_locations.PkLocID, tbl_locations.LocName
								FROM
									tbl_locations
								WHERE
									tbl_locations.ParentID = '" . $_REQUEST['province_id'] . "' ORDER BY tbl_locations.LocName");
    while ($rowDist = mysql_fetch_array($getDistricts)) {
        if ($_SESSION['districts'] == $rowDist['PkLocID']) {
            $sel = "selected='selected'";
        } else {
            $sel = "";
        }
        ?>
        <option value="<?php echo $rowDist['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowDist['LocName']; ?></option>
        <?php
    }
    echo '</select>';
}
?>