<?php

//including files
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

//initializing variables
//forid
$formid = 'pro-admin';
//strDo
$strDo = '';
//Get All SubAdmin User
$rsUsers = $objuser->GetAllSubAdminUser();

if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //getting Do
    $strDo = $_REQUEST['Do'];
}
if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    //getting Id
    $nstkId = $_REQUEST['Id'];
}
/**
 * Delete
 */
if ($strDo == "Delete") {
    $objuser->m_npkId = $nstkId;
    //Delete User
    $objuser->DeleteUser();

    $objuserprov->m_nuserId = $nstkId;
    //Delete province
    $objuserprov->delete();
    //Delete stakeholder
    $objuserstk->m_nuserId = $nstkId;
    $objuserstk->delete();

    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    //redirecting to ManageProAdmin
    echo '<script>window.location="ManageProAdmin.php"</script>';
    exit;
}

if ($strDo == "Edit") {

    $formid = 'sub-admin2';
    ///////////////
    $objuserprov->m_npkId = $nstkId;
    //Get Province By User Id
    $user_prov = $objuserprov->GetProvByUserId();

    $objuserstk->m_npkId = $nstkId;
    //Get Stakeholder By User Id
    $user_stk = $objuserstk->GetStkByUserId();
//////////////

    $objuser->m_npkId = $nstkId;
    //Get User By User ID
    $rsuser = $objuser->GetUserByUserID();
    if ($rsuser != FALSE && mysql_num_rows($rsuser) > 0) {
        $RowEditStk = mysql_fetch_object($rsuser);
        //echo '<pre>';print_r($RowEditStk);exit;
        //usrlogin_id
        $usrlogin_id = $RowEditStk->usrlogin_id;
        //sysusr_pwd
        $sysusr_pwd = $RowEditStk->sysusr_pwd;
        //sysusr_name
        $sysusr_name = $RowEditStk->sysusr_name;
        //sysusr_email
        $sysusr_email = $RowEditStk->sysusr_email;
        //sysusr_ph
        $sysusr_ph = $RowEditStk->sysusr_ph;
        //retrieving user id
        $sysusr_UserID = $RowEditStk->UserID;
        //retrieving stk id
        $sysusr_stkid = $RowEditStk->stkid;
        //retrieving prov id
        $sysusr_prov_id = $RowEditStk->prov_id;
        
    }
}
//Get All Stakeholders
$rsStakeholders = $objstk->GetAllMainStakeholders();

$strDo = ($strDo == 'Edit') ? $strDo : 'Add';
//including file
include("xml_proadmins.php");
?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //including files
        include $_SESSION['menu'];
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Provincial Admin Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Provincial Admin</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="ManageProAdminAction.php" id="<?php echo $formid; ?>">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Full Name<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="text" name="name" id="name" value="<?php echo (!empty($sysusr_name)?$sysusr_name:''); ?>" class="form-control input-medium">
                                                    </div>
                                                </div>
                                            </div>
<?php if ($strDo != 'Edit') : ?>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label>Login<font color="#FF0000">*</font></label>
                                                        <div class="controls">
                                                            <input type="text" name="login" id="login" value="<?php echo (!empty($usrlogin_id)?$usrlogin_id:''); ?>" class="form-control input-medium" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label>Password<font color="#FF0000">*</font></label>
                                                        <div class="controls">
                                                            <input type="password" name="password" id="password" class="form-control input-medium">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label>Confirm Password<font color="#FF0000">*</font></label>
                                                        <div class="controls">
                                                            <input type="password" name="cpassword" id="cpassword" class="form-control input-medium">
                                                        </div>
                                                    </div>
                                                </div>
