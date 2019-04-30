<?php
require_once("application/includes/classes/Configuration.inc.php");
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    $url = SITE_URL . $_SESSION['landing_page'];
    echo "<script>window.location='$url'</script>";
}
?>
<!DOCTYPE html>

<!--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.1.1
Version: 2.0.2
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8"/>
        <title>Contraceptive | LMIS
            <?php $title; ?>
        </title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <!-- BEGIN GLOBAL MANDATORY STYLES -->

        <link href="<?php echo PUBLIC_URL; ?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo PUBLIC_URL; ?>assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo PUBLIC_URL; ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo PUBLIC_URL; ?>assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo PUBLIC_URL; ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
        <!-- END GLOBAL MANDATORY STYLES -->

        <!-- BEGIN THEME STYLES -->
        <link href="<?php echo PUBLIC_URL; ?>assets/global/css/components.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo PUBLIC_URL; ?>assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo PUBLIC_URL; ?>assets/frontend/layout/css/style.css" rel="stylesheet" type="text/css"/>
        <!--    <link href="<?php //echo PUBLIC_URL;   ?>assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>-->
        <!-- END THEME STYLES -->
        <link rel="shortcut icon" href="favicon.ico"/>
    </head>
    <!-- END HEAD -->
    <!-- BEGIN BODY -->
    <body class="page-header-fixed" style="overflow-x:hidden;">
      
        <!-- BEGIN HEADER -->
        <div class="header navbar bgImg"> 
            <!-- BEGIN TOP NAVIGATION BAR -->
            <div class="header-inner "> 
                <!-- BEGIN LOGO --> 
                <a class="navbar-brand" href="index.php"> <img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/main_logo.png" height="63" width="327" alt="contraceptive LMIS" /> </a> 
                <!-- END LOGO --> 
                <!-- BEGIN RESPONSIVE MENU TOGGLER --> 
                <a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <img src="<?php echo PUBLIC_URL; ?>assets/img/menu-toggler.png" alt=""/> </a> 
                <!-- END RESPONSIVE MENU TOGGLER --> 
            </div>
            <!-- END TOP NAVIGATION BAR --> 
        </div>
        <!-- START SUB HEADER -->
        <div class="header-menu"> 
            <!-- BEGIN TOP NAVIGATION BAR -->
            <div class="header-inner headerorange">
                <button type="button" class="btn btn-lg btn-primary pull-right btn-head-grey" onclick="window.location.href = 'faqs.php'" >FAQs</button>
                <button type="button" class="btn btn-lg btn-primary pull-right btn-head-blue" onclick="window.location.href = 'contactus.php'">Contact Us</button>
                <span class="btn btn-lg btn-primary pull-right btn-head-orange">Contraceptive </span> </div>
            <!-- END TOP NAVIGATION BAR --> 
        </div>
        <!-- END HEADER -->
        <div class="clearfix"> </div>
        <!-- BEGIN CONTAINER -->
        <div class="page-container"> 
            <!-- BEGIN CONTENT -->
            <div class=" page-content-wrapper">
                <div class="page-content landing-content"> 
                    <!-- BEGIN BANNER -->
                    <div class="contraceptive-banner">
                        <div class="container">
                            <p class="contraceptives-landing-orange">Management Information System</p>
                            <h1><img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/contraceptives_log.png" height="71"></h1>
                            <p class="contraceptive-headerTxt">Provides upto date contraceptive logistics data for all public and private sector stakeholders</p>
                        </div>
                    </div>
                    <div class="orange-bar"><span class="blue-bar pull-right"></span><span class="grey-bar pull-right"></span></div>
                    <!-- END BANNER --> 
                    <!-- BEGIN LOGIN --> 
                    <script language="JAVASCRIPT" type="TEXT/JAVASCRIPT">
                        //Start Validation Function for Not Guest User Login
                        function CheckUser()
                        {
                        if(document.LogIn.login.value=="")
                        {
                        alert("Please Enter Your Login ID")
                        document.LogIn.login.focus();
                        return false
                        }

                        if(document.LogIn.pass.value=="")
                        {
                        alert("Please Enter Your Password")
                        document.LogIn.pass.focus();
                        return false
                        }
                        }
                        //End Validation Function for Not Guest User Login
                    </script> 

                    <!-- BEGIN FORM-->
                    <div class="row login-area">
                        <?php
                        if (!empty($_SESSION['err'])) {
                            ?>
                            <p style="text-align: center; margin-left: -103px; color: #c40040;" class="col-md-12"><?php echo $_SESSION['err']; ?></p>
                            <?php
                            unset($_SESSION['err']);
                        }
                        ?>
                        <form name="LogIn" id="LogIn" action="application/default/index.php" method="post" onsubmit="return CheckUser()" role="form">
                            <input name="lmis" type="hidden" value="0"  />
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="login-img"> <img src="<?php echo PUBLIC_URL; ?>assets/global/img/landing-images/contraceptives-images/contraceptives_login.png" height="48" width="172" alt="Contraceptive Login" class="img-responsive"><br>
                                        <a href="whatsnew.php"><img src="<?php echo PUBLIC_URL; ?>assets/global/img/landing-images/whatsnew.png" height="48" width="172" alt="Whatsnew" class="img-responsive"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="LgID" class="control-label">Username <span class="require">*</span></label>
                                    <div class="input-group col-md-12">
                                        <input type="text" class="form-control" id="login" name="login">
                                    </div>
                                </div>
                                <p>For limited access username: guest and password: <strong>guest</strong></p>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="LgPW" class="control-label">Password <span class="require">*</span></label>
                                    <div class="input-group col-md-12">
                                        <input type="password" class="form-control" id="pass" name="pass">
                                    </div>
                                </div>
                                <div align="center" id="errMsg" style="color:#060">
                                    <?php
                                    if (isset($_GET['msg'])) {
                                        echo $_GET['msg'];
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-form-orange"><i class="icon-ok"></i> Login</button>
                                    <button type="button" class="btn btn-default btn-form-grey" onclick="javascript: alert('Please contact on this email address: support@lmis.gov.pk');">Forget Password</button>
                                    <!--<button type="button" class="btn btn-default btn-form-grey hide" onclick="window.location.href = 'application/admin/register.php'" >Register Here</button> -->

                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- END FORM--> 

                    <!-- END LOGIN --> 
                    <!-- BEGIN PROVANCE --><!-- END PROVANCE --> 
                    <!-- BEGIN FEATURES -->
                    <div class="row feature-area">
                        <div class="col-md-12">
                            <h3 class="feature-heading"> Achieving Logistics Data Visibility and Availability of Health Products at all levels through <span> LMIS!</span></h3>
                            <ul class="nav nav-tabs nav-justified list-feature">
                                <li><img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/health_icon.png" height="63" width="76">
                                    <p>Are you interested in improving supply management of health commodities?</p>
                                </li>
                                <li><img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/evidence_icon.png" height="72" width="76">
                                    <p>Are you making informed, evidence-based decisions?</p>
                                </li>
                                <li><img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/committment_icon.png" height="70" width="82">
                                    <p>Are you committed to improving collaboration among resource providers</p>
                                </li>
                                <li><img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/role_icon.png" height="70" width="86">
                                    <p>Are you playing a role in forecasting, quantification and supply planning?</p>
                                </li>
                                <li><img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/emergency_icon.png" height="70" width="93">
                                    <p>Are you preventing supply emergencies?</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- END FEATURES --> 
                    <!-- BEGIN  PAGE CONTENT -->
                    <div class="row content-area">
                        <div class="col-md-12">
                            <h3>Strengthening Supply Chain Logistics Management</h3>
                            <p>Strengthening the Logistics Management Information Systems (LMIS) is an important objective in addressing the challenges of health commodities distribution in Pakistan. A systematic architected and rational approach was applied to define the country&rsquo;s needs for supply chain and logistics information management and most effective technology solutions were identified to address those needs. The LMIS is designed and implemented to be a sustainable source for health commodity supply chain monitoring and for informed decision making.</p>
                            <h4 class="heading-orange">Logistics Management Information System for contraceptive (cLIMS)</h4>
                            <ul class="nav nav-tabs nav-justified content-features">
                                <li><span class="heading-orange">01</span>
                                    <p>The LMIS is a web-based health commodity information system catering the logistics management of health products for both population and health sectors.</p>
                                    <img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/tick_icon.png" height="40" width="43"> </li>
                                <li><span class="heading-orange">02</span>
                                    <p>The LMIS helps the decision makers in planning, quantification, procurement and distribution of health commodities to the end user level.</p>
                                    <img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/tick_icon.png" height="40" width="43"></li>
                                <li><span class="heading-orange">03</span>
                                    <p>The LMIS helps to ensure commodity security through historical and up-to-date data from public and private stakeholders across the country.</p>
                                    <img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/tick_icon.png" height="40" width="43"></li>
                                <li><span class="heading-orange">04</span>
                                    <p>The LMIS provides continuous monitoring and availability of health products at all levels from national, provincial, districts and health facilities in both public and private sector thus preventing stock-outs and expiries to prevent No Commodity No Program situation.</p>
                                    <img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/tick_icon.png" height="40" width="43"></li>
                            </ul>
                        </div>
                    </div>
                    <!-- END PAGE CONTENT --> 

                    <!-- BEGIN  PATNERS -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="tab-content stake-holder">stakeholders</div>
                            <ul class="nav nav-tabs stake-holder-list" style="float: left;">
                                <li> <img src="<?php echo PUBLIC_URL; ?>assets/frontend/layout/img/contraceptive-partners/govt-of-pak-logo.png" alt=""/></li>
                                <li> <img src="<?php echo PUBLIC_URL; ?>assets/frontend/layout/img/contraceptive-partners/us-aid-logo.png" alt=""/></li>
                            </ul>
                            <!--<div class="usdeliver-logo" style="float:right; padding-top: 35px; padding-right: 20px;"><img alt="" src="<?php echo PUBLIC_URL; ?>assets/frontend/layout/img/contraceptive-partners/usa-delivered-logo.png"></div>-->
                        </div>
                    </div>
                    <!-- END PATNERS --> 
                </div>
            </div>
            <!-- END CONTENT --> 
        </div>
        <!-- END CONTAINER --> 
        <!-- BEGIN FOOTER -->
        <div class="footer">
            <div class="footer-inner" style="width:95%;">
                <p style="float:left;">For any comments and suggestions please write to <a href="mailto:support@lmis.gov.pk" style="color:#FFF;">support@lmis.gov.pk</a></p>
                <a href="/index/release-notes" style="color:white;  padding-left: 600px;">
                    Release notes
                </a>
                |
                <p style="float:right;"><a style="color:white;" href="http://lmis.gov.pk">http://lmis.gov.pk</a></p>
            </div>
            <div class="footer-tools"> <a href="#"><span class="go-top"> <i class="fa fa-angle-up"></i> </span></a> </div>
        </div>
        <!-- END FOOTER --> 
        <script>
            (function (i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-76834262-1', 'auto');
            ga('send', 'pageview');

        </script>
        <script data-jsd-embedded data-key="761b7cb7-aad3-4bd1-9bf6-60108d090c0a" data-base-url="https://embedded-jsd.atlassian.io" src="https://embedded-jsd.atlassian.io/assets/embed.js"></script>
    </body>
    <!-- END BODY -->
</html>