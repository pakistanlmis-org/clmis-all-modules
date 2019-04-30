<?php

/**
 * uploadRpt
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Including required files
include("../../application/includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include('auth.php');

//Getting form data
//Getting WHID
$WHID = $_REQUEST['W'];
//Getting YYYY
$YYYY= $_REQUEST['Y'];
//Getting MM
$MM= $_REQUEST['M'];
//Getting ITEM_ID
$ITEM_ID=$_REQUEST['I'];
//Getting OBa
$OBa=$_REQUEST['OBa'];
//Getting OB
$OB=$_REQUEST['OB'];
//Getting RCV
$RCV=$_REQUEST['R'];
//Getting ISSUE
$ISSUE=$_REQUEST['IS'];
//Getting ADJA
$ADJA=$_REQUEST['A'];
//Getting ADJB
$ADJB=$_REQUEST['B'];
//Getting CB
$CB=$_REQUEST['CB'];


$id=0;
if(!empty($WHID) && !empty($YYYY) && !empty($MM) && !empty($ITEM_ID))
{
	$query="SELECT tbl_wh_data.*
	   FROM tbl_wh_data
	   WHERE
		tbl_wh_data.report_month = $MM AND
		tbl_wh_data.report_year = $YYYY AND
		tbl_wh_data.item_id = '$ITEM_ID' AND
		tbl_wh_data.wh_id = $WHID";
        //Query result
	$rs = mysql_query($query) or die(print mysql_error());
	
	while ($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
    $id=$row['w_id'];	;
}
	mysql_free_result($rs);

	if ($id==0)
	{
		$query="INSERT INTO tbl_wh_data(report_month,report_year,item_id,wh_id,wh_obl_a,wh_obl_c,wh_received,wh_issue_up,wh_cbl_a,wh_cbl_c,wh_adja,wh_adjb,RptDate)
				 VALUES($MM,$YYYY,'$ITEM_ID',$WHID,$OBa,$OB,$RCV,$ISSUE,$CB,$CB,$ADJA,$ADJB,'$YYYY-$MM-01')";
		//Query result
		$rs = mysql_query($query) or die(print mysql_error());
		$id = mysql_insert_id();
	}
	else
	{	
		$query="UPDATE tbl_wh_data SET wh_obl_a=$OB,wh_obl_a=$OBa,wh_obl_c=$OB,wh_received=$RCV,wh_issue_up=$ISSUE,wh_cbl_a=$CB,wh_cbl_c=$CB,wh_cbl_a=$CB,wh_adja=$ADJA,wh_adjb=$ADJB
				 WHERE w_id=$id";
                //Query result
		$rs = mysql_query($query) or die(print mysql_error());
	}
}
else{
	print "-1";
}

//example: http://localhost/lmis/ws/UploadRpt.php?W=124&Y=2011&M=1&I=IT-001&OB=0&R=0&IS=0&A=0&B=0&CB=0&Z=0
print $id;
?>