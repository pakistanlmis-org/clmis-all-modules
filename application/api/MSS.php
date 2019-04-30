
<?php
/**
 * MMS
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../../application/includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include('auth.php');

 

	{
		$query="SELECT sysuser_tab.sysusr_pwd,sysuser_tab.usrlogin_id
				FROM sysuser_tab WHERE sysuser_tab.stkid = 6 order by sysuser_tab.usrlogin_id";
		$rs = mysql_query($query) or die(print mysql_error());
		$i=1;
		while($r = mysql_fetch_array($rs)) 
		{
			print $i++." ".$r[1]."        ".base64_decode($r[0])."<br>"; 
		}
  }
  
// http://localhost/lmisn/ws/loginsWH.php?ID=164
?>

