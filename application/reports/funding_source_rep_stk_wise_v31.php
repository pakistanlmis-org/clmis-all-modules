<?php
include("../includes/classes/AllClasses.php");
//include FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");

$open_month =  date('m')-1;
$open_year =  date('Y');
$open_month_d =  date('d');

$province = '';
$date = date("Y-m-d");
if (!empty($_REQUEST['province']))
    $province = $_REQUEST['province'];
if (!empty($_REQUEST['to_date']))
    $date = $_REQUEST['to_date'];

$sector = 'all';

if (!empty($_REQUEST['sector']))
    $sector = $_REQUEST['sector'];



?>
<style>
    .objbox {
        overflow-x: hidden !important;
    }
</style>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">

                <div class="widget" data-toggle="collapse-widget">
                    <div class="widget-head">
                        <h3 class="heading">Filter by</h3>
                    </div>
                    <div class="widget-body">
                        <div class="row">
                            <div class="col-md-12">


                                <form id="aform" action="">
                                    <div class="col-md-2 hide">
                                        <div class="form-group">
                                            <label class="control-label">Province</label>
                                            <div class="form-group">
                                                <select name="province" id="province" class="form-control input-sm">
                                                    <option value="">Select</option>
                                                    <?php
                                                    $where = '';

                                                    $querys = "SELECT
                                                                tbl_locations.LocName,
                                                                tbl_locations.PkLocID,
                                                                tbl_locations.LocLvl,
                                                                tbl_locations.ParentID,
                                                                tbl_locations.LocType
                                                            FROM
                                                                tbl_locations
                                                            WHERE
                                                                tbl_locations.LocLvl = 2
                                                            ORDER BY 
                                                                tbl_locations.PkLocID
                                                            ";
                                                    //query result
                                                    $rsprov = mysql_query($querys) or die();
                                                    $stk_name = '';
                                                    while ($rowp = mysql_fetch_array($rsprov)) {
                                                        if ($province == $rowp['PkLocID']) {
                                                            $sel = "selected='selected'";
                                                            $pro_name = $rowp['LocName'];
                                                        } else {
                                                            $sel = "";
                                                        }
                                                        //Populate prov_sel combo
                                                        ?>
                                                        <option value="<?php echo $rowp['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowp['LocName']; ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Last Date</label>
                                            <div class="form-group">
                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php echo $date; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 hide">
                                        <div class="form-group">
                                            <label class="control-label">Sector</label>
                                            <div class="form-group">
                                               <select name="sector" id="sector" class="form-control input-sm">
                                                    <option value="all" <?=(($sector == 'all')?' selected ':'')?>>All</option>
                                                    <option value="public"   <?=(($sector == 'public')?' selected ':'')?>>Public Only</option>
                                               </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">&nbsp;</label>
                                            <div class="form-group">
                                                <button type="submit" name="submit" class="btn btn-primary input-sm">Go</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <?php
                $and = '';

                if (!empty($date))
                    $and .= " AND DATE_FORMAT(national_stock.tr_date,'%Y-%m-%d') <= '$date'  ";
                
                if (!empty($province))
                    $and .= " AND national_stock.prov_id = $province  ";


                $qry = "SELECT
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
                        Sum(national_stock.quantity) as opening,
                        national_stock.prov_id,
                        national_stock.item_id
                        FROM
                        national_stock
                        INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                        INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                        INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                        WHERE
                        
                        national_stock.ref = 'ob'
                        $and
                        GROUP BY
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name
                        ORDER BY
                        national_stock.prov_id,
                        national_stock.stk_id,
                        national_stock.item_id
                ";
                //query result
                $qryRes = mysql_query($qry);
                $opening_bal = $issue_arr = $stk_arr = $closing_bal = $closing_bal_all = $prov_arr = $prod_arr = array();
                while ($row = mysql_fetch_assoc($qryRes)) {
                    //print_r($row);
                    $opening_bal[$row['LocName']][$stk_name][$row['itm_name']] = $row['opening'];
                    $closing_bal[$row['LocName']][$row['itm_name']] = $row['opening'];
                    
                    $prov_arr[$row['prov_id']] = $row['LocName'];
                    $products[$row['itm_name']] = $row['item_id'];
                    
                    $prod_arr[$row['LocName']][$row['item_id']] = $row['itm_name'];
                }

                $and_sector = '';
                if($sector=='public') $and_sector = " AND stakeholder.stk_type_id = 0 ";

                $qry = "(
                            SELECT
                                tbl_locations.LocName,
                                stakeholder.stkname,
                                itminfo_tab.itm_name,
                                Sum(national_stock.quantity) as qty,
                                national_stock.prov_id,
                                national_stock.item_id
                            FROM
                                national_stock
                                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                            WHERE
                                national_stock.ref = 'issue'
                                AND stakeholder.stk_type_id = 0
                                $and
                            GROUP BY
                                tbl_locations.LocName,
                                stakeholder.stkname,
                                itminfo_tab.itm_name
                            ORDER BY
                                national_stock.prov_id,
                                national_stock.stk_id,
                                national_stock.item_id
                    )";
                
                if($sector=='all') 
                {
                    $qry .= " UNION
                    (
                            SELECT
                                tbl_locations.LocName,
                                'Private Stk / NGOs' as stkname,
                                itminfo_tab.itm_name,
                                Sum(national_stock.quantity) as qty,
                                national_stock.prov_id,
                                national_stock.item_id
                            FROM
                                national_stock
                                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                            WHERE
                                national_stock.ref = 'issue'
                                $and
                                AND stakeholder.stk_type_id <> 0
                            GROUP BY
                                    tbl_locations.LocName,
                                    itminfo_tab.itm_name
                            ORDER BY
                                    national_stock.prov_id,
                                    national_stock.item_id
                    )";
                }
                   $qry .= "  UNION
                    (
                            SELECT
                                tbl_locations.LocName,
                                'Issuance' as stkname,
                                itminfo_tab.itm_name,
                                Sum(national_stock.quantity) as qty,
                                national_stock.prov_id,
                                national_stock.item_id
                            FROM
                                national_stock
                                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                            WHERE
                                national_stock.ref in ('ob','issue')
                                $and_sector
                                $and
                                
                            GROUP BY
                                    tbl_locations.LocName,
                                    itminfo_tab.itm_name
                            ORDER BY
                                    national_stock.prov_id,
                                    national_stock.item_id
                    )
                    UNION
                    (
                            SELECT
                                tbl_locations.LocName,
                                'Loans/Relocations' as stkname,
                                itminfo_tab.itm_name,
                                Sum(national_stock.quantity) as qty,
                                national_stock.prov_id,
                                national_stock.item_id
                            FROM
                                national_stock
                                INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                                INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                                INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                            WHERE
                                national_stock.ref in ('loan','relocate')
                                $and
                                
                            GROUP BY
                                    tbl_locations.LocName,
                                    itminfo_tab.itm_name
                            ORDER BY
                                    national_stock.prov_id,
                                    national_stock.item_id
                    )
                ";
                //query result
                //echo $qry;exit;
                $qryRes = mysql_query($qry);
                $all_products = $all_totals = array();
                while ($row = mysql_fetch_assoc($qryRes)) {
                    //print_r($row);
                    $issue_arr[$row['LocName']][$row['stkname']][$row['itm_name']] = $row['qty'];
                    if (empty($closing_bal[$row['LocName']][$row['itm_name']]))
                        $closing_bal[$row['LocName']][$row['itm_name']] = 0;

                    if ($row['stkname'] != 'Issuance')
                        $closing_bal[$row['LocName']][$row['itm_name']] += $row['qty'];
                    $stk_arr[$row['stkname']] = $row['stkname'];

                    $prov_arr[$row['prov_id']] = $row['LocName'];
                    $prod_arr[$row['LocName']][$row['item_id']] = $row['itm_name'];
                    
                    
                    $all_products[$row['item_id']] = $row['itm_name'];
                }

                
                
