<?php

/**
 * PPMR
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH."html/header.php");



// When form is submitted
if (isset($_POST['submit']))
{
	// Get values into variables
   //selected month
    $selMonth = mysql_real_escape_string($_POST['month']);
   //selected  year
    $selYear = mysql_real_escape_string($_POST['year']);
   //selected sector  
    $selSector = mysql_real_escape_string($_POST['sector']);
	//reporting Date 
    $reportingDate = $selYear . '-' . $selMonth;
}
// Default case
else
{
    //check date
	if (date('d') > 10) {
    	//set date
		$date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
    } else {
    	//set date    
        $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
    }
    //seleted month
    $selMonth = date('m', strtotime($date));
    //seleted year
    $selYear = date('Y', strtotime($date));
	//reporting Date 
    $reportingDate = $selYear . '-' . $selMonth;
	//seleted Sector
    $selSector = 0;
}
// Central Warehosue Id
$cwh_id = 123;
//If sector is Public then Get Central Warehouse ID ele Get the GS Warehouse ID
if($selSector == 0){
	//warehouse id
    $wh_id = $cwh_id;
	//recipient
    $recipient = 10;
}else{
	//warehouse id
    $wh_id = 5621;
	//recipient
    $recipient = 3;
}
// Stakeholder Product filter
$stkFilter = ($selSector == 0) ? " AND stakeholder.stk_type_id = 0" : " AND stakeholder.stkid = 5";
//sector
$sector = ($selSector == 0) ? " Public Sector" : " Private Sector";
// Donwload file name
$fileName = 'PPMR - ' . $sector . ' for ' . date('M-Y', strtotime($reportingDate));




$clmis_shipments = array();
//fetching data of pipeline from cLMIS tables
if($_REQUEST['sector'] == '0')
{
    $qry = "SELECT
                shipments.item_id,
                shipments.shipment_date as s_dates,
                shipments.shipment_quantity as qty,
                tbl_warehouse.wh_name as proc_by
            FROM
                    shipments
            INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
            INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
            INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
            WHERE
                    shipments.shipment_date >=  '$reportingDate-01'
                    AND shipments.status NOT IN ('Cancelled','Received')
            ORDER BY
                shipments.item_id,
                shipments.procured_by,
                shipments.shipment_date 
    ";
    //Query result
    //echo $qry;exit;
    $qryRes = mysql_query($qry);

    while($row = mysql_fetch_assoc($qryRes))
    {
       $clmis_shipments[$row['item_id']][]= $row;
    }
}
//echo '<pre>';print_r($clmis_shipments);exit;
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
    <?php 
    ////include top
    include PUBLIC_PATH."html/top.php";
        //include top_im
        include PUBLIC_PATH."html/top_im.php";?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Procurement Planning Monitoring Report</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Month</label>
                                                    <div class="controls">
                                                        <select name="month" id="month" class="form-control input-sm" required>
                                                            <?php
                                                            for ($i = 1; $i <= 12; $i++) {
                                                                if ($selMonth == $i) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo date('m', mktime(0, 0, 0, $i, 1)); ?>"<?php echo $sel; ?> ><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Year</label>
                                                    <div class="controls">
                                                        <select name="year" id="year" class="form-control input-sm" required>
                                                            <?php
                                                            for ($j = date('Y'); $j >= 2010; $j--) {
                                                                if ($selYear == $j) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Sector</label>
                                                    <div class="controls">
                                                        <select class="form-control input-sm" id="sector" name="sector" required>
                                                            <option <?php echo ($selSector == 0) ? 'selected="selected"' : ''; ?> value="0">Public</option>
                                                            <option <?php echo ($selSector == 1) ? 'selected="selected"' : ''; ?> value="1">GS</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name="submit" id="go" value="GO" class="btn btn-primary input-sm" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php   
                //select query
				// Get all Products
                //name
                //id
				$qry = "SELECT DISTINCT
							itminfo_tab.itm_name,
							itminfo_tab.itm_id
						FROM
							itminfo_tab
						INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
						INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
						WHERE
							itminfo_tab.itm_category = 1
						$stkFilter
						AND itminfo_tab.itm_id != 33
						ORDER BY
							itminfo_tab.frmindex ASC";
                                //query result
				$qryRes = mysql_query($qry);
                                //fetch result
				while ($row = mysql_fetch_array($qryRes))
				{
					//item id	
                    $items[] = $row['itm_id'];
					//item name
                    $data[$row['itm_id']]['Product'] = $row['itm_name'];
					//AMC
                    $data[$row['itm_id']]['AMC'] = 0;
					//SOH
                    $data[$row['itm_id']]['SOH'] = 0;
				}
				//SELECT query
               	//gets
				// AMC of All Products except Implenon and Jadelle
                //item id
				//item name
				//avg consumption
				$qryAMC = "SELECT
								itminfo_tab.itm_id,
								itminfo_tab.itm_name,
								ROUND(SUM(summary_national.avg_consumption)) AS avg_consumption
							FROM
								summary_national
							INNER JOIN itminfo_tab ON summary_national.item_id = itminfo_tab.itmrec_id
							INNER JOIN stakeholder ON summary_national.stakeholder_id = stakeholder.stkid
							WHERE
								DATE_FORMAT(summary_national.reporting_date, '%Y-%m') = '$reportingDate'
							AND itminfo_tab.itm_category = 1
							AND itminfo_tab.itm_id NOT IN (8, 13)
							$stkFilter
							AND itminfo_tab.itm_id IN (".implode(',', $items).")
							GROUP BY
								itminfo_tab.itmrec_id
							ORDER BY
								itminfo_tab.frmindex ASC";
                                //query result
				$qryAMCRes = mysql_query($qryAMC);
                                //if result exists
				if (mysql_num_rows(mysql_query($qryAMC)) > 0) {
                                    //fetch result
					while ($row = mysql_fetch_array($qryAMCRes)){
						$data[$row['itm_id']]['AMC'] = $row['avg_consumption'];
					}
				}
				
				if($selSector == 0)
				{
					// AMC of Implenon
					 $qryAMC = "SELECT
									A.item_id AS itm_id,
									A.itm_name,
									ROUND(AVG(A.Qty)) AS Qty
								FROM
									(
										SELECT
											stock_batch.item_id,
											itminfo_tab.itm_name,
											ABS(SUM(tbl_stock_detail.Qty)) AS Qty,
											DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m') AS TranDate
										FROM
											tbl_stock_master
										INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
										INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
										INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
										WHERE
											stock_batch.item_id = 8
										AND tbl_stock_master.WHIDFrom = $cwh_id
										AND tbl_stock_master.TranTypeID = 2
										AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m') <= '$reportingDate'
										GROUP BY
											itminfo_tab.itm_id,
											DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m')
										ORDER BY
											itminfo_tab.frmindex ASC,
											tbl_stock_master.TranDate DESC
										LIMIT 3
									) A";
					//query result
					$qryAMCRes = mysql_query($qryAMC);
					//if result exists
					if (mysql_num_rows(mysql_query($qryAMC)) > 0) {
										 //fetch result
						while ($row = mysql_fetch_array($qryAMCRes)){
							$data[$row['itm_id']]['AMC'] = $row['Qty'];
						}
					}
					// AMC of Jadelle
					$qryAMC = "SELECT
									A.item_id AS itm_id,
									A.itm_name,
									ROUND(AVG(A.Qty)) AS Qty
								FROM
									(
										SELECT
											stock_batch.item_id,
											itminfo_tab.itm_name,
											ABS(SUM(tbl_stock_detail.Qty)) AS Qty,
											DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m') AS TranDate
										FROM
											tbl_stock_master
										INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
										INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
										INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
										WHERE
											stock_batch.item_id = 13
										AND tbl_stock_master.WHIDFrom = $cwh_id
										AND tbl_stock_master.TranTypeID = 2
										AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m') <= '$reportingDate'
										GROUP BY
											itminfo_tab.itm_id,
											DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m')
										ORDER BY
											itminfo_tab.frmindex ASC,
											tbl_stock_master.TranDate DESC
										LIMIT 3
									) A";
					//query result
					$qryAMCRes = mysql_query($qryAMC);
					//if result exists
					if (mysql_num_rows(mysql_query($qryAMC)) > 0) {
										 //fetch result
						while ($row = mysql_fetch_array($qryAMCRes)){
							$data[$row['itm_id']]['AMC'] = $row['Qty'];
						}
					}
				}
				else
				{
					// AMC of Implenon
					$qryAMC = "SELECT
									A.itm_id,
									A.itm_name,
									ROUND(AVG(A.Qty)) AS Qty
								FROM
									(
										SELECT
											itminfo_tab.itm_id,
											itminfo_tab.itm_name,
											SUM(tbl_wh_data.wh_issue_up) AS Qty
										FROM
											tbl_warehouse
										INNER JOIN tbl_wh_data ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
										INNER JOIN stakeholder AS Office ON Office.stkid = tbl_warehouse.stkofficeid
										INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
										WHERE
											tbl_wh_data.RptDate <= '$reportingDate'
										AND tbl_warehouse.stkid = 5
										AND Office.lvl = 1
										AND itminfo_tab.itm_id = 8
										GROUP BY
											tbl_wh_data.RptDate
										ORDER BY
											tbl_wh_data.RptDate DESC
										LIMIT 3
									) A";
					//query result
					$qryAMCRes = mysql_query($qryAMC);
					//if result exists
					if (mysql_num_rows(mysql_query($qryAMC)) > 0) {
										 //fetch result
						while ($row = mysql_fetch_array($qryAMCRes)){
							$data[$row['itm_id']]['AMC'] = $row['Qty'];
						}
					}
					// AMC of Jadelle
					$qryAMC = "SELECT
									A.itm_id,
									A.itm_name,
									ROUND(AVG(A.Qty)) AS Qty
								FROM
									(
										SELECT
											itminfo_tab.itm_id,
											itminfo_tab.itm_name,
											SUM(tbl_wh_data.wh_issue_up) AS Qty
										FROM
											tbl_warehouse
										INNER JOIN tbl_wh_data ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
										INNER JOIN stakeholder AS Office ON Office.stkid = tbl_warehouse.stkofficeid
										INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
										WHERE
											tbl_wh_data.RptDate <= '$reportingDate'
										AND tbl_warehouse.stkid = 5
										AND Office.lvl = 1
										AND itminfo_tab.itm_id = 13
										GROUP BY
											tbl_wh_data.RptDate
										ORDER BY
											tbl_wh_data.RptDate DESC
										LIMIT 3
									) A";
					//query result
					$qryAMCRes = mysql_query($qryAMC);
					//if result exists
					if (mysql_num_rows(mysql_query($qryAMC)) > 0) {
										 //fetch result
						while ($row = mysql_fetch_array($qryAMCRes)){
							$data[$row['itm_id']]['AMC'] = $row['Qty'];
						}
					}
				}
				
				// Get SOH of all products
			$qrySOH = "SELECT
								itminfo_tab.itm_id,
								itminfo_tab.itm_name,
								tbl_wh_data.wh_cbl_a
							FROM
								tbl_wh_data
							INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
							WHERE
								DATE_FORMAT(tbl_wh_data.RptDate, '%Y-%m') = '$reportingDate'
							AND tbl_wh_data.wh_id = $wh_id AND itminfo_tab.itm_category <> 4";
                                //query result
				$qryAMCRes = mysql_query($qryAMC);
				$qrySOHRes = mysql_query($qrySOH);
                                //if result exists
				if (mysql_num_rows(mysql_query($qrySOH)) > 0) {
                                     //fetch result
					while ($row = mysql_fetch_array($qrySOHRes)){
						$data[$row['itm_id']]['SOH'] = $row['wh_cbl_a'];
					}
				}
                ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widget">
                                <div class="widget-body">
                                    <?php include('sub_dist_reports.php'); ?>
                                    <table width="95%" align="center">
                                        <tr>
                                            <td align="center">
                                                <h4 class="center">
                                                    Procurement Planning Monitoring Report<br>
                                                    For the month of <?php echo date('M', mktime(0, 0, 0, $selMonth, 1)) . '-' . $selYear . ',  ' . $sector; ?>
                                                </h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 10px;">
                                                <table width="100%" id="myTable" cellspacing="0" align="center">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="5"></th>
                                                            <th colspan="3" class="center">Detail of Next Shipments(RHI)</th>
                                                            <th colspan="4" class="center">Detail of Next Shipments(cLMIS)</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="center">Sr. No.</th>
                                                            <th>Product</th>
                                                            <th>Stock On Hand</th>
                                                            <th>Avg. Monthly Consumption</th>
                                                            <th>Month of Stock</th>
                                                            <th>Expected Arrival Date</th>
                                                            <th>RO Number</th>
                                                            <th>Quantity</th>
                                                            
                                                            <th width="100px">Expected Arrival Date</th>
                                                            <th width="160px">Procured By</th>
                                                            <th width="100px">Quantity</th>
                                                            <th >Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $e2eCon = mysql_connect($e2eHost, $e2eUser, $e2ePass, true);
                                                    mysql_select_db($e2eDB, $e2eCon);
                                                    $itemsQry = "SELECT
                                                                    Product.ProductID,
                                                                    Product.`Name`
                                                                FROM
                                                                    Product";
                                                    //query result
                                                    $itemsQryRes = mysql_query($itemsQry);
                                                    while ($row = mysql_fetch_array($itemsQryRes)){
                                                        $items[$row['Name']] = $row['ProductID'];
                                                    }
                                                    
                                                    while ($row = mysql_fetch_array($shipmentQryRes))
                                                    {
                                                        $data[$row['ProductID']]['Date'] = $row['wh_cbl_a'];
                                                        $data[$row['ProductID']]['RO'] = $row['wh_cbl_a'];
                                                    }
                                                    $i = 1;
                                                    foreach($data as $productId=>$subArr)
                                                    {
                                                    ?>
                                                        <tr>
                                                            <td class="center"><?php echo $i++;?></td>
                                                            <td><?php echo $subArr['Product'];?></td>
                                                            <td class="right"><?php echo number_format($subArr['SOH']);?></td>
                                                            <td class="right"><?php echo number_format($subArr['AMC']);?></td>
                                                            <td class="right"><?php echo ($subArr['AMC']>0)?(round($subArr['SOH'] / $subArr['AMC'], 2)): 'UNK';?></td>
                                                            <?php
                                                            //select query
                                                            //gets
                                                            //Quantity
                                                            //Expected Receipt Date
                                                            //Donor Purchase Order ID
                                                            //
                                                            $pid = (!empty($items[$subArr['Product']])?$items[$subArr['Product']]:'');
                                                            $shipmentQry = "SELECT
                                                                                SUM(Shipment.Quantity) AS Quantity,
                                                                                DATE_FORMAT(Shipment.ExpectedReceiptDate, '%d/%m/%Y') AS ExpectedReceiptDate,
                                                                                Shipment.DonorPurchaseOrderID
                                                                            FROM
                                                                                Shipment
                                                                            WHERE
                                                                                Shipment.ExpectedReceiptDate > '".$reportingDate."-01'
                                                                            AND Shipment.RecipientID = $recipient
                                                                            AND Shipment.ProductID = " . $pid . "
                                                                            GROUP BY
                                                                                Shipment.DonorPurchaseOrderID
                                                                            ORDER BY
                                                                                Shipment.ExpectedReceiptDate ASC";
                                                            //query result
                                                            $shipmentQryRes = mysql_query($shipmentQry);
                                                            if(@mysql_num_rows($shipmentQryRes) > 0){
                                                                $expDate = $PO = $qty = '';
                                                                 //fetch result
                                                                while ($row = mysql_fetch_array($shipmentQryRes))
                                                                {
                                                                    //exp Date 
                                                                    $expDate .= $row['ExpectedReceiptDate'].'<br>';
                                                                    //PO
                                                                    $PO .= $row['DonorPurchaseOrderID'].'<br>';
                                                                    //qty
                                                                    $qty .= number_format($row['Quantity']).'<br>';
                                                                }
                                                                echo '<td class="center">'.$expDate.'</td>';
                                                                echo '<td>'.$PO.'</td>';
                                                                echo '<td class="right">'.$qty.'</td>';
                                                                
                                                                
                                                            }
                                                            else
                                                            {
                                                                echo '<td colspan="3" class="center">No Upcoming Shipments</td>';
                                                            }
                                                            $prod_total=0;
                                                            if(isset($clmis_shipments[$productId]))
                                                            {
                                                                
                                                                echo '<td colspan="3">';
                                                                echo '<table border="0" class="table table-borderless" style="border-collapse: collapse;">';
                                                                    foreach($clmis_shipments[$productId] as $itm_id=>$v)
                                                                    {
                                                                        echo '<tr style="border: 0px;">';
                                                                        echo '<td style=" border:0px; " width="100px">'.$v['s_dates'].'</td>';
                                                                        echo '<td style=" border: 0px;" width="160px">'.$v['proc_by'].'</td>';
                                                                        echo '<td style=" border: 0px;" width="100px" class="right">'.number_format($v['qty']).'</td>';
                                                                        echo '</tr>';
                                                                        
                                                                        $prod_total += $v['qty'];
                                                                    }
                                                                echo '</table>';
                                                                echo '</td>';
                                                                
                                                                echo '<td class="right">'.number_format($prod_total).'</td>';
                                                                
                                                                
                                                                //echo '<td >'.(implode('</br>',$clmis_shipments[$productId]['s_dates'])).'</td>';
                                                                //echo '<td >'.(implode('</br>',$clmis_shipments[$productId]['proc_by'])).'</td>';
                                                                //echo '<td class="right">'.$clmis_shipments[$productId]['qty'].'</td>';
                                                            }
                                                            else
                                                            {
                                                                echo '<td colspan="3" class="center">No Upcoming Shipments</td>';
                                                            }
                                                            ?>
                                                        </tr>
                                                    <?php
                                                    }
                                                    mysql_close($e2eCon);
                                                    ?>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top:10px;">
                                                <h6>The Procurement Planning and Monitoring Report (PPMR) describes stock status of contraceptive products. Data is reported by trained cLMIS operators of Public and Private sector stakeholder. Shipment data is pulled from RHI (i.e. https://www.myaccessrh.org)</h6><br>
                                                <h6><mark>Note : </mark></h6>
                                                        <h6><mark>- For Implants( Jadelle & Implanon) AMC is calculated based on issuance from <?php echo ($selSector == 1) ? 'GS ' : '';?>Central Warehouse</mark></h6>
                                                        <h6><mark>- No shipments available for private sector.</mark></h6>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php 
//include footer
include PUBLIC_PATH."/html/footer.php";?>
</body>
</html>