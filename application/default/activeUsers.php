<?php

/**
 * changePassUser
 * @package default
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including files
include("../includes/classes/AllClasses.php");

//$province = $_SESSION['user_province1'];
//$stk = $_SESSION['user_stakeholder1'];
//AND sysuser_tab.stkid = $stk
//AND sysuser_tab.province = $province
$date = date("Y-m-d");

$strSql = "SELECT DISTINCT
	sysuser_tab.sysusr_name,
	sysuser_tab.sysusr_deg,
	sysuser_tab.sysusr_dept
FROM
	tbl_user_login_log
INNER JOIN sysuser_tab ON tbl_user_login_log.user_id = sysuser_tab.UserID
WHERE
	tbl_user_login_log.login_time >= NOW() - INTERVAL 2 HOUR
ORDER BY
	tbl_user_login_log.pk_id DESC";

$rsSql = mysql_query($strSql) or die("Error");
//query result
?>
    <table class="table table-condensed table-hover" cellpadding="4" cellspacing="0" border="1">
        <tr>
            <th nowrap>S.No</th>
            <th nowrap>Username</th>
            <th nowrap>Designation</th>
            <th nowrap>Department</th>        
        </tr>
        <?php
        $count = 1;
        while($row = mysql_fetch_array($rsSql)){
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><?php echo $row['sysusr_name']; ?></td>
                <td><?php echo $row['sysusr_deg']; ?></td>
                <td><?php echo $row['sysusr_dept']; ?></td>
            </tr>
            <?php
            $count++;
        }
        ?>
    </table>
