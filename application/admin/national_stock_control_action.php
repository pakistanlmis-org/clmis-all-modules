<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");
$date = $_REQUEST['date'];
if (isset($_REQUEST['submit'])) {

         $strSql = " DELETE FROM national_stock_control where date_from ='".$date."'  ";
         $rsSql = mysql_query($strSql);
            
        foreach($_REQUEST as $k=>$v)
        {
            $ids = array();
            $ids = explode('_',$k);
            $name= $ids[0];

            if($name=='checkbox')
            {
                $stk_id = $ids[1];
                $prov_id = $ids[2];

                $strSql = "  INSERT INTO `national_stock_control` 
                            ( `stkid`, `provid`, `checked`, `date_from`) 
                            VALUES ( '".$stk_id."', '".$prov_id."', '1', '".$date."');

                        ";
                //echo $strSql;exit;
                $rsSql = mysql_query($strSql);
            }
        }
    
    $_SESSION['err']['text'] = 'Changes have been successfully saved.';
    $_SESSION['err']['type'] = 'success';
}
//Redirecting to ManageSubAdmin
header("location:national_stock_control.php");
exit;
?>