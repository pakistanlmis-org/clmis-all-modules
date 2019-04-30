<?php
/**
 * stock_availability
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
//include FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//include header
include(PUBLIC_PATH . "html/header.php");

$province='';
if(!empty($_REQUEST['province'])) $province=$_REQUEST['province'];

 


//echo '<pre>';
//echo 'open:';print_r($yr_list);
//echo 'issue:';print_r($qtr_list);
/*echo '<pre>';
echo 'open:';print_r($opening_bal);
echo 'issue:';print_r($issue_arr);
echo 'closing:';print_r($closing_bal);
exit;
*/

?>
<style>
    .objbox {
        overflow-x: hidden !important;
    }
</style>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <h3 class="page-title">Provincial Utilization of Federal Stocks Report</h3>
                <div class="widget" data-toggle="collapse-widget">
                    <div class="widget-head">
                        <h3 class="heading">Filter by</h3>
                    </div>
                    <div class="widget-body">
                        <div class="row">
            <div class="col-md-12">
                <form id="aform" action="">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">Province</label>
                                    <div class="form-group">
                                        <select name="province" id="province" class="form-control input-sm">
                                            <option value="">All</option>
                                            <?php
                                            $where='';

                                                $querys = "SELECT
                                                                tbl_locations.LocName,
                                                                tbl_locations.PkLocID,
                                                                tbl_locations.LocLvl,
                                                                tbl_locations.ParentID,
                                                                tbl_locations.LocType
                                                            FROM
                                                                tbl_locations
                                                            WHERE
                                                                tbl_locations.LocLvl = 2
                                                                AND LocType = 2
                                                            ORDER BY 
                                                                tbl_locations.PkLocID
                                                            ";
                                                //query result
                                                $rsprov = mysql_query($querys) or die();
                                                $stk_name='';
                                                while ($rowp = mysql_fetch_array($rsprov)) {
                                                    if ($province == $rowp['PkLocID']) {
                                                        $sel = "selected='selected'";
                                                        $pro_name=$rowp['LocName'];
                                                    } else {
                                                        $sel = "";
                                                    }
                                                    //Populate prov_sel combo
                                                    ?>
                                                <option value="<?php echo $rowp['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $rowp['LocName']; ?></option>
                                                <?php
                                            }
                                         if ($province == 'ppw') {
                                                $sel = "selected='selected'";
                                                $pro_name="PPW";
                                            } else {
                                                $sel = " ";
                                            }        
                                        ?>
                                        <option value="ppw" <?php echo $sel; ?>>PPW</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <div class="form-group">
                                    <button type="submit" name="submit" class="btn btn-primary input-sm">Go</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>

        </div>
                    </div>
                </div>
                 <?php
                 $and = '';
                
                if(!empty($province))
                {
                    if($province=='ppw') 
                        $and .= " AND tbl_locations.LocType = 3 ";
                    else        
                        $and .= " AND national_stock.prov_id = $province  ";
                }
                 
                 $qry = "SELECT
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
                        Sum(national_stock.quantity) as opening,
                        tbl_locations.LocType
                        FROM
                        national_stock
                        INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                        INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                        INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                        WHERE
                        
                        national_stock.ref = 'ob'
                        $and
                        GROUP BY
                        tbl_locations.LocName,
                        
                        itminfo_tab.itm_name
                        ORDER BY
                        national_stock.prov_id,
                        national_stock.stk_id,
                        national_stock.item_id
                ";
                //query result
                // echo $qry;exit;
                $qryRes = mysql_query($qry);
                $opening_bal=$issue_arr=$stk_arr=$closing_bal=$prov_arr=$prod_arr=array();
                
                while($row = mysql_fetch_assoc($qryRes))
                {
                    //print_r($row);
                    
                    //for suming up other regions to PPW , as per req of wasif sb
                    if($row['LocType'] == '3')
                    {
                        if(empty($opening_bal['PPW'][$row['itm_name']])) $opening_bal['PPW'][$row['itm_name']]=0;
                        $opening_bal['PPW'][$row['itm_name']] += $row['opening'];
                        
                        if(empty($closing_bal["PPW"][$row['itm_name']]))  $closing_bal["PPW"][$row['itm_name']] =0;
                        $closing_bal["PPW"][$row['itm_name']] += $row['opening'];
                        
                        $prov_arr["PPW"] = "PPW";
                        $prod_arr["PPW"][$row['itm_name']] = $row['itm_name'];
                    }
                    else
                    {
                        $opening_bal[$row['LocName']][$row['itm_name']]=$row['opening'];
                        $closing_bal[$row['LocName']][$row['itm_name']]=$row['opening'];
                        $prov_arr[$row['LocName']] = $row['LocName'];
                        $prod_arr[$row['LocName']][$row['itm_name']] = $row['itm_name'];
                    }
                }

                
                $qry = "
                    SELECT
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
                        Sum(national_stock.quantity) as qty,
                        tbl_locations.LocType
                    FROM
                        national_stock
                        INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                        INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                        INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                    WHERE
                        national_stock.ref = 'issue'
                        AND stakeholder.stk_type_id = 0
                        $and
                    GROUP BY
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name
                    ORDER BY
                        national_stock.prov_id,
                        national_stock.stk_id,
                        national_stock.item_id

                ";
                //query result
                //echo $qry;exit;
                $qryRes = mysql_query($qry);
                while($row = mysql_fetch_assoc($qryRes))
                {
                    //print_r($row);
                    
                    //for suming up other regions to PPW , as per req of wasif sb
                    if($row['LocType'] == '3')
                    {
                        if(empty($issue_arr['PPW'][$row['stkname']][$row['itm_name']])) $issue_arr['PPW'][$row['stkname']][$row['itm_name']]=0;
                        $issue_arr['PPW'][$row['stkname']][$row['itm_name']] += $row['qty'];
                        
                       if(empty($closing_bal["PPW"][$row['itm_name']]))
                           $closing_bal["PPW"][$row['itm_name']]=0;

                        $closing_bal["PPW"][$row['itm_name']]+=$row['qty'];
                        $stk_arr[$row['stkname']] = $row['stkname'];

                        $prov_arr["PPW"] = "PPW";
                        $prod_arr["PPW"][$row['itm_name']] = $row['itm_name'];
                    }
                    else
                    {
                        $issue_arr[$row['LocName']][$row['stkname']][$row['itm_name']]=$row['qty'];
                        if(empty($closing_bal[$row['LocName']][$row['itm_name']]))
                               $closing_bal[$row['LocName']][$row['itm_name']]=0;

                        $closing_bal[$row['LocName']][$row['itm_name']]+=$row['qty'];
                        $stk_arr[$row['stkname']] = $row['stkname'];

                        $prov_arr[$row['LocName']] = $row['LocName'];
                        $prod_arr[$row['LocName']][$row['itm_name']] = $row['itm_name'];
                    }
                }
                
                
