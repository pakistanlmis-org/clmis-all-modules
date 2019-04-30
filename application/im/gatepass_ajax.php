<?php
/**
 * gatepass_ajax
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
//wh id
$whId = $_SESSION['user_warehouse'];
// Get vehicles
if (isset($_POST['vehicleType'])) {
    $vNum = isset($_POST['vNum']) ? mysql_real_escape_string($_POST['vNum']) : '';
    //vehicle Type 
    $vehicleType = mysql_real_escape_string($_POST['vehicleType']);
    //query
    //gets
    //gatepass_vehicles.pk_id,
    //gatepass_vehicles.number
    $qry = "SELECT
				gatepass_vehicles.pk_id,
				gatepass_vehicles.number
			FROM
				gatepass_vehicles
			WHERE
				gatepass_vehicles.vehicle_type_id = $vehicleType";
    //query result
    $qryRes = mysql_query($qry);
    //check if result exists
    if (mysql_num_rows(mysql_query($qry)) > 0) {
        //fetch result
        while ($row = mysql_fetch_array($qryRes)) {
            ?>
            <option value="<?php echo $row['pk_id']; ?>" <?php echo ($vNum == $row['pk_id']) ? 'selected="selected"' : ''; ?>><?php echo $row['number']; ?></option>
            <?php
        }
    } else {
        echo '<option value="">Select</option>';
    }
}
if (isset($_POST['dateFrom']) && isset($_POST['dateTo'])) {
    //date from
    $dateFrom = dateToDbFormat(mysql_real_escape_string($_POST['dateFrom']));
    //date to
    $dateTo = dateToDbFormat(mysql_real_escape_string($_POST['dateTo']));
    //select query
    //gets
    //pk stock id
    //transaction num
    //transaction date
    //pk detail id
    //detail qty
    $qry = "SELECT
				*
			FROM
				(
					SELECT
						tbl_stock_master.PkStockID,
						tbl_stock_master.TranNo,
						tbl_stock_master.TranDate,
						tbl_stock_detail.PkDetailID,
						SUM(tbl_stock_detail.Qty) AS detailQty,
						IFNULL(
							SUM((
								SELECT
									SUM(gatepass_detail.quantity)
								FROM
									gatepass_detail
								WHERE
									gatepass_detail.stock_detail_id = tbl_stock_detail.PkDetailID
							)),
							0
						) AS gatepassQty
					FROM
						tbl_stock_master
					INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
					WHERE
						tbl_stock_master.WHIDFrom = $whId
					AND tbl_stock_master.TranTypeID = 2
					AND tbl_stock_master.CreatedOn BETWEEN '$dateFrom'
					AND '$dateTo'
					GROUP BY
						tbl_stock_master.PkStockID
					ORDER BY
						tbl_stock_master.TranDate DESC
				) A
			WHERE
				A.detailQty + A.gatepassQty != 0
			ORDER BY
				A.TranDate DESC";
    //query result
    $qryRes = mysql_query($qry);
    //fetch result
    while ($row = mysql_fetch_array($qryRes)) {
        ?>
        <option value="<?php echo $row['PkStockID']; ?>"><?php echo $row['TranNo']; ?></option>
        <?php
    }
}

// If issued numbers are selected
if (isset($_POST['issueNum'])) {
    //select query 
    //gets
    //pk stock id
    //item name
    //batch id
    //batch no
    //qty
    $qry = "SELECT * FROM (SELECT
				tbl_stock_master.PkStockID,
				itminfo_tab.itm_name,
				stock_batch.batch_id,
				stock_batch.batch_no,
				SUM(ABS(tbl_stock_detail.Qty)) - 
				(
					COALESCE((SELECT
						SUM(gatepass_detail.quantity)
					FROM
						gatepass_detail
					WHERE
						gatepass_detail.stock_detail_id = tbl_stock_detail.PkDetailID
					), NULL, 0)
				) AS Qty
			FROM
				tbl_stock_master
			INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
			INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
			INNER JOIN itminfo_tab ON itminfo_tab.itm_id = stock_batch.item_id
			WHERE
				tbl_stock_master.WHIDFrom = $whId
			AND tbl_stock_master.TranTypeID = 2
			AND tbl_stock_master.PkStockID IN (" . implode(',', $_POST['issueNum']) . ")
			GROUP BY
				stock_batch.batch_id) A
			WHERE A.Qty > 0";
    //query result
    $qryRes = mysql_query($qry);
    ?>	
    <style>
        table#myTable tr td, table#myTable tr th{font-size:13px;padding:5px; text-align:left; border:1px solid #999;}
    </style>
    <table width="100%" id="myTable" cellpadding="4" cellspacing="4">
        <thead>
            <tr bgcolor="#009C00" style="color:#FFF;">
                <th>Product</th>
                <th width="20%">Batch No.</th>
                <th width="15%">Issued Quantity</th>
                <th width="12%">Quantity</th>
                <th width="15%">Remaining Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php
            //fetch results
            while ($row = mysql_fetch_array($qryRes)) {
                ?>
                <tr>
                    <td><?php echo $row['itm_name']; ?></td>
                    <td><?php echo $row['batch_no']; ?></td>
                    <td style="text-align:right;"><?php echo number_format($row['Qty']); ?></td>
                    <td style="text-align:right;"><input type="text" style="text-align:right; height:24px;" id="test" class="qty form-control input-small" autocomplete="off" onkeyup="updateQty(this.name, this.max)" max="<?php echo $row['Qty']; ?>" name="qty[<?php echo $row['batch_id']; ?>]" /></td>
                    <td style="text-align:right;"><?php echo number_format($row['Qty']); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
?>
<script>
    $(function() {
        $('.qty').keydown(function(e) {
            if (e.shiftKey || e.ctrlKey || e.altKey) { // if shift, ctrl or alt keys held down
                e.preventDefault();         // Prevent character input
            } else {
                var n = e.keyCode;
                if (!((n == 8)              // backspace
                        || (n == 9)                // Tab
                        || (n == 46)                // delete
                        || (n >= 35 && n <= 40)     // arrow keys/home/end
                        || (n >= 48 && n <= 57)     // numbers on keyboard
                        || (n >= 96 && n <= 105))   // number on keypad
                        ) {
                    e.preventDefault();     // Prevent character input
                }
            }
        });
    })

    function updateQty(qtyFld, maxVal)
    {
        var userQty = $('input[name="' + qtyFld + '"]').val();
        var remaining = parseInt(maxVal) - ((userQty != '') ? parseInt(userQty) : 0);
        $('input[name="' + qtyFld + '"]').parent().next('td').html(remaining);
    }
</script>