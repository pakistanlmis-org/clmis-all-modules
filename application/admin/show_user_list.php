<?php
//Including Configuration file
include("../includes/classes/Configuration.inc.php");
//Login
Login();

//Including db file
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
$users = $_GET['ids'];

$qry = "SELECT
	sysuser_tab.sysusr_name
FROM
	sysuser_tab
WHERE
	sysuser_tab.UserID IN ($users)";
$qryRes = mysql_query($qry);
?>
<table class="table table-striped table-hover table-condensed">
    <thead style="font-size: 10px">
        <tr>
            <th class="col-md-2">S.No</th>
            <th class="col-md-2">Username</th>
        </tr>
    </thead>
    <tbody  style="font-size: 10px">
        <?php
            $count = 1;
            while ($row = mysql_fetch_assoc($qryRes)) {
            ?>
        <tr>            
            <td><?php echo $count; ?></td>
            <td><?php echo $row['sysusr_name']; ?></td>            
        </tr>
        <?php
            $count++;
            }
            ?>
    </tbody>
</table>