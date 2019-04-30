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

$stakeholder='1';
if(!empty($_REQUEST['stakeholder'])) $stakeholder=$_REQUEST['stakeholder'];

 


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
                                <label class="control-label">Stakeholder</label>
                                    <div class="form-group">
                                        <select name="stakeholder" id="stakeholder" required class="form-control input-sm">
                                            <option value="">Select</option>
                                            <?php

                                            $where='';

                                                $querys = "SELECT
                                                                stakeholder.stkid,
                                                                stakeholder.stkname
                                                                FROM
                                                                stakeholder
                                                                WHERE
                                                                stakeholder.ParentID IS NULL
                                                                AND stakeholder.stk_type_id IN (0, 1)
                                                                AND stakeholder.stkid in (
                                                                    SELECT
                                                                    distinct national_stock.stk_id
                                                                    FROM
                                                                    national_stock
                                                                    WHERE
                                                                    national_stock.ref = 'issue'
                                                                )        
                                                                ORDER BY
                                                                stakeholder.stkorder ASC";
                                                //query result
                                                $rsprov = mysql_query($querys) or die();
                                                $stk_name='';
                                                while ($rowp = mysql_fetch_array($rsprov)) {
                                                    if ($stakeholder == $rowp['stkid']) {
                                                        $sel = "selected='selected'";
                                                        $stk_name=$rowp['stkname'];
                                                    } else {
                                                        $sel = "";
                                                    }
                                                    //Populate prov_sel combo
                                                    ?>
                                                <option value="<?php echo $rowp['stkid']; ?>" <?php echo $sel; ?>><?php echo $rowp['stkname']; ?></option>
                                                <?php
                                            }

                                        ?>
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
                 
                 
                 $qry = "SELECT
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
                        Sum(national_stock.quantity) as opening
                        FROM
                        national_stock
                        INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                        INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                        INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                        WHERE
                        /*national_stock.stk_id = $stakeholder AND*/
                        national_stock.ref = 'ob'
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
                $qryRes = mysql_query($qry);
                $opening_bal=$issue_arr=$yr_list=$qtr_list=$closing_bal=$prov_arr=$prod_arr=array();
                while($row = mysql_fetch_assoc($qryRes))
                {
                    //print_r($row);
                    $opening_bal[$row['LocName']][$stk_name][$row['itm_name']]=$row['opening'];
                    $closing_bal[$row['LocName']][$stk_name][$row['itm_name']]=$row['opening'];
                    $prov_arr[$row['LocName']] = $row['LocName'];
                    $prod_arr[$row['LocName']][$row['itm_name']] = $row['itm_name'];
                }


                $qry = "
                    SELECT
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
                        YEAR(national_stock.tr_date) as yr,
                        QUARTER(national_stock.tr_date) as qrtr,
                        Sum(national_stock.quantity) as qty
                    FROM
                        national_stock
                        INNER JOIN itminfo_tab ON national_stock.item_id = itminfo_tab.itm_id
                        INNER JOIN stakeholder ON national_stock.stk_id = stakeholder.stkid
                        INNER JOIN tbl_locations ON national_stock.prov_id = tbl_locations.PkLocID
                    WHERE
                        national_stock.stk_id = $stakeholder AND
                        national_stock.ref = 'issue'
                    GROUP BY
                        tbl_locations.LocName,
                        stakeholder.stkname,
                        itminfo_tab.itm_name,
                        YEAR(national_stock.tr_date),
                        QUARTER(national_stock.tr_date)
                    ORDER BY
                        national_stock.prov_id,
                        national_stock.stk_id,
                        national_stock.item_id,
                        YEAR(national_stock.tr_date),
                        QUARTER(national_stock.tr_date)

                ";
                //query result
                $qryRes = mysql_query($qry);
                while($row = mysql_fetch_assoc($qryRes))
                {
                    //print_r($row);
                    $issue_arr[$row['LocName']][$row['stkname']][$row['itm_name']][$row['yr']][$row['qrtr']]=$row['qty'];
                    if(empty($closing_bal[$row['LocName']][$row['stkname']][$row['itm_name']]))
                           $closing_bal[$row['LocName']][$row['stkname']][$row['itm_name']]=0;

                    $closing_bal[$row['LocName']][$row['stkname']][$row['itm_name']]+=$row['qty'];
                    $yr_list[$row['yr'].'-'.$row['qrtr']]=$row['yr'].'-'.$row['qrtr'];
                    $qtr_list[$row['yr']][$row['qrtr']]=$row['qrtr'];
                    
                    $prov_arr[$row['LocName']] = $row['LocName'];
                    $prod_arr[$row['LocName']][$row['itm_name']] = $row['itm_name'];
                }
                ksort($qtr_list);
                
                /*echo '<pre>';
                echo 'open:';print_r($opening_bal);
                echo 'issue:';print_r($issue_arr);
                echo 'closing:';print_r($closing_bal);
                 
                 */
                 ?>
                <div class="row">
                    <div class="col-md-12">
                        
                         
                        
                            <table border="1" width="100%" cellpadding="0" cellspacing="0" style="">
                                
                                <?php
                                $cols = count($yr_list);
                                $cols+=3;
                                echo '  <tr>
                                            <th align="center" colspan="">Product</th>
                                            <th align="center" colspan="">Opening Balance</th>
                                        ';
                                foreach($qtr_list as $yr => $qtr)
                                {
                                    foreach($qtr as $k => $v)
                                    {
                                        echo '<th align="center">Q'.$k.'-'.$yr.'</th>';
                                    }
                                }
                                echo '<th align="center" colspan="">Closing Balance</th>';
                                echo '</tr>';
                                foreach($prov_arr as $k => $prov)
                                {
                                    if(!empty($opening_bal[$prov]))
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
                                        
                                    }
                                    
                                    echo '  <tr>
                                                <td align="left" colspan="'.$cols.'" style="background-color:#93d893">'.$prov.'</td>
                                            </tr>';
                                    
                                    foreach($prov_data as $stk => $prod_data)
                                    {
                                        echo '  <tr>
                                                    <td align="left" colspan="'.$cols.'" style="background-color:#c2edc2">'.$stk.'</td>
                                                </tr>';
                                        foreach($prod_data as $prod => $qty)
                                        {
                                            echo '  <tr>
                                                        <td align="center" colspan="">'.$prod.'</td>
                                                        <td align="right" colspan="">'.number_format($qty).'</td>
                                                    ';
                                            
                                            foreach($qtr_list as $yr => $qtr)
                                            {
                                                foreach($qtr as $k => $v)
                                                {
                                                    echo '<td  align="right" >'.(!empty($issue_arr[$prov][$stk][$prod][$yr][$k])?number_format(abs($issue_arr[$prov][$stk][$prod][$yr][$k])):'0').'</td>';
                                                }
                                            }
                                            echo '<td align="right" colspan="">'.(!empty($closing_bal[$prov][$stk][$prod])?number_format($closing_bal[$prov][$stk][$prod]):'0').'</td>';
                                            echo '</tr>';
                                        }
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