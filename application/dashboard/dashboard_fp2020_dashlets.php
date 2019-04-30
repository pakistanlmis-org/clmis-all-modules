
                    
                      
                    
                        
                        <div class=" hide_divs">
                            <div class="row hide_divs">

                                    <div class="col-md-6 my_dash_cols">
                                        <div class="col-md-12 "  style=" " >
                                            <div class="center"><h4>Transactional Data</h4></div>
                                        </div>
                                        <div class="col-md-12 "  style=" ">
                                            <div class="dashlet_graph_html" id="dashboard_ss_a1" href='dashboard_fp2020_a1.php'></div>
                                        </div>

                                        <div class="col-md-12 "  style="padding-left: 0;">
                                            <p style=" font-size: 10px">
                                            <ul style=" font-size: 10px">
                                                <li>ReOrdering Dates are calculated in order to maintain stock sufficiency level of <b><?=$min_stock_months.'-'.$sufficiency_upper_level?> months</b></li>
                                                <li>Shipments may take estimated delivery time of:<b>2 - <?=$min_delivery_time?> months</b>.</li>
                                                <li> Key: POP (Progestin only Pill), COC (Combined Oral Contraceptive); ECP (Emergency Contraceptive Pill), MoS (Months of Stock), AMC (Average Monthly Consumption) </li>
                                                <li>*All above estimated MoS represents provincially procured and MoNHSR&C allocations from past USAID support
                                                </li>
                                                <li>AMC** : Average Monthly Consumption calculated on <b>last 12 Non-Zero months</b></li>
                                                <li>ReOrder On          : Estimated Date to order , for maintaining minimum stock level.</li>
                                                <li>ReOrdering Quantity : Estimated quantity to be ordered.</li>
                                                <li>ReOrdering Quantity : Calculated only on current month.</li>
                                                <li>LHW Program reports only 3 products i.e. Condom ,COC , 3-Month Inj</li>

                                            </ul>
                                             </p>
                                        </div>

                                        <div class="col-md-12 "  style=" ">
                                            <div class="widget1 widget-tabs">    
                                                <div class="widget-body" style="height:auto;overflow: auto;">

                                                    <p style=" font-size: 10px">
                                                        <?php 
                                                        $dashlet_id='120';
                                                        if(!empty($comments_arr[$dashlet_id]))
                                                            foreach($comments_arr[$dashlet_id] as $k=>$comment)echo (!empty($comment)?$comment.'</br></br>':'');
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 my_dash_cols">

                                        <div class="row">
                                            <div class="col-md-12 "  style=" ">
                                                <div class="center"><h4>Monthly Data</h4></div>
                                            </div>
                                        </div>

                                        <?php
                                         if(!($hide_graphs))
                                         {

                                        ?>
                                        <div id="general_summary_main_div" class="col-md-12" style="display:none;">
                                             <div class="widget widget-tabs ">    
                                                 <div class="widget-body">
                                                     <div class="row">
                                                         <div class="col-md-6 center">
                                                                 <div class="dashboard-stat red-pink" style="height:100px"  id="dashboard_ss_c1_details"  data-content="Loading..." data-toggle="popover" title="Availability Rate Breakdown at Districts" data-trigger="hover" data-placement="left" data-html="true">
                                                                         <div class="visual"><i class="fa fa-bar-chart-o"></i></div>
                                                                         <div class="details">
                                                                                 <div class="number" id="general_av_dist">-</div>
                                                                                 <div class="desc" style="font-size:13px;">Average stock availability rate <br/>  of  <?php echo $prov_name;?> District Stores for <?=count($itm_arr_request);?> products</div>
                                                                         </div>
                                                                 </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="dashboard-stat red-pink" style="height:100px"   id="dashboard_ss_c2_details"  data-content="Loading..." data-toggle="popover" title="Availability Rate Breakdown at SDPs" data-trigger="hover" data-placement="left" data-html="true">
                                                                            <div class="visual"><i class="fa fa-globe"></i></div>
                                                                            <div class="details">
                                                                                    <div class="number" id="general_av_sdp">-</div>
                                                                                    <div class="desc" style="font-size:13px;">Average stock availability rate <br/> of <?php echo $prov_name;?> Service Delivery Points for <?=count($itm_arr_request);?> products </div>
                                                                            </div>
                                                                    </div>
                                                            </div>
                                                    </div>
                                                </div>
                                            </div>    
                                        </div>
                                        <?php
                                         }
                                        ?>
                                        <div class="col-md-12 ">
                                            <div class="<?=(($hide_graphs)?' ':'dashlet_graph_json')?>" id="dashboard_ss_c1" href='dashboard_fp2020_b1.php'>
                                                <?=(($hide_graphs)?' Districts and health facilities data reporting is on monthly basis. Stocks data of districts and health facilities will be displayed in '.date('M Y',strtotime("+1 month",strtotime($fromDate))).' after data entry of '.date('M Y',strtotime($fromDate)):'')?>

                                            </div>
                                        </div>
                                        <div class="col-md-12 ">
                                            <div class="<?=(($hide_graphs)?' hide ':'')?>"  style=" font-size: 10px"> * Stock availability is derived from reported District Stores.</div>
                                        </div>
                                        <div class="col-md-12 ">
                                            <div class="widget1 widget-tabs">    
                                                <div class="widget-body" style="height:auto;overflow: auto;">

                                                    <p style=" font-size: 10px">
                                                        <?php 
                                                        $dashlet_id='121';
                                                        if(!empty($comments_arr[$dashlet_id]))
                                                            foreach($comments_arr[$dashlet_id] as $k=>$comment)echo (!empty($comment)?$comment.'</br></br>':'');
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-12 ">
                                            <div class="<?=(($hide_graphs)?' ':'dashlet_graph_json')?>" id="dashboard_ss_c2" href='dashboard_fp2020_b2.php'></div>
                                        </div>
                                        <div class="col-md-12 ">
                                            <div class="<?=(($hide_graphs)?' hide ':' ')?>"  style=" font-size: 10px"> * Stock availability is derived from reported SDPs</div>
                                            <div class="<?=(($hide_graphs)?' hide ':' ')?>"  style=" font-size: 10px"> * These SDP Types are not included in the calculations of SDP Level :MSU,Social Mobilizer,RHS-B,RMPS,Hakeems,Homeopaths,PLDs,TBAs,Counters,DDPs</div>

                                            <?php
                                            if($selProv==3){
                                                echo '<div class="'.(($hide_graphs)?' hide ':' ').'"  style=" font-size: 10px"> * SDP data of MNCH and LHW Program in Khyber Pakhtunkhwa is not reported in LMIS</div>';
                                            }
                                            if($selProv==4){
                                                echo '<div class="'.(($hide_graphs)?' hide ':' ').'"  style=" font-size: 10px"> * SDP data of  MNCH and LHW Program in Balochistan is not reported in LMIS</div>';
                                            }
                                            ?>
                                        </div>








                                        <div class="col-md-12 ">
                                            <div class="widget1 widget-tabs">    
                                                <div class="widget-body" style="height:auto;overflow: auto;">

                                                    <p style=" font-size: 10px">
                                                        <?php 
                                                        $dashlet_id='122';
                                                        if(!empty($comments_arr[$dashlet_id]))
                                                            foreach($comments_arr[$dashlet_id] as $k=>$comment)echo (!empty($comment)?$comment.'</br></br>':'');
                                                        ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                            </div>
                            
                            <div class="well">
                                <div class="row hide_divs well bg-grey-cascade page-title <?=(($hide_graphs)?'hide':' ')?>">
                                    <div class="col-md-12 center ">
                                        <div class="col-md-4 center">
                                                Product : 
                                        </div>
                                    <div class="col-md-4 center">
                                        <select    name="product_second"  id="product_second" class="form-control input-sm" onchange="loadDashlets_2('#dashboard_ss_b3,#dashboard_ss_b4','')">

                                            <?php
                                            $queryprov = "SELECT
                                                        itminfo_tab.itm_id,
                                                        itminfo_tab.itm_name
                                                        FROM
                                                        itminfo_tab
                                                        WHERE
                                                        itminfo_tab.itm_category = 1 AND
                                                        itminfo_tab.method_type IS NOT NULL
                                                        ORDER BY
                                                        itminfo_tab.method_rank ASC
                                                ";
                                            //query result
                                            $rsprov = mysql_query($queryprov) or die();

                                            while ($rowprov = mysql_fetch_array($rsprov)) {
                                                if($itm_arr_request[0] == $rowprov['itm_id'] ) $sel=' selected ';
                                                else $sel='';

                                                ?>
                                                    <option   value="<?php echo $rowprov['itm_id']; ?>" <?php echo $sel; ?> ><?php echo $rowprov['itm_name']; ?></option>
                                                <?php
                                                }

                                        ?>
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                <div class="row hide_divs">
                                    <div class="col-md-12 center">
                                        <div class="col-md-6 ">
                                                <div class="<?=(($hide_graphs)?' ':'dashlet_graph_html')?>"  id="dashboard_ss_b4" href="dashboard_fp2020_b4.php"></div>
                                        </div>
                                        <div class="col-md-6 ">
                                                <div class="<?=(($hide_graphs)?' ':'dashlet_graph_html')?>"  id="dashboard_ss_b3" href="dashboard_fp2020_b3.php"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 ">
                            <div class="<?=(($hide_graphs)?' hide ':' ')?>"  style=" font-size: 10px"> * These SDP Types are not included in the calculations of SDP Level :MSU,Social Mobilizer,RHS-B,RMPS,Hakeems,Homeopaths,PLDs,TBAs,Counters,DDPs</div>
                        </div>
                        <div class="row">
                                <div class="col-md-12 my_dash_cols">
                                   <div class="col-md-12 ">
                                    <div class="widget1 widget-tabs">    
                                        <div class="widget-body" style="height:auto;overflow: auto;">
                                            
                                            <p style=" font-size: 10px">
                                                    <?php 
                                                    $dashlet_id='0';
                                                    if(!empty($comments_arr[$dashlet_id]))
                                                        foreach($comments_arr[$dashlet_id] as $k=>$comment)echo (!empty($comment)?$comment.'</br></br>':'');
                                                    ?>
                                                </p>
                                        </div>
                                    </div>
                                   </div>     
                                   
                                </div>
                        </div>
                