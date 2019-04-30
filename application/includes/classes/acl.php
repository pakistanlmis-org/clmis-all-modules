<?php
$actual_link = $_SERVER['REQUEST_URI'];
$link = ltrim($actual_link, '/');

$user_id = $_SESSION['user_id'];

$query = "SELECT
	role_resources.pk_id
FROM
	role_resources
INNER JOIN sysuser_tab ON sysuser_tab.sysusr_type = role_resources.role_id
INNER JOIN resources ON role_resources.resource_id = resources.pk_id
WHERE
	sysuser_tab.UserID = $user_id
AND resources.resource_name LIKE '$link'";
$num = mysql_num_rows(mysql_query($query));

if($num == 0){
    echo "<h3>Access denied, you are not authorized to access this page</h3>";
    echo "<a href=\"javascript:history.go(-1)\"><b>GO BACK</b></a>";
    exit;
}