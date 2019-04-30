<?php
/**
 * MoS Manage
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
//n
$n = 0;
//itm_id
$itm_id = 0;
//productidz
$productidz = '';
//strDo
$strDo = '';
//itemrecordid
$itemrecordid = '';
//longterm
$longterm = '';
//shortterm
$shortterm = '';
//sclend
$sclend = '';
//sclstart
$sclstart = '';
//colorcode
$colorcode = '';
//stakeholderid
$stakeholderid = '';
//levelid
$levelid = '';
//itemid
$itemid = '';
//product_name
$product_name = '';
//buttonA
$buttonA = 'Add';
//colorcodee
$colorcodee = '';
//shortterm1
$shortterm1 = '';
//longterm1
$longterm1 = '';
//sclstart1
$sclstart1 = '';
//editid
$editid = 0;
//number
$number = 1;
//register globals
if (!ini_get('register_globals')) {
    $superglobals = array($_GET, $_POST, $_COOKIE, $_SERVER);
    if (isset($_SESSION)) {
        array_unshift($superglobals, $_SESSION);
    }
    foreach ($superglobals as $superglobal) {
        extract($superglobal, EXTR_SKIP);
    }
    ini_set('register_globals', true);
}

if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //Getting Do
    $strDo = $_REQUEST['Do'];
}
/**
 * Edit
 */
if ($strDo == "Edit") {
    $_SESSION['editid'] = $_REQUEST['Id'];
    $sql = "select * from mosscale_tab where row_id='" . $_SESSION['editid'] . "'";
    $sql_query = mysql_query($sql);
    $row = mysql_fetch_array($sql_query);
    //assigning values to variables
    //itemrecordid
    $itemrecordid = $row['itmrec_id'];
    //shortterm1
    $shortterm1 = $row['shortterm'];
    //longterm1
    $longterm1 = $row['longterm'];
    //sclstart1
    $sclstart1 = $row['sclstart'];
    //sclend
    $sclend = $row['sclsend'];
    //colorcodee
    $colorcodee = $row['colorcode'];
    //stakeholderid
    $stakeholderid = $row['stkid'];
    //levelid
    $levelid = $row['lvl_id'];
    //itemid
    $itemid = $row['item_id'];

    if ($strDo == "Edit") {
        $buttonA = 'Edit';
    }
}

//deleting values
if ($strDo == "Delete") {
    $sql = "Delete from mosscale_tab where row_id='" . $_REQUEST['Id'] . "'";
    $delete = mysql_query($sql);
    //Display message
    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    //Redirecting to MoSManage
    echo '<script>window.location="MoSManage.php"</script>';
    exit;
}

//Add query
if ((isset($_POST['Add'])) && ($_REQUEST['hdnToDo'] == 'Add')) {
    $itmid = $_POST['itm_id'];
    $strSQL = "select itm_id from itminfo_tab where itmrec_id='" . $itmid . "'";
    $sql = mysql_query($strSQL);
    $row = mysql_fetch_array($sql);

    $itmrec_id = $itmid;
    //Getting shortterm
    $shortterm = $_POST['shortterm'];
    //Getting longterm
    $longterm = $_POST['longterm'];
    //Getting sclstart
    $sclstart = $_POST['sclstart'];
    //Getting sclsend
    $sclsend = $_POST['sclsend'];
    //Getting colorcode
    $colorcode = $_POST['colorcode'];
    //Getting stkid
    $stkid = $_POST['stkid'];
    //Getting lvl_id
    $lvl_id = $_POST['lvl_id'];
    //Add query
    $sqll = "insert into mosscale_tab set itmrec_id='" . $itmrec_id . "',
							shortterm='" . $shortterm . "',
							longterm='" . $longterm . "',
							sclstart='" . $sclstart . "',
							sclsend='" . $sclsend . "',	
							stkid='" . $stkid . "',										
							colorcode='" . $colorcode . "',
							lvl_id='" . $lvl_id . "'";
    //Query result
    $row1 = mysql_query($sqll) or die("Error Mos Manage Item");
    //Display messages
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
    //Redirecting to MoSManage
    header("location:MoSManage.php");
    exit;
}

//edit vallues
if (isset($_POST['Add']) && ($_REQUEST['hdnToDo'] == 'Edit')) {
    $itmid1 = $_POST['itm_id'];
    $strSQL = "select itm_id from itminfo_tab where itmrec_id='" . $itmid1 . "'";
    $sql = mysql_query($strSQL);
    $row = mysql_fetch_array($sql);

    $itmrec_id = $itmid1;
    //Getting itm_id
    $itm_id = $row['itm_id'];
    //Getting shortterm
    $shortterm = $_POST['shortterm'];
    //Getting longterm
    $longterm = $_POST['longterm'];
    //Getting sclstart
    $sclstart = $_POST['sclstart'];
    //Getting sclsend
    $sclsend = $_POST['sclsend'];
    //Getting colorcode
    $colorcode = $_POST['colorcode'];
    //Getting stkid
    $stkid = $_POST['stkid'];
    //Getting lvl_id
    $lvl_id = $_POST['lvl_id'];
    //Edit query
    $sqll = "update mosscale_tab set shortterm='" . $shortterm . "',
							longterm='" . $longterm . "',
							sclstart='" . $sclstart . "',
							sclsend='" . $sclsend . "',	
							stkid='" . $stkid . "',										
							colorcode='" . $colorcode . "',
							itmrec_id='" . $itmrec_id . "',
							lvl_id='" . $lvl_id . "' where row_id='" . $_SESSION['editid'] . "'";
    //Query results
    $row = mysql_query($sqll) or die("Error update Mos Manage Item");
    //Display messages
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
    //Redirect to MoSManage
    header("location:MoSManage.php");
    exit;
}
//Including file
include("xml_mos.php");
?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
<?php include $_SESSION['menu']; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">MoS Scale Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Product's Group</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frmDatamos" id="frmDatamos" action="MoSManage.php" method="POST" enctype="MULTIPART/FORM-DATA" >
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Product<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="itm_id" tabindex="6" id="itm_id" class="form-control input-medium">
                                                            <option value="">Select</option>
