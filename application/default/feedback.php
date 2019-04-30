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
$userSql = "select * from sysuser_tab where UserID = $user_id";
$userResult = mysql_query($userSql) or die("Error " . $userSql);
$rowUser = mysql_fetch_assoc($userResult);
$sysusr_name = $rowUser['sysusr_name'];
$sysusr_email = $rowUser['sysusr_email'];
$sysusr_deg = $rowUser['sysusr_deg'];
$sysusr_dept = $rowUser['sysusr_dept'];
$sysusr_cell = $rowUser['sysusr_cell'];

if (isset($_REQUEST['submit']) && !empty($_REQUEST['submit'])) {

    $button = $_REQUEST['submit'];

    if ($button == 'contact') {
        $sysusr_name = $_POST['name'];
        $sysusr_deg = $_POST['office'];
        $sysusr_email = $_POST['email'];
        $sysusr_dept = $_POST['department'];
        $sysusr_cell = $_POST['cellnumber'];        
        $created_date = $modified_date = date("Y-m-d H:i:s");

        $strSqlUpd = "Update sysuser_tab SET sysusr_name = '$sysusr_name', sysusr_email = '$sysusr_email' , sysusr_deg = '$sysusr_deg' ,sysusr_dept = '$sysusr_dept' ,sysusr_cell = '$sysusr_cell',sysusr_ph = '$sysusr_cell' where UserID = $user_id";
        mysql_query($strSqlUpd);
        
        $_SESSION['e'] = 'contact';        
    } else {
        if (empty($_POST['data_difficulty'])) {
            $Q1_data_difficulty = 'NULL';
        } else {
            $Q1_data_difficulty = $_POST['data_difficulty'];
        }
        if (empty($_POST['report'])) {
            $Q2_report = 'NULL';
        } else {
            $Q2_report = $_POST['report'];
        }
        
        $comment = $_POST['comment'];
        $Q1_Y_N = $_POST['y_n_data_difficulty'];
        $Q2_Y_N = $_POST['y_n_report'];
        $user_id = $created_by = $modified_by = $_SESSION['user_id'];
        $created_date = $modified_date = date("Y-m-d H:i:s");
        //echo $name;
        if (!empty($_POST['feedback_id'])) {
            $feed_back_id = $_POST['feedback_id'];
            $strSql = "Update survey SET name = '$sysusr_name', email = '$sysusr_email' , office = '$sysusr_deg' ,department = '$sysusr_dept' ,cell_number = $sysusr_cell,q1_data_difficulty = '$Q1_data_difficulty',q2_report = '$Q2_report',comment =' $comment',q1_y_n = '$Q1_Y_N',q2_y_n = '$Q2_Y_N',  modified_by = '$modified_by', modified_date ='$modified_date' where pk_id = '$feed_back_id'";
            mysql_query($strSql) or die("Error " . $strSql);
        } else {
            $strSql = ("INSERT INTO survey (name, email, office,department,cell_number,q1_data_difficulty,q2_report,comment,q1_y_n,q2_y_n, user_id, created_by, created_date, modified_by, modified_date) VALUES ('$sysusr_name', '$sysusr_email', '$sysusr_deg','$sysusr_dept','$sysusr_cell','$Q1_data_difficulty','$Q2_report','$comment','$Q1_Y_N','$Q2_Y_N', '$user_id', '$created_by', '$created_date', $modified_by, '$modified_date')");
            mysql_query($strSql) or die("Error " . $strSql);
        }
        $_SESSION['e'] = 'feedback';
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
                survey.pk_id,
                survey.`name`,
                survey.email,
                survey.department,
                survey.office,
                survey.cell_number,
                survey.q1_data_difficulty,
                survey.q2_report,
                survey.`comment`,
                survey.q1_y_n,
                survey.q2_y_n,
                survey.user_id,
                survey.created_by,
                survey.created_date,
                survey.modified_by,
                survey.modified_date
                FROM
                survey
                where survey.user_id = '$user_d_id'
                and DATE_FORMAT(survey.created_date,'%Y-%m-%d') = '$current_date'";
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
                                <h3 class="heading">Please give us your valuable feedback!</h3>
                            </div>
                            <div class="widget-body">
                                <form action="" method="post" id="survey" name="survey">


                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?php
                                                if (!empty($row)) {
                                                    if ($row['q1_y_n'] == 1) {
                                                        $q1_checked1 = 'checked="checked"';
                                                        $q1_checked2 = "";
                                                        $q1_checked = "";
                                                    } else if ($row['q1_y_n'] == 0) {
                                                        $q1_checked2 = 'checked="checked"';
                                                        $q1_checked1 = "";
                                                        $q1_checked = "";
                                                    }
                                                } else {
                                                    $q1_checked = 'checked="checked"';
                                                    $q1_checked1 = "";
                                                    $q1_checked2 = "";
                                                }
                                                ?>
                                                <label class="control-label" for="data_difficulty">
                                                    Do you have any difficulty in data entry?<br>
                                                    <input type="radio" name="y_n_data_difficulty" value="1" id="dyes" <?php echo $q1_checked1; ?> > Yes
                                                    <input type="radio" name="y_n_data_difficulty" value="0" id="dno" <?php
                                                    echo $q1_checked;
                                                    echo $q1_checked2;
                                                    ?>> No<br>
                                                </label>
                                                <br>
                                                <textarea style="display:none;" tabindex="6" class="form-control" name="data_difficulty"id="data_difficulty"cols="80" rows="3"> <?php
                                                    if (!empty($row)) {
                                                        echo $row['q1_data_difficulty'];
                                                    }
                                                    ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <?php
                                                if (!empty($row)) {
                                                    if ($row['q2_y_n'] == 1) {
                                                        $q2_checked1 = 'checked="checked"';
                                                        $q2_checked2 = "";
                                                        $q2_checked = "";
                                                    } else if ($row['q2_y_n'] == 0) {
                                                        $q2_checked2 = 'checked="checked"';
                                                        $q2_checked1 = "";
                                                        $q2_checked = "";
                                                    }
                                                } else {
                                                    $q2_checked = 'checked="checked"';
                                                    $q2_checked1 = "";
                                                    $q2_checked2 = "";
                                                }
                                                ?>
                                                <label class="control-label" for="report">
                                                    Do you use any report?<br>
                                                    <input type="radio" name="y_n_report" value="1" id="yes"  <?php echo $q2_checked1; ?>> Yes
                                                    <input type="radio" name="y_n_report" value="0" id="no" <?php
                                                    echo $q2_checked;
                                                    echo $q2_checked2;
                                                    ?>> No<br>
                                                </label>

                                                <textarea style="display:none;" class="form-control" name="report" id="report" cols="80" rows="3"> <?php
                                                    if (!empty($row)) {
                                                        echo $row['q2_report'];
                                                    }
                                                    ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label" for="comment">
                                                    Any Comment / Improvement / Problems
                                                </label>
                                                <textarea class="form-control" name="comment" cols="80" rows="3"> <?php
                                                    if (!empty($row)) {
                                                        echo $row['comment'];
                                                    }
                                                    ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <button type ="submit" id="submit" name="submit" value="feedback"  class="btn btn-primary"><?php if (!empty($row)) { ?>Update<?php } else { ?>SAVE<?php } ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
