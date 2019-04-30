<?php
ini_set('max_execution_time', 60);
//phpinfo();
include("../includes/classes/AllClasses.php");
require("../includes/classes/clsLogin.php");


$setting_name   = $_REQUEST['setting_name'];
$setting_value  = $_REQUEST['setting_value'];

echo $qry="  UPDATE `system_settings` SET
                    `setting_value`='".$setting_value."'
                WHERE 
                    `setting_name`='".$setting_name."';
                ";
 mysql_query($qry);