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
if (isset($_REQUEST['t']) && !empty($_REQUEST['t'])) {
    $token = $_REQUEST['t'];
    list($txt, $prov, $stk, $product, $hf_type) = explode("_", $token);

    $rowdata = array();
    $resset = mysql_query("SELECT DISTINCT
prov.LocName AS Province,
stakeholder.stkname AS Stakeholder,
dist.LocName AS District,
tbl_warehouse.wh_name,
tbl_warehouse.wh_id
FROM
	wh_user
INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
INNER JOIN tbl_locations AS dist ON tbl_warehouse.dist_id = dist.PkLocID
INNER JOIN tbl_locations AS prov ON tbl_warehouse.prov_id = prov.PkLocID
WHERE
	tbl_warehouse.hf_type_id = $hf_type
AND tbl_warehouse.prov_id = $prov
AND tbl_warehouse.stkid = $stk
AND stakeholder.lvl = 7
ORDER BY stakeholder.stkname,
prov.LocName,
dist.LocName,
tbl_warehouse.wh_name
");
    if (mysql_num_rows($resset) > 0) {
        while ($resrow = mysql_fetch_assoc($resset)) {
            $province = $resrow['Province'];
            $stkholder = $resrow['Stakeholder'];
            $rowdata[$resrow['District']][$resrow['wh_id']] = $resrow['wh_name'];
        }
    }
}

$qry2 = "SELECT DISTINCT warehouse_id, value FROM alerts_mapping WHERE stakeholder_id = $stk AND province_id = $prov AND "
        . " hf_type_id = $hf_type AND product_id = $product AND alert_type = 1";
$res2 = mysql_query($qry2);
$selectedrow = array();
if (mysql_num_rows($res2) > 0) {
    while ($resset2 = mysql_fetch_assoc($res2)) {
        $selectedrow[$resset2['warehouse_id']] = $resset2['value'];
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
        //include $_SESSION['menu'];
        //include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="">
            <div class="page-content">

                <?php
                //if (!empty($_REQUEST['stk-prov-filter']) && $_REQUEST['stk-prov-filter'] == 'submit') {
                //$ItemOfGroup->m_npkId = 1;
                //$objMIs = $ItemOfGroup->GetItemsOfGroupById();
                //$objTypes = $HealthFacilityType->GetAllHealthFacilityType();
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <?php
                                //Display All Roles
                                ?>
                                <h3 class="heading"></h3>
                            </div>
                            <div class="widget-body">
                                <table cellpadding="0" cellspacing="0" align="center" class="table table-condensed table-hover">                                    
                                    <tr>
                                        <th width="5%">X</th>
                                        <td>means facilities don’t provide the same product </td>
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
                                <form action="stockout_configuration_hf.php" name="soconfig" id="soconfig" method="post">
                                    <table cellpadding="0" cellspacing="0" align="center" class="table table-condensed table-bordered">
                                        <?php
                                        foreach ($rowdata as $district => $wh) {
                                            $cnt = 0;
                                            //$itemarray[$cnt] = $Rowrankss->ItemID;
                                            //$cnt++;
                                            //foreach ($districts as $district=>$wh) {
                                            ?>
                                            <tr>  
                                                <th colspan="10" style="width: 7%; background-color: green; color: white"><?php echo $district; ?></th>
                                            </tr>
                                            <tr>
                                                <?php
                                                foreach ($wh as $wh_id => $wh_name) {
                                                    if ($cnt % 5 == 0) {
                                                        ?>
                                                    </tr>
                                                    <tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    <td style="width: 7%"><?php
                                                        echo $wh_name;
                                                        $selX = ($selectedrow[$wh_id] == 'X') ? 'selected="selected"' : '';
                                                        $selNA = ($selectedrow[$wh_id] == 'N/A') ? 'selected="selected"' : '';
                                                        $selAvailable = ($selectedrow[$wh_id] == 'Available') ? 'selected="selected"' : '';
                                                        ?>
                                                        <select id="sector_<?php echo $prov; ?>_<?php echo $stk; ?>_<?php echo $product; ?>_<?php echo $hf_type; ?>_<?php echo $wh_id; ?>" class="form-control input-sm" name="selected_options[<?php echo $hf_type; ?>][<?php echo $product; ?>][<?php echo $wh_id; ?>]">
                                                            <option value="X" <?php echo $selX; ?>>X</option>
                                                            <option value="N/A" <?php echo $selNA; ?>>N/A</option>
                                                            <option value="Available" <?php echo $selAvailable; ?>>Available</option>
                                                        </select>
                                                    </td>
                                                    <?php
                                                    $cnt++;
                                                }
                                                ?>
                                            </tr>
                                            <?php
                                            //} 
                                        }
                                        ?>


                                    </table>
                                    <div class="row">
                                        <div class="col-md-12 right"><button  class="btn btn-primary" type="submit"> Update </button></div>
                                    </div>
                                    <input name="prov_id" id="prov_id" value="<?php echo $prov; ?>" type="hidden"/>
                                    <input name="stk_id" id="stk_id" value="<?php echo $stk; ?>" type="hidden"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
<?php //}   ?>
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
                    //window.open("selected_hf.php?t="+suffix, "Selected Health Facilites", "width=800,height=800");
                }
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