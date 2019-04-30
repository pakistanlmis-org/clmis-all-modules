<?php
/**
 * Manage Items Groups
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Including required file
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

//Initializing variables
$act = 2;
$strDo = "Add";
$nstkId = 0;

$ItemGroupName = "";
//Getting Do
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    $strDo = $_REQUEST['Do'];
}
//Getting Id
if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    $nstkId = $_REQUEST['Id'];
}



/**
 * 
 * Delete Item Group
 * 
 */
if ($strDo == "Delete") {
    $ItemGroup->m_npkId = $nstkId;
    //DeleteItemGroup
    $rsDelCat = $ItemGroup->DeleteItemGroup();

    //Setting messages
    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    //Redirecting to ManageItemsGroups
    echo '<script>window.location="ManageItemsGroups.php"</script>';
    exit;
}
if (isset($_SESSION['pk_id'])) {
    //unset session
    unset($_SESSION['pk_id']);
}
/**
 * 
 * Edit Item Group
 * 
 */
if ($strDo == "Edit") {
    $ItemGroup->m_npkId = $nstkId;
    $_SESSION['pk_id'] = $nstkId;
    //Get Item Group By Id
    $rsEditstk = $ItemGroup->GetItemGroupById();
    if ($rsEditstk != FALSE && mysql_num_rows($rsEditstk) > 0) {
        $RowEditStk = mysql_fetch_object($rsEditstk);
        $ItemGroupName = $RowEditStk->ItemGroupName;
    }
}
//Get All Item Group
$ItmGrp = $ItemGroup->GetAllItemGroup();

//Including required file
include("xml_items_group.php");
?>


<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Product Group Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Product Group</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="ManageItemGroupAction.php" name="manageitemgroups" id="manageitemgroups">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Product Group<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input autocomplete="off" type="text" name="ItemGroupName" value="<?= $ItemGroupName ?>" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <div class="control-group">
                                                        <label>&nbsp;</label>
                                                        <div class="controls">
                                                            <input type="hidden" name="hdnstkId" value="<?= $nstkId ?>" />
                                                            <input  type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
                                                            <input type="submit" value="<?= $strDo ?>" class="btn btn-primary" />
                                                            <input name="btnAdd" type="button" id="btnCancel" class="btn btn-info" value="Cancel" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
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
                                <h3 class="heading">All Product's Group</h3>
                            </div>
                            <div class="widget-body">
                                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td style="text-align:right;">
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(2, true);
                                                    mygrid.setColumnHidden(3, true);
                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                    mygrid.setColumnHidden(2, false);
                                                    mygrid.setColumnHidden(3, false);" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(2, true);
                                                    mygrid.setColumnHidden(3, true);
                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                    mygrid.setColumnHidden(2, false);
                                                    mygrid.setColumnHidden(3, false);" />
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
    //incliding required files
    include PUBLIC_PATH . "/html/footer.php";
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
        //Edit Manage Items Groups
        function editFunction(val) {
            window.location = "ManageItemsGroups.php?Do=Edit&Id=" + val;
        }
        //Delete Manage Items Groups
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageItemsGroups.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
        //Initializing Grid
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Group Name'>Group Name</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            mygrid.setInitWidths("60,*,30,30");
            mygrid.setColAlign("center,left,center,center")
            mygrid.setColSorting("int,str,,");
            mygrid.setColTypes("ro,ro,img,img");
            mygrid.enableRowsHover(true, 'onMouseOver');
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
        //Unset session
        unset($_SESSION['err']);
    }
    ?>
</body>
</html>