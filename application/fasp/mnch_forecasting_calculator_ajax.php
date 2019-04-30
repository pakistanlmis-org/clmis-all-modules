<?php

include("../includes/classes/AllClasses.php");
//print_r($_REQUEST);exit;

$growth_rate = 2.1;
$debug=false;

$qry = "SELECT
            fq_calculation_indicators.pk_id,
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
            itminfo_tab.itm_name
        FROM
            fq_calculation_indicators
        LEFT JOIN itminfo_tab ON fq_calculation_indicators.product_id = itminfo_tab.itm_id
        WHERE
            fq_calculation_indicators.age_group_id = ".$_REQUEST['age_group']."
        ORDER BY 
        fq_calculation_indicators.rank
";
//echo $qry;
$rsQry = mysql_query($qry);
$ind_arr = $prods_arr = array();
while($row = mysql_fetch_assoc($rsQry)){
    $prods_arr[$row['product_id']] = $row['itm_name'];

    $ind_arr[$row['product_id']][$row['pk_id']] = $row;
}


//Fetch medicine settings

$qry = "SELECT
            fq_item_settings.pk_id,
            fq_item_settings.age_group_id,
            fq_item_settings.product_id,
            fq_item_settings.form_of_prod,
            fq_item_settings.qty_per_episode,
            fq_item_settings.treatment_duration_days,
            fq_item_settings.size_of_prod,
            fq_item_settings.unit_of_prod
        FROM
            fq_item_settings
        WHERE
            fq_item_settings.age_group_id = ".$_REQUEST['age_group']."
";
//echo $qry;
$rsQry = mysql_query($qry);
$prod_settings  = array();
while($row = mysql_fetch_assoc($rsQry)){
    $prod_settings[$row['product_id']] = $row;
    $quantity_per_episode[$row['product_id']] = $row['qty_per_episode'];
}
//$quantity_per_episode
//echo '<pre>';print_r($prod_settings);exit;


foreach($prods_arr as $p_id => $p_val){
//    if(FALSE && empty($p_id) || $p_id == ''){ 
//         $prod_year_arr[$p_id][$_REQUEST['base_year']] = $_REQUEST['base_year'];
//    }
//    else{
        for($i=$_REQUEST['start_year']; $i<=$_REQUEST['end_year'] ; $i++){
             $prod_year_arr[$p_id][$i] = $i;
        }
    
}

$total_population = array();
$last_year_pop = $_REQUEST['total_population'];
for($i=$_REQUEST['base_year']; $i<=$_REQUEST['end_year'] ; $i++){
    if($i == $_REQUEST['base_year']) 
        $total_population[$i]= $_REQUEST['total_population'] ;
    else
        $total_population[$i] = $last_year_pop + (($growth_rate / 100 ) * $last_year_pop);
    
    $last_year_pop = $total_population[$i];
}
//echo '<pre>';print_r($total_population);exit;