$qry = "SELECT
                tbl_warehouse.stkid,
                (CASE WHEN stk.stk_type_id <> 0 THEN 'Private Stk / NGOs'	ELSE	stk.stkname END	) AS stkname,
                    tbl_locations.PkLocID,
                    itminfo_tab.itm_id,
                    DATE_FORMAT(
                        tbl_stock_master.TranDate,
                        '%Y-%m-%d'
                    ) TranDate,
                    (tbl_stock_detail.Qty) Qty,
                        itminfo_tab.itm_name,
                        tbl_locations.LocName
                FROM
                    tbl_stock_master
                INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
                INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                LEFT JOIN tbl_warehouse AS fundingSource ON stock_batch.funding_source = fundingSource.wh_id
                INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                LEFT JOIN stakeholder AS stk_ofc ON tbl_warehouse.stkofficeid = stk_ofc.stkid
                LEFT JOIN stakeholder AS stk ON tbl_warehouse.stkid = stk.stkid
                LEFT JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
                WHERE
                    DATE_FORMAT(
                        tbl_stock_master.TranDate,
                        '%Y-%m-%d'
                    ) BETWEEN '2016-10-01' AND  '$date'
                AND  stock_batch.funding_source = 6891
                AND tbl_stock_master.TranTypeID = 2
                AND stock_batch.wh_id = 123
                AND tbl_stock_detail.temp = 0
                
                ORDER BY
                    TranDate";
