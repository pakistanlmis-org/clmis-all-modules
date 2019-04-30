<?php
/**
 * consumption
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including Configuration file
include("../../application/includes/classes/Configuration.inc.php");
//Including db file
include(APP_PATH . "includes/classes/db.php");

//Initializing variables
//reporting_month
$reporting_month = '';
//product_id
$product_id = '';
//recipient_id
$recipient_id = '';
//level
$level = '';

//Checking username
if (isset($_GET['username']) && !empty($_GET['username'])) {
    //Getting username
    $username = mysql_real_escape_string($_GET['username']);
    //Gets
    //auth
    $query = "SELECT
				sysuser_tab.auth
			FROM
				sysuser_tab
			WHERE
				sysuser_tab.usrlogin_id = '$username' ";
    //Query result
    $num = mysql_num_rows(mysql_query($query));
    if ($num != 1) {
        //Display message
        $data = array('Response' => 'Authentication failed.');
    } else {
        //Getting reporting_month
        $reporting_month = mysql_real_escape_string($_GET['reporting_month']);
        //Getting level
        $level = mysql_real_escape_string($_GET['level']);

        $product_filter = '';
        //Checking product_id
        if (!empty($_GET['product_id'])) {
            //Getting product_id
            $product_id = mysql_real_escape_string($_GET['product_id']);
            $product_filter = " AND itminfo_tab.itm_id = $product_id ";
        }
        //Checking recipient
        if (!empty($_GET['recipient'])) {
            //Checking recipient
            $recipient_id = mysql_real_escape_string($_GET['recipient']);
            $recipient_filter = " AND tbl_warehouse.stkid = $recipient_id ";
            $recipient_filter1 = " AND summary_province.stakeholder_id = $recipient_id ";
        }
        if (!empty($reporting_month) && strlen($level) > 0) {
            //Checking level
            if ($level == '0') {
                //Sstart date
                $startDate = date('Y-m-d', strtotime("-3 month", strtotime($reporting_month . '-01')));
                //End date
                $endDate = $reporting_month . '-01';
                //endDateLastDay
                $endDateLastDay = date('Y-m-t', strtotime($endDate));
                //Gets
                //ReportingDate
                //ProductId
                //ProductName
                //CYPFactor
                //OpeningBalance
                //Received
                //Consumption
                //CoupleYearProtection
                //StockOnHand
                //
				$qry = "SELECT
							'$reporting_month' AS ReportingDate,
							itminfo_tab.itm_id AS ProductId,
							itminfo_tab.itm_name AS ProductName,
							itminfo_tab.extra AS CYPFactor,
							#SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') < '$endDate', tbl_stock_detail.Qty, 0)) AS OpeningBalance,
							SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$endDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDateLastDay' AND tbl_stock_master.TranTypeID = 1, tbl_stock_detail.Qty, 0)) AS Received,
							SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$endDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDateLastDay' AND tbl_stock_master.TranTypeID = 2, ABS(tbl_stock_detail.Qty), 0)) AS Consumption,
							ROUND(SUM(IF (DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') >= '$endDate' AND DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDateLastDay' AND tbl_stock_master.TranTypeID = 2, ABS(tbl_stock_detail.Qty), 0)) * itminfo_tab.extra) AS CoupleYearProtection,
							SUM(tbl_stock_detail.Qty) AS StockOnHand,
							(SELECT
									ROUND(SUM(ABS(tbl_stock_detail.Qty)) / 3)
								FROM
									tbl_stock_master
								INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
								INNER JOIN stock_batch ON stock_batch.batch_id = tbl_stock_detail.BatchID
								WHERE
									tbl_stock_master.TranTypeID = 2
								AND tbl_stock_master.TranDate BETWEEN '$startDate'
								AND '$endDate'
								AND tbl_stock_master.WHIDFrom = 123
								AND stock_batch.item_id = itminfo_tab.itm_id
							) AS AverageMonthlyConsumption
						FROM
							itminfo_tab
						INNER JOIN stock_batch ON itminfo_tab.itm_id = stock_batch.item_id
						LEFT JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
						INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
						INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
						INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
						WHERE
						DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') <= '$endDateLastDay' AND
						(tbl_stock_master.WHIDFrom = 123 OR
						tbl_stock_master.WHIDTo = 123)
						$product_filter
						GROUP BY
							itminfo_tab.itm_id
						ORDER BY
							itminfo_tab.frmindex ASC";
            }
            //Checkig level
            if ($level == 1) {
                //Gets
                //ReportingDate
                //ProductId
                //CYPFactor
                //RecipientId
                //RecipientName
                //OpeningBalance
                //Received
                //Consumption
                //AverageMonthlyConsumption
                //StockOnHand
                //CoupleYearProtection
                $qry = "SELECT
							DATE_FORMAT(tbl_wh_data.RptDate, '%Y-%m') AS ReportingDate,
							itminfo_tab.itm_id AS ProductId,
							itminfo_tab.itm_name AS ProductName,
							itminfo_tab.extra AS CYPFactor,
							stakeholder.stkid AS RecipientId,
							stakeholder.stkname AS RecipientName,
							#SUM(tbl_wh_data.wh_obl_a) AS OpeningBalance,
							SUM(tbl_wh_data.wh_received) AS Received,
							SUM(tbl_wh_data.wh_issue_up) AS Consumption,
							summary_national.avg_consumption AS AverageMonthlyConsumption,
							SUM(tbl_wh_data.wh_cbl_a) AS StockOnHand,
							ROUND(SUM(tbl_wh_data.wh_issue_up) * itminfo_tab.extra) AS CoupleYearProtection
						FROM
							tbl_warehouse
						INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
						INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
						INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
						INNER JOIN summary_national ON summary_national.stakeholder_id = tbl_warehouse.stkid
						AND summary_national.item_id = tbl_wh_data.item_id
						AND summary_national.reporting_date = tbl_wh_data.RptDate
						WHERE
							itminfo_tab.itm_category = 1
						AND DATE_FORMAT(tbl_wh_data.RptDate, '%Y-%m') = '$reporting_month'
						$recipient_filter
						$product_filter
						GROUP BY
							tbl_wh_data.item_id,
							tbl_wh_data.RptDate,
							tbl_warehouse.stkid";
            }
            //Checking level
            if ($level == 2) {
                //Gets
                //ReportingDate
                //ProductId
                //ProductName
                //CYPFactor
                //RecipientId
                //ProvinceId
                //ProvinceName
                //OpeningBalance
                //Received
                //Consumption
                //AverageMonthlyConsumption
                //StockOnHand
                //CoupleYearProtection
                //
				$qry = "SELECT
							DATE_FORMAT(A.RptDate, '%Y-%m') AS ReportingDate,
							itminfo_tab.itm_id AS ProductId,
							itminfo_tab.itm_name AS ProductName,
							itminfo_tab.extra AS CYPFactor,
							stakeholder.stkid AS RecipientId,
							stakeholder.stkname AS RecipientName,
							tbl_locations.PkLocID AS ProvinceId,
							tbl_locations.LocName AS ProvinceName,
							#A.OB AS OpeningBalance,
							A.Rcv AS Received,
							A.Issue AS Consumption,
							B.AvgCons AS AverageMonthlyConsumption,
							A.CB AS StockOnHand,
							ROUND(A.Issue * itminfo_tab.extra) AS CoupleYearProtection
						FROM
							(
								SELECT
									tbl_warehouse.prov_id,
									tbl_warehouse.stkid,
									tbl_wh_data.RptDate,
									SUM(tbl_wh_data.wh_obl_a) AS OB,
									SUM(tbl_wh_data.wh_received) AS Rcv,
									SUM(tbl_wh_data.wh_issue_up) AS Issue,
									SUM(tbl_wh_data.wh_cbl_a) AS CB,
									itminfo_tab.itm_id
								FROM
									tbl_wh_data
								INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
								INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
								WHERE
									DATE_FORMAT(tbl_wh_data.RptDate, '%Y-%m') = '$reporting_month'
								$recipient_filter
								$product_filter
								GROUP BY
									tbl_wh_data.item_id,
									tbl_warehouse.prov_id,
									tbl_warehouse.stkid,
									tbl_wh_data.RptDate
							) A
						INNER JOIN (
							SELECT
								summary_province.stakeholder_id,
								summary_province.province_id,
								summary_province.avg_consumption,
								summary_province.reporting_date,
								ROUND(SUM(summary_province.avg_consumption)) AS AvgCons,
								itminfo_tab.itm_id
							FROM
								summary_province
							INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
							WHERE
								summary_province.province_id != 10
							AND DATE_FORMAT(summary_province.reporting_date, '%Y-%m') = '$reporting_month'
							$recipient_filter1
							$product_filter
							GROUP BY
								summary_province.item_id,
								summary_province.stakeholder_id,
								summary_province.province_id,
								summary_province.reporting_date
						) B ON A.itm_id = B.itm_id
						AND A.prov_id = B.province_id
						AND A.stkid = B.stakeholder_id
						JOIN itminfo_tab ON A.itm_id = itminfo_tab.itm_id
						JOIN tbl_locations ON A.prov_id = tbl_locations.PkLocID
						JOIN stakeholder ON A.stkid = stakeholder.stkid
						ORDER BY
							tbl_locations.PkLocID";
            }
            //Query result
            $rows = mysql_query($qry);
            //Checking if there is any record
            if (mysql_num_rows(mysql_query($qry)) > 0) {
                //Getting result
                while ($row = mysql_fetch_assoc($rows)) {
                    $data['Response'][] = $row;
                }
            } else {
                //Set message
                $data = array('Response' => 'No data found');
            }
        } else {
            //Set message
            $data = array('Response' => 'No data found.');
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>cLMIS Web Service</title>
        <style>
            * {
                font-family: "HelveticaNeue", "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size: 12px;
            }
            input[type="submit"], input[type="button"] {
                font-weight: bold;
                background: #eee; /* Old browsers */
                background: -moz-linear-gradient(top, rgba(255,255,255,.2) 0%, rgba(0,0,0,.2) 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(255,255,255,.2)), color-stop(100%, rgba(0,0,0,.2))); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(top, rgba(255,255,255,.2) 0%, rgba(0,0,0,.2) 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(top, rgba(255,255,255,.2) 0%, rgba(0,0,0,.2) 100%); /* Opera11.10+ */
                background: -ms-linear-gradient(top, rgba(255,255,255,.2) 0%, rgba(0,0,0,.2) 100%); /* IE10+ */
                background: linear-gradient(top, rgba(255,255,255,.2) 0%, rgba(0,0,0,.2) 100%); /* W3C */
                border: 1px solid #aaa;
                border-top: 1px solid #ccc;
                border-left: 1px solid #ccc;
                padding: 4px 12px;
                -moz-border-radius: 3px;
                -webkit-border-radius: 3px;
                border-radius: 3px;
                color: #444;
                display: inline-block;
                text-decoration: none;
                text-shadow: 0 1px rgba(255, 255, 255, .75);
                cursor: pointer;
                line-height: 21px;
            }
            input[type="submit"]:hover, input[type="button"]:hover {
                color: #222;
                background: #ddd; /* Old browsers */
                background: -moz-linear-gradient(top, rgba(255,255,255,.3) 0%, rgba(0,0,0,.3) 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, rgba(255,255,255,.3)), color-stop(100%, rgba(0,0,0,.3))); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(top, rgba(255,255,255,.3) 0%, rgba(0,0,0,.3) 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(top, rgba(255,255,255,.3) 0%, rgba(0,0,0,.3) 100%); /* Opera11.10+ */
                background: -ms-linear-gradient(top, rgba(255,255,255,.3) 0%, rgba(0,0,0,.3) 100%); /* IE10+ */
                background: linear-gradient(top, rgba(255,255,255,.3) 0%, rgba(0,0,0,.3) 100%); /* W3C */
                border: 1px solid #888;
                border-top: 1px solid #aaa;
                border-left: 1px solid #aaa;
            }
            input[type="text"], input[type="password"] {
                width: 220px;
                border: 1px solid #D1D1D1;
                padding: 10px;
                border-radius: 3px 3px 3px 3px;
                color: #000;
                background-color: #F8F8F8;
            }
            input[type="text"]:hover, input[type="password"]:hover {
                background-color: #F8F8F8;
                border: 1px solid #C6C6C6;
                border-radius: 3px 3px 3px 3px;
                color: #000;
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            }
            input[type="text"]:focus, input[type="password"]:focus {
                background-color: #FFF;
                color: #333333;
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            }
            select {
                background-color: #F8F8F8;
                color: #000 !important;
                border: 1px solid #D1D1D1;
                padding: 10px;
                width: 244px;
                border-radius: 3px 3px 3px 3px;
            }
            select:hover {
                background-color: #F8F8F8;
                border: 1px solid #C6C6C6 !important;
                border-radius: 3px 3px 3px 3px;
                color: #000;
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            }
            select:focus {
                background-color: #FFF;
                color: #333333;
                -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            }
            div.label {
                font-size: 14px;
                font-weight: normal;
                margin-bottom: 10px;
                color: #5f5f5f;
            }
        </style>
        <script src="../assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
        <script>
            $(function() {
                $('#level').change(function(e) {
                    if ($(this).val() == 0)
                    {
                        $('#recipient_row').hide();
                    }
                    else
                    {
                        $('#recipient_row').show();
                    }
                });
            })
        </script>
    </head>

    <body>
        <div style="width:30%; float:left;">
            <form name="frm" id="frm" action="" method="get">
                <table width="100%" cellpadding="10" cellspacing="5">
                    <tr>
                        <td>
                            <div class="label">Reporting Month</div>
                            <input type="text" name="reporting_month" id="reporting_month" required="required" placeholder="YYYY-MM" value="<?php echo $reporting_month; ?>" autocomplete="off" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="label">Product</div>
                            <select name="product_id">
                                <option value="">All</option>
                                <?php
                                //Gets
                                //itm_id
                                //itm_name
                                $qry = "SELECT DISTINCT
									itminfo_tab.itm_id,
									itminfo_tab.itm_name
								FROM
									itminfo_tab
								INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
								WHERE
									itminfo_tab.itm_category = 1
								ORDER BY
									itminfo_tab.frmindex ASC";
                                //Query result
                                $qryRes = mysql_query($qry);
                                //Populate product_id combo
                                while ($row = mysql_fetch_array($qryRes)) {
                                    $selected = ($product_id == $row['itm_id']) ? 'selected="selected"' : '';
                                    echo "<option value=\"" . $row['itm_id'] . "\" $selected>" . $row['itm_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="label">Consumption Level</div>
                            <select name="level" id="level" required>
                                <option value="">Select</option>
                                <option value="0" <?php echo ($level == '0') ? 'selected="selected"' : ''; ?>>Central Warehouse</option>
                                <option value="1" <?php echo ($level == 1) ? 'selected="selected"' : ''; ?>>National</option>
                                <option value="2" <?php echo ($level == 2) ? 'selected="selected"' : ''; ?>>Provincial</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="recipient_row" <?php echo ($level == '0') ? 'style="display:none;"' : ''; ?>>
                        <td>
                            <div class="label">Recipient</div>
                            <select name="recipient" id="recipient">
                                <option value="">All</option>
                                <?php
                                //Gets
                                //stkid
                                //stk_type_id
                                //stk_type_descr
                                $qry = "SELECT
									stakeholder.stkid,
									stakeholder.stkname,
									stakeholder.stk_type_id,
									stakeholder_type.stk_type_descr
								FROM
									stakeholder
								INNER JOIN stakeholder_type ON stakeholder.stk_type_id = stakeholder_type.stk_type_id
								WHERE
									stakeholder.ParentID IS NULL
								AND stakeholder.stk_type_id IN (0, 1)
								ORDER BY
									stakeholder.stk_type_id ASC,
									stakeholder.stkorder ASC";
                                //Query result
                                $qryRes = mysql_query($qry);
                                $optGroup = '';
                                //Getting result
                                while ($row = mysql_fetch_array($qryRes)) {
                                    if ($optGroup == '') {
                                        echo "<optgroup label=\"" . $row['stk_type_descr'] . "\">";
                                    }
                                    if ($optGroup != $row['stk_type_id'] && $optGroup != '') {
                                        echo "</optgroup>";
                                        echo "<optgroup label=\"" . $row['stk_type_descr'] . "\">";
                                    }

                                    $selected = ($recipient_id == $row['stkid']) ? 'selected="selected"' : '';
                                    //Populate recipient combo
                                    echo "<option value=\"" . $row['stkid'] . "\" $selected>" . $row['stkname'] . "</option>";
                                    $optGroup = $row['stk_type_id'];
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="label">Username</div>
                            <input type="text" name="username" required="required" autocomplete="off" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" value="Submit" />
                            <input type="button" value="Rest" onclick="window.location = 'consumption.php'" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div style="width:70%; float:right; height:600px; min-height:100%; overflow:auto;">
            <pre>
                <?php
                if (isset($_GET['username']) && !empty($_GET['username'])) {
                    //Encode in json
                    echo json_encode($data, JSON_PRETTY_PRINT);
                }
                ?>
            </pre>
        </div>
    </body>
</html>