<?php
// Include configuration file
require_once("../../application/includes/classes/Configuration.inc.php");
//include header
include(PUBLIC_PATH . "html/header.php");
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content" style="margin-left:0px !important;">
                <div class="row">
                    <div class="col-md-12"> 
                        <!-- BEGIN ALERTS PORTLET-->
                        <div class="portlet yellow box">
                            <div class="portlet-title">
                                <div class="caption"> <i class="fa fa-cogs"></i>Contraceptives indicator definitions </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-12 right">
                                        Reference: Chapter 6, Logistics Management Information System, Page 22 - <a href="http://lmis.gov.pk/docs/3_punjab/1_contraceptives_logistics_manual_english_punjab/contraceptives_logistics_manual_english_punjab.pdf" target="_blank">Contraceptives Logistics Manual</a>
                                    </div>
                                </div>
                                <div class="note note-success">
                                    <h4 class="block">Consumption</h4>
                                    <p> It is the number (quantity) of contraceptives dispensed / issued to the clients/users at the facility level. However, in case facility level issuance data is not available issuance of contraceptives to facilities by district store can be considered as proxy for consumption </p>
                                </div>
                                <div class="note note-warning">
                                    <h4 class="block">Average Monthly Consumption</h4>
                                    <p> It is the average aggregated consumption (of a contraceptive) of the last three non-zero consumption months </p>
                                </div>
                                <div class="note note-success">
                                    <h4 class="block">Stock on Hand - National Level</h4>
                                    <p> It is the sum of the quantity of usable stock available in facilities, district stores, provincial stores and national store at a given time</p>
                                </div>
                                <div class="note note-warning">
                                    <h4 class="block">Stock on Hand - Provincial Level</h4>
                                    <p> It is the sum of the quantity of usable stock available in facilities, district stores and provincial store at a given time</p>
                                </div>
                                <div class="note note-success">
                                    <h4 class="block">Stock on Hand - District Level</h4>
                                    <p> It is the sum of the quantity of usable stock available in facilities and district store at a given time</p>
                                </div>
                                <div class="note note-warning">
                                    <h4 class="block">Stock on Hand - Field Level</h4>
                                    <p> It is the quantity of usable stock available at facility level in a district at a given time</p>
                                </div>
                                <div class="note note-success">
                                    <h4 class="block">Stock on Hand - Store</h4>
                                    <p> It is the quantity of usable stock available in a store at a given time</p>
                                </div>
                                <div class="note note-warning">
                                    <h4 class="block">Months of Stock - National Level</h4>
                                    <p> It is the number of months, the available stock (stock on hand) at a given time at national level will last or will be sufficient for. It can be calculated by dividing the available quantity (stock on hand) at national level by AMC at national level.<br />
                                        MOS =  SOH (Stock on Hand) / AMC (Average Monthly Consumption)</p>
                                </div>
                                <div class="note note-success">
                                    <h4 class="block">Months of Stock - Provincial Level</h4>
                                    <p> It is the number of months, the available stock (stock on hand) at a given time at provincial level will last or will be sufficient for. It can be calculated by dividing the available quantity (stock on hand) at a provincial level by AMC at provincial level.<br />
                                        MOS =  SOH (Stock on Hand) / AMC (Average Monthly Consumption)</p>
                                </div>
                                <div class="note note-warning">
                                    <h4 class="block">Months of Stock - District Level</h4>
                                    <p> It is the number of months, the available stock (stock on hand) at a given time at district level will last or will be sufficient for. It can be calculated by dividing the available quantity (stock on hand) at a district level by AMC at district level.<br />
                                        MOS =  SOH (Stock on Hand) / AMC (Average Monthly Consumption)</p>
                                </div>
                                <div class="note note-success">
                                    <h4 class="block">Months of Stock - Field Level</h4>
                                    <p> It is the number of months, the available stock (stock on hand) at a given time in facility level stores will last or will be sufficient for. It can be calculated by dividing the available quantity (stock on hand) in facility level stores by AMC of that store.<br />
                                        MOS =  SOH (Stock on Hand) / AMC (Average Monthly Consumption)</p>
                                </div>
                                <div class="note note-warning">
                                    <h4 class="block">Months of Stock – Store</h4>
                                    <p> It is the number of months, the available stock (stock on hand) at a given time in a store will last or will be sufficient for. It can be calculated by dividing the available quantity (stock on hand) in a store by AMC of that store.<br />
                                        MOS =  SOH (Stock on Hand) / AMC (Average Monthly Consumption)</p>
                                </div>
                                <div class="note note-success">
                                    <h4 class="block">Couple Year Protection</h4>
                                    <p> The term Couple Year Protection (CYP) is used to estimate the quantity or the number of a specific type of contraceptive required to protect a couple from contraception / pregnancy for one year</p>
                                </div>
                                <div class="note note-warning">
                                    <h4 class="block">Reporting Rate (in percentage)</h4>
                                    <p> It is the percentage of stores / SDPs reported in a given time period</p>
                                </div>
                                <div class="note note-success">
                                    <h4 class="block">Stock Issued</h4>
                                    <p> It is the number (quantity) of contraceptives given to a store / health facility</p>
                                </div>
                                <div class="note note-warning">
                                    <h4 class="block">Stock Received</h4>
                                    <p> It is the number (quantity) of contraceptives received from a store</p>
                                </div>
                                <div class="note note-success">
                                    <h4 class="block">Contraceptives Min and Max:</h4> <p>
                                        <b>1.	Provincial level:</b> keep the contraceptives for a minimum of 6 months and maximum of 18 months: </p><p> 
                                        <b>2.	District level:</b> keep the contraceptives for a minimum of 3 months and maximum of 6 months: </p><p> 
                                        <b>3.	Health facility level:</b> keep the contraceptives for a minimum of 1 month and maximum of 3 months <br>
                                        <i>Ref: Page 62 Contraceptives Logistics Manual</i></p>
                                </div>                             
                            </div>
                        </div>
                        <!-- END ALERTS PORTLET--> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12"> 
                        <!-- BEGIN ALERTS PORTLET-->
                        <div class="portlet green box" style="color: black;">
                            <div class="portlet-title">
                                <div class="caption"> <i class="fa fa-cogs"></i>Vaccines indicator definitions </div>
                            </div>
                            <div class="portlet-body">
                                <div class="note note-success">
                                    <h4 class="block">Generic logistics definition</h4>
                                    <p> <b>Reporting Rate</b> = No of Reporting UCs / Total UCs</p><p> 
                                        <b>Average Monthly Consumption (AMC)</b> = Sum of Consumption of last 3 non-zero months / 3</p><p> 
                                        <b>Stock on Hand (SOH)</b> = Closing Balance of the Month</p><p> 
                                        <b>Months of Stock (MOS)</b> = SOH/Target 
                                    </p>
                                </div>
                                <div class="note note-warning">
                                    <h4 class="block">Vaccine Coverage: </h4>
                                    <p> <b>Inside UC</b> = Fixed + Outreach + Defaulter Covered</p><p> 
                                        <b>Outside UC</b> = Fixed + Referred by LB</p><p> 
                                        <b>Consumption Inside UC</b>= Total Children Vaccinated Inside UC + Referral</p><p> 
                                        <b>Consumption Total</b>= Total Children Vaccinated Inside + Outside UC</p><p> 
                                        <b>Dropout Rate</b> = (Penta I – Penta III / Penta I) * 100</p><p> 
                                        <b>Wastage Rate</b> = (Total Wastage/ (Total Consumption + Total Wastages)) * 100<br>
                                        <i>Ref: National EPI Policy and Strategic Guidelines 2014</i></p><p> 

                                        <b>Target</b>: Live birth (Population 3.5%) BCG and OPV-0 only, Surviving children= 92.3 % of live births</p><p> 
                                        <b>Annualized Coverage Rate</b> = ((Total Consumption/Annual Target) /Total Months *12) * 100<br>
                                        <i>Ref: Administrative coverage excel sheets data (EPI Program)</i></p><p> 

                                        <b>Wastage Permissible</b> = Wastage within allowed percentage</p><p> 
                                        <b>Over Wastage</b> = Wastage above allowed percentage 
                                        <i>Ref: National EPI Policy and Strategic Guidelines 2014 Page 18</i></p> 

                                    <h4 class="block">Vaccine Min and Max:</h4> <p> 
                                        <b>1.	National level:</b> keep the vaccines for a maximum of 6 months: </p><p> 
                                        <b>2.	Provincial level:</b> keep the vaccines for a maximum of 3 months: </p><p> 
                                        <b>3.	District level:</b> keep the vaccines for a maximum of 1 month: </p><p> 
                                        <b>4.	Health facility level:</b> keep all the vaccines for a maximum of 2 weeks <br>
                                        <i>Ref: Page 31 & 34 Vaccine Logistics Manual & National EPI Policy and Strategic Guidelines 2014 Page 17</i></p>
                                    <h4 class="block">Batch Management: </h4><p> 
                                        <b>Priority 1:</b> If VVM stage is 2 or expiry is less than 3 months.</p><p> 
                                        <b>Priority 2:</b> If VVM stage is 1 and expiry is more than 3 months and less than 12 months.</p><p> 
                                        <b>Priority 3:</b> If VVM stage is 1 and expiry is more than 12 months<br>
                                        <i>Ref: Vaccine Logistics Manual page 135</i></p>

                                    <h4 class="block">CCEM</h4><p>  
                                        <b>Capacity required per dose: </b><br>
                                        <i>Ref: National EPI Policy and Strategic Guidelines 2014 Page 20</i></p><p> 
                                        <b>Gross Capacity</b> = Gross Capacity 20 + Gross Capacity 4 </p><p> 
                                        <b>Net Usable Capacity</b> = Net Capacity 20 + Net Capacity 4 </p><p> 
                                        <b>Being Used</b> = (Placed Quantity X Volume per vial) / 1000 <br>
                                        <i>Ref: National EPI Policy and Strategic Guidelines 2014 Page 20 also refer to use following standards given in Cold Chain Equipment Manager tool (CCEM-II) also CMYP Page 31 </i></p><p> 

                                        <b>Required Capacity</b>= target population x expected coverage x number of doses of the particular vaccine required x wastage factor x Volume per vial<br>
                                        <i>Ref: (Vaccine Logistics Manual page 23 for Calculating Order Size and Cold Chain Equipment Manager tool (CCEM-II) by Washington University)</i>
                                    </p>
                                </div>

                                <div class="note note-success">
                                    <h4 class="block">Reference documents link:</h4>
                                    <p> <a href="http://lmis.gov.pk/docs/training_manuals/vlmis_user_&_training_manuals/vaccines_logistics_manual-cold_chain-updated.pdf" target="_blank">Vaccines Logistics Manual</a></p>
                                    <p> <a href="http://epi.gov.pk/wp-content/uploads/2014/09/National-cMYP.pdf" target="_blank">Comprehensive Multi-Year Plan, Immunization Program of Pakistan</a></p>
                                    <p> <a href="http://lmis.gov.pk/docs/National_epi_policy_and_strategic_guidelines_pakistan_2014.pdf" target="_blank">National EPI Policy and Strategic Guidelines 2014</a></p>
                                </div>
                            </div>
                        </div>
                        <!-- END ALERTS PORTLET--> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<!-- END BODY -->
</html>