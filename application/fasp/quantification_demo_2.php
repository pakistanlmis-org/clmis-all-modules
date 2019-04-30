<?php
/**
 * quantification
 * @package fasp
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
include(PUBLIC_PATH . "html/header.php");
//echo '<pre>';print_r($_REQUEST);exit;
$office_level = (!empty($_REQUEST['office_level']))?$_REQUEST['office_level']:'';
$source = (!empty($_REQUEST['source']))?$_REQUEST['source']:'';
$selYear = (!empty($_REQUEST['year']))?$_REQUEST['year']:'';
//if(empty($selYear))  $selYear = date('Y');

$prod_base_years = array();

//fetch the forecasting master data
$qry = "SELECT
            fq_master_data.pk_id,
            fq_master_data.base_year,
            fq_master_data.start_year,
            fq_master_data.end_year,
            fq_master_data.purpose,
            fq_master_data.source,
            fq_master_data.stakeholders,
            fq_master_data.province_id,
            fq_master_data.forecasting_methods,
            fq_master_data.item_group,
            tbl_locations.LocName
        FROM
            fq_master_data
        INNER JOIN tbl_locations ON fq_master_data.province_id = tbl_locations.PkLocID
        WHERE fq_master_data.pk_id = " . $_REQUEST['forecasted_id'] . " ";
//query result
$rsQry = mysql_query($qry) or die();
//fetch result
$c = 1;
$fq_master = mysql_fetch_assoc($rsQry);


$years_arr = array();
$years_count = 0;
for ($i = $fq_master['start_year']; $i <= $fq_master['end_year']; $i++) {
    $years_arr[$i] = $i;
    $years_count++;
}

//fetch stk list
$qry = "SELECT
                stakeholder.stkid,
                stakeholder.stkname
            FROM
                stakeholder
            WHERE
                stakeholder.is_reporting = 1 AND
                stakeholder.stk_type_id = 0 AND
                stakeholder.ParentID IS NULL
    ";
$rsQry = mysql_query($qry);
$stk_arr = array();
while ($row = mysql_fetch_array($rsQry)) {
    $stk_arr[$row['stkid']] = $row['stkname'];
}

$qry = "
 

SELECT
            fq_master_data.pk_id,
            fq_master_data.base_year as `year`,
            'Forecasting' as reference,
            '2' as `level`,
            fq_master_data.province_id as level_id,
            fq_master_data.item_group,
            tbl_locations.LocName,
            'Provincial' as lvl_name
        FROM
            fq_master_data
        INNER JOIN tbl_locations ON fq_master_data.province_id = tbl_locations.PkLocID
         
        ORDER BY fq_master_data.pk_id desc
        ";
//query result
$rsQry = mysql_query($qry) or die();
//fetch result
$forecasting_opts = $fc_name='';
$forecasting_data  = array();
while ($row = mysql_fetch_assoc($rsQry)) {
    //pipulate province combo
    $sel ='';
    if(isset($_REQUEST['forecasted_id']) && $_REQUEST['forecasted_id'] == $row['pk_id']){ 
        $sel="selected";
        $forecasting_data = $row;
    }
    
    $forecasting_opts .= '<option value="'.$row['pk_id'].'" '.$sel.'>'.$row['reference'].' ('.$row['pk_id'].') </option>';
}
$task_order = (isset($_REQUEST['forecasted_id']))?$forecasting_data['item_group']:'';
//echo '<pre>';print_r($forecasting_data);exit;
if($task_order=='to3') 
{
    $task_order_full_name = 'Family Planning';
    $itm_cat = '1';
}    
if($task_order=='to4'){
    $task_order_full_name = 'MNCH';
    $itm_cat = '5';
}


$qry_f = "SELECT
            funding_stk_prov.funding_source_id
            FROM
            funding_stk_prov
            WHERE
            funding_stk_prov.province_id = ".$forecasting_data['level_id'];
$res_f = mysql_query($qry_f);
$funding_stks=array();
while($row_f=mysql_fetch_assoc($res_f))
{
    $funding_stks[$row_f['funding_source_id']]=$row_f['funding_source_id'];
}
//
//$qry = "SELECT
//            list_master.pk_id as master_id,
//            list_master.list_master_name,
//            list_detail.list_value,
//            list_detail.pk_id as list_id,
//            list_detail.description,
//            list_detail.rank
//        FROM
//             list_master
//        INNER JOIN list_detail ON list_detail.list_master_id = list_master.pk_id
//        WHERE
//            list_master.pk_id IN (22,23)";
//$rsQry = mysql_query($qry);
//$list_arr = array();
//while ($row = mysql_fetch_assoc($rsQry)) 
//{
//    $list_arr[$row['master_id']][$row['list_id']]  = $row;
//}
//echo '<pre>';print_r($list_arr);exit;


$all_cols = array();
$all_cols[$task_order]['soh']['short_name'] = 'B';
$all_cols[$task_order]['soh']['long_name'] = 'Stock on hand SOH';
$all_cols[$task_order]['forecast']['short_name'] = 'A';
$all_cols[$task_order]['forecast']['long_name'] = 'Proposed Forecast';
$all_cols[$task_order]['pipeline']['short_name'] = 'C';
$all_cols[$task_order]['pipeline']['long_name'] = 'Stock in pipeline';
$all_cols[$task_order]['quantification']['short_name'] = 'D';
$all_cols[$task_order]['quantification']['long_name'] = 'Quantification';
$all_cols[$task_order]['quantification']['formula'] = 'A-(B+C)';
//$all_cols[$task_order]['unit']['short_name'] = 'E';
//$all_cols[$task_order]['unit']['long_name'] = 'Unit of Measure UOM';
//$all_cols[$task_order]['orderFrequency']['short_name'] = 'F';
//$all_cols[$task_order]['orderFrequency']['long_name'] = 'Order Frequency';
//$all_cols[$task_order]['price']['short_name'] = 'G';
//$all_cols[$task_order]['price']['long_name'] = 'Price';
//$all_cols[$task_order]['amount']['short_name'] = 'H';
//$all_cols[$task_order]['amount']['long_name'] = 'Amount';
//$all_cols[$task_order]['amount']['formula'] = 'I=DxG';
$all_cols[$task_order]['remarks']['short_name'] = 'I';
$all_cols[$task_order]['remarks']['long_name'] = 'Remarks';


$disabled = '';
if(isset($_REQUEST['forecasted_id']))
{
    $disabled = ' disabled ';
    
    
    $qry = "SELECT
                    itminfo_tab.itmrec_id,
                    itminfo_tab.itm_id,
                    itminfo_tab.itm_name,
                    itminfo_tab.method_type
                FROM
                    itminfo_tab
INNER JOIN fq_fp_products_data ON fq_fp_products_data.prod_id = itminfo_tab.itm_id
                WHERE
                    itminfo_tab.method_type IS NOT NULL AND
                    itminfo_tab.itm_category = $itm_cat
                       AND  fq_fp_products_data.master_id = '".$_REQUEST['forecasted_id']."'
                ORDER BY
                    itminfo_tab.method_rank ASC";
    $rsQry = mysql_query($qry);
    $all_prods = array();
    while ($row = mysql_fetch_array($rsQry)) 
    {
        $all_prods[$task_order][$row['itm_id']]  = $row['itm_name'];
    }

$qry = "SELECT
            distinct fq_master_data.pk_id,
            fq_master_data.base_year,
            fq_master_data.start_year,
            fq_master_data.end_year,
            fq_master_data.purpose,
            fq_master_data.source,
            fq_master_data.stakeholders,
            fq_master_data.province_id,
            fq_master_data.forecasting_methods,
            fq_master_data.item_group,
            tbl_locations.LocName,
            fq_fp_products_data.base_year_1,
            fq_fp_products_data.base_year_2,
            fq_fp_products_data.base_year_3,
            itminfo_tab.itm_name,
                    fq_fp_products_data.average_amc_of_base_years
        FROM
            fq_master_data
        INNER JOIN tbl_locations ON fq_master_data.province_id = tbl_locations.PkLocID
        LEFT JOIN fq_fp_products_data ON fq_master_data.pk_id = fq_fp_products_data.master_id
        LEFT JOIN fq_fp_products_forecasting ON fq_fp_products_forecasting.fp_product_key = fq_fp_products_data.pk_id
        LEFT JOIN itminfo_tab ON fq_fp_products_data.prod_id = itminfo_tab.itm_id
        WHERE fq_master_data.pk_id = '".$_REQUEST['forecasted_id']."' ";
//query result
//echo $qry;exit;
$rsQry = mysql_query($qry) or die();
//fetch result
$c = 1;
$prod_base_years = array();
while ($row = mysql_fetch_assoc($rsQry)) {
    $b = '<span class="portlet">' . $row['itm_name'] . ' :  &nbsp; &nbsp; &nbsp; ';
    $b .= '   <a class="btn green btn-xs btn-round">' . $row['base_year_1'] . '-' . substr($row['base_year_1'] + 1, 2) . '</a>';
    $b .= ' , <a class="btn green btn-xs btn-round">' . $row['base_year_2'] . '-' . substr($row['base_year_2'] + 1, 2) . '</a>';
    if (!empty($row['base_year_3']))
        $b .= ' , <a class="btn green btn-xs btn-round">' . $row['base_year_3'] . '-' . substr($row['base_year_3'] + 1, 2) . '</a>';

    $b .= '   AMC : (' . number_format($row['average_amc_of_base_years']) . ')';
    $b .= '<hr/></span>';

    $prod_base_years[] = $b;
    //because master data is same
    $fq_master = $row;
}
    $fetched_data = array();
    $qry = "	SELECT
	fq_fp_products_data.pk_id,
	fq_fp_products_data.prod_id,
	fq_fp_products_forecasting.`year`,
	fq_fp_products_forecasting.forecasted_val,
	fq_master_data.pk_id
	FROM
	fq_master_data
	LEFT JOIN fq_fp_products_data ON fq_fp_products_data.master_id = fq_master_data.pk_id
	LEFT JOIN fq_fp_products_forecasting ON fq_fp_products_forecasting.fp_product_key = fq_fp_products_data.pk_id
	WHERE
	fq_master_data.pk_id = '".$_REQUEST['forecasted_id']."'
            ";
    //query result
    //echo $qry;exit;
    $rsQry = mysql_query($qry) or die('err forecast');
    
    while ($row = mysql_fetch_assoc($rsQry)) {
        $fetched_data[$task_order]['forecast'][$row['prod_id']][$row['year']] = $row['forecasted_val'];
        
    }
    //echo '<pre>';print_r($fetched_data);exit;
    $qry = "
		SELECT
                        itminfo_tab.itm_name,
                        SUM(tbl_stock_detail.Qty) AS soh,
                        tbl_itemunits.UnitType,
                        itminfo_tab.itm_id
                FROM
                        stock_batch
                INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                WHERE
                (
                        tbl_stock_master.WHIDFrom = 123
                        OR tbl_stock_master.WHIDTo = 123
                )
                AND stock_batch.funding_source IN  (".implode(',',$funding_stks).") 
                AND tbl_stock_master.temp = 0
                GROUP BY
                        itminfo_tab.itm_id
                ORDER BY
                        itminfo_tab.frmindex
            ";
    //query result
    $rsQry = mysql_query($qry) or die('err forecast');
    
    while ($row = mysql_fetch_assoc($rsQry)) {
        $fetched_data[$task_order]['soh'][$row['itm_id']] = $row['soh'];
    }
    
    
    foreach ($years_arr as $yk => $year) 
    {

        $qry_6 = "

                SELECT
                
                    shipments.pk_id,shipments.shipment_date,
                    itminfo_tab.itm_id,
                    (shipments.shipment_quantity) as shipment_quantity,
                    sum(tbl_stock_detail.Qty) as received_qty
                FROM
                        shipments
                INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
                INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
                INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
                LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = shipments.pk_id
                LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                WHERE
                    DATE_FORMAT(shipments.shipment_date,'%Y') = '".$year."' 
                    AND shipments.status NOT IN ('Cancelled','Received')

                    AND shipments.stk_id IN  (".implode(',',$funding_stks).") 
                
                GROUP BY
                    shipments.pk_id,
                    itminfo_tab.itm_id

                        ";
//    echo $qry_6;exit;

        $res_6 = mysql_query($qry_6);
        $pipeline_detail_arr = array();
        while ($row_6 = mysql_fetch_assoc($res_6)) {
            $pipe_qty = $row_6['shipment_quantity'] - $row_6['received_qty'];

            if(empty($fetched_data[$task_order]['pipeline'][$row_6['itm_id']][$year]))$fetched_data[$task_order]['pipeline'][$row_6['itm_id']][$year]=0;

            $fetched_data[$task_order]['pipeline'][$row_6['itm_id']][$year] += $pipe_qty;
        }
    }
}
//echo '<pre>';print_r($fetched_data);exit;
?>

<style>
    .nav-pills > li > a:hover,.nav-pills > li.active > a{
        color: #FFFFFF !important;
        background-color: #009C00 !important;
    } 
    .nav-pills > li > a{
        color: #000 !important;
        background-color: #FFFFFF !important;
    }
    .grid_input_number{
        padding: 0px !important;
        font-size: 11px !important;
    }
</style>
    
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im	
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <a href="../../../../../Users/Ahmed/AppData/Local/Temp/Gate Pass.url"></a>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">
                            Quantification
                        <?php include("back_include.php"); ?> 

                        </h3>
                                <form name="frm" id="frm" action="" method="post" role="form">
                                    
                        <div id="main_div" class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Forecasting Detail</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="mnch_forecasting_calculator_action.php" method="post" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-condensed ">
                                                <tr style="display:none;">
                                                    <td width="20%">
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Forecasting Reference</label>
                                                        </div>
                                                    </td>
                                                    <td colspan="3">
                                                        <div class="control-group">
                                                            <label><?= $reference_txt ?></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Purpose</label>
                                                        </div>
                                                    </td>
                                                    <td colspan="3">
                                                        <div class="control-group">
                                                            <label class=""><?= $fq_master['purpose'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>    

                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Forecast on Base Year</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= $fq_master['base_year'] ?></label>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Product Category</label>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= (($fq_master['item_group'] == 'to3') ? 'Family Planning' : 'MNCH') ?></label>
                                                        </div>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Starting From Year</label>
                                                        </div>
                                                    </td>
                                                    <td  width="30%">
                                                        <div class="control-group">
                                                            <label><?= $fq_master['start_year'] ?></label>
                                                        </div>


                                                    </td>
                                                    <td width="30%">
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">To (End Year)</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= $fq_master['end_year'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>

                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Data Sources</label>

                                                        </div>
                                                    </td>
                                                    <td  colspan="">
                                                        <div class="control-group">
                                                            <label>LMIS<?php //$fq_master['source'] ?></label>
                                                        </div>
                                                    </td>
                                                    <td>

                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Method</label>

                                                        </div>
                                                    </td>
                                                    <td  colspan="">
                                                        <div class="control-group">
                                                            <label>Consumption</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Stakeholder/s</label>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label>
<?php
$st = explode(',', $fq_master['stakeholders']);
$st2 = array();
foreach ($st as $k => $stk_id) {
    $st2[] = $stk_arr[$stk_id];
}
echo implode(',', $st2);
?>
                                                            </label>
                                                        </div>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Province</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label><?= $fq_master['LocName'] ?></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="control-group">
                                                            <label class="caption-subject font-green bold  ">Base Years</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="control-group">
                                                            <label>
                                                                <?php
                                                                echo implode("  <br/> ", $prod_base_years);
                                                                ?>
                                                            </label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table> 


                                        </div>
                                    </div>
                                </form>


                            </div>
                        </div>
                            
                                    </form>
                                    <?php
                                    if(isset($_REQUEST['forecasted_id']) )
                                    {
                                        
                                    ?>
                                <form name="frm2" id="frm2" action="quantification_action.php" method="post" role="form">
                                    <input type="hidden" name="forecasted_id"      value="<?=(isset($_REQUEST['forecasted_id']))?$_REQUEST['forecasted_id']:''?>" >
                                    <div class="row">
                                        <div class="col-md-12">
                                              
                                        <div class="widget" data-toggle="collapse-widget">
                                            <div class="widget-head">
                                                <h3 class="heading">Quantification</h3>
                                            </div>
                                            <div class="widget-body">
                                                        <div class="row">
				
								<div class="col-md-12">
                                                                     <?php
                                                                        foreach ($years_arr as $yk => $year) {
                                                                            if(!empty($year))
                                                                            {
                                                                            echo '<div class="col-md-1">';
                                                                            echo '<a  class ="btn '.(($year==$selYear)?'btn-primary':'btn-default').'" href="quantification.php?forecasted_id='.$_REQUEST['forecasted_id'].'&year='.$year.'">'.$year.'</a>';
                                                                            echo '</div>';
                                                                            }
                                                                        }
                                                                        ?>
                                                                </div>
                                                                    
                                                        </div>
                                                
                                                        <div class="row">
				
								<div class="col-md-12">
                                                                    <?php if(!empty($selYear)) { ?>
                                                            
                                                                    <div class="  " id="">
                                                                        <div class="portlet  ">
                                                                            <div class="portlet-body" style="overflow-x:scroll; ">
                                                                                <table  class="table table-bordered table-condensed " style=" border: 1px solid;">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <td width="220px"> </td>
                                                                                            <td align="center"> </td>
                                                                                            <?php
//                                                                                            foreach ($years_arr as $yk => $year) {
//                                                                                                if($selYear == $year){
//                                                                                                    echo '<td align="center" colspan="3">'.$year.'</td>';
//                                                                                                }
//                                                                                            }
                                                                                            ?>
                                                                                        </tr> 
                                                                                       
                                                                                    </thead>
                                                                                    <tbody> 
 <tr class="info h4" >

      <td colspan="5" >  Product 1 ( Condom ) </td>

    </tr>
    <tr >

      <td colspan="3"></td>
     <td>Example</td>

    </tr>
    <tr>
      <td>Step 1</td>
      <td width="50%"><P>Estimate SOH at all levels of Distribution System</P></td>
      <td>5,000</td>
       <td>AMC</td>
    </tr>

    <tr>
      <td>Step 2</td>
      <td><P>Determine Stock on Order (pipeline stock)</P></td>
      <td>10,000</td>
       <td>15,000</td>
    </tr>

    <tr>
      <td>Step 3</td>
      <td><P>Forecast estimated consumption during the year</P></td>
      <td>66,000</td>
       <td>-51,000</td>
    </tr>

    <tr>
      <td>Step 4</td>
      <td><P>Estimate Adjustment during the year</P><P>(assuming 0.05%)</P></td>
      <td>250</td>
       <td>-51,250</td>
    </tr>
 
      <tr>
      <td>Step 5</td>
      <td>Determine Stock Levels</td>
      <td></td>
       <td>24 Months</td>
    </tr>

      <tr>
      <td>Step 6</td>
      <td><P>Determine  Desired Stock at end of Year</P></td>
      <td></td>
       <td>117,500</td>
    </tr>

      <tr>
      <td>Step 7</td>
      <td><P>Determine  Net Supply Requirement / Quantified Stock for Procurement</P><P>If the desired stock is in MINUS it indicates to start procurement immediately.</P></td>
      <td>168,750</td>
       <td>-168,750</td>
    </tr>
    
    
     <tr class="info h4" >

      <td colspan="5" >  Product 2 ( 3-Month Inj ) </td>

    </tr>
    <tr >

      <td colspan="3"></td>
     <td>Example</td>

    </tr>
    <tr>
      <td>Step 1</td>
      <td width="50%"><P>Estimate SOH at all levels of Distribution System</P></td>
      <td>5,000</td>
       <td>AMC</td>
    </tr>

    <tr>
      <td>Step 2</td>
      <td><P>Determine Stock on Order (pipeline stock)</P></td>
      <td>10,000</td>
       <td>15,000</td>
    </tr>

    <tr>
      <td>Step 3</td>
      <td><P>Forecast estimated consumption during the year</P></td>
      <td>66,000</td>
       <td>-51,000</td>
    </tr>

    <tr>
      <td>Step 4</td>
      <td><P>Estimate Adjustment during the year</P><P>(assuming 0.05%)</P></td>
      <td>250</td>
       <td>-51,250</td>
    </tr>
 
      <tr>
      <td>Step 5</td>
      <td>Determine Stock Levels</td>
      <td></td>
       <td>24 Months</td>
    </tr>

      <tr>
      <td>Step 6</td>
      <td><P>Determine  Desired Stock at end of Year</P></td>
      <td></td>
       <td>117,500</td>
    </tr>

      <tr>
      <td>Step 7</td>
      <td><P>Determine  Net Supply Requirement / Quantified Stock for Procurement</P><P>If the desired stock is in MINUS it indicates to start procurement immediately.</P></td>
      <td>168,750</td>
       <td>-168,750</td>
    </tr>
                                                                                </table>
                                                                                <table class="table table-bordered table-condensed " >
      
   <tr class="success h4" >

      <td colspan="5" >  Costing and Financial Gap Analysis </td>

    </tr>
    <tr>
      <td>Step 8</td>
      <td colspan="3"><P><b>Costing</b></P>

               <table border="1">
              <tr>

                  <td><p>S. No</p><p>1</p><p>2</p></td>

                  <td><p>Product</p><p>condom</p><p>3-month</p></td>

              <td><p>Unit Cost</p><p>1.97</p><p>71.5</p><p></p></td>

              <td><p>Total Cost-PKR</p><p>100,000</p><p>200,000</p></td>

              </tr>
              <tr><td colspan="3">Grand Total:</td><td>300,000</td></tr>

          </table>

      </td>


    </tr>



      <tr>
      <td>Step 9</td>
      <td colspan="3"> <b> Financial Gap Analysis</b>

          <table border="1">
              <tr>

                  <td><p>Available Budget</p><p>280,000</p></td>&nbsp;&nbsp;&nbsp;

                  <td><p>Required Budget</p><p>300,000</p></td>&nbsp;&nbsp;

              <td><p>Gap</p><p>-20,000</p></td>&nbsp;&nbsp;


              </tr>

          </table>

      </td>


    </tr>


                                                                                    </tbody>
                                                                                    
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row col-md-12">
                                                                            <div class="right"><input class="btn btn-green hide" type="submit" value="Save"></div>
                                                                        </div>
                                                                        
                                                                    </div>
                                                                    <?php } ?>
								</div>
							</div>
						</div>
                                                
					</div>
                                   
                                        </div>
                                    </div>
                                    
                                </form>
                                    <?php
                                    }
                                    if(isset($_REQUEST['forecasted_id']) && isset($already_saved))
                                    {
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12"> 
                                                 <div class="portlet light">
                                                    <div class="portlet-body">
                                                        Already 3 Demographic Data Entries have been saved for the year <b><?=$_REQUEST['year']?></b> of <b><?=$_REQUEST['source']?> ,  <?=($level_name)?></b> Level
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
        </div>
    </div>
    <?php 
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; ?>
    <script>
        $(document).ready(function() {
            $(".grid_input_number").keyup(function(event) {
//                if ($.isNumeric($(this).val())){
//                    $(this).attr('style', "border-radius: 1px; border:#00000 1px solid;");
//                }
//                else
//                {
//                    $(this).attr('style', "border-radius: 5px; border:#FF0000 1px solid;");
//                }
            });
            
            
            $(".price_column").keyup(function(event) {
                var price       = $(this).val();
                var prod_id     = $(this).data('prod-id');
                var quantification    = $('.quantification_column[data-prod-id='+prod_id+']').val().replace(/,/g, '');
                var pr        = parseFloat(price) 
                var qt         = parseFloat(quantification);
                var total       = pr * qt;

                 if (isNaN(total)) total =0;

                $('.amount_column[data-prod-id='+prod_id+']').val(total);
                console.log(pr+','+qt);
            });
        });
        
        $(function() {
            $('#office_level').change(function() {
                officeType($(this).val());
            });
//            $('#province').change(function() {
//                var provId = $(this).val();
//                showDistricts(provId);
//            });
            

            // Submit Form
            $('#submit').click(function(e) {
                $('body').addClass("loading");
            });
        });
        function officeType(officeLevel)
        {
            if (parseInt(officeLevel) == 2)
            {
                $('#province_div').hide();
                $('#district_div').hide();
            }
            else if (parseInt(officeLevel) == 3)
            {
                $('#province_div').show();
                //showDistricts($('#province').val());
            }
        }
        function showDistricts(provId)
        {
            var officeLevel = $('#office_level').val();
            if (officeLevel == 3)
            {
                $('#district_div').show();
                $('#district').html('<option value="">Loading...</option>');
                $.ajax({
                    type: "POST",
                    url: "ajax_calls.php",
                    data: {provinceId: provId, validate: 'yes'},
                    dataType: 'html',
                    success: function(data)
                    {
                        $('#district_data').html(data);
                    }
                });
            }
        }
        
        
		function showProvinces(pid) {
			var stk = $('#stk_sel').val();
			if (typeof stk !== 'undefined')
			{
				$.ajax({
					url: 'ajax_stk.php',
					type: 'POST',
					data: {stakeholder: stk, provinceId: pid, showProvinces: 1, showAllOpt: 0},
					success: function(data) {
						$('#province').html(data);
					}
				})
			}
		}
    </script>
    <?php
    if($_REQUEST['err']=='0')
    {
    ?>
    <script>
        var self = $('[data-toggle="notyfy"]');
        notyfy({
            force: true,
            text: 'Demographics Data Entry Saved Successfully.',
            type: 'success',
            layout: self.data('layout')
        });

    </script>
    <?php
    }
    ?>
</body>
</html>