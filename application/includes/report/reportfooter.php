<?php
/**
 * reportfooter
 * @package includes/reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
?>
<!--BEGIN Report Footer -->
<script src="../../plmis_js/jquery.js" type="text/javascript"></script>
  <link href="../../plmis_js/facebox/facebox.css" media="screen" rel="stylesheet" type="text/css"/>
  <script src="../../plmis_js/facebox/facebox.js" type="text/javascript"></script> 
  <script type="text/javascript">
            jQuery(document).ready(function($) {
              $('a[rel*=facebox]').facebox({
                loading_image : 'loading.gif',
                close_image   : 'closelabel.gif'
              }) 
            })
  </script>
 <tr>
   <td colspan="2" class="sb1NormalFontArial" style="padding-right:20px;">
    <?php echo getReportDescriptionFooter($report_id); ?> 
   </td>
 </tr>
<!--END Report Footer -->
