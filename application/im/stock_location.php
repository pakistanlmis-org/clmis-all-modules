<?php
/**
 * stock_location
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
//wh id
$wh_id = $_SESSION['user_warehouse'];
//location id
$loc_id = $_REQUEST['loc_id'];
//select query
//get location name
$getLocationName = mysql_query("select location_name from placement_config where pk_id=" . $loc_id) or die("select location_name from placement_config where pk_id=" . $loc_id);
//fetch result
$rowLocation = mysql_fetch_assoc($getLocationName);
//select query
//gets
//batch_no
//item_id
//itm_name
//itm_type
//batch_expiry
//is_placed
//quantity
//qty_carton
//placement_location_id
//stock_detail_id
$strSQL = "SELECT
			stock_batch.batch_no,
			stock_batch.item_id,
			itminfo_tab.itm_name,
			itminfo_tab.itm_type,
			stock_batch.batch_expiry,
			placements.is_placed,
			SUM(placements.quantity) AS quantity,
			itminfo_tab.qty_carton,
			stock_batch.batch_id,
			placements.placement_location_id,
			placements.stock_detail_id
		FROM
			stock_batch
		LEFT JOIN placements ON stock_batch.batch_id = placements.stock_batch_id
		INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
		INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
		WHERE
			placements.placement_location_id = $loc_id
		AND placement_config.warehouse_id = $wh_id
		GROUP BY
			placements.stock_batch_id";
//echo $strSQL;
//exit;
$getStock = mysql_query($strSQL) or die(mysql_error());

$getLocations = mysql_query("SELECT DISTINCT
                                    location_name,
                                    pk_id,
                                    warehouse_id
                                FROM
                                    placement_config
                                WHERE
                                    placement_config.warehouse_id = $wh_id
                                    AND placement_config.pk_id != $loc_id
                                    AND placement_config.`status` = 1
                                ORDER BY
                                    location_name") or die("Err Get Transfer to Location");
$location_select = "";
while ($rowTransfer = mysql_fetch_assoc($getLocations)) {
    $location_select .= "<option value=" . $rowTransfer['pk_id'] . ">" . $rowTransfer['location_name'] . "</option>";
}


$getVolumeUsed = mysql_query("SELECT
            placement_config.pk_id,
            placement_config.volume_used
            FROM
            placement_config
            WHERE
            placement_config.pk_id = $loc_id") or die("Err get volume user");

$rowVolumeUsed = mysql_fetch_assoc($getVolumeUsed);
$volume_used = $rowVolumeUsed['volume_used'];
?>
<style>
    .btn-link {
        color: #fff !important;
        text-shadow: none;
    }
    input, button.btn-primary, button.btn-danger, select {
        height: 25px !important;
        padding-top: 0px;
        padding: 3px !important;
        font-size: 12px !important;
    }
</style>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
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
                    <div class="col-md-12"><!-- Content --> 
                        <!-- Widget -->
                        <div class="row">
                            <div class="col-md-12 right" style="padding:5px 15px;">
                                <button onClick="window.location = 'stock_placement.php?<?php echo 'area=' . $_REQUEST['area'] . '&level=' . $_REQUEST['level']; ?>'" class="btn btn-primary"> Back to Locations </button>
                                <button id="add_stock_<?php echo $_REQUEST['loc_id']; ?>" class="btn btn-primary"> Add More Stock </button>
                            </div>
                        </div>
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading"> Stock placed at <?php echo $rowLocation['location_name'] ?> </h3>
                            </div>
                            <!-- // Widget heading END -->

                            <div class="widget-body"> 

                                <!-- Table --> 
                                <!-- Table -->
                                <form id="update_volume" name="update_volume" action="volume_used_action.php">
                                    <table>
                                        <tr> <td>  Update Volume Used   <input class="qty form-control input-small"  type="text" autocomplete="off" name="volume_used" id="volume_used"  required style="width:120px !important; display:inline-block; background:#ffffcf;" value="<?php
                                                if (!empty($volume_used)) {
                                                    echo $volume_used;
                                                }
                                                ?>"/> 
                                                <input type="hidden" name="hidden_loc_id" id="hidden_loc_id" value="<?php echo $loc_id; ?>">
                                                <input type="hidden" id="hiddFld" name="hiddFld" value="<?php echo 'area=' . $_REQUEST['area'] . '&level=' . $_REQUEST['level']; ?>">
                                                <button type="submit"  name="save" value="submit" class="btn btn-primary" style="margin-left:20px; float:right">Update</button></td></tr></table>
                                </form>
                                <table class="table table-striped table-bordered table-condensed">

                                    <!-- Table heading -->
                                    <thead>
                                        <tr>
                                            <th width="5%">S.No.</th>
                                            <th width="15%">Product</th>
                                            <th width="10%">Batch</th>
                                            <th width="8%">Expiry</th>
                                            <th width="23%">Quantity</th>
                                            <th>Transfer Quantity</th>
                                        </tr>
                                    </thead>
                                    <!-- // Table heading END --> 

                                    <!-- Table body --> 

                                    <!-- Table row -->

                                    <?php
                                    $counter = 1;
                                    //check if record exists
                                    if (mysql_num_rows($getStock) > 0) {
                                        //fetch result
                                        while ($rowStock = mysql_fetch_assoc($getStock)) {
                                            //check quantity
                                            if ($rowStock['quantity'] > 0) {
                                                $submitBtnId = 'submit-' . $counter;
                                                ?>
                                                <tr class="gradeX">
                                                    <td class="center"><?php echo $counter; ?></td>
                                                    <td><?php echo $rowStock['itm_name'] ?></td>
                                                    <td><?php echo $rowStock['batch_no']; ?></td>
                                                    <td><?php echo date('m/Y', (strtotime($rowStock['batch_expiry']))); ?></td>
                                                    <td class="right"><?php
                                                        //carton qty
                                                        $cartonQty = $rowStock['quantity'] / $rowStock['qty_carton'];
                                                        if ($rowStock['quantity'] > 0) {
                                                            echo number_format($rowStock['quantity']) . ' ' . $rowStock['itm_type'] . ' / ';
                                                            echo ((floor($cartonQty) != $cartonQty) ? number_format($cartonQty, 2) : number_format($cartonQty)) . ' Cartons';
                                                        } else {
                                                            echo '0';
                                                        }
                                                        ?></td>
                                                    <td><form name="transfer_stock" method="post" id="transfer_stock" action="transfer_stock_action.php" onSubmit="disableButton(this.form, '<?php echo $submitBtnId; ?>')">
                                                            <input type="hidden" id="loc_id" name="loc_id" value="<?php echo $loc_id; ?>"/>
                                                            <input type="hidden" id="qty_carton" name="qty_carton" value="<?php echo $rowStock['qty_carton']; ?>"/>
                                                            <input type="hidden" id="available_qty" name="available_qty" value="<?php echo $rowStock['quantity']; ?>"/>
                                                            <input type="hidden" id="item_id" name="item_id" value="<?php echo $rowStock['item_id'] ?>"/>
                                                            <input type="hidden" id="batch_id" name="batch_id" value="<?php echo $rowStock['batch_id'] ?>"/>
                                                            <input type="hidden" id="stock_detail_id" name="stock_detail_id" value="<?php echo $rowStock['stock_detail_id'] ?>"/>
                                                            <input class="qty form-control input-small" equalto="#available_qty" type="text" autocomplete="off" name="transfer_qty" id="transfer_qty_<?php echo $loc_id ?>" onKeyUp="formValidation(this.value, '<?php echo $rowStock['quantity']; ?>', '<?php echo $submitBtnId; ?>'); showCartons(this.value, '<?php echo $rowStock['qty_carton']; ?>', '<?php echo $counter; ?>');" required style="width:120px !important; display:inline-block; background:#ffffcf;" value=""/>
                                                            <select class="form-control input-small" name="transfer_to" id="transfer_to_<?php echo $loc_id ?>" required style="width:150px; display:inline-block;">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //select query
                                                                //gets
                                                                //location name
                                                                //warehouse id

                                                                echo $location_select;
                                                                ?>
                                                            </select>
                                                            <input type="hidden" id="hiddFld" name="hiddFld" value="<?php echo 'area=' . $_REQUEST['area'] . '&level=' . $_REQUEST['level']; ?>">
                                                            <button type="button" id="del_<?php echo $submitBtnId; ?>" name="delete" value="delete" class="btn btn-danger" style="float:right; margin-left:10px;" onClick="deletePlacement(<?php echo $loc_id; ?>, <?php echo $rowStock['batch_id']; ?>);">Delete</button>
                                                            <button type="submit" id="<?php echo $submitBtnId; ?>" name="save" value="submit" class="btn btn-primary" style="float:right">Transfer</button>
                                                            <div id="allocatedCarton<?php echo $counter; ?>"></div>
                                                        </form></td>
                                                </tr>
                                                <?php
                                                $counter++;
                                            }
                                        }
                                    } else {
                                        ?>
                                        <input type="hidden" id="hiddFld" name="hiddFld" value="<?php echo 'area=' . $_REQUEST['area'] . '&level=' . $_REQUEST['level']; ?>">
                                        <?php
                                        echo '<tr><td colspan="6">No record found.</td></tr>';
                                    }
                                    ?>
                                    <!-- // Table row END -->

                                    </tbody>

                                </table>
                                <!-- // Table END --> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- // Content END -->
    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/stockplacement.js"></script>
    <?php
    if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
        ?>
        <script>
                                                        var self = $('[data-toggle="notyfy"]');
                                                        notyfy({
                                                            force: true,
                                                            text: 'Data has been saved successfully!',
                                                            type: 'success',
                                                            layout: self.data('layout')
                                                        });
        </script>
        <?php
//unset success
        unset($_SESSION['success']);
    }
    ?>
    <script>
        function formValidation(transfer, available, submitBtnId)
        {
            var transfer_qty = parseInt(transfer);
            var available_qty = parseInt(available);
            if (isNaN(transfer_qty))
            {
                alert('Enter only numeric data');
                $('#' + submitBtnId).attr('disabled', true);
                return false;
            } else if (transfer_qty == 0)
            {
                alert('Transfer quantity can not be 0');
                $('#' + submitBtnId).attr('disabled', true);
                return false;
            } else
            {
                $('#' + submitBtnId).removeAttr('disabled');
            }

            if (transfer_qty > available_qty)
            {
                alert('Transfer quantity can not be greater than ' + available);
                $('#' + submitBtnId).attr('disabled', true);
                return false;
            } else
            {
                $('#' + submitBtnId).removeAttr('disabled');
            }
        }
        function disableButton(formId, submitBtnId)
        {
            $('#' + submitBtnId).attr('disabled', true);
            $('#' + submitBtnId).html('Submitting...');
        }

        function deletePlacement(id, batchId) {
            if (confirm('Are You sure, You want to delete?')) {
                $.ajax({
                    type: "POST",
                    url: "delete_placement.php",
                    data: {id: id, batchId: batchId},
                    dataType: 'html',
                    success: function (data) {
                        window.location = window.location;
                    }
                });

            }
        }
        $(function () {
            $('.qty').keydown(function (e) {
                if (e.shiftKey || e.ctrlKey || e.altKey) { // if shift, ctrl or alt keys held down
                    e.preventDefault();         // Prevent character input
                } else {
                    var n = e.keyCode;
                    if (!(
                            (n == 8)              // backspace
                            || (n == 9)                // Tab
                            || (n == 46)                // delete
                            || (n >= 35 && n <= 40)     // arrow keys/home/end
                            || (n >= 48 && n <= 57)     // numbers on keyboard
                            || (n >= 96 && n <= 105))   // number on keypad
                            )
                    {
                        e.preventDefault();     // Prevent character input
                    }
                }
            });
        })
        function showCartons(qty, cartonQty, cartonId)
        {
            $('#' + cartonId).html('').hide();
            if (qty != '' && parseInt(qty) > 0)
            {
                var cartons = (parseFloat(qty) / parseFloat(cartonQty));
                cartons = eval(cartons.toFixed(2) + 0) + ' Carton(s)';
                $('#allocatedCarton' + cartonId).html(cartons).show();
            }
        }
    </script>
</body>
<!-- END BODY -->
</html>