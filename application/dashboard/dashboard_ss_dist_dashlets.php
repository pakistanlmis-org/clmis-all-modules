<?php
//only for debugging purposes
$hide_graphs = true;
?>
                        
                    
                        
                        <div class=" row hide_divs">
                                
                                <div class="col-md-6 my_dash_cols">
                                    
                                    <div class="col-md-12 "  style=" ">
                                        <div class="center"><h4>Transactional Data</h4></div>
                                    </div>
                                    
                                    <div class="col-md-12 "  style=" ">
                                        <div class="dashlet_graph" id="dashboard_ss_b3" href='dashboard_ss_dist_a1.php'></div>
                                    </div>
                                    <div class="col-md-12 "  style="padding-left: 0;">
                                        <p style=" font-size: 10px">
                                            <ul style=" font-size: 10px">
                                            <li>Key: POP (Progestin only Pill), COC (Combined Oral Contraceptive); ECP (Emergency Contraceptive Pill), MoS (Months of Stock), AMC (Average Monthly Consumption)</li>
                                            <li>All above estimated MoS represents provincially procured and MoNHSR&C allocations from past USAID support</li>
                                            <li>These SDP Types are not included in calculating stock availability of SDP Level :MSU,Social Mobilizer,RHS-B,RMPS,Hakeems,Homeopaths,PLDs,TBAs,Counters,DDPs. (Where as they are reflected in reporting rate)</li>
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
                                    
                                    <div class="col-md-12 "  style=" ">
                                        <div class="center"><h4>Monthly Data</h4></div>
                                    </div>
                                    
                                    <div class="col-md-12 ">
                                        <div class="<?=(($hide_graphs)?' ':'dashlet_graph')?>" id="dashboard_ss_c1" href='dashboard_ss_dist_b1.php'>
                                            <?=(($hide_graphs)?' Stock availability graphs will be available after data entry of '.date('M Y',strtotime($fromDate)):'')?>
                                        </div>
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
                                        <div class="<?=(($hide_graphs)?' ':'dashlet_graph')?>" id="dashboard_ss_c2" href='dashboard_ss_dist_b2.php'></div>
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
                                    
                                    <div class="col-md-12 ">
                                        <div class="<?=(($hide_graphs)?' ':'dashlet_graph')?>" id="dashboard_ss_c3" href='dashboard_ss_dist_b3.php'></div>
                                    </div>
                                    <div class="col-md-12 ">
                                        <div class="<?=(($hide_graphs)?' hide ':' ')?>"  style=" font-size: 10px"> * These SDP Types are not included in the calculations of Stock Availability for SDP Level :MSU,Social Mobilizer,RHS-B,RMPS,Hakeems,Homeopaths,PLDs,TBAs,Counters,DDPs</div>
                                    </div>
                                    
                                    
                                </div>
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
               