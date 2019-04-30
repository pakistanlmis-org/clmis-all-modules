<?php
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

$batch_no = "";


if (isset($_REQUEST['body_id'])) {
    $body_id = $_REQUEST['body_id'];

}
$qry="SELECT 
alerts_log.body 
FROM
alerts_log
WHERE
alerts_log.pk_id=$body_id";
$res=mysql_query($qry);
while ($row = mysql_fetch_array($res)) {
    echo $row['body'];
    
}

?>
