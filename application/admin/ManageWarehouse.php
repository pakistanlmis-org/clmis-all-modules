<?php
/**
 * Manage Warehouse
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
//act
$act = 2;
//strDo
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
$stkofc_lvl = '';
//wh_type_id
$wh_type_id = 0;
//whType
$whType = '';
//stkname
$stkname = "";
//test
$test = 'true';
//reporting_start_month
$reporting_start_month = '';
$im_date = '';
//editable_data_entry_months
$editable_data_entry_months = 2;
//is_lock_data_entry
$is_lock_data_entry = 0;
//wh_rank
$wh_rank = '';
//is_active
$is_active = '1';
//hf_code
$hf_code = '';

if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //Getting Do
    $strDo = $_REQUEST['Do'];
}

if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    //Getting nstkId
    $nstkId = $_REQUEST['Id'];
}

/**
 * Delete
 */
if ($strDo == "Delete") {
    $objwarehouse->m_npkId = $nstkId;
    //Delete warehouse
    $objwarehouse->Deletewarehouse();

    $_SESSION['err']['text'] = 'Data has been successfully deleted.';
    $_SESSION['err']['type'] = 'success';
    //Redirecting to ManageWarehouse
    echo '<script>window.location="ManageWarehouse.php"</script>';
    exit;
}
// allowIM
if ($strDo == "im_allowed") {
    $objwarehouse->m_npkId = $nstkId;
    $objwarehouse->allowIM($nstkId);
    exit;
}

/**
 * Edit
 */
if ($strDo == "Edit") {

    $objwarehouse->m_npkId = $nstkId;

    $objWhData->m_wh_id = $nstkId;
    //Get Data By WhId
    $isDataAvailable = $objWhData->getDataByWhId();
    //Get Warehouse ById
    $rswarehouse = $objwarehouse->GetWarehouseById();
    //
    if ($rswarehouse != FALSE && mysql_num_rows($rswarehouse) > 0) {
        $RowEditStk = mysql_fetch_object($rswarehouse);
        //echo '<pre>';print_r($RowEditStk);exit;
        $stkofc_lvl= $RowEditStk->stkofc_level;
        //stkid
        $stkid = $RowEditStk->stkid;
        //stkname
        $stkname = $RowEditStk->stkname;
        //wh_name
        $wh_name = $RowEditStk->wh_name;
        //stkOfficeId
        $stkOfficeId = $RowEditStk->stkofficeid;
        //Setting stkOfficeId in session
        $_SESSION['user_stakeholder_office'] = $stkOfficeId;
        //prov_id
        $prov_id = $RowEditStk->prov_id;
        //dist_id
        $dist_id = $RowEditStk->dist_id;
        //Setting dist_id in session
        $_SESSION['dist_id'] = $dist_id;
        //Setting pk_id in session
        $_SESSION['pk_id'] = $nstkId;
        //province
        $province = $RowEditStk->province;
        //district
        $district = $RowEditStk->district;
        //whType
        $whType = $RowEditStk->hf_type_id;
        //reporting_start_month
        $reporting_start_month = substr($RowEditStk->reporting_start_month, 0, -3);
        
        $im_date = substr($RowEditStk->im_start_month, 0, -3);
        //editable_data_entry_months
        $editable_data_entry_months = $RowEditStk->editable_data_entry_months;
        //is_lock_data_entry
        $is_lock_data_entry = $RowEditStk->is_lock_data_entry;
        //wh_rank
        $wh_rank = $RowEditStk->wh_rank;
        //hf_code
        $hf_code = $RowEditStk->dhis_code;
        //is_active
        $is_active = $RowEditStk->is_active;
        //Show IM
        $is_allowed_im = $RowEditStk->is_allowed_im;
        if ($stkOfficeId != '') {
            $objstk->m_npkId = $stkOfficeId;
            //Get stakeholder name
            $stkOffice = $objstk->get_stakeholder_name();
        }
    }
}

if ($_SESSION['user_id'] == 1) {
    $max = 99;
} else {
    $max = 2;
}
//Get All Stakeholders
$rsStakeholders = $objstk->GetAllStakeholders();
$rsStakeholders1 = $objstk->GetAllStakeholders();
//Get WH Types
$rsWHTypes = $objwarehouse->GetWHTypes();

$objloc->LocLvl = 2;

