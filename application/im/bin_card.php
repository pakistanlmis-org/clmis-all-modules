<?php
/**
 * bin_card
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
//area
$area = $arearow = '';
//wh id
$wh_id = $_SESSION['user_warehouse'];
if (isset($_POST) && !empty($_POST['area'])) {
    //check area
    if (isset($_POST['area']) && !empty($_POST['area'])) {
        //get area
        $area = $_POST['area'];
    }
    //check row
    if (isset($_POST['row']) && !empty($_POST['row'])) {
        //get row
        $arearow = $_POST['row'];
    }
//get wh id
    $wh_id = $_SESSION['user_warehouse'];
    //main query
    //gets
    //expiry
    //batchID
    //batchNo
    //itemID
    //ItemName
    //itm_type
    //qty_per_pack
    //DetailID
    //LocationName
    //LocationID
    //wh_id
    //Qty
    $mainSQL = "SELECT	* FROM (SELECT
	stock_batch.batch_expiry AS expiry,
	placements.stock_batch_id AS batchID,
	stock_batch.batch_no AS batchNo,
	stock_batch.item_id AS itemID,
	itminfo_tab.itm_name AS ItemName,
	itminfo_tab.itm_type,
	itminfo_tab.qty_carton AS qty_per_pack,
	placements.stock_detail_id AS DetailID,
	placement_config.location_name AS LocationName,
	placement_config.pk_id AS LocationID,
	placements.pk_id AS PlacementID,
	placement_config.warehouse_id AS wh_id,
	abs(SUM((placements.quantity))) AS Qty
        FROM
                placements
        INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
        INNER JOIN stock_batch ON placements.stock_batch_id = stock_batch.batch_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        WHERE
        placement_config.warehouse_id = " . $wh_id . " and placement_config.location_name like  '" . $area . $arearow . "%'" .
            " GROUP BY batchNo,itemID order BY itemID) AS A
                WHERE	A.Qty > 0";
//query result
    $Bincard = mysql_query($mainSQL) or die("mainSQL");
}
?>
<style>
    .btn-link {
        color: #fff !important;
        text-shadow: none;
    }
</style>

<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im    
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 

                <!-- BEGIN PAGE HEADER-->

                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Location's Bin Cards</h3>
                            </div>
                            <div class="widget-body">
                                <form method="POST" name="placement_location" id="placement_location" action="">
                                    <!-- Row -->
                                    <div class="row-fluid">
                                        <div class="col-md-2"> 
                                            <!-- Group Receive No-->
                                            <div class="control-group">
                                                <label class="control-label" for="receive_no"> Area <span style="color: red">*</span> </label>
                                                <div class="controls">
                                                    <select class="form-control input-small" name="area" id="area" required>
                                                        <option value="">Select</option>
                                                        <?php
                                                        //query get area
                                                        //gets
                                                        //pk id
                                                        //list value
                                                        $getArea = mysql_query("SELECT        
                                                        list_detail.pk_id,
                                                        list_detail.list_value
                                                        FROM
                                                        list_master
                                                        INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
                                                        WHERE
                                                        list_master.pk_id = 14") or die("ERR Get Area");
                                                        //fetch result
                                                        while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                            ?>
                                                            <option value="<?php echo $rowArea['list_value']; ?>"
                                                            <?php
                                                            if ($rowArea['list_value'] == $area) {
                                                                echo "selected=selected";
                                                            }
                                                            ?>> <?php echo $rowArea['list_value']; ?> </option>
                                                                <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label class="control-label" for="receive_no"> Row <span
                                                        style="color: red">*</span> </label>
                                                <div class="controls">
                                                    <select class="form-control input-small" name="row" id="row" required>
                                                        <option value="">Select</option>
                                                        <?php
                                                        //query get rows
                                                        //gets
                                                        //pk id
                                                        //list value
                                                        $getRows = mysql_query("SELECT        
                                                        list_detail.pk_id,
                                                        list_detail.list_value
                                                        FROM
                                                        list_master
                                                        INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
                                                        WHERE
                                                        list_master.pk_id = 15") or die("ERR Get Area");
                                                        //fetch result
                                                        while ($rowArea = mysql_fetch_assoc($getRows)) {
                                                            ?>
                                                            <option value="<?php echo $rowArea['list_value']; ?>"
                                                            <?php
                                                            if ($rowArea['list_value'] == $arearow) {
                                                                echo "selected=selected";
                                                            }
                                                            ?>> <?php echo $rowArea['list_value']; ?> </option>
                                                                <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span3">
                                            <div class="control-group">
                                                <label class="control-label" for="firstname"> &nbsp; </label>
                                                <div class="controls">
                                                    <button type="submit" class="btn btn-primary"
                                                            id="location_status">Show Bin Card</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- // Row END -->
                <?php
                if (isset($_POST) && !empty($_POST['area'])) {
                    ?>
                    <div class="row">
                        <div class="col-md-12"> 

                            <!-- Widget -->
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Bin Card - Location
                                        <?php
                                        if ($area != '') {
                                            echo "(Area - " . $area . " / Row # " . $arearow . ")";
                                        }
                                        ?>
                                    </h3>
                                </div>
                                <div class="widget-body" >
                                    <table class="bincard table table-striped table-bordered table-condensed">
                                        <thead>
                                            <tr>
                                                <th width="6%">S. No.</th>
                                                <th>Product</th>
                                                <th width="15%">Batch No.</th>
                                                <th width="13%">Quantity</th>
                                                <th width="8%">Unit</th>
                                                <th width="10%">Cartons</th>
                                                <th width="12%">Expiry Date</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $i = 1;
                                        while ($row = mysql_fetch_array($Bincard)) {
                                            ?>
                                            <tr>
                                                <td style="text-align:center;"><?php echo $i; ?></td>
                                                <td><?php echo $row["ItemName"] ?></td>
                                                <td><?php echo $row["batchNo"] ?></td>
                                                <td style="text-align:right;"><?php echo number_format($row["Qty"]) ?></td>
                                                <td><?php echo $row["itm_type"] ?></td>
                                                <td style="text-align:right;"><?php echo number_format($row["Qty"] / $row["qty_per_pack"]) ?></td>
                                                <td style="text-align:center;"><?php echo date("d/m/y", strtotime($row["expiry"])); ?></td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </table>
                                    <?php if ($Bincard != null) { ?>
                                        <div class="right" style="margin-top:10px !important;">
                                            <div style="float:right;">
                                                <button id="print_bincard" onClick="window.open('bin_card_list.php?area=<?php echo $area; ?>&row=<?php echo $arearow; ?>', '_blank', 'scrollbars=1,width=780,height=595')" type="button" class="btn btn-warning">Print</button>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </div>
                                        <?php $i++;
                                    }
                                    ?>
                                </div>
                            </div>
                            <!-- Widget --> 
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- // Content END -->
<?php include PUBLIC_PATH . "/html/footer.php"; ?>
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/stockplacement.js"></script>
</body>
<!-- END BODY -->
</html>