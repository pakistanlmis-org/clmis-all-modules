<?php
/**
 * pipeline_consignment
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//get user_warehouse
$wh_id = $_SESSION['user_warehouse'];
//user_stakeholder
$stakeholderId = $_SESSION['user_stakeholder'];
?>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //include top file
        include PUBLIC_PATH . "html/top.php";
        //include top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <form method="POST" name="future_arrival" id="future_arrival" action="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">New Pipeline Consignment</h3>
                                </div>
                                <div class="widget-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label"> Source <span style="color: red">*</span> </label>
                                                    <div class="controls">
                                                        <select class="form-control input-small" name="from_warehouse_id" id="from_warehouse_id" required>
                                                            <option value="">Select</option>
                                                            <?php
                                                            //query 
                                                            //gets
                                                            //stkid
                                                            //stkname
                                                            $qry = "SELECT
                                                                        stakeholder.stkid,
                                                                        stakeholder.stkname
                                                                    FROM
                                                                        stakeholder
                                                                    WHERE
                                                                        stakeholder.stk_type_id = 2";
                                                            //query result
                                                            $qryRes = mysql_query($qry);
                                                            //feching results
                                                            while ($row = mysql_fetch_array($qryRes)) {
                                                                echo "<option value=\"$row[stkid]\">$row[stkname]</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label"> Expected Arrival Date <span style="color: red">*</span> </label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control input-small" name="expected_arrival_date" id="expected_arrival_date" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label"> Reference No. <span style="color: red">*</span> </label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control input-small" name="reference_number" id="reference_number" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label"> Description <span style="color: red">*</span> </label>
                                                    <div class="controls">
                                                        <textarea class="form-control input-medium" name="description" id="description" style="resize:none;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Receive List</h3>
                                </div>
                                <div class="widget-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-primary" type="button" id="add_more" style="float:right; margin-bottom:10px; padding:0 3px 0 3px;">Add Row</button>
                                            <table class="table table-striped table-bordered table-condensed" id="mytable">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Batch</th>
                                                        <th>Production Date</th>
                                                        <th>Expiry Date</th>
                                                        <th>Manufacturer</th>
                                                        <th>Unit Price</th>
                                                        <th>Quantity</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    for ($i = 0; $i < 5; $i++) {
                                                        ?>
                                                        <tr class="dynamic-rows">
                                                            <td><select name="item_id[]" class="form-control input-small">
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    //query
                                                                    //gets
                                                                    //itm_name
                                                                    //itm_id
                                                                    $qry = "SELECT
                                                                        itminfo_tab.itm_name,
                                                                        itminfo_tab.itm_id
                                                                    FROM
                                                                        itminfo_tab
                                                                    INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                                                                    WHERE
                                                                        stakeholder_item.stkid = $stakeholderId
                                                                    ORDER BY
                                                                        itminfo_tab.frmindex ASC";
                                                                    //query result
                                                                    $qryRes = mysql_query($qry);
                                                                    //fetching results
                                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                                        echo "<option value=\"$row[itm_id]\">$row[itm_name]</option>";
                                                                    }
                                                                    ?>
                                                                </select></td>
                                                            <td><input type="text" class="form-control input-small" name="batch_number[]"></td>
                                                            <td><input type="text" class="form-control input-small" name="production_date[]"></td>
                                                            <td><input type="text" class="form-control input-small" name="expiry_date[]"></td>
                                                            <td><select class="form-control input-small" name="manufacturer_id[]">
                                                                    <option value="">Select</option>
                                                                    <?php
                                                                    //query
                                                                    //gets
                                                                    //stkid
                                                                    //stkname
                                                                    $qry = "SELECT
                                                                        stakeholder.stkid,
                                                                        stakeholder.stkname
                                                                    FROM
                                                                        stakeholder
                                                                    WHERE
                                                                        stakeholder.stk_type_id = 3";
                                                                    //query result
                                                                    $qryRes = mysql_query($qry);
                                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                                        echo "<option value=\"$row[stkid]\">$row[stkname]</option>";
                                                                    }
                                                                    ?>
                                                                </select></td>
                                                            <td><input type="text" class="form-control input-small" name="unit_price[]"></td>
                                                            <td><input type="text" class="form-control input-small" name="quantity[]"></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12 right">
                                <button class="btn btn-primary" type="submit" id="add_stock">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- // Content END -->
    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?php echo PUBLIC_URL; ?>js/pipeline-consignments.js"></script>
</body>
<!-- END BODY -->
</html>