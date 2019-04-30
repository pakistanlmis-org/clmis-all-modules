<?php
/**
 * Role Management
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including files
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

//Initializing variables
//strDo
$strDo = "Add";
//Getting Do
$strDo = (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) ? $_REQUEST['Do'] : 'Add';
$pk_id = $role_name = $description = '';

if ($strDo == "Edit") {
    //Getting id
    $pk_id = $_GET['id'];
    //Query for editing role
    $qry = "SELECT
				roles.role_name,
				roles.description,
				roles.landing_resource_id,
				roles.role_level
			FROM
				roles
			WHERE
				roles.pk_id = $pk_id ";
    //Query result
    $qryRes = mysql_fetch_assoc(mysql_query($qry));
    //role name
    $role_name = $qryRes['role_name'];
    //description
    $description = $qryRes['description'];
    //landing resource id
    $landing_resource_id = $qryRes['landing_resource_id'];
    
    $role_level = $qryRes['role_level'];
}
/**
 * Delete
 */
if ($strDo == "Delete") {
    $pk_id = $_GET['id'];
    //Query for deleting role
    $qry = "DELETE
			FROM
				roles
			WHERE
				roles.pk_id = $pk_id ";
    //Query result
    $qryRes = mysql_query($qry);

    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    //Redirecting to role_management
    echo "<script>window.location='role_management.php'</script>";
    exit;
}
//Including file
include("xml_roles.php");
?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
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
                        <h3 class="page-title row-br-b-wp">Role Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Role</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="role_management_action.php" name="frm" id="frm">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Role Name<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input autocomplete="off" required name="role_name" id="role_name" type="text" value="<?php echo $role_name; ?>" size="100" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            
                                             <div class="col-md-4">
                                                <div class="control-group">
                                                    <label>Level<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="role_level" id="role_level" class="form-control input-large" required="required">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //Populate landing_resource_id combo
                                                            $qry = "SELECT * FROM tbl_dist_levels";
                                                            $qryRes = mysql_query($qry);
                                                            while ($row = mysql_fetch_assoc($qryRes)) {
                                                                $sel = ($role_level == $row['lvl_id']) ? 'selected="selected"' : '';
                                                                echo "<option value=\"" . $row['lvl_id'] . "\" $sel>" . $row['lvl_name'] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="control-group">
                                                    <label>Landing Page<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="landing_resource_id" id="landing_resource_id" class="form-control input-large" required="required">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //Populate landing_resource_id combo
                                                            $qry = "SELECT
                                                                            resources.pk_id,
                                                                            IF(ISNULL(resources.page_title), resources.resource_name, resources.page_title) AS resource_name,
                                                                            resources.parent_id
                                                                    FROM
                                                                            resources";
                                                            $qryRes = mysql_query($qry);
                                                            while ($row = mysql_fetch_assoc($qryRes)) {
                                                                $sel = ($landing_resource_id == $row['pk_id']) ? 'selected="selected"' : '';
                                                                echo "<option value=\"" . $row['pk_id'] . "\" $sel>" . $row['resource_name'] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Role Description</label>
                                                    <div class="controls">
                                                        <textarea name="description" id="description" class="form-control" style="resize:vertical"><?php echo $description; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-9 right">
                                                <div class="control-group">
                                                    <div class="control-group">
                                                        <label>&nbsp;</label>
                                                        <div class="controls">
                                                            <input type="hidden" name="pk_id" value="<?= $pk_id ?>" />
                                                            <input type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
                                                            <input type="submit" class="btn btn-primary" value="<?= $strDo ?>" />
                                                            <input name="btnAdd" class="btn btn-info" type="button" id="btnCancel" value="Cancel" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
                                                        </div>
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
                                <?php
                                //Display All Roles
                                ?>
                                <h3 class="heading">All Roles</h3>
                            </div>
                            <div class="widget-body">
                                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td style="text-align:right;">
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(3, true);
                                                    mygrid.setColumnHidden(4, true);
                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                    mygrid.setColumnHidden(3, false);
                                                    mygrid.setColumnHidden(4, false);" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(3, true);
                                                    mygrid.setColumnHidden(4, true);
                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                    mygrid.setColumnHidden(3, false);
                                                    mygrid.setColumnHidden(4, false);" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><div id="mygrid_container" style="width:100%; height:350px; background-color:white;overflow:hidden"></div></td>
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
    //Including files
    include PUBLIC_PATH . "/html/footer.php";
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
        function editFunction(val) {
            window.location = "role_management.php?Do=Edit&id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "role_management.php?Do=Delete&id=" + val;
            }
        }
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,Role Name, Landing Page, Role Description,Actions,#cspan");
            mygrid.setInitWidths("50,200,200,*,30,30");
            mygrid.setColAlign("center,left,left,left,center,center")
            mygrid.setColSorting("int,str,str,str,,");
            mygrid.setColTypes("ro,ro,ro,ro,img,img");
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
        //Unset session err
        unset($_SESSION['err']);
    }
    ?>
</body>
</html>