<?php
set_time_limit(0);

include 'config.php';

$fm = $_GET['fm'];
$tm = $_GET['tm'];
$year = $_GET['year'];

if(!empty($fm) && !empty($tm) && !empty($year)){

$qry = "SELECT
	field_summary.rep_month,
	field_summary.rep_year,
	field_summary.stkname,
	field_summary.province,
	field_summary.district,
	field_summary.wh_name,
	field_summary.itm_name,
	field_summary.SOH,
	field_summary.AMC,
	field_summary.MOS
FROM
	field_summary
WHERE
	field_summary.rep_month BETWEEN $fm
AND $tm
AND field_summary.rep_year = $year";

    $queryA1 = $conn->query($qry);

$body = '<table border=1 cellspacing=0 cellpadding=2>
        <tr>
            <th>Month</th>
        	<th>Year</th>
            <th>Stakeholder</th>
            <th>Province</th>
            <th>District</th>
            <th>Store</th>
            <th>Item</th>    
            <th>SOH</th>    
            <th>AMC</th>    
            <th>MOS</th>           
        </tr>';
    while ($row = $queryA1->fetch_object()) {
        
        $body .= '<tr>
        	<td>'.$row->rep_month.'</td>
            <td>'.$row->rep_year.'</td>            
            <td>'.$row->stkname.'</td>
            <td>'.$row->province.'</td>
            <td>'.$row->district.'</td>
            <td>'.$row->wh_name.'</td>
            <td>'.$row->itm_name.'</td>
            <td>'.$row->SOH.'</td>
            <td>'.$row->AMC.'</td>
            <td>'.$row->MOS.'</td>
        </tr>';
        } 
    $body .= '</table>';

    echo $body;
} else {
	echo "Please provide date parameter.";
}
    
    ?>