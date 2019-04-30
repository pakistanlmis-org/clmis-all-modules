<?php
//echo '<pre>';print_r($_REQUEST);exit;
ini_set('max_execution_time', 0);
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

@$wh_id = $_REQUEST['wh_id'];
@$item_sel = $_REQUEST['itm'];


$chart_id = 'b2_1';
$qry_1 = "  SELECT
            itminfo_tab.itmrec_id,
            itminfo_tab.itm_name,
            itminfo_tab.itm_id
        FROM
            itminfo_tab
            INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
        WHERE
            itminfo_tab.itm_category <> 2 AND
            stakeholder_item.stkid =  ".$_SESSION['user_stakeholder1']."  
        ORDER BY
            itminfo_tab.frmindex ASC
    ";
    //echo $qry_1;exit;
    $res_1 = mysql_query($qry_1);
    $itm_arr=$itm_arr2 = array();
    while($row_1 = mysql_fetch_array($res_1))
    {
        $itm_arr[$row_1['itm_id']]=$row_1['itm_name'];
    }
    
    
    $qry_1 = "  SELECT
                    tbl_hf_data.received_balance,
                    tbl_hf_data.closing_balance,
                    tbl_hf_data.issue_balance,
                    tbl_hf_data.reporting_date,
                    tbl_hf_data.item_id,
                    tbl_hf_data.warehouse_id,
                    tbl_warehouse.wh_name
                FROM
                    tbl_hf_data
                    INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                WHERE
                    tbl_hf_data.warehouse_id = $wh_id AND
                    tbl_hf_data.item_id = $item_sel
                ORDER BY
                tbl_hf_data.reporting_date ASC
            ";
    //echo $qry_1;exit;
    $res_1 = mysql_query($qry_1);
    $trend_arr =$cb_arr =$issue_arr = $months_arr = array();
    $wh_name = '';
    while($row_1 = mysql_fetch_array($res_1))
    {
        $trend_arr[$row_1['reporting_date']]    = $row_1['received_balance'];
        $cb_arr[$row_1['reporting_date']]       = $row_1['closing_balance'];
        $issue_arr[$row_1['reporting_date']]       = $row_1['issue_balance'];
        $months_arr[$row_1['reporting_date']]   = $row_1['reporting_date'];
        $wh_name                                = $row_1['wh_name'];
    }
    
    
include PUBLIC_PATH . "html/top_im.php";
?>
<link href="<?php echo PUBLIC_URL;?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <?php
    
    echo '<div class="">';
    foreach($itm_arr as $itm_id => $itm_name){
        $clr = ' yellow ';
        if($item_sel == $itm_id ) $clr = ' green ';
        echo '<a class="btn btn-xs btn-rounded '.$clr.'" href="sdp_issuance_trend.php?wh_id='.$wh_id.'&itm='.$itm_id.'">'.$itm_name.'</a>  ';
    }
    echo '</div>';
    
    $caption = ' Stock issuance trend to  '.$wh_name;
    $downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');   
    ?>
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;display:none;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 

    $xmlstore = '<chart caption="'.$caption.'" yaxismaxvalue="100"  subcaption="" xaxisname="Months" yaxisname="Issuance"  palettecolors="#e07700,#03af03,#7e9bea" exportEnabled="1"  numberprefix="" theme="fint">';
    $xmlstore .= ' <categories>';
    foreach($months_arr as $k => $mon)
    {
         $xmlstore .= '     <category label="'.date('Y-M',strtotime($mon)).'"/>';  
    }
    $xmlstore .= ' </categories>';
    
    $xmlstore .= ' <dataset seriesname="Issued to '.$wh_name.' " showvalues="1">';
    foreach($months_arr as $k => $mon)
    {
        $xmlstore .= '     <set label="'.date('Y-M',strtotime($mon)).'" value="'.(($trend_arr[$mon])).'"   />';
    }
    $xmlstore .= '  </dataset>';
    
    $xmlstore .= ' <dataset seriesname="SOH at '.$wh_name.' " showvalues="1">';
    foreach($months_arr as $k => $mon)
    {
        $xmlstore .= '     <set label="'.date('Y-M',strtotime($mon)).'" value="'.(($cb_arr[$mon])).'"   />';
    }
    $xmlstore .= '  </dataset>';
    
    $xmlstore .= ' <dataset seriesname="Consumption at '.$wh_name.' " showvalues="1">';
    foreach($months_arr as $k => $mon)
    {
        $xmlstore .= '     <set label="'.date('Y-M',strtotime($mon)).'" value="'.(($issue_arr[$mon])).'"   />';
    }
    $xmlstore .= '  </dataset>';

    $xmlstore .= ' </chart>';
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/MSLine.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    ?>
	</div>
</div>
<script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    
<script>
    </script>