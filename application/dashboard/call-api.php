<?php
//Including Configuration file
include("../includes/classes/Configuration.inc.php");
//Login
Login();
//Including db file
include(APP_PATH . "includes/classes/db.php");

$user_id = $_SESSION['user_id'];

$t = base64_decode($_GET['t']);

list($cell1, $wh_id) = explode("|", $t);
$cell = substr_replace($cell1, "0", 0, 2);

$all_lines = array('9110041', '9110042', '9110043', '9110044', '9110045');

$qry = "SELECT
	GROUP_CONCAT(DISTINCT call_log.call_from) busylines
FROM
	call_log where created_date > date_sub(now(), interval 5 minute)";
$rs = mysql_query($qry);
$rowcount = mysql_num_rows($rs);
$busy_lines = array();
if ($rowcount > 0) {
    $row = mysql_fetch_array($rs);
    $busy_lines = explode(",", $row['busylines']);
}

$available_lines = array_diff($all_lines, $busy_lines);
$call_from = current($available_lines);
$token = "d2hhdA" . base64_encode($cell . "|175.107.60.26|1234|$call_from");

if (isset($call_from) && !empty($call_from)) {
    $qryins = "INSERT INTO call_log SET call_from=$call_from, call_to=$cell1, warehouse_id = $wh_id, created_by = $user_id";
    mysql_query($qryins);
    ?>
    <script>
        window.open('https://webcall.ntc.net.pk:8443?p=<?php echo $token; ?>','LMIS Call','width=600,height=400');
    </script>
    <?php
} else {
    echo "Currently all phone lines are busy. Please try again in 5 minutes. Thanks.";
}
?>