<?php
//Query for items
$strSQL = "select itm_id,itmrec_id,itm_name from itminfo_tab order by itmrec_id";
$rsTemp1 = mysql_query($strSQL) or die(mysql_error());
//Populate itm_id combo
while ($rsRow1 = mysql_fetch_array($rsTemp1)) {
    ?>
                                                                <option value=<?php echo $rsRow1['itmrec_id']; ?> <?php if ($rsRow1['itmrec_id'] == $itemrecordid) {
                                                                echo 'selected="selected"';
                                                            } ?> ><?php echo $rsRow1['itm_name']; ?></option>
                                                                <?php
                                                            }
                                                            mysql_free_result($rsTemp1);
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Short Description<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input autocomplete="off" type="text" name="shortterm" id="shortterm" value="<?php echo $shortterm1; ?>" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Long Description<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="text" name="longterm" id="longterm" value="<?php echo $longterm1; ?>"  class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Color Code<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input value="<?php echo $colorcodee; ?>" class="colorpicker-default form-control input-medium" type="text" name="colorcode" id="colorcode">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Scale Start<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="text" name="sclstart" id="sclstart" value="<?php echo $sclstart1; ?>" class="form-control input-medium right">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Scale End<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="text" name="sclsend" id="sclsend" value="<?php echo $sclend; ?>" class="form-control input-medium right">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Stakeholder<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="stkid" id="stkid" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //Query for stakeholder
                                                            $query = "SELECT * FROM `stakeholder` where parentid is null";
                                                            $rs = mysql_query($query) or die(mysql_error());
                                                            //Populate stkid combo
                                                            while ($row1 = mysql_fetch_array($rs)) {
                                                                ?>
                                                                <option value="<?php echo $row1['stkid']; ?>" <?php if ($row1['stkid'] == $stakeholderid) {
                                                                echo 'selected="selected"';
                                                            } ?>><?php echo $row1['stkname']; ?></option>
<?php }
?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Distribution Level<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select  name="lvl_id" id="lvl_id" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //Query for level
                                                            $query = "SELECT * FROM `tbl_dist_levels`";
                                                            $rs = mysql_query($query) or die(mysql_error());
                                                            //Populate lvl_id
                                                            while ($row = mysql_fetch_array($rs)) {
                                                                ?>
                                                                <option value="<?php echo $row['lvl_id']; ?>" <?php if ($row['lvl_id'] == $levelid) {
                                                                echo 'selected="selected"';
                                                            } ?>><?php echo $row['lvl_name']; ?></option>
<?php }
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
                                                    <div class="control-group">
                                                        <label>&nbsp;</label>
                                                        <div class="controls">
                                                            <input type="hidden" name="LogedUser" value="<?php echo $LogedUser; ?>">
                                                            <input type="hidden" name="LogedID" value="<?php echo $LogedID; ?>">
                                                            <input type="hidden" name="LogedUserWH" value="<?php echo $LogedUserWH; ?>">
                                                            <input type="hidden" name="LogedUserType" value="<?php echo $LogedUserType; ?>">
                                                            <input type="hidden" name="hdnToDo" value="<?= $buttonA ?>" />
                                                            <input class="btn btn-primary" type="submit" value="<?php echo $buttonA; ?>" name="Add"/>
                                                            <input class="btn btn-info" name="btnAdd" type="button" id="btnCancel" value="Cancel" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
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
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(9, true);
                                                    mygrid.setColumnHidden(10, true);
                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                    mygrid.setColumnHidden(9, false);
                                                    mygrid.setColumnHidden(10, false);" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(9, true);
                                                    mygrid.setColumnHidden(10, true);
                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                    mygrid.setColumnHidden(9, false);
                                                    mygrid.setColumnHidden(10, false);" />
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
            window.location = "MoSManage.php?Do=Edit&Id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "MosManage.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Item Name'>Item Name</span>,<span title='Stakeholder Name'>Stakeholder</span>,<span title='Distribution Level'>Distribution Level</span>,<span title='Short Description'>Short Description</span>,<span title='Long Description'>Long Description</span>,<span title='Scale Start'>Scale Start</span>,<span title='Scale End'>Scale End</span>,<span title='Color Code'>Color Code</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            mygrid.attachHeader(",#select_filter,#select_filter,#select_filter,#select_filter,,,,,,");
            mygrid.setInitWidths("60,150,120,120,120,*,100,100,100,30,30");
            mygrid.setColAlign("center,left,left,left,left,left,right,right,center,center,center")
            mygrid.setColSorting("int,str,str,str,str,str,int,int,,,");
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,img,img");
            mygrid.setSkin("light");
            mygrid.init();
            mygrid.clearAll();
            mygrid.loadXMLString('<?php echo $xmlstore; ?>');
        }
    </script>
    <style>
        #colocode
        {
            border:1px solid #CDCFCF;
            border-radius: 3px 3px 3px 3px;
            box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.1) inset;
        }
    </style>
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
    <script type="text/javascript" src="<?php echo PUBLIC_URL; ?>js/jscolor.js"></script>
</body>
</html>