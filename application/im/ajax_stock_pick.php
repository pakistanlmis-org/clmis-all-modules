<?php
/**
 * ajax_stock_pick
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
//Getting user_warehouse
$wh_id = $_SESSION['user_warehouse'];
//Checking id
if (isset($_REQUEST['id'])) {
    //
    $arr = explode('_', base64_decode($_REQUEST['id']));
    //item_id
    $item_id = $arr[0];
    //batch
    $batch = $arr[1];
    //batch_expiry
    $batch_expiry = $arr[2];
    //item
    $item = $arr[3];
    //detail_id
    $detail_id = $arr[4];
    //maxValue
    $maxValue = $arr[5];
    //tran_no
    $tran_no = $arr[6];
    //dateFrom
    $dateFrom = $arr[7];
    //dateTo
    $dateTo = $arr[8];
    //get Batch No
    $getBatchNo = mysql_query("select batch_no from stock_batch where batch_id=$batch") or die(mysql_error('GetBatchNumber'));
    //batch_no
    $batch_no = mysql_fetch_assoc($getBatchNo);
    ?>
    <script>
        $(function() {
            var $form = $('#stockpick'),
                    $summands = $form.find('.pick_qty'),
                    $sumDisplay = $('#totalpick');
            $form.delegate('.pick_qty', 'keyup', function() {
                var sum = 0;
                $summands.each(function() {
                    var value = Number($(this).val());
                    if (!isNaN(value))
                        sum += value;
                });
                $sumDisplay.text(sum);
            });
            $('.close').click(function() {
                $('#modal-pick').hide();
                $('.modal-backdrop, .modal-backdrop.fade.in').hide();
            });

        });

        /*$('[data-toggle="modal"]').on('click',
         function(e) {
         $('#ajax').remove();
         e.preventDefault();
         var $this = $(this)
         , $remote = $this.data('remote') || $this.attr('href')
         , $modal = $('<div class="modal" id="ajax"><div class="modal-body"></div></div>');
         $('body').append($modal);
         $modal.modal({backdrop: 'static', keyboard: false});
         $modal.load($remote);
         }
         );*/

        $('body').on('hidden.bs.modal', '.modal', function() {
            $(this).removeData('bs.modal');
        });

        $('#save_pick').click(function(e) {
            var q = 0;
            var inp = $('.qty');
            for (var i = 0; i < inp.length; i++) {
                if (inp[i].value != '') {
                    q++;
                    if (parseInt(inp[i].value) == 0)
                    {
                        alert('Quantity can not be 0');
                        inp[i].focus();
                        return false;
                    }
                    else if (parseInt(inp[i].value) > parseInt(inp[i].getAttribute('max'))) {
                        alert('Quantity can not be greater than ' + parseInt(inp[i].getAttribute('max')));
                        inp[i].focus();
                        return false;
                    }
                }
            }

            if (q == 0) {
                alert('Please enter at least one quantity');
                return false;
            }
            if (parseInt($('#totalpick').html()) > parseInt($('#maxPicked').val()))
            {
                alert('Picked quantity can not be greater than ' + $('#maxPicked').val());
                return false;
            }
            $('#stockpick').submit();
        });
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
        $(function() {
            $('.qty').keydown(function(e) {
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
                            || (n >= 96 && n <= 105)   // number on keypad
                            )
                            )
                    {
                        e.preventDefault();     // Prevent character input
                    }
                }
            });
        })
    </script>

    <form name="stockpick"  id="stockpick" action="pick_stock_action.php" method="POST">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 class="modal-title">Pick from Location</h4>
        </div>
        <div class="modal-body">
            <div class="span8">
                <?php
                //Query for locations
                //Gets
                //quantity
                //quantityCarton
                //location_name
                //location_name
                //location_name
                //
                  $getLocsSql = "SELECT
				  					SUM(placements.quantity) AS quantity,
									SUM(placements.quantity / itminfo_tab.qty_carton) AS quantityCarton,
									placement_config.location_name,
									placement_config.pk_id,
									itminfo_tab.qty_carton,
									itminfo_tab.itm_type
								FROM
									placements
								INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
								INNER JOIN stock_batch ON stock_batch.batch_id = placements.stock_batch_id
								INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
								WHERE
									stock_batch.batch_id = $batch
								AND placement_config.warehouse_id = $wh_id
								GROUP BY
									placement_config.location_name";
                //Query result
                $resLocs = mysql_query($getLocsSql) or die(mysql_error());
                //Check if there is a record
                if (mysql_num_rows($resLocs) > 0) {
                    ?>
                    Product:<?php echo $item ?>, Batch No: <?php echo $batch_no['batch_no'] ?>, Expiry:<?php echo date('m/Y', strtotime($batch_expiry)); ?>
                    <?php }
                    ?>
                <table class="table table-striped table-bordered table-condensed">
    <?php if (mysql_num_rows($resLocs) > 0) { ?>
                        <tr>
                            <th width="5%">S.No.</th>
                            <th>Location</th>
                            <th>Available Quantity</th>
                            <th>Pick Quantity</th>
                        </tr>
                        <?php
                        $counterLocs = 1;
                        while ($rowLocs = mysql_fetch_assoc($resLocs)) {
                            if ($rowLocs['quantity'] > 0) {
                                ?>
                                <tr>
                                    <td class="center"><?php echo $counterLocs ?></td>
                                    <td><?php echo $rowLocs['location_name']; ?></td>
                                    <td class="right">
                                        <?php
                                        $cartonQty = $rowLocs['quantity'] / $rowLocs['qty_carton'];
                                        if ($rowLocs['quantity'] > 0) {
                                            echo number_format($rowLocs['quantity']) . ' ' . $rowLocs['itm_type'] . ' / ';
                                            echo ((floor($cartonQty) != $cartonQty) ? number_format($cartonQty, 2) : number_format($cartonQty)) . ' Cartons';
                                        } else {
                                            echo '0';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <input type="hidden" name="loc_id[<?php echo $rowLocs['pk_id'] ?>]" id="loc_id" value="<?php echo $rowLocs['pk_id']; ?>"/>
                                        <input type="hidden" name="carton[<?php echo $rowLocs['pk_id'] ?>]" id="loc_id" value="<?php echo $rowLocs['qty_carton']; ?>"/>
                                        <input type="hidden" name="stock_detail_id" id="stock_detail_id" value="<?php echo $detail_id; ?>"/>
                                        <input type="hidden" name="available_qty[<?php echo $rowLocs['pk_id'] ?>]" id="available_qty" value="<?php echo $rowLocs['quantity']; ?>"/>
                                        <input type="hidden" name="batch_id[<?php echo $rowLocs['pk_id'] ?>]" id="batch_id[<?php echo $rowLocs['pk_id'] ?>]" value="<?php echo $batch; ?>"/>
                                        <input type="text" autocomplete="off" max="<?php echo $rowLocs['quantity']; ?>" class= "qty num pick_qty form-control input-small input-sm" name="allocate_qty[<?php echo $rowLocs['pk_id'] ?>]" id="pick_qty" onkeyup="showCartons(this.value, '<?php echo $rowLocs['qty_carton']; ?>', '<?php echo $counterLocs; ?>');" />
                                        <input type="hidden" name="tran_no" id="tran_no" value="<?php echo $tran_no; ?>" />
                                        <div id="allocatedCarton<?php echo $counterLocs; ?>"></div>
                                    </td>	
                                </tr>
                <?php $counterLocs++;
            }
        } ?>

                        <tr>
                            <td colspan="3" style="text-align:right">Total Picked Quantity</td>
                            <td id="totalpick"></td>
                        </tr>
                    <?php } else {
                        ?>
                        <tr>
                            <td colspan="6">No Records Found!</td>
                        </tr>
    <?php } ?>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="maxPicked" id="maxPicked" value="<?php echo $maxValue; ?>" />
            <input type="hidden" name="dateFrom" value="<?php echo $dateFrom; ?>" />
            <input type="hidden" name="dateTo" value="<?php echo $dateTo; ?>" />
            <button type="button" class="btn default" data-dismiss="modal">Close</button>
    <?php if (mysql_num_rows($resLocs) > 0) { ?>
                <button type="submit" class="btn btn-primary" id="save_pick" name="save_pick" data-dismiss="modal">Save changes</button>
    <?php } ?>
        </div>

    </form>
    <?php
}