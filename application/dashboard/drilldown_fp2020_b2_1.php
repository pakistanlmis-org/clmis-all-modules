<?php

//echo '<pre>';print_r($_REQUEST);exit;
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH . "html/header.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$reporting_rate = $_REQUEST['reporting_rate'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$province_param = 'all';
$province_param = (($province!='all')?$province:'\'all\'');
$province_name_param = 'All';
$province_name_param = (($province!='all')?$prov_name:'All');
     

$stk_id = $_REQUEST['stk_id'];
$stk_name = $_REQUEST['stk_name'];
$products = $_REQUEST['products'];

//Chart caption
$caption = 'Stock Availability Rate at SDPs - '.$prov_name.' - '.$stk_name.' for '.date('M-Y',strtotime($from_date));
//Chart heading sub Caption

//download File Name
$downloadFileName = $caption . ' - ' . date('Y-m-d H:i:s');
//chart_id
$chart_id = 'b2_1';
$qry_1 = "  SELECT
            itminfo_tab.itmrec_id,
            itminfo_tab.itm_name,
            itminfo_tab.itm_id
        FROM
            itminfo_tab
            INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
        WHERE
            itminfo_tab.itm_category = 1 AND
            stakeholder_item.stkid = $stk_id ";
        if(empty($products) || $products == 'null') 
               $qry_1 .= " AND  itminfo_tab.itm_id NOT IN(4,6,10,33) ";
        else
               $qry_1 .= " AND  itminfo_tab.itm_id IN(".$products.") ";
    $qry_1 .= "    ORDER BY
            itminfo_tab.frmindex ASC
    ";
    //echo $qry_1;exit;
    $res_1 = mysql_query($qry_1);
    $itm_arr=$itm_arr2 = array();
    while($row_1 = mysql_fetch_array($res_1))
    {
        $itm_arr[$row_1['itm_id']]=$row_1['itm_name'];
    }
include PUBLIC_PATH . "html/top_im.php";
?>
<link href="<?php echo PUBLIC_URL;?>assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></script>
    <script language="Javascript" src="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></script>
