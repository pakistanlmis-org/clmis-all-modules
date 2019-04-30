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

$user_id = $created_by = $modified_by = $_SESSION['user_id'];
$dist_id=$_REQUEST['dist_id'];
$stk_id=$_REQUEST['stk_id'];
$userSql = "SELECT
	sysuser_tab.sysusr_email,
sysuser_tab.usrlogin_id,
sysuser_tab.sysusr_name,
sysuser_tab.sysusr_cell,
sysuser_tab.sysusr_ph,
sysuser_tab.sysusr_deg,
sysuser_tab.sysusr_dept,
sysuser_tab.UserID
FROM
	sysuser_tab
INNER JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
	tbl_warehouse.dist_id = $dist_id
AND stakeholder.lvl = 3
AND tbl_warehouse.stkid = $stk_id
AND sysuser_tab.user_level = 3
AND (sysusr_email <> '')
AND sysuser_tab.sysusr_type <> 23
LIMIT 1";
//print_r($userSql);exit;
$userResult = mysql_query($userSql) or die("Error " . $userSql);
$rowUser = mysql_fetch_assoc($userResult);
$sysusr_name = $rowUser['sysusr_name'];
$sysusr_email = $rowUser['sysusr_email'];
$sysusr_deg = $rowUser['sysusr_deg'];
$sysusr_dept = $rowUser['sysusr_dept'];
$sysusr_cell = $rowUser['sysusr_cell'];
$u_id=$rowUser['UserID'];
if (isset($_REQUEST['submit']) && !empty($_REQUEST['submit'])) {

    $button = $_REQUEST['submit'];

    if ($button == 'contact') {
        $sysusr_name = $_POST['name'];
        $sysusr_deg = $_POST['office'];
        $sysusr_email = $_POST['email'];
        $sysusr_dept = $_POST['department'];
        $sysusr_cell = $_POST['cellnumber'];        
        $created_date = $modified_date = date("Y-m-d H:i:s");

        $strSqlUpd = "UPDATE sysuser_tab
INNER JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid SET sysusr_name = '$sysusr_name', sysusr_email = '$sysusr_email' , sysusr_deg = '$sysusr_deg' ,sysusr_dept = '$sysusr_dept' ,sysusr_cell = '$sysusr_cell',sysusr_ph = '$sysusr_cell' 
 
WHERE
		sysuser_tab.UserID=$u_id
 ";
//        print_r($strSqlUpd);
        mysql_query($strSqlUpd);
        
        $_SESSION['e'] = 'contact';        
    }   
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
if (isset($_SESSION['e']) && $_SESSION['e'] == 'feedback') {
    ?>
    <div class="note note-danger">

        <p style="color:red">
            Thanks for your Feedback! 
        </p>
        
      
    </div>
<?php unset($_SESSION['e']); }
?>
        
                <?php
if (isset($_SESSION['e']) && $_SESSION['e'] == 'contact') {
    ?>
    <div class="note note-danger">

        <p style="color:red">
            Thanks for updating your contact information!
        </p>
        
      
    </div>
<?php unset($_SESSION['e']); }
?>
        <?php
//Including files
        //include $_SESSION['menu'];
        //include PUBLIC_PATH . "html/top_im.php";
        $user_d_id = $_SESSION['user_id'];
        $current_date = date("Y-m-d");

        $getLocsSql = "SELECT
	sysuser_tab.sysusr_email,
sysuser_tab.usrlogin_id,
sysuser_tab.sysusr_name,
sysuser_tab.sysusr_cell,
sysuser_tab.sysusr_ph,
sysuser_tab.sysusr_deg,
sysuser_tab.sysusr_dept
FROM
	sysuser_tab
INNER JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
WHERE
	tbl_warehouse.dist_id = $dist_id
AND stakeholder.lvl = 3
AND tbl_warehouse.stkid = $stk_id
AND sysuser_tab.user_level = 3
AND (sysusr_email <> '')
AND sysuser_tab.sysusr_type <> 23
LIMIT 1";
//Query result
        $res = mysql_query($getLocsSql) or die(mysql_error());
        $row = mysql_fetch_assoc($res);
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                

               
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">User Profile</h3>
                            </div>
                            <div class="widget-body">
                                <form action="" method="post" id="survey" name="survey">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">

                                                <label class="control-label" for="name">
                                                    Name
                                                </label>
                                                <input class="form-control" tabindex="1"  type="text" name="name"  id="name" required value="<?php
                                                echo $sysusr_name;
                                                ?>">

                                                <?php if (!empty($row)) { ?>
                                                    <input class="form-control"  type="hidden" name="feedback_id"  id="feedback_id" value="<?php
                                                    if (!empty($row)) {
                                                        echo $row['pk_id'];
                                                    }
                                                    ?>" > <?php } ?>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="CELL NUMBER">
                                                    Cell number
                                                </label>
                                                <input class="form-control" tabindex="2" type="text" name="cellnumber" required value="<?php
                                                echo $sysusr_cell;
                                                ?>">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="cnic">
                                                    Designation
                                                </label>
                                                <input class="form-control" tabindex="3" type="text" name="office" required value="<?php
                                                echo $sysusr_deg;
                                                ?>">

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="control-label" for="cnic">
                                                    Department
                                                </label>
                                                <input class="form-control" tabindex="4" type="text" name="department" required value="<?php
                                                echo $sysusr_dept;
                                                ?>">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label" for="email">
                                                    Email
                                                </label>
                                                <input class="form-control" type="email" tabindex="5" name="email" id="email"  required value="<?php
                                                echo $sysusr_email;
                                                ?>">

                                            </div>
                                        </div>
                                        <!--            <div class="col-md-5">
                                                        <div class="form-group">
                                                        </div>
                                                    </div>
                                        -->
                                    </div>


                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <button type ="submit" id="submit" name="submit" value="contact"  class="btn btn-primary">Update</button>
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
//include(PUBLIC_PATH . "/html/footer_template.php");
//include (PUBLIC_PATH . "/html/footer.php");
            ?>
            
            <script>
                $(document).ready(function () {

                    if ($('#yes').is(':checked')) {
                        $("#report").show();
                    }
                    if ($('#dyes').is(':checked')) {
                        $("#data_difficulty").show();
                    }
                    $("#no").click(function () {
                        $("#report").hide();
                    });
                    $("#yes").click(function () {
                        $("#report").show();
                    });
                    $("#dno").click(function () {
                        $("#data_difficulty").hide();
                    });
                    $("#dyes").click(function () {
                        $("#data_difficulty").show();
                    });
                });
            </script>
            <!-- END JAVASCRIPTS -->
            </body>
            <!-- END BODY -->
            </html>
