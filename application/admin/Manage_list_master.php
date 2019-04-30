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
    $list_master_id = $nstkId;
    //Get Location By Id
    $qry = "SELECT
                    list_master.list_master_name,
                    list_detail.pk_id,
                    list_detail.list_value,
                    list_detail.description,
                    list_detail.rank,
                    list_detail.reference_id,
                    list_detail.parent_id,
                    list_detail.list_master_id,
                    list_detail.created_by,
                    list_detail.created_date,
                    list_detail.modified_by,
                    list_detail.modified_date
            FROM
                list_master
            INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
            WHERE
                list_detail.pk_id = '" . $nstkId . "'
	";
    //echo $qry;
    $qryRes = mysql_query($qry);
    $RowEditStk = mysql_fetch_object($qryRes);
    //location_level
    $list_master_id = $RowEditStk->list_master_id;
    
    $list_value = $RowEditStk->list_value;
    //location_type
    $description = $RowEditStk->description;
    //ParentID
    $rank = $RowEditStk->rank;

    //Setting variables in session 
    $_SESSION['pk_id'] = $nstkId;

}

/**
 * 
 * Delete Location
 * 
 */
if ($strDo == "Delete") {
    $objloc->PkLocID = $nstkId;
    
    $strSql = "DELETE FROM  list_detail WHERE pk_id=" . $nstkId;
    //query result
    $rsSql = mysql_query($strSql) or die("Error Delete location");

    //Setting messages
    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    //Redirecting to ManageLocations
    echo "<script>window.location='Manage_list_master.php'</script>";
    exit;
}

//Including required file
include("xml_list_master.php");
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
                        <h3 class="page-title row-br-b-wp">List Master/Detail Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <?php 
                                //display All location
                                ?>
                                <h3 class="heading"><?php echo $strDo; ?> List</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="Manage_list_master_action.php" name="managelocation" id="managelocation">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Master List <font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="master_id" id="master_id" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //populate master_id combo
                                                            $strSql = "SELECT * FROM list_master ";
                                                            $rsSql = mysql_query($strSql);
                                                            if(empty($list_master_id)) $list_master_id='';
                                                            if (mysql_num_rows($rsSql) > 0) {
                                                                while ($RowLoc2 = mysql_fetch_array($rsSql)) {
                                                                    ?>
                                                                    <option value="<?php echo $RowLoc2['pk_id']; ?>" <?php if ($RowLoc2['pk_id'] == $list_master_id) {echo 'selected="selected"';} ?>><?php echo $RowLoc2['list_master_name']; ?></option>
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
                                                    <label>Name<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input name="item_name" id="item_name" value="<?=isset($list_value)?$list_value:''?>" class="form-control input-medium"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                     <label>Description<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input name="item_desc" id="item_desc" value="<?=isset($description)?$description:''?>" class="form-control input-medium"/>
                                                   
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Rank<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="rank" id="rank" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                                 for($i=1;$i<20;$i++)
                                                                 {
                                                                     echo '<option value="'.$i.'" '.((isset($rank) && $rank == $i)?' selected ':'').'>'.$i.'</option>';
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
                                            
                                            <div class="col-md-9 right">
                                                <div class="control-group">
                                                    <div class="control-group">
                                                        <label>&nbsp;</label>
                                                        <div class="controls">
                                                            <input type="hidden" name="detail_id" value="<?= $nstkId ?>" />
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
                                <h3 class="heading">All Items</h3>
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
            window.location = "Manage_list_master.php?Do=Edit&Id=" + val;
        }
        //Delete Manage Locations
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "Manage_list_master.php?Do=Delete&Id=" + val;
            }
        }
        var mygrid;
        //Initializing Grid
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='List Name'>List Name</span>,<span title='Item Name'>Item Name</span>,<span title='Rank'>Rank</span>,<span title='Description'>Description</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            mygrid.attachHeader(",#select_filter,#text_filter,#select_filter,#text_filter");
            mygrid.setInitWidths("50,200,150,150,*,30,30");
            mygrid.setColAlign("center,left,left,left,left,center,center")
            mygrid.setColSorting("int,str,str,str,str");
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
        //Unset session
        unset($_SESSION['err']);
    }
    ?>
</body>
<!-- END body -->
</html>