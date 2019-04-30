<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_FILES);
//echo '<pre>';print_r($_REQUEST);exit;
 if (!empty($_FILES['fileToUpload']['name'][0])) {
        $myFile = $_FILES['fileToUpload'];
        $fileCount = count($myFile["name"]);

        for ($i = 0; $i < $fileCount; $i++) {
                    //upload files
                    $file_ext=strtolower(end(explode('.',$_FILES['fileToUpload']['name'][$i])));

                    $file_name_orig=strtolower(str_replace(' ', '', $_FILES['fileToUpload']['name'][$i]));
                    $file_name_orig=strtolower(str_replace('_', '', $file_name_orig));
                    $file_name_orig=strtolower(str_replace(',', '', $file_name_orig));
                    $file_name_orig=strtolower(str_replace('-', '', $file_name_orig));
                    //echo $file_ext;
                    $expensions= array("jpeg","jpg","png","xls","xlsx","doc","docx","pdf");
                    $errors=array();
                     if(in_array($file_ext,$expensions)=== false){
                        $errors[]="extension not allowed . Tried to upload :".$file_ext.". ";
                     }

                     if($_FILES['fileToUpload']['size'][$i] > 2097152) {
                        $errors[]='File size must be less than 2 MB';
                     }

                     if(empty($errors)==true) {
                         //echo $file_name_orig;
                        $this_file_name = "Doc_".$_REQUEST['id']."_".$file_name_orig;
                        move_uploaded_file($_FILES['fileToUpload']['tmp_name'][$i],"../../user_uploads/fasp_attachments/".$this_file_name);
                        $qryd = "DELETE FROM fq_docs WHERE document_name = '".$this_file_name."' AND fq_master_id = '" . $_REQUEST['id'] . "' ";
                        mysql_query($qryd);
                        $qry2 = "INSERT INTO fq_docs SET document_name = '".$this_file_name."' , fq_master_id = '" . $_REQUEST['id'] . "' ";
                       // echo $qry2;exit;
                        mysql_query($qry2);
                     }else{
                         echo 'Error while uploading file';
                        print_r($errors);
                        exit;
                     } 
        }
    }
          
           


$parent_id = $_REQUEST['id'];

$update_data_arr  = $insert_data_arr = $year_increment = array();
foreach($_REQUEST as $field_name => $field_val)
{
    $temp = array();
    $temp = explode('_',$field_name);
    if($temp[0] == 'input')
    {
        $prod_id=$temp[2];
        if($temp[1] == 'adjustment'){
            $update_data_arr[$parent_id][$prod_id]['adjustment']=$field_val;
        }
        if($temp[1] == 'remarks'){
            $update_data_arr[$parent_id][$prod_id]['remarks']=$field_val;
        }
    }
    elseif($temp[0] == 'fc')
    {
        $product_key_id = $temp[1];
        $year = $temp[2];
        
        $insert_data_arr[$product_key_id][$year]=$field_val;
    }
    elseif($temp[0] == 'increment')
    {
        $year = $temp[1];
        $year_increment[$year]=$field_val;
    }
}

//echo '<pre>';print_r($update_data_arr);exit;
foreach($update_data_arr as $master => $master_data)
{
    foreach($master_data as $prod => $prod_data)
    { 
        $strSql2 = " UPDATE `fq_fp_products_data` 
                        SET adjustment = '".(!empty($prod_data['adjustment'])?$prod_data['adjustment']:0)."', remarks='".implode(',',$prod_data['remarks'])."'
                     WHERE 
                        `master_id`     = $master AND 
                        `prod_id`    = '$prod'  
           ";

        //echo $strSql2.'</br>';
        $rsSql2 = mysql_query($strSql2) or die('Err inserting forecasting C') ;
           
    }
}


foreach($insert_data_arr as $prod_key_id => $p_data)
{
     foreach($p_data as $year => $fc_val)
    {
         $fc_val= str_replace( ',', '', $fc_val );

         $p_inc = 0;
         if(!empty($year_increment[$year]))$p_inc=$year_increment[$year];
         
        $strSql2 = " DELETE  FROM `fq_fp_products_forecasting` WHERE fp_product_key = '$prod_key_id' AND year = '$year'";
        $rsSql2 = mysql_query($strSql2) or die('deleting qry') ;
        
        $strSql2 = " INSERT INTO  `fq_fp_products_forecasting`
                (  `fp_product_key`, `year`, `percent_increase`, `forecasted_val`) 
                VALUES ( '$prod_key_id', '$year', '$p_inc', '$fc_val');
       ";
    //echo $strSql2.'</br>';
    $rsSql2 = mysql_query($strSql2) or die('Err inserting fq_fp_products_forecasting') ;
    }
}

//echo 'Forecasted Values Saved. Now Redirecting.';
//exit;
$_SESSION['err']['msg']='Data Saved Successfully.';
$_SESSION['err']['type']='success';
header("location:forecasting_view.php?id=$parent_id");
exit;

?>