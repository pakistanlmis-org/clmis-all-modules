<?php
require_once("application/includes/classes/Configuration.inc.php");
include_once('public/html/html.inc.php');
startHtml('Contact Us');
siteMenu("Contact Us");
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
                    <div class="caption">Release Notes</div>
                </div>
                <div class="portlet-body" style="min-height:300px;">
                    <table class="">
                        <tbody>
                        <tr>
                            <td><h4>cLMIS Release Notes 2.4 (September 26, 2017)</h4></td>
                            <td style="padding-left: 100px;"><a class="btn btn-primary input-sm green" target="_blank" href="http://lmis.gov.pk/release_notes/cLMIS_Release_Notes_v2.4.pdf">Download</a></td>
                        </tr>
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                    <tr>
                            <td><h4>cLMIS Release Notes 2.3 (June 2, 2017)</h4></td>
                            <td style="padding-left: 100px;"><a class="btn btn-primary input-sm green" target="_blank" href="http://lmis.gov.pk/release_notes/cLMIS_Release_Notes_v2.3.pdf">Download</a></td>
                        </tr>
                        </tbody>
                    </table>
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