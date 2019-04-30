<?php
//Including Configuration file
include("../includes/classes/Configuration.inc.php");
//Login
Login();


//Including db file
include(APP_PATH . "includes/classes/db.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
//month
$month = date('m', strtotime($date));
//year
$year = date('Y', strtotime($date));
//Getting user_level
$level = $_SESSION['user_level'];
//Getting user_province1
$province = $_SESSION['user_province1'];
//Getting user_district
$district = $_SESSION['user_district'];
//itemid
$itemId = 1;
//proFilter
$proFilter = 2;
//sel_stk
$sel_stk = '';
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
                                <li class="active"><a href="#"> <b>Uptime</b></a></li>
                                <li><a href="userstats.php"> <b>User Stats</b></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <iframe style="border:none;" width="100%" height="1000" src="https://stats.uptimerobot.com/LZyZoCMKQ/779326964"></iframe>
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