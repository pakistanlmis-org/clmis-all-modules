<?php

/**
 * update_batch_by_ajax
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

$dtl        = $_REQUEST['val'];
$batch_id   = $_REQUEST['batch'];

$qry = "UPDATE stock_batch  SET dtl = '$dtl'  WHERE batch_id = $batch_id ";
mysql_query($qry);
echo 'Batch info updated.';
?>