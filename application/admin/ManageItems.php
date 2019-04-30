<?php
/**
 * Manage Items
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
$stakeid = array('');
$groupid = array('');
$strDo = "Add";
$nstkId = 0;
//item name
$itm_name = "";
//generic name
$generic_name = "";
//item type
$itm_type = "";
//item category
$itm_category = "";
//qty carton
$qty_carton = 0;
//field color
$field_color = "";
//item_des
$itm_des = "";
//item_status
$itm_status = "";
//frmindex
$frmindex = 0;
//extra
$extra = "";
//stkname
$stkname = "";
//stkorder
$stkorder = 0;

// Getting Do
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    $strDo = $_REQUEST['Do'];
}
// Getting form Id
if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    $nstkId = $_REQUEST['Id'];
}

/**
 * 
 * Delete 
 * 
 */
if ($strDo == "Delete") {
    $objManageItem->m_npkId = $nstkId;
    //Delete Manage Item
    $objManageItem->DeleteManageItem();

    //deleting value from stakeholder item
    $objstakeholderitem->m_stk_item = $nstkId;
    $objstakeholderitem->Deletestkholderitem();


    //deleting value from items of groups
    $ItemOfGroup->m_ItemID = $nstkId;
    $ItemOfGroup->DeleteItemGroup();

    $strDo = 'Add';

    //setting messages
    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    echo '<script>window.location="ManageItems.php"</script>';
    exit;
}

//retrieving maximum value of an index
$sql = mysql_query("Select MAX(frmindex) AS frmindex from itminfo_tab");
$sql_index = mysql_fetch_array($sql);
$frmindex = $sql_index['frmindex'] + 1;

//unset pk_id
if (isset($_SESSION['pk_id'])) {
    unset($_SESSION['pk_id']);
}

/**
 * 
 * Edit 
 * 
 */
if ($strDo == "Edit") {
    $objManageItem->m_npkId = $nstkId;
    $_SESSION['pk_id'] = $nstkId;

    //Get Manage Item By Id
    $rsEditstk = $objManageItem->GetManageItemById();
    //Gettin results
    if ($rsEditstk != FALSE && mysql_num_rows($rsEditstk) > 0) {
        $n = 0;
        //getting results
        while ($RowEditStk = mysql_fetch_object($rsEditstk)) {
            //$itm_name
            $itm_name = $RowEditStk->itm_name;
            //$generic_name
            $generic_name = $RowEditStk->generic_name;
            //$itm_type
            $itm_type = $RowEditStk->item_unit_id;
            //$itm_category
            $itm_category = $RowEditStk->itm_category;
            //$itm_des
            $itm_des = $RowEditStk->itm_des;
            //$itm_status
            $itm_status = $RowEditStk->itm_status;
            //$frmindex
            $frmindex = $RowEditStk->frmindex;
            //$stakeid
            $stakeid[$n] = $RowEditStk->stkid;
            //$groupid
            $groupid[$n] = $RowEditStk->GroupID;

            $n++;
        }
    }
}

//retrieving All Stakeholders
$rsStakeholders = $objstk->GetAllStakeholders();

//retrieving All Item Group
$rsranks = $ItemGroup->GetAllItemGroup();

//retrieving product type
$ItmType = $objItemUnits->GetAllItemUnits();

//retrieving product category
$ItmCategory = $objitemcategory->GetAllItemCategory();

//retrieving product status
$ItmStatus = $objitemstatus->GetAllItemStatus();

