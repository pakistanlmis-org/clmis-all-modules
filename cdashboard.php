<?php
$salt = 'jboFHjeQK5mc1K0cdSz5';
$token = sha1(md5($salt.date('Y-m-d')));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>cLMIS Dashboard</title>
</head>

<body>
	<div style="position:fixed !important;position:absolute;top:0;right:0;bottom:0;left:0;">
		<iframe src="http://c.lmis.gov.pk/clmis.php?token=<?php echo $token;?>" width="100%" height="100%"></iframe>
    </div>
</body>
</html>