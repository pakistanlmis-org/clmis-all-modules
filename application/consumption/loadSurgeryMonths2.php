<?php
//include AllClasses
include("../includes/classes/AllClasses.php");

if (isset($_REQUEST['wharehouse_id'])) {
    //Getting wharehouse_id
    $wh_Id = $_REQUEST['wharehouse_id'];
    //start date
    $startDate = date('2010-01-01');
    //end date
    //$endDate = date('2016-03-01');
    $endDate =  date('Y-m-d', strtotime("first day of last month"));
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    //date interval
    $i = DateInterval::createFromDateString('1 month');
    
    $get_qry = " SELECT tbl_hf_data.pk_id,
                        tbl_hf_data.warehouse_id,
                        tbl_hf_data.item_id,
                        tbl_hf_data.reporting_date
                    FROM tbl_hf_data
                    WHERE warehouse_id='".$wh_Id."'
                        AND reporting_date >= '".$startDate."'
                        AND item_id in ('31','32')
                    ORDER BY reporting_date ";
    //echo $get_qry;exit;
    //query result
    $ch_res=mysql_query($get_qry);
    $existing_data=array();
    while($row = mysql_fetch_array($ch_res))
    {
        
       // echo '<pre>';
       // print_r($row);
        $existing_data[$row['reporting_date']]= $row;
    }
    //echo '<pre>';
    //print_r($existing_data);exit;
    //Loop
    while ($end >= $start) {
        $selected = (!empty($rpt_date) && $end->format("Y-m") == $rpt_date) ? 'selected="selected"' : '';
        //encode url
        $cls1=' green ';
        $cls2=' fa fa-plus ';
        $isNewRpt = '1';
        
        if(!empty($existing_data[$end->format("Y-m-d")])) 
        {
            $cls1=' red ';
            $cls2=' fa fa-edit ';
            $isNewRpt = '0';
            
        }
        $do3Months = urlencode("Z" . ($wh_Id + 77000) . '|' . $end->format("Y-m-") . '01|'.$isNewRpt);
        $url = "data_entry_surgery_form.php?Do=" . $do3Months;
        $allMonths[] = "<a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs $cls1\">" . $end->format("M-Y") .(!empty($draft))." <i class=\" $cls2 \"></i></a>";
        $end = $end->sub($i);
    }
    //implode
    echo implode(' ', $allMonths);
    //exit;
}