//echo $qry;exit;
$result = mysql_query($qry);
$issuance_data = $issuance_data_ruled = array();
while ($row = mysql_fetch_assoc($result)) {
//    if($row['itm_name'] != 'Jadelle') continue;
//    if($row['LocName'] != 'Sindh') continue;
//    if($row['stkname'] != 'Private Stk / NGOs') continue;
//    echo $row['Qty'].':';

    if(empty($issuance_data_ruled['National'][$row['stkname']][$row['itm_name']])) $issuance_data_ruled['National'][$row['stkname']][$row['itm_name']] = 0;
    @$issuance_data_ruled[$row['LocName']][$row['stkname']][$row['itm_name']]+=abs($row['Qty']);
    
    @$issuance_data[$row['LocName']][$row['stkname']][$row['itm_name']] += abs($row['Qty']);
}
//echo '<pre>';print_r($rules);print_r($issuance_data_ruled);exit;
                 
                 
                ?>
                <div class="row">
                    <div id="div1" class="col-md-12">



                        <table id="USAID_Supported_Stock" border="1" width="100%" cellpadding="0" cellspacing="0" style="" class="table table-bordered table-condensed">
                            <div class="col-md-12">
                                <div class="col-md-11 h4 center">USAID Supported Commodities After Quota Allocation ( 01-Oct-2016 )</div>
                                <div class=" col-md-1 right">
                                    <a id="btnExport" onclick="javascript:xport.toCSV('USAID_Supported_Stock');"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></a>
                                </div>
                            </div>    <?php
                            $cols = count($stk_arr);
                            $cols += 3;
                            echo '  <tr>
                                            <th align="center" colspan="">Product</th>
                                            <th align="center" colspan="">Opening Balance </th>
                                            <th style="text-align:center" colspan="'.(($sector=='all')?'8':'7').'">Issuance </th>
                                            <th align="center" colspan=""></th>
                                            <th align="center" colspan=""></th>
                                            <th align="center" colspan=""></th>
                                            <th align="center" colspan=""></th>
                                        ';
                            echo '</tr>';
                            echo '  <tr>
                                            <th align="center" colspan="">Product</th>
                                            <th align="center" colspan="">Opening Balance <br/>(As on 01-Oct-2016)</th>
                                        ';
                            foreach ($stk_arr as $k => $stk) {
                                if ($stk == 'Issuance')
                                    $stk = 'Opening - Issuance';
                                echo '<th align="center">' . $stk . '</th>';
                            }
                            echo '<th align="center" colspan="">Closing Balance</th>';
                            echo '</tr>';
                            foreach ($prov_arr as $province_id => $prov) {
                                if (!empty($opening_bal[$prov])) {

                                    $prov_data = $opening_bal[$prov];
                                } else {
                                    $prov_data = array();
                                    // echo '<pre>'.$prov.'::';print_r($prod_arr[$prov]);
                                    foreach ($prod_arr[$prov] as $prv => $prd) {
                                        $prov_data[$stk_name][$prd] = 0;
                                    }
                                }
//echo '<pre>';
//echo 'open:';print_r($prov_data);
//echo 'open:';print_r($opening_bal);
//echo 'issue:';print_r($issue_arr);
//echo 'closing:';print_r($closing_bal);
                                echo '  <tr>
                                                <td align="left" colspan="' . $cols . '" style="background-color:#93d893">' . $prov . '</td>
                                            </tr>';

                                foreach ($prov_data as $stk => $prod_data) {
                                    echo '  <tr>
                                                    <td align="left" colspan="' . $cols . '" style="background-color:#c2edc2">' . $stk . '</td>
                                                </tr>';
                                    foreach ($prod_data as $prod => $qty) {
                                        $pr_issuance = 0;
                                        $pr_cb = 0;
                                        echo '  <tr>
                                                        <td align="center" colspan="">' . $prod . '</td>
                                                        <td align="right" colspan="">' . number_format($qty) . '</td>
                                                    ';

                                        foreach ($stk_arr as $k => $stk) {

                                            
                                            echo '<td  align="right" >';
                                            if ($stk == 'Issuance') {
                                                //echo (!empty($issue_arr[$prov][$stk][$prod]) ? number_format(($issue_arr[$prov][$stk][$prod])) : '0');
                                                //echo ' + ';
                                                echo number_format($qty - $pr_issuance);
                                                //echo ' = '.($qty);
                                                //echo ' - '.($pr_issuance);
                                                echo '</td>';
                                                
                                                $pr_cb = (($qty - $pr_issuance));
                                            } 
                                            elseif ( $stk =='Loans/Relocations') {
                                                echo '<a style="cursor: pointer" onclick="window.open(\'http:loan_breakdown.php?prov='.$province_id.'&prod='.$products[$prod].'&date='.$date.'\', \'_blank\', \'scrollbars=1,width=600,height=500\');">';
                                                echo (!empty($issue_arr[$prov][$stk][$prod]) ? number_format(($issue_arr[$prov][$stk][$prod])) : '0') . '</a></td>';
                                                $pr_cb+=(!empty($issue_arr[$prov][$stk][$prod]) ?(($issue_arr[$prov][$stk][$prod])) : '0');
                                            } 
                                            elseif ( $stk =='Private Stk / NGOs') {
                                                echo '<a style="cursor: pointer" onclick="window.open(\'http:ngo_breakdown.php?prov='.$province_id.'&prod='.$products[$prod].'&date='.$date.'\', \'_blank\', \'scrollbars=1,width=800,height=500\');">';
                                                $new_r_qty = (!empty($issuance_data_ruled[$prov][$stk][$prod]) ? (($issuance_data_ruled[$prov][$stk][$prod])) : '0');
                                                echo number_format($new_r_qty);
                                                //echo $prov.','.$stk.','.$prod;
                                                echo  '</a></td>';
                                                $pr_issuance += $new_r_qty;
                                            } 
                                            else
                                            {
                                                $new_r_qty = (!empty($issuance_data_ruled[$prov][$stk][$prod]) ? (($issuance_data_ruled[$prov][$stk][$prod])) : '0');
                                                //echo (!empty($issue_arr[$prov][$stk][$prod]) ? number_format(abs($issue_arr[$prov][$stk][$prod])) : '0');
                                                //echo ' + ';
                                                //echo (!empty($issuance_data[$prov][$stk][$prod]) ? number_format(($issuance_data[$prov][$stk][$prod])) : '0');
                                                //echo ' + ';
                                                echo number_format($new_r_qty);
                                                //echo $prov.','.$stk.','.$prod;
                                                echo  '</td>';
                                                $pr_issuance += $new_r_qty;
                                                

                                            }
                                        }
                                        
                                        $cb = 0;
                                        if(!empty($closing_bal[$prov][$prod])) $cb = $closing_bal[$prov][$prod];
                                        echo '<td align="right" colspan="">';
                                        //echo  number_format($cb); 
                                        //echo ' + ';
                                        echo number_format($pr_cb);
                                        echo '</td>';
                                        $closing_bal_all[$prov][$prod] = $pr_cb;
                                        echo '</tr>';
                                    }
                                }
                            }
                            ?>



                        </table>
                        
                        <hr>
                        <h3>Province Wise Totals</h3>
                        <table  border="1" width="100%" cellpadding="0" cellspacing="0" style="" class="table table-bordered table-condensed">
                            <tr>
                                <td> Product/ Province</td>
                                <?php
                                foreach ($prov_arr as $prov_id => $province_name) {
                                    echo '<td>'.$province_name.'</td>';
                                }
                                ?>
                                <td>Total</td>
                            </tr>
                            <?php
                            ksort($all_products);
                            foreach ($all_products as $prod_id => $product) {
                                $this_total = 0;
                                echo '<tr>';
                                echo '<td>'.$product.'</td>';
                                foreach ($prov_arr as $prov_id => $province_name) {
                                    $this_val = (!empty($closing_bal_all[$province_name][$product]) ? $closing_bal_all[$province_name][$product] : '0');
                                    $this_total += $this_val ;
                                    echo '<td align="right">'.number_format($this_val).'</td>';
                                }
                                echo '<td align="right"><b>'.number_format($this_total).'</b></td>';
                                echo '</tr>';
                            }
                            ?>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
