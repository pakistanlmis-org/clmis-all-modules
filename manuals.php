<?php
include("application/includes/classes/AllClasses.php");
include(PUBLIC_PATH."html/header.php");
?>

<style>
    h4{ color:#666666 !important; margin:20px 0 10px 0 !important;}
    p{line-height:1.5 !important;}
    .thumbnail{
        width: 114px;
        height: 160px;
        border: solid #D3D3D3 1px;
    }
</style>
<script>
    $(function () {
        $("#tabs").tabs();
        // Hover states on the static widgets
        $("#dialog-link, #icons li").hover(
                function () {
                    $(this).addClass("ui-state-hover");
                },
                function () {
                    $(this).removeClass("ui-state-hover");
                }
        );
    });

    /* DataTables */
    if ($('.receiveSearch').size() > 0)
    {
        var datatable = $('.receiveSearch').dataTable({
            "sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
            "sDom": 'T<"clear">lfrtip',
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "oColumnFilterWidgets": {
                "aiExclude": [0, 5, 6, 7, 8, 9]
            },
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "copy",
                        "sButtonText": "Copy",
                        "mColumns": [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    }

                ],
                "sSwfPath": appName + "/common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }
</script>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <!-- BEGIN HEADER -->


    <div class="page-container">
    	<?php echo include ($_SESSION['menu']);?>
        <?php include PUBLIC_PATH."html/top_im.php";?>
        <div class="page-content-wrapper">
            <div class="page-content">

                <!-- BEGIN PAGE HEADER-->
                <div class="row">

                    <div class="row">
                        <div class="col-md-12">

                            <div class="box-generic">

                                <!-- Tabs Heading -->
                                <div class="tabsbar">
                                    <ul>
                                        <li class="glyphicons camera active"><a href="#tab1-3" data-toggle="tab"> <b>Contraceptives</b></a></li>
                                        <li class="glyphicons folder_open"><a href="#tab2-3" data-toggle="tab"> <b>Courses</b></a></li>
                                        <li class="glyphicons circle_plus tab-stacked"><a href="#tab3-3" data-toggle="tab"> <b>Logistics</b></a></li>
                                    </ul>
                                </div>
                                <!-- // Tabs Heading END -->

                                <div class="tab-content">

                                    <!-- Tab content -->
                                    <div class="tab-pane active" id="tab1-3">
                                        <p>The USAID | DELIVER PROJECT Pakistan has been tasked by the Government of Pakistan with implementing a functioning LogisticsManagement Information System (LMIS) using a web based approach. During enhancement process LMIS was contextualized to local stakeholder structure and devolution. The LMIS was launched by Prime Minister of Pakistan on July 2011 as first Logistics Management Information System of Pakistan.</p>
                                        <p>LMIS has the flexibility to integrate other health commodities in addition to contraceptives. In addition to public sector, application is also able to record contraceptives national sales data of private sector. Currently, the system is able to respond to district level reporting by aggregating facilities level data through paper based reports. The future vision is to enhance the application for facility level reporting on logistics indicators for each district along with district store commodities status. To strengthen reporting and visibility of private sector contraceptives, district level interface will also be incorporated in LMIS applications which will enables provincial and regional health and population departments to see the contribution of private sector in their geographical areas.</p>
                                        <p>For nationwide implementation of LMIS, the Project needed to train the expected users of the system on its uses and functionality. The two day training provides skills and knowledge required to independently enter and upload data into web-based LMIS. Web-based LMIS played a significant role in improving stock monitoring at the district level. The real time monitoring helped eliminate stock-outs at district level.</p>
                                        <p>Timely and accurate data entry and submission of a monthly report at the district level is critical to the functioning of the LMIS. The data collected from the LMIS can then subsequently be used at each level of the supply chain to enhance informed decision making to meet service delivery demands. Utilization of the LMIS will depend heavily on the level of understanding of those trained on its various functionalities.</p>
                                        <p>The below mentioned training material has been developed to train the master trainers and different level users on LMIS</p>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Essential Medicines List   
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/eml/final_eml_punjab.pdf">Punjab</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/eml/final_eml_balochistan.pdf">Balochistan</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/eml/final_eml_kpk.pdf">KPK</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/eml/final_eml_sindh.pdf">Sindh</a>
                                                </h4>
                                                <p>
                                                    The Essential medicines list document presents the list of minimum medicine 
                                                    need for a basic healthcare system. Its list the most efficacious, safe and 
                                                    cost effective medicines for priority conditions. Priority conditions are 
                                                    determined on the basis of current and estimated future public health relevance 
                                                    and the potential for safe and cost effective treatment.  
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <img src="http://lmis.gov.pk/images/manuals/clmis/essential_medicines_list.png"  alt="Essential Medicines List"
                                                     class="thumbnail">
                                            </div>-->
                                        </div>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Contraceptive Logistics Manual   
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_english/clm_punjab.pdf">Punjab - En</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_english/clm_balochistan.pdf">Balochistan - En</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_english/clm_khyber_pakhtunkhwa.pdf">KPK - En</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_english/clm_sindh.pdf">Sindh - En</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_english/clm_ajk.pdf">AJK - En</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_english/clm_fata.pdf">FATA - En</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_english/clm_gb.pdf">GB - En</a>
                                                </h4>
                                                <h4>Contraceptive Logistics Manual Urdu 
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_urdu/clm_urdu_punjab.pdf">Punjab - Ur</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_urdu/clm_urdu_balochistan.pdf">Balochistan - Ur</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_urdu/clm_urdu_kpk.pdf">KPK - Ur</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_urdu/clm_urdu_sindh.pdf">Sindh - Ur</a>
                                                </h4>
                                                <h4>Contraceptive Logistics Manual Sindhi 
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/clm_sindhi/clm_sindhi.pdf">Punjab - Sindhi</a>
                                                </h4>
                                                <p>
                                                    This manual basic information about the techniques required to manage contraceptive commodities. 
                                                    The operational guidelines in this manual will assist the readers working in 
                                                    all parts of the logistics system, including those managing the information and inventory system.   
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <img src="http://lmis.gov.pk/images/manuals/clmis/contraceptive_logistics_manual.png"  alt="Contraceptive Logistics Manual"
                                                     class="thumbnail">
                                            </div>-->
                                        </div>


                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Medicines And Supplies Procurement Manual   
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/empm/final_empm_punjab.pdf">Punjab</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/empm/final_empm_kpk.pdf">KPK</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/empm/final_empm_sindh.pdf">Sindh</a>
                                                </h4>
                                                <p>
                                                    This manual is a practical resource for the procurement and management of the medicine 
                                                    supplies at primary and secondary health care facilities. It also gives sound perspective 
                                                    to those involved in health planning and management, training and managing medical stores 
                                                    at the provincial level. This manual incorporates guiding principles for selecting medicines 
                                                    and supplies along with the guidelines on procurement. To encourage good procurement practices, 
                                                    the manual explains how to use standard lists as a tool, it also includes the Essential 
                                                    Medicine List: Primary and Secondary Healthcare Facilities.    
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <img src="http://lmis.gov.pk/images/manuals/clmis/medicines_and_supplies_procurement_manual.png"  alt="Medicines And Supplies Procurement Manual"
                                                     class="thumbnail">
                                            </div>-->
                                        </div>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Contraceptive Procurement Manual   
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/cpm/final_cpm_punjab.pdf">Punjab</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/cpm/final_cpm_kpk.pdf">KPK</a>
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/cpm/final_cpm_sindh.pdf">Sindh</a>
                                                </h4>
                                                <p>
                                                    This manual offers valuable information for the procurement of both the Health and Population 
                                                    Welfare Departments about the guidelines that are required when procuring contraceptives of good 
                                                    quality in the international market. This aligns with the Government’s efforts to meet the desired 
                                                    goals in maternal and child health within the province. This manual also incorporates the best 
                                                    international procurement practices, promoting and encouraging transparency, accountability 
                                                    and efficient during public sector procurement planning, issuance of bid invitation, bid evaluation, 
                                                    supplier selection, contract award and management.    
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <img src="http://lmis.gov.pk/images/manuals/clmis/contraceptive_procurement_manual.png"  alt="Contraceptive Procurement Manual"
                                                     class="thumbnail">
                                            </div>-->
                                        </div>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Federal EPI Warehouse Procedures Monitoring Checklist   
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/cw_s_publications/warehouse_procedures_monitoring_checklist.pdf">Download</a>
                                                </h4>
                                                <p>
                                                    This checklist with detailed warehouse procedures ensure best warehousing practices and 
                                                    procedures are adopted and followed. Moreover, the checklist can help the EPI warehouse 
                                                    to determine if it has delivered against strategic priorities, met service delivery 
                                                    obligations and key stakeholder expectations and therefore reduced key risk indicators. 
                                                    This will also help the project to assess what is working and what is not, make 
                                                    adjustments to plans and strategies, and address new workforce and organizational issues that may arise.   
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <a target="_blank"
                                                   href="http://lmis.gov.pk/training_manuals/clm/cw_s_publications/warehouse_procedures_monitoring_checklist.pdf">
                                                    <img src="http://lmis.gov.pk/images/manuals/clmis/federal_epi_warehouse_procedures_monitoring_checklist.png"  alt="Federal EPI Warehouse Procedures Monitoring Checklist"
                                                         class="thumbnail">
                                                </a>
                                            </div>-->
                                        </div>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Staff Health and Safety Manual   
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/cw_s_publications/warehouse_staff_safety_manual.pdf">Download</a>
                                                </h4>
                                                <p>
                                                    This manual assist in achieving workplace safety and security by guiding the teams and 
                                                    individuals on how to carry out self-assessment of their surroundings. 
                                                    This will also be very beneficial for each warehouse department 
                                                    (Store, Administration, Transport, and Finance) to perform a hazard assessment against specific workplace 
                                                    safety and security issues as indicated in the checklist. 
                                                    The safety and security checklist assessment helps to determine risks for the warehouse employees, 
                                                    supplies, and assets as well as to evaluate their susceptibility to workplace 
                                                    violence by sharing their findings and observations with the responsible departmental heads or managers.    
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <a target="_blank"
                                                   href="http://lmis.gov.pk/training_manuals/clm/cw_s_publications/warehouse_staff_safety_manual.pdf">
                                                    <img src="http://lmis.gov.pk/images/manuals/clmis/staff_health_and_safety_manual.png"  alt="Staff Health and Safety Manual"
                                                         class="thumbnail"></a>

                                            </div>-->
                                        </div>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Warehouse Standard Operating Procedure   
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clm/cw_s_publications/warehouse_standard_operating_procedures.pdf">Download</a>
                                                </h4>
                                                <p>
                                                    Standard operating procedures (SOPs) are written guidelines for various routine functions at the 
                                                    Central Warehouse, and they elaborate on how the warehouse receives, issues, stores, and disposes 
                                                    of supplies coming from suppliers in other countries. The SOP manual briefly explains the 
                                                    various steps required under each of these functions. It is hoped that the application and 
                                                    practical use of these SOPs will support operations at the warehouse by helping employees
                                                    to do their jobs more efficiently, reduce errors and variations, and make it easier to conduct 
                                                    employee performance appraisals as well as replicate processes across the warehouse.  
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <a target="_blank"
                                                   href="http://lmis.gov.pk/training_manuals/clm/cw_s_publications/warehouse_standard_operating_procedures.pdf">
                                                    <img src="http://lmis.gov.pk/images/manuals/clmis/warehouse_standard_operating_procedure.png"  alt="Warehouse Standard Operating Procedure"
                                                         class="thumbnail">
                                                </a>
                                            </div>-->
                                        </div>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Contraceptive Logistics Management Information System Facilitator Manual  
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clmis/clmis_facilitators_manual.pdf">Download</a>
                                                </h4>
                                                <p>
                                                    This facilitator manual contains the training sessions for the “Training on Pakistan’s 
                                                    Contraceptive Logistics Management Information System (cLMIS)”. 
                                                    The steps required to design, develop and deliver the training cLMIS can be 
                                                    found in the “LMIS Training Plan”. The Training Plan outlines the objectives, 
                                                    needs, strategy, and curriculum to be addressed when training users on LMIS.   
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <a target="_blank" href="http://lmis.gov.pk/training_manuals/clmis/clmis_facilitators_manual.pdf">
                                                    <img src="http://lmis.gov.pk/images/manuals/clmis/clmis_facilitator manual.png"  alt="cLMIS Facilitator Manual"
                                                         class="thumbnail">
                                                </a>
                                            </div>-->
                                        </div>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Contraceptive Logistics Management Information System Participants Guide   
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clmis/clmis_participants_guide.pdf">Download</a>
                                                </h4>
                                                <p>
                                                    This participant manual contains the training sessions for the “Training on 
                                                    Pakistan’s Contraceptive Logistics Management Information System (cLMIS)”. 
                                                    The steps required to design, develop and deliver the “Training on Pakistan’s 
                                                    Contraceptive Logistics Management Information System (cLMIS)” can be found in 
                                                    the “LMIS Training Plan”. The Training Plan outlines the objectives, needs, strategy, 
                                                    and curriculum to be addressed when training users on LMIS.   
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <a target="_blank" href="http://lmis.gov.pk/training_manuals/clmis/clmis_participants_guide.pdf">
                                                    <img src="http://lmis.gov.pk/images/manuals/clmis/clmis_participants_guide.png"  alt="cLMIS Participants Guide"
                                                         class="thumbnail">
                                                </a>
                                            </div>-->
                                        </div>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Training of Trainers Manual   
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clmis/clmis_tot_manual.pdf">Download</a>
                                                </h4>
                                                <p>
                                                    This step by step training of trainers manual contains the training sessions for the 
                                                    “Training on Pakistan’s Contraceptive Logistics Management Information System (cLMIS)”.
                                                    The steps required to design, develop and deliver the “Training on Pakistan’s 
                                                    Contraceptive Logistics Management Information System (cLMIS)” can be found in 
                                                    the “LMIS Training Plan”. The Training Plan outlines the objectives, needs, 
                                                    strategy, and curriculum to be addressed when training users on LMIS.   
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <a target="_blank" href="http://lmis.gov.pk/training_manuals/clmis/clmis_tot_manual.pdf">
                                                    <img src="http://lmis.gov.pk/images/manuals/clmis/clmis_training_of_trainers_manual.png"  alt="Training of Trainers Manual"
                                                         class="thumbnail">
                                                </a>
                                            </div>-->
                                        </div>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Contraceptive Logistics Management Information System User Manual   
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clmis/clmis_user_manual_pwd_users.pdf">Download</a>
                                                </h4>
                                                <p>
                                                    The cLMIS user guide provides step-by-step instructions which help you get started with 
                                                    Logistics Management Information System (LMIS). It provides guidelines on application 
                                                    features to manage logistics data. This user guide is organized according to the logical 
                                                    flow of LMIS features and describes tasks in the same order you can use while working with the application.   
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <a target="_blank" href="http://lmis.gov.pk/training_manuals/clmis/clmis_user_manual_pwd_users.pdf">
                                                    <img src="http://lmis.gov.pk/images/manuals/clmis/clmis_user_manual.png"  alt="cLMIS User Manual"
                                                         class="thumbnail">
                                                </a>
                                            </div>-->
                                        </div>

                                        <div class="row">
                                            <div class="col col-md-12">
                                                <h4>Contraceptive Logitics Management Ifnromation System PWD User Manual:    
                                                    <a class="btn btn-primary input-sm" target="_blank"
                                                       href="http://lmis.gov.pk/training_manuals/clmis/clmis_user_manual.pdf">Download</a>
                                                </h4>
                                                <p>
                                                    The cLMIS PWD user manual guide provides step-by-step instructions which help you get 
                                                    started with Logistics Management Information System (LMIS). It provides guidelines 
                                                    on application features to manage logistics data. This user guide is organized according 
                                                    to the logical flow of LMIS features and describes tasks in the same order you can use 
                                                    while working with the application.   
                                                </p>
                                            </div>
<!--                                            <div class="col col-md-2">
                                                <a target="_blank" href="http://lmis.gov.pk/training_manuals/clmis/clmis_user_manual.pdf">
                                                    <img src="http://lmis.gov.pk/images/manuals/clmis/clmis_pwd_user_manual.png"  alt="cLMIS PWD User Manual"
                                                         class="thumbnail">
                                                </a>
                                            </div>-->
                                        </div>



                                    </div>
                                    <!-- // Tab content END -->

                                    <!-- Tab content -->

                                    <div class="tab-pane" id="tab2-3">
                                        <p>For the first time Health Services Academy (HSA) Islamabad offers following courses, in conjunction with the USAID|DELIVER Project;</p>

                                        <ul id="bullets">
                                            <li>Three Credit Course for Supply Chain Management of Public Health Commodities</li>
                                            <li>Certificate Course on Supply Chain Management of Health Commodities</li>
                                        </ul>
                                        <p>The courses will help to increase the participants&#39; understanding of the fundamentals of logistics management and the relationship between supply chain logistics and commodity security. It also will strengthen the participants&#39; ability to implement improvements to basic elements of their logistics systems.</p>
                                        <h4>Lecturer&#39;s Guide: Three Credit Course for Supply Chain Management of Public Health Commodities</h4>
                                        <p>The Lecture Guide for the Three Credit Course on Supply Chain Management of Public Health Commodities is meant for lecturers whoare supposed to conduct the course. This curriculum is written following Adult Learning Theory principles which emphasize more interactive and participatory approaches than traditional teaching techniques. Small group exercises are placed into the curriculum and Lecturers are encouraged to run these activities as written where ever possible. The curriculum not only provides basic principles for running a logistic system but includes elements that are Pakistan specific. For this course each major unit of learning is referred to as a Session and each section within is referred to as an Activity. This course has 17 different Sessions. Lecturers are highly encouraged to read the Lecturer Preparation section at the beginning of each session well before leading each session. Note that Synthesis Questions are presented at the end of each session. These can be used to ensure students are grasping the key concepts of each session. Unlike the Certificate course this course is competency-based.</p>
                                        <a target="_blank" href="http://lmis.gov.pk/training_manuals/three_credit_lecturer_guide.pdf">Download</a>
                                        <h4>Student Guide: Three Credit Course on Supply Chain Management of Health Commodities</h4>
                                        <p>The Guide is meant for use of three credit course students.</p>
                                        <a target="_blank" href="http://lmis.gov.pk/training_manuals/three_credit_student_guide.pdf">Download</a>
                                        <h4><br>Lecturer&#39;s Guide: Certificate Course on Supply Chain Management of Health Commodities</h4>

                                        <p>The Lecture Guide for the Certificate Course on Supply Chain Management of Public Health Commodities is meant for lecturers who are supposed to conduct the course. This will become a three credit course and likely expand to a larger offering in time. This curriculum is written following Adult Learning Theory principles which emphasize more interactive and participatory approaches than traditional teaching techniques. Small group exercises are placed into the curriculum and Lecturers are encouraged to run these activities as written. The curriculum not only provides basic principles for running a logistic system but includes elements that are Pakistan specific. For this course each major unit of learning is referred to as a Session and each part of a Session is referred to as an Activity. This course has 16 different Sessions. Lecturers are highly encouraged to read the &#39;Lecturer Preparation&#39; section at the beginning of each session well before leading each session.</p>
                                        <a target="_blank" href="http://lmis.gov.pk/training_manuals/certificate_course_lect_guide.pdf">Download</a>
                                        <h4>Student Handbook: Certificate Course on Supply Chain Management of Health Commodities</h4>

                                        <p>The handbook is meant for use of certificate course students.</p>
                                        <a target="_blank" href="http://lmis.gov.pk/training_manuals/certificate_course_student_guide.pdf">Download</a>
                                    </div>
                                    <!-- // Tab content END -->

                                    <!-- Tab content -->
                                    <div class="tab-pane" id="tab3-3">
                                        <h4>Contraceptives Procurement Manuals</h4>
                                        <br>
                                        <p>The Province specific Contraceptive Procurement Manual has been developed to provide the Provincial Governments&#39; Population Welfare and Health Departments&#39; procurement personnel with the information and instructions needed to procure contraceptives of good quality on the international market to support the coordinated goals of the Provincial Governments towards improving maternal and child health in their respective provinces. The Contraceptive Procurement Manual incorporates best international procurement practices that help promote transparency, accountability and efficiency in the public sector procurement process.  The Procurement Manual addresses the key phases of the procurement cycle, from procurement planning and issuing invitations to bid, to bid evaluation, supplier selection, contract award and management.
                                        </p>
                                        <p><br>The primary audience for the Contraceptive Procurement Manual is procurement officers and other direct procurement staff who are assigned responsibility for procuring quality contraceptives.   The Procurement Manual provides these personnel with step-by-step instructions for completing standard bidding documents, opening bids from suppliers, evaluating supplier bids and monitoring supplier performance.   The manual also includes supplementary materials, such as information on pre-qualification and pre-shipment compliance programs, which are designed to support procurement officers in effectively implementing public sector procurement of contraceptives.</p>
                                        <p><br>The Contraceptive Procurement Manual also provides pertinent information for policymakers and mid-level decision makers who are not required to understand the detailed procedures of the procurement process, but should understand the overall procurement process for contraceptives and the role they can play to help ensure the procurement process is effectively implemented.  It is recommended that this audience review Appendix IV: Summary Guide for Policymakers, Directors and Managers.
                                            The Contraceptive Procurement Manual also includes key reference documents, such as the Public Procurement Rules 2010 and the Drugs (Labeling and Packing) Rules 1986, to ensure that procurement officers have access to original resource documents as they prepare for and conduct public sector procurement of contraceptives.
                                            Users of the Contraceptive Procurement Manual are encouraged to thoroughly review the Manual in order to fully understand the breadth and scope of the information it contains so that they can be fully prepared to conduct effective public sector procurement of quality contraceptives for the people of their respective provinces.
                                        </p><br>
                                        <a target="_blank" href="http://lmis.gov.pk/training_manuals/contraceptive_manual_kpk.pdf">Government of Khyber Pakhtunkhwa</a>
                                        <br/>
                                        <a target="_blank" href="http://lmis.gov.pk/training_manuals/contraceptive_manual_punjab.pdf">Government of Punjab</a>
                                        <br/>
                                        <a target="_blank" href="http://lmis.gov.pk/training_manuals/contraceptive_manual_sindh.pdf">Government of Sindh</a>
                                    </div>
                                    <!-- // Tab content END -->

                                </div>
                            </div>

                        </div>
                    </div>
                </div>


            </div>
        </div>
        <div class="clearfix"></div>
        <?php include PUBLIC_PATH."html/footer.php"; ?>


</body>
<!-- END BODY -->
</html>