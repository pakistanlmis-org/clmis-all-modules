<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");


//****************************
//Quick Query Settings
$title= 'Commodity Security - Reasons and Comments';
//****************************
$month = date('Y-m-01');
if(!empty($_REQUEST['month']))
@$month = $_REQUEST['month'];
$month = date('Y-m-01',strtotime($month));
?>
<html>
    <h3 align="center"><?=$title?></h3>
    <body>
        <form action="">
            Month (yyyy-mm-01):<input name="month" value="<?=$month?>">
            <input type="submit">
        </form>
<?php

$qry_summary_dist= "
    SELECT
            pro.LocName AS province_name,
            tbl_locations.LocName as district_name,
stakeholder.stkname,
            tbl_warehouse.wh_name,
            itminfo_tab.itm_name,
            stock_out_reasons.`month`,(
                    SELECT
                            GROUP_CONCAT(list_detail.list_value) as reasons
                    FROM
                            list_detail
                    WHERE
                            /*list_detail.pk_id IN (stock_out_reasons.reason)*/
                            FIND_IN_SET(list_detail.pk_id,stock_out_reasons.reason) 

            ) as reasons,
            (
                    SELECT
                            GROUP_CONCAT(list_detail.list_value) as reasons
                    FROM
                            list_detail
                    WHERE
                            FIND_IN_SET(list_detail.pk_id,stock_out_reasons.action_suggested) 

            ) as actions,
            stock_out_reasons.comments
        FROM
        stock_out_reasons
        INNER JOIN tbl_warehouse ON stock_out_reasons.wh_id = tbl_warehouse.wh_id
        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
        INNER JOIN itminfo_tab ON stock_out_reasons.itm_id = itminfo_tab.itm_id
        INNER JOIN tbl_locations AS pro ON tbl_warehouse.prov_id = pro.PkLocID
INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
        WHERE
            stock_out_reasons.`month` = '".$month."' 
        order by 
                pro.LocName  ,
                tbl_locations.LocName ,
                tbl_warehouse.wh_name,
                itminfo_tab.itm_name

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
               $this_val = str_replace(',', '<br/>', $disp[$col]);
            echo ' <td>'.$this_val.'</td>';
           }   
           echo '<tr>';
        }
        ?>
</table>
    </body>
    
<script src="<?php echo PUBLIC_URL;?>js/jquery-1.4.4.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>
</html>
