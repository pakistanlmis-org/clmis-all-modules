<?php
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");

//fetch stk list
$qry = "SELECT
            fq_calculation_indicators.pk_id as indicator_id,
            fq_calculation_indicators.indicator_name,
            fq_calculation_indicators.first_label,
            fq_calculation_indicators.second_label,
            fq_calculation_indicators.formula,
            fq_calculation_indicators.indicator_value,
            fq_calculation_indicators.indicator_unit,
            fq_calculation_indicators.description,
            fq_calculation_indicators.age_group_id,
            fq_calculation_indicators.rank,
            fq_calculation_indicators.calculate_on,
            fq_calculation_indicators.section,
            fq_calculation_indicators.type,
            fq_calculation_indicators.product_id,
            itminfo_tab.itm_name,
            fq_age_groups.age_group_name,
            fq_clinical_conditions.short_name,
            fq_age_groups.clinical_condition_id
        FROM
            fq_calculation_indicators
        INNER JOIN itminfo_tab ON fq_calculation_indicators.product_id = itminfo_tab.itm_id
        INNER JOIN fq_age_groups ON fq_calculation_indicators.age_group_id = fq_age_groups.pk_id
        INNER JOIN fq_clinical_conditions ON fq_age_groups.clinical_condition_id = fq_clinical_conditions.pk_id

";
$rsQry = mysql_query($qry);
$data_arr = $cc_arr = $age_group_arr = $prod_arr = $row_counts_arr = array();
while ($row = mysql_fetch_assoc($rsQry)) {
    $data_arr[$row['clinical_condition_id']][$row['age_group_id']][$row['product_id']][$row['indicator_id']] = $row;
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
                        echo '<td align="center"><b>Indicator ID</b></td>';
                        echo '<td align="center"><b>Indicator Name</b></td>';
                        echo '<td align="center" colspan="3"><b>Formula</b></td>';
                    echo '</tr>';
                    foreach($data_arr as $cc_id => $cc_data){

                        $rowspan = $row_counts_arr[$cc_id]+count($data_arr[$cc_id])+1; 

                        echo '<tr>';
                        echo '<td style="background-color:#73BA73;color:#fff;font-size:16px;"  rowspan="'.$rowspan.'"><b>'.$cc_arr[$cc_id].'</b></td>';
                        echo '<td style="background-color:#73BA73;color:#fff;"  colspan="20"><b>'.$cc_arr[$cc_id].'</b></td>';
                        foreach($cc_data as $age_group_id => $age_group_data){
                            echo '<tr>';
                            echo '<td style="background-color:#E0D8E8;font-size:14px;"  colspan="20"><b>'.$age_group_arr[$age_group_id].'</b></td>';
                            $old_prod='';
                            foreach($age_group_data as $prod_id => $prod_data){
                                foreach($prod_data as $ind_id => $ind_data){
                                    echo '<tr>';
                                    $r_sp = count($prod_data);
                                        if($old_prod != $prod_id )
                                        echo '<td rowspan="'.$r_sp.'">'.$prod_arr[$prod_id].'</td>';
                                        echo '<td>'.$ind_data['indicator_id'].'</td>';
                                        echo '<td>'.$ind_data['indicator_name'].'</td>';
                                        echo '<td>'.$ind_data['formula'].'</td>';
                                        //echo '<td>'.$ind_data['indicator_value'].'</td>';
                                        //echo '<td>'.$ind_data['indicator_unit'].'</td>';
                                        //echo '<td>'.$ind_data['calculate_on'].'</td>';
                                    echo '</tr>';
                                    $old_prod = $prod_id;
                                }
                            
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