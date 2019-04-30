<?php
include("../includes/classes/AllClasses.php");
include(APP_PATH . "includes/report/FunctionLib.php");
include(PUBLIC_PATH . "html/header.php");

$date = date("Y-m-01");

if (!empty($_REQUEST['to_date']))
    $date = $_REQUEST['to_date'];

$qry2 = " SELECT
            stakeholder.stkid,
            stakeholder.stkname
            FROM
            stakeholder
            WHERE
            stakeholder.lvl = 1 AND
            stakeholder.stk_type_id IN (0,1)
 ";
$qryRes2 = mysql_query($qry2);
$stakeholders= array();
$c=1;
while ($row = mysql_fetch_assoc($qryRes2)) {
    $stakeholders[$row['stkid']] = $row['stkname'];
}
$qry2 = " SELECT
itminfo_tab.itm_id,
itminfo_tab.itm_name
FROM
itminfo_tab
WHERE
itminfo_tab.itm_id IN (1,5,7,9)

 ";
$qryRes2 = mysql_query($qry2);
$itms= array();
$c=1;
while ($row = mysql_fetch_assoc($qryRes2)) {
    $itms[$row['itm_id']] = $row['itm_name'];
}
$qry = "SELECT
                                    tbl_warehouse.wh_id,
                                    tbl_warehouse.wh_name,
                                    tbl_wh_data.RptDate,
                                    tbl_wh_data.wh_cbl_a,
                                    stakeholder.lvl,
                                    tbl_warehouse.stkid,
                                    tbl_wh_data.item_id,
                                    tbl_locations.LocName AS province_name,
                                    s.stkname,
                                    itminfo_tab.itm_name
                                    FROM
                                    tbl_wh_data
                                    INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                    INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
                                    INNER JOIN stakeholder AS s ON tbl_warehouse.stkid = s.stkid
                                    INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
                                    WHERE
                                    stakeholder.lvl = 3 AND
                                    tbl_wh_data.RptDate = '" . $date . "' AND
                                    tbl_wh_data.item_id IN ('IT-001', 'IT-005', 'IT-007', 'IT-009') AND
                                    tbl_warehouse.stkid IN (1, 2, 7, 73)
                                    ORDER BY
                                    tbl_warehouse.prov_id ASC,
                                    tbl_warehouse.wh_name ASC,
                                    tbl_warehouse.stkid ASC,
                                    tbl_wh_data.item_id ASC

                                    ";
                            //echo $qry;exit;
                            $qryRes = mysql_query($qry);
                            $data = array();
while($row = mysql_fetch_assoc($qryRes))
{
    $data[$row['wh_id']] = $row['wh_cbl_a'];
}


$qry_hf= "
   SELECT

        Count(*) AS cc,
        tbl_warehouse.dist_id,
        s.stkname,
s.stkid
        FROM
        tbl_wh_data
        RIGHT JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
        RIGHT JOIN stakeholder a ON tbl_warehouse.stkofficeid = a.stkid  
        INNER JOIN stakeholder s ON tbl_warehouse.stkid = s.stkid
        WHERE  
            tbl_wh_data.RptDate = '".$date."' AND
            tbl_wh_data.item_id = 'IT-001' AND
            a.lvl = 3 AND tbl_warehouse.stkid IN (1,2,7,73)
        group BY 
        tbl_warehouse.dist_id,
            a.stkname  "; 
//echo $qry_hf;
$Res2 =mysql_query($qry_hf);
$reporting_rate = array();
while($row = mysql_fetch_assoc($Res2))
{
    $perc = 0;
    if(!empty($row['cc']) && $row['cc']>0) $perc = 100;
    $reporting_rate[$row['dist_id']][$row['stkid']]= floatval($perc);
}
//echo '<pre>';print_r($reporting_rate);exit;

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

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="control-label">Last Date</label>
                                            <div class="form-group">
                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php echo $date; ?>">
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
<?php ?>
                <div class="row">
                    <div id="div1" class="col-md-12">
                        <table id="cLMIS_table" border="1" width="100%" cellpadding="0" cellspacing="0" style="" class="table table-bordered table-condensed">
                            <div class="col-md-12">
                                <div class="col-md-11 h4 center">Stock Availability Rate - District stores</div>
                                <div class=" col-md-1 right">
                                    <a id="btnExport" onclick="javascript:xport.toCSV('cLMIS_table');"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png" onClick="mygrid.toExcel('<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxGrid/grid2excel/server/generate.php');" title="Export to Excel" /></a>
                                </div>
                            </div>    
                           
                            <tr class="info">
                                <td>#</td>
                                <td>Province</td>
                                <td>Stakeholder</td>
                                <td>District</td>
                                <td>Product</td>
                                
                                <td>Reporting Rate</td>
                                <td>Stock Out</td>
                                <td>SOH</td>
                            </tr>
                            <?php
                            $qry2 = " SELECT
                                        tbl_warehouse.wh_id,
                                        tbl_warehouse.wh_name,
                                        stakeholder.lvl,
                                        tbl_warehouse.stkid,
                                        tbl_locations.LocName AS province_name,
                                        s.stkname
                                        FROM
                                        tbl_warehouse
                                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                        INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
                                        INNER JOIN stakeholder AS s ON tbl_warehouse.stkid = s.stkid
                                        WHERE
                                        stakeholder.lvl = 3 AND
                                        tbl_warehouse.stkid IN (1, 2, 7, 73)
                                        ORDER BY
                                        tbl_warehouse.prov_id ASC,
                                        tbl_warehouse.wh_name ASC,
                                        tbl_warehouse.stkid ASC

                                         ";
                            $qryRes2 = mysql_query($qry2);
                            $dist_stores= array();
                            $c=1;
                            while ($row = mysql_fetch_assoc($qryRes2)) {
                                foreach($itms as $itm_id => $itm_name)
                                {
                                    echo '<tr>';
                                    echo '<td>'.$c++.'</td>';
                                    echo '<td>'.$row['province_name'].'</td>';
                                    echo '<td>'.$row['stkname'].'</td>';
                                    echo '<td>'.$row['wh_name'].'</td>';
                                    echo '<td>'.$itm_name.'</td>';

                                    $rr = 0;$so=1; $cb=0;
                                    if(!empty($data[$row['wh_id']]))  $rr = 100;
                                    if(!empty($data[$row['wh_id']]) && $data[$row['wh_id']]>0){
                                        $so = 0;
                                        $cb = $data[$row['wh_id']];
                                    }

                                    echo '<td align="right">'. number_format($rr).'</td>';
                                    echo '<td align="right">'. number_format($so).'</td>';
                                    echo '<td align="right">'. number_format($cb).'</td>';
                                    echo '</tr>';
                                }
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
                            include PUBLIC_PATH . "/html/footer.php";
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