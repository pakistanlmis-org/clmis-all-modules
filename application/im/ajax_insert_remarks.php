<?php 

include("../includes/classes/AllClasses.php");
$master_id=$_REQUEST['master_id'];
$prod_id=$_REQUEST['prod_id'];
$remarks= $_REQUEST['remarks'] ;
 $num=0;
 for($i=0;$i<sizeof($master_id);$i++){
     $remark_spec= addslashes($remarks[$i]);
    $qry_pro_type = "UPDATE clr_details
SET clr_details.remarks_prov = '$remark_spec'
WHERE
	clr_details.pk_master_id =$master_id[$i]
            AND clr_details.itm_id =$prod_id[$i]";
    
       $res= mysql_query($qry_pro_type);
        $num=mysql_affected_rows ( );
 }
 
// print_r($num);exit;
    if($qry_pro_type)
    {
        print_r(1);
    }
 
 