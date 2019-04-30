<?php

/*echo '<pre>';
print_r($_REQUEST);
exit;*/
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
//get warehouse id
$wh_id = $_SESSION['user_warehouse'];
$sCriteria = array();
$date_to = '';
$product = '';
$stock_ledger = array();
$batch_ob = array();
$batch_cb = array();
$query = array();
parse_str($_POST['data'], $query);
//check if submitted
if (isset($_POST['data']) && !empty($_POST['data'])) {
    //check product
    if (isset($query['product']) && !empty($query['product'])) {
        //get product
        $product = $query['product'];

        $sCriteria['product'] = $product;
        //set product
        $objStockMaster->item_id = $product;

        $qry3 = "SELECT
               itminfo_tab.itm_name
        FROM
             itminfo_tab 
        WHERE
                itminfo_tab.itm_id = $product";
        $qryRes3 = mysql_query($qry3);
        $pr = mysql_fetch_array($qryRes3);
    } else {
        $sCriteria['product'] = 1;
        //set product
        $objStockMaster->item_id = 1;
    }
    //check from date
    if (isset($query['date_from']) && !empty($query['date_from'])) {
        //get to date
        $date_from = $query['date_from'];
        $dateArr = explode('/', $date_from);
        $sCriteria['date_from'] = dateToDbFormat($date_from);
        //set to date
        $objStockMaster->fromDate = dateToDbFormat($date_from);
    }
    //check to date
    if (isset($query['date_to']) && !empty($query['date_to'])) {
        //get to date
        $date_to = $query['date_to'];
        $dateArr = explode('/', $date_to);
        $sCriteria['date_to'] = dateToDbFormat($date_to);
        //set to date
        $objStockMaster->toDate = dateToDbFormat($date_to);
    }
    if (isset($query['funding_source']) && !empty($query['funding_source'])) {
        //get to date
        $funding_source = $query['funding_source'];
        $sCriteria['funding_source'] = $funding_source;
        //set to date
        $objStockMaster->funding_source = $funding_source;
    }
    $_SESSION['sCriteria'] = $sCriteria;

    $stock_ledger = $objStockMaster->GetStockLedger();
    //echo '<pre>';print_r($stock_ledger);exit;
    $batch_ob = $objStockMaster->getBatchOBCB('OB', $product, dateToDbFormat($date_from),$funding_source);
    $batch_cb = $objStockMaster->getBatchOBCB('CB', $product, dateToDbFormat($date_to),$funding_source);
    $batch_cb1 = $objStockMaster->getBatchOBCB('CB', $product, dateToDbFormat($date_to),$funding_source);
    
    //echo '<pre>';
    //print_r($batch_ob);
    //echo $product;
    //print_r($batch_cb);
    //print_r($batch_cb1);
    //exit;
    
} else {
    //date to
    $date_to = date('d/m/Y');
    //set from date
    $date_from = date('1/m/Y');
    $objStockMaster->toDate = dateToDbFormat($date_to);
    $objStockMaster->fromDate = dateToDbFormat($date_from);

    $sCriteria['date_to'] = dateToDbFormat($date_to);
    $sCriteria['date_from'] = dateToDbFormat($date_from);
    $_SESSION['sCriteria'] = $sCriteria;
}

