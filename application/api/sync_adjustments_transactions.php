<?php
/**
 * sync_adjustments_transactions
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

// Include Database Connection File
include_once("DBCon.php");          
// Example Call for 1st time: http://localhost/clmis/ws/sync_transactions.php?td=2014-02-19&tn=0006&tt=1&tr=000056&wf=1&wt=2&cb=99&co=2014-02-19&rr=remakrs&bn=b00001&be=2015-01-01&itm=12&qty=1000
// Example Call for when we have master Id: http://localhost/clmis/ws/sync_transactions.php?bn=b00001&be=2015-01-01&itm=12&qty=1000&mId=12

//Getting transactionDate
$transactionDate = !empty($_REQUEST['td']) ? $_REQUEST['td'] : '';
//Getting transactionNum
$transactionNum = !empty($_REQUEST['tn']) ? $_REQUEST['tn'] : '';
//Getting transactionTypeId
$transactionTypeId = !empty($_REQUEST['tt']) ? $_REQUEST['tt'] : '';
//Getting transactionRef
$transactionRef = !empty($_REQUEST['tr']) ? $_REQUEST['tr'] : '';
//Getting warehouseFrom
$warehouseFrom = !empty($_REQUEST['wf']) ? $_REQUEST['wf'] : '';
//Getting warehouseTo
$warehouseTo = !empty($_REQUEST['wt']) ? $_REQUEST['wt'] : '';
//Getting createdBy
$createdBy = !empty($_REQUEST['cb']) ? $_REQUEST['cb'] : '';
//Getting createdOn
$createdOn = !empty($_REQUEST['co']) ? $_REQUEST['co'] : '';
//Getting receivedRemarks
$receivedRemarks = !empty($_REQUEST['rr']) ? $_REQUEST['rr'] : '';
//Getting batchNum
$batchNum = !empty($_REQUEST['bn']) ? $_REQUEST['bn'] : '';
//Getting batchid
$batchid = !empty($_REQUEST['bid']) ? $_REQUEST['bid'] : '';
//Getting batchExpiry
$batchExpiry = !empty($_REQUEST['be']) ? $_REQUEST['be'] : '';
//Getting itemId 
$itemId = !empty($_REQUEST['itm']) ? $_REQUEST['itm'] : '';
//Getting quantity
$quantity = !empty($_REQUEST['qty']) ? $_REQUEST['qty'] : '';
//Getting masterId
$masterId = !empty($_REQUEST['mId']) ? $_REQUEST['mId'] : '';


// Insert into stock Batch table (stock_batch)
if (!empty($batchNum)) {
    // check if batch already exists
    $qry = "";

    // +
    if ($quantity > 0) {
        $qry = "SELECT
			COUNT(batch_id) AS num,
			batch_id
			FROM
			stock_batch
			WHERE
			stock_batch.batch_no = '" . $batchNum . "' AND wh_id='" . $warehouseTo;
        //Result
        $batchExist = mysql_fetch_array(mysql_query($qry));

        if ($batchExist['num'] == 0) {
            // Insert Batch
            $qry = "INSERT INTO stock_batch
				SET
                                        batch_id = '" . $batchid . "',
                                        Qty = '" . $quantity . "', 
					batch_no = '" . $batchNum . "',
					batch_expiry = '" . $batchExpiry . "',
					wh_id = '" . $warehouseTo . "',
					item_id = '" . $itemId . "' ";
            mysql_query($qry);
            //Returns bach id
            $batchPKId = mysql_insert_id();
        } else {

            $qry = "SELECT
			Qty
			FROM
			stock_batch
			WHERE
			stock_batch.batch_no = '" . $batchNum . "' AND wh_id='" . $warehouseTo;

            $batchqty = mysql_fetch_array(mysql_query($qry));

            $batchPKId = $batchExist['batch_id'];

            $updatedQty = $batchqty['Qty'] + $quantity;

            // Update Batch qty.
            $qry = "Update  stock_batch
				SET                                      
                              Qty = '" . $updatedQty . "' WHERE batch_id='" . $batchPKId . "' AND wh_id='" . $warehouseTo;

            mysql_query($qry);
        }
    }   // -
    else if ($quantity < 0) {
        $qry = "SELECT
				COUNT(batch_id) AS num,
				batch_id
			FROM
				stock_batch
			WHERE
				stock_batch.batch_no = '" . $batchNum . "' AND wh_id='" . $warehouseFrom;
    }
}

// Insert into stock master table (tbl_stock_master)
if (empty($masterId)) {
    $qry = "INSERT INTO tbl_stock_master
			SET
				TranDate = '" . $transactionDate . "',
				TranNo = '" . $transactionNum . "',
				TranTypeID = '" . $transactionTypeId . "',
				TranRef = '" . $transactionRef . "',
				WHIDFrom = '" . $warehouseFrom . "',
				WHIDTo = '" . $warehouseTo . "',
				CreatedBy = '" . $createdBy . "',
				CreatedOn = '" . $createdOn . "',
				temp='0',
				ReceivedRemarks = '" . $receivedRemarks . "' ";
    mysql_query($qry);
    //Returns master pk id
    $masterPKId = mysql_insert_id();
} else {
    $masterPKId = $masterId;
}

// Insert into stock detail table (tbl_stock_detail)
if (!empty($quantity)) {
    $qry = "INSERT INTO tbl_stock_detail
			SET
				fkStockID = '" . $masterPKId . "',
				BatchID = '" . $batchPKId . "',
				temp='0',
				Qty = '" . $quantity . "' ";
    mysql_query($qry);
    //Returns detail pk id
    $detailPKId = mysql_insert_id();
}
$arr = array('master_id' => $masterPKId, 'detail_id' => $detailPKId);

$arr1[] = $arr;
//Encode in json
print(json_encode($arr1));
?>