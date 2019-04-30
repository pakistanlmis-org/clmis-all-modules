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
//get batch
if (!empty($_REQUEST['batch']))
{
    $batch = $_REQUEST['batch'];
    list($d, $m, $y) = explode('/', $_REQUEST['expiry_date']);
    $receive_from = $_REQUEST['receive_from'];
    $manufacturer = $_REQUEST['manufacturer'];
    $product = $_REQUEST['product'];
	$expiry_date = $y.'-'.$m.'-'.$d;
	$qry = "INSERT INTO stock_batch
		SET
			stock_batch.batch_no = '".$batch."',
			stock_batch.batch_expiry = '".$expiry_date."',
			stock_batch.item_id = '".$product."',
			stock_batch.`status` = 'Finished',
			stock_batch.wh_id = '".$_SESSION['user_warehouse']."',
			stock_batch.funding_source = '".$receive_from."',
			stock_batch.manufacturer = '".$manufacturer."' ";
        //query result
	mysql_query($qry);
	echo mysql_insert_id();
}