$ind_values_arr = $ind_total_val_arr = array();
foreach($ind_arr as $p_id => $p_val){
        foreach($p_val as $ind_id => $ind_val){
            foreach($prod_year_arr[$p_id] as $year => $py_v){
                
                    //$ind_values_arr[$ind_id] = 'X';
                    $formula = $ind_val['formula'];

                    $split_formula = array();
                    if (strpos($formula, '*')!=FALSE){
                        $split_formula = explode('*',$formula);
                    }
                    elseif (strpos($formula, '+')!=FALSE){
                        $split_formula = explode('+',$formula);
                    }

                     $var_1 = $split_formula[0];
                     $var_2 = $split_formula[1];

                     if($var_1 == 'total_population'){
                         $value_1 = $total_population[$year];
                     }
                     elseif(strpos($var_1, 'indicator')!==FALSE){
                         $index = explode('indicator',$var_1);
                         $value_1 = $ind_values_arr[$index[1]][$year];
                     }
                     elseif($var_1 == 'quantity_per_episode'){
                         $value_1 = $quantity_per_episode[$p_id];
                     }
                     else{
                         $value_1 = $var_1;
                     }




                     if($var_2 == 'total_population'){
                         $value_2 = $total_population[$year];
                     }
                     elseif(strpos($var_2, 'indicator')!==FALSE){
                         $index = explode('indicator',$var_2);
                         $value_2 = $ind_values_arr[$index[1]][$year];
                     }
                     elseif($var_2 == 'quantity_per_episode'){
                         $value_2 = $quantity_per_episode[$p_id];
                     }
                     else{
                         $value_2 = $var_2;
                     }


                     if($ind_id==7){
                         //echo strpos($var_1, 'indicator');
                         //echo $value_1;echo $value_2;
                         //exit;
                     }


                     if (strpos($formula, '*')!=FALSE){
                        $ind_values_arr[$ind_id][$year] = $value_1 * $value_2;
                     }
                     elseif (strpos($formula, '+')!=FALSE){
                        $ind_values_arr[$ind_id][$year] = $value_1 + $value_2;
                     }
                    if(empty($ind_total_val_arr[$ind_id]))$ind_total_val_arr[$ind_id]=0;
                    $ind_total_val_arr[$ind_id] += $ind_values_arr[$ind_id][$year];

        }
    }
}
//echo '<pre>';print_r($total_population);
//echo '<pre>';print_r($ind_values_arr);
//echo '<pre>';print_r($ind_arr);

