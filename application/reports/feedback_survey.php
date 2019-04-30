<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");


//****************************
//Quick Query Settings
$title= 'Feedback Survey';
//****************************

?>
<html>
    <h3 align="center"><?=$title?></h3>
    <body>
<?php

$qry_summary_dist= "
    SELECT

        sysuser_tab.usrlogin_id,
        survey.`name`,
        survey.q1_data_difficulty as problem_in_data_entry,
        survey.q2_report as remarks_about_reports,
        survey.`comment`,
        /*survey.q1_y_n as difficulty_in_data_entry,
        survey.q2_y_n as using_any_report,*/
        survey.created_date
        FROM
        survey
        INNER JOIN sysuser_tab ON survey.created_by = sysuser_tab.UserID
        ORDER BY
        survey.pk_id DESC


";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);
$display_data  = $columns_data = array();

while($row = mysql_fetch_assoc($Res2))
{
   $display_data[] = $row;
   $row2=$row;
   //echo '<pre>';print_r($row);
}

foreach($row2 as $k=>$v)
{
   $columns_data[] = $k;
}
//echo '<pre>';print_r($columns_data);print_r($display_data);
?>
<table border="1" class="table table-condensed table-striped left" >
    <tr bgcolor="#afb5ea">
        <?php
        echo '<td>#</td>';
        foreach($columns_data as $k=>$v)
        {
           echo '<td>'.$v.'</td>';
        }
        ?>
    </tr>
    
    <?php
    $count_of_row = 0;
        foreach($display_data as $k => $disp)
        {
           echo '<tr>';
           echo '<td>'.++$count_of_row.'</td>';
           foreach($columns_data as $k2=>$col)
           {
               if($col=='comment'){
                    echo ' <td ><i>'.$disp[$col].'</i></td>';
               }else
               {
                    echo ' <td >'.$disp[$col].'</td>';
               }
           }   
           echo '<tr>';
        }
        ?>
</table>
    </body>
    
<script src="<?php echo PUBLIC_URL;?>js/jquery-1.4.4.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>
</html>
