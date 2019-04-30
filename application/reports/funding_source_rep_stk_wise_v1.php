<?php
/**
 * stock_availability
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
//include FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");

$province='';
if(!empty($_REQUEST['province'])) $province=$_REQUEST['province'];

 


//echo '<pre>';
//echo 'open:';print_r($yr_list);
//echo 'issue:';print_r($qtr_list);
/*echo '<pre>';
echo 'open:';print_r($opening_bal);
echo 'issue:';print_r($issue_arr);
echo 'closing:';print_r($closing_bal);
exit;
*/

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
                                <label class="control-label">Province</label>
                                    <div class="form-group">
                                        <select name="province" id="province" required class="form-control input-sm">
                                            <option value="">Select</option>
                                            <?php

                                            $where='';

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
                                                $stk_name='';
                                                while ($rowp = mysql_fetch_array($rsprov)) {
                                                    if ($province == $rowp['PkLocID']) {
                                                        $sel = "selected='selected'";
                                                        $pro_name=$rowp['LocName'];
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
                
                if(!empty($province))
                    $and .= " AND national_stock.prov_id = $province  ";
                
                 
                 $qry = "SELECT
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
                        Sum(national_stock.quantity) as opening
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
                $opening_bal=$issue_arr=$stk_arr=$closing_bal=$prov_arr=$prod_arr=array();
                while($row = mysql_fetch_assoc($qryRes))
                {
                    //print_r($row);
                    $opening_bal[$row['LocName']][$stk_name][$row['itm_name']]=$row['opening'];
                    $closing_bal[$row['LocName']][$row['itm_name']]=$row['opening'];
                    $prov_arr[$row['LocName']] = $row['LocName'];
                    $prod_arr[$row['LocName']][$row['itm_name']] = $row['itm_name'];
                }

                
                $qry = "
                    SELECT
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
                        Sum(national_stock.quantity) as qty
                    FROM
                        national_stock
                        INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                        INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                        INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                    WHERE
                        national_stock.ref = 'issue'
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
                //echo $qry;exit;
                $qryRes = mysql_query($qry);
                while($row = mysql_fetch_assoc($qryRes))
                {
                    //print_r($row);
                    $issue_arr[$row['LocName']][$row['stkname']][$row['itm_name']]=$row['qty'];
                    if(empty($closing_bal[$row['LocName']][$row['itm_name']]))
                           $closing_bal[$row['LocName']][$row['itm_name']]=0;

                    $closing_bal[$row['LocName']][$row['itm_name']]+=$row['qty'];
                    $stk_arr[$row['stkname']] = $row['stkname'];
                    
                    $prov_arr[$row['LocName']] = $row['LocName'];
                    $prod_arr[$row['LocName']][$row['itm_name']] = $row['itm_name'];
                }
                
                
                /*echo '<pre>';
                echo 'open:';print_r($opening_bal);
                echo 'issue:';print_r($issue_arr);
                echo 'closing:';print_r($closing_bal);
                 */
                 
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
                                $cols+=3;
                                echo '  <tr>
                                            <th align="center" colspan="">Product</th>
                                            <th align="center" colspan="">Opening Balance <br/>(As on 01-Oct-2016)</th>
                                        ';
                                foreach($stk_arr as $k => $stk)
                                {
                                    
                                    echo '<th align="center">'.$stk.'</th>';
                                    
                                }
                                echo '<th align="center" colspan="">Closing Balance</th>';
                                echo '</tr>';
                                foreach($prov_arr as $k => $prov)
                                {
                                    if(!empty($opening_bal[$prov]))
                                    {
                                        
                                        $prov_data=$opening_bal[$prov];
                                    }
                                    else
                                    {
                                        $prov_data=array();
                                       // echo '<pre>'.$prov.'::';print_r($prod_arr[$prov]);
                                        foreach($prod_arr[$prov] as $prv => $prd)
                                        {
                                            $prov_data[$stk_name][$prd]=0;
                                        }
                                        
                                    }
                                    
                                    echo '  <tr>
                                                <td align="left" colspan="'.$cols.'" style="background-color:#93d893">'.$prov.'</td>
                                            </tr>';
                                    
                                    foreach($prov_data as $stk => $prod_data)
                                    {
                                        echo '  <tr>
                                                    <td align="left" colspan="'.$cols.'" style="background-color:#c2edc2">'.$stk.'</td>
                                                </tr>';
                                        foreach($prod_data as $prod => $qty)
                                        {
                                            echo '  <tr>
                                                        <td align="center" colspan="">'.$prod.'</td>
                                                        <td align="right" colspan="">'.number_format($qty).'</td>
                                                    ';
                                            
                                            foreach($stk_arr as $k => $stk)
                                            {
                                               
                                               echo '<td  align="right" >'.(!empty($issue_arr[$prov][$stk][$prod])?number_format(abs($issue_arr[$prov][$stk][$prod])):'0').'</td>';
                                                
                                            }
                                            echo '<td align="right" colspan="">'.(!empty($closing_bal[$prov][$prod])?number_format($closing_bal[$prov][$prod]):'0').'</td>';
                                            echo '</tr>';
                                        }
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
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
//include report_includes
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
     var xport = {
  _fallbacktoCSV: true,  
  toXLS: function(tableId, filename) {   
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
  toCSV: function(tableId, filename) {
    this._filename = (typeof filename === 'undefined') ? tableId : filename;
    // Generate our CSV string from out HTML Table
    var csv = this._tableToCSV(document.getElementById(tableId));
    // Create a CSV Blob
    var blob = new Blob([csv], { type: "text/csv" });

    // Determine which approach to take for the download
    if (navigator.msSaveOrOpenBlob) {
      // Works for Internet Explorer and Microsoft Edge
      navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
    } else {      
      this._downloadAnchor(URL.createObjectURL(blob), 'csv');      
    }
  },
  _getMsieVersion: function() {
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
  _isFirefox: function(){
    if (navigator.userAgent.indexOf("Firefox") > 0) {
      return 1;
    }
    
    return 0;
  },
  _downloadAnchor: function(content, ext) {
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
  _tableToCSV: function(table) {
    // We'll be co-opting `slice` to create arrays
    var slice = Array.prototype.slice;

    return slice
      .call(table.rows)
      .map(function(row) {
        return slice
          .call(row.cells)
          .map(function(cell) {
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