$items = $objManageItem->GetAllManageItem();
?>
<?php if ($stock_ledger) { ?>
    <div class="row">
        <div class="col-md-12">
            <table class="stkledger table table-striped table-bordered table-condensed" >
                <thead>
                    <tr>
                        <th rowspan="2">S.No</th>
                        <th rowspan="2">Voucher Date</th>
                        <th rowspan="2">Voucher Number</th>
                        <th rowspan="2">Type</th>
                        <th rowspan="2">Particulars</th>
                        <th rowspan="2">Batch No.</th>
                        <th rowspan="2">Funding Source</th>
                        <th rowspan="2">Expiry</th>
                        <th colspan="2" class="center">Quantity</th>
                        <th rowspan="2" >Batch Balance</th>
                        <th rowspan="2" >Product Balance</th>
                        <th rowspan="2">Created Date</th>
                        <th rowspan="2">Created By</th>
                    </tr>
                    <tr>
                        <th>Receive</th>
                        <th>Issue</th>
                        <!--<th>Doses</th> -->
                    </tr>
                </thead>
                <?php
                //$balance_vials = $this->ob;
                ?>
                <tbody>
                    <?php
                    $count = 1;
                    $batch_cb = array();
                    $balance_vials_ob = 0;

                    if (count($batch_ob) > 0  ) {

                        foreach ($batch_ob as $b) {
                            $balance_vials_ob = $balance_vials_ob + ($b['Qty']);
                            ?>
                            <tr>
                                <th><?php echo $count; ?></th>
                                <th><?php echo $date_from; ?></th>
                                <th></th>
                                <th></th>
                                <th>Opening Balance (<?php echo $b['batch_no']; ?>)</th>
                                <th>
                                <?php
                                $pop = 'onclick="window.open(\'product-ledger-history.php?id=' . $b['batch_id'] . '\',\'_blank\',\'scrollbars=1,width=840,height=595\')"';
                                echo "<a class='alert-link' " . $pop . " ></br>" . $b['batch_no'] . "</a>";

//                                echo $b['batch_no']; 
                                ?>
                                </th>
                                <th><?php echo $b['funding_source_name']; ?></th>
                                
                                <th></th>
                                <th class="right"><?php ?></th>
                                <th class="right"></th>
                                <th class="right"><?php echo number_format($b['Qty']); ?></th>
                                <th class="right"></th>
                                <th class="right"><?php ?></th>
                                <th class="right"></th>

                            </tr>
                            <?php
                            $count++;
                        }
                    }
                    ?>

                    <tr>
                        <th><?php echo $count; ?></th>
                        <th><?php echo $date_from; ?></th>
                        <th></th>
                        <th></th>
                        <th>Opening Balance (<?php echo $pr['itm_name']; ?>) </th>
                        <th></th>
                        <th></th>
                        <th class="right"><?php ?></th>
                        <th class="right"></th>
                        <th class="right"><?php ?></th>
                        <th class="right"><?php echo number_format($balance_vials_ob); ?></th>
                        <th class="right"></th>


                        <th class="right"></th>

                    </tr>
                    <?php
                    $count++;
                    $balance_vials = $balance_vials_ob;
                    foreach ($stock_ledger as $row) {

                        $nature = $row['trans_nature'];
                        $quantity_vials = $row['quantity'];
                        //$quantity_doses = $quantity_vials * $row->getStockBatchWarehouse()->getStockBatch()->getPackInfo()->getStakeholderItemPackSize()->getItemPackSize()->getNumberOfDoses();
                        $balance_vials = $balance_vials + ($nature . ABS($quantity_vials));
                        //$balance_doses = $balance_doses + ($nature . ABS($quantity_doses));
                        $created_date = $row['CreatedOn'];
                        $created_by = $row['usrlogin_id'];

                        if (array_key_exists($row['stock_batch_warehouse_id'], $batch_cb)) {

                            $batch_cb[$row['stock_batch_warehouse_id']] = $batch_cb[$row['stock_batch_warehouse_id']] + ($nature . ABS($quantity_vials));
                            //$batch_cb_doses = $batch_cb[$row->getStockBatchWarehouse()->getPkId()] * $row->getStockBatchWarehouse()->getStockBatch()->getPackInfo()->getStakeholderItemPackSize()->getItemPackSize()->getNumberOfDoses();
                        } else {
                            $batch_cb[$row['stock_batch_warehouse_id']] = $objStockMaster->getBatchOB($row['stock_batch_warehouse_id'], $date_from) + ($nature . ABS($quantity_vials));
                            //$batch_cb_doses = $batch_cb[$row['stock_batch_warehouse_id']] * $row->getStockBatchWarehouse()->getStockBatch()->getPackInfo()->getStakeholderItemPackSize()->getItemPackSize()->getNumberOfDoses();
                        }

                        if ($row['TranTypeID'] == 2) {
                            $print_link = 'printIssue.php?id=' . $row['stock_master_id'];
                            $warehouse_name = "To " . $row['toWh'];
                        } else {
                            $print_link = 'printReceive.php?id=' . $row['stock_master_id'] . '&type=' . $row['TranTypeID'];
                            $warehouse_name = "From " . $row['fromWh'];
                        }
                        ?>
                        <tr>
                            <td><?php echo $count; ?></td>
                            <td title="<?php echo $row['TranNo']; ?>"><?php echo $row['TranDate']; ?></td>
                            <td><a onclick="window.open('<?php echo $print_link; ?>', '_blank', 'scrollbars=1,width=860,height=595');" href="javascript:void(0);"><?php echo $row['TranNo']; ?></a></td>
                            <td><?php echo $row['trans_type']; ?></td>
                            <td><?php echo $warehouse_name; ?></td>
                            <td title=""><?php
                                $pop = 'onclick="window.open(\'product-ledger-history.php?id=' . $row['batch_id'] . '\',\'_blank\',\'scrollbars=1,width=840,height=595\')"';
                                echo "<a class='alert-link' " . $pop . " ></br>" . $row['batch_no'] . "</a>";

//                                echo $b['batch_no']; 
                                ?>
                            </td>
                            <td title=""><?php echo $row['funding_source_name']; ?></td>
                            <td><?php echo $row['batch_expiry']; ?></td>
                            <?php if ($row['quantity'] > 0) { ?>
                                <td class="right"><?php echo number_format(ABS($quantity_vials)); ?></td>
                                <td class="right"></td>
                            <?php } else { ?>
                                <td class="right"></td>
                                <td class="right"><?php echo number_format(ABS($quantity_vials)); ?></td>
                            <?php } ?>
        <!--<td class="right"><?php //echo number_format($batch_cb_doses);              ?></td> -->
                            <td class="right"><?php echo number_format($batch_cb[$row['stock_batch_warehouse_id']]); ?></td>
                            <!-- <td class="right"><?php //echo number_format($balance_doses);                  ?></td> -->
                            <td class="right"><?php echo number_format($balance_vials); ?></td>
                            <td><?php echo $created_date; ?></td>
                            <td><?php echo $created_by; ?></td>
                            <!-- <td class="right"><?php ?></td> -->
                        </tr>
                        <?php
                        $count++;
                    }
                    foreach ($batch_cb1 as $cb) {
                        ?>
                        <tr>
                            <th><?php echo $count; ?></th>
                            <th><?php echo $date_to; ?></th>
                            <th></th>
                            <th></th>
                            <th>Closing Balance (<?php echo $cb['batch_no']; ?>)</th>
                            
                            <th><?php 
                            $pop = 'onclick="window.open(\'product-ledger-history.php?id=' . $cb['batch_id'] . '\',\'_blank\',\'scrollbars=1,width=840,height=595\')"';
                            echo "<a class='alert-link' " . $pop . " ></br>" . $cb['batch_no'] .  "</a>";
                                
                            //echo $b['batch_no']; ?>
                            </th>
                            
                            <th><?php echo $cb['funding_source_name']; ?></th>
                            <th></th>
                            <th class="right"><?php ?></th>
                            <th class="right"></th>
                            <th class="right"><?php echo number_format($cb['Qty']); ?></th>
                            <th class="right"></th>
                            <th class="right"><?php ?></th>
                            <th class="right"></th>

                                                                                  <!-- <th class="right"><?php ?></th> -->
                            <!-- </tr>-->
                        </tr>
                        <?php
                        $count++;
                    }
                    ?>

                    <tr>
                        <th><?php echo $count; ?></th>
                        <th><?php echo $date_to; ?></th>
                        <th></th>
                        <th></th>
                        <th>Closing Balance (<?php echo $pr['itm_name']; ?>)</th>
                        <th></th>
                        <th></th>
                        <th class="right"><?php ?></th>
                        <th class="right"></th>
                        <th class="right"><?php ?></th>

                        <th class="right"><?php echo number_format($balance_vials); ?></th>
                        <th class="right"></th>
                        <th class="right"></th>
                                                   <!-- <th class="right"><?php ?></th> -->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php } else { ?>
    <div class="row">
        <div class="col-md-12">
            <h4>Transactions not found</h4>
        </div>
    </div>
<?php } ?>
