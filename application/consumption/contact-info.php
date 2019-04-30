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

$_SESSION['backURL'] = $_SERVER['REQUEST_URI'];
$user_id = $created_by = $modified_by = $_SESSION['user_id'];
$userSql = "select * from sysuser_tab where (sysusr_email NOT REGEXP '^[a-zA-Z0-9][a-zA-Z0-9._-]*@[a-zA-Z0-9][a-zA-Z0-9._-]*\\.[a-zA-Z]{2,}$' OR sysusr_cell NOT REGEXP \"^(0|92|0092|\\\+92)(3)([0-9]{9})$\" OR sysusr_cell is null) AND UserID = $user_id";

$userResult = mysql_query($userSql) or die("Error " . $userSql);
$count = mysql_num_rows($userResult);
if ($count > 0) {
    $rowUser = mysql_fetch_assoc($userResult);
    $sysusr_name = $rowUser['sysusr_name'];
    $sysusr_email = $rowUser['sysusr_email'];
    $sysusr_deg = $rowUser['sysusr_deg'];
    $sysusr_dept = $rowUser['sysusr_dept'];
    $sysusr_cell = substr_replace($rowUser['sysusr_cell'],'0',0,2);
}
$_SESSION['e'] = '';

if (isset($_REQUEST['submit']) && !empty($_REQUEST['submit'])) {

    $button = $_REQUEST['submit'];

    if ($button == 'contact') {
        $sysusr_name = $_POST['name'];
        $sysusr_deg = $_POST['office'];
        $sysusr_email = $_POST['email'];
        $sysusr_dept = $_POST['department'];
        $cell = $_POST['cellnumber'];

        $sysusr_cell = '92' . substr($cell, -10);

        $created_date = $modified_date = date("Y-m-d H:i:s");

        mysql_query("INSERT INTO email_verification(user_id,email_address) VALUES($user_id,'$sysusr_email')");

        $strSqlUpd = "Update sysuser_tab SET sysusr_name = '$sysusr_name' , sysusr_deg = '$sysusr_deg' ,sysusr_dept = '$sysusr_dept' ,sysusr_cell = '$sysusr_cell',sysusr_ph = '$sysusr_cell' where UserID = $user_id";
        mysql_query($strSqlUpd);

        $_SESSION['e'] = 'verify';

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $headers .= "From: no-reply@lmis.gov.pk" . "\r\n" .
                "Reply-To: no-reply@lmis.gov.pk" . "\r\n" .
                "X-Mailer: PHP/" . phpversion();

        $t = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 5).base64_encode($sysusr_email).substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 3);
        mail($sysusr_email, "Verify your email address", "Please click on the following button to verify your email address. <p><a href='http://c.lmis.gov.pk/application/consumption/contact-info-verify.php?t=$t'><button>Verify this email</button></a></p>", $headers);
    }
}

$evrs = "select * from email_verification where user_id = $user_id AND is_verified = 0";

$evresult = mysql_query($evrs) or die("Error " . $evrs);
$countev = mysql_num_rows($evresult);
if ($countev > 0) {
    $rowUserev = mysql_fetch_assoc($evresult);
    $user_vemail = $rowUserev['email_address'];
    $_SESSION['e'] = 'verify';
}
?>
<?php
if ($count > 0) {
    ?>
    <form action="" method="post" id="survey" name="survey" onsubmit="return ValidateForm();">
        <!-- Modal -->
        <div id="myModal" class="modal fade col-md-6 center-block" style="margin-top: 20px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <?php if (empty($_SESSION['e'])) { ?>
                <div class="modal-header">
                    <h3 id="myModalLabel"><b class="red">Please update your contact information! It's mandatory.</b></h3>
                </div>
                <div class="modal-body" style="background-color:white;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">

                                <label class="control-label" for="name">
                                    Name <span class="red">*</span>
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
                                    Cell number <span class="red">*</span> (Format: 03XXXXXXXXX)
                                </label>
                                <input class="form-control" placeholder="0300xxxxxxx" tabindex="2" type="text" name="cellnumber" id="cellnumber" required value="<?php
                                echo $sysusr_cell;
                                ?>">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="cnic">
                                    Designation <span class="red">*</span>
                                </label>
                                <input class="form-control" tabindex="3" type="text" name="office" required value="<?php
                                echo $sysusr_deg;
                                ?>">

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="cnic">
                                    Department <span class="red">*</span>
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
                                    Email <span class="red">*</span>
                                </label>
                                <input class="form-control" type="email" tabindex="5" name="email" id="email"  required value="<?php
                                echo (!empty($sysusr_email) ? $sysusr_email : $_SESSION['email']);
                                ?>">

                            </div>
                        </div>
                        <!--                                <div class="col-md-5">
                                                            <div class="form-group">
                                                                <p class="red"> Please verify your email address if you already entered.</p>
                                                            </div>
                                                        </div>-->

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
                            }, 1500);
                        </script>
                        <?php
                    }
                    ?>
                    <button type = "submit" id = "submit" name = "submit" value = "contact" class = "btn btn-primary">Update</button>
                </div>

            <?php } else if ($_SESSION['e'] == 'verify') { ?>
                <div class="modal-header">
                    <h3 id="myModalLabel"><b class="red">Verify your email address</b></h3>
                </div>
                <div class="modal-body" style="background-color:white;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <p>Thanks for updating your contact information!</p>
                                <p>We now need to verify your email address. We've sent an email to <?php echo $user_vemail; ?> to verify your address. Please click the link in that email to continue.</p>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="contact-info-delink.php?id=<?php echo $user_id; ?>&e=<?php echo $user_vemail; ?>">Need to resend the email, change your address, or get help?</a>
                </div>
            <?php } ?>
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
        var new_regex = new RegExp(/^(0|92|0092|\+92)3[0-9]{9}$/);
        if (stripped == "") {
            error = "You didn't enter a phone number.";
        } else if (isNaN(stripped)) {
            phone = "";
            error = "The phone number contains illegal characters.";
        } else if (!(new_regex.test(stripped))) {
            phone = "";
            error = "Invalid phone number format. Make sure that number is in this format : 03xx xxxxxxx.\n";
        }


        $('#cellnumber').val(stripped);
        if (error != "") {
            alert(error);
            document.getElementById('cellnumber').focus();
            return false;
        }

        return true;
    }
</script> 