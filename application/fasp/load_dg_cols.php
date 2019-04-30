<?php

/**
 * load_districts
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../includes/classes/AllClasses.php");
$stkFilter = '';
if (isset($_REQUEST['task_order'])) {
    $qry = "SELECT
                fq_cols.pk_id,
                fq_cols.item_group,
                fq_cols.short_name,
                fq_cols.long_name
            FROM
                fq_cols
            WHERE
                /*fq_cols.item_group = '".$_REQUEST['task_order']."' AND*/
                fq_cols.col_type = 'main' AND
                fq_cols.is_active = 1
            ORDER BY
                fq_cols.order_by ASC
";
    //query result
    $qryRes = mysql_query($qry);
    while ($row = mysql_fetch_array($qryRes)) {
        echo '<option value="'.$row['pk_id'].'">'.$row['long_name'].'</option>';
    }
}