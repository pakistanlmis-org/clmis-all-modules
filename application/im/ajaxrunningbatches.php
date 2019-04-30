<?php
/**
 * ajaxrunningbatches
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
	$id = $_POST['product'];
	$funding_source = (!empty($_POST['funding_source'])) ? $_POST['funding_source'] : '';
	$objStockBatch->funding_source = $funding_source;
	if(isset($_POST['adjustment']))
	{
                //Get All Adjustment Batches
		$result = $objStockBatch->GetAllAdjustmentBatches($id);
	}
	else
	{
                //Get All Running Batches
		$result = $objStockBatch->GetAllRunningBatches($id);
	}
?>
	<option value="">Select</option>
<?php
	while($row = mysql_fetch_object($result)){ ?>
		<option value="<?php echo $row->batch_id; ?>"><?php echo $row->batch_no; ?></option>
<?php } } ?>
<?php
if(isset($_POST['batch']) && !empty($_POST['batch'])){
	$id = $_POST['batch'];
        //Get Batch Expiry
	$result = $objStockBatch->GetBatchExpiry($id);	
?>
    <div class="col-md-3">
        <label class="control-label" for="firstname">Available</label>
        <div class="controls">
            <input type="text" class="form-control input-small num" name="available_qty" id="available_qty" readonly="" value="<?php echo number_format($result['qty']); ?>" />
            <input type="hidden" class="span10" name="ava_qty" id="ava_qty" value="<?php echo $result['qty']; ?>"/>
        </div>
    </div>
	<div class="col-md-2" id="expiry_div" <?php if($result['cat'] == '2') {?> style="display: none;" <?php } ?>>
		<label class="control-label" for="expiry_date">Expiry date</label>
		<div class="controls">
				<input type="text" class="form-control input-small num" name="expiry_date" id="expiry_date" readonly="" value="<?php echo date("d M, Y", strtotime($result['date'])); ?>" />
		</div>
	</div>

<?php } ?>
<?php if(isset($_POST['batch_no']) && !empty($_POST['batch_no'])){
	$id = $_POST['batch_no'];
        //Get Batch Expiry
	$result = $objStockBatch->GetBatchExpiry($id);
?>
        <input class="form-control input-small num" id="available" name="available" type="text" value="<?php echo number_format($result['qty']); ?>"  required="" disabled="" />
<?php } ?>