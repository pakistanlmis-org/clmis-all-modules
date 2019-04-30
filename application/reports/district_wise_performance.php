<?php
set_time_limit(0);
/**
 * spr3
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$rptId = 'spr3';
//distrcit id
$districtId = '';
//stakeholder 
$stakeholder = 1;
//selected year
$selYear = '';
////selected province
$selProv = '';
//check if submitted
if (isset($_POST['submit'])) {
    //selected province
    $selProv = mysql_real_escape_string($_POST['prov_sel']);
    //from date
    $fromDate = isset($_POST['from_date']) ? mysql_real_escape_string($_POST['from_date']) : '';
    //to date
    $toDate = isset($_POST['to_date']) ? mysql_real_escape_string($_POST['to_date']) : '';
    //start date
    $startDate = $fromDate . '-01';
    //end date
    $endDate = date("Y-m-t", strtotime($toDate));
    //select query
    // Get Province name
    $qry = "SELECT
                tbl_locations.LocName
            FROM
                tbl_locations
            WHERE
                tbl_locations.PkLocID = $selProv";
    //fetch result
    $row = mysql_fetch_array(mysql_query($qry));
    //province name
    $provinceName = $row['LocName'];
    //file name
    $fileName = 'SPR3_' . $provinceName . '_from_' . date('M-Y', strtotime($startDate)) . '_to_' . date('M-Y', strtotime($endDate));
}
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        
        
        //two files are separated because of the difference in CS cases values.
        //once the CS cases values are fixed  . we can use the NEW_QUERY file.
        if(!empty($fromDate) && $fromDate < '2015-03-01') 
        {
            include "district_wise_performance_new_query.php";
        }
        else
        {
            include "district_wise_performance_old_query.php";
        }
        ?>
         
    </div>
</div>
<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
//include combos
include ('combos.php');
?>
</body>
</html>