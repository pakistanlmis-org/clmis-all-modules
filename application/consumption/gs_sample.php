<?php
/**
 * gs_sample
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including files
include("../includes/classes/AllClasses.php");
include_once(PUBLIC_PATH."php-excel/xlsxwriter.class.php");
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

$filename = "GS.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
//Header consists of
//Store Name
//Store Code
//Product ID
//Opening Balance
//Received
//Adjustment Positive and Negative
//Sale
//Closing Balance
$header = array(
    'Store Name'=>'string',
    'Store Code'=>'string',
    'Product ID'=>'string',
    'Opening Balance'=>'integer',
    'Received'=>'integer',
    'Adjustment + '=>'double',
    'Adjustment - '=>'double',
    'Sale'=>'integer',
    'Closing Balance '=>'double'
);
//Query for Green Star sample
$qry = "SELECT DISTINCT
			tbl_warehouse.wh_name,
			tbl_warehouse.wh_id,
			itminfo_tab.itmrec_id,
			'' AS opening_balance,
			'' AS receive,
			'' AS `adjustment+`,
			'' AS `adjustment-`,
			'' AS sale,
			'' AS closing_balance
		FROM
			tbl_warehouse
		INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid,
		 itminfo_tab
		INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
		WHERE
			tbl_warehouse.stkid = ".$_SESSION['user_stakeholder']."
		AND stakeholder.lvl = 4
		AND stakeholder_item.stkid = ".$_SESSION['user_stakeholder']."
		ORDER BY
			tbl_warehouse.wh_id ASC,
			itminfo_tab.frmindex ASC";
//Query result
$rows = mysql_query($qry);
$data = array();
while( $row = mysql_fetch_row($rows) )
{
	$data[] = $row;
}
//Creating XLSXWriter
$writer = new XLSXWriter();
//Author 
$writer->setAuthor('LMIS');
//Write data on sheet
$writer->writeSheet($data,'Sheet1',$header);
$writer->writeToStdOut();
exit(0);