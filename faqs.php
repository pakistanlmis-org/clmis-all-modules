<?php 
require_once("application/includes/classes/Configuration.inc.php");
include_once('public/html/html.inc.php');
startHtml('FAQ');
siteMenu("FAQs");
?>
<style>
	.green{ color:#000;}
	.nav-tabs > li > a:hover, .nav-pills > li > a, .nav-pills > li > a:hover{color:#000;}
</style>

<div class=" page-content-wrapper">
    <div class="page-content landing-content"><br />

        <div class="col-md-12 "> 
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-folder"></i>Frequently Asked Questions
                    </div>
                </div>
                <div class="portlet-body">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a data-toggle="tab" href="#tab_1_1">
                                Logistics Management (Draft) </a>
                        </li>
                        <li class="">
                            <a data-toggle="tab" href="#tab_1_2">
                                Contraceptive LMIS (Draft) </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab_1_1" class="tab-pane fade active in">
                            <ol><li><b>What is Logistics? </b> 
                                    <p>Supply chain management encompasses the planning and management of all activities involved in sourcing and procuremen&hellip;and all logistics management activities. Importantly, it also includes coordination and collaboration with channel partners, which can be suppliers, intermediaries, third party service providers, and customers. In essence, supply chain management integrates supply and demand management within and across companies.</p>
                                </li>
                                <li><b>Why Logistics Matters?</b>
                                    <p>The goal of a health logistics system is much larger than simply making sure a product gets where it needs to go. Ultimately, the goal of every public health logistics system is to help ensure that every customer has commodity security. Commodity security exists when every person is able to obtain and use quality essential health supplies whenever he or she needs them. A properly functioning supply chain is a critical part of ensuring commodity security&ndash;financing, policies, and commitment are also necessary.</p></li>
                                <li><b>How Logistics helps in improving Public Health?</b>
                                    <p>Effective supply chains not only help ensure commodity security, they also help determine the success or failure of any public health program. Both in business and in the public sector, decision makers increasingly direct their attention to improving supply chains, because logistics improvements bring important, quantifiable benefits. Well-functioning supply chains benefit public health programs in important ways by&ndash;</p>
                                    <ul><li> increasing program impact</li>
                                        <li> enhancing quality of care</li>
                                        <li> improving cost effectiveness and efficiency.</li></ul></li>
                                <li><b>What is logistics management Information system?</b>
                                    <p>It is an online web application which captures the stock indicators data from Service delivery points and informs it users about Stock positions, stock out and consumption pattern for the purpose of smooth supply of commodities by quantification and procurement of commodities.</p></li>
                                <li><b>What is LMIS and how it is different from DHIS?</b>
                                    <p>An LMIS collects data about commodities; this information is often used for activities, such as filling routine supply orders for health facilities. DHIS collects information on the total number of patients seen or diagnosed; data from DHIS is not used as often as LMIS data&ndash; i.e., annually&ndash;and it is used for different purposes&ndash;i.e., for evaluating program impact. Logisticians emphasize the use of logistics data for making decisions about activities within the logistics cycle.</p></li>
                            </ol>

                            <p style="text-align: right;">Last Modified: 11/Mar/2014 </p>
                        </div>
                        <div id="tab_1_2" class="tab-pane fade">
                            <ol><li><b>What is Contraceptive logistics management system?</b>
                                    <p>It is an online web Logistics Management Information System (LMIS) for different products used for different birth spacing methods. This system is hosted at lmis.gov.pk and one can find it by log in under contraceptive tab. This system monthly captures different stock indicators entered by different public and Private sector stakeholders across Pakistan. This LMIS aggregates and calculate different Logistics indicators in the forms of reports and graphs.</p></li> 
                                <li><b>Which are data entry stock indicators?</b>
                                    <p>These indicators are opening balance, stock receive, stock issue, positive/negative adjustments and closing stock.</p></li>
                                <li><b>Data enters at what levels?</b>
                                    <p>Data enters from different stores at National, District and Sub-District levels across Pakistan.</p></li>
                                <li><b>Data enters at what intervals?</b>
                                    <p>Data enter on monthly basis.</p> </li>
                                <li><b>Who entered data in to Contraceptive LMIS?</b>
                                    <p>Following organizations enters the data
                                    <ol><li>	Public Sector: Public Welfare Department, Department of Health, Lady Health Worker Program, People's Primary HealthCare Initiative (PPHI), Chief Minister's Initiative for Primary Healthcare (CMIPHC)</li>
                                        <li>	Private Sector: Marie Stopes Society (MSS), Green Star Marketing, MNCH Program, Jhpiego</li>

                                        <li><b>What are the output Indicators of contraceptive LMIS?</b></li>
                                        <li>	Couple Year Protection (CYP)</li>
                                        <li>	Consumption</li>
                                        <li>	Average Monthly Consumption (AMC)</li>
                                        <li>	Stock on Hand (SoH)</li>
                                        <li>	Months of Stock (MoS)</li>
                                    </ol>
                                    </p>
                                </li>
                                <li><b>What is Couple Year Protection (CYP) in Contraceptive LMIS?</b>
                                    <p>The estimated protection provided by family planning (FP) services during a one-year period, based upon the volume of all contraceptives sold or distributed free of charge to clients during that period. The CYP is calculated by multiplying the quantity of each method distributed to clients by a conversion factor, to yield an estimate of the duration of contraceptive protection provided per unit of that method.</p></li>
                                <li><b>What is Consumption in Contraceptive LMIS?</b>
                                    <p>Consumption is issue of contraceptive commodities to its end user at the Health or Social Welfare Facility.</p></li>
                                <li><b>What is Average Monthly Consumption (AMC) in Contraceptive LMIS?</b>
                                    <p>Average Monthly Consumption is calculated as average of aggregated consumption of the last three non-zero consumption months of contraceptive Products.</p></li>
                                <li><b>What is Stock on Hand (SoH) in Contraceptive LMIS?</b>
                                    <p>Stock on Hand is the amount of product on hand in order to monitor stock positions and anticipate stock outs in advance.</p></li>
                                <li><b>What is Months of Stock (MoS) in Contraceptive LMIS?</b>
                                    <p>Month of Stocks is the estimate of number of months the stock will last. This obtains by dividing Stock of Hand by average monthly consumption.</p></li>
                                <li><b>What are Summary reports?</b>
                                    <p>Summary Reports are based on Logistics Indicators i.e. Couple Year Protection, Consumption, Average Monthly Consumption, Stock on Hand and Month of Stock calculated on various levels from sub District (Field) to National level.</p></li>
                                <li><b>What is vLMIS explorer?</b>
                                    <p>The LMIS explorer enables you to view the previously submitted Monthly Consumption Report data for the selected warehouse and the specified month - year.</p></li>
                                <li><b>Is Inventory Control System is included in Contraceptive LMIS?</b>
                                    <p>No, Warehouse Management System (WMS) is a separate software for inventory management, which is installed at Central warehouse for contraceptive products under the administration of Ministry of National Health Services, Regulations & Coordination (MoNHSRC)</p>
                                </li></ol> <p style="text-align: right;">Last Modified: 11/Mar/2014 </p>
                        </div>
                    </div>
                    <div class="clearfix margin-bottom-20">
                    </div>                       

                </div>
            </div>
        </div>
        <!-- BEGIN  PATNERS -->
        <div class="row">
            <div class="col-lg-12">
                <div class="tab-content stake-holder">stakeholders</div>
                <ul class="nav nav-tabs stake-holder-list">
                    <li> <img src="<?php echo PUBLIC_URL; ?>assets/frontend/layout/img/contraceptive-partners/govt-of-pak-logo.png" alt=""/></li>
                    <li> <img src="<?php echo PUBLIC_URL; ?>assets/frontend/layout/img/contraceptive-partners/us-aid-logo.png" alt=""/></li>
                </ul>                    
            </div>
        </div>
        <!-- END PATNERS -->
    </div>
</div>


<script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script>
var Metronic = function () {
	var handleTabs = function () {
		//activate tab if tab id provided in the URL
		if (location.hash) {
			var tabid = location.hash.substr(1);
			$('a[href="#' + tabid + '"]').parents('.tab-pane:hidden').each(function(){
				var tabid = $(this).attr("id");
				$('a[href="#' + tabid + '"]').click();    
			});            
			$('a[href="#' + tabid + '"]').click();
		}
	}
}
</script>
<?php
endHtml();
?>