if(!empty($_SESSION['user_level']) && $_SESSION['user_level']=='2')
{
    if(!empty($_SESSION['user_province1']))
        $pro=$_SESSION['user_province1'];
    $rsloc = $objloc->GetLocationsById($pro); 
    $rsloc1 = $objloc->GetLocationsById($pro);    
}
else
{
    $rsloc = $objloc->GetAllLocations();
    $rsloc1 = $objloc->GetAllLocations();

}

//echo '<pre>';print_r($_SESSION);exit;
?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doOnLoad()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Warehouse Management</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading"><?php echo $strDo; ?> Warehouse</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="ManagewarehouseAction.php" name="managewarehouses" id="managewarehouses">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Stakeholder<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="Stakeholders" id="Stakeholders" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //Populate Stakeholders combo
                                                            while ($RowGroups = mysql_fetch_object($rsStakeholders)) {
                                                                ?>
                                                                <option value="<?= $RowGroups->stkid ?>" <?php echo ($stkid == $RowGroups->stkid) ? 'selected' : ''; ?>>
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
                                                    <label>Office Type<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="StakeholdersOffices" id="StakeholdersOffices" class="form-control input-medium">
                                                            <option value="">Select</option>
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
                                                            while ($RowLoc = mysql_fetch_object($rsloc)) {
                                                                ?>
                                                                <option value="<?= $RowLoc->PkLocID ?>" <?php if ($RowLoc->PkLocID == $prov_id) {
                                                                echo 'selected="selected"';
                                                            } ?>>
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
                                                    <label>District<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <select name="districts" id="districts" class="form-control input-medium">
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
                                                    <label>Warehouse / Store Name<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input  name="wh_name" autocomplete="off" id="wh_name" type="text" value="<?php echo (!empty($wh_name)?$wh_name:''); ?>" size="30" class="form-control input-medium">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3" id="facility_type_div" style="<?=($stkofc_lvl=='7')?'':'display:none'?>">
                                                <div class="control-group">
                                                    <label>Health Facility Type</label>
                                                    <div class="controls">
                                                        <select name="wh_type" id="wh_type" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //Populate wh_type combo
                                                            while ($row = mysql_fetch_object($rsWHTypes)) {
                                                                ?>
                                                                <option value="<?= $row->pk_id ?>" <?php echo ($whType == $row->pk_id) ? 'selected' : ''; ?>>
                                                                <?= $row->hf_type ?>
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
                                                    <label>1st Data Entry Month<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="text" autocomplete="off" name="reporting_start_month" id="reporting_start_month" value="<?php echo $reporting_start_month; ?>" class="form-control input-small" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Editable Data Month<font color="#FF0000">*</font></label>
                                                    <div class="controls">
                                                        <input type="text" value="<?php echo $editable_data_entry_months; ?>" autocomplete="off" max="<?php echo $max; ?>" min="1" maxlength="2" name="editable_data_entry_months" id="editable_data_entry_months" class="form-control input-small" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label></label>
                                                    <div class="controls">
                                                        <input type="radio" <?php echo ($is_lock_data_entry == 1) ? 'checked="checked"' : ''; ?> value="1" name="is_lock_data_entry" id="is_lock_data_entry" />
                                                        Lock Data Entry<br>
                                                        <input type="radio" <?php echo ($is_lock_data_entry == 0) ? 'checked="checked"' : ''; ?> value="0" name="is_lock_data_entry" id="is_lock_data_entry" />
                                                        Un-lock Data Entry </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Facility Code</label>
                                                    <div class="controls">
                                                        <input type="text" name="hf_code" id="hf_code" class="form-control input-small" value="<?= $hf_code; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Rank</label>
                                                    <div class="controls">
                                                        <input type="text" min="" name="wh_rank" id="wh_rank" class="form-control input-small" style="border:1px solid #d8d9da;text-align:right; padding-right:5px;" value="<?= round($wh_rank) ?>" />
                                                        <input type="hidden" name="wh_rank_old" value="<?= (!empty($wh_rank)) ? round($wh_rank) : '' ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="radio" <?php echo ($is_active == 1) ? 'checked="checked"' : ''; ?> value="1" name="is_active" id="is_active" />
                                                        Active<br>
                                                        <input type="radio" <?php echo ($is_active == 0) ? 'checked="checked"' : ''; ?> value="0" name="is_active" id="is_active" />
                                                        In-Active
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <?php
                                                        if(!empty($is_allowed_im) && $is_allowed_im == 1){
                                                            echo '<i style="color:green !important;" class="fa fa-check "></i>';
                                                            echo 'Inventory Management is Enabled';
                                                            echo '<input type="hidden" '.((!empty($is_allowed_im) && $is_allowed_im == 1) ? 'checked="checked"' : '').' value="1" name="is_allowed_im" id="is_allowed_im" />';
                                                        }
                                                        else{
                                                        ?>
                                                        <input type="checkbox" <?php echo (!empty($is_allowed_im) && $is_allowed_im == 1) ? 'checked="checked"' : ''; ?> value="1" name="is_allowed_im" id="is_allowed_im" />
                                                        Enable Inventory Management
                                                        <?php
                                                        }
                                                        ?>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="im_date_div" style="<?php echo (!empty($is_allowed_im) && $is_allowed_im == 1) ? '' : 'display:none;'; ?>">
                                                <div class="control-group">
                                                    <label>IM start month<font color="#FF0000">*</font></label>
                                                    
                                                    <div class="controls">
                                                        <?php
                                                        if(!empty($is_allowed_im) && $is_allowed_im == 1){
                                                            echo '<label name="im_date" id="im_date"  class="btn green btn-sm"> '.  $im_date   .' </label>';
                                                            echo '<br/>Once IM is enabled at a warehouse, you can NOT disable it.';
                                                            echo '<input type="hidden" autocomplete="off" name="im_date" id="im_date" value="'. $im_date . '" class="form-control input-small" />';
                                                        }
                                                        else{
                                                        ?>
                                                        <input type="text" autocomplete="off" name="im_date" id="im_date" value="<?php echo $im_date; ?>" class="form-control input-small" />
                                                    <?php
                                                        }
                                                        ?>
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
                                                        <input name="btnAdd" type="button" id="btnCancel" value="Cancel" class="btn btn-info" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';">
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
                                <h3 class="heading">All Warehouses</h3>
                            </div>
                            <div class="widget-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label>Province</label>
                                                <div class="controls">
                                                    <select name="prov" id="prov" class="form-control input-sm">
                                                        <option value="">Select</option>
                                                        <?php
                                                        //Populate prov combo
                                                        while ($RowLoc = mysql_fetch_object($rsloc1)) {
                                                            ?>
                                                            <option value="<?= $RowLoc->PkLocID ?>">
                                                            <?= $RowLoc->LocName ?>
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
                                                <label>District</label>
                                                <div class="controls">
                                                    <select name="dist" id="dist" class="form-control input-sm">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label>Stakeholder</label>
                                                <div class="controls">
                                                    <select name="stk" id="stk" class="form-control input-sm">
                                                        <option value="">Select</option>
                                                        <?php
                                                        //Populate stk combo
                                                        while ($RowGroups = mysql_fetch_object($rsStakeholders1)) {
                                                            ?>
                                                            <option value="<?= $RowGroups->stkid ?>">
                                                            <?= $RowGroups->stkname ?>
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
                                                <label>Office Type</label>
                                                <div class="controls">
                                                    <select name="stkofc" id="stkofc" class="form-control input-sm">
                                                        <option value="">Select</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label>Store/Facility</label>
                                                <div class="controls">
                                                    <input name="wh" id="wh" class="form-control input-sm" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label>&nbsp;</label>
                                                <div class="controls">
                                                    <button type="button" class="btn btn-primary input-sm" onClick="reloadGrid()">Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td style="text-align:right;">
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/pdf-32.png" onClick="mygrid.setColumnHidden(6, true);
                                                    mygrid.setColumnHidden(7, true);
                                                    mygrid.toPDF('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2pdf/server/generate.php');
                                                    mygrid.setColumnHidden(6, false);
                                                    mygrid.setColumnHidden(7, false);" />
                                            <img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.setColumnHidden(6, true);
                                                    mygrid.setColumnHidden(7, true);
                                                    mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');
                                                    mygrid.setColumnHidden(6, false);
                                                    mygrid.setColumnHidden(7, false);" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><div id="mygrid_container" style="width:100%; height:350px; background-color:white;overflow:hidden"></div>
                                            <div><span id="pagingArea"></span>&nbsp;<span id="infoArea"></span><span id="recfound"></span></div></td>
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
    <script type="text/javascript">
<?php if ($test == 'true') { ?>
            // JavaScript Document
            $(document).ready(function() {

                $('#StakeholdersOffices').change(function(e) {
                    if ($(this).val() != '') {
                        $.ajax({
                            url: 'getfromajax.php',
                            type: 'POST',
                            data: {ctype: 12, stkid: $(this).val()},
                            success: function(lvl) {
                                if (lvl == 7) {
                                    $('#facility_type_div').show();
                                } else {
                                    $('#facility_type_div').hide();
                                }
                            }
                        })
                    }
                });

                $('#reporting_start_month').datepicker({
                    dateFormat: "yy-mm"
                });
                 $('#im_date').datepicker({
                    dateFormat: "yy-mm"
                });
                showOfficeTypes();
                showDistricts();
                $("select#Stakeholders").change(function() {
                    // if changed after last element has been selected, will reset last boxes choice to default
                    showOfficeTypes();

                    $('#facility_type_div').hide();
                    showFacilityTypes();
                });
                $("select#stk").change(function() {
                    // if changed after last element has been selected, will reset last boxes choice to default
                    showOfficeTypes1();
                });

                $("select#Provinces").change(function() {
                    showDistricts();
                });

                $("select#prov").change(function() {
                    showDistricts1();
                });
                $("#is_allowed_im").change(function() {
//                    alert("check");
                    $("#im_date_div").toggle();
                });
                $("select#districts").change(function() {
                    $("select#Warehouses").html("<option value=''>Please wait...</option>");
                    var bid = $("select#districts option:selected").attr('value');
                    var pid = $("select#StakeholdersOffices option:selected").attr('value');

                    $.post("getfromajax.php", {
                        ctype: 5, id: bid, id2: pid
                    }, function(data) {
                        $("select#Warehouses").html(data);
                    });
                });
            }

            //  ---------------------------------------

            );
<?php } ?>
        function imAllowedFunction(val, name) {
            if (confirm("Are you sure you want to change Inventory Managment settings for this warehouse?")) {
                //window.location="ManageWarehouse.php?Do=im_allowed&Id="+val;
                $.post("ManageWarehouse.php", {Do: 'im_allowed', Id: val}, function(data) {
                });
            }
            else
            {
                $('input[name="' + name + '"]').attr('checked', 'checked');
            }
        }
        function showOfficeTypes()
        {
            var pid = $("select#Stakeholders option:selected").attr('value');
            if (pid == '')
            {
                $("select#Warehouses").html("<option value=''>Select</option>");
                return false;
            }
            $("select#Warehouses").html('<option value="" selected="selected">Choose...</option>');
            $("select#StakeholdersOffices").html("<option>Please wait...</option>");
            $("select#Warehouses").html("<option value=''>Please wait...</option>");
            $.post("getfromajax.php", {
                ctype: 1, id: pid
            }, function(data) {
                $("select#StakeholdersOffices").html(data);
            });
        }
        function showOfficeTypes1()
        {
            var pid = $("select#stk option:selected").attr('value');
            $("select#stkofc").html("<option>Please wait...</option>");
            $.post("getfromajax.php", {
                ctype: 1, id: pid
            }, function(data) {
                $("select#stkofc").html(data);
            });
        }
        function showDistricts()
        {
            var bid = $("select#Provinces option:selected").attr('value');
            if (bid == '')
            {
                $("select#districts").html("<option value=''>Select</option>");
                return false;
            }
            $("select#districts").html("<option value=''>Please wait...</option>");

            $.post("getfromajax.php", {
                ctype: 8, id: bid
            }, function(data) {
                $("select#districts").html(data);
            });
        }
        function showDistricts1()
        {
            var bid = $("select#prov option:selected").attr('value');
            if (bid == '')
            {
                $("select#dist").html("<option value=''>Select</option>");
                return false;
            }
            $("select#dist").html("<option value=''>Please wait...</option>");

            $.post("getfromajax.php", {
                ctype: 8, id: bid
            }, function(data) {
                $("select#dist").html(data);
            });
        }
        function showFacilityTypes() {
            if ($('#Stakeholders').val() != '') {
                $.ajax({
                    url: 'getfromajax.php',
                    type: 'POST',
                    data: {ctype: 11, stkid: $('#Stakeholders').val()},
                    success: function(data) {
                        $('#wh_type').html(data);
                    }
                })
            }
        }

        function editFunction(val) {
            window.location = "ManageWarehouse.php?Do=Edit&Id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "ManageWarehouse.php?Do=Delete&Id=" + val;
            }
        }

        // Grid
        var mygrid;
        var timeoutHnd;
        var flAuto = false;
        function doOnLoad() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Province'>Province</span>,<span title='District'>District</span>,<span title='Stakeholder'>Stakeholder</span>,<span title='Office type'>Office Type</span>,<span title='Warehouse'>Warehouse</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            //mygrid.attachHeader(",#select_filter,#select_filter,#select_filter,#text_filter,#text_filter,,");
            mygrid.setInitWidths("60,150,200,130,150,*,30,30");
            mygrid.setColAlign("center,left,left,left,left,left,center,center")
            mygrid.setColTypes("ro,ro,ro,ro,ro,ro,img,img");
            //mygrid.enableLightMouseNavigation(true);
            mygrid.enableRowsHover(true, 'onMouseOver');

            //available in pro version only
            if (mygrid.setColspan)
                mygrid.attachEvent("onBeforeSorting", customColumnSort)

            mygrid.setSkin("light");
            mygrid.init();
            mygrid.splitAt(1)
            mygrid.attachEvent("onBeforePageChanged", function() {
                if (!this.getRowsNum())
                    return false;
                return true;
            })
            //this code enables paging and sets its skin
            mygrid.enablePaging(true, 50, 15, "pagingArea", true, "infoArea");
            mygrid.setPagingSkin("bricks");


            //code below written to support standard edtiton
            //it written especially for current sample and may not work
            //in other cases, DON'T USE it if you have pro version
            mygrid.sortField_old = mygrid.sortField;
            mygrid.sortField = function() {
                mygrid.setColSorting("int,str,str,str,str,str");
                if (customColumnSort(arguments[0]))
                    mygrid.sortField_old.apply(this, arguments);
            }
            mygrid.sortRows = function(col, type, order) {
            }


            mygrid.attachEvent("onXLE", showLoading);
            mygrid.attachEvent("onXLS", function() {
                showLoading(true)
            })//setOnLoadingStart(function(){showLoading(true)})
            mygrid.loadXML("xml_warehouse.php?un=" + Date.parse(new Date()));
        }

        function showLoading(fl) {
            var span = document.getElementById("recfound")
            if (!span)
                return;

            if (!mygrid._serialise) {
                span.innerHTML = "<i>Loading... available in Professional Edition of dhtmlxGrid</i>"
                return;
            }
            span.style.color = "#009C00";
            if (fl === true)
                span.innerHTML = "loading...";
            else
                span.innerHTML = "";
        }
        function doSearch(ev) {
            if (!flAuto)
                return;
            var elem = ev.target || ev.srcElement;
            if (timeoutHnd)
                clearTimeout(timeoutHnd)
            timeoutHnd = setTimeout(reloadGrid, 500)
        }
        function reloadGrid() {
            var prov = document.getElementById("prov").value;
            var dist = document.getElementById("dist").value;
            var stk = document.getElementById("stk").value;
            var stkofc = document.getElementById("stkofc").value;
            var wh = document.getElementById("wh").value;
            showLoading(true)
            mygrid.clearAndLoad("xml_warehouse.php?prov=" + prov + "&dist=" + dist + "&stk=" + stk + "&stkofc=" + stkofc + "&wh=" + wh + "&orderBy=" + window.s_col + "&direction=" + window.a_direction);
            if (window.a_direction)
                mygrid.setSortImgState(true, window.s_col, window.a_direction);
        }
        function enableAutosubmit(state) {
            flAuto = state;
            document.getElementById("submitButton").disabled = state
        }
        function customColumnSort(ind) {
            /*if (ind==1) {
             alert("Table can't be sorted by this column.");
             if (window.s_col)
             mygrid.setSortImgState(true,window.s_col,window.a_direction);
             return false;
             }*/
            var a_state = mygrid.getSortingState();
            window.s_col = ind;
            window.a_direction = ((a_state[1] == "des") ? "asc" : "des");
            reloadGrid();
            return true;
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
        unset($_SESSION['err']);
    }
    ?>
    <style>
        .dhx_pline{width:100% !important;}
    </style>
</body>
</html>