<?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Contact No</label>
                                                    <div class="controls">
                                                        <input type="text" name="contact_no" id="contact_no" value="<?php echo (!empty($sysusr_ph)?$sysusr_ph:''); ?>" class="form-control input-medium">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Email</label>
                                                    <div class="controls">
                                                        <input type="text" name="email" id="email" value="<?php echo (!empty($sysusr_email)?$sysusr_email:''); ?>" class="form-control input-medium">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Stakeholders<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <div class="multi-select col-md-6" style="padding:0;">
                                                            <select id="stackholders1" name="stkholders[]" class=" form-control input-medium">
                                                                <?php
                                                                //populate stackholders1 combo
                                                                
                                                                while ($row = mysql_fetch_array($rsStakeholders)) {
                                                                    $sel = '';
                                                                    if ($row['stkid'] == $sysusr_stkid) $sel = ' selected ';
                                                                        ?>
                                                                        <option <?=$sel?> value="<?php echo $row['stkid']; ?>"><?php echo $row['stkname']; ?></option>
                                                                        <?php
                                                                    
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Province<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <div class="multi-select col-md-6" style="padding:0;">
                                                            <select  id="provinces1" name="prov[]" class=" form-control input-medium">
                                                                <?php
                                                                $objloc->LocLvl = 2;
                                                                //populate provinces1 combo
                                                                //Get All Locations
                                                                $rsProvinces = $objloc->GetAllProvinces();
                                                                while ($row = mysql_fetch_array($rsProvinces)) {
                                                                    $sel = '';
                                                                    if ($row['PkLocID'] == $sysusr_prov_id) $sel = ' selected ';
                                                                        ?>
                                                                        <option <?=$sel?> value="<?php echo $row['PkLocID']; ?>"><?php echo $row['LocName']; ?></option>
                                                                        <?php
                                                                    
                                                                }
                                                                ?>
                                                            </select>
                                                         </div>
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12 right">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
<?php if ($strDo == 'Edit') : ?>
                                                            <input name="Id" value="<?php echo $sysusr_UserID; ?>" type="hidden" />
                                                            <input name="submit" value="Edit User" type="submit" id="submit" class="btn btn-primary" />
                                                            <input name="Do" value="Edit" type="hidden" id="Do" />
<?php else: ?>
                                                            <input name="Do" value="Add" type="hidden" id="Do" />
                                                            <input name="submit" value="Add User" type="submit" id="submit" class="btn btn-primary" />
<?php endif; ?>
                                                        <input name="cancel" value="Cancel" type="button" id="cancel" class="btn btn-info" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';" />
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">All Provincial Admins</h3>
                            </div>
                            <div class="widget-body">
                                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td style="text-align:right;">
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(6, true);
                                                    mygrid.setColumnHidden(7, true);
                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                    mygrid.setColumnHidden(6, false);
                                                    mygrid.setColumnHidden(7, false);" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(6, true);
                                                    mygrid.setColumnHidden(7, true);
                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                    mygrid.setColumnHidden(6, false);
                                                    mygrid.setColumnHidden(7, false);" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><div id="mygrid_container" style="width:100%; height:390px; background-color:white;overflow:hidden"></div></td>
                                    </tr>
                                    <tr>
                                        <td><div id="recinfoArea"></div></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    //Including required files
    include PUBLIC_PATH . "/html/footer.php";
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script type="text/javascript">
        $(function() {
            $('#<?php echo $formid; ?>').on('submit', function(e) {
                $('#stackholders2 option').attr('selected', 'selected');
                $('#provinces2 option').attr('selected', 'selected');
            });

            $('#stk-add').click(function() {
                return !$('#stackholders1 option:selected').remove().appendTo('#stackholders2');
            });
            $('#stk-remove').click(function() {
                return !$('#stackholders2 option:selected').remove().appendTo('#stackholders1');
            });

            $('#prov-add').click(function() {
                return !$('#provinces1 option:selected').remove().appendTo('#provinces2');
            });
            $('#prov-remove').click(function() {
                return !$('#provinces2 option:selected').remove().appendTo('#provinces1');
            });
        });
    </script> 
    <script>
        function editFunction(val) {
            window.location = "ManageProAdmin.php?Do=Edit&Id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageProAdmin.php?Do=Delete&Id=" + val;
            }
        }

        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Username'>Username</span>,<span title='Name'>Name</span>,<span title='Contact No'>Contact No</span>,<span title='Email'>Email</span>,<span title='Provinces'>Provinces</span>,<span title='Stakeholders'>Stakeholders</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            mygrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#text_filter");
            mygrid.setInitWidths("60,120,120,100,170,*,250,30,30");
            mygrid.setColAlign("center,left,left,left,left,left,left")
            mygrid.setColSorting("str");
            mygrid.enableMultiline(true);
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,img,img");
            //mygrid.enableLightMouseNavigation(true);
            mygrid.enableRowsHover(true, 'onMouseOver');
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script>
    <?php
    if (isset($_SESSION['err'])) {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: '<?php echo $_SESSION['err']['text']; ?>',
                type: '<?php echo $_SESSION['err']['type']; ?>',
                layout: self.data('layout')
            });
        </script>
        <?php
        unset($_SESSION['err']);
    }
    ?>
</body>
</html>