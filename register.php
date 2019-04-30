<?php
require_once("application/includes/classes/Configuration.inc.php");
$_SESSION['user_id'] = 1;
include_once('public/html/html.inc.php');
include("application/includes/classes/db.php");
include("application/includes/classes/AllClasses.php");
startHtml('Contact Us');
siteMenu("Contact Us");
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

<style>
    .green {
        color: #000;
    }
    .nav-tabs > li > a:hover, .nav-pills > li > a, .nav-pills > li > a:hover {
        color: #000;
    }
    .form-group{
        padding-top:  5px;
    }
    textarea {
        resize: none;
    }
    .col-md-offset-4_5 {
        margin-left: 29.175%;
    }
    .col-sm-offset-4_5 {
        margin-left: 29.175%;
    }
</style>
<?php
$rsStakeholders = $objstk->GetAllStakeholders();
$rsloc = $objloc->GetAllLocationsL2();
?>

<div class=" page-content-wrapper">
    <div class="page-content landing-content"><br />
        <div class="col-md-4 col-md-offset-4"> </div>
        <div class="col-md-5 col-md-offset-4_5 col-sm-offset-4_5">
            <div class="portlet box green ">
                <div class="portlet-title">
                    Register User

                </div>
                <div class="portlet-body" style="min-height:300px;">
                    <form method="post" action="application/admin/userRegisterAction.php" name="manageuser" id="manageuser" enctype='multipart/form-data'>
                        <div class="row">
                            <div class="form-group">
                                <label for="e_mail" class="col-md-2 control-label">
                                    Full Name<font color="#FF0000">*</font>
                                </label>
                                <div class="col-md-6">

                                    <input type="text" name="full_name" id='full_name' value="" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <div class="control-group">
                                        <label>User Picture</label>
                                        <div class="controls">
                                            <input type="file" name="sysusr_photo" id="sysusr_photo" class="input-medium" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="organization" class="col-md-2 control-label">
                                    Organization<font color="#FF0000">*</font>
                                </label>
                                <div class="col-md-6">


                                    <select name="Stakeholders" id="Stakeholders" class="form-control">
                                        <option value="">Select</option>
<?php
//Populate select combo
if ($rsStakeholders != FALSE && mysql_num_rows($rsStakeholders) > 0) {
    while ($RowGroups = mysql_fetch_object($rsStakeholders)) {
        ?>
                                                <option value="<?= $RowGroups->stkid ?>" > <?php echo $RowGroups->stkname; ?> </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="other" class="col-md-2 control-label">
                                    Other
                                </label>
                                <div class="col-md-6">

                                    <input type="text" name="other" id='other' value="" class="form-control">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="designation" class="col-md-2 control-label">
                                    Designation<font color="#FF0000">*</font>
                                </label>
                                <div class="col-md-6">

                                    <input type="text" name="designation" id='designation' value="" class="form-control">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="e_mail" class="col-md-2 control-label">
                                    Email<font color="#FF0000">*</font>
                                </label>
                                <div class="col-md-6">

                                    <input type="text" name="email_id" id='email_id' value="" class="form-control">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="e_mail" class="col-md-2 control-label">
                                    Contact<font color="#FF0000">*</font>
                                </label>
                                <div class="col-md-6">

                                    <input type="text" name="contact" id='contact' value="" class="form-control">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="e_mail" class="col-md-2 control-label">
                                    Level<font color="#FF0000">*</font>
                                </label>
                                <div class="col-md-6">

                                    <select name="level" id="level" class="form-control">
                                        <option value="all">All</option>
                                        <option value="1">National</option>
                                        <option value="2">Province</option>
                                        <option value="3">District</option>
                                        <option value="4">Field</option>
                                        <option value="7">Health Facility</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="e_mail" class="col-md-3 control-label">
                                    Request For<font color="#FF0000">*</font>
                                </label>
                                <div class="col-md-6">

                                    <input type="checkbox" name="inventory" id="inventory"> Inventory
                                    <input type="checkbox" name="consumption" id="consumption"> Consumption
                                    <input type="checkbox" name="analytics" id="analytics"> Analytics
                                </div>
                            </div>



                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="comments" class="col-md-2 control-label">
                                    Comments
                                </label>
                                <div class="col-md-10">

                                    <textarea id="comments" class="form-control" name="comments"></textarea>

                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="address" class="col-md-2 control-label">
                                    Address
                                </label>
                                <div class="col-md-10">

                                    <input type="text" name="address" id='address' value="" class="form-control">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="province" class="col-md-2 control-label">
                                    Province<font color="#FF0000">*</font>
                                </label>
                                <div class="col-md-4">

                                    <select name="province" id="province" class="form-control">
                                        <option value="">Select</option>
<?php
//Populate select3 combo
if ($rsloc != FALSE && mysql_num_rows($rsloc) > 0) {
    while ($RowLoc = mysql_fetch_object($rsloc)) {
        ?>
                                                <option value="<?= $RowLoc->PkLocID ?>" > <?php echo $RowLoc->LocName; ?> </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <label for="districts" class="col-md-2 control-label">
                                    District<font color="#FF0000">*</font>
                                </label>
                                <div class="col-md-4">

                                    <select name="districts" id="districts" class="form-control">
                                        <option value="">Select</option>
<?php
//Populate select3 combo
if ($rsloc != FALSE && mysql_num_rows($rsloc) > 0) {
    while ($RowLoc = mysql_fetch_object($rsloc)) {
        ?>
                                                <option value="<?= $RowLoc->PkLocID ?>" > <?php echo $RowLoc->LocName; ?> </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12 right">
                                    <div class="control-group">
                                        <label>&nbsp;</label>
                                        <div class="controls">

                                            <input type="submit" value="Register" class="btn btn-primary" />
                                            <input name="btnAdd" type="reset" id="btnCancel" value="Cancel" class="btn btn-info" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
<?php
if (isset($_REQUEST['msg']) && !empty($_REQUEST['msg'])) {
    //Display error messages
    print '<p style=\'color:#FF0000\'>Error:' . $_REQUEST['msg'] . "</p>";
}
?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form></div>
            </div>
        </div>
        <!-- BEGIN  PATNERS -->
        <div class="row">
            <div class="col-lg-12">
                <div class="tab-content stake-holder">stakeholders</div>
                <ul class="nav nav-tabs stake-holder-list">
                    <li> <img src="<?php echo PUBLIC_URL; ?>assets/frontend/layout/img/contraceptive-partners/govt-of-pak-logo.png" alt=""/></li>
                    <li> <img src="<?php echo PUBLIC_URL; ?>assets/frontend/layout/img/contraceptive-partners/us-aid-logo.png" alt=""/></li>
                </ul>
            </div>
        </div>
        <!-- END PATNERS -->
    </div>
</div>
</div>
</div>
<?php
endHtml();
?>
<script type="text/javascript" src="public/js/admin/jquery.validate.js"></script>
<script>
                                                $("#province").change(function () {
                                                    var bid = $("#province").val();
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "application/admin/getfromajax.php",
                                                        data: {ctype: 8, id: bid},
                                                        dataType: 'html',
                                                        success: function (data) {
                                                            $("#districts").html(data);

                                                        }
                                                    });

                                                });

                                                $("#manageuser").validate({
                                                    rules: {
                                                        full_name: "required",
                                                        Stakeholders: "required",
                                                        designation: "required",
                                                        email_id: {
                                                            email: true,
                                                            required: true
                                                        },
                                                        contact: "required",
                                                        level: "required",
                                                        province: "required",
                                                        districts: "required"



                                                    },
                                                    messages: {

                                                    }
                                                });

</script>