<?php
require_once("application/includes/classes/Configuration.inc.php");
include_once('public/html/html.inc.php');
startHtml('Uptime Robot');
siteMenu("Uptime Robot");
?>
<style>
.green {
	color: #000;
}
.nav-tabs > li > a:hover, .nav-pills > li > a, .nav-pills > li > a:hover {
	color: #000;
}
</style>

<div class=" page-content-wrapper">
    <div class="page-content landing-content"><br />
        <div class="col-md-12 ">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">Uptime Robot</div>
                </div>
                <div class="portlet-body" style="min-height:300px;">
                    
                    
                    <iframe style="border:none;" width="100%" height="1000" src="https://stats.uptimerobot.com/LZyZoCMKQ/779326964"></iframe> 
                
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
</div>
</div>
<?php
endHtml();
?>