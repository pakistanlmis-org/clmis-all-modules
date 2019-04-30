<?php 
function startHtml($title = ""){?>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta charset="utf-8"/>
        <title>Contraceptive | <?php echo $title; ?></title>
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
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="<?php echo PUBLIC_URL; ?>assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css"/>
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME STYLES -->
        <link href="<?php echo PUBLIC_URL; ?>assets/global/css/components.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo PUBLIC_URL; ?>assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo PUBLIC_URL; ?>assets/frontend/layout/css/style.css" rel="stylesheet" type="text/css"/>
        <!--    <link href="<?php echo PUBLIC_URL; ?>assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>-->
        <!-- END THEME STYLES -->
        <link rel="shortcut icon" href="favicon.ico"/>

    </head>
	
<?php }

function siteMenu($page) {?>
	<body class="page-header-fixed">
<!-- BEGIN HEADER -->
<div class="header navbar bgImg">
    <!-- BEGIN TOP NAVIGATION BAR -->
    <div class="header-inner ">
        <!-- BEGIN LOGO -->
        <a class="navbar-brand" href="index.php">
            <img src="<?php echo PUBLIC_URL; ?>assets/img/landing-images/contraceptives-images/main_logo.png" height="63" width="327" alt="vaccine LMIS" />
        </a>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <img src="<?php echo PUBLIC_URL; ?>assets/img/menu-toggler.png" alt=""/>
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
    </div>
    <!-- END TOP NAVIGATION BAR -->
</div>
<!-- START SUB HEADER -->
<div class="header-menu">
    <!-- BEGIN TOP NAVIGATION BAR -->
    <div class="header-inner headerorange">
        <button type="button" class="btn btn-lg btn-primary pull-right btn-head-grey" onClick="window.location.href='faqs.php'" >FAQs</button>
        <button type="button" class="btn btn-lg btn-primary pull-right btn-head-blue" onClick="window.location.href='contactus.php'">Contact Us</button>
        <span class="btn btn-lg btn-primary pull-right btn-head-orange">Contraceptive </span>
    </div>
    <!-- END TOP NAVIGATION BAR -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<?php }
function contents($contents, $title){?>
<div class="page-container">
    <!-- BEGIN CONTENT -->
    <div class=" page-content-wrapper">
        <div class="row">
           <div class="col-md-12">
               <div class="row content-area">
                   <h3 class=""> <?php echo $title;?> </h3>
               </div>
               <?php if($title == "Contact Us"){ echo "Comming Soon";}else{ echo $contents; }?>
           </div>
        </div>

   		</div>
    </div>
<?php }

function leftContents($contents, $title){?>
	<div class="wrraper">
       <div class="content">
       	<div class="content-left">
        	<?php if ($title == "Contact US"){?>
			            <div style="padding-left:40px; font:Verdana, Geneva, sans-serif; font-weight:bold; font-size:15px;"><?php echo $title;?></div>
            <?php }else {?>
						<div style="font:Verdana, Geneva, sans-serif; font-weight:bold; font-size:15px;"><?php echo $title;?></div>
					<?php }?> 
		<?php echo $contents;?> 
        </div>          
<?php }
function rightContents(){?>
	<script language="JAVASCRIPT" type="TEXT/JAVASCRIPT">
<!--
	//Start Validation Function for Not Guest User Login
	function CheckUser()
	{
		if(document.LogIn.LgID.value=="")
			{
				alert("Please Enter Your Login ID")
				document.LogIn.LgID.focus();
				return false
			}
		
		if(document.LogIn.LgPW.value=="")
			{
				alert("Please Enter Your Password")
				document.LogIn.LgPW.focus();
				return false
			}
	}
	//End Validation Function for Not Guest User Login
//-->
</script>


   </div>
   </div> 
<?php }
function footer(){?>
<div class="footer">
    <div class="footer-inner">
		For any comments and suggestions please write to <a href="mailto:support@lmis.gov.pk" style="color:#FFF;">support@lmis.gov.pk</a>
    </div>
    <div class="footer-tools">
		<span class="go-top">
			<i class="fa fa-angle-up"></i>
		</span>
    </div>
</div>
<?php }
function endHtml(){?>
	</body>
</html>
<?php } ?>