//Including required files
include("xml_item.php");
?>
</head>
<!-- BEGIN BODY -->
<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Product Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <?php 
                            //display all product
                            ?>
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Product</h3>
                            </div>
                            <div class="widget-body">
                                <form name="manageitems" id="manageitems" method="post" action="ManageItemAction.php">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Product<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input autocomplete="off" type="text" name="txtStkName1" value="<?= $itm_name ?>" id="txtStkName1" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Generic Name</label>
                                                    <div class="controls">
                                                        <input autocomplete="off" type="text" name="generic_name" value="<?= $generic_name ?>" id="generic_name" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Unit of Measure<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="txtStkName2" id="txtStkName2" class="form-control input-small">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //populate txtStkName2 combo
                                                            while ($RowItmType = mysql_fetch_object($ItmType)) {
                                                                ?>
                                                                <option value="<?= $RowItmType->pkUnitID . '-' . $RowItmType->UnitType ?>" <?php
                                                            if ($RowItmType->pkUnitID == $itm_type) {
                                                                echo 'selected="selected"';
                                                            }
                                                                ?>>
                                                                        <?= $RowItmType->UnitType ?>
                                                                </option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Category<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="txtStkName4" id="txtStkName4" class="form-control input-small">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //populate txtStkName4
                                                            while ($RowItmCategory = mysql_fetch_object($ItmCategory)) {
                                                                ?>
                                                                <option value="<?= $RowItmCategory->PKItemCategoryID ?>" <?php
                                                            if ($RowItmCategory->PKItemCategoryID == $itm_category) {
                                                                echo 'selected="selected"';
                                                            }
                                                                ?>>
                                                                        <?= $RowItmCategory->ItemCategoryName ?>
                                                                </option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Status<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="txtStkName6" id="txtStkName6" class="form-control input-small">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //populate txtStkName6
                                                            while ($RowItmStatus = mysql_fetch_object($ItmStatus)) {
                                                                ?>
                                                                <option value="<?= $RowItmStatus->PKItemStatusID ?>" <?php
                                                            if ($RowItmStatus->PKItemStatusID == $itm_status) {
                                                                echo 'selected="selected"';
                                                            }
                                                                ?>>
                                                                        <?= $RowItmStatus->ItemStatusName ?>
                                                                </option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Description</label>
                                                    <div class="controls">
                                                        <input type="text" name="txtStkName7" value="<?= $itm_des ?>" class="form-control input-medium">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Index<font color="#FF0000">*</font></label>
                                                    <div class="controls"> 
                                                        <!--<input type="text" name="txtStkName8" id="txtStkName8" class="form-control input-medium" value="<?= $frmindex ?>" />
                                                            <img src="images/sort_asc.gif" alt="" onClick="update_counter()" />
                                                            <img src="images/sort_desc.gif" alt="" onClick="update_counter_down()" />-->
                                                        <input type="text" name="txtStkName8" id="spinner1" class="form-control input-small" style="border:1px solid #d8d9da;text-align:right; padding-right:5px;" value="<?= $frmindex ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Stakeholders<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="stkid[]" size="5" multiple="multiple" class="form-control input-medium">
                                                            <?php
                                                            while ($RowStakeholders = mysql_fetch_object($rsStakeholders)) {
                                                                ?>
                                                                <option value="<?= $RowStakeholders->stkid ?>" <?php
                                                            if (in_array($RowStakeholders->stkid, $stakeid)) {
                                                                echo 'selected="selected"';
                                                            }
                                                                ?>>
                                                                        <?= $RowStakeholders->stkname ?>
                                                                </option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Product Group<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="select2[]" size="5" multiple="multiple" class="form-control input-medium">
                                                            <?php
                                                            //pipulate product group combo
                                                            while ($Rowranks = mysql_fetch_object($rsranks)) {
                                                                ?>
                                                                <option value="<?= $Rowranks->PKItemGroupID ?>" <?php
                                                            if (in_array($Rowranks->PKItemGroupID, $groupid)) {
                                                                echo 'selected="selected"';
                                                            }
                                                                ?>>
                                                                        <?= $Rowranks->ItemGroupName ?>
                                                                </option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
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
                                                        <input type="hidden" name="hdnstkId" value="<?= $nstkId ?>" />
                                                        <input  type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
                                                        <input type="submit" value="<?= $strDo ?>" class="btn btn-primary" />
                                                        <input name="btnAdd" type="button" id="btnCancel" class="btn btn-info" value="Cancel" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
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
                                <h3 class="heading">All Products</h3>
                            </div>
                            <div class="widget-body">
                                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td style="text-align:right;">
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(7, true);
                                                    mygrid.setColumnHidden(8, true);
                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                    mygrid.setColumnHidden(7, false);
                                                    mygrid.setColumnHidden(8, false);" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(7, true);
                                                    mygrid.setColumnHidden(8, true);
                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                    mygrid.setColumnHidden(7, false);
                                                    mygrid.setColumnHidden(8, false);" />
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
        //Edit Manage Items
        function editFunction(val) {
            window.location = "ManageItems.php?Do=Edit&Id=" + val;
        }
        //Delete Manage Items
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageItems.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
        /**
         * Initializing Grid
         */
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Product'>Product</span>,<span title='Generic Name'>Generic Name</span>,<span title='Method Type'>Method Type</span>,<span title='Unit'>Unit</span>,<span title='Status'>Status</span>,<span title='Index'>Index</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            mygrid.attachHeader(",#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,,,");
            mygrid.setInitWidths("60,*,200,200,80,150,50,30,30");
            mygrid.setColAlign("center,left,left,left,left,left,right,center,center")
            mygrid.setColSorting("int,str,,,,,int,,");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,img,img");
            mygrid.enableRowsHover(true, 'onMouseOver');
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
        $('#spinner1').spinner();
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
        //Unsetting session
        unset($_SESSION['err']);
    }
    ?>
</body>
</html>