<div class="widget widget-tabs">    
    <div class="widget-body">
    <a href="javascript:exportChart('<?php echo $chart_id;?>', '<?php echo $downloadFileName;?>')" style="float:right; display:none;"><img class="export_excel" src="<?php echo PUBLIC_URL;?>images/excel-16.png" alt="Export" /></a>
	<?php 
        //query for getting reported facilities
        $q_reporting  = "SELECT
                                   tbl_warehouse.stkid,
                                    COUNT(
                                            DISTINCT tbl_warehouse.wh_id
                                    ) AS reportedWH
                            FROM
                                    tbl_warehouse
                            /*INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id*/
                            INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
                            INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                            WHERE
                                    tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                            AND tbl_warehouse.wh_id NOT IN (
                                    SELECT
                                            warehouse_status_history.warehouse_id
                                    FROM
                                            warehouse_status_history
                                    INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                                    WHERE
                                            warehouse_status_history.reporting_month = '".$from_date."'
                                    AND warehouse_status_history.`status` = 0
                                    
                            )
                           
                            AND tbl_warehouse.stkid = ".$stk_id."
                            AND tbl_hf_data.reporting_date = '".$from_date."'
                            GROUP BY
                                    tbl_warehouse.stkid";
        if($province!='all')
            $q_reporting .=" AND tbl_warehouse.prov_id = ".$province." ";
        //echo $q_reporting;exit;
        $res_reporting = mysql_query($q_reporting);
        $reporting_wh_arr = array();
        $total_reporting_wh = 0;
        while($row=mysql_fetch_assoc($res_reporting))
        {
            $reporting_wh_arr[$row['stkid']]=$row['reportedWH'];
            $total_reporting_wh +=$row['reportedWH'];
        }
        
        
        //Query for shipment main dashboard
        $qry = "SELECT
                stakeholder.stkid,
                stakeholder.stkname,
                tbl_hf_data.item_id ,
                itminfo_tab.itm_name,
                count(distinct tbl_hf_data.warehouse_id) as stock_outs
                    FROM
                            tbl_warehouse
                    INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkid
                    INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                    WHERE
                       tbl_warehouse.wh_id NOT IN (
                            SELECT
                                    warehouse_status_history.warehouse_id
                            FROM
                                    warehouse_status_history
                            INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                            WHERE
                                    warehouse_status_history.reporting_month = '".$from_date."'
                            AND warehouse_status_history.`status` = 0

                    )
                    AND tbl_hf_data.reporting_date = '".$from_date."'

                    AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                    AND ROUND( (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 2 ) > 0
                    AND itminfo_tab.itm_category = 1
                    AND tbl_warehouse.stkid = '".$stk_id."'
              ";
            if(empty($products) || $products == 'null') 
                   $qry .= " AND itminfo_tab.itm_id NOT IN(4,6,10,33) ";
            else
                   $qry .= " AND itminfo_tab.itm_id IN(".$products.") ";
          
    if($province!='all')
        $qry .=" AND tbl_warehouse.prov_id = ".$province."
                    AND tbl_hf_type_rank.province_id = ".$province." ";
    $qry .= " GROUP BY
                    stakeholder.stkid,tbl_hf_data.item_id
                ORDER BY 
                    stakeholder.stkid,tbl_hf_data.item_id
        ";
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c=1;
    $dist_arr  = $so_arr = array();
   
    while($row = mysql_fetch_assoc($qryRes))
    {
        $stk_arr[$row['stkid']] = $row['stkname'];

        $so_arr[$row['item_id']] = $row['stock_outs'];
        
    }    
    //echo '<pre>'.$total_reporting_wh;print_r($so_arr);print_r($stk_arr);print_r($reporting_wh_arr);exit;    

    //xml for chart
    $xmlstore = '<chart caption="'.$caption.'" yaxismaxvalue="100"  subcaption="" xaxisname="Stakeholders" yaxisname="Percentage"  palettecolors="#26C281,#FF0000,#57c5ed" exportEnabled="1"  numberprefix="" theme="fint">';

   
    $xmlstore .= ' <categories>';
     foreach($itm_arr as $itm_id => $itm_name)
    {
         $xmlstore .= '     <category label="'.$itm_name.'"/>';  
    }
    $xmlstore .= ' </categories>';
    
    $xmlstore .= ' <dataset seriesname="Stock Availability Rate at SDPs" showvalues="1">';
    foreach($itm_arr as $itm_id => $itm_name)
    {
        $val=(isset($so_arr[$itm_id])?$so_arr[$itm_id]:'0');
        if(!empty($reporting_wh_arr[$stk_id]) && $reporting_wh_arr[$stk_id] > 0)
            $perc = ((!empty($val)?$val:0)* 100)/$reporting_wh_arr[$stk_id];
        else
            $perc = 0;
        
        $xmlstore .= '     <set label="'.$itm_name.'" value="'.(number_format($perc  , 2)).'" tooltext="'.(number_format($perc  , 2)).', Click for drill down"  link="JavaScript:showDrillDown_lvl2('.$province_param.',\''.$province_name_param.'\',\''.$from_date.'\','.$stk_id.',\''.$stk_name.'\',\''.$itm_id.'\',\''.$itm_name.'\');" />';
        //$xmlstore .= '     <set label="'.$itm_name.'" value="'.(number_format($perc  , 2)).'" tooltext="'.(number_format($perc  , 2)).', Click for drill down"  />';
    }
    $xmlstore .= '  </dataset>';

    
    $xmlstore .= ' <dataset  seriesname="Reporting Rate (SDP Level)"  renderas="line" showvalues="1">';
    foreach($itm_arr as $itm_id => $itm_name)
    {
        $xmlstore .= '     <set label="'.$itm_name.'" value="'.(number_format($reporting_rate,1)).'"   />';
    }
    $xmlstore .= '  </dataset>';
    
    
$xmlstore .= ' </chart>';
    //end chart
   
    //Render chart
    FC_SetRenderer('javascript');
    echo renderChart(PUBLIC_URL."FusionCharts/Charts/StackedColumn2DLine.swf", "", $xmlstore, $chart_id, '100%', 330, false, false);
    ?>
	</div>
</div>
     <div class="widget widget-tabs" style='display:none;'>    
        <div class="widget-body" id="drilldown_div_2">
            
        </div>
    </div>
    
     <div class="widget widget-tabs" style='display:none;'>    
        <div class="widget-body " id="drilldown_div_3">
            
        </div>
    </div>
<script src="<?php echo PUBLIC_URL;?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    
<script>
         
    $(document).on('click', '.legend_block', function(){
        var clr = $(this).data('clr');
        //alert(clr);
        $('.t_rows').hide();
        $('.t_rows[data-clr='+clr+']').show();
        $('#show_all').show();
        
    });
    $(document).on('click', '#show_all', function(){
        $('.t_rows').show();
    });
     function showDrillDown_lvl2(prov,prov_name,from_date,stk,stk_name,prod_id,prod_name) {
       
        var div_id = "drilldown_div_2";
        var url = 'drilldown_fp2020_b2_2.php';
        
        var dataStr='';
        dataStr += "province="+prov+"&prov_name="+prov_name+"&from_date="+from_date+"&stakeholder="+stk+"&stk_name="+stk_name+"&prod_id="+prod_id+"&prod_name="+prod_name;

        $('#'+div_id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");
        $('#drilldown_div_3').html('');
        $.ajax({
            type: "POST",
            url: '<?php echo APP_URL; ?>dashboard/' + url,
            data: dataStr,
            dataType: 'html',
            success: function(data) {
                    $("#"+div_id).html(data);
            },
            beforeSend:function(){
                    
                    $("#"+div_id).html("<center><div id='loadingmessage'><img src='<?php echo PUBLIC_URL; ?>images/ajax-loader.gif'/></div></center>");
                    $("#"+div_id).parent().show();
            }
        });
    
    }
    </script>