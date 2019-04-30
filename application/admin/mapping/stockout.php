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
if (!empty($_REQUEST['stk-prov-filter']) && $_REQUEST['stk-prov-filter'] == 'submit') {
    $int_stk_id = $_REQUEST['stakeholder'];
    $int_pro_id = $_REQUEST['province'];

    $rowdata = array();
    $resset = mysql_query("SELECT * FROM alerts_mapping WHERE stakeholder_id = $int_stk_id AND province_id = $int_pro_id AND warehouse_id IS NULL");
    if (mysql_num_rows($resset) > 0) {
        while ($resrow = mysql_fetch_assoc($resset)) {
            $rowdata[$resrow['hf_type_id'] . "_" . $resrow['product_id']] = $resrow['value'];
        }
    }
}
//Including file
//include("xml_roles.php");
?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content">
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
                        <h3 class="page-title row-br-b-wp">Stock out alert mapping</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filters</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="" name="frm" id="frm">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="control-group">
                                                <label>Stakeholder<font color="#FF0000">*</font></label>
                                                <div class="controls">
                                                    <select name="stakeholder" id="stakeholder" class="form-control input-large" required="required">
                                                        <option value="">Select</option>
                                                        <?php
                                                        //Populate landing_resource_id combo
                                                        $qry1 = "SELECT
	stakeholder.stkname,
	stakeholder.stkid
FROM
	stakeholder
WHERE
	stakeholder.lvl = 1
