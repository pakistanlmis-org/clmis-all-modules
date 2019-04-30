<?php
/**
 * ajax_stock_batch_placement_info
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
    //Explode
    $arr = explode('|', base64_decode($_REQUEST['id']));
    //item
    $item = $arr[0];
    //batch_id
    $batch_id = $arr[1];
    //batch_no
    $batch_no = $arr[2];
    //batch_expiry
    $batch_expiry = $arr[3];
    ?>
    <script>
        $('body').on('hidden.bs.modal', '.modal', function() {
            $(this).removeData('bs.modal');
        });
    </script>

    <div class="span8">
        <?php
        //Query for getting location
        //Gets
        //quantity
        //quantityCarton
        //location_name
        //qty_carton
        $getLocsSql = "SELECT
                            SUM(placements.quantity)AS quantity,
							SUM(placements.quantity / itminfo_tab.qty_carton) AS quantityCarton,
                            placement_config.location_name,
                            placement_config.pk_id,
                            itminfo_tab.qty_carton,
							stock_batch.batch_id
                        FROM
                            placements
                        INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
                        INNER JOIN stock_batch ON stock_batch.batch_id = placements.stock_batch_id
                        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                        WHERE
                            stock_batch.batch_id = $batch_id
                        AND placement_config.warehouse_id = $wh_id
                        GROUP BY
                            placement_config.location_name";
        //Query result
        $resLocs = mysql_query($getLocsSql) or die(mysql_error());
        //Checking if there is a record
        if (mysql_num_rows($resLocs) > 0) {
            ?>
            <strong>Product : </strong><?php echo $item ?>,&nbsp;&nbsp;&nbsp;<strong>Batch No : </strong> <?php echo $batch_no; ?>,&nbsp;&nbsp;&nbsp;<strong>Expiry : </strong><?php echo $batch_expiry; ?>
        <?php }
        ?>
        <table class="table table-striped table-bordered table-condensed">
            <?php if (mysql_num_rows($resLocs) > 0) { ?>
                <tr>
                    <th width="5%">S.No.</th>
                    <th>Location</th>
                    <th width="20%">Available Quantity</th>
                    <th width="30%">Available Quantity(Cartons)</th>
                    <th width="10%" class="center">Action</th>
                </tr>
                <?php
                $counterLocs = 1;
				$totalQty = 0;
				$totalCartons = 0;
                while ($rowLocs = mysql_fetch_assoc($resLocs)) {
                    if ($rowLocs['quantity'] > 0) {
						$totalQty += $rowLocs['quantity'];
						$totalCartons += $rowLocs['quantityCarton'];
                        ?>
                        <tr>
                            <td class="center"><?php echo $counterLocs ?></td>
                            <td><?php echo $rowLocs['location_name']; ?></td>
                            <td class="right"><?php echo number_format($rowLocs['quantity']); ?></td>
                            <td class="right"><?php echo (floor($rowLocs['quantityCarton']) != $rowLocs['quantityCarton']) ? number_format($rowLocs['quantityCarton'], 2) : number_format($rowLocs['quantityCarton']); ?></td>
                            <td class="center">
                                <a onclick="deletePlacement('<?php echo $rowLocs['pk_id'] ?>', '<?php echo $rowLocs['batch_id'] ?>')" id="<?php echo $rowLocs['pk_id'] ?>" >
                                    <img class="cursor" src="<?php echo PUBLIC_URL;?>images/cross.gif" />
                                </a>
                            </td>
                        </tr>
                        <?php
                        $counterLocs++;
                    }
                }
                ?>
                        
            <?php } else {
                ?>
                <tr>
                    <td colspan="6">Place the product first</td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php
}
?>
