<?php
/**
 * ajax_stk
 * @package maps/api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../../includes/classes/Configuration.inc.php");
//include db
include(APP_PATH . "includes/classes/db.php");

//get type
$type = $_REQUEST["type"];
if ($type == '1') {
    $stkFilter = 'AND stk_type_id = 1';
    $stk = '5';
} else if ($type == '0') {
    $stkFilter = ' AND stk_type_id = 0';
    $stk = '1';
} else {
    $stkFilter = ' AND stk_type_id IN (0, 1)';
}


//query
//gets
//stkid,
//stkname
$query = "SELECT stkid,stkname FROM stakeholder Where ParentID is null $stkFilter order by stkorder";

$rsstk = mysql_query($query);
echo '<option value="all">All</option>';
//fetch data from rsstk
while ($result = mysql_fetch_array($rsstk)) {
    if ($stk == $result['stkid']) {
        $sel = "selected='selected'";
    } else {
        $sel = "";
    }
    ?>
    <option value="<?php echo $result['stkid']; ?>" <?php echo $sel; ?> ><?php echo $result['stkname']; ?></option>
    <?php
}
?>