AND stakeholder.stk_type_id IN (0, 1)";
                                                        $qryRes1 = mysql_query($qry1);
                                                        while ($row1 = mysql_fetch_assoc($qryRes1)) {
                                                            if ($int_stk_id == $row1['stkid']) {
                                                                $var_stk = $row1['stkname'];
                                                            }
                                                            $sel = ($int_stk_id == $row1['stkid']) ? 'selected="selected"' : '';
                                                            echo "<option value=\"" . $row1['stkid'] . "\" $sel>" . $row1['stkname'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="control-group">
                                                <label>Province<font color="#FF0000">*</font></label>
                                                <div class="controls">
                                                    <select name="province" id="province" class="form-control input-large" required="required">
                                                        <option value="">Select</option>
                                                        <?php
                                                        //Populate landing_resource_id combo
                                                        $qry = "SELECT
	tbl_locations.PkLocID,
	tbl_locations.LocName
FROM
	tbl_locations
WHERE
	tbl_locations.LocLvl = 2
AND tbl_locations.ParentID = 10";
                                                        $qryRes = mysql_query($qry);
                                                        while ($row = mysql_fetch_assoc($qryRes)) {
                                                            if ($int_pro_id == $row['PkLocID']) {
                                                                $var_prov = $row['LocName'];
                                                            }
                                                            $sel = ($int_pro_id == $row['PkLocID']) ? 'selected="selected"' : '';
                                                            echo "<option value=\"" . $row['PkLocID'] . "\" $sel>" . $row['LocName'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="control-group">
                                                <label>&nbsp;</label>
                                                <div class="controls">
                                                    <button  class="btn btn-primary" value="submit" type="submit" name="stk-prov-filter"> Go </button>
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
                if (!empty($_REQUEST['stk-prov-filter']) && $_REQUEST['stk-prov-filter'] == 'submit') {
                    $HealthFacilityType->m_npkId = $objstakeholderitem->m_npkId = $_REQUEST['stakeholder'];
                    $objMIs = $objstakeholderitem->GetstakeholderItemsById();
                    $objTypes = $HealthFacilityType->GetAllHealthFacilityType();
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widget">
                                <div class="widget-head">
                                    <?php
                                    //Display All Roles
                                    ?>
                                    <h3 class="heading"><?php echo $var_stk . " - " . $var_prov; ?></h3>
                                </div>
                                <div class="widget-body">
                                    <table width="100%" cellpadding="0" cellspacing="0" align="center" class="table table-condensed table-hover">                                    
                                        <tr>
                                            <th width="5%">X</th>
                                            <td>means facilities donâ€™t provide the same product </td>
                                        </tr>
                                        <tr>
                                            <th width="5%">N/A</th>
                                            <td>means facilities provide the services however product is not available but reference to last yellow rows in each box  its means not applicable</td>
                                        </tr>
                                        <tr>
                                            <th width="5%">Available</th>
                                            <td>means product is available and they are providing the services as well</td>
                                        </tr>
                                    </table>
                                    <form action="stockout_configuration.php" name="soconfig" method="post">
                                        <table width="100%" cellpadding="0" cellspacing="0" align="center" class="table table-condensed table-bordered">                                    
                                            <tr>
                                                <th></th>
                                                <?php
                                                $cnt = 1;
                                                while ($Rowrankss = mysql_fetch_object($objMIs)) {
                                                    if (!in_array($Rowrankss->itm_id, array("30","31","32","34"))) {
                                                        $itemarray[$cnt] = $Rowrankss->itm_id;
                                                        $cnt++;
                                                        ?>
                                                        <th style="width: 10%"><?php echo $Rowrankss->itm_name; ?></th>                              
                                                    <?php }
                                                }
                                                ?>
                                            </tr>
                                            <?php
                                            while ($objTypesrow = mysql_fetch_object($objTypes)) {
                                                ?>
                                                <tr>
                                                    <th><?php echo $objTypesrow->health_facility_type; ?></th>
                                                    <?php
                                                    for ($i = 1; $i < $cnt; $i++) {
                                                        $selX = ($rowdata[$objTypesrow->pk_id . "_" . $itemarray[$i]] == 'X') ? 'selected="selected"' : '';
                                                        $selNA = ($rowdata[$objTypesrow->pk_id . "_" . $itemarray[$i]] == 'N/A') ? 'selected="selected"' : '';
                                                        $selAvailable = ($rowdata[$objTypesrow->pk_id . "_" . $itemarray[$i]] == 'Available') ? 'selected="selected"' : '';
                                                        $selAvailableat = ($rowdata[$objTypesrow->pk_id . "_" . $itemarray[$i]] == 'Available (at selected HFs)') ? 'selected="selected"' : '';
                                                        ?>
                                                        <td class="center">
                                                            <select id="sector_<?php echo $int_pro_id; ?>_<?php echo $int_stk_id; ?>_<?php echo $itemarray[$i]; ?>_<?php echo $objTypesrow->pk_id; ?>" class="form-control input-sm" name="selected_options[<?php echo $objTypesrow->pk_id; ?>][<?php echo $itemarray[$i]; ?>]">
                                                                <option value="X" <?php echo $selX; ?>>X</option>
                                                                <option value="N/A" <?php echo $selNA; ?>>N/A</option>
                                                                <option value="Available" <?php echo $selAvailable; ?>>Available</option>
                                                                <option value="Available (at selected HFs)" <?php echo $selAvailableat; ?>>Available (at selected <?php echo $objTypesrow->health_facility_type; ?>)</option>
                                                            </select>

                                                            <?php
                                                            $id = "update_" . $int_pro_id . "_" . $int_stk_id . "_" . $itemarray[$i] . "_" . $objTypesrow->pk_id;
                                                            
                                                            echo ($rowdata[$objTypesrow->pk_id . "_" . $itemarray[$i]] == 'Available (at selected HFs)') ? '<button class="btn btn-sm" id="' . $id . '">update list</button>' : '';
                                                            ?>
                                                        </td>
                                                    <?php }
                                                    ?>                                        
                                                </tr>
    <?php } ?>
                                        </table>
                                        <div class="row">
                                            <div class="col-md-12 right"><button  class="btn btn-primary" type="submit"> Update </button></div>
                                        </div>
                                        <input name="prov_id" id="prov_id" value="<?php echo $int_pro_id; ?>" type="hidden"/>
                                        <input name="stk_id" id="stk_id" value="<?php echo $int_stk_id; ?>" type="hidden"/>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
<?php } ?>
            </div>
        </div>
    </div>
    <?php
    //Including files
    include PUBLIC_PATH . "/html/footer.php";
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
        $(function () {
            $("select[id^='sector_']").change(function (e) {
                var suffix = this.id;
                var sufval = $("#" + suffix).val();
                if (sufval == 'Available (at selected HFs)') {
                    window.open("selected_hf.php?t=" + suffix, "Selected Health Facilites", "width=800,height=800");
                }
            });

            $("button[id^='update_']").click(function (e) {
                e.preventDefault();
                var suffix = this.id;
                window.open("selected_hf.php?t=" + suffix, "Selected Health Facilites", "width=800,height=800");
            });
        });

        function editFunction(val) {
            window.location = "role_management.php?Do=Edit&id=" + val;
        }
        function delFunction(val) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "role_management.php?Do=Delete&id=" + val;
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
        //Unset session err
        unset($_SESSION['err']);
    }
    ?>
</body>
</html>