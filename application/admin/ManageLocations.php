<?php
/**
 * Manage Locations
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

// Ajax call for districts
if (isset($_REQUEST['id'])) {
    $qry = "SELECT
				tbl_locations.PkLocID,
				tbl_locations.LocName
			FROM
				tbl_locations
			WHERE tbl_locations.LocLvl = 3 AND tbl_locations.ParentID = '" . $_REQUEST['id'] . "'
			ORDER BY tbl_locations.LocName";
    $qryRes = mysql_query($qry);
    while ($row = mysql_fetch_array($qryRes)) {
        ?>
        <option value="<?php echo $row['PkLocID']; ?>" <?php echo (isset($_SESSION['ParentID']) && $_SESSION['ParentID'] == $row['PkLocID']) ? 'selected=selected' : '' ?>><?php echo $row['LocName']; ?></option>
        <?php
    }
    exit;
}
include(PUBLIC_PATH . "html/header.php");

//Initializing variables
$act = 2;
$strDo = "Add";
//nwharehouseId
$nwharehouseId = 0;
//nstkId
$nstkId = 0;
//stkOfficeId
$stkOfficeId = "";
//dist_id
$dist_id = 0;
//prov_id
$prov_id = 0;
//stkid
$stkid = 0;
//wh_type_id
$wh_type_id = 0;
//stkname
$stkname = "";
//test
$test = 'false';

//Getting Do
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    $strDo = $_REQUEST['Do'];
}
//Getting Id
if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    $nstkId = $_REQUEST['Id'];
}
//Getting pk_id
if (isset($_SESSION['pk_id'])) {
    unset($_SESSION['pk_id']);
}

/**
 * 
 * Edit Location
 * 
 */
$province=$location_name='';
if ($strDo == "Edit") {
    $objloc->PkLocID = $nstkId;
    //Get Location By Id
    $rsloc = $objloc->GetLocationById();
    $RowEditStk = mysql_fetch_object($rsloc);
    //location_level
    $location_level = $RowEditStk->LocLvl;
    //location_type
    $location_type = $RowEditStk->LocType;
    //ParentID
    $ParentID = $RowEditStk->ParentID;
    //location_name
    $location_name = $RowEditStk->LocName;
    //province
    $province = $RowEditStk->Province;

    //Setting variables in session 
    $_SESSION['pk_id'] = $nstkId;

    $_SESSION['loc_type'] = $location_type;
    $_SESSION['ParentID'] = $ParentID;
}

/**
 * 
 * Delete Location
 * 
 */
if ($strDo == "Delete") {
    $objloc->PkLocID = $nstkId;
    $objloc->DeleteLocation();

    //Setting messages
    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    //Redirecting to ManageLocations
    echo "<script>window.location='ManageLocations.php'</script>";
    exit;
}

$objloc->LocLvl = 2;
//Get All Locations
if($_SESSION['user_level'] == '2')
{
    $rsloc = $objloc->GetLocationsById($_SESSION['user_province1']);
}
else
{
    $rsloc = $objloc->GetAllLocationsL2();
}
//Including required file
include("xml_location.php");
?>
</head>
<!-- BEGIN body -->
<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Location Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <?php 
                                //display All location
                                ?>
                                <h3 class="heading"><?php echo $strDo; ?> Location</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="ManageLocationAction.php" name="managelocation" id="managelocation">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Location Level<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="loc_level" id="loc_level" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //populate loc_level combo
                                                            $strSql = "SELECT * FROM tbl_dist_levels WHERE lvl_id IN (3,4)";
                                                            $rsSql = mysql_query($strSql);
                                                            if(empty($location_level)) $location_level='';
                                                            if (mysql_num_rows($rsSql) > 0) {
                                                                while ($RowLoc2 = mysql_fetch_array($rsSql)) {
                                                                    ?>
                                                                    <option value="<?php echo $RowLoc2['lvl_id']; ?>" <?php if ($RowLoc2['lvl_id'] == $location_level) {echo 'selected="selected"';} ?>><?php echo $RowLoc2['lvl_name']; ?></option>
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
                                                    <label>Province<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="provinces" id="provinces" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //populate provinces combo
                                                            if ($rsloc != FALSE && mysql_num_rows($rsloc) > 0) {
                                                                while ($RowLoc = mysql_fetch_object($rsloc)) {
                                                                    ?>
                                                                    <option value="<?= $RowLoc->PkLocID ?>" <?php if ($RowLoc->PkLocID == $province) {
                                                                echo 'selected="selected"';
                                                            } ?>>
                                                                    <?= $RowLoc->LocName ?>
                                                                    </option>
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
                                                    <label>Location Type<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="loc_type" id="loc_type" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3" id="districts" style="display:none">
                                                <div class="control-group">
                                                    <label>District<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="dist_id" id="dist_id" class="form-control input-medium">
                                                            <option value="">Select</option>
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
                                                    <label>Location Name<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input autocomplete="off" name="loc_name" id="loc_name" type="text" value="<?php echo $location_name; ?>" size="30" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-9 right">
                                                <div class="control-group">
                                                    <div class="control-group">
                                                        <label>&nbsp;</label>
                                                        <div class="controls">
                                                            <input type="hidden" name="hdnstkId" value="<?= $nstkId ?>" />
                                                            <input  type="hidden" name="hdnToDo" value="<?= $strDo ?>" />
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
                                <h3 class="heading">All Locations</h3>
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
    //Including Required files
    include PUBLIC_PATH . "/html/footer.php";
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
        //Edit Manage Locations
        function editFunction(val) {
            window.location = "ManageLocations.php?Do=Edit&Id=" + val;
        }
        //Delete Manage Locations
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageLocations.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
        //Initializing Grid
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Province'>Province</span>,<span title='Location Level'>Location Level</span>,<span title='Location Type'>Location Type</span>,<span title='Location Name'>Location Name</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            mygrid.attachHeader(",#select_filter,#select_filter,#select_filter,#text_filter");
            mygrid.setInitWidths("50,200,150,150,*,30,30");
            mygrid.setColAlign("center,left,left,left,left,center,center")
            mygrid.setColSorting("int,str,str,str,str");
            mygrid.setColTypes("ro,ro,ro,ro,ro,img,img");
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }

        $(document).ready(function Stakeholders() {
<?php
if (isset($_REQUEST['Do'])) {
    ?>
    /**
     * 
     * show Loc Types
     * 
     */            
    showLocTypes();
    <?php
}
?>

            $("#loc_level").change(function() {
                showLocTypes();
            });

            $('#provinces').change(function() {
                
                /**
                 * 
                 * show Districts
                 * 
                 */
                showDistricts();
            })
        });
        /**
         * 
         * show Location Types
         * 
         */
        function showLocTypes()
        {
            if (bid == 4)
            {
                $('#districts').show();
            }
            else
            {
                $('#districts').hide();
            }
            $("#loc_type").html("<option value=''>Please wait...</option>");
            var bid = $("#loc_level").val();
            $.post("getfromajax.php", {ctype: 9, id: bid}, function(data) {
                $("#loc_type").html(data);
<?php
if (isset($_REQUEST['Do'])) {
    ?>
                    showDistricts();
    <?php
}
?>
            });
        }
        /**
        * 
        * show Districts 
        * 
         */ 
        function showDistricts()
        {
            var bid = $("#loc_level").val();
            if (bid == 4)
            {
                $('#districts').show();
                $.post("ManageLocations.php", {id: $('#provinces').val()}, function(data) {
                    $("#dist_id").html(data);
                });
            }
            else
            {
                $('#districts').hide();
            }
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