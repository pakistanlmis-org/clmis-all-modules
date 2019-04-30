<?php
//echo '<pre>';print_r($_REQUEST);exit;
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$prov_name= (!empty($_REQUEST['prov_name'])?$_REQUEST['prov_name']:'');
$prod_name= (!empty($_REQUEST['prod_name'])?$_REQUEST['prod_name']:'');
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$itm_id = $_REQUEST['itm_id'];
$funding_source = (!empty($_REQUEST['funding_source'])?$_REQUEST['funding_source']:'');

//Chart caption
$caption = "Stock Availability Status in Reporting Districts";
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'expiry_pie';
$qry_f = "SELECT
            funding_stk_prov.funding_source_id
            FROM
            funding_stk_prov
            WHERE
            funding_stk_prov.province_id = $province";
$res_f = mysql_query($qry_f);
$funding_stks=array();
while($row_f=mysql_fetch_assoc($res_f))
{
    $funding_stks[$row_f['funding_source_id']]=$row_f['funding_source_id'];
}
?>
<html>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="">
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
    <SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right;display:none;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        //query for total districts of the stakeholder
        $qry_5 = "SELECT
                        itminfo_tab.itm_id,
                        stock_batch.batch_expiry AS batch_expiry,
                        sum(stock_batch.Qty) as qty,
                        tbl_warehouse.wh_name
                FROM
                	stock_batch
                        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                        INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                        INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                        INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                        INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                WHERE
                    DATE_FORMAT(
                                     tbl_stock_master.TranDate,
                                     '%Y-%m-%d'
                             ) <= '" . $from_date . "'
                         AND (
                                tbl_stock_master.WHIDFrom = 123
                                OR tbl_stock_master.WHIDTo = 123
                        )AND 
                    stock_batch.funding_source IN (".implode(',',$funding_stks).") AND
                    itminfo_tab.itm_id IN ($itm_id) AND
                    stock_batch.Qty > 0 ";
        if(!empty($funding_source) && $funding_source <> 'all')
        $qry_5 .= " AND stock_batch.funding_source = $funding_source ";
        $qry_5 .="
                GROUP BY
                        itminfo_tab.itm_id,stock_batch.batch_expiry
                ORDER BY
                        stock_batch.batch_expiry

                         ";
                
    //echo $qry_5;exit;
    $res_5 = mysql_query($qry_5);

    $display_arr = array();
    while ($row_5 = mysql_fetch_assoc($res_5)) {
        $display_month = date('Y-M-d',strtotime($row_5['batch_expiry']));
        $display_arr[$row_5['wh_name'].' - '.$display_month]=$row_5['qty'];
        
    }
    //echo '<pre>';print_r($display_arr);exit;    

    $data_1 = "";
   
    foreach($display_arr as $date1 => $v)
    {
        
        $data_1 .= "<set label='".$date1."' value='".$v."' />";
    }

    $xmlstore = "<chart showLegend='1' theme='fint' numberSuffix=' ' exportEnabled='1' exportAction='Download' caption=' Expiry Wise Quantities - ".date('Y-M-d',strtotime($from_date))." - ".$prod_name." - ".$prov_name."' exportFileName='Capacity Occupation" . date('Y-m-d H:i:s') . "'>";
    $xmlstore .= $data_1;
    $xmlstore .= "</chart>";
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/Pie2D.swf", "", $xmlstore, 'capacity_occupation', '100%', 450, false, false);
    ?>
	</div>
</div>
    </body>
</html>