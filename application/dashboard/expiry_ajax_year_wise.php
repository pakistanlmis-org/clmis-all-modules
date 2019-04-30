<?php
/**
 * expiry_ajax_year_wise
 * @package dashboard
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include Configuration
include("../includes/classes/Configuration.inc.php");
//include db
include(APP_PATH . "includes/classes/db.php");
//include fusion chart
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");

//get wh id
$whId = $_SESSION['user_warehouse'];
//get stk id
$stkId = $_SESSION['user_stakeholder'];
//get item id
$itemId = isset($_REQUEST['itemId']) ? $_REQUEST['itemId'] : $_REQUEST['product'];

$and = '';

$and .= " AND (tbl_stock_master.WHIDFrom = $whId OR tbl_stock_master.WHIDTo = $whId )";


if (isset($_REQUEST['product'])) {
    //data for pie graph
    $and3=$and4=" ";
    if ($_REQUEST['fund_source'] != 'all') {
        $and3 = " tbl_stock_master.WHIDFrom         = " . $_REQUEST['fund_source'] . "      AND ";
        $and4 = " AND stock_batch.funding_source    = " . $_REQUEST['fund_source'] . " ";
    }
    //query 

    if($_REQUEST['product']!='all')
    {
        $and .="    AND stock_batch.item_id = $itemId 
                     $and4 ";
        /*$and .="    AND stock_batch.item_id = $itemId 
                    AND stock_batch.batch_id IN (
			SELECT DISTINCT
				stock_batch.batch_id
			FROM
				tbl_stock_master
			INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
			INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
			WHERE
				$and3 tbl_stock_master.WHIDTo = $whId
			AND stock_batch.item_id = $itemId
		)";*/
    }
    else
    {
        $and .="      $and4  ";
        /*$and .="   
                    AND stock_batch.batch_id IN (
			SELECT DISTINCT
				stock_batch.batch_id
			FROM
				tbl_stock_master
			INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
			INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
			WHERE
                            $and3
			tbl_stock_master.WHIDTo = $whId 
		)";*/
    }
    
    $qry = "SELECT
                            stock_batch.item_id,
                            itminfo_tab.itm_name,
                            SUM(tbl_stock_detail.Qty) AS qty,
                            YEAR(stock_batch.batch_expiry) as yr
			FROM
				stock_batch
			INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
			INNER JOIN tbl_stock_master ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
			INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
			WHERE
                            stock_batch.item_id IS NOT NULL
                            AND stock_batch.Qty > 0
                            AND tbl_stock_detail.temp = 0
                            AND stock_batch.wh_id = $whId
                            $and
			GROUP BY 
                            YEAR(stock_batch.batch_expiry)
                        ORDER BY
                            YEAR(stock_batch.batch_expiry)"
            . "";
    //query result
    //echo $qry;exit;
    
    //xml
    $xmlstore1=$itm_name='';
    $res=mysql_query($qry);
    while($row = mysql_fetch_assoc($res))
    {
        //Expire 18 Greater
        if($_REQUEST['product']!='all'){
            $itm_name= $row['itm_name'];
        }
        else {
            $itm_name= 'All Products';
            $row['item_id'] = 'all';
        }
        $xmlstore1 .= "<set label='Year ".$row['yr']."' value='".$row['qty']."' link=\"JavaScript:showData('$row[item_id], ".$row['yr']."');\" />";
        
        
    }
    $xmlstore = "<chart theme='fint' numberSuffix='' exportEnabled='1' exportAction='Download' caption='Expiry Schedule $itm_name' exportFileName='Expiry Schedule $itm_name" . date('Y-m-d H:i:s') . "'>";
    $xmlstore .= $xmlstore1;
    $xmlstore .= "</chart>";
    //include chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_PATH . "FusionCharts/Charts/Pie3D.swf", "", $xmlstore, 'Expiry' . $row['item_id'], '100%', 350, false, false);
    echo '<p class="center" style="color:#060">Note:- Click on the graph to see batch wise expiry details</p>';
}

