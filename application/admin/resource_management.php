<?php
/**
 * Resource Management
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
$pk_id = $resource_name = $description = $page_title = $level = $parent_id = $resource_type_id = $icon_class = '';

/**
 * Edit
 */
if ($strDo == "Edit") {
    //Getting id
    $pk_id = $_GET['id'];
    //Query for getting user
    $qry = "SELECT
				resources.resource_name,
				resources.description,
				resources.page_title,
				resources.parent_id,
				resources.resource_type_id,
				resources.icon_class
			FROM
				resources
			WHERE
				resources.pk_id = $pk_id ";
    //Query result
    $qryRes = mysql_fetch_row(mysql_query($qry));
    //resource_name
    $resource_name = $qryRes[0];
    //description
    $description = $qryRes[1];
    //page_title
    $page_title = $qryRes[2];
    //parent_id
    $parent_id = $qryRes[3];
    //resource_type_id
    $resource_type_id = $qryRes[4];
    //icon_class
    $icon_class = $qryRes[5];
}
/**
 * Delete
 */
if ($strDo == "Delete") {
    $pk_id = $_GET['id'];
    //Query for deleting user
    $qry = "DELETE
			FROM
				resources
			WHERE
				resources.pk_id = $pk_id ";
    //Query results
    $qryRes = mysql_query($qry);
    //Setting message in session
    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    //Redirecting to resource_management
    echo "<script>window.location='resource_management.php'</script>";
    exit;
}
$display = ($resource_type_id != 2) ? 'display:none;' : 'display:block;';
//Including file
include("xml_resources.php");
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
                        <h3 class="page-title row-br-b-wp">Resource Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Role</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="resource_management_action.php" name="frm" id="frm">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Resource Type<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select required name="resource_type_id" id="resource_type_id" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //Populate resource_type_id combo
                                                            $qry = "SELECT
																	resource_types.pk_id,
																	resource_types.resource_type
																FROM
																	resource_types";
                                                            $qryRes = mysql_query($qry);
                                                            while ($row = mysql_fetch_array($qryRes)) {
                                                                $sel = ($resource_type_id == $row['pk_id']) ? 'selected="selected"' : '';
                                                                echo "<option value=\"" . $row['pk_id'] . "\" $sel>" . $row['resource_type'] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Resource Name<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input autocomplete="off" required name="resource_name" id="resource_name" type="text" value="<?php echo $resource_name; ?>" size="100" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Page Title</label>
                                                    <div class="controls">
                                                        <input autocomplete="off" name="page_title" id="page_title" type="text" value="<?php echo $page_title; ?>" size="100" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Resource Description</label>
                                                    <div class="controls">
                                                        <textarea name="description" id="description" class="form-control" style="resize:vertical"><?php echo $description; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="menu_row" style=" <?php echo $display; ?>">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Parent Menu</label>
                                                    <div class="controls">
                                                        <select name="parent_id" id="parent_id" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //Populate parent_id combo
                                                            $qry = "SELECT
																	resources.pk_id,
																	IF(ISNULL(resources.page_title), resources.resource_name, resources.page_title) AS resource_name,
																	resources.parent_id
																FROM
																	resources
																WHERE
																	resources.resource_type_id = 2
                                                                                                                                        ORDER BY IF(ISNULL(resources.page_title), resources.resource_name, resources.page_title)";
                                                            $qryRes = mysql_query($qry);
                                                            while ($row = mysql_fetch_assoc($qryRes)) {
                                                                $sel = ($parent_id == $row['pk_id']) ? 'selected="selected"' : '';
                                                                echo "<option value=\"" . $row['pk_id'] . "\" $sel>" . $row['resource_name'] . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>                                        
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Menu Icon Class</label>
                                                    <div class="controls">
                                                        <input autocomplete="off" name="icon_class" id="icon_class" type="text" value="<?php echo $icon_class; ?>" size="100" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 right">
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
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
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
    //Including required files
    include PUBLIC_PATH . "/html/footer.php";
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
        $(function() {
            $('#resource_type_id').change(function(e) {
                if ($(this).val() == 2) {
                    $('#menu_row').show();
                } else {
                    $('#menu_row').hide();
                }
            });
        })
        function editFunction(val) {
            window.location = "resource_management.php?Do=Edit&id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "resource_management.php?Do=Delete&id=" + val;
            }
        }
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Pk ID'>Pk ID</span>,Resource Type,Resource Name,Title,Resource Description,Actions,#cspan");
            mygrid.attachHeader(",#text_filter,#select_filter,#text_filter,#text_filter,,,");
            mygrid.setInitWidths("50,50,120,350,200,*,30,30");
            mygrid.setColAlign("center,center,left,left,left,left,center,center")
            mygrid.setColSorting("int,int,str,str,str,str,,");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,img,img");
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
        //Unsetting session err
        unset($_SESSION['err']);
    }
    ?>
</body>
</html>