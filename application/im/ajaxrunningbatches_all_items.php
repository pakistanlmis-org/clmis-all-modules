<?php
/**
 * ajaxrunningbatchesa_all_items
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Including required files
include("../includes/classes/AllClasses.php");

if(isset($_POST['product']) && !empty($_POST['product'])){
        //Product
	$id = $_POST['product'];

	$result = $objStockBatch->GetAllRunningBatches($id);
?>
	<option value="">Select</option>
<?php
	while($row = mysql_fetch_object($result)){ ?>
		<option value="<?php echo $row->batch_id; ?>"><?php echo $row->batch_no; ?></option>
<?php } } ?>
<?php
if(isset($_POST['batch']) && !empty($_POST['batch'])){
        //Batch
	$id = $_POST['batch'];
	$result = $objStockBatch->GetBatchExpiry($id);	
?>
    <div class="span6">
        <label class="control-label" for="firstname">Available</label>
        <div class="controls">
            <input type="text" class="span10" name="available_qty" id="available_qty" readonly="" value="<?php echo number_format($result['qty']); ?>" />
            <input type="hidden" class="span10" name="ava_qty" id="ava_qty" value="<?php echo $result['qty']; ?>"/>
        </div>
    </div>
	<div class="span6" id="expiry_div" <?php if($result['cat'] == '2') {?> style="display: none;" <?php } ?>>
		<label class="control-label" for="expiry_date">Expiry date</label>
		<div class="controls">
				<input type="text" class="span10" name="expiry_date" id="expiry_date" readonly="" value="<?php echo date("d M, Y", strtotime($result['date'])); ?>" />
		</div>
	</div>

<?php } ?>
<?php if(isset($_POST['batch_no']) && !empty($_POST['batch_no'])){
        //Batch No.
	$id = $_POST['batch_no'];
	$result = $objStockBatch->GetBatchExpiry($id);
?>
        <input class="span10" id="available" name="available" type="text" value="<?php echo number_format($result['qty']); ?>"  required="" disabled="" />
<?php } ?>