if (isset($_REQUEST['itemId']) && isset($_REQUEST['year'])) {
    //data for expiring stock table
    $and3=$and4=" ";
    if ($_REQUEST['fund_source'] != 'all') {
        $and3 = " tbl_stock_master.WHIDFrom = " . $_REQUEST['fund_source'] . " AND ";
        $and4 = "  AND stock_batch.funding_source    = " . $_REQUEST['fund_source'] . " ";
    }
    
    if($_REQUEST['itemId'] != 'all')
    {
        $and .= " 
                AND stock_batch.item_id = $itemId
                 $and4 ";
        
        /*$and .= " 
        AND stock_batch.item_id = $itemId
        AND stock_batch.batch_id IN (
            SELECT DISTINCT
                    stock_batch.batch_id
            FROM
                    tbl_stock_master
            INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
            INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
            WHERE
                   $and3 tbl_stock_master.WHIDTo = $whId
            AND stock_batch.item_id = $itemId
            )";*/
    }
    else
    {
        
        $and .= "    $and4 ";
        /*$and .= " AND stock_batch.batch_id IN (
                SELECT DISTINCT
                        stock_batch.batch_id
                FROM
                        tbl_stock_master
                INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                WHERE
                        $and3  tbl_stock_master.WHIDTo = $whId

                )";*/
        
    }
    
    
    //type
    $year = $_REQUEST['year'];
    //item name
    $itemId = $_REQUEST['itemId'];
    //query
    //gets
    //item name
    
    if($_REQUEST['itemId'] != 'all')
    {
        $getItmName = mysql_fetch_array(mysql_query("SELECT getItemNameByID(" . $_REQUEST['itemId'] . ") AS item_name FROM DUAL "));
        $itmName = $getItmName['item_name'];
    }
    else
    {
        $itmName = 'All Products';
    }
    $where = '';
    
    //title	
    $title = " Stock Expiring in Year ".$_REQUEST['year'];
    $and .= ' AND YEAR(stock_batch.batch_expiry) = '.$_REQUEST['year'].' ';

    $qry = "SELECT
                    itminfo_tab.itm_name,
                    itminfo_tab.qty_carton,
                    stock_batch.batch_no,
                    DATE_FORMAT(stock_batch.batch_expiry, '%m/%d/%Y') AS batch_expiry,
                    stock_batch.`status`,
                    SUM(tbl_stock_detail.Qty) AS Qty,
                    SUM(tbl_stock_detail.Qty) / itminfo_tab.qty_carton AS carton,
                    tbl_warehouse.wh_name
            FROM
                    tbl_stock_master
            INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
            INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
            INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
            INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
            WHERE
                    stock_batch.item_id IS NOT NULL
            AND stock_batch.Qty > 0
            AND tbl_stock_detail.temp = 0
            
            $and
            GROUP BY
                    stock_batch.batch_no
            ORDER BY
                    itminfo_tab.itm_id,
                    stock_batch.batch_expiry ASC,
                    stock_batch.batch_no ASC";
    //query result
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    ?>
    <script>
        $(function()
        {
            if ($('.dynamicTable2').size() > 0)
            {
                var datatable = $('.dynamicTable2').dataTable({
                    "sPaginationType": "bootstrap",
                    //"sDom": 'W<"clear">lfrtip',
                    // "sDom": 'T<"clear">lfrtip',
                    "sDom": "<'row'<'col-md-11'>T<'clear'>><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-5'i><'col-md-7'p>>",
                    // "sDom": '<"clear">lfrtipT',
                    "oLanguage": {
                        "sLengthMenu": "_MENU_ records per page"
                    },
                    "oTableTools": {
                        "aButtons": [
                            {
                                "sExtends": "xls",
                                "sButtonText": "<img src=../../public/images/excel-16.png>",
                                "sTitle": "Expiry Schedule"
                            },
                            {
                                "sExtends": "pdf",
                                "sButtonText": "<img src=../../public/images/pdf-16.png>",
                                "sTitle": "Expiry Schedule",
                                "sPdfOrientation": "landscape"
                            }

                        ],
                        "sSwfPath": basePath + "/common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
                    }

                });

            }
        });
    </script>
    <div class="widget widget-tabs">
        <div class="widget">
            <div class="widget-head">
                <h3 class="heading"><?php echo $itmName . ' - ' . $title; ?></h3>
            </div>
            <div class="widget-body" id="expiryData">
                <table width="100%" class="dynamicTable2 table table-striped table-bordered table-condensed dataTable">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Product</th>
                            <th>Batch No</th>
                            <th>Funding Source</th>
                            <th>Expiry</th>
                            <th>Quantity</th>
                            <th>Cartons</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    $i = 1;
    //fetch result
    while ($row = mysql_fetch_array($qryRes)) {
        ?>
                            <tr>
                                <td align="center"><?php echo $i++; ?></td>
                                <td><?php echo $row['itm_name']; ?></td>
                                <td><?php echo $row['batch_no']; ?></td>
                                <td><?php echo $row['wh_name']; ?></td>
                                <td><?php echo $row['batch_expiry']; ?></td>
                                <td class="right"><?php echo number_format($row['Qty']); ?></td>
                                <td class="right"><?php echo number_format($row['carton']); ?></td>
                                <td><?php echo $row['status']; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}