//                echo '<pre>';
//                
//                echo 'province:';print_r($prov_arr);
//                echo 'products:';print_r($prod_arr);
//                echo 'open:';print_r($opening_bal);
//                echo 'issue:';print_r($issue_arr);
//                echo 'closing:';print_r($closing_bal);
//                 
                 
                 ?>
                <div class="row">
                    <div class="col-md-12">
                        
                         
                        
                            <table border="1" width="100%" cellpadding="0" cellspacing="0" class="table table-bordered table-condensed">
                                
                                <?php
                                $cols = count($stk_arr);
                                $cols+=3;
                                echo '  <tr>
                                            <th align="center" colspan="">Product</th>
                                            <th align="center" colspan="">Opening Balance</th>
                                        ';
                                foreach($stk_arr as $k => $stk)
                                {
                                    
                                    echo '<th align="center">'.$stk.'</th>';
                                    
                                }
                                echo '<th align="center" colspan="">Closing Balance</th>';
                                echo '</tr>';
                                foreach($prov_arr as $k => $prov)
                                {
                                    /*if(!empty($opening_bal[$prov]))
                                    {
                                        
                                        $prov_data=$opening_bal[$prov];
                                    }
                                    else
                                    {
                                        $prov_data=array();
                                       // echo '<pre>'.$prov.'::';print_r($prod_arr[$prov]);
                                        foreach($prod_arr[$prov] as $prv => $prd)
                                        {
                                            $prov_data[$stk_name][$prd]=0;
                                        }
                                        
                                    }*/
                                    
                                    echo '  <tr>
                                                <td align="left" colspan="'.$cols.'" style="background-color:#93d893">'.$prov.'</td>
                                            </tr>';
                                    
                                    foreach($prod_arr[$prov] as $prod_k => $prod_v)
                                    {
                                       
                                            echo '  <tr>
                                                        <td align="center" colspan="">'.$prod_v.'</td>
                                                        <td align="right" colspan="">'.number_format(!empty($opening_bal[$prov][$prod_v])?$opening_bal[$prov][$prod_v]:'0').'</td>
                                                    ';
                                            
                                            foreach($stk_arr as $k => $stk)
                                            {
                                               
                                               echo '<td  align="right" >'.(!empty($issue_arr[$prov][$stk][$prod_v])?number_format(abs($issue_arr[$prov][$stk][$prod_v])):'0').'</td>';
                                                
                                            }
                                            echo '<td align="right" colspan="">'.((!empty($closing_bal[$prov][$prod_v]))?number_format($closing_bal[$prov][$prod_v]):'0').'</td>';
                                            echo '</tr>';
                                        
                                    }
                                }
                                ?>
                                
                                
                                
                            </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
//include report_includes
    include PUBLIC_PATH . "/html/reports_includes.php";
    ?>
    <script>
     
    </script>
</body>
</html>