?>
    <table  class="table table-bordered table-condensed" >
    <tbody>
          <tr style="background-color:#7ec97e;">
        <td>&nbsp;</td>
        <?=((($debug)?'<td>ID</td>':''))?>
        <td>Parameters</td>
        
        <?php
        if($debug) echo '<td>(Formula)</td>';
        for($i=$_REQUEST['start_year']; $i<=$_REQUEST['end_year'] ; $i++){
            echo '<td align="center">'.$i.'</td>';
        }
        ?>
        <td>Totals</td>
        </tr>   
    <?php
    
    $row_char = 'A';
    //section 1 of all products
    echo '<tr>';
    echo '<td>'.$row_char++.'</td>';
    echo '<td>Total Population (Projected)<span style="font-size:9x;color:grey"> Growth Rate: '.$growth_rate.'%</span></td>';
    foreach($prod_year_arr[$p_id] as $year => $py_v){
        echo '<td align="right">'.number_format($total_population[$year]).'</td>';
    }
    echo '</tr>';
    foreach($ind_arr[''] as $ind_id => $ind_val){
        echo '<tr>';
        echo '<td>'.$row_char++.'</td>';
        if($debug)  echo '<td>'.$ind_id.'</td>';
        echo '<td>'.$ind_val['first_label'].'</td>';
        if($debug) echo '<td>'.$ind_val['formula'].'</td>';
        
        foreach($prod_year_arr[$p_id] as $year => $py_v){
            
            if(!empty($ind_val['second_label'])){
                echo '<td align="right">'.$ind_val['indicator_value'].''.$ind_val['indicator_unit'].'</td>';
            }
            else{
                echo '<td align="right">'.number_format((isset($ind_values_arr[$ind_id][$year])?$ind_values_arr[$ind_id][$year]:''),2).'</td>';
            }
        }
        echo '<td align="right">'.((empty($ind_val['second_label']))?number_format($ind_total_val_arr[$ind_id],2):'').'</td>';
        echo '</tr>';
        
        if(!empty($ind_val['second_label'])){
            echo '<tr>';
            echo '<td>'.$row_char++.'</td>';
            if($debug)  echo '<td>'.$ind_id.'</td>';
            echo '<td>'.$ind_val['second_label'].'</td>';
            if($debug) echo '<td>'.$ind_val['formula'].'</td>';
            foreach($prod_year_arr[$p_id] as $year => $py_v){
                echo '<td align="right" ><h5 style="color:#5175db"><b>'.(number_format((isset($ind_values_arr[$ind_id][$year])?$ind_values_arr[$ind_id][$year]:''),2)).'</b></h5></td>';
            }
            echo '<td align="right">'.number_format($ind_total_val_arr[$ind_id],2).'</td>';
            echo '</tr>';
        }
    }
    
    echo '</tbody>
    </table>';
    

    foreach($prods_arr as $prod_id => $prod_val){
        if(empty($prod_id)) continue;
            echo '<table  class="table table-bordered table-condensed" >';
    
            echo '<tr>';
            echo '<td style="background-color:#7E5D99;color:#fff;font-size:14px;" align="center" colspan="10">'.$prod_val.'</td>';
            echo '</tr>';
            
            echo '<tr>';
            echo '<td>'.$row_char++.'</td>';
            echo '<td>Form of '.$prod_val.'</td>';
            echo '<td>';
            echo '<span style="color:#5175db"><b>'.$prod_settings[$prod_id]['size_of_prod'].' '.$prod_settings[$prod_id]['unit_of_prod'].'</b></span> '.$prod_settings[$prod_id]['form_of_prod'];
            echo '<span class="pull-right"><a target="_blank" href="product_settings.php" class="btn btn-sm red"><i class="fa fa-cogs"></i>  View Settings</a></span>';
            echo '</td>';
            echo '</tr>';
            
            echo '<tr>';
            echo '<td>'.$row_char++.'</td>';
            echo '<td>Qty of '.$prod_val.' per episode</td>';
            echo '<td>';
            echo '<span style="color:#5175db"><b>'.$prod_settings[$prod_id]['qty_per_episode'].'</b></span> ';
            echo $prod_settings[$prod_id]['form_of_prod'].'s ';
            echo 'Per Episode for <span style="color:#5175db"><b>'.$prod_settings[$prod_id]['treatment_duration_days'].'</b></span> days.';
            echo '</td>';
            echo '</tr>';
            
    ?>

    </table>
<table  class="table table-bordered table-condensed" >
        <tr style="background-color:#CAA8E6;">
        <td>&nbsp;</td>
        
        <?php
        if($debug) if($debug) echo '<td>ID</td>';
        echo '<td>&nbsp;</td>';
        if($debug) echo '<td>(Formula)</td>';
        for($i=$_REQUEST['start_year']; $i<=$_REQUEST['end_year'] ; $i++){
            echo '<td align="center">'.$i.'</td>';
        }
        ?>
        <td>Totals</td>
        </tr>     

        <?php
        //section 2 for products
        foreach($ind_arr[$prod_id] as $ind_id => $ind_val){
            echo '<tr>';
            echo '<td>'.$row_char++.'</td>';
            if($debug)  echo '<td>'.$ind_id.'</td>';
            echo '<td>'.$ind_val['first_label'].'</td>';
            
            if($debug) echo '<td>'.$ind_val['formula'].'</td>';
            
            foreach($prod_year_arr[$p_id] as $year => $py_v){
                
                if(!empty($ind_val['second_label'])){
                    echo '<td align="right">'.$ind_val['indicator_value'].''.$ind_val['indicator_unit'].'</td>';
                }
                else{
                    echo '<td align="right"><h5 style="color:#5175db"><b>'.number_format((isset($ind_values_arr[$ind_id][$year])?$ind_values_arr[$ind_id][$year]:''),2).'</b></h5></td>';
                }
            }
            echo '<td align="right">'.((empty($ind_val['second_label']))?number_format($ind_total_val_arr[$ind_id],2):'').'</td>';
            
            echo '</tr>';

            if(!empty($ind_val['second_label'])){
                echo '<tr>';
                echo '<td>'.$row_char++.'</td>';
                if($debug)  echo '<td>'.$ind_id.'</td>';
                echo '<td>'.$ind_val['second_label'].'</td>';
                if($debug) echo '<td>'.$ind_val['formula'].'</td>';
               
                foreach($prod_year_arr[$p_id] as $year => $py_v){
                    echo '<td align="right"><h5 style="color:#5175db"><b>'. number_format((isset($ind_values_arr[$ind_id][$year])?$ind_values_arr[$ind_id][$year]:''),2).'</td>';
                }
                echo '<td align="right">'.number_format($ind_total_val_arr[$ind_id],2).'</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
    }//end of prod
    ?>
    
  
    
