<?php
/**
 * Manage Health Facility Type
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

//Initializinf variables
$act = 2;
$strDo = "Add";
$nstkId = 0;

/**
 * 
 * Get New Rank
 * 
 */
$rsRank = $HealthFacilityType->GetNewRank();
//getting results
if ($rsRank != FALSE && mysql_num_rows($rsRank) > 0) {
    $RowRank = mysql_fetch_object($rsRank);
    $HealthFacilityRank = $RowRank->rank;
}

//initializing variables
$HealthFacilityTypeName = "";
$StakeholderID = "";

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
 * Get All Stakeholders
 * 
 */
$rsStakeholders = $objstk->GetAllStakeholders();


/**
 * 
 * Delete Health Facility Type
 * 
 */
if ($strDo == "Delete") {
    $HealthFacilityType->m_npkId = $nstkId;
    $rsDelCat = $HealthFacilityType->DeleteHealthFacilityType();


    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    //redirect to ManageHealthFacilityType
    echo '<script>window.location="ManageHealthFacilityType.php"</script>';
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
    $HealthFacilityType->m_npkId = $nstkId;
    $_SESSION['pk_id'] = $nstkId;
    //GetHealthFacilityById
    $rsEditstk = $HealthFacilityType->GetHealthFacilityById();

    //getting results
    if ($rsEditstk != FALSE && mysql_num_rows($rsEditstk) > 0) {
        $RowEditStk = mysql_fetch_object($rsEditstk);
        $HealthFacilityTypeName = $RowEditStk->health_facility_type;
        $HealthFacilityRank = $RowEditStk->health_facility_rank;
        $StakeholderID = $RowEditStk->stakeholder_id;
    }
}

/**
 * 
 * Get All Health Facility Type
 * 
 */
$ItmGrp = $HealthFacilityType->GetAllHealthFacilityType();

//including file
include("xml_health_facility_type.php");
?>


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
                        <h3 class="page-title row-br-b-wp">Health Facility Type Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Health Facility Type</h3>
                            </div>
                            <div class="widget-body">
                                <!-- BEGIN FORM -->
                                <form method="post" action="ManageHealthFacilityTypeAction.php" name="managehealthfacilitytype" id="managehealthfacilitytype">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Stakeholder<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="select" id="Stakeholders" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //popolate stakeholde cobmo
                                                            if ($rsStakeholders != FALSE && mysql_num_rows($rsStakeholders) > 0) {
                                                                while ($RowGroups = mysql_fetch_object($rsStakeholders)) {
                                                                    ?>
                                                                    <option value="<?= $RowGroups->stkid ?>" <?php if ($RowGroups->stkid == $StakeholderID) {
                                                                echo 'selected="selected"';
                                                            } ?>> <?php echo $RowGroups->stkname; ?> </option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Health Facility Type<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input autocomplete="off" type="text" name="HealthFacilityTypeName" value="<?= $HealthFacilityTypeName ?>" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Rank</label>
                                                    <div class="controls"> 
                                                        <input type="text" name="HealthFacilityRank" id="spinner1" class="form-control input-small" style="border:1px solid #d8d9da;text-align:right; padding-right:5px;" value="<?= $HealthFacilityRank ?>" />
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
                                <!-- END FORM -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">All Health Facility Types</h3>
                                <?php 
                                //display All Health Facility Types
                                ?>
                            </div>
                            <div class="widget-body">
                                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td style="text-align:right;">
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="
                                                    mygrid.setColumnHidden(4, true);
                                                    mygrid.setColumnHidden(5, true);
                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                    mygrid.setColumnHidden(4, false);
                                                    mygrid.setColumnHidden(5, false);" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="
                                                    mygrid.setColumnHidden(4, true);
                                                    mygrid.setColumnHidden(5, true);
                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                    mygrid.setColumnHidden(4, false);
                                                    mygrid.setColumnHidden(5, false);" />
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
<?php include PUBLIC_PATH . "/html/footer.php"; ?>
<?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>
    <script>
        function editFunction(val) {
            window.location = "ManageHealthFacilityType.php?Do=Edit&Id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageHealthFacilityType.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
        //Inituializing Grid
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Stakeholder'>Stakeholder</span>,<span title='Health Facility Type'>Health Facility Type</span>,<span title='Health Facility Rank'>Health Facility Rank</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            mygrid.setInitWidths("60,*,*,80,30,30");
            mygrid.setColAlign("center,left,left,center,center")
            mygrid.setColSorting("int,str,,");
            mygrid.setColTypes("ro,ro,ro,ro,img,img");
            mygrid.enableRowsHover(true, 'onMouseOver');
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