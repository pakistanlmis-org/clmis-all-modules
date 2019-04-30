<?php
ini_set('max_execution_time', 0);
include("../includes/classes/Configuration.inc.php");

if(isset($_REQUEST['submit_btn'])){
    //echo '<pre>';print_r($_REQUEST);exit;
}
include(APP_PATH . "includes/classes/db.php");
include APP_PATH . "includes/classes/functions.php";
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");
include(PUBLIC_PATH . "html/header.php");
$fundingSourceText = 'All Funding Sources';
$caption = 'Product wise Distribution and SOH';
$subCaption = '';
$downloadFileName = 'a';
$chart_id = 'distributionAndSOH';

$f_date= (!empty($_REQUEST['from_date'])?$_REQUEST['from_date']:date("Y-m").'-01');
$t_date= (!empty($_REQUEST['to_date'])?$_REQUEST['to_date']:$f_date);
$from_date = date("Y-m-d", strtotime($f_date));
$to_date = date("Y-m-t", strtotime($t_date));

$time1  = strtotime($from_date); 
$time2  = strtotime($to_date); 
$my     = date('mY', $time2); 

$months_list = array(date('Y-m-01', $time1)); 

if($f_date != $t_date){
    while($time1 < $time2) { 
       $time1 = strtotime(date('Y-m-d', $time1).' +1 month'); 
       if(date('mY', $time1) != $my && ($time1 < $time2)) 
          $months_list[] = date('Y-m-01', $time1); 
    } 

    $months_list[] = date('Y-m-01', $time2); 
}
$number_of_months = count($months_list);
//echo '<pre>';print_r($months_list);exit;

$province_arr = (!empty($_REQUEST['province'])?$_REQUEST['province']:'');
$stk_arr = (!empty($_REQUEST['stakeholder'])?$_REQUEST['stakeholder']:'');
$itm_arr_request = (!empty($_REQUEST['product'])?$_REQUEST['product']:'');

if(isset($_REQUEST['submit_btn'])){
    $province = implode(',',$province_arr);
    $stk = implode(',',$stk_arr);
    $itm = implode(',',$itm_arr_request);
}
$where_clause ="";

if(!empty($province))   $where_clause .= " AND summary_district.province_id in (".$province.")  ";
if(!empty($stk))        $where_clause .= " AND summary_district.stakeholder_id in (".$stk.")  ";
if(!empty($itm))        $where_clause .= " AND itminfo_tab.itm_id in (".$itm.")  ";


