<?php
/**
 * reporting_rate
 * @package maps
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required files
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
?>
<link href="<?php echo PUBLIC_URL; ?>css/map.css" rel="stylesheet" />
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/Charts/FusionCharts.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>FusionCharts/themes/fusioncharts.theme.fint.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>js/maps/html2canvas.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>js/maps/download.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>js/maps/symbology.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>js/maps/Legend.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>js/maps/reporting_rate.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>js/maps/Filter.js"></SCRIPT>
<SCRIPT LANGUAGE="Javascript" SRC="<?php echo PUBLIC_URL; ?>js/maps/refineLegend.js"></SCRIPT>
</head><!-- END HEAD -->

<!-- BEGIN BODY -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Reporting Rate Map</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <?php include(APP_PATH . "includes/maps/reportingRateForm.php"); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table width="100%">
                            <tr>
                                <td style="width:100%" align="right"><img id='excel' src="<?php echo PUBLIC_URL; ?>images/excel-32.png" style="cursor:pointer;width:35px;height:35px" /> <img id="image" src="<?php echo PUBLIC_URL; ?>images/map-icon.png" style="cursor:pointer;width:35px;height:35px" /> <img id="print" src="<?php echo PUBLIC_URL; ?>images/print.png" style="cursor:pointer; margin-left:-5px;width:35px;height:35px" /></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-8">
                            <div style="width:auto;height:auto">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="100%"><div id="map">
                                                <div id="customZoom"> <a href="#customZoomIn" id="customZoomIn">in</a> <a href="#customZoomOut" id="customZoomOut">out</a> </div>
                                                <div id="legendDiv">
                                                    <div>
                                                        <table id='legend'>
                                                        </table>
                                                    </div>
                                                </div>
                                                <img id="loader" src="<?php echo PUBLIC_URL; ?>images/ajax-loader.gif" />
                                                <div id="mapTitle"></div>
                                                <div id="printedDate"></div>
                                            </div></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <ul class="nav nav-tabs">
                                <li class="active"> <a data-toggle="tab" href="#tab-1">District Info</a> </li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane fade active in">
                                    <table border='1' class='infoTable'>
                                        <tr>
                                            <td class='bold'>Province</td>
                                            <td id='prov'></td>
                                        </tr>
                                        <tr>
                                            <td class='bold'>District</td>
                                            <td id='district'></td>
                                        </tr>
                                        <tr>
                                            <td class='bold'>Stakeholder</td>
                                            <td id='stakeholder'></td>
                                        </tr>
                                        <tr>
                                            <td class='bold'>Product</td>
                                            <td id='product'></td>
                                        </tr>
                                        <tr>
                                            <td class='bold'>Total Warehouses</td>
                                            <td id='total_warehouses'></td>
                                        </tr>
                                        <tr>
                                            <td class='bold'>Reported</td>
                                            <td id='reported'></td>
                                        </tr>
                                        <tr>
                                            <td class='bold'>Reporting rate ( % )</td>
                                            <td id='reporting_rate'></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <hr/>
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Reporting Rate Status</h3>
                                </div>
                                <div class="widget-body">
                                    <div id="pie"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">District Wise Reporting Rate Ranking</h3>
                                </div>
                                <div class="widget-body">
                                    <div id='districtRanking'></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">District Wise Reporting Rate Status</h3>
                                </div>
                                <div class="widget-body">
                                    <div id='attributeGrid'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include PUBLIC_PATH . "/html/footer.php"; ?>
</body>
<!-- END BODY -->

</html>