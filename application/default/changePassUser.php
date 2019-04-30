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
require("../includes/classes/clsLogin.php");

$objLogin = new clsLogin();
$strMsg = NULL;
if (isset($_REQUEST['Submit']) && !empty($_REQUEST['Submit'])) {
    //Getting new_pass
    $objLogin->m_strPass = md5($_REQUEST['new_pass']);
    //Getting user_id
    $objLogin->m_login = $_SESSION['user_id'];
    $rs = $objLogin->Update();
    $_SESSION['e'] = 1;
    header("location: changePassUser.php");
    exit;
}

?>
<?php
//Including file
include(PUBLIC_PATH . "/html/header.php");
?>
</head>
<!-- END HEAD -->

<!-- BEGIN body -->
<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //Including files
        include $_SESSION['menu'];
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Change Password</h3>
                            </div>
                            <div class="widget-body">
                                <form id="form1" name="form1" method="post" action="">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Old Password<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="password" name="old_pass" id="old_pass" class="form-control input-sm input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>New Password<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="password" name="new_pass" id="new_pass" class="form-control input-sm input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Confirm New Password<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="password" name="confirm_pas" id="confirm_pas" class="form-control input-sm input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name="Submit" id="Submit" value="Change Password" class="btn green" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            //Including files
            include(PUBLIC_PATH . "/html/footer_template.php");
            include (PUBLIC_PATH . "/html/footer.php");
            ?>
            <?php
            if (isset($_SESSION['e'])) {
                ?>
                <script>
                    var self = $('[data-toggle="notyfy"]');
                    notyfy({
                        force: true,
                        text: 'Password changed successfully',
                        type: 'success',
                        layout: self.data('layout')
                    });
                </script>
                <?php
                //Unset session
                unset($_SESSION['e']);
            }
            ?>
            <!-- END JAVASCRIPTS -->
            </body>
            <!-- END BODY -->
            </html>