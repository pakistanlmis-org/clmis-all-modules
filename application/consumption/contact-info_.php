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
$userSql = "select * from sysuser_tab where (sysusr_email NOT REGEXP '^[a-zA-Z0-9][a-zA-Z0-9._-]*@[a-zA-Z0-9][a-zA-Z0-9._-]*\\.[a-zA-Z]{2,}$' OR sysusr_cell NOT REGEXP '^[0-9().-]{12,}$') AND UserID = $user_id";

$userResult = mysql_query($userSql) or die("Error " . $userSql);
$count = mysql_num_rows($userResult);
if ($count > 0) {
    $rowUser = mysql_fetch_assoc($userResult);
    $sysusr_name = $rowUser['sysusr_name'];
    $sysusr_email = $rowUser['sysusr_email'];
    $sysusr_deg = $rowUser['sysusr_deg'];
    $sysusr_dept = $rowUser['sysusr_dept'];
    $sysusr_cell = $rowUser['sysusr_cell'];
}
$_SESSION['e'] = '';

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
    }
}
?>
<?php
if ($count > 0) {
    ?>
    <form action="" method="post" id="survey" name="survey" onsubmit="return ValidateForm();">
        <!-- Modal -->
        <div id="myModal" class="modal fade col-md-6 center-block" style="margin-top: 20px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <h3 id="myModalLabel"><b>Please update your contact information! It's mandatory.</b></h3>
            </div>
            <div class="modal-body" style="background-color:white;">


                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="CELL NUMBER">
                                Cell number (Format: 923331234567)
                            </label>
                            <input class="form-control" tabindex="2" type="text" name="cellnumber" id="cellnumber" required value="<?php
                            echo $sysusr_cell;
                            ?>">

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label" for="cnic">
                                Designation
                            </label>
                            <input class="form-control" tabindex="3" type="text" name="office" required value="<?php
                            echo $sysusr_deg;
                            ?>">

                        </div>
                    </div>
                    <div class="col-md-6">
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

            </div>
            <div class="modal-footer">
                <?php
                if ($_SESSION['e'] == 'contact') {
                    ?>
                    <span style="color: red;">Thanks for updating your contact information! &nbsp;&nbsp;&nbsp;&nbsp;</span>
                    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
                    <script>
                        setTimeout(function () {
                            $('#myModal').modal('hide');
                        }, 3000);
                    </script>
                    <?php
                }
                ?>
                <button type = "submit" id = "submit" name = "submit" value = "contact" class = "btn btn-primary">Update</button>
            </div>
        </div>
    </form>
    <?php
}
?>
<script>
<?php
if ($count > 0) {
    ?>
        $(function () {
            $('#myModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });
    <?php
}
?>

    function ValidateForm() {
        var email = document.getElementById('email').value;
        var cellnumber = document.getElementById('cellnumber').value;

        if (ValidateEmail(email)) {
            if (validatePhone(cellnumber)) {
                return true;
            }
        }
        return false;
    }
    function ValidateEmail(mail) {
        if (!(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(mail))) {
            alert("You have entered an invalid email address!");
            document.getElementById('email').focus();
            return false;
        }
        return true;
    }
    function validatePhone(phone) {
        var error = "";
        var stripped = phone.replace(/[\(\)\.\-\ ]/g, '');

        if (stripped == "") {
            error = "You didn't enter a phone number.";
        } else if (isNaN(parseInt(stripped))) {
            phone = "";
            error = "The phone number contains illegal characters.";
        } else if (!(stripped.length == 12)) {
            phone = "";
            error = "The phone number is the wrong length. Make sure that country code is included without zero.\n";
        }

        if (error != "") {
            alert(error);
            document.getElementById('cellnumber').focus();
            return false;
        }

        return true;
    }
</script> 