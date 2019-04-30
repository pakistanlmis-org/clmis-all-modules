<?php
/**
 * Manage Stakeholders Office Types
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
//act
$act = 2;
//strDo
$strDo = "Add";
//stkid
$stkid = 0;
//lvl_id
$lvl_id = 0;
//stktype
$stktype = "";
//nstktypeId
$nstktypeId = 0;
if (isset($_SESSION['level_id'])) {
    unset($_SESSION['level_id']);
    unset($_SESSION['parent_id']);
}

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
 * Delete 
 * 
 */
if ($strDo == "Delete") {
    $objstk->m_npkId = $nstkId;
    //Delete Stakeholder
    $rsEditCat = $objstk->DeleteStakeholder();
    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    echo '<script>window.location="ManageStakeholdersOfficeTypes.php"</script>';
    exit;
}
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
    $objstk->m_npkId = $nstkId;
    $_SESSION['pk_id'] = $nstkId;
    //Get Stakeholders By Id
    $rsEditstk = $objstk->GetStakeholdersById();
    if ($rsEditstk != FALSE && mysql_num_rows($rsEditstk) > 0) {
        $RowEditStk = mysql_fetch_object($rsEditstk);
        //StkparentID
        $StkparentID = $RowEditStk->ParentID;
        //gpid
        $gpid = $RowEditStk->MainStakeholder;

        if ($StkparentID == 0) {
            $StkparentID = $RowEditStk->stkid;
            $selfParent = TRUE;
        } else {
            $selfParent = FALSE;
        }
        //stkname
        $stkname = $RowEditStk->stkname;


        $lvl_id = $RowEditStk->lvl;
        $_SESSION['level_id'] = $lvl_id;
        $lvl_name = $RowEditStk->lvl_name;
        $stknameabb = $RowEditStk->stkname;
        $stkorder = $RowEditStk->stkorder;
        $objstk->m_npkId = $StkparentID;
        $_SESSION['parent_id'] = $StkparentID;
        //get stakeholder name
        $StkparentID = $objstk->get_stakeholder_name();
        $objstk->m_npkId = $gpid;
        $StkgparentID = $objstk->get_stakeholder_name();
    }
}

//Get All Stakeholders Office Types
$rsStakeholdersTypes = $objstk->GetAllStakeholdersOfficeTypes();
//Get All Stakeholders
$rsStakeholders = $objstk->GetAllStakeholders();

//Get All levels
$rslvls = $objlvl->GetAlllevels();
//including file
include("xml_stakeholder_office.php");
?>
</head>

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
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Manage Stakeholder Office Types</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Stakeholder Office Types</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="ManageStakeholdersOfficeTypesAction.php" id="ManageStakeholdersOfficeTypesAction">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Stakeholder<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="Stakeholders" id="Stakeholders" class="multi form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //populate Stakeholders combo
                                                            while ($RowGroups = mysql_fetch_object($rsStakeholders)) {
                                                                ?>
                                                                <option value="<?= $RowGroups->stkid ?>" <?php echo ($RowGroups->stkid == $gpid) ? 'selected="selected"' : ''; ?>>
                                                                    <?= $RowGroups->stkname ?>
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
                                                    <label>Level<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="lstlvl" id="lstlvl" class="multi form-control input-medium">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3" id="parentOffice" style="display:none;">
                                                <div class="control-group">
                                                    <label>Parent Office</label>
                                                    <div class="controls">
                                                        <select name='lststkholdersParent' id="lststkholdersParent" class="multi form-control input-medium">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Office Type<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input autocomplete="off" type="text" name="txtStktype" class="multi form-control input-medium" value="<?= $stkname ?>" />
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
                                                        <input type="hidden" name="ID" value="<?= $nstkId ?>" />
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">All Sub-admins</h3>
                            </div>
                            <div class="widget-body">
                                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td style="text-align:right;">
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(5, true);
                                                    mygrid.setColumnHidden(6, true);
                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                    mygrid.setColumnHidden(5, false);
                                                    mygrid.setColumnHidden(6, false);" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(5, true);
                                                    mygrid.setColumnHidden(6, true);
                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                    mygrid.setColumnHidden(5, false);
                                                    mygrid.setColumnHidden(6, false);" />
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
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script type="text/javascript">
        function editFunction(val) {
            window.location = "ManageStakeholdersOfficeTypes.php?Do=Edit&Id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageStakeholdersOfficeTypes.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Main Stakeholder Name'>Main Stakeholder</span>,<span title='Parent Stakeholder Name'>Parent Stakeholder</span>,<span title='Level'>Level</span>,<span title='Office Type'>Office Type</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            mygrid.attachHeader(",#text_filter,#text_filter,#select_filter,#text_filter,,");
            mygrid.setInitWidths("60,150,250,215,*,30,30");
            mygrid.setColAlign("center,left,left,left,left,center,center")
            mygrid.setColSorting("int,str,str,str");
            mygrid.setColTypes("ro,ro,ro,ro,ro,img,img");
            //mygrid.enableLightMouseNavigation(true);
            mygrid.enableRowsHover(true, 'onMouseOver');
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }

        $(function() {

            showLevels();
            $("select#Stakeholders").change(function() {
                showLevels();
            });

            $("select#lstlvl").change(function() {
                showParentOffices();
            });

        });
        function showParentOffices()
        {
            $("select#lststkholdersParent").html("<option>Please wait...</option>");
            var bid = $("select#lstlvl option:selected").attr('value');
            var pid = $("select#Stakeholders option:selected").attr('value');
            if (bid != 1)
            {
                $.post("getfromajax.php", {ctype: 4, id: bid, id2: pid}, function(data) {
                    //   $("select#lststkholdersParent").removeAttr("disabled");
                    $("#parentOffice").show();
                    $("select#lststkholdersParent").html(data);
                });
            }
            else
            {
                $("#parentOffice").hide();
            }
        }
        function showLevels()
        {
            var bid = $("select#Stakeholders option:selected").attr('value');
            if (bid == '')
            {
                $("select#lstlvl").html('<option>Select</option>');
                return false;
            }
            $("select#lstlvl").html("<option>Please wait...</option>");
            $.post("getfromajax.php", {ctype: 7, id: bid}, function(data) {
                //  $("select#lstlvl").removeAttr("disabled");
                $("select#lstlvl").html(data);
<?php if (isset($_REQUEST['Do'])) {
    ?>
                    showParentOffices();
<?php } ?>
            });
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
        //Unsetting session
        unset($_SESSION['err']);
    }
    ?>
</body>
</html>