<?php
/**
 * ajaxbatch
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
//Checking id
if (isset($_POST['id']) && !empty($_POST['id'])) {
//Getting id
    $id = $_POST['id'];
    //Setting id
    $result = $objStockBatch->find_by_item($id);
    //Check result
    if (isset($result)) {
        (int) $RunningDoses = $result->RunningQty;
        (int) $StackedDoses = $result->StackedQty;
        (int) $FinishedDoses = $result->FinishedQty;
        (int) $total = $RunningDoses + $StackedDoses + $FinishedDoses;
    } else {
        $RunningDoses = 0;
        $StackedDoses = 0;
        $FinishedDoses = 0;
        $total = 0;
    }
    ?>
    <!-- Widget heading -->
    <div class="widget-head">
        <h4 class="heading"> <?php echo $result->itm_name; ?></span></h4>
    </div>
    <!-- // Widget heading END -->

    <div class="widget-body">
        <div class="col-md-4">
            <p><b>Batch Status</b></p>
            <p>Running</p>
            <p>Stacked</p>
            <p>Finished</p>
            <p><b>Total</b></p>
        </div>
        <div class="col-md-4 center">
            <p style="text-align:right"><b>No of Batches</b></p>
            <p style="text-align:right" id="running"><?php echo!empty($result->running) ? $result->running : 0; ?></p>
            <p style="text-align:right" id="stacked"><?php echo!empty($result->stacked) ? $result->stacked : 0; ?></p>
            <p style="text-align:right" id="finished"><?php echo!empty($result->finished) ? $result->finished : 0; ?></p>
            <p style="text-align:right" id="total"><b><?php echo $result->running + $result->stacked + $result->finished; ?></b></p>
        </div>
        <div class="col-md-4 center">
            <p style="text-align:right"><b>Quantity (<?php echo $result->itm_type; ?>)</b></p>
            <p style="text-align:right"><?php echo number_format($RunningDoses); ?></p>
            <p style="text-align:right"><?php echo number_format($StackedDoses); ?></p>
            <p style="text-align:right"><?php echo number_format($FinishedDoses); ?></p>
            <p style="text-align:right"><b><?php echo number_format($total); ?></b></p>
        </div>
    </div>
    <div style="clear:both;"></div>
    <?php
}
//Checking batch_id
if (isset($_POST['batch_id']) && !empty($_POST['batch_id'])) {
    //Getting batch_id
    $batch_id = $_POST['batch_id'];
    //Getting status
    $status = $_POST['status'];
    //Check status
    if ($status == 'Running' || $status == 'Finished') {
        $button = 'Stacked';
    } else {
        $button = 'Running';
    }
    //change Status
    $result = $objStockBatch->changeStatus($batch_id, $button);
    if ($result) {
        $array = array(
            'status' => $button,
            'button' => $status
        );
    } else {
        $array = array(
            'status' => $status,
            'button' => $button
        );
    }
    //Encode in json
    echo json_encode($array);
}
?>