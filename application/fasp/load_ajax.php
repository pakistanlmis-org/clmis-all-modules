<?php
include("../includes/classes/AllClasses.php");

if (isset($_REQUEST['get_medicines'])) {
    $qry = "SELECT
                itminfo_tab.itm_name,
                fq_clinical_conditions.short_name,
                fq_age_groups.age_group_name,
                fq_calculation_indicators.indicator_name,
                itminfo_tab.itm_id
            FROM
                fq_calculation_indicators
            INNER JOIN fq_age_groups ON fq_age_groups.pk_id = fq_calculation_indicators.age_group_id
            INNER JOIN fq_clinical_conditions ON fq_clinical_conditions.pk_id = fq_age_groups.clinical_condition_id
            INNER JOIN itminfo_tab ON fq_calculation_indicators.product_id = itminfo_tab.itm_id
            WHERE
                fq_clinical_conditions.pk_id = ".$_REQUEST['clinical_condition']."
            group BY
                itminfo_tab.itm_id
        ";
    //query result
    $qryRes = mysql_query($qry);
    while ($row = mysql_fetch_array($qryRes)) {
        echo '<option value="'.$row['itm_id'].'">'.$row['itm_name'].'</option>';
    }
}

if (isset($_REQUEST['get_medicines_names'])) {
    $qry = "SELECT
                itminfo_tab.itm_name,
                fq_clinical_conditions.short_name,
                fq_age_groups.age_group_name,
                fq_calculation_indicators.indicator_name,
                itminfo_tab.itm_id
            FROM
                fq_calculation_indicators
            INNER JOIN fq_age_groups ON fq_age_groups.pk_id = fq_calculation_indicators.age_group_id
            INNER JOIN fq_clinical_conditions ON fq_clinical_conditions.pk_id = fq_age_groups.clinical_condition_id
            INNER JOIN itminfo_tab ON fq_calculation_indicators.product_id = itminfo_tab.itm_id
            WHERE
                fq_clinical_conditions.pk_id = ".$_REQUEST['clinical_condition']."
            group BY
                itminfo_tab.itm_id
        ";
    //query result
    $qryRes = mysql_query($qry);
    $med = array();
    while ($row = mysql_fetch_array($qryRes)) {
        $med[] =  '<span class="label label-success"><i class="fa fa-arrow-right"></i> '.$row['itm_name'].'</span>';
    }
    echo implode(' ',$med);
}

if (isset($_REQUEST['get_age_groups'])) {
    $qry = "SELECT
                fq_clinical_conditions.short_name,
                fq_age_groups.pk_id,
                fq_age_groups.age_group_name,
                fq_age_groups.rank
            FROM
                fq_clinical_conditions
            INNER JOIN fq_age_groups ON fq_age_groups.clinical_condition_id = fq_clinical_conditions.pk_id
            WHERE
                fq_age_groups.clinical_condition_id = ".$_REQUEST['clinical_condition']."
            order by 
                fq_age_groups.rank

        ";
    //query result
    $qryRes = mysql_query($qry);
    while ($row = mysql_fetch_array($qryRes)) {
        echo '<option value="'.$row['pk_id'].'">'.$row['age_group_name'].'</option>';
    }
}