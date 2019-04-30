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
include("../../includes/classes/AllClasses.php");
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
	stock_sources.stock_source_id,
	stock_sources.stakeholder_id,
	stock_sources.province_id
FROM
	stock_sources
WHERE
	stock_sources.pk_id = $pk_id";
    //Query result
    $qryRes = mysql_fetch_assoc(mysql_query($qry));
    $src_id = $qryRes['stock_source_id'];
    $nstk_id = $qryRes['stakeholder_id'];
    $nprov_id = $qryRes['province_id'];
}
/**
 * Delete
 */
if ($strDo == "Delete") {
    $pk_id = $_GET['id'];
    //Query for deleting role
    $qry = "DELETE
			FROM
				stock_sources
			WHERE
				stock_sources.pk_id = $pk_id ";
    //Query result
    $qryRes = mysql_query($qry);

    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
}
//Including file
include("xml_sources.php");
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
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Data source management</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="assign.php" name="frm" id="frm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="control-group">
                                                <label>Data Source<font color="#FF0000">*</font></label>
                                                <div class="controls">
                                                    <select name="Sources" id="Sources" class="form-control input-medium">
                                                        <option value="">Select</option>
                                                        <?php
                                                        //Populate Stakeholders combo
                                                        $rssources = mysql_query("SELECT
	list_detail.pk_id `id`,
	list_detail.list_value `name`
FROM
	list_detail
WHERE
	list_detail.list_master_id = 24
ORDER BY
list_detail.rank ASC
");
                                                        while ($RowSrcs = mysql_fetch_object($rssources)) {
                                                            ?>
                                                            <option value="<?= $RowSrcs->id ?>" <?php echo ($src_id == $RowSrcs->id) ? 'selected' : ''; ?>>
                                                                <?= $RowSrcs->name ?>
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
                                                <label>Province<font color="#FF0000">*</font></label>
                                                <div class="controls">
                                                    <select name="Provinces" id="Provinces" class="form-control input-medium">
                                                        <option value="">Select</option>
                                                        <?php
                                                        //Populating Provinces combo
                                                        $objloc->LocLvl = 2;
                                                        $rsloc = $objloc->GetAllLocations();
                                                        while ($RowLoc = mysql_fetch_object($rsloc)) {
                                                            ?>
                                                            <option value="<?= $RowLoc->PkLocID ?>" <?php
                                                            if ($RowLoc->PkLocID == $nprov_id) {
                                                                echo 'selected="selected"';
                                                            }
                                                            ?>>
                                                            <?= $RowLoc->LocName ?>
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
                                                <label>Stakeholder<font color="#FF0000">*</font></label>
                                                <div class="controls">
                                                    <select name="Stakeholders" id="Stakeholders" class="form-control input-medium">
                                                        <option value="">Select</option>
                                                        <?php
                                                        //Populate Stakeholders combo
                                                        $rsStakeholders = $objstk->GetAllStakeholders();
                                                        while ($RowGroups = mysql_fetch_object($rsStakeholders)) {
                                                            ?>
                                                            <option value="<?= $RowGroups->stkid ?>" <?php echo ($nstk_id == $RowGroups->stkid) ? 'selected' : ''; ?>>
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
                                                    <select name="lvl" id="lvl" class="form-control input-medium">
                                                        <option value="3">District</option>
                                                        <option value="7">Health Facility</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 right">
                                            <div class="control-group">
                                                <label>&nbsp;</label>
                                                <div class="controls">
                                                    <input type="hidden" name="pk_id" value="<?= $pk_id ?>" />
                                                    <input type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
                                                    <input type="submit" class="btn btn-primary" value="<?= $strDo ?>" />
                                                </div>
                                            </div>
                                        </div>                                             
                                    </div>                                    
                                </form>
                            </div>
                        </div>
                        <div class="widget">
                            <div class="widget-head">
                                <?php
                                //Display All Roles
                                ?>
                                <h3 class="heading">All assigned data sources</h3>
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
            window.location = "index.php?Do=Edit&id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "index.php?Do=Delete&id=" + val;
            }
        }
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,Province, Stakehodler, Data Source,Created Date, Actions,#cspan");
            mygrid.setInitWidths("50,200,200,200,*,30,30");
            mygrid.setColAlign("center,left,left,left,left,center,center")
            mygrid.setColSorting("int,str,str,str,str,,");
            mygrid.setColTypes("ro,ro,ro,ro,ro,img,img");
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