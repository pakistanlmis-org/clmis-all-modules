<?php
$actual_link = $_SERVER['REQUEST_URI'];

$link = ltrim($actual_link, '/');

$query = "SELECT
        resources.pk_id,
        resources.resource_name
        FROM
        resources
        WHERE
        resources.resource_name = '$link'";
$num = mysql_num_rows(mysql_query($query));

if ($num == 1) {
    $qryRes = mysql_query($query);
    $row = mysql_fetch_array($qryRes);

    $resource_id = $row['pk_id'];
    $user_login_log_id = $_SESSION['user_login_log_id'];
    $current_date = date('Y-m-d H:i:s');
    $userid = $_SESSION['user_id'];
    $session_id = session_id();
    //Query for auth
    $strSql = "INSERT INTO  user_click_paths(user_id,resource_id,user_login_log_id,created_date) VALUES('$userid','$resource_id','$user_login_log_id','$current_date')";
    //query result
    $rsSql = mysql_query($strSql) or die("Error user_click_paths");
}
?>