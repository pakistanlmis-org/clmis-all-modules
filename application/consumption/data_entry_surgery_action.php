<?php

//$_POST=$_REQUEST;
//exit;

include("../includes/classes/AllClasses.php");
$province = $_SESSION['user_province'];
$mainStk = $_SESSION['user_stakeholder'];
if (!isset($_SESSION['user_id'])) {
    $location = SITE_URL . 'index.php';
    ?>

    <script type="text/javascript">
        window.location = "<?php echo $location; ?>";
    </script>
    <?php
}
date_default_timezone_set("Asia/Karachi");
function getClientIp() {
    
    $ip = (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:'');
    return $ip;
}

//add
if ($_POST['ActionType'] == 'Add') {

    // Validation: Start
    $error = '';
    $response = array();

    // Validation: End
    if (strlen($error) > 0) {
        $response['resp'] = 'err';
        $response['msg'] = $error;
        //encode in json
        echo json_encode($response);
        exit;
    }
    else 
    {
        //check if the report exists
        
        $qry="SELECT * from tbl_hf_data
                              where 
                              warehouse_id ='".$_REQUEST['wh_id']."'
                              AND reporting_date = '".$_REQUEST['RptDate']."'
                              AND item_id in ('31','32')";
        $rs1=mysql_query($qry);
        $num=mysql_num_rows($rs1);
        
	//check if report already exists
        if ($num > 0) 
        {
            //if the report already exists, then get the data.
            $addDate = $_POST['add_date'];
            	
              $temp_data=array();
              while($data1 = mysql_fetch_array($rs1))
              {
                      //print_r($data1);
                      $temp_data[$data1['pk_id']]['hf_data_id']		=$data1['pk_id'];
                      $temp_data[$data1['pk_id']]['item_id']            =$data1['item_id'];
                      $temp_data[$data1['pk_id']]['reporting_date']	=$data1['reporting_date'];

                       $delQry = "DELETE FROM tbl_hf_data_reffered_by WHERE hf_data_id = '" . $data1['pk_id'] . "'  ";

                       mysql_query($delQry);
              }
        } 
        else 
        {
            //if the report does not exist, then insert new row, and put data in temporary array
            
            $temp_data=array();
            $addDate = date('Y-m-d H:i:s');
            
            $cols =",opening_balance =0,
                    received_balance =0,
                    issue_balance =0,
                    closing_balance =0,
                    adjustment_positive =0,
                    adjustment_negative =0,
                    avg_consumption =0,
                    new =0,
                    old =0,
                    ip_address='".$_SERVER['REMOTE_ADDR']."',
                    created_by='".$_SESSION['user_id']."',
                    created_date='".$addDate."' ";
            
             $qry="INSERT INTO tbl_hf_data
                    SET
                    warehouse_id        = '".$_REQUEST['wh_id']."'
                    , reporting_date  = '".$_REQUEST['RptDate']."'
                    , item_id         = '31' ".$cols;
            $rs1=mysql_query($qry);
            $pk_id= mysql_insert_id();
            
            $temp_data[$pk_id]['hf_data_id']            = $pk_id;
            $temp_data[$pk_id]['item_id']               = '31';
            $temp_data[$pk_id]['reporting_date']	= $_REQUEST['RptDate'];


             $qry="INSERT INTO tbl_hf_data
                    SET
                    warehouse_id        = '".$_REQUEST['wh_id']."'
                    , reporting_date  = '".$_REQUEST['RptDate']."'
                    , item_id         = '32' ".$cols;
            $rs1=mysql_query($qry);	
             
            $pk_id= mysql_insert_id();
            
            $temp_data[$pk_id]['hf_data_id']            = $pk_id;
            $temp_data[$pk_id]['item_id']               = '32';
            $temp_data[$pk_id]['reporting_date']	= $_REQUEST['RptDate'];
              
              
            
        }
        
       
      // echo '<pre>';
       // print_r($temp_data);
       // print_r($_SESSION);
       // print_r($_SERVER);
       // exit;
        
        $lastUpdate = date('Y-m-d H:i:s');
        // Client IP
        $clientIp = getClientIp();


			
            $rhs_male 		= isset($_POST['rhs_male']) ? $_POST['rhs_male'] : '';
            $rhs_female 	= isset($_POST['rhs_female']) ? $_POST['rhs_female' ] : '';
            $fwc_male 		= isset($_POST['fwc_male']) ? $_POST['fwc_male' ] : '';
            $fwc_female 	= isset($_POST['fwc_female']) ? $_POST['fwc_female' ] : '';
            $other_male 	= isset($_POST['other_male' ]) ? $_POST['other_male' ] : '';
            $other_female 	= isset($_POST['other_female']) ? $_POST['other_female' ] : '';
			
            $static_male 	= isset($_POST['static_male']) ? $_POST['static_male' ] : '';
            $static_female 	= isset($_POST['static_female']) ? $_POST['static_female' ] : '';
            $camp_male 		= isset($_POST['camp_male']) ? $_POST['camp_male' ] : '';
            $camp_female 	= isset($_POST['camp_female']) ? $_POST['camp_female' ] : '';
			
			
            //$staticCamp = isset($_POST['staticCamp' . $itemid]) ? $_POST['staticCamp' . $itemid] : '';
            //$itemCategory = $_POST['flitm_category'][$count++];

            $wh_id = $_POST['wh_id'];
            //$report_year = $_POST['yy'];
            //$report_month = $_POST['mm'];
	
	
	
                foreach($temp_data as $k=>$v)
                {
                    if($v['item_id']=='31')
                    {
                     $query_reff = "INSERT INTO tbl_hf_data_reffered_by(
                                    tbl_hf_data_reffered_by.hf_data_id,
                                    tbl_hf_data_reffered_by.hf_type_id,
                                    tbl_hf_data_reffered_by.ref_surgeries,
                                    tbl_hf_data_reffered_by.static,
                                    tbl_hf_data_reffered_by.camp)
                                    Values(							
                                    '" . $v['hf_data_id'] . "',
                                    '4',
                                    '" . $rhs_male . "',    
                                    '".$static_male."',
                                    '".$camp_male."'
                                    )";
                            $res_reff = mysql_query($query_reff) or die(mysql_error());

                             $query_reff = "INSERT INTO tbl_hf_data_reffered_by(
                                    tbl_hf_data_reffered_by.hf_data_id,
                                    tbl_hf_data_reffered_by.hf_type_id,
                                    tbl_hf_data_reffered_by.ref_surgeries,
                                    tbl_hf_data_reffered_by.static,
                                    tbl_hf_data_reffered_by.camp)
                                    Values(							
                                    '" . $v['hf_data_id'] . "',
                                    '1',
                                    '" . $fwc_male . "',    
                                    '0',
                                    '0'
                                    )";
                            $res_reff = mysql_query($query_reff) or die(mysql_error());

                             $query_reff = "INSERT INTO tbl_hf_data_reffered_by(
                                    tbl_hf_data_reffered_by.hf_data_id,
                                    tbl_hf_data_reffered_by.hf_type_id,
                                    tbl_hf_data_reffered_by.ref_surgeries,
                                    tbl_hf_data_reffered_by.static,
                                    tbl_hf_data_reffered_by.camp)
                                    Values(							
                                    '" . $v['hf_data_id'] . "',
                                    '13',
                                    '" . $other_male . "',    
                                    '0',
                                    '0'
                                    )";
                            $res_reff = mysql_query($query_reff) or die(mysql_error());
                    }
                    elseif($v['item_id']=='32')
                    {
                     $query_reff = "INSERT INTO tbl_hf_data_reffered_by(
                                    tbl_hf_data_reffered_by.hf_data_id,
                                    tbl_hf_data_reffered_by.hf_type_id,
                                    tbl_hf_data_reffered_by.ref_surgeries,
                                    tbl_hf_data_reffered_by.static,
                                    tbl_hf_data_reffered_by.camp)
                                    Values(							
                                    '" . $v['hf_data_id'] . "',
                                    '4',
                                    '" . $rhs_female . "',    
                                    '".$static_female."',
                                    '".$camp_female."'
                                    )";
                            $res_reff = mysql_query($query_reff) or die(mysql_error());

                             $query_reff = "INSERT INTO tbl_hf_data_reffered_by(
                                    tbl_hf_data_reffered_by.hf_data_id,
                                    tbl_hf_data_reffered_by.hf_type_id,
                                    tbl_hf_data_reffered_by.ref_surgeries,
                                    tbl_hf_data_reffered_by.static,
                                    tbl_hf_data_reffered_by.camp)
                                    Values(							
                                    '" . $v['hf_data_id'] . "',
                                    '1',
                                    '" . $fwc_female . "',    
                                    '0',
                                    '0'
                                    )";
                            $res_reff = mysql_query($query_reff) or die(mysql_error());

                             $query_reff = "INSERT INTO tbl_hf_data_reffered_by(
                                    tbl_hf_data_reffered_by.hf_data_id,
                                    tbl_hf_data_reffered_by.hf_type_id,
                                    tbl_hf_data_reffered_by.ref_surgeries,
                                    tbl_hf_data_reffered_by.static,
                                    tbl_hf_data_reffered_by.camp)
                                    Values(							
                                    '" . $v['hf_data_id'] . "',
                                    '13',
                                    '" . $other_female . "',    
                                    '0',
                                    '0'
                                    )";
                            $res_reff = mysql_query($query_reff) or die(mysql_error());
                    }
            }     
              // exit;     
    
	
        $response['resp'] = 'ok';
        //encode in json
        echo json_encode($response);
        exit;
    }
}