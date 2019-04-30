<?php
/**
 * Manage Items of Groups
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
$itm_id = 0;
// item name
$itm_name = "";
// PK Item Group ID
$PKItemGroupID = 0;
//  Item Group Name
$ItemGroupName = "";
$pkItemsofGroupsID = 0;
// Item ID
$ItemID = 0;
// Group ID
$GroupID = 0;
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
 * Delete Item Of Group
 * 
 */
if ($strDo == "Delete") {
    $ItemOfGroup->m_GroupID = $nstkId;
    $rsEditCat = $ItemOfGroup->DeleteItemOfGroup();
    //Redirecting to ManageItemsofGroups
    echo '<script>window.location="ManageItemsofGroups.php"</script>';
    exit;
}
/**
 * 
 * Edit Item Of Group
 * 
 */
if ($strDo == "Edit") {
    $ItemOfGroup->m_npkId = $nstkId;
    //Get Item Of Group By Id
    $objGIa = $ItemOfGroup->GetItemOfGroupById();
    if ($objGIa != FALSE && mysql_num_rows($objGIa) > 0) {
        $RowEditStk = mysql_fetch_object($objGIa);
        $GroupName = $RowEditStk->ItemGroupName;
    }
    //Get All Manage Item
    $objMI = $objManageItem->GetAllManageItem();
}
//Get All Item Group
$ItmOfGrp = $ItemGroup->GetAllItemGroup();

//Including required file
include("xml_items_of_groups.php");
?>
</head>

<!-- BEGIN body -->
<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //Including required files
        include $_SESSION['menu'];
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <?php
                if (isset($_REQUEST['Do'])) {
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-title row-br-b-wp">Items Group Management</h3>
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading"><?php echo $strDo; ?> Group Items for '<?php echo $GroupName; ?>'</h3>
                                </div>
                                <!-- BEGIN widget-body -->
                                <div class="widget-body">
                                    <form method="post" action="ManageItemsofGroupsAction.php">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label>Products<font color="#FF0000">*</font></label>
                                                        <div class="controls">
                                                            <select name="ItemID[]" size="5" multiple="multiple" class="form-control input-medium">
                                                                <?php
                                                                while ($Rowranks = mysql_fetch_object($objMI)) {
                                                                    ?>
                                                                    <option value="<?= $Rowranks->itm_id ?>" <?php
                                                                    $ItemOfGroup->m_npkId = $nstkId;
                                                                    //Get Items Of Group By Id
                                                                    $objMIs = $ItemOfGroup->GetItemsOfGroupById();
                                                                    if ($objMIs != FALSE && mysql_num_rows($objMIs) > 0) {
                                                                        while ($Rowrankss = mysql_fetch_object($objMIs)) {
                                                                            if ($Rowranks->itm_id == $Rowrankss->ItemID) {
                                                                                echo 'selected="selected"';
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>>
                                                                    <?= $Rowranks->itm_name ?>
                                                                    </option>
                                                                <?php }
                                                                ?>
                                                            </select>
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
                                <!-- END widget-body -->
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <!-- All Product's Group -->
                                <h3 class="heading">All Product's Group</h3>
                            </div>
                             <!-- BEGIN widget-body -->
                            <div class="widget-body">
                                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td style="text-align:right;">
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(3, true);
                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>plmis_src/operations/dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                    mygrid.setColumnHidden(3, false);" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(3, true);
                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
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
                              <!-- END widget-body -->
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
        //Edit Manage Items of Groups
        function editFunction(val) {
            window.location = "ManageItemsofGroups.php?Do=Edit&Id=" + val;
        }
        //Delete Manage Items of Groups
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageItemsofGroups.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
        //Initializing Grid
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Product Groups'>Groups</span>,<span title='List of all Products'>Products</span>,<span title='Use this column to perform the desired operation'>Actions</span>");
            mygrid.setInitWidths("60,150,*,60");
            mygrid.setColAlign("center,left,left,center")
            mygrid.setColSorting("int,str");
            mygrid.setColTypes("ro,ro,ro,img");
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
<!-- END body -->
</html>