$colors_arr=array();
$colors_arr[1]='#BAE8F7';
$colors_arr[2]='#FCE3A7';
$colors_arr[7]='#FAB6B6';
$colors_arr[73]='#FFBDF5';
$colors_arr[9]='#AEE8C3';
$colors_arr[7]='#FAB6B6';
?>

    <style>
            span.multiselect-native-select {
                position: relative
            }
            span.multiselect-native-select select {
                border: 0!important;
                clip: rect(0 0 0 0)!important;
                height: 1px!important;
                margin: -1px -1px -1px -3px!important;
                overflow: hidden!important;
                padding: 0!important;
                position: absolute!important;
                width: 1px!important;
                left: 50%;
                top: 30px
            }
            .multiselect-container {
                position: absolute;
                list-style-type: none;
                margin: 0;
                padding: 0
            }
            .multiselect-container .input-group {
                margin: 5px
            }
            .multiselect-container>li {
                padding: 0
            }
            .multiselect-container>li>a.multiselect-all label {
                font-weight: 700
            }
            .multiselect-container>li.multiselect-group label {
                margin: 0;
                padding: 3px 20px 3px 20px;
                height: 100%;
                font-weight: 700
            }
            .multiselect-container>li.multiselect-group-clickable label {
                cursor: pointer
            }
            .multiselect-container>li>a {
                padding: 0
            }
            .multiselect-container>li>a>label {
                margin: 0;
                height: 100%;
                cursor: pointer;
                font-weight: 400;
                padding: 3px 0 3px 30px
            }
            .multiselect-container>li>a>label.radio, .multiselect-container>li>a>label.checkbox {
                margin: 0
            }
            .multiselect-container>li>a>label>input[type=checkbox] {
                margin-bottom: 5px
            }
            

        </style>
    
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!--<div class="pageLoader"></div>-->
    <!-- BEGIN HEADER -->
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>

    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
               <div class="row">
                   <div class="col-md-12">
                            <div class="widget" data-toggle="">
                                <div class="widget-head">
                                    <h3 class="heading">Filter by</h3>
                                </div>
                                <div class="widget-body collapse in">
                                    <form name="frm" id="frm" action="" method="get">
                                        <table width="100%">
                                            <tbody>
                                            <tr>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">From</label>
                                                            <div class="form-group">
                                                                <input type="text" name="from_date" id="from_date"  class="form-control input-sm" value="<?php echo date('Y-m',strtotime($from_date)); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">To</label>
                                                            <div class="form-group">
                                                                <input type="text" name="to_date" id="to_date"  class="form-control input-sm" value="<?php echo date('Y-m',strtotime($to_date)); ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </td>
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Province</label>
                                                                <select required name="province[]" id="province"  class="multiselect-ui form-control input-sm"  >
                                                                    <?php
                                                                       $queryprov = "SELECT
                                                                                           tbl_locations.PkLocID AS prov_id,
                                                                                           tbl_locations.LocName AS prov_title
                                                                                       FROM
                                                                                           tbl_locations
                                                                                       WHERE
                                                                                           LocLvl = 2
                                                                                           AND parentid IS NOT NULL
                                                                                           AND tbl_locations.LocType = 2";
                                                                       //query result
                                                                       $rsprov = mysql_query($queryprov) or die();

                                                                       while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                           if (in_array($rowprov['prov_id'],$province_arr)) {
                                                                               $sel = "selected='selected'";
                                                                               $prov_name[]=$rowprov['prov_title'];
                                                                           } else {
                                                                               $sel = "";
                                                                           }
                                                                           ?>
                                                                               <option value="<?php echo $rowprov['prov_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['prov_title']; ?></option>
                                                                    <?php
                                                                           }
                                                                   ?>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Stakeholder</label>
                                                                <select  required name="stakeholder[]" id="stakeholder" class="multiselect-ui form-control input-sm" multiple>
                                                                    <?php

                                                                    $queryprov = "SELECT
                                                                            stakeholder.stkname,
                                                                            stakeholder.stkid
                                                                            FROM
                                                                            stakeholder
                                                                            WHERE
                                                                            stakeholder.stk_type_id = 0 AND
                                                                            stakeholder.lvl = 1 AND
                                                                            stakeholder.is_reporting = 1
                                                                        ";
                                                                    //query result
                                                                    $rsprov = mysql_query($queryprov) or die();

                                                                    while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                        if (in_array($rowprov['stkid'],$stk_arr)) {
                                                                            $sel = "selected='selected'";
                                                                            $stk_name[]=$rowprov['stkname'];
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        ?>
                                                                            <option value="<?php echo $rowprov['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowprov['stkname']; ?></option>
                                                                 <?php
                                                                        }

                                                                ?>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                
                                                <td class="col-md-2">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="control-label">Product</label>
                                                                <select required  name="product[]" id="product" class="multiselect-ui form-control input-sm" multiple>
                                                                    <?php
                                                                    $queryprov = "SELECT
                                                                                itminfo_tab.itm_id,
                                                                                itminfo_tab.itm_name
                                                                                FROM
                                                                                itminfo_tab
                                                                                WHERE
                                                                                itminfo_tab.itm_category = 1 AND
                                                                                itminfo_tab.method_type IS NOT NULL
                                                                                ORDER BY
                                                                                itminfo_tab.method_rank ASC
                                                                        ";
                                                                    //query result
                                                                    $rsprov = mysql_query($queryprov) or die();

                                                                    while ($rowprov = mysql_fetch_array($rsprov)) {
                                                                        if (in_array($rowprov['itm_id'],$itm_arr_request)) {
                                                                            $sel = "selected='selected'";
                                                                            $itm_name[]=$rowprov['itm_name'];
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        ?>
                                                                            <option value="<?php echo $rowprov['itm_id']; ?>" <?php echo $sel; ?>><?php echo $rowprov['itm_name']; ?></option>
                                                                 <?php
                                                                        }

                                                                ?>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="col-md-2">
                                                   
                                                    <label class="control-label">&nbsp;</label>
                                                    <input name="submit_btn" type="submit" class="btn btn-succes" value="Go">
                                                    
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                   </div>
               </div>
                 <?php
                 if(isset($_REQUEST['submit_btn'])){
                     
                        $qry = "SELECT
                                    provincial_cyp_factors.pk_id,
                                    provincial_cyp_factors.province_id,
                                    provincial_cyp_factors.item_id,
                                    provincial_cyp_factors.stakeholder_id,
                                    provincial_cyp_factors.cyp_factor
                                FROM
                                    provincial_cyp_factors
                                where provincial_cyp_factors.province_id   = ".$province."
                                ";
                        $qryRes = mysql_unbuffered_query($qry);
                        $cyp_arr =   array();

                        while($row = mysql_fetch_assoc($qryRes))
                        {
                           $cyp_arr[$row['stakeholder_id']][$row['item_id']] =  $row['cyp_factor'];
                        }
                     
                            $qry = "SELECT
                                        stakeholder.stkname,
                                        dist.LocName AS dist_name,
                                        summary_district.province_id,
                                        summary_district.stakeholder_id,
                                        summary_district.district_id,
                                        summary_district.reporting_date,
                                        itminfo_tab.itm_id,
                                        itminfo_tab.itm_name,
                                        sum(summary_district.consumption) as total_consumption,
                                        SUBSTRING_INDEX(GROUP_CONCAT(CAST(summary_district.avg_consumption AS CHAR) ORDER BY summary_district.reporting_date DESC), ',', 1 ) as last_amc,
                                        SUBSTRING_INDEX(GROUP_CONCAT(CAST(summary_district.soh_district_lvl AS CHAR) ORDER BY summary_district.reporting_date DESC), ',', 1 ) as last_soh

                                    FROM
                                        summary_district
                                    INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
                                    INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
                                    INNER JOIN tbl_locations AS dist ON summary_district.district_id = dist.PkLocID
                                    WHERE
                                        summary_district.reporting_date BETWEEN  '".$from_date."' and '".$to_date."'
                                        $where_clause
                                    group BY
                                        summary_district.province_id,
                                        summary_district.stakeholder_id,
                                        summary_district.district_id,
                                        itminfo_tab.itm_id
                                    ORDER BY
                                        summary_district.province_id,
                                        summary_district.stakeholder_id,
                                        dist.LocName,
                                        itminfo_tab.itm_id
                            ";
                        //echo $qry;exit;
                        $qryRes = mysql_unbuffered_query($qry);
                        $display_arr = $data_stk_arr = $data_dist_arr = $data_itm_arr =   array();

                        while($row = mysql_fetch_assoc($qryRes))
                        {
                            $display_arr[$row['stakeholder_id']][$row['district_id']][$row['itm_id']]['consumption']   = $row['total_consumption'];
                            $display_arr[$row['stakeholder_id']][$row['district_id']][$row['itm_id']]['amc']           = $row['last_amc'];
                            $display_arr[$row['stakeholder_id']][$row['district_id']][$row['itm_id']]['soh']           = $row['last_soh'];
                        
                            //$data_prov_arr[$row['province_id']]     = $row[''];
                            $data_stk_arr[$row['stakeholder_id']]   = $row['stkname'];
                            $data_dist_arr[$row['district_id']]     = $row['dist_name'];
                            $data_itm_arr[$row['itm_id']]           = $row['itm_name'];
                        }    
                //echo '<pre>';print_r($display_arr);exit;    
                ?>
                <div class="portlet box green">
                    <div class="portlet-title">
                            <div class="caption">
                                    <i class="fa fa-medkit"></i>Indicators Data
                            </div>
                            <div class="tools"><a href="javascript:;" class="collapse" data-original-title="" title=""></a></div>
                    </div>
                    
                    <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="page-title1   center"> Reporting Period : <?php echo date('M-Y',strtotime($from_date)); ?> To <?php echo date('M-Y',strtotime($to_date)); ?></h3>
                        <h4 class="page-title1 row  center"> 
                            <div class=" col-md-11">
                               <?php  echo implode(',',$prov_name); ?> - <?php  echo implode(',',$stk_name); ?> 
                            </div>
                            <div class=" col-md-1 right">
                                <a id="btnExport" onclick="javascript:xport.toCSV('AvgStockOut');"><img style="cursor:pointer;" src="<?php echo PUBLIC_URL; ?>images/excel-32.png"  title="Export to Excel" /></a>
                            </div>
                        </h4>
                        
                   </div>
                    
                </div>
                 <div class="row">
                    <div class="col-md-12">
                        <div class="widget1 widget-tabs"> 
                            <div class="widget-body" style="overflow:auto">
                                <table id="AvgStockOut" name="tbl" class="table table-bordered table-condensed font-grey-gallery" border="">    
                                    <tr>
                                        <th rowspan="2" align="center" width="60px">Indicator</th>
                                        <th rowspan="2" align="center" width="150px">Product</th>
                                        <?php
                                        foreach($data_stk_arr as $stk_id => $stk_name){ 
                                            $cspan=count($data_dist_arr);
                                            echo '<td colspan="'.$cspan.'" style="background-color:'.$colors_arr[$stk_id].';" align="centera">'.$stk_name.'</td>';
                                        }
                                        ?>
                                    </tr>
                                    <tr>
                                        <?php
                                        foreach($data_stk_arr as $stk_id => $stk_name){ 
                                            foreach($data_dist_arr as $dist_id => $dist_name){ 
                                                echo '<td  style="background-color:'.$colors_arr[$stk_id].'"><b>'.$dist_name.'</b></td>';
                                            }
                                        }
                                        ?>
                                    </tr>
                            <?php
                            $indicators = array();
                            $indicators['consumption']='Consumption';
                            $indicators['amc']='AMC';
                            $indicators['soh']='SOH';
                            $indicators['cyp']='CYP';
                            $old_ind = '';
                            foreach($indicators as $ind_id => $indicator_name  )
                            { 
                                foreach($data_itm_arr as $itm_id => $itm_name){ 
                                    echo '<tr>';
                                    if($old_ind != $ind_id ){
                                        $rspan = count($data_itm_arr);
                                        echo '<td rowspan="'.$rspan.'"><b>'.$indicator_name.'</b></td>';
                                    }
                                    echo '<td><b>'.$itm_name.'</b></td>';
                                    
                                    foreach($data_stk_arr as $stk_id => $stk_name){ 
                                            foreach($data_dist_arr as $dist_id => $dist_name){ 
                                                $val =0;
                                                if($ind_id == 'cyp'){
                                                    if(!empty($display_arr[$stk_id][$dist_id][$itm_id]['consumption']))
                                                        $val = $cyp_arr[$stk_id][$itm_id] * $display_arr[$stk_id][$dist_id][$itm_id]['consumption'];
                                                }
                                                else{
                                                    if(!empty($display_arr[$stk_id][$dist_id][$itm_id][$ind_id]))
                                                        $val = $display_arr[$stk_id][$dist_id][$itm_id][$ind_id];
                                                    
                                                }
                                                echo '<td align="right">'.number_format($val).'</td>';
                                            }
                                        }
                                    
                                    echo '</tr>';
                                    $old_ind = $ind_id;
                                }
                            }
                            
                                ?>
                                </table>
                          </div>
                      </div>
                   </div>
                 </div>
                    </div>
                </div>
                
                
                <?php
                 }
                ?>
            </div>
        </div>
    </div>

    <?php 
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; ?>
    <script src="<?=PUBLIC_URL?>js/bootstrap_multiselect.js"></script>


    <script>
        $(function() {
           
            $('#from_date, #to_date').datepicker({
                dateFormat: "yy-mm",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                maxDate: '' 
            });
            
            $('.multiselect-ui').multiselect({
                    includeSelectAllOption: true
                });

            
            
        })
        
      
    </script>
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
<!-- END BODY -->
</html>