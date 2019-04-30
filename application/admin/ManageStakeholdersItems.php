<?php
/**
 * Manage Stakeholders Items
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required files
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");


//Initializing variables
$act = 2;
$strDo = "Add";
$nstkId = 0;
$stk_id = 0;
$stkid = 0;
// stk_item
$stk_item = 0;
$type = "";
// stkname
$stkname = 0;
// itm_id
$itm_id = 0;
// itm_name
$itm_name = 0;
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //getting Do
    $strDo = $_REQUEST['Do'];
}

if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    //getting Id
    $nstkId = $_REQUEST['Id'];
}

/**
 * 
 * Edit Manage Stakeholder Items
 * 
 */
if ($strDo == "Edit") {
    $objstakeholderitem->m_npkId = $nstkId;
    $objManageItem->m_stkid = $nstkId;
    //Get stakeholder item By Id
    $objGIa = $objstakeholderitem->GetstakeholderitemById();

    if ($objGIa != FALSE && mysql_num_rows($objGIa) > 0) {
        while ($row = mysql_fetch_object($objGIa)) {
            $itmArr[] = $row->stk_item;
            $stkName = $row->stkname;
        }
    }
}
// Get All Manage Item
$objMI = $objManageItem->GetAllManageItem();
//Get All stakeholder
$odjstkitems = $objstakeholderitem->GetAllstakeholder();
//including file
include("xml_stakeholder_items.php");
?>
</head>
<!-- BEGIN BODY -->
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
                            <h3 class="page-title row-br-b-wp">Manage Stakeholder Items</h3>
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Edit Stakeholder Items for '<?php echo $stkName; ?>'</h3>
                                </div>
                                <div class="widget-body">
                                    <form method="post" name="ManageStakeholdersItems" id="ManageStakeholdersItems" action="ManageStakeholdersItemsAction.php">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label>Products<font color="#FF0000">*</font></label>
                                                        <div class="controls">
                                                            <select name="ItemID[]" size="10" multiple="multiple" class="multi form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                while ($Rowranks = mysql_fetch_object($objMI)) {
                                                                    ?>
                                                                    <option value="<?= $Rowranks->itm_id ?>" <?php echo (in_array($Rowranks->itm_id, $itmArr)) ? 'selected="selected"' : ''; ?>>
                                                                        <?= $Rowranks->itm_name ?>
                                                                    </option>
                                                                <?php }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-9 right">
                                                    <div class="control-group">
                                                        <label>&nbsp;</label>
                                                        <div class="controls" style="margin-top:140px;">
                                                            <input type="hidden" name="hdnstkId" value="<?= $nstkId ?>" />
                                                            <input  type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
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
                    <?php
                }
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">All Stakeholder Products</h3>
                            </div>
                            <div class="widget-body">
                                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td style="text-align:right;">
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(3, true);
                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                    mygrid.setColumnHidden(3, false);" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(3, true);
                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                    mygrid.setColumnHidden(3, false);" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><div id="mygrid_container" style="width:100%; height:390px; background-color:white;overflow:hidden"></div></td>
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
    include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script type="text/javascript">
        function editFunction(val) {
            window.location = "ManageStakeholdersItems.php?Do=Edit&Id=" + val;
        }
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Stakeholder Name'>Stakeholder</span>,<span title='List of all products that come under this stakeholder'>Products</span>,<span title='Use this column to perform the desired operation'>Action</span>");
            mygrid.attachHeader(",#select_filter,,");
            mygrid.setInitWidths("60,200,*,60");
            mygrid.setColAlign("center,left,left,center")
            mygrid.setColSorting("int,str");
            mygrid.setColTypes("ro,ro,ro,img");
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
        //unsetting session
        unset($_SESSION['err']);
    }
    ?>
</body>
</html>