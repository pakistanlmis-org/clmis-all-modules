<?php
include("../includes/classes/AllClasses.php");
if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    $detail_id = $_REQUEST['Id'];
    $type = $_REQUEST['type'];

    if ($type == 'qty') {
        $uQty = str_replace(",", "", $_REQUEST['data']);
        $objStockDetail->editReceive($detail_id, $uQty);
    } else if ($type == 'batch') {
        $ubatch = $_REQUEST['data'];
        $objStockBatch->editBatchNo($detail_id, $ubatch);
    }
}
?>