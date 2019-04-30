<?php 
/**
 * levelcombos_all_levels_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
$getWHName="select wh_name,stkid from tbl_warehouse where wh_id='".$_SESSION['user_warehouse']."'";
$resWHName=mysql_query($getWHName) or die(mysql_error());
$whName=mysql_fetch_row($resWHName);

$getStkLogo="select report_logo,report_title3 from stakeholder where stkid='".$whName[1]."'";
$resStkLogo=mysql_query($getStkLogo) or die(mysql_error());
$logo=mysql_fetch_row($resStkLogo);
?>

<div style="line-height:1;">
    <div id="logoLeft" style="float:left; width:107px; text-align:right;"> <img src="<?php echo PUBLIC_URL;?>images/gop.png" / > </div>
    <div id="report_type" style="float:left; width:440px; text-align:center;"> <span style="line-height:20px"><?php echo $logo[1]?></span><br/>
        <span style="line-height:15px"><b>Store: </b><?php echo $whName[0];?></span>
        <hr style="margin:3px 10px;" />
        <p><b><?php echo $rptName;?> 
            <!--Date: <?php echo date('d/m/y');?></b>--> 
        </p>
    </div>
</div>
<div style="clear:both"></div>
