
<?php
    $params = "";
    if(isset($_REQUEST['date']))
        $params .= "?date=".$_REQUEST['date'];
    if(isset($_REQUEST['prov']))
        $params .= "&prov=".$_REQUEST['prov'];
    if(isset($_REQUEST['dist']))
        $params .= "&dist=".$_REQUEST['dist'];
    if(isset($_REQUEST['stk']))
        $params .= "&stk=".$_REQUEST['stk'];
?>

<a href="../data_reconcile/reconcile_hf_type_data_all.php<?=$params?>">(*). Reconcile ALL hfdata with hftypedata</a> || 
<a href="data_comparison_hf_data_hf_type_data.php<?=$params?>">(1). Compare HF_Data with HF_TYPE_DATA</a> || 
<a href="data_comparison_hf_data_wh_data.php<?=$params?>">(2). Compare HF_Data with WH_DATA</a> || 
<a href="data_comparison_dist_field.php<?=$params?>">(3). Compare District Summary with HF_Data (FIELD)</a> || 
<a href="data_comparison_dist_store_soh.php<?=$params?>">(4). Compare District Summary with wh data (DIST STORES)</a> || 
<a href="data_comparison_province.php<?=$params?>">(5). Compare HF_DATA with Province Summary</a> || 
<a href="data_comparison_national.php<?=$params?>">(6). Compare HF_DATA with National Summary</a> || 
<a href="data_comparison_closing_opening_bal.php<?=$params?>">(7). Compare Opening Bal with Last Closing Bal</a> || 
<a href="closing_calculations.php<?=$params?>">(8). Closing Balance Calculations (OB + R - I = CB ) tbl_hf_data</a> || 
<a href="clear_mismatches_queue.php<?=$params?>">DELETE QUEUE</a> || 
<a href="index.php">MAIN MENU</a> || 