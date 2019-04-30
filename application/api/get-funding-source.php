<?php

/**
 * get-funding-source
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

include_once("DBCon.php");

// Sample Call http://localhost/clmisr2/ws/get-funding-source.php

//for funding source
$query="SELECT
			stakeholder.stkid,
			stakeholder.stkname
		FROM
			stakeholder
		WHERE
			stakeholder.stk_type_id = 2";

$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
print json_encode($rows);