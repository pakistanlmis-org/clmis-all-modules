<?php
require_once("../includes/classes/Configuration.inc.php");
$_SESSION['user_id'] = 1;

include_once(PUBLIC_PATH . "/html/html.inc.php");
include("../includes/classes/db.php");
include("../includes/classes/AllClasses.php");
startHtml('Contact Us');
siteMenu("Contact Us");
?>
<link href="<?php echo PUBLIC_URL; ?>common/theme/scripts/plugins/notifications/notyfy/themes/default.css" rel="stylesheet" />

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="<?php echo PUBLIC_URL; ?>js/jquery.notyfy.js"></script>

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
    .error {
        color:red;
    }
    /* File Upload */
    .fake-shadow {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }
    .fileUpload {
        position: relative;
        overflow: hidden;
    }
    .fileUpload #logo-id {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        padding: 0;
        font-size: 33px;
        cursor: pointer;
        opacity: 0;
        filter: alpha(opacity=0);
    }
    .img-preview {
        max-width: 100%;
    }
</style>
<?php
$rsStakeholders = $objstk->GetAllStakeholders();
$rsloc = $objloc->GetAllLocationsL2();
?>

<div class=" page-content-wrapper">
    <div class="page-content landing-content"><br />
        <div class="col-md-4 col-md-offset-4">
            <?php if (isset($_SESSION['err'])) { ?>
                <div class="alert alert-info" id="saved">
                    <button data-dismiss="alert" class="close" type="button">
                        ×
                    </button>
                    Your request is submitted successfully. <br>

                </div>
            <?php } ?>
        </div>
        <div class="col-md-4 col-md-offset-4"> </div>
        <div class="col-md-5 col-md-offset-4_5 col-sm-offset-4_5">
            <div class="portlet box green ">
                <div class="portlet-title">
                    Register User

                </div>
                <div class="portlet-body" style="min-height:300px;">
                    <form method="post" action="userRegisterAction.php" name="manageuser" id="manageuser" enctype='multipart/form-data'>
                        <div class="row">
                            <div class="form-group">
                                <label for="e_mail" class="col-md-2 control-label">
                                    Full Name<font color="#FF0000">*</font>
                                </label>
                                <div class="col-md-6">

                                    <input type="text" name="full_name" id='full_name' value="" class="form-control">
                                </div>

                                <div class="col-md-3">


                                    <div class="form-group">
                                        <div class="main-img-preview">
                                            <img class="thumbnail img-preview" src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&text=no+image" title="Preview Logo">
                                        </div>
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <div class="fileUpload btn btn-danger fake-shadow">
                                                    <span><i class="glyphicon glyphicon-upload"></i>Photo</span>
                                                    <input id="logo-id" name="photo" type="file" class="attachment_upload">
                                                </div>
                                            </div>
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
                                        <option value="7" >Other </option>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="row" style="display: none" id="other">
                            <div class="form-group">
                                <label for="other" class="col-md-2 control-label">
                                    Other<font color="#FF0000">*</font>
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
                                <label for="country" class="col-md-2 control-label" style="margin-right:13px">
                                    Request for
                                </label>
                                <div class="col-md-6">
                                    <div class="mt-checkbox-inline">
                                        <label class="mt-checkbox">
                                            <input id="inlineCheckbox21" value="option1" type="checkbox"> Inventory
                                            <span></span>
                                        </label>
                                        <label class="mt-checkbox">
                                            <input id="inlineCheckbox22" value="option2" type="checkbox"> Consumption
                                            <span></span>
                                        </label>

                                        <label class="mt-checkbox">
                                            <input id="inlineCheckbox22" value="option2" type="checkbox"> Analytics
                                            <span></span>
                                        </label>

                                    </div>

                                </div>
                            </div>
                        </div>
                        <br>
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


                        <div class="row" >
                            <div class="col-md-12 right">
                                <div class="control-group right" style="margin-left:450px;">
                                    <label>&nbsp;</label>
                                    <div class="controls right">

                                        <input type="submit" value="Register" class="btn btn-primary" />
                                        <input name="btnAdd" type="reset" id="btnCancel" value="Cancel" class="btn btn-info" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
                                        <label>&nbsp;</label>
                                        <p class="text-right">
                                            <a href="<?php echo SITE_URL ?>" class="">Go Back</a>
                                        </p>

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
<script src="<?php echo PUBLIC_URL ?>js/admin/jquery.validate.js"></script>

<script>
                                            $("#province").change(function () {
                                                var bid = $("#province").val();
                                                $.ajax({
                                                    type: "POST",
                                                    url: "getfromajax.php",
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
                                                    other: "required",
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
                                            });</script>
<?php
unset($_SESSION['err']);
?>





<script>

    $('#Stakeholders').change(function () {
        var org = $('#Stakeholders').val();
        if (org == 7) {
            $("#other").css("display", "block");
        } else {
            $("#other").css("display", "none");
        }

    });

</script>
<script>
    $(document).ready(function () {
        var brand = document.getElementById('logo-id');
        brand.className = 'attachment_upload';
        brand.onchange = function () {
            document.getElementById('fakeUploadLogo').value = this.value.substring(12);
        };

        // Source: http://stackoverflow.com/a/4459419/6396981
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('.img-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#logo-id").change(function () {
            readURL(this);
        });
    });
</script>