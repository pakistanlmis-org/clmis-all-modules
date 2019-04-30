<?php
/**
 * Manage Sub Admin
 * @package Admin
 * 
 * @author     Muhammad Waqas Azeem 
 * @email <waqas@deliver-pk.org>
 * 
 * @version    2.2
 * 
 */
//including files
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

//initializing variables
//forid
$formid = 'sub-admin';
//strDo
$strDo = '';
//Get All SubAdmin User
$rsUsers = $objuser->GetAllSubAdminUser();

if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //getting Do
    $strDo = $_REQUEST['Do'];
}
if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    //getting Id
    $nstkId = $_REQUEST['Id'];
}
/**
 * Delete
 */

if ($strDo == "Edit") {

    $formid = 'sub-admin2';

}
//Get All Stakeholders
$rsStakeholders = $objstk->GetAllStakeholders();

$strDo = ($strDo == 'Edit') ? $strDo : 'Add';
//including file
include("xml_assign_reporting_stakeholders.php");
?>
</head>

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
                        <h3 class="page-title row-br-b-wp">Reporting Stakeholders of Funding Sources</h3>
<?php if ($strDo == 'Edit') : ?>
                        <?php
 
                        $qry = " SELECT
                                    sub_stk.stkname AS stkname_sub,
                                    integrated_stakeholders.sub_stk_id,
                                    integrated_stakeholders.main_stk_id
                                FROM
                                    integrated_stakeholders
                                INNER JOIN stakeholder AS sub_stk ON integrated_stakeholders.sub_stk_id = sub_stk.stkid
                                WHERE
                                    integrated_stakeholders.main_stk_id = ".$nstkId." AND
                                    integrated_stakeholders.province_id = ".$_REQUEST['prov']."
                                ORDER BY
                                    stkname_sub ASC
                            ";
                        //query result
                        // echo $qry;exit;
                        $rsStakeholders2 = mysql_query($qry);
                        $sub_stk_arr = array();
                        while ($row = mysql_fetch_array($rsStakeholders2)) {
                            
                            $sub_stk_arr[$row['sub_stk_id']] = $row['stkname_sub'];
                        }
                        
                        ?>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Assign Reporting Stakeholders to Funding Source</h3>
                            </div>
                            <div class="widget-body">
                                <form method="post" action="Assign_reporting_stakeholders_action.php" id="<?php echo $formid; ?>">
                                    
                                   
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <div class="control-group">
                                                    <label>Select Reporting stakeholders </label>
                                                    <div class="controls">
                                                        <div class="multi-select col-md-6" style="padding:0;">
                                                            <select multiple id="stackholders1" class="multi form-control input-medium">
                                                                <?php
                                                                //populate stackholders1 combo
                                                                while ($row = mysql_fetch_array($rsStakeholders)) {
                                                                    if (!in_array($row['stkname'], $sub_stk_arr) ) {
                                                                        
                                                                        ?>
                                                                        <option value="<?php echo $row['stkid']; ?>"><?php echo $row['stkname']; ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                            <a href="#" id="stk-add">add >></a> </div>
                                                        <div class="multi-select col-md-6">
                                                            <select multiple id="stackholders2" name="stkholders[]" class="multi form-control input-medium">
                                                                <?php
                                                                foreach($sub_stk_arr as $k =>$v) {
                                                                       
                                                                            ?>
                                                                            <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                                                            <?php
                                                                        
                                                                    }
                                                                ?>
                                                            </select>
                                                            <a href="#" id="stk-remove"><< remove</a> </div>
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

                                                            <input name="Id" value="<?php echo $nstkId; ?>" type="hidden" />
                                                            <input name="prov" value="<?php echo $_REQUEST['prov']; ?>" type="hidden" />
                                                            <input name="submit" value="Save" type="submit" id="submit" class="btn btn-primary" />
                                                            <input name="Do" value="Edit" type="hidden" id="Do" />

                                                        <input name="cancel" value="Cancel" type="button" id="cancel" class="btn btn-info" OnClick="window.location = '<?= $_SERVER["PHP_SELF"]; ?>';" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
<?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Reporting Stakeholders</h3>
                            </div>
                            <div class="widget-body">
                                <table width="100%" cellpadding="0" cellspacing="0" align="center">
                                    
                                    <tr>
                                        <td><div id="mygrid_container" style="width:100%; height:390px; background-color:white;overflow:hidden"></div></td>
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
    <script type="text/javascript">
        $(function() {
            $('#<?php echo $formid; ?>').on('submit', function(e) {
                $('#stackholders2 option').attr('selected', 'selected');
                $('#provinces2 option').attr('selected', 'selected');
            });

            $('#stk-add').click(function() {
                return !$('#stackholders1 option:selected').remove().appendTo('#stackholders2');
            });
            $('#stk-remove').click(function() {
                return !$('#stackholders2 option:selected').remove().appendTo('#stackholders1');
            });

            $('#prov-add').click(function() {
                return !$('#provinces1 option:selected').remove().appendTo('#provinces2');
            });
            $('#prov-remove').click(function() {
                return !$('#provinces2 option:selected').remove().appendTo('#provinces1');
            });
        });
    </script> 
    <script>
        function editFunction(val,prov) {
            window.location = "Assign_reporting_stakeholders.php?Do=Edit&Id=" + val + "&prov="+prov;
        }
        function delFunction(val,prov) {
            if (confirm("Are you sure you want to delete the record?")) {
                window.location = "Assign_reporting_stakeholders.php?Do=Delete&Id=" + val + "&prov="+prov;
            }
        }

        var mygrid;
        function doInitGrid() {
            mygrid = new dhtmlXGridObject('mygrid_container');
            mygrid.selMultiRows = true;
            mygrid.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/codebase/imgs/");
            mygrid.setHeader("<span title='Serial Number'>Sr. No.</span>,<span title='Province'>Province</span>,<span title='Main Stakeholder'>Main Stakeholder</span>,<span title='Sub Stakeholder'>Sub Stakeholder</span>,<span title='Use this column to perform the desired operation'>Actions</span>,#cspan");
            mygrid.attachHeader(",#select_filter,#select_filter,#text_filter");
            mygrid.setInitWidths("60,150,150,580,50");
            mygrid.setColAlign("center,left,left,left")
            mygrid.setColSorting("str");
            mygrid.enableMultiline(true);
            mygrid.setColTypes("ro,ro,ro,ro,img");
            //mygrid.enableLightMouseNavigation(true);
            mygrid.enableRowsHover(true, 'onMouseOver');
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
        unset($_SESSION['err']);
    }
    ?>
</body>
</html>