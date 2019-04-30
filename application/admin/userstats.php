<?php
//Including Configuration file
include("../includes/classes/Configuration.inc.php");
//Login
Login();


//Including db file
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
$from_date = date("Y-m", strtotime("-6 months"));
$to_date = date("Y-m");
//Getting user_level
$level = $_SESSION['user_level'];
//Getting user_province1
$qry = "SELECT
    GROUP_CONCAT(DISTINCT sysuser_tab.UserID) UserID,
	Count(DISTINCT tbl_user_login_log.pk_id) total,
	sysuser_tab.sysusr_type,
	tbl_warehouse.stkid,
	tbl_warehouse.prov_id,
	tbl_locations.LocName,
	stakeholder.stkname,
	DATE_FORMAT(
		tbl_user_login_log.login_time,
		'%Y-%m'
	) login_time
FROM
	wh_user
INNER JOIN sysuser_tab ON sysuser_tab.UserID = wh_user.sysusrrec_id
INNER JOIN tbl_user_login_log ON sysuser_tab.UserID = tbl_user_login_log.user_id
INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
WHERE
	DATE_FORMAT(
		tbl_user_login_log.login_time,
		'%Y-%m'
	) BETWEEN '$from_date'
AND '$to_date'
AND tbl_warehouse.prov_id != 10
AND stakeholder.stk_type_id = 0
GROUP BY
	tbl_warehouse.stkid,
	tbl_warehouse.prov_id ORDER BY tbl_locations.PkLocID,stakeholder.stkid,tbl_user_login_log.login_time";
$qryRes = mysql_query($qry);
$array = array();
while ($row = mysql_fetch_assoc($qryRes)) {
    $stknames[$row['stkname']] = $row['stkname'];
    $provinces[$row['LocName']] = $row['LocName'];
    $times[date("M-Y", strtotime($row['login_time']))] = date("M-Y", strtotime($row['login_time']));
    $array[$row['LocName']][$row['stkname']] += $row['total'];
    $array['users'][$row['LocName']][$row['stkname']] = $row['UserID'];
}

usort($times, "compare_months");

function compare_months($a, $b) {
    $monthA = date_parse($a);
    $monthB = date_parse($b);

    return $monthA["month"] - $monthB["month"];
}

$qry2 = "SELECT
    GROUP_CONCAT(DISTINCT sysuser_tab.UserID) UserID,
	COUNT(DISTINCT tbl_user_login_log.pk_id) AS total,
	sysuser_tab.province,
	tbl_locations.LocName,
	DATE_FORMAT(
		tbl_user_login_log.login_time,
		'%Y-%m'
	) login_time
FROM
	sysuser_tab
INNER JOIN tbl_user_login_log ON sysuser_tab.UserID = tbl_user_login_log.user_id
INNER JOIN tbl_locations ON sysuser_tab.province = tbl_locations.PkLocID
WHERE
	sysuser_tab.sysusr_type IN (16, 19, 7, 8)
AND DATE_FORMAT(
	tbl_user_login_log.login_time,
	'%Y-%m'
) BETWEEN '$from_date'
AND '$to_date'
GROUP BY
	DATE_FORMAT(
		tbl_user_login_log.login_time,
		'%Y-%m'
	),
	sysuser_tab.province ORDER BY tbl_locations.PkLocID,tbl_user_login_log.login_time";
$qryRes2 = mysql_query($qry2);
$array2 = array();
while ($row2 = mysql_fetch_assoc($qryRes2)) {
    $provinces2[$row2['LocName']] = $row2['LocName'];
    $array2[$row2['LocName']][date("M-Y", strtotime($row2['login_time']))] += $row2['total'];
    $array2['users'][$row2['LocName']][date("M-Y", strtotime($row2['login_time']))] = $row2['UserID'];
}

?>
<style>
    .my_dash_cols{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }
    .my_dashlets{
        padding-left: 1px;
        padding-right: 0px;
        padding-top: 1px;
        padding-bottom: 0px;
    }
</style>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->


    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <?php
//Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="tabsbar">
                            <ul>
                                <li><a href="awstats.php"> <b>AWStats</b></a></li>
                                <li><a href="uptime.php"> <b>Uptime</b></a></li>
                                <li class="active"><a href="#"> <b>User Stats</b></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center">Decision makers logged on during <?php echo date("M-Y", strtotime($from_date)); ?> to <?php echo date("M-Y", strtotime($to_date)); ?></div>
                        <table class="table table-bordered table-hover table-condensed">
                            <thead style="font-size: 10px">
                                <tr>
                                    <th class="col-md-2"></th>
                                    <?php
                                    foreach ($times as $timeval) {
                                        ?>
                                        <th class="col-md-1"><?php echo $timeval; ?></th>
                                        <?php
                                    }
                                    ?>                                    
                                </tr>
                            </thead>
                            <tbody  style="font-size: 10px">
                                <?php
                                foreach ($provinces2 as $proval) {
                                    ?>
                                    <tr>
                                        <td><?php echo (($proval == "National") ? "Guest" : $proval); ?></td>
                                        <?php
                                        foreach ($times as $timeval1) {
                                            ?>
                                        <td><a onclick="window.open('show_user_list.php?ids=<?=$array2['users'][$proval][$timeval1]?>','_blank', 'scrollbars=1,width=600,height=500')"><?php echo (!empty($array2[$proval][$timeval1]) ? $array2[$proval][$timeval1] : '0'); ?></a></td>
                                            <?php
                                        }
                                        ?>  
                                    </tr>
                                    <?php
                                }
                                ?>  

                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center">Data entry users logged on during <?php echo date("M-Y", strtotime($from_date)); ?> to <?php echo date("M-Y", strtotime($to_date)); ?></div>
                        <table class="table table-bordered table-hover table-condensed">
                            <thead style="font-size: 10px">
                                <tr>
                                    <th></th>
                                    <?php
                                    foreach ($stknames as $stkval) {
                                        ?>
                                        <th class="col-md-2"><?php echo $stkval; ?></th>
                                        <?php
                                    }
                                    ?>                                    
                                </tr>
                            </thead>
                            <tbody  style="font-size: 10px">
                                <?php
                                foreach ($provinces as $proval) {
                                    ?>
                                    <tr>
                                        <td><?php echo (($proval == "National") ? "Guest" : $proval); ?></td>
                                        <?php
                                        foreach ($stknames as $stkval1) {
                                            ?>
                                        <td><a onclick="window.open('show_user_list.php?ids=<?=$array['users'][$proval][$stkval1]?>','_blank', 'scrollbars=1,width=600,height=500')"><?php echo (!empty($array[$proval][$stkval1]) ? $array[$proval][$stkval1] : '0'); ?></a></td>
                                            <?php
                                        }
                                        ?>  
                                    </tr>
                                    <?php
                                }
                                ?>  

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php
//Including footer file
    include PUBLIC_PATH . "/html/footer.php";
    ?>
</body>
<!-- END BODY -->
</html>