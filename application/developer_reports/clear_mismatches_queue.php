<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");

include("menu.php");
$qry_summary_dist= "
    DELETE from 
        data_mismatches
        WHERE
        data_mismatches.`status` = 'MISMATCH'
";
$Res2 =mysql_query($qry_summary_dist);
echo 'Pending queue is cleared now.';