//include report_includes
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
        $(function () {
            $('#to_date').datepicker({
                dateFormat: "yy-mm-dd",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,

<?php
    echo 'minDate: new Date( 2016, 9, 1 ),
    maxDate: new Date( ' . $open_year . ', ' . $open_month . ' , ' . $open_month_d . ')';

if (!empty($date)) {
    $d1 = explode('-', $date);
    echo ' 
                                 ,setDate: new Date(' . $d1[0] . ', ' . $d1[1] . ',' . $d1[2] . ') ';
}
?>

            });
        });
        var xport = {
            _fallbacktoCSV: true,
            toXLS: function (tableId, filename) {
                this._filename = (typeof filename == 'undefined') ? tableId : filename;

                //var ieVersion = this._getMsieVersion();
                //Fallback to CSV for IE & Edge
                if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
                    return this.toCSV(tableId);
                } else if (this._getMsieVersion() || this._isFirefox()) {
                    alert("Not supported browser");
                }

                //Other Browser can download xls
                var htmltable = document.getElementById(tableId);
                var html = htmltable.outerHTML;

                this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls');
            },
            toCSV: function (tableId, filename) {
                this._filename = (typeof filename === 'undefined') ? tableId : filename;
                // Generate our CSV string from out HTML Table
                var csv = this._tableToCSV(document.getElementById(tableId));
                // Create a CSV Blob
                var blob = new Blob([csv], {type: "text/csv"});

                // Determine which approach to take for the download
                if (navigator.msSaveOrOpenBlob) {
                    // Works for Internet Explorer and Microsoft Edge
                    navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
                } else {
                    this._downloadAnchor(URL.createObjectURL(blob), 'csv');
                }
            },
            _getMsieVersion: function () {
                var ua = window.navigator.userAgent;

                var msie = ua.indexOf("MSIE ");
                if (msie > 0) {
                    // IE 10 or older => return version number
                    return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
                }

                var trident = ua.indexOf("Trident/");
                if (trident > 0) {
                    // IE 11 => return version number
                    var rv = ua.indexOf("rv:");
                    return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
                }

                var edge = ua.indexOf("Edge/");
                if (edge > 0) {
                    // Edge (IE 12+) => return version number
                    return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
                }

                // other browser
                return false;
            },
            _isFirefox: function () {
                if (navigator.userAgent.indexOf("Firefox") > 0) {
                    return 1;
                }

                return 0;
            },
            _downloadAnchor: function (content, ext) {
                var anchor = document.createElement("a");
                anchor.style = "display:none !important";
                anchor.id = "downloadanchor";
                document.body.appendChild(anchor);

                // If the [download] attribute is supported, try to use it

                if ("download" in anchor) {
                    anchor.download = this._filename + "." + ext;
                }
                anchor.href = content;
                anchor.click();
                anchor.remove();
            },
            _tableToCSV: function (table) {
                // We'll be co-opting `slice` to create arrays
                var slice = Array.prototype.slice;

                return slice
                        .call(table.rows)
                        .map(function (row) {
                            return slice
                                    .call(row.cells)
                                    .map(function (cell) {
                                        return '"t"'.replace("t", cell.textContent);
                                    })
                                    .join(",");
                        })
                        .join("\r\n");
            }
        };

    </script>
</body>
</html>