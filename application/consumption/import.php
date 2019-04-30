<?php
/**
 * import
 * @package consumption
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
//Query for import
$qry = "SELECT
			YEAR (DATE_ADD(MAX(tbl_wh_data.RptDate), INTERVAL 1 MONTH)) AS currentRptYear,
			MONTH (DATE_ADD(MAX(tbl_wh_data.RptDate), INTERVAL 1 MONTH)) AS currentRptMonth
		FROM
			tbl_warehouse
		INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
		WHERE
			tbl_warehouse.stkid = " . $_SESSION['user_stakeholder'] . "
		AND stakeholder.lvl = 4
		ORDER BY
			tbl_warehouse.wh_id ASC";
//Query result
$qryRes = mysql_fetch_array(mysql_query($qry));
$currentRptYear = $qryRes['currentRptYear'];
$currentRptMonth = $qryRes['currentRptMonth'];
//Getting month
$sel_month = isset($_GET['month']) ? mysql_real_escape_string($_GET['month']) : $currentRptMonth;
//Getting year
$sel_year = isset($_GET['year']) ? mysql_real_escape_string($_GET['year']) : $currentRptYear;
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //Including files
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Import Data</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="import_action.php" method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-12"> <a href="gs_sample.php">Download file with Store Codes and Product IDs</a> </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label">Month</label>
                                                    <div class="controls">
                                                        <select name="month" id="month" class="form-control input-sm">
                                                            <?php
                                                            //populate month combo
                                                            for ($i = 1; $i <= $currentRptMonth; $i++) {
                                                                $sel = ($sel_month == $i) ? 'selected="selected"' : '';
                                                                ?>
                                                                <option value="<?php echo $i; ?>" <?php echo $sel; ?>><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label">Month</label>
                                                    <div class="controls">
                                                        <select name="year" id="year" class="form-control input-sm">
                                                            <?php
                                                            //Populate year cpmbo
                                                            for ($i = $currentRptYear; $i >= 2010; $i--) {
                                                                $sel = ($sel_year == $i) ? 'selected="selected"' : '';
                                                                ?>
                                                                <option value="<?php echo $i; ?>" <?php echo $sel; ?>><?php echo $i; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label">Select Data File(xlsx)</label>
                                                    <div class="controls">
                                                        <input type="file" name="data_file" id="data_file" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label class="control-label">&nbsp;</label>
                                                    <div class="controls">
                                                        <button type="submit" name="submit" value="submit" class="btn btn-primary">Import</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-12" style="color:#F00;">
                                                <?php
                                                echo isset($_SESSION['error']) ? $_SESSION['error'] : '';
                                                //unset session
                                                unset($_SESSION['error']);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- // Content END --> 

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    //Including file
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script>
        $.validator.addMethod("extension", function(value, element, param) {
            param = typeof param === "string" ? param.replace(/,/g, "|") : "png|jpe?g|gif";
            return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
        }, $.validator.format("Please select excel file."));

        $("#frm").validate({
            onfocusout: function(e) {
                this.element(e);
            },
            rules: {
                data_file: {
                    required: true,
                    extension: "xlsx"
                }
            }
        });
    </script>
    <?php
    if (!empty($_SESSION['msg'])) {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: 'Data imported successfully',
                type: 'success',
                layout: self.data('layout')
            });
        </script>
        <?php
        unset($_SESSION['msg']);
    }
    ?>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>