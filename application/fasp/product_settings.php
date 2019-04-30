<?php
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");

//fetch stk list
$qry = "SELECT
            fq_item_settings.form_of_prod,
            fq_item_settings.qty_per_episode,
            fq_item_settings.treatment_duration_days,
            fq_item_settings.size_of_prod,
            fq_item_settings.unit_of_prod,
            fq_age_groups.age_group_name,
            fq_clinical_conditions.short_name,
            fq_clinical_conditions.full_name,
            itminfo_tab.itm_name,
            tbl_product_category.ItemCategoryName,
            fq_item_settings.age_group_id,
            fq_item_settings.product_id,
            fq_age_groups.clinical_condition_id
        FROM
            fq_item_settings
        INNER JOIN fq_age_groups ON fq_item_settings.age_group_id = fq_age_groups.pk_id
        INNER JOIN fq_clinical_conditions ON fq_age_groups.clinical_condition_id = fq_clinical_conditions.pk_id
        INNER JOIN itminfo_tab ON fq_item_settings.product_id = itminfo_tab.itm_id
        INNER JOIN tbl_product_category ON itminfo_tab.itm_category = tbl_product_category.PKItemCategoryID
        WHERE
            fq_clinical_conditions.is_active = 1
        ORDER BY
            fq_clinical_conditions.rank
";
$rsQry = mysql_query($qry);
$data_arr = $cc_arr = $age_group_arr = $prod_arr = $row_counts_arr = array();
while ($row = mysql_fetch_assoc($rsQry)) {
    $data_arr[$row['clinical_condition_id']][$row['age_group_id']][$row['product_id']] = $row;
    $cc_arr[$row['clinical_condition_id']] = $row['short_name'];
    $age_group_arr[$row['age_group_id']] = $row['age_group_name'];
    $prod_arr[$row['product_id']] = $row['itm_name'];
    
    if(empty($row_counts_arr[$row['clinical_condition_id']]))$row_counts_arr[$row['clinical_condition_id']]=0;
    $row_counts_arr[$row['clinical_condition_id']] ++;
}
?>

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
    <div class="page-content-wrapper">
        <div class="page-content">
            
                    <div class="page-title row-br-b-wp">  
                        <i class="fa fa-cogs font-green-jungle"></i>
                        <span class="caption-subject font-green-sharp bold  ">Forecast Base Settings</span>
                        <span class="caption-helper">Products / Clinical Conditions</span>
                        <?php include("back_include.php"); ?>
                    </div>
            <div class="portlet ">
                
                <div class="portlet-body ">
                    
                <table class="table table-bordered table-condensed">
                <?php
                    //echo '<pre>';print_r($data_arr);
                    echo '<tr style="background-color:#03A303;color:#fff;" >';
                        echo '<td align="center"><b>Clinical Condition</b></td>';
                        echo '<td align="center"><b>Product Name</b></td>';
                        echo '<td align="center"><b>Form of Product</b></td>';
                        echo '<td align="center"><b>Qty per Episode</b></td>';
                        echo '<td align="center"><b>Treatment Duration in Days</b></td>';
                        echo '<td align="center"><b>Size of Product</b></td>';
                        echo '<td align="center"><b>Unit of Measure</b></td>';
                    echo '</tr>';
                    foreach($data_arr as $cc_id => $cc_data){

                        $rowspan = $row_counts_arr[$cc_id]+count($data_arr[$cc_id])+1; 

                        echo '<tr>';
                        echo '<td style="background-color:#73BA73;color:#fff;font-size:16px;"  rowspan="'.$rowspan.'"><b>'.$cc_arr[$cc_id].'</b></td>';
                        echo '<td style="background-color:#73BA73;color:#fff;"  colspan="20"><b>'.$cc_arr[$cc_id].'</b></td>';
                        foreach($cc_data as $age_group_id => $age_group_data){
                            echo '<tr>';
                            echo '<td style="background-color:#E0D8E8;font-size:14px;"  colspan="20"><b>'.$age_group_arr[$age_group_id].'</b></td>';
                            foreach($age_group_data as $prod_id => $prod_data){
                                echo '<tr>';
                                    echo '<td>'.$prod_arr[$prod_id].'</td>';
                                    echo '<td><input readonly class="form-control form-control-sm" name="form_of_prod" value="'.$prod_data['form_of_prod'].'"></td>';
                                    echo '<td><input readonly class="form-control form-control-sm" name="form_of_prod" value="'.$prod_data['qty_per_episode'].'"></td>';
                                    echo '<td><input readonly class="form-control form-control-sm" name="form_of_prod" value="'.$prod_data['treatment_duration_days'].'"></td>';
                                    echo '<td><input readonly class="form-control form-control-sm" name="form_of_prod" value="'.$prod_data['size_of_prod'].'"></td>';
                                    echo '<td><input readonly class="form-control form-control-sm" name="form_of_prod" value="'.$prod_data['unit_of_prod'].'"></td>';
                                echo '</tr>';
                            }
                            echo '</tr>';
                        }

                        echo '</tr>';
                    }
                ?>
                </table>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include PUBLIC_PATH . "/html/footer.php";
?>
</body>
</html>